<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { money } from './FormHelpers';
import { computed, onBeforeUnmount, ref } from 'vue';
import { confirmAction, errorAlert } from '@/Utils/swal';

const props = defineProps({
    title: String,
    subtitle: String,
    eyebrow: String,
    action: String,
    method: { type: String, default: 'POST' },
    backHref: String,
    submitLabel: String,
    product: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    memberships: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
    isClass: { type: Boolean, default: false },
    showClassSchedule: { type: Boolean, default: true },
});

const dateInputValue = (value) => value ? String(value).slice(0, 10) : '';
const timeInputValue = (value) => value ? String(value).slice(0, 5) : '';

const form = useForm({
    _method: props.method,
    name: props.product?.name || '',
    product_type: props.isClass ? 'class' : (props.product?.product_type || 'source_code'),
    product_category_ids: props.product?.categories?.map(c => c.id) || [],
    membership_id: props.product?.membership_id || '',
    price: props.product?.price || '',
    sale_price: props.product?.sale_price || '',
    status: props.product?.status || 'draft',
    download_limit: props.product?.download_limit || '',
    short_description: props.product?.short_description || '',
    description: props.product?.description || '',
    thumbnail_file: null,
    screenshots: [],
    file_path: props.product?.file_path || '',
    installation_guide_file: null,
    demo_url: props.product?.demo_url || '',
    // Class details
    schedule_type: props.product?.class_product?.schedule_type || 'fixed',
    start_date: dateInputValue(props.product?.class_product?.start_date),
    end_date: dateInputValue(props.product?.class_product?.end_date),
    start_time: timeInputValue(props.product?.class_product?.start_time),
    end_time: timeInputValue(props.product?.class_product?.end_time),
    recurring_days: props.product?.class_product?.recurring_days || [],
    platform: props.product?.class_product?.platform || '',
    meeting_link: props.product?.class_product?.meeting_link || '',
    max_participants: props.product?.class_product?.max_participants || '',
});

const daysOfWeek = [
    { value: 'Monday', label: 'Senin' },
    { value: 'Tuesday', label: 'Selasa' },
    { value: 'Wednesday', label: 'Rabu' },
    { value: 'Thursday', label: 'Kamis' },
    { value: 'Friday', label: 'Jumat' },
    { value: 'Saturday', label: 'Sabtu' },
    { value: 'Sunday', label: 'Minggu' },
];

const screenshotInput = ref(null);
const selectedScreenshots = ref([]);
const screenshotError = ref('');

const screenshotServerErrors = computed(() => {
    const errs = [];
    if (form.errors.screenshots) errs.push(form.errors.screenshots);
    for (let i = 0; i < 5; i++) {
        if (form.errors[`screenshots.${i}`]) {
            errs.push(form.errors[`screenshots.${i}`]);
        }
    }
    return errs;
});

const handleThumbnailChange = (e) => {
    const file = e.target.files[0];
    if (file && file.size > 3 * 1024 * 1024) {
        errorAlert('Ukuran thumbnail maksimal 3MB. Silakan pilih gambar yang lebih kecil.', 'File Terlalu Besar');
        e.target.value = '';
        form.thumbnail_file = null;
        return;
    }
    form.thumbnail_file = file;
};

const handleGuideChange = (e) => {
    const file = e.target.files[0];
    if (file && file.size > 10 * 1024 * 1024) {
        errorAlert('Ukuran file panduan maksimal 10MB. Silakan pilih file yang lebih kecil.', 'File Terlalu Besar');
        e.target.value = '';
        form.installation_guide_file = null;
        return;
    }
    form.installation_guide_file = file;
};

const autoSlug = computed(() => {
    return (form.name || '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
});

const assetUrl = (path) => {
    if (!path) return '';
    return path.startsWith('/') ? path : `/${path}`;
};

const formatSize = (bytes) => {
    if (!bytes) return '0 KB';
    return `${(bytes / 1024 / 1024).toFixed(2)} MB`;
};

const syncScreenshotInput = () => {
    if (!screenshotInput.value) return;
    const dataTransfer = new DataTransfer();
    selectedScreenshots.value.forEach((item) => dataTransfer.items.add(item.file));
    screenshotInput.value.files = dataTransfer.files;
};

const clearScreenshotPreviews = () => {
    selectedScreenshots.value.forEach((item) => URL.revokeObjectURL(item.preview));
};

const setScreenshots = (files) => {
    clearScreenshotPreviews();
    screenshotError.value = '';

    const imageFiles = Array.from(files || []).filter((file) => file.type.startsWith('image/'));

    if (imageFiles.length !== Array.from(files || []).length) {
        screenshotError.value = 'Beberapa file diabaikan karena bukan gambar.';
    }

    let filesToKeep = imageFiles;

    if (filesToKeep.length > 5) {
        screenshotError.value = 'Maksimal 5 screenshot. Sisa file diabaikan.';
        filesToKeep = filesToKeep.slice(0, 5);
    }

    const oversized = filesToKeep.filter((f) => f.size > 4 * 1024 * 1024);
    if (oversized.length > 0) {
        errorAlert(`Terdapat ${oversized.length} gambar yang melebihi batas 4MB dan dibatalkan.`, 'File Terlalu Besar');
        screenshotError.value = `Terdapat ${oversized.length} gambar yang melebihi batas 4MB dan dibatalkan.`;
        filesToKeep = filesToKeep.filter((f) => f.size <= 4 * 1024 * 1024);
    }

    selectedScreenshots.value = filesToKeep.map((file) => ({
        file,
        name: file.name,
        size: file.size,
        preview: URL.createObjectURL(file),
    }));

    syncScreenshotInput();
};

const handleScreenshotInput = (event) => {
    setScreenshots(event.target.files);
};

const handleScreenshotDrop = (event) => {
    setScreenshots(event.dataTransfer.files);
};

const removeScreenshot = (index) => {
    const [removed] = selectedScreenshots.value.splice(index, 1);
    if (removed) URL.revokeObjectURL(removed.preview);
    screenshotError.value = '';
    syncScreenshotInput();
};

const submitForm = async (event) => {
    const formElement = event.currentTarget;

    if (!formElement.reportValidity()) {
        const invalidElements = formElement.querySelectorAll(':invalid');
        if (invalidElements.length > 0) {
            alert('Validasi form gagal pada input: ' + (invalidElements[0].name || invalidElements[0].tagName));
            console.log('Invalid elements:', Array.from(invalidElements).map(el => el.name || el.tagName));
        }
        return;
    }

    const isEditing = props.method !== 'POST';
    const confirmed = await confirmAction({
        title: isEditing ? 'Simpan perubahan produk?' : 'Tambah produk?',
        text: isEditing ? 'Data produk akan diperbarui.' : 'Produk baru akan disimpan.',
        confirmButtonText: isEditing ? 'Ya, simpan' : 'Ya, tambah',
    });

    if (!confirmed) return;

    form.screenshots = selectedScreenshots.value.map(s => s.file);

    form.post(props.action, {
        preserveScroll: true,
        forceFormData: true,
        onError: (errors) => {
            const firstMessage = Object.values(errors)[0];
            errorAlert(firstMessage || 'Periksa kembali data yang diisi.', 'Perubahan Belum Disimpan');
        },
    });
};

onBeforeUnmount(clearScreenshotPreviews);
</script>

<template>
    <div class="space-y-5">
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5 border border-slate-100">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">{{ eyebrow }}</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">{{ title }}</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-500">{{ subtitle }}</p>
                </div>
                <Link :href="backHref" class="rounded-md border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 hover:bg-slate-50">
                    Kembali
                </Link>
            </div>
        </section>

        <form method="post" :action="action" enctype="multipart/form-data" class="grid gap-5 xl:grid-cols-[1fr_360px]" @submit.prevent="submitForm">
            <div class="space-y-5">
                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <h2 class="text-xl font-black">Informasi Produk</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label class="text-sm font-bold">Nama Produk
                            <input name="name" required v-model="form.name" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 outline-none focus:border-[#0284c7] focus:ring-4 focus:ring-sky-100">
                            <span v-if="form.errors.name" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.name }}</span>
                        </label>

                        <div class="text-sm font-bold">Slug Otomatis
                            <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">
                                {{ autoSlug || 'slug-otomatis-dari-nama-produk' }}
                            </div>
                            <p class="mt-2 text-xs font-medium text-slate-400">Slug dibuat otomatis dari judul dan dibuat unik oleh sistem.</p>
                        </div>

                        <label v-if="isAdmin && !isClass" class="text-sm font-bold md:col-span-2">Tipe Produk
                            <select name="product_type" required v-model="form.product_type" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                                <option value="source_code">Produk Digital (Source Code, E-book, dll)</option>
                                <option value="class">Produk Kelas (Sesi, Webinar, Workshop)</option>
                            </select>
                            <span v-if="form.errors.product_type" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.product_type }}</span>
                        </label>
                        <input v-else-if="isAdmin && isClass" type="hidden" name="product_type" v-model="form.product_type" />

                        <div class="text-sm font-bold md:col-span-2">Kategori (Pilih minimal 1)
                            <div class="mt-2 flex flex-wrap gap-3 rounded-md border border-slate-200 p-4">
                                <label v-for="category in categories" :key="category.id" class="flex cursor-pointer items-center gap-2 rounded-md border border-slate-200 px-3 py-2 hover:bg-slate-50 transition">
                                    <input type="checkbox" name="product_category_ids[]" :value="category.id" v-model="form.product_category_ids" class="rounded border-slate-300 text-sky-600 focus:ring-sky-600">
                                    <span class="text-sm font-medium">{{ category.name }}</span>
                                </label>
                            </div>
                            <span v-if="form.errors.product_category_ids" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.product_category_ids }}</span>
                        </div>

                        <label v-if="isAdmin" class="text-sm font-bold">Membership
                            <select name="membership_id" v-model="form.membership_id" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                                <option value="">Tanpa membership</option>
                                <option v-for="membership in memberships" :key="membership.id" :value="membership.id">{{ membership.name }}</option>
                            </select>
                            <span v-if="form.errors.membership_id" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.membership_id }}</span>
                        </label>

                        <label class="text-sm font-bold">Harga Normal
                            <input name="price" type="number" min="0" required v-model="form.price" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.price" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.price }}</span>
                        </label>

                        <label class="text-sm font-bold">Harga Promo
                            <input name="sale_price" type="number" min="0" v-model="form.sale_price" placeholder="Opsional" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.sale_price" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.sale_price }}</span>
                        </label>

                        <label v-if="isAdmin" class="text-sm font-bold">Status
                            <select name="status" v-model="form.status" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                            </select>
                            <span v-if="form.errors.status" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.status }}</span>
                        </label>

                        <label v-if="form.product_type !== 'class'" class="text-sm font-bold">Download Limit
                            <input name="download_limit" type="number" min="1" v-model="form.download_limit" placeholder="Opsional" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.download_limit" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.download_limit }}</span>
                        </label>

                        <label class="text-sm font-bold md:col-span-2">{{ form.product_type === 'class' ? 'Deskripsi Singkat Kelas' : 'Deskripsi Singkat' }}
                            <input name="short_description" v-model="form.short_description" :placeholder="form.product_type === 'class' ? 'Ringkasan singkat tentang kelas' : 'Ringkasan singkat produk'" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.short_description" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.short_description }}</span>
                        </label>

                        <label class="text-sm font-bold md:col-span-2">{{ form.product_type === 'class' ? 'Deskripsi Lengkap Kelas / Pelatihan' : 'Deskripsi Lengkap' }}
                            <textarea name="description" rows="7" v-model="form.description" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3" :placeholder="form.product_type === 'class' ? 'Materi yang akan dipelajari, benefit, dan syarat peserta.' : 'Fitur, stack, modul, benefit, dan catatan produk.'"></textarea>
                            <span v-if="form.errors.description" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.description }}</span>
                        </label>
                    </div>
                </section>

                <section v-if="form.product_type === 'class' && showClassSchedule" class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <h2 class="text-xl font-black">Detail Kelas & Jadwal</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label class="text-sm font-bold md:col-span-2">Tipe Jadwal
                            <select name="schedule_type" required v-model="form.schedule_type" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                                <option value="fixed">Jadwal Tetap (dalam 1 hari)</option>
                                <option value="recurring">Jadwal Mingguan</option>
                            </select>
                        </label>

                        <label class="text-sm font-bold">Tanggal Mulai
                            <input type="date" name="start_date" v-model="form.start_date" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.start_date" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.start_date }}</span>
                        </label>

                        <label class="text-sm font-bold">Tanggal Selesai (Opsional)
                            <input type="date" name="end_date" v-model="form.end_date" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.end_date" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.end_date }}</span>
                        </label>

                        <label class="text-sm font-bold">Waktu Mulai
                            <input type="time" name="start_time" v-model="form.start_time" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.start_time" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.start_time }}</span>
                        </label>

                        <label class="text-sm font-bold">Waktu Selesai (Opsional)
                            <input type="time" name="end_time" v-model="form.end_time" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.end_time" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.end_time }}</span>
                        </label>

                        <div v-if="form.schedule_type === 'recurring'" class="text-sm font-bold md:col-span-2">
                            Hari Berulang
                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <label v-for="day in daysOfWeek" :key="day.value" class="flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2">
                                    <input type="checkbox" :value="day.value" v-model="form.recurring_days" class="rounded border-slate-300 text-sky-600 focus:ring-sky-600">
                                    <span class="text-sm font-medium">{{ day.label }}</span>
                                </label>
                            </div>
                        </div>

                        <label class="text-sm font-bold">Platform Meeting
                            <input type="text" name="platform" v-model="form.platform" placeholder="Misal: Zoom, Google Meet" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        </label>
                        
                        <label class="text-sm font-bold">Link Akses / Meeting
                            <input type="url" name="meeting_link" v-model="form.meeting_link" placeholder="Link undangan kelas" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        </label>

                        <label class="text-sm font-bold md:col-span-2">Batas Kuota Peserta (Opsional)
                            <input type="number" min="1" name="max_participants" v-model="form.max_participants" placeholder="Kosongkan jika tidak dibatasi" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        </label>
                    </div>
                </section>

                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <h2 class="text-xl font-black">{{ form.product_type === 'class' ? 'Thumbnail Kelas' : 'Media Aplikasi' }}</h2>
                    <p v-if="form.product_type !== 'class'" class="mt-2 text-sm text-slate-500">Upload screenshot aplikasi secara bulk. Maksimal 5 gambar.</p>

                    <div class="mt-5 grid gap-4" :class="form.product_type === 'class' ? 'md:grid-cols-1' : 'md:grid-cols-2'">
                        <label class="text-sm font-bold">Thumbnail
                            <input name="thumbnail_file" type="file" accept="image/png,image/jpeg,image/webp" @change="handleThumbnailChange" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span class="mt-2 block text-xs font-medium text-slate-400">Kosongkan untuk memakai default thumbnail. Maksimal 3MB.</span>
                            <span v-if="form.errors.thumbnail_file" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.thumbnail_file }}</span>
                        </label>

                        <div v-if="product?.thumbnail" class="text-sm font-bold">Thumbnail Saat Ini
                            <img :src="assetUrl(product.thumbnail)" :alt="product.name" class="mt-2 aspect-video w-full max-w-[300px] rounded-md border border-slate-100 object-cover">
                        </div>
                    </div>

                    <template v-if="form.product_type !== 'class'">
                        <div
                            class="mt-5 rounded-md border-2 border-dashed border-sky-200 bg-sky-50/60 px-5 py-8 text-center transition hover:border-[#0284c7] hover:bg-sky-50"
                            @dragover.prevent
                            @drop.prevent="handleScreenshotDrop"
                        >
                            <input
                                ref="screenshotInput"
                                name="screenshots[]"
                                type="file"
                                accept="image/png,image/jpeg,image/webp"
                                multiple
                                class="hidden"
                                @change="handleScreenshotInput"
                            >
                            <p class="text-lg font-black text-slate-950">Drag & drop screenshot aplikasi</p>
                            <p class="mt-2 text-sm font-medium text-slate-500">Pilih sampai 5 gambar. Format JPG, PNG, atau WEBP.</p>
                            <button type="button" class="mt-5 rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white" @click="screenshotInput?.click()">
                                Pilih Gambar
                            </button>
                            <p class="mt-3 text-xs font-bold" :class="selectedScreenshots.length <= 5 ? 'text-emerald-600' : 'text-slate-400'">
                                {{ selectedScreenshots.length }} / 5 screenshot dipilih
                            </p>
                            <p v-if="screenshotError" class="mt-3 text-sm font-bold text-red-600">{{ screenshotError }}</p>
                            <div v-if="screenshotServerErrors.length" class="mt-3">
                                <p v-for="err in screenshotServerErrors" :key="err" class="text-sm font-bold text-red-600">{{ err }}</p>
                            </div>
                        </div>

                        <div v-if="selectedScreenshots.length" class="mt-5 grid gap-3 md:grid-cols-3">
                            <div v-for="(item, index) in selectedScreenshots" :key="item.preview" class="overflow-hidden rounded-md border border-slate-100 bg-white shadow-sm shadow-sky-950/5">
                                <img :src="item.preview" :alt="item.name" class="aspect-video w-full object-cover">
                                <div class="p-3">
                                    <p class="truncate text-sm font-black">{{ item.name }}</p>
                                    <div class="mt-2 flex items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-slate-400">{{ formatSize(item.size) }}</span>
                                        <button type="button" class="text-xs font-black text-red-500" @click="removeScreenshot(index)">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="product?.screenshots?.length" class="mt-5">
                            <p class="text-sm font-black">Screenshot Saat Ini</p>
                            <div class="mt-3 grid gap-3 md:grid-cols-3">
                                <a v-for="screenshot in product.screenshots" :key="screenshot" :href="assetUrl(screenshot)" target="_blank" class="block overflow-hidden rounded-md border border-slate-100 bg-slate-50">
                                    <img :src="assetUrl(screenshot)" alt="Screenshot aplikasi" class="aspect-video w-full object-cover">
                                </a>
                            </div>
                        </div>
                    </template>
                </section>

                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5">
                    <h2 class="text-xl font-black">{{ form.product_type === 'class' ? 'File Panduan / Modul' : 'File Produk & Panduan' }}</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label v-if="form.product_type !== 'class'" class="text-sm font-bold">File Path / Link Download Source Code
                            <input name="file_path" v-model="form.file_path" placeholder="URL Google Drive, path storage, atau catatan akses" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.file_path" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.file_path }}</span>
                        </label>

                        <label class="text-sm font-bold" :class="form.product_type === 'class' ? 'md:col-span-2' : ''">File Panduan Instalasi / Modul
                            <input name="installation_guide_file" type="file" @change="handleGuideChange" accept=".pdf,.txt,.md,.doc,.docx" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span class="mt-2 block text-xs font-medium text-slate-400">Opsional. Bisa PDF, TXT, Markdown, DOC, atau DOCX. Maksimal 10MB.</span>
                            <span v-if="form.errors.installation_guide_file" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.installation_guide_file }}</span>
                        </label>

                        <label v-if="form.product_type !== 'class'" class="text-sm font-bold md:col-span-2">Demo URL
                            <input name="demo_url" type="url" v-model="form.demo_url" placeholder="https://demo-produk.com" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <span v-if="form.errors.demo_url" class="mt-1 block text-xs font-bold text-red-500">{{ form.errors.demo_url }}</span>
                        </label>
                    </div>

                    <a v-if="product?.installation_guide" :href="assetUrl(product.installation_guide)" target="_blank" class="mt-4 inline-flex rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">
                        Lihat panduan instalasi saat ini
                    </a>
                </section>
            </div>

            <aside class="space-y-5">
                <section class="rounded-md border border-slate-100 bg-white p-5 shadow-sm shadow-sky-950/5 xl:sticky xl:top-24">
                    <h2 class="text-xl font-black">Ringkasan</h2>
                    <div class="mt-5 space-y-4 text-sm">
                        <div class="rounded-md bg-slate-50 p-4">
                            <p class="font-bold text-slate-500">Nama Produk</p>
                            <p class="mt-1 font-black text-slate-950">{{ form.name || 'Belum diisi' }}</p>
                        </div>
                        <div class="rounded-md bg-sky-50 p-4">
                            <p class="font-bold text-sky-600">Slug Preview</p>
                            <p class="mt-1 break-all font-black text-[#0284c7]">{{ autoSlug || '-' }}</p>
                        </div>
                        <div v-if="product" class="rounded-md bg-emerald-50 p-4">
                            <p class="font-bold text-emerald-700">Harga Saat Ini</p>
                            <p class="mt-1 font-black text-emerald-800">{{ money(form.sale_price || form.price) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <button :disabled="form.processing" class="rounded-md bg-[#0284c7] px-5 py-4 text-sm font-black text-white shadow-lg shadow-sky-950/10 disabled:opacity-50">
                            {{ submitLabel }}
                        </button>
                        <Link :href="backHref" class="rounded-md border border-slate-200 px-5 py-4 text-center text-sm font-black text-slate-700 hover:bg-slate-50">
                            Batal
                        </Link>
                    </div>
                </section>
            </aside>
        </form>
    </div>
</template>
