<template>
    <div class="component" id="channel-performance">
        <loader_gif v-if="showLoader"/>

        <div class="views">
            <div v-if="coubsCount">Всего кубов: {{ coubsCount }}</div>
            <div v-if="isSelfCount">Своих: {{ isSelfCount }}</div>
            <div v-if="isRepostCount">Репосты: {{ isRepostCount }}</div>
            <div v-if="banCount">Забаненых: {{ banCount }}</div>
            <div v-if="likesCount">Всего лайков: {{ likesCount }}</div>
        </div>
        <div class="info">
            <div class="small">
                <line-chart v-if="showChart"
                            :chart-data="dataCollection"
                            :options="dataCollection.options"
                            :width="400"
                            :height="200"
                ></line-chart>
            </div>
        </div>
        {{ error }}
    </div>
</template>

<script>
import axios from "axios";
import LineChart from './LineChart.js';
import Loader_gif from "../loader_gif";

export default {
    name: "channel_stat",
    props: {
        channel_name: {
            type: String,
            required: true
        },
        statistic_type: {
            type: String,
            required: false
        },
    },
    components: {
        Loader_gif,
        LineChart,
    },
    data() {
        return {
            data: {},
            coubsCount: null,
            isSelfCount: null,
            isRepostCount: null,
            likesCount: null,
            banCount: null,
            showLoader: false,
            showChart: false,
            error: null,
            dataCollection: {
                labels: null,
                datasets: [],
                options: {
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true
                                }
                            }
                        ]
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            }
        }
    },
    mounted() {
        this.getCoubData();
    },
    methods: {
        fillData: function (labels, datasets) {
            this.dataCollection = {
                labels: labels,
                datasets: datasets
            }
        },
        getCoubData: function () {
            let that = this;

            if (this.channel_name) {
                this.clearData();
                this.showLoader = true;

                const bodyFormData = new FormData();
                bodyFormData.set('channel_name', this.channel_name);
                bodyFormData.set('statistic_type', this.statistic_type);

                axios({
                    method: 'post',
                    url: '/api/channel/get_channel_stat',
                    data: bodyFormData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then((response) => {
                        let data = response['data'];
                        let datasets = [];
                        let coubsData;

                        that.error = '';
                        that.showLoader = false;

                        // console.log('data', data);

                        if (data) {
                            if (typeof data === 'string') {
                                data = JSON.parse(data);
                            }

                            coubsData = that.getCoubsCount(data['data']);

                            if (coubsData) {
                                that.showChart = true;

                                if (
                                    coubsData['views_count'] &&
                                    coubsData['views_count'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Просмотры',
                                        backgroundColor: 'rgba(78,103,245,0.8)',
                                        data: coubsData['views_count']
                                    });
                                }
                                if (
                                    coubsData['repost_count'] &&
                                    coubsData['repost_count'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Репосты',
                                        backgroundColor: 'rgba(248,145,46,0.8)',
                                        data: coubsData['repost_count']
                                    });
                                }
                                if (
                                    coubsData['remixes_count'] &&
                                    coubsData['remixes_count'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Рекоубы',
                                        backgroundColor: 'rgba(105,248,178,0.8)',
                                        data: coubsData['remixes_count']
                                    });
                                }
                                if (
                                    coubsData['like_count'] &&
                                    coubsData['like_count'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Лайки',
                                        backgroundColor: 'rgb(105,248,107,0.8)',
                                        data: coubsData['like_count']
                                    });
                                }
                                if (
                                    coubsData['dislikes_count'] &&
                                    coubsData['dislikes_count'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Дизлайки',
                                        backgroundColor: 'rgb(105,107,248,0.8)',
                                        data: coubsData['dislikes_count']
                                    });
                                }
                                if (
                                    coubsData['is_kd'] &&
                                    coubsData['is_kd'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'КД',
                                        backgroundColor: 'rgb(248,105,207,0.8)',
                                        data: coubsData['is_kd']
                                    });
                                }
                                if (
                                    coubsData['featured'] &&
                                    coubsData['featured'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Фичи',
                                        backgroundColor: 'rgb(248,207,105,0.8)',
                                        data: coubsData['featured']
                                    });
                                }
                                if (
                                    coubsData['banned'] &&
                                    coubsData['banned'].some(item => item !== 0)
                                ) {
                                    datasets.push({
                                        label: 'Баны',
                                        backgroundColor: 'rgb(91,74,186,0.8)',
                                        data: coubsData['banned']
                                    });
                                }

                                //отравим данные для графика
                                that.fillData(coubsData['dates'], datasets);
                            }

                            if (data['error']) {
                                that.error = 'Error: ' + data['error'];
                                that.clearData();
                            }
                        }

                        if (!data) {
                            that.clearData();
                        }
                    })
                    .catch((error) => {
                        console.error('catch error: ', error);
                        if (String(error) === 'Error: Request failed with status code 404') {
                            this.error = 'Не найдено';
                        } else {
                            this.error = 'Error: ' + error;
                        }

                        this.clearData();
                    });
            }
        },
        getCoubsCount: function (data) {
            let result = [];
            let temp = [];
            let dates = [];

            if (!data) {
                return [];
            }

            for (let i = 0, len = data.length; i < len; i++) {
                let date = data[i]['timestamp']['date'];

                if (!dates.includes(date)) {
                    dates.push(date);
                }

                if (!temp[date]) {
                    temp[date] = [];
                    temp[date]['views_count'] = 0;
                    temp[date]['repost_count'] = 0;
                    temp[date]['remixes_count'] = 0;
                    temp[date]['like_count'] = 0;
                    temp[date]['dislikes_count'] = 0;
                    temp[date]['is_kd'] = 0;
                    temp[date]['featured'] = 0;
                    temp[date]['banned'] = 0;
                }

                if (data[i]['views_count']) {
                    temp[date]['views_count'] += +data[i]['views_count'];
                }
                if (data[i]['repost_count']) {
                    temp[date]['repost_count'] += +data[i]['repost_count'];
                }
                if (data[i]['remixes_count']) {
                    temp[date]['remixes_count'] += +data[i]['remixes_count'];
                }
                if (data[i]['like_count']) {
                    temp[date]['like_count'] = +temp[date]['like_count'] + +data[i]['like_count'];
                }
                if (data[i]['dislikes_count']) {
                    temp[date]['dislikes_count'] += +data[i]['dislikes_count'];
                }
                if (data[i]['is_kd']) {
                    temp[date]['is_kd']++;
                }
                if (data[i]['featured']) {
                    temp[date]['featured']++;
                }
                if (data[i]['banned']) {
                    temp[date]['banned']++;
                }
            }

            result['views_count'] = [];
            result['repost_count'] = [];
            result['remixes_count'] = [];
            result['like_count'] = [];
            result['dislikes_count'] = [];
            result['is_kd'] = [];
            result['featured'] = [];
            result['banned'] = [];

            for (let i = 0, len = dates.length; i < len; i++) {
                let item = temp[dates[i]];

                result['views_count'].push(item['views_count']);
                result['repost_count'].push(item['repost_count']);
                result['remixes_count'].push(item['remixes_count']);
                result['like_count'].push(item['like_count']);
                result['dislikes_count'].push(item['dislikes_count']);
                result['is_kd'].push(item['is_kd']);
                result['featured'].push(item['featured']);
                result['banned'].push(item['banned']);
            }

            result['dates'] = dates;

            return result;
        },
        generatecolor: function () {

        },
        clearData: function () {
            this.coubsCount = '';
            this.isSelfCount = '';
            this.isRepostCount = '';
            this.likesCount = '';
            this.banCount = '';
            this.showChart = false;
            this.showLoader = false;
        },
    }
};
</script>