<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const editor = ref(null);

onMounted(() => {
    editor.value.innerHTML = props.modelValue || '';
});

// Reflect external changes (e.g. quick-template inserts) into the editor.
// During typing, modelValue already equals innerHTML, so this is a no-op
// and the caret is left untouched.
watch(
    () => props.modelValue,
    (val) => {
        if (editor.value && val !== editor.value.innerHTML) {
            editor.value.innerHTML = val || '';
        }
    },
);

function sync() {
    emit('update:modelValue', editor.value.innerHTML);
}

function exec(command, value = null) {
    editor.value.focus();
    document.execCommand(command, false, value);
    sync();
}

function setBlock(tag) {
    exec('formatBlock', tag);
}

function addLink() {
    const url = window.prompt('Link URL', 'https://');
    if (url) exec('createLink', url);
}

const colors = ['#12263b', '#139dd5', '#0f7fae', '#dc2626', '#16a34a', '#d97706'];

const tools = [
    { label: 'B', title: 'Bold', cmd: () => exec('bold'), class: 'font-bold' },
    { label: 'I', title: 'Italic', cmd: () => exec('italic'), class: 'italic' },
    { label: 'U', title: 'Underline', cmd: () => exec('underline'), class: 'underline' },
];
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-slate-200 focus-within:border-brand-purple focus-within:ring-1 focus-within:ring-brand-purple">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-1 border-b border-slate-100 bg-slate-50 px-2 py-1.5">
            <button v-for="t in tools" :key="t.title" type="button" :title="t.title" @click="t.cmd"
                class="h-8 w-8 rounded-lg text-sm text-slate-600 hover:bg-white" :class="t.class">{{ t.label }}</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" title="Heading" @click="setBlock('h2')" class="h-8 rounded-lg px-2 text-sm font-semibold text-slate-600 hover:bg-white">H</button>
            <button type="button" title="Normal text" @click="setBlock('p')" class="h-8 rounded-lg px-2 text-sm text-slate-600 hover:bg-white">¶</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" title="Bullet list" @click="exec('insertUnorderedList')" class="h-8 w-8 rounded-lg text-slate-600 hover:bg-white">•</button>
            <button type="button" title="Numbered list" @click="exec('insertOrderedList')" class="h-8 w-8 rounded-lg text-sm text-slate-600 hover:bg-white">1.</button>
            <button type="button" title="Link" @click="addLink" class="h-8 w-8 rounded-lg text-slate-600 hover:bg-white">🔗</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <span class="flex items-center gap-1">
                <button v-for="col in colors" :key="col" type="button" :title="`Colour ${col}`" @click="exec('foreColor', col)"
                    class="h-5 w-5 rounded-full ring-1 ring-black/10" :style="{ backgroundColor: col }"></button>
            </span>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" title="Clear formatting" @click="exec('removeFormat')" class="h-8 rounded-lg px-2 text-xs text-slate-500 hover:bg-white">Clear</button>
        </div>

        <!-- Editable area -->
        <div ref="editor" contenteditable="true" @input="sync" @blur="sync"
            class="prose-email min-h-[260px] max-w-none px-4 py-3 text-sm text-slate-800 focus:outline-none"></div>
    </div>
</template>

<style scoped>
.prose-email :deep(h2) { @apply font-heading text-lg font-bold text-brand-ink my-2; }
.prose-email :deep(p) { @apply my-1.5; }
.prose-email :deep(ul) { @apply list-disc pl-6 my-2; }
.prose-email :deep(ol) { @apply list-decimal pl-6 my-2; }
.prose-email :deep(a) { @apply text-brand-purple underline; }
.prose-email:empty::before { content: 'Write your message…'; @apply text-slate-400; }
</style>
