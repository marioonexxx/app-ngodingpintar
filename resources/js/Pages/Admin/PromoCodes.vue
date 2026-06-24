<script setup>
import AdminLayout from '../../Components/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

defineProps({ promoCodes: Object, discountOptions: Array });

const showModal = ref(false);
const selected = ref(null);
const code = ref('');

const generateCode = () => {
    const random = Math.random().toString(36).slice(2, 10).toUpperCase();

    return `MDV-${random}`;
};

const openCreate = () => {
    selected.value = null;
    code.value = generateCode();
    showModal.value = true;
};

const openEdit = (promoCode) => {
    selected.value = promoCode;
    code.value = promoCode.code || '';
    showModal.value = true;
};

const regenerate = () => {
    code.value = generateCode();
};

const formAction = computed(() => selected.value ? `/admin/promo-codes/${selected.value.id}` : '/admin/promo-codes');

const submitForm = (event) => {
    const formData = new FormData(event.currentTarget);
    const isEditing = !!selected.value;

    router.post(formAction.value, formData, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            successAlert(isEditing ? 'Kode promo berhasil diperbarui.' : 'Kode promo berhasil ditambahkan.');
        },
        onError: () => errorAlert(),
    });
};

const deletePromoCode = async (promoCode) => {
    const confirmed = await confirmAction({
        title: 'Hapus kode promo?',
        text: `Kode "${promoCode.code}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(`/admin/promo-codes/${promoCode.id}`, {
        preserveScroll: true,
        onSuccess: () => successAlert('Kode promo berhasil dihapus.'),
        onError: () => errorAlert('Kode promo gagal dihapus.'),
    });
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Kode Promo</h1>
                    <p class="mt-3 text-sm text-slate-500">Generate kode promo checkout dengan pilihan diskon 5%, 10%, atau 20%.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ promoCodes.total || promoCodes.data.length }} kode</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Kode Promo</h2>
                    <button type="button" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white" @click="openCreate">Tambah</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Diskon</th>
                            <th class="px-4 py-3">Dipakai</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="promoCode in promoCodes.data" :key="promoCode.id" class="border-t border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-black tracking-wide">{{ promoCode.code }}</p>
                                <p class="mt-1 text-xs text-slate-500">Dibuat {{ promoCode.created_at?.slice(0, 10) }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-md bg-emerald-50 px-3 py-1 text-sm font-black text-emerald-700">{{ promoCode.discount_percent }}%</span>
                            </td>
                            <td class="px-4 py-4 font-bold">{{ promoCode.used_count }}</td>
                            <td class="px-4 py-4">
                                <span
                                    class="rounded px-3 py-1 text-xs font-bold"
                                    :class="promoCode.is_active ? 'bg-sky-50 text-[#0284c7]' : 'bg-slate-100 text-slate-500'"
                                >
                                    {{ promoCode.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#0284c7] hover:bg-sky-100" @click="openEdit(promoCode)">Edit</button>
                                    <button type="button" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" @click="deletePromoCode(promoCode)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="promoCodes.data.length === 0">
                            <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-slate-400">Belum ada kode promo.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div v-if="showModal" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-10 backdrop-blur-sm">
            <form @submit.prevent="submitForm" class="mx-auto w-full max-w-xl overflow-hidden rounded-md bg-white shadow-2xl shadow-slate-950/20">
                <input v-if="selected" type="hidden" name="_method" value="PATCH">
                <div class="flex items-start justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-xl font-black">{{ selected ? 'Edit Kode Promo' : 'Tambah Kode Promo' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Kode bisa digenerate otomatis atau diisi manual.</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl" @click="showModal = false">x</button>
                </div>
                <div class="grid gap-4 px-5 py-5">
                    <label class="text-sm font-bold">Kode Promo
                        <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_auto]">
                            <input name="code" v-model="code" class="w-full rounded-md border border-slate-200 px-4 py-3 uppercase" placeholder="Kosongkan untuk otomatis">
                            <button type="button" class="rounded-md border border-sky-200 px-4 py-3 text-sm font-black text-[#0284c7]" @click="regenerate">Generate</button>
                        </div>
                    </label>
                    <label class="text-sm font-bold">Diskon
                        <select name="discount_percent" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3">
                            <option v-for="discount in discountOptions" :key="discount" :value="discount" :selected="selected?.discount_percent === discount">
                                {{ discount }}%
                            </option>
                        </select>
                    </label>
                    <label class="flex items-center gap-3 rounded-md border border-slate-200 px-4 py-3 text-sm font-bold">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" :checked="selected ? selected.is_active : true" class="size-4 rounded border-slate-300">
                        Aktifkan kode promo
                    </label>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4">
                    <button type="button" class="rounded-md border border-slate-200 px-5 py-3 text-sm font-black" @click="showModal = false">Batal</button>
                    <button class="rounded-md bg-[#0284c7] px-5 py-3 text-sm font-black text-white">{{ selected ? 'Simpan Perubahan' : 'Tambah Kode' }}</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
