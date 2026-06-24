<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { csrf, money } from '../../Components/FormHelpers';
import ProductRating from '../../Components/ProductRating.vue';

defineProps({ products: Object, categories: Array, filters: Object });
const page = usePage();

const toolIcons = ['JS', '</>', '{}', '#', 'UI', 'API'];

const priceLabel = (product) => {
    const value = product.sale_price || product.price;
    return Number(value || 0) <= 0 ? 'Free' : money(value);
};

const imageSrc = (path) => {
    if (!path) return '/img/thumbnail/default-thumbnail.png';

    return path.startsWith('/') ? path : `/${path}`;
};

const loginRedirect = (path) => `/login?redirect=${encodeURIComponent(path)}`;

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

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
    <Head>
        <title>Katalog Produk & Source Code</title>
        <meta name="description" content="Temukan berbagai source code premium, template website, UI kit, dan digital tools lainnya untuk mempercepat pembuatan proyek Anda." />
    </Head>
    <AppLayout>
        <section class="py-12 text-center">
            <p class="text-sm font-black text-emerald-500">#BuildFasterDeliverBetter</p>
            <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">Eksplorasi Kelas & Webinar</h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-500">
                Tingkatkan skill ngoding Anda melalui kelas interaktif, webinar, dan bimbingan eksklusif bersama ahlinya.
            </p>

            <form method="get" action="/kelas" class="mx-auto mt-8 grid max-w-5xl gap-3 rounded-md bg-white p-3 shadow-sm shadow-sky-950/5 sm:grid-cols-2 md:grid-cols-[1fr_180px_160px_140px_100px]">
                <input name="search" :value="filters?.search" class="rounded-md border border-slate-200 px-4 py-3 text-sm" placeholder="Cari kelas (contoh: Vue, Laravel)...">
                <select name="category" class="rounded-md border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Semua Kategori</option>
                    <option v-for="category in categories" :key="category.id" :value="category.slug" :selected="filters?.category === category.slug">{{ category.name }}</option>
                </select>
                <select name="schedule_type" class="rounded-md border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Semua Jadwal</option>
                    <option value="fixed" :selected="filters?.schedule_type === 'fixed'">Jadwal Tetap</option>
                    <option value="recurring" :selected="filters?.schedule_type === 'recurring'">Jadwal Rutin</option>
                </select>
                <input type="date" name="date" :value="filters?.date" class="rounded-md border border-slate-200 px-4 py-3 text-sm text-slate-500">
                <button class="rounded-md bg-[#0284c7] px-4 py-3 text-sm font-black text-white hover:bg-sky-700 transition">Cari</button>
            </form>
        </section>

        <section class="mt-5 overflow-hidden py-4">
            <div class="mx-auto max-w-5xl">
                <h2 class="text-2xl font-black text-slate-900">Kategori Kelas</h2>
            </div>
            
            <div class="group relative mt-6 flex w-full flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]">
                <div class="flex w-max shrink-0 animate-marquee items-center justify-center gap-5 pr-5 group-hover:[animation-play-state:paused]">
                    <Link
                        v-for="(category, index) in categories"
                        :key="category.id"
                        :href="`/kelas?category=${category.slug}`"
                        class="flex w-[300px] items-center gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-sky-100 hover:shadow-md"
                    >
                        <span class="grid size-14 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-sky-50 to-cyan-50 text-lg font-black text-[#0284c7]">
                            {{ toolIcons[index % toolIcons.length] }}
                        </span>
                        <span class="text-left">
                            <span class="block font-black text-slate-900 line-clamp-1">{{ category.name }}</span>
                            <span class="mt-1 block text-xs font-semibold text-slate-400">{{ category.products_count }} Source Codes</span>
                        </span>
                    </Link>
                </div>
                
                <div aria-hidden="true" class="flex w-max shrink-0 animate-marquee items-center justify-center gap-5 pr-5 group-hover:[animation-play-state:paused]">
                    <Link
                        v-for="(category, index) in categories"
                        :key="'dup-'+category.id"
                        :href="`/kelas?category=${category.slug}`"
                        class="flex w-[300px] items-center gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-sky-100 hover:shadow-md"
                    >
                        <span class="grid size-14 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-sky-50 to-cyan-50 text-lg font-black text-[#0284c7]">
                            {{ toolIcons[index % toolIcons.length] }}
                        </span>
                        <span class="text-left">
                            <span class="block font-black text-slate-900 line-clamp-1">{{ category.name }}</span>
                            <span class="mt-1 block text-xs font-semibold text-slate-400">{{ category.products_count }} Kelas</span>
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto mt-14 max-w-5xl">
            <h2 class="text-2xl font-black text-slate-900">Kelas Terbaru</h2>
            <div class="mt-6 grid gap-7 md:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="product in products.data"
                    :key="product.id"
                    class="group flex flex-col overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-950/5 hover:border-slate-200"
                >
                    <Link :href="`/products/${product.slug}`" class="relative block overflow-hidden aspect-[4/3] bg-slate-50">
                        <img :src="imageSrc(product.thumbnail)" :alt="product.name" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute top-3 right-3 flex gap-2">
                            <span 
                                class="rounded-full px-3 py-1 text-[10px] font-bold tracking-wide shadow-sm"
                                :class="Number(product.sale_price || product.price || 0) <= 0 ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-slate-900'"
                            >
                                {{ Number(product.sale_price || product.price || 0) <= 0 ? 'FREE' : 'PREMIUM' }}
                            </span>
                        </div>
                    </Link>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                {{ product.product_type === 'class' ? 'Kelas / Webinar' : 'Source Code' }}
                            </span>
                        </div>
                        <Link :href="`/products/${product.slug}`" class="line-clamp-2 text-sm font-bold leading-snug text-slate-900 transition-colors group-hover:text-[#0284c7]">
                            {{ product.name }}
                        </Link>
                        <ProductRating :product="product" />
                        
                        <div class="mt-3 flex items-start gap-1.5 text-xs font-semibold text-slate-500" v-if="product.class_product">
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
                        
                        <div class="mt-4 flex items-center gap-2">
                            <span class="grid size-6 place-items-center rounded-full bg-sky-100 text-[10px] font-black text-[#0284c7]">M</span>
                            <span class="text-xs text-slate-500">NgodingPintar</span>
                        </div>

                        <div class="mt-auto pt-5">
                            <div class="flex items-end justify-between mb-4">
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 line-through mb-0.5" v-if="product.sale_price">
                                        {{ money(product.price) }}
                                    </p>
                                    <p class="font-black" :class="Number(product.sale_price || product.price || 0) <= 0 ? 'text-emerald-500' : 'text-[#0284c7]'">
                                        {{ priceLabel(product) }}
                                    </p>
                                </div>
                            </div>
                            
                            <Link :href="`/kelas/${product.slug}`" class="block w-full rounded-xl bg-slate-50 px-4 py-2.5 text-center text-sm font-bold text-[#0284c7] transition-colors hover:bg-[#0284c7] hover:text-white">
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>
                </article>
            </div>
            <div v-if="products.data.length === 0" class="mt-14 rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center">
                <p class="text-sm font-bold text-slate-500">Tidak ada kelas yang ditemukan.</p>
            </div>
        </section>
    </AppLayout>
</template>
