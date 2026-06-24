<script setup>
import { Link } from '@inertiajs/vue3';
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import { money, date } from '../../../Components/FormHelpers';

defineProps({ 
    earnings: Object, 
    totalEarnings: [Number, String],
    currentType: String,
    isVendor: Boolean,
    isMentor: Boolean
});
</script>

<template>
    <PartnerLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Partner Panel</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Riwayat Pendapatan</h1>
                    <p class="mt-3 text-sm text-slate-500">Pantau detail transaksi dan penghasilan dari produk atau kelas Anda.</p>
                </div>
            </div>
        </section>

        <!-- Tabs -->
        <div class="mt-5 flex gap-2 border-b border-slate-200">
            <Link 
                v-if="isVendor"
                href="/partner/earnings?type=source_code" 
                class="px-5 py-3 text-sm font-bold border-b-2 transition"
                :class="currentType === 'source_code' ? 'border-[#0284c7] text-[#0284c7]' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
                Penjualan Source Code
            </Link>
            <Link 
                v-if="isMentor"
                href="/partner/earnings?type=class" 
                class="px-5 py-3 text-sm font-bold border-b-2 transition"
                :class="currentType === 'class' ? 'border-[#0284c7] text-[#0284c7]' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
                Penjualan Kelas
            </Link>
        </div>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h2 class="text-lg font-black">Detail Transaksi ({{ currentType === 'source_code' ? 'Source Code' : 'Kelas' }})</h2>
                <div class="rounded-md bg-emerald-50 px-4 py-2 border border-emerald-100">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Total Pendapatan Bersih</p>
                    <p class="text-lg font-black text-emerald-800">{{ money(totalEarnings) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Tanggal / Invoice</th>
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-right">Harga Final</th>
                            <th class="px-4 py-3 text-right">Fee (20%)</th>
                            <th class="px-4 py-3 text-right text-emerald-600">Pendapatan Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="earning in earnings.data" :key="earning.id" class="transition hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ earning.transaction?.invoice_number }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ date(earning.created_at) }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ earning.transaction?.user?.name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ earning.transaction?.user?.email }}</p>
                            </td>
                            <td class="px-4 py-4 max-w-xs">
                                <p class="font-medium truncate" :title="earning.product?.name">{{ earning.product?.name || '-' }}</p>
                            </td>
                            <td class="px-4 py-4 text-right">{{ money(earning.amount) }}</td>
                            <td class="px-4 py-4 text-right text-red-500">-{{ money(earning.platform_fee) }}</td>
                            <td class="px-4 py-4 text-right font-black text-emerald-600">{{ money(earning.net_earning) }}</td>
                        </tr>
                        <tr v-if="earnings.data.length === 0">
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 border-t border-slate-100 pt-4 flex flex-wrap gap-1" v-if="earnings.last_page > 1">
                <template v-for="(link, i) in earnings.links" :key="i">
                    <Link v-if="link.url" :href="link.url" class="rounded px-3 py-1 text-sm border transition" :class="link.active ? 'bg-[#0284c7] text-white border-[#0284c7]' : 'text-slate-500 border-slate-200 hover:bg-slate-50'" v-html="link.label" />
                    <span v-else class="rounded px-3 py-1 text-sm border border-slate-200 text-slate-400" v-html="link.label" />
                </template>
            </div>
        </section>
    </PartnerLayout>
</template>
