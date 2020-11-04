<template>
    <div>
        <div class="wrapper">
            <menu_mobile/>
            <left_menu/>
            <div class="main-block">
                <router-view v-if="show"></router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import Left_menu from "../components/left_menu";
    import Menu_mobile from "../components/menu_mobile";

    export default {
        name: "Dashboard",
        components: {
            Menu_mobile,
            Left_menu
        },
        data() {
            return {
                show: false
            };
        },
        beforeMount() {
            this.getUserData();
        },
        methods: {
            getUserData: function () {
                this.show = false;

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
                            this.$store.commit('setStatisticType', 'month1');
                            this.show = true;
                        }
                    })
                    .catch((error) => {
                        console.error('catch error: ', error);
                    });
            },
        }

    }
</script>
