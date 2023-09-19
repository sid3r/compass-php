<template>
  <div v-if="!loading" class="app-container">
    <el-row class="top-bar">
      <!-- <el-col :span="12">
        <h4>{{ $t('documents.edit') | uppercaseAll }}</h4>
      </el-col> -->
      <el-col :span="24" class="buttons">
        <!-- <el-switch v-model="formDocument.status" :active-text="$t('documents.status.draft') | uppercaseFirst" class="mr-2" /> -->
        <el-button type="primary" icon="el-icon-check" @click="saveDocument">
          {{ $t('documents.save') }}
        </el-button>
      </el-col>
    </el-row>
    <el-form ref="form" :model="formDocument" label-position="top">
      <!-- document head -->
      <el-row :gutter="15">
        <el-col :span="6">
          <el-form-item
            :label="$t('documents.code')"
            prop="code"
            :rules="{ required: true, trigger: 'blur'}"
            :show-message="false"
          >
            <el-select v-model="document.type" style="width: 49%;" disabled> <!-- @change="setCode" -->
              <el-option :label="$t('documents.types.estimate')" value="estimate" />
              <el-option :label="$t('documents.types.invoice')" value="invoice" />
            </el-select>
            <el-input v-model="formDocument.code" style="width: 49%;" />
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <!-- :rules="{ required: true, trigger: 'blur'}" -->
          <el-form-item
            :label="$t('documents.client')"
            prop="client"
            :show-message="false"
          >
            <el-autocomplete
              v-model="formDocument.company.name"
              prefix-icon="el-icon-user"
              :fetch-suggestions="fetchClients"
              placeholder=""
              style="width: 100%"
              clearable
              @clear="clearClient"
              @select="handleSelectClient"
            >
              <template slot-scope="{ item }">
                <div class="value">{{ item.name }}</div>
              </template>
            </el-autocomplete>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item
            :label="$t('documents.date')"
            prop="date"
            :rules="{ required: true, trigger: 'blur'}"
            :show-message="false"
          >
            <el-date-picker v-model="formDocument.date" format="dd/MM/yyyy" type="date" :placeholder="$t('documents.date')" style="width: 100%;" />
          </el-form-item>
        </el-col>
        <el-col :span="3">
          <el-form-item
            :label="$t('documents.vatrate')"
          >
            <el-input v-model.number="formDocument.vatrate" placeholder="0" type="number" step="any" />
          </el-form-item>
        </el-col>
        <el-col :span="3">
          <el-form-item :label="$t('documents.stamprate')">
            <el-input v-model.number="formDocument.stamprate" placeholder="0" type="number" step="any" />
          </el-form-item>
        </el-col>
      </el-row>
      <!-- doc items -->
      <el-card shadow="never" class="subtle-card" :body-style="{ padding: '20px 40px' }">
        <div style="margin-top: 20px">
          <el-row :gutter="5" class="titles">
            <el-col :span="8"><span>{{ $t('documents.description') }}</span></el-col>
            <el-col :span="3"><span>{{ $t('documents.qty') }}</span></el-col>
            <el-col :span="2"><span>{{ $t('documents.unit') }}</span></el-col>
            <el-col :span="3"><span>{{ $t('documents.u_price') }}</span></el-col>
            <el-col :span="3"><span>{{ $t('documents.discount') }}</span></el-col>
            <el-col :span="4" align="right"><span>{{ $t('documents.amount') }} ({{ currency }})</span></el-col>
          </el-row>
          <el-row
            v-for="(item, index) in formDocument.items"
            :key="index"
            :gutter="5"
            align="middle"
          >
            <el-col :span="8">
              <el-form-item
                :prop="'items.' + index + '.designation'"
                :rules="{ required: true, trigger: 'blur'}"
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
              <el-button type="text" round :disabled="formDocument.items.length < 2 " @click="removeItem(item)"><i class="el-icon-delete" /></el-button>
            </el-col>
          </el-row>
        </div>
        <!-- new-btn -->
        <el-row class="new-btn-wrapper">
          <el-button type="text" round plain icon="el-icon-plus" @click="newItem">{{ $t('documents.newItem') }}</el-button>
        </el-row>
        <!-- totals -->
        <el-row class="totals">
          <el-col :span="24">
            <table class="totals-table">
              <tbody>
                <tr>
                  <th><h5>{{ $t('documents.total_ht') }}:</h5></th>
                  <th><h5 class="bold">{{ formDocument.total_ht | currencyFormat }} {{ currency }}</h5></th>
                </tr>
                <tr v-if="formDocument.vatrate > 0">
                  <th><h5>{{ $t('documents.total_tva') }} {{ formDocument.vatrate }}%:</h5></th>
                  <th><h5 class="bold">{{ formDocument.total_tax | currencyFormat }} {{ currency }}</h5></th>
                </tr>
                <tr v-if="formDocument.stamprate > 0">
                  <th><h5>{{ $t('documents.total_stamp') }} {{ formDocument.stamprate }}%:</h5></th>
                  <th><h5 class="bold">{{ formDocument.total_stamp | currencyFormat }} {{ currency }}</h5></th>
                </tr>
                <tr>
                  <th><h5>{{ $t('documents.total_stamp') }}:</h5></th>
                  <th><h5 class="bold">{{ formDocument.total_discount | currencyFormat }} {{ currency }}</h5></th>
                </tr>
                <tr>
                  <th><h5>{{ $t('documents.total_ttc') }}:</h5></th>
                  <th><h5 class="bold">{{ formDocument.total_net | currencyFormat }} {{ currency }}</h5></th>
                </tr>
              </tbody>
            </table>
          </el-col>
        </el-row>
      </el-card>
    </el-form>
    <!-- {{ document }} -->
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import Resource from '@/api/resource';
import DocResource from '@/api/document';
const companyResource = new Resource('companies');
const docResource = new DocResource();

export default {
  data(){
    return {
      loading: true,
      docId: null,
      document: null,
      clients: [],
      dialogVisible: false,
      showConfirm: true,
      latest: {},
    };
  },
  computed: {
    formDocument(){
      return this.$options.filters.totals(this.document);
    },
    currency(){
      return getCurrency();
    },
  },
  created() {
    this.docId = this.$route.params.docId;
    this.loadDocument();
    this.loadClients();
  },
  async beforeRouteLeave(route, redirect, next) {
    // wait for confirm
    if (this.showConfirm){
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
    async loadDocument(){
      this.loading = true;
      const { document } = await docResource.get(this.docId);
      this.document = document;
      // console.log(document);
      this.document.code = this.leftPad(this.document.code);
      // this.setCode();
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
      this.latest = latest;
      this.setCode();
    },
    setCode(){
      this.document.code = this.leftPad(this.document.code + 1, 4);
    },
    leftPad(num){
      return this.$options.filters.leftPad(num, 4);
    },
    newItem(){
      this.document.items.push({ id: '', description: '', unit: '', quantity: null, unit_price: null, discount: null });
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
      return (client) => {
        return (client.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    handleSelectClient(item){
      this.document.company = item;
      // this.document.client = item.name;
    },
    clearClient(){
      this.document.company = { id: null, name: '' };
    },
    async saveDocument(){
      this.$refs['form'].validate((valid) => {
        if (valid) {
          docResource.update(this.docId, this.document).then(res => {
            console.log(res);
            this.$message({
              showClose: true,
              message: this.$t('documents.updated', { code: this.leftPad(this.docId) }),
              type: 'success',
              dangerouslyUseHTMLString: true,
            });
            this.showConfirm = false;
            this.$router.push({ name: 'viewdocument', params: { docId: this.docId }});
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
    color: #B7BEC7;
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
</style>
