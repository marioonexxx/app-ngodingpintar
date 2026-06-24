<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Components/AdminLayout.vue';
import { confirmAction, errorAlert } from '@/Utils/swal';

const props = defineProps({
    product: Object,
    batches: Array,
    statuses: Array,
    nextBatchNumber: Number,
    storeUrl: String,
});

const editingBatch = ref(null);
const days = [
    { value: 1, label: 'Senin' },
    { value: 2, label: 'Selasa' },
    { value: 3, label: 'Rabu' },
    { value: 4, label: 'Kamis' },
    { value: 5, label: 'Jumat' },
    { value: 6, label: 'Sabtu' },
    { value: 7, label: 'Minggu' },
];

const emptyBatch = () => ({
    batch_id: null,
    status: 'open',
    schedule_type: 'fixed',
    start_date: '',
    end_date: '',
    start_time: '',
    end_time: '',
    recurring_days: [],
    platform: '',
    meeting_link: '',
    max_participants: '',
});

const form = useForm(emptyBatch());

const dateValue = (value) => value ? String(value).slice(0, 10) : '';
const timeValue = (value) => value ? String(value).slice(0, 5) : '';
const formatBatchDate = (value) => value
    ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(String(value).slice(0, 10) + 'T00:00:00'))
    : 'Tanggal belum ditentukan';

const createBatch = () => {
    editingBatch.value = null;
    form.defaults(emptyBatch());
    form.reset();
    form.clearErrors();
};

const editBatch = (batch) => {
    editingBatch.value = batch;
    form.batch_id = batch.id;
    form.status = batch.status;
    form.schedule_type = batch.schedule_type;
    form.start_date = dateValue(batch.start_date);
    form.end_date = dateValue(batch.end_date);
    form.start_time = timeValue(batch.start_time);
    form.end_time = timeValue(batch.end_time);
    form.recurring_days = batch.recurring_days || [];
    form.platform = batch.platform || '';
    form.meeting_link = batch.meeting_link || '';
    form.max_participants = batch.max_participants || '';
    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => createBatch(),
    };

    if (editingBatch.value) {
        if (!editingBatch.value.update_url) {
            errorAlert('URL update batch tidak ditemukan. Silakan muat ulang halaman.');
            return;
        }

        form.patch(editingBatch.value.update_url, options);
        return;
    }

    if (!props.storeUrl) {
        errorAlert('URL tambah batch tidak ditemukan. Silakan muat ulang halaman.');
        return;
    }

    form.post(props.storeUrl, options);
};

const deleteBatch = async (batch) => {
    const confirmed = await confirmAction({
        title: `Hapus Batch ${batch.batch_number}?`,
        text: 'Batch hanya dapat dihapus jika belum memiliki transaksi.',
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#dc2626',
    });

    if (!confirmed) return;

    if (!batch.delete_url) {
        errorAlert('URL hapus batch tidak ditemukan. Silakan muat ulang halaman.');
        return;
    }

    router.delete(batch.delete_url, {
        data: { batch_id: batch.id },
        preserveScroll: true,
        onError: () => errorAlert('Batch gagal dihapus.'),
    });
};

const statusClass = (status) => ({
    open: 'bg-emerald-50 text-emerald-700',
    closed: 'bg-slate-100 text-slate-600',
    completed: 'bg-sky-50 text-sky-700',
    cancelled: 'bg-red-50 text-red-600',
}[status] || 'bg-slate-100 text-slate-600');
</script>

<template>
    <Head :title="`Batch - ${product.name}`" />
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <Link href="/admin/class-products" class="text-sm font-bold text-sky-600">← Kembali ke Produk Kelas</Link>
                    <p class="mt-5 text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Kelola Batch</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">{{ product.name }}</h1>
                    <p class="mt-2 text-sm text-slate-500">Data produk tetap sama; cukup tambahkan jadwal baru untuk pelaksanaan berikutnya.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ batches.length }} batch</span>
            </div>
        </section>

        <div class="mt-5 grid gap-5 xl:grid-cols-[420px_1fr]">
            <form @submit.prevent="submit" class="h-max rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-black">{{ editingBatch ? `Edit Batch ${editingBatch.batch_number}` : `Tambah Batch ${nextBatchNumber}` }}</h2>
                        <p v-if="!editingBatch" class="mt-1 text-xs text-slate-500">Nomor batch dihitung otomatis oleh sistem.</p>
                    </div>
                    <button v-if="editingBatch" type="button" @click="createBatch" class="text-xs font-bold text-slate-500">Batal Edit</button>
                </div>

                <div class="mt-5 space-y-4">
                    <label class="block text-sm font-bold">Status
                        <select v-model="form.status" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                        </select>
                    </label>
                    <label class="block text-sm font-bold">Tipe Jadwal
                        <select v-model="form.schedule_type" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <option value="fixed">Jadwal Tetap</option>
                            <option value="recurring">Jadwal Mingguan</option>
                        </select>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-sm font-bold">Tanggal Mulai
                            <input v-model="form.start_date" type="date" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-3">
                        </label>
                        <label class="text-sm font-bold">Tanggal Selesai
                            <input v-model="form.end_date" type="date" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-3">
                        </label>
                        <label class="text-sm font-bold">Waktu Mulai
                            <input v-model="form.start_time" type="time" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-3">
                        </label>
                        <label class="text-sm font-bold">Waktu Selesai
                            <input v-model="form.end_time" type="time" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-3">
                        </label>
                    </div>
                    <div v-if="form.schedule_type === 'recurring'">
                        <p class="text-sm font-bold">Hari Berulang</p>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <label v-for="day in days" :key="day.value" class="flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm">
                                <input v-model="form.recurring_days" type="checkbox" :value="day.value">
                                {{ day.label }}
                            </label>
                        </div>
                    </div>
                    <label class="block text-sm font-bold">Platform
                        <input v-model="form.platform" placeholder="Zoom / Google Meet" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                    </label>
                    <label class="block text-sm font-bold">Link Pertemuan
                        <input v-model="form.meeting_link" type="url" placeholder="https://..." class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                    </label>
                    <label class="block text-sm font-bold">Kuota Peserta
                        <input v-model="form.max_participants" type="number" min="1" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                    </label>
                    <p v-if="Object.keys(form.errors).length" class="rounded-md bg-red-50 px-3 py-2 text-xs font-bold text-red-600">
                        Periksa kembali data batch yang diisi.
                    </p>
                    <button :disabled="form.processing" class="w-full rounded-md bg-[#0284c7] px-4 py-3 font-black text-white disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan...' : (editingBatch ? 'Simpan Perubahan' : 'Buat Batch Otomatis') }}
                    </button>
                </div>
            </form>

            <section class="rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
                <h2 class="text-xl font-black">Riwayat Batch</h2>
                <div class="mt-5 space-y-4">
                    <article v-for="batch in batches" :key="batch.id" class="rounded-xl border border-slate-200 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-black">Batch {{ batch.batch_number }}</h3>
                                    <span class="rounded px-2 py-1 text-[10px] font-bold uppercase" :class="statusClass(batch.status)">{{ batch.status }}</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-600">
                                    {{ formatBatchDate(batch.start_date) }}
                                    <span v-if="batch.end_date"> s/d {{ formatBatchDate(batch.end_date) }}</span>
                                </p>
                                <p class="mt-1 text-sm text-slate-500">{{ batch.start_time || '-' }} - {{ batch.end_time || 'Selesai' }} · {{ batch.platform || 'Platform belum ditentukan' }}</p>
                                <p class="mt-2 text-xs font-semibold text-sky-600">{{ batch.paid_participants_count }} peserta lunas</p>
                            </div>
                            <div class="flex gap-3 text-sm font-bold">
                                <button type="button" @click="editBatch(batch)" class="text-[#0284c7]">Edit</button>
                                <button type="button" @click="deleteBatch(batch)" class="text-red-500">Hapus</button>
                            </div>
                        </div>
                    </article>
                    <p v-if="!batches.length" class="rounded-md border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">Belum ada batch.</p>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
