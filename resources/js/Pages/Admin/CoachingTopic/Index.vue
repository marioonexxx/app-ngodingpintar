<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Components/AdminLayout.vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({
    topics: Object,
    filters: Object,
});

const isModalOpen = ref(false);
const isEditing = ref(false);
const currentTopicId = ref(null);

const form = useForm({
    name: '',
    description: '',
    is_active: true,
    sort_order: 0,
});

const openCreateModal = () => {
    form.reset();
    isEditing.value = false;
    currentTopicId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (topic) => {
    form.name = topic.name;
    form.description = topic.description || '';
    form.is_active = topic.is_active;
    form.sort_order = topic.sort_order;
    isEditing.value = true;
    currentTopicId.value = topic.id;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.coaching-topics.update', currentTopicId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                successAlert('Topik berhasil diperbarui.');
            },
            onError: () => errorAlert(),
        });
    } else {
        form.post(route('admin.coaching-topics.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                successAlert('Topik berhasil ditambahkan.');
            },
            onError: () => errorAlert(),
        });
    }
};

const deleteTopic = async (topic) => {
    const confirmed = await confirmAction({
        title: 'Hapus topik?',
        text: `Topik "${topic.name}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(route('admin.coaching-topics.destroy', topic.id), {
        preserveScroll: true,
        onSuccess: () => successAlert('Topik berhasil dihapus.'),
        onError: () => errorAlert('Topik gagal dihapus. Pastikan tidak sedang dipakai layanan coaching.'),
    });
};

const search = ref(props.filters.search || '');
const handleSearch = () => {
    router.get(route('admin.coaching-topics.index'), { search: search.value }, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Coaching Topics" />

    <AppLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Coaching Topics</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola topik untuk layanan coaching.</p>
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Topik</h2>
                    <button type="button" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white" @click="openCreateModal">Tambah Topik</button>
                </div>
                <div class="flex gap-2">
                    <input v-model="search" @keyup.enter="handleSearch" class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari topik...">
                    <button @click="handleSearch" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Sort Order</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="topic in topics.data" :key="topic.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold">{{ topic.name }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ topic.slug }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded px-3 py-1 text-xs font-bold" :class="topic.is_active ? 'bg-sky-50 text-[#0284c7]' : 'bg-red-50 text-red-500'">
                                    {{ topic.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">{{ topic.sort_order }}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-4">
                                    <button type="button" class="font-bold text-[#0284c7]" @click="openEditModal(topic)">Edit</button>
                                    <button type="button" class="font-bold text-red-500" @click="deleteTopic(topic)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="topics.data.length === 0">
                            <td colspan="5" class="px-4 py-4 text-center text-slate-500">Tidak ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-10 backdrop-blur-sm">
            <form @submit.prevent="submitForm" class="mx-auto w-full max-w-lg overflow-hidden rounded-md bg-white shadow-2xl shadow-slate-950/20">
                <div class="flex items-start justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-xl font-black">{{ isEditing ? 'Edit Topik' : 'Tambah Topik' }}</h2>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl" @click="closeModal">×</button>
                </div>
                
                <div class="grid gap-4 px-5 py-5">
                    <label class="text-sm font-bold">Nama Topik
                        <input v-model="form.name" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</div>
                    </label>

                    <label class="text-sm font-bold">Deskripsi
                        <textarea v-model="form.description" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3"></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</div>
                    </label>

                    <label class="text-sm font-bold">Urutan
                        <input v-model="form.sort_order" type="number" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        <div v-if="form.errors.sort_order" class="mt-1 text-xs text-red-500">{{ form.errors.sort_order }}</div>
                    </label>

                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="rounded border-slate-300 text-[#0284c7]">
                        <span class="text-sm font-bold">Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4">
                    <button type="button" class="rounded-md border border-slate-200 px-5 py-3 text-sm font-black" @click="closeModal">Batal</button>
                    <button type="submit" :disabled="form.processing" class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white" :class="{ 'opacity-50': form.processing }">
                        {{ isEditing ? 'Simpan Perubahan' : 'Tambah Topik' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
