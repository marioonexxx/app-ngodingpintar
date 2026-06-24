<script setup>
import MemberLayout from '../../Components/MemberLayout.vue';
import { money } from '../../Components/FormHelpers';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    transactions: Object,
    bankAccounts: Array,
});

const form = useForm({
    payment_proof: null,
});

const uploadingId = ref(null);

const handleFileUpload = (e, trxId) => {
    form.payment_proof = e.target.files[0];
    uploadingId.value = trxId;
    form.post(`/member/transactions/${trxId}/proof`, {
        preserveScroll: true,
        onSuccess: () => {
            uploadingId.value = null;
            form.reset();
        },
        onError: () => {
            uploadingId.value = null;
        }
    });
};
</script>

<template>
    <MemberLayout>
        <div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm shadow-slate-100/50">
            <div class="border-b border-slate-100 pb-5 mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-black text-slate-800 tracking-tight">Transaksi Aktif</h1>
                    <p class="text-xs text-slate-500 mt-1">Daftar transaksi Anda yang sedang menunggu pembayaran atau dalam proses.</p>
                </div>
            </div>

            <!-- List -->
            <div class="divide-y divide-slate-100">
                <div v-for="trx in transactions.data" :key="trx.id" class="py-5 first:pt-0 last:pb-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-800">{{ trx.invoice_number }}</span>
                                <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 uppercase tracking-wider">
                                    {{ trx.status }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700 uppercase tracking-wider">
                                    {{ trx.payment_status }}
                                </span>
                            </div>
                            <!-- Items List -->
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span v-for="item in trx.items" :key="item.id" class="inline-flex items-center rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-1 text-xs text-slate-600">
                                    {{ item.product_name }}<template v-if="item.class_batch"> · Batch {{ item.class_batch.batch_number }}</template>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6">
                            <div class="text-left sm:text-right">
                                <p class="text-xs text-slate-400">Total Pembayaran</p>
                                <p class="text-base font-black text-slate-900 mt-0.5">{{ money(trx.total) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload Bukti Transfer -->
                    <div v-if="trx.payment_gateway === 'manual_transfer' && trx.payment_status === 'unpaid'" class="mt-4 rounded-lg bg-sky-50 p-4 border border-sky-100">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Menunggu Pembayaran (Transfer Manual)</p>
                                <p class="text-xs text-slate-500 mt-1">Silakan transfer sesuai nominal ke salah satu rekening kami dan unggah buktinya.</p>
                            </div>
                            <div>
                                <input type="file" :id="`proof-${trx.id}`" accept="image/*" class="hidden" @change="(e) => handleFileUpload(e, trx.id)">
                                <label :for="`proof-${trx.id}`" class="inline-flex cursor-pointer items-center justify-center rounded-md bg-[#0284c7] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-700">
                                    <span v-if="uploadingId === trx.id && form.processing">Mengunggah...</span>
                                    <span v-else>Upload Bukti Transfer</span>
                                </label>
                            </div>
                        </div>
                        <div v-if="bankAccounts?.length" class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div v-for="bank in bankAccounts" :key="bank.id" class="rounded-lg border border-sky-200 bg-white px-4 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#0284c7]">{{ bank.bank_name }}</p>
                                <p class="mt-1 font-mono text-lg font-bold tracking-wider text-slate-900">{{ bank.account_number }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">a.n. {{ bank.account_name }}</p>
                            </div>
                        </div>
                        <p v-else class="mt-4 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">
                            Rekening transfer belum tersedia. Silakan hubungi admin.
                        </p>
                        <p v-if="uploadingId === trx.id && form.errors.payment_proof" class="mt-2 text-xs text-red-600">{{ form.errors.payment_proof }}</p>
                    </div>

                    <!-- Sedang Verifikasi -->
                    <div v-if="trx.payment_gateway === 'manual_transfer' && trx.payment_status === 'verifying'" class="mt-4 rounded-lg bg-amber-50 p-4 border border-amber-100">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="size-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800">Bukti Transfer Sedang Diverifikasi</p>
                                    <p class="text-xs text-amber-700 mt-0.5">Periksa kembali bukti Anda atau upload ulang jika file yang dikirim salah.</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a
                                    v-if="trx.payment_proof"
                                    :href="'/member/transactions/' + trx.id + '/proof'"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center justify-center rounded-md border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100"
                                >
                                    Lihat Bukti Transfer
                                </a>
                                <input type="file" :id="'reproof-' + trx.id" accept="image/*" class="hidden" @change="(e) => handleFileUpload(e, trx.id)">
                                <label :for="'reproof-' + trx.id" class="inline-flex cursor-pointer items-center justify-center rounded-md bg-amber-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-amber-700">
                                    <span v-if="uploadingId === trx.id && form.processing">Mengunggah...</span>
                                    <span v-else>Upload Ulang</span>
                                </label>
                            </div>
                        </div>
                        <p v-if="uploadingId === trx.id && form.errors.payment_proof" class="mt-2 text-xs text-red-600">{{ form.errors.payment_proof }}</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!transactions.data?.length" class="py-12 text-center flex flex-col items-center">
                    <div class="rounded-full bg-slate-50 p-4 text-slate-400">
                        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-bold text-slate-700">Tidak ada transaksi aktif</p>
                    <p class="mt-1 text-xs text-slate-500 max-w-xs leading-relaxed">Saat ini tidak ada transaksi aktif yang perlu diselesaikan.</p>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
