<template>
    <div class="component" id="coub-performance">
        <loader_gif v-if="showLoader"/>

        <div class="views">
            <div v-if="coub.views">Просмотры: {{ coub.views }}</div>
            <div v-if="coub.likes">Лайки: {{ coub.likes }}</div>
            <div v-if="coub.dislikes">Дизлайки: {{ coub.dislikes }}</div>
            <div v-if="coub.reposts">Репосты: {{ coub.reposts }}</div>
            <div v-if="coub.reposts">Рекоубы: {{ coub.recoubs }}</div>
            <div v-if="coub.kd">КД: {{ coub.kd }}</div>
            <div v-if="coub.featured">Фич: {{ coub.featured }}</div>
            <div v-if="coub.banned">Бан: {{ coub.banned }}</div>
        </div>
        <div class="info">
            <div class="small">
                <line-chart
                    v-if="showChart"
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

                        // console.log('data', data);

                        if (data) {
                            if (typeof data === 'string') {
                                data = JSON.parse(data);
                            }

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

                                this.pushToDataset(coubsData, 'views_count', 'Просмотры');
                                this.pushToDataset(coubsData, 'like_count', 'Лайки');
                                this.pushToDataset(coubsData, 'repost_count', 'Репосты');
                                this.pushToDataset(coubsData, 'remixes_count', 'Рекоубы');
                                //todo Реализовать отображение одиночной метки для кд, фича, бана
                                // this.pushToDataset(coubsData, 'is_kd', 'КД');
                                // this.pushToDataset(coubsData, 'featured', 'Фич');
                                // this.pushToDataset(coubsData, 'banned', 'Бан');

                                //отравим данные для графика
                                if (this.tempDataset) {
                                    that.fillData(coubsData['dates'], this.tempDataset);
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

        pushToDataset: function(data, item, label) {
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