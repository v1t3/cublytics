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
                    // {
                    //     type: 'is_kd',
                    //     label: 'КД',
                    //     color: ''
                    // },
                    // {
                    //     type: 'featured',
                    //     label: 'Фичи',
                    //     color: ''
                    // },
                    // {
                    //     type: 'banned',
                    //     label: 'Баны',
                    //     color: ''
                    // }
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
                    bodyFormData.set('timezone', this.$store.state.timezone);

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

                                // console.log('data', data);

                                if (
                                    undefined !== data['data'] &&
                                    Object.keys(data['data']).length
                                ) {
                                    //fixme не работает с ассоциативным массивом (объектом) и это глупый кусок
                                    // let lastCoub = data['data'][data['data'].length - 1];
                                    //
                                    // this.coub = {
                                    //     views: lastCoub['views_count'],
                                    //     likes: lastCoub['like_count'],
                                    //     dislikes: lastCoub['dislikes_count'],
                                    //     reposts: lastCoub['repost_count'],
                                    //     recoubs: lastCoub['remixes_count'],
                                    //     // kd: lastCoub['is_kd'],
                                    //     // featured: lastCoub['featured'],
                                    //     // banned: lastCoub['banned'],
                                    // };

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

                // console.log('dates', dates);
                // console.log('data getCoubsCount', data);

                // Забиваем temp dummy данными
                if (dates && dates.length) {
                    for (let i = 0, len = dates.length; i < len; i++) {
                        let date = dates[i];
                        temp[date] = [];

                        if (undefined !== data[date]) {
                            if (data[date]['views_count']) {
                                temp[date]['views_count'] = +data[date]['views_count'];
                            }
                            if (data[date]['repost_count']) {
                                temp[date]['repost_count'] = +data[date]['repost_count'];
                            }
                            if (data[date]['recoubs_count']) {
                                temp[date]['remixes_count'] = +data[date]['recoubs_count'];
                            }
                            if (data[date]['like_count']) {
                                temp[date]['like_count'] = +data[date]['like_count'];
                            }
                            if (data[date]['dislikes_count']) {
                                temp[date]['dislikes_count'] = +data[date]['dislikes_count'];
                            }
                        }
                    }
                }

                result['views_count'] = [];
                result['repost_count'] = [];
                result['remixes_count'] = [];
                result['like_count'] = [];
                result['dislikes_count'] = [];

                // console.log('temp1', temp);

                for (let i = 0, len = dates.length; i < len; i++) {
                    let item = temp[dates[i]];

                    if (item) {
                        result['views_count'].push(item['views_count']);
                        result['like_count'].push(item['like_count']);
                        result['repost_count'].push(item['repost_count']);
                        result['remixes_count'].push(item['remixes_count']);
                        result['dislikes_count'].push(item['dislikes_count']);
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
                        label: label,       // заголовок датасета
                        backgroundColor: bckndColor || this.generateColor(), // цвет фона
                        // borderColor: bckndColor || this.generateColor(),     // цвет линии
                        fill: true,         // отображать фон под линией
                        spanGaps: true,     // заполнять пустые промежутки
                        lineTension: 0,     // степень сглаживания углов
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