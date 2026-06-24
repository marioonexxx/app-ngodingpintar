<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';
import { ref, watch } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({ products: Object, categories: Array, statuses: Array, filters: Object });

const filter = ref({
    status: props.filters?.status || '',
    category_id: props.filters?.category_id || '',
    search: props.filters?.search || '',
    owner: props.filters?.owner || '',
});

const applyFilter = () => {
    router.get('/admin/products', {
        status: filter.value.status,
        category_id: filter.value.category_id,
        search: filter.value.search,
        owner: filter.value.owner,
    }, { preserveState: true, preserveScroll: true });
};

watch(() => [filter.value.status, filter.value.category_id, filter.value.owner], applyFilter);

const imageSrc = (path) => {
    if (!path) return '/img/thumbnail/default-thumbnail.png';

    return path.startsWith('/') ? path : `/${path}`;
};

const deleteProduct = async (product) => {
    const confirmed = await confirmAction({
        title: 'Hapus produk?',
        text: `Produk "${product.name}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(`/admin/products/${product.id}`, {
        preserveScroll: true,
        onSuccess: () => successAlert('Produk berhasil dihapus.'),
        onError: () => errorAlert('Produk gagal dihapus. Pastikan produk tidak terkait transaksi aktif.'),
    });
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Data Source Code</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola katalog, media aplikasi, file download, panduan instalasi, status publish, dan demo produk.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ products.total || products.data.length }} produk</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Produk</h2>
                    <Link href="/admin/products/create" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Tambah</Link>
                </div>
                <div class="grid gap-2 md:grid-cols-[140px_140px_140px_200px_80px]">
                    <select v-model="filter.owner" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Pemilik</option>
                        <option value="admin">Hanya Admin</option>
                        <option value="vendor">Hanya Vendor</option>
                    </select>
                    <select v-model="filter.status" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Status</option>
                        <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                    </select>
                    <select v-model="filter.category_id" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <input v-model="filter.search" @keyup.enter="applyFilter" class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari nama produk...">
                    <button type="button" @click="applyFilter" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Pemilik</th>
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
                            <td class="px-4 py-4">
                                <span v-if="product.vendor" class="block w-max rounded bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-600">Vendor: {{ product.vendor.name }}</span>
                                <span v-else class="block w-max rounded bg-sky-50 px-2 py-1 text-[10px] font-bold text-sky-600">Admin</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="cat in product.categories" :key="cat.id" class="rounded bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600">{{ cat.name }}</span>
                                    <span v-if="!product.categories || product.categories.length === 0" class="text-slate-400">-</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-bold">{{ money(product.sale_price || product.price) }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded bg-sky-50 px-3 py-1 text-xs font-bold text-[#0284c7]">{{ product.status }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/admin/products/${product.id}/edit`" class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100">Edit</Link>
                                    <button type="button" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" @click="deleteProduct(product)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada produk.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
