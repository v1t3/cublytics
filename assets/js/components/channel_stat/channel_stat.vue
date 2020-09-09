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
                <line-chart v-if="showChart" :chart-data="dataCollection"></line-chart>
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
                    datasets: []
                },
            }
        },
        mounted() {
            this.getViews();
        },
        methods: {
            fillData: function (data, labels) {
                this.dataCollection = {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Все',
                            backgroundColor: 'rgba(78,103,245,0.8)',
                            data: data['total']
                        },
                        {
                            label: 'Свои',
                            backgroundColor: 'rgba(248,145,46,0.8)',
                            data: data['self']
                        },
                        {
                            label: 'Репосты',
                            backgroundColor: 'rgba(105,248,178,0.8)',
                            data: data['reposts']
                        }
                    ]
                }
            },
            getViews: function () {
                if (this.channel_name) {
                    this.clearData();
                    this.showLoader = true;

                    const bodyFormData = new FormData();
                    bodyFormData.set('params', JSON.stringify({
                        url: this.channel_name,
                        type: 'performance'
                    }));

                    axios({
                        method: 'post',
                        url: '/api/coub/getdata',
                        data: bodyFormData,
                        headers: {"X-Requested-With": "XMLHttpRequest"}
                    })
                        .then((response) => {
                            let data = response['data'];

                            this.error = '';
                            this.showLoader = false;

                            // console.log('data', data);

                            if (data) {
                                if (typeof data === 'string') {
                                    data = JSON.parse(data);
                                }

                                this.coubsCount = data['total_coubs'] || '';
                                this.isSelfCount = data['self_coubs'] || '';
                                this.isRepostCount = data['reposted'] || '';
                                this.likesCount = data['total_likes'] || '';
                                this.banCount = data['banned'] || '';

                                if (
                                    data['total_points_month'] &&
                                    Object.keys(data['total_points_month']).length > 0
                                ) {
                                    this.showChart = true;

                                    let chartPointsTotal = [];
                                    let chartPointsSelf = [];
                                    let chartPointsReposts = [];
                                    let coubsMonth = [];

                                    for (let i = 0, max = data['total_points_month'].length; i < max; i++) {
                                        chartPointsTotal[i] = data['total_points_month'][i]['count'];
                                        coubsMonth[i] = data['total_points_month'][i]['date'];
                                    }
                                    for (let i = 0, max = data['self_points_month'].length; i < max; i++) {
                                        chartPointsSelf[i] = data['self_points_month'][i]['count'];
                                    }
                                    for (let i = 0, max = data['reposts_points_month'].length; i < max; i++) {
                                        chartPointsReposts[i] = data['reposts_points_month'][i]['count'];
                                    }

                                    this.fillData(
                                        {
                                            'total'  : chartPointsTotal,
                                            'self'   : chartPointsSelf,
                                            'reposts': chartPointsReposts,
                                        },
                                        coubsMonth
                                    );
                                }

                                if (data['error']) {
                                    this.error = 'Error: ' + data['error'];
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
            getTestViews: function () {
                if ('' !== this.channel_name) {
                    this.clearData();
                    this.showLoader = true;

                    let data = { "total_coubs": 131, "self_coubs": 86, "total_likes": 16509, "reposted": 45, "total_points_month": [ { "date": "11.2017", "count": 1 }, { "date": "12.2017", "count": 0 }, { "date": "01.2018", "count": 2 }, { "date": "02.2018", "count": 2 }, { "date": "03.2018", "count": 8 }, { "date": "04.2018", "count": 5 }, { "date": "05.2018", "count": 8 }, { "date": "06.2018", "count": 2 }, { "date": "07.2018", "count": 3 }, { "date": "08.2018", "count": 2 }, { "date": "09.2018", "count": 5 }, { "date": "10.2018", "count": 0 }, { "date": "11.2018", "count": 1 }, { "date": "12.2018", "count": 1 }, { "date": "01.2019", "count": 4 }, { "date": "02.2019", "count": 0 }, { "date": "03.2019", "count": 4 }, { "date": "04.2019", "count": 6 }, { "date": "05.2019", "count": 6 }, { "date": "06.2019", "count": 2 }, { "date": "07.2019", "count": 4 }, { "date": "08.2019", "count": 8 }, { "date": "09.2019", "count": 5 }, { "date": "10.2019", "count": 11 }, { "date": "11.2019", "count": 14 }, { "date": "12.2019", "count": 2 }, { "date": "01.2020", "count": 5 }, { "date": "02.2020", "count": 3 }, { "date": "03.2020", "count": 4 }, { "date": "04.2020", "count": 6 }, { "date": "05.2020", "count": 1 }, { "date": "06.2020", "count": 1 } ], "self_points_month": [ { "date": "06.2020", "count": 1 }, { "date": "05.2020", "count": 0 }, { "date": "04.2020", "count": 4 }, { "date": "03.2020", "count": 4 }, { "date": "02.2020", "count": 1 }, { "date": "01.2020", "count": 3 }, { "date": "12.2019", "count": 0 }, { "date": "11.2019", "count": 2 }, { "date": "10.2019", "count": 1 }, { "date": "09.2019", "count": 1 }, { "date": "08.2019", "count": 6 }, { "date": "07.2019", "count": 3 }, { "date": "06.2019", "count": 0 }, { "date": "05.2019", "count": 2 }, { "date": "04.2019", "count": 5 }, { "date": "03.2019", "count": 4 }, { "date": "02.2019", "count": 0 }, { "date": "01.2019", "count": 4 }, { "date": "12.2018", "count": 1 }, { "date": "11.2018", "count": 1 }, { "date": "10.2018", "count": 0 }, { "date": "09.2018", "count": 5 }, { "date": "08.2018", "count": 2 }, { "date": "07.2018", "count": 3 }, { "date": "06.2018", "count": 2 }, { "date": "05.2018", "count": 8 }, { "date": "04.2018", "count": 5 }, { "date": "03.2018", "count": 8 }, { "date": "02.2018", "count": 2 }, { "date": "01.2018", "count": 2 }, { "date": "12.2017", "count": 0 }, { "date": "11.2017", "count": 1 } ], "reposts_points_month": [ { "date": "06.2020", "count": 0 }, { "date": "05.2020", "count": 1 }, { "date": "04.2020", "count": 2 }, { "date": "03.2020", "count": 0 }, { "date": "02.2020", "count": 2 }, { "date": "01.2020", "count": 2 }, { "date": "12.2019", "count": 2 }, { "date": "11.2019", "count": 12 }, { "date": "10.2019", "count": 10 }, { "date": "09.2019", "count": 4 }, { "date": "08.2019", "count": 2 }, { "date": "07.2019", "count": 1 }, { "date": "06.2019", "count": 2 }, { "date": "05.2019", "count": 4 }, { "date": "04.2019", "count": 1 }, { "date": "03.2019", "count": 0 }, { "date": "02.2019", "count": 0 }, { "date": "01.2019", "count": 0 }, { "date": "12.2018", "count": 0 }, { "date": "11.2018", "count": 0 }, { "date": "10.2018", "count": 0 }, { "date": "09.2018", "count": 0 }, { "date": "08.2018", "count": 0 }, { "date": "07.2018", "count": 0 }, { "date": "06.2018", "count": 0 }, { "date": "05.2018", "count": 0 }, { "date": "04.2018", "count": 0 }, { "date": "03.2018", "count": 0 }, { "date": "02.2018", "count": 0 }, { "date": "01.2018", "count": 0 }, { "date": "12.2017", "count": 0 }, { "date": "11.2017", "count": 0 } ] };

                    this.error = '';
                    this.showLoader = false;

                    console.log(data);

                    if (data) {
                        this.coubsCount = data['total_coubs'] || '';
                        this.isSelfCount = data['self_coubs'] || '';
                        this.isRepostCount = data['reposted'] || '';
                        this.likesCount = data['total_likes'] || '';
                        this.banCount = data['banned'] || '';

                        if (
                            data['total_points_month'] &&
                            Object.keys(data['total_points_month']).length > 0
                        ) {
                            this.showChart = true;

                            let chartPointsTotal = [];
                            let chartPointsSelf = [];
                            let chartPointsReposts = [];
                            let coubsMonth = [];

                            for (let i = 0, max = data['total_points_month'].length; i < max; i++) {
                                chartPointsTotal[i] = data['total_points_month'][i]['count'];
                                coubsMonth[i] = data['total_points_month'][i]['date'];
                            }
                            for (let i = 0, max = data['self_points_month'].length; i < max; i++) {
                                chartPointsSelf[i] = data['self_points_month'][i]['count'];
                            }
                            for (let i = 0, max = data['reposts_points_month'].length; i < max; i++) {
                                chartPointsReposts[i] = data['reposts_points_month'][i]['count'];
                            }

                            this.fillData(
                                {
                                    'total'  : chartPointsTotal,
                                    'self'   : chartPointsSelf,
                                    'reposts': chartPointsReposts,
                                },
                                coubsMonth
                            );
                        }

                        if (data['error']) {
                            this.error = 'Error: ' + data['error'];
                            this.clearData();
                        }
                    }

                    if (!data) {
                        this.clearData();
                    }
                }
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

<style>
    .small {
        max-width: 600px;
    }
</style>


