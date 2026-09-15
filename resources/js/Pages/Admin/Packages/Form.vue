<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ package: Object });

const isEdit = !!props.package;
const p = props.package || {};

const form = useForm({
    title: p.title || '',
    slug: p.slug || '',
    destination: p.destination || '',
    nights: p.nights || '',
    price: p.price || '',
    tag: p.tag || '',
    color: p.color || '#139dd5',
    active: p.active ?? true,
    sort_order: p.sort_order ?? 0,
    includes: p.includes?.length ? [...p.includes] : [''],
});

const swatches = ['#139dd5', '#0f7fae', '#2ba7da', '#0d6790', '#114a63', '#1b9e77', '#d9772b', '#9b3b8a'];

function submit() {
    form.transform((data) => ({ ...data, includes: data.includes.filter((i) => i.trim()) }));
    const opts = { preserveScroll: true };
    isEdit
        ? form.put(route('admin.packages.update', props.package.id), opts)
        : form.post(route('admin.packages.store'), opts);
}
</script>

<template>
    <Head :title="isEdit ? `Edit ${p.title}` : 'Add Package'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit ${p.title}` : 'Add Tour Package' }}</template>

        <Link href="/admin/packages" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to packages</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-3xl space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Details</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="lbl">Title</label>
                        <input v-model="form.title" class="inp" placeholder="Bangkok & Pattaya" />
                        <p v-if="form.errors.title" class="err">{{ form.errors.title }}</p>
                    </div>
                    <div>
                        <label class="lbl">Slug <span class="text-slate-400">(auto if blank)</span></label>
                        <input v-model="form.slug" class="inp" placeholder="bangkok-pattaya" />
                        <p v-if="form.errors.slug" class="err">{{ form.errors.slug }}</p>
                    </div>
                    <div><label class="lbl">Destination</label><input v-model="form.destination" class="inp" placeholder="Thailand" /></div>
                    <div><label class="lbl">Duration</label><input v-model="form.nights" class="inp" placeholder="4N / 5D" /></div>
                    <div><label class="lbl">Price from (৳)</label><input v-model="form.price" class="inp" placeholder="38,900" /></div>
                    <div><label class="lbl">Tag</label><input v-model="form.tag" class="inp" placeholder="Best Seller" /></div>
                </div>

                <div class="mt-4">
                    <label class="lbl">Cover colour</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <button v-for="s in swatches" :key="s" type="button" @click="form.color = s"
                            class="h-8 w-8 rounded-full ring-2 ring-offset-2 transition" :style="{ backgroundColor: s }"
                            :class="form.color === s ? 'ring-brand-purple' : 'ring-transparent'"></button>
                        <input v-model="form.color" class="inp w-28" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">What's included</h3>
                    <button type="button" @click="form.includes.push('')" class="text-sm font-semibold text-brand-purple">+ Add</button>
                </div>
                <div class="mt-4 space-y-2">
                    <div v-for="(inc, i) in form.includes" :key="i" class="flex gap-2">
                        <input v-model="form.includes[i]" class="inp" placeholder="e.g. Return air ticket" />
                        <button type="button" @click="form.includes.splice(i, 1)" class="px-2 text-slate-400 hover:text-red-500">✕</button>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" v-model="form.active" class="rounded border-slate-300 text-brand-purple focus:ring-brand-purple" />
                        Active (visible on site)
                    </label>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-slate-700">Sort order</label>
                        <input v-model.number="form.sort_order" type="number" class="inp w-24" />
                    </div>
                </div>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">
                <p class="font-semibold">Please fix the following before saving:</p>
                <ul class="mt-1 list-disc pl-5">
                    <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
                </ul>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">
                    {{ isEdit ? 'Save changes' : 'Add package' }}
                </button>
                <Link href="/admin/packages" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
                <span v-if="form.recentlySuccessful" class="text-sm text-emerald-600">Saved ✓</span>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
