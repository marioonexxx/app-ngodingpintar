<script setup>
import { computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const count = computed(() => Number(props.product?.approved_testimonials_count || 0));
const rating = computed(() => Number(props.product?.approved_testimonials_avg_rating || 0));
</script>

<template>
    <div class="mt-2 flex items-center gap-1.5 text-xs">
        <span class="text-base leading-none text-amber-400">&#9733;</span>
        <template v-if="count > 0">
            <span class="font-bold text-slate-700">{{ rating.toFixed(1) }}</span>
            <span class="text-slate-400">({{ count }} ulasan)</span>
        </template>
        <span v-else class="text-slate-400">Belum ada ulasan</span>
    </div>
</template>
