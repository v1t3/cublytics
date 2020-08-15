<template>
    <div>
        <h1>Статистика</h1>
        <channel-performance/>
    </div>
</template>

<script>
    import ChannelPerformance from "../components/channel-performance";

    export default {
        name: "Statistic",
        components: {
            ChannelPerformance,
        }
    }
</script>
