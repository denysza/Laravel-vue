import Vue from 'vue';
import Vuex from 'vuex';

import Config from 'js/modules/Config/index.js';
import Property from 'js/modules/Property/index.js';

require('es6-promise/auto');

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    Config,
    Property,
  }
});