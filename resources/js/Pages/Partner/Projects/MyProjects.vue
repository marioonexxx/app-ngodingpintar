<script setup>
import PartnerLayout from '@/Components/PartnerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
});

const getStatusColor = (status) => {
    const colors = {
        'in_progress': 'bg-purple-50 text-purple-700 border-purple-200',
        'completed': 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'complained': 'bg-rose-50 text-rose-700 border-rose-200',
        'refund_pending': 'bg-orange-50 text-orange-700 border-orange-200',
        'refunded': 'bg-slate-100 text-slate-700 border-slate-300',
    };
    return colors[status] || 'bg-slate-50 text-slate-700 border-slate-200';
};

const getStatusLabel = (status) => {
    const labels = {
        'in_progress': 'Sedang Dikerjakan',
        'completed': 'Selesai',
        'complained': 'Ada Komplain',
        'refund_pending': 'Refund (Menunggu Admin)',
        'refunded': 'Dibatalkan (Refund)',
    };
    return labels[status] || status;
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
    <Head title="Project Saya" />

    <PartnerLayout>
        <div class="px-5 py-8 md:px-10 md:py-12">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-800">Project Saya</h1>
                <p class="mt-2 text-sm text-slate-500">Daftar project dari member yang sedang atau telah Anda kerjakan.</p>
            </div>

            <div v-if="!projects.data.length" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Belum ada project</h3>
                <p class="text-xs text-slate-500">Anda belum mengerjakan project apapun. Silakan cek Bursa Project.</p>
                <Link :href="route('partner.project-bursa.index')" class="inline-block mt-4 text-xs font-semibold text-sky-600 hover:underline">
                    Ke Bursa Project &rarr;
                </Link>
            </div>
            
            <div v-else class="space-y-4">
                <div v-for="project in projects.data" :key="project.id" class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm shadow-slate-100/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:border-sky-200 hover:shadow-sky-100">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 inline-flex text-[10px] uppercase tracking-wider font-bold rounded-full" :class="getStatusColor(project.status)">
                                {{ getStatusLabel(project.status) }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                                {{ project.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">{{ project.title }}</h3>
                        <div class="text-xs text-slate-500 mt-1 mb-3">Member: {{ project.user.name }}</div>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                            <span class="flex items-center gap-1.5 font-medium text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ formatPrice(project.budget) }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ new Date(project.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0">
                        <Link :href="route('partner.my-projects.show', project.id)" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white rounded-full text-sm font-semibold transition-colors duration-200">
                            Detail
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PartnerLayout>
</template>
