<script setup>
import PartnerLayout from '@/Components/PartnerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
    appliedProjectIds: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head title="Bursa Project" />

    <PartnerLayout>
        <div class="px-5 py-8 md:px-10 md:py-12">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-800">Bursa Project (Marketplace)</h1>
                <p class="mt-2 text-sm text-slate-500">Daftar request pembuatan aplikasi dari member. Ajukan penawaran Anda untuk mengerjakan project di bawah ini.</p>
            </div>

            <div v-if="projects.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="project in projects.data" :key="project.id" class="rounded-2xl border border-slate-100 bg-white overflow-hidden shadow-sm shadow-slate-100/50 flex flex-col hover:border-sky-300 transition-colors">
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-sky-50 text-sky-600">
                                {{ project.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi' }}
                            </span>
                            <span class="text-xs font-medium text-slate-400">{{ new Date(project.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 leading-snug mb-2 line-clamp-2">
                            {{ project.title }}
                        </h3>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-3">
                            {{ project.description }}
                        </p>
                        <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase font-semibold">Budget (Fix)</p>
                                <p class="text-base font-bold text-sky-600">{{ formatPrice(project.budget) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        <Link v-if="!appliedProjectIds.includes(project.id)" :href="route('partner.project-bursa.show', project.id)" class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition-colors">
                            Lihat & Ajukan Penawaran
                        </Link>
                        <button v-else disabled class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-semibold cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Sudah Mengajukan
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!projects.data.length" class="text-center py-16 rounded-2xl border border-dashed border-slate-300 bg-white shadow-sm shadow-slate-100/50">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900 mb-1">Belum ada request project</h3>
                <p class="text-slate-500">Saat ini tidak ada request project yang open/tersedia di bursa.</p>
            </div>
            
            <div class="mt-8 flex justify-center" v-if="projects.links && projects.links.length > 3">
                <!-- Pagination -->
            </div>
        </div>
    </PartnerLayout>
</template>
