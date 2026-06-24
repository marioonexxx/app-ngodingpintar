<script setup>
import AppLayout from '../../../Components/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.user);

const form = useForm({
    title: '',
    category: 'new_app',
    budget: '',
    deadline_date: '',
    whatsapp: '',
    description: '',
    attachment: null,
});

const submit = () => {
    if (isLoggedIn.value) {
        form.post('/request');
    }
};
</script>

<template>
    <Head title="Buat Request Project" />

    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-sky-900">Buat Request Project</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-sky-100 overflow-hidden p-6">
            <div class="mb-6 p-4 rounded-md bg-amber-50 border border-amber-200">
                <p class="text-sm text-amber-800" v-if="!isLoggedIn">
                    Silakan <strong>login terlebih dahulu</strong> untuk dapat mengisi formulir request aplikasi ini.
                </p>
                <p class="text-sm text-amber-800" v-else>
                    Silakan isi detail request aplikasi Anda di bawah ini dengan jelas.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-bold text-slate-800">Judul Project</label>
                    <input
                        id="title"
                        type="text"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                        :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                        v-model="form.title"
                        required
                        :disabled="!isLoggedIn"
                        placeholder="Contoh: Aplikasi Kasir Toko Baju"
                    >
                    <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label for="category" class="block text-sm font-bold text-slate-800">Kategori Project</label>
                    <select
                        id="category"
                        v-model="form.category"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                        :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                        required
                        :disabled="!isLoggedIn"
                    >
                        <option value="new_app">Pembuatan Aplikasi Baru</option>
                        <option value="revision">Revisi / Penambahan Fitur Aplikasi Lama</option>
                    </select>
                    <p v-if="form.errors.category" class="mt-2 text-sm text-red-600">{{ form.errors.category }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="budget" class="block text-sm font-bold text-slate-800">Budget / Harga Penawaran (Rp)</label>
                        <input
                            id="budget"
                            type="number"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                            :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                            v-model="form.budget"
                            required
                            min="50000"
                            :disabled="!isLoggedIn"
                            placeholder="Minimal Rp 50.000"
                        >
                        <p v-if="form.errors.budget" class="mt-2 text-sm text-red-600">{{ form.errors.budget }}</p>
                    </div>

                    <div>
                        <label for="deadline_date" class="block text-sm font-bold text-slate-800">Deadline / Batas Waktu Penyelesaian</label>
                        <input
                            id="deadline_date"
                            type="date"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                            :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                            v-model="form.deadline_date"
                            required
                            :disabled="!isLoggedIn"
                        >
                        <p v-if="form.errors.deadline_date" class="mt-2 text-sm text-red-600">{{ form.errors.deadline_date }}</p>
                    </div>

                    <div>
                        <label for="whatsapp" class="block text-sm font-bold text-slate-800">No. WhatsApp Aktif</label>
                        <input
                            id="whatsapp"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                            :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                            v-model="form.whatsapp"
                            required
                            :disabled="!isLoggedIn"
                            placeholder="Contoh: 08123456789"
                        >
                        <p v-if="form.errors.whatsapp" class="mt-2 text-sm text-red-600">{{ form.errors.whatsapp }}</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-slate-800">Deskripsi Kebutuhan & Fitur</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="6"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                        :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                        required
                        :disabled="!isLoggedIn"
                        placeholder="Jelaskan secara detail fitur apa saja yang Anda butuhkan..."
                    ></textarea>
                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                    <p class="mt-2 text-sm text-slate-500">Semakin detail penjelasan Anda, semakin mudah vendor memberikan penawaran dan mencegah komplain di kemudian hari.</p>
                </div>

                <div>
                    <label for="attachment" class="block text-sm font-bold text-slate-800">File Pendukung (Opsional)</label>
                    <input
                        id="attachment"
                        type="file"
                        @input="form.attachment = $event.target.files[0]"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100"
                        :class="{'cursor-not-allowed opacity-60': !isLoggedIn}"
                        :disabled="!isLoggedIn"
                        accept=".pdf,.doc,.docx,.zip,.rar"
                    >
                    <p v-if="form.errors.attachment" class="mt-2 text-sm text-red-600">{{ form.errors.attachment }}</p>
                    <p class="mt-2 text-sm text-slate-500">
                        Unggah dokumen pendukung atau <em>System Requirements</em> (kebutuhan fitur aplikasi) agar vendor dapat memahami ekspektasi Anda dengan lebih jelas.<br>
                        Format yang diizinkan: PDF, DOC, DOCX, ZIP, RAR (Maksimal 10MB).
                    </p>
                </div>

                <div class="flex items-center justify-end">
                    <Link v-if="!isLoggedIn" href="/login" class="flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-white shadow-md shadow-amber-500/25 transition-all hover:bg-amber-600">
                        Login untuk Request
                    </Link>
                    <button v-else type="submit" :disabled="form.processing" class="flex items-center justify-center gap-2 rounded-xl bg-[#0284c7] px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-500/25 transition-all hover:bg-[#1a40ba] disabled:opacity-50">
                        Selanjutnya
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
