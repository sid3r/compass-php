<script>
import { Line } from 'vue-chartjs';
import { currencyFormat, numberFormatter } from '@/filters/';

export default {
  extends: Line,
  props: {
    data: {
      type: Array,
      required: true,
    },
    profit: {
      type: Array,
      required: false,
      default: () => {
        return [];
      },
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
          position: 'top',
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
    const datasets = [
      {
        label: this.$t('products.total'),
        borderColor: '#224FE4',
        pointBackgroundColor: 'white',
        borderWidth: 2,
        pointBorderColor: '#224FE4',
        backgroundColor: 'transparent',
        data: this.data,
      },
    ];

    if (this.profit) {
      datasets.push({
        label: this.$t('products.profit'),
        borderColor: '#2ba362',
        pointBackgroundColor: 'white',
        borderWidth: 2,
        pointBorderColor: '#2ba362',
        backgroundColor: 'transparent',
        data: this.profit,
      });
    }
    this.renderChart({
      labels: this.labels,
      datasets: datasets,
    }, this.options);
  },
};
</script>
