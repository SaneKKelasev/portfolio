<script setup>
defineOptions({
    inheritAttrs: false,
});

defineProps({
    modelValue: {
        type: String,
        default: '',
    },

    invalid: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <textarea
        v-bind="$attrs"
        :value="modelValue"
        class="mt-2 min-h-36 max-h-72 w-full resize-y rounded-2xl border
               bg-background/70 px-4 py-3 text-base font-medium text-text
               outline-none transition placeholder:text-text-muted/55
               focus:border-accent focus:ring-2 focus:ring-accent/15"
        :class="invalid ? 'border-rose-400/70' : 'border-border'"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
