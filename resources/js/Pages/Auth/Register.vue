<script setup>
import AppLayout from '../../Components/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="mx-auto flex max-w-[480px] flex-col items-center justify-center py-10 sm:py-16">
            <!-- Header Text -->
            <div class="mb-8 text-center">
                <p class="text-xs font-extrabold uppercase tracking-[0.25em] text-[#0284c7]">Daftar Member</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 md:text-4xl">Mulai Akses Produk</h1>
                <p class="mt-3 text-sm text-slate-500">Daftar sebagai member untuk checkout, melihat riwayat transaksi, dan download produk digital.</p>
            </div>

            <!-- Register Card -->
            <div class="w-full rounded-2xl border border-slate-100 bg-white p-6 shadow-xl shadow-sky-950/5 sm:p-10 text-center">
                
                <div class="mx-auto mb-6 grid size-16 place-items-center rounded-full bg-sky-50">
                    <svg class="size-8 text-[#0284c7]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>

                <h2 class="mb-2 text-xl font-bold text-slate-900">Satu Langkah Lagi</h2>
                <p class="mb-8 text-sm text-slate-500">Pendaftaran hanya menggunakan akun Google untuk keamanan dan kemudahan akses Anda.</p>

                <!-- Google Register -->
                <a href="/auth/google" class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 text-sm font-bold text-slate-700 shadow-sm transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-md">
                    <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C4 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 4 3.47 2.18 7.07l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/>
                    </svg>
                    Daftar dengan Google
                </a>

                <!-- Login Redirect -->
                <p class="mt-8 text-sm font-medium text-slate-500">
                    Sudah punya akun?
                    <a href="/login" class="font-bold text-[#0284c7] transition-colors hover:text-sky-700 hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
