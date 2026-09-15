<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
    mailPasswordSet: Boolean,
});

const form = useForm({
    company_name: props.settings.company_name,
    tagline: props.settings.tagline,
    support_email: props.settings.support_email,
    support_phone: props.settings.support_phone,
    office_address: props.settings.office_address,
    office_hours: props.settings.office_hours,
    alert_email: props.settings.alert_email,
    facebook_url: props.settings.facebook_url,
    instagram_url: props.settings.instagram_url,
    whatsapp_number: props.settings.whatsapp_number,
    mail_mailer: props.settings.mail_mailer || 'log',
    mail_host: props.settings.mail_host,
    mail_port: props.settings.mail_port,
    mail_username: props.settings.mail_username,
    mail_password: '',
    mail_encryption: props.settings.mail_encryption ?? 'tls',
    mail_from_address: props.settings.mail_from_address,
    mail_from_name: props.settings.mail_from_name,
});

function save() {
    form.patch(route('admin.settings.update'), { preserveScroll: true });
}

const testForm = useForm({ test_email: props.settings.alert_email });
function sendTest() {
    testForm.post(route('admin.settings.test'), { preserveScroll: true });
}

const groups = {
    brand: [
        { key: 'company_name', label: 'Company name', type: 'text' },
        { key: 'tagline', label: 'Tagline', type: 'text' },
    ],
    contact: [
        { key: 'support_email', label: 'Support email', type: 'email' },
        { key: 'support_phone', label: 'Support phone', type: 'text' },
        { key: 'office_address', label: 'Office address', type: 'text', wide: true },
        { key: 'office_hours', label: 'Office hours', type: 'text' },
    ],
    notifications: [
        { key: 'alert_email', label: 'New-application alert email', type: 'email', wide: true, hint: 'Where new application & lead alerts are sent.' },
    ],
    social: [
        { key: 'facebook_url', label: 'Facebook URL', type: 'url' },
        { key: 'instagram_url', label: 'Instagram URL', type: 'url' },
        { key: 'whatsapp_number', label: 'WhatsApp number', type: 'text' },
    ],
};
</script>

<template>
    <Head title="Settings" />
    <AdminLayout>
        <template #title>Settings</template>

        <div class="mx-auto max-w-3xl space-y-6">
            <form @submit.prevent="save" class="space-y-6">
                <div v-for="(group, name) in groups" :key="name" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold capitalize text-brand-ink">{{ name }}</h3>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div v-for="f in group" :key="f.key" :class="f.wide ? 'sm:col-span-2' : ''">
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">{{ f.label }}</label>
                            <input v-model="form[f.key]" :type="f.type" class="inp" />
                            <p v-if="f.hint" class="mt-1 text-xs text-slate-400">{{ f.hint }}</p>
                            <p v-if="form.errors[f.key]" class="err">{{ form.errors[f.key] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Email / SMTP -->
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-semibold text-brand-ink">Email (SMTP)</h3>
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="form.mail_mailer === 'smtp' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'">
                            {{ form.mail_mailer === 'smtp' ? 'Live sending' : 'Log only (not sending)' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">Configure your mail server so the system can send real emails (e.g. cPanel mail).</p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="lbl">Mailer</label>
                            <select v-model="form.mail_mailer" class="inp">
                                <option value="log">Log only (capture, don't send)</option>
                                <option value="smtp">SMTP (send for real)</option>
                            </select>
                        </div>
                        <div>
                            <label class="lbl">Encryption</label>
                            <select v-model="form.mail_encryption" class="inp">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                        <div>
                            <label class="lbl">SMTP host</label>
                            <input v-model="form.mail_host" class="inp" placeholder="mail.yourdomain.com" />
                            <p v-if="form.errors.mail_host" class="err">{{ form.errors.mail_host }}</p>
                        </div>
                        <div>
                            <label class="lbl">Port</label>
                            <input v-model="form.mail_port" class="inp" placeholder="587" />
                        </div>
                        <div>
                            <label class="lbl">Username</label>
                            <input v-model="form.mail_username" class="inp" placeholder="no-reply@yourdomain.com" autocomplete="off" />
                        </div>
                        <div>
                            <label class="lbl">Password</label>
                            <input v-model="form.mail_password" type="password" class="inp" autocomplete="new-password"
                                :placeholder="mailPasswordSet ? '•••••••• (leave blank to keep)' : 'SMTP password'" />
                        </div>
                        <div>
                            <label class="lbl">From address</label>
                            <input v-model="form.mail_from_address" type="email" class="inp" placeholder="no-reply@yourdomain.com" />
                        </div>
                        <div>
                            <label class="lbl">From name</label>
                            <input v-model="form.mail_from_name" class="inp" placeholder="Window Trip" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Save settings</button>
                    <span v-if="form.recentlySuccessful" class="text-sm text-emerald-600">Saved ✓</span>
                </div>
            </form>

            <!-- Test email (separate form) -->
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                <h3 class="font-heading font-semibold text-brand-ink">Send a test email</h3>
                <p class="mt-1 text-sm text-slate-500">Save your settings first, then send a test to confirm delivery.</p>
                <form @submit.prevent="sendTest" class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-56">
                        <label class="lbl">Send to</label>
                        <input v-model="testForm.test_email" type="email" class="inp" />
                        <p v-if="testForm.errors.test_email" class="err">{{ testForm.errors.test_email }}</p>
                    </div>
                    <button type="submit" :disabled="testForm.processing" class="rounded-full border border-brand-purple/30 bg-white px-6 py-3 text-sm font-semibold text-brand-purple hover:bg-brand-50 disabled:opacity-50">
                        {{ testForm.processing ? 'Sending…' : 'Send test' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
