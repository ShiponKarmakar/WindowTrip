<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    country: Object,
    prefill: Object,
});

const steps = [
    { key: 'trip', title: 'Trip', icon: '✈️' },
    { key: 'personal', title: 'Personal', icon: '🧑' },
    { key: 'passport', title: 'Passport', icon: '📘' },
    { key: 'contact', title: 'Contact', icon: '📞' },
    { key: 'review', title: 'Review', icon: '✅' },
];

const current = ref(0);
const agree = ref(false);

const form = useForm({
    visa_type: props.country?.visa_types?.[0] || 'Tourist',
    travellers: 1,
    travel_date: '',
    full_name: props.prefill?.full_name || '',
    date_of_birth: '',
    gender: '',
    nationality: 'Bangladeshi',
    passport_number: '',
    passport_expiry: '',
    email: props.prefill?.email || '',
    phone: '',
    notes: '',
    passport_scan: null,
    photo: null,
});

const progress = computed(() => Math.round((current.value / (steps.length - 1)) * 100));

// Per-step required fields for lightweight client-side gating.
const stepValid = computed(() => {
    switch (steps[current.value].key) {
        case 'trip':
            return form.visa_type && form.travellers >= 1;
        case 'personal':
            return form.full_name.trim().length > 1 && form.nationality.trim().length > 1;
        case 'passport':
            return true; // documents optional at this stage
        case 'contact':
            return /^\S+@\S+\.\S+$/.test(form.email);
        default:
            return true;
    }
});

function next() {
    if (current.value < steps.length - 1 && stepValid.value) current.value++;
}
function back() {
    if (current.value > 0) current.value--;
}
function goTo(i) {
    if (i <= current.value) current.value = i;
}

function onFile(field, e) {
    form[field] = e.target.files[0] || null;
}

// Which step each field belongs to, so we can jump back to a server error.
const fieldStep = {
    visa_type: 0, travellers: 0, travel_date: 0,
    full_name: 1, date_of_birth: 1, gender: 1, nationality: 1,
    passport_number: 2, passport_expiry: 2, passport_scan: 2, photo: 2,
    email: 3, phone: 3, notes: 3,
};

function submit() {
    form.post(route('visa.apply.store', props.country.slug), {
        forceFormData: true,
        preserveScroll: true,
        onError: (errors) => {
            // Jump to the earliest step that has an error so it's never hidden.
            const step = Object.keys(errors)
                .map((f) => fieldStep[f])
                .filter((s) => s !== undefined)
                .sort((a, b) => a - b)[0];
            if (step !== undefined) current.value = step;
        },
    });
}

const fileName = (f) => (f ? f.name : '');
</script>

<template>
    <Head :title="`Apply — ${country.name} Visa`" />

    <div class="min-h-screen bg-slate-50">
        <!-- Top bar -->
        <header class="border-b border-slate-100 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
                <a href="/" class="flex items-center">
                    <img :src="$page.props.site?.logo || '/brand/logo-horizontal.svg'" alt="Window Trip" class="h-9 w-auto" />
                </a>
                <a :href="route('visa.show', country.slug)" class="text-sm font-medium text-slate-500 hover:text-brand-purple">
                    ← Back to {{ country.name }} visa
                </a>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <!-- Heading -->
            <div class="text-center">
                <span class="text-4xl">{{ country.flag }}</span>
                <h1 class="mt-2 font-heading text-2xl font-bold text-brand-ink sm:text-3xl">
                    {{ country.name }} Visa Application
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ country.subtitle }} · Processing {{ country.processing }} · From ৳{{ country.fee_from }}
                </p>
            </div>

            <!-- Stepper -->
            <div class="mt-8">
                <div class="flex items-center justify-between">
                    <button
                        v-for="(s, i) in steps"
                        :key="s.key"
                        type="button"
                        @click="goTo(i)"
                        class="flex flex-1 flex-col items-center gap-1.5"
                        :class="i <= current ? 'cursor-pointer' : 'cursor-default'"
                    >
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold transition"
                            :class="i < current
                                ? 'bg-brand-gradient text-white'
                                : i === current
                                    ? 'bg-white text-brand-purple ring-2 ring-brand-purple'
                                    : 'bg-slate-100 text-slate-400'"
                        >
                            <span v-if="i < current">✓</span>
                            <span v-else>{{ i + 1 }}</span>
                        </span>
                        <span class="text-[11px] font-medium" :class="i <= current ? 'text-brand-ink' : 'text-slate-400'">{{ s.title }}</span>
                    </button>
                </div>
                <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-brand-gradient transition-all duration-500" :style="{ width: progress + '%' }"></div>
                </div>
            </div>

            <!-- Card -->
            <div class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                <!-- STEP 1: TRIP -->
                <section v-show="steps[current].key === 'trip'" class="space-y-5">
                    <h2 class="font-heading text-lg font-semibold text-brand-ink">Trip details</h2>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Visa type</label>
                        <select v-model="form.visa_type" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">
                            <option v-for="t in country.visa_types" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Number of travellers</label>
                            <input v-model.number="form.travellers" type="number" min="1" max="20" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Intended travel date <span class="text-slate-400">(optional)</span></label>
                            <input v-model="form.travel_date" type="date" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.travel_date" class="mt-1 text-xs text-red-500">{{ form.errors.travel_date }}</p>
                        </div>
                    </div>
                </section>

                <!-- STEP 2: PERSONAL -->
                <section v-show="steps[current].key === 'personal'" class="space-y-5">
                    <h2 class="font-heading text-lg font-semibold text-brand-ink">Personal information</h2>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Full name <span class="text-slate-400">(exactly as in passport)</span></label>
                        <input v-model="form.full_name" type="text" placeholder="e.g. MD RAFIUL HASAN" class="w-full rounded-xl border-slate-200 uppercase focus:border-brand-purple focus:ring-brand-purple" />
                        <p v-if="form.errors.full_name" class="mt-1 text-xs text-red-500">{{ form.errors.full_name }}</p>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Date of birth</label>
                            <input v-model="form.date_of_birth" type="date" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.date_of_birth" class="mt-1 text-xs text-red-500">{{ form.errors.date_of_birth }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Gender</label>
                            <select v-model="form.gender" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Nationality</label>
                            <input v-model="form.nationality" type="text" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.nationality" class="mt-1 text-xs text-red-500">{{ form.errors.nationality }}</p>
                        </div>
                    </div>
                </section>

                <!-- STEP 3: PASSPORT -->
                <section v-show="steps[current].key === 'passport'" class="space-y-5">
                    <h2 class="font-heading text-lg font-semibold text-brand-ink">Passport &amp; documents</h2>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Passport number</label>
                            <input v-model="form.passport_number" type="text" class="w-full rounded-xl border-slate-200 uppercase focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.passport_number" class="mt-1 text-xs text-red-500">{{ form.errors.passport_number }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Passport expiry date</label>
                            <input v-model="form.passport_expiry" type="date" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.passport_expiry" class="mt-1 text-xs text-red-500">{{ form.errors.passport_expiry }}</p>
                        </div>
                    </div>

                    <!-- Uploads -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-5 text-center hover:border-brand-purple/50">
                            <span class="text-2xl">📄</span>
                            <span class="mt-2 text-sm font-medium text-brand-ink">Passport bio-data page</span>
                            <span class="text-xs text-slate-400">PDF / JPG / PNG · max 5MB</span>
                            <span v-if="fileName(form.passport_scan)" class="mt-2 text-xs font-medium text-brand-purple">{{ fileName(form.passport_scan) }}</span>
                            <input type="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="onFile('passport_scan', $event)" />
                        </label>
                        <label class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-5 text-center hover:border-brand-purple/50">
                            <span class="text-2xl">🖼️</span>
                            <span class="mt-2 text-sm font-medium text-brand-ink">Passport-size photo</span>
                            <span class="text-xs text-slate-400">JPG / PNG · max 5MB</span>
                            <span v-if="fileName(form.photo)" class="mt-2 text-xs font-medium text-brand-purple">{{ fileName(form.photo) }}</span>
                            <input type="file" accept=".jpg,.jpeg,.png" class="hidden" @change="onFile('photo', $event)" />
                        </label>
                    </div>

                    <p v-if="form.errors.passport_scan" class="text-xs text-red-500">{{ form.errors.passport_scan }}</p>
                    <p v-if="form.errors.photo" class="text-xs text-red-500">{{ form.errors.photo }}</p>
                    <p class="text-xs text-slate-400">You can also upload documents later from your portal after submitting.</p>
                </section>

                <!-- STEP 4: CONTACT -->
                <section v-show="steps[current].key === 'contact'" class="space-y-5">
                    <h2 class="font-heading text-lg font-semibold text-brand-ink">Contact details</h2>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                            <input v-model="form.email" type="email" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Phone <span class="text-slate-400">(WhatsApp)</span></label>
                            <input v-model="form.phone" type="text" placeholder="+880…" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Notes <span class="text-slate-400">(optional)</span></label>
                        <textarea v-model="form.notes" rows="3" placeholder="Anything we should know?" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"></textarea>
                    </div>
                </section>

                <!-- STEP 5: REVIEW -->
                <section v-show="steps[current].key === 'review'" class="space-y-5">
                    <h2 class="font-heading text-lg font-semibold text-brand-ink">Review &amp; submit</h2>
                    <dl class="divide-y divide-slate-100 rounded-2xl bg-slate-50 p-5 text-sm">
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Destination</dt><dd class="font-medium text-brand-ink">{{ country.flag }} {{ country.name }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Visa type</dt><dd class="font-medium capitalize text-brand-ink">{{ form.visa_type }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Travellers</dt><dd class="font-medium text-brand-ink">{{ form.travellers }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Travel date</dt><dd class="font-medium text-brand-ink">{{ form.travel_date || '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Full name</dt><dd class="font-medium uppercase text-brand-ink">{{ form.full_name }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Date of birth</dt><dd class="font-medium text-brand-ink">{{ form.date_of_birth || '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Nationality</dt><dd class="font-medium text-brand-ink">{{ form.nationality }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Passport no.</dt><dd class="font-medium uppercase text-brand-ink">{{ form.passport_number || '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Documents</dt><dd class="font-medium text-brand-ink">{{ [form.passport_scan, form.photo].filter(Boolean).length }} file(s)</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-slate-500">Email</dt><dd class="font-medium text-brand-ink">{{ form.email }}</dd></div>
                    </dl>

                    <!-- No-guarantee disclaimer -->
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        <strong>Please note:</strong> Window Trip provides visa <strong>processing &amp; documentation assistance only</strong>.
                        We do not issue visas and <strong>cannot guarantee approval</strong> — the final decision rests solely with the
                        embassy/consulate. Service fees cover our processing work and are non-refundable once started, regardless of the outcome.
                    </div>

                    <label class="flex items-start gap-2 text-sm text-slate-600">
                        <input type="checkbox" v-model="agree" class="mt-0.5 rounded border-slate-300 text-brand-purple focus:ring-brand-purple" />
                        <span>
                            I confirm the information is accurate, I understand the visa outcome is decided by the embassy (not guaranteed),
                            and I agree to the
                            <a href="/terms" target="_blank" class="font-semibold text-brand-purple hover:underline">Terms &amp; Conditions</a>
                            and
                            <a href="/privacy" target="_blank" class="font-semibold text-brand-purple hover:underline">Privacy Policy</a>.
                        </span>
                    </label>
                </section>

                <!-- Validation error banner (jumps to the bad step automatically) -->
                <div v-if="form.hasErrors" class="mt-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">
                    Please fix the highlighted fields — we’ve taken you to the step that needs attention.
                </div>

                <!-- Footer nav -->
                <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
                    <button v-if="current > 0" type="button" @click="back" class="rounded-full px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-brand-ink">
                        ← Back
                    </button>
                    <span v-else></span>

                    <button
                        v-if="steps[current].key !== 'review'"
                        type="button"
                        @click="next"
                        :disabled="!stepValid"
                        class="rounded-full bg-brand-gradient px-7 py-2.5 text-sm font-semibold text-white shadow-brand transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Continue →
                    </button>
                    <button
                        v-else
                        type="button"
                        @click="submit"
                        :disabled="!agree || form.processing"
                        class="rounded-full bg-brand-gradient px-7 py-2.5 text-sm font-semibold text-white shadow-brand transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <span v-if="form.processing">Submitting…</span>
                        <span v-else>Submit Application</span>
                    </button>
                </div>
            </div>

            <p class="mt-4 text-center text-xs text-slate-400">🔒 Your information is encrypted and used only to process your application.</p>
        </main>
    </div>
</template>
