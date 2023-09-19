<template>
  <div class="app-container">
    <div class="data-padding">
      <!-- {{ purchase }} -->
      <div v-if="!loading" class="purchase-header">
        <el-row :gutter="15">
          <el-col :xs="24" :sm="24" :md="12" :lg="12">
            <div class="price">
              <span class="title">{{ $t('products.total') }}</span>
              <h3>{{ formatedPurchase.total | currencyFormat }} {{ currency }}</h3>
            </div>
          </el-col>
          <el-col v-if="roles.includes('admin')" :span="12" :xs="24" :sm="24" :md="12" :lg="12">
            <div class="price">
              <span class="title">{{ $t('products.totalEstimated') }}</span>
              <h3 class="text-success">{{ formatedPurchase.total_estimated | currencyFormat }} {{ currency }}</h3>
            </div>
          </el-col>
        </el-row>
      </div>
      <el-table
        v-loading="loading"
        element-loading-background="#FBFCFD"
        :data="formatedPurchase.items"
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
          :label="$t('products.cost')"
          width="160"
          align="right"
        >
          <template slot-scope="scope">
            <b>{{ scope.row.cost | currencyFormat }}</b>
          </template>
        </el-table-column>
        <el-table-column
          v-if="roles.includes('admin')"
          :label="$t('products.estimated')"
          width="160"
          align="right"
        >
          <template slot-scope="scope">
            <b class="text-success">{{ scope.row.estimated | currencyFormat }}</b>
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
const purchaseResource = new Resource('purchases');
export default {
  data() {
    return {
      purchaseId: null,
      loading: true,
      purchase: {},
    };
  },
  computed: {
    ...mapGetters([
      'roles',
    ]),
    currency() {
      return getCurrency();
    },
    formatedPurchase() {
      const purchase = this.purchase;
      if (!this.loading) {
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
      }
      return purchase;
    },
  },
  created() {
    if (this.$route.params.purchaseId) {
      this.purchaseId = this.$route.params.purchaseId;
      this.loadPurchase();
    }
  },
  methods: {
    async loadPurchase() {
      this.loading = true;
      const data = await purchaseResource.get(this.purchaseId);
      this.purchase = data;
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
