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
</script>

<template>
    <Head>
        <title>Katalog Produk & Source Code</title>
        <meta name="description" content="Temukan berbagai source code premium, template website, UI kit, dan digital tools lainnya untuk mempercepat pembuatan proyek Anda." />
    </Head>
    <AppLayout>
        <section class="py-12 text-center">
            <p class="text-sm font-black text-emerald-500">#BuildFasterDeliverBetter</p>
            <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">Source Code Products</h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-500">
                Dapatkan source code berkualitas tinggi untuk mempercepat pengembangan proyekmu.
            </p>

            <form method="get" action="/products" class="mx-auto mt-8 grid max-w-3xl gap-3 rounded-md bg-white p-3 shadow-sm shadow-sky-950/5 md:grid-cols-[1fr_220px_110px]">
                <input name="search" :value="filters?.search" class="rounded-md border border-slate-200 px-4 py-3 text-sm" placeholder="Cari source code...">
                <select name="category" class="rounded-md border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Semua Tools</option>
                    <option v-for="category in categories" :key="category.id" :value="category.slug" :selected="filters?.category === category.slug">{{ category.name }}</option>
                </select>
                <button class="rounded-md bg-[#0284c7] px-4 py-3 text-sm font-black text-white">Cari</button>
            </form>
        </section>

        <section class="mt-5 overflow-hidden py-4">
            <div class="mx-auto max-w-5xl">
                <h2 class="text-2xl font-black text-slate-900">Browse by Kategori</h2>
            </div>
            
            <div class="group relative mt-6 flex w-full flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]">
                <div class="flex w-max shrink-0 animate-marquee items-center justify-center gap-5 pr-5 group-hover:[animation-play-state:paused]">
                    <Link
                        v-for="(category, index) in categories"
                        :key="category.id"
                        :href="`/products?category=${category.slug}`"
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
                        :href="`/products?category=${category.slug}`"
                        class="flex w-[300px] items-center gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-sky-100 hover:shadow-md"
                    >
                        <span class="grid size-14 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-sky-50 to-cyan-50 text-lg font-black text-[#0284c7]">
                            {{ toolIcons[index % toolIcons.length] }}
                        </span>
                        <span class="text-left">
                            <span class="block font-black text-slate-900 line-clamp-1">{{ category.name }}</span>
                            <span class="mt-1 block text-xs font-semibold text-slate-400">{{ category.products_count }} Produk</span>
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto mt-14 max-w-5xl">
            <h2 class="text-2xl font-black text-slate-900">Latest Products</h2>
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
                            
                            <form v-if="page.props.auth?.user" method="post" :action="`/cart/${product.slug}`">
                                <input type="hidden" name="_token" :value="csrf()">
                                <button class="w-full rounded-xl bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-700 transition-colors hover:bg-[#0284c7] hover:text-white">Tambah ke Cart</button>
                            </form>
                            <Link v-else :href="loginRedirect(page.url)" class="block w-full rounded-xl bg-slate-50 px-4 py-2.5 text-center text-sm font-bold text-slate-700 transition-colors hover:bg-[#0284c7] hover:text-white">
                                Tambah ke Cart
                            </Link>
                        </div>
                    </div>
                </article>
            </div>
            <p v-if="!products.data?.length" class="mt-6 rounded-md border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">
                Belum ada produk aktif yang sesuai filter.
            </p>
        </section>
    </AppLayout>
</template>
