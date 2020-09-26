<template>
    <div class="user-settings">
        <h1>Настройки</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                   aria-controls="profile" aria-selected="true">
                    Профиль
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="channels-tab" data-toggle="tab" href="#channels" role="tab"
                   aria-controls="channels" aria-selected="false">
                    Каналы
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="settings-main">
                    <div v-if="user.username"><span>Имя: </span><span>{{ user.username }}</span></div>
                    <div v-if="user.email"><span>Email: </span><span>{{ user.email }}</span></div>
                </div>

                <form v-on:submit="updateSettings" :class="{ 'form-group--error': $v.user.$anyError }">
                    <div>
                        <label>
                            <span>E-mail:</span>
                            <input v-model="user.newEmail" :class="{ 'error': $v.user.newEmail.$error }">
                        </label>

                        <p class="form-group--error-text" v-if="!$v.user.newEmail.required">
                            Поле не может быть пустым
                        </p>
                        <p class="form-group--error-text" v-if="!$v.user.newEmail.email">
                            Некорректный email
                        </p>
                    </div>
                    <div>
                        <label>
                            <span>Пароль:</span>
                            <input type="password" v-model="user.password"
                                   :class="{ 'error': $v.user.password.$error }">
                        </label>

                        <p class="form-group--error-text" v-if="!$v.user.password.required">
                            Поле не может быть пустым
                        </p>
                        <p class="form-group--error-text" v-if="!$v.user.password.minLength">
                            Минимальная длина: {{ $v.user.password.$params.minLength.min }} символов
                        </p>
                    </div>
                    <div>
                        <label>
                            <span>Повторите пароль:</span>
                            <input type="password" v-model="user.repeatPassword"
                                   :class="{ 'error': $v.user.repeatPassword.$error }">
                        </label>

                        <p class="form-group--error-text" v-if="!$v.user.repeatPassword.minLength">
                            Минимальная длина: {{ $v.user.repeatPassword.$params.minLength.min }} символов
                        </p>
                        <p class="form-group--error-text" v-if="!$v.user.repeatPassword.sameAsPassword">
                            Пароль не совпадает!
                        </p>
                    </div>

                    <button>Отправить</button>

                    <p class="form-group--success-text" v-if="response.result">
                        {{ response.message }}
                    </p>
                </form>
            </div>

            <div class="tab-pane fade" id="channels" role="tabpanel" aria-labelledby="channels-tab">
                Каналы:

                <div class="channels-list" v-if="user.channels"  v-for="channel in user.channels" :key="channel.name">
                    {{ channel.name }}
                    <br>
                    <label>
                        Активен:
                        <input type="checkbox"
                               v-model="channel.checkboxActive"
                               @change="updateChannel(channel.name, 'is_active', channel.checkboxActive)">
                    </label>
                    <br>
                    <label>
                        Наблюдается:
                        <input type="checkbox"
                               v-model="channel.checkboxWatching"
                               @change="updateChannel(channel.name, 'is_watching', channel.checkboxWatching)">
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {required, sameAs, minLength, email} from 'vuelidate/lib/validators';
import axios from "axios";

export default {
    name: "Settings",
    data() {
        return {
            user: {
                email: '',
                newEmail: '',
                password: '',
                repeatPassword: '',
                username: '',
                avatar: {
                    link: '',
                },
                channels: {},
                checkboxActive: '',
                checkboxWatching: '',
            },
            response: {
                result: '',
                message: '',
            },
            error: '',
        }
    },
    validations: {
        user: {
            newEmail: {
                required,
                email
            },
            password: {
                required,
                minLength: minLength(8)
            },
            repeatPassword: {
                sameAsPassword: sameAs('password'),
                minLength: minLength(8)
            }
        }
    },
    mounted() {
        this.getSettings();
        this.getChannelsList();
    },
    methods: {
        getSettings: function () {
            if (undefined !== this.$store.state.user) {
                this.user.username = this.$store.state.user.username;
                this.user.email = this.$store.state.user.email || 'Email не задан';
            }
        },
        updateSettings: function (e) {
            e.preventDefault();

            //при наличии ошибок ничего не делать
            this.$v.user.$touch();
            if (this.$v.user.$anyError) {
                return;
            }

            const formData = new FormData();
            formData.set('email', this.user.newEmail);
            formData.set('password', this.user.password);

            axios({
                method: 'post',
                url: '/api/user/update_settings',
                data: formData
            })
                .then((response) => {
                    let data = response['data'];

                    // console.log('data', data);

                    if (data) {
                        this.response.result = data['result'];
                        this.response.message = data['message'];

                        if ('success' === data['result']) {
                            this.getSettings();
                        }
                    } else {
                        this.clearData();
                    }
                })
                .catch((error) => {
                    console.log('catch error', error);

                    this.error = error;

                    this.clearData();
                });

        },
        getChannelsList: function () {
            if (undefined !== this.$store.state.user) {
                this.user.channels = this.$store.state.user.channels;

                if (this.user.channels.length) {
                    for (let i = 0, len = this.user.channels.length; i < len; i++) {
                        this.user.channels[i]['checkboxActive'] = this.user.channels[i]['is_active'];
                        this.user.channels[i]['checkboxWatching'] = this.user.channels[i]['is_watching'];
                    }
                }
            }
        },
        updateChannel: function (channel, type, checkboxVal) {
            const bodyFormData = new FormData();
            bodyFormData.set('channel_permalink', channel);
            bodyFormData.set('type', type);
            bodyFormData.set('new_val', checkboxVal);

            axios({
                method: 'post',
                url: '/api/channel/update_settings',
                data: bodyFormData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
                .then((response) => {
                    let data = response['data'];

                    // console.log('updateChannel data', data);

                    if (
                        data &&
                        'success' === data['result']
                    ) {
                        let params = [];
                        params['channel'] = channel;
                        params['type'] = type;
                        params['new_val'] = data['data'][type];

                        this.$store.commit(
                            'updateChannel',
                            params
                        );
                    }
                })
                .catch((error) => {
                    console.error('catch error: ', error);
                });
        },
        clearData: function () {
            this.user = {
                email: '',
                password: '',
                repeatPassword: '',
            };
            this.response = {
                result: '',
                message: ''
            };
            this.error = '';
        }
    }

}
</script>
