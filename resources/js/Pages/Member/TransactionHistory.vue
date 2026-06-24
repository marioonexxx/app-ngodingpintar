<script setup>
import MemberLayout from '../../Components/MemberLayout.vue';
import { Link } from '@inertiajs/vue3';
import { money } from '../../Components/FormHelpers';

defineProps({ transactions: Object });
</script>

<template>
    <MemberLayout>
        <div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm shadow-slate-100/50">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Riwayat &amp; Download Produk</h1>
                <p class="text-xs text-slate-500 mt-1">Unduh file source code Anda dan kirimkan testimonial untuk transaksi yang telah selesai.</p>
            </div>

            <!-- List -->
            <div class="divide-y divide-slate-100">
                <div v-for="trx in transactions.data" :key="trx.id" class="py-6 first:pt-0 last:pb-0">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="font-bold text-slate-800">{{ trx.invoice_number }}</span>
                                <span 
                                    class="inline-flex items-center rounded px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                    :class="trx.status === 'completed' 
                                        ? 'bg-emerald-50 text-emerald-700' 
                                        : 'bg-slate-100 text-slate-600'"
                                >
                                    {{ trx.status }}
                                </span>
                                <span 
                                    class="inline-flex items-center rounded px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                    :class="trx.payment_status === 'paid' 
                                        ? 'bg-emerald-50 text-emerald-700' 
                                        : 'bg-sky-50 text-sky-700'"
                                >
                                    {{ trx.payment_status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Total Pembayaran: <span class="font-bold text-slate-700">{{ money(trx.total) }}</span></p>
                        </div>

                        <!-- Action Buttons (Only for completed/paid transactions) -->
                        <div v-if="trx.payment_status === 'paid' || trx.status === 'completed'" class="flex flex-wrap gap-2 text-xs md:justify-end">
                            <template v-for="item in trx.items" :key="item.id">
                                <a 
                                    v-if="!item.product || item.product.product_type === 'source_code'"
                                    :href="`/member/downloads/${item.id}`" 
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 font-bold text-white shadow-md shadow-sky-500/10 hover:bg-sky-500 hover:shadow-lg transition-all"
                                >
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span>Unduh {{ item.product_name }}</span>
                                </a>

                                <div v-if="item.product?.product_type === 'class' && item.class_batch" class="w-full rounded-md border border-sky-200 bg-[#f4f8ff] p-4 text-left">
                                    <p class="text-sm font-bold text-sky-800">Akses Kelas: {{ item.product_name }}</p>
                                    <div class="mt-2 text-xs text-slate-700">
                                        <p class="mb-2 font-bold text-sky-700">Batch {{ item.class_batch.batch_number }}</p>
                                        <p v-if="item.class_batch.schedule_type === 'fixed'">
                                            <strong>Jadwal:</strong> {{ item.class_batch.start_date }} <span v-if="item.class_batch.end_date">s/d {{ item.class_batch.end_date }}</span> | Pukul {{ item.class_batch.start_time }} - {{ item.class_batch.end_time || 'Selesai' }}
                                        </p>
                                        <p v-else>
                                            <strong>Hari Rutin:</strong> {{ item.class_batch.recurring_days?.join(', ') || '-' }} | Pukul {{ item.class_batch.start_time }} - {{ item.class_batch.end_time || 'Selesai' }}
                                        </p>
                                        <p class="mt-1"><strong>Platform:</strong> {{ item.class_batch.platform || '-' }}</p>
                                    </div>
                                    <a v-if="trx.payment_status === 'paid' && item.class_batch.meeting_link" :href="item.class_batch.meeting_link" target="_blank" class="mt-3 inline-block rounded-md bg-[#0284c7] px-4 py-2 text-xs font-bold text-white hover:bg-[#0369a1]">
                                        Buka Link Meeting / Grup
                                    </a>
                                </div>
                            </template>
                            
                            <template v-if="trx.status === 'completed'">
                                <Link 
                                    v-if="!trx.testimonials || trx.testimonials.length === 0" 
                                    :href="`/member/transactions/${trx.id}/testimonial`" 
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 font-bold text-slate-700 hover:bg-slate-50 transition-colors"
                                >
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>Kirim Testimonial</span>
                                </Link>
                                <span 
                                    v-else 
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-slate-50 border border-slate-100 px-4 py-2.5 text-xs font-bold text-slate-500"
                                >
                                    <svg class="size-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Telah Diulas
                                </span>

                                <Link 
                                    v-if="(trx.status === 'cancelled' && trx.payment_status === 'paid' && (!trx.refund_requests || trx.refund_requests.length === 0)) || (trx.project_request && trx.project_request.status === 'refund_pending' && (!trx.refund_requests || trx.refund_requests.length === 0))"
                                    :href="`/member/transactions/${trx.id}/refund`" 
                                    class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 font-bold text-red-700 hover:bg-red-100 transition-colors"
                                >
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                    </svg>
                                    <span>Ajukan Refund</span>
                                </Link>

                                <span 
                                    v-if="trx.refund_requests && trx.refund_requests.length > 0" 
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-orange-50 border border-orange-100 px-4 py-2.5 text-xs font-bold text-orange-600"
                                >
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Refund Sedang Diproses
                                </span>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Rejection Reason (Full Width Below) -->
                    <div v-if="trx.payment_status === 'rejected'" class="mt-4 rounded-xl bg-red-50 border border-red-100 p-4">
                        <div class="flex items-start gap-3">
                            <div class="rounded-full bg-red-100 p-2 text-red-600 mt-0.5">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-red-700">Pembayaran Ditolak</p>
                                <p class="text-xs text-red-600 mt-1">{{ trx.rejection_reason || 'Bukti transfer tidak valid atau nominal tidak sesuai.' }}</p>
                                <p class="text-[11px] text-red-500 font-medium mt-2">Silakan buat pesanan baru dari halaman Katalog jika Anda masih berminat dengan produk ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!transactions.data?.length" class="py-12 text-center flex flex-col items-center">
                    <div class="rounded-full bg-slate-50 p-4 text-slate-400">
                        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-bold text-slate-700">Belum ada riwayat transaksi</p>
                    <p class="mt-1 text-xs text-slate-500 max-w-xs leading-relaxed">Setelah Anda melakukan checkout dan melakukan pembayaran, riwayat transaksi Anda akan muncul di sini.</p>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
