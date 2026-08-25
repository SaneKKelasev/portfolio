<script setup>
import ProjectGallery from '@/Components/Project/Gallery.vue';
import TechnologyBadge from '@/Components/Project/TechnologyBadge.vue';
import { Link } from '@inertiajs/vue3';

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
        class="group relative overflow-hidden rounded-3xl border
               border-border-bright/70 bg-surface/80 shadow-2xl
               shadow-black/30 backdrop-blur transition duration-300
               hover:-translate-y-1 hover:border-accent/70"
    >
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-px
                   bg-gradient-to-r from-transparent via-accent/80
                   to-transparent opacity-70"
        />
        <div
            class="pointer-events-none absolute -right-28 -top-28 h-72 w-72
                   rounded-full bg-primary/20 blur-3xl transition duration-500
                   group-hover:bg-accent/20"
        />

        <div
            class="relative grid gap-10 p-6
                   sm:p-8
                   lg:grid-cols-[0.82fr_1.18fr] lg:items-center lg:p-12"
            :class="{ 'lg:[&>*:first-child]:order-2': reverse }"
        >
            <div class="max-w-xl">
                <p
                    class="mb-5 text-xs font-semibold uppercase
                           tracking-[0.18em] text-accent"
                >
                    Избранный проект
                </p>

                <h2
                    class="text-3xl font-semibold tracking-tight text-text
                           sm:text-4xl"
                >
                    {{ project.title }}
                </h2>

                <p class="mt-5 leading-8 text-text-muted">
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
                    <Link
                        :href="`/projects/${project.slug}`"
                        class="inline-flex items-center gap-2 rounded-full
                               bg-primary px-5 py-3 text-sm font-semibold
                               text-white shadow-lg shadow-primary/25
                               transition hover:-translate-y-0.5
                               hover:bg-violet-500"
                    >
                        Подробнее
                    </Link>

                    <a
                        v-if="project.website_url"
                        :href="project.website_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 text-sm
                               font-semibold text-violet-200 transition
                               hover:text-white"
                    >
                        Открыть сайт
                        <span aria-hidden="true">↗</span>
                    </a>

                    <a
                        v-if="project.repository_url"
                        :href="project.repository_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center rounded-full border
                               border-border-bright/70 px-5 py-3 text-sm
                               font-semibold text-text-muted transition
                               hover:-translate-y-0.5 hover:border-accent/70
                               hover:text-white"
                    >
                        Исходный код
                    </a>
                </div>
            </div>

            <ProjectGallery :images="project.images" />
        </div>
    </article>
</template>
