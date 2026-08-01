<script setup>
    import { ref } from 'vue';

    const title = ref('PortfolioHub');

    function changeTitle() {
        title.value = 'Hello';
    }
</script>

<template>
    <h1>{{ title }}</h1>

    <button type="button" @click="changeTitle">
        Изменить заголовок
    </button>
</template>   