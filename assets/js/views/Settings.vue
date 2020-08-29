<template>
    <div>
        <h1>Настройки</h1>

        <div>Аватар</div>

        <div v-if="user.username"><span>Имя: </span><span>{{ user.username }}</span></div>

        <form v-on:submit="updateSettings" :class="{ 'form-group--error': $v.user.$anyError }">
            <div>
                <label>
                    <span>E-mail:</span>
                    <input v-model="user.email" :class="{ 'error': $v.user.email.$error }">
                </label>

                <p class="form-group--error-text" v-if="!$v.user.email.required">
                    Поле не может быть пустым
                </p>
                <p class="form-group--error-text" v-if="!$v.user.email.email">
                    Некорректный email
                </p>
            </div>
            <div>
                <label>
                    <span>Пароль:</span>
                    <input type="password" v-model="user.password" :class="{ 'error': $v.user.password.$error }">
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
                    password: '',
                    repeatPassword: '',
                    username: '',
                    avatar: {
                        link: '',
                    }
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
                email: {
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
            this.getUsername();
        },
        methods: {
            getUsername: function () {
                //todo Сделать общее получение информации и хранение в сторе

                const formData = new FormData();
                formData.set('field', 'username');

                axios({
                    method: 'post',
                    url: '/api/user/get_username',
                    data: formData
                })
                    .then((response) => {
                        let data = response['data'];

                        console.log('data', data);
                        if (data) {
                            if ('success' === data['result']) {
                                this.user.username = data['message'];
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
                formData.set('email', this.user.email);
                formData.set('password', this.user.password);

                axios({
                    method: 'post',
                    url: '/api/user/update_settings',
                    data: formData
                })
                    .then((response) => {
                        let data = response['data'];

                        console.log('data', data);
                        if (data) {
                            this.response.result = data['result'];
                            this.response.message = data['message'];
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
