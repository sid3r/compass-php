<template>
  <el-dialog :title="dialogTitle" :visible.sync="dialogVisible" class="el-dialog-primary">
    <div v-loading="companyCreating" class="form-container">
      <el-form ref="form" :rules="rules" :model="currentCompany" label-position="left">
        <el-row :gutter="15">
          <!-- company -->
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <el-form-item :label="$t('companies.name')" prop="name">
              <el-input v-model="currentCompany.name" />
            </el-form-item>
            <el-form-item :label="$t('companies.tel')" prop="tel">
              <el-input v-model="currentCompany.tel" />
            </el-form-item>
            <el-form-item :label="$t('companies.fax')" prop="fax">
              <el-input v-model="currentCompany.fax" />
            </el-form-item>
            <el-form-item :label="$t('companies.email')" prop="email">
              <el-input v-model="currentCompany.email" />
            </el-form-item>
            <el-form-item :label="$t('companies.address')" prop="address">
              <el-input v-model="currentCompany.address" />
            </el-form-item>
            <el-form-item :label="$t('companies.region')" prop="region">
              <el-autocomplete
                v-model="currentCompany.region"
                :fetch-suggestions="fetchRegions"
                :placeholder="$t('companies.region')"
                style="width: 100%"
                clearable
                @clear="clearRegion"
                @select="handleSelectRegion"
              >
                <template slot-scope="{ item }">
                  <div class="value">{{ item }}</div>
                </template>
              </el-autocomplete>
            </el-form-item>
          </el-col>
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <el-form-item
              :label="$t('companies.tags_name')"
              prop="tags_ids"
            >
              <el-select
                v-model="currentCompany.tags"
                :placeholder="$t('companies.tags_name')"
                multiple
                style="width: 100%"
              >
                <el-option
                  v-for="tag in tags"
                  :key="tag.id"
                  :label="$t('companies.tags.'+tag.name)"
                  :value="tag.id"
                />
              </el-select>
            </el-form-item>
            <el-form-item :label="$t('companies.activity')" prop="activity">
              <el-autocomplete
                v-model="currentCompany.activity"
                :fetch-suggestions="fetchActivities"
                :placeholder="$t('companies.activity')"
                style="width: 100%"
                clearable
                @clear="clearActivity"
                @select="handleSelectActivity"
              >
                <template slot-scope="{ item }">
                  <div class="value">{{ item }}</div>
                </template>
              </el-autocomplete>
            </el-form-item>
            <el-form-item :label="$t('companies.nif')" prop="nif">
              <el-input v-model="currentCompany.nif" />
            </el-form-item>
            <el-form-item :label="$t('companies.rc')" prop="rc">
              <el-input v-model="currentCompany.rc" />
            </el-form-item>
            <el-form-item :label="$t('companies.ai')" prop="ai">
              <el-input v-model="currentCompany.ai" />
            </el-form-item>
          </el-col>
        </el-row>
        <!-- {{ currentCompany }} -->
      </el-form>
      <div slot="footer" class="dialog-footer" style="text-align: right;">
        <el-button
          @click="closeDialog"
        >
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" :loading="companyCreating" @click="saveCompany()">
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import CompanyResource from '@/api/company';
import Resource from '@/api/resource';
const companyResource = new CompanyResource('companies');
const tagResource = new Resource('tags');

export default {
  props: {
    dialogTitle: { type: String, default: '' },
    company: { type: Object, default: () => {
      return {
        name: '',
        tel: '',
        fax: '',
        email: '',
        address: '',
        region: '',
        activity: '',
        tags: [],
        nif: '',
        rc: '',
        ai: '',
      };
    },
    },
    visible: { type: Boolean, default: false },
  },
  data(){
    return {
      rules: {
        name: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        tel: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        fax: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        email: [
          { required: true, message: this.$t('contacts.emailRequired'), trigger: 'blur' },
          { type: 'email', message: this.$t('contacts.emailIncorrect'), trigger: ['blur', 'change'] },
        ],
        address: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        region: [{ required: true, message: this.$t('companies.required'), trigger: 'change' }],
        activity: [{ required: true, message: this.$t('companies.required'), trigger: 'change' }],
        nif: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        rc: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        ai: [{ required: true, message: this.$t('companies.required'), trigger: 'blur' }],
        tags: [{ required: true, message: this.$t('companies.required'), trigger: 'change' }],
      },
      companyCreating: false,
      tags: [],
      regions: [],
      activities: [],
    };
  },
  computed: {
    currentCompany() {
      const company = this.company;
      if (company.documents){
        delete company.documents;
      }
      if (company.contacts){
        delete company.contacts;
      }
      return company;
    },
    dialogVisible: {
      get: function() {
        return this.visible;
      },
      set: function(newValue) {
        this.$emit('close-event');
      },
    },
  },
  mounted() {
    this.loadTags();
    this.loadRegions();
    this.loadActivities();
  },
  methods: {
    async loadTags(){
      const { data } = await tagResource.list();
      this.tags = data;
    },
    async loadRegions() {
      const regions = await companyResource.regions();
      this.regions = regions.map(item => {
        return item.region;
      });
    },
    async loadActivities() {
      const activities = await companyResource.activities();
      this.activities = activities.map(item => {
        return item.activity;
      });
    },
    fetchRegions(queryString, cb) {
      var regions = this.regions;
      var results = queryString ? regions.filter(this.createFilter(queryString)) : regions;
      cb(results);
    },
    fetchActivities(queryString, cb) {
      var activities = this.activities;
      var results = queryString ? activities.filter(this.createFilter(queryString)) : activities;
      cb(results);
    },
    createFilter(queryString) {
      return (item) => {
        return (item.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    handleSelectRegion(item){
      this.currentCompany.region = item;
    },
    handleSelectActivity(item){
      this.currentCompany.activity = item;
    },
    clearRegion(){
      this.currentCompany.region = '';
    },
    clearActivity(){
      this.currentCompany.activity = '';
    },
    saveCompany(){
      this.$refs['form'].validate((valid) => {
        if (valid) {
          if (this.currentCompany.id){
            // update
            this.companyCreating = true;
            companyResource.update(this.currentCompany.id, this.currentCompany)
              .then(response => {
                this.$message({
                  message: 'Company Updated',
                  type: 'success',
                  duration: 5 * 1000,
                });
                console.log(response);
                this.closeDialog();
                this.$emit('update-event');
              })
              .catch(error => {
                console.log(error);
              })
              .finally(() => {
                this.companyCreating = false;
              });
          } else {
            // create
            this.companyCreating = true;
            companyResource.store(this.currentCompany)
              .then(response => {
                this.$message({
                  message: 'Company created',
                  type: 'success',
                  duration: 5 * 1000,
                });
                // console.log(response);
                this.$emit('create-event', response);
                this.closeDialog();
              })
              .catch(error => {
                console.log(error);
              })
              .finally(() => {
                this.companyCreating = false;
              });
          }
        }
      });
    },
    closeDialog(){
      this.$refs['form'].resetFields();
      this.$refs['form'].clearValidate();
      this.$emit('close-event');
    },
  },
};
</script>

<style>

</style>
