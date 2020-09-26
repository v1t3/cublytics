<template>
    <div>
        <h1>Статистика по коубам</h1>

        <div class="channel-list">
            Каналы:
            <select v-model="channel_active_id" @change="getActiveChannel()">
                <option v-for="channel in channelList" :value="channel.channel_id">{{ channel.name }}</option>
            </select>
        </div>

        <br>

        <div v-if="!coubList.length">No coubs</div>
        <div class="channel-list" v-if="coubList.length">
            Коуб:
            <select v-model="coub_active_id" @change="getActiveCoub()">
                <option v-for="coub in coubList" :value="coub.coub_id">{{ coub.title }}</option>
            </select>

            <div>
                <span class="statistic-btn-time btn" @click="getActiveCoub('day')">День</span>
                <span class="statistic-btn-time btn" @click="getActiveCoub('week')">Неделя</span>
                <span class="statistic-btn-time btn" @click="getActiveCoub('month1')">Месяц</span>
                <span class="statistic-btn-time btn" @click="getActiveCoub('month6')">Пол года</span>
                <span class="statistic-btn-time btn" @click="getActiveCoub('year')">Год</span>
                <span class="statistic-btn-time btn" @click="getActiveCoub('all')">Всё время</span>
            </div>
        </div>

        <coub_stat v-if="show_stat && coub_active_id"
                   :coub_id="coub_active_id"
                   :statistic_type="statistic_type"
        ></coub_stat>
    </div>
</template>

<script>
    import Loader_gif from "../components/loader_gif";
    import axios from "axios";
    import Coub_stat from "../components/coub_stat/coub_stat";

    export default {
        name: "Coubdata",
        components: {
            Loader_gif,
            Coub_stat,
        },
        data() {
            return {
                channelList: null,
                channel_active_id: '',
                coubList: [],
                coub_active_id: '',
                show_stat: true,
                showLoader: false,
                statistic_type: '',
            }
        },
        mounted() {
            this.getChannelsList();
        },
        methods: {
            getChannelsList: function () {
                //hide coubs list
                this.coubList = [];

                if (
                    undefined !== this.$store.state.user.channels &&
                    undefined !== this.$store.state.user.channels[0]['channel_id']
                ) {
                    this.channelList = this.$store.state.user.channels;
                    this.channel_active_id = this.channelList[0]['channel_id'];

                    this.getCoubList();
                }
            },
            getActiveChannel: function () {
                let that = this;
                this.show_stat = false;
                this.$nextTick(function () {
                    that.getCoubList();
                });
            },
            getCoubList: function () {
                //hide coubs list
                this.coubList = [];

                if (this.channel_active_id) {
                    this.show_stat = false;

                    const formData = new FormData();
                    formData.set('channel_id', this.channel_active_id);

                    axios({
                        method: 'post',
                        url: '/api/coub/get_list',
                        data: formData
                    })
                        .then((response) => {
                            let data = response['data'];

                            // console.log('data user', data);

                            if (
                                data
                                && 'success' === data['result']
                                && undefined !== data['data']['coubs'][0]
                            ) {
                                this.coubList = data['data']['coubs'];
                                this.coub_active = this.coubList[0].title;
                            }
                        })
                        .catch((error) => {
                            console.error('catch error: ', error);
                        });
                }
            },
            getActiveCoub: function (type = '') {
                let that = this;
                this.show_stat = false;
                this.$nextTick(function () {
                    if (type) {
                        that.statistic_type = type;
                    }
                    that.show_stat = true;
                });
            }
        }
    }
</script>