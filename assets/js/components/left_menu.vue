<template>
    <div class="left-menu-block">
        <ul>
            <li><router-link :to="{ name: 'home' }">Сводка</router-link></li>
            <li><router-link :to="{ name: 'Channel_data' }">Каналы</router-link></li>
            <li><router-link :to="{ name: 'Coubdata' }">Коубы</router-link></li>
            <hr/>
            <li><router-link :to="{ name: 'Settings' }">Настройки</router-link></li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "left_menu"
    }
</script>