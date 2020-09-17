<template>
    <div>
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
                    <div>Аватар</div>
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
                <select v-model="user.channel_active.name" @change="getActive()">
                    <option v-for="channel in user.channels" :key="channel.name">{{ channel.name }}</option>
                </select>
                <div v-if="user.channels">
                    <label>Активен: <input type="checkbox" v-bind:checked="user.channel_active.isActive"></label>
                    <label>Наблюдается: <input type="checkbox" v-bind:checked="user.channel_active.isWatching"></label>
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
                channel_active: {
                    name: '',
                    isActive: false,
                    isWacthing: false,
                },
                channels: {}
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
            //todo Сделать общее получение информации и хранение в сторе

            axios({
                method: 'post',
                url: '/api/user/get_settings',
                data: {}
            })
                .then((response) => {
                    let data = response['data'];

                    console.log('data', data);

                    if (data) {
                        if ('success' === data['result']) {
                            this.user.username = data['data']['username'];
                            this.user.email = data['data']['email'];
                        }
                    } else {
                        console.log('data error', error);
                    }
                })
                .catch((error) => {
                    console.log('catch error', error);

                    this.error = error;
                });

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
                        // this.error = 'User not found';
                        console.log('data error', error);

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
            axios({
                method: 'post',
                url: '/api/stat/get_channels_list',
                data: {}
            })
                .then((response) => {
                    let data = response['data'];

                    // console.log('data', data);

                    if (data) {
                        if ('success' === data['result']) {
                            this.user.channels = data['channels'];

                            this.user.channel_active.name = this.user.channels[0].name;
                            this.user.channel_active.isActive = this.user.channels[0]['is_active'];
                            this.user.channel_active.isWatching = this.user.channels[0]['is_watching'];
                        }
                    }
                })
                .catch((error) => {
                    console.log('catch error', error);

                    this.error = error;
                });

        },
        getActive: function (type = '') {
            this.user.channel_active = this.user.channels
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
