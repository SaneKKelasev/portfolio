<script setup>
import FormInput from '@/Components/Form/Input.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    email: '',
    password: '',
});

function submit() {
    form.post('/login');
}
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <main
        class="flex min-h-screen items-center justify-center bg-background
               px-5 py-12"
    >
        <form
            class="w-full max-w-md rounded-3xl border border-border
                   bg-surface/80 p-6 shadow-2xl shadow-black/30 sm:p-8"
            @submit.prevent="submit"
        >
            <p
                class="text-xs font-semibold uppercase tracking-[0.18em]
                       text-accent"
            >
                Админка
            </p>
            <h1 class="mt-4 text-3xl font-semibold text-text">
                Вход в панель
            </h1>

            <label class="mt-8 block">
                <span class="text-sm font-semibold text-text">Email</span>
                <FormInput
                    v-model="form.email"
                    type="email"
                    :invalid="Boolean(form.errors.email)"
                />
                <span
                    v-if="form.errors.email"
                    class="mt-2 block text-sm text-rose-300"
                >
                    {{ form.errors.email }}
                </span>
            </label>

            <label class="mt-5 block">
                <span class="text-sm font-semibold text-text">Пароль</span>
                <FormInput
                    v-model="form.password"
                    type="password"
                    :invalid="Boolean(form.errors.password)"
                />
                <span
                    v-if="form.errors.password"
                    class="mt-2 block text-sm text-rose-300"
                >
                    {{ form.errors.password }}
                </span>
            </label>

            <button
                type="submit"
                :disabled="form.processing"
                class="mt-8 w-full rounded-full bg-primary px-5 py-3 text-sm
                       font-semibold text-white shadow-lg shadow-primary/25
                       transition hover:bg-violet-500 disabled:opacity-60"
            >
                {{ form.processing ? 'Входим...' : 'Войти' }}
            </button>
        </form>
    </main>
</template>
