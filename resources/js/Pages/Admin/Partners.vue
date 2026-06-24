<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Components/AdminLayout.vue';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import { successAlert } from '@/Utils/swal';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ partners: Object, filters: Object });

const searchQuery = ref(props.filters?.search || '');
const roleFilter = ref(props.filters?.role_type || '');

const selectedPartners = ref([]);

const applyFilters = () => {
    router.get('/admin/partners', {
        search: searchQuery.value,
        role_type: roleFilter.value,
    }, { preserveState: true, preserveScroll: true });
};

const allSelected = computed(() => {
    return props.partners.data.length > 0 && selectedPartners.value.length === props.partners.data.length;
});

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedPartners.value = [];
    } else {
        selectedPartners.value = props.partners.data.map(p => p.partner_profile.id);
    }
};

const bulkDeactivate = async () => {
    if (selectedPartners.value.length === 0) return;

    const result = await Swal.fire({
        title: 'Nonaktifkan Partner?',
        text: `Anda akan menonaktifkan ${selectedPartners.value.length} partner terpilih.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Nonaktifkan',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    router.post('/admin/partners/bulk-deactivate', {
        partner_profile_ids: selectedPartners.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedPartners.value = [];
            successAlert('Partner terpilih berhasil dinonaktifkan.');
        }
    });
};

const updateStatus = async (partnerProfile, type, status) => {
    let confirmText = `Ubah status ${type} menjadi ${status}?`;
    const result = await Swal.fire({
        title: 'Konfirmasi',
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0284c7',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    router.patch(`/admin/partners/${partnerProfile.id}/status`, {
        [`${type}_status`]: status
    }, {
        preserveScroll: true,
        onSuccess: () => successAlert('Status berhasil diperbarui.')
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-amber-100 text-amber-700',
        approved: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-red-100 text-red-600',
    };
    return colors[status] || 'bg-slate-100 text-slate-600';
};
</script>

<template>
    <AdminLayout>
        <section class="rounded-md bg-white p-6 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#0284c7]">Administrator</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight">Data Partner</h1>
                    <p class="mt-3 text-sm text-slate-500">Kelola akun yang mendaftar sebagai Vendor maupun Mentor.</p>
                </div>
                <span class="rounded-md bg-sky-50 px-4 py-3 text-sm font-black text-[#0284c7]">{{ partners.total || partners.data.length }} partner</span>
            </div>
        </section>

        <section class="mt-5 rounded-md bg-white p-5 shadow-sm shadow-sky-950/5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button v-if="selectedPartners.length > 0" @click="bulkDeactivate" class="rounded-md bg-red-500 px-4 py-2 text-sm font-bold text-white hover:bg-red-600">
                        Nonaktifkan Terpilih ({{ selectedPartners.length }})
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="roleFilter" @change="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Role</option>
                        <option value="vendor">Vendor Saja</option>
                        <option value="mentor">Mentor Saja</option>
                        <option value="both">Vendor & Mentor (Dual)</option>
                    </select>
                    <input v-model="searchQuery" @keyup.enter="applyFilters" class="rounded-md border border-slate-200 px-3 py-2 text-sm" placeholder="Cari nama/email...">
                    <button @click="applyFilters" class="rounded-md bg-[#0284c7] px-4 py-2 text-sm font-black text-white">Cari</button>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center rounded-tl-md">
                                <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="rounded border-slate-300 text-[#0284c7] focus:ring-[#0284c7]">
                            </th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status Vendor</th>
                            <th class="px-4 py-3">Status Mentor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="partner in partners.data" :key="partner.id" class="hover:bg-slate-50/50">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" v-model="selectedPartners" :value="partner.partner_profile.id" class="rounded border-slate-300 text-[#0284c7] focus:ring-[#0284c7]">
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-900">{{ partner.name }}</p>
                                <p class="text-xs text-slate-500">{{ partner.email }}</p>
                                <p class="mt-1 text-xs text-slate-400 font-mono">{{ partner.partner_profile?.brand_name }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex gap-1">
                                    <span v-if="partner.partner_profile?.vendor_status !== null" class="rounded bg-[#0284c7]/10 px-2 py-0.5 text-[10px] font-black tracking-wider text-[#0284c7]">VENDOR</span>
                                    <span v-if="partner.partner_profile?.mentor_status !== null" class="rounded bg-indigo-500/10 px-2 py-0.5 text-[10px] font-black tracking-wider text-indigo-700">MENTOR</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div v-if="partner.partner_profile?.vendor_status !== null" class="flex flex-col gap-2 items-start">
                                    <span :class="getStatusColor(partner.partner_profile.vendor_status)" class="rounded px-2 py-0.5 text-[10px] font-bold uppercase">{{ partner.partner_profile.vendor_status }}</span>
                                    <select 
                                        @change="updateStatus(partner.partner_profile, 'vendor', $event.target.value)"
                                        class="rounded border border-slate-200 px-2 py-1 text-xs text-slate-700 bg-white"
                                    >
                                        <option value="pending" :selected="partner.partner_profile.vendor_status === 'pending'">Pending</option>
                                        <option value="approved" :selected="partner.partner_profile.vendor_status === 'approved'">Approved</option>
                                        <option value="rejected" :selected="partner.partner_profile.vendor_status === 'rejected'">Rejected</option>
                                    </select>
                                </div>
                                <span v-else class="text-xs text-slate-400 italic">-</span>
                            </td>
                            <td class="px-4 py-4">
                                <div v-if="partner.partner_profile?.mentor_status !== null" class="flex flex-col gap-2 items-start">
                                    <span :class="getStatusColor(partner.partner_profile.mentor_status)" class="rounded px-2 py-0.5 text-[10px] font-bold uppercase">{{ partner.partner_profile.mentor_status }}</span>
                                    <select 
                                        @change="updateStatus(partner.partner_profile, 'mentor', $event.target.value)"
                                        class="rounded border border-slate-200 px-2 py-1 text-xs text-slate-700 bg-white"
                                    >
                                        <option value="pending" :selected="partner.partner_profile.mentor_status === 'pending'">Pending</option>
                                        <option value="approved" :selected="partner.partner_profile.mentor_status === 'approved'">Approved</option>
                                        <option value="rejected" :selected="partner.partner_profile.mentor_status === 'rejected'">Rejected</option>
                                    </select>
                                </div>
                                <span v-else class="text-xs text-slate-400 italic">-</span>
                            </td>
                        </tr>
                        <tr v-if="partners.data.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                Belum ada partner yang mendaftar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 border-t border-slate-100 pt-4 flex flex-wrap gap-1" v-if="partners.last_page > 1">
                <template v-for="(link, i) in partners.links" :key="i">
                    <Link v-if="link.url" :href="link.url" class="rounded px-3 py-1 text-sm border transition" :class="link.active ? 'bg-[#0284c7] text-white border-[#0284c7]' : 'text-slate-500 border-slate-200 hover:bg-slate-50'" v-html="link.label" />
                    <span v-else class="rounded px-3 py-1 text-sm border border-slate-200 text-slate-400" v-html="link.label" />
                </template>
            </div>
        </section>
    </AdminLayout>
</template>
