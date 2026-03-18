<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="pt-4">
                <div class="max-w-full mx-auto px-4">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Media page settings
                        </h3>
                    </div>
                </div>
            </header>

            <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting">
                <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                    <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                        <li
                            class="mr-2"
                            v-for="language in languages"
                            :key="language.id"
                        >
                            <a
                                href="#"
                                @click.prevent="updateLanguageId(language)"
                                :class="[
                                    'inline-block rounded font-FuturaMdCnBT px-5 py-2 lg:text-lg md:text-base sm:text-base text-base hover:bg-blue-100 border border-primary text-center hover:border-blue-500 hover:text-blue-600',
                                    (activeLanguageId == null && language.is_default) ||
                                    activeLanguageId == language.id
                                        ? 'bg-primary text-white'
                                        : '',
                                    checkValidationError(validationErros, language)
                                        ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                        : '',
                                ]"
                            >
                                {{ language.name }}
                            </a>
                        </li>
                    </ul>
                </div>

                <template v-for="language in languages" :key="language.id">
                    <div
                        v-if="
                            (activeLanguageId == null && language.is_default) ||
                            language.id == activeLanguageId
                        "
                    >
                        <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                            <div class="relative z-0 w-full group">
                                <div>
                                    <div class="flex justify-between">
                                        <label :for="`main_heading_${activeLanguageId}`">
                                            Main heading
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        :name="`main_heading_${activeLanguageId}`"
                                        :id="`main_heading_${activeLanguageId}`"
                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                        placeholder=" "
                                        :value="getCurrentValue('main_heading')"
                                        @input="handleInput($event.target.value, language, 'main_heading')"
                                    />
                                </div>
                                <p
                                    class="mt-2 text-sm text-red-400"
                                    v-if="validationErros.has(`main_heading.main_heading_${activeLanguageId}`)"
                                    v-text="validationErros.get(`main_heading.main_heading_${activeLanguageId}`)"
                                ></p>
                            </div>

                            <div class="relative z-0 w-full group">
                                <div>
                                    <div class="flex justify-between">
                                        <label :for="`read_article_button_label_${activeLanguageId}`">
                                            Read article button label
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        :name="`read_article_button_label_${activeLanguageId}`"
                                        :id="`read_article_button_label_${activeLanguageId}`"
                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                        placeholder=" "
                                        :value="getCurrentValue('read_article_button_label')"
                                        @input="handleInput($event.target.value, language, 'read_article_button_label')"
                                    />
                                </div>
                                <p
                                    class="mt-2 text-sm text-red-400"
                                    v-if="validationErros.has(`read_article_button_label.read_article_button_label_${activeLanguageId}`)"
                                    v-text="validationErros.get(`read_article_button_label.read_article_button_label_${activeLanguageId}`)"
                                ></p>
                            </div>

                            <div class="relative z-0 w-full group">
                                <div>
                                    <div class="flex justify-between">
                                        <label :for="`meta_description_${activeLanguageId}`">
                                            Meta description
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        :name="`meta_description_${activeLanguageId}`"
                                        :id="`meta_description_${activeLanguageId}`"
                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                        placeholder=" "
                                        :value="getCurrentValue('meta_description')"
                                        @input="handleInput($event.target.value, language, 'meta_description')"
                                    />
                                </div>
                            </div>

                            <div class="relative z-0 w-full group">
                                <div>
                                    <div class="flex justify-between">
                                        <label :for="`meta_keywords_${activeLanguageId}`">
                                            Meta keywords
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        :name="`meta_keywords_${activeLanguageId}`"
                                        :id="`meta_keywords_${activeLanguageId}`"
                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                        placeholder=" "
                                        :value="getCurrentValue('meta_keywords')"
                                        @input="handleInput($event.target.value, language, 'meta_keywords')"
                                    />
                                </div>
                            </div>

                            <div class="relative z-0 w-full group">
                                <div>
                                    <div class="flex justify-between">
                                        <label :for="`agency_label_${activeLanguageId}`">
                                            Agency label
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        :name="`agency_label_${activeLanguageId}`"
                                        :id="`agency_label_${activeLanguageId}`"
                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                        placeholder=" "
                                        :value="getCurrentValue('agency_label')"
                                        @input="handleInput($event.target.value, language, 'agency_label')"
                                    />
                                </div>
                                <p
                                    class="mt-2 text-sm text-red-400"
                                    v-if="validationErros.has(`agency_label.agency_label_${activeLanguageId}`)"
                                    v-text="validationErros.get(`agency_label.agency_label_${activeLanguageId}`)"
                                ></p>
                            </div>

                            <div class="relative z-0 w-full group">
                                <div>
                                    <div class="flex justify-between">
                                        <label :for="`added_by_label_${activeLanguageId}`">
                                            Added by label
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        :name="`added_by_label_${activeLanguageId}`"
                                        :id="`added_by_label_${activeLanguageId}`"
                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                        placeholder=" "
                                        :value="getCurrentValue('added_by_label')"
                                        @input="handleInput($event.target.value, language, 'added_by_label')"
                                    />
                                </div>
                                <p
                                    class="mt-2 text-sm text-red-400"
                                    v-if="validationErros.has(`added_by_label.added_by_label_${activeLanguageId}`)"
                                    v-text="validationErros.get(`added_by_label.added_by_label_${activeLanguageId}`)"
                                ></p>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="submit" class="button-exp-fill mt-5">
                    Submit
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import axios from "axios";
import ErrorHandling from "../../ErrorHandling.js";

export default {
    data() {
        return {
            activeLanguageId: null,
            languages: [],
            form: {},
            validationErros: new ErrorHandling(),
            loading: false,
        };
    },
    created() {
        this.fetchLanguages();
    },
    methods: {
        getCurrentValue(name) {
            return this.form[name] &&
                this.form[name][`${name}_${this.activeLanguageId}`]
                ? this.form[name][`${name}_${this.activeLanguageId}`]
                : "";
        },
        handleInput(value, language, key) {
            if (this.form.hasOwnProperty(key)) {
                this.form[key][`${key}_${language.id}`] = value;
            } else {
                this.form[key] = {};
                this.form[key][`${key}_${language.id}`] = value;
            }
        },
        updateLanguageId(language) {
            this.activeLanguageId = language.id;
        },
        fetchLanguages() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}languages?getAll=1`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        this.languages = res?.data?.data;
                        let defaultLang = this.languages.filter(
                            (x) => x.is_default == "1"
                        );
                        this.activeLanguageId = defaultLang?.[0]?.id || null;

                        const languages = res?.data?.data;
                        languages.map((language) => {
                            this.handleInput("", language, "name");
                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "read_article_button_label");
                            this.handleInput("", language, "agency_label");
                            this.handleInput("", language, "added_by_label");
                        });

                        this.fetchMediaSetting();
                    }
                });
        },
        fetchMediaSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-media-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        const details =
                            res?.data?.data?.media_setting_detail || [];
                        details.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.meta_keywords,
                                setting?.language,
                                "meta_keywords"
                            );
                            this.handleInput(
                                setting?.meta_description,
                                setting?.language,
                                "meta_description"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.read_article_button_label,
                                setting?.language,
                                "read_article_button_label"
                            );
                            this.handleInput(
                                setting?.agency_label,
                                setting?.language,
                                "agency_label"
                            );
                            this.handleInput(
                                setting?.added_by_label,
                                setting?.language,
                                "added_by_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-media-setting`,
                    this.form
                )
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        this.validationErros = new ErrorHandling();
                        helper.swalSuccessMessage(res.data.message);
                    } else {
                        helper.swalErrorMessage(res.data.message);
                    }
                    this.loading = false;
                })
                .catch((error) => {
                    this.validationErros = new ErrorHandling();
                    if (error.response && error.response.status == 422) {
                        this.validationErros.record(error.response.data.errors);
                    } else if (
                        error.response &&
                        error.response.data &&
                        error.response.data.status == "Error"
                    ) {
                        helper.swalErrorMessage(error.response.data.message);
                    }
                    this.loading = false;
                })
                .finally(() => (this.loading = false));
        },
        checkValidationError(validationErros, language) {
            return (
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `read_article_button_label.read_article_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `agency_label.agency_label_${language.id}`
                ) ||
                validationErros.has(
                    `added_by_label.added_by_label_${language.id}`
                )
            );
        },
    },
};
</script>

