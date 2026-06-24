<script setup>
import AppLayout from '../../Components/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { csrf, money } from '../../Components/FormHelpers';
import ProductRating from '../../Components/ProductRating.vue';

import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({ product: Object, relatedProducts: Array });
const page = usePage();

const loginRedirect = (path) => `/login?redirect=${encodeURIComponent(path)}`;

const imageSrc = (path) => {
    if (!path) return '/img/thumbnail/default-thumbnail.png';

    return path.startsWith('/') ? path : `/${path}`;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const currentTestiIndex = ref(0);
let testiInterval = null;

onMounted(() => {
    if (props.product.testimonials && props.product.testimonials.length > 1) {
        testiInterval = setInterval(() => {
            currentTestiIndex.value = (currentTestiIndex.value + 1) % props.product.testimonials.length;
        }, 5000);
    }
});

onUnmounted(() => {
    if (testiInterval) clearInterval(testiInterval);
});
</script>

<template>
    <Head>
        <title>{{ product.name }}</title>
        <meta name="description" :content="product.short_description || `Beli ${product.name} sekarang dengan harga terbaik.`" />
    </Head>
    <AppLayout>
        <div class="grid gap-8 rounded-md bg-white p-6 shadow-sm shadow-sky-950/5 lg:grid-cols-[1fr_360px]">
            <section>
                <img :src="imageSrc(product.thumbnail)" :alt="product.name" class="mb-6 aspect-[16/9] w-full rounded-md object-cover">
                <div class="flex flex-wrap gap-2">
                    <span v-for="cat in product.categories" :key="cat.id" class="inline-flex rounded-md bg-sky-50 px-3 py-2 text-sm font-black uppercase text-[#0284c7]">
                        {{ cat.name }}
                    </span>
                </div>
                <h1 class="mt-4 text-5xl font-black leading-tight tracking-tight">{{ product.name }}</h1>
                <p class="mt-4 text-lg leading-8 text-slate-600">{{ product.short_description }}</p>
                <div class="prose mt-8 max-w-none whitespace-pre-line text-slate-700">{{ product.description }}</div>

                <div v-if="product.testimonials && product.testimonials.length" class="mt-12 w-full overflow-hidden">
                    <h2 class="mb-6 text-2xl font-black">Testimonial & Ulasan Pembeli</h2>
                    <div class="relative min-h-[220px] w-full rounded-xl border border-slate-100 bg-slate-50 p-6">
                        <transition name="fade" mode="out-in">
                            <div :key="currentTestiIndex" class="w-full">
                                <div class="mb-4 flex items-center gap-4">
                                    <img :src="product.testimonials[currentTestiIndex].user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(product.testimonials[currentTestiIndex].user?.name || 'User')}&color=0284c7&background=e0f2fe`" class="size-12 rounded-full object-cover">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ product.testimonials[currentTestiIndex].user?.name || 'Member' }}</p>
                                        <div class="mt-1 flex gap-1 text-amber-400">
                                            <svg v-for="i in 5" :key="i" class="size-4" :class="i <= product.testimonials[currentTestiIndex].rating ? 'fill-current' : 'fill-slate-300 text-slate-300'" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <h3 v-if="product.testimonials[currentTestiIndex].title" class="mb-2 font-bold text-slate-800">{{ product.testimonials[currentTestiIndex].title }}</h3>
                                <p class="text-sm leading-relaxed text-slate-600">{{ product.testimonials[currentTestiIndex].content }}</p>
                            </div>
                        </transition>

                        <div v-if="product.testimonials.length > 1" class="absolute bottom-4 left-0 right-0 flex justify-center gap-2">
                            <button 
                                v-for="(_, idx) in product.testimonials" 
                                :key="idx" 
                                @click="currentTestiIndex = idx" 
                                class="size-2 rounded-full transition-colors" 
                                :class="idx === currentTestiIndex ? 'bg-[#0284c7]' : 'bg-slate-300 hover:bg-slate-400'"
                                aria-label="Lihat testimonial lainnya"
                            ></button>
                        </div>
                    </div>
                </div>
            </section>
            <aside class="rounded-md border border-sky-100 bg-[#f4f8ff] p-6">
                <div v-if="product.product_type === 'class' && product.class_product" class="mb-6 rounded-md border border-sky-200 bg-white p-4">
                    <p class="text-sm font-black text-sky-700">Jadwal Kelas · Batch {{ product.class_product.batch_number }}</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        <div v-if="product.class_product.schedule_type === 'fixed'">
                            <p><strong>Tanggal:</strong> {{ formatDate(product.class_product.start_date) }} <span v-if="product.class_product.end_date">s/d {{ formatDate(product.class_product.end_date) }}</span></p>
                            <p><strong>Waktu:</strong> {{ product.class_product.start_time }} - {{ product.class_product.end_time || 'Selesai' }}</p>
                        </div>
                        <div v-if="product.class_product.schedule_type === 'recurring'">
                            <p><strong>Hari:</strong> {{ product.class_product.recurring_days?.join(', ') || '-' }}</p>
                            <p><strong>Waktu:</strong> {{ product.class_product.start_time }} - {{ product.class_product.end_time || 'Selesai' }}</p>
                        </div>
                        <p><strong>Platform:</strong> {{ product.class_product.platform || '-' }}</p>
                        <p v-if="product.class_product.max_participants"><strong>Kuota Maksimal:</strong> {{ product.class_product.max_participants }} Peserta</p>
                    </div>
                </div>

                <p class="text-sm font-black uppercase tracking-wide text-[#0284c7]">Harga Produk</p>
                <p class="mt-2 text-3xl font-black">{{ money(product.sale_price || product.price) }}</p>
                <p v-if="product.membership" class="mt-2 text-sm text-slate-500">Termasuk benefit {{ product.membership.name }}</p>
                <form v-if="page.props.auth?.user" class="mt-6" method="post" :action="`/cart/${product.slug}`">
                    <input type="hidden" name="_token" :value="csrf()">
                    <button class="w-full rounded-md bg-[#0284c7] px-4 py-3 font-black text-white hover:bg-[#0369a1]">
                        {{ product.product_type === 'class' ? 'Daftar' : 'Tambah ke Cart' }}
                    </button>
                </form>
                <Link v-else :href="loginRedirect(`/products/${props.product.slug}`)" class="mt-6 block w-full rounded-md bg-[#0284c7] px-4 py-3 text-center font-black text-white hover:bg-[#0369a1]">
                    {{ product.product_type === 'class' ? 'Daftar' : 'Tambah ke Cart' }}
                </Link>
                <Link
                    v-if="product.product_type !== 'class' && page.props.auth?.user"
                    :href="`/cart/${product.slug}/buy-now`"
                    class="mt-3 block w-full rounded-md border border-sky-200 bg-white px-4 py-3 text-center font-black text-[#0284c7]"
                >
                    Buy Now
                </Link>
                <Link
                    v-else-if="product.product_type !== 'class'"
                    :href="loginRedirect(`/cart/${props.product.slug}/buy-now`)"
                    class="mt-3 block w-full rounded-md border border-sky-200 bg-white px-4 py-3 text-center font-black text-[#0284c7]"
                >
                    Buy Now
                </Link>
                <a v-if="product.demo_url" :href="product.demo_url" class="mt-3 block rounded-md border border-sky-200 bg-white px-4 py-3 text-center font-black text-[#0284c7]">Lihat Demo</a>
            </aside>
        </div>

        <section v-if="relatedProducts?.length" class="mt-10">
            <h2 class="mb-4 text-2xl font-black">Produk Terkait</h2>
            <div class="grid gap-4 md:grid-cols-4">
                <Link v-for="item in relatedProducts" :key="item.id" :href="`/products/${item.slug}`" class="rounded-md border border-slate-100 bg-white p-4 shadow-sm shadow-sky-950/5">
                    <img :src="imageSrc(item.thumbnail)" :alt="item.name" class="mb-3 aspect-[4/3] w-full rounded-md object-cover">
                    <p class="font-black">{{ item.name }}</p>
                    <ProductRating :product="item" />
                    <p class="mt-2 text-sm text-slate-600">{{ money(item.sale_price || item.price) }}</p>
                </Link>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
