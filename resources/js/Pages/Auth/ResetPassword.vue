<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { csrf } from '../../Components/FormHelpers';

defineProps({ email: String, token: String });
</script>

<template>
    <AppLayout>
        <form method="post" action="/reset-password" class="mx-auto max-w-md rounded-md border bg-white p-6">
            <input type="hidden" name="_token" :value="csrf()">
            <input type="hidden" name="token" :value="token">
            <h1 class="text-2xl font-semibold">Password Baru</h1>
            <label class="mt-5 block text-sm font-semibold">Email</label>
            <input name="email" type="email" required :value="email" class="mt-1 w-full rounded-md border px-3 py-2">
            <label class="mt-4 block text-sm font-semibold">Password</label>
            <input name="password" type="password" required class="mt-1 w-full rounded-md border px-3 py-2">
            <label class="mt-4 block text-sm font-semibold">Konfirmasi Password</label>
            <input name="password_confirmation" type="password" required class="mt-1 w-full rounded-md border px-3 py-2">
            <button class="mt-6 w-full rounded-md bg-teal-700 px-4 py-3 font-semibold text-white">Simpan Password</button>
        </form>
    </AppLayout>
</template>
