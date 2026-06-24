<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PartnerLayout from '../../Components/PartnerLayout.vue';

const props = defineProps({
    partner: Object,
});

const form = useForm({
    brand_name: props.partner?.brand_name || '',
    bio: props.partner?.bio || '',
    whatsapp: props.partner?.whatsapp || '',
    linkedin_url: props.partner?.linkedin_url || '',
    github_url: props.partner?.github_url || '',
    website_url: props.partner?.website_url || '',
    portfolio_url: props.partner?.portfolio_url || '',
    expertise_area: props.partner?.expertise_area || '',
    experience_years: props.partner?.experience_years || 0,
    certifications: props.partner?.certifications || '',
    bank_name: props.partner?.bank_name || '',
    bank_account_name: props.partner?.bank_account_name || '',
    bank_account_number: props.partner?.bank_account_number || '',
    profile_picture_file: null,
});

const submit = () => {
    form.post('/partner/profile', {
        preserveScroll: true,
    });
};

const handleFileChange = (e) => {
    form.profile_picture_file = e.target.files[0];
};
</script>

<template>
    <Head>
        <title>Profil & Keuangan Partner</title>
        <meta name="description" content="Kelola profil partner dan rekening pencairan." />
    </Head>
    <PartnerLayout>
        <div class="w-full">
            <div class="mb-8">
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Profil Partner</h1>
                <p class="mt-2 text-slate-600">Kelola identitas, portofolio, dan rekening pencairan saldo Anda di sini.</p>
            </div>

            <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Profil Dasar -->
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Profil Dasar</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Nama / Brand <span class="text-red-500">*</span></label>
                                <input v-model="form.brand_name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                <div v-if="form.errors.brand_name" class="mt-1 text-sm text-red-500">{{ form.errors.brand_name }}</div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Foto Profil</label>
                                <div class="flex items-center gap-4">
                                    <img v-if="partner?.profile_picture" :src="'/' + partner.profile_picture" class="size-16 rounded-full object-cover border border-slate-200">
                                    <div class="flex-1">
                                        <input type="file" @change="handleFileChange" accept="image/png, image/jpeg, image/webp" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                    </div>
                                </div>
                                <div v-if="form.errors.profile_picture_file" class="mt-1 text-sm text-red-500">{{ form.errors.profile_picture_file }}</div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Bio Singkat</label>
                                <textarea v-model="form.bio" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20"></textarea>
                                <div v-if="form.errors.bio" class="mt-1 text-sm text-red-500">{{ form.errors.bio }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Keahlian & Pengalaman -->
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4 mt-8">Keahlian & Pengalaman</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Bidang Keahlian</label>
                                <input v-model="form.expertise_area" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                <div v-if="form.errors.expertise_area" class="mt-1 text-sm text-red-500">{{ form.errors.expertise_area }}</div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Pengalaman (Tahun)</label>
                                <input v-model="form.experience_years" type="number" min="0" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                <div v-if="form.errors.experience_years" class="mt-1 text-sm text-red-500">{{ form.errors.experience_years }}</div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Sertifikasi & Penghargaan</label>
                                <textarea v-model="form.certifications" rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20"></textarea>
                                <div v-if="form.errors.certifications" class="mt-1 text-sm text-red-500">{{ form.errors.certifications }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak & Tautan -->
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4 mt-8">Kontak & Tautan</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Nomor WhatsApp</label>
                                <input v-model="form.whatsapp" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                <div v-if="form.errors.whatsapp" class="mt-1 text-sm text-red-500">{{ form.errors.whatsapp }}</div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">URL LinkedIn</label>
                                <input v-model="form.linkedin_url" type="url" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                <div v-if="form.errors.linkedin_url" class="mt-1 text-sm text-red-500">{{ form.errors.linkedin_url }}</div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">URL GitHub</label>
                                <input v-model="form.github_url" type="url" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                <div v-if="form.errors.github_url" class="mt-1 text-sm text-red-500">{{ form.errors.github_url }}</div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">URL Website Personal / Portfolio</label>
                                <input v-model="form.website_url" type="url" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                <div v-if="form.errors.website_url" class="mt-1 text-sm text-red-500">{{ form.errors.website_url }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4 mt-8">Informasi Rekening Pencairan Dana</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Bank / E-Wallet <span class="text-red-500">*</span></label>
                                <input v-model="form.bank_name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                <div v-if="form.errors.bank_name" class="mt-1 text-sm text-red-500">{{ form.errors.bank_name }}</div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Nomor Rekening / HP <span class="text-red-500">*</span></label>
                                <input v-model="form.bank_account_number" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                <div v-if="form.errors.bank_account_number" class="mt-1 text-sm text-red-500">{{ form.errors.bank_account_number }}</div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                                <input v-model="form.bank_account_name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                <div v-if="form.errors.bank_account_name" class="mt-1 text-sm text-red-500">{{ form.errors.bank_account_name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="rounded-xl bg-[#0284c7] px-6 py-3 text-sm font-bold text-white transition hover:bg-sky-600 focus:ring focus:ring-sky-500/30 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Profil</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PartnerLayout>
</template>
