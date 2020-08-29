<template>
    <div>
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
                        <router-link :to="{ name: 'Coubdata' }">Данные коуба</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'UserData' }">Данные пользователя</router-link>
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
    export default {
        name: "Dashboard"
    }
</script>
