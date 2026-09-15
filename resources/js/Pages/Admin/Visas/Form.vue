<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ country: Object });

const isEdit = !!props.country;
const c = props.country || {};

const form = useForm({
    name: c.name || '',
    slug: c.slug || '',
    flag: c.flag || '',
    subtitle: c.subtitle || '',
    visa_types: c.visa_types?.length ? [...c.visa_types] : ['Tourist'],
    processing_time: c.processing || '',
    validity: c.validity || '',
    stay: c.stay || '',
    fee_from: c.fee_from || '',
    overview: c.overview || '',
    photo_spec: c.photo_spec || '',
    active: c.active ?? true,
    sort_order: c.sort_order ?? 0,
    requirements: c.requirements?.length ? [...c.requirements] : [''],
    documents: c.documents?.length ? c.documents.map((d) => ({ ...d })) : [{ title: '', desc: '' }],
    faqs: c.faqs?.length ? c.faqs.map((f) => ({ ...f })) : [{ q: '', a: '' }],
});

function submit() {
    // strip empty rows
    form.transform((data) => ({
        ...data,
        processing: data.processing_time,
        visa_types: data.visa_types.filter((t) => t.trim()),
        requirements: data.requirements.filter((r) => r.trim()),
        documents: data.documents.filter((d) => d.title?.trim()),
        faqs: data.faqs.filter((f) => f.q?.trim()),
    }));
    const opts = { preserveScroll: true };
    isEdit
        ? form.put(route('admin.visas.update', props.country.id), opts)
        : form.post(route('admin.visas.store'), opts);
}

const addReq = () => form.requirements.push('');
const addDoc = () => form.documents.push({ title: '', desc: '' });
const addFaq = () => form.faqs.push({ q: '', a: '' });
</script>

<template>
    <Head :title="isEdit ? `Edit ${c.name}` : 'Add Visa'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit ${c.name} Visa` : 'Add Visa Destination' }}</template>

        <Link href="/admin/visas" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to destinations</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-3xl space-y-6">
            <!-- Basics -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Basics</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="lbl">Country name</label>
                        <input v-model="form.name" class="inp" placeholder="e.g. Japan" />
                        <p v-if="form.errors.name" class="err">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="lbl">Slug <span class="text-slate-400">(URL, auto if blank)</span></label>
                        <input v-model="form.slug" class="inp" placeholder="japan" />
                        <p v-if="form.errors.slug" class="err">{{ form.errors.slug }}</p>
                    </div>
                    <div>
                        <label class="lbl">Flag emoji</label>
                        <input v-model="form.flag" class="inp" placeholder="🇯🇵" />
                    </div>
                    <div>
                        <label class="lbl">Visa type / subtitle</label>
                        <input v-model="form.subtitle" class="inp" placeholder="Tourist Visa" />
                    </div>
                </div>
                <div class="mt-4">
                    <label class="lbl">Overview</label>
                    <textarea v-model="form.overview" rows="2" class="inp" placeholder="Short description shown on the page."></textarea>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <label class="lbl mb-0">Visa types offered <span class="text-slate-400">(shown in the application form)</span></label>
                        <button type="button" @click="form.visa_types.push('')" class="text-sm font-semibold text-brand-purple">+ Add</button>
                    </div>
                    <div class="mt-2 space-y-2">
                        <div v-for="(t, i) in form.visa_types" :key="i" class="flex gap-2">
                            <input v-model="form.visa_types[i]" class="inp" placeholder="e.g. Tourist, Transit, Business, Student" />
                            <button type="button" @click="form.visa_types.splice(i, 1)" class="px-2 text-slate-400 hover:text-red-500">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facts -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Quick facts</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div><label class="lbl">Processing</label><input v-model="form.processing_time" class="inp" placeholder="5–7 days" /></div>
                    <div><label class="lbl">Validity</label><input v-model="form.validity" class="inp" placeholder="90 days" /></div>
                    <div><label class="lbl">Stay</label><input v-model="form.stay" class="inp" placeholder="30 days" /></div>
                    <div><label class="lbl">Fee from (৳)</label><input v-model="form.fee_from" class="inp" placeholder="5,000" /></div>
                </div>
                <div class="mt-4">
                    <label class="lbl">Photo specification</label>
                    <input v-model="form.photo_spec" class="inp" placeholder="35×45mm, white background" />
                </div>
            </div>

            <!-- Requirements -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Requirements</h3>
                    <button type="button" @click="addReq" class="text-sm font-semibold text-brand-purple">+ Add</button>
                </div>
                <div class="mt-4 space-y-2">
                    <div v-for="(r, i) in form.requirements" :key="i" class="flex gap-2">
                        <input v-model="form.requirements[i]" class="inp" placeholder="e.g. Passport valid 6+ months" />
                        <button type="button" @click="form.requirements.splice(i, 1)" class="px-2 text-slate-400 hover:text-red-500">✕</button>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Required documents</h3>
                    <button type="button" @click="addDoc" class="text-sm font-semibold text-brand-purple">+ Add</button>
                </div>
                <div class="mt-4 space-y-3">
                    <div v-for="(d, i) in form.documents" :key="i" class="flex gap-2">
                        <input v-model="d.title" class="inp w-1/3" placeholder="Title" />
                        <input v-model="d.desc" class="inp flex-1" placeholder="Description" />
                        <button type="button" @click="form.documents.splice(i, 1)" class="px-2 text-slate-400 hover:text-red-500">✕</button>
                    </div>
                </div>
            </div>

            <!-- FAQs -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">FAQs</h3>
                    <button type="button" @click="addFaq" class="text-sm font-semibold text-brand-purple">+ Add</button>
                </div>
                <div class="mt-4 space-y-3">
                    <div v-for="(f, i) in form.faqs" :key="i" class="flex gap-2">
                        <input v-model="f.q" class="inp w-1/3" placeholder="Question" />
                        <input v-model="f.a" class="inp flex-1" placeholder="Answer" />
                        <button type="button" @click="form.faqs.splice(i, 1)" class="px-2 text-slate-400 hover:text-red-500">✕</button>
                    </div>
                </div>
            </div>

            <!-- Visibility -->
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
                    {{ isEdit ? 'Save changes' : 'Add visa' }}
                </button>
                <Link href="/admin/visas" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
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
