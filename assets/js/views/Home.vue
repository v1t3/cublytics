<template>
    <div class="view-container home-view">
        <div class="user-block" v-if="user">
            <p>Привет, {{ user.username }}</p>
        </div>
        <div class="row">
            <div class="home-view_col col-xs-12 col-lg-9">
                <h3>Каналы</h3>
                <hr>
                <div class="d-flex flex-wrap">
                    <div class="channel-home col-md-3"
                         v-for="channel in user.channels"
                         v-bind:class="{ deactivated: !channel.is_active || !channel.is_watching }">
                        <img :src="channel.avatar"
                             :alt="channel.title">
                        <div class="channel-home_title">
                            <a :href="'https://coub.com/' + channel.name"
                               target="_blank"
                               rel="noopener norefferer">
                                {{ channel.title }}
                                <font-awesome-icon icon="external-link-alt"/>
                            </a>
                        </div>
                        <div class="channel-home_counters">
                            <span>Подписчиков: {{ channel.followers_count || 0 }}</span>
                            <span>Лайков: {{ channel.likes_count || 0 }}</span>
                            <span>Репостов: {{ channel.reposts_count || 0 }}</span>
                            <span>Рекоубов: {{ channel.recoubs_count || 0 }}</span>
                            <span>Историй: {{ channel.stories_count || 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="home-view_col col-xs-12 col-lg-3">
                Коуб дня
                <hr>
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

                    console.log('this.user.channels', this.user.channels);
                }
            }
        }
    }
</script>
