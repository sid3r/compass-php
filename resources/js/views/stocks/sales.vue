<template>
  <div class="sales-wrapper data-padding">
    <!-- chart -->
    <div class="graph">
      <el-row v-if="roles.includes('admin')" :gutter="15">
        <el-col :xs="24" :sm="24" :md="24" :lg="24">
          <div v-loading="statsLoading" element-loading-background="#FBFCFD">
            <cost-chart v-if="!statsLoading" :styles="graphStyles" :labels="graph.labels" :data="graph.data" :profit="graph.profit" />
            <p class="text-center text-muted"><small>{{ $t('products.totalSalesTitle') }}</small></p>
          </div>
        </el-col>
      </el-row>
    </div>
    <!-- filters -->
    <div class="filters">
      <el-row :gutter="10">
        <!-- filter btn -->
        <el-col :span="3">
          <el-button class="el-button-mobile" icon="el-icon-s-operation" @click="showFilters = !showFilters">{{ $t('table.filter') }}</el-button>
        </el-col>
        <!-- new btn -->
        <el-col :span="21">
          <el-button
            class="el-button-mobile fr"
            type="primary"
            icon="el-icon-plus"
            @click="$router.push({
              name: 'newsale'
            })"
          >
            {{ $t('table.add') }}
          </el-button>
        </el-col>
      </el-row>
      <!-- filters -->
      <transition name="el-zoom-in-top">
        <el-row v-if="showFilters" :gutter="10" class="mt-15">
          <el-col :xs="8" :sm="6" :md="5" :lg="4">
            <el-select v-model="query.store" clearable class="fw" :placeholder="$t('products.storehouse')" @change="handleFilter()">
              <el-option v-for="store in storehouses" :key="store.id" :value="store.id" :label="store.name" />
            </el-select>
          </el-col>
          <el-col :xs="8" :sm="6" :md="5" :lg="4">
            <el-date-picker
              v-model="query.from"
              type="date"
              :placeholder="$t('table.from')"
              format="dd/MM/yyyy"
              clearable
              class="fw"
              @change="handleFilter()"
            />
          </el-col>
          <el-col :xs="8" :sm="6" :md="5" :lg="4">
            <el-date-picker
              v-model="query.to"
              type="date"
              :placeholder="$t('table.to')"
              format="dd/MM/yyyy"
              clearable
              class="fw"
              @change="handleFilter()"
            />
          </el-col>
        </el-row>
      </transition>
    </div>
    <div class="content-wrapper">
      <!-- table -->
      <el-row :gutter="15">
        <el-col :xs="24" :sm="24" :md="8" :lg="8">
          <div v-loading="loading" class="sales-table" element-loading-background="#FBFCFD">
            <el-card :body-style="{padding: 0}" shadow="never">
              <el-row :gutter="10" class="titles">
                <el-col :span="8"><b>{{ $t('table.date') }}</b></el-col>
                <el-col :span="8"><b>{{ $t('products.storehouse') }}</b></el-col>
                <el-col :span="8" align="right"><b>{{ $t('products.total') }} ({{ currency }})</b></el-col>
              </el-row>
              <div v-for="sale in formatedSales" :key="sale.id" class="sale-item" :class="{ 'active': selected === sale }" @click="selectItem(sale)">
                <el-row :gutter="10">
                  <el-col :span="8"><span>{{ sale.created_at | parseTime('{d}-{m}-{y}') }}</span></el-col>
                  <el-col :span="8"><span>{{ sale.storehouse.name }}</span></el-col>
                  <el-col :span="8" align="right"><span><b>{{ sale.total | currencyFormat }}</b></span></el-col>
                </el-row>
              </div>
            </el-card>
          </div>
          <div v-if="!loading && total === 0" class="text-center text-muted">
            <span>{{ $t('nodata') }}</span>
          </div>
        </el-col>
        <!-- view selected sale -->
        <el-col :xs="24" :sm="24" :md="16" :lg="16" class="hidden-sm-and-down">
          <div v-if="selected" class="sale-wrapper">
            <div class="sale-header">
              <el-row :gutter="15">
                <el-col :span="14">
                  <div class="price">
                    <span class="title">{{ $t('products.total') }}</span>
                    <h3>{{ selected.total | currencyFormat }} {{ currency }}</h3>
                  </div>
                </el-col>
                <el-col v-if="roles.includes('admin')" :span="10">
                  <div class="price">
                    <span class="title">{{ $t('products.totalProfit') }}</span>
                    <h3 class="text-success">{{ selected.profit | currencyFormat }} {{ currency }}</h3>
                  </div>
                </el-col>
              </el-row>
            </div>
            <el-table
              :data="selected.items"
              border
              stripe
              style="width: 100%"
            >
              <el-table-column
                prop="product.name"
                :label="$t('products.product')"
                min-width="180"
              />
              <el-table-column
                prop="qty"
                :label="$t('products.qty')"
                width="70"
                align="right"
              />
              <el-table-column
                prop="unit_price"
                :label="$t('products.unit_price')"
                width="100"
                align="right"
              >
                <template slot-scope="scope">
                  {{ scope.row.product.unit_price | currencyFormat }}
                </template>
              </el-table-column>
              <el-table-column
                prop="unit_sale_price"
                :label="$t('products.unit_sale_price')"
                width="100"
                align="right"
              >
                <template slot-scope="scope">
                  {{ scope.row.product.unit_sale_price | currencyFormat }}
                </template>
              </el-table-column>
              <el-table-column
                :label="$t('products.amount')"
                width="120"
                align="right"
              >
                <template slot-scope="scope">
                  <b>{{ scope.row.cost | currencyFormat }}</b>
                </template>
              </el-table-column>
              <el-table-column
                v-if="roles.includes('admin')"
                :label="$t('products.profit')"
                width="100"
                align="right"
              >
                <template slot-scope="scope">
                  <b class="text-success">{{ scope.row.gain | currencyFormat }}</b>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </el-col>
      </el-row>
      <el-row :gutter="15">
        <!-- Pagination -->
        <pagination v-show="total>0" class="pagination text-center" :auto-scroll="true" :scroll-to="310" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getSales()" />
      </el-row>
    </div>
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import { mapGetters } from 'vuex';
import CostChart from './charts/cost';
import Pagination from '@/components/Pagination';
import Resource from '@/api/resource';
import StatResource from '@/api/stats';
const salesResource = new Resource('sales');
const storehouseResource = new Resource('storehouses');
const statResource = new StatResource('sales');

export default {
  components: { Pagination, CostChart },
  data() {
    return {
      loading: true,
      statsLoading: true,
      showFilters: false,
      sales: [],
      stats: [],
      storehouses: null,
      total: 0,
      selected: null,
      selectedStore: {
        id: null,
        name: null,
      },
      query: {
        page: 1,
        limit: 30,
        store: '',
      },
    };
  },
  computed: {
    currency() {
      return getCurrency();
    },
    w_width() {
      return window.innerWidth;
    },
    graphStyles() {
      return {
        height: '240px',
        position: 'relative',
      };
    },
    ...mapGetters([
      'roles',
    ]),
    formatedSales() {
      const sales = this.sales;
      sales.forEach(sale => {
        sale.total = 0;
        sale.profit = 0;
        sale.items.forEach(item => {
          const cost = item.product.unit_price * item.qty;
          const sale_ = item.product.unit_sale_price * item.qty;
          const discount = sale_ * item.product.discount / 100;
          const gain = sale_ - cost - discount;

          item.cost = cost;
          item.gain = gain;
          sale.total += cost;
          sale.profit += gain;
        });
      });
      return sales;
    },
    graph() {
      const graph = { labels: [], data: [], profit: [] };
      this.stats.map((item) => {
        graph.labels.push(item.month);
        graph.data.push(item.total);
        graph.profit.push(item.total_profit);
        return true;
      });
      return graph;
    },
  },
  mounted() {
    this.getStats();
    this.getSales();
    this.getStorehouses();
  },
  methods: {
    async getStats() {
      this.statsLoading = true;
      const { data } = await statResource.sales({ store: this.query.store });
      this.stats = data;
      this.statsLoading = false;
    },
    async getSales() {
      this.loading = true;
      const { data, meta } = await salesResource.list(this.query);
      this.sales = data;
      this.total = meta.total;
      this.loading = false;
      this.selected = data[0];
    },
    async getStorehouses() {
      const data = await storehouseResource.list();
      this.storehouses = data;
      if (data.length === 1){
        this.query.store = data[0].id;
      }
    },
    handleFilter() {
      this.selected = null;
      this.getStats();
      this.getSales();
    },
    selectItem(item) {
      // on md and down go to viewpurchase
      if (this.w_width > 992) {
        this.selected = item;
      } else {
        this.$router.push({ name: 'viewsale', params: { saleId: item.id }});
      }
    },
    showDocument(doc) {
      console.log(doc);
    },
  },
};
</script>

<style lang="scss" scoped>
.sales-wrapper{
  width: 100%;
  overflow: scroll;
  height: calc(100% - 50px);
  .graph {
    height: 290px;
  }
  .filters {
    margin-top: 20px;
    label {
      font-size: 12px;
      color: #555;
      margin-right: 20px;
    }
    .el-select {
      flex: 1;
    }
  }
  .content-wrapper{
    .sales-table {
      min-height: 200px;
      .sale-item {
        font-size: 13px;
        border-bottom: 1px solid #f7f7f7;
        cursor: pointer;
        padding: 10px 14px;
        &:hover {
          background: #f7f7f7;
        }
        &.active {
          background: #ECF0FD;
        }
      }
    }
    .pagination {
      padding: 30px 10px 0 10px !important;
    }
  }
}
.titles {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  color: #B7BEC7;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-bottom: 1px solid #f7f7f7;
}
.sale-header {
  .price {
    text-align: right;
    h3 {
      margin-top: 0;
      padding-top: 5px;
    }
    span {
      font-size: 11px;
      text-transform: uppercase;
      font-weight: bold;
      color: #aaa;
    }
  }
}
.fw{
  width: 100%;
}
.mt-15{
  margin-top: 15px;
}
</style>
