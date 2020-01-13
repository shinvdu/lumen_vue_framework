'use strict';

import Vue from 'vue'
import App from './App.vue'
import store from './store'
import router from './router'
import vue_resource from 'vue-resource'
import parts from './parts'
import {Utils} from './store/util'
// import storageIndexedDB from './store/storageIndexedDB'
// import storageWebSQL from './store/storageWebSQL'
// import md5 from './store/md5'
// import uuid from './store/uuid'
import '../sass/app.scss'

Vue.use(vue_resource);
Vue.use(parts);
// Vue.use(storageIndexedDB);
// Vue.use(storageWebSQL);
// Vue.use(md5);
// Vue.use(uuid);

const app = new Vue({
  store,
  router,
  ...App
});


Vue.http.options.timeout = 30000; // 超时设置需要大于env api超时时间

Vue.http.interceptors.push((request, next) => {
  // 如果是登陆时，则使用openid使用认证信息
  if (request.url === '/api/auth/verifysms' || request.url === '/api/auth/sendsms') {
    next((response) => {
      if (response.headers.get('refreshedtoken')) {
        // 如果设置了刷新refreshedtoken则store里面设置新token
        app.$store.state.acpro.bearer.token = response.headers.get('refreshedtoken');
      }
    });
  } else if (app.$store.state.acpro.bearer.token) {
    request.headers.set('Authorization', 'Bearer ' + app.$store.state.acpro.bearer.token);
    next((response) => {
      if (response.headers.get('refreshedtoken')) {
        // 如果设置了刷新refreshedtoken则store里面设置新token
        app.$store.state.acpro.bearer.token = response.headers.get('refreshedtoken');
        console.log('set new token');
        localStorage.setItem('token', response.headers.get('refreshedtoken'));
      }
    });
  } else {
    // return false; 
    next();
  }
});

Vue.prototype.router_push = (path) => {
  // 如果前进时间戳与路由改变时间戳不一致则判断为后退事件
  // 前进都滚到顶
  window.scrollTo(0, 0);
  app.$store.state.acpro.forwarding_timestamp = new Date().getTime();
  app.$router.push(path);
};

export { app, store, router }
