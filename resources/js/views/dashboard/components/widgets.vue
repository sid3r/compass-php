<template>
  <div class="widgets-wrapper">
    <el-row v-loading="loading" element-loading-background="#FBFCFD" :gutter="20" class="widgets">
      <el-col v-for="(widget, index) in widgets" :key="index" :xs="12" :sm="12" :md="8" :lg="4">
        <div v-if="permissions.includes(widget.permission)" class="widget" :class="widget.color" @click="goto(widget.route)">
          <div class="icon">
            <svg-icon :icon-class="widget.icon" />
          </div>
          <div class="body">
            <span class="title">{{ $t('route.' + widget.route) }}</span>
            <span class="count">{{ widget.count }}</span>
          </div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import DashboardResource from '@/api/dashboard';
const dashboardresource = new DashboardResource('dashboard');

export default {
  data() {
    return {
      loading: true,
      widgets: [],
    };
  },
  computed: {
    ...mapGetters([
      'permissions',
    ]),
  },
  mounted(){
    this.getWidgets();
  },
  methods: {
    async getWidgets(){
      const widgets = await dashboardresource.widgets();
      this.widgets = widgets;
      this.loading = false;
    },
    goto(route){
      // console.log(route);
      this.$router.push({ name: route });
    },
  },
};
</script>

<style lang="scss" scoped>
.widgets-wrapper{
  min-height: 120px;
}
.section-title {
  padding: 0 0 20px 0;
  color: rgb(107, 107, 107);
  margin: 0;
  font-size: 13px;
  text-transform: uppercase;
}
.widgets {
  margin-bottom: 10px;
  .widget {
    display: flex;
    align-items: center;
    min-height: 70px;
    width: 100%;
    cursor: pointer;
    border-radius: 4px;
    padding: 10px 7px;
    box-shadow: 2px 2px 8px 0 rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    transition: all ease-in-out .2s;
    &.primary{
      background: #8395cf;
      &:hover {
        background: darken(#8395cf, 10%);
      }
    }
    &.red{
      background: #cf8383;
      &:hover {
        background: darken(#cf8383, 10%);
      }
    }
    &.yellow{
      background: rgb(199, 195, 136);
      &:hover {
        background: darken(rgb(211, 207, 145), 20%);
      }
    }
    &.green{
      background: rgb(140, 212, 185);
      &:hover {
        background: darken(rgb(140, 212, 185), 20%);
      }
    }
    &.purple{
      background: rgb(192, 141, 212);
      &:hover {
        background: darken(rgb(192, 141, 212), 20%);
      }
    }
    &.orange{
      background: rgb(207, 171, 129);
      &:hover {
        background: darken(rgb(207, 171, 129), 20%);
      }
    }
    &:hover {
      transform: scale(1.05);
    }
    .icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      color: #ffffff;
      svg {
        fill: rgba(#ffffff, 0.7);
        stroke-width: 0.2;
        width: 28px;
        height: 28px;
      }
    }
    .body {
      .title {
        color: #ffffff;
        font-size: 12px;
        font-weight: bold;
        width: 100%;
        display: block;
        margin-bottom: 2px;
      }
      .count {
        display: block;
        font-size: 14px;
        color: #ffffff;
      }
    }
  }
}
</style>
