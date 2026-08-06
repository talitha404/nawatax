# 📖 NawaTax — Shipbroker Profit & Tax Calculator

**NawaTax** adalah aplikasi web untuk menghitung **profit & pajak shipbroker** di Indonesia.  
Dibangun dengan stack modern **Laravel**, **Blade**, dan **Alpine.js**, NawaTax menghadirkan kalkulasi finansial yang **real-time, presisi tinggi, dan sesuai regulasi perpajakan**.

---

## 🌟 Nilai Unik Produk
Perhitungan pajak *freight* dan *charter* kapal di Indonesia memiliki aturan yang kompleks.  
NawaTax hadir untuk:
- Menghilangkan kebingungan manual.  
- Meminimalisir risiko kesalahan hitung.  
- Mengotomatisasi aturan perpajakan sesuai regulasi.  
- Mengelola skema *commission split* antar-broker dengan akurat.  

---

## 🎯 Tujuan Utama
NawaTax membantu *shipbroker*, pemilik kapal, dan praktisi keuangan maritim dalam menentukan:
1. **Brokerage Fees** & komisi bersih.  
2. **Implikasi Pajak Transaksi** (PPh 15, PPh 23, PPh 26).  
3. **PPN** sesuai status PKP.  
4. **Mekanisme Pajak Sub-Broker** tanpa membebani broker utama.  
5. **Biaya Operasional & Net Profit**.  

---

## 🚀 Fitur Utama

- 🧮 **Comprehensive Financial Calculator**  
  Input transaksi → hasil *cash flow*, rincian pajak, dan profitabilitas lengkap.  

- ⚖️ **Dynamic Tax Handling**  
  Mendukung skenario pajak Indonesia:  
  - PPh 15 (SIUPAL)  
  - PPh 23 (Jasa Perantara)  
  - PPh 15 WPLN (BUT)  
  - PPh 26 (Non-Resident)  
  - PPN berbasis status PKP  

- 🤝 **Inter-Broker Split Management**  
  Atur pembagian komisi dengan sub-broker, otomatis hitung PPh 23/21 sesuai entitas.  

- ⚡ **Real-Time Interactive Results**  
  Didukung **Alpine.js**, hasil perhitungan langsung berubah tanpa reload.  

- 📄 **Professional Document Generator**  
  Ekspor hasil ke PDF rapi & siap presentasi.  

- 🔓 **Guest-Friendly Access**  
  Bisa digunakan tanpa login, cepat & praktis.  

---

## 🛠️ Tech Stack
- **Backend**: PHP 8.x, [Laravel](https://laravel.com)  
- **Frontend**: Blade, [Alpine.js](https://alpinejs.dev)  
- **CSS**: Tailwind CSS  
- **PDF Engine**: Library kompatibel Laravel  

---

## 💻 Panduan Instalasi Lokal

1. **Clone Repository**
   ```bash
   git clone https://github.com/talitha404/nawatax.git
   cd nawatax
   ```

2. **Instalasi Dependensi**
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

Akses aplikasi di: **http://localhost:8000**

---