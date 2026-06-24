<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import { errorAlert, successAlert, confirmAction } from '@/Utils/swal';

import { Link } from '@inertiajs/vue3';

const props = defineProps({ transactions: Object, filters: Object });

const showModal = ref(false);
const selected = ref(null);
const selectedIds = ref([]);

const filterStatus = ref(props.filters?.status || 'Semua Order');
const filterPayment = ref(props.filters?.payment_status || 'Semua Payment');
const searchQuery = ref(props.filters?.search || '');
const perPage = ref(props.filters?.per_page || '15');

const applyFilters = () => {
    router.get('/admin/transactions', {
        status: filterStatus.value,
        payment_status: filterPayment.value,
        search: searchQuery.value,
        per_page: perPage.value,
    }, { preserveState: true, preserveScroll: true });
};

const verifyPayment = async (transaction, action) => {
    let rejectionReason = null;

    if (action === 'reject') {
        const { value: text, isConfirmed } = await Swal.fire({
            title: 'Tolak Pembayaran',
            text: 'Silakan masukkan alasan penolakan untuk diinformasikan kepada member.',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Bukti transfer buram atau nominal tidak sesuai...',
            showCancelButton: true,
            confirmButtonText: 'Tolak Pembayaran',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan tidak boleh kosong!';
                }
            }
        });

        if (!isConfirmed) return;
        rejectionReason = text;
    } else {
        const isConfirmed = await confirmAction({
            title: 'Validasi Pembayaran',
            text: 'Pastikan dana sudah masuk ke rekening.',
            confirmButtonText: 'Ya, Validasi',
            confirmButtonColor: '#10b981'
        });

        if (!isConfirmed) return;
    }

    router.patch(`/admin/transactions/${transaction.id}/verify`, { 
        action,
        rejection_reason: rejectionReason
    }, {
        preserveScroll: true,
        preserveState: false,
    });
};

const showProofModal = ref(false);
const proofImageUrl = ref('');

const viewProof = (transaction) => {
    proofImageUrl.value = transaction.payment_proof ? `/storage/${transaction.payment_proof}` : '';
    showProofModal.value = true;
};

const toggleAll = (e) => {
    if (e.target.checked) {
        selectedIds.value = props.transactions.data.map(t => t.id);
    } else {
        selectedIds.value = [];
    }
};

const deleteSelected = async () => {
    if (selectedIds.value.length === 0) return;
    
    const count = selectedIds.value.length;
    const isConfirmed = await confirmAction({
        title: 'Hapus Transaksi',
        text: `Yakin ingin menghapus ${count} transaksi terpilih? Data tidak bisa dikembalikan.`,
        confirmButtonText: 'Ya, Hapus',
        confirmButtonColor: '#ef4444' // red
    });

    if (isConfirmed) {
        router.post('/admin/transactions/bulk-destroy', { ids: selectedIds.value }, {
            preserveScroll: true,
            onSuccess: () => {
                selectedIds.value = [];
                successAlert('Transaksi berhasil dihapus.');
            }
        });
    }
};

const approveSelected = async () => {
    if (selectedIds.value.length === 0) return;
    
    const count = selectedIds.value.length;
    const isConfirmed = await confirmAction({
        title: 'Validasi Pembayaran',
        text: `Yakin ingin memvalidasi ${count} pembayaran transaksi terpilih?`,
        confirmButtonText: 'Ya, Validasi',
        confirmButtonColor: '#10b981'
    });

    if (isConfirmed) {
        router.post('/admin/transactions/bulk-verify', { ids: selectedIds.value }, {
            preserveScroll: true,
            onSuccess: () => {
                selectedIds.value = [];
                successAlert('Pembayaran transaksi berhasil divalidasi.');
            }
        });
    }
};

const deleteItem = async (id) => {
    const isConfirmed = await confirmAction({
        title: 'Hapus Transaksi',
        text: 'Yakin ingin menghapus transaksi ini? Data tidak bisa dikembalikan.',
        confirmButtonText: 'Ya, Hapus',
        confirmButtonColor: '#ef4444'
    });

    if (isConfirmed) {
        router.post(`/admin/transactions/${id}/destroy`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                selectedIds.value = selectedIds.value.filter(itemId => itemId !== id);
                successAlert('Transaksi berhasil dihapus.');
            }
        });
    }
};

const openStatus = (transaction) => {
    selected.value = transaction;
    showModal.value = true;
};

const submitStatus = (event) => {
    const formData = new FormData(event.currentTarget);

    router.patch(`/admin/transactions/${selected.value.id}/status`, {
        status: formData.get('status'),
        payment_status: formData.get('payment_status'),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert('Status transaksi berhasil disimpan.');
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
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Data Transaksi</h1>
                    <p class="mt-3 text-sm text-slate-500">Pantau invoice, status order, dan status pembayaran.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ transactions.total || transactions.data.length }} transaksi</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Transaksi</h2>
                    <div v-if="selectedIds.length > 0" class="flex gap-2">
                        <button @click="approveSelected" class="flex items-center gap-1.5 rounded-md bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-200">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ selectedIds.length === 1 ? 'Validasi 1 Transaksi' : `Validasi Terpilih (${selectedIds.length})` }}
                        </button>
                        <button @click="deleteSelected" class="flex items-center gap-1.5 rounded-md bg-red-100 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-200">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ selectedIds.length === 1 ? 'Hapus 1 Transaksi' : `Hapus Terpilih (${selectedIds.length})` }}
                        </button>
                    </div>
                </div>
                <div class="grid gap-2 md:flex md:items-center">
                    <select v-model="perPage" @change="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm min-w-[100px]">
                        <option value="10">10 Baris</option>
                        <option value="15">15 Baris</option>
                        <option value="25">25 Baris</option>
                        <option value="50">50 Baris</option>
                        <option value="100">100 Baris</option>
                        <option value="all">Semua</option>
                    </select>
                    <select v-model="filterStatus" @change="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm min-w-[140px]">
                        <option>Semua Order</option><option>pending</option><option>processing</option><option>completed</option><option>cancelled</option>
                    </select>
                    <select v-model="filterPayment" @change="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm min-w-[140px]">
                        <option>Semua Payment</option><option>verifying</option><option>unpaid</option><option>paid</option><option>expired</option>
                    </select>
                    <input v-model="searchQuery" @keyup.enter="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm min-w-[160px]" placeholder="Cari invoice/email...">
                    <button @click="applyFilters" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white shrink-0">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" class="rounded border-slate-300 text-[#0284c7] focus:ring-[#0284c7]" @change="toggleAll" :checked="selectedIds.length === transactions.data.length && transactions.data.length > 0">
                                    <span class="whitespace-nowrap">CHECK ALL</span>
                                </label>
                            </th>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="trx in transactions.data" :key="trx.id" class="border-t border-slate-100" :class="{'bg-sky-50/50': selectedIds.includes(trx.id)}">
                            <td class="px-4 py-4">
                                <input type="checkbox" :value="trx.id" v-model="selectedIds" class="rounded border-slate-300 text-[#0284c7] focus:ring-[#0284c7]">
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ trx.invoice_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ trx.payment_gateway || '-' }}</p>
                            </td>
                            <td class="px-4 py-4">{{ trx.user?.name }}</td>
                            <td class="px-4 py-4 font-bold">{{ money(trx.total) }}</td>
                            <td class="px-4 py-4"><span class="rounded bg-slate-100 px-3 py-1 text-xs font-bold">{{ trx.status }}</span></td>
                            <td class="px-4 py-4"><span class="rounded bg-sky-50 px-3 py-1 text-xs font-bold text-[#0284c7]">{{ trx.payment_status }}</span></td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <template v-if="trx.payment_status === 'verifying' && trx.payment_gateway === 'manual_transfer'">
                                        <button @click="viewProof(trx)" class="rounded bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-200">Lihat Bukti</button>
                                        <button @click="verifyPayment(trx, 'approve')" class="rounded bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-200">Validasi</button>
                                        <button @click="verifyPayment(trx, 'reject')" class="rounded bg-red-100 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-200">Tolak</button>
                                    </template>
                                    <template v-else>
                                        <button type="button" class="text-slate-400 hover:text-[#0284c7] transition" @click="openStatus(trx)" title="Update Status">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="text-slate-400 hover:text-red-500 transition" @click="deleteItem(trx.id)" title="Hapus">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="transactions.data.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                Belum ada transaksi yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 border-t border-slate-100 pt-4 flex flex-wrap gap-1" v-if="transactions.last_page > 1">
                <template v-for="(link, i) in transactions.links" :key="i">
                    <Link v-if="link.url" :href="link.url" class="rounded px-3 py-1 text-sm border transition" :class="link.active ? 'bg-[#0284c7] text-white border-[#0284c7]' : 'text-slate-500 border-slate-200 hover:bg-slate-50'" v-html="link.label" />
                    <span v-else class="rounded px-3 py-1 text-sm border border-slate-200 text-slate-400" v-html="link.label" />
                </template>
            </div>
        </section>

        <div v-if="showModal" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-16 backdrop-blur-sm">
            <form @submit.prevent="submitStatus" class="mx-auto w-full max-w-xl overflow-hidden rounded-md bg-white shadow-2xl shadow-slate-950/20">
                <div class="flex items-start justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-xl font-black">Update Transaksi</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ selected.invoice_number }}</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl" @click="showModal = false">×</button>
                </div>
                <div class="grid gap-4 px-5 py-5">
                    <label class="text-sm font-bold">Status Order
                        <select name="status" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <option v-for="status in ['pending','processing','completed','cancelled']" :key="status" :selected="selected.status === status">{{ status }}</option>
                        </select>
                    </label>
                    <label class="text-sm font-bold">Status Pembayaran
                        <select name="payment_status" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <option v-for="status in ['unpaid','paid','expired']" :key="status" :selected="selected.payment_status === status">{{ status }}</option>
                        </select>
                    </label>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4">
                    <button type="button" class="rounded-md border border-slate-200 px-5 py-3 text-sm font-black" @click="showModal = false">Batal</button>
                    <button class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white">Simpan Status</button>
                </div>
            </form>
        </div>
        <div v-if="showProofModal" class="fixed inset-0 z-50 grid place-items-start overflow-y-auto bg-slate-950/80 px-4 py-16 backdrop-blur-sm" @click.self="showProofModal = false">
            <div class="mx-auto w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-black">Bukti Transfer</h2>
                    <button @click="showProofModal = false" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-5 text-center">
                    <img v-if="proofImageUrl" :src="proofImageUrl" alt="Bukti Transfer" class="mx-auto max-h-[60vh] rounded-md object-contain shadow-sm border border-slate-100" />
                    <p v-else class="text-sm text-slate-500 py-10">Bukti transfer tidak tersedia.</p>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
