import axios from 'axios';
import { Api } from "js/route/config.js";

export default {
  async loadSelect({ commit, getters }) {
    if (!getters.loaded_select) {
      await axios.post(Api.select).then((response) => {
        commit('select', response.data);
        commit('loaded_select', true);
      }).catch((e) => {
        throw e;
      });
    }
  },
};