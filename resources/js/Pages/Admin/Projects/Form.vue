<script setup>
import FormCheckbox from '@/Components/Form/Checkbox.vue';
import FormInput from '@/Components/Form/Input.vue';
import FormTextarea from '@/Components/Form/Textarea.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
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

const maxProjectImages = 5;
const isEditing = computed(() => props.project.id !== null);
const uploadInput = ref(null);
const isDragging = ref(false);
const galleryError = ref('');
let uploadedImageId = 0;

const form = useForm({
    title: props.project.title ?? '',
    description: props.project.description ?? '',
    problem: props.project.problem ?? '',
    solution: props.project.solution ?? '',
    result: props.project.result ?? '',
    website_url: props.project.website_url ?? '',
    repository_url: props.project.repository_url ?? '',
    started_at: props.project.started_at ?? '',
    finished_at: props.project.finished_at ?? '',
    published: props.project.published ?? false,
    technologies: props.project.technologies ?? [],
    images: [],
    uploaded_images: [],
    uploaded_images_meta: [],
});
const hasErrors = computed(() => Object.keys(form.errors).length > 0);

const galleryItems = ref((props.project.images ?? []).map((image, index) => ({
    id: `existing-${image.path}`,
    type: 'existing',
    path: image.path,
    large_path: image.large_path,
    card_path: image.card_path,
    thumb_path: image.thumb_path,
    url: image.url,
    alt: image.alt ?? '',
    sort_order: image.sort_order ?? index + 1,
})));

const datePickerClass = 'portfolio-date-picker mt-2';
const labelClass = 'flex items-center gap-2 text-sm font-semibold text-text';
const requiredBadgeClass = 'rounded-full bg-accent/10 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-wide text-accent';
const optionalBadgeClass = 'rounded-full border border-border px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-wide text-text-muted';
const datePickerFormats = {
    input: 'dd.MM.yyyy',
};
const datePickerTimeConfig = {
    enableTimePicker: false,
};
const today = new Date();
const russianDayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

const uploadErrors = computed(() => Object.entries(form.errors)
    .filter(([key]) => key.startsWith('uploaded_images'))
    .map(([, message]) => message));
const galleryErrors = computed(() => [...new Set([
    galleryError.value,
    ...uploadErrors.value,
].filter(Boolean))]);

function toggleTechnology(id) {
    if (form.technologies.includes(id)) {
        form.technologies = form.technologies.filter((technologyId) => technologyId !== id);
        return;
    }

    form.technologies = [...form.technologies, id];
}

function removeImage(index) {
    const [item] = galleryItems.value.splice(index, 1);
    galleryError.value = '';

    if (item?.type === 'upload' && item.preview) {
        URL.revokeObjectURL(item.preview);
    }
}

function addFiles(files) {
    galleryError.value = '';
    const imageFiles = Array.from(files).filter((file) => file.type.startsWith('image/'));
    const remainingSlots = Math.max(maxProjectImages - galleryItems.value.length, 0);

    imageFiles
        .slice(0, remainingSlots)
        .forEach((file) => {
            uploadedImageId += 1;
            galleryItems.value.push({
                id: `upload-${uploadedImageId}`,
                type: 'upload',
                file,
                preview: URL.createObjectURL(file),
                alt: imageAltFromFileName(file.name),
            });
        });

    if (imageFiles.length > remainingSlots) {
        galleryError.value = `Можно добавить не больше ${maxProjectImages} изображений вместе с главным.`;
    }
}

function imageAltFromFileName(fileName) {
    const alt = fileName.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ').trim();

    if (alt.length < 3 || /^\d+$/.test(alt)) {
        return '';
    }

    return alt;
}

function selectUploads(event) {
    addFiles(event.target.files ?? []);

    if (uploadInput.value) {
        uploadInput.value.value = '';
    }
}

function openUploadDialog() {
    if (galleryItems.value.length >= maxProjectImages) {
        galleryError.value = `Можно добавить не больше ${maxProjectImages} изображений вместе с главным.`;

        return;
    }

    uploadInput.value?.click();
}

function dropUploads(event) {
    isDragging.value = false;
    addFiles(event.dataTransfer.files ?? []);
}

function moveImage(index, direction) {
    const nextIndex = index + direction;

    if (nextIndex < 0 || nextIndex >= galleryItems.value.length) {
        return;
    }

    const items = [...galleryItems.value];
    [items[index], items[nextIndex]] = [items[nextIndex], items[index]];
    galleryItems.value = items;
}

function makeCover(index) {
    if (index === 0) {
        return;
    }

    const items = [...galleryItems.value];
    const [item] = items.splice(index, 1);
    items.unshift(item);
    galleryItems.value = items;
}

function normalizedImages(images) {
    return images.filter((image) => (image.path ?? '').trim() !== '');
}

function syncGalleryPayload() {
    const existingImages = [];
    const uploadedImages = [];
    const uploadedImagesMeta = [];

    galleryItems.value.forEach((item, index) => {
        const imageData = {
            alt: item.alt,
            sort_order: index + 1,
        };

        if (item.type === 'existing') {
            existingImages.push({
                ...imageData,
                path: item.path,
                large_path: item.large_path,
                card_path: item.card_path,
                thumb_path: item.thumb_path,
            });
            return;
        }

        uploadedImages.push(item.file);
        uploadedImagesMeta.push(imageData);
    });

    form.images = existingImages;
    form.uploaded_images = uploadedImages;
    form.uploaded_images_meta = uploadedImagesMeta;
}

function submit() {
    if (galleryItems.value.length > maxProjectImages) {
        galleryError.value = `Можно добавить не больше ${maxProjectImages} изображений вместе с главным.`;

        return;
    }

    syncGalleryPayload();

    form.transform((data) => ({
        ...data,
        images: normalizedImages(data.images),
        ...(isEditing.value ? { _method: 'put' } : {}),
    }));

    if (isEditing.value) {
        form.post(`/admin/projects/${props.project.id}`, {
            forceFormData: true,
            preserveScroll: true,
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
            v-if="hasErrors"
            class="mt-6 rounded-2xl border border-rose-400/40 bg-rose-400/10
                   px-4 py-3 text-sm font-semibold text-rose-200"
        >
            Проверьте поля формы. Некоторые данные нужно исправить перед сохранением.
        </div>

        <form class="mt-8 space-y-8" @submit.prevent="submit">
            <section
                class="grid gap-5 rounded-3xl border border-border
                       bg-surface/75 p-6 lg:grid-cols-2"
            >
                <label class="block lg:col-span-2">
                    <span :class="labelClass">
                        Название
                        <span :class="requiredBadgeClass">Обязательно</span>
                    </span>
                    <FormInput
                        v-model="form.title"
                        required
                        :invalid="Boolean(form.errors.title || form.errors.slug)"
                    />
                    <span v-if="form.errors.title" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.title }}
                    </span>
                    <span v-if="form.errors.slug" class="mt-2 block text-sm text-rose-300">
                        URL-адрес для этого названия уже занят.
                    </span>
                </label>

                <label class="block lg:col-span-2">
                    <span :class="labelClass">
                        Описание
                        <span :class="requiredBadgeClass">Обязательно</span>
                    </span>
                    <FormTextarea
                        v-model="form.description"
                        required
                        rows="4"
                        :invalid="Boolean(form.errors.description)"
                    />
                    <span v-if="form.errors.description" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.description }}
                    </span>
                </label>

                <label class="block">
                    <span :class="labelClass">
                        Сайт
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>
                    <FormInput
                        v-model="form.website_url"
                        :invalid="Boolean(form.errors.website_url)"
                    />
                    <span v-if="form.errors.website_url" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.website_url }}
                    </span>
                </label>

                <label class="block">
                    <span :class="labelClass">
                        Репозиторий
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>
                    <FormInput
                        v-model="form.repository_url"
                        :invalid="Boolean(form.errors.repository_url)"
                    />
                    <span v-if="form.errors.repository_url" class="mt-2 block text-sm text-rose-300">
                        {{ form.errors.repository_url }}
                    </span>
                </label>

                <label class="block">
                    <span :class="labelClass">
                        Начало
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>

                   <VueDatePicker
                        v-model="form.started_at"
                        :class="datePickerClass"
                        model-type="yyyy-MM-dd"
                        :formats="datePickerFormats"
                        :day-names="russianDayNames"
                        :max-date="today"
                        :time-config="datePickerTimeConfig"
                        :auto-apply="true"
                        :clearable="true"
                        dark
                        placeholder="Выберите дату начала"
                    />

                    <span
                        v-if="form.errors.started_at"
                        class="mt-2 block text-sm text-rose-300"
                    >
                        {{ form.errors.started_at }}
                    </span>
                </label>

               <label class="block">
                    <span :class="labelClass">
                        Завершение
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>

                   <VueDatePicker
                        v-model="form.finished_at"
                        :class="datePickerClass"
                        model-type="yyyy-MM-dd"
                        :formats="datePickerFormats"
                        :max-date="today"
                        :time-config="datePickerTimeConfig"
                        :auto-apply="true"
                        :clearable="true"
                        dark
                        placeholder="Выберите дату завершения"
                    />

                    <span
                        v-if="form.errors.finished_at"
                        class="mt-2 block text-sm text-rose-300"
                    >
                        {{ form.errors.finished_at }}
                    </span>
                </label>

                <label class="flex items-center gap-3 lg:col-span-2">
                    <FormCheckbox v-model="form.published" />
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
                    <span :class="labelClass">
                        Задача
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>
                    <FormTextarea v-model="form.problem" rows="6" class="min-h-40 max-h-80" />
                </label>
                <label class="block">
                    <span :class="labelClass">
                        Решение
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>
                    <FormTextarea v-model="form.solution" rows="6" class="min-h-40 max-h-80" />
                </label>
                <label class="block">
                    <span :class="labelClass">
                        Результат
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </span>
                    <FormTextarea v-model="form.result" rows="6" class="min-h-40 max-h-80" />
                </label>
            </section>

            <section class="rounded-3xl border border-border bg-surface/75 p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="flex items-center gap-2 text-xl font-semibold text-text">
                        Технологии
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </h2>
                    <Link
                        href="/admin/technologies"
                        class="text-sm font-semibold text-accent transition
                               hover:text-cyan-200"
                    >
                        Управлять технологиями
                    </Link>
                </div>
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
                    <h2 class="flex items-center gap-2 text-xl font-semibold text-text">
                        Изображения
                        <span :class="optionalBadgeClass">Необязательно</span>
                    </h2>
                </div>

                <div
                    class="mt-5 rounded-2xl border border-dashed border-border-bright/60
                           bg-background/45 p-4"
                    :class="isDragging ? 'border-accent bg-accent/10' : ''"
                    @dragenter.prevent="isDragging = true"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="dropUploads"
                >
                    <div class="block">
                        <p class="text-sm font-semibold text-text">
                            Перетащите изображения сюда или выберите файлы
                        </p>
                        <input
                            ref="uploadInput"
                            type="file"
                            multiple
                            accept="image/png,image/jpeg,image/webp"
                            class="sr-only"
                            @change="selectUploads"
                        >
                        <button
                            type="button"
                            :disabled="galleryItems.length >= maxProjectImages"
                            class="mt-4 rounded-full bg-primary px-5 py-3
                                   text-sm font-semibold text-white shadow-lg
                                   shadow-primary/25 transition
                                   hover:bg-violet-500 disabled:cursor-not-allowed
                                   disabled:opacity-50"
                            @click="openUploadDialog"
                        >
                            Выбрать изображения
                        </button>
                    </div>

                    <p class="mt-3 text-sm text-text-muted">
                        Можно добавить до {{ maxProjectImages }} изображений вместе с главным. Первое изображение в списке станет главным.
                    </p>

                    <p
                        v-for="error in galleryErrors"
                        :key="error"
                        class="mt-2 text-sm text-rose-300"
                    >
                        {{ error }}
                    </p>
                </div>

                <div class="mt-4 space-y-4">
                    <div
                        v-for="(image, index) in galleryItems"
                        :key="image.id"
                        class="grid gap-4 rounded-2xl border border-border
                               bg-background/25 p-4
                               lg:grid-cols-[12rem_minmax(0,1fr)]
                               lg:items-center"
                    >
                        <div
                            class="relative aspect-video overflow-hidden rounded-2xl
                                   border border-border bg-background/70"
                        >
                            <img
                                :src="image.url ?? image.preview"
                                :alt="image.alt || 'Изображение проекта'"
                                class="h-full w-full object-cover"
                            >
                            <span
                                class="absolute left-2 top-2 rounded-full border
                                       bg-background/80 px-3 py-1 text-xs
                                       font-semibold backdrop-blur"
                                :class="
                                    index === 0
                                        ? 'border-accent/60 text-accent'
                                        : 'border-border-bright/60 text-text-muted'
                                "
                            >
                                {{ index === 0 ? 'Главное' : `#${index + 1}` }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <label class="block">
                                <span class="text-xs font-semibold text-text-muted">
                                    Описание изображения
                                </span>
                                <FormInput v-model="image.alt" placeholder="Например: Главный экран проекта" />
                            </label>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <button type="button" :disabled="index === 0" class="rounded-full border border-border px-3 py-2 text-xs font-semibold text-text-muted transition enabled:hover:border-accent/70 enabled:hover:text-white disabled:opacity-40" @click="makeCover(index)">
                                    Сделать главным
                                </button>
                                <button type="button" :disabled="index === 0" class="rounded-full border border-border px-3 py-2 text-xs font-semibold text-text-muted transition enabled:hover:border-accent/70 enabled:hover:text-white disabled:opacity-40" @click="moveImage(index, -1)">
                                    Вверх
                                </button>
                                <button type="button" :disabled="index === galleryItems.length - 1" class="rounded-full border border-border px-3 py-2 text-xs font-semibold text-text-muted transition enabled:hover:border-accent/70 enabled:hover:text-white disabled:opacity-40" @click="moveImage(index, 1)">
                                    Вниз
                                </button>
                                <button type="button" class="rounded-full border border-rose-400/50 px-3 py-2 text-xs font-semibold text-rose-300 transition hover:border-rose-300 hover:text-rose-200" @click="removeImage(index)">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    </div>

                    <p
                        v-if="galleryItems.length === 0"
                        class="rounded-2xl border border-border bg-background/45
                               p-5 text-sm text-text-muted"
                    >
                        Изображений пока нет. Добавьте одно или несколько изображений выше.
                    </p>
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

<style scoped>
.portfolio-date-picker {
    --dp-background-color: rgb(4 7 18);
    --dp-text-color: rgb(248 250 252);
    --dp-hover-color: rgb(255 255 255 / 0.06);
    --dp-hover-text-color: rgb(255 255 255);
    --dp-primary-color: rgb(139 92 246);
    --dp-primary-text-color: rgb(255 255 255);
    --dp-secondary-color: rgb(148 163 184);
    --dp-border-color: rgb(56 71 104);
    --dp-menu-border-color: rgb(56 71 104);
    --dp-border-color-hover: rgb(34 211 238);
    --dp-border-color-focus: rgb(34 211 238);
    --dp-icon-color: rgb(203 213 225);
    --dp-disabled-color: rgb(15 23 42);
    --dp-disabled-color-text: rgb(100 116 139);
    --dp-border-radius: 1rem;
    --dp-cell-border-radius: 0.75rem;
    --dp-input-padding: 0.875rem 2.75rem 0.875rem 1rem;
    --dp-font-size: 1rem;
    --dp-menu-min-width: 19rem;
}

.portfolio-date-picker :deep(.dp__input) {
    border-color: rgb(56 71 104);
    background-color: rgb(4 7 18 / 0.7);
    color: rgb(248 250 252);
    font-weight: 600;
}

.portfolio-date-picker :deep(.dp__input::placeholder) {
    color: rgb(148 163 184);
    font-weight: 500;
}

.portfolio-date-picker :deep(.dp__input_wrap:hover .dp__input) {
    border-color: rgb(34 211 238);
}

.portfolio-date-picker :deep(.dp__menu) {
    box-shadow: 0 24px 70px rgb(0 0 0 / 0.45);
}

</style>
