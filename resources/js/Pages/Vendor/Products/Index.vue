<script setup>
import { Link, router } from '@inertiajs/vue3';
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import { csrf, money } from '../../../Components/FormHelpers';
import { ref, watch } from 'vue';

const props = defineProps({ products: Object, categories: Array, statuses: Array, filters: Object });

const filter = ref({
    status: props.filters?.status || '',
    category_id: props.filters?.category_id || '',
    search: props.filters?.search || '',
});

const applyFilter = () => {
    router.get('/vendor/products', {
        status: filter.value.status,
        category_id: filter.value.category_id,
        search: filter.value.search,
    }, { preserveState: true, preserveScroll: true });
};

watch(() => [filter.value.status, filter.value.category_id], applyFilter);

const imageSrc = (path) => {
    if (!path) return '/img/thumbnail/default-thumbnail.png';

    return path.startsWith('/') ? path : `/${path}`;
};

const statusBadge = (status) => {
    const map = {
        draft: 'bg-slate-100 text-slate-600',
        pending: 'bg-amber-100 text-amber-700',
        active: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-red-100 text-red-700',
        archived: 'bg-slate-200 text-slate-700',
    };

    return map[status] || 'bg-slate-100 text-slate-600';
};
</script>

<template>
    <PartnerLayout>
        <section class="rounded-md border border-slate-100 bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Vendor Area</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Produk Saya</h1>
                    <p class="mt-3 text-sm text-slate-500">Upload dan kelola software/source code Anda. Produk yang ditambahkan atau diedit akan ditinjau oleh Admin.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ products.total || products.data.length }} produk</span>
            </div>
        </section>

        <section class="mt-5 rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Produk</h2>
                    <Link href="/vendor/products/create" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Tambah Produk</Link>
                </div>
                <div class="grid gap-2 md:grid-cols-[170px_170px_220px_80px]">
                    <select v-model="filter.status" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Status</option>
                        <option v-for="status in statuses" :key="status" :value="status">{{ status.toUpperCase() }}</option>
                    </select>
                    <select v-model="filter.category_id" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <input v-model="filter.search" @keyup.enter="applyFilter" class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari produk...">
                    <button type="button" @click="applyFilter" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id" class="border-t border-slate-100">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="imageSrc(product.thumbnail)" :alt="product.name" class="size-14 rounded-md object-cover">
                                    <div>
                                        <p class="font-bold">{{ product.name }}</p>
                                        <p class="mt-1 max-w-md truncate text-xs text-slate-500">{{ product.short_description || product.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">{{ product.category?.name }}</td>
                            <td class="px-4 py-4 font-bold">{{ money(product.sale_price || product.price) }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded px-3 py-1 text-xs font-bold uppercase" :class="statusBadge(product.status)">{{ product.status }}</span>
                                <p v-if="product.status === 'rejected' && product.rejection_reason" class="mt-1 max-w-[240px] text-xs text-red-600">
                                    Alasan: {{ product.rejection_reason }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/vendor/products/${product.id}/edit`" class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100">Edit</Link>
                                    <form method="post" :action="`/vendor/products/${product.id}`">
                                        <input type="hidden" name="_token" :value="csrf()">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada produk. Silakan tambah produk baru.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </PartnerLayout>
</template>
