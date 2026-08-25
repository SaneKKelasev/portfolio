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
            class="mt-8 max-w-4xl rounded-3xl border border-border
                   bg-surface/75 p-6"
        >
            <div
                v-if="successMessage"
                class="mb-6 rounded-2xl border border-success/40 bg-success/10
                       px-4 py-3 text-sm font-semibold text-success"
            >
                {{ successMessage }}
            </div>

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold text-text">
                        {{ message.name }}
                    </h1>
                    <p class="mt-2 text-text-muted">{{ message.email }}</p>
                </div>

                <button
                    v-if="!message.read_at"
                    type="button"
                    class="rounded-full bg-primary px-5 py-3 text-sm
                           font-semibold text-white transition
                           hover:bg-violet-500"
                    @click="markAsRead"
                >
                    Отметить прочитанным
                </button>
            </div>

            <p class="mt-8 whitespace-pre-line leading-8 text-text-muted">
                {{ message.message }}
            </p>
        </article>
    </AdminLayout>
</template>
