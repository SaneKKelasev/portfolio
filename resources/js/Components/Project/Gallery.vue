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
    return props.images.slice(0, 3);
});

function selectImage(index) {
    activeIndex.value = index;
}
</script>

<template>
    <div class="min-w-0">
        <div
            v-if="activeImage"
            class="overflow-hidden rounded-2xl border border-border bg-background/60"
        >
            <img
                :key="activeImage.id"
                :src="activeImage.url"
                :alt="activeImage.alt ?? ''"
                class="aspect-[3/2] w-full object-contain"
            >
        </div>

        <div
            v-if="images.length > 1"
            class="mt-4 grid grid-cols-3 gap-4"
        >
            <button
                v-for="(image, index) in thumbnails"
                :key="image.id"
                type="button"
                class="overflow-hidden rounded-xl border bg-background/60
                       transition duration-200"
                :class="
                    index === activeIndex
                        ? 'border-primary ring-2 ring-primary/30'
                        : 'border-border hover:-translate-y-0.5 hover:border-primary/50'
                "
                @click="selectImage(index)"
            >
                <img
                    :src="image.url"
                    :alt="image.alt ?? ''"
                    loading="lazy"
                    class="aspect-[3/2] w-full object-contain"
                >
            </button>
        </div>

        <div
            v-if="images.length === 0"
            class="flex aspect-[3/2] items-center justify-center rounded-2xl
                   border border-dashed border-border bg-background/30
                   px-6 text-center text-sm text-text-muted"
        >
            Изображения пока не добавлены
        </div>
    </div>
</template>