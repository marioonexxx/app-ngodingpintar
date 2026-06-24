<script setup>
import MemberLayout from '@/Components/MemberLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';





const props = defineProps({
    transaction: Object,
});

const form = useForm({
    bank_name: '',
    account_name: '',
    account_number: '',
});

const submit = () => {
    form.post(route('member.transactions.refund.store', props.transaction.id));
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
    <Head title="Pengajuan Refund" />

    <MemberLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Pengajuan Refund Dana</h1>
            <Link :href="route('member.transactions.history')" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Batal
            </Link>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 max-w-3xl">
            <h3 class="text-blue-800 font-medium mb-1">Informasi Refund:</h3>
            <p class="text-sm text-blue-600">
                Anda berhak mendapatkan pengembalian dana penuh sebesar <strong>{{ formatPrice(transaction.total) }}</strong> atas transaksi <strong>{{ transaction.invoice_number }}</strong>.
                Silakan isi data rekening bank Anda di bawah ini dengan benar. Admin akan memproses pengembalian dana ke rekening tersebut.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6 max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="bank_name" value="Nama Bank" />
                    <TextInput
                        id="bank_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.bank_name"
                        required
                        placeholder="Contoh: BCA / Mandiri / BNI / BRI"
                    />
                    <InputError class="mt-2" :message="form.errors.bank_name" />
                </div>

                <div>
                    <InputLabel for="account_number" value="Nomor Rekening" />
                    <TextInput
                        id="account_number"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.account_number"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.account_number" />
                </div>

                <div>
                    <InputLabel for="account_name" value="Nama Pemilik Rekening" />
                    <TextInput
                        id="account_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.account_name"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.account_name" />
                </div>

                <div class="flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Kirim Pengajuan Refund
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </MemberLayout>
</template>
