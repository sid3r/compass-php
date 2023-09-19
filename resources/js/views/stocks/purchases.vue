<template>
  <div class="purchases-wrapper data-padding">
    <!-- graph -->
    <div class="graph">
      <el-row v-if="roles.includes('admin')" :gutter="15">
        <el-col :xs="24" :sm="24" :md="24" :lg="24">
          <div v-loading="statsLoading" element-loading-background="#FBFCFD">
            <cost-chart v-if="!statsLoading" :styles="graphStyles" :labels="graph.labels" :data="graph.data" />
            <p class="text-center text-muted"><small>{{ $t('products.totalPurchasesTitle') }}</small></p>
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
              name: 'newpurchase'
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
              <el-option v-for="store in storehouses" :key="store.id" :value="store.id" :label="store.name" :disabled="store.status !== 'active'" />
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
      <el-row :gutter="15">
        <!-- purchases table -->
        <el-col :xs="24" :sm="24" :md="8" :lg="8">
          <div v-loading="loading" class="purchases-table" element-loading-background="#FBFCFD">
            <el-card :body-style="{padding: 0}" shadow="never">
              <el-row :gutter="10" class="titles">
                <el-col :span="8"><b>{{ $t('table.date') }}</b></el-col>
                <el-col :span="8"><b>{{ $t('products.storehouse') }}</b></el-col>
                <el-col :span="8" align="right"><b>{{ $t('products.total') }} ({{ currency }})</b></el-col>
              </el-row>
              <div v-for="purchase in formatedPurchases" :key="purchase.id" class="purchase-item" :class="{ 'active': selected === purchase }" @click="selectItem(purchase)">
                <el-row :gutter="10">
                  <el-col :span="8"><span>{{ purchase.created_at | parseTime('{d}-{m}-{y}') }}</span></el-col>
                  <el-col :span="8"><span>{{ purchase.storehouse.name }}</span></el-col>
                  <el-col :span="8" align="right"><span><b>{{ purchase.total | currencyFormat }}</b></span></el-col>
                </el-row>
              </div>
            </el-card>
          </div>
          <div v-if="!loading && total === 0" class="text-center text-muted">
            <span>{{ $t('nodata') }}</span>
          </div>
        </el-col>
        <!-- purchase view -->
        <el-col :xs="24" :sm="24" :md="16" :lg="16" class="hidden-sm-and-down">
          <div v-if="selected" class="purchase-wrapper">
            <div class="purchase-header">
              <el-row :gutter="15">
                <el-col :span="14">
                  <div class="price">
                    <span class="title">{{ $t('products.totalCost') }}</span>
                    <h3>{{ selected.total | currencyFormat }} {{ currency }}</h3>
                  </div>
                </el-col>
                <el-col v-if="roles.includes('admin')" :span="10">
                  <div class="price">
                    <span class="title">{{ $t('products.totalEstimated') }}</span>
                    <h3 class="text-success">{{ selected.total_estimated | currencyFormat }} {{ currency }}</h3>
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
                prop="product.discount"
                :label="$t('products.discount')"
                width="90"
                align="right"
              />
              <el-table-column
                :label="$t('products.cost')"
                width="100"
                align="right"
              >
                <template slot-scope="scope">
                  <b>{{ scope.row.cost | currencyFormat }}</b>
                </template>
              </el-table-column>
              <el-table-column
                v-if="roles.includes('admin')"
                :label="$t('products.estimated')"
                width="130"
                align="right"
              >
                <template slot-scope="scope">
                  <b class="text-success">{{ scope.row.estimated | currencyFormat }}</b>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </el-col>
      </el-row>
      <el-row :gutter="15">
        <!-- Pagination -->
        <pagination v-show="total>0" class="pagination text-center" :auto-scroll="true" :scroll-to="310" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getPurchases()" />
        <!-- :layout="'total, prev, next'" -->
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
const purchaseResource = new Resource('purchases');
const storehouseResource = new Resource('storehouses');
const statresource = new StatResource('purchases');

export default {
  components: { Pagination, CostChart },
  data() {
    return {
      loading: true,
      statsLoading: true,
      showFilters: false,
      purchases: [],
      stats: [],
      storehouses: null,
      total: 0,
      selected: null,
      selectedStore: {},
      query: {
        page: 1,
        limit: 30,
        store: '',
        from: '',
        to: '',
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
        height: '200px',
        position: 'relative',
      };
    },
    ...mapGetters([
      'roles',
    ]),
    formatedPurchases() {
      const purchases = this.purchases;
      purchases.forEach(purchase => {
        purchase.total = 0;
        purchase.total_estimated = 0;
        purchase.items.forEach(item => {
          const cost = item.product.unit_price * item.qty;
          const sale = item.product.unit_sale_price * item.qty;
          const discount = sale * item.product.discount / 100;
          const gain = sale - cost - discount;

          item.cost = cost;
          item.estimated = gain;
          purchase.total += cost;
          purchase.total_estimated += gain;
        });
      });
      return purchases;
    },
    graph() {
      const graph = { labels: [], data: [] };
      this.stats.map((item) => {
        graph.labels.push(item.month);
        graph.data.push(item.total);
        return true;
      });
      return graph;
    },
  },
  mounted() {
    this.getStats();
    this.getPurchases();
    this.getStorehouses();
  },
  methods: {
    async getStats() {
      this.statsLoading = true;
      const { data } = await statresource.purchases({ store: this.query.store });
      this.stats = data;
      this.statsLoading = false;
    },
    async getPurchases() {
      this.loading = true;
      const { data, meta } = await purchaseResource.list(this.query);
      this.purchases = data;
      this.total = meta.total;
      this.selected = data[0];
      this.loading = false;
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
      this.getPurchases();
    },
    selectItem(item) {
      // on md and down go to viewpurchase
      if (this.w_width > 992) {
        this.selected = item;
      } else {
        this.$router.push({ name: 'viewpurchase', params: { purchaseId: item.id }});
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.purchases-wrapper{
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
    .graph {
      min-height: 200px;
    }
    .purchases-table {
      min-height: 200px;
      .purchase-item {
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

.purchase-header {
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
