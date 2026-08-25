<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <header
        class="relative z-10 mx-auto flex w-full max-w-7xl flex-wrap
               items-center justify-between gap-4 px-5 pt-6 sm:px-8
               lg:px-10"
    >
        <Link href="/" class="inline-flex items-center gap-3">
            <span
                class="flex size-9 items-center justify-center rounded-xl
                       border border-primary/40 bg-primary/15 text-sm
                       font-black text-white shadow-lg shadow-primary/20"
            >
                P
            </span>
            <span class="font-semibold text-text">PortfolioHub</span>
        </Link>

        <nav
            class="flex flex-wrap items-center gap-2 rounded-full border
                   border-border bg-surface/70 p-1 text-sm font-semibold
                   shadow-2xl shadow-black/20 backdrop-blur"
        >
            <Link
                href="/projects"
                class="rounded-full px-4 py-2 text-text-muted transition
                       hover:bg-white/[0.04] hover:text-white"
            >
                Проекты
            </Link>
            <a
                href="/#contact"
                class="rounded-full px-4 py-2 text-text-muted transition
                       hover:bg-white/[0.04] hover:text-white"
            >
                Контакты
            </a>
            <Link
                href="/login"
                class="rounded-full bg-primary px-4 py-2 text-white
                       shadow-lg shadow-primary/20 transition
                       hover:bg-violet-500"
            >
                Admin demo
            </Link>
        </nav>
    </header>
</template>
