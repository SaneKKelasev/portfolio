<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true,
    },

    title: {
        type: String,
        default: 'Проект',
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
            class="mt-4 grid grid-cols-5 gap-4"
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
            class="flex aspect-video items-center justify-center overflow-hidden
                   rounded-2xl border border-border-bright/70
                   bg-[linear-gradient(135deg,rgb(8_13_28),rgb(21_15_43))]
                   px-6 text-center"
        >
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-[0.18em]
                           text-accent"
                >
                    Демо-галерея
                </p>
                <p class="mt-3 text-lg font-semibold text-text">
                    {{ title }}
                </p>
                <p class="mt-2 text-sm text-text-muted">
                    Скриншоты для этого демо-кейса пока не добавлены
                </p>
            </div>
        </div>
    </div>
</template>
