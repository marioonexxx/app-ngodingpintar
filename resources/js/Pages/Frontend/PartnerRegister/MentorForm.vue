<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '../../../Components/AppLayout.vue';

const props = defineProps({
    existingData: Object
});

const form = useForm({
    name: props.existingData?.brand_name || '',
    bio: props.existingData?.bio || '',
    whatsapp: props.existingData?.whatsapp || '',
    profile_picture_file: null,
    linkedin_url: props.existingData?.linkedin_url || '',
    github_url: props.existingData?.github_url || '',
    website_url: props.existingData?.website_url || '',
    expertise_area: props.existingData?.expertise_area || '',
    experience_years: props.existingData?.experience_years || 0,
    certifications: props.existingData?.certifications || '',
    bank_name: props.existingData?.bank_name || '',
    bank_account_name: props.existingData?.bank_account_name || '',
    bank_account_number: props.existingData?.bank_account_number || '',
});

const submit = () => {
    form.post('/partner/register/mentor');
};

const handleFileChange = (e) => {
    form.profile_picture_file = e.target.files[0];
};
</script>

<template>
    <Head>
        <title>Pendaftaran Mentor</title>
        <meta name="description" content="Formulir pendaftaran untuk menawarkan jasa coaching di NgodingPintar.id." />
    </Head>
    <AppLayout>
        <div class="bg-slate-50 py-16 md:py-24">
            <div class="mx-auto max-w-3xl px-5">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900">Formulir Pendaftaran Mentor</h1>
                    <p class="mt-2 text-slate-600">Lengkapi data di bawah ini untuk menawarkan jasa coaching di NgodingPintar.id.</p>
                </div>

                <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Profil Mentor -->
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Profil Mentor</h2>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input v-model="form.name" type="text" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Foto Profil</label>
                                    <input type="file" @change="handleFileChange" accept="image/png, image/jpeg, image/webp" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                    <div v-if="form.errors.profile_picture_file" class="mt-1 text-sm text-red-500">{{ form.errors.profile_picture_file }}</div>
                                    <p class="mt-1 text-xs text-slate-500">Akan digunakan di halaman profil mentor. Opsional.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Bio Singkat</label>
                                    <textarea v-model="form.bio" rows="3" placeholder="Ceritakan latar belakang profesional Anda..." class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20"></textarea>
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
                                    <input v-model="form.expertise_area" type="text" placeholder="Frontend, Backend, UI/UX..." class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.expertise_area" class="mt-1 text-sm text-red-500">{{ form.errors.expertise_area }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Pengalaman (Tahun) <span class="text-red-500">*</span></label>
                                    <input v-model="form.experience_years" type="number" min="0" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                    <div v-if="form.errors.experience_years" class="mt-1 text-sm text-red-500">{{ form.errors.experience_years }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Sertifikasi & Penghargaan (Opsional)</label>
                                    <textarea v-model="form.certifications" rows="2" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20"></textarea>
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
                                    <input v-model="form.whatsapp" type="text" placeholder="0812..." class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.whatsapp" class="mt-1 text-sm text-red-500">{{ form.errors.whatsapp }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">URL LinkedIn</label>
                                    <input v-model="form.linkedin_url" type="url" placeholder="https://linkedin.com/in/..." class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.linkedin_url" class="mt-1 text-sm text-red-500">{{ form.errors.linkedin_url }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">URL GitHub</label>
                                    <input v-model="form.github_url" type="url" placeholder="https://github.com/..." class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.github_url" class="mt-1 text-sm text-red-500">{{ form.errors.github_url }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">URL Website Personal</label>
                                    <input v-model="form.website_url" type="url" placeholder="https://" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.website_url" class="mt-1 text-sm text-red-500">{{ form.errors.website_url }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembayaran -->
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4 mt-8">Informasi Pencairan Dana</h2>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Bank / E-Wallet</label>
                                    <input v-model="form.bank_name" type="text" placeholder="BCA / Mandiri / Dana" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.bank_name" class="mt-1 text-sm text-red-500">{{ form.errors.bank_name }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nomor Rekening / HP</label>
                                    <input v-model="form.bank_account_number" type="text" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.bank_account_number" class="mt-1 text-sm text-red-500">{{ form.errors.bank_account_number }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Pemilik Rekening</label>
                                    <input v-model="form.bank_account_name" type="text" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.bank_account_name" class="mt-1 text-sm text-red-500">{{ form.errors.bank_account_name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100">
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="w-full rounded-xl bg-emerald-500 px-6 py-4 text-center text-sm font-bold text-white transition hover:bg-emerald-600 focus:ring focus:ring-emerald-500/30 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Mengirim Pendaftaran...</span>
                                <span v-else>Kirim Pendaftaran Mentor</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
