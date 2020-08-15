<template>
    <div class="component" id="user-info">
        <div class="form form-user-info">
            <h3>Данные пользователя</h3>
            <hr/>
            <form v-on:submit="getViews">
                <div class="form-group" :class="{ 'form-group--error': $v.url.$error }">
                    <label>
                        <span>Имя пользователя</span>
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
            <img v-if="couber.image" :src="couber.image" :alt="couber.name">
            <p v-if="couber.name">Имя: {{ couber.name }} </p>
            <p v-if="couber.description">Подпись: {{ couber.description }} </p>
            <p v-if="couber.totalCoubs">Всего коубов: {{ couber.totalCoubs }} </p>
            <p v-if="couber.created">Создан: {{ couber.created }} </p>
            <p v-if="couber.viewsCount">Просмотров: {{ couber.viewsCount }} </p>
            <p v-if="couber.followersCount">Подписчиков: {{ couber.followersCount }} </p>
            <p v-if="couber.name">Страница коубера: <a :href="couber.link" target="_blank">{{ couber.name }}</a></p>
        </div>
        {{ error }}
    </div>
</template>

<script>
    import {required, minLength} from 'vuelidate/lib/validators';
    import axios from "axios";

    export default {
        name: "user-info",
        components: {},
        data() {
            return {
                url: '',
                couber: {
                    image: '',
                    name: '',
                    link: '',
                    description: '',
                    totalCoubs: '',
                    chanelCreated: '',
                    viewsCount: '',
                    followersCount: '',
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
        mounted() {
        },
        methods: {
            getViews: function (e) {
                e.preventDefault();

                this.$v.url.$touch();
                if (this.$v.url.$error) return;

                const bodyFormData = new FormData();
                bodyFormData.set('params', JSON.stringify({
                    url: this.url,
                    type: 'userdata'
                }));

                axios({
                    method: 'post',
                    url: '/api/coub/getdata',
                    data: bodyFormData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then((response) => {
                        let data = response['data'];

                        // let data = {
                        //     simple_coubs_count: 92,
                        //     id: 36320,
                        //     user_id: 56847,
                        //     crown: null,
                        //     permalink: "theruslan",
                        //     title: "The Ruslan",
                        //     description: "Designer ~ Developer ~ Photographer ~ Vegan ~ Biker ~ Traveler",
                        //     contacts: {homepage: "http://theruslan.ru/", tumblr: "", youtube: "", vimeo: ""},
                        //     created_at: "2014-07-10T14:57:41Z",
                        //     updated_at: "2018-10-30T14:54:18Z",
                        //     avatar_versions: {
                        //         template: "https://coubsecure-s.akamaihd.net/get/b85/p/channel/cw_avatar/b35b8f88b8d/65888bd14d1ab4429bdd0/%{version}_1474481743_IMG_7966.jpg",
                        //         versions: ["medium", "medium_2x", "profile_pic", "profile_pic_new", "profile_pic_new_2x", "tiny", "tiny_2x", "small", "small_2x", "ios_large", "ios_small"]
                        //     },
                        //     followers_count: 199,
                        //     following_count: 25,
                        //     recoubs_count: 1,
                        //     likes_count: 2710,
                        //     channel_notifications_enabled: null,
                        //     stories_count: 0,
                        //     authentications: [
                        //         {id: 2011014, channel_id: 36320, provider: "facebook", username_from_provider: "Ruslan Zaynetdinov"},
                        //         {id: 1126469, channel_id: 36320, provider: "google", username_from_provider: "Ruslan Zaynetdinov"},
                        //         {id: 2011020, channel_id: 36320, provider: "twitter", username_from_provider: "@The__Ruslan"},
                        //         {id: 2011021, channel_id: 36320, provider: "vkontakte", username_from_provider: "Руслан Зайнетдинов"}
                        //     ],
                        //     background_coub: { flag: null, abuses: null, recoubs_by_users_channels: null, favourite: false, promoted_id: null, recoub: null, like: null, dislike: null, reaction: null, in_my_best2015: false, id: 6823912, type: "Coub::Simple", permalink: "3m400", title: "Under black star", visibility_type: "public", original_visibility_type: "public", channel_id: 36320, created_at: "2014-10-08T19:33:07Z", updated_at: "2020-05-27T01:27:48Z", is_done: true, views_count: 33836, cotd: null, cotd_at: null, visible_on_explore_root: true, visible_on_explore: true, featured: true, published: true, published_at: "2014-10-08T19:33:07Z", reversed: false, from_editor_v2: true, is_editable: true, original_sound: false, has_sound: false, recoub_to: null, file_versions: { html5: { video: { higher: { url: "https://coubsecure-s.akamaihd.net/get/b135/p/coub/simple/cw_file/5636b00c296/423c76ca3e102ecae8cd6/muted_mp4_huge_size_1567734285_muted_huge.mp4", size: 825672 }, high: { url: "https://coubsecure-s.akamaihd.net/get/b135/p/coub/simple/cw_file/5636b00c296/423c76ca3e102ecae8cd6/muted_mp4_big_size_1567734285_muted_big.mp4", size: 644296 }, med: { url: "https://coubsecure-s.akamaihd.net/get/b135/p/coub/simple/cw_file/5636b00c296/423c76ca3e102ecae8cd6/muted_mp4_med_size_1567734285_muted_med.mp4", size: 111014 } }, audio: { high: { url: "https://coubsecure-s.akamaihd.net/get/b91/p/coub/simple/cw_looped_audio/bffcaa74506/65ab8c3e95b1a1ba336f2/high_1471624233_high.mp3", size: 7221908 }, med: { url: "https://coubsecure-s.akamaihd.net/get/b91/p/coub/simple/cw_looped_audio/bffcaa74506/65ab8c3e95b1a1ba336f2/med_1471624233_med.mp3", size: 5916102 } } }, mobile: { video: "https://coubsecure-s.akamaihd.net/get/b135/p/coub/simple/cw_file/5636b00c296/423c76ca3e102ecae8cd6/muted_mp4_med_size_1567734285_muted_med.mp4", audio: [ "https://coubsecure-s.akamaihd.net/get/b91/p/coub/simple/cw_looped_audio/bffcaa74506/65ab8c3e95b1a1ba336f2/med_1471624233_med.mp3" ] }, share: { default: "https://coubsecure-s.akamaihd.net/get/b52/p/coub/simple/cw_video_for_sharing/62b8c8eb0e8/c0287a25949e15bd1c7ea/1567734295_looped_1567734293.mp4" } }, audio_versions: { template: "https://coubsecure-s.akamaihd.net/get/b70/p/coub/simple/cw_audio/17d6624ae46/b1257f67f497510b06bd9/mid_1471624161_out.mp3", versions: [ "mid", "low" ], chunks: { template: "https://coubsecure-s.akamaihd.net/get/b70/p/coub/simple/cw_audio/17d6624ae46/b1257f67f497510b06bd9/mp3_%{version}_c%{chunk}_1471624161_out.mp3", versions: [ "mid", "low" ], chunks: [ 1, 2, 3, 4 ] } }, image_versions: { template: "https://coubsecure-s.akamaihd.net/get/b63/p/coub/simple/cw_image/55712e4dc1f/82a43b898a7d81e7fee3d/%{version}_1471624143_00032.jpg", versions: [ "micro", "tiny", "age_restricted", "ios_large", "ios_mosaic", "big", "med", "small", "pinterest" ] }, first_frame_versions: { template: "https://coubsecure-s.akamaihd.net/get/b91/p/coub/simple/cw_timeline_pic/cbdc9e8fe9b/86874147b17189bcf1c8e/%{version}_1471624157_image.jpg", versions: [ "big", "med", "small", "ios_large" ] }, dimensions: { big: [ 1280, 576 ], med: [ 640, 288 ] }, site_w_h: [ 640, 288 ], page_w_h: [ 640, 288 ], site_w_h_small: [ 310, 140 ], size: [ 1280, 576 ], age_restricted: false, age_restricted_by_admin: false, not_safe_for_work: null, allow_reuse: false, dont_crop: false, banned: false, global_safe: true, audio_file_url: "https://coubsecure-s.akamaihd.net/get/b70/p/coub/simple/cw_audio/17d6624ae46/b1257f67f497510b06bd9/low_1471624161_out.mp3", external_download: { type: "Vimeo", service_name: "Vimeo", url: "http://vimeo.com/97647244", has_embed: true }, application: null, channel: { id: 36320, permalink: "theruslan", title: "The Ruslan", description: "Designer ~ Developer ~ Photographer ~ Vegan ~ Biker ~ Traveler", followers_count: 199, following_count: 25, avatar_versions: { template: "https://coubsecure-s.akamaihd.net/get/b85/p/channel/cw_avatar/b35b8f88b8d/65888bd14d1ab4429bdd0/%{version}_1474481743_IMG_7966.jpg", versions: [ "medium", "medium_2x", "profile_pic", "profile_pic_new", "profile_pic_new_2x", "tiny", "tiny_2x", "small", "small_2x", "ios_large", "ios_small" ] } }, file: null, picture: "https://coubsecure-s.akamaihd.net/get/b63/p/coub/simple/cw_image/55712e4dc1f/82a43b898a7d81e7fee3d/med_1471624143_00032.jpg", timeline_picture: "https://coubsecure-s.akamaihd.net/get/b91/p/coub/simple/cw_timeline_pic/cbdc9e8fe9b/86874147b17189bcf1c8e/ios_large_1471624157_image.jpg", small_picture: "https://coubsecure-s.akamaihd.net/get/b63/p/coub/simple/cw_image/55712e4dc1f/82a43b898a7d81e7fee3d/ios_mosaic_1471624143_00032.jpg", sharing_picture: null, percent_done: 100, tags: [ { id: 369561, title: "black earth", value: "black%20earth" }, { id: 284, title: "earth", value: "earth" }, { id: 5096, title: "star", value: "star" }, { id: 1594, title: "black", value: "black" }, { id: 74910, title: "lustmord", value: "lustmord" }, { id: 568122, title: "kessler parallax", value: "kessler%20parallax" }, { id: 407113, title: "sennheiser", value: "sennheiser" }, { id: 26527, title: "sfx", value: "sfx" }, { id: 3098, title: "sound", value: "sound" }, { id: 2940, title: "travel", value: "travel" }, { id: 1259, title: "forest", value: "forest" }, { id: 403146, title: "earthporn", value: "earthporn" }, { id: 1094, title: "nature", value: "nature" }, { id: 13669, title: "canon", value: "canon" }, { id: 319113, title: "c300", value: "c300" }, { id: 30955, title: "hope", value: "hope" }, { id: 568121, title: "blanket creek", value: "blanket%20creek" }, { id: 54205, title: "revelstoke", value: "revelstoke" }, { id: 568120, title: "tofino", value: "tofino" }, { id: 10102, title: "canada", value: "canada" }, { id: 9925, title: "bc", value: "bc" }, { id: 75986, title: "british columbia", value: "british%20columbia" } ], categories: [ { id: 9, title: "Nature & Travel", permalink: "nature-travel", subscriptions_count: 6410461, big_image_url: "https://coubsecure-s.akamaihd.net/get/b110/p/category/cw_image/a4b57370b28/3c9d9a8cf4246d5a30610/big_1545567929_Nature.png", small_image_url: "https://coubsecure-s.akamaihd.net/get/b110/p/category/cw_image/a4b57370b28/3c9d9a8cf4246d5a30610/small_1545567929_Nature.png", med_image_url: "https://coubsecure-s.akamaihd.net/get/b110/p/category/cw_image/a4b57370b28/3c9d9a8cf4246d5a30610/med_1545567929_Nature.png", visible: true } ], communities: [ { id: 9, title: "Nature & Travel", permalink: "nature-travel", subscriptions_count: 6410461, big_image_url: "https://coubsecure-s.akamaihd.net/get/b110/p/category/cw_image/a4b57370b28/3c9d9a8cf4246d5a30610/big_1545567929_Nature.png", small_image_url: "https://coubsecure-s.akamaihd.net/get/b110/p/category/cw_image/a4b57370b28/3c9d9a8cf4246d5a30610/small_1545567929_Nature.png", med_image_url: "https://coubsecure-s.akamaihd.net/get/b110/p/category/cw_image/a4b57370b28/3c9d9a8cf4246d5a30610/med_1545567929_Nature.png", i_subscribed: true, community_notifications_enabled: null, description: null } ], recoubs_count: 117, remixes_count: 1, likes_count: 369, dislikes_count: 0, raw_video_id: 881397, uploaded_by_ios_app: false, uploaded_by_android_app: false, media_blocks: { uploaded_raw_videos: [ ], external_raw_videos: [ { id: 11517738, title: "EARTH PORN // VOL 1 // BRITISH COLUMBIA", url: "http://vimeo.com/97647244", image: "https://coubsecure-s.akamaihd.net/get/b132/p/media_block/cw_image/0aa95a7dca9/2ac0c2bc158ce313100a0/video_1488662438_1412796261_2fzbw_att-url-download.jpg", image_retina: "https://coubsecure-s.akamaihd.net/get/b132/p/media_block/cw_image/0aa95a7dca9/2ac0c2bc158ce313100a0/video_retina_1488662438_1412796261_2fzbw_att-url-download.jpg", meta: { service: "Vimeo", duration: "241.48" }, duration: 241.48, raw_video_id: 881397, has_embed: true } ], remixed_from_coubs: [ ], external_video: { id: 11517738, title: "EARTH PORN // VOL 1 // BRITISH COLUMBIA", url: "http://vimeo.com/97647244", image: "https://coubsecure-s.akamaihd.net/get/b132/p/media_block/cw_image/0aa95a7dca9/2ac0c2bc158ce313100a0/video_1488662438_1412796261_2fzbw_att-url-download.jpg", image_retina: "https://coubsecure-s.akamaihd.net/get/b132/p/media_block/cw_image/0aa95a7dca9/2ac0c2bc158ce313100a0/video_retina_1488662438_1412796261_2fzbw_att-url-download.jpg", meta: { service: "Vimeo", duration: "241.48" }, duration: 241.48, raw_video_id: 881397, has_embed: true }, audio_track: { id: 19398859, title: "Black Star", url: "https://geo.itunes.apple.com/us/album/dog-star-descends/id429464140?i=429464316&uo=4&at=10l5bB&app=music", image: "https://coubsecure-s.akamaihd.net/get/b39/p/media_block/cw_image/65c66b2a69d/bc3972e67f3014008e8a7/audio_1492079974_1471281340_17twwhw_att-url-download.jpg", image_retina: "https://coubsecure-s.akamaihd.net/get/b39/p/media_block/cw_image/65c66b2a69d/bc3972e67f3014008e8a7/audio_retina_1492079974_1471281340_17twwhw_att-url-download.jpg", meta: { year: null, album: "Darkwave", title: "Black Star", artist: "Lustmord", echonest_id: null }, duration: null, amazon_url: null, google_play_url: null, bandcamp_url: null, soundcloud_url: null, track_name: null, track_artist: null, track_album: null, itunes_url: "https://geo.itunes.apple.com/us/album/dog-star-descends/id429464140?i=429464316&uo=4&at=10l5bB&app=music" } }, raw_video_thumbnail_url: "https://coubsecure-s.akamaihd.net/get/b81/p/raw_video/cw_image/943e4594798/16b34b52cae3d9f58edf5/coub_media_1470564426_1t07bnz_att-url-download.jpg", raw_video_title: "EARTH PORN // VOL 1 // BRITISH COLUMBIA", video_block_banned: false, duration: 10, promo_winner: false, promo_winner_recoubers: null, editorial_info: { }, promo_hint: null, beeline_best_2014: null, from_web_editor: true, normalize_sound: true, normalize_change_allowed: true, best2015_addable: false, ahmad_promo: null, promo_data: null, audio_copyright_claim: null, ads_disabled: null, is_safe_for_ads: true, megafon_contents: [ ] },
                        //     background_image: "https://coubsecure-s.akamaihd.net/get/b87/p/background/cw_banner_image/c718d0406e5/099105ee16d8e0a653280/1471636547_1412797014_00032.jpg",
                        //     timeline_banner_image: "https://coubsecure-s.akamaihd.net/get/b87/p/background/cw_banner_image/c718d0406e5/099105ee16d8e0a653280/channel_timeline_banner_1471636547_1412797014_00032.jpg",
                        //     meta: { description: "Designer ~ Developer ~ Photographer ~ Vegan ~ Biker ~ Traveler", homepage: "http://theruslan.ru/", facebook: "1220061763", google: "104967024393426082046", twitter: "The__Ruslan", vkontakte: "the__ruslan", tumblr: null, youtube: "", vimeo: "" },
                        //     views_count: 968010,
                        //     hide_owner: false
                        // };

                        this.error = '';

                        console.log('data', data);
                        if (data) {
                            if (data['avatar_versions'] && data['avatar_versions']['template']) {
                                let avatar = data['avatar_versions']['template'];
                                this.couber.image = avatar.replace('%{version}', 'profile_pic_big');
                            }
                            this.couber.description = data['description'] || '';
                            this.couber.totalCoubs = data['simple_coubs_count'] || '';
                            this.couber.created = data['created_at'] || '';
                            this.couber.viewsCount = data['views_count'] || '';
                            this.couber.followersCount = data['followers_count'] || '';
                            this.couber.name = data['title'] || '';
                            this.couber.link = data['permalink'] || '';

                            if (data['error']) {
                                if (data['error'] === 'Unhandled exception') {
                                    this.error = 'User not found';
                                } else {
                                    this.error = data['error'];
                                }

                                this.clearData();
                            }
                        }

                        if (!data) {
                            // this.error = 'User not found';

                            this.clearData();
                        }
                    })
                    .catch((error) => {
                        if (String(error) === 'Error: Request failed with status code 404') {
                            this.error = 'Юзер не найден';
                        } else {
                            console.log('error', error);
                        }

                        this.clearData();
                    });
            },
            clearData: function () {
                this.couber.image = '';
                this.couber.link = '';
                this.couber.name = '';
                this.couber.description = '';
                this.couber.totalCoubs = '';
                this.couber.created = '';
                this.couber.viewsCount = '';
                this.couber.followersCount = '';
            }
        }
    }
    ;
</script>



