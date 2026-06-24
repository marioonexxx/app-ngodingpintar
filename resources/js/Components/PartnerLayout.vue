<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ApplicationLogo from './ApplicationLogo.vue';
import NavIcon from './NavIcon.vue';

const page = usePage();
const showProfileMenu = ref(false);

const isVendor = ['vendor', 'partner'].includes(page.props.auth?.user?.role);
const isMentor = ['mentor', 'partner'].includes(page.props.auth?.user?.role);

const isActive = (href) => page.url.startsWith(href);
</script>

<template>
    <div class="min-h-screen bg-sky-50/50 text-slate-950">
        <aside class="fixed inset-y-0 left-0 hidden w-[260px] bg-gradient-to-b from-sky-950 via-sky-900 to-cyan-900 text-white lg:block">
            <div class="flex h-[88px] items-center border-b border-white/10 px-5">
                <Link href="/partner/dashboard" class="flex items-center gap-3 rounded-lg bg-white px-2 py-1.5">
                    <ApplicationLogo class="scale-75 origin-left" />
                </Link>
            </div>

            <div class="px-5 py-6">
                <p class="text-xs font-medium uppercase tracking-widest text-sky-200">Partner Panel</p>
                <p class="mt-1 text-sm font-medium">{{ isVendor && isMentor ? 'Vendor & Mentor' : (isVendor ? 'Vendor' : (isMentor ? 'Mentor' : 'Partner')) }}</p>
            </div>

            <nav class="space-y-1 px-3">
                <Link
                    href="/partner/dashboard"
                    class="flex items-center gap-3 rounded-md px-4 py-3 text-sm font-medium text-sky-100 transition"
                    :class="page.url === '/partner/dashboard' ? 'bg-white text-sky-600 shadow-sm' : 'hover:bg-white/10 hover:text-white'"
                >
                    <span class="grid size-6 place-items-center">
                        <NavIcon name="dashboard" />
                    </span>
                    <span>Dashboard</span>
                </Link>

                <template v-if="isVendor || isMentor">
                    <div class="mt-2 border-t border-white/10 pt-4">
                        <p class="mb-2 px-4 text-xs font-medium uppercase tracking-wider text-sky-200/80">Menu Partner</p>

                        <Link v-if="isVendor"
                            href="/vendor/products"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/vendor/products')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="box" />
                            <span>Produk Saya</span>
                        </Link>

                        <Link v-if="isVendor"
                            href="/vendor/custom-feature-requests"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/vendor/custom-feature-requests')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="tool" />
                            <span>Custom Requests</span>
                        </Link>

                        <Link v-if="isVendor"
                            href="/partner/project-bursa"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/partner/project-bursa') || isActive('/partner/project-bursa/*')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="tag" />
                            <span>Bursa Project</span>
                        </Link>

                        <Link v-if="isVendor"
                            href="/partner/my-projects"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/partner/my-projects') || isActive('/partner/my-projects/*')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="briefcase" />
                            <span>Project Saya</span>
                        </Link>

                        <Link v-if="isMentor"
                            href="/mentor/classes"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/mentor/classes')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="graduation" />
                            <span>Kelas Saya</span>
                        </Link>
                    </div>
                    
                    <div class="mt-2 border-t border-white/10 pt-4">
                        <p class="mb-2 px-4 text-xs font-medium uppercase tracking-wider text-sky-200/80">Profil</p>

                        <Link
                            href="/partner/profile"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/partner/profile')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="user" />
                            <span>Profil Partner</span>
                        </Link>
                    </div>
                    
                    <div class="mt-2 border-t border-white/10 pt-4">
                        <p class="mb-2 px-4 text-xs font-medium uppercase tracking-wider text-sky-200/80">Keuangan</p>

                        <Link
                            href="/partner/earnings"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/partner/earnings')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="file" />
                            <span>Riwayat Pendapatan</span>
                        </Link>

                        <Link
                            href="/partner/withdrawals"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/partner/withdrawals')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        >
                            <NavIcon name="credit-card" />
                            <span>Pencairan Saldo</span>
                        </Link>
                    </div>
                </template>
            </nav>
        </aside>

        <section class="lg:pl-[260px]">
            <header class="sticky top-0 z-20 flex h-[72px] items-center justify-between border-b border-sky-100 bg-white px-5">
                <div class="flex items-center gap-3">
                    <button class="grid size-10 place-items-center rounded-md border border-sky-100 text-slate-600 lg:hidden">
                        <NavIcon name="menu" />
                    </button>
                    <Link href="/partner/dashboard" class="flex items-center gap-3 rounded-lg bg-slate-50 p-1.5 lg:hidden">
                        <ApplicationLogo class="scale-50 origin-left" />
                    </Link>
                </div>

                <div class="flex items-center gap-4">
                    <a href="/" target="_blank" class="hidden items-center justify-center rounded-md border border-sky-100 bg-sky-50 px-3 py-1.5 text-sky-700 hover:bg-sky-100 transition-colors md:inline-flex" title="Lihat Store">
                        <NavIcon name="home" class="size-4" />
                    </a>
                    <div class="relative">
                        <button type="button" class="flex items-center gap-3 rounded-md px-2 py-1 hover:bg-slate-50" @click="showProfileMenu = !showProfileMenu">
                            <span class="hidden text-right sm:block">
                                <span class="block text-sm font-semibold">{{ page.props.auth?.user?.name }}</span>
                                <span class="block text-xs text-slate-500">{{ page.props.auth?.user?.email }}</span>
                            </span>
                            <span class="grid size-10 place-items-center rounded-md border border-sky-100 bg-sky-50 font-semibold text-sky-600">
                                {{ page.props.auth?.user?.name?.charAt(0) || 'P' }}
                            </span>
                        </button>

                        <div v-if="showProfileMenu" class="absolute right-0 top-12 z-40 w-64 overflow-hidden rounded-md border border-slate-200 bg-white shadow-xl shadow-slate-950/10">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="font-semibold">{{ page.props.auth?.user?.name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ page.props.auth?.user?.email }}</p>
                            </div>
                            <Link href="/member/dashboard" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-sky-50 hover:text-sky-600">
                                <NavIcon name="dashboard" />
                                <span>Member Dashboard</span>
                            </Link>
                            <form method="post" action="/logout" class="border-t border-slate-100">
                                <input type="hidden" name="_token" :value="page.props.csrf">
                                <button type="submit" class="flex w-full cursor-pointer items-center gap-2 px-4 py-3 text-left text-sm font-bold text-red-600 hover:bg-red-50">
                                    <NavIcon name="logout" />
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-5 md:p-7">
                <div v-if="page.props.flash?.success" class="mb-5 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-800">
                    {{ page.props.flash.success }}
                </div>
                <div v-if="page.props.flash?.error" class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                    {{ page.props.flash.error }}
                </div>
                <slot />
            </main>
        </section>
    </div>
</template>
