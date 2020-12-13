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
                },
                dates: {},
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
                                                this.chartsInfo[i]['backgroundColor'],
                                                this.chartsInfo[i]['borderColor'],
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

                if (!data) {
                    return [];
                }

                this.setDatesRange(this.statistic_type);

                result['dates'] = this.dates;

                if (undefined !== data['views_count']) {
                    result['views_count'] = this.getPreparedCount(data['views_count']);
                }

                if (undefined !== data['like_count']) {
                    result['like_count'] = this.getPreparedCount(data['like_count']);
                }

                if (undefined !== data['repost_count']) {
                    result['repost_count'] = this.getPreparedCount(data['repost_count']);
                }

                if (undefined !== data['recoubs_count']) {
                    result['recoubs_count'] = this.getPreparedCount(data['remixes_count']);
                }

                if (undefined !== data['dislikes_count']) {
                    result['dislikes_count'] = this.getPreparedCount(data['dislikes_count']);
                }

                return result;
            },

            getPreparedCount: function (data) {
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

            setDatesRange: function(type) {
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