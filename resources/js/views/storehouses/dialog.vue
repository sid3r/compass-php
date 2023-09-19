<template>
  <el-dialog :title="dialogTitle" :visible.sync="dialogVisible" :show-close="false" class="el-dialog-primary">
    <div v-loading="storeCreating" class="form-container">
      <el-form ref="form" :rules="rules" :model="currentStore" :show-message="false" label-position="top">
        <el-row :gutter="30">
          <!-- store -->
          <el-col v-if="currentStore" :span="24">
            <el-form-item :label="$t('storehouses.name')" prop="name">
              <el-input v-model="currentStore.name" />
            </el-form-item>
            <el-form-item :label="$t('storehouses.code')" prop="code">
              <el-input v-model="currentStore.code" />
            </el-form-item>
            <el-form-item :label="$t('storehouses.adress')" prop="adress">
              <el-input v-model="currentStore.adress" type="textarea" />
            </el-form-item>
            <el-form-item
              :label="$t('storehouses.users')"
              prop="users_ids"
            >
              <el-select
                v-model="currentStore.users_ids"
                :placeholder="$t('storehouses.users')"
                multiple
                style="width: 100%"
              >
                <el-option
                  v-for="user in users"
                  :key="user.id"
                  :label="user.name"
                  :value="user.id"
                />
              </el-select>
            </el-form-item>
            <el-form-item :label="$t('storehouses.status')">
              <el-radio-group v-model="currentStore.status">
                <el-radio label="active">{{ $t('storehouses.storestatus.active') }}</el-radio>
                <el-radio label="inactive">{{ $t('storehouses.storestatus.inactive') }}</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer" style="text-align: right;">
        <el-button
          @click="closeDialog"
        >
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" :loading="storeCreating" @click="savestore()">
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import Resource from '@/api/resource';
const storeResource = new Resource('storehouses');
const userResource = new Resource('users');

export default {
  props: {
    dialogTitle: { type: String, default: '' },
    store: { type: Object, default: null },
    visible: { type: Boolean, default: false },
  },
  data(){
    return {
      rules: {
        name: [{ required: true, trigger: 'blur' }],
        code: [{ required: true, trigger: 'blur' }],
        adress: [{ required: true, trigger: 'blur' }],
        status: [{ required: true, trigger: 'focu' }],
        users_ids: [{ required: true, trigger: 'focu' }],
      },
      storeCreating: false,
      users: null,
    };
  },
  computed: {
    currentStore() {
      return this.store;
    },
    dialogVisible(){
      return this.visible;
    },
  },
  created() {
    this.loadUsers();
  },
  methods: {
    async loadUsers() {
      const { data } = await userResource.list();
      this.users = data;
      // console.log(data);
    },
    savestore(){
      this.$refs['form'].validate((valid) => {
        if (valid) {
          if (this.currentStore.id){
            // update
            this.storeCreating = true;
            storeResource.update(this.currentStore.id, this.currentStore)
              .then(response => {
                this.$message({
                  message: this.$t('storehouses.updated'),
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
                this.storeCreating = false;
              });
          } else {
            // create
            this.storeCreating = true;
            storeResource.store(this.currentStore)
              .then(response => {
                this.$message({
                  message: this.$t('storehouses.created'),
                  type: 'success',
                  duration: 5 * 1000,
                });
                // console.log(response);
                this.closeDialog();
                this.$emit('update-event');
              })
              .catch(error => {
                console.log(error);
              })
              .finally(() => {
                this.storeCreating = false;
              });
          }
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
