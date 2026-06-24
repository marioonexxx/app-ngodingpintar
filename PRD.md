# PRD.md - NgodingPintar

## 1. Project Overview

NgodingPintar adalah website toko online untuk menjual produk digital berupa source code aplikasi, template website, software, layanan instalasi, dan coaching 1-on-1. Website utama berada di domain `ngodingpintar.id`.

Platform ini digunakan untuk memasarkan produk digital NgodingPintar, menerima transaksi online, mengelola membership, mengelola pesanan, serta menampilkan testimonial pelanggan pada landing page.

## 2. Tech Stack

* Framework: Laravel 13
* Frontend: Blade + Vue/Inertia
* Database: MySQL
* Authentication: Laravel Breeze + Google OAuth
* Payment Gateway: DOKU / Midtrans / Duitku
* Notification: Email + Telegram Bot
* Hosting Target: Cloud Hosting cPanel

## 3. User Roles

### 3.1 Guest

Guest dapat:

* Melihat landing page
* Melihat daftar produk
* Melihat detail produk
* Melakukan registrasi
* Login menggunakan email atau Google OAuth

### 3.2 Member

Member dapat:

* Login menggunakan email
* Login menggunakan Google OAuth
* Aktivasi akun melalui email verification
* Mengelola profil
* Reset password
* Melihat transaksi aktif
* Melihat history transaksi
* Membeli produk digital
* Memilih add-ons sebelum checkout
* Memberikan testimonial setelah transaksi
* Mengunduh produk setelah pembayaran berhasil

### 3.3 Admin

Admin dapat:

* Mengelola produk
* Mengelola kategori produk
* Mengecek pesanan
* Mengecek transaksi
* Melihat laporan keuangan
* Mengecek dan approve testimonial
* Mengecek membership
* Mengelola data member
* Mengirim/memantau notifikasi transaksi

## 4. Main Features

## 4.1 Landing Page

Landing page memiliki beberapa section:

### Hero Section

Menjelaskan NgodingPintar sebagai penyedia jasa software, produk digital, source code aplikasi, dan coaching 1-on-1.

Konten utama:

* Headline
* Deskripsi singkat
* CTA ke produk
* CTA kontak WhatsApp

### Product Section

Menampilkan produk digital yang tersedia.

Informasi produk:

* Nama produk
* Thumbnail
* Kategori
* Harga
* Deskripsi singkat
* CTA "Buy This Product"
* CTA "View Detail"

### Why Us Section

Berisi alasan memilih NgodingPintar:

* Aman dan terpercaya
* Produk digital siap pakai
* Support instalasi
* Tersedia coaching 1-on-1
* Pembayaran online
* Dokumentasi produk
* Cocok untuk sekolah, kampus, desa, gereja, UMKM, dan organisasi

### Testimonial Section

Menampilkan testimonial pelanggan yang sudah disetujui admin.

Syarat:

* Testimonial hanya tampil jika status approved
* Testimonial berasal dari member yang pernah transaksi
* Admin dapat menolak testimonial yang tidak layak ditampilkan

### Footer

Berisi:

* Email support
* Nomor WhatsApp
* Link produk
* Link kontak
* Copyright NgodingPintar

## 5. Product Module

Produk adalah item utama yang dijual di platform.

### Product Fields

* id
* category_id
* name
* slug
* thumbnail
* short_description
* description
* price
* discount_price
* qty
* product_type
* file_type
* download_link
* demo_link
* documentation_link
* repository_link
* status
* is_featured
* created_at
* updated_at

### Product Type

* Source Code
* Template Website
* Software
* Add-ons
* Service
* Coaching
* Installation Service

### Product Status

* Draft
* Published
* Archived

### Product Rules

* Produk published tampil di landing page dan katalog produk.
* Produk draft hanya terlihat oleh admin.
* Produk digital dapat memiliki link download dari GitHub, Google Drive, atau storage lain.
* Produk dapat memiliki demo URL.
* Produk dapat memiliki dokumentasi/panduan instalasi.
* Produk dapat memiliki stok/qty.
* Produk dapat dijadikan featured product.

## 6. Product Category Module

Kategori produk digunakan untuk mengelompokkan produk.

### Suggested Categories

* Source Code Aplikasi
* Website Sekolah
* Sistem Informasi Akademik
* LMS / E-Learning
* CBT / Ujian Online
* Template Website
* Plugin / Modul Tambahan
* Jasa Instalasi
* Coaching 1-on-1
* Add-ons Hosting

### Category Fields

* id
* name
* slug
* description
* icon
* status
* created_at
* updated_at

## 7. Add-ons Module

Add-ons adalah layanan tambahan yang dapat dipilih member sebelum checkout.

Contoh add-ons:

* Panduan instalasi lokal via Google Meet
* Instalasi aplikasi di hosting
* Setup database dan deployment
* Custom domain
* Konsultasi/coaching 1-on-1
* Bantuan konfigurasi payment gateway

### Add-ons Rules

* Add-ons bisa dikelola sebagai produk dengan kategori "Add-ons".
* Add-ons bisa muncul di halaman checkout.
* Member dapat memilih satu atau lebih add-ons.
* Harga add-ons akan ditambahkan ke total checkout.

## 8. Cart Module

Keranjang belanja digunakan untuk menyimpan produk sebelum checkout.

### Cart Fields

* id
* user_id
* product_id
* qty
* price
* subtotal
* created_at
* updated_at

### Cart Rules

* Member dapat menambahkan produk ke cart.
* Member dapat menghapus item dari cart.
* Member dapat memilih add-ons sebelum checkout.
* Total cart dihitung dari harga produk + add-ons.

## 9. Checkout & Transaction Module

Transaksi digunakan untuk mencatat pembelian member.

### Transaction Fields

* id
* invoice_number
* user_id
* total_amount
* payment_gateway
* payment_method
* payment_status
* order_status
* paid_at
* expired_at
* payment_reference
* payment_url
* notes
* created_at
* updated_at

### Transaction Item Fields

* id
* transaction_id
* product_id
* product_name
* product_price
* qty
* subtotal
* item_type
* created_at
* updated_at

### Payment Status

* pending
* paid
* failed
* expired
* cancelled
* refunded

### Order Status

* waiting_payment
* processing
* completed
* cancelled

### Checkout Flow

1. Member memilih produk.
2. Member menambahkan produk ke cart.
3. Member memilih add-ons jika diperlukan.
4. Member klik checkout.
5. Sistem membuat invoice.
6. Sistem mengirim notifikasi pesanan ke email admin dan Telegram admin.
7. Member memilih metode pembayaran.
8. Sistem menghubungkan transaksi ke payment gateway.
9. Member melakukan pembayaran.
10. Payment gateway mengirim callback/webhook ke sistem.
11. Sistem memperbarui status transaksi.
12. Jika pembayaran berhasil, member mendapat akses download produk.
13. Sistem mengirim email konfirmasi pembayaran ke member.
14. Admin dapat melihat transaksi di dashboard.

## 10. Payment Gateway Integration

Payment gateway yang disiapkan:

* DOKU
* Midtrans
* Duitku

### Payment Gateway Requirements

* Membuat invoice pembayaran.
* Mendapatkan payment URL.
* Menerima webhook/callback.
* Memvalidasi signature webhook.
* Mengubah status transaksi berdasarkan callback.
* Menyimpan payment reference.
* Mendukung QRIS, virtual account, e-wallet, dan bank transfer jika tersedia.

## 11. Notification Module

Sistem mengirim notifikasi melalui:

* Email
* Telegram Bot

### Notification Events

Notifikasi dikirim saat:

* Member melakukan registrasi
* Member melakukan checkout
* Ada transaksi baru
* Pembayaran berhasil
* Pembayaran gagal
* Transaksi expired
* Member mengirim testimonial

### Telegram Notification

Saat ada pesanan baru, sistem mengirim pesan ke Telegram admin:

* Nama member
* Email member
* Nomor invoice
* Produk yang dibeli
* Total nominal
* Status pembayaran
* Link dashboard transaksi

## 12. Testimonial Module

Member dapat memberikan testimonial setelah transaksi.

### Testimonial Fields

* id
* user_id
* transaction_id
* rating
* message
* status
* approved_at
* created_at
* updated_at

### Testimonial Status

* pending
* approved
* rejected

### Testimonial Rules

* Testimonial hanya dapat dibuat oleh member yang sudah pernah transaksi.
* Testimonial default status pending.
* Testimonial hanya tampil di landing page jika status approved.
* Admin dapat approve atau reject testimonial.

## 13. Membership Module

Membership digunakan untuk membedakan akses member.

### Membership Fields

* id
* user_id
* membership_type
* status
* started_at
* expired_at
* created_at
* updated_at

### Membership Type

* Free
* Premium
* Lifetime

### Membership Rules

* Semua user baru default Free.
* Admin dapat mengubah membership user.
* Membership dapat digunakan untuk diskon, akses produk tertentu, atau fitur khusus di masa depan.

## 14. User Profile Module

Member dapat mengelola profil.

### User Fields

* id
* name
* email
* email_verified_at
* google_id
* avatar
* phone
* address
* password
* role
* created_at
* updated_at

### Profile Features

* Update nama
* Update foto profil
* Update nomor WhatsApp
* Update alamat
* Reset password
* Email verification

## 15. Admin Dashboard

Dashboard admin menampilkan:

* Total produk
* Total member
* Total transaksi
* Total transaksi pending
* Total transaksi paid
* Total pendapatan
* Produk terlaris
* Transaksi terbaru
* Testimonial pending
* Membership aktif

## 16. Database Tables

Minimal tabel yang perlu dibuat:

* users
* products
* product_categories
* carts
* cart_items
* transactions
* transaction_items
* testimonials
* memberships
* payment_logs
* notifications

## 17. Security Requirements

* Gunakan email verification.
* Password harus di-hash.
* Payment webhook harus divalidasi signature-nya.
* Link download hanya boleh diakses member yang sudah membayar.
* Admin route dilindungi middleware role admin.
* Member route dilindungi auth middleware.
* File/link produk tidak boleh tampil publik tanpa transaksi paid.
* Gunakan CSRF protection.
* Gunakan validation request untuk semua form.

## 18. Suggested Folder Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Frontend/
│   │   ├── Member/
│   │   └── Admin/
│   ├── Requests/
│   └── Middleware/
├── Models/
├── Services/
│   ├── Payment/
│   ├── Notification/
│   └── Transaction/
├── Notifications/
└── Policies/

resources/
├── views/
│   ├── frontend/
│   ├── member/
│   └── admin/
└── js/
    ├── Pages/
    └── Components/
```

## 19. Development Phases

### Phase 1 - Core System

* Setup Laravel 13
* Auth email
* Google OAuth
* Email verification
* Landing page
* Product listing
* Product detail
* Cart
* Checkout manual
* Admin product management

### Phase 2 - Payment Gateway

* Integrasi DOKU/Midtrans/Duitku
* Callback/webhook
* Payment status update
* Payment logs
* Email notification
* Telegram notification

### Phase 3 - Member Dashboard

* My transactions
* Active transaction
* Transaction history
* Download product
* Profile management
* Reset password

### Phase 4 - Testimonial & Membership

* Testimonial submission
* Admin approval
* Display testimonial on landing page
* Membership management

### Phase 5 - Enhancement

* Coupon/discount
* License key
* Product update history
* Affiliate/reseller
* Subscription billing
* AI product recommendation

## 20. Final Goal

Membangun NgodingPintar sebagai platform toko online produk digital yang profesional, aman, mudah digunakan, dan siap menerima pembayaran online melalui payment gateway Indonesia.
