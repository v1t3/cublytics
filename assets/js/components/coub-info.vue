<template>
    <div class="component" id="coub-info">
        <div class="form form-coub_info">
            <h3>Данные коуба</h3>
            <hr/>
            <form v-on:submit="getViews">
                <div class="form-group" :class="{ 'form-group--error': $v.url.$error }">
                    <label>
                        <span>ID коуба</span>
                        <input v-model="url" :class="{ 'error': $v.url.$error }">
                    </label>
                    <button>Отправить</button>

                    <p class="form-group--error-text" v-if="!$v.url.required">Поле не может быть пустым</p>
                    <p class="form-group--error-text" v-if="!$v.url.minLength">
                        Минимальная длина: {{ $v.url.$params.minLength.min }} символа
                    </p>
                </div>
            </form>
        </div>
        <div class="views">
            <img v-if="coub.image" class="coub-image" :src="coub.image" alt="">
            <p v-if="coub.name">Название: {{ coub.name }} </p>
            <p v-if="coub.views">Просмотров: {{ coub.views }}</p>
            <p v-if="coub.likes">Лайков: {{ coub.likes }}</p>
            <p v-if="coub.reposts">Репостов: {{ coub.reposts }}</p>
            <p v-if="coub.recoubs">Рекубов: {{ coub.recoubs }}</p>
            <p v-if="coub.banned">Забанен: {{ coub.banned }}</p>
            <p v-if="coub.nsfw">NSFW: {{ coub.nsfw }} {{ coub.nsfw18 }}</p>
            <p v-if="coub.cotd">Коуб дня: {{ coub.cotd }} ({{ coub.cotd_date }})</p>
            <p v-if="coub.favourite">Любимое: {{ coub.favourite }}</p>
            <p v-if="coub.featured">Фич: {{ coub.featured }}</p>
            <p v-if="couber.name">Создатель: <a :href="couber.link" target="_blank">{{ couber.name }}</a></p>
        </div>
        {{ error }}
    </div>
</template>

<script>
    import {required, minLength} from 'vuelidate/lib/validators';
    import axios from "axios";

    export default {
        name: "show-info",
        data() {
            return {
                url: '',
                coub: {
                    image: null,
                    name: null,
                    views: null,
                    likes: null,
                    reposts: null,
                    recoubs: null,
                    banned: null,
                    nsfw: null,
                    nsfw18: null,
                    cotd: null,
                    cotd_date: null,
                    favourite: null,
                    featured: null,
                },
                couber: {
                    name: null,
                    link: null
                },
                error: null
            };
        },
        validations: {
            url: {
                required,
                minLength: minLength(3)
            }
        },
        methods: {
            getViews: function (e) {
                e.preventDefault();

                this.$v.url.$touch();
                if (this.$v.url.$error) return;

                const bodyFormData = new FormData();

                bodyFormData.set('params', JSON.stringify({
                    url: this.url,
                    type: 'coubdata'
                }));

                axios({
                    method: 'post',
                    url: '/api/coub/getdata',
                    data: bodyFormData
                })
                    .then((response) => {
                        let data = response['data'];

                        // let data = {
                        //     flag: null,
                        //     abuses: null,
                        //     recoubs_by_users_channels: null,
                        //     favourite: false,
                        //     promoted_id: null,
                        //     recoub: null,
                        //     like: null,
                        //     dislike: null,
                        //     reaction: null,
                        //     in_my_best2015: false,
                        //     id: 1111,
                        //     type: "Coub::Simple",
                        //     permalink: "18dkqd",
                        //     title: "Mini Goodwood",
                        //     visibility_type: "public",
                        //     original_visibility_type: "public",
                        //     channel_id: 1178729,
                        //     created_at: "2011-12-21T09:43:31Z",
                        //     updated_at: "2019-07-09T10:15:56Z",
                        //     is_done: true,
                        //     views_count: 306,
                        //     cotd: null,
                        //     cotd_at: null,
                        //     visible_on_explore_root: false,
                        //     visible_on_explore: false,
                        //     featured: false,
                        //     published: true,
                        //     published_at: "2011-12-21T09:43:31Z",
                        //     reversed: false,
                        //     from_editor_v2: true,
                        //     is_editable: true,
                        //     original_sound: true,
                        //     has_sound: true,
                        //     recoub_to: null,
                        //     file_versions: {
                        //         html5: {
                        //             video: {
                        //                 high: {
                        //                     url: "https://coubsecure-s.akamaihd.net/get/b36/p/coub/simple/cw_file/29e1d90a699/d564d1dc974bbfd1fa76e/muted_mp4_big_size_1447774014_muted_big.mp4",
                        //                     size: 252780
                        //                 },
                        //                 med: {
                        //                     url: "https://coubsecure-s.akamaihd.net/get/b36/p/coub/simple/cw_file/29e1d90a699/d564d1dc974bbfd1fa76e/muted_mp4_med_size_1447774014_muted_med.mp4",
                        //                     size: 252780
                        //                 }
                        //             },
                        //             audio: {
                        //                 high: {
                        //                     url: "https://coubsecure-s.akamaihd.net/get/b9/p/coub/simple/cw_looped_audio/d129eb7185a/1c2cb8a0cc00a612783ce/high_1447774032_high.mp3",
                        //                     size: 1314689
                        //                 },
                        //                 med: {
                        //                     url: "https://coubsecure-s.akamaihd.net/get/b9/p/coub/simple/cw_looped_audio/d129eb7185a/1c2cb8a0cc00a612783ce/med_1447774032_med.mp3",
                        //                     size: 876459
                        //                 },
                        //                 sample_duration: 6.87
                        //             }
                        //         },
                        //         mobile: {
                        //             video: "https://coubsecure-s.akamaihd.net/get/b36/p/coub/simple/cw_file/29e1d90a699/d564d1dc974bbfd1fa76e/muted_mp4_med_size_1447774014_muted_med.mp4",
                        //             audio: ["https://coubsecure-s.akamaihd.net/get/b9/p/coub/simple/cw_looped_audio/d129eb7185a/1c2cb8a0cc00a612783ce/med_m4a_1447774032_med.m4a", "https://coubsecure-s.akamaihd.net/get/b9/p/coub/simple/cw_looped_audio/d129eb7185a/1c2cb8a0cc00a612783ce/med_1447774032_med.mp3"]
                        //         },
                        //         share: {default: null}
                        //     },
                        //     audio_versions: {},
                        //     image_versions: {
                        //         template: "https://coubsecure-s.akamaihd.net/get/b56/p/coub/simple/cw_image/6bd4a75c84a/05e2bdc8494f291d71f5d/%{version}_1409076516_1381508382_att-migration20121219-1328-io317h.jpg",
                        //         versions: ["micro", "tiny", "age_restricted", "ios_large", "ios_mosaic", "big", "med", "small", "pinterest"]
                        //     },
                        //     first_frame_versions: {
                        //         template: "https://coubsecure-s.akamaihd.net/get/b45/p/coub/simple/cw_timeline_pic/6bd4a75c84a/05e2bdc8494f291d71f5d/%{version}_1409081749_1382451793_att-migration20121219-1328-1nqrrf6.jpg",
                        //         versions: ["big", "med", "small", "ios_large"]
                        //     },
                        //     dimensions: {big: [640, 360], med: [640, 360]},
                        //     site_w_h: [640, 360],
                        //     page_w_h: [640, 360],
                        //     site_w_h_small: [310, 174],
                        //     size: [640, 360],
                        //     age_restricted: false,
                        //     age_restricted_by_admin: false,
                        //     not_safe_for_work: null,
                        //     allow_reuse: false,
                        //     dont_crop: false,
                        //     banned: false,
                        //     global_safe: true,
                        //     audio_file_url: null,
                        //     external_download: {
                        //         type: "Vimeo",
                        //         service_name: "Vimeo",
                        //         url: "http://vimeo.com/23969793",
                        //         has_embed: true
                        //     },
                        //     application: null,
                        //     channel: {
                        //         id: 1178729,
                        //         permalink: "why.did.i.coub.it",
                        //         title: "Why did I coub it?",
                        //         description: "Bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla",
                        //         followers_count: 341,
                        //         following_count: 22,
                        //         avatar_versions: {
                        //             template: "https://coubsecure-s.akamaihd.net/get/b124/p/channel/cw_avatar/e7b9b9776dd/0969bd393a1b89d8f48c5/%{version}_1474572572_oscar_statue2.png",
                        //             versions: ["medium", "medium_2x", "profile_pic", "profile_pic_new", "profile_pic_new_2x", "tiny", "tiny_2x", "small", "small_2x", "ios_large", "ios_small"]
                        //         },
                        //         background_image: "https://coubsecure-s.akamaihd.net/get/b70/p/background/cw_banner_image/05cdd8c6cdb/d8873c3431912c41265c8/big_channel_1471637013_1406945358_00019.jpg",
                        //         coubs_count: 159,
                        //         recoubs_count: 39
                        //     },
                        //     file: null,
                        //     picture: "https://coubsecure-s.akamaihd.net/get/b56/p/coub/simple/cw_image/6bd4a75c84a/05e2bdc8494f291d71f5d/med_1409076516_1381508382_att-migration20121219-1328-io317h.jpg",
                        //     timeline_picture: "https://coubsecure-s.akamaihd.net/get/b45/p/coub/simple/cw_timeline_pic/6bd4a75c84a/05e2bdc8494f291d71f5d/ios_large_1409081749_1382451793_att-migration20121219-1328-1nqrrf6.jpg",
                        //     small_picture: "https://coubsecure-s.akamaihd.net/get/b56/p/coub/simple/cw_image/6bd4a75c84a/05e2bdc8494f291d71f5d/ios_mosaic_1409076516_1381508382_att-migration20121219-1328-io317h.jpg",
                        //     sharing_picture: null,
                        //     percent_done: 100,
                        //     tags: [{id: 10170, title: "after effects", value: "after%20effects"}, {
                        //         id: 35538,
                        //         title: "particular",
                        //         value: "particular"
                        //     }, {id: 405675, title: "langnickel", value: "langnickel"}, {
                        //         id: 64337,
                        //         title: "sebastian",
                        //         value: "sebastian"
                        //     }, {id: 43335, title: "trapcode", value: "trapcode"}, {
                        //         id: 1173,
                        //         title: "car",
                        //         value: "car"
                        //     }, {id: 12789, title: "mini", value: "mini"}, {
                        //         id: 386351,
                        //         title: "mini goodwood",
                        //         value: "mini%20goodwood"
                        //     }],
                        //     categories: [{
                        //         id: 2,
                        //         title: "Art & Design",
                        //         permalink: "art",
                        //         subscriptions_count: 6414142,
                        //         big_image_url: "https://coubsecure-s.akamaihd.net/get/b191/p/category/cw_image/bc49636b5e9/9d819c4215f13ccf07a7b/big_1544749302_art2.png",
                        //         small_image_url: "https://coubsecure-s.akamaihd.net/get/b191/p/category/cw_image/bc49636b5e9/9d819c4215f13ccf07a7b/small_1544749302_art2.png",
                        //         med_image_url: "https://coubsecure-s.akamaihd.net/get/b191/p/category/cw_image/bc49636b5e9/9d819c4215f13ccf07a7b/med_1544749302_art2.png",
                        //         visible: true
                        //     }],
                        //     communities: [{
                        //         id: 2,
                        //         title: "Art & Design",
                        //         permalink: "art",
                        //         subscriptions_count: 6414142,
                        //         big_image_url: "https://coubsecure-s.akamaihd.net/get/b191/p/category/cw_image/bc49636b5e9/9d819c4215f13ccf07a7b/big_1544749302_art2.png",
                        //         small_image_url: "https://coubsecure-s.akamaihd.net/get/b191/p/category/cw_image/bc49636b5e9/9d819c4215f13ccf07a7b/small_1544749302_art2.png",
                        //         med_image_url: "https://coubsecure-s.akamaihd.net/get/b191/p/category/cw_image/bc49636b5e9/9d819c4215f13ccf07a7b/med_1544749302_art2.png",
                        //         i_subscribed: true,
                        //         community_notifications_enabled: null,
                        //         description: null
                        //     }],
                        //     recoubs_count: 0,
                        //     remixes_count: 0,
                        //     likes_count: 2,
                        //     dislikes_count: 0,
                        //     raw_video_id: 806,
                        //     uploaded_by_ios_app: false,
                        //     uploaded_by_android_app: false,
                        //     media_blocks: {
                        //         uploaded_raw_videos: [],
                        //         external_raw_videos: [{
                        //             id: 10925713,
                        //             title: "Mini Goodwood",
                        //             url: "http://vimeo.com/23969793",
                        //             image: "https://coubsecure-s.akamaihd.net/get/b105/p/media_block/cw_image/90df4589710/5f7e942e3a8297c710e42/video_1488374100_1407444539_1395230988_19wt4mg_att-url-download.jpg",
                        //             image_retina: "https://coubsecure-s.akamaihd.net/get/b105/p/media_block/cw_image/90df4589710/5f7e942e3a8297c710e42/video_retina_1488374100_1407444539_1395230988_19wt4mg_att-url-download.jpg",
                        //             meta: {service: "Vimeo", duration: "81.76"},
                        //             duration: 81.76,
                        //             raw_video_id: 806,
                        //             has_embed: true
                        //         }],
                        //         remixed_from_coubs: [],
                        //         external_video: {
                        //             id: 10925713,
                        //             title: "Mini Goodwood",
                        //             url: "http://vimeo.com/23969793",
                        //             image: "https://coubsecure-s.akamaihd.net/get/b105/p/media_block/cw_image/90df4589710/5f7e942e3a8297c710e42/video_1488374100_1407444539_1395230988_19wt4mg_att-url-download.jpg",
                        //             image_retina: "https://coubsecure-s.akamaihd.net/get/b105/p/media_block/cw_image/90df4589710/5f7e942e3a8297c710e42/video_retina_1488374100_1407444539_1395230988_19wt4mg_att-url-download.jpg",
                        //             meta: {service: "Vimeo", duration: "81.76"},
                        //             duration: 81.76,
                        //             raw_video_id: 806,
                        //             has_embed: true
                        //         }
                        //     },
                        //     raw_video_thumbnail_url: "https://coubsecure-s.akamaihd.net/get/b54/p/raw_video/cw_image/71cecd0424c/85e09c6eb81bf57c9a94e/coub_media_1408523655_1385103573_att-url-download20131122-18197-s2wsqy.jpg",
                        //     raw_video_title: null,
                        //     video_block_banned: false,
                        //     duration: 6.84,
                        //     promo_winner: false,
                        //     promo_winner_recoubers: null,
                        //     editorial_info: {},
                        //     promo_hint: null,
                        //     beeline_best_2014: null,
                        //     from_web_editor: true,
                        //     normalize_sound: true,
                        //     normalize_change_allowed: true,
                        //     best2015_addable: false,
                        //     ahmad_promo: null,
                        //     promo_data: null,
                        //     audio_copyright_claim: null,
                        //     ads_disabled: null,
                        //     is_safe_for_ads: true,
                        //     megafon_contents: []
                        // };

                        this.error = '';

                        console.log('data', data);
                        if (data) {
                            this.coub = {
                                image: data['picture'] ? data['picture'] : '',
                                name: data['title'],
                                views: data['views_count'],
                                likes: data['likes_count'],
                                reposts: data['recoubs_count'],
                                recoubs: data['remixes_count'],
                                banned: data['banned'] ? 'Да' : '',
                                nsfw: data['not_safe_for_work'] ? 'Да' : '',
                                nsfw18: (data['age_restricted'] || data['age_restricted_by_admin']) ? '18+' : '',
                                cotd: data['cotd'] ? 'Да' : '',
                                cotd_date: data['cotd_at'],
                                favourite: data['favourite'] ? 'Да' : '',
                                featured: data['featured'] ? 'Да' : '',
                            };

                            if (data['channel']) {
                                this.couber.link = 'https://coub.com/' + data['channel']['permalink'];

                                this.couber.name = data['channel']['title'];
                            }

                            if (data['error']) {
                                this.error = data['error'];

                                this.clearData();
                            }
                        }

                        if (!data) {
                            this.error = 'Coub not found';

                            this.clearData();
                        }
                    })
                    .catch((error) => {
                        console.log('error', error);

                        if (String(error) === 'Error: Request failed with status code 404') {
                            this.error = 'Не найдено';
                        } else {
                            this.error = error;
                        }

                        this.clearData();
                    });
            },
            clearData: function () {
                this.coub = {
                    image: null,
                    name: null,
                    views: null,
                    likes: null,
                    reposts: null,
                    recoubs: null,
                    banned: null,
                    nsfw: null,
                    nsfw18: null,
                    cotd: null,
                    cotd_date: null,
                    favourite: null,
                    featured: null,
                };

                this.couber = {
                    name: null,
                    link: null
                };
            }
        }
    }
    ;
</script>

<style lang="scss">
</style>
