<script setup>
import FormCheckbox from '@/Components/Form/Checkbox.vue';
import FormInput from '@/Components/Form/Input.vue';
import FormTextarea from '@/Components/Form/Textarea.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    message: '',
    privacy_consent: false,
});

const successMessage = computed(() => page.props.flash?.success ?? null);

function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <section
        id="contact"
        class="mx-auto w-full max-w-7xl px-5 pb-16 sm:px-8 lg:px-10"
    >
        <div
            class="grid gap-8 rounded-3xl border border-border bg-surface/75
                   p-6 shadow-2xl shadow-black/30 backdrop-blur sm:p-8
                   lg:grid-cols-[0.85fr_1.15fr] lg:p-10"
        >
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-[0.18em]
                           text-accent"
                >
                    Контакты
                </p>

                <h2 class="mt-4 text-3xl font-semibold text-text sm:text-4xl">
                    Обсудим проект
                </h2>

                <p class="mt-5 max-w-xl leading-8 text-text-muted">
                    Напишите, если нужен Laravel-разработчик, ревью проекта или
                    помощь с fullstack-задачей. Я отвечу на указанную почту.
                </p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div
                    v-if="successMessage"
                    class="rounded-2xl border border-success/40 bg-success/10
                           px-4 py-3 text-sm font-medium text-success"
                >
                    {{ successMessage }}
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-text">
                            Имя *
                        </span>
                        <FormInput
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                            :invalid="Boolean(form.errors.name)"
                            placeholder="Александр"
                        />
                        <span
                            v-if="form.errors.name"
                            class="mt-2 block text-sm text-rose-300"
                        >
                            {{ form.errors.name }}
                        </span>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-text">
                            Email *
                        </span>
                        <FormInput
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            :invalid="Boolean(form.errors.email)"
                            placeholder="mail@example.com"
                        />
                        <span
                            v-if="form.errors.email"
                            class="mt-2 block text-sm text-rose-300"
                        >
                            {{ form.errors.email }}
                        </span>
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-text">
                        Сообщение *
                    </span>
                    <FormTextarea
                        v-model="form.message"
                        rows="5"
                        class="max-h-56"
                        :invalid="Boolean(form.errors.message)"
                        placeholder="Коротко опишите задачу"
                    />
                    <span
                        v-if="form.errors.message"
                        class="mt-2 block text-sm text-rose-300"
                    >
                        {{ form.errors.message }}
                    </span>
                </label>

                <label class="flex items-start gap-3 text-sm leading-5 text-text-muted">
                    <FormCheckbox
                        v-model="form.privacy_consent"
                        required
                    />
                    <span>
                        Я согласен на обработку персональных данных для ответа на это сообщение.
                    </span>
                </label>
                <span
                    v-if="form.errors.privacy_consent"
                    class="-mt-3 block text-sm text-rose-300"
                >
                    {{ form.errors.privacy_consent }}
                </span>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex rounded-full bg-primary px-6 py-3
                           text-sm font-semibold text-white shadow-lg
                           shadow-primary/25 transition hover:bg-violet-500
                           disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ form.processing ? 'Отправка...' : 'Отправить сообщение' }}
                </button>
            </form>
        </div>
    </section>
</template>
