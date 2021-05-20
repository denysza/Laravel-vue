import actions from './actions.js';
import { state, getters, mutations } from './mutations.js';

export default {
  namespaced: true,
  actions,
  getters,
  state,
  mutations
};