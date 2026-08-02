```markdown
<p align="center">
  <h1 align="center">NawaTax</h1>
  <p align="center">
    <strong>Kalkulator Keuntungan & Pajak Terintegrasi untuk Industri Shipbroker Indonesia</strong>
  </p>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Status Build"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Unduhan"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Versi Stabil Terbaru"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="Lisensi"></a>
</p>

---

## 📌 Tentang NawaTax

**NawaTax** adalah aplikasi web *Shipbroker Profit & Tax Calculator* yang dirancang khusus untuk memecahkan kompleksitas perhitungan finansial dan perpajakan pada transaksi perkapalan (*shipbroking*) di Indonesia. Built di atas stack modern **Laravel**, **Blade**, dan **Alpine.js**, NawaTax memberikan kepastian angka secara real-time dan presisi tinggi.

### 🌟 Nilai Unik Produk (Value Proposition)
Perhitungan pajak *freight* dan *charter* kapal di Indonesia memiliki aturan perpajakan yang sangat spesifik tergantung pada status bendera kapal, entitas pemilik, dan skema keagenan. NawaTax mengeliminasi kebingungan manual dan risiko kesalahan perhitungan dengan otomatisasi aturan perpajakan yang sesuai regulasi (*tax compliance*), sekaligus memperhitungkan skema *commission split* antar-broker secara akurat.

---

## 🎯 Tujuan Utama

NawaTax bertujuan untuk memberikan transparansi finansial dan kemudahan kalkulasi bagi para *shipbroker*, pemilik kapal, dan praktisi keuangan maritim dalam menentukan:
1. **Brokerage Fees** & Komisi bersih.
2. **Implikasi Pajak Transaksi** (PPh 15, PPh 23, PPh 26) berdasarkan status kapal dan regulasi berjalan.
3. **Pajak Pertambahan Nilai (PPN)** sesuai status Pengusaha Kena Pajak (PKP).
4. **Mekanisme Pajak Sub-Broker** tanpa membebani broker utama.
5. **Biaya Operasional & Estimasi Keuntungan Bersih (Net Profit)**.

---

## 🚀 Fitur Pembeda & Keunggulan Utama

- 🧮 **Comprehensive Financial Calculator**  
  Cukup masukkan nilai transaksi, profil perpajakan, dan skema pembagian komisi untuk mendapatkan ringkasan *cash flow*, rincian pajak, dan laporan profitabilitas secara menyeluruh.

- ⚖️ **Dynamic Indonesian Tax Regulations Handling**  
  Mendukung berbagai skenario pajak perkapalan Indonesia secara otomatis:
  - **PPh 15** (Pelayaran Nasional / SIUPAL)
  - **PPh 23** (Jasa Perantara / non-SIUPAL)
  - **PPh 15 WPLN** (Pelayaran Asing dengan BUT)
  - **PPh 26** (Wajib Pajak Luar Negeri / Non-Resident)
  - Perhitungan **PPN** kontekstual berbasis status PKP.

- 🤝 **Inter-Broker Split Management**  
  Atur pembagian komisi dengan sub-broker/mitra secara akurat. Sistem menghitung pemotongan pajak terkait (**PPh 23** untuk entitas badan, **PPh 21** untuk perorangan) secara otomatis sehingga pemotongan pajak sub-broker terisolasi dan tidak membebani komisi broker utama.

- ⚡ **Real-Time Interactive Results**  
  Didukung oleh **Alpine.js**, seluruh hasil perhitungan, *breakdown* pajak, dan *net profit* diperbarui secara instan saat parameter diubah tanpa perlunya *page reload*.

- 📄 **Professional Document Generator**  
  Ekspor hasil kalkulasi ke dalam format ringkasan PDF yang rapi dan profesional, siap digunakan untuk arsip internal maupun presentasi langsung ke klien.

- 🔓 **Guest-Friendly Access**  
  Dapat digunakan langsung tanpa perlu proses *login/authentication*, memberikan akses cepat bagi developer maupun pengguna akhir yang membutuhkan kalkulasi instan.

---

## 🛠️ Stack Teknologi

Aplikasi ini dibangun menggunakan *tech stack* Laravel yang ringkas dan performan:
- **Backend**: PHP 8.x, [Laravel Framework](https://laravel.com)
- **Frontend**: Blade Templating, [Alpine.js](https://alpinejs.dev) (Reaktivitas UI)
- **CSS Framework**: Tailwind CSS
- **PDF Engine**: PDF Generation Library (Laravel-compatible)

---

## 💻 Panduan Instalasi Lokal (Developer Quickstart)

Untuk menjalankan proyek NawaTax di lingkungan lokal Anda:

1. **Cloning Repository**
   ```bash
   git clone [https://github.com/username/nawatax.git](https://github.com/username/nawatax.git)
   cd nawatax

```

2. **Instalasi Dependensi PHP & JavaScript**
```bash
composer install
npm install && npm run dev

```


3. **Konfigurasi Environment**
```bash
cp .env.example .env
php artisan key:generate

```


4. **Jalankan Development Server**
```bash
php artisan serve

```

Aplikasi dapat diakses melalui `http://localhost:8000`.