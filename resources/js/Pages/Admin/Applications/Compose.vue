<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ application: Object });
const a = props.application;

const form = useForm({
    subject: `Your ${a.country} visa application (${a.reference})`,
    body: '',
});

function send() {
    form.post(route('admin.applications.message', a.id));
}

// Quick-insert templates
function template(kind) {
    const map = {
        docs: `<p>Hi ${a.full_name},</p><p>To continue processing your <strong>${a.country} visa</strong> application, please upload the following documents to your portal:</p><ul><li>Passport bio-data page</li><li>Recent passport-size photo</li></ul><p>Thank you!</p>`,
        approved: `<p>Hi ${a.full_name},</p><p>Great news — your <strong>${a.country} visa</strong> has been <span style="color:#16a34a"><strong>approved</strong></span>! 🎉</p><p>We'll be in touch shortly with the next steps.</p>`,
        update: `<p>Hi ${a.full_name},</p><p>Here's a quick update on your <strong>${a.country} visa</strong> application (${a.reference}):</p><p>...</p>`,
    };
    form.body = map[kind];
}
</script>

<template>
    <Head :title="`Email ${application.reference}`" />
    <AdminLayout>
        <template #title>Compose Email</template>

        <Link :href="route('admin.applications.show', a.id)" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to application</Link>

        <form @submit.prevent="send" class="mt-4 grid gap-6 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <!-- Recipient -->
                <div class="flex items-center gap-3 rounded-2xl bg-brand-gradient p-4 text-white shadow-brand">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 font-semibold">{{ a.full_name?.charAt(0) }}</span>
                    <div>
                        <div class="text-sm text-white/80">To</div>
                        <div class="font-semibold">{{ a.full_name }} · {{ a.email }}</div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Subject</label>
                    <input v-model="form.subject" class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple" />
                    <p v-if="form.errors.subject" class="mt-1 text-xs text-red-500">{{ form.errors.subject }}</p>

                    <label class="mb-1.5 mt-5 block text-sm font-medium text-slate-700">Message</label>
                    <RichTextEditor v-model="form.body" />
                    <p v-if="form.errors.body" class="mt-1 text-xs text-red-500">{{ form.errors.body }}</p>
                    <p class="mt-2 text-xs text-slate-400">Your message is wrapped in the branded Window Trip email template when sent.</p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">
                        {{ form.processing ? 'Sending…' : 'Send email' }}
                    </button>
                    <Link :href="route('admin.applications.show', a.id)" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
                </div>
            </div>

            <!-- Templates sidebar -->
            <aside class="space-y-3">
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Quick templates</h3>
                    <p class="mt-1 text-xs text-slate-500">Click to insert, then edit.</p>
                    <div class="mt-3 space-y-2">
                        <button type="button" @click="template('docs')" class="w-full rounded-xl border border-slate-100 px-4 py-2.5 text-left text-sm hover:border-brand-purple/40 hover:bg-brand-50">📄 Request documents</button>
                        <button type="button" @click="template('update')" class="w-full rounded-xl border border-slate-100 px-4 py-2.5 text-left text-sm hover:border-brand-purple/40 hover:bg-brand-50">📣 Status update</button>
                        <button type="button" @click="template('approved')" class="w-full rounded-xl border border-slate-100 px-4 py-2.5 text-left text-sm hover:border-brand-purple/40 hover:bg-brand-50">✅ Visa approved</button>
                    </div>
                </div>
            </aside>
        </form>
    </AdminLayout>
</template>
