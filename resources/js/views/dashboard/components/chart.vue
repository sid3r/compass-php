<script>
import { Line } from 'vue-chartjs';
import { currencyFormat, numberFormatter } from '@/filters/';
// currencyFormat
// datasets
export default {
  extends: Line,
  props: {
    datasets: {
      type: Array,
      required: true,
    },
    labels: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: false,
              callback: function(label, index, labels) {
                return numberFormatter(label);
              },
            },
            gridLines: {
              display: true,
            },
          }],
          xAxes: [{
            gridLines: {
              display: true,
            },
          }],
        },
        legend: {
          display: true,
          position: 'bottom',
        },
        responsive: true,
        maintainAspectRatio: false,
        hover: {
          animationDuration: 0,
        },
        responsiveAnimationDuration: 0,
        tooltips: {
          callbacks: {
            label: function(tt, data) {
              // the second argument has the dataset label
              return data.datasets[tt.datasetIndex].label + ': ' + currencyFormat(tt.yLabel);
            },
          },
        },
        // title: {
        //   display: true,
        //   text: 'Cost totals',
        // },
      },
    };
  },
  mounted() {
    const datasets = [];
    const colors = ['#1539AE', '#009688', '#8a4af3', '#922820', '#795548'];
    this.datasets.forEach((dataset, index) => {
      datasets.push({
        label: dataset.store,
        borderColor: colors[index],
        pointBackgroundColor: 'white',
        borderWidth: 2,
        pointBorderColor: colors[index],
        backgroundColor: 'transparent',
        data: dataset.data,
      });
    });
    this.renderChart({
      labels: this.labels,
      datasets: datasets,
    }, this.options);
  },
};
</script>
