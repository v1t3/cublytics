import Vue from 'vue'
import VueRouter from 'vue-router'
import vuelidate from 'vuelidate'
import Vuex from 'vuex'
import { library } from '@fortawesome/fontawesome-svg-core'
import {faUserSecret, faExternalLinkAlt} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import App from './views/App'
import Home from './views/Home'
import Channel_data from './views/Channel_data'
import Settings from './views/Settings'
import Coubdata from './views/Coubdata'
// import Page404 from './views/Page404'

require('./bootstrap');

Vue.use(VueRouter);
Vue.use(vuelidate);
Vue.use(Vuex);

library.add(faUserSecret, faExternalLinkAlt);

Vue.component('font-awesome-icon', FontAwesomeIcon);

//css
require('../sass/app.scss');

const store = new Vuex.Store({
    state: {
        user: {},
    },
    mutations: {
        setUserData(state, params) {
            if (params.hasOwnProperty('user_id') && !state.user[params['user_id']]) {
                state.user = params;
            }
        },

        setStatisticType(state, params) {
            if (state.user && params) {
                state.user.statistic_type = params;
            }
        },

        updateChannel(state, params) {
            if (
                params.hasOwnProperty('channel')
                && params.hasOwnProperty('type')
                && params.hasOwnProperty('new_val')
                && state.user.channels
            ) {
                for (let i = 0, len = state.user.channels.length; i < len; i++) {
                    if (params['channel'] === state.user.channels[i]['name']) {
                        state.user.channels[i][params['type']] = params['new_val'];
                    }
                }
            }
        },
    }
});

let index = '/dashboard';

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: index,
            name: 'home',
            component: Home
        },
        {
            path: index + '/channel-statistic',
            name: 'Channel_data',
            component: Channel_data
        },
        {
            path: index + '/сoub-statistic',
            name: 'Coubdata',
            component: Coubdata
        },
        {
            path: index + '/settings',
            name: 'Settings',
            component: Settings
        },
        {
            path: index + '/*',
            redirect: '/dashboard'
        }
    ],
});

const app = new Vue({
    el: '#app',
    components: {
        App
    },
    router,
    vuelidate,
    store: store,
});
