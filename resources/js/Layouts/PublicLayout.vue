<script setup>
import PublicHeader from '@/Components/public/PublicHeader.vue';
import PublicFooter from '@/Components/public/PublicFooter.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import { runPublicAnimations } from '@/composables/usePublicAnimations.js';

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);
const showFlash = ref(true);

let cleanup = () => {};
onMounted(() => { cleanup = runPublicAnimations(document); });
onBeforeUnmount(() => cleanup());
</script>

<template>
    <div class="min-h-screen bg-white font-sans text-brand-ink antialiased">
        <PublicHeader />

        <div v-if="(flashSuccess || flashError) && showFlash" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mt-4 flex items-center justify-between rounded-xl px-4 py-3 text-sm font-medium"
                :class="flashError ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700'">
                <span>{{ flashError || flashSuccess }}</span>
                <button @click="showFlash = false" class="opacity-60 hover:opacity-100">✕</button>
            </div>
        </div>

        <main>
            <slot />
        </main>

        <PublicFooter />
    </div>
</template>
