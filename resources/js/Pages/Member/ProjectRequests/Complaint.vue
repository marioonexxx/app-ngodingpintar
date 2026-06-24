<script setup>
import MemberLayout from '@/Components/MemberLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';




const props = defineProps({
    projectRequest: Object,
});

const form = useForm({
    member_reason: '',
    member_proof: null,
});

const submit = () => {
    form.post(route('member.project-requests.complaint.store', props.projectRequest.id));
};
</script>

<template>
    <Head title="Ajukan Komplain Project" />

    <MemberLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Ajukan Komplain Project</h1>
            <Link :href="route('member.project-requests.show', projectRequest.id)" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Batal
            </Link>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6 max-w-3xl">
            <h3 class="text-red-800 font-medium mb-1">Informasi Komplain:</h3>
            <p class="text-sm text-red-600">
                Jika project melewati batas waktu deadline yang ditentukan atau hasil kerja vendor sangat tidak sesuai dengan kesepakatan, Anda dapat mengajukan komplain. Komplain akan ditinjau oleh Admin. Jika disetujui, dana akan dikembalikan 100% ke akun Anda. Anda hanya dapat mengirimkan alasan dan bukti sebanyak 1 kali.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6 max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="member_reason" value="Alasan Komplain" />
                    <textarea
                        id="member_reason"
                        v-model="form.member_reason"
                        rows="6"
                        class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm"
                        required
                        placeholder="Jelaskan secara detail alasan komplain Anda..."
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.member_reason" />
                </div>

                <div>
                    <InputLabel for="member_proof" value="Upload Bukti (Opsional, Max 2MB, Format Gambar)" />
                    <input
                        id="member_proof"
                        type="file"
                        @input="form.member_proof = $event.target.files[0]"
                        class="mt-1 block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-md file:border-0
                               file:text-sm file:font-semibold
                               file:bg-red-50 file:text-red-700
                               hover:file:bg-red-100"
                        accept="image/*"
                    />
                    <InputError class="mt-2" :message="form.errors.member_proof" />
                </div>

                <div class="flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-red-600 hover:bg-red-700 focus:ring-red-500">
                        Kirim Komplain
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </MemberLayout>
</template>
