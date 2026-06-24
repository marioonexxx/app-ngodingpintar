<script setup>
import AdminLayout from '@/Components/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';



const props = defineProps({
    refunds: Object,
});

const form = useForm({
    admin_notes: '',
    transfer_proof: null,
});

const selectedRefund = ref(null);
const showProcessModal = ref(false);

const openProcessModal = (refund) => {
    selectedRefund.value = refund;
    form.reset();
    showProcessModal.value = true;
};

const processRefund = () => {
    form.post(route('admin.refund-requests.process', selectedRefund.value.id), {
        onSuccess: () => {
            showProcessModal.value = false;
            form.reset();
        }
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head title="Manajemen Refund" />

    <AdminLayout>
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Refund Requests</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dan proses pengembalian dana ke member akibat komplain atau pembatalan transaksi.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="py-4 px-6 text-xs font-semibold text-slate-600 uppercase">Waktu Pengajuan</th>
                                <th class="py-4 px-6 text-xs font-semibold text-slate-600 uppercase">Member</th>
                                <th class="py-4 px-6 text-xs font-semibold text-slate-600 uppercase">Nominal</th>
                                <th class="py-4 px-6 text-xs font-semibold text-slate-600 uppercase">Alasan & Rekening</th>
                                <th class="py-4 px-6 text-xs font-semibold text-slate-600 uppercase">Status</th>
                                <th class="py-4 px-6 text-xs font-semibold text-slate-600 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr v-for="refund in refunds.data" :key="refund.id" class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-6 text-sm text-slate-500">
                                    {{ new Date(refund.created_at).toLocaleString('id-ID') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800">{{ refund.user.name }}</div>
                                    <div class="text-xs text-slate-500">{{ refund.user.email }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-base font-bold text-sky-600">{{ formatPrice(refund.amount) }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-xs text-slate-600 font-medium bg-slate-100 p-2 rounded-md mb-2">
                                        {{ refund.reason }}
                                    </div>
                                    <div class="text-xs text-slate-800 font-mono bg-amber-50 p-2 border border-amber-100 rounded-md">
                                        Data Bank Member (Bila Ada)<br>
                                        <span class="text-slate-500 italic">Lihat profil member atau hubungi manual jika belum tersedia.</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span v-if="refund.status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold bg-orange-100 text-orange-700 rounded-full border border-orange-200">
                                        Menunggu Transfer
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 rounded-full border border-emerald-200">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Selesai
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button v-if="refund.status === 'pending'" @click="openProcessModal(refund)" class="text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded-lg transition-colors">
                                        Proses Refund
                                    </button>
                                    <a v-if="refund.transfer_proof" :href="`/storage/${refund.transfer_proof}`" target="_blank" class="text-xs font-semibold text-sky-600 hover:underline">
                                        Lihat Bukti
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="!refunds.data.length">
                                <td colspan="6" class="py-10 text-center text-slate-500">
                                    Belum ada pengajuan refund.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="refunds.links && refunds.links.length > 3" class="px-6 py-4 border-t border-slate-200">
                    <Pagination :links="refunds.links" />
                </div>
            </div>
        </div>

        <!-- Modal Proses Refund -->
        <div v-if="showProcessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Proses Transfer Refund</h3>
                    <button @click="showProcessModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form @submit.prevent="processRefund" class="p-6">
                    <div class="mb-5 p-4 bg-sky-50 rounded-lg border border-sky-100">
                        <p class="text-sm text-sky-800 mb-1">Total yang harus ditransfer:</p>
                        <p class="text-2xl font-bold text-sky-600">{{ formatPrice(selectedRefund.amount) }}</p>
                        <p class="text-xs text-sky-700 mt-2 font-medium">Member: {{ selectedRefund.user.name }}</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Bukti Transfer (Wajib)</label>
                            <input type="file" @input="form.transfer_proof = $event.target.files[0]" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" required />
                            <InputError :message="form.errors.transfer_proof" class="mt-1" />
                            <p class="text-[10px] text-slate-500 mt-1">Upload foto/screenshot bukti transfer (Max 2MB).</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea v-model="form.admin_notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Misal: Ditransfer via BCA ke norek 123..."></textarea>
                            <InputError :message="form.errors.admin_notes" class="mt-1" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showProcessModal = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-bold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50" :disabled="form.processing">
                            Selesaikan Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
