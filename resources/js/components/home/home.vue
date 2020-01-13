<template>
  <div id="page">
    <div class="weui-tab">
      <div class="weui-navbar">
        <div class="weui-navbar__item">
          <a class="btn-back" @click.prevent="back()">
            <img src="~../../../images/icon-back.png" alt="" height="22">
          </a>
          {{ titleName }}
        </div>
      </div>

      <keep-alive>
        <router-view v-if="$route.meta.keepAlive"></router-view>
      </keep-alive>
      <router-view v-if="!$route.meta.keepAlive"></router-view>

    </div>
  </div>
</template>
<script>
  import {mapGetters} from 'vuex';
  export default {
    data () {
      return {
        body: null,
        loading: false
      }
    },
    mounted () {
      // this.body = document.getElementsByTagName('body');
      // this.$store.dispatch('getWeChatSign', window.location.href).then((wxconfig) => {
      //   this.$store.commit('SET_WX_CONFIG', wxconfig);
      //   window.wx.config(wxconfig);
      //   window.wx.ready(() => {
      //     // 初始化微信使用的接口
      //     console.log('wx ready');
      //   });
      //   window.wx.error((res) => {
      //     console.log(res);
      //   });
      // });
    },
    computed: {
      ...mapGetters(['titleName', 'acpro'])
    },
    activated () {
      this.$store.commit('SET_TITLE_NAME', 'Lumen Vue Framework');
      this.nav.tab_index = 0;
    },
    methods: {
      active () {
      },
      back () {
        // this.$router.push('/home/consumer');
        history.back();
      },

      checkRouter (newval, oldval) {
        let oldpath = oldval.path;
        if (oldpath === '/home') {
          console.log('home here.')
        }
      }
    },
    watch: {
      '$route': 'checkRouter'
    }
  }
</script>
