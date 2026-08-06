<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>{!! $printCss !!}</style>
</head>
<body>
@php
    $money = static fn ($value): string => 'Rp '.number_format((float) ($value ?? 0), 0, ',', '.');
    $yesNo = static fn ($value): string => $value ? 'Ya' : 'Tidak';
    $taxes = $breakdownDetail['taxes'] ?? [];
    $vat = $taxes['vat'] ?? [];
    $subBroker = $breakdownDetail['sub_broker_split'] ?? [];
    $freight = $calculationResult['freight'] ?? [];
    $profitability = $calculationResult['profitability'] ?? [];
    $shipownerLabels = ['siupal' => 'Pelayaran Nasional (SIUPAL)', 'sewa_harta' => 'Sewa Harta / Non-SIUPAL', 'asing_but' => 'Pelayaran Asing dengan BUT', 'asing_non_but' => 'Pelayaran Asing tanpa BUT'];
@endphp

<header class="report-header">
    <div>
        @if ($logoDataUri)<img class="brand-logo" src="{{ $logoDataUri }}" alt="NawaTax logo">@endif
        <span class="brand-name">NawaTax</span>
    </div>
    <h1 class="report-title">NawaTax Report</h1>
    <p class="generated-at">Dibuat pada {{ $generatedAt }}</p>
</header>

<section class="section">
    <h2 class="section-title">Input Summary</h2>
    <table class="data-table">
        <tr><th>Freight Owner</th><td class="amount">{{ $money($freight['owner_amount'] ?? $inputSummary['freight_owner'] ?? 0) }}</td></tr>
        <tr><th>Freight Shipper</th><td class="amount">{{ $money($freight['shipper_amount'] ?? $inputSummary['freight_shipper'] ?? 0) }}</td></tr>
        <tr><th>Operational Cost</th><td class="amount">{{ $money($inputSummary['reimbursable_costs'] ?? 0) }}</td></tr>
        <tr><th>Tax Profile - PPh Shipowner</th><td>{{ $shipownerLabels[$inputSummary['shipowner_status'] ?? ''] ?? '-' }}</td></tr>
        <tr><th>Tax Profile - PPh Agen</th><td>{{ ($inputSummary['agent_tax_type'] ?? 'pph23') === 'pph15' ? 'PPh Pasal 15' : 'PPh Pasal 23' }}</td></tr>
        <tr><th>PPN / Status PKP (Agen, Owner, Shipper)</th><td>{{ !empty($inputSummary['vat_enabled']) ? 'Aktif' : 'Tidak aktif' }} / {{ $yesNo($inputSummary['pkp_agen'] ?? false) }}, {{ $yesNo($inputSummary['pkp_shipowner'] ?? false) }}, {{ $yesNo($inputSummary['pkp_shipper'] ?? false) }}</td></tr>
    </table>
</section>

<section class="section">
    <h2 class="section-title">Breakdown Perhitungan</h2>
    <table class="data-table">
        <tr><th>Brokerage Fee / Komisi Bruto</th><td class="amount">{{ $money($profitability['gross_commission'] ?? 0) }}</td></tr>
        <tr><th>{{ $taxes['agent_withholding']['type'] ?? 'PPh Agen' }} dipotong Shipper</th><td class="amount">{{ $money($taxes['agent_withholding']['amount'] ?? 0) }}</td></tr>
        <tr><th>{{ $taxes['shipowner_withholding']['type'] ?? 'PPh Shipowner' }} diteruskan ke Shipowner</th><td class="amount">{{ $money($taxes['shipowner_withholding']['amount'] ?? 0) }}</td></tr>
        <tr><th>PPN Output</th><td class="amount">{{ $money($vat['output_vat'] ?? 0) }}</td></tr>
        <tr><th>PPN Input</th><td class="amount">{{ $money($vat['input_vat'] ?? 0) }}</td></tr>
        <tr><th>PPN Kurang Bayar</th><td class="amount">{{ $money($vat['vat_payable'] ?? 0) }}</td></tr>
        @if (!empty($subBroker['active']))
            <tr><th>Split Sub-Broker (Bruto)</th><td class="amount">{{ $money($subBroker['gross_commission'] ?? 0) }}</td></tr>
            <tr><th>{{ $taxes['sub_broker_withholding']['type'] ?? 'PPh Sub-Broker' }} Sub-Broker</th><td class="amount">{{ $money($taxes['sub_broker_withholding']['amount'] ?? 0) }}</td></tr>
            <tr><th>Kas Dibayarkan ke Sub-Broker</th><td class="amount">{{ $money($subBroker['cash_paid_to_sub_broker'] ?? 0) }}</td></tr>
        @endif
    </table>
</section>

<section class="profit-card">
    <div class="label">Nett Profit</div>
    <div class="amount">{{ $money($profitability['net_profit'] ?? 0) }}</div>
    <p class="note">Keuntungan bersih sesuai hasil final Calculator Engine.</p>
</section>

<footer class="footer">NawaTax - Shipbroker Profit &amp; Tax Calculator</footer>
</body>
</html>
