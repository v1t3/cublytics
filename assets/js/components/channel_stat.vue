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
    import moment from 'moment';
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
                        backgroundColor: '#74d8ff',
                        borderColor: '#498298'
                    },
                    {
                        type: 'views_count',
                        label: 'Просмотры',
                        backgroundColor: '#2d34ff',
                        borderColor: '#161a73'
                    },
                    {
                        type: 'like_count',
                        label: 'Лайки',
                        backgroundColor: '#2dcfff',
                        borderColor: '#1b7a96',
                    },
                    {
                        type: 'dislikes_count',
                        label: 'Дизлайки',
                        backgroundColor: '#ff8e2d',
                        borderColor: '#965218'
                    },
                    {
                        type: 'repost_count',
                        label: 'Репосты',
                        backgroundColor: '#2dffb1',
                        borderColor: '#189667'
                    },
                    {
                        type: 'remixes_count',
                        label: 'Рекоубы',
                        backgroundColor: '#7e2dff',
                        borderColor: '#4a1898'
                    },
                    {
                        type: 'is_kd',
                        label: 'КД',
                        backgroundColor: '#ff8a8a',
                        borderColor: '#984949'
                    },
                    {
                        type: 'featured',
                        label: 'Фичи',
                        backgroundColor: '#c65991',
                        borderColor: '#bf4c85'
                    },
                    {
                        type: 'banned',
                        label: 'Баны',
                        backgroundColor: '#b674ff',
                        borderColor: '#7547a7'
                    }
                ],
                dataCollectionOptions: {
                    scales: {
                        xAxes: [],
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: false,
                                },
                            }
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
                },
                dates: {},
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
                    bodyFormData.set('timezone', this.$store.state.timezone);

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

                            if (data) {
                                if (typeof data === 'string') {
                                    data = JSON.parse(data);
                                }
                                // console.log('data stat', data);

                                if (
                                    undefined !== data['data'] &&
                                    undefined !== data['data']['total'] &&
                                    undefined !== data['data']['counts']
                                ) {
                                    this.channel = {
                                        followers: data['data']['total']['followers_count'],
                                        views: data['data']['total']['views_count'],
                                        likes: data['data']['total']['likes_count'],
                                        dislikes: data['data']['total']['dislikes_count'],
                                        reposts: data['data']['total']['repost_count'],
                                        recoubs: data['data']['total']['remixes_count'],
                                        kd: data['data']['total']['kd_count'],
                                        featured: data['data']['total']['featured_count'],
                                        banned: data['data']['total']['banned_count'],
                                    };

                                    coubsData = that.getCoubsCount(data['data']['counts']);

                                    if (coubsData) {
                                        that.showChart = true;

                                        for (let i = 0, len = this.chartsInfo.length; i < len; i++) {
                                            this.pushDataCollection(
                                                coubsData,
                                                this.chartsInfo[i]['type'],
                                                this.chartsInfo[i]['label'],
                                                this.chartsInfo[i]['backgroundColor'],
                                                this.chartsInfo[i]['borderColor'],
                                            );
                                        }
                                    }
                                }

                                if (
                                    undefined !== data['error'] &&
                                    undefined !== data['error']['message']
                                ) {
                                    that.error = data['error']['message'];
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

                if (!data) {
                    return [];
                }

                this.setDatesRange(this.statistic_type);

                result['dates'] = this.dates;

                if (undefined !== data['views_count']) {
                    result['views_count'] = this.prepareCount(data['views_count']);
                }

                if (undefined !== data['like_count']) {
                    result['like_count'] = this.prepareCount(data['like_count']);
                }

                if (undefined !== data['repost_count']) {
                    result['repost_count'] = this.prepareCount(data['repost_count']);
                }

                if (undefined !== data['recoubs_count']) {
                    result['recoubs_count'] = this.prepareCount(data['remixes_count']);
                }

                if (undefined !== data['dislikes_count']) {
                    result['dislikes_count'] = this.prepareCount(data['dislikes_count']);
                }

                return result;
            },

            prepareCount: function (data) {
                if (!data) {
                    return [];
                }

                let result = [];

                if (this.dates.length) {
                    for (let i = 0, len = this.dates.length; i < len; i++) {
                        if (data[this.dates[i]]) {
                            result.push(+data[this.dates[i]]);
                        } else {
                            result.push({});
                        }
                    }
                }

                return result;
            },

            pushDataCollection: function (coubsData, type, label, backgroundColor = '', borderColor = '') {
                let datasets = [];
                let temp = {};

                if (
                    coubsData[type] &&
                    coubsData[type].some(item => item !== 0)
                ) {
                    datasets.push({
                        label: label,                                           // заголовок датасета
                        backgroundColor: backgroundColor || this.generateColor(), // цвет фона
                        borderColor: borderColor || this.generateColor(),       // цвет линии
                        fill: true,                                             // отображать фон под линией
                        spanGaps: true,                                         // заполнять пустые промежутки
                        lineTension: 0,                                         // степень сглаживания углов
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

            setDatesRange: function (type) {
                let currentDate, stopDate;
                let result = [];
                let dateArray = [];

                if (type) {
                    switch (type) {
                        case 'day':
                            currentDate = moment().format('YYYY-MM-DD 23:59');
                            currentDate = moment(currentDate);
                            stopDate = moment(currentDate).format('YYYY-MM-DD 00:00');

                            while (currentDate.isAfter(stopDate)) {
                                dateArray.push(moment(currentDate).format('HH:00'));
                                currentDate = moment(currentDate).add(-1, 'hours');
                            }

                            break;
                        case 'week':
                            currentDate = moment();
                            stopDate = moment().add(-7, 'days');

                            while (currentDate >= stopDate) {
                                dateArray.push(moment(currentDate).format('DD.MM'));
                                currentDate = moment(currentDate).add(-1, 'days');
                            }

                            break;
                        case 'month1':
                            currentDate = moment();
                            stopDate = moment().add(-1, 'month');

                            while (currentDate >= stopDate) {
                                dateArray.push(moment(currentDate).format('DD.MM'));
                                currentDate = moment(currentDate).add(-1, 'days');
                            }

                            break;
                        case 'month6':
                            // получить последний день месяца
                            currentDate = moment().endOf('month');
                            stopDate = moment().startOf('month').add(-6, 'month');

                            while (currentDate >= stopDate) {
                                dateArray.push(moment(currentDate).format('MM.YYYY'));
                                currentDate = moment(currentDate).add(-1, 'month');
                            }

                            break;
                        case 'year':
                            // получить последний день месяца
                            currentDate = moment().endOf('month');
                            stopDate = moment().startOf('month').add(-1, 'year');

                            while (currentDate >= stopDate) {
                                dateArray.push(moment(currentDate).format('MM.YYYY'));
                                currentDate = moment(currentDate).add(-1, 'month');
                            }

                            break;
                    }

                    if (dateArray) {
                        result = dateArray.reverse();
                    }
                }

                this.dates = result;
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