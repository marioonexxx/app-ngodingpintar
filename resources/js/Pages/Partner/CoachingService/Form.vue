<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import PartnerLayout from '@/Components/PartnerLayout.vue';
import { ref } from 'vue';
import { errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({
    service: Object,
    topics: Array,
});

const isEditing = !!props.service;
const indexUrl = '/partner/coaching-services';
const storeUrl = indexUrl;
const updateUrl = isEditing ? `${indexUrl}/${props.service.id}` : indexUrl;

const form = useForm({
    name: props.service?.name || '',
    coaching_topic_id: props.service?.coaching_topic_id || '',
    session_duration_minutes: props.service?.session_duration_minutes || 60,
    meeting_platform: props.service?.meeting_platform || 'Zoom',
    price: props.service?.price || '',
    sale_price: props.service?.sale_price || '',
    short_description: props.service?.short_description || '',
    description: props.service?.description || '',
    thumbnail_file: null,
});

const photoPreview = ref(props.service?.thumbnail || null);

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.thumbnail_file = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    if (isEditing) {
        // use _method: PUT for file upload
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(updateUrl, {
            preserveScroll: true,
            onSuccess: () => successAlert('Layanan coaching berhasil diperbarui.'),
            onError: () => errorAlert(),
        });
    } else {
        form.post(storeUrl, {
            preserveScroll: true,
            onSuccess: () => successAlert('Layanan coaching berhasil ditambahkan.'),
            onError: () => errorAlert(),
        });
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Coaching Service' : 'Create Coaching Service'" />

    <PartnerLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Mentor Panel</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">{{ isEditing ? 'Edit Layanan' : 'Tambah Layanan' }}</h1>
                    <p class="mt-3 text-sm text-slate-500">Lengkapi form di bawah ini untuk layanan coaching Anda.</p>
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <form @submit.prevent="submit" class="mx-auto w-full max-w-4xl">
                <div class="grid gap-6 md:grid-cols-2">
                    
                    <!-- Thumbnail -->
                    <div class="md:col-span-2">
                        <label class="text-sm font-bold">Thumbnail
                            <div class="mt-2 flex items-center space-x-4">
                                <div class="h-24 w-24 bg-slate-50 rounded flex items-center justify-center overflow-hidden border border-slate-200">
                                    <img v-if="photoPreview" :src="photoPreview" class="h-24 w-24 object-cover" />
                                    <span v-else class="text-slate-400 text-xs">No Img</span>
                                </div>
                                <input type="file" @change="handlePhotoChange" accept="image/*" class="w-full rounded-md border border-slate-200 px-4 py-3">
                            </div>
                            <div v-if="form.errors.thumbnail_file" class="mt-1 text-xs text-red-500">{{ form.errors.thumbnail_file }}</div>
                        </label>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="text-sm font-bold">Nama Layanan
                            <input v-model="form.name" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</div>
                        </label>
                    </div>

                    <!-- Topic -->
                    <div>
                        <label class="text-sm font-bold">Topik Coaching
                            <select v-model="form.coaching_topic_id" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                                <option value="">Pilih Topik</option>
                                <option v-for="topic in topics" :key="topic.id" :value="topic.id">
                                    {{ topic.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.coaching_topic_id" class="mt-1 text-xs text-red-500">{{ form.errors.coaching_topic_id }}</div>
                        </label>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="text-sm font-bold">Durasi (Menit)
                            <input v-model="form.session_duration_minutes" type="number" min="15" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <div v-if="form.errors.session_duration_minutes" class="mt-1 text-xs text-red-500">{{ form.errors.session_duration_minutes }}</div>
                        </label>
                    </div>

                    <!-- Platform -->
                    <div>
                        <label class="text-sm font-bold">Platform Meeting
                            <input v-model="form.meeting_platform" placeholder="Contoh: Zoom, Google Meet" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <div v-if="form.errors.meeting_platform" class="mt-1 text-xs text-red-500">{{ form.errors.meeting_platform }}</div>
                        </label>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="text-sm font-bold">Harga Normal (IDR)
                            <input v-model="form.price" type="number" min="0" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <div v-if="form.errors.price" class="mt-1 text-xs text-red-500">{{ form.errors.price }}</div>
                        </label>
                    </div>

                    <!-- Sale Price -->
                    <div>
                        <label class="text-sm font-bold">Harga Promo (Opsional)
                            <input v-model="form.sale_price" type="number" min="0" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <div v-if="form.errors.sale_price" class="mt-1 text-xs text-red-500">{{ form.errors.sale_price }}</div>
                        </label>
                    </div>

                    <!-- Short Description -->
                    <div class="md:col-span-2">
                        <label class="text-sm font-bold">Deskripsi Singkat
                            <textarea v-model="form.short_description" rows="2" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3"></textarea>
                            <div v-if="form.errors.short_description" class="mt-1 text-xs text-red-500">{{ form.errors.short_description }}</div>
                        </label>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="text-sm font-bold">Deskripsi Lengkap
                            <textarea v-model="form.description" rows="6" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3"></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</div>
                        </label>
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-6">
                    <Link :href="indexUrl" class="rounded-md border border-slate-200 px-5 py-3 text-sm font-black text-slate-700">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white" :class="{ 'opacity-50': form.processing }">
                        {{ isEditing ? 'Simpan Perubahan' : 'Tambah Layanan' }}
                    </button>
                </div>
            </form>
        </section>
    </PartnerLayout>
</template>
