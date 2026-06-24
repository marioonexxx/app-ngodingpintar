<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '../../../Components/AppLayout.vue';

const props = defineProps({
    existingData: Object
});

const form = useForm({
    store_name: props.existingData?.brand_name || '',
    developer_description: props.existingData?.bio || '',
    whatsapp: props.existingData?.whatsapp || '',
    github_url: props.existingData?.github_url || '',
    website_url: props.existingData?.website_url || '',
    portfolio_url: props.existingData?.portfolio_url || '',
    bank_name: props.existingData?.bank_name || '',
    bank_account_name: props.existingData?.bank_account_name || '',
    bank_account_number: props.existingData?.bank_account_number || '',
});

const submit = () => {
    form.post('/partner/register/vendor');
};
</script>

<template>
    <Head>
        <title>Pendaftaran Vendor</title>
        <meta name="description" content="Formulir pendaftaran untuk menjadi Vendor di NgodingPintar.id." />
    </Head>
    <AppLayout>
        <div class="bg-slate-50 py-16 md:py-24">
            <div class="mx-auto max-w-3xl px-5">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900">Formulir Pendaftaran Vendor</h1>
                    <p class="mt-2 text-slate-600">Lengkapi data di bawah ini untuk bergabung menjadi Vendor di NgodingPintar.id.</p>
                </div>

                <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Profil Vendor -->
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Profil Vendor</h2>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Toko / Developer <span class="text-red-500">*</span></label>
                                    <input v-model="form.store_name" type="text" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20" required>
                                    <div v-if="form.errors.store_name" class="mt-1 text-sm text-red-500">{{ form.errors.store_name }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">Deskripsi Singkat</label>
                                    <textarea v-model="form.developer_description" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20"></textarea>
                                    <div v-if="form.errors.developer_description" class="mt-1 text-sm text-red-500">{{ form.errors.developer_description }}</div>
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
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">URL Website</label>
                                    <input v-model="form.website_url" type="url" placeholder="https://" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.website_url" class="mt-1 text-sm text-red-500">{{ form.errors.website_url }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">URL GitHub</label>
                                    <input v-model="form.github_url" type="url" placeholder="https://github.com/..." class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.github_url" class="mt-1 text-sm text-red-500">{{ form.errors.github_url }}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-700">URL Portfolio</label>
                                    <input v-model="form.portfolio_url" type="url" placeholder="https://" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#0284c7] focus:ring focus:ring-[#0284c7]/20">
                                    <div v-if="form.errors.portfolio_url" class="mt-1 text-sm text-red-500">{{ form.errors.portfolio_url }}</div>
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
                                class="w-full rounded-xl bg-[#0284c7] px-6 py-4 text-center text-sm font-bold text-white transition hover:bg-sky-600 focus:ring focus:ring-sky-500/30 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Mengirim Pendaftaran...</span>
                                <span v-else>Kirim Pendaftaran Vendor</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
