<template>
  <div class="dashboard-editor-container data-padding">
    <widgets />
    <pendingdocs v-if="permissions.includes('view menu documents')" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Widgets from './../components/widgets';
import Pendingdocs from './../components/pendingdocs';

export default {
  name: 'DashboardEditor',
  components: { Widgets, Pendingdocs },
  data() {
    return {
      //
    };
  },
  computed: {
    ...mapGetters([
      'permissions',
    ]),
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

  .dashboard-editor-container {

    min-height: 100vh;

    .pan-info-roles {
      font-size: 12px;
      font-weight: 700;
      color: #333;
      display: block;
    }
    .info-container {
      position: relative;
      margin-left: 190px;
      height: 150px;
      line-height: 200px;
      .display_name {
        font-size: 48px;
        line-height: 48px;
        color: #212121;
        position: absolute;
        top: 25px;
      }
    }
  }
</style>
