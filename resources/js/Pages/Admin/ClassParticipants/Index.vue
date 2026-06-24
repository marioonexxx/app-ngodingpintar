<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../../Components/AdminLayout.vue';

defineProps({
    classes: Object,
});
</script>

<template>
    <Head title="Peserta Kelas" />
    <AdminLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Peserta Kelas</h1>
            <p class="mt-1 text-sm text-slate-500">Pilih kelas untuk melihat daftar peserta yang sudah melunasi pembayaran.</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div v-for="cls in classes.data" :key="cls.id" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
                <div class="aspect-video w-full bg-slate-100">
                    <img :src="cls.thumbnail ? '/storage/' + cls.thumbnail : '/img/thumbnail/default-thumbnail.png'" class="h-full w-full object-cover">
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-bold text-slate-900 line-clamp-2">{{ cls.name }}</h3>
                    <div class="mt-auto pt-4">
                        <Link :href="`/admin/class-participants/${cls.id}`" class="block w-full rounded-md bg-[#0284c7] px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-sky-700">
                            Lihat Peserta
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!classes.data || classes.data.length === 0" class="rounded-xl border border-dashed border-slate-300 py-12 text-center text-slate-500">
            Belum ada produk kelas.
        </div>
    </AdminLayout>
</template>
