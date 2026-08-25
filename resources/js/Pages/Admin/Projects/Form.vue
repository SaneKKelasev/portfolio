<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },

    technologies: {
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
const isEditing = computed(() => props.project.id !== null);
const uploadInput = ref(null);

const form = useForm({
    title: props.project.title ?? '',
    slug: props.project.slug ?? '',
    description: props.project.description ?? '',
    role: props.project.role ?? '',
    problem: props.project.problem ?? '',
    solution: props.project.solution ?? '',
    result: props.project.result ?? '',
    website_url: props.project.website_url ?? '',
    repository_url: props.project.repository_url ?? '',
    started_at: props.project.started_at ?? '',
    finished_at: props.project.finished_at ?? '',
    published: props.project.published ?? false,
    sort_order: props.project.sort_order ?? 100,
    technologies: props.project.technologies ?? [],
    images: props.project.images ?? [],
    uploaded_images: [],
});

const uploadErrors = computed(() => Object.entries(form.errors)
    .filter(([key]) => key.startsWith('uploaded_images'))
    .map(([, message]) => message));

function toggleTechnology(id) {
    if (form.technologies.includes(id)) {
        form.technologies = form.technologies.filter((technologyId) => technologyId !== id);
        return;
    }

    form.technologies = [...form.technologies, id];
}

function removeImage(index) {
    form.images.splice(index, 1);
}

function selectUploads(event) {
    form.uploaded_images = Array.from(event.target.files ?? []);
}

function normalizedImages(images) {
    return images.filter((image) => (image.path ?? '').trim() !== '');
}

function submit() {
    form.transform((data) => ({
        ...data,
        images: normalizedImages(data.images),
        ...(isEditing.value ? { _method: 'put' } : {}),
    }));

    if (isEditing.value) {
        form.post(`/admin/projects/${props.project.id}`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.uploaded_images = [];
                if (uploadInput.value) {
                    uploadInput.value.value = '';
                }
            },
        });
        return;
    }

    form.post('/admin/projects', {
        forceFormData: true,
    });
}
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <AdminLayout>
        <header>
            <Link
                href="/admin/projects"
                class="text-sm font-semibold text-text-muted transition
                       hover:text-white"
            >
                ← К проектам
            </Link>

            <h1 class="mt-5 text-4xl font-semibold text-text">
                {{ isEditing ? 'Редактирование проекта' : 'Новый проект' }}
            </h1>
        </header>

        <div
            v-if="successMessage"
            class="mt-6 rounded-2xl border border-success/40 bg-success/10
                   px-4 py-3 text-sm font-semibold text-success"
        >
            {{ successMessage }}
        </div>

        <form class="mt-8 space-y-8" @submit.prevent="submit">
            <section
                class="grid gap-5 rounded-3xl border border-border
                       bg-surface/75 p-6 lg:grid-cols-2"
            >
                <label class="block">
                    <span class="text-sm font-semibold text-text">Название</span>
                    <input
                        v-model="form.title"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                    <span v-if="form.errors.title" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.title }}
                    </span>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Slug</span>
                    <input
                        v-model="form.slug"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                    <span v-if="form.errors.slug" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.slug }}
                    </span>
                </label>

                <label class="block lg:col-span-2">
                    <span class="text-sm font-semibold text-text">Описание</span>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    />
                    <span v-if="form.errors.description" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.description }}
                    </span>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Роль</span>
                    <input
                        v-model="form.role"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Порядок</span>
                    <input
                        v-model="form.sort_order"
                        type="number"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Сайт</span>
                    <input
                        v-model="form.website_url"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Репозиторий</span>
                    <input
                        v-model="form.repository_url"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Начало</span>
                    <input
                        v-model="form.started_at"
                        type="date"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-text">Завершение</span>
                    <input
                        v-model="form.finished_at"
                        type="date"
                        class="mt-2 w-full rounded-2xl border border-border
                               bg-background/70 px-4 py-3 text-text outline-none
                               focus:border-accent"
                    >
                </label>

                <label class="flex items-center gap-3 lg:col-span-2">
                    <input v-model="form.published" type="checkbox">
                    <span class="text-sm font-semibold text-text">
                        Опубликован
                    </span>
                </label>
            </section>

            <section
                class="grid gap-5 rounded-3xl border border-border
                       bg-surface/75 p-6 lg:grid-cols-3"
            >
                <label class="block">
                    <span class="text-sm font-semibold text-text">Задача</span>
                    <textarea v-model="form.problem" rows="6" class="mt-2 w-full rounded-2xl border border-border bg-background/70 px-4 py-3 text-text outline-none focus:border-accent" />
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-text">Решение</span>
                    <textarea v-model="form.solution" rows="6" class="mt-2 w-full rounded-2xl border border-border bg-background/70 px-4 py-3 text-text outline-none focus:border-accent" />
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-text">Результат</span>
                    <textarea v-model="form.result" rows="6" class="mt-2 w-full rounded-2xl border border-border bg-background/70 px-4 py-3 text-text outline-none focus:border-accent" />
                </label>
            </section>

            <section class="rounded-3xl border border-border bg-surface/75 p-6">
                <h2 class="text-xl font-semibold text-text">Технологии</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button
                        v-for="technology in technologies"
                        :key="technology.id"
                        type="button"
                        class="rounded-full border px-4 py-2 text-sm font-semibold"
                        :class="
                            form.technologies.includes(technology.id)
                                ? 'border-accent bg-accent/10 text-accent'
                                : 'border-border-bright/60 text-text-muted'
                        "
                        @click="toggleTechnology(technology.id)"
                    >
                        {{ technology.name }}
                    </button>
                </div>
            </section>

            <section class="rounded-3xl border border-border bg-surface/75 p-6">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-semibold text-text">Изображения</h2>
                </div>

                <div
                    class="mt-5 rounded-2xl border border-dashed border-border-bright/60
                           bg-background/45 p-4"
                >
                    <label class="block">
                        <span class="text-sm font-semibold text-text">
                            Загрузить изображения
                        </span>
                        <input
                            ref="uploadInput"
                            type="file"
                            multiple
                            accept="image/png,image/jpeg,image/webp"
                            class="mt-3 block w-full text-sm text-text-muted
                                   file:mr-4 file:rounded-full file:border-0
                                   file:bg-primary file:px-4 file:py-2
                                   file:text-sm file:font-semibold file:text-white
                                   hover:file:bg-violet-500"
                            @change="selectUploads"
                        >
                    </label>

                    <p class="mt-3 text-sm text-text-muted">
                        Файлы сохранятся автоматически, а приложение само подставит их в галерею проекта.
                    </p>

                    <div
                        v-if="form.uploaded_images.length > 0"
                        class="mt-3 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="file in form.uploaded_images"
                            :key="file.name"
                            class="rounded-full border border-border-bright/60 px-3 py-1
                                   text-xs font-semibold text-text"
                        >
                            {{ file.name }}
                        </span>
                    </div>

                    <p
                        v-if="form.errors.uploaded_images"
                        class="mt-3 text-sm text-rose-300"
                    >
                        {{ form.errors.uploaded_images }}
                    </p>

                    <p
                        v-for="error in uploadErrors"
                        :key="error"
                        class="mt-2 text-sm text-rose-300"
                    >
                        {{ error }}
                    </p>
                </div>

                <div class="mt-4 space-y-4">
                    <div
                        v-for="(image, index) in form.images"
                        :key="index"
                        class="grid gap-3 rounded-2xl border border-border p-4
                               lg:grid-cols-[10rem_1fr_8rem_auto]"
                    >
                        <div
                            class="aspect-video overflow-hidden rounded-2xl
                                   border border-border bg-background/70"
                        >
                            <img
                                v-if="image.url"
                                :src="image.url"
                                :alt="image.alt || 'Изображение проекта'"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <input v-model="image.alt" placeholder="Описание изображения" class="rounded-2xl border border-border bg-background/70 px-4 py-3 text-text outline-none focus:border-accent">
                        <input v-model="image.sort_order" type="number" class="rounded-2xl border border-border bg-background/70 px-4 py-3 text-text outline-none focus:border-accent">
                        <button type="button" class="text-sm font-semibold text-rose-300" @click="removeImage(index)">
                            Удалить
                        </button>
                    </div>
                </div>
            </section>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-primary px-6 py-3 text-sm font-semibold
                       text-white shadow-lg shadow-primary/25 transition
                       hover:bg-violet-500 disabled:opacity-60"
            >
                {{ form.processing ? 'Сохранение...' : 'Сохранить проект' }}
            </button>
        </form>
    </AdminLayout>
</template>
