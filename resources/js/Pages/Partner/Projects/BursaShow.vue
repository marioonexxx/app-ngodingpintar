<script setup>
import PartnerLayout from '@/Components/PartnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';




const props = defineProps({
    project: Object,
    hasApplied: Boolean,
});

const form = useForm({
    cover_letter: '',
});

const submit = () => {
    form.post(route('partner.project-bursa.apply', props.project.id));
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
    <Head :title="project.title" />

    <PartnerLayout>
        <div class="px-5 py-8 md:px-10 md:py-12">
            <div class="mb-6 flex items-center justify-between">
                <Link :href="route('partner.project-bursa.index')" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Bursa
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-sky-50 text-sky-600">
                                {{ project.category === 'new_app' ? 'Aplikasi Baru' : 'Revisi' }}
                            </span>
                            <span class="text-xs font-medium text-slate-500">
                                Diposting {{ new Date(project.created_at).toLocaleDateString('id-ID') }}
                            </span>
                        </div>
                        
                        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ project.title }}</h1>
                        
                        <div class="prose prose-sm prose-slate max-w-none">
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-3 border-b pb-2">Deskripsi Kebutuhan</h3>
                            <p class="whitespace-pre-wrap text-slate-600 leading-relaxed">{{ project.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info & Apply Form -->
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4 border-b pb-2">Informasi Pekerjaan</h3>
                        
                        <dl class="space-y-4 mb-6">
                            <div>
                                <dt class="text-xs font-medium text-slate-500">Budget Member (Fix)</dt>
                                <dd class="text-xl font-bold text-sky-600 mt-1">{{ formatPrice(project.budget) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-slate-500">Target Waktu Pengerjaan</dt>
                                <dd class="text-sm font-bold text-slate-800 mt-1">{{ new Date(project.deadline_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-slate-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Open (Menerima Penawaran)
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <div v-if="hasApplied" class="p-4 bg-emerald-50 rounded-lg border border-emerald-100 flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div>
                                <p class="text-sm font-bold text-emerald-800">Anda sudah mengajukan diri</p>
                                <p class="text-xs text-emerald-600 mt-1">Menunggu admin untuk mereview dan memilih vendor. Notifikasi akan dikirim jika Anda terpilih.</p>
                            </div>
                        </div>

                        <form v-else @submit.prevent="submit" class="mt-6 pt-6 border-t border-slate-100 space-y-4">
                            <div>
                                <InputLabel for="cover_letter" value="Pesan / Penawaran Anda" class="text-slate-700" />
                                <p class="text-[10px] text-slate-500 mb-2">Jelaskan mengapa Anda cocok untuk mengerjakan project ini. Anda dapat menyertakan link portofolio yang relevan.</p>
                                <textarea
                                    id="cover_letter"
                                    v-model="form.cover_letter"
                                    rows="4"
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm"
                                    required
                                    placeholder="Halo, saya berpengalaman membuat aplikasi serupa..."
                                ></textarea>
                                <InputError class="mt-1" :message="form.errors.cover_letter" />
                            </div>

                            <PrimaryButton class="w-full justify-center py-3 bg-slate-900 hover:bg-slate-800" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                Ajukan Diri Sekarang
                            </PrimaryButton>
                            <p class="text-center text-[10px] text-slate-400 mt-3">Dengan mengajukan diri, Anda menyetujui budget dan deadline yang ditetapkan.</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </PartnerLayout>
</template>
