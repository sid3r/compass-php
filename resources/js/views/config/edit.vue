<template>
  <div class="data-padding">
    <!-- top menu -->
    <el-row :gutter="15" class="top-menu">
      <el-col :span="24">
        <el-button type="primary" :loading="saving" class="el-mobile-btn fr" icon="el-icon-check" @click="saveConfig">
          {{ $t('table.save') }}
        </el-button>
      </el-col>
    </el-row>
    <el-form ref="form" :model="form" label-position="top" label-width="">
      <el-row :gutter="20">
        <el-col :xs="24" :sm="12" :md="6" :lg="6">
          <!-- company -->
          <el-card shadow="never" class="subtle-card">
            <div v-for="(item, index) in configGrouped.company" :key="index" class="config-item">
              <div v-if="item.name === 'company_logo'">
                <el-upload
                  action="/api/uploadimage"
                  :headers="{ 'X-CSRF-TOKEN': csrf}"
                  :on-success="handleUploadSuccess"
                  :multiple="false"
                  :show-file-list="false"
                  class="upload-zone"
                  drag
                >
                  <img v-if="preview" :src="preview" class="company-logo">
                  <div v-else class="el-upload__text">
                    <i class="el-icon-upload" /> <br>
                    <span>{{ $t('products.upload') }}</span>
                  </div>
                </el-upload>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" :md="9" :lg="9">
          <!-- invoicing -->
          <el-card shadow="never" class="subtle-card">
            <div slot="header" class="clearfix">
              <b>{{ $t('configuration.company.company') }}</b>
            </div>
            <div v-for="(item, index) in configGrouped.company" :key="index" class="config-item">
              <el-form-item v-if="item.name !== 'company_logo'" :label="$t('configuration.company.'+item.name)">
                <el-input v-model="item.value" />
              </el-form-item>
            </div>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" :md="9" :lg="9">
          <!-- currency -->
          <el-card shadow="never" class="subtle-card">
            <div slot="header" class="clearfix">
              <b>{{ $t('configuration.invoicing.invoicing') }}</b>
            </div>
            <div v-for="(item, index) in configGrouped.invoicing" :key="index" class="config-item">
              <el-form-item v-if="item.name !== 'display_stamp'" :label="$t('configuration.invoicing.'+item.name)">
                <el-input v-model="item.value" />
              </el-form-item>
              <el-form-item v-else :label="$t('configuration.invoicing.'+item.name)">
                <el-radio v-model="item.value" label="yes">{{ $t('configuration.yes') }}</el-radio>
                <el-radio v-model="item.value" label="no">{{ $t('configuration.no') }}</el-radio>
              </el-form-item>
            </div>
            <div v-for="(item, index) in configGrouped.currency" :key="index+20" class="config-item">
              <el-form-item :label="$t('configuration.currency.'+item.name)">
                <el-input v-model="item.value" />
              </el-form-item>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import { groupBy } from '@/utils/';
import { mapGetters } from 'vuex';
import Resource from '@/api/resource';
const configResource = new Resource('config');

export default {
  data() {
    return {
      saving: false,
      preview: null,
      form: {},
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };
  },
  computed: {
    ...mapGetters([
      'config',
    ]),
    configGrouped(){
      return groupBy(this.config, 'group');
    },
  },
  mounted() {
    const found = this.configGrouped.company.filter(item => {
      return item.name === 'company_logo';
    });
    this.preview = 'uploads/' + found[0].value;
  },
  methods: {
    handleUploadSuccess(response, file) {
      console.log(response);
      this.preview = URL.createObjectURL(file.raw);
      this.configGrouped.company[0].value = response;
    },
    saveConfig(){
      this.saving = true;
      configResource.store(this.config).then(res => {
        this.$message({
          showClose: true,
          message: this.$t('configuration.saved'),
          type: 'success',
        });
        this.saving = false;
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.top-menu {
  margin-bottom: 30px;
  min-height: 30px;
}
.company-logo {
  width: 100%;
}
</style>
