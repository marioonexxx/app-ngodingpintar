<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ categories: Array, memberships: Array, statuses: Array });
</script>

<template>
    <AdminLayout>
        <ProductFormPage
            eyebrow="Administrator"
            title="Tambah Produk"
            subtitle="Buat produk katalog NgodingPintar.id melalui halaman form penuh agar data produk, media, file, dan panduan instalasi lebih mudah diisi."
            action="/admin/products"
            back-href="/admin/products"
            submit-label="Tambah Produk"
            :categories="categories"
            :memberships="memberships"
            :statuses="statuses"
            :is-admin="true"
        />
    </AdminLayout>
</template>
