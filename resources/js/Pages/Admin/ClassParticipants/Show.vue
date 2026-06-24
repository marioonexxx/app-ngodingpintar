<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../../Components/AdminLayout.vue';

defineProps({
    product: Object,
    batches: Array,
    selectedBatchId: Number,
    participants: Object,
});
</script>

<template>
    <Head :title="`Peserta: ${product.name}`" />
    <AdminLayout>
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <Link href="/admin/class-participants" class="grid size-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Peserta Kelas</h1>
                <p class="mt-1 text-sm font-semibold text-[#0284c7]">{{ product.name }}</p>
            </div>
            <form method="get" class="ml-auto flex items-center gap-2">
                <select name="batch_id" class="rounded-md border border-slate-200 px-4 py-2 text-sm">
                    <option v-for="batch in batches" :key="batch.id" :value="batch.id" :selected="batch.id === selectedBatchId">
                        Batch {{ batch.batch_number }} — {{ batch.status }}
                    </option>
                </select>
                <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-bold text-white">Tampilkan</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Peserta</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Tgl Pembayaran</th>
                        <th class="px-6 py-4 font-semibold">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-if="!participants.data || participants.data.length === 0">
                        <td colspan="4" class="py-10 text-center text-slate-500">Belum ada peserta yang lunas untuk kelas ini.</td>
                    </tr>
                    <tr v-for="participant in participants.data" :key="participant.id" class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-bold text-slate-900">{{ participant.transaction?.user?.name }}</td>
                        <td class="px-6 py-4">{{ participant.transaction?.user?.email }}</td>
                        <td class="px-6 py-4">{{ participant.transaction?.paid_at ? new Date(participant.transaction.paid_at).toLocaleString('id-ID') : '-' }}</td>
                        <td class="px-6 py-4 font-mono text-xs">{{ participant.transaction?.invoice_number }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
