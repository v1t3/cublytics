import { Line, mixins } from 'vue-chartjs';
const { reactiveProp } = mixins;

export default {
    extends: Line,
    mixins: [reactiveProp],
    props: ['options'],
    mounted () {
            // this.chartData создаётся внутри миксина
        this.renderChart(this.chartData, this.options);
    },
    watch: {
        chartData () {
            this.$data._chart.update()
        }
    }
}