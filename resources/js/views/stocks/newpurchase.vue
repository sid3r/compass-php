<template>
  <div class="data-padding">
    <!-- filters -->
    <div class="filters">
      <el-autocomplete
        v-model="found"
        prefix-icon="el-icon-search"
        :fetch-suggestions="fetchProducts"
        :placeholder="$t('table.search')"
        style="width: 240px"
        clearable
        @select="handleSelect"
      >
        <template slot-scope="{ item }">
          <div class="value">
            <b>{{ item.name }}</b>
            <b class="text-success fr">{{ item.unit_price | currencyFormat }}</b>
          </div>
        </template>
      </el-autocomplete>
      <el-button class="el-button-mobile fr ml-2" type="primary" icon="el-icon-check" @click="savePurchase">
        {{ $t('table.save') }}
      </el-button>
      <el-button :disabled="form.items.length === 0" class="el-button-mobile fr" icon="el-icon-close" @click="resetForm">
        {{ $t('table.empty') }}
      </el-button>
    </div>
    <!-- row -->
    <el-form ref="form" :model="form">
      <el-row :gutter="15">
        <el-col :xs="24" :md="16">
          <div class="form-left">
            <el-card v-if="form.items.length > 0" shadow="never" class="card-items subtle-card">
              <el-row :gutter="10" class="titles">
                <el-col :span="8">
                  <span class="title">{{ $t('products.product') }}</span>
                </el-col>
                <el-col :span="6">
                  <span class="title">{{ $t('products.unit_price') }}</span>
                </el-col>
                <el-col :span="3">
                  <span class="title">{{ $t('products.qty') }}</span>
                </el-col>
                <el-col :span="7">
                  <span class="title fr">{{ $t('products.amount') }}</span>
                </el-col>
              </el-row>
              <el-row v-for="item in formatedForm.items" :key="item.id" :gutter="10" class="item">
                <el-col :span="8">
                  <h5>{{ item.name }}</h5>
                </el-col>
                <el-col :span="6">
                  <el-input v-model.number="item.unit_price" type="number" step="any" />
                </el-col>
                <el-col :span="3">
                  <el-input v-model.number="item.qty" type="number" step="any" />
                </el-col>
                <el-col :span="7">
                  <h5 class="fr">{{ item.subtotal | currencyFormat }}</h5>
                </el-col>
              </el-row>
            </el-card>
            <div v-else class="no-product">
              <h1><i class="el-icon-goods" /></h1>
              <br>
              <span>{{ $t('products.emptyBasket') }}</span>
            </div>
          </div>
        </el-col>
        <el-col :xs="24" :md="8">
          <!-- total -->
          <div class="total">
            <el-row :gutter="10">
              <el-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="total-wrapper text-right">
                  <small class="text-muted">{{ $t('products.total') }} ({{ currency }})</small>
                  <h1 class="text-success totalttc">{{ formatedForm.total | currencyFormat }}</h1>
                </div>
              </el-col>
            </el-row>
          </div>
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>
<script>
import { getCurrency } from '@/utils/auth';
import Resource from '@/api/resource';
const productResource = new Resource('products');
const purchaseResource = new Resource('purchases');

export default {
  data() {
    return {
      found: null,
      products: [],
      form: {
        items: [],
      },
    };
  },
  computed: {
    currency() {
      return getCurrency();
    },
    formatedForm() {
      const form = { total: 0, total_discount: 0, items: [] };
      this.form.items.forEach(item => {
        const price = item.unit_price * item.qty;
        item.subtotal = price - (price * item.discount / 100);
        form.items.push(item);
        form.total += item.subtotal;
        form.total_discount += item.subtotal * item.discount / 100;
      });
      return form;
    },
  },
  created() {
    this.loadProducts();
  },
  methods: {
    async loadProducts() {
      const { data } = await productResource.list({ nopaginate: true });
      this.products = data;
    },
    handleSelect(product) {
      this.form.items.push({ id: product.id, name: product.name, qty: 1, unit_price: product.unit_price, discount: product.discount });
    },
    fetchProducts(queryString, cb) {
      var products = this.products;
      var results = queryString ? products.filter(this.createFilter(queryString)) : products;
      // call callback function to return suggestions
      cb(results);
    },
    createFilter(queryString) {
      return (product) => {
        return product.name.toLowerCase().includes(queryString.toLowerCase());
      };
    },
    getSubtotal(item) {
      const price = item.unit_sale_price * item.qty;
      return price - (price * item.discount / 100);
    },
    resetForm() {
      this.form = { items: [] };
    },
    savePurchase() {
      if (this.form.items.length > 0) {
        purchaseResource.store(this.formatedForm).then((res) => {
          this.$message({
            showClose: true,
            message: this.$t('products.purchaseAdded'),
            type: 'success',
          });
          this.resetForm();
        });
      } else {
        this.$message({
          showClose: true,
          message: this.$t('products.emptyBasket'),
          type: 'error',
        });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.card-items {
  flex: 1;
  overflow: scroll;
  margin-top: 10px;
  margin-bottom: 20px;
  .titles {
    margin-bottom: 20px;
    span{
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      color: #a3a7ac;
    }
  }
  .item {
    margin-bottom: 5px;
    h5 {
      margin-top: 10px;
      margin-bottom: 0;
    }
  }
}
.no-product {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-top: 10px;
  margin-bottom: 20px;
  background: #fff;
  border-radius: 4px;
  justify-content: center;
  align-items: center;
  color: #dad8d8;
  min-height: 400px;
  h1 {
    margin: 0;
    padding: 0;
    font-size: 42px;
  }
  span {
    text-transform: uppercase;
    font-weight: bold;
  }
}
.total-wrapper {
  h1{
    margin: 5px 0;
    padding: 0;
  }
}
.ml-2 {
  margin-left: 10px;
}
.text-right {
  text-align: right;
}
</style>
