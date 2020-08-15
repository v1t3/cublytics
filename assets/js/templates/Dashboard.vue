<template>
    <div>
        <div class="header">
            <div class="logo">
            </div>
            <div class="top-menu">
                <div class="top-left links">
                    <a href="/" title="">Главная</a>
                    <a href="/about" title="">Где я?</a>
                </div>
                <div class="top-right links">
                    <router-link :to="{ name: 'home' }">Кабинет</router-link>
                    <a href="/logout" title="Выйти">Выйти</a>
                </div>
            </div>
        </div>
        <div class="wrapper">
            <div class="left-menu-block">
                <ul>
                    <li>
                        <router-link :to="{ name: 'home' }">Главная</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Statistic' }">Статистика</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Coubdata' }">Данные</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'Settings' }">Настройки</router-link>
                    </li>
                </ul>
            </div>
            <div class="main-block">
                <div class="container">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Dashboard"
    }
</script>
