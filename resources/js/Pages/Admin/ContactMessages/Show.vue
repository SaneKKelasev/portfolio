<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: Object,
        required: true,
    },

    meta: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);

function markAsRead() {
    router.patch(`/admin/contact-messages/${props.message.id}/read`, {}, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description">
    </Head>

    <AdminLayout>
        <Link
            href="/admin/contact-messages"
            class="text-sm font-semibold text-text-muted transition
                   hover:text-white"
        >
            ← К сообщениям
        </Link>

        <article
            class="mt-8 max-w-5xl rounded-3xl border border-border
                   bg-surface/75 p-6"
        >
            <div
                v-if="successMessage"
                class="mb-6 rounded-2xl border border-success/40 bg-success/10
                       px-4 py-3 text-sm font-semibold text-success"
            >
                {{ successMessage }}
            </div>

            <div class="flex flex-wrap items-start justify-between gap-5">
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.18em]
                               text-accent"
                    >
                        Сообщение
                    </p>
                    <h1 class="text-3xl font-semibold text-text">
                        {{ message.name }}
                    </h1>
                    <p class="mt-2 text-text-muted">{{ message.email }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        :href="`mailto:${message.email}`"
                        class="rounded-full border border-border px-5 py-3
                               text-sm font-semibold text-text transition
                               hover:border-accent hover:text-accent"
                    >
                        Ответить
                    </a>
                    <button
                        v-if="!message.is_read"
                        type="button"
                        class="rounded-full bg-primary px-5 py-3 text-sm
                               font-semibold text-white transition
                               hover:bg-violet-500"
                        @click="markAsRead"
                    >
                        Отметить прочитанным
                    </button>
                </div>
            </div>

            <dl class="mt-8 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-border bg-background/40 p-4">
                    <dt class="text-xs uppercase tracking-wide text-text-subtle">
                        Дата
                    </dt>
                    <dd class="mt-2 font-semibold text-text">
                        {{ message.created_at }}
                    </dd>
                </div>
                <div class="rounded-2xl border border-border bg-background/40 p-4">
                    <dt class="text-xs uppercase tracking-wide text-text-subtle">
                        Статус
                    </dt>
                    <dd class="mt-2">
                        <span
                            class="rounded-full border px-3 py-1 text-xs
                                   font-semibold"
                            :class="
                                message.is_read
                                    ? 'border-border text-text-muted'
                                    : 'border-accent text-accent'
                            "
                        >
                            {{ message.is_read ? 'прочитано' : 'новое' }}
                        </span>
                    </dd>
                </div>
            </dl>

            <section
                class="mt-6 rounded-3xl border border-border bg-background/50 p-6"
            >
                <h2 class="text-lg font-semibold text-text">Текст сообщения</h2>
                <p class="mt-4 whitespace-pre-line leading-8 text-text-muted">
                    {{ message.message }}
                </p>
            </section>
        </article>
    </AdminLayout>
</template>
