<template>
  <div v-if="!loading" class="app-container">
    <!-- top menu -->
    <el-row class="top-bar">
      <el-col :span="24" class="buttons">
        <el-button type="danger" icon="el-icon-delete" @click="handleDelete(company.id, company.name)">
          {{ $t('table.delete') }}
        </el-button>
        <el-button type="primary" icon="el-icon-edit" @click="handleEdit">
          {{ $t('table.edit') }}
        </el-button>
      </el-col>
    </el-row>
    <!-- company -->
    <div class="">
      <el-row :gutter="20">
        <el-col :xs="24" :sm="24" :md="8" :lg="8">
          <el-card shadow="never" class="subtle-card">
            <div slot="header" class="clearfix">
              <i class="el-icon-office-building" /> <b>{{ company.name | uppercaseAll }}</b>
            </div>
            <div class="tags-wrapper">
              <div v-for="tag in tags" :key="tag.id">
                <el-tag v-if="company.tags.includes(tag.id)" size="mini" :type="tag.color">
                  {{ $t('companies.tags.' + tag.name) }}
                </el-tag>
              </div>
            </div>
            <span class="info-comp"><i class="el-icon-phone-outline" /> {{ company.tel }}</span> <br>
            <span class="info-comp"><i class="el-icon-printer" /> {{ company.fax }}</span> <br>
            <span class="info-comp"><i class="el-icon-message" /> {{ company.email }}</span> <br>
            <span class="info-comp"><i class="el-icon-location-outline" /> {{ company.address }} - {{ company.region }}</span> <br>
            <el-divider />
            <span class="info-comp"><b>N.I.F:</b> {{ company.nif }}</span> <br>
            <span class="info-comp"><b>R.C:</b> {{ company.rc }}</span> <br>
            <span class="info-comp"><b>A.I:</b> {{ company.ai }}</span> <br>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="24" :md="16" :lg="16">
          <el-tabs value="contacts">
            <!-- contacts -->
            <el-tab-pane :label="$t('route.contacts')" name="contacts">
              <div v-if="contacts.length > 0" class="mt-10">
                <el-table
                  :data="contacts"
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
                    min-width="200"
                  />
                  <el-table-column
                    prop="function"
                    :label="$t('contacts.function')"
                    min-width="160"
                  />
                </el-table>
              </div>
              <div v-else class="mt-10">
                <span>aucun contact</span>
              </div>
            </el-tab-pane>
            <!-- docs -->
            <el-tab-pane :label="$t('route.documents')" name="docs">
              <div v-if="documents.length > 0" class="mt-10">
                <el-card
                  v-for="doc in documents"
                  :key="doc.id"
                  shadow="never"
                  class="subtle-card"
                  :body-style="{ padding: '15px' }"
                >
                  <el-row :gutter="15">
                    <el-col :span="6">
                      <el-link type="primary" @click="$router.push({ name: 'viewdocument', params: { docId: doc.id } })"><b>{{ $t('documents.types.'+doc.type) | uppercaseAll }} {{ leftPad(doc.code) }}</b></el-link>
                    </el-col>
                    <el-col :span="4">
                      <span class="info"><time class="time">{{ doc.date | parseTime('{d}-{m}-{y}') }}</time></span>
                    </el-col>
                    <el-col :span="4">
                      <span class="info">{{ doc.items.length }} {{ $t('documents.items') }}</span>
                    </el-col>
                    <el-col :span="6" align="right">
                      <span class="info">{{ total_net(doc) | currencyFormat }}</span>
                    </el-col>
                    <el-col :span="4" align="right">
                      <el-tag size="mini" effect="dark" :type="tagClass(doc.status)">{{ $t('documents.status.'+doc.status) }}</el-tag>
                    </el-col>
                  </el-row>
                </el-card>
              </div>
              <div v-else class="mt-10">
                <span>aucun document</span>
              </div>
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
    </div>
    <!-- dialog -->
    <Dialog
      :visible="dialogVisible"
      :dialog-title="dialogTitle"
      :company="company"
      @close-event="closeDialog"
      @update-event="loadCompany"
    />
  </div>
</template>

<script>
import { docStatus } from '@/utils/';
import Dialog from './dialog';
import Resource from '@/api/resource';
const companyResource = new Resource('companies');
const tagResource = new Resource('tags');

export default {
  components: { Dialog },
  data(){
    return {
      companyId: null,
      company: null,
      contacts: [],
      documents: [],
      tags: [],
      loading: true,
      dialogVisible: false,
      dialogTitle: this.$t('companies.dialogEdit'),
    };
  },
  created(){
    this.companyId = this.$route.params.companyId;
    this.loadCompany();
    this.loadTags();
  },
  methods: {
    async loadCompany(){
      this.loading = true;
      const { company, contacts, documents } = await companyResource.get(this.companyId);
      this.company = company;
      this.contacts = contacts;
      this.documents = documents;
      this.loading = false;
    },
    async loadTags(){
      const { data } = await tagResource.list();
      this.tags = data;
    },
    handleDelete(id, name) {
      // wait for confirm
      this.$confirm(this.$t('companies.confirmDelete', { name: name }), '', {
        confirmButtonText: this.$t('table.confirm'),
        cancelButtonText: this.$t('table.cancel'),
        type: 'warning',
        showClose: false,
      }).then(() => {
        // delete product
        companyResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: this.$t('companies.deleteSucces', { name: name }),
          });
          // redirect to companies
          this.$router.push({ name: 'companies' });
        }).catch(error => {
          console.log(error);
        });
      });
    },
    handleEdit(){
      this.dialogVisible = true;
    },
    closeDialog(){
      this.dialogVisible = false;
    },
    leftPad(code){
      return this.$options.filters.leftPad(code, 4);
    },
    total_net(document){
      return this.$options.filters.total_net(document);
    },
    tagClass(status){
      return docStatus(status);
    },
    transTag(tag){
      if (tag.native === 'yes'){
        return this.$t('contacts.types.' + tag.name);
      } else {
        return tag.name;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.subtitle{
  margin-top: 0;
  padding-top: 0;
}
.mt-10{
  margin-top: 15px;
}
.tags-wrapper{
  margin-bottom: 10px;
  div{
    display: inline-block;
    margin-right: 5px;
  }
}
.info-comp{
  font-size: 14px;
  line-height: 25px;
}
</style>
