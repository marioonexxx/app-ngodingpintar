<script setup>
import MemberLayout from '@/Components/MemberLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';


const props = defineProps({
    projectRequest: Object,
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

const completeForm = useForm({
    completion_notes: ''
});

const completeProject = () => {
    if (confirm('Apakah Anda yakin ingin menyelesaikan project ini? Dana akan otomatis diteruskan ke vendor dan tidak bisa ditarik kembali.')) {
        completeForm.post(route('member.project-requests.complete', props.projectRequest.id));
    }
};
</script>

<template>
    <Head :title="projectRequest.title" />

    <MemberLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Detail Request</h1>
            <Link :href="route('member.project-requests.index')" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">{{ projectRequest.title }}</h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusColor(projectRequest.status)">
                            {{ getStatusLabel(projectRequest.status) }}
                        </span>
                    </div>

                    <div v-if="projectRequest.status === 'rejected'" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                        <h3 class="text-red-800 font-medium mb-1">Request Ditolak</h3>
                        <p class="text-sm text-red-600">{{ projectRequest.rejection_reason || 'Tidak ada alasan.' }}</p>
                        <div class="mt-3">
                            <Link :href="route('member.project-requests.edit', projectRequest.id)" class="text-sm font-medium text-red-700 hover:text-red-800 underline">Edit Request</Link>
                        </div>
                    </div>

                    <div class="prose max-w-none mb-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Deskripsi & Fitur</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ projectRequest.description }}</p>
                    </div>

                    <div v-if="projectRequest.status === 'in_progress' || projectRequest.status === 'completed'" class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Pekerjaan</h3>
                        <div v-if="projectRequest.github_link" class="mb-4">
                            <span class="text-sm text-gray-500 block mb-1">Repository Github:</span>
                            <a :href="projectRequest.github_link" target="_blank" rel="noopener" class="text-sky-600 hover:underline">{{ projectRequest.github_link }}</a>
                        </div>
                        <div v-if="projectRequest.progress_updates && projectRequest.progress_updates.length > 0">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Update Progress:</h4>
                            <ul class="space-y-4">
                                <li v-for="update in projectRequest.progress_updates" :key="update.id" class="bg-gray-50 p-4 rounded-md">
                                    <div class="text-sm text-gray-500 mb-2">{{ new Date(update.created_at).toLocaleString('id-ID') }}</div>
                                    <p class="text-gray-900 text-sm whitespace-pre-wrap mb-2">{{ update.description }}</p>
                                    <a v-if="update.attachment_path" :href="`/storage/${update.attachment_path}`" target="_blank" class="text-sm text-sky-600 hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        Lihat Lampiran
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="text-sm text-gray-500 italic">
                            Belum ada update progress dari vendor.
                        </div>

                        <!-- Action for In Progress -->
                        <div v-if="projectRequest.status === 'in_progress'" class="mt-8 p-4 bg-green-50 border border-green-200 rounded-md">
                            <h3 class="font-medium text-green-900 mb-2">Selesaikan Project</h3>
                            <p class="text-sm text-green-700 mb-4">Jika aplikasi sudah sesuai dengan kesepakatan, silakan klik tombol selesai. Dana akan diteruskan ke vendor.</p>
                            
                            <form @submit.prevent="completeProject">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-green-800 mb-1">Catatan Tambahan (Opsional)</label>
                                    <textarea v-model="completeForm.completion_notes" class="w-full border-green-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" rows="2"></textarea>
                                </div>
                                <PrimaryButton :class="{ 'opacity-25': completeForm.processing }" :disabled="completeForm.processing" class="bg-green-600 hover:bg-green-700">
                                    Konfirmasi Project Selesai
                                </PrimaryButton>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Project</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Kategori</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ projectRequest.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi Aplikasi' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Budget</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ formatPrice(projectRequest.budget) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Deadline Target</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ new Date(projectRequest.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">No. WhatsApp</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                <a :href="'https://wa.me/' + projectRequest.whatsapp" target="_blank" class="text-sky-600 hover:underline">
                                    {{ projectRequest.whatsapp }}
                                </a>
                            </dd>
                        </div>
                        <div class="pt-4 border-t" v-if="projectRequest.vendor">
                            <dt class="text-sm text-gray-500 mb-2">Dikerjakan Oleh</dt>
                            <dd class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 overflow-hidden">
                                    <img v-if="projectRequest.vendor.avatar_url" :src="projectRequest.vendor.avatar_url" alt="" class="h-full w-full object-cover" />
                                    <svg v-else class="h-full w-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ projectRequest.vendor.name }}</div>
                                </div>
                            </dd>
                        </div>
                    </dl>

                    <div v-if="projectRequest.status === 'waiting_payment'" class="mt-6 pt-6 border-t">
                        <Link :href="route('member.project-requests.pay', projectRequest.id)" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            Bayar Sekarang
                        </Link>
                    </div>

                    <div v-if="projectRequest.status === 'in_progress'" class="mt-6 pt-6 border-t">
                        <Link :href="route('member.project-requests.complaint', projectRequest.id)" class="w-full flex justify-center py-2 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Ajukan Komplain
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
