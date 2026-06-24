<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ──────────────────────────────────────────
            // 1. Website & Landing Page
            // ──────────────────────────────────────────
            'website-landing-page' => [
                [
                    'name' => 'SchoolWeb - Website Sekolah Modern',
                    'slug' => 'schoolweb-website-sekolah-modern',
                    'short_description' => 'Website profil sekolah lengkap dengan PPDB online, agenda, dan galeri.',
                    'description' => 'SchoolWeb aealah template website sekolah modern yang dibuat dengan Laravel & Vue.js. Dilengkapi fitur PPDB online, manajemen agenda sekolah, galeri foto/video, profil guru & staff, berita sekolah, dan halaman kontak. Responsive design dengan tampilan profesional.',
                    'price' => 350000,
                    'sale_price' => 249000,
                    'demo_url' => 'https://demo.ngodingpintar.id/schoolweb',
                ],
                [
                    'name' => 'DesaDigital - Website Desa/Kelurahan',
                    'slug' => 'desadigital-website-desa',
                    'short_description' => 'Website resmi desa dengan layanan surat online dan data kependudukan.',
                    'description' => 'DesaDigital membantu pemerintah desa/kelurahan memiliki website resmi modern. Fitur: profil desa, layanan surat online, data kependudukan, berita desa, galeri kegiatan, APBDes transparan, dan peta lokasi. Dibangun dengan Laravel 11 & Inertia.js.',
                    'price' => 400000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/desadigital',
                ],
                [
                    'name' => 'ChurchSite - Website Gereja',
                    'slug' => 'churchsite-website-gereja',
                    'short_description' => 'Website gereja elegan dengan jadwal ibadah, renungan, dan donasi online.',
                    'description' => 'ChurchSite aealah template website gereja yang elegan dan spiritual. Fitur lengkap: jadwal ibadah, renungan harian, streaming ibadah, manajemen jemaat, donasi/persembahan online, galeri kegiatan, dan blog rohani. Multi-bahasa ready.',
                    'price' => 300000,
                    'sale_price' => 199000,
                    'demo_url' => 'https://demo.ngodingpintar.id/churchsite',
                ],
                [
                    'name' => 'BizLanding - Landing Page UMKM',
                    'slug' => 'bizlaneing-landing-page-umkm',
                    'short_description' => 'Landing page konversi tinggi untuk UMKM dengan WhatsApp integration.',
                    'description' => 'BizLanding aealah landing page yang dioptimalkan untuk konversi penjualan UMKM. Fitur: hero section menarik, katalog produk, testimoni pelanggan, FAQ accordion, WhatsApp floating button, Google Maps embed, SEO-optimized, dan fast loading. Cocok untuk toko online, jasa, F&B.',
                    'price' => 200000,
                    'sale_price' => 149000,
                    'demo_url' => 'https://demo.ngodingpintar.id/bizlaneing',
                ],
            ],

            // ──────────────────────────────────────────
            // 2. Web Application
            // ──────────────────────────────────────────
            'web-application' => [
                [
                    'name' => 'SiAkademik - Sistem Informasi Akademik',
                    'slug' => 'siakademik-sistem-informasi-akademik',
                    'short_description' => 'Sistem akademik lengkap untuk universitas dan sekolah tinggi.',
                    'description' => 'SiAkademik aealah sistem informasi akademik komprehensif. Fitur: KRS/KHS online, jadwal kuliah, manajemen dosen & mahasiswa, absensi, nilai & transkrip, pembayaran SPP, perpustakaan digital, e-learning dasar. Dibangun dengan Laravel 11, Vue 3, dan MySQL.',
                    'price' => 750000,
                    'sale_price' => 599000,
                    'demo_url' => 'https://demo.ngodingpintar.id/siakademik',
                ],
                [
                    'name' => 'InventoryPro - Manajemen Stok Barang',
                    'slug' => 'inventorypro-manajemen-stok-barang',
                    'short_description' => 'Aplikasi inventaris barang dengan barcode scanner dan laporan stok.',
                    'description' => 'InventoryPro membantu bisnis mengelola stok barang secara efisien. Fitur: manajemen produk & kategori, barcode/QR code scanner, stok masuk/keluar, transfer antar gudang, stok opname, supplier management, laporan stok & pergerakan, alert stok minimum, export PDF/Excel.',
                    'price' => 500000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/inventorypro',
                ],
                [
                    'name' => 'HRMSystem - Sistem HRD & Payroll',
                    'slug' => 'hrmsystem-sistem-hrd-payroll',
                    'short_description' => 'Aplikasi HRD lengkap dengan absensi, cuti, dan penggajian otomatis.',
                    'description' => 'HRMSystem aealah solusi manajemen SDM terintegrasi. Fitur: database karyawan, absensi (fingerprint/GPS), manajemen cuti & izin, penggajian otomatis, slip gaji digital, PPh 21, BPJS, KPI & penilaian kinerja, recruitment, training management. Multi-cabang support.',
                    'price' => 850000,
                    'sale_price' => 699000,
                    'demo_url' => 'https://demo.ngodingpintar.id/hrmsystem',
                ],
                [
                    'name' => 'KlinikSehat - Sistem Informasi Klinik',
                    'slug' => 'kliniksehat-sistem-informasi-klinik',
                    'short_description' => 'Aplikasi manajemen klinik dengan rekam medis elektronik dan antrian.',
                    'description' => 'KlinikSehat membantu klinik dan praktek dokter mengelola operasional harian. Fitur: pendaftaran pasien, rekam medis elektronik (EMR), antrian online, jadwal dokter, resep & farmasi, kasir/billing, laporan kunjungan, integrasi BPJS Kesehatan. HIPAA-aware design.',
                    'price' => 650000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/kliniksehat',
                ],
            ],

            // ──────────────────────────────────────────
            // 3. Web SaaS
            // ──────────────────────────────────────────
            'web-saas' => [
                [
                    'name' => 'InvoiceKit - SaaS Invoice & Billing',
                    'slug' => 'invoicekit-saas-invoice-billing',
                    'short_description' => 'Platform SaaS pembuatan invoice, quotation, dan billing otomatis.',
                    'description' => 'InvoiceKit aealah platform SaaS untuk membuat invoice profesional. Fitur: multi-tenant, invoice & quotation builder, recurring billing, payment gateway integration (Midtrans/Xeneit), client portal, expense tracking, laporan keuangan, white-label branding. Subscription-based.',
                    'price' => 950000,
                    'sale_price' => 799000,
                    'demo_url' => 'https://demo.ngodingpintar.id/invoicekit',
                ],
                [
                    'name' => 'FormFlow - SaaS Form Builder',
                    'slug' => 'formflow-saas-form-builder',
                    'short_description' => 'Platform drag-and-drop form builder dengan analytics response.',
                    'description' => 'FormFlow aealah SaaS form builder yang powerful. Fitur: drag-and-drop builder, 20+ field types, conditional logic, file upload, multi-page form, response analytics & charts, webhook integration, email notification, embed & share, API access. Multi-tenant architecture.',
                    'price' => 700000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/formflow',
                ],
                [
                    'name' => 'ProjectHub - SaaS Project Management',
                    'slug' => 'projecthub-saas-project-management',
                    'short_description' => 'Aplikasi manajemen proyek kolaboratif dengan Kanban board dan Gantt chart.',
                    'description' => 'ProjectHub aealah platform project management modern. Fitur: Kanban board, Gantt chart, task management, time tracking, team collaboration, file sharing, milestones, client access portal, invoice from timesheet, reporting dashboard. Real-time dengan WebSocket.',
                    'price' => 1200000,
                    'sale_price' => 899000,
                    'demo_url' => 'https://demo.ngodingpintar.id/projecthub',
                ],
                [
                    'name' => 'BookingEngine - SaaS Reservasi Online',
                    'slug' => 'bookingengine-saas-reservasi-online',
                    'short_description' => 'Platform reservasi online untuk hotel, salon, klinik, dan coworking space.',
                    'description' => 'BookingEngine aealah SaaS booking system multi-purpose. Fitur: calendar booking, slot management, multi-service, staff assignment, online payment, reminder email/WhatsApp, customer portal, embeddable widget, multi-location, analytics. White-label ready.',
                    'price' => 800000,
                    'sale_price' => 649000,
                    'demo_url' => 'https://demo.ngodingpintar.id/bookingengine',
                ],
            ],

            // ──────────────────────────────────────────
            // 4. Web Machine Learning
            // ──────────────────────────────────────────
            'web-machine-learning' => [
                [
                    'name' => 'SentimenAnalyzer - Analisis Sentimen Media Sosial',
                    'slug' => 'sentimenanalyzer-analisis-sentimen',
                    'short_description' => 'Aplikasi analisis sentimen Twitter/X dengan NLP dan visualisasi real-time.',
                    'description' => 'SentimenAnalyzer menggunakan Natural Language Processing untuk menganalisis sentimen publik di media sosial. Fitur: scraping Twitter/X, preprocessing teks bahasa Indonesia, klasifikasi sentimen (positif/negatif/netral), word cloud, trend analysis, export laporan. Model: Naive Bayes & LSTM.',
                    'price' => 550000,
                    'sale_price' => 449000,
                    'demo_url' => 'https://demo.ngodingpintar.id/sentimenanalyzer',
                ],
                [
                    'name' => 'PrediksiHarga - Prediksi Harga Properti',
                    'slug' => 'prediksiharga-prediksi-harga-properti',
                    'short_description' => 'Sistem prediksi harga rumah menggunakan Random Forest dan XGBoost.',
                    'description' => 'PrediksiHarga membantu user memperkirakan harga properti berdasarkan fitur lokasi, luas, fasilitas, dll. Fitur: input parameter properti, prediksi harga real-time, perbaneingan model (Random Forest, XGBoost, Linear Regression), feature importance, confidence interval, history prediksi.',
                    'price' => 450000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/prediksiharga',
                ],
                [
                    'name' => 'FaceAttend - Absensi Face Recognition',
                    'slug' => 'faceattend-absensi-face-recognition',
                    'short_description' => 'Sistem absensi menggunakan pengenalan wajah berbasis deep learning.',
                    'description' => 'FaceAttend aealah sistem absensi modern dengan face recognition. Fitur: registrasi wajah karyawan, absensi via webcam/HP, anti-spoofing detection, laporan kehadiran, integrasi dengan HRM, GPS location tagging, offline mode, dashboard analytics. Model: FaceNet + MTCNN.',
                    'price' => 700000,
                    'sale_price' => 549000,
                    'demo_url' => 'https://demo.ngodingpintar.id/faceattend',
                ],
                [
                    'name' => 'ChatBotAI - Chatbot Customer Service',
                    'slug' => 'chatbotai-chatbot-customer-service',
                    'short_description' => 'Chatbot AI untuk customer service dengan intent recognition bahasa Indonesia.',
                    'description' => 'ChatBotAI aealah chatbot cerdas untuk layanan pelanggan. Fitur: intent recognition bahasa Indonesia, entity extraction, multi-channel (web widget, WhatsApp, Telegram), training interface, conversation flow builder, handover to human agent, analytics & reports. NLP Engine berbasis Transformer.',
                    'price' => 600000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/chatbotai',
                ],
            ],

            // ──────────────────────────────────────────
            // 5. Web Data Science
            // ──────────────────────────────────────────
            'web-data-science' => [
                [
                    'name' => 'SalesInsight - Dashboard Analitik Penjualan',
                    'slug' => 'salesinsight-dashboard-analitik-penjualan',
                    'short_description' => 'Dashboard BI untuk analisis penjualan dengan chart interaktif.',
                    'description' => 'SalesInsight aealah dashboard business intelligence untuk penjualan. Fitur: KPI cards, interactive charts (Chart.js/ApexCharts), filter periode & produk, revenue forecasting, customer segmentation, cohort analysis, top products ranking, export PDF/Excel, scheduled email report.',
                    'price' => 500000,
                    'sale_price' => 399000,
                    'demo_url' => 'https://demo.ngodingpintar.id/salesinsight',
                ],
                [
                    'name' => 'SPK-AHP - Sistem Pendukung Keputusan',
                    'slug' => 'spk-ahp-sistem-pendukung-keputusan',
                    'short_description' => 'Aplikasi SPK dengan metode AHP, SAW, TOPSIS, dan WP.',
                    'description' => 'SPK-AHP aealah sistem pendukung keputusan multi-metode. Fitur: input kriteria & alternatif, perhitungan AHP (Analytic Hierarchy Process), SAW, TOPSIS, WP, consistency ratio check, ranking hasil, perbaneingan antar metode, sensitivity analysis, export hasil ke PDF. Ideal untuk skripsi & riset.',
                    'price' => 350000,
                    'sale_price' => 279000,
                    'demo_url' => 'https://demo.ngodingpintar.id/spk-ahp',
                ],
                [
                    'name' => 'DataViz - Platform Visualisasi Data',
                    'slug' => 'dataviz-platform-visualisasi-data',
                    'short_description' => 'Platform visualisasi data interaktif dengan drag-and-drop chart builder.',
                    'description' => 'DataViz memungkinkan user membuat visualisasi data tanpa coding. Fitur: import CSV/Excel/API, drag-and-drop chart builder, 15+ chart types, dashboard builder, filter & drill-down, data transformation, sharing & embed, scheduled refresh, collaboration, export PNG/PDF.',
                    'price' => 650000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/dataviz',
                ],
                [
                    'name' => 'GeoAnalytics - Analisis Data Geospasial',
                    'slug' => 'geoanalytics-analisis-data-geospasial',
                    'short_description' => 'Dashboard peta interaktif untuk analisis data berbasis lokasi.',
                    'description' => 'GeoAnalytics menampilkan data dalam bentuk peta interaktif. Fitur: heatmap, choropleth map, marker clustering, polygon drawing, radius search, data overlay, GeoJSON import, Leaflet/Mapbox integration, lokasi real-time, spatial query, dan export map sebagai image.',
                    'price' => 550000,
                    'sale_price' => 449000,
                    'demo_url' => 'https://demo.ngodingpintar.id/geoanalytics',
                ],
            ],

            // ──────────────────────────────────────────
            // 6. Source Code Premium
            // ──────────────────────────────────────────
            'source-code-premium' => [
                [
                    'name' => 'LaraShop - E-Commerce Laravel Premium',
                    'slug' => 'larashop-ecommerce-laravel-premium',
                    'short_description' => 'Source code e-commerce lengkap dengan multi-vendor dan payment gateway.',
                    'description' => 'LaraShop aealah source code e-commerce premium built with Laravel 11, Vue 3, Inertia.js. Fitur: product management, categories, cart & checkout, multi payment gateway (Midtrans, Xeneit, Duitku), order management, customer account, wishlist, review & rating, coupon system, multi-vendor ready.',
                    'price' => 900000,
                    'sale_price' => 699000,
                    'demo_url' => 'https://demo.ngodingpintar.id/larashop',
                ],
                [
                    'name' => 'LaraCMS - Content Management System',
                    'slug' => 'laracms-content-management-system',
                    'short_description' => 'CMS modern dengan page builder, media manager, dan SEO tools.',
                    'description' => 'LaraCMS aealah content management system modern. Fitur: visual page builder, rich text editor (TipTap), media manager, SEO tools, multi-language, user roles & permissions, blog system, menu builder, theme customizer, sitemap generator, analytics dashboard. Laravel 11 + Vue 3.',
                    'price' => 600000,
                    'sale_price' => null,
                    'demo_url' => 'https://demo.ngodingpintar.id/laracms',
                ],
                [
                    'name' => 'LaraLMS - Learning Management System',
                    'slug' => 'laralms-learning-management-system',
                    'short_description' => 'Platform e-learning dengan course builder, quiz, dan sertifikat.',
                    'description' => 'LaraLMS aealah platform e-learning komprehensif. Fitur: course builder, video hosting, quiz & assessment, progress tracking, certificate generator, discussion forum, instructor dashboard, student portal, payment & enrollment, gamification badges, API integration. Multi-tenant ready.',
                    'price' => 1100000,
                    'sale_price' => 849000,
                    'demo_url' => 'https://demo.ngodingpintar.id/laralms',
                ],
                [
                    'name' => 'LaraPos - Point of Sale System',
                    'slug' => 'larapos-point-of-sale-system',
                    'short_description' => 'Aplikasi kasir/POS modern dengan receipt printing dan multi-outlet.',
                    'description' => 'LaraPos aealah sistem point of sale modern untuk retail & F&B. Fitur: POS interface (touchscreen-optimized), barcode scanner, receipt printing (thermal/A4), multi-outlet, shift management, cash drawer, stock management, customer loyalty, daily/monthly report, offline mode, API sync.',
                    'price' => 750000,
                    'sale_price' => 599000,
                    'demo_url' => 'https://demo.ngodingpintar.id/larapos',
                ],
            ],

            // ──────────────────────────────────────────
            // 7. Add-ons & Services
            // ──────────────────────────────────────────
            'addons-services' => [
                [
                    'name' => 'Instalasi Hosting & Domain',
                    'slug' => 'instalasi-hosting-domain',
                    'short_description' => 'Jasa instalasi source code di hosting cPanel/VPS beserta setup domain.',
                    'description' => 'Layanan instalasi source code di hosting pilihan Anda. Termasuk: setup hosting cPanel atau VPS, konfigurasi domain & SSL, deployment source code, setup database, konfigurasi environment, testing, dan panduan maintenance. Mendukung shared hosting, VPS, dan cloud server.',
                    'price' => 250000,
                    'sale_price' => null,
                    'demo_url' => null,
                ],
                [
                    'name' => 'Setup VPS & Server',
                    'slug' => 'setup-vps-server',
                    'short_description' => 'Konfigurasi VPS dari nol: Nginx, PHP, MySQL, SSL, dan deployment.',
                    'description' => 'Setup VPS profesional dari awal. Termasuk: instalasi OS (Ubuntu/Debian), setup Nginx/Apache, PHP-FPM, MySQL/PostgreSQL, Redis, Supervisor, SSL Let\'s Encrypt, firewall (UFW), fail2ban, deployment aplikasi, cron jobs, backup otomatis. Dokumentasi lengkap disertakan.',
                    'price' => 400000,
                    'sale_price' => 349000,
                    'demo_url' => null,
                ],
                [
                    'name' => 'Coaching 1-on-1 Development',
                    'slug' => 'coaching-1on1-development',
                    'short_description' => 'Sesi coaching privat Laravel & Vue.js via Google Meet (2 jam).',
                    'description' => 'Coaching privat 1-on-1 via Google Meet selama 2 jam. Topik bisa disesuaikan: Laravel basics/advanced, Vue.js & Inertia.js, database design, API development, deployment, debugging, code review, arsitektur aplikasi, atau konsultasi proyek. Termasuk rekaman sesi dan catatan materi.',
                    'price' => 350000,
                    'sale_price' => null,
                    'demo_url' => null,
                ],
                [
                    'name' => 'Custom Development Request',
                    'slug' => 'custom-development-request',
                    'short_description' => 'Ajukan kebutuhan pengembangan custom fitur untuk proyek Anda.',
                    'description' => 'Layanan custom development untuk kebutuhan spesifik Anda. Setelah pembelian, tim NgoeingPintar akan mereview requirement Anda dan memberikan estimasi waktu & biaya tambahan jika diperlukan. Cocok untuk: modifikasi fitur, integrasi API pihak ketiga, penambahan modul baru, dan optimasi performa.',
                    'price' => 500000,
                    'sale_price' => null,
                    'demo_url' => null,
                ],
            ],
        ];

        foreach ($products as $categorySlug => $items) {
            $category = ProductCategory::where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            foreach ($items as $item) {
                Product::updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'product_category_id' => $category->id,
                        'name' => $item['name'],
                        'short_description' => $item['short_description'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'sale_price' => $item['sale_price'],
                        'demo_url' => $item['demo_url'],
                        'status' => Product::STATUS_ACTIVE,
                    ],
                );
            }
        }
    }
}
