<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_scheme' => ['nullable', 'string', 'in:undisclosed,pure_brokerage'],
            'freight_owner' => ['nullable', 'numeric', 'min:0'],
            'freight_shipper' => ['nullable', 'numeric', 'min:0'],
            'freight_rate_owner' => ['nullable', 'numeric', 'min:0'],
            'freight_rate_shipper' => ['nullable', 'numeric', 'min:0'],
            'freight_total' => ['nullable', 'numeric', 'min:0'],
            'freight_rate' => ['nullable', 'numeric', 'min:0'],
            'cargo_quantity' => ['nullable', 'numeric', 'min:0'],
            'commission_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reimbursable_costs' => ['nullable', 'numeric', 'min:0'],
            'shipowner_status' => ['required', 'string', 'in:siupal,sewa_harta,asing_but,asing_non_but'],
            'pkp_agen' => ['nullable', 'boolean'],
            'pkp_shipowner' => ['nullable', 'boolean'],
            'pkp_shipper' => ['nullable', 'boolean'],
            'subbroker_split_active' => ['nullable', 'boolean'],
            'split_type' => ['required_if:subbroker_split_active,true', 'nullable', 'string', 'in:percentage,fixed'],
            'split_value' => ['required_if:subbroker_split_active,true', 'nullable', 'numeric', 'min:0'],
            'sub_broker_entity' => ['required_if:subbroker_split_active,true', 'nullable', 'string', 'in:corporate,individual'],
        ];
    }

    public function messages(): array
    {
        return [
            'freight_owner.numeric' => 'Freight owner harus berupa angka',
            'freight_owner.min' => 'Freight tidak boleh negatif',
            'freight_shipper.numeric' => 'Freight shipper harus berupa angka',
            'freight_shipper.min' => 'Freight tidak boleh negatif',
            'freight_total.numeric' => 'Freight total harus berupa angka',
            'freight_total.min' => 'Freight total tidak boleh negatif',
            'freight_rate.numeric' => 'Freight rate harus berupa angka',
            'freight_rate.min' => 'Freight rate tidak boleh negatif',
            'freight_rate_owner.numeric' => 'Freight rate shipowner harus berupa angka',
            'freight_rate_owner.min' => 'Freight rate shipowner tidak boleh negatif',
            'freight_rate_shipper.numeric' => 'Freight rate ke shipper harus berupa angka',
            'freight_rate_shipper.min' => 'Freight rate ke shipper tidak boleh negatif',
            'cargo_quantity.numeric' => 'Kuantitas kargo harus berupa angka',
            'cargo_quantity.min' => 'Kuantitas kargo tidak boleh negatif',
            'commission_percentage.numeric' => 'Persentase komisi harus berupa angka',
            'commission_percentage.min' => 'Persentase komisi tidak boleh negatif',
            'commission_percentage.max' => 'Persentase komisi tidak boleh melebihi 100%',
            'reimbursable_costs.numeric' => 'Biaya operasional harus berupa angka',
            'reimbursable_costs.min' => 'Biaya operasional tidak boleh negatif',
            'shipowner_status.required' => 'Status shipowner wajib diisi',
            'shipowner_status.in' => 'Status shipowner tidak valid',
            'split_type.required_if' => 'Split wajib diisi saat fitur sub-broker aktif',
            'split_type.in' => 'Tipe split tidak valid',
            'split_value.required_if' => 'Split wajib diisi saat fitur sub-broker aktif',
            'split_value.numeric' => 'Split harus berupa angka',
            'split_value.min' => 'Split tidak boleh negatif',
            'sub_broker_entity.required_if' => 'Jenis entitas sub-broker wajib diisi saat fitur sub-broker aktif',
            'sub_broker_entity.in' => 'Jenis entitas sub-broker tidak valid',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        $data['transaction_scheme'] = $data['transaction_scheme'] ?? 'undisclosed';

        $subbrokerActive = $this->toBoolean($data['subbroker_split_active'] ?? null);
        if (!$subbrokerActive && empty($data['split_type'])) {
            $data['split_type'] = 'percentage';
        }

        foreach (['pkp_agen', 'pkp_shipowner', 'pkp_shipper', 'subbroker_split_active'] as $field) {
            $data[$field] = $this->toBoolean($data[$field] ?? null);
        }

        foreach (['freight_owner', 'freight_shipper', 'freight_rate_owner', 'freight_rate_shipper', 'freight_total', 'freight_rate', 'cargo_quantity', 'commission_percentage', 'reimbursable_costs', 'split_value'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                $data[$field] = (float) $data[$field];
            }
        }

        $this->replace($data);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $data = $this->validated();

            if (($data['subbroker_split_active'] ?? false) && ($data['split_type'] ?? null) === 'percentage') {
                if (isset($data['split_value']) && $data['split_value'] > 100) {
                    $validator->errors()->add('split_value', 'Split tidak boleh melebihi 100%');
                }
            }

            if (empty($data['pkp_agen'])) {
                $data['pkp_agen'] = false;
            }

            if (($data['transaction_scheme'] ?? 'undisclosed') === 'pure_brokerage') {
                $hasTotal = isset($data['freight_total']) && $data['freight_total'] !== null && $data['freight_total'] !== '';
                $hasRateAndQuantity = isset($data['freight_rate']) && $data['freight_rate'] !== null && $data['freight_rate'] !== ''
                    && isset($data['cargo_quantity']) && $data['cargo_quantity'] !== null && $data['cargo_quantity'] !== '';

                if (! $hasTotal && ! $hasRateAndQuantity) {
                    $validator->errors()->add('freight_total', 'Untuk skema pure brokerage, isi freight total atau freight rate + cargo quantity');
                }

                if ($hasTotal && ($hasRateAndQuantity || ! empty($data['freight_rate']) || ! empty($data['cargo_quantity']))) {
                    $validator->errors()->add('freight_total', 'Isi Total Freight atau Freight Rate dan Kuantitas Kargo, bukan keduanya.');
                }

                if (! isset($data['commission_percentage']) || $data['commission_percentage'] === null || $data['commission_percentage'] === '') {
                    $validator->errors()->add('commission_percentage', 'Persentase komisi wajib diisi untuk skema pure brokerage');
                }
            }

            $isUndisclosed = ($data['transaction_scheme'] ?? 'undisclosed') === 'undisclosed';
            if ($isUndisclosed) {
                $hasAmounts = isset($data['freight_owner'], $data['freight_shipper'])
                    && $data['freight_owner'] !== '' && $data['freight_shipper'] !== '';
                $hasRates = isset($data['freight_rate_owner'], $data['freight_rate_shipper'], $data['cargo_quantity'])
                    && $data['freight_rate_owner'] !== '' && $data['freight_rate_shipper'] !== '' && $data['cargo_quantity'] !== '';

                if (! $hasAmounts && ! $hasRates) {
                    $validator->errors()->add('freight_owner', 'Isi freight dasar dan harga jual, atau freight rate shipowner, freight rate ke shipper, dan kuantitas kargo.');
                }
                if ($hasAmounts && ($hasRates || ! empty($data['freight_rate_owner']) || ! empty($data['freight_rate_shipper']) || ! empty($data['cargo_quantity']))) {
                    $validator->errors()->add('freight_owner', 'Gunakan salah satu metode input freight: nominal atau rate × kuantitas.');
                }
                if ($hasAmounts && $data['freight_shipper'] < $data['freight_owner']) {
                    $validator->errors()->add('freight_shipper', 'Freight shipper harus lebih besar atau sama dengan freight owner');
                }
                if ($hasRates && $data['freight_rate_shipper'] < $data['freight_rate_owner']) {
                    $validator->errors()->add('freight_rate_shipper', 'Freight rate ke shipper harus lebih besar atau sama dengan freight rate shipowner.');
                }
            }
        });
    }

    private function toBoolean($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }
}
