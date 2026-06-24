<script setup>
import PartnerLayout from '../../Components/PartnerLayout.vue';
import { csrf, money } from '../../Components/FormHelpers';
import { ref } from 'vue';

defineProps({
    requests: Object,
    statuses: Array,
    currentStatus: String,
});

const showModal = ref(false);
const selected = ref(null);

const openDetail = (req) => {
    selected.value = { ...req };
    showModal.value = true;
};

const statusBadge = (status) => {
    const map = {
        pending_review: 'bg-amber-50 text-amber-700 border-amber-200',
        quoted: 'bg-sky-50 text-sky-700 border-sky-200',
        approved: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        rejected: 'bg-red-50 text-red-700 border-red-200',
        completed: 'bg-teal-50 text-teal-700 border-teal-200',
    };
    return map[status] || 'bg-slate-100 text-slate-600';
};

const statusLabel = (status) => {
    const map = {
        pending_review: 'Pending Review',
        quoted: 'Quoted',
        approved: 'Approved',
        rejected: 'Rejected',
        completed: 'Completed',
    };
    return map[status] || status;
};
</script>

<template>
    <PartnerLayout>
        <!-- Header -->
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm shadow-slate-100/50">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Vendor Panel</p>
                    <h1 class="mt-3 text-2xl font-black tracking-tight">Custom Feature Requests</h1>
                    <p class="mt-2 text-sm text-slate-500">Request fitur custom dari customer untuk produk Anda.</p>
                </div>
                <span class="rounded-xl bg-sky-50 px-4 py-2.5 text-sm font-black text-[#0284c7]">{{ requests.total || requests.data.length }} request</span>
            </div>
        </section>

        <!-- Filter -->
        <section class="mt-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm shadow-slate-100/50">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-black">Daftar Request</h2>
                <form method="get" action="/vendor/custom-feature-requests" class="flex gap-2">
                    <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Status</option>
                        <option v-for="s in statuses" :key="s" :value="s" :selected="currentStatus === s">{{ statusLabel(s) }}</option>
                    </select>
                    <button class="rounded-xl bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Filter</button>
                </form>
            </div>

            <!-- Table -->
            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="req in requests.data" :key="req.id" class="border-t border-slate-100">
                            <td class="px-4 py-4 font-bold text-slate-500">#{{ req.id }}</td>
                            <td class="px-4 py-4">
                                <p class="font-bold">{{ req.user?.name }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ req.product?.name || '-' }}</td>
                            <td class="max-w-[200px] px-4 py-4">
                                <p class="truncate text-sm text-slate-600">{{ req.description }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider" :class="statusBadge(req.status)">
                                    {{ statusLabel(req.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-500">{{ new Date(req.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</td>
                            <td class="px-4 py-4 text-right">
                                <button type="button" class="font-bold text-[#0284c7]" @click="openDetail(req)">Detail</button>
                            </td>
                        </tr>
                        <tr v-if="requests.data.length === 0">
                            <td colspan="7" class="px-4 py-10 text-center text-sm font-semibold text-slate-400">Belum ada request untuk produk Anda.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Detail Modal -->
        <div v-if="showModal && selected" class="fixed inset-0 z-50 grid place-items-start bg-slate-950/45 px-4 py-16 backdrop-blur-sm" @click.self="showModal = false">
            <div class="mx-auto w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-2xl shadow-slate-950/20">
                <!-- Header -->
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="text-xl font-black">Detail Request</h2>
                        <p class="mt-1 text-sm text-slate-500">#{{ selected.id }}</p>
                    </div>
                    <button type="button" class="grid size-9 place-items-center rounded-md bg-slate-100 text-xl hover:bg-slate-200 transition-colors" @click="showModal = false">×</button>
                </div>

                <!-- Body -->
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5 space-y-5">
                    <!-- Info -->
                    <div class="rounded-lg border border-slate-100 bg-slate-50/50 p-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs font-bold uppercase text-slate-400">Customer</p>
                                <p class="mt-1 font-bold">{{ selected.user?.name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <span class="mt-1 inline-flex rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider" :class="statusBadge(selected.status)">
                                    {{ statusLabel(selected.status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Deskripsi Request</p>
                        <div class="mt-2 rounded-lg border border-slate-100 bg-white p-4 text-sm leading-relaxed text-slate-700">
                            {{ selected.description }}
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div v-if="selected.admin_notes">
                        <p class="text-xs font-bold uppercase text-slate-400">Catatan Admin</p>
                        <div class="mt-2 rounded-lg border border-sky-100 bg-sky-50/50 p-4 text-sm leading-relaxed text-slate-700">
                            {{ selected.admin_notes }}
                        </div>
                    </div>

                    <!-- Vendor Notes Form -->
                    <form :action="`/vendor/custom-feature-requests/${selected.id}/notes`" method="post">
                        <input type="hidden" name="_token" :value="csrf()">
                        <input type="hidden" name="_method" value="PATCH">

                        <label class="block text-sm font-bold">
                            Catatan Vendor
                            <textarea name="vendor_notes" rows="4" class="mt-2 w-full resize-none rounded-lg border border-slate-200 px-4 py-3 text-sm placeholder-slate-400 focus:border-[#0284c7] focus:outline-none focus:ring-2 focus:ring-sky-500/20" placeholder="Tambahkan catatan atau estimasi Anda...">{{ selected.vendor_notes }}</textarea>
                        </label>

                        <p class="mt-2 text-xs text-slate-400">
                            Catatan ini akan dilihat oleh admin. Anda tidak dapat mengubah status atau membuat invoice langsung.
                        </p>

                        <div class="mt-4 flex justify-end gap-3 border-t border-slate-100 pt-4">
                            <button type="button" class="rounded-md border border-slate-200 px-5 py-2.5 text-sm font-black" @click="showModal = false">Batal</button>
                            <button type="submit" class="rounded-md bg-[#0284c7] px-5 py-2.5 text-sm font-black text-white">Simpan Catatan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </PartnerLayout>
</template>
