<script setup>
import MemberLayout from '../../Components/MemberLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ transaction: Object });

const form = useForm({
    product_id: props.transaction.items[0]?.product_id || '',
    rating: 5,
    title: '',
    content: '',
});
</script>

<template>
    <MemberLayout>
        <div class="max-w-2xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm shadow-slate-100/50">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Kirim Testimonial</h1>
                <p class="text-xs text-slate-500 mt-1">Bagikan pengalaman Anda menggunakan produk digital kami untuk membantu pembeli lainnya.</p>
            </div>

            <form @submit.prevent="form.post(`/member/transactions/${transaction.id}/testimonial`)" class="space-y-5">
                
                <!-- Product selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Produk Terkait</label>
                    <select v-model="form.product_id" class="mt-2 w-full rounded-xl border border-slate-200/80 bg-slate-50/50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-500/10">
                        <option value="">Umum (Seluruh Transaksi)</option>
                        <option v-for="item in transaction.items" :key="item.id" :value="item.product_id">
                            {{ item.product_name }}
                        </option>
                    </select>
                </div>

                <!-- Rating -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Rating (1 - 5 Bintang)</label>
                    <div class="mt-2 flex gap-1 text-amber-500">
                        <button type="button" v-for="i in 5" :key="i" @click="form.rating = i" class="hover:scale-110 transition-transform">
                            <svg class="size-6" :class="i <= form.rating ? 'fill-current' : 'fill-slate-200 text-slate-200'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-5">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Judul Ulasan</label>
                    <input v-model="form.title" type="text" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-[#0284c7] focus:ring-1 focus:ring-[#0284c7]" placeholder="Ulas singkat pengalaman Anda...">
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                </div>

                <!-- Content -->
                <div class="mb-5">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Ulasan Lengkap</label>
                    <textarea v-model="form.content" rows="4" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-[#0284c7] focus:ring-1 focus:ring-[#0284c7]" placeholder="Ceritakan pengalaman Anda menggunakan produk ini..."></textarea>
                    <p v-if="form.errors.content" class="mt-1 text-xs text-red-500">{{ form.errors.content }}</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button :disabled="form.processing" class="rounded-xl bg-sky-600 px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-500/15 hover:bg-sky-500 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </MemberLayout>
</template>
