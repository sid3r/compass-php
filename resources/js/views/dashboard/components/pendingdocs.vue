<template>
  <div class="recent-docs-wrappers">
    <el-row :gutter="20">
      <el-col :xs="24" :sm="24" :md="24" :lg="24">
        <h4 class="section-title">{{ $t('app.recentdocs') }}</h4>
        <div v-loading="loading" element-loading-background="#FBFCFD" class="recent-docs">
          <el-row v-for="doc in documents" :key="doc.index" :gutter="10">
            <!-- product card -->
            <el-card class="subtle-card" shadow="never" :body-style="{ padding: '15px' }">
              <el-col :span="5">
                <el-link type="primary" @click="$router.push({ name: 'viewdocument', params: { docId: doc.id } })"><b>{{ $t('documents.types.'+doc.type) | uppercaseAll }}<span v-if="doc.code"> #{{ leftPad(doc.code) }}</span> </b></el-link>
              </el-col>
              <el-col :span="4">
                <span class="info"><time class="time">{{ doc.date | parseTime('{d}-{m}-{y}') }}</time></span>
              </el-col>
              <el-col :span="7">
                <span class="info">{{ doc.company.name }}</span>
                <span class="info">( {{ doc.company.tel }} )</span>
              </el-col>
              <el-col :span="4" align="right">
                <span class="info">{{ total_net(doc) | currencyFormat }} {{ currency }}</span>
              </el-col>
              <el-col :span="4" align="right">
                <el-tag effect="dark" size="mini" :type="tagClass(doc.status)">{{ $t('documents.status.'+doc.status) }}</el-tag>
              </el-col>
            </el-card>
          </el-row>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import { docStatus } from '@/utils/';
import DashboardResource from '@/api/dashboard';
const dashboardresource = new DashboardResource('dashboard');

export default {
  data() {
    return {
      loading: true,
      documents: [],
    };
  },
  computed: {
    currency() {
      return getCurrency();
    },
  },
  mounted(){
    this.getRecentDocs();
  },
  methods: {
    async getRecentDocs(){
      const documents = await dashboardresource.recentdocs();
      this.documents = documents;
      this.loading = false;
    },
    tagClass(status){
      return docStatus(status);
    },
    leftPad(code){
      return this.$options.filters.leftPad(code, 4);
    },
    total_net(document){
      return this.$options.filters.total_net(document);
    },
  },
};
</script>

<style lang="scss">
.section-title {
  padding: 0 0 20px 0;
  color: rgb(107, 107, 107);
  margin: 0;
  font-size: 13px;
  text-transform: uppercase;
}
</style>
