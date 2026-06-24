<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../Components/AdminLayout.vue';

const props = defineProps({
    bankAccounts: Array,
});

const isModalOpen = ref(false);
const editMode = ref(false);
const currentId = ref(null);

const form = useForm({
    bank_name: '',
    account_number: '',
    account_name: '',
    is_active: true,
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    editMode.value = false;
    currentId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (account) => {
    form.reset();
    form.clearErrors();
    editMode.value = true;
    currentId.value = account.id;
    form.bank_name = account.bank_name;
    form.account_number = account.account_number;
    form.account_name = account.account_name;
    form.is_active = Boolean(account.is_active);
    isModalOpen.value = true;
};

const submitForm = () => {
    if (editMode.value) {
        form.put(`/admin/bank-accounts/${currentId.value}`, {
            onSuccess: () => {
                isModalOpen.value = false;
            },
        });
    } else {
        form.post('/admin/bank-accounts', {
            onSuccess: () => {
                isModalOpen.value = false;
            },
        });
    }
};

const deleteAccount = (id) => {
    if (confirm('Yakin ingin menghapus rekening ini?')) {
        router.delete(`/admin/bank-accounts/${id}`);
    }
};
</script>

<template>
    <Head title="Manajemen Rekening Bank" />
    <AdminLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Rekening Bank</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola nomor rekening untuk pembayaran transfer manual.</p>
            </div>
            <button @click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-[#0284c7] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-700">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Rekening
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Bank</th>
                        <th class="px-6 py-4 font-semibold">Nomor Rekening</th>
                        <th class="px-6 py-4 font-semibold">Atas Nama</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-if="bankAccounts.length === 0">
                        <td colspan="5" class="py-10 text-center text-slate-500">Belum ada data rekening bank.</td>
                    </tr>
                    <tr v-for="account in bankAccounts" :key="account.id" class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ account.bank_name }}</td>
                        <td class="px-6 py-4 font-mono">{{ account.account_number }}</td>
                        <td class="px-6 py-4">{{ account.account_name }}</td>
                        <td class="px-6 py-4">
                            <span v-if="account.is_active" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                            <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                <span class="size-1.5 rounded-full bg-slate-400"></span> Nonaktif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEditModal(account)" class="rounded-md p-1.5 text-sky-600 transition hover:bg-sky-50">Edit</button>
                                <button @click="deleteAccount(account.id)" class="rounded-md p-1.5 text-red-600 transition hover:bg-red-50">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="isModalOpen = false"></div>
            <div class="relative w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
                <form @submit.prevent="submitForm">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-900">{{ editMode ? 'Edit Rekening' : 'Tambah Rekening' }}</h3>
                    </div>
                    <div class="space-y-4 p-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Nama Bank</label>
                            <input v-model="form.bank_name" placeholder="Contoh: BCA, Mandiri, BRI" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#0284c7] focus:outline-none focus:ring-1 focus:ring-[#0284c7]">
                            <p v-if="form.errors.bank_name" class="mt-1 text-xs text-red-600">{{ form.errors.bank_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Nomor Rekening</label>
                            <input v-model="form.account_number" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#0284c7] focus:outline-none focus:ring-1 focus:ring-[#0284c7]">
                            <p v-if="form.errors.account_number" class="mt-1 text-xs text-red-600">{{ form.errors.account_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Atas Nama</label>
                            <input v-model="form.account_name" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#0284c7] focus:outline-none focus:ring-1 focus:ring-[#0284c7]">
                            <p v-if="form.errors.account_name" class="mt-1 text-xs text-red-600">{{ form.errors.account_name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-slate-300 text-[#0284c7] focus:ring-[#0284c7]">
                            <label for="is_active" class="text-sm text-slate-700">Aktifkan Rekening Ini</label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <button type="button" @click="isModalOpen = false" class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200">Batal</button>
                        <button type="submit" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-medium text-white hover:bg-sky-700 disabled:opacity-50" :disabled="form.processing">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
