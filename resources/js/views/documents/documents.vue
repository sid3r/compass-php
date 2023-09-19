<template>
  <div class="data-padding">
    <!-- Filters -->
    <div class="filters">
      <el-input v-model="query.keyword" clearable prefix-icon="el-icon-search" :placeholder="$t('table.search')" style="width: 240px; float: left" class="filter-item" @keyup.enter.native="handleFilter" @clear="handleFilter" />
      <el-button class="el-button-mobile fr" type="primary" icon="el-icon-plus" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
    </div>
    <div class="documents-wrapper">
      <el-row :gutter="15">
        <el-col :xs="24" :md="4" :lg="4">
          <!-- <div v-for="(item, index) in stats" :key="index" class="stat-item">
                <span>{{ $t('documents.stats.'+item.type+item.status) }}</span>
                <b>{{ item.total }}</b>
              </div> -->
          <el-menu v-if="stats" :default-active="'invoice'" :mode="menuOrientation" class="el-menu-docs" @select="handleSelect">
            <el-menu-item v-for="(item, index) in stats" :key="index" :index="item.type">{{ $t('documents.types.'+item.type) }} ({{ item.total }})</el-menu-item>
          </el-menu>
        </el-col>
        <el-col :xs="24" :md="20" :lg="20">
          <!-- Documents -->
          <div v-if="total>0" ref="documentsScroll" v-loading="loading" element-loading-background="#FBFCFD">
            <el-row class="titles" :gutter="10">
              <el-col :span="6"><span class="title">{{ $t('documents.document') }}</span></el-col>
              <el-col :span="3"><span class="title"><i class="el-icon-date" /> {{ $t('documents.date') }}</span></el-col>
              <el-col :span="7"><span class="title"><i class="el-icon-office-building" /> {{ $t('documents.client') }}</span></el-col>
              <el-col :span="4" align="right"><span class="title"><i class="el-icoSshopping-cart-2" /> {{ $t('documents.amount') }}</span></el-col>
              <el-col :span="4" align="right"><span class="title"><i class="el-icon-finished" /> {{ $t('documents.docstatus') }}</span></el-col>
            </el-row>
            <el-row v-for="doc in list" :key="doc.index" :gutter="10">
              <!-- card -->
              <el-card class="subtle-card" shadow="never" :body-style="{ padding: '15px' }">
                <el-col :span="6">
                  <el-link type="primary" @click="$router.push({ name: 'viewdocument', params: { docId: doc.id } })"><b>{{ $t('documents.types.'+doc.type) | uppercaseAll }}<span v-if="doc.code"> #{{ leftPad(doc.code) }}</span> </b></el-link>
                </el-col>
                <el-col :span="3">
                  <span class="info"><time class="time">{{ doc.date | parseTime('{d}-{m}-{y}') }}</time></span>
                </el-col>
                <el-col :span="7">
                  <el-popover trigger="hover" placement="top">
                    <p class="client-popover">
                      <span><i class="el-icon-phone-outline" /> {{ doc.company.tel }}</span> <br>
                      <span><i class="el-icon-message" /> {{ doc.company.email }}</span> <br>
                      <span><i class="el-icon-location-outline" /> {{ doc.company.region }}</span> <br>
                    </p>
                    <div slot="reference" class="name-wrapper">
                      <span class="info">{{ doc.company.name }}</span>
                    </div>
                  </el-popover>
                </el-col>
                <el-col :span="4" align="right">
                  <span class="info">{{ total_net(doc) | currencyFormat }} {{ currency }}</span>
                </el-col>
                <el-col :span="4" align="right">
                  <el-tag effect="dark" size="mini" :type="tagClass(doc.status)">{{ $t('documents.status.'+doc.status) }}</el-tag>
                </el-col>
              </el-card>
            </el-row>
            <!-- Pagination -->
            <pagination v-show="total>0" class="pagination" :auto-scroll="true" :total="total" :page.sync="query.page" :limit.sync="query.limit" :page-sizes="[12, 24, 48, 96]" @pagination="getList" />
          </div>
          <!-- no-data -->
          <div v-if="!loading && total === 0" class="no-data">
            <div>
              <span><i class="el-icon-warning-outline" /> {{ $t('nodata') }}</span>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import { docStatus } from '@/utils/';
import { mapGetters } from 'vuex';
import Pagination from '@/components/Pagination';
import DocResource from '@/api/document';
const documentResource = new DocResource('documents');

export default {
  components: { Pagination },
  data(){
    return {
      loading: true,
      list: [],
      total: 0,
      stats: null,
      types: ['estimate', 'invoice'],
      query: {
        page: 1,
        limit: 24,
        keyword: '',
        type: 'invoice',
        status: '',
      },
    };
  },
  computed: {
    ...mapGetters([
      'device',
    ]),
    currency() {
      return getCurrency();
    },
    menuOrientation(){
      let orientation = 'vertical';
      if (this.device === 'mobile') {
        orientation = 'horizontal';
      }
      return orientation;
    },
  },
  created() {
    this.getStats();
    this.getList();
  },
  methods: {
    leftPad(code){
      return this.$options.filters.leftPad(code, 4);
    },
    tagClass(status){
      return docStatus(status);
    },
    total_net(document){
      return this.$options.filters.total_net(document);
    },
    handleSelect(key) {
      switch (key) {
        case 'invoice':
          this.query.type = 'invoice';
          break;
        case 'estimate':
          this.query.type = 'estimate';
          break;
        default:
          this.query.type = 'invoice';
      }
      this.handleFilter();
    },
    async getList(){
      this.list = [];
      this.loading = true;
      const { data, meta } = await documentResource.list(this.query);
      this.list = data;
      this.total = meta.total;
      this.loading = false;
      // console.log(data);
    },
    async getStats() {
      const stats = await documentResource.stats();
      this.stats = stats.reverse();
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate(){
      this.$router.push({ name: 'newdocument' });
    },
    toString(index) {
      return index.toString();
    },
  },
};
</script>

<style lang="scss" scoped>
.data-padding{
  width: 100%;
  overflow: scroll;
  height: calc(100% - 50px);
}
.documents-wrapper {
  margin-top: 20px;
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
.pagination{
  padding: 20px 10px 0 10px;
}
.stats {
  padding: 10px;
  .stat-item {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin-top: 7px;
    margin-bottom: 7px;
    span,b {
      font-size: 13px;
      font-weight: bold;
    }
    span {
      color: #666666;
    }
  }
}
@media only screen and (max-width: 768px) {
  .el-menu-docs{
    margin-bottom: 20px !important;
    margin-top: 0;
    .el-menu-item{
      padding: 20px !important;;
    }
  }
}
</style>
