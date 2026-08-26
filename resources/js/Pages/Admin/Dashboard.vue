<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },

    latestMessages: {
        type: Array,
        required: true,
    },

    meta: {
        type: Object,
        required: true,
    },
});
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
                Панель
            </p>
            <h1 class="mt-4 text-4xl font-semibold text-text">
                Панель управления
            </h1>
        </header>

        <section class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="rounded-3xl border border-border bg-surface/75 p-6"
            >
                <p class="text-3xl font-semibold text-text">{{ stat.value }}</p>
                <p class="mt-2 text-sm uppercase tracking-wide text-text-muted">
                    {{ stat.label }}
                </p>
            </div>
        </section>

        <section
            class="mt-8 rounded-3xl border border-border bg-surface/75 p-6"
        >
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-text">
                    Последние сообщения
                </h2>
                <Link
                    href="/admin/contact-messages"
                    class="text-sm font-semibold text-accent"
                >
                    Все сообщения
                </Link>
            </div>

            <div class="mt-5 divide-y divide-border">
                <Link
                    v-for="message in latestMessages"
                    :key="message.id"
                    :href="`/admin/contact-messages/${message.id}`"
                    class="flex items-center justify-between gap-4 py-4"
                >
                    <div>
                        <p class="font-semibold text-text">{{ message.name }}</p>
                        <p class="mt-1 text-sm text-text-muted">
                            {{ message.email }}
                        </p>
                    </div>
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
                </Link>

                <p
                    v-if="latestMessages.length === 0"
                    class="py-6 text-text-muted"
                >
                    Сообщений пока нет.
                </p>
            </div>
        </section>
    </AdminLayout>
</template>
