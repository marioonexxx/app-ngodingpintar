# Rencana Skenario Testing: Transaksi Transfer Bank

Gunakan member dummy yang telah dibuat di pengujian sebelumnya untuk menyimulasikan pembelian semua produk milik partner. Semua pembayaran disimulasikan menggunakan opsi "Transfer Bank" (Manual) agar Admin bisa melakukan validasi.

## 1. Persiapan Testing
- `[ ]` Pastikan seluruh 20 produk dummy dari vendor & mentor (total 10 Source Code & 10 Kelas) di `todo-test-registrasi.md` sudah berstatus **Active** di Dashboard Admin.
- `[ ]` Pastikan Admin sudah mengatur nomor rekening tujuan (Admin Dashboard > Pengaturan Rekening Bank) untuk menerima transfer manual.

## 2. Simulasi Transaksi (Oleh Member)
Login bergantian menggunakan beberapa akun member dummy.

### Member 1-5 (Membeli Produk Vendor Source Code)
- `[ ]` Member 1 membeli "Dummy Web POS" (Vendor 1). Checkout dengan opsi Transfer Bank. Upload bukti transfer palsu.
- `[ ]` Member 2 membeli "Dummy Web Kasir" (Vendor 1). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 3 membeli "Dummy App Android Ojek" (Vendor 2). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 4 membeli "Dummy SIAKAD Web" (Vendor 3). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 5 membeli "Dummy IoT Dashboard" (Vendor 5). Checkout via Transfer Bank, upload bukti transfer.

### Member 6-10 (Membeli Produk Kelas Mentor)
- `[ ]` Member 6 mendaftar "Dummy Kelas React JS" (Mentor 1). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 7 mendaftar "Dummy Kelas Flutter" (Mentor 2). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 8 mendaftar "Dummy Kelas Data Analyst" (Mentor 3). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 9 mendaftar "Dummy Kelas Figma Basic" (Mentor 4). Checkout via Transfer Bank, upload bukti transfer.
- `[ ]` Member 10 mendaftar "Dummy Kelas Ethical Hacking" (Mentor 5). Checkout via Transfer Bank, upload bukti transfer.

### Member 11-15 (Membeli Produk Dual Partner)
- `[ ]` Member 11 membeli "Dummy Code Template 1" (Dual 1). Transfer Bank, upload bukti.
- `[ ]` Member 12 mendaftar "Dummy Class Master 2" (Dual 2). Transfer Bank, upload bukti.
- `[ ]` Member 13 memborong 2 produk sekaligus ("Dummy Code Template 3" & "Dummy Class Master 3" dari Dual 3). Transfer Bank, upload bukti.
- `[ ]` Member 14 membeli produk Dual 4. Transfer Bank, upload bukti.
- `[ ]` Member 15 membeli produk Dual 5. Transfer Bank, upload bukti.

## 3. Validasi Transaksi Oleh Admin
Login sebagai Admin untuk memproses transaksi di atas.

- `[ ]` Buka menu **Daftar Transaksi** di Dashboard Admin.
- `[ ]` Cek satu per satu transaksi yang masuk dengan status **Waiting/Pending** atau bukti transfer yang menunggu persetujuan.
- `[ ]` Lakukan klik **Verify Payment / Setujui** untuk **semua** transaksi di atas sehingga status berubah menjadi **Paid**.

## 4. Pengecekan Hasil (Penting)
Setelah Admin memvalidasi transaksi, verifikasi hal-hal berikut:

- `[ ]` **Sisi Member**: Pastikan kelas muncul di menu "Kelas Saya" dan Source Code muncul di "Daftar Pembelian / Download" milik member yang bersangkutan.
- `[ ]` **Sisi Partner (Vendor & Mentor)**: Login sebagai salah satu vendor/mentor (misal `mentor1+dummy@gmail.com`). 
    - Cek Dashboard Partner, pastikan **Saldo Pendapatan** bertambah sebesar (Harga Produk - Diskon Promo) dikurangi Platform Fee 20%.
    - Buka halaman **Peserta Kelas**, pastikan `member6+dummy@gmail.com` tercatat di tabel peserta dengan status lunas.
- `[ ]` Coba lakukan **Pencairan Saldo** (minimal Rp 100.000) oleh salah satu partner, dan lihat apakah muncul di menu *Pencairan Partner* pada Dashboard Admin.
