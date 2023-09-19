<template>
  <div class="navbar" :class="{ 'navbar-mobile' : device === 'mobile'}">
    <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

    <breadcrumb id="breadcrumb-container" class="breadcrumb-container" />

    <!-- right menu -->
    <div class="right-menu">
      <!-- Quick actions -->
      <template v-if="device!=='mobile'">
        <!-- <el-tooltip :content="$t('navbar.size')" effect="dark" placement="bottom">
          <size-select id="size-select" class="right-menu-item hover-effect" />
        </el-tooltip> -->
        <el-tooltip :content="$t('navbar.lang')" effect="dark" placement="bottom">
          <lang-select class="right-menu-item hover-effect" />
        </el-tooltip>
        <!-- <el-tooltip :content="$t('navbar.fullscreen')" effect="dark" placement="bottom">
          <screenfull id="screenfull" class="right-menu-item hover-effect" />
        </el-tooltip> -->
      </template>
      <!-- POS -->
      <el-tooltip :content="$t('route.pos')" effect="dark" placement="bottom">
        <el-button icon="el-icon-s-shop" class="pos-btn" @click="$router.push({ name: 'pos' })" />
      </el-tooltip>
      <!-- User avatar dropdown -->
      <el-dropdown class="avatar-container right-menu-item hover-effect" trigger="click">
        <div class="avatar-wrapper">
          <!-- <img src="images/logo.svg" class="user-avatar"> -->
          <el-avatar size="small" class="avatar">{{ userChar }}</el-avatar>
        </div>
        <el-dropdown-menu slot="dropdown">
          <router-link to="/">
            <el-dropdown-item>
              {{ $t('navbar.dashboard') }}
            </el-dropdown-item>
          </router-link>
          <router-link v-show="userId !== null" :to="`/profile/edit`">
            <el-dropdown-item>
              {{ $t('navbar.profile') }}
            </el-dropdown-item>
          </router-link>
          <el-dropdown-item divided>
            <span style="display:block;" @click="logout">{{ $t('navbar.logOut') }}</span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Breadcrumb from '@/components/Breadcrumb';
import Hamburger from '@/components/Hamburger';
// import Screenfull from '@/components/Screenfull';
// import SizeSelect from '@/components/SizeSelect';
import LangSelect from '@/components/LangSelect';

export default {
  components: {
    Breadcrumb,
    Hamburger,
    // Screenfull,
    // SizeSelect,
    LangSelect,
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'name',
      'avatar',
      'device',
      'userId',
    ]),
    userChar() {
      return this.name[0];
    },
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    },
  },
};
</script>

<style lang="scss" scoped>
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #ffffff;
  border-bottom: 1px solid #F8F8F8;

  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background .3s;
    -webkit-tap-highlight-color:transparent;

    &:hover {
      background: rgba(0, 0, 0, .025)
    }
  }

  .breadcrumb-container {
    float: left;
  }

  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }

  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 15px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background .3s;

        &:hover {
          background: rgba(0, 0, 0, .025)
        }
      }
    }
    .pos-btn {
      vertical-align: top;
      margin-top: 10px;
      margin-left: 7px;
      font-size: 16px;
      padding: 5px;
      // width: 30px;
      border-radius: 100%;
    }

    .avatar-container {
      margin-right: 5px;

      .avatar-wrapper {
        margin-top: 8px;
        position: relative;
        .el-avatar {
          font-size: 20px !important;
        }

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 4px;
        }

        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
// .navbar-mobile {
//   background: #1539ae;
//   border-bottom: 1px solid #1539ae;

//   > .hamburger{
//     fill: #ffffff !important;
//   }
//   > .no-redirect {
//     color: #ffffff;
//   }
// }
</style>
