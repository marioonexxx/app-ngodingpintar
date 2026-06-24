<script setup>
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../../Components/AdminLayout.vue';
import { money as formatRupiah } from '../../../Components/FormHelpers';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import { successAlert, errorAlert } from '@/Utils/swal';

const props = defineProps({
    withdrawals: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const applyFilters = () => {
    router.get('/admin/partner-withdrawals', {
        search: searchQuery.value,
        status: statusFilter.value,
    }, { preserveState: true, preserveScroll: true });
};

const getStatusBadge = (status) => {
    const badges = {
        waiting: 'bg-amber-100 text-amber-700',
        paid: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-red-100 text-red-700',
    };
    return badges[status] || 'bg-slate-100 text-slate-700';
};

const fileInputRef = ref(null);
const currentWithdrawalId = ref(null);
const uploadForm = useForm({
    status: 'paid',
    proof_of_transfer_file: null,
});

const rejectForm = useForm({
    status: 'rejected',
    rejection_reason: '',
});

const triggerUpload = (id) => {
    currentWithdrawalId.value = id;
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
};

const handleFileUpload = (e) => {
    if (!e.target.files.length || !currentWithdrawalId.value) return;
    
    uploadForm.proof_of_transfer_file = e.target.files[0];
    uploadForm.post(`/admin/partner-withdrawals/${currentWithdrawalId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            successAlert('Bukti transfer berhasil diunggah dan status diubah menjadi Paid.');
            uploadForm.reset();
            e.target.value = '';
            currentWithdrawalId.value = null;
        },
        onError: () => {
            errorAlert('Gagal mengunggah bukti transfer.');
        }
    });
};

const handleReject = async (id) => {
    const { value: reason } = await Swal.fire({
        title: 'Tolak Pencairan',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan',
        inputPlaceholder: 'Tulis alasan kenapa pencairan ini ditolak...',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Tolak & Kembalikan Saldo',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Anda wajib mengisi alasan penolakan!';
            }
        }
    });

    if (reason) {
        rejectForm.rejection_reason = reason;
        rejectForm.post(`/admin/partner-withdrawals/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                successAlert('Pencairan berhasil ditolak dan saldo telah dikembalikan.');
                rejectForm.reset();
            }
        });
    }
};
</script>

<template>
    <Head>
        <title>Pencairan Saldo Partner</title>
        <meta name="description" content="Kelola pengajuan pencairan saldo (withdrawals) partner." />
    </Head>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Pencairan Saldo</h1>
                    <p class="mt-3 text-sm text-slate-500">Verifikasi dan proses pengajuan penarikan saldo partner.</p>
                </div>
                <span class="rounded-md bg-amber-50 px-4 py-3 text-sm font-black text-amber-700 border border-amber-200">
                    {{ withdrawals.data.filter(w => w.status === 'waiting').length }} Menunggu Proses
                </span>
            </div>
        </section>

        <!-- Hidden File Input -->
        <input type="file" ref="fileInputRef" class="hidden" accept="image/*" @change="handleFileUpload">

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5 border border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <!-- Placeholder for left side actions -->
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="statusFilter" @change="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                        <option value="">Semua Status</option>
                        <option value="waiting">Waiting (Menunggu Transfer)</option>
                        <option value="paid">Paid (Sudah Ditransfer)</option>
                        <option value="rejected">Rejected (Ditolak)</option>
                    </select>
                    <input v-model="searchQuery" @keyup.enter="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" placeholder="Cari nama / email...">
                    <button @click="applyFilters" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white hover:bg-sky-600 transition">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-md">Tanggal</th>
                            <th class="px-4 py-3">Partner</th>
                            <th class="px-4 py-3">Nominal & Bank Tujuan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-tr-md">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="withdrawal in withdrawals.data" :key="withdrawal.id" class="hover:bg-slate-50/50">
                            <td class="px-4 py-4 text-slate-600">
                                {{ new Date(withdrawal.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-900">{{ withdrawal.user.name }}</p>
                                <p class="text-xs text-slate-500">{{ withdrawal.user.email }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-900 text-base text-[#0284c7]">{{ formatRupiah(withdrawal.amount) }}</p>
                                <p class="mt-1 font-bold text-slate-700 text-xs">{{ withdrawal.bank_name }}</p>
                                <p class="text-xs text-slate-500">{{ withdrawal.bank_account_number }} a.n {{ withdrawal.bank_account_name }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span :class="getStatusBadge(withdrawal.status)" class="rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                                    {{ withdrawal.status }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div v-if="withdrawal.status === 'waiting'" class="flex gap-2">
                                    <button @click="triggerUpload(withdrawal.id)" :disabled="uploadForm.processing && currentWithdrawalId === withdrawal.id" class="rounded bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-600 transition disabled:opacity-50">
                                        <span v-if="uploadForm.processing && currentWithdrawalId === withdrawal.id">Proses...</span>
                                        <span v-else>Upload Bukti (Paid)</span>
                                    </button>
                                    <button @click="handleReject(withdrawal.id)" class="rounded bg-red-100 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-200 transition">
                                        Tolak
                                    </button>
                                </div>
                                <div v-else-if="withdrawal.status === 'paid' && withdrawal.proof_of_transfer">
                                    <a :href="'/' + withdrawal.proof_of_transfer" target="_blank" class="rounded bg-sky-50 border border-sky-200 px-3 py-1.5 text-xs font-bold text-sky-600 hover:bg-sky-100 inline-block">
                                        Lihat Bukti Transfer
                                    </a>
                                </div>
                                <div v-else-if="withdrawal.status === 'rejected'">
                                    <p class="text-xs text-red-600 max-w-[200px] whitespace-normal" :title="withdrawal.rejection_reason">
                                        <span class="font-bold block">Alasan:</span>
                                        {{ withdrawal.rejection_reason }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="withdrawals.data.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                Belum ada data pengajuan pencairan saldo.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex flex-wrap gap-1" v-if="withdrawals.last_page > 1">
                <template v-for="(link, i) in withdrawals.links" :key="i">
                    <Link v-if="link.url" :href="link.url" class="rounded border px-3 py-1 text-sm transition" :class="link.active ? 'border-[#0284c7] bg-[#0284c7] text-white' : 'border-slate-200 text-slate-500 hover:bg-slate-50'" v-html="link.label" />
                    <span v-else class="rounded border border-slate-200 px-3 py-1 text-sm text-slate-400" v-html="link.label" />
                </template>
            </div>
        </section>
    </AdminLayout>
</template>
