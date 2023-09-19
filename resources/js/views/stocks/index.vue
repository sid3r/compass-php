<template>
  <div class="inventory-container">
    <div class="data-padding">
      <div class="filters">
        <el-row :gutter="10">
          <el-col :span="8">
            <el-input v-model="query.keyword" clearable prefix-icon="el-icon-search" :placeholder="$t('table.search')" style="width: 300px;" class="filter-item" @keyup.enter.native="handleFilter" @clear="handleFilter" />
          </el-col>
          <el-col :span="16">
            <!-- <el-button type="primary" icon="el-icon-monitor" style="float: right;" @click="$router.push({ name: 'pos' })">
              {{ $t('route.pos') }}
            </el-button> -->
          </el-col>
        </el-row>
      </div>
      <div class="content-wrapper">
        <el-row :gutter="15">
          <el-col :span="16">
            <el-table
              v-loading="loading"
              element-loading-background="#FBFCFD"
              :data="stocks"
              border
              stripe
              style="width: 100%"
              :row-class-name="tableRowClassName"
            >
              <el-table-column
                prop="product_name"
                :label="$t('products.product')"
              />
              <el-table-column
                prop="storehouse_name"
                :label="$t('products.storehouse')"
                width="140"
                align="center"
                sortable
              />
              <el-table-column
                prop="min_qty"
                :label="$t('products.min_qty')"
                width="140"
                align="right"
              />
              <el-table-column
                :label="$t('products.stock_qty')"
                width="140"
                align="right"
              >
                <template slot-scope="scope">
                  {{ scope.row.total_purchases - scope.row.total_sales }}
                </template>
              </el-table-column>
              <el-table-column
                label="Limit"
                width="80"
                align="center"
              >
                <template slot-scope="scope">
                  <!-- {{ scope.row.min_qty }} -->
                  <span v-if="outOfStock(scope.row)" class="danger"><i class="el-icon-warning" /></span>
                </template>
              </el-table-column>
            </el-table>
            <!-- Pagination -->
            <pagination v-show="total>0" class="pagination" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getStocks" />
          </el-col>
          <el-col :span="8">
            <!-- <h4>Out of stocks</h4> -->
            <!-- <span>{{ outofstock_products }}</span> -->
          </el-col>
        </el-row>
        <!-- <pre>{{ stocks }}</pre> -->
      </div>
    </div>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination';
import Resource from '@/api/resource';
const stockResource = new Resource('stocks');
const storehouseResource = new Resource('storehouses');

export default {
  components: { Pagination },
  data() {
    return {
      loading: true,
      stocks: null,
      storehouses: null,
      total: 0,
      selected: null,
      selectedStore: {},
      query: {
        page: 1,
        limit: 30,
        store: '',
      },
    };
  },
  mounted() {
    this.getStocks();
    this.getStorehouses();
  },
  methods: {
    async getStocks() {
      this.loading = true;
      const data = await stockResource.list(this.query);
      this.stocks = data.data;
      this.total = data.total;
      this.loading = false;
      this.selected = null;
    },
    async getStorehouses() {
      this.loading = true;
      const data = await storehouseResource.list();
      this.storehouses = data;
      // console.log(data);
      this.loading = false;
    },
    handleFilter() {
      this.getStocks();
    },
    tableRowClassName(row) {
      var remaining = row.total_purchases - row.total_sales;
      var classN = '';
      if (remaining < row.min_qty) {
        classN = 'danger-row';
      }
      return classN;
    },
    outOfStock(row) {
      var remaining = row.total_purchases - row.total_sales;
      var status = false;
      if (remaining < row.min_qty) {
        status = true;
      }
      return status;
    },
  },
};
</script>

<style lang="scss">
.inventory-container{
  width: 100%;
  height: calc(100vh - 50px);
  overflow-y: scroll;
  .content-wrapper{
    border-bottom: 1px solid #f7f7f7;
  }
}
.danger {
  color: rgb(245, 26, 26) !important;
}
.el-table .warning-row {
  background: rgb(255, 227, 209) !important;
}
.pagination {
  padding: 30px 10px 0 10px !important;
}
</style>
