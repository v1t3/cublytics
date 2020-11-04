<template>
    <div class="" id="coub-performance">
        <loader_gif v-if="showLoader"/>

        <div class="views-total">
            <div class="views-total_row" v-if="coub.views">
                <span class="views-total_title">Просмотры:</span>
                <span class="views-total_text">{{ coub.views }}</span>
            </div>
            <div class="views-total_row" v-if="coub.likes">
                <span class="views-total_title">Лайки:</span>
                <span class="views-total_text">{{ coub.likes }}</span>
            </div>
            <div class="views-total_row" v-if="coub.dislikes">
                <span class="views-total_title">Дизлайки:</span>
                <span class="views-total_text">{{ coub.dislikes }}</span>
            </div>
            <div class="views-total_row" v-if="coub.reposts">
                <span class="views-total_title">Репосты:</span>
                <span class="views-total_text">{{ coub.reposts }}</span>
            </div>
            <div class="views-total_row" v-if="coub.reposts">
                <span class="views-total_title">Рекоубы:</span>
                <span class="views-total_text">{{ coub.recoubs }}</span>
            </div>
            <div class="views-total_row" v-if="coub.kd">
                <span class="views-total_title">КД:</span>
                <span class="views-total_text">{{ coub.kd }}</span>
            </div>
            <div class="views-total_row" v-if="coub.featured">
                <span class="views-total_title">Фич:</span>
                <span class="views-total_text">{{ coub.featured }}</span>
            </div>
            <div class="views-total_row" v-if="coub.banned">
                <span class="views-total_title">Бан:</span>
                <span class="views-total_text">{{ coub.banned }}</span>
            </div>
        </div>

        <div class="chart-wrap">
            <div class="chart chart-info"
                 v-for="chart in charts">
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
    import moment from "moment";

    export default {
        name: "coub_stat",
        props: {
            coub_id: {
                type: Number,
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
                coub: {
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
                tempDataset: [],
                coubsData: null,
                error: null,
                charts: {},
                chartsInfo: [
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
            this.getCoubData();
        },
        methods: {
            getCoubData: function () {
                let that = this;

                if (this.coub_id) {
                    this.clearData();
                    this.showLoader = true;

                    const bodyFormData = new FormData();
                    bodyFormData.set('coub_id', String(this.coub_id));
                    bodyFormData.set('statistic_type', this.statistic_type);

                    axios({
                        method: 'post',
                        url: '/api/coub/get_coub_stat',
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

                                if (
                                    undefined !== data['data'] &&
                                    data['data'].length
                                ) {
                                    let lastCoub = data['data'][data['data'].length - 1];

                                    this.coub = {
                                        views: lastCoub['views_count'],
                                        likes: lastCoub['like_count'],
                                        dislikes: lastCoub['dislikes_count'],
                                        reposts: lastCoub['repost_count'],
                                        recoubs: lastCoub['remixes_count'],
                                        kd: lastCoub['is_kd'],
                                        featured: lastCoub['featured'],
                                        banned: lastCoub['banned'],
                                    };

                                    coubsData = that.getCoubsCount(data['data']);

                                    if (coubsData) {
                                        that.showChart = true;

                                        //todo Реализовать отображение одиночной метки для кд, фича, бана
                                        for (let i = 0, len = this.chartsInfo.length; i < len; i++) {
                                            this.pushDataCollection(
                                                coubsData,
                                                this.chartsInfo[i]['type'],
                                                this.chartsInfo[i]['label'],
                                                this.chartsInfo[i]['color'],
                                            );
                                        }
                                    }
                                }

                                if (
                                    'error' === data['result'] &&
                                    undefined !== data['error'] &&
                                    undefined !== data['error']['message']
                                ) {
                                    this.error = data['error']['message'];
                                    this.clearData();
                                }
                            }

                            if (!data) {
                                this.clearData();
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

                dates = this.getDatesRange(this.statistic_type);

                for (let i = 0, len = data.length; i < len; i++) {
                    let date = data[i]['timestamp'];

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

                    if (item) {
                        result['views_count'].push(item['views_count']);
                        result['repost_count'].push(item['repost_count']);
                        result['remixes_count'].push(item['remixes_count']);
                        result['like_count'].push(item['like_count']);
                        result['dislikes_count'].push(item['dislikes_count']);
                        result['is_kd'].push(item['is_kd']);
                        result['featured'].push(item['featured']);
                        result['banned'].push(item['banned']);
                    }
                }

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

            pushToDataset: function (data, item, label) {
                if (
                    data[item] &&
                    data[item].some(item => item !== 0)
                ) {
                    this.tempDataset.push({
                        label: label,
                        backgroundColor: this.generateColor(),
                        data: data[item]
                    });
                }
            },

            generateColor: function () {
                let r, g, b, opacity = 0.8;

                r = Math.floor(Math.random() * (256));
                g = Math.floor(Math.random() * (256));
                b = Math.floor(Math.random() * (256));

                return 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')';
            },

            getDatesRange: function(type) {
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
                            currentDate = moment();
                            stopDate = moment().add(-6, 'month');

                            while (currentDate >= stopDate) {
                                dateArray.push(moment(currentDate).format('DD.MM.YYYY'));
                                currentDate = moment(currentDate).add(-1, 'days');
                            }

                            break;
                        case 'year':
                            currentDate = moment();
                            stopDate = moment().add(-1, 'year');

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

                return result;
            },

            clearData: function () {
                this.coub = {
                    views: '',
                    reposts: '',
                    likes: '',
                    dislikes: '',
                    kd: '',
                    featured: '',
                    banned: '',
                };
                this.tempDataset = [];
                this.showChart = false;
                this.showLoader = false;
            }
        }
    };
</script>