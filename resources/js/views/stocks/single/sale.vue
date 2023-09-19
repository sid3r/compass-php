<template>
  <div class="app-container">
    <div class="data-padding">
      <!-- {{ purchase }} -->
      <div v-if="!loading" class="purchase-header">
        <el-row :gutter="15">
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <div class="price">
              <span class="title">{{ $t('products.total') }}</span>
              <h3>{{ formatedSale.total | currencyFormat }}</h3>
            </div>
          </el-col>
          <el-col v-if="roles.includes('admin')" :xs="24" :sm="12" :md="12" :lg="12">
            <div class="price">
              <span class="title">{{ $t('products.profit') }}</span>
              <h3 class="text-success">{{ formatedSale.profit | currencyFormat }}</h3>
            </div>
          </el-col>
        </el-row>
      </div>
      <el-table
        v-loading="loading"
        element-loading-background="#FBFCFD"
        :data="formatedSale.items"
        border
        stripe
        style="width: 100%"
      >
        <el-table-column
          prop="product.name"
          :label="$t('products.product')"
          min-width="200"
        />
        <el-table-column
          prop="qty"
          :label="$t('products.qty')"
          width="70"
          align="right"
        />
        <el-table-column
          prop="item.product.unit_price"
          :label="$t('products.unit_price')"
          width="100"
          align="right"
        >
          <template slot-scope="scope">
            {{ scope.row.product.unit_price | currencyFormat }}
          </template>
        </el-table-column>
        <el-table-column
          prop="item.product.unit_sale_price"
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
          :label="$t('products.amount')"
          width="160"
          align="right"
        >
          <template slot-scope="scope">
            <b>{{ scope.row.cost | currencyFormat }}</b>
          </template>
        </el-table-column>
        <el-table-column
          v-if="roles.includes('admin')"
          :label="$t('products.profit')"
          width="160"
          align="right"
        >
          <template slot-scope="scope">
            <b class="text-success">{{ scope.row.profit | currencyFormat }}</b>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import { mapGetters } from 'vuex';
import Resource from '@/api/resource';
const saleResource = new Resource('sales');

export default {
  data() {
    return {
      purchaseId: null,
      loading: true,
      sale: {},
    };
  },
  computed: {
    ...mapGetters([
      'roles',
    ]),
    currency() {
      return getCurrency();
    },
    formatedSale() {
      const sale = this.sale;
      if (!this.loading) {
        sale.total = 0;
        sale.profit = 0;
        sale.items.forEach(item => {
          const cost = item.product.unit_price * item.qty;
          const sale_ = item.product.unit_sale_price * item.qty;
          const discount = sale_ * item.product.discount / 100;
          const gain = sale_ - cost - discount;

          item.cost = cost;
          item.profit = gain;
          sale.total += cost;
          sale.profit += gain;
        });
      }
      return sale;
    },
  },
  created() {
    if (this.$route.params.saleId) {
      this.saleId = this.$route.params.saleId;
      this.loadSale();
    }
  },
  methods: {
    async loadSale() {
      this.loading = true;
      const data = await saleResource.get(this.saleId);
      this.sale = data;
      this.loading = false;
    },
  },
};
</script>

<style lang="scss" scoped>
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
</style>
