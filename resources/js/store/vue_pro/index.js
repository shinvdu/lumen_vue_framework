import {getters} from './getters'
import {actions} from './actions'
import {mutations} from './mutations'
const state = {
  title: 'lumen vue framework',
  title_name: 'lumen vue framework',
  back_path: 'home',
  user_info: {
  },
  points_stores: {
  },
  bearer: {
    token: null,
    exp: null
  }
};

export default {
  state,
  getters,
  actions,
  mutations
}
