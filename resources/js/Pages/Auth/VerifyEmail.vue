<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { csrf } from '../../Components/FormHelpers';
</script>

<template>
    <AppLayout>
        <section class="mx-auto max-w-xl rounded-md border bg-white p-6">
            <h1 class="text-2xl font-semibold">Verifikasi Email</h1>
            <p class="mt-3 text-zinc-600">Cek email Anda untuk link verifikasi sebelum mengakses area member.</p>
            <form method="post" action="/email/verification-notification" class="mt-5">
                <input type="hidden" name="_token" :value="csrf()">
                <button class="rounded-md bg-teal-700 px-4 py-2 font-semibold text-white">Kirim ulang email</button>
            </form>
        </section>
    </AppLayout>
</template>
