<script setup>
import AppLayout from '@/Components/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    projectRequest: Object,
    bankAccounts: Array,
});

const form = useForm({
    payment_gateway: 'manual_transfer',
});

const submit = () => {
    form.post(route('member.project-requests.process-payment', props.projectRequest.id));
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head>
        <title>Pembayaran Project</title>
        <meta name="description" content="Review tagihan project Anda dan buat transaksi pembayaran." />
    </Head>
    
    <AppLayout>
        <section class="rounded-md bg-gradient-to-r from-[#0284c7] to-[#0ea5e9] p-6 text-white md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-100">NgodingPintar.id</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Pembayaran Project</h1>
            <p class="mt-3 max-w-2xl text-sm font-medium text-sky-100">Review tagihan project Anda dan buat transaksi pembayaran.</p>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Order Items -->
                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold">Detail Tagihan</h2>
                    </div>

                    <div class="flex items-start justify-between gap-4 py-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ projectRequest.title }}</p>
                            <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ projectRequest.description }}</p>
                        </div>
                        <span class="font-semibold text-slate-900">{{ formatPrice(projectRequest.budget) }}</span>
                    </div>
                </section>
            </div>

            <!-- Right Column (Sidebar) -->
            <aside class="space-y-4">
                <!-- Payment Summary -->
                <form @submit.prevent="submit" class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <h2 class="text-xl font-semibold">Ringkasan Pembayaran</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <!-- Subtotal -->
                        <div class="flex justify-between gap-4">
                            <span class="font-semibold text-slate-500">Biaya Project</span>
                            <span class="font-semibold">{{ formatPrice(projectRequest.budget) }}</span>
                        </div>

                        <!-- Total -->
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-sm font-semibold text-slate-500">Total Bayar</p>
                            <p class="mt-1 text-3xl font-semibold text-slate-950">
                                {{ formatPrice(projectRequest.budget) }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <label class="mt-6 block text-sm font-semibold">Metode Pembayaran</label>
                    
                    <div class="mt-3 space-y-3">
                        <!-- PAID: Manual Transfer -->
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 transition hover:bg-slate-50" :class="form.payment_gateway === 'manual_transfer' ? 'border-[#0284c7] bg-sky-50/50 ring-1 ring-[#0284c7]' : ''">
                            <input type="radio" name="payment_gateway" value="manual_transfer" v-model="form.payment_gateway" class="mt-1 border-slate-300 text-[#0284c7] focus:ring-[#0284c7]">
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
                    </div>

                    <!-- Bank Accounts Display -->
                    <div v-if="form.payment_gateway === 'manual_transfer'" class="mt-4 rounded-lg bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500">Tujuan Transfer:</p>
                        <div class="mt-3 space-y-3">
                            <div v-for="bank in bankAccounts" :key="bank.id" class="rounded-md border border-slate-200 bg-white p-3">
                                <p class="text-sm font-bold text-[#0284c7]">{{ bank.bank_name }}</p>
                                <p class="mt-1 font-mono text-lg font-semibold tracking-wider text-slate-900">{{ bank.account_number }}</p>
                                <p class="text-xs font-medium text-slate-500">a.n {{ bank.account_name }}</p>
                            </div>
                        </div>
                    </div>

                    <button :disabled="form.processing" class="mt-6 w-full rounded-md px-4 py-3 font-semibold text-white bg-[#0284c7] hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-50">
                        Bayar Sekarang
                    </button>
                </form>
            </aside>
        </div>
    </AppLayout>
</template>
