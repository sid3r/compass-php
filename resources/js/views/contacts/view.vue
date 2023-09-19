<template>
  <div class="app-container">
    <el-row v-if="!loading" element-loading-background="#FBFCFD" class="top-bar">
      <el-col :span="24" class="buttons">
        <el-button type="danger" icon="el-icon-delete" @click="handleDelete(contact.id, contact.name)">
          {{ $t('table.delete') }}
        </el-button>
        <el-button type="primary" icon="el-icon-edit" @click="handleUpdate">
          {{ $t('table.edit') }}
        </el-button>
      </el-col>
    </el-row>
    <!-- contact info -->
    <div v-if="!loading" class="">
      <el-row :gutter="15">
        <el-col :xs="24" :sm="24" :md="12" :lg="12">
          <el-card shadow="never" class="subtle-card">
            <div slot="header" class="clearfix">
              <i class="el-icon-office-building" /> <b>{{ contact.name }}</b>
            </div>
            <p><i class="el-icon-mobile-phone" /> {{ contact.mobile }}</p>
            <p><i class="el-icon-message" /> {{ contact.email }}</p>
          </el-card>
        </el-col>
        <el-col v-if="contact.company" :xs="24" :sm="24" :md="12" :lg="12">
          <el-card shadow="never" class="subtle-card">
            <p>{{ contact.function }} @</p>
            <p><i class="el-icon-office-building" /> {{ contact.company.name }}</p>
            <p><i class="el-icon-phone-outline" /> {{ contact.company.tel }}</p>
            <p><i class="el-icon-printer" /> {{ contact.company.fax }}</p>
            <p><i class="el-icon-location-outline" /> {{ contact.company.address }} - {{ contact.company.region }}</p>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <!-- edit contact dialog -->
    <Dialog
      v-if="!loading"
      :visible="dialogFormVisible"
      :dialog-form-title="dialogFormTitle"
      :contact="contact"
      @close-event="closeDialog"
      @update-event="updatePage"
    />
  </div>
</template>

<script>
import Dialog from './dialog';

import Resource from '@/api/resource';
const contactResource = new Resource('contacts');

export default {
  components: { Dialog },
  data(){
    return {
      contactId: 0,
      loading: true,
      contact: {
        name: '',
      },
      dialogFormVisible: false,
      dialogFormTitle: this.$t('contacts.dialogTitleUpdate'),
    };
  },
  created() {
    this.contactId = this.$route.params.contactId;
    this.loadContact();
  },
  methods: {
    async loadContact(){
      const { contact } = await contactResource.get(this.contactId);
      // console.log(contact);
      this.contact = contact;
      this.loading = false;
    },
    handleUpdate(){
      this.dialogFormVisible = true;
    },
    handleDelete(id, name) {
      // wait for confirm
      this.$confirm(this.$t('contacts.confirmDelete', { name: name }), '', {
        confirmButtonText: this.$t('table.confirm'),
        cancelButtonText: this.$t('table.cancel'),
        type: 'warning',
        showClose: false,
      }).then(() => {
        // delete contact
        contactResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: this.$t('contacts.deleteSucces', { name: name }),
          });
          // redirect to contacts
          this.$router.push({ name: 'contacts' });
        }).catch(error => {
          console.log(error);
        });
      });
    },
    transTag(tag){
      if (tag.native === 'yes'){
        return this.$t('contacts.types.' + tag.name);
      } else {
        return tag.name;
      }
    },
    closeDialog(){
      this.dialogFormVisible = false;
    },
    updatePage(){
      this.dialogFormVisible = false;
      this.loadContact();
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
