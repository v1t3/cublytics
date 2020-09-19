<template>
    <div>
        <div class="wrapper">
            <div class="left-menu-block">
                <ul>
                    <li>
                        <router-link :to="{ name: 'home' }">Главная</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Statistic' }">Статистика по каналам</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Coubdata' }">Статиститика по коубам</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Settings' }">Настройки</router-link>
                    </li>
                </ul>
            </div>
            <div class="main-block">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Dashboard",
    data() {
        return {};
    },
    beforeMount() {
        this.getUserData();
    },
    methods: {
        getUserData: function () {
            axios({
                method: 'post',
                url: '/api/user/get_data',
                data: {},
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
                .then((response) => {
                    let data = response['data'];

                    if (
                        data &&
                        'success' === data['result']
                    ) {
                        this.$store.commit('setUserData', data['data']);
                    }

                    // console.log('user', this.$store.state.user);
                })
                .catch((error) => {
                    console.error('catch error: ', error);
                });
        },
    }

}
</script>
