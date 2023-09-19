<template>
  <div class="products-container">
    <!-- left menu -->
    <transition name="slide">
      <div v-if="showFilters" class="left-menu" @click="hideCategories">
        <el-row :gutter="10" class="categories-header">
          <el-col :span="12">
            <h5 style="margin: 0">{{ $t('products.categories') }}</h5>
          </el-col>
          <el-col :span="12" align="right">
            <el-button plain icon="el-icon-setting" class="manage-icon" @click="$router.push({ name: 'categories' })" />
            <el-button plain icon="el-icon-close" class="manage-icon" @click="hideCategories" />
          </el-col>
        </el-row>
        <el-tree
          :data="categories"
          :props="defaultProps"
          default-expand-all
          icon-class="el-icon-arrow-right"
          :highlight-current="true"
          :expand-on-click-node="false"
          @node-click="handleNodeClick"
        />
        <div v-if="categorySelected" class="reinit-categories">
          <el-button type="button" plain style="width: 100%" @click="resetCategory">
            <i class="el-icon-refresh" /> {{ $t('products.reset') }}</el-button>
        </div>
      </div>
    </transition>
    <!-- page content -->
    <div class="right-content data-padding">
      <!-- Filters -->
      <div class="filters">
        <el-input v-model="query.keyword" style="width: 240px" clearable prefix-icon="el-icon-search" :placeholder="$t('table.search')" @keyup.enter.native="handleFilter" @clear="handleFilter" />
        <el-button class="el-button-mobile" icon="el-icon-s-operation" @click="showFilters = !showFilters">{{ $t('table.filter') }}</el-button>
        <el-button class="el-button-mobile fr" type="primary" icon="el-icon-plus" @click="$router.push({ name: 'newproduct' })">
          {{ $t('table.add') }}
        </el-button>
      </div>
      <!-- product list -->
      <div v-if="total>0" ref="productsScroll" v-loading="loading" element-loading-background="#FBFCFD" class="products-wrapper">
        <el-row :gutter="15">
          <el-col v-for="(product, index) in list" :key="index" :xs="24" :sm="12" :md="8" :lg="6">
            <div class="product-card subtle-card">
              <div class="product-image">
                <img :src="'uploads/'+product.image" :alt="product.name">
              </div>
              <div class="body">
                <el-link type="primary" class="product-name" @click="$router.push({ name: 'viewproduct', params: { productId: product.id } })">{{ product.name }}</el-link>
                <b v-if="product.category.name" class="category-name">{{ product.category.name }}</b>
                <span class="info">{{ product.bar_code }}</span>
              </div>
            </div>
          </el-col>
        </el-row>
        <!-- Pagination -->
        <pagination v-show="total>0" class="pagination" :auto-scroll="true" :total="total" :page.sync="query.page" :limit.sync="query.limit" :page-sizes="[48, 96]" @pagination="getList" />
      </div>
      <!-- nodata -->
      <div v-else-if="!loading && total === 0" class="no-data">
        <div>
          <span><i class="el-icon-warning-outline" /> {{ $t('nodata') }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination';

import Resource from '@/api/resource';
const productResoure = new Resource('products');
const categoryResoure = new Resource('categories');

export default {
  components: { Pagination },
  data(){
    return {
      loading: true,
      showFilters: false,
      list: '',
      total: 0,
      categories: [],
      categorySelected: true,
      defaultProps: {
        children: 'children',
        label: 'name',
      },
      query: {
        page: 1,
        limit: 48,
        keyword: '',
        category: '',
      },
    };
  },
  created() {
    this.getList();
    this.getCategories();
  },
  methods: {
    async getList(){
      this.loading = true;
      const { data, meta } = await productResoure.list(this.query);
      // console.log(data);
      this.list = data;
      this.total = meta.total;
      this.loading = false;
    },
    async getCategories(){
      const data = await categoryResoure.list({ nest: true });
      this.categories = data;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
      // this.$refs.productsScroll.scrollTo({
      //   top: 0,
      //   behavior: 'smooth',
      // });
    },
    handleNodeClick(data) {
      this.query.category = data.id;
      this.categorySelected = true;
      this.getList();
      this.hideCategories();
    },
    resetCategory(){
      this.query.category = '';
      this.categorySelected = false;
      this.getList();
    },
    hideCategories(){
      this.showFilters = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.products-container{
  width: 100%;
  height: calc(100% - 50px);
  position: relative;
  overflow: scroll;
  .categories-header{
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    padding-left: 10px;
    .manage-icon {
      padding: 9px;
      margin-left: 4px;
    }
  }
  .left-menu{
    position: absolute;
    // left: -220px;
    top: 0;
    bottom: 0;
    width: 220px;
    height: calc(100vh - 50px);
    background: #fff;
    border-right: 1px solid #f7f7f7;
    z-index: 2000;
    padding: 10px;
    overflow-y: scroll;
    .reinit-categories{
      text-align: center;
      margin-top: 10px;
      width: 100%;
    }
  }
  .right-content{
    .products-wrapper{
      border-bottom: 1px solid #f7f7f7;
    }
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
.product-card{
  background: #fff;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  .product-image{
    max-height: 80px;
    width: 80px;
    overflow: hidden;
    img{
      position: relative;
      height: 80px
    }
  }
  .body{
    padding: 10px;
  }
  .product-name{
    font-size: 14px;
  }
  .category-name{
    font-size: 12px;
    color: #5f5f5f;
    display: block;
    margin-top: 3px;
    margin-bottom: 8px;
  }
  .info{
    margin-top: 2px;
    display: block;
    font-size: 12px;
    color: #777;
  }
}
.pagination{
  padding: 20px 10px 0 10px;
}
.slide-enter-active, .slide-leave-active{
  left: 0;
  opacity: 1;
  transition: all .5s;
}
.slide-enter, .slide-leave-to{
  opacity: 0;
  left: -220px;
}
</style>
