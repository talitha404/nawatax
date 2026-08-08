@php
    // Keep the dashboard visible before the first calculation and reuse the
    // latest submitted values when the calculator view is rendered again.
    $result = $result ?? [
        'input' => [
            'transaction_scheme' => 'undisclosed', 'freight_owner' => '', 'freight_shipper' => '',
            'freight_rate_owner' => '', 'freight_rate_shipper' => '',
            'freight_total' => '', 'freight_rate' => '', 'cargo_quantity' => '', 'commission_percentage' => '',
            'reimbursable_costs' => '', 'shipowner_status' => 'sewa_harta',
            'pkp_agen' => true, 'pkp_shipowner' => true, 'pkp_shipper' => true,
            'subbroker_split_active' => false, 'split_type' => 'percentage',
            'split_value' => '', 'sub_broker_entity' => 'corporate',
        ],
        'freight' => ['owner_amount' => 0, 'shipper_amount' => 0],
        'cash_flow' => ['cash_in_from_shipper' => 0, 'operational_cash_out' => 0, 'cash_out_to_shipowner' => 0, 'vat_payable_to_state' => 0, 'net_cash_received_broker' => 0],
        'profitability' => ['net_profit' => 0, 'gross_commission' => 0],
        'taxes' => [
            'agent_withholding' => ['type' => 'PPh', 'amount' => 0],
            'shipowner_withholding' => ['type' => 'PPh', 'amount' => 0],
            'vat' => ['output_vat' => 0, 'input_vat' => 0],
            'sub_broker_withholding' => ['type' => 'PPh', 'amount' => 0],
        ],
        'sub_broker_split' => ['active' => false],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NawaTax — Shipbroker Profit & Tax Calculator</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-500 selection:text-white">

    <!-- 1. Navigasi Sticky -->
    <nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('nawatax.png') }}" alt="NawaTax Logo" class="h-8 w-auto">
                    <span class="text-xl font-extrabold bg-gradient-to-r from-blue-700 to-indigo-800 bg-clip-text text-transparent">
                        NawaTax
                    </span>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#calculator" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">Kalkulator</a>
                    <a href="#how-to-use" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">Cara Pakai</a>
                    <a href="#faq" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">FAQ</a>
                </div>

                <!-- Tombol Hamburger Mobile -->
                <div class="flex md:hidden items-center">
                    <button @click="open = !open" 
                            type="button" 
                            class="p-2 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                            aria-label="Toggle Menu">
                        
                        <!-- Ikon Hamburger (saat ditutup) -->
                        <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <!-- Ikon Silang / Close (saat dibuka) -->
                        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu Mobile -->
        <div x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            x-cloak
            class="md:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-4 space-y-2 shadow-lg">
            
            <a href="#calculator" @click="open = false" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                Kalkulator
            </a>
            <a href="#how-to-use" @click="open = false" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                Cara Pakai
            </a>
            <a href="#faq" @click="open = false" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                FAQ
            </a>
        </div>
    </nav>

    <main>
        <!-- 2. Hero Section dengan Gradient Biru -->
        <section class="bg-gradient-to-br from-blue-700 via-indigo-800 to-slate-900 text-white py-12 md:py-16 px-4 sm:px-6 lg:px-8 shadow-inner">
            <div class="max-w-7xl mx-auto text-center">
                <span class="font-semibold">
                    Shipbroker Financial Tool
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Kalkulator Margin & Pajak Shipbroker
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-sm sm:text-base text-blue-100/90 leading-relaxed">
                    Hitung komisi bersih, skema PPh Pasal 15/23/26, PPN, dan pembagian komisi sub-broker 
                </p>
            </div>
        </section>

        <!-- 3. Kalkulator (Main App Layout) -->
        <section id="calculator" class="py-8 md:py-12 -mt-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- KOLOM KIRI: Form Input (lg:col-span-7) -->
                    <div class="lg:col-span-7 space-y-6">
                        <form id="calculator-form" method="POST" action="{{ route('calculator.calculate') }}" class="space-y-6"
                            x-data="{
                                amounts: {
                                    freight_owner: @js(old('freight_owner', $result['input']['freight_owner'] ?? '')),
                                    freight_shipper: @js(old('freight_shipper', $result['input']['freight_shipper'] ?? '')),
                                    freight_rate_owner: @js(old('freight_rate_owner', $result['input']['freight_rate_owner'] ?? '')),
                                    freight_rate_shipper: @js(old('freight_rate_shipper', $result['input']['freight_rate_shipper'] ?? '')),
                                    freight_total: @js(old('freight_total', $result['input']['freight_total'] ?? '')),
                                    freight_rate: @js(old('freight_rate', $result['input']['freight_rate'] ?? '')),
                                    cargo_quantity: @js(old('cargo_quantity', $result['input']['cargo_quantity'] ?? '')),
                                    reimbursable_costs: @js(old('reimbursable_costs', $result['input']['reimbursable_costs'] ?? '')),
                                    split_value: @js(old('split_value', $result['input']['split_value'] ?? '')),
                                },
                                formatAmount(value) {
                                    const raw = String(value ?? '').replace(/\D/g, '');
                                    return raw ? new Intl.NumberFormat('id-ID').format(Number(raw)) : '';
                                },
                                updateAmount(field, event) {
                                    this.amounts[field] = event.target.value.replace(/\D/g, '');
                                    event.target.value = this.formatAmount(this.amounts[field]);
                                },
                                clearFields() {
                                    this.amounts.freight_owner = '';
                                    this.amounts.freight_shipper = '';
                                    this.amounts.freight_rate_owner = '';
                                    this.amounts.freight_rate_shipper = '';
                                    this.amounts.freight_total = '';
                                    this.amounts.freight_rate = '';
                                    this.amounts.cargo_quantity = '';
                                    this.amounts.reimbursable_costs = '';
                                    this.amounts.split_value = '';
                                },
                                scheme: @js(old('transaction_scheme', $result['input']['transaction_scheme'] ?? 'undisclosed')),
                            }">
                            @csrf

                            <!-- CARD 1: Skema Transaksi & Nilai Freight -->
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 transition-all hover:shadow-md">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                            1
                                        </div>
                                        <div>
                                            <h2 class="text-base font-bold text-slate-800">Transaksi & Skema Transaksi</h2>
                                            <p class="text-xs text-slate-500">Nilai <strong>freight</strong> dan metode penagihan agen</p>
                                        </div>
                                    </div>
                                    <!-- Tombol Reset Nilai Freight -->
                                    <a href="{{ route('calculator.index') }}" 
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 active:bg-red-200 border border-red-200/60 transition-all duration-200 shadow-sm"
                                    title="Reset Form & Hasil">
                                        <!-- Ikon Sampah (Trash Icon) -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Reset Input & Hasil</span>
                                    </a>
                                </div>

                                <div class="space-y-4">
                                    <!-- Skema Transaksi -->
                                    <div>
                                        <label for="transaction_scheme" class="flex items-center gap-1 text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                            Skema Keagenan <span title="Undisclosed Principal: agen menagih freight gross dan meneruskannya ke Shipowner. Pure Brokerage: agen hanya menerima komisi; freight dibayar langsung antara Shipper dan Shipowner." class="cursor-help text-slate-400 normal-case" aria-label="Info skema keagenan">ⓘ</span>
                                        </label>
                                        <select id="transaction_scheme" name="transaction_scheme" x-model="scheme" @change="clearFields()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            <option value="undisclosed" @selected(old('transaction_scheme', $result['input']['transaction_scheme']) === 'undisclosed')>Undisclosed Principal / Back-to-Back</option>
                                            <option value="pure_brokerage" @selected(old('transaction_scheme', $result['input']['transaction_scheme']) === 'pure_brokerage')>Pure Brokerage / Direct Agency</option>
                                        </select>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="space-y-6">
                                            <!-- UNDISCLOSED PRINCIPAL -->
                                            <template x-if="scheme === 'undisclosed'">
                                                <div x-transition class="space-y-4">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-700">Input Undisclosed Principal</p>
                                                        <p class="mt-1 text-[11px] text-slate-500">Masukkan <strong>freight shipowner dan shipper</strong> atau hitung dari <strong>rate × quantity</strong>.</p>
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="freight_owner" class="block text-xs font-semibold text-slate-700 mb-1">Freight Dasar Shipowner (IDR)</label>
                                                            <div class="relative">
                                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                                <input type="hidden" name="freight_owner" :value="amounts.freight_owner">
                                                                <input type="text" id="freight_owner" inputmode="numeric" 
                                                                    :value="formatAmount(amounts.freight_owner)" 
                                                                    @input="updateAmount('freight_owner', $event)" 
                                                                    :disabled="String(amounts.freight_rate_owner).length > 0 || String(amounts.freight_rate_shipper).length > 0" 
                                                                    class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('freight_owner') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                                    placeholder="100000000">
                                                            </div>
                                                            @error('freight_owner') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="freight_shipper" class="block text-xs font-semibold text-slate-700 mb-1">Harga Jual ke Shipper (IDR)</label>
                                                            <div class="relative">
                                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                                <input type="hidden" name="freight_shipper" :value="amounts.freight_shipper">
                                                                <input type="text" id="freight_shipper" inputmode="numeric" 
                                                                    :value="formatAmount(amounts.freight_shipper)" 
                                                                    @input="updateAmount('freight_shipper', $event)" 
                                                                    :disabled="String(amounts.freight_rate_owner).length > 0 || String(amounts.freight_rate_shipper).length > 0" 
                                                                    class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('freight_shipper') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                                    placeholder="110000000">
                                                            </div>
                                                            @error('freight_shipper') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-200">
                                                        <div>
                                                            <label for="freight_rate_owner" class="block text-xs font-semibold text-slate-700 mb-1">Freight Rate Shipowner (IDR)</label>
                                                            <div class="relative">
                                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                                <input type="hidden" name="freight_rate_owner" :value="amounts.freight_rate_owner">
                                                                <input type="text" id="freight_rate_owner" inputmode="numeric" 
                                                                    :value="formatAmount(amounts.freight_rate_owner)" 
                                                                    @input="updateAmount('freight_rate_owner', $event)" 
                                                                    :disabled="String(amounts.freight_owner).length > 0 || String(amounts.freight_shipper).length > 0" 
                                                                    class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('freight_rate_owner') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                                    placeholder="1000000">
                                                            </div>
                                                            @error('freight_rate_owner') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="freight_rate_shipper" class="block text-xs font-semibold text-slate-700 mb-1">Freight Rate ke Shipper (IDR)</label>
                                                            <div class="relative">
                                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                                <input type="hidden" name="freight_rate_shipper" :value="amounts.freight_rate_shipper">
                                                                <input type="text" id="freight_rate_shipper" inputmode="numeric" 
                                                                    :value="formatAmount(amounts.freight_rate_shipper)" 
                                                                    @input="updateAmount('freight_rate_shipper', $event)" 
                                                                    :disabled="String(amounts.freight_owner).length > 0 || String(amounts.freight_shipper).length > 0" 
                                                                    class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('freight_rate_shipper') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                                    placeholder="1100000">
                                                            </div>
                                                            @error('freight_rate_shipper') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- PURE BROKERAGE -->
                                            <template x-if="scheme === 'pure_brokerage'">
                                                <div x-transition class="space-y-4">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-700">Input Pure Brokerage</p>
                                                        <p class="mt-1 text-[11px] text-slate-500">Isi <strong>total freight</strong> atau <strong>rate × quantity</strong> untuk menghitung komisi broker.</p>
                                                    </div>

                                                    <div>
                                                        <label for="freight_total" class="block text-xs font-semibold text-slate-700 mb-1">Total Freight (IDR)</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                            <input type="hidden" name="freight_total" :value="amounts.freight_total">
                                                            <input type="text" id="freight_total" inputmode="numeric" 
                                                                :value="formatAmount(amounts.freight_total)" 
                                                                @input="updateAmount('freight_total', $event)" 
                                                                :disabled="String(amounts.freight_rate).length > 0" 
                                                                class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('freight_total') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                                placeholder="1000000000">
                                                        </div>
                                                        @error('freight_total') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-200">
                                                        <div>
                                                            <label for="freight_rate" class="block text-xs font-semibold text-slate-700 mb-1">Freight Rate (IDR)</label>
                                                            <div class="relative">
                                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                                <input type="hidden" name="freight_rate" :value="amounts.freight_rate">
                                                                <input type="text" id="freight_rate" inputmode="numeric" 
                                                                    :value="formatAmount(amounts.freight_rate)" 
                                                                    @input="updateAmount('freight_rate', $event)" 
                                                                    :disabled="String(amounts.freight_total).length > 0" 
                                                                    class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('freight_rate') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                                    placeholder="1000000">
                                                            </div>
                                                            @error('freight_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="commission_percentage" class="block text-xs font-semibold text-slate-700 mb-1">Persentase Komisi (%)</label>
                                                            <input type="number" id="commission_percentage" name="commission_percentage" min="0" max="100" step="0.01" value="{{ old('commission_percentage', $result['input']['commission_percentage']) }}" class="w-full px-3.5 py-2.5 bg-white border @error('commission_percentage') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all" placeholder="2.5">
                                                            @error('commission_percentage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Kuantitas Kargo (Universal Input) -->
                                            <div>
                                                <label for="cargo_quantity" class="block text-xs font-semibold text-slate-700 mb-1">Kuantitas Kargo</label>
                                                <div class="relative">
                                                    <input type="number" id="cargo_quantity" name="cargo_quantity" min="0" step="0.01" 
                                                        x-model="amounts.cargo_quantity" 
                                                        :disabled="scheme === 'undisclosed' 
                                                            ? (String(amounts.freight_owner).length > 0 || String(amounts.freight_shipper).length > 0)
                                                            : String(amounts.freight_total).length > 0"
                                                        class="w-full pr-16 px-3.5 py-2.5 bg-white border @error('cargo_quantity') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all disabled:bg-slate-100 disabled:text-slate-400" 
                                                        placeholder="1000 Ton">
                                                    <span class="pointer-events-none absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-semibold text-slate-400">Ton</span>
                                                </div>
                                                @error('cargo_quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reimbursable Costs -->
                                    <div class="pt-2">
                                        <label for="reimbursable_costs" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Biaya Operasional Agen / Reimbursable (IDR) <span class="text-slate-400 font-normal">(Opsional)</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                            <input type="hidden" name="reimbursable_costs" :value="amounts.reimbursable_costs">
                                            <input type="text" id="reimbursable_costs" inputmode="numeric" 
                                                :value="formatAmount(amounts.reimbursable_costs)" 
                                                @input="updateAmount('reimbursable_costs', $event)" 
                                                class="w-full pl-9 pr-3.5 py-2.5 bg-white border @error('reimbursable_costs') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 focus:border-blue-500 placeholder-slate-300 transition-all" 
                                                placeholder="0">
                                        </div>
                                        @error('reimbursable_costs') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- CARD 2: Profil Pajak & Status Perizinan -->
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 transition-all hover:shadow-md">
                                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4 mb-5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                        2
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-800">Profil Pajak & Perizinan Entitas</h2>
                                        <p class="text-xs text-slate-500">Menentukan jenis PPh dan perlakuan PPN</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <!-- Status Shipowner -->
                                    <div>
                                        <label for="shipowner_status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                            Status Legalitas Shipowner
                                        </label>
                                        <select id="shipowner_status" name="shipowner_status" x-init="$el.value = @js(old('shipowner_status', $result['input']['shipowner_status']))" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            <option value="siupal" @selected(old('shipowner_status', 'siupal') === 'siupal')>Pelayaran Nasional (Pemilik SIUPAL) — PPh 15 (1.2% Final)</option>
                                            <option value="sewa_harta" @selected(old('shipowner_status') === 'sewa_harta')>Sewa Harta / Non-SIUPAL — PPh 23 (2.0%)</option>
                                            <option value="asing_but" @selected(old('shipowner_status') === 'asing_but')>Pelayaran Asing dengan BUT — PPh 15 WPLN (2.64% Final)</option>
                                            <option value="asing_non_but" @selected(old('shipowner_status') === 'asing_non_but')>Pelayaran Asing Tanpa BUT — PPh 26 (20% / Treaty)</option>
                                        </select>
                                        @error('shipowner_status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Grid Toggle Status PKP -->
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                            Status Pengusaha Kena Pajak (PKP)
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <!-- PKP Agen -->
                                            <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-100/80 transition-colors">
                                                <span class="text-xs font-semibold text-slate-700">PKP Agen</span>
                                                <input type="checkbox" id="pkp_agen" name="pkp_agen" value="1" @checked(old('pkp_agen', $result['input']['pkp_agen'])) class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            </label>

                                            <!-- PKP Shipowner -->
                                            <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-100/80 transition-colors">
                                                <span class="text-xs font-semibold text-slate-700">PKP Shipowner</span>
                                                <input type="checkbox" id="pkp_shipowner" name="pkp_shipowner" value="1" @checked(old('pkp_shipowner', $result['input']['pkp_shipowner'])) class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            </label>

                                            <!-- PKP Shipper -->
                                            <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-100/80 transition-colors">
                                                <span class="text-xs font-semibold text-slate-700">PKP Shipper</span>
                                                <input type="checkbox" id="pkp_shipper" name="pkp_shipper" value="1" @checked(old('pkp_shipper', $result['input']['pkp_shipper'])) class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD 3: Skema Split Sub-Broker (Managed by Alpine.js UI State) -->
                            <div x-data="{ isSplitActive: @js((bool) old('subbroker_split_active', $result['input']['subbroker_split_active'])) }" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 transition-all hover:shadow-md">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                            3
                                        </div>
                                        <div>
                                            <h2 class="text-base font-bold text-slate-800">Pembagian Komisi Sub-Broker</h2>
                                            <p class="text-xs text-slate-500">Pembagian komisi dengan co-broker/mitra</p>
                                        </div>
                                    </div>
                                    <!-- Toggle Switch Active -->
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="isSplitActive" name="subbroker_split_active" value="1" @checked(old('subbroker_split_active', $result['input']['subbroker_split_active'])) class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Collapsible Form Section -->
                                <div x-show="isSplitActive" x-transition class="space-y-4 pt-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="split_type" class="block text-xs font-semibold text-slate-700 mb-1">Tipe Split</label>
                                            <select id="split_type" name="split_type" x-init="$el.value = @js(old('split_type', $result['input']['split_type']))" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                                <option value="percentage" @selected(old('split_type', 'percentage') === 'percentage')>Persentase (%)</option>
                                                <option value="fixed" @selected(old('split_type') === 'fixed')>Nominal Fixed (IDR)</option>
                                            </select>
                                            @error('split_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="split_value" class="block text-xs font-semibold text-slate-700 mb-1">Nilai Split</label>
                                            <input type="hidden" name="split_value" :value="amounts.split_value">
                                            <input type="text" id="split_value" inputmode="numeric" :value="formatAmount(amounts.split_value)" @input="updateAmount('split_value', $event)" class="w-full px-3.5 py-2.5 bg-white border @error('split_value') border-red-500 @else border-slate-300 @enderror rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 placeholder-slate-300 transition-all" placeholder="40">
                                            @error('split_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="sub_broker_entity" class="block text-xs font-semibold text-slate-700 mb-1">Status Legalitas Sub-Broker</label>
                                        <select id="sub_broker_entity" name="sub_broker_entity" x-init="$el.value = @js(old('sub_broker_entity', $result['input']['sub_broker_entity']))" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                            <option value="corporate" @selected(old('sub_broker_entity', 'corporate') === 'corporate')>Badan Usaha (PT/CV) — Potong PPh 23 (2%)</option>
                                            <option value="individual" @selected(old('sub_broker_entity') === 'individual')>Perorangan / Individu — Potong PPh 21</option>
                                        </select>
                                        @error('sub_broker_entity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Hitung Profit
                            </button>
                        </form>
                    </div>

                    <!-- KOLOM KANAN: Result Panel / Sticky Dashboard (lg:col-span-5) -->
                    <div class="lg:col-span-5 lg:sticky lg:top-20 space-y-4">
                        @php
                            $pdfPayload = [
                                'input_summary' => $result['input'],
                                'calculation_result' => [
                                    'freight' => $result['freight'],
                                    'cash_flow' => $result['cash_flow'],
                                    'profitability' => $result['profitability'],
                                ],
                                'breakdown_detail' => [
                                    'taxes' => $result['taxes'],
                                    'sub_broker_split' => $result['sub_broker_split'],
                                ],
                            ];
                        @endphp
                        <!-- MAIN HERO RESULT CARD -->
                        <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 rounded-2xl p-6 text-white shadow-xl border border-slate-800 relative overflow-hidden">
                            <!-- Background Decorator -->
                            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

                            <div class="relative z-10">
                                <div class="flex items-center justify-between text-blue-200/80 mb-2">
                                    <span class="text-xs font-semibold uppercase tracking-wider">Hasil Akhir Broker</span>
                                </div>

                                <p class="text-xs text-slate-400">Keuntungan Bersih (Net Profit):</p>
                                <div class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight my-1">
                                    Rp {{ number_format($result['profitability']['net_profit'], 0, ',', '.') }}
                                </div>
                                <p class="text-[11px] text-blue-300/70 mt-1">
                                    *Sudah dipotong PPh Final & memperhitungkan selisih PPN.
                                </p>
                            </div>
                        </div>

                        <!-- BREAKDOWN DETAILS CARD -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 space-y-5">
                            
                            <!-- Section A: Arus Kas -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
                                    <span>A. Ringkasan Arus Kas</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Cashflow</span>
                                </h3>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">(+) Kas Masuk dari Shipper</span>
                                        <span class="font-semibold text-slate-800">Rp {{ number_format($result['cash_flow']['cash_in_from_shipper'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">(-) Biaya Operasional</span>
                                        <span class="font-semibold text-slate-800">Rp {{ number_format($result['cash_flow']['operational_cash_out'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">(-) Kas Keluar ke Shipowner</span>
                                        <span class="font-semibold text-slate-800">Rp {{ number_format($result['cash_flow']['cash_out_to_shipowner'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">(-) Setoran PPN ke Kas Negara</span>
                                        <span class="font-semibold text-slate-800">Rp {{ number_format($result['cash_flow']['vat_payable_to_state'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-slate-100 font-bold text-sm text-slate-900">
                                        <span>Sisa Uang di Rekening</span>
                                        <span class="text-blue-600">Rp {{ number_format($result['cash_flow']['net_cash_received_broker'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">B. Nilai Bruto</h3>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Freight Shipowner</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['freight']['owner_amount'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Freight Shipper</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['freight']['shipper_amount'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Komisi Bruto Broker</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['profitability']['gross_commission'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Section B: Rincian Pemotongan Pajak -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                                    B. Rincian Pemotongan Pajak
                                </h3>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">{{ $result['taxes']['agent_withholding']['type'] }} Dipotong Shipper</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['taxes']['agent_withholding']['amount'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">{{ $result['taxes']['shipowner_withholding']['type'] }} Dipotong Agen atas Shipowner</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['taxes']['shipowner_withholding']['amount'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">PPN Keluaran</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['taxes']['vat']['output_vat'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">PPN Masukan</span>
                                        <span class="font-medium text-slate-800">Rp {{ number_format($result['taxes']['vat']['input_vat'], 0, ',', '.') }}</span>
                                    </div>
                                    @if ($result['sub_broker_split']['active'])
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-600">{{ $result['taxes']['sub_broker_withholding']['type'] }} Sub-Broker</span>
                                            <span class="font-medium text-slate-800">Rp {{ number_format($result['taxes']['sub_broker_withholding']['amount'], 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- PDF Export Button -->
                            <div class="pt-2">
                                <button type="button" x-data="{ loading: false }" @click="loading = true; exportPdf(@js($pdfPayload)).catch(error => alert(error.message)).finally(() => loading = false)" :disabled="loading" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:cursor-wait disabled:opacity-70 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span x-text="loading ? 'Menyiapkan PDF...' : 'Download Laporan PDF'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 4. Halaman Cara Menggunakan -->
        <section id="how-to-use" class="py-16 bg-white border-t border-slate-200/60">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Cara Menggunakan Kalkulator</h2>
                    <p class="mt-2 text-sm text-slate-600">Panduan singkat 4 langkah untuk menghasilkan proyeksi keuntungan & pajak yang akurat.</p>
                </div>

                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Tentukan Skema & Nilai Freight</h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">Pilih <strong>skema keagenan</strong> dan masukkan <strong>freight dasar</strong> milik pemilik kapal (<strong>Shipowner</strong>) dan nilai jual yang kamu tawarkan ke pengirim barang (<strong>Shipper</strong>).</p>
                        </div>
                    </div>

                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Atur Profil Pajak & Status PKP</h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">Pilih legalitas kapal (<strong>SIUPAL</strong>, <strong>Non-SIUPAL</strong>, <strong>BUT</strong>) serta status Pengusaha Kena Pajak (PKP) dari masing-masing pihak.</p>
                        </div>
                    </div>

                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">3</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Aktifkan Sub-Broker Split (Jika Ada)</h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">Jika kamu berbagi komisi dengan mitra lain, aktifkan fitur split dan tentukan potongan PPh 23 / PPh 21 mitra secara otomatis.</p>
                        </div>
                    </div>

                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">4</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Analisis Hasil & Unduh PDF</h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">Klik tombol "Hitung Profit" untuk menghasilkan ringkasan <strong>Net Profit After Tax</strong> dan rincian setoran PPN untuk kamu cetak.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Halaman FAQ (Managed by Alpine.js UI State) -->
        <section id="faq" class="py-16 bg-slate-50 border-t border-slate-200/60" x-data="{ activeFaq: 1 }">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Pertanyaan Umum (FAQ)</h2>
                    <p class="mt-2 text-sm text-slate-600">Penjelasan mendalam seputar pajak & keagenan perkapalan.</p>
                </div>

                <div class="space-y-3">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 1 ? 0 : 1)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Bagaimana konsep alur pajak pada skema Undisclosed Principal?</span>
                            <svg :class="{'rotate-180': activeFaq === 1}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 1" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            Dalam skema <strong>undisclosed principal</strong>, agen menagih nilai <strong>freight</strong> secara penuh (<strong>gross</strong>) kepada Shipper atas nama agen sendiri. Shipper memotong PPh (misal PPh 15) dari total tagihan. Agen lalu meneruskan pokok <strong>freight</strong> beserta bukti potong PPh porsi <strong>Shipowner</strong> kepada pemilik kapal.
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 2 ? 0 : 2)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Bagaimana konsep alur pajak pada skema Pure Brokerage?</span>
                            <svg :class="{'rotate-180': activeFaq === 2}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 2" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            Pada skema <strong>pure brokerage</strong>, tagihan utama (freight) bertransaksi langsung antara Shipper dan Shipowner. Agen hanya menerbitkan tagihan atas <strong>jasa perantara/komisi keagenan</strong> saja, sehingga potong/pungut pajak (PPh & PPN) yang dikelola agen terbatas pada nilai komisi bersihnya.
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 3 ? 0 : 3)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Jenis-jenis pajak apa yang perlu diperhatikan?</span>
                            <svg :class="{'rotate-180': activeFaq === 3}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 3" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            Pajak utama meliputi: <strong>PPh Pasal 15</strong> (jasa pelayaran dalam negeri), <strong>PPh Pasal 23</strong> (jasa keagenan/sewa), <strong>PPh Pasal 26</strong> (transaksi dengan pelayaran luar negeri), serta <strong>PPN 11%</strong> atas penyerahan jasa keagenan atau selisih penagihan.
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 4 ? 0 : 4)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Kapan PPh Pasal 15 vs PPh Pasal 23 diterapkan?</span>
                            <svg :class="{'rotate-180': activeFaq === 4}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 4" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            <strong>PPh Pasal 15</strong> (1.2% Final) dikenakan khusus atas penghasilan jasa pelayaran oleh perusahaan pelayaran nasional yang memiliki SIUPAL. Jika penyedia kapal tidak memiliki SIUPAL (sewa harta biasa/jasa agen), maka dikenakan <strong>PPh Pasal 23</strong> (2%).
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 5 ? 0 : 5)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Bagaimana perhitungan PPN selisih (Kurang Bayar) di Agen?</span>
                            <svg :class="{'rotate-180': activeFaq === 5}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 5" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            Agen memungut PPN Keluaran 11% dari Shipper atas total tagihan dan menerima PPN Masukan 11% dari tagihan Shipowner. Selisih antara PPN Keluaran dan PPN Masukan (setara 11% dari margin komisi bersih) disetorkan oleh Agen ke Kas Negara.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 6. Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
            <p class="font-semibold text-slate-300">NawaTax — Shipbroker Profit & Tax Calculator</p>
            <p>&copy; {{ date('Y') }} NawaTax. Designed for Indonesian Maritime & Logistics Industry. Developed by Talitha Nabila C.</p>
        </div>
    </footer>

</body>
</html>