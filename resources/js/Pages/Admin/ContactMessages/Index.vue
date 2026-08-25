<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    messages: {
        type: Object,
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
                Сообщения
            </p>
            <h1 class="mt-4 text-4xl font-semibold text-text">
                Контактные сообщения
            </h1>
        </header>

        <section
            class="mt-8 overflow-hidden rounded-3xl border border-border
                   bg-surface/75"
        >
            <Link
                v-for="message in messages.data"
                :key="message.id"
                :href="`/admin/contact-messages/${message.id}`"
                class="grid gap-4 border-b border-border p-5 transition
                       last:border-b-0 hover:bg-white/[0.03]
                       lg:grid-cols-[1fr_auto] lg:items-center"
            >
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-xl font-semibold text-text">
                            {{ message.name }}
                        </h2>
                        <span
                            class="rounded-full border px-3 py-1 text-xs
                                   font-semibold"
                            :class="
                                message.read_at
                                    ? 'border-border text-text-muted'
                                    : 'border-accent text-accent'
                            "
                        >
                            {{ message.read_at ? 'прочитано' : 'новое' }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-text-muted">
                        {{ message.email }}
                    </p>
                </div>

                <p class="text-sm text-text-muted">
                    {{ message.created_at }}
                </p>
            </Link>

            <p v-if="messages.data.length === 0" class="p-8 text-text-muted">
                Сообщений пока нет.
            </p>
        </section>
    </AdminLayout>
</template>
