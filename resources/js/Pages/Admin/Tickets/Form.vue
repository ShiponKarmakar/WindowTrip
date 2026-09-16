<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ClientPicker from '@/Components/ClientPicker.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    ticket: { type: Object, default: null },
    prefill: { type: Object, default: null },
    nextNumber: { type: String, default: '' },
    statuses: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
});

const isEdit = !!props.ticket;
const t = props.ticket || {};
const pf = props.prefill || {};
const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    number: t.number || props.nextNumber || '',
    visa_application_id: t.visa_application_id ?? pf.visa_application_id ?? null,
    user_id: t.user_id ?? pf.user_id ?? null,
    client_name: t.client_name || pf.client_name || '',
    client_email: t.client_email || pf.client_email || '',
    client_phone: t.client_phone || pf.client_phone || '',
    pnr: t.pnr || '',
    booking_ref: t.booking_ref || '',
    airline: t.airline || '',
    currency: t.currency || 'BDT',
    fare_total: t.fare_total != null ? Number(t.fare_total) : null,
    issue_date: t.issue_date ? t.issue_date.slice(0, 10) : today,
    status: t.status || 'draft',
    notes: t.notes || '',
    source_file: null,
    passengers: t.passengers?.length
        ? t.passengers.map((p) => ({ name: p.name || '', type: p.type || 'adult', ticket_number: p.ticket_number || '', seat: p.seat || '' }))
        : [{ name: pf.client_name || '', type: 'adult', ticket_number: '', seat: '' }],
    segments: t.segments?.length
        ? t.segments.map((s) => ({ airline: s.airline || '', flight_number: s.flight_number || '', cabin: s.cabin || '', from_code: s.from_code || '', from_city: s.from_city || '', to_code: s.to_code || '', to_city: s.to_city || '', depart_at: s.depart_at || '', arrive_at: s.arrive_at || '', baggage: s.baggage || '' }))
        : [{ airline: '', flight_number: '', cabin: 'Economy', from_code: '', from_city: '', to_code: '', to_city: '', depart_at: '', arrive_at: '', baggage: '' }],
});

function onClientSelect(client) {
    if (!client) return; // "walk-in" — keep whatever is typed
    form.client_name = client.name || '';
    form.client_email = client.email || '';
    form.client_phone = client.phone || '';
}
function addPassenger() { form.passengers.push({ name: '', type: 'adult', ticket_number: '', seat: '' }); }
function addSegment() { form.segments.push({ airline: '', flight_number: '', cabin: 'Economy', from_code: '', from_city: '', to_code: '', to_city: '', depart_at: '', arrive_at: '', baggage: '' }); }
const parsing = ref(false);
const parseMsg = ref('');
const parseOk = ref(false);
function onFile(e) {
    form.source_file = e.target.files[0] || null;
    parseMsg.value = '';
}

function applyParsed(d) {
    if (d.pnr) form.pnr = d.pnr;
    if (d.booking_ref) form.booking_ref = d.booking_ref;
    if (d.airline) form.airline = d.airline;
    if (d.passengers?.length) {
        form.passengers = d.passengers.map((p) => ({ name: p.name || '', type: (p.type || 'adult').toLowerCase(), ticket_number: p.ticket_number || '', seat: p.seat || '' }));
        // Seed the client (booking contact) name from the primary passenger,
        // unless a client is already selected or a name was typed.
        if (!form.user_id && !form.client_name && d.passengers[0]?.name) {
            form.client_name = d.passengers[0].name;
        }
    }
    if (d.client_email && !form.user_id && !form.client_email) form.client_email = d.client_email;
    if (d.client_phone && !form.user_id && !form.client_phone) form.client_phone = d.client_phone;
    if (d.segments?.length) {
        form.segments = d.segments.map((s) => ({ airline: s.airline || '', flight_number: s.flight_number || '', cabin: s.cabin || 'Economy', from_code: s.from_code || '', from_city: s.from_city || '', to_code: s.to_code || '', to_city: s.to_city || '', depart_at: s.depart_at || '', arrive_at: s.arrive_at || '', baggage: s.baggage || '' }));
    }
}

async function autofill() {
    if (!form.source_file) return;
    parsing.value = true;
    parseMsg.value = '';
    parseOk.value = false;
    try {
        const fd = new FormData();
        fd.append('source_file', form.source_file);
        const { data } = await window.axios.post(route('admin.tickets.parse'), fd);
        if (!data.ok) {
            parseMsg.value = data.reason === 'no_text'
                ? 'This looks like a scanned/image PDF — there’s no text to read. Please enter the details manually.'
                : 'Couldn’t recognise this ticket’s layout. Please enter the details manually.';
            return;
        }
        applyParsed(data.data);
        parseOk.value = true;
        parseMsg.value = (data.source === 'ai' ? 'Fields auto-filled using AI' : 'Fields auto-filled from the PDF')
            + ' — please review everything before saving.';
    } catch (e) {
        parseMsg.value = 'Could not read this file. Make sure it’s a text-based PDF, or enter the details manually.';
    } finally {
        parsing.value = false;
    }
}

function submit() {
    const opts = { preserveScroll: true, forceFormData: true };
    if (isEdit) {
        form.transform((d) => ({ ...d, _method: 'put' })).post(route('admin.tickets.update', props.ticket.id), opts);
    } else {
        form.post(route('admin.tickets.store'), opts);
    }
}
</script>

<template>
    <Head :title="isEdit ? `Edit ${t.number}` : 'New Ticket'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit ${t.number}` : 'New Flight Ticket' }}</template>

        <Link href="/admin/tickets" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to tickets</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-4xl space-y-6">
            <!-- Upload original -->
            <div class="rounded-2xl border border-dashed border-brand-purple/30 bg-brand-50/40 p-6">
                <h3 class="font-heading font-semibold text-brand-ink">Original ticket <span class="text-slate-500">(optional)</span></h3>
                <p class="mt-1 text-sm text-slate-500">Upload the airline/GDS ticket (PDF or image). We store it alongside this record. For a text-based PDF, use <strong>Auto-fill from PDF</strong> to read the PNR, flights and passengers into the fields below — then review. The branded WindowTrip e-ticket is generated from those fields.</p>
                <input type="file" accept=".pdf,.png,.jpg,.jpeg" @change="onFile" class="mt-3 block w-full text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-gradient file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white" />

                <div v-if="form.source_file" class="mt-3 flex flex-wrap items-center gap-3">
                    <button type="button" @click="autofill" :disabled="parsing"
                        class="inline-flex items-center gap-2 rounded-full bg-brand-ink px-4 py-2 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-50">
                        <svg v-if="parsing" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                        <span>{{ parsing ? 'Reading PDF…' : '✨ Auto-fill from PDF' }}</span>
                    </button>
                    <span class="text-xs text-slate-400">Text-based PDFs only. Always review the result.</span>
                </div>
                <p v-if="parseMsg" class="mt-2 rounded-lg px-3 py-2 text-xs" :class="parseOk ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">{{ parseMsg }}</p>

                <p v-if="isEdit && t.source_file_name" class="mt-2 text-xs text-slate-500">Current file: <span class="font-medium">{{ t.source_file_name }}</span> — uploading a new one replaces it.</p>
                <p v-if="form.errors.source_file" class="err">{{ form.errors.source_file }}</p>
            </div>

            <!-- Booking & client -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Booking & passenger contact</h3>
                <div class="mt-4">
                    <ClientPicker v-model="form.user_id" :clients="clients" @select="onClientSelect" />
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Booking PNR</label><input v-model="form.pnr" class="inp uppercase" placeholder="e.g. X4Y9ZK" /><p v-if="form.errors.pnr" class="err">{{ form.errors.pnr }}</p></div>
                    <div><label class="lbl">Airline ref <span class="text-slate-400">(optional)</span></label><input v-model="form.booking_ref" class="inp" /></div>
                    <div><label class="lbl">Airline</label><input v-model="form.airline" class="inp" placeholder="e.g. Emirates" /></div>
                    <div><label class="lbl">Ticket number</label><input v-model="form.number" class="inp" /><p v-if="form.errors.number" class="err">{{ form.errors.number }}</p></div>
                    <div><label class="lbl">Client name</label><input v-model="form.client_name" class="inp" /><p v-if="form.errors.client_name" class="err">{{ form.errors.client_name }}</p></div>
                    <div><label class="lbl">Client email <span class="text-slate-400">(optional)</span></label><input v-model="form.client_email" type="email" class="inp" /><p v-if="form.errors.client_email" class="err">{{ form.errors.client_email }}</p></div>
                    <div><label class="lbl">Client phone</label><input v-model="form.client_phone" class="inp" /></div>
                    <div><label class="lbl">Issue date</label><input v-model="form.issue_date" type="date" class="inp" /><p v-if="form.errors.issue_date" class="err">{{ form.errors.issue_date }}</p></div>
                    <div>
                        <label class="lbl">Status</label>
                        <select v-model="form.status" class="inp capitalize"><option v-for="s in statuses" :key="s" :value="s">{{ s }}</option></select>
                    </div>
                </div>
            </div>

            <!-- Flight segments -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Flights</h3>
                    <button type="button" @click="addSegment" class="text-sm font-semibold text-brand-purple">+ Add flight</button>
                </div>
                <p v-if="form.errors.segments" class="err">{{ form.errors.segments }}</p>
                <div class="mt-4 space-y-5">
                    <div v-for="(s, i) in form.segments" :key="i" class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Flight {{ i + 1 }}</span>
                            <button v-if="form.segments.length > 1" type="button" @click="form.segments.splice(i,1)" class="text-slate-400 hover:text-red-500">✕ remove</button>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-4">
                            <div><label class="lbl">Airline</label><input v-model="s.airline" class="inp" :placeholder="form.airline || 'Carrier'" /></div>
                            <div><label class="lbl">Flight no.</label><input v-model="s.flight_number" class="inp uppercase" placeholder="EK585" /></div>
                            <div><label class="lbl">Cabin</label><input v-model="s.cabin" class="inp" placeholder="Economy" /></div>
                            <div><label class="lbl">Baggage</label><input v-model="s.baggage" class="inp" placeholder="30kg" /></div>
                            <div><label class="lbl">From (code)</label><input v-model="s.from_code" class="inp uppercase" placeholder="DAC" /></div>
                            <div class="sm:col-span-3"><label class="lbl">From (city)</label><input v-model="s.from_city" class="inp" placeholder="Dhaka" /></div>
                            <div><label class="lbl">To (code)</label><input v-model="s.to_code" class="inp uppercase" placeholder="DXB" /></div>
                            <div class="sm:col-span-3"><label class="lbl">To (city)</label><input v-model="s.to_city" class="inp" placeholder="Dubai" /></div>
                            <div class="sm:col-span-2"><label class="lbl">Departure</label><input v-model="s.depart_at" type="datetime-local" class="inp" /></div>
                            <div class="sm:col-span-2"><label class="lbl">Arrival</label><input v-model="s.arrive_at" type="datetime-local" class="inp" /></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passengers -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Passengers</h3>
                    <button type="button" @click="addPassenger" class="text-sm font-semibold text-brand-purple">+ Add passenger</button>
                </div>
                <p v-if="form.errors.passengers" class="err">{{ form.errors.passengers }}</p>
                <div class="mt-4 space-y-2">
                    <div class="hidden grid-cols-12 gap-2 px-1 text-xs uppercase tracking-wide text-slate-400 sm:grid">
                        <div class="col-span-5">Name (as in passport)</div><div class="col-span-2">Type</div><div class="col-span-3">Ticket number</div><div class="col-span-2">Seat</div>
                    </div>
                    <div v-for="(p, i) in form.passengers" :key="i" class="grid grid-cols-12 items-center gap-2">
                        <input v-model="p.name" placeholder="Full name" class="inp col-span-12 sm:col-span-5" />
                        <select v-model="p.type" class="inp col-span-4 capitalize sm:col-span-2"><option value="adult">adult</option><option value="child">child</option><option value="infant">infant</option></select>
                        <input v-model="p.ticket_number" placeholder="Ticket #" class="inp col-span-6 sm:col-span-3" />
                        <input v-model="p.seat" placeholder="Seat" class="inp col-span-1 sm:col-span-1" />
                        <button v-if="form.passengers.length > 1" type="button" @click="form.passengers.splice(i,1)" class="col-span-1 text-slate-400 hover:text-red-500">✕</button>
                    </div>
                </div>
            </div>

            <!-- Fare + notes -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div><label class="lbl">Currency</label><input v-model="form.currency" class="inp" /></div>
                    <div class="sm:col-span-2"><label class="lbl">Total fare <span class="text-slate-400">(optional)</span></label><input v-model.number="form.fare_total" type="number" min="0" step="0.01" class="inp" /></div>
                </div>
                <div class="mt-4">
                    <label class="lbl">Notes <span class="text-slate-400">(optional)</span></label>
                    <textarea v-model="form.notes" rows="3" placeholder="Fare rules, visa reminders, baggage notes…" class="inp"></textarea>
                </div>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">Please fix the highlighted fields before saving.</div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">{{ isEdit ? 'Save ticket' : 'Create ticket' }}</button>
                <Link href="/admin/tickets" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
