<script setup>
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ categories: Array });
</script>

<template>
    <PartnerLayout>
        <ProductFormPage
            eyebrow="Vendor Area"
            title="Tambah Produk Baru"
            subtitle="Lengkapi data produk digital Anda. Produk akan masuk status pending untuk ditinjau admin."
            action="/vendor/products"
            back-href="/vendor/products"
            submit-label="Tambah & Ajukan"
            :categories="categories"
        />
    </PartnerLayout>
</template>
