<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Tooltip,
    ArcElement,
} from 'chart.js';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import { computed } from 'vue';

ChartJS.register(
    ArcElement,
    BarElement,
    CategoryScale,
    Filler,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Tooltip,
);

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },

    analytics: {
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

const textColor = '#f8fafc';
const mutedColor = '#94a3b8';
const borderColor = 'rgba(148, 163, 184, 0.18)';
const chartColors = ['#22d3ee', '#8b5cf6', '#34d399', '#f59e0b', '#fb7185', '#60a5fa'];

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: mutedColor,
                boxWidth: 10,
                boxHeight: 10,
                usePointStyle: true,
            },
        },
        tooltip: {
            backgroundColor: '#070a13',
            borderColor,
            borderWidth: 1,
            titleColor: textColor,
            bodyColor: mutedColor,
        },
    },
    scales: {
        x: {
            grid: {
                color: borderColor,
            },
            ticks: {
                color: mutedColor,
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                color: borderColor,
            },
            ticks: {
                color: mutedColor,
                precision: 0,
            },
        },
    },
};

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: chartOptions.plugins,
};

const viewsData = computed(() => ({
    labels: props.analytics.viewsByDay.labels,
    datasets: [
        {
            label: 'Просмотры',
            data: props.analytics.viewsByDay.values,
            borderColor: '#22d3ee',
            backgroundColor: 'rgba(34, 211, 238, 0.16)',
            fill: true,
            tension: 0.35,
        },
    ],
}));

const messagesData = computed(() => ({
    labels: props.analytics.messagesByDay.labels,
    datasets: [
        {
            label: 'Сообщения',
            data: props.analytics.messagesByDay.values,
            backgroundColor: '#8b5cf6',
            borderRadius: 8,
        },
    ],
}));

const topProjectsData = computed(() => ({
    labels: props.analytics.topProjects.map((project) => project.title),
    datasets: [
        {
            label: 'Просмотры за 30 дней',
            data: props.analytics.topProjects.map((project) => project.views),
            backgroundColor: '#34d399',
            borderRadius: 8,
        },
    ],
}));

const technologyData = computed(() => ({
    labels: props.analytics.technologyUsage.map((technology) => technology.name),
    datasets: [
        {
            label: 'Проекты',
            data: props.analytics.technologyUsage.map((technology) => technology.projects),
            backgroundColor: chartColors,
            borderColor: '#0b1020',
            borderWidth: 3,
        },
    ],
}));
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
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.18em]
                               text-accent"
                    >
                        Аналитика
                    </p>
                    <h2 class="mt-3 text-2xl font-semibold text-text">
                        Как живёт портфолио
                    </h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-text-muted">
                    Просмотры считаются один раз в день для одного посетителя,
                    чтобы цифры были полезными, а не раздутыми обновлениями страницы.
                </p>
            </div>

            <div class="mt-6 grid gap-4 xl:grid-cols-2">
                <article class="rounded-2xl border border-border bg-background/40 p-5">
                    <h3 class="text-base font-semibold text-text">
                        Просмотры за 7 дней
                    </h3>
                    <div class="mt-4 h-64">
                        <Line :data="viewsData" :options="chartOptions" />
                    </div>
                </article>

                <article class="rounded-2xl border border-border bg-background/40 p-5">
                    <h3 class="text-base font-semibold text-text">
                        Сообщения за 7 дней
                    </h3>
                    <div class="mt-4 h-64">
                        <Bar :data="messagesData" :options="chartOptions" />
                    </div>
                </article>

                <article class="rounded-2xl border border-border bg-background/40 p-5">
                    <h3 class="text-base font-semibold text-text">
                        Топ проектов за 30 дней
                    </h3>
                    <div class="mt-4 h-64">
                        <Bar :data="topProjectsData" :options="chartOptions" />
                    </div>
                </article>

                <article class="rounded-2xl border border-border bg-background/40 p-5">
                    <h3 class="text-base font-semibold text-text">
                        Технологии в проектах
                    </h3>
                    <div class="mt-4 h-64">
                        <Doughnut :data="technologyData" :options="doughnutOptions" />
                    </div>
                </article>
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
