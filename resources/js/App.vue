<template>
  <div>
    <we-loading :disp="loading_disp"></we-loading>
    <keep-alive>
      <router-view></router-view>
    </keep-alive>
  </div>
</template>
<style>
  *:not(input,textarea) {
    -webkit-touch-callout:none;
    -webkit-user-select:none;
    -khtml-user-select:none;
    -moz-user-select:none;
    -ms-user-select:none;
    user-select:none;
    -webkit-tap-highlight-color: transparent;
  }
</style>
<script type="es6">
  import {Utils} from './store/util'
  import {mapGetters} from "vuex"
  import Vue from 'vue';

  export default {
    data (){
      return {
        loading_disp: false,
        backUrl: '',
        push_disable: false
      }
    },
    created() {
      this.$store.commit('SET_WX_FUNS', {
        scan_qr: this.scan_qr,
        choose_img: this.choose_img,
        upload_img: this.upload_img,
        get_token: this.get_token,
        set_token: this.set_token,
        logout: this.logout,
        refresh_session: this.refresh_session
      });

      Vue.directive('loading', {
        update: (el, binding) => {
          this.loading_disp = binding.value;
        }
      });
      // this.checkIsLogin();
    },
    methods: {
      checkIsLogin() {
        let token = this.$store.state.acpro.wx_functions.get_token();
        // 从存储里加载会话到vue全局的实例变量
        if (token) {
          this.$store.commit('SET_BEARER', {token: token});
          let cm_instance_state = localStorage.getItem('cm_instance_state');
          if (cm_instance_state) {
            // 如果localstorage有数据标识意外刷新恢复到之前状态
            let revert_payload = JSON.parse(cm_instance_state);
            if (revert_payload) {
              this.$store.commit('REVERT', revert_payload);
            } else {
              localStorage.removeItem('cm_instance_state');
              this.$router.push('/home');
            }
          } else {
            this.$router.push('/home');
          }
        }
        else {
          window.location.href = '/oauth/redir';
        }
      },
      checkRouter(newval, oldval) {
        //return;
        let old_path = oldval.path;
        let new_path = newval.path;
        let token = this.$store.state.acpro.bearer.token;
        // 如果没有登录阻止路由跳转.
        if (!token && (newval.path != '/login') || newval.path != '/home') {
          this.$router.push('/login');
          return false;
        }
      },
      scan_qr() {
        return new Promise((resolve, reject) => {
          wx.scanQRCode({
            needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
            scanType: ["qrCode", "barCode"], // 可以指定扫二维码还是一维码，默认二者都有
            success: (res) => {
              resolve(res.resultStr);
            }
          });
        });
      },
      choose_img() {
        return new Promise((resolve, reject) => {
          wx.chooseImage({
            count: 1, // 默认9
            sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['camera'], // 可以指定来源是相册还是相机，默认二者都有 // todo debug开启了相册上传
            success: (res) => {
              resolve(res.localIds[0]);
            }
          });
        })
      },
      upload_img(local_id) {
        return new Promise((resolve, reject) => {
          wx.uploadImage({
            localId: local_id, // 需要上传的图片的本地ID，由chooseImage接口获得
            isShowProgressTips: 1, // 默认为1，显示进度提示
            success: (res) => {
              resolve(res.serverId);
            }
          });
        });
      },
      get_token() {
          return localStorage.getItem('token');
      },
      set_token(token) {
        this.$store.commit('SET_BEARER', {token: token});
        localStorage.setItem('token', token);
      },
      refresh_session() {
        this.$store.commit('SET_BEARER', {token: ''});
        localStorage.removeItem('token');
        window.location.href = '/oauth/redir';
      },
      logout() {
        this.$store.commit('SET_BEARER', {token: ''});
        localStorage.removeItem('token');
        window.location.href = '/';
        // window.location.reload(true);
      }
    },
    // watch: {
    //   '$route': 'checkRouter'
    // }
  }
</script>
