<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Mesin perhitungan transaksi shipbroker berdasarkan PRD NawaTax.
 *
 * Semua nilai uang menggunakan IDR. PPh dibulatkan ke bawah ke Rupiah penuh,
 * sedangkan PPN dibulatkan ke Rupiah terdekat.
 */
class ProfitCalculatorService
{
    private const VAT_RATE = 0.11;

    /** @var array<string, array{type: string, rate: float}> */
    private const SHIPOWNER_TAX_PROFILES = [
        'siupal' => ['type' => 'PPh Pasal 15', 'rate' => 0.012],
        'sewa_harta' => ['type' => 'PPh Pasal 23', 'rate' => 0.02],
        'asing_but' => ['type' => 'PPh Pasal 15 WPLN', 'rate' => 0.0264],
        'asing_non_but' => ['type' => 'PPh Pasal 26', 'rate' => 0.20],
    ];

    /** @var list<array{limit: float|null, rate: float}> */
    private const PPH_21_BRACKETS = [
        ['limit' => 60000000.0, 'rate' => 0.05],
        ['limit' => 250000000.0, 'rate' => 0.15],
        ['limit' => 500000000.0, 'rate' => 0.25],
        ['limit' => 5000000000.0, 'rate' => 0.30],
        ['limit' => null, 'rate' => 0.35],
    ];

    /**
     * Menghitung transaksi Undisclosed Principal atau Pure Brokerage.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function calculate(array $input): array
    {
        $data = $this->normalizeInput($input);
        $freight = $this->calculateFreight($data);
        $commission = $this->calculateCommission($data, $freight);
        $agentTax = $this->calculateAgentTax($data, $commission, $freight);
        $vat = $this->calculateVat($data, $freight, $commission);
        $shipownerTax = $this->calculateShipownerTax($data, $freight);
        $subBroker = $this->calculateSubBrokerSplit($data, $commission['gross_amount']);

        return $data['transaction_scheme'] === 'pure_brokerage'
            ? $this->pureBrokerageResult($data, $freight, $commission, $agentTax, $vat, $shipownerTax, $subBroker)
            : $this->undisclosedResult($data, $freight, $commission, $agentTax, $vat, $shipownerTax, $subBroker);
    }

    /**
     * Menentukan nilai freight yang menjadi dasar transaksi.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, float>
     */
    private function calculateFreight(array $data): array
    {
        if ($data['transaction_scheme'] === 'undisclosed') {
            return [
                'owner_amount' => $data['freight_owner'],
                'shipper_amount' => $data['freight_shipper'],
                'total_amount' => $data['freight_shipper'],
            ];
        }

        // Pure Brokerage: total freight dapat diberikan langsung atau dari rate x kuantitas.
        $total = $data['freight_total'] ?? $data['freight_rate'] * $data['cargo_quantity'];

        return [
            'owner_amount' => 0.0,
            'shipper_amount' => $total,
            'total_amount' => $total,
        ];
    }

    /**
     * Menghitung komisi broker. Pada Undisclosed, komisi adalah margin freight.
     * Pada Pure Brokerage, komisi adalah total freight x persentase komisi.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, float>  $freight
     * @return array<string, float>
     */
    private function calculateCommission(array $data, array $freight): array
    {
        $grossAmount = $data['transaction_scheme'] === 'pure_brokerage'
            ? $freight['total_amount'] * ($data['commission_percentage'] / 100)
            : $freight['shipper_amount'] - $freight['owner_amount'];

        return [
            'rate' => $data['transaction_scheme'] === 'pure_brokerage' ? $data['commission_percentage'] / 100 : 0.0,
            'gross_amount' => $grossAmount,
        ];
    }

    /**
     * Menghitung PPh 23: 2% bila ber-NPWP dan 4% bila tidak ber-NPWP.
     * Dasar pengenaan adalah penghasilan bruto agen, tidak termasuk PPN.
     *
     * @return array<string, mixed>
     */
    private function calculateTaxPPh23(float $taxBase, bool $hasNpwp, bool $enabled): array
    {
        $rate = $hasNpwp ? 0.02 : 0.04;

        return [
            'type' => 'PPh Pasal 23',
            'rate' => $rate,
            'tax_base' => $taxBase,
            'amount' => $enabled ? $this->floorTax($taxBase * $rate) : 0.0,
            'is_final' => false,
        ];
    }

    /**
     * Menghitung PPh 15 final sebesar 1,2% dari penghasilan bruto.
     *
     * @return array<string, mixed>
     */
    private function calculateTaxPPh15(float $taxBase, bool $enabled): array
    {
        return [
            'type' => 'PPh Pasal 15',
            'rate' => 0.012,
            'tax_base' => $taxBase,
            'amount' => $enabled ? $this->floorTax($taxBase * 0.012) : 0.0,
            'is_final' => true,
        ];
    }

    /**
     * Memilih pajak Agen. PPh 23 memakai komisi/agency fee; PPh 15 memakai
     * freight bruto karena berlaku untuk transaksi freight/charter pelayaran.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, float>  $commission
     * @param  array<string, float>  $freight
     * @return array<string, mixed>
     */
    private function calculateAgentTax(array $data, array $commission, array $freight): array
    {
        if ($data['agent_tax_type'] === 'pph15') {
            return $this->calculateTaxPPh15($freight['total_amount'], $data['withholding_tax_enabled']);
        }

        return $this->calculateTaxPPh23(
            $commission['gross_amount'],
            $data['agent_has_npwp'],
            $data['withholding_tax_enabled'],
        );
    }

    /**
     * Menghitung PPh 21 sub-broker individu dengan DPP 50% dari bruto dan
     * tarif progresif Pasal 17.
     *
     * @return array<string, mixed>
     */
    private function calculateSubBrokerTax(float $grossCommission, bool $enabled): array
    {
        $taxBase = $grossCommission * 0.5;
        $remaining = $taxBase;
        $previousLimit = 0.0;
        $amount = 0.0;
        $tiers = [];

        foreach (self::PPH_21_BRACKETS as $bracket) {
            if ($remaining <= 0) {
                break;
            }

            $tierCapacity = $bracket['limit'] === null
                ? $remaining
                : $bracket['limit'] - $previousLimit;
            $tierBase = min($remaining, $tierCapacity);
            // Setiap pajak pada lapisan tarif dikembalikan dalam Rupiah penuh.
            $tierTax = $this->floorTax($tierBase * $bracket['rate']);
            $amount += $tierTax;
            $tiers[] = ['tax_base' => $tierBase, 'rate' => $bracket['rate'], 'amount' => $tierTax];
            $remaining -= $tierBase;
            $previousLimit = $bracket['limit'] ?? $previousLimit;
        }

        return [
            'type' => 'PPh Pasal 21',
            'rate' => $taxBase > 0 ? $amount / $taxBase : 0.0,
            'tax_base' => $taxBase,
            'amount' => $enabled ? $amount : 0.0,
            'tiers' => $tiers,
        ];
    }

    /** @param array<string, mixed> $data @param array<string, float> $freight @param array<string, float> $commission @return array<string, float> */
    private function calculateVat(array $data, array $freight, array $commission): array
    {
        $outputBase = $data['transaction_scheme'] === 'pure_brokerage'
            ? $commission['gross_amount']
            : $freight['shipper_amount'];
        $inputBase = $data['transaction_scheme'] === 'undisclosed' ? $freight['owner_amount'] : 0.0;

        // PPN keluaran hanya boleh dipungut Agen PKP.
        $outputVat = $data['vat_enabled'] && $data['pkp_agen']
            ? $this->roundVat($outputBase * self::VAT_RATE)
            : 0.0;
        // PPN masukan hanya dapat dikreditkan bila Agen dan Shipowner sama-sama PKP.
        $inputVat = $data['vat_enabled'] && $data['pkp_agen'] && $data['pkp_shipowner']
            ? $this->roundVat($inputBase * self::VAT_RATE)
            : 0.0;

        return [
            'rate' => self::VAT_RATE,
            'output_tax_base' => $outputBase,
            'output_vat' => $outputVat,
            'input_tax_base' => $inputBase,
            'input_vat' => $inputVat,
            'vat_payable' => $outputVat - $inputVat,
        ];
    }

    /** @param array<string, mixed> $data @param array<string, float> $freight @return array<string, mixed> */
    private function calculateShipownerTax(array $data, array $freight): array
    {
        if ($data['transaction_scheme'] === 'pure_brokerage' && $data['shipowner_status'] === null) {
            return ['type' => '', 'rate' => 0.0, 'tax_base' => 0.0, 'amount' => 0.0, 'is_final' => false];
        }

        $profile = self::SHIPOWNER_TAX_PROFILES[$data['shipowner_status']];
        // Tarif treaty hanya dapat menggantikan tarif standar PPh 26 untuk non-residen.
        $rate = $data['shipowner_tax_treaty_rate'] ?? $profile['rate'];

        return [
            'type' => $profile['type'],
            'rate' => $rate,
            'tax_base' => $freight['owner_amount'],
            'amount' => $data['withholding_tax_enabled']
                ? $this->floorTax($freight['owner_amount'] * $rate)
                : 0.0,
            'is_final' => $data['shipowner_status'] === 'siupal',
        ];
    }

    /**
     * Menghitung hak bruto Sub-Broker dan pajaknya. Pajak dipotong dari hak
     * tersebut, sehingga total biaya Broker selalu tetap sebesar gross split.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function calculateSubBrokerSplit(array $data, float $commissionBase): array
    {
        if (! $data['subbroker_split_active']) {
            return $this->emptySubBrokerSplit();
        }

        $grossCommission = $data['split_type'] === 'percentage'
            ? $commissionBase * ($data['split_value'] / 100)
            : $data['split_value'];

        if ($grossCommission > $commissionBase) {
            throw new InvalidArgumentException('Nominal split tidak boleh melebihi komisi bruto Broker.');
        }

        if ($data['sub_broker_entity'] === 'corporate') {
            $tax = $this->calculateTaxPPh23($grossCommission, true, $data['withholding_tax_enabled']);
            $tax['rate'] = 0.02; // PRD menetapkan Sub-Broker badan usaha selalu 2%.
        } else {
            $tax = $this->calculateSubBrokerTax($grossCommission, $data['withholding_tax_enabled']);
        }

        return [
            'active' => true,
            'type' => $data['split_type'],
            'value' => $data['split_value'],
            'entity' => $data['sub_broker_entity'],
            'gross_commission' => $grossCommission,
            'withholding_type' => $tax['type'],
            'withholding_rate' => $tax['rate'],
            'tax_base' => $tax['tax_base'],
            'withholding_tax' => $tax['amount'],
            'tax_tiers' => $tax['tiers'] ?? [],
            'cash_paid_to_sub_broker' => $grossCommission - $tax['amount'],
            'cash_paid_to_state' => $tax['amount'],
            'total_broker_cost' => $grossCommission,
        ];
    }

    /** @param array<string, mixed> $data @param array<string, float> $freight @param array<string, float> $commission @param array<string, mixed> $agentTax @param array<string, float> $vat @param array<string, mixed> $shipownerTax @param array<string, mixed> $subBroker @return array<string, mixed> */
    private function undisclosedResult(array $data, array $freight, array $commission, array $agentTax, array $vat, array $shipownerTax, array $subBroker): array
    {
        $cashIn = $freight['shipper_amount'] + $vat['output_vat'] - $agentTax['amount'];
        $cashOutToShipowner = $freight['owner_amount'] + $vat['input_vat'] - $shipownerTax['amount'];
        $netProfit = $commission['gross_amount'] - $subBroker['gross_commission'] - $data['reimbursable_costs'] - $agentTax['amount'];

        return $this->result(
            $data,
            $freight,
            $commission,
            $agentTax,
            $shipownerTax,
            $vat,
            $subBroker,
            [
                'cash_in_from_shipper' => $cashIn,
                'operational_cash_out' => $data['reimbursable_costs'],
                'cash_out_to_shipowner' => $cashOutToShipowner,
                'vat_payable_to_state' => $vat['vat_payable'],
                'net_cash_received_broker' => $cashIn - $cashOutToShipowner - $vat['vat_payable'],
            ],
            $netProfit,
        );
    }

    /** @param array<string, mixed> $data @param array<string, float> $freight @param array<string, float> $commission @param array<string, mixed> $agentTax @param array<string, float> $vat @param array<string, mixed> $shipownerTax @param array<string, mixed> $subBroker @return array<string, mixed> */
    private function pureBrokerageResult(array $data, array $freight, array $commission, array $agentTax, array $vat, array $shipownerTax, array $subBroker): array
    {
        // Agen hanya menerima komisi; freight utama tidak melalui kas Agen.
        $cashIn = $commission['gross_amount'] + $vat['output_vat'] - $agentTax['amount'];
        $netProfit = $commission['gross_amount'] - $subBroker['gross_commission'] - $data['reimbursable_costs'] - $agentTax['amount'];

        return $this->result(
            $data,
            $freight,
            $commission,
            $agentTax,
            $shipownerTax,
            $vat,
            $subBroker,
            [
                'cash_in_from_shipper' => $cashIn,
                'operational_cash_out' => $data['reimbursable_costs'],
                'cash_out_to_shipowner' => 0.0,
                'vat_payable_to_state' => $vat['vat_payable'],
                'net_cash_received_broker' => $cashIn - $vat['vat_payable'],
            ],
            $netProfit,
        );
    }

    /** @param array<string, mixed> $data @param array<string, float> $freight @param array<string, float> $commission @param array<string, mixed> $agentTax @param array<string, mixed> $shipownerTax @param array<string, float> $vat @param array<string, mixed> $subBroker @param array<string, float> $cashFlow @return array<string, mixed> */
    private function result(array $data, array $freight, array $commission, array $agentTax, array $shipownerTax, array $vat, array $subBroker, array $cashFlow, float $netProfit): array
    {
        return [
            'input' => $data,
            'freight' => $freight,
            'commission' => $commission,
            'cash_flow' => $cashFlow,
            'taxes' => [
                'agent_withholding' => $agentTax,
                'shipowner_withholding' => $shipownerTax,
                'vat' => $vat,
                'sub_broker_withholding' => [
                    'type' => $subBroker['withholding_type'], 'rate' => $subBroker['withholding_rate'],
                    'tax_base' => $subBroker['tax_base'], 'amount' => $subBroker['withholding_tax'],
                    'tiers' => $subBroker['tax_tiers'],
                ],
            ],
            'sub_broker_split' => $subBroker,
            'profitability' => [
                'gross_commission' => $commission['gross_amount'],
                'sub_broker_gross_split' => $subBroker['gross_commission'],
                'operational_costs' => $data['reimbursable_costs'],
                'agent_income_tax' => $agentTax['amount'],
                'net_profit' => $netProfit,
            ],
        ];
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    private function normalizeInput(array $input): array
    {
        $scheme = $input['transaction_scheme'] ?? 'undisclosed';
        if (! in_array($scheme, ['undisclosed', 'pure_brokerage'], true)) {
            throw new InvalidArgumentException('Skema transaksi tidak valid.');
        }

        $isUndisclosed = $scheme === 'undisclosed';
        $freightOwner = $isUndisclosed ? $this->amount($input, 'freight_owner') : 0.0;
        $freightShipper = $isUndisclosed ? $this->amount($input, 'freight_shipper') : 0.0;
        if ($isUndisclosed && $freightShipper < $freightOwner) {
            throw new InvalidArgumentException('Freight shipper harus lebih besar atau sama dengan freight owner.');
        }

        $freightTotal = array_key_exists('freight_total', $input) && $input['freight_total'] !== null && $input['freight_total'] !== ''
            ? $this->amount($input, 'freight_total')
            : null;
        $freightRate = array_key_exists('freight_rate', $input) && $input['freight_rate'] !== null && $input['freight_rate'] !== ''
            ? $this->amount($input, 'freight_rate')
            : null;
        $cargoQuantity = array_key_exists('cargo_quantity', $input) && $input['cargo_quantity'] !== null && $input['cargo_quantity'] !== ''
            ? $this->amount($input, 'cargo_quantity')
            : null;
        if (! $isUndisclosed && $freightTotal === null && ($freightRate === null || $cargoQuantity === null)) {
            throw new InvalidArgumentException('Pure Brokerage memerlukan freight_total atau freight_rate dan cargo_quantity.');
        }

        $commissionPercentage = ! $isUndisclosed ? $this->amount($input, 'commission_percentage') : 0.0;
        if ($commissionPercentage > 100) {
            throw new InvalidArgumentException('Persentase komisi tidak boleh melebihi 100%.');
        }

        $status = $input['shipowner_status'] ?? null;
        if (($isUndisclosed || $status !== null) && ! is_string($status)) {
            throw new InvalidArgumentException('Status shipowner tidak valid.');
        }
        if ($status !== null && ! array_key_exists($status, self::SHIPOWNER_TAX_PROFILES)) {
            throw new InvalidArgumentException('Status shipowner tidak valid.');
        }

        $treatyRate = array_key_exists('shipowner_tax_treaty_rate', $input)
            ? $this->percentageRate($input, 'shipowner_tax_treaty_rate')
            : (array_key_exists('tax_treaty_rate', $input) ? $this->percentageRate($input, 'tax_treaty_rate') : null);
        if ($treatyRate !== null && $status !== 'asing_non_but') {
            throw new InvalidArgumentException('Tarif tax treaty hanya berlaku untuk Shipowner asing tanpa BUT.');
        }

        $splitActive = $this->boolean($input['subbroker_split_active'] ?? false);
        $splitType = $input['split_type'] ?? 'percentage';
        $splitValue = $splitActive ? $this->amount($input, 'split_value') : 0.0;
        $entity = $input['sub_broker_entity'] ?? '';
        if ($splitActive && ! in_array($splitType, ['percentage', 'fixed'], true)) {
            throw new InvalidArgumentException('Tipe split tidak valid.');
        }
        if ($splitActive && ! in_array($entity, ['corporate', 'individual'], true)) {
            throw new InvalidArgumentException('Jenis entitas sub-broker tidak valid.');
        }
        if ($splitActive && $splitType === 'percentage' && $splitValue > 100) {
            throw new InvalidArgumentException('Split tidak boleh melebihi 100%.');
        }

        $agentTaxType = $input['agent_tax_type'] ?? 'pph23';
        if (! in_array($agentTaxType, ['pph23', 'pph15'], true)) {
            throw new InvalidArgumentException('Jenis PPh Agen tidak valid.');
        }

        return [
            'transaction_scheme' => $scheme,
            'freight_owner' => $freightOwner,
            'freight_shipper' => $freightShipper,
            'freight_total' => $freightTotal,
            'freight_rate' => $freightRate ?? 0.0,
            'cargo_quantity' => $cargoQuantity ?? 0.0,
            'commission_percentage' => $commissionPercentage,
            'reimbursable_costs' => $this->amount($input, 'reimbursable_costs', 0.0),
            'shipowner_status' => $status,
            'shipowner_tax_treaty_rate' => $treatyRate,
            'agent_tax_type' => $agentTaxType,
            'agent_has_npwp' => $this->boolean($input['agent_has_npwp'] ?? $input['agent_npwp'] ?? true),
            'pkp_agen' => $this->boolean($input['pkp_agen'] ?? $input['pkp_agent'] ?? true),
            'pkp_shipowner' => $this->boolean($input['pkp_shipowner'] ?? true),
            'pkp_shipper' => $this->boolean($input['pkp_shipper'] ?? true),
            'vat_enabled' => $this->boolean($input['vat_enabled'] ?? true),
            'withholding_tax_enabled' => $this->boolean($input['withholding_tax_enabled'] ?? true),
            'subbroker_split_active' => $splitActive,
            'split_type' => $splitType,
            'split_value' => $splitValue,
            'sub_broker_entity' => $entity,
        ];
    }

    /** @return array<string, mixed> */
    private function emptySubBrokerSplit(): array
    {
        return [
            'active' => false, 'type' => '', 'value' => 0.0, 'entity' => '',
            'gross_commission' => 0.0, 'withholding_type' => '', 'withholding_rate' => 0.0,
            'tax_base' => 0.0, 'withholding_tax' => 0.0, 'tax_tiers' => [],
            'cash_paid_to_sub_broker' => 0.0, 'cash_paid_to_state' => 0.0, 'total_broker_cost' => 0.0,
        ];
    }

    /** @param array<string, mixed> $input */
    private function amount(array $input, string $key, ?float $default = null): float
    {
        $value = $input[$key] ?? $default;
        if (! is_numeric($value) || ! is_finite((float) $value) || (float) $value < 0) {
            throw new InvalidArgumentException("{$key} harus berupa nominal angka yang tidak negatif.");
        }

        return (float) $value;
    }

    private function boolean(mixed $value): bool
    {
        return is_bool($value) ? $value : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /** @param array<string, mixed> $input */
    private function percentageRate(array $input, string $key): float
    {
        $value = $this->amount($input, $key);
        if ($value > 100) {
            throw new InvalidArgumentException("{$key} tidak boleh melebihi 100%.");
        }

        return $value / 100;
    }

    private function floorTax(float $amount): float
    {
        return (float) floor($amount + 0.0000001);
    }

    private function roundVat(float $amount): float
    {
        return (float) round($amount, 0, PHP_ROUND_HALF_UP);
    }
}
