<template>
  <div class="pos-wrapper">
    <div class="menu">
      <router-link to="/">
        <img v-if="logo" :src="logo" class="pos-logo">
        <h1 class="pos-title">{{ title }} </h1>
      </router-link>
    </div>
    <div class="pos-main">
      <el-form ref="form" :model="form">
        <el-row :gutter="15">
          <el-col :xs="24" :md="16">
            <div class="form-left">
              <div class="total">
                <el-row :gutter="10">
                  <el-col :xs="20" :sm="20" :md="18" :lg="20">
                    <div class="total-wrapper">
                      <h1 class="text-success totalttc">{{ formatedForm.total | currencyFormat }}</h1>
                    </div>
                  </el-col>
                  <el-col :xs="4" :sm="4" :md="6" :lg="4">
                    <el-button type="primary" :disabled="form.items.length === 0" size="medium" icon="el-icon-check" class="validate-btn el-button-mobile" @click="saveSale">{{ $t('table.validate') }}</el-button>
                  </el-col>
                </el-row>
              </div>
              <el-row :gutter="10">
                <el-col :xs="20" :sm="20" :md="18" :lg="20">
                  <el-autocomplete
                    v-model="found"
                    size="medium"
                    prefix-icon="el-icon-search"
                    :fetch-suggestions="fetchProducts"
                    :placeholder="$t('table.search')"
                    style="width: 100%"
                    clearable
                    @select="handleSelect"
                  >
                    <template slot-scope="{ item }">
                      <div class="value">
                        <b>{{ item.name }}</b>
                        <b class="text-success fr">{{ item.unit_sale_price | currencyFormat }}</b>
                      </div>
                    </template>
                  </el-autocomplete>
                </el-col>
                <el-col :xs="4" :sm="4" :md="6" :lg="4">
                  <el-button :disabled="form.items.length === 0" size="medium" icon="el-icon-close" class="el-button-mobile" style="width: 100%" @click="resetForm">{{ $t('table.empty') }}</el-button>
                </el-col>
              </el-row>
              <el-card v-if="form.items.length > 0" shadow="never" class="card-items">
                <el-row :gutter="10" class="titles">
                  <el-col :span="6">
                    <span class="title">{{ $t('products.product') }}</span>
                  </el-col>
                  <el-col :span="4">
                    <span class="title">{{ $t('products.unit_sale_price') }}</span>
                  </el-col>
                  <el-col :span="4">
                    <span class="title">{{ $t('products.qty') }}</span>
                  </el-col>
                  <el-col :span="4">
                    <span class="title">{{ $t('products.discount') }}</span>
                  </el-col>
                  <el-col :span="6">
                    <span class="title fr">{{ $t('products.amount') }}</span>
                  </el-col>
                </el-row>
                <el-row v-for="item in formatedForm.items" :key="item.id" :gutter="10" class="item">
                  <el-col :span="6">
                    <h5>{{ item.name }}</h5>
                  </el-col>
                  <el-col :span="4">
                    <el-input v-model.number="item.unit_sale_price" type="number" step="any" />
                  </el-col>
                  <el-col :span="4">
                    <el-input v-model.number="item.qty" type="number" step="any" />
                  </el-col>
                  <el-col :span="4">
                    <el-input v-model.number="item.discount" type="number" step="any" />
                  </el-col>
                  <el-col :span="6">
                    <h5 class="fr">{{ getSubtotal(item) | currencyFormat }}</h5>
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
          <el-col :span="8" class="hidden-on-mobile">
            <pre>
              <!-- {{ products }} -->
            </pre>
          </el-col>
        </el-row>
      </el-form>
    </div>
  </div>
</template>

<script>
import Resource from '@/api/resource';
const productResource = new Resource('products');
const saleResource = new Resource('sales');

export default {
  data() {
    return {
      title: 'COMPASS',
      logo: '/svg/logo.svg',
      products: [],
      found: '',
      form: {
        items: [],
      },
    };
  },
  computed: {
    formatedForm() {
      const form = { total: 0, items: [] };
      this.form.items.forEach(item => {
        const price = item.unit_sale_price * item.qty;
        item.subtotal = price - (price * item.discount / 100);
        item.profit = item.subtotal - price;
        form.items.push(item);
        form.total += item.subtotal;
        form.profit += item.profit;
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
      this.form.items.push({ id: product.id, name: product.name, qty: 1, unit_sale_price: product.unit_sale_price, discount: product.discount });
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
    saveSale() {
      if (this.form.items.length > 0) {
        saleResource.store(this.formatedForm).then((res) => {
          this.$message({
            showClose: true,
            message: this.$t('products.saleAdded'),
            type: 'success',
          });
          // console.log(res);
        });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.pos-wrapper{
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
  background: #333;
  .menu{
    height: 50px;
    width: 100%;
    background: #222;
    display: flex;
    justify-content: space-between;
    align-items: center;
    .pos-logo {
      width: 32px;
      height: 32px;
      vertical-align: middle;
      margin-right: 5px;
      margin-left: 10px;
    }
    .pos-title {
      display: inline-block;
      margin: 0;
      color: #fff;
      font-weight: 600;
      line-height: 50px;
      font-size: 14px;
      font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
      vertical-align: middle;
    }
  }
  .pos-main{
    padding: 10px;
    width: 100%;
    height: calc(100vh - 50px);
    overflow: hidden;
    .form-left {
      display: flex;
      flex-direction: column;
      height: calc(100vh - 50px);
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
      .total {
        height: 70px;
        padding: 5px 0;
        .total-wrapper {
          background: #222;
          text-align: center;
          border-radius: 4px;
          h1 {
            padding: 7px 0;
            margin: 0;
          }
        }
        .validate-btn {
          width: 100%;
          // margin-top: 17px;
          height: 49px;
          line-height: 16px;
          // padding-top: 15px;
          // padding-bottom: 14px;
          text-transform: uppercase;
          font-size: 16px;
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
    }
  }
}
// small screens
@media only screen and (max-width: 768px) {
  .hidden-on-mobile{
    display: none;
  }
}
</style>
