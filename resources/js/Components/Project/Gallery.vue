<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

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
const isLightboxOpen = ref(false);

const activeImage = computed(() => {
    return props.images[activeIndex.value] ?? null;
});

const thumbnails = computed(() => {
    return props.images;
});

const activeImageUrl = computed(() => {
    return activeImage.value?.large_url ?? activeImage.value?.url;
});

function selectImage(index) {
    activeIndex.value = index;
}

function openLightbox() {
    if (activeImage.value) {
        isLightboxOpen.value = true;
    }
}

function closeLightbox() {
    isLightboxOpen.value = false;
}

function showPrevious() {
    activeIndex.value = activeIndex.value === 0
        ? props.images.length - 1
        : activeIndex.value - 1;
}

function showNext() {
    activeIndex.value = activeIndex.value === props.images.length - 1
        ? 0
        : activeIndex.value + 1;
}

function handleKeydown(event) {
    if (! isLightboxOpen.value) {
        return;
    }

    if (event.key === 'Escape') {
        closeLightbox();
    }

    if (event.key === 'ArrowLeft' && props.images.length > 1) {
        showPrevious();
    }

    if (event.key === 'ArrowRight' && props.images.length > 1) {
        showNext();
    }
}

watch(isLightboxOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : '';
});

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div class="min-w-0">
        <div
            v-if="activeImage"
            class="flex aspect-video w-full items-center justify-center
                   overflow-hidden rounded-2xl border border-border-bright/70
                   bg-background/70 shadow-2xl shadow-primary/10
                   transition hover:border-accent/70"
        >
            <button
                type="button"
                aria-label="Открыть изображение на весь экран"
                class="group/image relative inline-flex max-h-full max-w-full
                       cursor-zoom-in overflow-hidden"
                @click="openLightbox"
            >
                <img
                    :key="activeImage.id"
                    :src="activeImageUrl"
                    :alt="activeImage.alt ?? ''"
                    class="block max-h-full max-w-full object-contain"
                >
                <span
                    class="absolute inset-0 flex items-center justify-center
                           bg-background/0 opacity-0 transition duration-200
                           group-hover/image:bg-background/45
                           group-hover/image:opacity-100"
                >
                    <span
                        class="inline-flex items-center gap-2 rounded-full border
                               border-border-bright/70 bg-surface/85 px-4 py-2
                               text-sm font-semibold text-white shadow-xl
                               shadow-black/30"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none">
                            <path d="M7 3H3v4M13 3h4v4M7 17H3v-4M13 17h4v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        На весь экран
                    </span>
                </span>
            </button>
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
                class="cursor-pointer overflow-hidden rounded-xl border bg-background/60
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
                    class="aspect-video w-full object-contain"
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

        <Teleport to="body">
            <div
                v-if="isLightboxOpen && activeImage"
                class="fixed inset-0 z-50 flex items-center justify-center
                       bg-background/95 p-4 backdrop-blur"
                role="dialog"
                aria-modal="true"
                @click.self="closeLightbox"
            >
                <button
                    type="button"
                    aria-label="Закрыть просмотр"
                    class="absolute right-5 top-5 rounded-full border
                           border-border-bright/70 bg-surface/80 p-3
                           text-text-muted transition hover:border-accent/70
                           hover:text-white"
                    @click="closeLightbox"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none">
                        <path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                </button>

                <button
                    v-if="images.length > 1"
                    type="button"
                    aria-label="Предыдущее изображение"
                    class="absolute left-5 top-1/2 hidden -translate-y-1/2
                           rounded-full border border-border-bright/70
                           bg-surface/80 p-3 text-text-muted transition
                           hover:border-accent/70 hover:text-white sm:block"
                    @click="showPrevious"
                >
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="none">
                        <path d="M12 5l-5 5 5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <figure class="w-full max-w-7xl">
                    <div
                        class="overflow-hidden rounded-2xl border
                               border-border-bright/70 bg-background
                               shadow-2xl shadow-black/40"
                    >
                        <img
                            :src="activeImageUrl"
                            :alt="activeImage.alt ?? ''"
                            class="max-h-[82vh] w-full object-contain"
                        >
                    </div>
                    <figcaption
                        class="mt-4 flex flex-wrap items-center justify-between
                               gap-3 text-sm text-text-muted"
                    >
                        <span>{{ activeImage.alt || title }}</span>
                        <span>{{ activeIndex + 1 }} / {{ images.length }}</span>
                    </figcaption>
                </figure>

                <button
                    v-if="images.length > 1"
                    type="button"
                    aria-label="Следующее изображение"
                    class="absolute right-5 top-1/2 hidden -translate-y-1/2
                           rounded-full border border-border-bright/70
                           bg-surface/80 p-3 text-text-muted transition
                           hover:border-accent/70 hover:text-white sm:block"
                    @click="showNext"
                >
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="none">
                        <path d="M8 5l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </Teleport>
    </div>
</template>
