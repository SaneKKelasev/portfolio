<script setup>
defineOptions({
    inheritAttrs: false,
});

defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <input
        v-bind="$attrs"
        type="checkbox"
        :checked="modelValue"
        class="mt-0.5 h-4 w-4 shrink-0 rounded border border-border
               bg-background/80 text-primary accent-violet-500 outline-none
               transition focus:ring-2 focus:ring-accent/25"
        @change="$emit('update:modelValue', $event.target.checked)"
    >
</template>
