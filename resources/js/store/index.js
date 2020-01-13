import Vue from 'vue'
import Vuex from 'vuex'
import acpro from './vue_pro/index'

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    acpro
  }
})
