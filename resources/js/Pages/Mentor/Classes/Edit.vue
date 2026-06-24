<script setup>
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ product: Object, categories: Array, memberships: Array, statuses: Array });
</script>

<template>
    <PartnerLayout>
        <ProductFormPage
            eyebrow="Administrator"
            title="Edit Produk Kelas"
            subtitle="Perbarui master produk kelas. Jadwal setiap pelaksanaan dikelola melalui menu Batch."
            :action="`/mentor/classes/${product.id}`"
            method="PATCH"
            back-href="/mentor/classes"
            submit-label="Simpan Perubahan"
            :product="product"
            :categories="categories"
            :memberships="memberships"
            :statuses="statuses"
            :is-admin="true"
            :is-class="true"
            :show-class-schedule="false"
        />
    </PartnerLayout>
</template>
