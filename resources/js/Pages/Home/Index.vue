<script setup>
import ProjectCard from '@/Components/Project/Card.vue';

defineProps({
    projects: {
        type: Array,
        required: true,
    },
});
</script>

<template>
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
                        Мои проекты
                    </p>

                    <h1
                        class="text-4xl font-semibold leading-tight text-text
                               sm:text-5xl
                               lg:text-6xl lg:leading-[1.04]"
                    >
                        Проекты, которые я создаю
                    </h1>

                    <p
                        class="mt-6 max-w-2xl text-base leading-7
                               text-text-muted sm:text-lg sm:leading-8"
                    >
                        Каждый проект — это решение реальной задачи с вниманием
                        к архитектуре, производительности и удобству
                        использования.
                    </p>
                </div>

                <div
                    class="grid grid-cols-3 overflow-hidden rounded-2xl border
                           border-border bg-surface/75 shadow-2xl
                           shadow-black/30 backdrop-blur"
                >
                    <div class="border-r border-border p-5">
                        <p class="text-2xl font-semibold text-text">Laravel</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-text-muted">
                            backend
                        </p>
                    </div>

                    <div class="border-r border-border p-5">
                        <p class="text-2xl font-semibold text-accent">Vue</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-text-muted">
                            inertia
                        </p>
                    </div>

                    <div class="p-5">
                        <p class="text-2xl font-semibold text-success">Tests</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-text-muted">
                            ci ready
                        </p>
                    </div>
                </div>
            </header>

            <div class="mt-10 space-y-8 sm:mt-12 lg:space-y-10">
                <ProjectCard
                    v-for="(project, index) in projects"
                    :key="project.id"
                    :project="project"
                    :reverse="index % 2 === 1"
                />
            </div>
        </section>
    </main>
</template>
