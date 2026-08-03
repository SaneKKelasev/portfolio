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
    <article>
        <div>
            <div>
                <h2>
                    {{ project.title }}
                </h2>

                <p>
                    {{ project.description }}
                </p>

                <ul>
                    <li
                        v-for="technology in project.technologies"
                        :key="technology.slug"
                    >
                        <TechnologyBadge :technology="technology" />
                    </li>
                </ul>

                <div>
                    <a
                        v-if="project.website_url"
                        :href="project.website_url"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Открыть сайт
                    </a>

                    <a
                        v-if="project.repository_url"
                        :href="project.repository_url"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Исходный код
                    </a>
                </div>
            </div>

            <ProjectGallery
                :images="project.images"
                :reverse="reverse"
            />
        </div>
    </article>
</template>