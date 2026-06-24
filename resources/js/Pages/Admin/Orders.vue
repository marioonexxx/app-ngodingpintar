<script setup>
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';

defineProps({ orders: Object });
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">List Pesanan</h1>
                    <p class="mt-3 text-sm text-slate-500">Pesanan aktif yang masih pending atau processing.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ orders.total || orders.data.length }} pesanan</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-black">Daftar Pesanan</h2>
                <div class="grid gap-2 md:grid-cols-[170px_220px_80px]">
                    <select class="rounded-md border border-slate-200 px-3 py-2 text-sm"><option>Semua Status</option><option>pending</option><option>processing</option></select>
                    <input class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari invoice/member...">
                    <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[920px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="order.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold">{{ order.invoice_number }}</td>
                            <td class="px-4 py-4">{{ order.user?.name }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ order.items.map((item) => item.product_name).join(', ') }}</td>
                            <td class="px-4 py-4 font-bold">{{ money(order.total) }}</td>
                            <td class="px-4 py-4"><span class="rounded bg-sky-50 px-3 py-1 text-xs font-bold text-[#0284c7]">{{ order.status }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
