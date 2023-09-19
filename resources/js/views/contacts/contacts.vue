<template>
  <div class="data-padding">
    <!-- filters -->
    <div class="filters">
      <el-input v-model="query.keyword" style="width: 240px" clearable prefix-icon="el-icon-search" :placeholder="$t('table.search')" class="filter-item" @keyup.enter.native="handleFilter" @clear="handleFilter" />
      <el-button class="el-button-mobile fr" type="primary" icon="el-icon-plus" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <el-select
        v-model="query.function"
        :placeholder="$t('contacts.function')"
        clearable
        style="width: 100px"
        @change="handleFilter"
      >
        <el-option
          v-for="(func, index) in functions"
          :key="index"
          :label="func"
          :value="func"
        />
      </el-select>
    </div>
    <!-- contacts list-->
    <div ref="contactsScroll" v-loading="loading" class="contacts-container" element-loading-background="#FBFCFD">
      <div v-if="total>0">
        <el-table
          :data="list"
          stripe
          border
          style="width: 100%"
        >
          <el-table-column
            :label="$t('contacts.name')"
            min-width="200"
          >
            <template slot-scope="scope">
              <el-link type="primary" @click="$router.push({ name: 'viewcontact', params: { contactId: scope.row.id } })">{{ scope.row.name }}</el-link>
            </template>
          </el-table-column>
          <el-table-column
            prop="mobile"
            :label="$t('contacts.mobile')"
            width="150"
          />
          <el-table-column
            prop="email"
            :label="$t('contacts.email')"
            min-width="170"
          />
          <el-table-column
            prop="company.name"
            :label="$t('contacts.company')"
            min-width="120"
          />
          <el-table-column
            prop="function"
            :label="$t('contacts.function')"
            min-width="120"
          />
        </el-table>
        <!-- Pagination -->
        <pagination v-show="total>0" class="pagination" :auto-scroll="true" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
      </div>
      <!-- nodata -->
      <div v-else-if="!loading" class="no-data">
        <div>
          <span><i class="el-icon-warning-outline" /> {{ $t('nodata') }}</span>
        </div>
      </div>
      <!-- Ajouter Contact Dialog -->
      <Dialog
        :visible="dialogVisible"
        :dialog-form-title="dialogTitle"
        @close-event="closeDialog"
        @update-event="updatePage"
      />
    </div>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination';
import Dialog from './dialog';

import ContactResource from '@/api/contact';
const contactResource = new ContactResource('contacts');

export default {
  components: { Pagination, Dialog },
  data() {
    return {
      loading: true,
      list: [],
      functions: [],
      total: 0,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        function: '',
      },
      currentContact: {},
      dialogVisible: false,
      dialogTitle: this.$t('contacts.dialogTitleNew'),
      newTag: {
        name: null,
        color: '',
      },
    };
  },
  created() {
    this.getList();
    this.getFunctions();
  },
  methods: {
    async getList() {
      this.loading = true;
      const { data, meta } = await contactResource.list(this.query);
      this.list = data;
      this.loading = false;
      this.total = meta.total;
    },
    async getFunctions(){
      const functions = await contactResource.functions();
      this.functions = functions.map(item => {
        return item.function;
      });
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
      this.$refs.contactsScroll.scrollTo({
        top: 0,
        behavior: 'smooth',
      });
    },
    handleCreate() {
      this.dialogVisible = true;
    },
    closeDialog(){
      this.dialogVisible = false;
    },
    updatePage(){
      this.dialogVisible = false;
      this.handleFilter();
    },
    handleUpdate($contact) {
      this.dialogFormTtile = this.$t('contacts.dialogTitleUpdate');
      this.currentContact = $contact;
      this.dialogVisible = true;
    },
  },
};
</script>

<style lang="scss" scoped>
.data-padding {
  width: 100%;
  overflow: scroll;
  height: calc(100vh - 50px);
}
.mb-1{
  margin-bottom: 1px;
}
.mb-2{
  margin-bottom: 15px;
}
.text-center{
  margin-top: 20px;
  text-align: center;
}
.pagination{
  padding: 30px 10px 0 10px;
}
</style>
