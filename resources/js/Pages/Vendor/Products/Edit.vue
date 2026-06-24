<script setup>
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ product: Object, categories: Array });
</script>

<template>
    <PartnerLayout>
        <ProductFormPage
            eyebrow="Vendor Area"
            title="Edit Produk"
            subtitle="Perubahan produk vendor akan dikembalikan ke status pending untuk ditinjau ulang oleh admin."
            :action="`/vendor/products/${product.id}`"
            method="PATCH"
            back-href="/vendor/products"
            submit-label="Simpan & Ajukan Ulang"
            :product="product"
            :categories="categories"
        />
    </PartnerLayout>
</template>
