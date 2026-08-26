<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
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
const successMessage = computed(() => page.props.flash?.success ?? null);

function destroyProject(project) {
    if (project.is_protected) {
        return;
    }

    if (confirm(`Удалить проект "${project.title}"?`)) {
        router.delete(`/admin/projects/${project.id}`);
    }
}
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <AdminLayout>
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-[0.18em]
                           text-accent"
                >
                    Проекты
                </p>
                <h1 class="mt-4 text-4xl font-semibold text-text">
                    Управление проектами
                </h1>
            </div>

            <Link
                href="/admin/projects/create"
                class="rounded-full bg-primary px-5 py-3 text-sm font-semibold
                       text-white shadow-lg shadow-primary/25 transition
                       hover:bg-violet-500"
            >
                Добавить проект
            </Link>
        </header>

        <div
            v-if="successMessage"
            class="mt-6 rounded-2xl border border-success/40 bg-success/10
                   px-4 py-3 text-sm font-semibold text-success"
        >
            {{ successMessage }}
        </div>

        <section
            class="mt-8 overflow-hidden rounded-3xl border border-border
                   bg-surface/75"
        >
            <div
                v-for="project in projects"
                :key="project.id"
                class="grid gap-4 border-b border-border p-5 last:border-b-0
                       lg:grid-cols-[1fr_auto] lg:items-center"
            >
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-xl font-semibold text-text">
                            {{ project.title }}
                        </h2>
                        <span
                            class="rounded-full border px-3 py-1 text-xs
                                   font-semibold"
                            :class="
                                project.published
                                    ? 'border-success/50 text-success'
                                    : 'border-border-bright/60 text-text-muted'
                            "
                        >
                            {{ project.published ? 'опубликован' : 'черновик' }}
                        </span>
                        <span
                            v-if="project.is_protected"
                            class="rounded-full border border-primary/50 px-3 py-1
                                   text-xs font-semibold text-violet-200"
                        >
                            защищён
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-text-muted">
                        /projects/{{ project.slug }}
                    </p>
                    <p class="mt-2 text-sm text-text-muted">
                        {{ project.technologies.join(', ') || 'Без технологий' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link
                        v-if="project.published"
                        :href="`/projects/${project.slug}`"
                        class="rounded-full border border-border px-4 py-2
                               text-sm font-semibold text-text-muted
                               transition hover:border-accent/70 hover:text-white"
                    >
                        Открыть
                    </Link>
                    <span
                        v-else
                        class="rounded-full border border-border px-4 py-2
                               text-sm font-semibold text-text-muted/70"
                    >
                        Не опубликован
                    </span>
                    <Link
                        v-if="!project.is_protected"
                        :href="`/admin/projects/${project.id}/edit`"
                        class="rounded-full border border-border px-4 py-2
                               text-sm font-semibold text-text-muted
                               transition hover:border-accent/70 hover:text-white"
                    >
                        Редактировать
                    </Link>
                    <span
                        v-else
                        class="rounded-full border border-border px-4 py-2
                               text-sm font-semibold text-text-muted/70"
                    >
                        Только просмотр
                    </span>
                    <button
                        v-if="!project.is_protected"
                        type="button"
                        class="rounded-full border border-rose-400/50 px-4 py-2
                               text-sm font-semibold text-rose-300 transition
                               hover:border-rose-300 hover:text-rose-200"
                        @click="destroyProject(project)"
                    >
                        Удалить
                    </button>
                </div>
            </div>

            <p v-if="projects.length === 0" class="p-8 text-text-muted">
                Проектов пока нет.
            </p>
        </section>
    </AdminLayout>
</template>
