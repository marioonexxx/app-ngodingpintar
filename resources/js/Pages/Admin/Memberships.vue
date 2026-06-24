<script setup>
import AdminLayout from '../../Components/AdminLayout.vue';
import { money } from '../../Components/FormHelpers';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

defineProps({ memberships: Object, users: Object });

const showModal = ref(false);
const selected = ref(null);

const openCreate = () => {
    selected.value = null;
    showModal.value = true;
};

const openEdit = (membership) => {
    selected.value = membership;
    showModal.value = true;
};

const formAction = computed(() => selected.value ? `/admin/memberships/${selected.value.id}` : '/admin/memberships');

const submitForm = (event) => {
    const formData = new FormData(event.currentTarget);
    const isEditing = !!selected.value;

    router.post(formAction.value, formData, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert(isEditing ? 'Membership berhasil diperbarui.' : 'Membership berhasil ditambahkan.');
        },
        onError: () => errorAlert(),
    });
};

const deleteMembership = async (membership) => {
    const confirmed = await confirmAction({
        title: 'Hapus membership?',
        text: `Membership "${membership.name}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(`/admin/memberships/${membership.id}`, {
        preserveScroll: true,
        onSuccess: () => successAlert('Membership berhasil dihapus.'),
        onError: () => errorAlert('Membership gagal dihapus. Pastikan data tidak sedang digunakan.'),
    });
};

const deleteUser = async (user) => {
    const confirmed = await confirmAction({
        title: 'Hapus Member?',
        text: `Akun member "${user.name}" beserta datanya akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(`/admin/memberships/user/${user.id}`, {
        preserveScroll: true,
        onSuccess: () => successAlert('Member berhasil dihapus.'),
        onError: () => errorAlert('Member gagal dihapus. Pastikan data tidak sedang digunakan.'),
    });
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Membership</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola paket membership, durasi akses, dan benefit produk.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ memberships.total || memberships.data.length }} membership</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Membership</h2>
                    <button type="button" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white" @click="openCreate">Tambah</button>
                </div>
                <div class="flex gap-2">
                    <input class="rounded-md border border-slate-200 px-4 py-2 text-sm" placeholder="Cari membership...">
                    <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="membership in memberships.data" :key="membership.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold">{{ membership.name }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ membership.slug }}</td>
                            <td class="px-4 py-4 font-bold">{{ money(membership.price) }}</td>
                            <td class="px-4 py-4">{{ membership.duration_days || '-' }} hari</td>
                            <td class="px-4 py-4">
                                <span class="rounded bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ membership.is_active ? 'aktif' : 'nonaktif' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100" @click="openEdit(membership)">Edit</button>
                                    <button type="button" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" @click="deleteMembership(membership)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-8 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Data Member (Last Login)</h2>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Login Via</th>
                            <th class="px-4 py-3">Terakhir Login</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold">
                                <div class="flex items-center gap-3">
                                    <div class="grid size-8 place-items-center rounded-full bg-sky-50 font-black text-[#0284c7]">
                                        {{ user.name?.charAt(0) || 'M' }}
                                    </div>
                                    {{ user.name }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-slate-500">{{ user.email }}</td>
                            <td class="px-4 py-4">
                                <span v-if="user.google_id" class="rounded bg-red-50 px-3 py-1 text-xs font-bold text-red-700">Google</span>
                                <span v-else class="rounded bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">Manual</span>
                            </td>
                            <td class="px-4 py-4 font-bold text-slate-600">
                                {{ user.last_login_at ? new Date(user.last_login_at).toLocaleString('id-ID') : 'Belum pernah' }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <button type="button" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" @click="deleteUser(user)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="!users.data || users.data.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada data member.</td>
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
                        <h2 class="text-xl font-black">{{ selected ? 'Edit Membership' : 'Tambah Membership' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Atur harga, durasi, dan benefit membership.</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl" @click="showModal = false">×</button>
                </div>
                <div class="grid gap-4 px-5 py-5">
                    <label class="text-sm font-bold">Nama
                        <input name="name" required :value="selected?.name" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                    </label>
                    <label class="text-sm font-bold">Slug
                        <input name="slug" :value="selected?.slug" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="text-sm font-bold">Harga
                            <input name="price" type="number" min="0" required :value="selected?.price" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        </label>
                        <label class="text-sm font-bold">Durasi Hari
                            <input name="duration_days" type="number" min="1" :value="selected?.duration_days" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                        </label>
                    </div>
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
                    <button class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white">{{ selected ? 'Simpan Perubahan' : 'Tambah Membership' }}</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
