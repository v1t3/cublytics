<template>
    <div class="view-container channel-view">
        <h1 class="view-title">Статистика каналов</h1>
        <div class="channel-block">
            <div v-if="!channels.length">Каналы отсутсвуют</div>
            <div class="channel-list" v-if="channels.length">
                <select v-model="channel_active_id" @change="getActive()">
                    <option v-for="channel in channels" :value="channel.channel_id">{{ channel.title }}</option>
                </select>
            </div>
            <div class="channel-info">
                <div class="statistic-btn" v-if="channels.length">
                    <span class="statistic-btn_time btn"
                          v-bind:class="{active: statistic_type === 'day'}"
                          @click="getActive('day')">
                        День
                    </span>
                    <span class="statistic-btn_time btn"
                          v-bind:class="{active: statistic_type === 'week'}"
                          @click="getActive('week')">
                        Неделя
                    </span>
                    <span class="statistic-btn_time btn"
                          v-bind:class="{active: statistic_type === 'month1'}"
                          @click="getActive('month1')">
                        Месяц
                    </span>
                    <span class="statistic-btn_time btn"
                          v-bind:class="{active: statistic_type === 'month6'}"
                          @click="getActive('month6')">
                          Пол года
                    </span>
                    <span class="statistic-btn_time btn"
                          v-bind:class="{active: statistic_type === 'year'}"
                          @click="getActive('year')">
                        Год
                    </span>
                </div>

                <channel_stat v-if="show_stat && channel_active"
                              :channel_name="channel_active"
                              :statistic_type="statistic_type"
                ></channel_stat>
            </div>

            <loader_gif v-if="showLoader"/>
        </div>
    </div>
</template>

<script>
    import Channel_stat from "../components/channel_stat";
    import Loader_gif from "../components/loader_gif";

    export default {
        name: "Channel_data",
        components: {
            Loader_gif,
            Channel_stat
        },
        data() {
            return {
                channels: [],
                channel_active_id: '',
                channel_active: '',
                statistic_type: '',
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

                if (undefined !== this.$store.state.user.statistic_type) {
                    this.statistic_type = this.$store.state.user.statistic_type;
                }

                if (
                    undefined !== this.$store.state.user.channels &&
                    this.$store.state.user.channels.length
                ) {
                    this.channels = this.$store.state.user.channels;
                    this.channel_active_id = this.channels[0]['channel_id'];
                    this.channel_active = this.channels[0].name;
                }

                this.showLoader = false;
            },
            getActive: function (type = '') {
                let that = this;

                if (type && type === this.statistic_type) {
                    return;
                }

                this.show_stat = false;
                this.showLoader = true;
                this.$nextTick(function () {
                    // found active channel name by id
                    for (let channel of this.channels) {
                        if (channel.channel_id === this.channel_active_id) {
                            this.channel_active = channel.name;
                            break;
                        }
                    }

                    if (type) {
                        this.$store.commit('setStatisticType', type);
                        that.statistic_type = type;
                    }

                    this.showLoader = false;
                    that.show_stat = true;
                });
            }
        }
    }
</script>
