<script setup>
import ProjectCard from '@/Components/Project/Card.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    projects: {
        type: Object,
        required: true,
    },

    technologies: {
        type: Array,
        required: true,
    },

    filters: {
        type: Object,
        required: true,
    },

    meta: {
        type: Object,
        required: true,
    },
});

const search = ref(props.filters.search ?? '');

let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        router.get(
            '/projects',
            {
                search: value || undefined,
                technology: props.filters.technology || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
});

function selectTechnology(slug) {
    router.get(
        '/projects',
        {
            search: search.value || undefined,
            technology: props.filters.technology === slug ? undefined : slug,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <main class="min-h-screen bg-background px-5 py-12 sm:px-8 lg:px-10">
        <section class="mx-auto w-full max-w-7xl">
            <Link
                href="/"
                class="text-sm font-semibold text-text-muted transition
                       hover:text-white"
            >
                ← На главную
            </Link>

            <header class="mt-10 max-w-3xl">
                <p
                    class="text-xs font-semibold uppercase tracking-[0.18em]
                           text-accent"
                >
                    Каталог
                </p>

                <h1 class="mt-4 text-4xl font-semibold text-text sm:text-6xl">
                    Все проекты
                </h1>

                <p class="mt-5 leading-8 text-text-muted">
                    Полный список опубликованных проектов с поиском и фильтром
                    по технологиям.
                </p>
            </header>

            <div
                class="mt-10 rounded-3xl border border-border bg-surface/75
                       p-5 shadow-2xl shadow-black/30 sm:p-6"
            >
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-center">
                    <label class="block">
                        <span class="text-sm font-medium text-text">Поиск</span>
                        <input
                            v-model="search"
                            type="search"
                            class="mt-2 w-full rounded-2xl border border-border
                                   bg-background/70 px-4 py-3 text-text outline-none
                                   transition placeholder:text-text-muted/60
                                   focus:border-accent"
                            placeholder="Название или описание проекта"
                        >
                    </label>

                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="technology in technologies"
                            :key="technology.slug"
                            type="button"
                            class="rounded-full border px-4 py-2 text-sm
                                   font-semibold transition"
                            :class="
                                filters.technology === technology.slug
                                    ? 'border-accent bg-accent/10 text-accent'
                                    : 'border-border-bright/60 text-text-muted hover:border-accent/70 hover:text-white'
                            "
                            @click="selectTechnology(technology.slug)"
                        >
                            {{ technology.name }}
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-if="projects.data.length > 0"
                class="mt-10 space-y-8 lg:space-y-10"
            >
                <ProjectCard
                    v-for="(project, index) in projects.data"
                    :key="project.id"
                    :project="project"
                    :reverse="index % 2 === 1"
                />
            </div>

            <div
                v-else
                class="mt-10 rounded-3xl border border-dashed border-border
                       bg-surface/50 p-10 text-center"
            >
                <h2 class="text-2xl font-semibold text-text">
                    Проекты не найдены
                </h2>
                <p class="mt-3 text-text-muted">
                    Попробуйте изменить запрос или сбросить фильтр технологии.
                </p>
            </div>
        </section>
    </main>
</template>
