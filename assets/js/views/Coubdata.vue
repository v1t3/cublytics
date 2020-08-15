<template>
    <div>
        <h1>Данные</h1>
        <coub-info/>
        <user-info/>
        <channel-performance/>
    </div>
</template>

<script>
    import coubInfo from '../components/coub-info.vue';
    import UserInfo from "../components/user-info";
    import ChannelPerformance from "../components/channel-performance";

    export default {
        name: "Coubdata",
        components: {
            coubInfo,
            UserInfo,
            ChannelPerformance,
        }
    }
</script>

<style scoped>

</style>
