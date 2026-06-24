<script setup>
import PartnerLayout from '../../../Components/PartnerLayout.vue';
import ProductFormPage from '../../../Components/ProductFormPage.vue';

defineProps({ categories: Array, memberships: Array, statuses: Array });
</script>

<template>
    <PartnerLayout>
        <ProductFormPage
            eyebrow="Administrator"
            title="Tambah Produk Kelas"
            subtitle="Buat produk katalog NgodingPintar.id khusus kelas dan tentukan jadwal pertemuannya."
            action="/mentor/classes"
            back-href="/mentor/classes"
            submit-label="Tambah Kelas"
            :categories="categories"
            :memberships="memberships"
            :statuses="statuses"
            :is-admin="true"
            :is-class="true"
        />
    </PartnerLayout>
</template>
