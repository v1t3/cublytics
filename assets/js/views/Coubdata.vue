<template>
    <div>
        <h1>Данные коуба</h1>
        <coub-info/>
    </div>
</template>

<script>
    import coubInfo from '../components/coub-info.vue';

    export default {
        name: "Coubdata",
        components: {
            coubInfo,
        }
    }
</script>

<style scoped>

</style>
