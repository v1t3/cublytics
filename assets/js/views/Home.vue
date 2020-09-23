<template>
    <div>
        <h1>Главная</h1>

        <div v-if="user">
            <h3>{{ user.username }}</h3>
        </div>
        <div class="row">
            <div class="col-md-9 d-flex flex-wrap">
                <div class="channel-home col-md-3" v-for="channel in user.channels">
                    <img :src="channel.avatar.replace('%{version}', 'profile_pic_big')"
                         :alt="channel.title">
                    <a :href="'https://coub.com/' + channel.name"
                       target="_blank"
                       rel="noopener norefferer">
                        {{ channel.title }}
                    </a>
                    <span>followers_count: {{ channel.followers_count }}</span>
                    <span>is_active: {{ channel.is_active }}</span>
                    <span>is_watching: {{ channel.is_watching }}</span>
                    <span>likes_count: {{ channel.likes_count }}</span>
                    <span>recoubs_count: {{ channel.recoubs_count }}</span>
                    <span>stories_count: {{ channel.stories_count }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kd-frame">
                    Тут будет КД
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Home",
        data() {
            return {
                user: {
                    username: '',
                    channels: null,
                },
            }
        },
        mounted() {
            this.showInfo();
        },
        methods: {
            showInfo: function () {
                if (undefined !== this.$store.state.user) {
                    this.user.username = this.$store.state.user.username;
                    this.user.channels = this.$store.state.user.channels;
                }

                // console.log('user2', this.user);
            }
        }
    }
</script>
