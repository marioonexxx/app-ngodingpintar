<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import NavIcon from '../../Components/NavIcon.vue';
import ProductRating from '../../Components/ProductRating.vue';
import { Head, Link } from '@inertiajs/vue3';
import { money } from '../../Components/FormHelpers';

defineProps({
    latestClasses: Array,
    latestProducts: Array,
    classCategories: Array,
    productCategories: Array,
    testimonials: Array
});

const imageSrc = (path) => {
    if (!path) return '/img/thumbnail/default-thumbnail.png';

    return path.startsWith('/') ? path : `/${path}`;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const daysMap = {
    1: 'Senin', 2: 'Selasa', 3: 'Rabu', 4: 'Kamis', 5: 'Jumat', 6: 'Sabtu', 7: 'Minggu'
};
const formatRecurringDays = (days) => {
    if (!days) return '-';
    try {
        const parsed = JSON.parse(days);
        return parsed.map(d => daysMap[d]).join(', ');
    } catch(e) {
        return '-';
    }
};

const categoryIcons = {
    'website-landing-page': 'LP',
    'web-application': '{}',
    'web-saas': 'SaaS',
    'web-machine-learning': 'AI',
    'web-data-science': 'DS',
    'source-code-premium': '</>',
    'addons-services': 'JS',
};

const initials = (name) => (name || 'M')
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase();

const productUrl = (product) => {
    if (!product) return '#';
    if (product.product_type === 'class') return `/kelas/${product.slug}`;
    return `/products/${product.slug}`;
};
</script>

<template>
    <Head>
        <title>Home</title>
        <meta name="description" content="Jasa pembuatan aplikasi dan source code murah terjangkau dengan dukungan panduan dari NgodingPintar." />
    </Head>

    <AppLayout>
        <section class="grid items-center gap-10 py-8 sm:py-12 lg:min-h-[660px] lg:grid-cols-[.9fr_1.1fr] lg:gap-12">
            <div>
                <p class="text-sm font-medium text-sky-600">#VibeCodingAndSourceCode</p>
                <h1 class="mt-4 max-w-xl text-4xl font-semibold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    Manfaatkan AI dengan Cara Pintar dan Efektif untuk Membangun Aplikasi
                </h1>
                <p class="mt-5 max-w-xl text-base leading-8 text-slate-600 sm:text-lg sm:leading-9">
                    NgodingPintar membantu Anda belajar menggunakan AI sebagai partner coding dalam proses pembuatan aplikasi. Mulai dari memahami ide, menyusun struktur project, membuat fitur, memperbaiki error, hingga menghasilkan aplikasi yang lebih rapi, fungsional, dan berkualitas.
                </p>
                <div class="mt-8 grid gap-3 sm:flex sm:flex-wrap sm:gap-4">
                    <Link href="/request" class="rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-7 py-4 text-center text-sm font-semibold text-white shadow-lg shadow-amber-950/10">
                        Request Aplikasi
                    </Link>
                    <Link href="/products" class="rounded-full bg-white px-7 py-4 text-center text-sm font-semibold text-slate-700 shadow-sm shadow-sky-950/5 ring-1 ring-sky-100">
                        Lihat Source Aplikasi
                    </Link>
                </div>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex -space-x-3">
                        <span v-for="n in 4" :key="n" class="grid size-10 place-items-center rounded-full border-2 border-white bg-sky-100 text-xs font-semibold text-sky-600">M</span>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-slate-500">
                        Cocok untuk belajar lebih terarah, mempercepat pengerjaan project, dan tetap punya panduan saat memakai source aplikasi.
                    </p>
                </div>
            </div>

            <div class="relative min-h-[420px] sm:min-h-[520px]">
                <div class="relative overflow-hidden rounded-[24px] shadow-2xl shadow-sky-950/10 sm:absolute sm:right-0 sm:top-6 sm:w-[92%] sm:rounded-[34px]">
                    <img :src="'/img/hero-section/hero1.png'" alt="NgodingPintar web solution preview" class="h-full w-full object-cover">
                </div>
                <div class="mt-4 w-full rounded-2xl bg-white p-5 shadow-xl shadow-sky-950/10 sm:absolute sm:left-0 sm:top-0 sm:mt-0 sm:w-56">
                    <p class="text-xs font-semibold text-slate-800">Progress Project</p>
                    <div class="mt-4 space-y-4">
                        <div>
                            <div class="flex justify-between text-xs font-medium"><span>Source App</span><span>90%</span></div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100"><div class="h-2 w-[90%] rounded-full bg-gradient-to-r from-sky-600 to-cyan-500"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-medium"><span>Panduan</span><span>75%</span></div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100"><div class="h-2 w-[75%] rounded-full bg-cyan-300"></div></div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 overflow-hidden rounded-2xl bg-slate-950 p-4 text-white shadow-2xl shadow-slate-950/20 sm:absolute sm:bottom-16 sm:left-16 sm:mt-0">
                    <div class="flex gap-2 text-xs text-slate-400"><span>.</span><span>AppRequest.php</span><span>guide.md</span></div>
                    <pre class="mt-4 text-xs leading-6 text-sky-100">class ProjectBuilder {
  public function start()
  {
    return App::buildWithGuide();
  }
}</pre>
                    <span class="absolute -right-5 top-1/2 grid size-16 -translate-y-1/2 place-items-center rounded-full bg-gradient-to-r from-sky-600 to-cyan-500 text-xl">&gt;</span>
                </div>
                <div class="mt-4 w-full rounded-2xl bg-white p-5 shadow-xl shadow-sky-950/10 sm:absolute sm:bottom-4 sm:right-4 sm:mt-0 sm:w-64">
                    <div class="flex -space-x-2">
                        <span v-for="n in 4" :key="n" class="grid size-8 place-items-center rounded-full border-2 border-white bg-sky-50 text-xs font-semibold text-sky-600">M</span>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-slate-700">Source code dan pembuatan aplikasi</p>
                    <p class="mt-3 text-sm leading-6 text-slate-500">"Lebih mudah belajar, instalasi jelas, dan source aplikasinya siap dikembangkan."</p>
                    <p class="mt-2 text-cyan-400">★★★★★</p>
                </div>
            </div>
        </section>

        <section class="grid gap-5 rounded-3xl bg-white p-5 shadow-sm shadow-sky-950/5 sm:grid-cols-2 lg:grid-cols-4">
            <article class="flex items-center gap-4 border-slate-100 p-4 md:border-r">
                <span class="grid size-14 place-items-center rounded-2xl bg-sky-100 text-sky-600"><NavIcon name="approval" /></span>
                <div><p class="font-semibold">Request Aplikasi</p><p class="mt-1 text-sm text-slate-500">Buat aplikasi sesuai kebutuhan.</p></div>
            </article>
            <article class="flex items-center gap-4 border-slate-100 p-4 md:border-r">
                <span class="grid size-14 place-items-center rounded-2xl bg-cyan-100 text-cyan-600"><NavIcon name="box" /></span>
                <div><p class="font-semibold">Source Aplikasi Murah</p><p class="mt-1 text-sm text-slate-500">Siap pakai dan terjangkau.</p></div>
            </article>
            <article class="flex items-center gap-4 border-slate-100 p-4 md:border-r">
                <span class="grid size-14 place-items-center rounded-2xl bg-sky-100 text-sky-600"><NavIcon name="star" /></span>
                <div><p class="font-semibold">Dukungan Panduan</p><p class="mt-1 text-sm text-slate-500">Instalasi dan arahan jelas.</p></div>
            </article>
            <article class="flex items-center gap-4 p-4">
                <span class="grid size-14 place-items-center rounded-2xl bg-sky-50 text-sky-600"><NavIcon name="message" /></span>
                <div><p class="font-semibold">Harga Terjangkau</p><p class="mt-1 text-sm text-slate-500">Belajar dan membangun hemat.</p></div>
            </article>
        </section>

        <!-- SECTION 1: KELAS TERBARU -->
        <section v-if="latestClasses?.length" class="mt-14 rounded-md bg-white px-5 py-8 shadow-sm shadow-sky-950/5">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-sky-600">#KelasTerbaru</p>
                    <h2 class="mt-2 text-3xl font-semibold">Produk Kelas & Webinar</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Ikuti kelas dan pelatihan interaktif untuk meningkatkan skill coding Anda.</p>
                </div>
                <Link href="/kelas" class="rounded-full bg-gradient-to-r from-sky-600 to-cyan-500 px-6 py-3 text-sm font-semibold text-white hover:opacity-90 transition shrink-0">
                    Lihat Semua Kelas
                </Link>
            </div>
            <div class="mb-5 flex flex-wrap gap-1.5">
                <Link v-for="category in classCategories" :key="'cls-hdr-'+category.id" :href="`/kelas?category=${category.slug}`" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-sky-100 hover:text-sky-700 transition border border-slate-200">
                    {{ category.name }}
                </Link>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article v-for="product in latestClasses.slice(0, 8)" :key="product.id" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all hover:-translate-y-1 hover:border-sky-100 hover:shadow-lg hover:shadow-sky-950/5">
                    <Link :href="`/kelas/${product.slug}`" class="relative block aspect-[4/3] overflow-hidden bg-slate-50">
                        <img :src="imageSrc(product.thumbnail)" :alt="product.name" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute right-3 top-3 flex gap-2">
                            <span class="rounded-full px-3 py-1 text-[10px] font-semibold tracking-wide shadow-sm" :class="Number(product.sale_price || product.price || 0) <= 0 ? 'bg-sky-600 text-white' : 'bg-cyan-200 text-sky-950'">
                                {{ Number(product.sale_price || product.price || 0) <= 0 ? 'FREE' : 'PREMIUM' }}
                            </span>
                        </div>
                    </Link>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-2 flex flex-wrap gap-1">
                            <Link v-for="cat in product.categories" :key="cat.id" :href="`/kelas?category=${cat.slug}`" class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:bg-sky-100 hover:text-sky-700 transition-colors">
                                {{ cat.name }}
                            </Link>
                            <span v-if="!product.categories?.length" class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Kelas / Webinar</span>
                        </div>
                        <Link :href="`/kelas/${product.slug}`" class="line-clamp-2 text-sm font-semibold leading-snug text-slate-900 transition-colors group-hover:text-sky-600">
                            {{ product.name }}
                        </Link>
                        <ProductRating :product="product" />
                        
                        <div class="mt-2 flex items-start gap-1.5 text-xs font-medium text-slate-500" v-if="product.class_product">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-[#0284c7] shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-5.25h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <span v-if="product.class_product.schedule_type === 'fixed'" class="leading-tight">
                                {{ formatDate(product.class_product.start_date) }} - {{ product.class_product.start_time }} WIB
                            </span>
                            <span v-else-if="product.class_product.schedule_type === 'recurring'" class="leading-tight">
                                Rutin ({{ formatRecurringDays(product.class_product.recurring_days) }})
                            </span>
                        </div>
                        <p v-if="product.class_product" class="mt-2 text-xs font-bold text-sky-600">Batch {{ product.class_product.batch_number }}</p>
                        <div class="mt-auto flex items-end justify-between pt-5">
                            <div>
                                <p class="mb-0.5 text-xs font-medium text-slate-400 line-through" v-if="product.sale_price">
                                    {{ money(product.price) }}
                                </p>
                                <p class="font-bold" :class="Number(product.sale_price || product.price || 0) <= 0 ? 'text-sky-600' : 'text-cyan-600'">
                                    {{ Number(product.sale_price || product.price || 0) <= 0 ? 'Free' : money(product.sale_price || product.price) }}
                                </p>
                            </div>
                            <Link :href="`/kelas/${product.slug}`" class="inline-flex items-center justify-center rounded-full bg-sky-50 px-5 py-2 text-xs font-bold text-sky-600 transition-colors hover:bg-sky-600 hover:text-white group-hover:bg-gradient-to-r group-hover:from-sky-600 group-hover:to-cyan-500 group-hover:text-white">
                                Daftar
                            </Link>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- SECTION 2: SOURCE CODE TERBARU -->
        <section v-if="latestProducts?.length" class="mt-14 rounded-md bg-white px-5 py-8 shadow-sm shadow-sky-950/5">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-sky-600">#SourceCode</p>
                    <h2 class="mt-2 text-3xl font-semibold">Source Code Premium</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Kumpulan source code aplikasi siap pakai untuk mempercepat pembuatan project Anda.</p>
                </div>
                <Link href="/products" class="rounded-full bg-gradient-to-r from-sky-600 to-cyan-500 px-6 py-3 text-sm font-semibold text-white hover:opacity-90 transition shrink-0">
                    Lihat Semua Produk
                </Link>
            </div>
            <div class="mb-5 flex flex-wrap gap-1.5">
                <Link v-for="category in productCategories" :key="'sc-hdr-'+category.id" :href="`/products?category=${category.slug}`" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-sky-100 hover:text-sky-700 transition border border-slate-200">
                    {{ category.name }}
                </Link>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article v-for="product in latestProducts.slice(0, 8)" :key="product.id" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all hover:-translate-y-1 hover:border-sky-100 hover:shadow-lg hover:shadow-sky-950/5">
                    <Link :href="`/products/${product.slug}`" class="relative block aspect-[4/3] overflow-hidden bg-slate-50">
                        <img :src="imageSrc(product.thumbnail)" :alt="product.name" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute right-3 top-3 flex gap-2">
                            <span class="rounded-full px-3 py-1 text-[10px] font-semibold tracking-wide shadow-sm" :class="Number(product.sale_price || product.price || 0) <= 0 ? 'bg-sky-600 text-white' : 'bg-cyan-200 text-sky-950'">
                                {{ Number(product.sale_price || product.price || 0) <= 0 ? 'FREE' : 'PREMIUM' }}
                            </span>
                        </div>
                    </Link>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-2 flex flex-wrap gap-1">
                            <Link v-for="cat in product.categories" :key="cat.id" :href="`/products?category=${cat.slug}`" class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:bg-sky-100 hover:text-sky-700 transition-colors">
                                {{ cat.name }}
                            </Link>
                            <span v-if="!product.categories?.length" class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Source Code</span>
                        </div>
                        <Link :href="`/products/${product.slug}`" class="line-clamp-2 text-sm font-semibold leading-snug text-slate-900 transition-colors group-hover:text-sky-600">
                            {{ product.name }}
                        </Link>
                        <ProductRating :product="product" />
                        <div class="mt-auto flex items-end justify-between pt-5">
                            <div>
                                <p class="mb-0.5 text-xs font-medium text-slate-400 line-through" v-if="product.sale_price">
                                    {{ money(product.price) }}
                                </p>
                                <p class="font-bold" :class="Number(product.sale_price || product.price || 0) <= 0 ? 'text-sky-600' : 'text-cyan-600'">
                                    {{ Number(product.sale_price || product.price || 0) <= 0 ? 'Free' : money(product.sale_price || product.price) }}
                                </p>
                            </div>
                            <Link :href="`/products/${product.slug}`" class="inline-flex items-center justify-center rounded-full bg-sky-50 px-5 py-2 text-xs font-bold text-sky-600 transition-colors hover:bg-sky-600 hover:text-white group-hover:bg-gradient-to-r group-hover:from-sky-600 group-hover:to-cyan-500 group-hover:text-white">
                                Beli
                            </Link>
                        </div>
                    </div>
                </article>
            </div>
        </section>



        <section v-if="testimonials?.length" class="mt-14 overflow-hidden rounded-3xl bg-gradient-to-br from-sky-950 via-sky-800 to-cyan-700 px-5 py-10 text-white shadow-xl shadow-sky-950/10 sm:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold text-cyan-200">#CeritaMember</p>
                <h2 class="mt-2 text-3xl font-semibold">Dipercaya oleh Member NgodingPintar</h2>
                <p class="mt-3 text-sm leading-6 text-sky-100/75">Pengalaman nyata member setelah berkolaborasi atau menggunakan source code kami.</p>
            </div>
            <div class="mt-8 flex w-full justify-center overflow-hidden group">
                <div :class="testimonials.length > 3 ? 'flex animate-marquee min-w-max group-hover:[animation-play-state:paused]' : 'flex gap-5 px-2.5 max-w-full overflow-x-auto scrollbar-none'">
                    <!-- Set 1 -->
                    <div class="flex shrink-0 gap-5" :class="testimonials.length > 3 ? 'px-2.5' : ''">
                        <article v-for="testimonial in testimonials" :key="'A'+testimonial.id" class="flex w-[320px] shrink-0 flex-col rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur-sm">
                            <div class="flex gap-1" :aria-label="`${testimonial.rating} dari 5 bintang`">
                                <span v-for="star in 5" :key="star" class="text-lg" :class="star <= testimonial.rating ? 'text-amber-300' : 'text-white/20'">&#9733;</span>
                            </div>
                            <h3 class="mt-4 font-semibold text-white">{{ testimonial.title || 'Pengalaman yang menyenangkan' }}</h3>
                            <p class="mt-2 line-clamp-4 text-sm leading-6 text-sky-50/80">“{{ testimonial.content }}”</p>
                            <div class="mt-auto flex items-center gap-3 pt-6">
                                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-xs font-bold text-sky-700">
                                    {{ initials(testimonial.user?.name) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ testimonial.user?.name || 'Member' }}</p>
                                    <Link v-if="testimonial.product" :href="productUrl(testimonial.product)" class="block truncate text-xs text-cyan-200 hover:text-white">
                                        {{ testimonial.product.name }}
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>
                    <!-- Set 2 -->
                    <div v-if="testimonials.length > 3" class="flex shrink-0 gap-5 px-2.5">
                        <article v-for="testimonial in testimonials" :key="'B'+testimonial.id" class="flex w-[320px] shrink-0 flex-col rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur-sm">
                            <div class="flex gap-1" :aria-label="`${testimonial.rating} dari 5 bintang`">
                                <span v-for="star in 5" :key="star" class="text-lg" :class="star <= testimonial.rating ? 'text-amber-300' : 'text-white/20'">&#9733;</span>
                            </div>
                            <h3 class="mt-4 font-semibold text-white">{{ testimonial.title || 'Pengalaman yang menyenangkan' }}</h3>
                            <p class="mt-2 line-clamp-4 text-sm leading-6 text-sky-50/80">“{{ testimonial.content }}”</p>
                            <div class="mt-auto flex items-center gap-3 pt-6">
                                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-xs font-bold text-sky-700">
                                    {{ initials(testimonial.user?.name) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ testimonial.user?.name || 'Member' }}</p>
                                    <Link v-if="testimonial.product" :href="productUrl(testimonial.product)" class="block truncate text-xs text-cyan-200 hover:text-white">
                                        {{ testimonial.product.name }}
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="!latestClasses?.length && !latestProducts?.length" class="mt-14 rounded-md border border-dashed border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
            Belum ada produk aktif untuk ditampilkan di homepage.
        </section>
    </AppLayout>
</template>

<style scoped>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    animation: marquee 35s linear infinite;
}
</style>
