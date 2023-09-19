<template>
  <div class="data-padding">
    <div class="filters">
      <!-- filters -->
      <el-input v-model="query.keyword" style="width: 240px" clearable prefix-icon="el-icon-search" :placeholder="$t('table.search')" @keyup.enter.native="handleFilter" @clear="handleFilter" />
      <el-button class="el-button-mobile" icon="el-icon-s-operation" @click="showFilters = !showFilters">{{ $t('table.filter') }}</el-button>

      <el-button class="el-button-mobile fr" type="primary" icon="el-icon-plus" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <!-- filterby -->
      <transition name="el-zoom-in-top">
        <el-row v-if="showFilters" :gutter="10" class="mt-15">
          <el-col :xs="8" :sm="8" :md="6" :lg="4">
            <el-select
              v-model="query.tag"
              :placeholder="$t('companies.tags_name')"
              clearable
              class="fw"
              @change="handleFilter"
            >
              <el-option
                v-for="tag in tags"
                :key="tag.id"
                :label="$t('companies.tags.'+tag.name)"
                :value="tag.name"
              />
            </el-select>
          </el-col>
          <el-col :xs="8" :sm="8" :md="6" :lg="4">
            <el-select
              v-model="query.region"
              :placeholder="$t('companies.region')"
              clearable
              class="fw"
              @change="handleFilter"
            >
              <el-option
                v-for="(region, index) in regions"
                :key="index"
                :label="region"
                :value="region"
              />
            </el-select>
          </el-col>
          <el-col :xs="8" :sm="8" :md="6" :lg="4">
            <el-select
              v-model="query.activity"
              :placeholder="$t('companies.activity')"
              clearable
              class="fw"
              @change="handleFilter"
            >
              <el-option
                v-for="(activity, index) in activities"
                :key="index"
                :label="activity"
                :value="activity"
              />
            </el-select>
          </el-col>
        </el-row>
      </transition>
    </div>
    <div ref="companiesScroll" v-loading="loading" element-loading-background="#FBFCFD" class="companies-wrapper">
      <div v-if="total>0">
        <el-table
          :data="list"
          stripe
          border
          style="width: 100%"
        >
          <el-table-column
            :label="$t('companies.name')"
            min-width="200"
            fixed
          >
            <template slot-scope="scope">
              <el-link type="primary" @click="$router.push({ name: 'viewcompany', params: { companyId: scope.row.id } })">{{ scope.row.name }}</el-link>
            </template>
          </el-table-column>
          <el-table-column
            prop="tel"
            :label="$t('companies.tel')"
            width="150"
          />
          <el-table-column
            prop="email"
            :label="$t('companies.email')"
            min-width="170"
          />
          <el-table-column
            prop="region"
            :label="$t('companies.region')"
            min-width="120"
          />
          <el-table-column
            prop="activity"
            :label="$t('companies.activity')"
            min-width="120"
          />
          <el-table-column
            :label="$t('companies.tags_name')"
            min-width="120"
            align="center"
          >
            <template slot-scope="scope">
              <div v-for="tag in tags" :key="tag.id" class="tag-inline">
                <el-tag v-if="scope.row.tags.includes(tag.id)" size="mini" :type="tag.color">
                  {{ $t('companies.tags.' + tag.name) }}
                </el-tag>
              </div>
            </template>
          </el-table-column>
        </el-table>
        <!-- Pagination -->
        <pagination v-if="total>0" class="pagination" :auto-scroll="true" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
      </div>
      <!-- nodata -->
      <div v-else-if="!loading && total<0" class="no-data">
        <div>
          <span><i class="el-icon-warning-outline" /> {{ $t('nodata') }}</span>
        </div>
      </div>
    </div>
    <!-- dialog -->
    <Dialog
      :visible="dialogVisible"
      :dialog-title="dialogTitle"
      @close-event="closeDialog"
      @create-event="getList"
    />
    <!-- @update-event="updatePage" -->
  </div>
</template>

<script>
import Pagination from '@/components/Pagination';
import Dialog from './dialog';
import Resource from '@/api/resource';
import CompanyResource from '@/api/company';
const companyResource = new CompanyResource('companies');
const tagResource = new Resource('tags');

export default {
  components: { Pagination, Dialog },
  data(){
    return {
      list: [],
      tags: [],
      regions: [],
      activities: [],
      loading: true,
      showFilters: false,
      total: 0,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        tag: '',
        region: '',
        activity: '',
      },
      dialogVisible: false,
      dialogTitle: this.$t('companies.dialogAdd'),
    };
  },
  created() {
    this.getList();
    this.getTags();
    this.getRegions();
    this.getActivities();
  },
  methods: {
    async getList() {
      this.loading = true;
      const { data, meta } = await companyResource.list(this.query);
      this.list = data;
      this.loading = false;
      this.total = meta.total;
    },
    async getTags(){
      const { data } = await tagResource.list();
      this.tags = data;
    },
    async getRegions() {
      const regions = await companyResource.regions();
      this.regions = regions.map(item => {
        return item.region;
      });
    },
    async getActivities() {
      const activities = await companyResource.activities();
      this.activities = activities.map(item => {
        return item.activity;
      });
    },
    handleFilter(){
      this.query.page = 1;
      this.getList();
      // this.$refs.companiesScroll.scrollTo({
      //   top: 0,
      //   behavior: 'smooth',
      // });
    },
    handleCreate(){
      this.dialogVisible = true;
    },
    closeDialog(){
      this.dialogVisible = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.data-padding{
  width: 100%;
  overflow: scroll;
  height: calc(100vh - 50px);
}
.companies-wrapper{
  .tag-inline{
    display: inline-block;
  }
}
.pagination{
  padding: 30px 10px 0 10px;
}
.mt-15{
  margin-top: 15px;
}
</style>
