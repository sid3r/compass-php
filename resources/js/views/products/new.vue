<template>
  <div class="app-container">
    <el-row class="top-bar">
      <el-col :span="24" class="buttons">
        <el-button type="primary" icon="el-icon-check" class="fr" @click="saveproduct">{{ $t('table.save') }}</el-button>
      </el-col>
    </el-row>
    <div class="data">
      <el-form ref="form" label-position="top" :model="form">
        <el-row :gutter="25">
          <el-col :xs="24" :sm="24" :md="24" :lg="8">
            <el-upload
              action="/api/uploadimage"
              :headers="{ 'X-CSRF-TOKEN': csrf}"
              :on-success="handleUploadSuccess"
              :multiple="false"
              :show-file-list="false"
              class="upload-zone"
              drag
            >
              <!-- :headers="{ 'X-CSRF-TOKEN': csrf}" -->
              <img v-if="preview" :src="preview" class="product-image">
              <div v-else class="el-upload__text">
                <i class="el-icon-upload" /> <br>
                <span>{{ $t('products.upload') }}</span>
              </div>
            </el-upload>
            <div v-if="form.image" style="margin-top: 10px">
              <i class="el-icon-info" /><small> {{ $t('products.uploadHelper') }}</small>
            </div>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="8">
            <el-card shadow="never">
              <el-form-item
                :label="$t('products.name')"
                prop="name"
                :rules="{ required: true, trigger: 'blur'}"
                :show-message="false"
              >
                <el-input v-model="form.name" type="text" :placeholder="$t('products.name')" />
              </el-form-item>
              <el-form-item
                :label="$t('products.category')"
                prop="category_id"
                :rules="{ required: true, trigger: 'change'}"
                :show-message="false"
              >
                <el-select
                  v-model="form.category_id"
                  :placeholder="$t('products.category')"
                  style="width: 100%"
                >
                  <el-option
                    v-for="cat in categories"
                    :key="cat.id"
                    :label="cat.name"
                    :value="cat.id"
                  />
                </el-select>
              </el-form-item>
              <el-form-item :label="$t('products.bar_code')">
                <el-input v-model="form.bar_code" type="text" :placeholder="$t('products.bar_code')" />
              </el-form-item>
              <el-form-item
                :label="$t('products.description')"
              >
                <el-input
                  v-model="form.description"
                  type="textarea"
                  :rows="1"
                  :placeholder="$t('products.description')"
                />
              </el-form-item>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="8">
            <el-card shadow="never">
              <el-form-item
                :label="$t('products.unit_price')"
                prop="unit_price"
                :rules="{ required: true, trigger: 'blur'}"
                :show-message="false"
              >
                <el-input v-model="form.unit_price" type="text" :placeholder="$t('products.unit_price')" />
              </el-form-item>
              <el-form-item
                :label="$t('products.unit_sale_price')"
                prop="unit_sale_price"
                :rules="{ required: true, trigger: 'blur'}"
                :show-message="false"
              >
                <el-input v-model="form.unit_sale_price" type="text" :placeholder="$t('products.unit_sale_price')" />
              </el-form-item>
              <el-form-item
                :label="$t('products.discount')"
              >
                <el-input v-model="form.discount" type="text" :placeholder="$t('products.discount')" />
              </el-form-item>
              <el-form-item
                :label="$t('products.min_qty')"
                prop="min_qty"
                :rules="{ required: true, trigger: 'blur'}"
                :show-message="false"
              >
                <el-input v-model="form.min_qty" type="text" :placeholder="$t('products.min_qty')" />
              </el-form-item>
            </el-card>
          </el-col>
        </el-row>
      </el-form>
    </div>
  </div>
</template>

<script>
import Resource from '@/api/resource';
const productResource = new Resource('products');
const categoryResource = new Resource('categories');

export default {
  data(){
    return {
      categories: [],
      form: {
        name: '',
        category_id: '',
        bar_code: '',
        description: '',
        unit_price: '',
        unit_sale_price: '',
        discount: '',
        min_qty: '',
        image: '',
      },
      preview: '',
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };
  },
  created() {
    if (this.$route.params.productId) {
      this.loadProduct(this.$route.params.productId);
    }
    this.getCategories();
  },
  methods: {
    async getCategories(){
      const data = await categoryResource.list();
      this.categories = data;
    },
    async loadProduct(id){
      const { product } = await productResource.get(id);
      this.form = product;
      this.preview = 'uploads/' + product.image;
    },
    handleUploadSuccess(response, file) {
      console.log(response);
      this.preview = URL.createObjectURL(file.raw);
      this.form.image = response;
    },
    async saveproduct() {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          if (this.form.id) {
            // update if id
            productResource.update(this.form.id, this.form).then((res) => {
              this.$message({
                showClose: true,
                message: this.$t('products.productUpdated', { name: this.form.name }),
                type: 'success',
                dangerouslyUseHTMLString: true,
              });
              // console.log(res);
              this.$router.push({ name: 'viewproduct', params: { productId: this.form.id }});
            });
          } else {
            // save new
            productResource.store(this.form).then((res) => {
              this.$message({
                showClose: true,
                message: this.$t('products.productAdded', { name: this.form.name }),
                type: 'success',
                dangerouslyUseHTMLString: true,
              });
              // console.log(res);
              this.$router.push({ name: 'viewproduct', params: { productId: res.id }});
            });
          }
        } else {
          this.$message({
            showClose: true,
            message: this.$t('products.formErrorMessage'),
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
.product-image {
  width: 100%;
  height: auto;
}
</style>
