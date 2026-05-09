# 🛒 Sistem POS Toko Sopia

Sistem **Point of Sale (POS)** berbasis web untuk Toko Sopia dirancang untuk mempermudah proses transaksi penjualan, manajemen stok barang, dan pelaporan penjualan secara real-time. Dibangun menggunakan **PHP native** dengan koneksi database menggunakan **PDO (PHP Data Objects)** untuk keamanan dan keandalan data.

Fitur utama aplikasi ini meliputi:
**Kasir / Checkout** yakni proses transaksi penjualan dengan pemilihan barang, metode pembayaran, dan kalkulasi kembalian otomatis.
**Laporan Penjualan** yakni grafik penjualan harian, bulanan, dan 10 produk terlaris menggunakan Chart.js.

---

## 📁 Struktur Direktori

```
sistem_POS_toko_sopia/
├── config/
│   └── database.php          # Konfigurasi koneksi PDO ke database MySQL
├── process/
│   ├── cart_process.php      # Proses tambah/hapus item keranjang belanja
│   ├── checkout.php          # Proses checkout: transaksi, detail, stok, pembayaran
│   ├── grafik.php            # API data grafik penjualan (JSON)
│   └── stock_process.php     # Proses update/tambah stok barang
├── public/
│   ├── index.php             # Halaman utama kasir (POS)
│   └── laporan.php           # Halaman laporan & grafik penjualan
└── Readme.md
```

---

## 🗄️ Struktur Tabel Utama

### 1. `pelanggan`
Menyimpan data pelanggan yang melakukan transaksi.

| Kolom          | Tipe Data     | Keterangan                  |
|---------------|---------------|-----------------------------|
| `id_pelanggan`| INT (PK, AI)  | ID unik pelanggan           |
| `nama_depan`  | VARCHAR       | Nama depan pelanggan        |
| `nama_belakang`| VARCHAR      | Nama belakang pelanggan     |


---

### 2. `kasir`
Menyimpan data akun kasir yang mengoperasikan sistem POS.

| Kolom          | Tipe Data    | Keterangan                  |
|----------------|--------------|-----------------------------|
| `id_kasir`     | INT (PK, AI) | ID unik kasir               |
| `username`     | VARCHAR      | Username login kasir        |
| `password`     | VARCHAR      | Password (terenkripsi)      |
| `nama_depan`   | VARCHAR      | Nama depan kasir            |
| `nama_belakang`| VARCHAR      | Nama belakang kasir         |

---

### 3. `kategori`
Menyimpan kategori/jenis pengelompokan barang.

| Kolom          | Tipe Data    | Keterangan                  |
|----------------|--------------|-----------------------------|
| `id_kategori`  | INT (PK, AI) | ID unik kategori            |
| `nama_kategori`| VARCHAR      | Nama kategori barang        |

---

### 4. `barang`
Menyimpan data produk/barang yang dijual di toko.

| Kolom        | Tipe Data    | Keterangan                         |
|--------------|--------------|------------------------------------|
| `id_barang`  | INT (PK, AI) | ID unik barang                     |
| `id_kategori`| INT (FK)     | Relasi ke tabel `kategori`         |
| `nama_barang`| VARCHAR      | Nama produk                        |
| `harga`      | INT          | Harga jual per satuan              |
| `stok`       | INT          | Jumlah stok tersedia               |

---

### 5. `transaksi`
Menyimpan data header setiap transaksi penjualan.

| Kolom              | Tipe Data    | Keterangan                         |
|--------------------|--------------|------------------------------------|
| `id_transaksi`     | INT (PK, AI) | ID unik transaksi                  |
| `id_pelanggan`     | INT (FK)     | Relasi ke tabel `pelanggan`        |
| `id_kasir`         | INT (FK)     | Relasi ke tabel `kasir`            |
| `tanggal`          | DATETIME     | Tanggal dan waktu transaksi        |
| `total_pembayaran` | INT          | Total harga seluruh item           |

---

### 6. `detail_transaksi`
Menyimpan rincian barang per transaksi (relasi many-to-many antara transaksi dan barang).

| Kolom                  | Tipe Data    | Keterangan                         |
|------------------------|--------------|------------------------------------|
| `id_detail_transaksi`  | INT (PK, AI) | ID unik detail transaksi           |
| `id_transaksi`         | INT (FK)     | Relasi ke tabel `transaksi`        |
| `id_barang`            | INT (FK)     | Relasi ke tabel `barang`           |
| `jumlah`               | INT          | Jumlah barang yang dibeli          |

---

### 7. `pembayaran`
Menyimpan informasi pembayaran dari setiap transaksi.

| Kolom                | Tipe Data    | Keterangan                              |
|----------------------|--------------|-----------------------------------------|
| `id_pembayaran`      | INT (PK, AI) | ID unik pembayaran                      |
| `id_transaksi`       | INT (FK)     | Relasi ke tabel `transaksi`             |
| `jumlah_bayar`       | INT          | Nominal uang yang dibayarkan pelanggan  |
| `metode_pembayaran`  | VARCHAR      | Metode bayar: `Tunai`, `QRIS`, dll.    |

---

## ⚙️ Cara Menjalankan Aplikasi

### Prasyarat
Pastikan perangkat kamu sudah terinstal:
- [XAMPP](https://www.apachefriends.org/) (versi dengan PHP 7.4+ dan MySQL/MariaDB)
- Browser modern (Chrome, Firefox, Edge, dll.)

---

### Langkah 1 — Clone / Salin Proyek

Tempatkan folder proyek di dalam direktori `htdocs` XAMPP:

```
C:\xampp\htdocs\sistem_POS_toko_sopia\
```

---

### Langkah 2 — Jalankan XAMPP

1. Buka **XAMPP Control Panel**.
2. Klik **Start** pada modul **Apache** dan **MySQL**.
3. Pastikan keduanya berstatus **Running** (hijau).

> ⚠️ Jika port MySQL bukan 3306 (default), sesuaikan nilai `$port` di file `config/database.php`.

---

### Langkah 3 — Buat Database

1. Buka browser, akses **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Buat database baru dengan nama: `toko_sopia`
3. Import struktur tabel sesuai skema di atas (atau gunakan file `.sql` jika tersedia).

---

### Langkah 4 — Konfigurasi Koneksi Database

Buka file `config/database.php` dan sesuaikan konfigurasi jika perlu:

```php
$host = 'localhost';
$port = '3307';      // Sesuaikan dengan port MySQL kamu (default: 3306)
$db   = 'toko_sopia';
$user = 'root';
$pass = '';           // Isi jika MySQL kamu menggunakan password
```

---

### Langkah 5 — Akses Aplikasi

Buka browser dan akses:

| Halaman          | URL                                                              |
|------------------|------------------------------------------------------------------|
| 🛒 Halaman Kasir  | http://localhost/sistem_POS_toko_sopia/public/index.php          |
| 📊 Laporan        | http://localhost/sistem_POS_toko_sopia/public/laporan.php        |

---

## 🛠️ Teknologi yang Digunakan

| Teknologi   | Kegunaan                                      |
|-------------|-----------------------------------------------|
| PHP Native  | Backend logic dan pemrosesan transaksi         |
| PDO         | Koneksi database yang aman (prepared statement)|
| MySQL       | Penyimpanan data transaksi dan produk          |
| Chart.js    | Visualisasi grafik laporan penjualan           |
| HTML/CSS/JS | Tampilan antarmuka kasir dan laporan           |

---
