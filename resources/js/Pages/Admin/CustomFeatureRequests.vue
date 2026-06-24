<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';
import { ref } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

defineProps({
    requests: Object,
    statuses: Array,
    currentStatus: String,
    currentSearch: String,
});

const showModal = ref(false);
const selected = ref(null);

const openDetail = (req) => {
    selected.value = { ...req };
    showModal.value = true;
};

const statusBadge = (status) => {
    const map = {
        pending_review: 'bg-amber-50 text-amber-700 border-amber-200',
        quoted: 'bg-sky-50 text-sky-700 border-sky-200',
        approved: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        rejected: 'bg-red-50 text-red-700 border-red-200',
        completed: 'bg-teal-50 text-teal-700 border-teal-200',
    };
    return map[status] || 'bg-slate-100 text-slate-600';
};

const statusLabel = (status) => {
    const map = {
        pending_review: 'Pending Review',
        quoted: 'Quoted',
        approved: 'Approved',
        rejected: 'Rejected',
        completed: 'Completed',
    };
    return map[status] || status;
};

const submitStatus = (event) => {
    const formData = new FormData(event.currentTarget);

    router.patch(`/admin/custom-feature-requests/${selected.value.id}/status`, {
        status: formData.get('status'),
        quoted_amount: formData.get('quoted_amount'),
        admin_notes: formData.get('admin_notes'),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert('Status custom request berhasil diperbarui.');
        },
        onError: () => errorAlert(),
    });
};

const createInvoice = async () => {
    const confirmed = await confirmAction({
        title: 'Buat invoice?',
        text: `Invoice sebesar ${money(selected.value.quoted_amount)} akan dibuat untuk customer.`,
        confirmButtonText: 'Ya, buat invoice',
    });

    if (!confirmed) return;

    router.post(`/admin/custom-feature-requests/${selected.value.id}/invoice`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert('Invoice custom request berhasil dibuat.');
        },
        onError: () => errorAlert('Invoice gagal dibuat.'),
    });
};

</script>

<template>
    <AdminLayout>
        <!-- Header -->
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Custom Feature Requests</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola request fitur custom dari customer.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ requests.total || requests.data.length }} request</span>
            </div>
        </section>

        <!-- Filter & Table -->
        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-black">Daftar Request</h2>
                <form method="get" action="/admin/custom-feature-requests" class="grid gap-2 md:grid-cols-[170px_220px_80px]">
                    <select name="status" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Status</option>
                        <option v-for="s in statuses" :key="s" :value="s" :selected="currentStatus === s">{{ statusLabel(s) }}</option>
                    </select>
                    <input name="search" :value="currentSearch" class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari member/invoice...">
                    <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </form>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Quoted</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="req in requests.data" :key="req.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold text-slate-500">#{{ req.id }}</td>
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ req.user?.name }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ req.user?.email }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-xs font-bold text-slate-600">{{ req.transaction?.invoice_number || '-' }}</span>
                            </td>
                            <td class="max-w-[240px] px-4 py-4">
                                <p class="truncate text-sm text-slate-600">{{ req.description }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider" :class="statusBadge(req.status)">
                                    {{ statusLabel(req.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 font-bold">
                                <span v-if="req.quoted_amount">{{ money(req.quoted_amount) }}</span>
                                <span v-else class="text-xs text-slate-400">-</span>
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-500">{{ new Date(req.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</td>
                            <td class="px-4 py-4 text-right">
                                <button type="button" class="font-bold text-[#0284c7]" @click="openDetail(req)">Detail</button>
                            </td>
                        </tr>
                        <tr v-if="requests.data.length === 0">
                            <td colspan="8" class="px-4 py-10 text-center text-sm font-semibold text-slate-400">Belum ada custom feature request.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Detail Modal -->
        <div v-if="showModal && selected" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-16 backdrop-blur-sm" @click.self="showModal = false">
            <div class="mx-auto w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl shadow-slate-950/20">
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="text-xl font-black">Detail Custom Request</h2>
                        <p class="mt-1 text-sm text-slate-500">#{{ selected.id }} — {{ selected.transaction?.invoice_number || 'N/A' }}</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl hover:bg-slate-200 transition-colors" @click="showModal = false">×</button>
                </div>

                <!-- Modal Body -->
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5 space-y-5">
                    <!-- Request Info Card -->
                    <div class="rounded-lg border border-slate-100 bg-slate-50/50 p-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs font-bold uppercase text-slate-400">Member</p>
                                <p class="mt-1 font-bold">{{ selected.user?.name }}</p>
                                <p class="text-xs text-slate-500">{{ selected.user?.email }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase text-slate-400">Produk</p>
                                <p class="mt-1 font-bold">{{ selected.product?.name || 'Tidak spesifik' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Deskripsi Request</p>
                        <div class="mt-2 rounded-lg border border-slate-100 bg-white p-4 text-sm leading-relaxed text-slate-700">
                            {{ selected.description }}
                        </div>
                    </div>

                    <!-- Vendor Notes (read-only) -->
                    <div v-if="selected.vendor_notes">
                        <p class="text-xs font-bold uppercase text-slate-400">Catatan Vendor</p>
                        <div class="mt-2 rounded-lg border border-slate-100 bg-amber-50/50 p-4 text-sm leading-relaxed text-slate-700">
                            {{ selected.vendor_notes }}
                        </div>
                    </div>

                    <!-- Update Form -->
                    <form @submit.prevent="submitStatus">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-bold">
                                Status
                                <select name="status" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm">
                                    <option v-for="s in statuses" :key="s" :value="s" :selected="selected.status === s">{{ statusLabel(s) }}</option>
                                </select>
                            </label>
                            <label class="block text-sm font-bold">
                                Quoted Amount (Rp)
                                <input type="number" name="quoted_amount" :value="selected.quoted_amount" min="0" step="1000" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm" placeholder="0">
                            </label>
                        </div>

                        <label class="mt-4 block text-sm font-bold">
                            Catatan Admin
                            <textarea name="admin_notes" rows="3" class="mt-2 w-full resize-none rounded-md border border-slate-200 px-4 py-3 text-sm" placeholder="Tambahkan catatan...">{{ selected.admin_notes }}</textarea>
                        </label>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                            <button type="submit" class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white">
                                Update Status
                            </button>
                        </div>
                    </form>

                    <!-- Create Invoice (separate form) -->
                    <form v-if="selected.status === 'quoted' && selected.quoted_amount > 0" class="border-t border-slate-100 pt-4" @submit.prevent="createInvoice">
                        <div class="flex items-center justify-between rounded-lg bg-emerald-50 p-4">
                            <div>
                                <p class="text-sm font-black text-emerald-800">Buat Custom Invoice</p>
                                <p class="mt-0.5 text-xs text-emerald-600">Invoice sebesar {{ money(selected.quoted_amount) }} akan dibuat untuk customer.</p>
                            </div>
                            <button type="submit" class="rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-black text-white hover:bg-emerald-700 transition-colors">
                                Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
