<template>
    <div class="menu-mobile-block">
        <nav role="navigation" v-on-clickaway="closeMenu">
            <div id="menuToggle">
                <div class="burger" @click.prevent="showMenu" v-bind:class="{ active: isActive }">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <ul id="menu">
                    <li><a href="/">Главная</a></li>
                    <hr/>
                    <li>
                        <router-link :to="{ name: 'home' }">Сводка</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Channel_data' }">Каналы</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Coubdata' }">Коубы</router-link>
                    </li>
                    <hr/>
                    <li>
                        <router-link :to="{ name: 'Settings' }">Настройки</router-link>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</template>

<script>
    import {mixin as clickaway} from 'vue-clickaway';

    export default {
        name: "menu_mobile",
        data() {
            return {
                isActive: false,
            }
        },
        mixins: [clickaway],
        methods: {
            showMenu: function () {
                this.isActive = !this.isActive;
            },

            closeMenu: function () {
                this.isActive = false;
            },
        }
    }
</script>