<template>
    <div class="" id="channel-performance">
        <loader_gif v-if="showLoader"/>

        <div class="views-total">
            <div class="views-total_row" v-if="channel.followers">
                <span class="views-total_title">Подписчики:</span>
                <span class="views-total_text">{{ channel.followers }}</span>
            </div>
            <div class="views-total_row" v-if="channel.views">
                <span class="views-total_title">Просмотры:</span>
                <span class="views-total_text">{{ channel.views }}</span>
            </div>
            <div class="views-total_row" v-if="channel.likes">
                <span class="views-total_title">Лайки:</span>
                <span class="views-total_text">{{ channel.likes }}</span>
            </div>
            <div class="views-total_row" v-if="channel.dislikes">
                <span class="views-total_title">Дизлайки:</span>
                <span class="views-total_text">{{ channel.dislikes }}</span>
            </div>
            <div class="views-total_row" v-if="channel.reposts">
                <span class="views-total_title">Репосты:</span>
                <span class="views-total_text">{{ channel.reposts }}</span>
            </div>
            <div class="views-total_row" v-if="channel.reposts">
                <span class="views-total_title">Рекоубы:</span>
                <span class="views-total_text">{{ channel.recoubs }}</span>
            </div>
            <div class="views-total_row" v-if="channel.kd">
                <span class="views-total_title">КД:</span>
                <span class="views-total_text">{{ channel.kd }}</span>
            </div>
            <div class="views-total_row" v-if="channel.featured">
                <span class="views-total_title">Фичи:</span>
                <span class="views-total_text">{{ channel.featured }}</span>
            </div>
            <div class="views-total_row" v-if="channel.banned">
                <span class="views-total_title">Баны:</span>
                <span class="views-total_text">{{ channel.banned }}</span>
            </div>
        </div>

        <div class="chart-wrap">
            <div class="chart chart-info" v-for="chart in charts">
                <div class="chart-title">
                    <span>{{ chart.title }}</span>
                </div>
                <div class="small">
                    <line-chart v-if="showChart"
                                :chart-data="chart.dataCollection"
                                :options="chart.dataCollection.options"
                                :width="lineChart.width"
                                :height="lineChart.height"
                    ></line-chart>
                </div>
            </div>
        </div>

        {{ error }}
    </div>
</template>

<script>
    import axios from "axios";
    import LineChart from './LineChart.js';
    import Loader_gif from "./loader_gif";

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
                channel: {
                    followers: '',
                    views: '',
                    likes: '',
                    dislikes: '',
                    reposts: '',
                    recoubs: '',
                    kd: '',
                    featured: '',
                    banned: '',
                },
                showLoader: false,
                showChart: false,
                error: null,
                charts: {},
                chartsInfo: [
                    {
                        type: 'followers_count',
                        label: 'Подписчики',
                        color: ''
                    },
                    {
                        type: 'views_count',
                        label: 'Просмотры',
                        color: ''
                    },
                    {
                        type: 'like_count',
                        label: 'Лайки',
                        color: ''
                    },
                    {
                        type: 'dislikes_count',
                        label: 'Дизлайки',
                        color: ''
                    },
                    {
                        type: 'repost_count',
                        label: 'Репосты',
                        color: ''
                    },
                    {
                        type: 'remixes_count',
                        label: 'Рекоубы',
                        color: ''
                    },
                    {
                        type: 'is_kd',
                        label: 'КД',
                        color: ''
                    },
                    {
                        type: 'featured',
                        label: 'Фичи',
                        color: ''
                    },
                    {
                        type: 'banned',
                        label: 'Баны',
                        color: ''
                    }
                ],
                dataCollectionOptions: {
                    scales: {
                        yAxes: [
                            {ticks: {beginAtZero: true}}
                        ]
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                },
                lineChart: {
                    // width: 500,
                    // height: 400
                }
            }
        },
        mounted() {
            this.getChannelData();
        },
        methods: {
            getChannelData: function () {
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

                                    for (let i = 0, len = this.chartsInfo.length; i < len; i++) {
                                        this.pushDataCollection(
                                            coubsData,
                                            this.chartsInfo[i]['type'],
                                            this.chartsInfo[i]['label'],
                                            this.chartsInfo[i]['color'],
                                        );
                                    }
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
                const reducer = (accumulator, currentValue) => accumulator + currentValue;

                if (!data) {
                    return [];
                }

                for (let i = 0, len = data.length; i < len; i++) {
                    let date = data[i]['timestamp'];

                    if (!dates.includes(date)) {
                        dates.push(date);
                    }

                    if (!temp[date]) {
                        temp[date] = [];
                        // temp[date]['followers_count'] = 0;
                        temp[date]['views_count'] = 0;
                        temp[date]['repost_count'] = 0;
                        temp[date]['remixes_count'] = 0;
                        temp[date]['like_count'] = 0;
                        temp[date]['dislikes_count'] = 0;
                        temp[date]['is_kd'] = 0;
                        temp[date]['featured'] = 0;
                        temp[date]['banned'] = 0;
                    }

                    // if (data[i]['followers_count']) {
                    //     temp[date]['followers_count'] += +data[i]['followers_count'];
                    // }
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
                    // if (data[i]['is_kd']) {
                    //     temp[date]['is_kd'] = +data[i]['is_kd'];
                    // }
                    // if (data[i]['featured']) {
                    //     temp[date]['featured'] = +data[i]['featured'];
                    // }
                    // if (data[i]['banned']) {
                    //     temp[date]['banned'] = +data[i]['banned'];
                    // }
                }

                // result['followers_count'] = [];
                result['views_count'] = [];
                result['repost_count'] = [];
                result['remixes_count'] = [];
                result['like_count'] = [];
                result['dislikes_count'] = [];
                // result['is_kd'] = [];
                // result['featured'] = [];
                // result['banned'] = [];

                for (let i = 0, len = dates.length; i < len; i++) {
                    let item = temp[dates[i]];

                    // result['followers_count'].push(item['followers_count']);
                    result['views_count'].push(item['views_count']);
                    result['like_count'].push(item['like_count']);
                    result['repost_count'].push(item['repost_count']);
                    result['remixes_count'].push(item['remixes_count']);
                    result['dislikes_count'].push(item['dislikes_count']);
                    // result['is_kd'].push(item['is_kd']);
                    // result['featured'].push(item['featured']);
                    // result['banned'].push(item['banned']);
                }

                //todo Настроить правильное получение кол-ва подписчиков, кд, фичей
                // this.channel.followers = result['followers_count'].reduce(reducer, 0);
                this.channel.views = result['views_count'].reduce(reducer, 0);
                this.channel.likes = result['like_count'].reduce(reducer, 0);
                this.channel.dislikes = result['dislikes_count'].reduce(reducer, 0);
                this.channel.reposts = result['repost_count'].reduce(reducer, 0);
                this.channel.recoubs = result['remixes_count'].reduce(reducer, 0);
                // this.channel.kd = result['is_kd'].reduce(reducer, 0);
                // this.channel.featured = result['featured'].reduce(reducer, 0);
                // this.channel.banned = result['banned'].reduce(reducer, 0);

                result['dates'] = dates;

                return result;
            },

            pushDataCollection: function (coubsData, type, label, bckndColor = '') {
                let datasets = [];
                let temp = {};

                if (
                    coubsData[type] &&
                    coubsData[type].some(item => item !== 0)
                ) {
                    datasets.push({
                        label: label,
                        backgroundColor: bckndColor || this.generateColor(),
                        data: coubsData[type]
                    });

                    temp[type] = {
                        title: label,
                        dataCollection: {
                            labels: coubsData['dates'],
                            datasets: datasets,
                            options: this.dataCollectionOptions
                        }
                    };

                    Object.assign(this.charts, temp);
                }
            },

            generateColor: function () {
                let r, g, b, opacity = 0.8;

                r = Math.floor(Math.random() * (256));
                g = Math.floor(Math.random() * (256));
                b = Math.floor(Math.random() * (256));

                return 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')';
            },

            clearData: function () {
                this.channel = {
                    followers: '',
                    views: '',
                    reposts: '',
                    likes: '',
                    dislikes: '',
                    kd: '',
                    featured: '',
                    banned: '',
                };
                this.showChart = false;
                this.showLoader = false;
            },
        }
    };
</script>