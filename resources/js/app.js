/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require("bootstrap-css-only/css/bootstrap.min.css");
require("mdbvue/lib/css/mdb.min.css");
require("@fortawesome/fontawesome-free/css/all.min.css");
require("vue-multiselect/dist/vue-multiselect.min.css");

import Vue from 'vue';
import store from 'js/store.js';

import { AppComponents } from 'js/components.js'; 


const app = new Vue({
    el: '#app',
    store,
    components: AppComponents
});
