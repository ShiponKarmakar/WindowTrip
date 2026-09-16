<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: [Number, String, null], default: null }, // selected user_id
    clients: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue', 'select']);

const selected = computed({
    get: () => props.modelValue ?? '',
    set: (v) => emit('update:modelValue', v === '' ? null : Number(v)),
});

function onChange(e) {
    const id = e.target.value === '' ? null : Number(e.target.value);
    emit('update:modelValue', id);
    const client = props.clients.find((c) => c.id === id) || null;
    emit('select', client); // parent fills name/email/phone (null = manual entry)
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Client</label>
            <Link :href="route('admin.clients.create')" target="_blank" class="text-xs font-semibold text-brand-purple hover:underline">+ New client</Link>
        </div>
        <select :value="selected" @change="onChange" class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple">
            <option value="">— Walk-in / type details manually —</option>
            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }} — {{ c.email }}</option>
        </select>
        <p class="mt-1 text-xs text-slate-400">Selecting a client fills the contact details below and links this to their portal.</p>
    </div>
</template>
