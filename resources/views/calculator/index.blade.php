<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NawaTax — Shipbroker Profit & Tax Calculator</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-500 selection:text-white">

    <!-- 1. Navigasi Sticky -->
    <nav class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('nawatax.png') }}" alt="NawaTax Logo" class="h-8 w-auto">
                    <span class="text-xl font-extrabold bg-gradient-to-r from-blue-700 to-indigo-800 bg-clip-text text-transparent">
                        NawaTax
                    </span>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#calculator" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">Kalkulator</a>
                    <a href="#how-to-use" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">Cara Pakai</a>
                    <a href="#faq" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">FAQ</a>
                </div>
            </div>
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
                        <form id="calculator-form" method="POST" action="#" class="space-y-6">
                            @csrf

                            <!-- CARD 1: Skema Transaksi & Nilai Freight -->
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 transition-all hover:shadow-md">
                                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4 mb-5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                        1
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-800">Transaksi & Skema Transaksi</h2>
                                        <p class="text-xs text-slate-500">Nilai <strong>freight</strong> dan metode penagihan agen</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- Skema Transaksi -->
                                    <div>
                                        <label for="transaction_scheme" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                            Skema Keagenan
                                        </label>
                                        <select id="transaction_scheme" name="transaction_scheme" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            <option value="undisclosed" selected>Undisclosed Principal (Freight Gross & Tax Pass-Through)</option>
                                            <option value="pure_brokerage">Pure Brokerage / Direct Agency (Hanya Komisi Agen)</option>
                                        </select>
                                    </div>

                                    <!-- Dual Input: Freight Owner vs Freight Shipper -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                        <div>
                                            <label for="freight_owner" class="block text-xs font-semibold text-slate-700 mb-1">
                                                Freight Dasar Shipowner (IDR)
                                            </label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                <input type="text" id="freight_owner" name="freight_owner" class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-300 transition-all" placeholder="100.000.000">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="freight_shipper" class="block text-xs font-semibold text-slate-700 mb-1">
                                                Harga Jual ke Shipper (IDR)
                                            </label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                                                <input type="text" id="freight_shipper" name="freight_shipper" class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-300 transition-all" placeholder="110.000.000">
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
                                            <input type="text" id="reimbursable_costs" name="reimbursable_costs" class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-300 transition-all" placeholder="0">
                                        </div>
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
                                        <select id="shipowner_status" name="shipowner_status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            <option value="siupal" selected>Pelayaran Nasional (Pemilik SIUPAL) — PPh 15 (1.2% Final)</option>
                                            <option value="non_siupal">Sewa Harta / Non-SIUPAL — PPh 23 (2.0%)</option>
                                            <option value="foreign_but">Pelayaran Asing dengan BUT — PPh 15 WPLN (2.64% Final)</option>
                                            <option value="foreign_non_but">Pelayaran Asing Tanpa BUT — PPh 26 (20% / Treaty)</option>
                                        </select>
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
                                                <input type="checkbox" id="pkp_agen" name="pkp_agen" value="1" checked class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            </label>

                                            <!-- PKP Shipowner -->
                                            <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-100/80 transition-colors">
                                                <span class="text-xs font-semibold text-slate-700">PKP Shipowner</span>
                                                <input type="checkbox" id="pkp_shipowner" name="pkp_shipowner" value="1" checked class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            </label>

                                            <!-- PKP Shipper -->
                                            <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-100/80 transition-colors">
                                                <span class="text-xs font-semibold text-slate-700">PKP Shipper</span>
                                                <input type="checkbox" id="pkp_shipper" name="pkp_shipper" value="1" checked class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD 3: Skema Split Sub-Broker (Managed by Alpine.js UI State) -->
                            <div x-data="{ isSplitActive: false }" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 transition-all hover:shadow-md">
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
                                        <input type="checkbox" x-model="isSplitActive" name="subbroker_split_active" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Collapsible Form Section -->
                                <div x-show="isSplitActive" x-transition class="space-y-4 pt-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="split_type" class="block text-xs font-semibold text-slate-700 mb-1">Tipe Split</label>
                                            <select id="split_type" name="split_type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                                <option value="percentage">Persentase (%)</option>
                                                <option value="fixed">Nominal Fixed (IDR)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="split_value" class="block text-xs font-semibold text-slate-700 mb-1">Nilai Split</label>
                                            <input type="text" id="split_value" name="split_value" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 placeholder-slate-300 transition-all" placeholder="40">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="sub_broker_entity" class="block text-xs font-semibold text-slate-700 mb-1">Status Legalitas Sub-Broker</label>
                                        <select id="sub_broker_entity" name="sub_broker_entity" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                            <option value="corporate">Badan Usaha (PT/CV) — Potong PPh 23 (2%)</option>
                                            <option value="individual">Perorangan / Individu — Potong PPh 21</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <!-- KOLOM KANAN: Live Result Panel / Sticky Dashboard (lg:col-span-5) -->
                    <div class="lg:col-span-5 lg:sticky lg:top-20 space-y-4">
                        
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
                                    Rp 9.880.000
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
                                        <span class="font-semibold text-slate-800">Rp 120.780.000</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">(-) Kas Keluar ke Shipowner</span>
                                        <span class="font-semibold text-slate-800">Rp 109.800.000</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">(-) Setoran PPN ke Kas Negara</span>
                                        <span class="font-semibold text-slate-800">Rp 1.100.000</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-slate-100 font-bold text-sm text-slate-900">
                                        <span>Sisa Uang di Rekening</span>
                                        <span class="text-blue-600">Rp 9.880.000</span>
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
                                        <span class="text-slate-600">PPh Dipotong Shipper (1.2%)</span>
                                        <span class="font-medium text-slate-800">Rp 1.320.000</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">PPh Diteruskan ke Owner</span>
                                        <span class="font-medium text-slate-800">Rp 1.200.000</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">PPN Keluaran (11%)</span>
                                        <span class="font-medium text-slate-800">Rp 12.100.000</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">PPN Masukan (11%)</span>
                                        <span class="font-medium text-slate-800">Rp 11.000.000</span>
                                    </div>
                                </div>
                            </div>

                            <!-- PDF Export Button -->
                            <div class="pt-2">
                                <button type="button" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Download Laporan PDF</span>
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
                            <h3 class="text-sm font-bold text-slate-800">Tentukan Nilai Freight</h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">Masukkan <strong>freight dasar</strong> milik pemilik kapal (<strong>Shipowner</strong>) dan nilai jual yang kamu tawarkan ke pengirim barang (<strong>Shipper</strong>).</p>
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
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">Sistem akan secara langsung memperbarui ringkasan <strong>Net Profit After Tax</strong> dan rincian setoran PPN untuk kamu cetak.</p>
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
                            Dalam skema <strong>undisclosed principal</strong>, agen menagih nilai <strong>freight</strong> secara penuh (<strong>gross</strong>) kepada Shipper atas nama agen sendiri. Shipper akan memotong PPh (misal PPh 15) dari total nilai tagihan. Agen kemudian meneruskan pokok <strong>freight</strong> beserta bukti potong PPh porsi <strong>Shipowner</strong> kepada pemilik kapal.
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 2 ? 0 : 2)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Kapan PPh Pasal 15 vs PPh Pasal 23 diterapkan?</span>
                            <svg :class="{'rotate-180': activeFaq === 2}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 2" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            PPh Pasal 15 (1.2% Final) dikenakan khusus atas penghasilan jasa pelayaran oleh perusahaan pelayaran nasional yang memiliki SIUPAL. Jika penyedia kapal tidak memiliki SIUPAL (sewa harta biasa), maka transaksi dikenakan PPh Pasal 23 (2%).
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                        <button @click="activeFaq = (activeFaq === 3 ? 0 : 3)" type="button" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-800 hover:bg-slate-50 transition-colors">
                            <span>Bagaimana perhitungan PPN selisih (Kurang Bayar) di Agen?</span>
                            <svg :class="{'rotate-180': activeFaq === 3}" class="w-4 h-4 text-slate-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === 3" x-transition class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            Agen memungut PPN Keluaran 11% dari Shipper atas total tagihan dan menerima PPN Masukan 11% dari tagihan Shipowner. Selisih antara PPN Keluaran dan PPN Masukan (yang secara efektif setara dengan 11% dari margin komisi bersih agen) disetorkan oleh Agen ke Kas Negara.
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
            <p>&copy; {{ date('Y') }} NawaTax. Designed for Indonesian Maritime & Logistics Industry.</p>
        </div>
    </footer>

</body>
</html>