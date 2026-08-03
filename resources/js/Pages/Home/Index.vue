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
    <main>
        <section>
            <header>
                <p>Мои проекты</p>

                <h1>
                    Проекты, которые я создаю
                </h1>

                <p>
                    Каждый проект — это решение реальной задачи с вниманием
                    к архитектуре, производительности и удобству использования.
                </p>
            </header>

            <div>
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