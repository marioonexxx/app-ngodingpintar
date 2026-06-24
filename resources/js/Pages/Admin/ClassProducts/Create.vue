<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ categories: Array, memberships: Array, statuses: Array });
</script>

<template>
    <AdminLayout>
        <ProductFormPage
            eyebrow="Administrator"
            title="Tambah Produk Kelas"
            subtitle="Buat produk katalog NgodingPintar.id khusus kelas dan tentukan jadwal pertemuannya."
            action="/admin/class-products"
            back-href="/admin/class-products"
            submit-label="Tambah Kelas"
            :categories="categories"
            :memberships="memberships"
            :statuses="statuses"
            :is-admin="true"
            :is-class="true"
        />
    </AdminLayout>
</template>
