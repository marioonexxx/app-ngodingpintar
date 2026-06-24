# Rencana Skenario Testing: Registrasi & Upload Produk (Dummy)

Gunakan daftar data dummy di bawah ini untuk memudahkan pelacakan dan penghapusan data setelah testing selesai. Seluruh email menggunakan format `+dummy` agar bisa dibedakan dengan mudah di database jika ingin dihapus massal (`WHERE email LIKE '%+dummy%'`).

## 1. Testing Registrasi 20 Member Biasa (Google Auth)
Diasumsikan proses OAuth Google disimulasikan atau registrasi standar jika Google Auth sulit di-mock. Jika dites manual, buat akun dengan kredensial berikut:

- `[ ]` Member 1: `member1+dummy@gmail.com`
- `[ ]` Member 2: `member2+dummy@gmail.com`
- `[ ]` Member 3: `member3+dummy@gmail.com`
- `[ ]` Member 4: `member4+dummy@gmail.com`
- `[ ]` Member 5: `member5+dummy@gmail.com`
- `[ ]` Member 6: `member6+dummy@gmail.com`
- `[ ]` Member 7: `member7+dummy@gmail.com`
- `[ ]` Member 8: `member8+dummy@gmail.com`
- `[ ]` Member 9: `member9+dummy@gmail.com`
- `[ ]` Member 10: `member10+dummy@gmail.com`
- `[ ]` Member 11: `member11+dummy@gmail.com`
- `[ ]` Member 12: `member12+dummy@gmail.com`
- `[ ]` Member 13: `member13+dummy@gmail.com`
- `[ ]` Member 14: `member14+dummy@gmail.com`
- `[ ]` Member 15: `member15+dummy@gmail.com`
- `[ ]` Member 16: `member16+dummy@gmail.com`
- `[ ]` Member 17: `member17+dummy@gmail.com`
- `[ ]` Member 18: `member18+dummy@gmail.com`
- `[ ]` Member 19: `member19+dummy@gmail.com`
- `[ ]` Member 20: `member20+dummy@gmail.com`

## 2. Testing Registrasi 5 Partnership Vendor (Source Code)
- `[ ]` Vendor 1: `vendor1+dummy@gmail.com` (Toko: Vendor Code A)
- `[ ]` Vendor 2: `vendor2+dummy@gmail.com` (Toko: Vendor Code B)
- `[ ]` Vendor 3: `vendor3+dummy@gmail.com` (Toko: Vendor Code C)
- `[ ]` Vendor 4: `vendor4+dummy@gmail.com` (Toko: Vendor Code D)
- `[ ]` Vendor 5: `vendor5+dummy@gmail.com` (Toko: Vendor Code E)
*→ Pastikan admin melakukan Approve pada kelima pendaftar ini.*

## 3. Testing Registrasi 5 Partnership Mentor (Kelas)
- `[ ]` Mentor 1: `mentor1+dummy@gmail.com` (Keahlian: Web Dev)
- `[ ]` Mentor 2: `mentor2+dummy@gmail.com` (Keahlian: Mobile Dev)
- `[ ]` Mentor 3: `mentor3+dummy@gmail.com` (Keahlian: Data Science)
- `[ ]` Mentor 4: `mentor4+dummy@gmail.com` (Keahlian: UI/UX)
- `[ ]` Mentor 5: `mentor5+dummy@gmail.com` (Keahlian: Cyber Security)
*→ Pastikan admin melakukan Approve pada kelima pendaftar ini.*

## 4. Testing Registrasi 5 Partnership Mentor & Vendor (Keduanya)
Mendaftar dua kali atau di-upgrade oleh admin agar memiliki Role `partner` (keduanya).
- `[ ]` Dual Partner 1: `dual1+dummy@gmail.com`
- `[ ]` Dual Partner 2: `dual2+dummy@gmail.com`
- `[ ]` Dual Partner 3: `dual3+dummy@gmail.com`
- `[ ]` Dual Partner 4: `dual4+dummy@gmail.com`
- `[ ]` Dual Partner 5: `dual5+dummy@gmail.com`
*→ Pastikan admin melakukan Approve pada kelima pendaftar ini.*

## 5. Testing Upload Produk (Masing-Masing 2 Produk)
### A. Produk Vendor (Source Code)
- `[ ]` Vendor 1: "Dummy Web POS" & "Dummy Web Kasir"
- `[ ]` Vendor 2: "Dummy App Android Ojek" & "Dummy Web Profil"
- `[ ]` Vendor 3: "Dummy SIAKAD Web" & "Dummy E-Learning"
- `[ ]` Vendor 4: "Dummy ERP Sistem" & "Dummy CRM"
- `[ ]` Vendor 5: "Dummy IoT Dashboard" & "Dummy Inventory"
*→ Semua produk source code ini harus di-Approve oleh Admin agar tampil.*

### B. Produk Mentor (Kelas)
- `[ ]` Mentor 1: "Dummy Kelas React JS" & "Dummy Kelas Vue JS"
- `[ ]` Mentor 2: "Dummy Kelas Flutter" & "Dummy Kelas Kotlin"
- `[ ]` Mentor 3: "Dummy Kelas Python ML" & "Dummy Kelas Data Analyst"
- `[ ]` Mentor 4: "Dummy Kelas Figma Basic" & "Dummy Kelas UI Advanced"
- `[ ]` Mentor 5: "Dummy Kelas Ethical Hacking" & "Dummy Kelas Network Sec"
*→ Semua produk kelas ini harus di-Approve oleh Admin agar tampil.*

### C. Produk Dual Partner
(Bisa upload masing-masing 1 Kelas & 1 Source Code agar total 2 produk per orang)
- `[ ]` Dual Partner 1: "Dummy Code Template 1" & "Dummy Class Master 1"
- `[ ]` Dual Partner 2: "Dummy Code Template 2" & "Dummy Class Master 2"
- `[ ]` Dual Partner 3: "Dummy Code Template 3" & "Dummy Class Master 3"
- `[ ]` Dual Partner 4: "Dummy Code Template 4" & "Dummy Class Master 4"
- `[ ]` Dual Partner 5: "Dummy Code Template 5" & "Dummy Class Master 5"
*→ Pastikan admin melakukan Approve.*
