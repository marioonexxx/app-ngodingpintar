<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MemberLayout from '../../Components/MemberLayout.vue';

defineProps({
    vendor: Object,
    mentor: Object,
    enableVendor: Boolean,
    enableMentor: Boolean,
});

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'pending': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'approved': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'rejected': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-slate-100 text-slate-800 border-slate-200';
    }
};

const getStatusText = (status) => {
    switch (status) {
        case 'pending': return 'Menunggu Persetujuan';
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        default: return 'Belum Mendaftar';
    }
};
</script>

<template>
    <Head title="Status Partner" />
    <MemberLayout>
        <div class="mx-auto max-w-4xl px-4 py-8">
            <h1 class="text-2xl font-black text-slate-900">Status Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Pantau status pendaftaran Anda sebagai Vendor atau Mentor.</p>

            <div class="mt-8 grid gap-6 md:grid-cols-2">
                <!-- Vendor Status -->
                <div class="rounded-2xl bg-white p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="grid size-10 place-items-center rounded-lg bg-sky-50 text-xl text-[#0284c7]">📦</div>
                            <h2 class="text-lg font-bold text-slate-900">Vendor</h2>
                        </div>
                        <span 
                            v-if="vendor" 
                            :class="['px-3 py-1 text-xs font-bold rounded-full border', getStatusBadgeClass(vendor.status)]"
                        >
                            {{ getStatusText(vendor.status) }}
                        </span>
                        <span v-else class="px-3 py-1 text-xs font-bold rounded-full border bg-slate-100 text-slate-500 border-slate-200">
                            Belum Mendaftar
                        </span>
                    </div>

                    <div v-if="vendor">
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-800">Nama Toko:</span> {{ vendor.store_name }}</p>
                            <p><span class="font-semibold text-slate-800">Tanggal Daftar:</span> {{ new Date(vendor.created_at).toLocaleDateString('id-ID') }}</p>
                        </div>

                        <div v-if="vendor.status === 'rejected' && vendor.rejection_reason" class="mt-4 rounded-xl bg-red-50 p-4 border border-red-100">
                            <p class="text-sm font-semibold text-red-800 mb-1">Alasan Penolakan:</p>
                            <p class="text-sm text-red-600">{{ vendor.rejection_reason }}</p>
                        </div>
                        
                        <div v-if="vendor.status === 'rejected'" class="mt-4">
                            <Link 
                                v-if="enableVendor"
                                href="/partner/register/vendor" 
                                class="inline-block rounded-lg bg-[#0284c7] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-sky-600"
                            >
                                Perbaiki & Ajukan Ulang
                            </Link>
                        </div>
                    </div>

                    <div v-else class="mt-6">
                        <p class="text-sm text-slate-600 mb-4">Anda belum mendaftar sebagai Vendor. Daftar sekarang untuk menjual produk digital di NgodingPintar.id.</p>
                        <Link 
                            v-if="enableVendor"
                            href="/partner/register/vendor" 
                            class="inline-block rounded-lg bg-[#0284c7] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-sky-600"
                        >
                            Daftar Vendor Sekarang
                        </Link>
                        <button v-else disabled class="inline-block rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-400 cursor-not-allowed">
                            Pendaftaran Ditutup
                        </button>
                    </div>
                </div>

                <!-- Mentor Status -->
                <div class="rounded-2xl bg-white p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="grid size-10 place-items-center rounded-lg bg-emerald-50 text-xl text-emerald-500">🎓</div>
                            <h2 class="text-lg font-bold text-slate-900">Mentor</h2>
                        </div>
                        <span 
                            v-if="mentor" 
                            :class="['px-3 py-1 text-xs font-bold rounded-full border', getStatusBadgeClass(mentor.status)]"
                        >
                            {{ getStatusText(mentor.status) }}
                        </span>
                        <span v-else class="px-3 py-1 text-xs font-bold rounded-full border bg-slate-100 text-slate-500 border-slate-200">
                            Belum Mendaftar
                        </span>
                    </div>

                    <div v-if="mentor">
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-800">Bidang Keahlian:</span> {{ mentor.expertise_area }}</p>
                            <p><span class="font-semibold text-slate-800">Tanggal Daftar:</span> {{ new Date(mentor.created_at).toLocaleDateString('id-ID') }}</p>
                        </div>

                        <div v-if="mentor.status === 'rejected' && mentor.rejection_reason" class="mt-4 rounded-xl bg-red-50 p-4 border border-red-100">
                            <p class="text-sm font-semibold text-red-800 mb-1">Alasan Penolakan:</p>
                            <p class="text-sm text-red-600">{{ mentor.rejection_reason }}</p>
                        </div>
                        
                        <div v-if="mentor.status === 'rejected'" class="mt-4">
                            <Link 
                                v-if="enableMentor"
                                href="/partner/register/mentor" 
                                class="inline-block rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-600"
                            >
                                Perbaiki & Ajukan Ulang
                            </Link>
                        </div>
                    </div>

                    <div v-else class="mt-6">
                        <p class="text-sm text-slate-600 mb-4">Anda belum mendaftar sebagai Mentor. Tawarkan jasa coaching 1-on-1 kepada komunitas.</p>
                        <Link 
                            v-if="enableMentor"
                            href="/partner/register/mentor" 
                            class="inline-block rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-600"
                        >
                            Daftar Mentor Sekarang
                        </Link>
                        <button v-else disabled class="inline-block rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-400 cursor-not-allowed">
                            Pendaftaran Ditutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
