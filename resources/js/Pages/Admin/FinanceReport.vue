<script setup>
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';

defineProps({ summary: Object, monthly: Array });
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Laporan Keuangan</h1>
                    <p class="mt-3 text-sm text-slate-500">Ringkasan revenue, pending amount, dan transaksi paid NgodingPintar.id.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">Month to Date</span>
            </div>
        </section>

        <section class="mt-5 grid gap-4 md:grid-cols-3">
            <div v-for="(value, key) in summary" :key="key" class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <p class="rounded bg-sky-50 px-3 py-1 text-xs font-black uppercase text-[#0284c7]">{{ key.replaceAll('_', ' ') }}</p>
                <p class="mt-5 text-3xl font-black">{{ key.includes('transactions') ? value : money(value) }}</p>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-black">Pendapatan Bulanan</h2>
                <div class="grid gap-2 md:grid-cols-[180px_180px_80px]">
                    <input type="date" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                    <input type="date" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                    <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Filter</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3">Total Revenue</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in monthly" :key="`${row.year}-${row.month}`" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold">{{ row.month }}/{{ row.year }}</td>
                            <td class="px-4 py-4 font-bold">{{ money(row.total) }}</td>
                            <td class="px-4 py-4"><span class="rounded bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">paid</span></td>
                        </tr>
                    </tbody>
                </table>
                <p v-if="!monthly?.length" class="px-4 py-6 text-sm text-slate-500">Belum ada transaksi paid untuk ditampilkan.</p>
            </div>
        </section>
    </AdminLayout>
</template>
