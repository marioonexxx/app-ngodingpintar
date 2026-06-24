<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ product: Object, categories: Array, memberships: Array, statuses: Array });
</script>

<template>
    <AdminLayout>
        <ProductFormPage
            eyebrow="Administrator"
            title="Edit Produk"
            subtitle="Perbarui data produk, media aplikasi, file source code, dan panduan instalasi."
            :action="`/admin/products/${product.id}`"
            method="PATCH"
            back-href="/admin/products"
            submit-label="Simpan Perubahan"
            :product="product"
            :categories="categories"
            :memberships="memberships"
            :statuses="statuses"
            :is-admin="true"
        />
    </AdminLayout>
</template>
