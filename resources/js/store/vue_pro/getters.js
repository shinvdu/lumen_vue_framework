'use strict';
export const getters = {
  acpro: state => {
    return state;
  },
  userInfo: state => {
    return state.user_info;
  },
  getPointsStore: state => {
    return store_id => {
      if (typeof (state.points_stores[store_id]) === 'undefined') {
        return false;
      } else {
        return state.points_stores[store_id];
      }
    };
  }
};
