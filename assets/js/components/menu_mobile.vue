<template>
    <div class="menu-mobile-block">
        <nav role="navigation" v-on-clickaway="closeMenu">
            <div id="menuToggle">
                <div class="burger" @click.prevent="showMenu" v-bind:class="{ active: isActive }">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </div>
                <ul id="menu">
                    <li><a href="/">Главная</a></li>
                    <hr/>
                    <li>
                        <router-link :to="{ name: 'home' }"><span @click="closeMenu">Сводка</span></router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Channel_data' }"><span @click="closeMenu">Каналы</span></router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Coubdata' }"><span @click="closeMenu">Коубы</span></router-link>
                    </li>
                    <hr/>
                    <li>
                        <router-link :to="{ name: 'Settings' }"><span @click="closeMenu">Настройки</span></router-link>
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