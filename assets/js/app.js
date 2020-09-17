import Vue from 'vue'
import VueRouter from 'vue-router'
import vuelidate from 'vuelidate'
import App from './views/App'
import Home from './views/Home'
import Statistic from './views/Statistic'
import Settings from './views/Settings'
import Coubdata from './views/Coubdata'
import UserData from './views/UserData'
// import Page404 from './views/Page404'

require('./bootstrap');

Vue.use(VueRouter);
Vue.use(vuelidate);

//css
require('../sass/app.scss');

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/dashboard',
            name: 'home',
            component: Home
        },
        {
            path: '/dashboard/statistic',
            name: 'Statistic',
            component: Statistic
        },
        {
            path: '/dashboard/settings',
            name: 'Settings',
            component: Settings
        },
        {
            path: '/dashboard/сoubdata',
            name: 'Coubdata',
            component: Coubdata
        },
        {
            path: '/dashboard/userdata',
            name: 'UserData',
            component: UserData
        },
        {
            path: '/dashboard/*',
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
});
