<script setup>
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';

defineProps({ metrics: Object, latestTransactions: Array });
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.35em] text-sky-700">Administrator</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight">Dashboard Administrator</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                        Resume katalog, transaksi, pembayaran, testimonial, dan aktivitas NgodingPintar.id.
                    </p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-medium text-sky-600">NgodingPintar.id</span>
            </div>
        </section>

        <section class="mt-5 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <span class="rounded bg-sky-50 px-3 py-1 text-xs font-medium uppercase text-sky-600">Source Code</span>
                <p class="mt-5 text-3xl font-semibold">{{ metrics.source_codes || 0 }}</p>
            </div>
            <div class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <span class="rounded bg-teal-50 px-3 py-1 text-xs font-medium uppercase text-teal-600">Kelas</span>
                <p class="mt-5 text-3xl font-semibold">{{ metrics.class_products || 0 }}</p>
            </div>
            <div class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <span class="rounded bg-cyan-50 px-3 py-1 text-xs font-medium uppercase text-cyan-600">Revenue</span>
                <p class="mt-5 text-3xl font-semibold">{{ money(metrics.paid_revenue || 0) }}</p>
            </div>
            <div class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <span class="rounded bg-sky-50 px-3 py-1 text-xs font-medium uppercase text-sky-600">Pending Order</span>
                <p class="mt-5 text-3xl font-semibold">{{ metrics.pending_orders || 0 }}</p>
            </div>
            <div class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <span class="rounded bg-slate-100 px-3 py-1 text-xs font-medium uppercase text-slate-700">Testimonial</span>
                <p class="mt-5 text-3xl font-semibold">{{ metrics.pending_testimonials || 0 }}</p>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_.9fr]">
            <div class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Produk per Status</h2>
                        <p class="mt-2 text-sm text-slate-500">Pantau kesiapan katalog dan produk digital.</p>
                    </div>
                    <span class="text-sm font-medium text-sky-600">3 status</span>
                </div>
                <div class="mt-8 space-y-6">
                    <div>
                        <div class="flex justify-between text-sm font-medium"><span>Digital Products</span><span>{{ metrics.source_codes || 0 }}</span></div>
                        <div class="mt-2 h-3 rounded-full bg-slate-100"><div class="h-3 w-4/5 rounded-full bg-gradient-to-r from-sky-600 to-cyan-500"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm font-medium"><span>Class Products</span><span>{{ metrics.class_products || 0 }}</span></div>
                        <div class="mt-2 h-3 rounded-full bg-slate-100"><div class="h-3 w-4/5 rounded-full bg-teal-400"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm font-medium"><span>Payment Ready</span><span>{{ metrics.pending_orders || 0 }}</span></div>
                        <div class="mt-2 h-3 rounded-full bg-slate-100"><div class="h-3 w-1/3 rounded-full bg-cyan-300"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm font-medium"><span>Need Review</span><span>{{ metrics.pending_testimonials || 0 }}</span></div>
                        <div class="mt-2 h-3 rounded-full bg-slate-100"><div class="h-3 w-1/5 rounded-full bg-sky-300"></div></div>
                    </div>
                </div>
            </div>

            <div class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
                <h2 class="text-xl font-semibold">Antrian Validasi</h2>
                <div class="mt-5 space-y-3">
                    <div class="rounded-md border border-sky-200 bg-sky-50 p-4">
                        <p class="text-sm font-medium text-sky-700">Pembayaran menunggu</p>
                        <p class="mt-2 text-3xl font-semibold text-sky-700">{{ metrics.pending_orders || 0 }}</p>
                    </div>
                    <div class="rounded-md border border-cyan-200 bg-cyan-50 p-4">
                        <p class="text-sm font-medium text-cyan-700">Transaksi paid</p>
                        <p class="mt-2 text-3xl font-semibold text-cyan-700">{{ money(metrics.paid_revenue || 0) }}</p>
                    </div>
                    <div class="rounded-md border border-sky-200 bg-sky-50 p-4">
                        <p class="text-sm font-medium text-sky-700">Testimonial pending</p>
                        <p class="mt-2 text-3xl font-semibold text-sky-700">{{ metrics.pending_testimonials || 0 }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-3">
            <div class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5 xl:col-span-2">
                <h2 class="text-xl font-semibold">Status Pembayaran</h2>
                <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between rounded-md bg-slate-50 px-4 py-4 text-sm font-medium"><span>Belum dibayar</span><span>{{ metrics.pending_orders || 0 }}</span></div>
                    <div class="flex items-center justify-between rounded-md bg-slate-50 px-4 py-4 text-sm font-medium"><span>Paid revenue</span><span>{{ money(metrics.paid_revenue || 0) }}</span></div>
                    <div class="flex items-center justify-between rounded-md bg-slate-50 px-4 py-4 text-sm font-medium"><span>Testimonial pending</span><span>{{ metrics.pending_testimonials || 0 }}</span></div>
                </div>
            </div>
            <div class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
                <h2 class="text-xl font-semibold">Quick Actions</h2>
                <div class="mt-5 grid gap-3">
                    <a href="/admin/products" class="rounded-md bg-gradient-to-r from-sky-600 to-cyan-500 px-4 py-3 text-sm font-medium text-white">Kelola Produk</a>
                    <a href="/admin/transactions" class="rounded-md bg-slate-100 px-4 py-3 text-sm font-medium text-slate-800">Cek Transaksi</a>
                    <a href="/admin/testimonials" class="rounded-md bg-slate-100 px-4 py-3 text-sm font-medium text-slate-800">Approval Testimonial</a>
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <h2 class="text-xl font-semibold">User Terbaru / Transaksi Terbaru</h2>
            <div v-for="trx in latestTransactions" :key="trx.id" class="mt-4 grid gap-2 border-t border-slate-100 pt-4 text-sm md:grid-cols-[1fr_140px_120px]">
                <span>{{ trx.invoice_number }} - {{ trx.user?.name }}</span>
                <span>{{ money(trx.total) }}</span>
                <span class="font-medium">{{ trx.payment_status }}</span>
            </div>
            <p v-if="!latestTransactions?.length" class="mt-4 text-sm text-slate-500">Belum ada transaksi.</p>
        </section>
    </AdminLayout>
</template>
