<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Components/AdminLayout.vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

const props = defineProps({
    services: Object,
    filters: Object,
});

const indexUrl = '/admin/coaching-services';
const createUrl = `${indexUrl}/create`;
const editUrl = (service) => `${indexUrl}/${service.id}/edit`;
const destroyUrl = (service) => `${indexUrl}/${service.id}`;
const search = ref(props.filters.search || '');

const handleSearch = () => {
    router.get(indexUrl, { search: search.value }, { preserveState: true, replace: true });
};

const deleteService = async (service) => {
    const confirmed = await confirmAction({
        title: 'Hapus layanan?',
        text: `Layanan "${service.name}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, hapus',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.delete(destroyUrl(service), {
        preserveScroll: true,
        onSuccess: () => successAlert('Layanan coaching berhasil dihapus.'),
        onError: () => errorAlert('Layanan coaching gagal dihapus.'),
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};
</script>

<template>
    <Head title="Coaching Services" />

    <AppLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Coaching Services</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola layanan coaching 1-on-1.</p>
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black">Daftar Layanan</h2>
                    <Link :href="createUrl" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Tambah Layanan</Link>
                </div>
                <div class="flex gap-2">
                    <input v-model="search" @keyup.enter="handleSearch" class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari layanan...">
                    <button @click="handleSearch" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Layanan</th>
                            <th class="px-4 py-3">Topik</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="service in services.data" :key="service.id" class="border-t border-slate-100">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-14 w-14 bg-gray-100 rounded-md overflow-hidden">
                                        <img v-if="service.thumbnail" :src="service.thumbnail" alt="" class="h-full w-full object-cover" />
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ service.name }}</div>
                                        <div class="text-xs text-slate-500 mt-1">Mentor: {{ service.mentor ? service.mentor.name : 'No Mentor' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-slate-500">
                                {{ service.coaching_topic?.name }}
                            </td>
                            <td class="px-4 py-4">
                                <div v-if="service.sale_price" class="flex flex-col">
                                    <span class="text-xs line-through text-slate-400">{{ formatCurrency(service.price) }}</span>
                                    <span class="text-[#0284c7] font-bold">{{ formatCurrency(service.sale_price) }}</span>
                                </div>
                                <div v-else class="font-bold">
                                    {{ formatCurrency(service.price) }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded px-3 py-1 text-xs font-bold" 
                                        :class="{
                                        'bg-green-100 text-green-800': service.status === 'active',
                                        'bg-yellow-100 text-yellow-800': service.status === 'draft',
                                        'bg-gray-100 text-gray-800': service.status === 'archived'
                                        }">
                                    {{ service.status }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-4">
                                    <Link :href="editUrl(service)" class="font-bold text-[#0284c7]">Edit</Link>
                                    <button @click="deleteService(service)" class="font-bold text-red-500">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="services.data.length === 0">
                            <td colspan="5" class="px-4 py-4 text-center text-slate-500">Tidak ada layanan coaching.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AppLayout>
</template>
