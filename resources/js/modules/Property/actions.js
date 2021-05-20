import axios from 'axios';
import { Api } from "js/route/user.js";

export default {
  async loadUserProperties({ commit }) {
    await axios.get(Api.properties).then(response => {
      commit('properties', response.data);
    }).catch(e => {
      throw e;
    });
  },
};