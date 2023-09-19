<template>
  <div v-if="!loading" class="data-padding">
    <!-- top bar -->
    <el-row class="top-bar">
      <el-col :span="24" class="buttons">
        <el-button type="danger" icon="el-icon-delete" @click="deleteDocument(document.id, document.code)">
          {{ $t('table.delete') }}
        </el-button>
        <el-button type="primary" plain icon="el-icon-printer" :loading="generating" @click="printDocument">
          {{ $t('table.print') }}
        </el-button>
        <el-button type="primary" icon="el-icon-edit" @click="$router.push({ name: 'editdocument', params: { docId: document.id } })">
          {{ $t('table.edit') }}
        </el-button>
        <el-dropdown @command="handleEditCommand">
          <el-button type="primary" plain icon="el-icon-more" class="more-btn" />
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="dublicate"><i class="el-icon-document-copy" /> {{ $t('documents.dublicate') }}</el-dropdown-item>
            <el-dropdown-item v-if="document.status === 'pending'" command="terminate"><i class="el-icon-document-checked" /> {{ $t('documents.terminate') }}</el-dropdown-item>
            <el-dropdown-item v-if="document.status === 'pending'" command="cancel"><i class="el-icon-document-delete" /> {{ $t('documents.cancel') }}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </el-col>
    </el-row>
    <!-- info doc -->
    <el-card v-if="document" shadow="never" class="subtle-card">
      <el-row :gutter="10" class="doc-infos">
        <el-col :span="4">
          <div class="doc-title">
            <h4>{{ $t('documents.types.'+document.type) | uppercaseAll }} <span> #{{ document.code | leftPad(4) }}</span></h4>
          </div>
        </el-col>
        <el-col :span="6">
          <el-link type="primary" @click="$router.push({ name: 'viewcompany', params: { companyId: document.company.id } })"><i class="el-icon-office-building" /> {{ document.company.name }}</el-link>
        </el-col>
        <el-col :span="6">
          <span class="info-item"><i class="el-icon-date" /> <time class="time">{{ document.date | parseTime('{d}-{m}-{y}') }}</time></span>
        </el-col>
        <el-col :span="8">
          <el-tag class="fr" :type="tagClass(document.status)" effect="dark" size="mini">
            {{ $t('documents.status.'+document.status) }}
          </el-tag>
        </el-col>
      </el-row>
      <!-- Document items -->
      <el-row class="document-items">
        <el-tabs v-model="activeTab">
          <!-- items tab -->
          <el-tab-pane :label="$t('documents.items')" name="first">
            <el-table
              :data="document.items"
              :loading="loading"
              border
              stripe
              style="width: 100%"
            >
              <el-table-column
                type="index"
                width="50"
                align="center"
              />
              <el-table-column
                prop="designation"
                :label="$t('documents.description')"
              />
              <el-table-column
                prop="quantity"
                :label="$t('documents.qty')"
                width="80"
                align="right"
              />
              <el-table-column
                prop="unit"
                :label="$t('documents.unit')"
                width="60"
                align="center"
              />
              <el-table-column
                prop="unit_price"
                :label="$t('documents.u_price')"
                align="right"
                width="120"
              >
                <template slot-scope="scope">
                  {{ scope.row.unit_price | currencyFormat }}
                </template>
              </el-table-column>
              <el-table-column
                prop="discount"
                :label="$t('documents.discount') +' %'"
                align="right"
                width="100"
              />
              <el-table-column
                :label="$t('documents.amount')"
                align="right"
                width="170"
              >
                <template slot-scope="scope">
                  {{ scope.row.amount | currencyFormat }}
                </template>
              </el-table-column>
            </el-table>
            <!-- totals -->
            <el-row class="totals">
              <el-col :span="24">
                <table class="totals-table">
                  <tbody>
                    <tr>
                      <th><h5>{{ $t('documents.total_ht') }}:</h5></th>
                      <th><h5 class="bold">{{ document.total_ht | currencyFormat }} {{ currency }}</h5></th>
                    </tr>
                    <tr v-if="document.vatrate > 0">
                      <th><h5>{{ $t('documents.total_tva') }} {{ document.vatrate }}%:</h5></th>
                      <th><h5 class="bold">{{ document.total_tax | currencyFormat }} {{ currency }}</h5></th>
                    </tr>
                    <tr v-if="document.stamprate > 0">
                      <th><h5>{{ $t('documents.total_stamp') }} {{ document.stamprate }}%:</h5></th>
                      <th><h5 class="bold">{{ document.total_stamp | currencyFormat }} {{ currency }}</h5></th>
                    </tr>
                    <tr>
                      <th><h5>{{ $t('documents.total_discount') }}:</h5></th>
                      <th><h5 class="bold">{{ document.total_discount | currencyFormat }} {{ currency }}</h5></th>
                    </tr>
                    <tr>
                      <th><h5>{{ $t('documents.total_ttc') }}:</h5></th>
                      <th><h5 class="bold">{{ document.total_net | currencyFormat }} {{ currency }}</h5></th>
                    </tr>
                  </tbody>
                </table>
              </el-col>
            </el-row>
          </el-tab-pane>
          <!-- Versements tab -->
          <el-tab-pane :label="$t('documents.payement')" name="second">Versements</el-tab-pane>
          <!-- observation tab -->
          <el-tab-pane :label="$t('documents.observations')" name="third">
            <el-row style="margin-top: 20px">
              <p>{{ document.observations }}</p>
            </el-row>
          </el-tab-pane>
        </el-tabs>
      </el-row>
    </el-card>
    <!-- pdf -->
    <vue-html2pdf
      v-if="!loading"
      ref="html2Pdf"
      :show-layout="false"
      :enable-download="false"
      :preview-modal="true"
      :paginate-elements-by-height="1400"
      filename="Document"
      :pdf-quality="2"
      :manual-pagination="false"
      pdf-format="a4"
      pdf-content-width="800px"
      @hasGenerated="hasGenerated($event)"
    >
      <section slot="pdf-content" class="pdf-content">
        <!-- header -->
        <el-row :gutter="15" flex justify="space-between" class="pdf-header">
          <el-col :span="3">
            <div class="logo-container">
              <img :src="'uploads/'+configFormated.company_logo" height="90px" alt="logo">
            </div>
          </el-col>
          <el-col :span="13">
            <div class="company-name">
              <h4>{{ configFormated.company_name }}</h4>
              <span>{{ configFormated.company_sector }}</span>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="company-ids">
              <p>{{ $t('config.ai') }}: {{ configFormated.company_ai }}</p>
              <p>{{ $t('config.rc') }}: {{ configFormated.company_rc }}</p>
              <p>{{ $t('config.nif') }}: {{ configFormated.company_nif }}</p>
              <p>{{ $t('config.nis') }}: {{ configFormated.company_nis }}</p>
              <p>{{ $t('config.rib') }}: {{ configFormated.company_rib }}</p>
            </div>
          </el-col>
        </el-row>
        <!-- document title -->
        <el-row :gutter="0" class="pdf-title items-center">
          <el-col :span="18">
            <div class="doc-type">
              <h4>{{ $t('documents.types.' + document.type) }}</h4>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="doc-code">
              {{ document | codeFormat($t('documents.types.'+document.type)) }}
            </div>
          </el-col>
        </el-row>
        <!-- client info -->
        <el-row :gutter="15" align="start" class="pdf-client">
          <el-col :span="8">
            <div class="client-info">
              <h5>{{ $t('documents.client') }}: {{ document.company.name }}</h5>
              <p>{{ $t('companies.address') }}: {{ document.company.address }} - {{ document.company.region }}</p>
              <p>{{ $t('companies.tel') }}: {{ document.company.tel }}</p>
              <p>{{ $t('companies.fax') }}: {{ document.company.fax }}</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="client-info">
              <br>
              <p>{{ $t('companies.ai') }}: {{ document.company.ai }}</p>
              <p>{{ $t('companies.rc') }}: {{ document.company.rc }}</p>
              <p>{{ $t('companies.nif') }}: {{ document.company.nif }}</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="client-info">
              <h5>{{ $t('documents.date') | uppercaseAll }}: {{ document.date | parseTime('{d}-{m}-{y}') }}</h5>
            </div>
          </el-col>
        </el-row>
        <!-- document items -->
        <el-row>
          <table class="items-table">
            <thead>
              <th v-for="(th, index) in thead" :key="index" :class="th.align" :width="th.width">
                {{ $t('documents.'+th.title) }}
              </th>
            </thead>
            <tbody>
              <tr v-for="item in document.items" :key="item.id">
                <td>
                  {{ item.designation }}
                </td>
                <td width="80px" class="right">
                  {{ item.quantity }}
                </td>
                <td width="40px">
                  {{ item.unit }}
                </td>
                <td width="100px" class="right">
                  {{ item.unit_price | currencyFormat }}
                </td>
                <td width="70px" class="right">
                  {{ item.discount }}
                </td>
                <td width="120px" class="right">
                  {{ item.amount | currencyFormat }}
                </td>
              </tr>
            </tbody>
          </table>
          <!-- totals -->
          <el-row class="totals">
            <el-col :span="24">
              <table class="totals-pdf">
                <tbody>
                  <tr>
                    <td width="118"><h5>{{ $t('documents.total_ht') }}:</h5></td>
                    <td><h5 class="bold">{{ document.total_ht | currencyFormat }} {{ currency }}</h5></td>
                  </tr>
                  <tr v-if="document.vatrate > 0">
                    <td><h5>{{ $t('documents.total_tva') }} {{ document.vatrate }}%:</h5></td>
                    <td><h5 class="bold">{{ document.total_tax | currencyFormat }} {{ currency }}</h5></td>
                  </tr>
                  <tr v-if="document.stamprate > 0">
                    <td><h5>{{ $t('documents.total_stamp') }} {{ document.stamprate }}%:</h5></td>
                    <td><h5 class="bold">{{ document.total_stamp | currencyFormat }} {{ currency }}</h5></td>
                  </tr>
                  <tr>
                    <td><h5>{{ $t('documents.total_discount') }}:</h5></td>
                    <td><h5 class="bold">{{ document.total_discount | currencyFormat }} {{ currency }}</h5></td>
                  </tr>
                  <tr>
                    <td><h5>{{ $t('documents.total_ttc') }}:</h5></td>
                    <td><h5 class="bold">{{ document.total_net | currencyFormat }} {{ currency }}</h5></td>
                  </tr>
                  <tr class="net-pay">
                    <td><h5>{{ $t('documents.total_net') }}:</h5></td>
                    <td><h5 class="bold">{{ document.total_net | currencyFormat }} {{ currency }}</h5></td>
                  </tr>
                </tbody>
              </table>
            </el-col>
          </el-row>
        </el-row>
        <!-- footer -->
        <el-row class="pdf-footer" :gutter="15">
          <el-col :span="24">
            <div class="company-ids">
              <b>{{ configFormated.company_name }}</b>
              <p>{{ $t('config.address') }}: {{ configFormated.company_address }}, {{ configFormated.company_region }} {{ configFormated.company_country }}.</p>
              <p>{{ $t('config.tel') }}: {{ configFormated.company_tel }}</p>
              <p v-if="configFormated.company_fax">{{ $t('config.fax') }}: {{ configFormated.company_fax }}</p>
              <p>{{ $t('config.email') }}: {{ configFormated.company_email }}</p>
            </div>
          </el-col>
        </el-row>
      </section>
    </vue-html2pdf>
  </div>
</template>

<script>
import { getCurrency } from '@/utils/auth';
import { mapGetters } from 'vuex';
import { docStatus } from '@/utils/';
import DocResource from '@/api/document';
const docResource = new DocResource();

export default {
  data(){
    return {
      docId: '',
      loading: true,
      generating: false,
      document: {},
      activeTab: 'first',
      thead: [
        { title: 'description', align: 'left', width: 'auto', print: true },
        { title: 'qty', align: 'left', width: '80px', print: true },
        { title: 'unit', align: 'left', width: '40px', print: true },
        { title: 'u_price', align: 'right', width: '100px', print: true },
        { title: 'discount', align: 'right', width: '70px', print: true },
        { title: 'amount', align: 'right', width: '120px', print: true },
      ],
    };
  },
  computed: {
    currency(){
      return getCurrency();
    },
    ...mapGetters(['config']),
    configFormated(){
      const config = {};
      this.config.forEach(item => {
        config[item.name] = item.value;
      });
      return config;
    },
  },
  created() {
    this.docId = this.$route.params.docId;
    this.loadDocument();
  },
  methods: {
    async loadDocument(){
      this.loading = true;
      const { document } = await docResource.get(this.docId);
      // this.document.code = this.leftPad(this.document.code);
      this.document = this.$options.filters.totals(document);
      // console.log(document);
      this.loading = false;
    },
    async deleteDocument(id, code){
      this.$confirm(this.$t('documents.confirmDelete', { code: this.leftPad(code) }), '', {
        confirmButtonText: this.$t('table.confirm'),
        cancelButtonText: this.$t('table.cancel'),
        type: 'warning',
        showClose: false,
        dangerouslyUseHTMLString: true,
      }).then(() => {
        // delete document
        docResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: this.$t('documents.deleteSucces', { code: this.leftPad(code) }),
            dangerouslyUseHTMLString: true,
          });
          // redirect to document
          this.$router.push({ name: 'documents' });
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        // do nothing
      });
    },
    async printDocument() {
      this.generating = true;
      this.$refs.html2Pdf.generatePdf();
    },
    async handleEditCommand(command) {
      if (command === 'terminate'){
        docResource.terminate(this.docId).then(res => {
          this.$message({
            type: 'success',
            message: this.$t('documents.finished'),
          });
          this.document.status = 'finished';
        });
      } else if (command === 'cancel') {
        docResource.cancel(this.docId).then(res => {
          this.$message({
            type: 'success',
            message: this.$t('documents.canceled'),
          });
          this.document.status = 'canceled';
        });
      } else if (command === 'dublicate') {
        this.$router.push({ name: 'newdocument', params: { docId: this.docId }});
      }
    },
    hasGenerated() {
      console.log('pdf ready');
      this.generating = false;
    },
    leftPad(num) {
      this.$options.filters.leftPad(num, 4);
    },
    tagClass(status){
      return docStatus(status);
    },
  },
};
</script>

<style lang="scss" scoped>
.document-wrapper{
  padding: 10px 60px;
}
.more-btn {
  margin-left: 0;
  padding: 9px
}
.doc-title{
  h4{
    display: inline-block;
    margin: 0 10px 0 0;
    font-size: 16px;
  }
}
.doc-infos{
  font-size: 13px;
  margin-bottom: 30px;
  .info-item{
    line-height: 1.3;
  }
}
.document-items{
  margin-top: 10px;
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
    padding-bottom: 10px;
  }
  .totals-table{
    width: 360px;
    float: right;
  }
}
.pdf-content {
  padding: 20px;
  height: 1120px;
  .pdf-header {
    display: flex;
    align-items: center;
    margin-bottom: 80px;
    .logo-container {
      height: 60px;
      img{
        height: 100%;
      }
    }
    .company-name {
      h4 {
        margin: 5px 0;
      }
      span{
        font-size: 13px;
      }
    }
    .company-ids {
      background: #f9f9f9;
      padding: 10px;
      border-radius: 2px;
      p{
        padding: 3px 0 0 0;
        margin: 0;
        font-size: 11px;
        font-weight: 400;
        &:first-child {
          padding: 0;
        }
      }
    }
  }
  .pdf-title{
    margin-top: 70px;
    .doc-type {
      padding: 15px;
      background: #1539AE;
      color: #f7f7f7;
      text-align: right;
      border-radius: 4px 0 0 4px;
      h4 {
        margin: 0;
        margin-right: 5px;
        text-transform: uppercase;
        font-size: 18px;
      }
    }
    .doc-code {
      padding: 15px;
      font-weight: bold;
      font-size: 18px;
      border-radius: 0 4px 4px 0;
      background: #f7f7f7;
    }
  }
  .pdf-client{
    margin: 50px 0;
    .client-info {
      h5 {
        margin: 0 0 3px 0;
        padding: 0;
        text-transform: uppercase;
      }
      p {
        padding: 3px 0 0 0;
        margin: 0;
        font-size: 12px;
        font-weight: 400;
        &:first-child {
          padding: 0;
        }
      }
    }
  }
  .items-table {
    border-collapse: collapse;
    width: 100%;
    // border: 1px solid #eeeeee;
    th, td {
      border: 1px solid #f7f7f7;
      padding: 7px 10px;
    }
    th{
      height: 40px;
      background: #f7f7f7;
      color: #444;
      font-size: 13px;
    }
    td {
      font-size: 11px;
      color: #666;
    }
    .right {
      text-align: right;
    }
    .left {
      text-align: left;
    }
  }
  .totals-pdf{
    border-collapse: collapse;
    width: 300px;
    float: right;
    tr{
      border: none;
      padding: 10px;
      h5{
        padding: 5px 10px;
      }
    }
    .net-pay {
      background: #1539AE;
      color: white;
    }
  }
  .pdf-footer{
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    margin: 0 20px;
    .company-ids {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-around;
      background: #f9f9f9;
      padding: 10px;
      border-radius: 2px;
      p{
        padding: 3px 0 0 0;
        margin: 0;
        font-size: 10px;
        font-weight: 400;
        &:first-child {
          padding: 0;
        }
      }
    }
  }
}
</style>
