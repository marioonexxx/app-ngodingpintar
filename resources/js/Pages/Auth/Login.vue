<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { csrf } from '../../Components/FormHelpers';
import ApplicationLogo from '../../Components/ApplicationLogo.vue';
import AppLayout from '../../Components/AppLayout.vue';

const showPassword = ref(false);
const page = usePage();
</script>

<template>
    <AppLayout>
        <div class="mx-auto flex max-w-[480px] flex-col items-center justify-center py-10 sm:py-16">
            <!-- Header Text -->
            <div class="mb-8 text-center">
                <p class="text-xs font-extrabold uppercase tracking-[0.25em] text-[#0284c7]">Masuk Akun</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 md:text-4xl">Login Member</h1>
                <p class="mt-3 text-sm text-slate-500">Akses produk digital, source code, dan riwayat transaksi Anda.</p>
            </div>

            <!-- Login Card -->
            <div class="w-full rounded-2xl border border-slate-100 bg-white p-6 shadow-xl shadow-sky-950/5 sm:p-10">
                <form method="post" action="/login">
                    <input type="hidden" name="_token" :value="csrf()">

                    <!-- Email Input -->
                    <div>
                        <label class="block text-sm font-bold text-slate-800">Email Address</label>
                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input 
                                name="email" 
                                type="email" 
                                required 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                                placeholder="nama@email.com"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mt-6">
                        <label class="block text-sm font-bold text-slate-800">Password</label>
                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                required 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-12 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                                placeholder="••••••••"
                            >
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors hover:text-slate-600 focus:outline-none"
                            >
                                <svg v-if="!showPassword" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 013.738-4.996m3.844-1.08A9.972 9.972 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-2.102-2.102L3 3m10.125 10.125a3 3 0 11-4.25-4.25" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="mt-6 flex items-center justify-between gap-3">
                        <label class="flex cursor-pointer select-none items-center gap-2.5 text-sm font-medium text-slate-500">
                            <input type="checkbox" name="remember" value="1" checked class="size-4 rounded border-slate-300 text-[#0284c7] focus:ring-sky-500/20">
                            Ingat saya
                        </label>
                        <a href="/forgot-password" class="text-sm font-bold text-[#0284c7] transition-colors hover:text-sky-700 hover:underline">Lupa password?</a>
                    </div>

                    <!-- Login Button -->
                    <button class="mt-8 flex w-full items-center justify-center gap-2 rounded-xl bg-[#0284c7] px-4 py-4 text-sm font-bold text-white shadow-md shadow-sky-500/25 transition-all hover:bg-[#1a40ba]">
                        Login ke Akun
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center justify-between gap-4">
                    <span class="h-px flex-1 bg-slate-100"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Atau masuk dengan</span>
                    <span class="h-px flex-1 bg-slate-100"></span>
                </div>

                <!-- Google Login -->
                <a href="/auth/google" class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-bold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:border-slate-300">
                    <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C4 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 4 3.47 2.18 7.07l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/>
                    </svg>
                    Google
                </a>

                <!-- Register Redirect -->
                <p class="mt-8 text-center text-sm font-medium text-slate-500">
                    Belum punya akun?
                    <a href="/register" class="font-bold text-[#0284c7] transition-colors hover:text-sky-700 hover:underline">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
