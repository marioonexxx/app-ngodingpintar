<script setup>
import AdminLayout from '@/Components/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';


const props = defineProps({
    project: Object,
});

const rejectForm = useForm({
    rejection_reason: '',
});

const showRejectModal = ref(false);

const approveProject = () => {
    if (confirm('Setujui project ini? Member akan diinstruksikan untuk membayar.')) {
        router.put(route('admin.project-requests.approve', props.project.id), {}, { preserveScroll: true });
    }
};

const rejectProject = () => {
    rejectForm.put(route('admin.project-requests.reject', props.project.id), {
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        }
    });
};

const assignVendor = (vendorId) => {
    if (confirm('Pilih vendor ini untuk mengerjakan project?')) {
        router.put(route('admin.project-requests.assign', props.project.id), { vendor_id: vendorId }, { preserveScroll: true });
    }
};

const resolveComplaint = (resolution) => {
    const message = resolution === 'refund_member' 
        ? 'Apakah Anda yakin ingin memenangkan Member? Dana akan dikembalikan.' 
        : 'Apakah Anda yakin ingin memenangkan Vendor? Pembayaran akan diteruskan ke Vendor.';
        
    if (confirm(message)) {
        const notes = prompt('Masukkan catatan resolusi admin:');
        if (notes) {
            router.post(route('admin.project-complaints.resolve', props.project.complaint.id), {
                resolution: resolution,
                admin_notes: notes
            }, { preserveScroll: true });
        }
    }
};

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
        'pending_admin': 'Menunggu Review Admin',
        'waiting_payment': 'Menunggu Pembayaran Member',
        'open': 'Open (Bursa)',
        'in_progress': 'Sedang Dikerjakan Vendor',
        'completed': 'Selesai',
        'complained': 'Komplain Member',
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
</script>

<template>
    <Head :title="'Project Detail - ' + project.title" />

    <AdminLayout>
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <Link :href="route('admin.project-requests.index')" class="text-sm font-medium text-slate-500 hover:text-slate-900 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </Link>
            </div>

            <!-- Header Info -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50 mb-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ project.title }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> Member: {{ project.user.name }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Deadline: {{ new Date(project.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="font-medium text-slate-700">Kategori: {{ project.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi' }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1 font-medium text-green-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M17.498 14.382c-.301-.15-1.767-.867-2.04-.966-.274-.101-.473-.15-.673.15-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.068-.3-.15-1.265-.462-2.405-1.485-.888-.795-1.484-1.77-1.66-2.07-.174-.3-.019-.465.13-.615.136-.135.301-.345.451-.523.146-.181.194-.301.297-.496.098-.205.048-.38-.025-.531-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51h-.576c-.2 0-.523.074-.796.369C7.394 6.945 6.645 7.64 6.645 9.06c0 1.42.82 2.795.94 2.95.12.15 2.012 3.078 4.88 4.28 2.868 1.2 2.868.802 3.39.75.524-.05 1.766-.722 2.016-1.425.248-.703.248-1.306.173-1.425-.074-.12-.272-.196-.576-.345zM12.002 2C6.479 2 2 6.478 2 12.002c0 1.76.459 3.42 1.305 4.862L2 22l5.29-1.385A9.957 9.957 0 0012.002 22c5.523 0 10-4.477 10-10.002C22 6.478 17.523 2 12.002 2zm0 18.324a8.312 8.312 0 01-4.237-1.155l-.304-.18-3.145.824.84-3.071-.197-.314A8.307 8.307 0 013.676 12.002c0-4.587 3.738-8.324 8.326-8.324 4.587 0 8.324 3.737 8.324 8.324 0 4.587-3.737 8.324-8.324 8.324z" clip-rule="evenodd" /></svg>
                            <a :href="'https://wa.me/' + project.whatsapp" target="_blank" class="hover:underline">{{ project.whatsapp }}</a>
                        </span>
                    </div>
                </div>
                <div class="flex flex-col md:items-end gap-2">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border" :class="getStatusColor(project.status)">
                        {{ getStatusLabel(project.status) }}
                    </span>
                    <span class="text-xl font-bold text-sky-600">{{ formatPrice(project.budget) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Content (Deskripsi, Progress, Komplain) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Review Admin Panel -->
                    <div v-if="project.status === 'pending_admin'" class="bg-amber-50 rounded-2xl border border-amber-200 p-6 shadow-sm shadow-amber-100/50 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-amber-800 mb-1">Butuh Review Admin</h2>
                            <p class="text-sm text-amber-700">Project ini baru diajukan oleh member. Silakan periksa kelayakan deskripsi dan budget.</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="showRejectModal = true" class="px-4 py-2 rounded-lg border border-red-200 bg-white text-red-600 text-sm font-semibold hover:bg-red-50">Tolak</button>
                            <button @click="approveProject" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm">Setujui Project</button>
                        </div>
                    </div>

                    <!-- Panel Penolakan (Modal) -->
                    <div v-if="showRejectModal" class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm shadow-red-100/50 relative">
                        <h3 class="text-lg font-bold text-red-700 mb-4">Tolak Project Request</h3>
                        <form @submit.prevent="rejectProject">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Penolakan</label>
                                <select v-model="rejectForm.rejection_reason" class="w-full rounded-lg border-slate-300 mb-2 text-sm">
                                    <option value="" disabled>Pilih Alasan Umum</option>
                                    <option value="Budget yang ditawarkan kurang sesuai dengan kompleksitas requirement.">Budget kurang sesuai.</option>
                                    <option value="Deskripsi kebutuhan belum jelas atau spesifik.">Deskripsi kurang jelas.</option>
                                    <option value="Waktu pengerjaan (deadline) terlalu singkat.">Deadline terlalu singkat.</option>
                                    <option value="Kami tidak menyediakan layanan untuk jenis aplikasi tersebut.">Tidak dapat dikerjakan.</option>
                                </select>
                                <textarea v-model="rejectForm.rejection_reason" rows="3" placeholder="Atau ketikkan alasan penolakan secara spesifik..." class="w-full rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" required></textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700" :disabled="rejectForm.processing">Kirim Penolakan</button>
                            </div>
                        </form>
                    </div>

                    <!-- Panel Resolusi Komplain Admin -->
                    <div v-if="project.status === 'complained' && project.complaint" class="bg-rose-50 rounded-2xl border border-rose-200 p-6 shadow-sm shadow-rose-100/50">
                        <h2 class="text-lg font-bold text-rose-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Resolusi Komplain Project
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-white rounded-lg p-4 border border-rose-100">
                                <h4 class="text-xs font-bold text-rose-700 uppercase tracking-wide mb-2">Tuntutan Member</h4>
                                <p class="text-sm text-slate-700 mb-2">{{ project.complaint.member_reason }}</p>
                                <a v-if="project.complaint.member_proof" :href="`/storage/${project.complaint.member_proof}`" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">Lihat Bukti Member &rarr;</a>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-rose-100">
                                <h4 class="text-xs font-bold text-rose-700 uppercase tracking-wide mb-2">Tanggapan Vendor</h4>
                                <div v-if="project.complaint.vendor_response">
                                    <p class="text-sm text-slate-700 mb-2">{{ project.complaint.vendor_response }}</p>
                                    <a v-if="project.complaint.vendor_proof" :href="`/storage/${project.complaint.vendor_proof}`" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">Lihat Bukti Vendor &rarr;</a>
                                </div>
                                <div v-else class="text-sm text-slate-400 italic">Vendor belum merespon.</div>
                            </div>
                        </div>

                        <div v-if="project.complaint.status === 'pending_admin'" class="bg-white p-4 rounded-lg border border-slate-200">
                            <p class="text-sm font-medium text-slate-800 mb-3">Tentukan Keputusan Resolusi:</p>
                            <div class="flex gap-3">
                                <button @click="resolveComplaint('refund_member')" class="flex-1 px-4 py-2 bg-orange-100 text-orange-700 hover:bg-orange-200 text-sm font-bold rounded-lg border border-orange-200">
                                    Menangkan Member (Refund)
                                </button>
                                <button @click="resolveComplaint('release_to_vendor')" class="flex-1 px-4 py-2 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 text-sm font-bold rounded-lg border border-emerald-200">
                                    Menangkan Vendor (Teruskan Dana)
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Deskripsi Project</h3>
                        <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed">{{ project.description }}</p>
                    </div>

                    <!-- Riwayat Progress -->
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                            Progress Vendor
                            <a v-if="project.github_link" :href="project.github_link" target="_blank" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1 bg-slate-100 px-2.5 py-1 rounded-md">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                                Repository
                            </a>
                        </h3>
                        <div v-if="project.progress_updates && project.progress_updates.length > 0" class="space-y-4">
                            <div v-for="update in project.progress_updates" :key="update.id" class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                                <p class="text-xs font-semibold text-slate-400 mb-1">{{ new Date(update.created_at).toLocaleString('id-ID') }}</p>
                                <p class="text-sm text-slate-700">{{ update.description }}</p>
                                <a v-if="update.attachment_path" :href="`/storage/${update.attachment_path}`" target="_blank" class="mt-2 inline-block text-xs font-medium text-sky-600 hover:underline">Lampiran File</a>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-slate-500 text-sm">
                            Belum ada laporan progress dari vendor.
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Applicant List & Status Pembayaran) -->
                <div class="space-y-6">
                    <!-- Status Pembayaran -->
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4 border-b pb-2">Status Transaksi</h3>
                        <div v-if="project.transaction">
                            <p class="text-xs font-medium text-slate-500 mb-1">Metode: {{ project.transaction.payment_method?.toUpperCase() || 'Bank Transfer' }}</p>
                            <p class="text-sm font-bold text-slate-800 mb-1">No. Ref: {{ project.transaction.reference_number }}</p>
                            <p class="text-xs">Status: 
                                <span class="font-bold text-sky-600 uppercase">{{ project.transaction.payment_status }}</span>
                            </p>
                            <p class="text-[10px] text-slate-400 mt-2">Dibuat: {{ new Date(project.transaction.created_at).toLocaleString('id-ID') }}</p>
                        </div>
                        <div v-else class="text-sm text-slate-500 italic">
                            Belum ada transaksi pembayaran.
                        </div>
                    </div>

                    <!-- Vendor Applicants -->
                    <div v-if="['open', 'in_progress', 'completed', 'complained'].includes(project.status)" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4 border-b pb-2">Kandidat Vendor</h3>
                        
                        <div v-if="project.vendor" class="mb-4 p-3 bg-sky-50 border border-sky-100 rounded-lg">
                            <p class="text-[10px] font-bold text-sky-600 uppercase tracking-wide mb-1">Vendor Terpilih</p>
                            <p class="text-sm font-bold text-slate-800">{{ project.vendor.name }}</p>
                        </div>

                        <div v-if="project.applicants && project.applicants.length > 0">
                            <p v-if="!project.vendor_id" class="text-xs text-slate-500 mb-3">Terdapat {{ project.applicants.length }} vendor yang mengajukan penawaran. Silakan pilih salah satu.</p>
                            <div class="space-y-3">
                                <div v-for="applicant in project.applicants" :key="applicant.id" class="p-3 border border-slate-100 rounded-lg text-sm bg-slate-50">
                                    <div class="font-bold text-slate-800">{{ applicant.vendor.name }}</div>
                                    <div class="text-[11px] text-slate-500 mb-2">{{ new Date(applicant.created_at).toLocaleDateString('id-ID') }}</div>
                                    <p class="text-xs text-slate-600 mb-3 italic line-clamp-3">"{{ applicant.cover_letter }}"</p>
                                    
                                    <button v-if="project.status === 'open'" @click="assignVendor(applicant.vendor.id)" class="w-full py-1.5 px-3 bg-slate-800 text-white text-xs font-semibold rounded-md hover:bg-slate-700 transition-colors">
                                        Pilih Vendor Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="!project.vendor_id" class="text-sm text-slate-500 italic">
                            Belum ada vendor yang mendaftar.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
