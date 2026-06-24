<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from './ApplicationLogo.vue';
import NavIcon from './NavIcon.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-sky-50/50 text-slate-950">
        <div class="bg-gradient-to-r from-sky-900 via-sky-700 to-cyan-600 text-white">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4 text-xs font-medium sm:px-6 sm:text-sm">
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <span class="rotate-[-6deg] rounded bg-white/15 px-3 py-2 text-xs font-semibold leading-none text-white ring-1 ring-white/20">TODAY'S<br>SALE</span>
                    <template v-if="page.props.active_promo_code">
                        <span>Dapatkan diskon {{ page.props.active_promo_code.discount_percent }}% spesial bulan ini.</span>
                        <span class="hidden sm:inline">Gunakan kode promo <strong>{{ page.props.active_promo_code.code }}</strong>.</span>
                    </template>
                    <template v-else>
                        <span>Nantikan promo menarik lainnya.</span>
                    </template>
                </div>
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <span class="hidden items-center gap-2 md:flex">
                        <span class="grid size-6 place-items-center rounded-full bg-white/15 ring-1 ring-white/20">
                            <NavIcon name="shield" icon-class="size-3.5" />
                        </span>
                        Akses Selamanya
                    </span>
                    <Link href="/products" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-sky-700 shadow-sm shadow-sky-950/10">
                        <NavIcon name="tag" />
                        Cek Produk Promo
                    </Link>
                </div>
            </div>
        </div>
        <header class="sticky top-0 z-30 border-b border-sky-100/80 bg-sky-50/95 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-3 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                <Link href="/" class="flex items-center">
                    <ApplicationLogo />
                </Link>
                <div class="flex w-full flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-700 sm:text-sm lg:w-auto lg:justify-end">

                    <Link class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" href="/">
                        <NavIcon name="home" />
                        <span>Home</span>
                    </Link>
                    <Link class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" href="/products">
                        <NavIcon name="box" />
                        <span>Source Code</span>
                    </Link>
                    <Link class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" href="/kelas">
                        <NavIcon name="tag" />
                        <span>Kelas</span>
                    </Link>
                    <Link class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 font-bold text-white shadow-sm transition hover:bg-amber-600" href="/request">
                        <NavIcon name="documentText" />
                        <span>Request Aplikasi</span>
                    </Link>
                    <Link v-if="!page.props.auth?.user || page.props.auth?.user?.role === 'member'" class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" href="/partner/register">
                        <NavIcon name="handshake" />
                        <span>Menjadi Partner</span>
                    </Link>
                    <Link v-if="page.props.auth?.user?.role !== 'admin'" class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" :href="page.props.auth?.user ? '/cart' : '/login?redirect=/cart'">
                        <NavIcon name="cart" />
                        <span>Cart</span>
                        <span v-if="page.props.cart_count > 0" class="ml-1 inline-flex items-center justify-center rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white shadow-sm">{{ page.props.cart_count }}</span>
                    </Link>

                    <a v-if="page.props.auth?.user?.role === 'admin'" class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" href="/admin/dashboard" target="_blank">
                        <NavIcon name="shield" />
                        <span>Admin</span>
                    </a>

                    <Link v-if="page.props.auth?.user && page.props.auth?.user?.role !== 'admin'" class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-sky-50 hover:text-sky-600" href="/member/dashboard">
                        <NavIcon name="user" />
                        <span>Member</span>
                        <span class="inline-flex items-center gap-1 rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[9px] font-semibold text-sky-600">
                            <span class="size-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                            Online
                        </span>
                    </Link>

                    <a v-if="['partner', 'vendor', 'mentor'].includes(page.props.auth?.user?.role)" class="inline-flex items-center gap-1.5 rounded-md px-2 py-2 hover:bg-cyan-50 hover:text-cyan-600" href="/partner/dashboard" target="_blank">
                        <NavIcon name="briefcase" />
                        <span>Partner</span>
                        <span class="inline-flex items-center gap-1 rounded border border-cyan-200 bg-cyan-50 px-1.5 py-0.5 text-[9px] font-semibold text-cyan-600">
                            Dashboard
                        </span>
                    </a>

                    <div v-if="!page.props.auth?.user" class="ml-2 flex items-center gap-2">
                        <Link href="/login" class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-4 py-2 text-slate-700 transition hover:bg-sky-50 hover:text-sky-600">
                            <NavIcon name="login" />
                            <span>Login</span>
                        </Link>
                        <Link href="/login" class="inline-flex items-center gap-1.5 rounded-md bg-gradient-to-r from-sky-600 to-cyan-500 px-4 py-2 text-white shadow-sm shadow-sky-900/10 transition hover:from-sky-700 hover:to-cyan-600">
                            <NavIcon name="userPlus" />
                            <span>Register</span>
                        </Link>
                    </div>

                    <form v-else method="post" action="/logout" class="ml-2">
                        <input type="hidden" name="_token" :value="page.props.csrf">
                        <button class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-slate-200 px-4 py-2 text-slate-600 transition hover:bg-slate-50 hover:text-slate-900" type="submit">
                            <NavIcon name="logout" />
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:py-8">
            <div v-if="page.props.flash?.success" class="mb-6 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-800">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ page.props.flash.error }}
            </div>
            <slot />
        </main>

        <footer class="mt-20 border-t border-sky-950 bg-gradient-to-br from-sky-950 via-sky-800 to-cyan-700 text-white">
            <div class="mx-auto max-w-7xl px-6 py-12 sm:px-8 lg:px-12">
                <div class="grid gap-8 md:grid-cols-[1.5fr_1fr_1fr] lg:gap-12">
                    <div class="flex flex-col gap-4">
                        <Link href="/" class="inline-flex self-start rounded-xl bg-white p-2">
                            <ApplicationLogo />
                        </Link>
                        <p class="max-w-sm text-sm leading-relaxed text-sky-100/75">
                            Template, source code, dan asset digital premium untuk membangun produk digital Anda lebih cepat dengan fondasi yang kokoh.
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-sky-200">Produk</p>
                        <ul class="mt-4 space-y-2 text-sm text-sky-100/65">
                            <li>
                                <Link href="/products" class="transition-colors hover:text-white">Katalog Source Code</Link>
                            </li>
                            <li>
                                <Link :href="page.props.auth?.user ? '/cart' : '/login?redirect=/cart'" class="transition-colors hover:text-white">Keranjang Belanja</Link>
                            </li>
                            <li>
                                <Link href="/products" class="transition-colors hover:text-white">Promo Spesial</Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-sky-200">Akun &amp; Bantuan</p>
                        <ul class="mt-4 space-y-2 text-sm text-sky-100/65">
                            <li>
                                <Link href="/login" class="transition-colors hover:text-white">Masuk Member</Link>
                            </li>
                            <li>
                                <Link href="/register" class="transition-colors hover:text-white">Daftar Akun</Link>
                            </li>
                            <li>
                                <Link href="/member/dashboard" class="transition-colors hover:text-white">Portal Dashboard</Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-sky-100/50 sm:flex-row">
                    <p>&copy; 2026 NgodingPintar. All rights reserved. Solusi Digital, Kode Nyata.</p>
                    <div class="flex gap-6">
                        <a href="#" class="transition-colors hover:text-white">Syarat &amp; Ketentuan</a>
                        <a href="#" class="transition-colors hover:text-white">Kebijakan Privasi</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
