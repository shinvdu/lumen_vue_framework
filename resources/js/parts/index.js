import we_dialog from './we_dialog.vue'
import we_loading from './loading.vue'

export default {
  install (Vue) {
    'use strict';
    Vue.component('we-dialog', we_dialog);
    Vue.component('we-loading', we_loading);
  }
}
