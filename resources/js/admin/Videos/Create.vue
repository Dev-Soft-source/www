<template>
    <AppLayout>
        <header class="py-4 bg-white">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="can-edu-h1">
                        Create Videos
                    </h1>
                    <router-link
                        :to="{ name: 'admin.videos.index' }"
                        class="button-exp-fill"
                    >
                        Back
                    </router-link>
                </div>
            </div>
        </header>
        <form class="py-4 px-4 bg-white" @submit.prevent="addUpdateForm()">
            <div
                class="text-sm font-medium text-center text-gray-500 border-b border-gray-200"
            >
                <ul class="flex gap-2 flex-wrap my-4">
                    <li
                        class="mr-2 mb-2"
                        v-for="language in languages"
                        :key="language.id"
                    >
                        <a
                            @click.prevent="changeLanguageTab(language)"
                            href="#"
                            :class="[
                                'inline-block py-2 px-4 text-primary border border-primary rounded  font-FuturaMdCnBT text-base lg:text-lg font-medium hover:text-white hover:bg-primary active:text-white active:bg-primary',
                                (activeTab == null && language.is_default) ||
                                activeTab == language.id
                                    ? 'bg-primary  text-white'
                                    : '',
                                validationErros.has(
                                    `link.link_${language.id}`
                                ) || validationErros.has(
                                    'page'
                                )
                                    ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                    : '',
                            ]"
                            >{{ language.name }}</a
                        >
                    </li>
                </ul>
            </div>

            <div
                class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6"
                v-for="language in languages"
                :key="language.id"
                :class="
                    (activeTab == null && language.is_default) ||
                    activeTab == language.id
                        ? 'block'
                        : 'hidden'
                "
            >
                <div class="relative z-0 w-full group">
                    <label for="link" class="">Youtube video link</label>
                    <input
                        type="url"
                        name="link"
                        id="link"
                        class="can-exp-input w-full block border border-gray-300 rounded"
                        placeholder=" "
                        @input="
                            handleMultipleInput(
                                'link',
                                $event.target.value,
                                language
                            )
                        "
                        :value="
                            form['link'] && form['link'][`link_${language.id}`]
                                ? form['link'][`link_${language.id}`]
                                : ''
                        "
                    />
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has(`link.link_${language.id}`)"
                        v-text="validationErros.get(`link.link_${language.id}`)"
                    ></p>
                </div>
                <div class="relative z-0 w-full group">
                    <label for="page" class="">Page</label>
                    <select
                        class="can-exp-input w-full block border border-gray-300 rounded"
                        @input="handleInput($event.target.value, 'page')"
                    >
                        <option value="">Select</option>
                        <option
                            :selected="form['page'] == 'For Students'"
                            value="For Students"
                        >
                            For Students
                        </option>
                        <option :selected="form['page'] == 'For Passengers'" value="For Passengers">
                            For Passengers
                        </option>
                        <option :selected="form['page'] == 'For Drivers'" value="For Drivers">
                            For Drivers
                        </option>
                        <option :selected="form['page'] == 'Introduction Video'" value="Introduction Video">
                            Introduction Video
                        </option>
                        <option :selected="form['page'] == 'How it works'" value="How it works">
                            How it works
                        </option>
                    </select>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has(`page`)"
                        v-text="validationErros.get(`page`)"
                    ></p>
                </div>
            </div>
            <button
                type="submit"
                class="button-exp-fill"
            >
                Submit
            </button>
        </form>
    </AppLayout>
</template>

<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState({
            form: (state) => state.videos.form,
            validationErros: (state) => state.videos.validationErros,
            languages: (state) => state.languages.languages,
        }),
    },
    data() {
        return {
            activeTab: null,
        };
    },
    methods: {
        handleInput(value, key) {
            this.$store.commit("videos/setState", { key, value });
        },
        handleMultipleInput(key, value, language) {
            this.$store.commit("videos/updateState", {
                value: value,
                id: language.id,
                key,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("videos/addUpdateForm")
                .then(() => this.$router.push({ name: "admin.videos.index" }));
        },
        changeLanguageTab(language) {
            this.activeTab = language.id;
        },
        fetchVideos() {
            if (this.$route.params.id) {
                let id = this.$route.params.id;

                this.$store.commit("videos/setIsFormEdit", 1);
                this.$store
                    .dispatch("videos/fetchVideos", {
                        id: id,
                        url: `${process.env.MIX_ADMIN_API_URL}videos/${id}?withVideoDetail=1`,
                    })
                    .then((res) => {
                        let keys = [
                            "page",
                        ];
                        this.$store.commit("videos/setState", {
                            key: "id",
                            value: id,
                        });
                        for (var i = 0; i < keys.length; i++) {
                            this.$store.commit("videos/setState", {
                                key: keys[i],
                                value: res.data.data[keys[i]],
                            });
                        }

                        if (res.data.data.image) {
                            this.convertImgUrlToBase64(
                                res.data.data.image.full_path,
                                `image/${res.data.data.image.extension}`
                            );
                        }
                        let data =
                            res.data.data && res.data.data.video_detail
                                ? res.data.data.video_detail
                                : [];
                        let obj = {};

                        data.map((res) => {
                            obj["link_" + res.language_id] = res.link;
                        });
                        this.$store.commit("videos/setState", {
                            key: "link",
                            value: obj,
                        });
                    });
            }
        },
    },
    created() {
        this.$store.commit("videos/resetForm");
        this.$store
            .dispatch("languages/fetchLanguages", {
                url: `${process.env.MIX_ADMIN_API_URL}languages?getAll=1`,
            })
            .then((res) => {
                let data = res.data.data;
                let obj = {};
                data.map((res) => {
                    obj["link_" + res.id] = "";
                });
                this.$store.commit("videos/setState", {
                    key: "link",
                    value: obj,
                });
                this.fetchVideos();
            });
    },
};
</script>
