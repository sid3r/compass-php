<template>
  <div class="dashboard-admin-container data-padding">
    <!-- widgets -->
    <Widgets />
    <h5 class="section-title">
      {{ $t('app.profitSummery') }}
    </h5>
    <div v-loading="statsLoading" class="graph" element-loading-background="#FBFCFD">
      <chart v-if="!statsLoading" :styles="graphStyles" :labels="graph.labels" :datasets="graph.datasets" />
    </div>
    <!-- <pre>
      {{ graph }}
    </pre> -->
    <!-- pending docs -->
    <pendingdocs />
  </div>
</template>

<script>
import { groupBy } from '@/utils/';
import StatResource from '@/api/stats';
const statResource = new StatResource('sales');

import Widgets from './../components/widgets';
import Pendingdocs from './../components/pendingdocs';
import Chart from './../components/chart';

export default {
  name: 'DashboardAdmin',
  components: { Widgets, Pendingdocs, Chart },
  data() {
    return {
      loading: true,
      documents: [],
      statsLoading: true,
      stats: [],
    };
  },
  computed: {
    graphStyles() {
      return {
        height: '240px',
        position: 'relative',
      };
    },
    graph() {
      const graph = { labels: [], datasets: [] };
      if (this.stats) {
        this.stats.forEach(store => {
          const dataset = { store: store[0], data: [] };
          // console.log(store[1]);
          store[1].forEach(sale => {
            if (!graph.labels.includes(sale.month)){
              graph.labels.push(sale.month);
            }
            dataset.data.push(parseFloat(sale.total_profit));
            // console.log(sale);
          });
          graph.datasets.push(dataset);
          // graph.labels.sort().reverse();
        });
      }
      return graph;
    },
  },
  mounted() {
    this.getStats();
  },
  methods: {
    async getStats() {
      this.statsLoading = true;
      const { data } = await statResource.stores();
      this.stats = Object.entries(groupBy(data, 'name'));
      // console.log(data);
      this.statsLoading = false;
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-admin-container {
  .section-title {
    padding: 0 0 20px 0;
    color: rgb(107, 107, 107);
    margin: 0;
    font-size: 13px;
    text-transform: uppercase;
  }
  .graph {
    min-height: 260px;
  }
  .recent-docs{
    min-height: 400px;
    .titles{
      margin-bottom: 15px;
      padding: 0 15px;
      .title{
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #B7BEC7;
      }
    }
  }
}
</style>
