<template>
    <AppLayout>
        <header class="py-4 bg-white">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="can-edu-h1">
                        Create packages
                    </h1>
                    <router-link
                        :to="{ name: 'admin.packages.index' }"
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
                                    `name.name_${language.id}`
                                ) || validationErros.has(
                                    `short_description.short_description_${language.id}`
                                ) || validationErros.has(
                                    'price'
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
                    <label for="name" class="">Name</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="can-exp-input w-full block border border-gray-300 rounded"
                        placeholder=" "
                        @input="
                            handleMultipleInput(
                                'name',
                                $event.target.value,
                                language
                            )
                        "
                        :value="
                            form['name'] && form['name'][`name_${language.id}`]
                                ? form['name'][`name_${language.id}`]
                                : ''
                        "
                    />
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has(`name.name_${language.id}`)"
                        v-text="validationErros.get(`name.name_${language.id}`)"
                    ></p>
                </div>
                <!-- <div class="relative z-0 w-full group">
                    <label for="short_description" class="">Short description</label>
                    <input
                        type="text"
                        name="short_description"
                        id="short_description"
                        class="can-exp-input w-full block border border-gray-300 rounded"
                        placeholder=" "
                        @input="
                            handleMultipleInput(
                                'short_description',
                                $event.target.value,
                                language
                            )
                        "
                        :value="
                            form['short_description'] && form['short_description'][`short_description_${language.id}`]
                                ? form['short_description'][`short_description_${language.id}`]
                                : ''
                        "
                    />
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has(`short_description.short_description_${language.id}`)"
                        v-text="validationErros.get(`short_description.short_description_${language.id}`)"
                    ></p>
                </div> -->
                <div class="relative z-0 w-full group">
                    <label for="price" class="">Price per month</label>
                    <input
                        type="number"
                        min="0"
                        step="1"
                        name="price"
                        id="price"
                        class="can-exp-input w-full block border border-gray-300 rounded"
                        placeholder=" "
                        @input="
                            handleInput(
                                $event.target.value,
                                'price'
                            )
                        "
                        :value="
                            form['price'] ? form['price']
                                : ''
                        "
                    />
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has(`price`)"
                        v-text="validationErros.get(`price`)"
                    ></p>
                </div>
                <div class="relative z-0 w-full group mt-8">
                    <fieldset>
                        <legend class="sr-only">Set as default</legend>
                        <div class="flex items-center mb-4">
                            <input
                                id="is_default"
                                name="is_default"
                                type="checkbox"
                                value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                :checked="is_default"
                                v-model="is_default"
                            />
                            <label
                                for="is_default"
                                class="ml-2 text-sm font-medium text-gray-900"
                                >Set as default</label
                            >
                        </div>
                    </fieldset>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has('is_default')"
                        v-text="validationErros.get('is_default')"
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
            form: (state) => state.packages.form,
            validationErros: (state) => state.packages.validationErros,
            languages: (state) => state.languages.languages,
        }),
        is_default: {
            get: function () {
                return this.$store.state.languages.form.is_default;
            },
            set: function (val) {
                this.$store.commit("packages/setForm", {
                    is_default: val,
                });
            },
        },
    },
    data() {
        return {
            activeTab: null,
        };
    },
    methods: {
        handleInput(value, key) {
            this.$store.commit("packages/setState", { key, value });
        },
        handleMultipleInput(key, value, language) {
            this.$store.commit("packages/updateState", {
                value: value,
                id: language.id,
                key,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("packages/addUpdateForm")
                .then(() => this.$router.push({ name: "admin.packages.index" }));
        },
        changeLanguageTab(language) {
            this.activeTab = language.id;
        },
        fetchPackages() {
            if (this.$route.params.id) {
                let id = this.$route.params.id;

                this.$store.commit("packages/setIsFormEdit", 1);
                this.$store
                    .dispatch("packages/fetchPackages", {
                        id: id,
                        url: `${process.env.MIX_ADMIN_API_URL}packages/${id}?withPackageDetail=1`,
                    })
                    .then((res) => {
                        let keys = [
                            "price",
                        ];
                        this.$store.commit("packages/setState", {
                            key: "id",
                            value: id,
                        });
                        for (var i = 0; i < keys.length; i++) {
                            this.$store.commit("packages/setState", {
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
                            res.data.data && res.data.data.package_detail
                                ? res.data.data.package_detail
                                : [];
                        let obj = {};

                        data.map((res) => {
                            obj["name_" + res.language_id] = res.name;
                        });
                        this.$store.commit("packages/setState", {
                            key: "name",
                            value: obj,
                        });
                    });
            }
        },
    },
    created() {
        this.$store.commit("packages/resetForm");
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
                this.$store.commit("packages/setState", {
                    key: "link",
                    value: obj,
                });
                this.fetchPackages();
            });
    },
};
</script>
