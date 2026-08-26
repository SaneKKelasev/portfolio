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
        class="inline-flex rounded-full border border-border-bright/60
               bg-white/[0.03] px-3.5 py-1.5 text-xs font-semibold
               text-text transition group-hover:border-accent/50"
    >
        {{ technology.name }}
    </span>
</template>
