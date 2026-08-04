<script setup>
import ProjectGallery from '@/Components/Project/Gallery.vue';
import TechnologyBadge from '@/Components/Project/TechnologyBadge.vue';

defineProps({
    project: {
        type: Object,
        required: true,
    },

    reverse: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <article
        class="group overflow-hidden rounded-3xl border border-border
               bg-surface/80 shadow-2xl shadow-black/20
               backdrop-blur-sm transition duration-300
               hover:-translate-y-1 hover:border-primary/40"
    >
        <div
            class="grid gap-10 p-6
                   sm:p-8
                   lg:grid-cols-2 lg:items-center lg:p-10"
            :class="{ 'lg:[&>*:first-child]:order-2': reverse }"
        >
            <div>
                <h2
                    class="text-2xl font-semibold tracking-tight text-text
                           sm:text-3xl"
                >
                    {{ project.title }}
                </h2>

                <p class="mt-4 max-w-xl leading-7 text-text-muted">
                    {{ project.description }}
                </p>

                <ul class="mt-6 flex flex-wrap gap-2">
                    <li
                        v-for="technology in project.technologies"
                        :key="technology.slug"
                    >
                        <TechnologyBadge :technology="technology" />
                    </li>
                </ul>

                <div class="mt-8 flex flex-wrap items-center gap-5">
                    <a
                        v-if="project.website_url"
                        :href="project.website_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 text-sm font-medium
                               text-violet-300 transition hover:text-white"
                    >
                        Открыть сайт
                        <span aria-hidden="true">↗</span>
                    </a>

                    <a
                        v-if="project.repository_url"
                        :href="project.repository_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 text-sm font-medium
                               text-text-muted transition hover:text-white"
                    >
                        Исходный код
                    </a>
                </div>
            </div>

            <ProjectGallery :images="project.images" />
        </div>
    </article>
</template>