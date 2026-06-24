<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({
    enableVendor: Boolean,
    enableMentor: Boolean,
    platformFeePercent: Number,
});

const form = useForm({
    enable_vendor_registration: props.enableVendor,
    enable_mentor_registration: props.enableMentor,
    platform_fee_percent: props.platformFeePercent || 20,
});

const submit = () => {
    form.patch('/admin/partner-settings', {
        preserveScroll: true,
        onSuccess: () => successAlert('Pengaturan partner berhasil disimpan.'),
        onError: () => errorAlert(),
    });
};
</script>

<template>
    <Head title="Pengaturan Partner" />
    <AdminLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Pengaturan Partner</h1>
                    <p class="text-sm text-slate-500">Aktifkan atau nonaktifkan pendaftaran vendor dan mentor.</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <form @submit.prevent="submit" class="divide-y divide-slate-100">
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Pendaftaran Vendor</h2>
                            <p class="text-sm text-slate-500">Izinkan user untuk mendaftar sebagai vendor dan menjual produk digital.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" v-model="form.enable_vendor_registration" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#0284c7]/30 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0284c7]"></div>
                        </label>
                    </div>
                    
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Pendaftaran Mentor</h2>
                            <p class="text-sm text-slate-500">Izinkan user untuk mendaftar sebagai mentor dan menawarkan jasa coaching.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" v-model="form.enable_mentor_registration" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#0284c7]/30 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0284c7]"></div>
                        </label>
                    </div>

                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Potongan Transaksi Partner (Platform Fee)</h2>
                            <p class="text-sm text-slate-500 mb-3">Persentase potongan harga setiap transaksi produk/kelas milik vendor atau mentor yang masuk ke pendapatan platform.</p>
                            <div class="flex items-center gap-3">
                                <input 
                                    type="number" 
                                    v-model="form.platform_fee_percent" 
                                    class="w-24 rounded-xl border-slate-200 px-4 py-2 text-center font-semibold text-slate-900 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" 
                                    min="0" 
                                    max="100" 
                                    required
                                >
                                <span class="text-lg font-bold text-slate-500">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="rounded-xl bg-[#0284c7] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-sky-600 disabled:opacity-50"
                        >
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
