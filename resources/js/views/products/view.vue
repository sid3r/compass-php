<template>
  <div class="app-container">
    <el-row v-if="!loading" class="top-bar">
      <el-col :span="24" class="buttons">
        <el-button type="danger" icon="el-icon-delete" @click="handleDelete(product.id, product.name)">
          {{ $t('table.delete') }}
        </el-button>
        <el-button type="primary" icon="el-icon-edit" @click="$router.push({ name: 'editproduct', params: { productId: product.id } })">
          {{ $t('table.edit') }}
        </el-button>
      </el-col>
    </el-row>
    <el-row v-if="!loading" :gutter="25" element-loading-background="#FBFCFD" class="">
      <el-col :xs="24" :sm="24" :md="24" :lg="8">
        <el-card shadow="never" class="subtle-card">
          <div slot="header" class="clearfix">
            <b>{{ product.name }}</b>
          </div>
          <img :src="'uploads/'+product.image" :alt="product.name" class="product-image">
          <div class="product-details">
            <div><b>{{ $t('products.category') }}: </b><span>{{ product.category.name }}</span></div>
            <div><b>{{ $t('products.bar_code') }}: </b><span>{{ product.bar_code }}</span></div>
            <p><b>{{ $t('products.description') }}: </b>{{ product.description }}</p>
            <el-divider />
            <div><b>{{ $t('products.unit_price') }}: </b><span>{{ product.unit_price | currencyFormat }} DZ</span></div>
            <div><b>{{ $t('products.unit_sale_price') }}: </b><span>{{ product.unit_sale_price | currencyFormat }} DZ</span></div>
            <div><b>{{ $t('products.discount') }}: </b><span>{{ product.discount }}</span></div>
            <el-divider />
            <div><b>{{ $t('products.min_qty') }}:</b> <span>{{ product.min_qty }}</span></div>
            <div><b>{{ $t('products.stock_qty') }}:</b> <span>00</span></div>
          </div>
        </el-card>
      </el-col>
      <el-col :xs="24" :sm="24" :md="24" :lg="16">
        <small>graph</small>
        <pre>
          {{ product_graph }}
        </pre>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import Resource from '@/api/resource';
const productResource = new Resource('products');

export default {
  data(){
    return {
      productId: null,
      loading: true,
      product: {},
      product_graph: null,
    };
  },
  created() {
    this.productId = this.$route.params.productId;
    this.loadProduct();
  },
  methods: {
    async loadProduct(){
      const { product } = await productResource.get(this.productId);
      this.product = product;
      this.loading = false;
    },
    handleDelete(id, name) {
      // wait for confirm
      this.$confirm(this.$t('products.confirmDelete', { name: name }), '', {
        confirmButtonText: this.$t('table.confirm'),
        cancelButtonText: this.$t('table.cancel'),
        type: 'warning',
        showClose: false,
        dangerouslyUseHTMLString: true,
      }).then(() => {
        // delete contact
        productResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: this.$t('products.deleteSucces', { name: name }),
            dangerouslyUseHTMLString: true,
          });
          // redirect to contacts
          this.$router.push({ name: 'products' });
        }).catch(error => {
          console.log(error);
        });
      });
    },
  },
};
</script>

<style lang="scss" scoped>
  .product-image{
    width: 100%;
    height: auto;
    align-self: center;
  }
  .product-details{
    padding: 0px;
    h3 {
      margin-top: 0;
      padding-top: 0;
    }
    div {
      font-size: 14px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
    }
    p {
      font-size: 14px;
    }
  }
</style>
