<script setup>
import ContactForm from '@/Components/Contact/Form.vue';
import ProjectCard from '@/Components/Project/Card.vue';
import SiteHeader from '@/Components/SiteHeader.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    projects: {
        type: Array,
        required: true,
    },

    meta: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const adminHref = computed(() => (page.props.auth?.user ? '/admin' : '/login'));
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <main class="relative min-h-screen overflow-hidden bg-background">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-[42rem]
                   bg-[radial-gradient(circle_at_72%_0%,rgba(139,92,246,0.28),transparent_34rem)]"
        />
        <div
            class="pointer-events-none absolute left-1/2 top-0 h-px w-[70rem]
                   -translate-x-1/2 bg-gradient-to-r from-transparent
                   via-accent/60 to-transparent"
        />

        <SiteHeader />

        <section
            class="mx-auto w-full max-w-7xl px-5 py-12
                   sm:px-8 sm:py-14
                   lg:px-10 lg:py-16"
        >
            <header
                class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_26rem]
                       lg:items-end"
            >
                <div class="max-w-3xl">
                    <p
                        class="mb-5 inline-flex rounded-full border
                               border-primary/40 bg-primary/10 px-4 py-2
                               text-xs font-semibold uppercase
                               tracking-[0.18em] text-violet-200"
                    >
                        PHP / Laravel портфолио
                    </p>

                    <h1
                        class="text-4xl font-semibold leading-tight text-text
                               sm:text-5xl
                               lg:text-6xl lg:leading-[1.04]"
                    >
                        Разбираю задачи и довожу их до работающего продукта
                    </h1>

                    <p
                        class="mt-6 max-w-2xl text-base leading-7
                               text-text-muted sm:text-lg sm:leading-8"
                    >
                        Здесь собраны проекты, где важны не только экраны, но и
                        backend: модели, связи, валидация, тесты, деплой и
                        понятная структура кода.
                    </p>
                </div>

                <aside
                    class="rounded-2xl border border-border bg-surface/75 p-5
                           shadow-2xl shadow-black/30 backdrop-blur"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.18em]
                               text-accent"
                    >
                        Быстрая проверка
                    </p>

                    <div class="mt-5 space-y-4">
                        <div class="flex gap-3">
                            <span
                                class="mt-1 flex h-6 w-6 shrink-0 items-center
                                       justify-center rounded-full
                                       border border-accent/60 text-xs
                                       font-semibold text-accent"
                            >
                                1
                            </span>
                            <div>
                                <p class="font-semibold text-text">
                                    Откройте демо-админку
                                </p>
                                <p class="mt-1 text-sm leading-6 text-text-muted">
                                    Можно посмотреть CRUD, роли и защиту данных.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <span
                                class="mt-1 flex h-6 w-6 shrink-0 items-center
                                       justify-center rounded-full
                                       border border-accent/60 text-xs
                                       font-semibold text-accent"
                            >
                                2
                            </span>
                            <div>
                                <p class="font-semibold text-text">
                                    Проверьте проект PortfolioHub
                                </p>
                                <p class="mt-1 text-sm leading-6 text-text-muted">
                                    Backend, галерея, фильтры, формы и тесты
                                    работают как единый продукт.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <span
                                class="mt-1 flex h-6 w-6 shrink-0 items-center
                                       justify-center rounded-full
                                       border border-success/60 text-xs
                                       font-semibold text-success"
                            >
                                ✓
                            </span>
                            <div>
                                <p class="font-semibold text-text">
                                    Код готов к review
                                </p>
                                <p class="mt-1 text-sm leading-6 text-text-muted">
                                    Есть миграции, сидеры, feature tests и CI.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            :href="adminHref"
                            class="rounded-full bg-primary px-5 py-3 text-sm
                                   font-semibold text-white shadow-lg
                                   shadow-primary/25 transition
                                   hover:bg-violet-500"
                        >
                            Демо-админка
                        </Link>
                        <a
                            href="https://github.com/SaneKKelasev/portfolio"
                            target="_blank"
                            rel="noreferrer"
                            class="rounded-full border border-border-bright/70
                                   px-5 py-3 text-sm font-semibold
                                   text-text-muted transition
                                   hover:border-accent/70 hover:text-white"
                        >
                            GitHub
                        </a>
                    </div>
                </aside>
            </header>

            <div class="mt-10 space-y-8 sm:mt-12 lg:space-y-10">
                <ProjectCard
                    v-for="(project, index) in projects"
                    :key="project.id"
                    :project="project"
                    :reverse="index % 2 === 1"
                />
            </div>

            <div class="mt-10 flex justify-center">
                <Link
                    href="/projects"
                    class="inline-flex rounded-full border border-border-bright/70
                           px-6 py-3 text-sm font-semibold text-text-muted
                           transition hover:border-accent/70 hover:text-white"
                >
                    Смотреть все проекты
                </Link>
            </div>
        </section>

        <ContactForm />
    </main>
</template>
