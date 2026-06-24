<script setup>
import AdminLayout from '@/Components/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({
    projects: Object,
});

const getStatusColor = (status) => {
    const colors = {
        'pending_admin': 'bg-amber-100 text-amber-800',
        'waiting_payment': 'bg-blue-100 text-blue-800',
        'open': 'bg-emerald-100 text-emerald-800',
        'in_progress': 'bg-purple-100 text-purple-800',
        'completed': 'bg-slate-100 text-slate-800',
        'complained': 'bg-rose-100 text-rose-800',
        'refund_pending': 'bg-orange-100 text-orange-800',
        'refunded': 'bg-gray-100 text-gray-800',
        'rejected': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-slate-100 text-slate-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'pending_admin': 'Menunggu Review',
        'waiting_payment': 'Menunggu Pembayaran',
        'open': 'Open (Bursa)',
        'in_progress': 'Sedang Dikerjakan',
        'completed': 'Selesai',
        'complained': 'Komplain',
        'refund_pending': 'Proses Refund',
        'refunded': 'Refunded',
        'rejected': 'Ditolak',
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

// Actions
const approveRequest = async (project) => {
    const confirmed = await confirmAction({
        title: 'Approve Project?',
        text: `Anda yakin ingin menyetujui request "${project.title}"?`,
        confirmButtonText: 'Ya, Approve',
        icon: 'question',
    });

    if (confirmed) {
        router.put(route('admin.project-requests.approve', project.id), {}, {
            preserveScroll: true,
            onSuccess: () => successAlert('Project request disetujui.'),
            onError: (err) => errorAlert(err.message || 'Terjadi kesalahan saat menyetujui project.'),
        });
    }
};

const deleteRequest = async (project) => {
    const confirmed = await confirmAction({
        title: 'Hapus Request?',
        text: `Data request "${project.title}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, Hapus',
        confirmButtonColor: '#ef4444',
        icon: 'warning',
    });

    if (confirmed) {
        router.delete(route('admin.project-requests.destroy', project.id), {
            preserveScroll: true,
            onSuccess: () => successAlert('Project request dihapus.'),
            onError: (err) => errorAlert(err.message || 'Terjadi kesalahan saat menghapus project.'),
        });
    }
};

// Modal Penolakan
const showRejectModal = ref(false);
const rejectingProject = ref(null);
const rejectionReason = ref('');

const openRejectModal = (project) => {
    rejectingProject.value = project;
    rejectionReason.value = '';
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    rejectingProject.value = null;
    rejectionReason.value = '';
};

const submitReject = () => {
    if (!rejectionReason.value) {
        errorAlert('Alasan penolakan harus diisi!');
        return;
    }

    router.put(route('admin.project-requests.reject', rejectingProject.value.id), {
        rejection_reason: rejectionReason.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            successAlert('Project request berhasil ditolak.');
            closeRejectModal();
        },
        onError: (err) => {
            errorAlert(err.rejection_reason || 'Gagal menolak project.');
        }
    });
};
</script>

<template>
    <Head title="Project Requests" />

    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Project Requests</h1>
                    <p class="mt-1 text-sm text-slate-500">Kelola request pembuatan/revisi project dari member.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ projects.total || projects.data.length }} project</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Project & Member</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Budget</th>
                            <th class="px-4 py-3">Tenggat Waktu</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="project in projects.data" :key="project.id" class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-800">{{ project.title }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ project.user.name }} ({{ project.user.email }})</div>
                                <div v-if="project.vendor" class="text-xs text-sky-600 mt-0.5 font-semibold">Vendor: {{ project.vendor.name }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600">
                                    {{ project.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 font-bold text-slate-700">{{ formatPrice(project.budget) }}</td>
                            <td class="px-4 py-4">
                                {{ new Date(project.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded px-2.5 py-1 text-xs font-bold" :class="getStatusColor(project.status)">
                                    {{ getStatusLabel(project.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Approve Button (Only if pending_admin) -->
                                    <button v-if="project.status === 'pending_admin'" @click="approveRequest(project)" class="rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600 hover:bg-emerald-100" title="Setujui">Approve</button>
                                    
                                    <!-- Reject Button (Only if pending_admin) -->
                                    <button v-if="project.status === 'pending_admin'" @click="openRejectModal(project)" class="rounded-md bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-600 hover:bg-orange-100" title="Tolak">Tolak</button>
                                    
                                    <!-- Detail Button -->
                                    <Link :href="route('admin.project-requests.show', project.id)" class="rounded-md bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100">Detail</Link>
                                    
                                    <!-- Delete Button (Only for pending or rejected) -->
                                    <button v-if="['pending_admin', 'rejected'].includes(project.status)" @click="deleteRequest(project)" class="rounded-md bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" title="Hapus">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="projects.data.length === 0">
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada project request.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination logic here if exists -->
        </section>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Tolak Project Request</h3>
                <div class="mb-4 text-sm text-slate-600">
                    <p>Anda akan menolak project: <strong class="text-slate-800">{{ rejectingProject?.title }}</strong></p>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan</label>
                    <textarea v-model="rejectionReason" class="w-full rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" rows="4" placeholder="Misal: Budget tidak masuk akal, atau deskripsi kurang jelas..." required></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="closeRejectModal" class="px-4 py-2 rounded-lg text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">Batal</button>
                    <button type="button" @click="submitReject" class="px-4 py-2 rounded-lg text-sm font-bold text-white bg-red-600 hover:bg-red-700">Tolak Project</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
