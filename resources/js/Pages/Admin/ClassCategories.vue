<script setup>
import AdminLayout from '../../Components/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({ categories: Object });

const showModal = ref(false);
const selected = ref(null);

const openCreate = () => {
    selected.value = null;
    showModal.value = true;
};

const openEdit = (category) => {
    selected.value = category;
    showModal.value = true;
};

const formAction = computed(() => selected.value ? `/admin/class-categories/${selected.value.id}` : '/admin/class-categories');

const submitForm = (event) => {
    const formData = new FormData(event.currentTarget);
    const isEditing = !!selected.value;

    router.post(formAction.value, formData, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert(isEditing ? 'Kategori berhasil diperbarui.' : 'Kategori berhasil ditambahkan.');
        },
        onError: () => errorAlert(),
    });
};

const deleteCategory = async (category) => {
    const confirmed = await confirmAction({
        title: 'Hapus kategori?',
        text: `Kategori "${category.name}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(`/admin/categories/${category.id}`, {
        preserveScroll: true,
        onSuccess: () => successAlert('Kategori berhasil dihapus.'),
        onError: () => errorAlert('Kategori gagal dihapus. Pastikan kategori tidak sedang digunakan.'),
    });
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Kategori Kelas</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola kategori untuk kelas dan webinar.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ categories.total || categories.data.length }} kategori</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Kategori</h2>
                    <button type="button" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white" @click="openCreate">Tambah</button>
                </div>
                <div class="flex gap-2">
                    <input class="rounded-md border border-slate-200 px-4 py-2 text-sm" placeholder="Cari nama kategori...">
                    <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categories.data" :key="category.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold">{{ category.name }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ category.slug }}</td>
                            <td class="px-4 py-4">{{ category.products_count }} kelas</td>
                            <td class="px-4 py-4">
                                <span class="rounded bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ category.is_active ? 'aktif' : 'nonaktif' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100" @click="openEdit(category)">Edit</button>
                                    <button type="button" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" @click="deleteCategory(category)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div v-if="showModal" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-16 backdrop-blur-sm">
            <form @submit.prevent="submitForm" class="mx-auto w-full max-w-xl overflow-hidden rounded-md bg-white shadow-2xl shadow-slate-950/20">
                <input v-if="selected" type="hidden" name="_method" value="PATCH">
                <div class="flex items-start justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-xl font-black">{{ selected ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Slug akan digenerate secara otomatis.</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl" @click="showModal = false">×</button>
                </div>
                <div class="grid gap-4 px-5 py-5">
                    <label class="text-sm font-bold">Nama Kategori
                        <input name="name" required :value="selected?.name" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                    </label>
                    <label class="text-sm font-bold">Deskripsi
                        <textarea name="description" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">{{ selected?.description }}</textarea>
                    </label>
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-2 text-sm font-bold">
                        <input type="checkbox" name="is_active" value="1" :checked="selected?.is_active ?? true">
                        Aktif
                    </label>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4">
                    <button type="button" class="rounded-md border border-slate-200 px-5 py-3 text-sm font-black" @click="showModal = false">Batal</button>
                    <button class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white">{{ selected ? 'Simpan Perubahan' : 'Tambah Kategori' }}</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
