<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { csrf } from '../../Components/FormHelpers';
</script>

<template>
    <AppLayout>
        <form method="post" action="/forgot-password" class="mx-auto max-w-md rounded-md border bg-white p-6">
            <input type="hidden" name="_token" :value="csrf()">
            <h1 class="text-2xl font-semibold">Reset Password</h1>
            <label class="mt-5 block text-sm font-semibold">Email</label>
            <input name="email" type="email" required class="mt-1 w-full rounded-md border px-3 py-2">
            <button class="mt-6 w-full rounded-md bg-teal-700 px-4 py-3 font-semibold text-white">Kirim Link Reset</button>
        </form>
    </AppLayout>
</template>
