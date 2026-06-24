<script setup>
import MemberLayout from '@/Components/MemberLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';





const props = defineProps({
    projectRequest: Object,
});

const form = useForm({
    title: props.projectRequest.title,
    category: props.projectRequest.category,
    budget: props.projectRequest.budget,
    deadline_date: props.projectRequest.deadline_date,
    whatsapp: props.projectRequest.whatsapp,
    description: props.projectRequest.description,
    attachment: null,
});

const submit = () => {
    form.put(route('member.project-requests.update', props.projectRequest.id));
};
</script>

<template>
    <Head title="Edit Request Project" />

    <MemberLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Edit & Ajukan Ulang Request Project</h1>
            <Link :href="route('member.project-requests.show', projectRequest.id)" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Batal
            </Link>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <h3 class="text-red-800 font-medium mb-1">Alasan Penolakan Admin:</h3>
            <p class="text-sm text-red-600">{{ projectRequest.rejection_reason }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="title" value="Judul Project" />
                    <TextInput
                        id="title"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.title"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div>
                    <InputLabel for="category" value="Kategori Project" />
                    <select
                        id="category"
                        v-model="form.category"
                        class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="new_app">Pembuatan Aplikasi Baru</option>
                        <option value="revision">Revisi / Penambahan Fitur Aplikasi Lama</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.category" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <InputLabel for="budget" value="Budget / Harga Penawaran (Rp)" />
                        <TextInput
                            id="budget"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.budget"
                            required
                            min="50000"
                        />
                        <InputError class="mt-2" :message="form.errors.budget" />
                    </div>

                    <div>
                        <InputLabel for="deadline_date" value="Deadline / Batas Waktu Penyelesaian" />
                        <input
                            id="deadline_date"
                            type="date"
                            class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
                            v-model="form.deadline_date"
                            required
                        >
                        <InputError class="mt-2" :message="form.errors.deadline_date" />
                    </div>

                    <div>
                        <InputLabel for="whatsapp" value="No. WhatsApp Aktif" />
                        <TextInput
                            id="whatsapp"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.whatsapp"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.whatsapp" />
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-slate-800">Deskripsi Kebutuhan & Fitur</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="6"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10"
                        required
                        placeholder="Jelaskan secara detail fitur apa saja yang Anda butuhkan..."
                    ></textarea>
                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                    <p class="mt-2 text-sm text-slate-500">Semakin detail penjelasan Anda, semakin mudah vendor memberikan penawaran dan mencegah komplain di kemudian hari.</p>
                </div>

                <div>
                    <label for="attachment" class="block text-sm font-bold text-slate-800">File Pendukung (Opsional)</label>
                    <div v-if="projectRequest.attachment" class="mb-3">
                        <a :href="'/storage/' + projectRequest.attachment" target="_blank" class="text-sm font-medium text-sky-600 hover:text-sky-800 underline">Lihat File Saat Ini</a>
                        <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
                    </div>
                    <input
                        id="attachment"
                        type="file"
                        @input="form.attachment = $event.target.files[0]"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-4 text-sm outline-none transition focus:border-[#0284c7] focus:bg-white focus:ring-4 focus:ring-sky-500/10 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100"
                        accept=".pdf,.doc,.docx,.zip,.rar"
                    >
                    <p v-if="form.errors.attachment" class="mt-2 text-sm text-red-600">{{ form.errors.attachment }}</p>
                    <p class="mt-2 text-sm text-slate-500">
                        Unggah dokumen pendukung atau <em>System Requirements</em> (kebutuhan fitur aplikasi) agar vendor dapat memahami ekspektasi Anda dengan lebih jelas.<br>
                        Format yang diizinkan: PDF, DOC, DOCX, ZIP, RAR (Maksimal 10MB).
                    </p>
                </div>

                <div class="flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Ajukan Ulang Request
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </MemberLayout>
</template>
