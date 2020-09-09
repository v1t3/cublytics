<template>
    <div>
        <h1>Статистика</h1>
        <div class="channel-list">
            <button class="btn-primary" v-on:click="getChannelsList()">Обновить список</button>
            <br>
            <br>

            Каналы:
            <select v-model="channel_active" @change="getActive()">
                <option v-for="channel in channels" :key="channel.name">{{ channel.name }}</option>
            </select>
            <div v-if="channels">
                <h3>Статистика по каналу: {{ channel_active }}</h3>
                <br><br>
                <channel_stat v-if="show_stat && channel_active" :channel_name="channel_active"></channel_stat>
            </div>

            <div class="loader" v-if="showLoader">
                <img src="/build/img/load.gif" alt="Loading">
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import ChannelPerformance from "../components/channel-performance";
    import Channel_stat from "../components/channel_stat/channel_stat";

    export default {
        name: "Statistic",
        components: {
            Channel_stat,
            ChannelPerformance,
        },
        data() {
            return {
                channels: null,
                channel_active: '',
                channel_name: '',
                show_stat: true,
                showLoader: false,
            }
        },
        mounted() {
            this.getChannelsList();
        },
        methods: {
            getChannelsList: function () {
                this.showLoader = true;

                axios({
                    method: 'post',
                    url: '/api/stat/getChannelsList',
                    data: {}
                })
                    .then((response) => {
                        let data = response['data'];

                        this.showLoader = false;

                        console.log('data', data);
                        if (data) {
                            if ('success' === data['result']) {
                                this.channels = data['channels'];

                                this.channel_active = this.channels[0].name;
                            }
                        }
                    })
                    .catch((error) => {
                        console.log('catch error', error);

                        this.error = error;
                    });

            },
            getActive: function () {
                this.show_stat = false;
                let that = this;
                this.$nextTick(function () {
                    that.channel_name = that.channel_active;
                    that.show_stat = true;
                });
            }
        }
    }
</script>
