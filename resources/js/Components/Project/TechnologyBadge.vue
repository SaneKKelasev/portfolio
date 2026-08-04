<script setup>
defineProps({
    technology: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <span
        class="inline-flex rounded-full border border-border
               bg-background/60 px-3 py-1.5
               text-xs font-medium text-text-muted"
    >
        {{ technology.name }}
    </span>
</template>