<template>
    <div>
        <h1>Статистика</h1>
        <div class="channel-list">
            <button class="btn-primary" v-on:click="getChannelsList()">Обновить список</button>
            <br>
            <br>

            Каналы:
            <select v-model="channel_active">
                <option v-for="channel in channels" :key="channel.name">{{ channel.name }}</option>
            </select>
            <div v-if="channels">
                <h3>Статистика по каналу: {{ channel_active }}</h3>
                <div>График по {{ channel_active }}</div>
            </div>

            <div class="loader" v-if="showLoader">
                <img src="/build/img/load.gif" alt="Loading">
            </div>
        </div>

        <br><br><br>
        <channel-performance/>
    </div>
</template>

<script>
    import {required, sameAs, minLength, email} from 'vuelidate/lib/validators';
    import axios from "axios";
    import ChannelPerformance from "../components/channel-performance";

    export default {
        name: "Statistic",
        components: {
            ChannelPerformance,
        },
        data() {
            return {
                channels: null,
                channel_active: '',
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
        }
    }
</script>
