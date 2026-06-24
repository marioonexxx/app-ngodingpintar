<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { csrf, money } from '../../Components/FormHelpers';
import { confirmAction, successAlert } from '@/Utils/swal';

defineProps({ cart: Object });

const clearCart = async () => {
    const isConfirmed = await confirmAction({
        title: 'Kosongkan Keranjang',
        text: 'Yakin ingin mengosongkan keranjang belanja?',
        confirmButtonText: 'Ya, Kosongkan',
        confirmButtonColor: '#ef4444'
    });

    if (isConfirmed) {
        router.post('/cart/clear-items', {}, { preserveScroll: true });
    }
};

const removeItem = async (id) => {
    const isConfirmed = await confirmAction({
        title: 'Hapus Item',
        text: 'Hapus item ini dari keranjang?',
        confirmButtonText: 'Ya, Hapus',
        confirmButtonColor: '#ef4444'
    });

    if (isConfirmed) {
        router.post(`/cart/items/${id}/destroy`, {}, { preserveScroll: true });
    }
};
</script>

<template>
    <Head>
        <title>Keranjang Belanja</title>
        <meta name="description" content="Tinjau kembali produk yang telah Anda pilih di keranjang belanja Anda." />
    </Head>
    <AppLayout>
        <section class="rounded-md bg-white p-6 md:p-8 shadow-sm shadow-sky-950/5">
            <h1 class="text-3xl font-semibold tracking-tight md:text-4xl text-slate-900">Keranjang Belanja</h1>
            <p class="mt-3 text-sm font-medium text-slate-500">Tinjau kembali produk yang telah Anda pilih sebelum melanjutkan ke pembayaran.</p>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Order Items -->
                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-900">Item Pesanan</h2>
                        <div class="flex items-center gap-3">
                            <button v-if="cart.items?.length" type="button" @click="clearCart" class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-semibold text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500" title="Kosongkan Keranjang">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Kosongkan</span>
                            </button>
                            <span class="rounded-md bg-sky-50 px-3 py-1 text-xs font-semibold text-[#0284c7]">{{ cart.items?.length || 0 }} item</span>
                        </div>
                    </div>

                    <div v-if="!cart.items?.length" class="py-16 text-center">
                        <div class="mx-auto grid size-16 place-items-center rounded-full bg-slate-50">
                            <svg class="size-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="mt-4 font-semibold text-slate-500">Keranjang masih kosong.</p>
                        <Link href="/products" class="mt-4 inline-flex rounded-md border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Mulai Belanja</Link>
                    </div>

                    <div v-for="item in cart.items" :key="item.id" class="flex flex-col gap-4 border-b border-slate-100 py-6 sm:flex-row sm:items-center sm:justify-between last:border-0 last:pb-2">
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-slate-900">{{ item.product?.name }}</p>
                            <p v-if="item.class_batch" class="mt-1 text-xs font-bold text-sky-600">Batch {{ item.class_batch.batch_number }}</p>
                            <p class="mt-1 text-sm font-semibold text-[#0284c7]">{{ money(item.unit_price) }}</p>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-4">
                            <form method="post" :action="`/cart/items/${item.id}`" class="flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 p-1">
                                <input type="hidden" name="_token" :value="csrf()">
                                <input type="hidden" name="_method" value="PATCH">
                                <span class="pl-3 text-xs font-semibold text-slate-500">Qty</span>
                                <input name="quantity" type="number" min="1" :value="item.quantity" class="w-16 rounded border-none bg-transparent px-2 py-1 text-center text-sm font-semibold text-slate-900 focus:ring-0">
                                <button class="rounded bg-white px-3 py-1 text-xs font-semibold text-[#0284c7] shadow-sm hover:bg-sky-50 transition-colors">Update</button>
                            </form>

                            <button @click="removeItem(item.id)" type="button" class="grid size-9 place-items-center rounded-md text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors" title="Hapus item">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column -->
            <aside v-if="cart.items?.length" class="space-y-4">
                <div class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5 sticky top-6">
                    <h2 class="text-xl font-semibold text-slate-900">Ringkasan</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="font-semibold text-slate-500">Total Produk</span>
                            <span class="font-semibold text-slate-900">{{ cart.items.length }} item</span>
                        </div>
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-sm font-semibold text-slate-500">Estimasi Total</p>
                            <p class="mt-1 text-3xl font-semibold text-slate-950">{{ money(cart.items.reduce((sum, item) => sum + Number(item.subtotal), 0)) }}</p>
                        </div>
                    </div>
                    <Link href="/checkout" class="mt-6 flex w-full justify-center rounded-md bg-[#0284c7] px-4 py-3 font-semibold text-white hover:bg-sky-600 transition-colors">
                        Lanjut ke Checkout
                    </Link>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>
