<script setup>
import PartnerLayout from '@/Components/PartnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';





const props = defineProps({
    project: Object,
});

const progressForm = useForm({
    description: '',
    github_link: props.project.github_link || '',
    attachment: null,
});

const responseForm = useForm({
    vendor_response: '',
    vendor_proof: null,
});

const submitProgress = () => {
    progressForm.post(route('partner.my-projects.progress', props.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            progressForm.reset('description', 'attachment');
        }
    });
};

const submitResponse = () => {
    responseForm.post(route('partner.my-projects.complaint-response', props.project.id));
};

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
    <Head :title="'Project: ' + project.title" />

    <PartnerLayout>
        <div class="px-5 py-8 md:px-10 md:py-12">
            <div class="mb-6 flex items-center justify-between">
                <Link :href="route('partner.my-projects.index')" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Project Saya
                </Link>
            </div>

            <!-- Header Info -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50 mb-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ project.title }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> Klien: {{ project.user.name }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Deadline: {{ new Date(project.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                    </div>
                </div>
                <div class="flex flex-col md:items-end gap-2">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border" :class="getStatusColor(project.status)">
                        {{ getStatusLabel(project.status) }}
                    </span>
                    <span class="text-xl font-bold text-sky-600">{{ formatPrice(project.budget) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content (Progress & Deskripsi) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Form Tanggapan Komplain (Jika Komplain) -->
                    <div v-if="project.status === 'complained' && project.complaint && project.complaint.status === 'pending_vendor'" class="bg-rose-50 rounded-2xl border border-rose-200 p-6 shadow-slate-100/50">
                        <h2 class="text-lg font-bold text-rose-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Member Mengajukan Komplain
                        </h2>
                        <div class="bg-white/60 rounded-lg p-4 mb-4 text-sm text-rose-900 border border-rose-100">
                            <strong>Alasan Member:</strong><br/>
                            {{ project.complaint.member_reason }}
                            <div v-if="project.complaint.member_proof" class="mt-2">
                                <a :href="`/storage/${project.complaint.member_proof}`" target="_blank" class="text-rose-700 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    Lihat Bukti Lampiran
                                </a>
                            </div>
                        </div>

                        <form @submit.prevent="submitResponse" class="space-y-4">
                            <div>
                                <InputLabel for="vendor_response" value="Tanggapan Anda" class="text-rose-800" />
                                <textarea id="vendor_response" v-model="responseForm.vendor_response" rows="4" class="mt-1 w-full rounded-lg border-rose-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm text-sm" required placeholder="Jelaskan pembelaan/tanggapan Anda..."></textarea>
                                <InputError :message="responseForm.errors.vendor_response" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel for="vendor_proof" value="Upload Bukti Tanggapan (Opsional, max 2MB)" class="text-rose-800" />
                                <input type="file" @input="responseForm.vendor_proof = $event.target.files[0]" class="mt-1 block w-full text-sm text-rose-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-white file:text-rose-700 hover:file:bg-rose-100" accept="image/*" />
                                <InputError :message="responseForm.errors.vendor_proof" class="mt-1" />
                            </div>
                            <PrimaryButton class="bg-rose-600 hover:bg-rose-700" :class="{ 'opacity-50': responseForm.processing }" :disabled="responseForm.processing">
                                Kirim Tanggapan ke Admin
                            </PrimaryButton>
                        </form>
                    </div>

                    <div v-else-if="project.status === 'complained'" class="bg-orange-50 rounded-2xl border border-orange-200 p-6 shadow-slate-100/50">
                        <h2 class="text-lg font-bold text-orange-800 mb-2">Komplain Sedang Direview Admin</h2>
                        <p class="text-sm text-orange-700">Tanggapan Anda telah dikirim. Saat ini Admin sedang meninjau komplain dan bukti dari kedua belah pihak untuk mengambil keputusan (meneruskan dana ke Anda atau mengembalikan dana ke Member).</p>
                    </div>

                    <!-- History Progress -->
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-lg font-bold text-slate-800 mb-6">Log Update Progress</h3>
                        
                        <div v-if="project.progress_updates && project.progress_updates.length > 0" class="space-y-6">
                            <div v-for="update in project.progress_updates" :key="update.id" class="relative pl-6 border-l-2 border-slate-100 pb-2 last:pb-0">
                                <div class="absolute w-3 h-3 bg-sky-500 rounded-full -left-[7px] top-1.5 ring-4 ring-white"></div>
                                <p class="text-xs font-semibold text-slate-400 mb-1">{{ new Date(update.created_at).toLocaleString('id-ID') }}</p>
                                <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ update.description }}</p>
                                <a v-if="update.attachment_path" :href="`/storage/${update.attachment_path}`" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 bg-sky-50 px-3 py-1.5 rounded-lg hover:bg-sky-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    Lampiran File
                                </a>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-sm text-slate-500 italic">Belum ada progress yang dilaporkan.</p>
                        </div>
                    </div>

                    <!-- Form Update Progress -->
                    <div v-if="project.status === 'in_progress'" class="bg-slate-50 rounded-2xl border border-slate-200 p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Laporkan Progress Baru</h3>
                        <form @submit.prevent="submitProgress" class="space-y-5">
                            <div>
                                <InputLabel for="github_link" value="Link Github Repository (Opsional, Update jika ada perubahan)" />
                                <TextInput id="github_link" type="url" v-model="progressForm.github_link" class="mt-1 block w-full text-sm" placeholder="https://github.com/username/repo" />
                                <InputError :message="progressForm.errors.github_link" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel for="description" value="Deskripsi Progress" />
                                <textarea id="description" v-model="progressForm.description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm text-sm" required placeholder="Misal: Sudah menyelesaikan fitur login dan register..."></textarea>
                                <InputError :message="progressForm.errors.description" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel for="attachment" value="Lampiran Screenshot / File (Opsional, Max 5MB)" />
                                <input type="file" @input="progressForm.attachment = $event.target.files[0]" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
                                <InputError :message="progressForm.errors.attachment" class="mt-1" />
                            </div>
                            <div class="flex justify-end pt-2">
                                <PrimaryButton :class="{ 'opacity-50': progressForm.processing }" :disabled="progressForm.processing">
                                    Simpan Progress
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar (Deskripsi Project Asli) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4 border-b pb-2">Deskripsi Awal Klien</h3>
                        <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed">{{ project.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </PartnerLayout>
</template>
