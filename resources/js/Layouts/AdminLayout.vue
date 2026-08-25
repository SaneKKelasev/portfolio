<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <main class="min-h-screen bg-background">
        <aside
            class="fixed inset-y-0 left-0 hidden w-72 border-r border-border
                   bg-surface/80 p-6 backdrop-blur lg:block"
        >
            <Link href="/admin" class="text-2xl font-semibold text-text">
                PortfolioHub
            </Link>
            <p class="mt-1 text-sm text-text-muted">Панель управления</p>

            <nav class="mt-10 space-y-2">
                <Link
                    href="/admin"
                    class="block rounded-2xl px-4 py-3 text-sm font-semibold
                           text-text-muted transition hover:bg-white/[0.04]
                           hover:text-white"
                >
                    Главная
                </Link>
                <Link
                    href="/admin/projects"
                    class="block rounded-2xl px-4 py-3 text-sm font-semibold
                           text-text-muted transition hover:bg-white/[0.04]
                           hover:text-white"
                >
                    Проекты
                </Link>
                <Link
                    href="/admin/contact-messages"
                    class="block rounded-2xl px-4 py-3 text-sm font-semibold
                           text-text-muted transition hover:bg-white/[0.04]
                           hover:text-white"
                >
                    Сообщения
                </Link>
            </nav>

            <div class="absolute inset-x-6 bottom-6">
                <p class="text-sm font-semibold text-text">{{ user?.name }}</p>
                <p class="mt-1 text-xs text-text-muted">{{ user?.email }}</p>
                <button
                    type="button"
                    class="mt-4 text-sm font-semibold text-text-muted
                           transition hover:text-white"
                    @click="logout"
                >
                    Выйти
                </button>
            </div>
        </aside>

        <section class="lg:pl-72">
            <header
                class="flex items-center justify-between border-b border-border
                       bg-surface/50 px-5 py-4 backdrop-blur sm:px-8 lg:hidden"
            >
                <Link href="/admin" class="font-semibold text-text">
                    PortfolioHub
                </Link>
                <button
                    type="button"
                    class="text-sm font-semibold text-text-muted"
                    @click="logout"
                >
                    Выйти
                </button>
            </header>

            <div class="px-5 py-8 sm:px-8 lg:px-10">
                <slot />
            </div>
        </section>
    </main>
</template>
