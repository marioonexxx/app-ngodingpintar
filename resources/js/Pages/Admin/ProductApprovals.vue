<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';
import { ref, watch } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({ products: Object, filters: Object });

const filter = ref({
    search: props.filters?.search || ''
});

const applyFilter = () => {
    router.get('/admin/product-approvals', {
        search: filter.value.search
    }, { preserveState: true, preserveScroll: true });
};

const showModal = ref(false);
const selected = ref(null);
const rejectionReason = ref('');
const isRejecting = ref(false);

const openDetail = (product) => {
    selected.value = product;
    rejectionReason.value = '';
    isRejecting.value = false;
    showModal.value = true;
};

const imageSrc = (path) => {
    if (!path) return '/img/thumbnail/default-thumbnail.png';

    return path.startsWith('/') ? path : `/${path}`;
};

const isVendorUpdate = (product) => {
    if (product.status !== 'pending' || !product.vendor) return false;
    const created = new Date(product.created_at).getTime();
    const updated = new Date(product.updated_at).getTime();
    return (updated - created) > 60000; // more than 1 minute difference
};

const approveProduct = async () => {
    const confirmed = await confirmAction({
        title: 'Setujui produk?',
        text: `Produk "${selected.value.name}" akan diterbitkan.`,
        confirmButtonText: 'Ya, setujui',
    });

    if (!confirmed) return;

    router.patch(`/admin/product-approvals/${selected.value.id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert('Produk berhasil disetujui dan diterbitkan.');
        },
        onError: () => errorAlert('Produk gagal disetujui.'),
    });
};

const rejectProduct = async () => {
    const confirmed = await confirmAction({
        title: 'Tolak produk?',
        text: `Produk "${selected.value.name}" akan ditolak dengan alasan yang diisi.`,
        confirmButtonText: 'Ya, tolak',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.patch(`/admin/product-approvals/${selected.value.id}/reject`, {
        rejection_reason: rejectionReason.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert('Produk berhasil ditolak.');
        },
        onError: () => errorAlert(),
    });
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Approval Source Code</h1>
                    <p class="mt-3 text-sm text-slate-500">Tinjau produk baru atau pembaruan yang diajukan oleh Vendor.</p>
                </div>
                <span class="rounded-md bg-amber-50 px-4 py-3 text-sm font-black text-amber-600">{{ products.total || products.data.length }} pending</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-black">Daftar Produk Pending</h2>
                <div class="flex gap-2 w-full md:w-auto">
                    <input v-model="filter.search" @keyup.enter="applyFilter" class="w-full md:w-64 rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari nama produk...">
                    <button type="button" @click="applyFilter" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white shrink-0">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Vendor / Mentor</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id" class="border-t border-slate-100">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="imageSrc(product.thumbnail)" :alt="product.name" class="size-14 rounded-md object-cover">
                                    <div>
                                        <p class="font-bold">
                                            {{ product.name }}
                                            <span v-if="isVendorUpdate(product)" class="ml-2 inline-flex items-center gap-1 rounded bg-amber-100 px-2 py-0.5 text-[10px] font-black uppercase text-amber-700" title="Vendor telah mengubah data produk ini. Silakan tinjau kembali.">Update Vendor</span>
                                            <span v-else class="ml-2 inline-flex items-center gap-1 rounded bg-sky-100 px-2 py-0.5 text-[10px] font-black uppercase text-sky-700">Ajuan Baru</span>
                                        </p>
                                        <p class="mt-1 max-w-md truncate text-xs text-slate-500">{{ product.short_description || product.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-medium">{{ product.vendor?.name || product.mentor?.name || 'Unknown' }}</td>
                            <td class="px-4 py-4">{{ product.category?.name }}</td>
                            <td class="px-4 py-4 font-bold">{{ money(product.sale_price || product.price) }}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100" @click="openDetail(product)">Periksa</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">Tidak ada produk yang menunggu persetujuan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div v-if="showModal && selected" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-10 backdrop-blur-sm overflow-y-auto">
            <div class="mx-auto w-full max-w-3xl overflow-hidden rounded-md bg-white shadow-2xl shadow-slate-950/20 my-auto">
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5 bg-white">
                    <div>
                        <h2 class="text-xl font-black">Detail Produk</h2>
                        <p class="mt-1 text-sm text-slate-500">Oleh: {{ selected.vendor?.name || selected.mentor?.name }}</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl hover:bg-slate-200" @click="showModal = false">×</button>
                </div>
                
                <div class="max-h-[60vh] overflow-y-auto px-6 py-6 space-y-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <img :src="imageSrc(selected.thumbnail)" class="w-full md:w-1/3 aspect-video object-cover rounded-lg border border-slate-100 shadow-sm" alt="Thumbnail">
                        <div class="flex-1 space-y-4">
                            <div>
                                <h3 class="font-black text-2xl">{{ selected.name }}</h3>
                                <p class="text-sm font-medium text-slate-500 mt-1">{{ selected.category?.name }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Normal</p>
                                    <p class="font-bold mt-1">{{ money(selected.price) }}</p>
                                </div>
                                <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Harga Promo</p>
                                    <p class="font-bold mt-1 text-emerald-700">{{ selected.sale_price ? money(selected.sale_price) : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Singkat</p>
                        <p class="mt-2 text-sm bg-slate-50 p-4 rounded-lg border border-slate-100">{{ selected.short_description || '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Lengkap</p>
                        <div class="mt-2 text-sm bg-slate-50 p-4 rounded-lg border border-slate-100 whitespace-pre-wrap leading-relaxed">{{ selected.description || '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Link Demo</p>
                            <a v-if="selected.demo_url" :href="selected.demo_url" target="_blank" class="mt-2 inline-block text-sm font-bold text-[#0284c7] hover:underline break-all">{{ selected.demo_url }}</a>
                            <p v-else class="mt-2 text-sm text-slate-500">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">File Download / Akses</p>
                            <p class="mt-2 text-sm bg-slate-50 p-3 rounded-md border border-slate-100 break-all">{{ selected.file_path || 'Belum disertakan' }}</p>
                        </div>
                    </div>

                    <div v-if="isRejecting" class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <form @submit.prevent="rejectProduct">
                            <label class="block text-sm font-bold text-red-900">Alasan Penolakan</label>
                            <textarea name="rejection_reason" required v-model="rejectionReason" class="mt-2 w-full rounded-md border border-red-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500" rows="3" placeholder="Jelaskan alasan produk ini ditolak..."></textarea>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" @click="isRejecting = false" class="px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 rounded-md">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-md">Kirim Penolakan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div v-if="!isRejecting" class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50">
                    <button type="button" @click="isRejecting = true" class="rounded-md border border-red-200 bg-white px-5 py-2.5 text-sm font-black text-red-600 hover:bg-red-50">Tolak Produk</button>
                    <button type="button" class="rounded-md bg-emerald-500 px-5 py-2.5 text-sm font-black text-white hover:bg-emerald-600" @click="approveProduct">Setujui & Terbitkan</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
