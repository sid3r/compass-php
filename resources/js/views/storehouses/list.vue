<template>
  <div class="data-padding">
    <el-row class="filters">
      <el-col :span="24" class="buttons">
        <el-button type="primary" icon="el-icon-plus" class="fr" @click="toggleCreate">{{ $t('table.add') }}</el-button>
      </el-col>
    </el-row>
    <div class="cards">
      <el-row v-loading="loading" :gutter="15" element-loading-background="#FBFCFD">
        <el-col v-for="store in list" :key="store.id" :xs="24" :sm="12" :md="12" :lg="8" style="margin-bottom: 15px">
          <el-card shadow="never" class="subtle-card">
            <div slot="header" class="clearfix">
              <b>{{ store.name }} <small class="text-muted">{{ store.code }}</small></b>
            </div>
            <div class="text item">
              <div class="title">{{ $t('storehouses.status') }}</div>
              <div class="value">
                <el-tag
                  effect="dark"
                  :type="statusClass(store.status)"
                >
                  {{ $t('storehouses.storestatus.'+store.status) }}
                </el-tag>
              </div>
            </div>
            <div class="text item">
              <div class="title"><a @click="showUsers(store.id)">{{ $t('route.users') }} <i class="el-icon-arrow-down" /></a></div>
              <div class="value">
                <b>{{ store.users.length }}</b>
              </div>
              <div class="break" />
              <div v-if="viewStoreUsers === store.id" class="user-list">
                <div v-if="store.users.length > 0">
                  <el-tag v-for="usr in store.users" :key="usr.id" size="mini">{{ usr.name }}</el-tag>
                </div>
                <div v-else>
                  <el-tag type="info">{{ $t('storehouses.nouser') }}</el-tag>
                </div>
              </div>
            </div>
            <el-divider />
            <div class="text item">
              <div class="title">{{ $t('storehouses.purchase_operations') }}</div>
              <div class="value">
                <el-tag
                  effect="plain"
                >
                  {{ store.purchases.length }}
                </el-tag>
              </div>
            </div>
            <!-- <div class="text item">
              <div class="title">{{ $t('storehouses.purchase_total') }}</div>
              <div class="value">
                <b>{{ store.total_purchase | currencyFormat }}</b>
              </div>
            </div> -->
            <!-- <el-divider /> -->
            <div class="text item">
              <div class="title">{{ $t('storehouses.sale_operations') }}</div>
              <div class="value">
                <el-tag
                  effect="plain"
                  type="danger"
                >
                  {{ store.sales.length }}
                </el-tag>
              </div>
            </div>
            <!-- <div class="text item">
              <div class="title">{{ $t('storehouses.sale_total') }}</div>
              <div class="value">
                <b>{{ store.total_sale | currencyFormat }}</b>
              </div>
            </div> -->
            <!-- <el-divider /> -->
            <div class="actions clearfix">
              <el-button plain class="button" icon="el-icon-edit" @click="toggleEdit(store)">{{ $t('table.edit') }}</el-button>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <Dialog :store="selected" :visible="showDialog" :dialog-title="title" @close-event="closeDialog" @update-event="getList" />
  </div>
</template>

<script>
import Dialog from './dialog';
import Resource from '@/api/resource';
const storeResource = new Resource('storehouses');

export default {
  components: { Dialog },
  data() {
    return {
      loading: true,
      list: null,
      selected: null,
      viewStoreUsers: null,
      showDialog: false,
      title: this.$t('storehouses.editTitle'),
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.loading = true;
      const list = await storeResource.list();
      list.forEach(store => {
        const users_ids = [];
        store.users.forEach(user => {
          users_ids.push(user.id);
        });
        store.users_ids = users_ids;
      });
      this.list = list;
      this.getTotals();
      this.loading = false;
    },
    getTotals(){
      this.list.forEach(store => {
        let total_purchase = 0;
        let total_sale = 0;
        if (store.purchases) {
          store.purchases.forEach(purchase => {
            total_purchase += parseFloat(purchase.total);
          });
        }
        if (store.sales) {
          store.sales.forEach(sale => {
            total_sale += parseFloat(sale.total);
          });
        }
        store.total_purchase = total_purchase;
        store.total_sale = total_sale;
      });
    },
    toggleEdit(store) {
      this.selected = store;
      this.title = this.$t('storehouses.editTitle');
      this.showDialog = true;
    },
    toggleCreate() {
      this.selected = { name: '', code: '', status: 'active', users: [], adress: '' };
      this.title = this.$t('storehouses.createTitle');
      this.showDialog = true;
    },
    closeDialog() {
      this.selected = null;
      this.showDialog = false;
      this.getList();
    },
    statusClass(status){
      switch (status) {
        case 'active':
          return 'success';
        default:
          return 'danger';
      }
    },
    showUsers(id) {
      if (this.viewStoreUsers && this.viewStoreUsers === id) {
        this.viewStoreUsers = null;
      } else {
        this.viewStoreUsers = id;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.cards {
  .subtle-card {
    min-height: 350px;
    position: relative;
  }
}
.title {
  padding: 0;
  margin: 0;
}
.text {
  font-size: 14px;
}
.item {
  margin-bottom: 10px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  .user-list {
    padding-top: 10px;
    margin-top: 10px;
    width: 100%;
    .el-tag {
      margin-right: 5px;
    }
    p {
      margin: 4px 0;
      font-size: 12px;
      font-weight: bold;
    }
  }
}
.break{
  flex-basis: 100%;
  height: 0;
}
.actions {
  margin-top: 20px;
  text-align: center;
  position: absolute;
  width: 100%;
  bottom: 20px;
}
.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}
.clearfix:after {
  clear: both
}
</style>
