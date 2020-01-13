import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);

// We are using Webpack code splitting here so that each route's associated
// component code is loaded on-demand only when the route is visited.
// It's actually not really necessary for a small project of this size but
// the goal is to demonstrate how to do it.
//
// Note that the dynamic import syntax should actually be just `import()`
// but buble/acorn doesn't support parsing that syntax until it's stage 4
// so we use the old System.import here instead.
//
// If using Babel, `import()` can be supported via
// babel-plugin-syntax-dynamic-import.

// const List_b = Vue.component('async-webpack-example', function (resolve) {
//     // 这个特殊的 require 语法告诉 webpack
//     // 自动将编译后的代码分割成不同的块，
//     // 这些块将通过 Ajax 请求自动下载。
//     require(['../components/List_b.vue'], resolve)
// })
const home = require('../components/home/home.vue');

// const user = require('../components/home/user_home.vue');
// const consumer = require('../components/home/consumer.vue');

const sms = require('../components/auth/sms.vue');

export default new Router({
  // mode: 'history',
  routes: [
    {
      path: '/login',
      component: sms
    },
    {
      path: '/home',
      component: home,
      name: 'home'
      // name: 'home',
      // children: [
      //   {
      //     path: 'user',
      //     component: user,
      //     name: 'user',
      //     meta: {
      //       back_path: '/home/stores'
      //     },
      //     store_commit: [
      //       {type: 'SET_CONSUMER_INFO', value: {}},
      //     ]
      //   },
      //   {
      //     path: 'consumer',
      //     name: 'consumer',
      //     component: consumer,
      //     meta: {
      //       back_path: '/home/user',
      //       store_commit: [
      //         {type: 'SET_CONSUMER_INFO', value: {}},
      //         {type: 'SET_TITLE_NAME', value: 'lumen framework'},
      //         {type: 'SET_EVENT_DIALOG_POPED', value: false}
      //       ],
      //     },
          // children: [
          //   {
          //     path: 'cart',
          //     component: cart,
          //     name: 'cart',
          //     meta: {
          //       back_path: '/home/cpath?confirm=1',
          //     }
          //   }
          // ]
      //   }
      // ]
    },
    {
      path: '/',
      redirect: '/home'
    }
  ]
})
