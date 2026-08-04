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
    <main class="relative min-h-screen overflow-hidden">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[700px]
                   bg-[radial-gradient(circle_at_70%_15%,rgba(124,58,237,0.18),transparent_40%)]"
        />

        <section
            class="mx-auto w-full max-w-7xl px-5 py-20
                   sm:px-8 sm:py-24
                   lg:px-10 lg:py-32"
        >
            <header class="max-w-2xl">
                <p
                    class="mb-5 text-sm font-semibold uppercase tracking-[0.18em]
                           text-violet-400"
                >
                    Мои проекты
                </p>

                <h1
                    class="text-4xl font-semibold tracking-tight text-white
                           sm:text-5xl
                           lg:text-6xl lg:leading-[1.08]"
                >
                    Проекты, которые я создаю
                </h1>

                <p
                    class="mt-6 max-w-xl text-base leading-7 text-slate-400
                           sm:text-lg sm:leading-8"
                >
                    Каждый проект — это решение реальной задачи с вниманием
                    к архитектуре, производительности и удобству использования.
                </p>
            </header>

            <div class="mt-14 space-y-8 sm:mt-20 lg:space-y-10">
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