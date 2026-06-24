<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ product: Object, categories: Array, memberships: Array, statuses: Array });
</script>

<template>
    <AdminLayout>
        <ProductFormPage
            eyebrow="Administrator"
            title="Edit Produk Kelas"
            subtitle="Perbarui master produk kelas. Jadwal setiap pelaksanaan dikelola melalui menu Batch."
            :action="`/admin/class-products/${product.id}`"
            method="PATCH"
            back-href="/admin/class-products"
            submit-label="Simpan Perubahan"
            :product="product"
            :categories="categories"
            :memberships="memberships"
            :statuses="statuses"
            :is-admin="true"
            :is-class="true"
            :show-class-schedule="false"
        />
    </AdminLayout>
</template>
