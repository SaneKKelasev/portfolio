<script setup>
const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const mainImage     = props.images[0] ?? null;
const thumbnails    = props.images.slice(1, 5);
</script>

<template>
    <div class="min-w-0">
        <div
            v-if="mainImage"
            class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_7rem]"
        >
            <div
                class="overflow-hidden rounded-2xl border border-border
                       bg-background/60"
            >
                <img
                    :src="mainImage.url"
                    :alt="mainImage.alt ?? ''"
                    class="aspect-[16/10] h-full w-full object-cover
                           transition duration-500
                           group-hover:scale-[1.02]"
                >
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-1">
                <div
                    v-for="image in thumbnails"
                    :key="image.id"
                    class="overflow-hidden rounded-xl border border-border
                           bg-background/60"
                >
                    <img
                        :src="image.url"
                        :alt="image.alt ?? ''"
                        class="aspect-[16/10] h-full w-full object-cover
                               transition duration-300
                               hover:scale-105"
                    >
                </div>

                <div
                    v-for="index in Math.max(0, 4 - thumbnails.length)"
                    :key="`placeholder-${index}`"
                    class="hidden aspect-[16/10] rounded-xl border
                           border-dashed border-border bg-background/30
                           sm:block"
                />
            </div>
        </div>

        <div
            v-else
            class="flex aspect-[16/10] items-center justify-center
                   rounded-2xl border border-dashed border-border
                   bg-background/30 px-6 text-center text-sm text-text-muted"
        >
            Изображения пока не добавлены
        </div>
    </div>
</template>