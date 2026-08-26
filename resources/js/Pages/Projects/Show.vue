<script setup>
import ProjectGallery from '@/Components/Project/Gallery.vue';
import SiteHeader from '@/Components/SiteHeader.vue';
import TechnologyBadge from '@/Components/Project/TechnologyBadge.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    project: {
        type: Object,
        required: true,
    },

    meta: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <main class="min-h-screen bg-background">
        <SiteHeader />

        <article class="mx-auto w-full max-w-7xl px-5 py-12 sm:px-8 lg:px-10">
            <Link
                href="/projects"
                class="text-sm font-semibold text-text-muted transition
                       hover:text-white"
            >
                ← Все проекты
            </Link>

            <section class="mt-10 grid gap-10 lg:grid-cols-[0.75fr_1.25fr]">
                <div
                    class="rounded-3xl border border-border-bright/70
                           bg-surface/80 p-6 shadow-2xl shadow-black/30
                           sm:p-8 lg:p-10"
                >
                    <p
                        class="text-xs font-semibold uppercase
                               tracking-[0.18em] text-accent"
                    >
                        Проект
                    </p>

                    <h1
                        class="mt-5 text-4xl font-semibold text-text
                               sm:text-6xl"
                    >
                        {{ project.title }}
                    </h1>

                    <p class="mt-6 leading-8 text-text-muted">
                        {{ project.description }}
                    </p>

                    <p
                        v-if="!project.website_url && !project.repository_url"
                        class="mt-4 inline-flex rounded-full border border-accent/40
                               bg-accent/10 px-3 py-1 text-xs font-semibold
                               uppercase tracking-wide text-accent"
                    >
                        Демо-кейс для демонстрации структуры портфолио
                    </p>

                    <ul class="mt-6 flex flex-wrap gap-2">
                        <li
                            v-for="technology in project.technologies"
                            :key="technology.slug"
                        >
                            <TechnologyBadge :technology="technology" />
                        </li>
                    </ul>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a
                            v-if="project.website_url"
                            :href="project.website_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-full bg-primary px-5 py-3 text-sm
                                   font-semibold text-white shadow-lg
                                   shadow-primary/25 transition
                                   hover:bg-violet-500"
                        >
                            Открыть сайт
                        </a>

                        <a
                            v-if="project.repository_url"
                            :href="project.repository_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-full border border-border-bright/70
                                   px-5 py-3 text-sm font-semibold
                                   text-text-muted transition hover:border-accent/70
                                   hover:text-white"
                        >
                            Исходный код
                        </a>
                    </div>

                    <dl
                        class="mt-10 grid grid-cols-2 gap-4 border-t
                               border-border pt-6 text-sm"
                    >
                        <div>
                            <dt class="text-text-muted">Начало</dt>
                            <dd class="mt-1 font-semibold text-text">
                                {{ project.started_at ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-text-muted">Завершение</dt>
                            <dd class="mt-1 font-semibold text-text">
                                {{ project.finished_at ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <ProjectGallery :images="project.images" />
                </div>
            </section>

            <section class="mt-10 grid gap-6 lg:grid-cols-3">
                <div
                    v-if="project.problem"
                    class="rounded-3xl border border-border bg-surface/70 p-6"
                >
                    <h2 class="text-xl font-semibold text-text">Задача</h2>
                    <p class="mt-4 leading-7 text-text-muted">
                        {{ project.problem }}
                    </p>
                </div>

                <div
                    v-if="project.solution"
                    class="rounded-3xl border border-border bg-surface/70 p-6"
                >
                    <h2 class="text-xl font-semibold text-text">Решение</h2>
                    <p class="mt-4 leading-7 text-text-muted">
                        {{ project.solution }}
                    </p>
                </div>

                <div
                    v-if="project.result"
                    class="rounded-3xl border border-border bg-surface/70 p-6"
                >
                    <h2 class="text-xl font-semibold text-text">Результат</h2>
                    <p class="mt-4 leading-7 text-text-muted">
                        {{ project.result }}
                    </p>
                </div>
            </section>
        </article>
    </main>
</template>
