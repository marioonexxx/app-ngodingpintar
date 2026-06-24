<script setup>
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import { money as formatRupiah } from '../../../Components/FormHelpers';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    partner: Object,
    withdrawals: Object,
    filters: Object,
});

const statusFilter = ref(props.filters?.status || '');

const applyFilter = () => {
    router.get('/partner/withdrawals', { status: statusFilter.value }, { preserveState: true, preserveScroll: true });
};

const form = useForm({
    amount: '',
});

const tarikSemua = () => {
    form.amount = Math.floor(props.user.balance / 10000) * 10000;
};

const submitWithdrawal = () => {
    form.post('/partner/withdrawals', {
        preserveScroll: true,
        onSuccess: () => form.reset('amount')
    });
};

const getStatusBadge = (status) => {
    const badges = {
        waiting: 'bg-amber-100 text-amber-700',
        paid: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-red-100 text-red-700',
    };
    return badges[status] || 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <Head>
        <title>Pencairan Saldo Partner</title>
        <meta name="description" content="Ajukan dan riwayat pencairan saldo." />
    </Head>
    <PartnerLayout>
        <div class="space-y-6">
            
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-2xl bg-gradient-to-br from-sky-600 to-[#0284c7] p-6 text-white shadow-lg shadow-sky-500/20 md:col-span-1">
                    <p class="text-sm font-medium text-sky-100 uppercase tracking-widest">Saldo Aktif</p>
                    <p class="mt-2 text-4xl font-black tracking-tight">{{ formatRupiah(user.balance) }}</p>
                    <div class="mt-6 border-t border-white/20 pt-4">
                        <p class="text-xs text-sky-100">Informasi Bank Pencairan:</p>
                        <p class="mt-1 text-sm font-bold truncate">
                            {{ partner?.bank_name || 'Belum diatur' }}
                        </p>
                        <p class="text-xs truncate">
                            {{ partner?.bank_account_number || '-' }} a.n {{ partner?.bank_account_name || '-' }}
                        </p>
                        <Link href="/partner/profile" class="mt-2 inline-block text-xs font-semibold text-sky-200 hover:text-white underline underline-offset-2">
                            Ubah Rekening
                        </Link>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200 md:col-span-2">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Ajukan Pencairan Baru</h2>
                    <form @submit.prevent="submitWithdrawal" class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-semibold text-slate-700">Jumlah Penarikan <span class="text-xs font-normal text-slate-500">(Min. Rp 100.000, kelipatan Rp 10.000)</span></label>
                                <button type="button" @click="tarikSemua" class="text-xs font-bold text-[#0284c7] hover:text-sky-800 transition">Tarik Maksimal</button>
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-slate-500 font-semibold">Rp</span>
                                </div>
                                <input v-model="form.amount" type="number" min="100000" :max="Math.floor(user.balance / 10000) * 10000" step="10000" class="w-full rounded-xl border border-slate-200 bg-white pl-12 pr-4 py-3 font-semibold focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" placeholder="0" required>
                            </div>
                            
                            <div class="mt-2 rounded-lg bg-sky-50 px-3 py-2 flex items-start gap-2 border border-sky-100">
                                <svg class="mt-0.5 size-4 shrink-0 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-sky-800 leading-snug">
                                    Pencairan minimal <span class="font-bold">Rp 100.000</span> dan berlaku kelipatan <span class="font-bold">Rp 10.000</span> (contoh: 110.000, 150.000).
                                </p>
                            </div>
                            
                            <div v-if="form.errors.amount" class="mt-1 text-sm text-red-500">{{ form.errors.amount }}</div>
                        </div>
                        <button type="submit" :disabled="form.processing || user.balance < 100000" class="w-full rounded-xl bg-slate-900 px-4 py-3 font-bold text-white transition hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed">
                            Tarik Saldo
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Riwayat Penarikan</h2>
                    <div class="flex items-center gap-2">
                        <select v-model="statusFilter" @change="applyFilter" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                            <option value="">Semua Status</option>
                            <option value="waiting">Menunggu (Waiting)</option>
                            <option value="paid">Selesai (Paid)</option>
                            <option value="rejected">Ditolak (Rejected)</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="border-b border-slate-100 bg-slate-50/50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold">Nominal</th>
                                <th class="px-4 py-3 font-semibold">Bank Tujuan</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Bukti/Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="withdrawal in withdrawals.data" :key="withdrawal.id" class="hover:bg-slate-50/50">
                                <td class="px-4 py-4 text-slate-600">
                                    {{ new Date(withdrawal.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-900">
                                    {{ formatRupiah(withdrawal.amount) }}
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-700">{{ withdrawal.bank_name }}</p>
                                    <p class="text-xs text-slate-500">{{ withdrawal.bank_account_number }} a.n {{ withdrawal.bank_account_name }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span :class="getStatusBadge(withdrawal.status)" class="rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                                        {{ withdrawal.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <a v-if="withdrawal.status === 'paid' && withdrawal.proof_of_transfer" :href="'/' + withdrawal.proof_of_transfer" target="_blank" class="inline-flex items-center gap-1.5 rounded-md bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-600 hover:bg-sky-100">
                                        Lihat Bukti
                                    </a>
                                    <p v-else-if="withdrawal.status === 'rejected'" class="text-xs text-red-600 max-w-[200px] truncate" :title="withdrawal.rejection_reason">
                                        {{ withdrawal.rejection_reason || 'Ditolak' }}
                                    </p>
                                    <span v-else class="text-slate-400 text-xs italic">-</span>
                                </td>
                            </tr>
                            <tr v-if="withdrawals.data.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                    Belum ada riwayat penarikan.
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
            </div>

        </div>
    </PartnerLayout>
</template>
