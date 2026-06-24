<script setup>
import MemberLayout from '../../Components/MemberLayout.vue';
import { money } from '../../Components/FormHelpers';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({ activeTransactions: Array, paidCount: Number });

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name || 'Member');
</script>

<template>
    <MemberLayout>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-600 to-cyan-500 p-6 text-white shadow-lg shadow-sky-500/10 sm:p-8">
            <div class="absolute -right-10 -top-10 size-40 rounded-full bg-white/10 blur-xl"></div>

            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[10px] font-medium uppercase tracking-wider text-sky-50 shadow-inner">
                    Member Area
                </span>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl">Selamat Datang Kembali, {{ userName }}!</h1>
                <p class="mt-2 max-w-xl text-sm text-sky-50/90">
                    Kelola lisensi produk digital, pantau riwayat transaksi, dan download source code premium Anda langsung dari portal member.
                </p>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/40 transition-all duration-300 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Pembelian Sukses</p>
                        <p class="mt-2.5 text-4xl font-semibold tracking-tight text-slate-900">{{ paidCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-sky-50 p-4 text-sky-600">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-4 text-xs text-slate-400">
                    <span>Semua produk berlisensi aktif</span>
                    <Link href="/member/transactions/history" class="font-medium text-sky-600 hover:text-sky-700 hover:underline">Riwayat &amp; Unduh &rarr;</Link>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/40 transition-all duration-300 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Transaksi Aktif</p>
                        <p class="mt-2.5 text-4xl font-semibold tracking-tight text-slate-900">{{ activeTransactions.length }}</p>
                    </div>
                    <div class="rounded-2xl bg-cyan-50 p-4 text-cyan-600">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-4 text-xs text-slate-400">
                    <span>Menunggu pembayaran atau proses</span>
                    <Link href="/member/transactions/active" class="font-medium text-sky-600 hover:text-sky-700 hover:underline">Kelola Transaksi &rarr;</Link>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/40">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-5">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight text-slate-800">Transaksi Terbaru</h2>
                    <p class="mt-1 text-xs text-slate-500">Daftar invoice transaksi aktif atau pembayaran terbaru Anda.</p>
                </div>
                <Link href="/member/transactions/history" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 transition-colors hover:bg-sky-50 hover:text-sky-600">
                    Lihat Semua
                </Link>
            </div>

            <div class="divide-y divide-slate-100">
                <div v-for="trx in activeTransactions" :key="trx.id" class="flex flex-col gap-3 py-4 first:pt-3 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-slate-50 p-2.5 text-slate-500">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ trx.invoice_number }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded bg-cyan-50 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-cyan-700">
                                    {{ trx.status }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded bg-sky-50 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-sky-700">
                                    {{ trx.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4 sm:justify-end">
                        <span class="text-sm font-semibold text-slate-950">{{ money(trx.total) }}</span>
                        <Link :href="`/member/transactions/active`" class="rounded-full bg-sky-50 px-3.5 py-1.5 text-xs font-medium text-sky-600 transition-colors hover:bg-sky-100">
                            Detail
                        </Link>
                    </div>
                </div>

                <div v-if="!activeTransactions.length" class="flex flex-col items-center py-10 text-center">
                    <div class="rounded-full bg-slate-50 p-4 text-slate-400">
                        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4.5m12 3V21m-6 0v-3" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-medium text-slate-700">Tidak ada transaksi aktif</p>
                    <p class="mt-1 max-w-xs text-xs leading-relaxed text-slate-500">Anda tidak memiliki transaksi aktif yang tertunda saat ini. Silakan kunjungi katalog kami untuk membeli produk baru.</p>
                    <a href="/products" class="mt-4 rounded-xl bg-gradient-to-r from-sky-600 to-cyan-500 px-4 py-2 text-xs font-medium text-white shadow-md shadow-sky-500/10 transition-all hover:shadow-lg">
                        Cari Produk
                    </a>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
