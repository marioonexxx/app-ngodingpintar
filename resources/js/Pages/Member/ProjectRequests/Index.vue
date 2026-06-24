<script setup>
import MemberLayout from '@/Components/MemberLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    requests: Object,
});

const getStatusColor = (status) => {
    const colors = {
        'pending_admin': 'bg-yellow-100 text-yellow-800',
        'rejected': 'bg-red-100 text-red-800',
        'waiting_payment': 'bg-blue-100 text-blue-800',
        'open': 'bg-indigo-100 text-indigo-800',
        'in_progress': 'bg-purple-100 text-purple-800',
        'completed': 'bg-green-100 text-green-800',
        'complained': 'bg-orange-100 text-orange-800',
        'refund_pending': 'bg-pink-100 text-pink-800',
        'refunded': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'pending_admin': 'Menunggu Review Admin',
        'rejected': 'Ditolak',
        'waiting_payment': 'Menunggu Pembayaran',
        'open': 'Open (Mencari Vendor)',
        'in_progress': 'Dikerjakan',
        'completed': 'Selesai',
        'complained': 'Komplain',
        'refund_pending': 'Refund Pending',
        'refunded': 'Refund Berhasil',
        'cancelled': 'Dibatalkan',
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

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Request Project" />

    <MemberLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Request Project Source Code</h1>
            <Link :href="route('member.project-requests.create')" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                + Buat Request Baru
            </Link>
        </div>

        <div v-if="requests.data.length === 0" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <p class="text-slate-500">Belum ada request project.</p>
        </div>

        <div v-else class="space-y-4">
            <div v-for="req in requests.data" :key="req.id" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm shadow-slate-100/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:border-sky-200 hover:shadow-sky-100">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-2.5 py-1 inline-flex text-[10px] uppercase tracking-wider font-bold rounded-full" :class="getStatusColor(req.status)">
                            {{ getStatusLabel(req.status) }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                            {{ req.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi' }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">{{ req.title }}</h3>
                    <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                        <span class="flex items-center gap-1.5 font-medium text-slate-700">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ formatPrice(req.budget) }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Deadline: {{ formatDate(req.deadline_date) }}
                        </span>
                        <span v-if="req.vendor" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Vendor: {{ req.vendor.name }}
                        </span>
                    </div>
                </div>
                
                <div class="flex-shrink-0">
                    <Link :href="route('member.project-requests.show', req.id)" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white rounded-full text-sm font-semibold transition-colors duration-200">
                        Detail
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </Link>
                </div>
            </div>
        </div>
            
            <div class="px-6 py-4 border-t" v-if="requests.links && requests.links.length > 3">
                <!-- Pagination can be added here using requests.links -->
            </div>
    </MemberLayout>
</template>
