<script setup>
import { Head } from '@inertiajs/vue3';
import PartnerLayout from '../../Components/PartnerLayout.vue';
import NavIcon from '../../Components/NavIcon.vue';

defineProps({
    user: Object,
    totalEarnings: Number,
    recentEarnings: Array,
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Partner Dashboard" />
    <PartnerLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900">Selamat datang, {{ user.name }}!</h1>
            <p class="text-sm text-slate-500">Pantau performa produk dan layanan Anda di NgodingPintar.id.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="grid size-12 place-items-center rounded-xl bg-sky-50 text-sky-600">
                        <NavIcon name="box" icon-class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Produk</p>
                        <p class="text-2xl font-semibold text-slate-900">0</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="grid size-12 place-items-center rounded-xl bg-cyan-50 text-cyan-600">
                        <NavIcon name="chart" icon-class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Saldo Aktif</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ formatRupiah(user.balance || 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="grid size-12 place-items-center rounded-xl bg-cyan-50 text-cyan-600">
                        <NavIcon name="chart" icon-class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ formatRupiah(totalEarnings || 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="grid size-12 place-items-center rounded-xl bg-sky-50 text-sky-600">
                        <NavIcon name="star" icon-class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Rating Rata-rata</p>
                        <p class="text-2xl font-semibold text-slate-900">0.0</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Penjualan Terbaru</h2>
            <div v-if="recentEarnings?.length > 0" class="mt-4 divide-y divide-slate-100">
                <div v-for="earning in recentEarnings" :key="earning.id" class="flex items-center justify-between py-4">
                    <div>
                        <p class="font-medium text-slate-900">{{ earning.product?.name || 'Produk' }}</p>
                        <p class="text-sm text-slate-500">{{ new Date(earning.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-emerald-600">+ {{ formatRupiah(earning.net_earning) }}</p>
                        <p class="text-xs text-slate-400">Fee: {{ formatRupiah(earning.platform_fee) }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="mt-4 py-10 text-center">
                <p class="text-sm text-slate-500">Belum ada aktivitas yang dapat ditampilkan.</p>
            </div>
        </div>
    </PartnerLayout>
</template>
