<script setup>
import MemberLayout from '../../Components/MemberLayout.vue';
import { csrf } from '../../Components/FormHelpers';
import { usePage } from '@inertiajs/vue3';

defineProps({ user: Object });
</script>

<template>
    <MemberLayout>
        <div class="max-w-2xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm shadow-slate-100/50">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Pengaturan Profil</h1>
                <p class="text-xs text-slate-500 mt-1">Perbarui informasi profil Anda untuk kemudahan transaksi dan akses produk.</p>
            </div>

            <form method="post" action="/member/profile" class="space-y-5">
                <input type="hidden" name="_token" :value="csrf()">
                <input type="hidden" name="_method" value="PATCH">
                
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                    <input 
                        name="name" 
                        :value="user.name" 
                        required
                        class="mt-2 w-full rounded-xl border border-slate-200/80 bg-slate-50/50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-500/10 placeholder:text-slate-400"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <input 
                            disabled 
                            :value="user.email" 
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-100/80 px-4 py-3 text-sm text-slate-400 cursor-not-allowed"
                        >
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 mt-1 inline-flex items-center gap-1 rounded bg-slate-200 px-2 py-0.5 text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">
                            Terkunci
                        </span>
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor Telepon / WhatsApp</label>
                    <input 
                        name="phone" 
                        :value="user.phone" 
                        placeholder="Contoh: 081234567890"
                        class="mt-2 w-full rounded-xl border border-slate-200/80 bg-slate-50/50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-500/10 placeholder:text-slate-400"
                    >
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        rows="4" 
                        placeholder="Tuliskan alamat pengiriman atau domisili lengkap Anda..."
                        class="mt-2 w-full rounded-xl border border-slate-200/80 bg-slate-50/50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-500/10 placeholder:text-slate-400"
                    >{{ user.address }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button class="rounded-xl bg-sky-600 px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-500/15 hover:bg-sky-500 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </MemberLayout>
</template>
