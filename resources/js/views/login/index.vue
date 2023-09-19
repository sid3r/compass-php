<template>
  <div class="login-wrapper">
    <div class="logo-container">
      <div class="content">
        <lang-select class="set-language" />
        <div class="center">
          <img src="/svg/logo.svg" alt="Logo" class="logo">
          <h3>Compass</h3>
          <p>{{ $t('app.tagline') }}</p>
        </div>
      </div>
      <div class="footer">
        <a href="#">www.compassapp.com</a>
      </div>
    </div>
    <div class="login-container">
      <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on" label-position="left">
        <el-card shadow="never" class="subtle-card">
          <h3 class="title">
            {{ $t('login.title') }}
          </h3>
          <el-form-item prop="email">
            <el-input v-model="loginForm.email" name="email" type="text" auto-complete="on" :placeholder="$t('login.email')" />
          </el-form-item>
          <el-form-item prop="password">
            <el-input
              v-model="loginForm.password"
              :type="pwdType"
              name="password"
              auto-complete="on"
              placeholder="password"
              @keyup.enter.native="handleLogin"
            />
          </el-form-item>
          <el-form-item>
            <el-button :loading="loading" type="primary" style="width:100%; margin-top: 15px;" @click.native.prevent="handleLogin">
              {{ $t('login.logIn') }}
            </el-button>
          </el-form-item>
        </el-card>
      </el-form>
    </div>
  </div>
</template>

<script>
import LangSelect from '@/components/LangSelect';
import { validEmail } from '@/utils/validate';
import { csrf } from '@/api/auth';

export default {
  name: 'Login',
  components: { LangSelect },
  data() {
    const validateEmail = (rule, value, callback) => {
      if (!validEmail(value)) {
        callback(new Error('Please enter the correct email'));
      } else {
        callback();
      }
    };
    const validatePass = (rule, value, callback) => {
      if (value.length < 4) {
        callback(new Error('Password cannot be less than 4 digits'));
      } else {
        callback();
      }
    };
    return {
      loginForm: {
        email: 'admin@compass.dev',
        password: 'compass',
      },
      loginRules: {
        email: [{ required: true, trigger: 'blur', validator: validateEmail }],
        password: [{ required: true, trigger: 'blur', validator: validatePass }],
      },
      loading: false,
      pwdType: 'password',
      redirect: undefined,
      otherQuery: {},
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        const query = route.query;
        if (query) {
          this.redirect = query.redirect;
          this.otherQuery = this.getOtherQuery(query);
        }
      },
      immediate: true,
    },
  },
  methods: {
    showPwd() {
      if (this.pwdType === 'password') {
        this.pwdType = '';
      } else {
        this.pwdType = 'password';
      }
    },
    handleLogin() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true;
          csrf().then(() => {
            this.$store.dispatch('user/login', this.loginForm)
              .then(() => {
                this.$router.push({ path: this.redirect || '/', query: this.otherQuery }, onAbort => {});
                this.loading = false;
              })
              .catch(() => {
                this.loading = false;
              });
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    getOtherQuery(query) {
      return Object.keys(query).reduce((acc, cur) => {
        if (cur !== 'redirect') {
          acc[cur] = query[cur];
        }
        return acc;
      }, {});
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
@import '../../styles/element-variables';
.login-wrapper {
  position: absolute;
  display: flex;
  align-items: center;
  // text-align: center;
  overflow-y: hidden;
  height: 100vh;
  width: 100%;
  // background: linear-gradient(lighten(#1539AE, 10%), #1539AE );
  left: 0;
  right: 0;
  .logo-container{
    position: relative;
    flex: 1;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background-image: url('/images/login.bg.jpg');
    background-repeat: no-repeat no-repeat;
    background-size: cover;
    background-position: center center;
    &::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background: transparentize($--color-primary, .1);
    }
    .content {
      flex: 1;
      display: flex;
      z-index: 999;
      align-items: center;
      text-align: center;
      justify-content: center;
      h3{
        text-transform: uppercase;
        color: #ffffff;
        margin-top: 30px !important;
        margin-bottom: 10px !important;
      }
      p{
        margin: 5px;
        color: rgba(#ffffff, .8);
        font-size: 13px;
      }
    }
    .footer{
      z-index: 999;
      width: 100%;
      height: 30px;
      text-align: center;
      font-size: 12px;
      a{
        color: rgba(#ffffff, .6);
      }
    }
  }
  .login-container{
    position: relative;
    display: flex;
    align-items: center;
    text-align: center;
    justify-items: center;
    width: 512px;
    height: 100%;
    // background: #efefef;
    .login-form {
      width: 400px;
      padding: 35px 35px 15px 35px;
      margin: 0 auto;
    }
  }
  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;
    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }
  .title {
    font-size: 16px;
    font-weight: 300;
    color: #555;
    margin: 0px auto 30px auto;
    text-align: center;
    font-weight: bold;
  }
  .set-language {
    color: rgba(#ffffff, .8);
    position: absolute;
    top: 30px;
    right: 30px;
  }
  .logo{
    width: 60px;
  }
}

@media (max-width: 768px) {
  .login-wrapper {
    flex-direction: column;
    .logo-container{
      width: 100% !important;
      height: 50% !important;
      flex: none;
      .center{
        margin-top: 80px;
      }
    }
    .login-container {
      width: 100% !important;
    }
  }
}
</style>
