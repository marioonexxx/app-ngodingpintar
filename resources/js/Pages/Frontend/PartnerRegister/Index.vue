<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '../../../Components/AppLayout.vue';

const props = defineProps({ enableVendor: Boolean, enableMentor: Boolean });
const page = usePage();
</script>

<template>
    <Head>
        <title>Bergabung dengan NgodingPintar</title>
        <meta name="description" content="Bergabung dengan NgodingPintar sebagai Vendor atau Mentor. Bagikan karya dan keahlian Anda." />
    </Head>
    <AppLayout>
        <div class="bg-slate-50 py-16 md:py-24">
            <div class="mx-auto max-w-4xl px-5 text-center">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#0284c7]">Menjadi Partner</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 md:text-5xl">Bergabung dengan NgodingPintar</h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600 leading-relaxed">
                    Bagikan karya dan keahlian Anda ke ribuan developer di seluruh Indonesia. Mulai hasilkan pendapatan tambahan dengan menjadi vendor atau mentor di NgodingPintar.id.
                </p>
                
                <div v-if="page.props.flash?.error" class="mt-8 mx-auto max-w-2xl rounded-xl bg-red-50 p-4 border border-red-100 text-sm font-medium text-red-600">
                    {{ page.props.flash.error }}
                </div>
            </div>

            <div class="mx-auto mt-16 grid max-w-5xl gap-8 px-5 md:grid-cols-2">
                <!-- Vendor Card -->
                <div 
                    class="relative flex flex-col rounded-2xl bg-white p-8 border border-slate-100 shadow-sm transition-all"
                    :class="enableVendor ? 'hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-950/5 hover:border-slate-200' : 'opacity-60'"
                >
                    <div class="flex items-start justify-between">
                        <div class="grid size-14 place-items-center rounded-xl bg-sky-50 text-2xl text-[#0284c7]">
                            📦
                        </div>
                        <span v-if="!enableVendor" class="rounded-full bg-red-50 px-3 py-1 text-[10px] font-bold tracking-wide text-red-600 border border-red-100">
                            PENDAFTARAN DITUTUP
                        </span>
                    </div>
                    
                    <h2 class="mt-6 text-2xl font-black text-slate-900">Daftar sebagai Vendor</h2>
                    <p class="mt-4 text-slate-600 leading-relaxed flex-1">
                        Jual produk digital, source code, template, dan aplikasi web di NgodingPintar.id. Dapatkan akses langsung ke ribuan pembeli potensial.
                    </p>
                    
                    <ul class="mt-6 space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="text-[#0284c7]">✓</span> Upload produk unlimited
                        </li>
                        <li class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="text-[#0284c7]">✓</span> Terima custom requests
                        </li>
                        <li class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="text-[#0284c7]">✓</span> Dashboard analitik penjualan
                        </li>
                    </ul>
                    
                    <Link 
                        v-if="enableVendor" 
                        href="/partner/register/vendor" 
                        class="mt-auto block w-full rounded-xl bg-slate-50 px-6 py-4 text-center text-sm font-bold text-slate-700 transition-colors hover:bg-[#0284c7] hover:text-white"
                    >
                        Daftar Vendor
                    </Link>
                    <button 
                        v-else 
                        disabled 
                        class="mt-auto block w-full rounded-xl bg-slate-100 px-6 py-4 text-center text-sm font-bold text-slate-400 cursor-not-allowed"
                    >
                        Pendaftaran Ditutup
                    </button>
                </div>

                <!-- Mentor Card -->
                <div 
                    class="relative flex flex-col rounded-2xl bg-white p-8 border border-slate-100 shadow-sm transition-all"
                    :class="enableMentor ? 'hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-950/5 hover:border-slate-200' : 'opacity-60'"
                >
                    <div class="flex items-start justify-between">
                        <div class="grid size-14 place-items-center rounded-xl bg-emerald-50 text-2xl text-emerald-500">
                            🎓
                        </div>
                        <span v-if="!enableMentor" class="rounded-full bg-red-50 px-3 py-1 text-[10px] font-bold tracking-wide text-red-600 border border-red-100">
                            PENDAFTARAN DITUTUP
                        </span>
                    </div>
                    
                    <h2 class="mt-6 text-2xl font-black text-slate-900">Daftar sebagai Mentor</h2>
                    <p class="mt-4 text-slate-600 leading-relaxed flex-1">
                        Tawarkan jasa coaching 1-on-1, konsultasi teknis, dan mentoring sesuai bidang keahlian Anda. Bantu developer lain berkembang.
                    </p>
                    
                    <ul class="mt-6 space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="text-emerald-500">✓</span> Atur jadwal & tarif fleksibel
                        </li>
                        <li class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="text-emerald-500">✓</span> Manajemen sesi via Google Meet / Zoom
                        </li>
                        <li class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="text-emerald-500">✓</span> Bangun reputasi profesional
                        </li>
                    </ul>
                    
                    <Link 
                        v-if="enableMentor" 
                        href="/partner/register/mentor" 
                        class="mt-auto block w-full rounded-xl bg-slate-50 px-6 py-4 text-center text-sm font-bold text-slate-700 transition-colors hover:bg-emerald-500 hover:text-white"
                    >
                        Daftar Mentor
                    </Link>
                    <button 
                        v-else 
                        disabled 
                        class="mt-auto block w-full rounded-xl bg-slate-100 px-6 py-4 text-center text-sm font-bold text-slate-400 cursor-not-allowed"
                    >
                        Pendaftaran Ditutup
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
