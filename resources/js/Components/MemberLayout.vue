<script setup>
import AppLayout from './AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavIcon from './NavIcon.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentUrl = computed(() => page.url);
const activeTxCount = computed(() => page.props.active_transactions_count || 0);
const historyTxCount = computed(() => page.props.history_transactions_count || 0);

const isActive = (path) => currentUrl.value === path;
</script>

<template>
    <AppLayout>
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-[280px_1fr]">
            <aside class="z-10 flex w-full flex-col gap-6 lg:sticky lg:top-24">
                <div class="hidden flex-col items-center rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm shadow-slate-100/50 lg:flex">
                    <div class="group relative">
                        <div class="absolute -inset-0.5 rounded-full bg-gradient-to-r from-sky-600 to-cyan-500 opacity-60 blur transition duration-300 group-hover:opacity-100"></div>
                        <div class="relative flex size-16 items-center justify-center rounded-full border-2 border-white bg-gradient-to-r from-sky-600 to-cyan-500 text-xl font-semibold text-white shadow-md">
                            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'M' }}
                        </div>
                    </div>
                    <h3 class="mt-4 text-base font-semibold leading-tight tracking-tight text-slate-800">{{ user?.name }}</h3>
                    <p class="mt-1 max-w-full truncate text-xs text-slate-400">{{ user?.email }}</p>

                    <div class="mt-3.5 flex flex-wrap items-center justify-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-sky-600">
                            <span class="size-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                            Member
                        </span>
                        <span v-if="['vendor', 'partner'].includes(user?.role)" class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-amber-600">
                            Vendor
                        </span>
                        <span v-if="['mentor', 'partner'].includes(user?.role)" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-emerald-600">
                            Mentor
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm shadow-slate-100/50">
                    <p class="mb-4 hidden px-3 text-[10px] font-medium uppercase tracking-widest text-slate-400 lg:block">Menu Member</p>

                    <nav class="scrollbar-none flex flex-row gap-1 overflow-x-auto pb-2 snap-x lg:flex-col lg:overflow-visible lg:pb-0">
                        <Link
                            href="/member/dashboard"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/member/dashboard')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <NavIcon name="dashboard" />
                            <span>Dashboard</span>
                        </Link>

                        <Link
                            href="/member/profile"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/member/profile')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <NavIcon name="user" />
                            <span>Profil Saya</span>
                        </Link>

                        <Link
                            href="/member/transactions/active"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/member/transactions/active')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <NavIcon name="approval" />
                            <span class="flex-1">Transaksi Aktif</span>
                            <span v-if="activeTxCount > 0" class="flex size-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm">
                                {{ activeTxCount > 99 ? '99+' : activeTxCount }}
                            </span>
                        </Link>

                        <Link
                            href="/member/transactions/history"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/member/transactions/history')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <NavIcon name="receipt" />
                            <span class="flex-1">Riwayat &amp; Download</span>
                            <span v-if="historyTxCount > 0" class="flex size-5 items-center justify-center rounded-full bg-emerald-500 text-[10px] font-bold text-white shadow-sm">
                                {{ historyTxCount > 99 ? '99+' : historyTxCount }}
                            </span>
                        </Link>

                        <Link
                            href="/member/project-requests"
                            class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                            :class="isActive('/member/project-requests')
                                ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white shadow-md shadow-sky-500/20'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <NavIcon name="briefcase" />
                            <span class="flex-1">Request Project</span>
                        </Link>

                        <div class="mt-2 border-t border-slate-100 pt-2">
                            <p class="mb-2 hidden px-4 text-[10px] font-medium uppercase tracking-wider text-slate-400 lg:block">Program Partner</p>

                            <a v-if="['partner', 'vendor', 'mentor'].includes(user?.role)"
                                href="/partner/dashboard"
                                target="_blank"
                                class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-slate-900"
                            >
                                <NavIcon name="briefcase" />
                                <span>Dashboard Partner</span>
                            </a>

                            <Link
                                href="/member/partner-status"
                                class="flex shrink-0 snap-center items-center gap-3 rounded-xl px-4 py-3 text-xs font-medium transition-all duration-200"
                                :class="$page.url.startsWith('/member/partner-status') ? 'bg-gradient-to-r from-sky-600 to-cyan-500 text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                            >
                                <NavIcon name="handshake" />
                                <span>Status Partner</span>
                            </Link>
                        </div>
                    </nav>
                </div>
            </aside>

            <main class="w-full flex-1 min-w-0">
                <slot />
            </main>
        </div>
    </AppLayout>
</template>
