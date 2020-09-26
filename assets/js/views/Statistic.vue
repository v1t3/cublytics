<template>
    <div>
        <h1>Статистика</h1>
        <div class="channel-list">
            Каналы:
            <select v-model="channel_active" @change="getActive()">
                <option v-for="channel in channels" :key="channel.name">{{ channel.name }}</option>
            </select>
            <div v-if="channels">
                <h3>Статистика по каналу: {{ channel_active }}</h3>
                <span class="statistic-btn-time btn" @click="getActive('day')">День</span>
                <span class="statistic-btn-time btn" @click="getActive('week')">Неделя</span>
                <span class="statistic-btn-time btn" @click="getActive('month1')">Месяц</span>
                <span class="statistic-btn-time btn" @click="getActive('month6')">Пол года</span>
                <span class="statistic-btn-time btn" @click="getActive('year')">Год</span>
                <span class="statistic-btn-time btn" @click="getActive('all')">Всё время</span>
                <br><br>
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
import Channel_stat from "../components/channel_stat/channel_stat";
import Loader_gif from "../components/loader_gif";

export default {
    name: "Statistic",
    components: {
        Loader_gif,
        Channel_stat
    },
    data() {
        return {
            channels: null,
            channel_active: '',
            channel_name: '',
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
            if (undefined !== this.$store.state.user.channels) {
                this.channels = this.$store.state.user.channels;
                this.channel_active = this.channels[0].name;
            }
        },
        getActive: function (type = '') {
            this.show_stat = false;
            let that = this;
            this.$nextTick(function () {
                that.channel_name = that.channel_active;
                if (type) {
                    that.statistic_type = type;
                }

                that.show_stat = true;
            });
        }
    }
}
</script>
