<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const activeIndex = ref(0);

const activeImage = computed(() => {
    return props.images[activeIndex.value] ?? null;
});

const thumbnails = computed(() => {
    return props.images;
});

function selectImage(index) {
    activeIndex.value = index;
}
</script>

<template>
    <div class="min-w-0">
        <div
            v-if="activeImage"
            class="overflow-hidden rounded-2xl border border-border-bright/70
                   bg-background/70 shadow-2xl shadow-primary/10"
        >
            <img
                :key="activeImage.id"
                :src="activeImage.url"
                :alt="activeImage.alt ?? ''"
                class="aspect-video w-full object-cover"
            >
        </div>

        <div
            v-if="images.length > 1"
            class="mt-4 grid grid-cols-3 gap-4 sm:grid-cols-5"
        >
            <button
                v-for="(image, index) in thumbnails"
                :key="image.id"
                type="button"
                :aria-label="`Показать изображение ${index + 1}`"
                class="overflow-hidden rounded-xl border bg-background/60
                       transition duration-200"
                :class="
                    index === activeIndex
                        ? 'border-accent ring-2 ring-accent/30'
                        : 'border-border hover:-translate-y-0.5 hover:border-primary/60'
                "
                @click="selectImage(index)"
            >
                <img
                    :src="image.thumb_url ?? image.url"
                    :alt="image.alt ?? ''"
                    loading="lazy"
                    class="aspect-video w-full object-cover"
                >
            </button>
        </div>

        <div
            v-if="images.length === 0"
            class="flex aspect-video items-center justify-center rounded-2xl
                   border border-dashed border-border bg-background/30
                   px-6 text-center text-sm text-text-muted"
        >
            Изображения пока не добавлены
        </div>
    </div>
</template>
