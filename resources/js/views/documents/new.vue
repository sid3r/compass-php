<template>
  <div v-if="!loading" class="app-container">
    <el-row class="top-bar">
      <!-- <el-col :span="12">
        <h4>{{ $t('documents.new') | uppercaseAll }}</h4>
      </el-col> -->
      <el-col :span="24" class="buttons">
        <el-button type="primary" icon="el-icon-check" @click="saveDocument">
          {{ $t('documents.save') }}
        </el-button>
      </el-col>
    </el-row>
    <el-form ref="document" :model="formatedDocument" label-position="top">
      <!-- document head -->
      <el-row :gutter="15">
        <el-col :span="6">
          <el-form-item
            :label="$t('documents.code')"
            prop="code"
            :rules="{ required: true, trigger: 'focu'}"
            :show-message="false"
          >
            <el-select v-model="formatedDocument.type" style="width: 49%;" @change="setCode">
              <el-option :label="$t('documents.types.estimate')" value="estimate" />
              <el-option :label="$t('documents.types.invoice')" value="invoice" />
            </el-select>
            <el-input v-model="document.code" style="width: 49%;" />
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <div class="client-input-wrapper">
            <el-form-item
              :label="$t('documents.client')"
              prop="company"
              :rules="{ required: true, trigger: 'focu'}"
              :show-message="false"
            >
              <!-- :trigger-on-focus="false" -->
              <el-autocomplete
                v-model="formatedDocument.company"
                prefix-icon="el-icon-office-building"
                :fetch-suggestions="fetchClients"
                placeholder=""
                class="client-input"
                clearable
                @clear="clearClient"
                @select="handleSelectClient"
              >
                <template slot-scope="{ item }">
                  <div class="value">{{ item.name }}</div>
                </template>
              </el-autocomplete>
            </el-form-item>
            <el-button icon="el-icon-plus" class="add-client-btn" @click="toggleClientDialog" />
          </div>
        </el-col>
        <el-col :span="6">
          <el-form-item
            :label="$t('documents.date')"
            prop="date"
            :rules="{ required: true, trigger: 'focu'}"
            :show-message="false"
          >
            <el-date-picker v-model="formatedDocument.date" format="dd/MM/yyyy" type="date" :placeholder="$t('documents.date')" style="width: 100%;" />
          </el-form-item>
        </el-col>
        <el-col :span="3">
          <el-form-item
            :label="$t('documents.vatrate')"
          >
            <el-input v-model.number="formatedDocument.vatrate" placeholder="0" type="number" step="any" />
          </el-form-item>
        </el-col>
        <el-col v-if="showStamp" :span="3">
          <el-form-item :label="$t('documents.stamprate')">
            <el-input v-model="formatedDocument.stamprate" placeholder="0" type="number" step="any" />
          </el-form-item>
        </el-col>
      </el-row>
      <!-- doc items -->
      <el-card shadow="never" class="subtle-card" :body-style="{ padding: '20px' }" style="margin-top: 20px">
        <div>
          <el-row :gutter="5" class="titles">
            <el-col :span="8"><span>{{ $t('documents.description') }}</span></el-col>
            <el-col :span="3"><span>{{ $t('documents.qty') }}</span></el-col>
            <el-col :span="2"><span>{{ $t('documents.unit') }}</span></el-col>
            <el-col :span="3"><span>{{ $t('documents.u_price') }}</span></el-col>
            <el-col :span="3"><span>{{ $t('documents.discount') }}</span></el-col>
            <el-col :span="4" align="right"><span>{{ $t('documents.amount') }} ({{ currency }})</span></el-col>
          </el-row>
          <el-row
            v-for="(item, index) in formatedDocument.items"
            :key="index"
            :gutter="5"
            align="middle"
          >
            <el-col :span="8">
              <el-form-item
                :prop="'items.' + index + '.designation'"
                :rules="{ required: true, trigger: 'focu'}"
                :show-message="false"
              >
                <el-input
                  v-model="item.designation"
                  :placeholder="$t('documents.description')"
                  type="textarea"
                  autosize
                />
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item
                :prop="'items.' + index + '.quantity'"
                :rules="[{ required: true, trigger: 'focu'}, { type: 'number' }]"
                :show-message="false"
              >
                <el-input
                  v-model.number="item.quantity"
                  :placeholder="$t('documents.qty')"
                  type="number"
                  step="any"
                  autosize
                />
              </el-form-item>
            </el-col>
            <el-col :span="2">
              <el-form-item
                :prop="'items.' + index + '.unit'"
                :rules="{ required: true, trigger: 'focu'}"
                :show-message="false"
              >
                <el-input
                  v-model="item.unit"
                  :placeholder="$t('documents.unit')"
                  type="text"
                  autosize
                />
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item
                :prop="'items.' + index + '.unit_price'"
                :rules="[{ required: true, trigger: 'focu'}, { type: 'number' }]"
                :show-message="false"
              >
                <el-input
                  v-model.number="item.unit_price"
                  :placeholder="$t('documents.u_price')"
                  type="number"
                  step="any"
                  autosize
                />
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item>
                <el-input
                  v-model.number="item.discount"
                  :placeholder="$t('documents.discount')"
                  type="number"
                  step="any"
                  autosize
                />
              </el-form-item>
            </el-col>
            <el-col :span="4" align="right">
              <span class="amount">{{ item.amount | currencyFormat }}</span>
            </el-col>
            <el-col :span="1" align="right">
              <el-button round type="text" :disabled="formatedDocument.items.length < 2 " @click="removeItem(item)"><i class="el-icon-delete" /></el-button>
            </el-col>
          </el-row>
          <!-- new item btn -->
          <el-row class="new-btn-wrapper">
            <el-button round plain icon="el-icon-plus" @click="newItem">{{ $t('documents.newItem') }}</el-button>
          </el-row>
          <!-- totals -->
          <el-row class="totals">
            <el-col :span="24">
              <table class="totals-table">
                <tbody>
                  <tr>
                    <th><h5>{{ $t('documents.total_ht') }}:</h5></th>
                    <th><h5 class="bold">{{ formatedDocument.total_ht | currencyFormat }} {{ currency }}</h5></th>
                  </tr>
                  <tr v-if="formatedDocument.vatrate > 0">
                    <th><h5>{{ $t('documents.total_tva') }} {{ formatedDocument.vatrate }}%:</h5></th>
                    <th><h5 class="bold">{{ formatedDocument.total_tax | currencyFormat }} {{ currency }}</h5></th>
                  </tr>
                  <tr v-if="formatedDocument.stamprate > 0">
                    <th><h5>{{ $t('documents.total_stamp') }} {{ formatedDocument.stamprate }}%:</h5></th>
                    <th><h5 class="bold">{{ formatedDocument.total_stamp | currencyFormat }} {{ currency }}</h5></th>
                  </tr>
                  <tr>
                    <th><h5>{{ $t('documents.total_discount') }}:</h5></th>
                    <th><h5 class="bold">{{ formatedDocument.total_discount | currencyFormat }} {{ currency }}</h5></th>
                  </tr>
                  <tr>
                    <th><h5>{{ $t('documents.total_ttc') }}:</h5></th>
                    <th><h5 class="bold">{{ formatedDocument.total_net | currencyFormat }} {{ currency }}</h5></th>
                  </tr>
                </tbody>
              </table>
            </el-col>
          </el-row>
        </div>
      </el-card>
    </el-form>
    <!-- Ajouter Client Dialog -->
    <Dialog
      :visible="newCompanyVisible"
      :dialog-title="dialogTitle"
      :company="newCompany"
      @close-event="closeDialog"
      @create-event="companyCreated"
    />
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import { mapGetters } from 'vuex';
import Resource from '@/api/resource';
import DocResource from '@/api/document';
import Dialog from '../companies/dialog';
const companyResource = new Resource('companies');
const docResource = new DocResource();

export default {
  components: { Dialog },
  data(){
    return {
      loading: true,
      document: {
        type: 'estimate',
        code: 0,
        company: null,
        company_id: null,
        date: new Date(),
        vatrate: null,
        stamprate: null,
        items: [
          { designation: '', unit: '', quantity: null, unit_price: null, discount: null },
        ],
      },
      clients: [],
      newCompanyVisible: false,
      dialogTitle: this.$t('companies.dialogAdd'),
      showConfirmMessage: true,
      latest: {},
      newCompany: {
        name: '',
        tel: '',
        fax: '',
        adress: '',
        region: '',
        nif: '',
        rc: '',
        ai: '',
      },
    };
  },
  computed: {
    formatedDocument(){
      return this.$options.filters.totals(this.document);
    },
    currency(){
      return getCurrency();
    },
    ...mapGetters(['config']),
    showStamp(){
      const found = this.config.filter(item => {
        return item.name === 'display_stamp';
      });

      return found[0].value;
    },
  },
  created() {
    this.loadClients();
    this.loadLatest();
    // facturate if docId
    if (this.$route.params.docId){
      this.loadDocument(this.$route.params.docId);
    }
  },
  // confirm route leave
  async beforeRouteLeave(route, redirect, next) {
    // wait for confirm
    if (this.showConfirmMessage){
      this.$confirm(this.$t('contacts.quitWithoutSaving'), '', {
        confirmButtonText: this.$t('table.confirm'),
        cancelButtonText: this.$t('table.cancel'),
        type: 'warning',
        showClose: false,
      }).then(() => {
        next();
      }).catch(() => {
        next(false);
      });
    } else {
      next();
    }
  },
  methods: {
    // needed for duplicate
    async loadDocument(id){
      this.loading = true;
      const { document } = await docResource.get(id);
      this.document = document;
      this.document.type = 'invoice';
      this.document.company = document.company.name;
      // this.document.company_id = document.company.id;
      this.setCode();
      this.document.code = this.leftPad(this.document.code);
      // text to float
      this.document.items.forEach(item => {
        item.quantity = parseFloat(item.quantity);
        item.unit_price = parseFloat(item.unit_price);
      });
      this.loading = false;
    },
    async loadClients(){
      const data = await companyResource.list();
      this.clients = data.data;
    },
    async loadLatest(){
      const latest = await docResource.latest();
      console.log(latest);
      this.latest = latest;
      this.setCode();
      this.loading = false;
    },
    toggleClientDialog() {
      this.newCompanyVisible = true;
    },
    companyCreated(response) {
      this.document.company_id = response.data.id;
      this.document.company = response.data.name;
    },
    closeDialog() {
      this.newCompanyVisible = false;
    },
    setCode(){
      if (this.document.type === 'estimate'){
        this.document.code = this.leftPad(this.latest.estimate + 1, 4);
      } else {
        this.document.code = this.leftPad(this.latest.invoice + 1, 4);
      }
    },
    leftPad(num){
      return this.$options.filters.leftPad(num, 4);
    },
    newItem(){
      this.document.items.push({ designation: '', unit: '', quantity: null, unit_price: null, discount: null });
    },
    removeItem(item){
      this.document.items.splice(this.document.items.indexOf(item), 1);
    },
    fetchClients(queryString, cb) {
      var clients = this.clients;
      var results = queryString ? clients.filter(this.createFilter(queryString)) : clients;
      // call callback function to return suggestions
      cb(results);
    },
    createFilter(queryString) {
      return (company) => {
        return (company.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    handleSelectClient(item){
      this.document.company_id = item.id;
      this.document.company = item.name;
    },
    clearClient(){
      this.document.company_id = '';
      this.document.company = '';
    },
    async saveDocument(){
      this.$refs['document'].validate((valid) => {
        if (valid) {
          docResource.store(this.document).then((res) => {
            this.$message({
              showClose: true,
              message: this.$t('documents.added', { code: this.leftPad(res.code) }),
              type: 'success',
              dangerouslyUseHTMLString: true,
            });
            this.showConfirmMessage = false;
            // console.log(res);
            this.$router.push({ name: 'viewdocument', params: { docId: res.id }});
          });
        } else {
          this.$message({
            showClose: true,
            message: this.$t('documents.formErrorMessage'),
            type: 'error',
          });
          return false;
        }
      });
    },
  },
};
</script>
<style lang="scss" scoped>
.items-table{
  width: 100%;
  margin-top: 10px;
}
.titles {
  margin-bottom: 15px;
  span{
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #a3a7ac;
  }
}
.amount{
  font-size: 14px;
  line-height: 32px;
}
.new-btn-wrapper{
  margin-top: 15px;
  text-align: center;
}
.totals{
  margin-top: 20px;
  text-align: right;
  h5{
    line-height: 1;
    margin: 0;
    padding-top: 10px;
    font-weight: 400;
    letter-spacing: .06em;
  }
  .bold{
    font-weight: 600;
  }
  tr{
    border: 1px solid #efefef;
    padding: 5px;
  }
  .totals-table{
    width: 360px;
    float: right;
  }
}
.mr-2{
  margin-right: 10px;
}
.client-input-wrapper{
  display: flex;
  align-items: center;
  justify-content: center;
  .add-client-btn {
    padding: 9px;
    margin-top: 14px;
    margin-left: 4px;
  }

}
</style>
