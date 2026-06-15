# Dokumentasi Relasi Database (ERD)

Dokumen ini berisi peta struktur tabel dan relasi database pada aplikasi Optik Suci untuk mempermudah rekan *developer* lain memahami alur data yang telah kita bangun.

## 1. Tabel Utama (Katalog & Master Data)

### `users`
Menyimpan data otentikasi (admin/petugas).
- **Relasi**: Belum ada relasi keluar. Berdiri secara mandiri.

### `patients` (Data Pasien)
Menyimpan informasi pelanggan/pasien (Nama, Kategori BPJS/Umum, No HP, Alamat, No Bon).
- **Relasi**: 
  - `hasMany` (Memiliki banyak) transaksi ke tabel `frame_transactions`. (Satu pasien bisa mengambil lebih dari satu frame di transaksi yang berbeda).

### `frame_categories` (Kategori Frame)
Data master (kategori/jenis/merk) yang akan dikaitkan dengan Frame.
- **Relasi**:
  - `hasMany` ke tabel `frames`. (Satu kategori bisa dimiliki banyak frame).

### `frames` (Katalog Frame)
Menyimpan daftar produk frame kacamata beserta harga beli dan harga jualnya.
- **Relasi**:
  - `belongsTo` ke tabel `frame_categories`. (Setiap frame merujuk pada satu kategori).
  - `hasMany` ke tabel `frame_transactions`. (Satu model frame bisa terjual berkali-kali).

### `lens_categories` (Kategori Lensa)
Data master yang mengklasifikasikan jenis lensa.
- **Relasi**:
  - `hasMany` ke tabel `lenses`.

### `lenses` (Katalog Lensa)
Menyimpan daftar tipe lensa, ukuran, index bias, aksesori, dsb.
- **Relasi**:
  - `belongsTo` ke tabel `lens_categories`.

---

## 2. Tabel Transaksional

### `frame_transactions` (Data Frame Keluar)
Tabel *pivot* / transaksional yang menghubungkan siapa pasien yang mengambil frame tertentu. Dibuat untuk mengatasi masalah pencatatan riwayat keluarnya frame.
- **Struktur Kunci:**
  - `patient_id` (Foreign Key -> `patients.id`)
  - `frame_id` (Foreign Key -> `frames.id`)
  - `harga` (Decimal)
  - `tanggal_keluar` (Date)
- **Relasi Utama**:
  - `belongsTo` ke tabel `patients`. (Setiap record transaksi adalah milik satu pasien).
  - `belongsTo` ke tabel `frames`. (Setiap record transaksi mencatat pengeluaran satu frame).

---

## Diagram Relasi Sederhana (Mermaid)

Berikut adalah visualisasi ERD-nya:

```mermaid
erDiagram
    USERS {
        int id PK
        string name
        string email
    }

    PATIENTS {
        int id PK
        string nama
        string kategori
        string no_bon
    }

    FRAME_CATEGORIES {
        int id PK
        string name
    }

    FRAMES {
        int id PK
        string name
        int frame_category_id FK
        decimal harga_jual
    }

    LENS_CATEGORIES {
        int id PK
        string name
    }

    LENSES {
        int id PK
        string ukuran
        int lens_category_id FK
    }

    FRAME_TRANSACTIONS {
        int id PK
        int patient_id FK
        int frame_id FK
        decimal harga
        date tanggal_keluar
    }

    FRAME_CATEGORIES ||--o{ FRAMES : "has many"
    LENS_CATEGORIES ||--o{ LENSES : "has many"
    
    PATIENTS ||--o{ FRAME_TRANSACTIONS : "makes"
    FRAMES ||--o{ FRAME_TRANSACTIONS : "included in"
```

> [!TIP]
> **Catatan Pengembangan:** Jika kedepannya Anda ingin membuat fitur "Data Lensa Keluar", pendekatannya akan sama persis dengan `frame_transactions` (membuat tabel relasi antara `patients` dan `lenses`).
