<template>
  <el-dialog :title="dialogFormTitle" :visible.sync="dialogVisible" class="el-dialog-primary">
    <div v-loading="contactCreating" class="form-container">
      <el-form v-if="currentContact" ref="form" :rules="rules" :model="currentContact" label-position="top">
        <el-row :gutter="15">
          <!-- contact -->
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <el-form-item :label="$t('contacts.name')" prop="name">
              <el-input v-model="currentContact.name" />
            </el-form-item>
            <el-form-item :label="$t('contacts.email')" prop="email">
              <el-input v-model="currentContact.email" />
            </el-form-item>
            <el-form-item :label="$t('contacts.mobile')" prop="mobile">
              <el-input v-model="currentContact.mobile" />
            </el-form-item>
            <el-form-item :label="$t('contacts.function')" prop="func">
              <el-autocomplete
                v-model="currentContact.function"
                :fetch-suggestions="fetchFuncs"
                :placeholder="$t('contacts.function')"
                style="width: 100%"
                clearable
                @clear="clearFunc"
                @select="handleSelectFunc"
              >
                <template slot-scope="{ item }">
                  <div class="value">{{ item }}</div>
                </template>
              </el-autocomplete>
            </el-form-item>
          </el-col>
          <!-- company -->
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <el-form-item :label="$t('contacts.company')" prop="company_id">
              <el-autocomplete
                v-model="currentContact.company.name"
                prefix-icon="el-icon-office-building"
                :fetch-suggestions="fetchCompanies"
                :label="$t('contacts.company')"
                style="width: 100%"
                clearable
                @clear="clearCompany"
                @select="handleSelectCompany"
              >
                <template slot-scope="{ item }">
                  <div class="value">{{ item.name }}</div>
                </template>
              </el-autocomplete>
            </el-form-item>
            <el-form-item :label="$t('contacts.tel')" prop="tel">
              <el-input v-model="currentContact.company.tel" />
              <!-- :disabled="currentContact.company.id ? true : false" -->
            </el-form-item>
            <el-form-item :label="$t('contacts.fax')" prop="fax">
              <el-input v-model="currentContact.company.fax" />
            </el-form-item>
            <el-form-item :label="$t('contacts.address')" prop="address">
              <el-input v-model="currentContact.company.address" />
            </el-form-item>
            <el-form-item :label="$t('contacts.region')" prop="region">
              <el-input v-model="currentContact.company.region" />
            </el-form-item>
          </el-col>
        </el-row>
        <!-- {{ currentContact }} -->
      </el-form>
      <div slot="footer" class="dialog-footer" style="text-align: right;">
        <el-button
          @click="closeDialog"
        >
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" :loading="contactCreating" @click="saveContact()">
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </div>
  </el-dialog>
</template>
<script>
import ContactResource from '@/api/contact';
import CompanyResource from '@/api/company';
const contactResource = new ContactResource('contacts');
const companyResource = new CompanyResource('companies');

export default {
  props: {
    dialogFormTitle: { type: String, default: '' },
    contact: { type: Object, default: () => {
      return {
        mode: 'create',
        id: '',
        name: '',
        email: '',
        function: '',
        company: {
          id: '',
          name: '',
          email: '',
          address: '',
          region: '',
          tel: '',
          fax: '',
          activity: '',
        },
      };
    },
    },
    visible: { type: Boolean, default: false },
  },
  data(){
    return {
      rules: {
        name: [{ required: true, message: this.$t('contacts.nameRequired'), trigger: 'blur' }],
        mobile: [{ required: true, message: this.$t('contacts.mobileRequired'), trigger: 'blur' }],
        email: [
          { required: true, message: this.$t('contacts.emailRequired'), trigger: 'blur' },
          { type: 'email', message: this.$t('contacts.emailIncorrect'), trigger: ['blur', 'change'] },
        ],
      },
      contactCreating: false,
      companies: [],
      functions: [],
    };
  },
  computed: {
    currentContact() {
      const contact = this.contact;
      return contact;
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
  created(){
    this.loadCompanies();
    this.loadFunctions();
  },
  methods: {
    async loadCompanies(){
      await companyResource.list().then(data => {
        this.companies = data.data;
      });
    },
    async loadFunctions(){
      const functions = await contactResource.functions();
      this.functions = functions.map(item => {
        return item.function;
      });
    },
    fetchCompanies(queryString, cb) {
      var companies = this.companies;
      var results = queryString ? companies.filter(this.createCompanyFilter(queryString)) : companies;
      // call callback function to return suggestions
      cb(results);
    },
    fetchFuncs(queryString, cb) {
      var functions = this.functions;
      var results = queryString ? functions.filter(this.createFuncFilter(queryString)) : functions;
      // call callback function to return suggestions
      cb(results);
    },
    createCompanyFilter(queryString) {
      return (company) => {
        return (company.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    createFuncFilter(queryString) {
      return (func) => {
        return (func.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    handleSelectCompany(item){
      this.currentContact.company = item;
    },
    handleSelectFunc(item){
      this.currentContact.function = item;
    },
    clearCompany(){
      this.currentContact.company = { id: null, name: '', tel: '', fax: '', address: '', region: '' };
    },
    clearFunc(){
      this.currentContact.function = '';
    },
    closeDialog(){
      this.$refs['form'].resetFields();
      this.$refs['form'].clearValidate();
      this.$emit('close-event');
    },
    saveContact() {
      // message
      var message = this.$t('contacts.contactAdded', { name: this.currentContact.name });
      if (this.currentContact.mode === 'update'){
        message = this.$t('contacts.contactUpdated', { name: this.currentContact.name });
      }
      // mode
      // contact.id is not null? 'update'
      if (this.currentContact.id){
        this.currentContact.mode = 'update';
      }
      // send data
      this.$refs['form'].validate((valid) => {
        if (valid) {
          this.contactCreating = true;
          contactResource
            .store(this.currentContact)
            .then(response => {
              this.$message({
                message: message,
                type: 'success',
                duration: 5 * 1000,
              });
              this.closeDialog();
              this.$emit('update-event');
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.contactCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
  },
};
</script>
