<template>
  <div class="app-container">
    <div v-if="categoriesNested" v-loading="loading" element-loading-background="#FBFCFD" class="data-padding">
      <el-row :gutter="30">
        <el-col :xs="24" :sm="12">
          <div class="reoder-menu">
            <h5>{{ $t('products.cat_reorder') }}</h5>
            <el-button v-if="listReordered" type="primary" plain icon="el-icon-check" @click="saveReordered">{{ $t('table.save') }}</el-button>
          </div>
          <!-- tree drag-n-drop -->
          <el-card shadow="never" class="subtle-card">
            <el-input
              v-model="filterText"
              :placeholder="$t('table.search')"
              style="margin-bottom: 10px"
            />
            <el-tree ref="tree" :data="formatedList" :props="props" draggable :default-expand-all="true" icon-class="el-icon-arrow-right" :expand-on-click-node="false" :allow-drop="allowDrop" :filter-node-method="filterNode" @node-drag-end="nodeDragEnd">
              <span slot-scope="{ node, data }" class="custom-tree-node">
                <b>{{ node.label }}</b>
                <span>
                  <el-button
                    type="text"
                    size="mini"
                    @click="() => handleEdit(data)"
                  >
                    <i class="el-icon-edit" />
                  </el-button>
                  <el-button
                    type="text"
                    size="mini"
                    @click="() => handleDelete(data)"
                  >
                    <i class="el-icon-delete" />
                  </el-button>
                </span>
              </span>
            </el-tree>
          </el-card>
          <div style="margin-top: 10px">
            <i class="el-icon-info" /> <small>{{ $t('products.dragToReorder') }}</small>
          </div>
        </el-col>
        <el-col :span="24" :sm="12">
          <h5 v-if="form.id">{{ $t('table.edit') }}</h5>
          <h5 v-else>{{ $t('table.add') }}</h5>
          <el-card shadow="never" class="subtle-card">
            <el-form ref="form" label-position="top" :model="form">
              <el-form-item
                :label="$t('products.name')"
                prop="name"
                :rules="{ required: true, trigger: 'blur'}"
                :show-message="false"
              >
                <el-input v-model="form.name" type="text" :placeholder="$t('products.name')" />
              </el-form-item>
              <el-form-item :label="$t('products.cat_parent')">
                <el-select
                  v-model="form.parent_id"
                  :placeholder="$t('products.cat_parent')"
                  style="width: 100%"
                >
                  <el-option
                    v-for="cat in categoriesNotNested"
                    :key="cat.id"
                    :label="cat.name"
                    :value="cat.id"
                  />
                </el-select>
              </el-form-item>
              <el-form-item :label="$t('products.cat_order')">
                <el-input v-model="form.order" type="text" :placeholder="$t('products.cat_order')" />
              </el-form-item>
              <el-form-item :label="$t('products.description')">
                <el-input v-model="form.description" type="textarea" :placeholder="$t('products.description')" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" icon="el-icon-check" class="fr" @click="saveCategory">{{ $t('table.save') }}</el-button>
                <el-button v-if="form.id" class="fr" style="margin-right: 5px" @click="cancelEdit">{{ $t('table.cancel') }}</el-button>
              </el-form-item>
            </el-form>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import CatResource from '@/api/category';
const categoryResource = new CatResource('categories');

export default {
  data(){
    return {
      loading: true,
      listReordered: false,
      categoriesNested: [],
      categoriesNotNested: [],
      formatedList: [],
      props: {
        label: 'name',
        children: 'children',
      },
      filterText: '',
      form: {
        name: '',
        parent_id: '',
        order: '',
        description: '',
      },
    };
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    },
  },
  created(){
    this.loadList();
  },
  methods: {
    filterNode(value, data) {
      if (!value) {
        return true;
      }
      return data.name.indexOf(value) !== -1;
    },
    async loadList(){
      const data = await categoryResource.list({ nest: true });
      this.categoriesNested = data;
      this.formatCategories();
      // console.log(data);
      const data_not_nested = await categoryResource.list();
      this.categoriesNotNested = data_not_nested;
      this.loading = false;
    },
    nodeDragEnd(node, nodeTo){
      if (this.allowDrop(node, nodeTo)){
        node.data.parent_id = nodeTo.data.id;
        this.listReordered = true;
      }
      // console.log(nodeTo);
    },
    allowDrop(node, nodeTo){
      if (nodeTo.level < 2){
        return true;
      } else {
        return false;
      }
    },
    async saveCategory(){
      this.$refs['form'].validate((valid) => {
        if (valid) {
          if (this.form.id) {
            // update if id
            categoryResource.update(this.form.id, this.form).then((res) => {
              this.$message({
                showClose: true,
                message: this.$t('products.categoryUpdated'),
                type: 'success',
              });
            });
          } else {
            // save new
            categoryResource.store(this.form).then((res) => {
              this.$message({
                showClose: true,
                message: this.$t('products.categoryAdded'),
                type: 'success',
              });
              // console.log(res);
            });
          }
          this.loadList();
          this.form = {
            name: '',
            parent_id: '',
            order: '',
            description: '',
          };
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
    async saveReordered() {
      await categoryResource.reorder(this.formatedList).then(res => {
        this.$message({
          showClose: true,
          message: this.$t('products.categoryUpdated'),
          type: 'success',
          dangerouslyUseHTMLString: true,
        });
        this.listReordered = false;
        this.loadList();
      }).catch(err => {
        console.log(err);
      });
    },
    formatCategories(){
      this.formatedList = [];
      this.categoriesNested.forEach(item => {
        const ct = { id: item.id, name: item.name, parent_id: item.parent_id, order: item.order, children: [] };
        if (item.children){
          item.children.forEach(child => {
            ct.children.push({ id: child.id, name: child.name, parent_id: child.parent_id, order: child.order, children: [] });
          });
        }
        this.formatedList.push(ct);
      });
    },
    handleEdit(data){
      this.form = Object.assign({}, data);
    },
    handleDelete(data){
      this.$confirm(this.$t('products.catDeleteConfim', { name: data.name }), '', {
        confirmButtonText: this.$t('table.confirm'),
        cancelButtonText: this.$t('table.cancel'),
        type: 'warning',
        showClose: false,
        dangerouslyUseHTMLString: true,
      }).then(() => {
        // delete contact
        categoryResource.destroy(data.id).then(response => {
          this.$message({
            type: 'success',
            message: this.$t('products.categoryDeleted', { name: data.name }),
            dangerouslyUseHTMLString: true,
          });
          this.loadList();
        }).catch(error => {
          console.log(error);
        });
      });
    },
    cancelEdit() {
      this.form = {
        name: '',
        parent_id: '',
        order: '',
        description: '',
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.title{
  padding: 0;
  margin: 0;
}
.categories-drag{
  list-style: none;
  padding-left: 0;
}
.reoder-menu{
  display: flex;
  align-items: center;
  h5{
    flex-grow: 1;
  }
}
.el-tree-node__content{
  height: 30px !important;
}
.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  font-weight: 200;
  padding-right: 8px;
}
</style>
