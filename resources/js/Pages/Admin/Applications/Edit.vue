<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ application: Object });
const a = props.application;

const form = useForm({
    visa_type: a.visa_type,
    travellers: a.travellers,
    travel_date: a.travel_date || '',
    full_name: a.full_name,
    date_of_birth: a.date_of_birth || '',
    gender: a.gender || '',
    nationality: a.nationality,
    present_address: a.present_address || '',
    occupation: a.occupation || '',
    purpose: a.purpose || '',
    passport_number: a.passport_number || '',
    passport_expiry: a.passport_expiry || '',
    email: a.email,
    phone: a.phone || '',
    notes: a.notes || '',
});

function submit() {
    form.patch(route('admin.applications.update', a.id));
}
</script>

<template>
    <Head :title="`Edit ${application.reference}`" />
    <AdminLayout>
        <template #title>Edit Application</template>

        <Link :href="route('admin.applications.show', a.id)" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to application</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-3xl space-y-6">
            <div class="rounded-2xl bg-brand-gradient p-5 text-white shadow-brand">
                <div class="text-sm text-white/80">{{ application.flag }} {{ application.country }} · {{ application.reference }}</div>
                <div class="font-heading text-lg font-bold capitalize">{{ application.full_name }}</div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Trip</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="lbl">Visa type</label>
                        <input v-model="form.visa_type" class="inp" placeholder="Tourist" />
                    </div>
                    <div><label class="lbl">Travellers</label><input v-model.number="form.travellers" type="number" min="1" class="inp" /></div>
                    <div><label class="lbl">Travel date</label><input v-model="form.travel_date" type="date" class="inp" /></div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Applicant &amp; passport</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Full name</label><input v-model="form.full_name" class="inp uppercase" /><p v-if="form.errors.full_name" class="err">{{ form.errors.full_name }}</p></div>
                    <div><label class="lbl">Date of birth</label><input v-model="form.date_of_birth" type="date" class="inp" /></div>
                    <div>
                        <label class="lbl">Gender</label>
                        <select v-model="form.gender" class="inp"><option value="">—</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                    </div>
                    <div><label class="lbl">Nationality</label><input v-model="form.nationality" class="inp" /></div>
                    <div><label class="lbl">Occupation</label><input v-model="form.occupation" class="inp" /></div>
                    <div class="sm:col-span-2"><label class="lbl">Present address</label><input v-model="form.present_address" class="inp" /></div>
                    <div class="sm:col-span-2"><label class="lbl">Purpose of travel</label><input v-model="form.purpose" class="inp" /></div>
                    <div><label class="lbl">Passport number</label><input v-model="form.passport_number" class="inp uppercase" /></div>
                    <div><label class="lbl">Passport expiry</label><input v-model="form.passport_expiry" type="date" class="inp" /></div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Contact</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Email</label><input v-model="form.email" type="email" class="inp" /><p v-if="form.errors.email" class="err">{{ form.errors.email }}</p></div>
                    <div><label class="lbl">Phone</label><input v-model="form.phone" class="inp" /></div>
                </div>
                <div class="mt-4"><label class="lbl">Notes</label><textarea v-model="form.notes" rows="2" class="inp"></textarea></div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Save changes</button>
                <Link :href="route('admin.applications.show', a.id)" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
