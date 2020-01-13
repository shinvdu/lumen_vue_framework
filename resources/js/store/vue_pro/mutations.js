'use strict';
import * as types from './types'

export const mutations = {
  [types.SET_TITLE_NAME] (state, name) {
    state.title_name = name;
  },
  [types.SET_BEARER] (state, payload) {
    state.bearer = payload;
  },
  [types.SET_USERINFO] (state, payload) {
    state.user_info = payload.user_info;
  },
  [types.SET_WX_FUNS] (state, funs) {
    state.wx_functions = funs;
  },
  [types.SET_WX_CONFIG] (state, config) {
    state.wx_config = config;
  },
  [types.REVERT] (state, payload) {
    state.user_info = payload.user_info;
  },
  [types.BACK_PATH] (state, value) {
    state.back_path = value;
  }
  // [types.OFFLINE_SET_COUPON_ERRORMSG] (state, obj) {
  //   obj.coupon.errorMsg = obj.errorMsg;
  //   if (typeof (obj.errorCodes) !== 'undefined') {
  //     obj.coupon.errorCodes = obj.errorCodes;
  //   }
  // },
};
