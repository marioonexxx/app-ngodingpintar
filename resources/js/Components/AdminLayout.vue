<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ApplicationLogo from './ApplicationLogo.vue';
import NavIcon from './NavIcon.vue';
import { successAlert, errorAlert } from '@/Utils/swal';

const page = usePage();
const showProfileMenu = ref(false);

watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        successAlert(flash.success);
    }
    if (flash?.error) {
        errorAlert(flash.error);
    }
}, { deep: true, immediate: true });

const menuGroups = [
    {
        label: 'Source Code',
        icon: 'box',
        items: [
            { href: '/admin/product-approvals', label: 'Approval Source Code' },
            { href: '/admin/categories', label: 'Kategori Source Code' },
            { href: '/admin/products', label: 'Data Source Code' },
        ]
    },

    {
        label: 'Kelas',
        icon: 'graduation',
        items: [
            { href: '/admin/class-approvals', label: 'Approval Kelas' },
            { href: '/admin/class-categories', label: 'Kategori Kelas' },
            { href: '/admin/class-products', label: 'Produk Kelas' },
            { href: '/admin/class-participants', label: 'Peserta Kelas' },
        ]
    },

    {
        label: 'Project & Refund',
        icon: 'tool',
        items: [
            { href: '/admin/project-requests', label: 'Project Requests' },
            { href: '/admin/refund-requests', label: 'Refund' },
        ]
    },

    {
        label: 'Kode Promo',
        icon: 'percent',
        items: [
            { href: '/admin/promo-codes', label: 'Semua Kode Promo' },
        ]
    },
    {
        label: 'Transaksi',
        icon: 'receipt',
        items: [
            { href: '/admin/orders', label: 'Pesanan' },
            { href: '/admin/transactions', label: 'Transaksi' },
            { href: '/admin/bank-accounts', label: 'No Rekening Bank' },
            { href: '/admin/finance', label: 'Laporan Keuangan' },
        ]
    },
    {
        label: 'Platform',
        icon: 'settings',
        items: [
            { href: '/admin/testimonials', label: 'Testimonial' },
            { href: '/admin/custom-feature-requests', label: 'Custom Requests' },
            { href: '/admin/memberships', label: 'Membership' },
        ]
    },
    {
        label: 'Partner',
        icon: 'userPlus',
        items: [
            { href: '/admin/partner-settings', label: 'Pengaturan Partner' },
            { href: '/admin/partners', label: 'Data Partner' },
            { href: '/admin/partner-withdrawals', label: 'Pencairan Partner' },
        ]
    }
];

const isActive = (href) => page.url.startsWith(href);

// Determine which group should be open by default based on current URL
const getActiveGroupIndex = () => {
    const index = menuGroups.findIndex(group => group.items.some(item => isActive(item.href)));
    return index !== -1 ? index : null;
};

const openGroup = ref(getActiveGroupIndex());

const toggleGroup = (index) => {
    openGroup.value = openGroup.value === index ? null : index;
};
</script>

<template>
    <div class="min-h-screen bg-sky-50/50 text-slate-950">
        <aside class="fixed inset-y-0 left-0 hidden w-[260px] bg-gradient-to-b from-sky-950 via-sky-900 to-cyan-900 text-white lg:block">
            <div class="flex h-[72px] items-center border-b border-white/10 px-5">
                <Link href="/admin/dashboard" class="flex items-center gap-3 rounded-lg bg-white px-3 py-2 shadow-sm">
                    <ApplicationLogo class="scale-[0.85] origin-left" />
                </Link>
            </div>

            <div class="px-5 py-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-sky-300">Menu Utama</p>
            </div>

            <nav class="space-y-1 px-3 pb-6">
                <Link
                    href="/admin/dashboard"
                    class="mb-2 flex items-center gap-3 rounded-md px-4 py-3 text-sm font-medium transition"
                    :class="page.url === '/admin/dashboard' ? 'bg-white text-sky-600 shadow-sm' : 'text-sky-100 hover:bg-white/10 hover:text-white'"
                >
                    <span class="grid size-6 place-items-center">
                        <NavIcon name="dashboard" />
                    </span>
                    <span>Dashboard</span>
                </Link>

                <div v-for="(group, index) in menuGroups" :key="group.label" class="mb-1">
                    <button 
                        @click="toggleGroup(index)" 
                        class="flex w-full items-center justify-between gap-3 rounded-md px-4 py-3 text-sm font-medium transition"
                        :class="openGroup === index ? 'bg-sky-900/50 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white'"
                    >
                        <div class="flex items-center gap-3">
                            <span class="grid size-6 place-items-center">
                                <NavIcon :name="group.icon" />
                            </span>
                            <span>{{ group.label }}</span>
                        </div>
                        <svg :class="{'rotate-180': openGroup === index}" class="size-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div v-show="openGroup === index" class="mt-1 space-y-1">
                        <Link
                            v-for="item in group.items"
                            :key="item.href"
                            :href="item.href"
                            class="block rounded-md py-2 pl-[52px] pr-4 text-sm font-medium transition"
                            :class="isActive(item.href) ? 'bg-white text-sky-600 shadow-sm' : 'text-sky-200 hover:bg-white/10 hover:text-white'"
                        >
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </nav>
        </aside>

        <section class="lg:pl-[260px]">
            <header class="sticky top-0 z-20 flex h-[72px] items-center justify-between border-b border-sky-100 bg-white px-5">
                <div class="flex items-center gap-3">
                    <button class="grid size-10 place-items-center rounded-md border border-sky-100 text-slate-600 lg:hidden">
                        <NavIcon name="menu" />
                    </button>
                    <Link href="/admin/dashboard" class="flex items-center gap-3 rounded-lg bg-slate-50 p-1.5 lg:hidden">
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
                                <span class="block text-sm font-extrabold text-slate-900">Administrator Sistem</span>
                                <span class="block text-xs text-slate-500">{{ page.props.auth?.user?.email }}</span>
                            </span>
                            <span class="grid size-10 place-items-center rounded-md border border-sky-100 bg-sky-50 font-extrabold text-sky-700">
                                {{ page.props.auth?.user?.name?.charAt(0) || 'A' }}
                            </span>
                        </button>

                        <div v-if="showProfileMenu" class="absolute right-0 top-12 z-40 w-64 overflow-hidden rounded-md border border-slate-200 bg-white shadow-xl shadow-slate-950/10">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="font-extrabold text-slate-900">{{ page.props.auth?.user?.name || 'Administrator' }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ page.props.auth?.user?.email }}</p>
                            </div>
                            <Link href="/member/profile" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-sky-50 hover:text-sky-600">
                                <NavIcon name="user" />
                                <span>Profil Saya</span>
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
