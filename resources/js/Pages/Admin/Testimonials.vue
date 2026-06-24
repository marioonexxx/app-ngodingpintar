<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { confirmAction, errorAlert, successAlert } from '@/Utils/swal';

defineProps({ testimonials: Object });

const approveTestimonial = async (item) => {
    if (!item?.approve_url) {
        errorAlert('URL approval testimonial tidak ditemukan. Silakan muat ulang halaman.');
        return;
    }

    const confirmed = await confirmAction({
        title: 'Approve testimonial?',
        text: `Testimonial "${item.title || 'Tanpa judul'}" dari ${item.user?.name || 'Member'} akan ditampilkan.`,
        confirmButtonText: 'Ya, approve',
    });

    if (!confirmed) return;

    router.patch(item.approve_url, {}, {
        preserveScroll: true,
        onSuccess: () => successAlert('Testimonial berhasil disetujui.'),
        onError: () => errorAlert('Testimonial gagal disetujui.'),
    });
};

const rejectTestimonial = async (item) => {
    if (!item?.reject_url) {
        errorAlert('URL penolakan testimonial tidak ditemukan. Silakan muat ulang halaman.');
        return;
    }

    const confirmed = await confirmAction({
        title: 'Reject testimonial?',
        text: `Testimonial "${item.title || 'Tanpa judul'}" dari ${item.user?.name || 'Member'} akan ditolak.`,
        confirmButtonText: 'Ya, reject',
        icon: 'warning',
        confirmButtonColor: '#ef4444',
    });

    if (!confirmed) return;

    router.patch(item.reject_url, {}, {
        preserveScroll: true,
        onSuccess: () => successAlert('Testimonial berhasil ditolak.'),
        onError: () => errorAlert('Testimonial gagal ditolak.'),
    });
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Approval Testimonial</h1>
                    <p class="mt-3 text-sm text-slate-500">Review testimonial member sebelum ditampilkan di homepage.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ testimonials.total || testimonials.data.length }} testimonial</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-black">Daftar Testimonial</h2>
                <div class="grid gap-2 md:grid-cols-[170px_220px_80px]">
                    <select class="rounded-md border border-slate-200 px-3 py-2 text-sm"><option>Semua Status</option><option>pending</option><option>approved</option><option>rejected</option></select>
                    <input class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari member/produk...">
                    <button class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Testimonial</th>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Rating</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in testimonials.data" :key="item.id" class="border-t border-slate-100 align-top">
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ item.title || 'Tanpa judul' }}</p>
                                <p class="mt-2 max-w-lg text-sm leading-6 text-slate-500">{{ item.content }}</p>
                            </td>
                            <td class="px-4 py-4">{{ item.user?.name }}</td>
                            <td class="px-4 py-4">{{ item.product?.name || '-' }}</td>
                            <td class="px-4 py-4">{{ item.rating }}/5</td>
                            <td class="px-4 py-4"><span class="rounded bg-sky-50 px-3 py-1 text-xs font-bold text-[#0284c7]">{{ item.status }}</span></td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600 hover:bg-emerald-100" @click="approveTestimonial(item)">Approve</button>
                                    <button type="button" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100" @click="rejectTestimonial(item)">Reject</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
