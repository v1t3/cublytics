<template>
    <div>
        <h1>Данные коубера</h1>
        <user-info/>
    </div>
</template>

<script>
    import UserInfo from "../components/user-info";

    export default {
        name: "UserData",
        components: {
            UserInfo,
        }
    }
</script>

<style scoped>

</style>
