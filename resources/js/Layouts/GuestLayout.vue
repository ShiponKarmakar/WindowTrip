<script setup>
defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
});

const highlights = [
    'Visa processing for 7 top destinations',
    'Live application status tracking',
    'Secure document vault',
    'Air tickets & curated tour packages',
];
</script>

<template>
    <div class="flex min-h-screen bg-white">
        <!-- Left brand panel -->
        <div class="relative hidden w-1/2 overflow-hidden lg:flex" style="background:#14142a">
            <div class="brand-mesh--dark absolute inset-0 opacity-90"></div>
            <div class="pointer-events-none absolute -top-20 -left-20 h-72 w-72 rounded-full bg-brand-blue/30 blur-3xl animate-blob"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 h-80 w-80 rounded-full bg-brand-magenta/30 blur-3xl animate-blob" style="animation-delay:-6s"></div>

            <div class="relative z-10 flex w-full flex-col justify-between p-12">
                <a href="/" class="inline-flex">
                    <img src="/brand/logo-white.svg" alt="Window Trip" class="h-10 w-auto" />
                </a>

                <div>
                    <h2 class="font-heading text-4xl font-extrabold leading-tight text-white">
                        Your journey starts at the
                        <span class="text-brand-300">right window.</span>
                    </h2>
                    <ul class="mt-8 space-y-3">
                        <li v-for="h in highlights" :key="h" class="flex items-center gap-3 text-white/90">
                            <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-white/15 text-sm">✓</span>
                            {{ h }}
                        </li>
                    </ul>
                </div>

                <p class="text-sm text-white/50">© {{ new Date().getFullYear() }} Window Trip — Your Complete Travel Partner</p>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="flex w-full flex-col justify-center px-6 py-10 sm:px-12 lg:w-1/2">
            <div class="mx-auto w-full max-w-md">
                <a href="/" class="mb-8 inline-flex lg:hidden">
                    <img :src="$page.props.site?.logo || '/brand/logo-horizontal.svg'" alt="Window Trip" class="h-10 w-auto" />
                </a>

                <div v-if="title">
                    <h1 class="font-heading text-2xl font-bold text-brand-ink sm:text-3xl">{{ title }}</h1>
                    <p v-if="subtitle" class="mt-1.5 text-slate-500">{{ subtitle }}</p>
                </div>

                <div class="mt-8">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
