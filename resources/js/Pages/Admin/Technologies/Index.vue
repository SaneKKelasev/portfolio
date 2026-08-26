<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps({
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
const editingId = ref(null);

const createForm = useForm({
    name: '',
    slug: '',
});

const editForm = useForm({
    name: '',
    slug: '',
});

function submitCreate() {
    createForm.post('/admin/technologies', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
}

function startEdit(technology) {
    editingId.value = technology.id;
    editForm.clearErrors();
    editForm.name = technology.name;
    editForm.slug = technology.slug;
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitEdit(technology) {
    editForm.put(`/admin/technologies/${technology.id}`, {
        preserveScroll: true,
        onSuccess: cancelEdit,
    });
}

function destroyTechnology(technology) {
    if (technology.has_protected_projects || technology.projects_count > 0) {
        return;
    }

    if (confirm(`Удалить технологию "${technology.name}"?`)) {
        router.delete(`/admin/technologies/${technology.id}`, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <AdminLayout>
        <header>
            <p
                class="text-xs font-semibold uppercase tracking-[0.18em]
                       text-accent"
            >
                Технологии
            </p>
            <h1 class="mt-4 text-4xl font-semibold text-text">
                Управление технологиями
            </h1>
        </header>

        <div
            v-if="successMessage"
            class="mt-6 rounded-2xl border border-success/40 bg-success/10
                   px-4 py-3 text-sm font-semibold text-success"
        >
            {{ successMessage }}
        </div>

        <form
            class="mt-8 grid gap-4 rounded-3xl border border-border
                   bg-surface/75 p-6 lg:grid-cols-[1fr_1fr_auto]"
            @submit.prevent="submitCreate"
        >
            <label class="block">
                <span class="text-sm font-semibold text-text">Название</span>
                <input
                    v-model="createForm.name"
                    class="mt-2 w-full rounded-2xl border border-border
                           bg-background/70 px-4 py-3 text-text outline-none
                           focus:border-accent"
                >
                <span v-if="createForm.errors.name" class="mt-2 block text-sm text-rose-300">
                    {{ createForm.errors.name }}
                </span>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-text">Slug</span>
                <input
                    v-model="createForm.slug"
                    placeholder="Можно оставить пустым"
                    class="mt-2 w-full rounded-2xl border border-border
                           bg-background/70 px-4 py-3 text-text outline-none
                           focus:border-accent"
                >
                <span v-if="createForm.errors.slug" class="mt-2 block text-sm text-rose-300">
                    {{ createForm.errors.slug }}
                </span>
            </label>

            <button
                type="submit"
                :disabled="createForm.processing"
                class="self-end rounded-full bg-primary px-5 py-3 text-sm
                       font-semibold text-white shadow-lg shadow-primary/25
                       transition hover:bg-violet-500 disabled:opacity-60"
            >
                {{ createForm.processing ? 'Добавление...' : 'Добавить' }}
            </button>
        </form>

        <section
            class="mt-8 overflow-hidden rounded-3xl border border-border
                   bg-surface/75"
        >
            <div
                v-for="technology in technologies"
                :key="technology.id"
                class="grid gap-4 border-b border-border p-5 last:border-b-0
                       xl:grid-cols-[1fr_auto] xl:items-center"
            >
                <form
                    v-if="editingId === technology.id"
                    class="grid gap-3 lg:grid-cols-2"
                    @submit.prevent="submitEdit(technology)"
                >
                    <label class="block">
                        <span class="text-sm font-semibold text-text">Название</span>
                        <input
                            v-model="editForm.name"
                            class="mt-2 w-full rounded-2xl border border-border
                                   bg-background/70 px-4 py-3 text-text outline-none
                                   focus:border-accent"
                        >
                        <span v-if="editForm.errors.name" class="mt-2 block text-sm text-rose-300">
                            {{ editForm.errors.name }}
                        </span>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-text">Slug</span>
                        <input
                            v-model="editForm.slug"
                            class="mt-2 w-full rounded-2xl border border-border
                                   bg-background/70 px-4 py-3 text-text outline-none
                                   focus:border-accent"
                        >
                        <span v-if="editForm.errors.slug" class="mt-2 block text-sm text-rose-300">
                            {{ editForm.errors.slug }}
                        </span>
                    </label>
                </form>

                <div v-else>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-xl font-semibold text-text">
                            {{ technology.name }}
                        </h2>
                        <span
                            v-if="technology.has_protected_projects"
                            class="rounded-full border border-primary/50 px-3 py-1
                                   text-xs font-semibold text-violet-200"
                        >
                            защищена
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-text-muted">
                        {{ technology.slug }} · проектов: {{ technology.projects_count }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <template v-if="editingId === technology.id">
                        <button
                            type="button"
                            class="rounded-full border border-border px-4 py-2
                                   text-sm font-semibold text-text-muted
                                   transition hover:border-accent/70 hover:text-white"
                            @click="cancelEdit"
                        >
                            Отмена
                        </button>
                        <button
                            type="button"
                            :disabled="editForm.processing"
                            class="rounded-full bg-primary px-4 py-2 text-sm
                                   font-semibold text-white transition
                                   hover:bg-violet-500 disabled:opacity-60"
                            @click="submitEdit(technology)"
                        >
                            Сохранить
                        </button>
                    </template>

                    <template v-else>
                        <button
                            type="button"
                            :disabled="technology.has_protected_projects"
                            class="rounded-full border border-border px-4 py-2
                                   text-sm font-semibold text-text-muted
                                   transition enabled:hover:border-accent/70
                                   enabled:hover:text-white disabled:opacity-50"
                            @click="startEdit(technology)"
                        >
                            Редактировать
                        </button>
                        <button
                            type="button"
                            :disabled="technology.has_protected_projects || technology.projects_count > 0"
                            class="rounded-full border border-rose-400/50 px-4 py-2
                                   text-sm font-semibold text-rose-300 transition
                                   enabled:hover:border-rose-300 enabled:hover:text-rose-200
                                   disabled:opacity-50"
                            @click="destroyTechnology(technology)"
                        >
                            Удалить
                        </button>
                    </template>
                </div>
            </div>

            <p v-if="technologies.length === 0" class="p-8 text-text-muted">
                Технологий пока нет.
            </p>
        </section>
    </AdminLayout>
</template>
