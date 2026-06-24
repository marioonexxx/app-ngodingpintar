<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { csrf, money } from '../../Components/FormHelpers';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    cart: Object,
    subtotal: [Number, String],
    promoInput: String,
    promoCode: Object,
    promoError: String,
    discountTotal: [Number, String],
    total: [Number, String],
    gateways: Array,
    bankAccounts: Array,
    checkoutAddons: Array,
});

const computedTotal = computed(() => {
    const sub = Number(props.subtotal || 0);
    const disc = Number(props.discountTotal || 0);
    return Math.max(0, sub - disc);
});

const isFree = computed(() => computedTotal.value <= 0);

const selectedPayment = ref(isFree.value ? 'free' : 'manual_transfer');

watch(isFree, (val) => {
    selectedPayment.value = val ? 'free' : 'manual_transfer';
});

const checkoutContext = computed(() => {
    if (!props.cart || !props.cart.items || props.cart.items.length === 0) return 'Produk Digital';
    const type = props.cart.items[0].product?.product_type;
    if (type === 'class') return 'Kelas / Webinar';
    if (type === 'coaching') return 'Coaching 1-on-1';
    if (type === 'source_code') return 'Source Code';
    return 'Produk Digital';
});
</script>

<template>
    <Head>
        <title>Checkout</title>
        <meta name="description" content="Review pesanan Anda dan selesaikan pembayaran." />
    </Head>
    <AppLayout>
        <section class="rounded-md bg-gradient-to-r from-[#0284c7] to-[#0ea5e9] p-6 text-white md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-100">NgodingPintar.id</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Checkout {{ checkoutContext }}</h1>
            <p class="mt-3 max-w-2xl text-sm font-medium text-sky-100">Review pesanan, gunakan kode promo, lalu buat transaksi pembayaran.</p>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Order Items -->
                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold">Item Pesanan</h2>
                        <span class="rounded-md bg-sky-50 px-3 py-1 text-xs font-semibold text-[#0284c7]">{{ cart.items.length }} item</span>
                    </div>

                    <div v-if="cart.items.length === 0" class="py-10 text-center text-sm font-semibold text-slate-400">
                        Keranjang masih kosong.
                    </div>

                    <div v-for="item in cart.items" :key="item.id" class="flex items-start justify-between gap-4 border-b border-slate-100 py-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ item.product?.name }}</p>
                            <p v-if="item.class_batch" class="mt-1 text-xs font-bold text-sky-600">Batch {{ item.class_batch.batch_number }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ item.quantity }} x {{ money(item.unit_price) }}</p>
                        </div>
                        <span class="font-semibold text-slate-900">{{ money(item.subtotal) }}</span>
                    </div>
                </section>


            </div>

            <!-- Right Column (Sidebar) -->
            <aside class="space-y-4">
                <!-- Promo Code -->
                <form method="get" action="/checkout" class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <label class="text-sm font-semibold text-slate-900">Kode Promo</label>
                    <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_auto] lg:grid-cols-1 xl:grid-cols-[1fr_auto]">
                        <input name="promo_code" :value="promoCode?.code || promoInput" placeholder="Contoh: MDV-PROMO" class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-bold uppercase">
                        <button class="rounded-md bg-[#0284c7] px-4 py-3 text-sm font-semibold text-white">Terapkan</button>
                    </div>
                    <p v-if="promoCode" class="mt-3 rounded-md bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700">
                        Kode {{ promoCode.code }} aktif. Diskon {{ promoCode.discount_percent }}%.
                    </p>
                    <p v-if="promoError" class="mt-3 rounded-md bg-red-50 px-3 py-2 text-sm font-bold text-red-600">{{ promoError }}</p>
                </form>

                <!-- Payment Summary -->
                <form method="post" action="/checkout" class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <input type="hidden" name="_token" :value="csrf()">
                    <input v-if="promoCode" type="hidden" name="promo_code" :value="promoCode.code">

                    <h2 class="text-xl font-semibold">Ringkasan Pembayaran</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <!-- Subtotal Produk -->
                        <div class="flex justify-between gap-4">
                            <span class="font-semibold text-slate-500">Subtotal Produk</span>
                            <span class="font-semibold">{{ money(subtotal) }}</span>
                        </div>

                        <!-- Discount -->
                        <div v-if="promoCode" class="flex justify-between gap-4 text-emerald-700">
                            <span class="font-semibold">Diskon {{ promoCode.discount_percent }}%</span>
                            <span class="font-semibold">-{{ money(discountTotal) }}</span>
                        </div>

                        <!-- Total -->
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-sm font-semibold text-slate-500">Total Bayar</p>
                            <p class="mt-1 text-3xl font-semibold" :class="isFree ? 'text-emerald-600' : 'text-slate-950'">
                                {{ isFree ? 'GRATIS' : money(computedTotal) }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <label class="mt-6 block text-sm font-semibold">Metode Pembayaran</label>
                    
                    <div class="mt-3 space-y-3">
                        <!-- FREE: Auto complete -->
                        <label v-if="isFree" class="flex items-start gap-3 rounded-lg border-2 border-emerald-400 bg-emerald-50 p-4">
                            <input type="radio" name="payment_gateway" value="free" v-model="selectedPayment" checked class="mt-1 border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <p class="text-sm font-bold text-emerald-700">Gratis — Akses Langsung</p>
                                <p class="mt-1 text-xs text-emerald-600">Pesanan akan langsung selesai dan Anda mendapat akses download atau link kelas.</p>
                            </div>
                        </label>

                        <!-- PAID: Manual Transfer -->
                        <template v-if="!isFree">
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 transition hover:bg-slate-50" :class="selectedPayment === 'manual_transfer' ? 'border-[#0284c7] bg-sky-50/50 ring-1 ring-[#0284c7]' : ''">
                                <input type="radio" name="payment_gateway" value="manual_transfer" v-model="selectedPayment" class="mt-1 border-slate-300 text-[#0284c7] focus:ring-[#0284c7]">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Transfer Bank Manual</p>
                                    <p class="mt-1 text-xs text-slate-500">Transfer langsung ke rekening kami, butuh upload bukti transfer.</p>
                                </div>
                            </label>

                            <!-- PAID: Payment Gateway (QR Code) — Coming Soon -->
                            <label class="flex cursor-not-allowed items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 opacity-60">
                                <input type="radio" value="payment_gateway" disabled class="mt-1 border-slate-300 text-slate-400">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-600">Pembayaran Online (QRIS)</p>
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">Segera Hadir</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Pembayaran otomatis via QRIS, Virtual Account, E-Wallet, dan Kartu Kredit.</p>
                                </div>
                            </label>
                        </template>
                    </div>

                    <!-- Bank Accounts Display -->
                    <div v-if="!isFree && selectedPayment === 'manual_transfer'" class="mt-4 rounded-lg bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500">Tujuan Transfer:</p>
                        <div class="mt-3 space-y-3">
                            <div v-for="bank in bankAccounts" :key="bank.id" class="rounded-md border border-slate-200 bg-white p-3">
                                <p class="text-sm font-bold text-[#0284c7]">{{ bank.bank_name }}</p>
                                <p class="mt-1 font-mono text-lg font-semibold tracking-wider text-slate-900">{{ bank.account_number }}</p>
                                <p class="text-xs font-medium text-slate-500">a.n {{ bank.account_name }}</p>
                            </div>
                        </div>
                    </div>

                    <button class="mt-6 w-full rounded-md px-4 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:bg-slate-300" :class="isFree ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-[#0284c7] hover:bg-sky-700'" :disabled="cart.items.length === 0">
                        {{ isFree ? 'Dapatkan Gratis' : 'Buat Transaksi' }}
                    </button>
                </form>
            </aside>
        </div>
    </AppLayout>
</template>

