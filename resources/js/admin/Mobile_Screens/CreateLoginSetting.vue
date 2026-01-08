<template>
    <AppLayout>
        <section class="login-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Login page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <form
                        class="px-4 md:px-6 lg:px-8"
                        @submit.prevent="updatePageSetting()"
                    >
                        <div
                            class="text-sm font-medium text-center text-gray-500 border-b border-gray-200"
                        >
                            <ul
                                class="flex flex-wrap mb-2 overflow-x-auto gap-1"
                            >
                                <li
                                    class="mr-2"
                                    v-for="language in languages"
                                    :key="language.id"
                                >
                                    <a
                                        href="#"
                                        @click.prevent="
                                            updateLanguageId(language)
                                        "
                                        :class="[
                                            'inline-block rounded font-FuturaMdCnBT px-5 py-2 lg:text-lg md:text-base sm:text-base text-base hover:bg-blue-100 border border-primary text-center hover:border-blue-500 hover:text-blue-600',
                                            (activeLanguageId == null &&
                                                language.is_default) ||
                                            activeLanguageId == language.id
                                                ? 'bg-primary  text-white'
                                                : '',
                                            checkValidationError(
                                                validationErros,
                                                language
                                            )
                                                ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                                : '',
                                        ]"
                                        >{{ language.name }}</a
                                    >
                                </li>
                            </ul>
                        </div>
                        <template
                            v-for="language in languages"
                            :key="language.id"
                        >
                            <div
                                v-if="
                                    (activeLanguageId == null &&
                                        language.is_default) ||
                                    language.id == activeLanguageId
                                "
                            >
                                <!-- <div
                                    class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                >
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`name_${activeLanguageId}`"
                                                    >Name</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`name_${activeLanguageId}`"
                                                :id="`name_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('name')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'name'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `name.name_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `name.name_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`meta_description_${activeLanguageId}`"
                                                    >Meta description</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`meta_description_${activeLanguageId}`"
                                                :id="`meta_description_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'meta_description'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'meta_description'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `meta_description.meta_description_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `meta_description.meta_description_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`meta_keywords_${activeLanguageId}`"
                                                    >Meta keywords</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`meta_keywords_${activeLanguageId}`"
                                                :id="`meta_keywords_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'meta_keywords'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'meta_keywords'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `meta_keywords.meta_keywords_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `meta_keywords.meta_keywords_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                </div> -->

                                <!-- main section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[1] =
                                                !collapseStates[1]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Main section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[1]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`main_heading_${activeLanguageId}`"
                                                    :id="`main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `main_heading.main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `main_heading.main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <!-- <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`continue_label_${activeLanguageId}`"
                                                        >Continue label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`continue_label_${activeLanguageId}`"
                                                    :id="`continue_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'continue_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'continue_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `continue_label.continue_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `continue_label.continue_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div> -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`email_label_${activeLanguageId}`"
                                                        >Email label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`email_label_${activeLanguageId}`"
                                                    :id="`email_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'email_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'email_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `email_label.email_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `email_label.email_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`email_placeholder_${activeLanguageId}`"
                                                        >Email placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`email_placeholder_${activeLanguageId}`"
                                                    :id="`email_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'email_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'email_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `email_placeholder.email_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `email_placeholder.email_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`password_label_${activeLanguageId}`"
                                                        >Password label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`password_label_${activeLanguageId}`"
                                                    :id="`password_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'password_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'password_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `password_label.password_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `password_label.password_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`password_placeholder_${activeLanguageId}`"
                                                        >Password placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`password_placeholder_${activeLanguageId}`"
                                                    :id="`password_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'password_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'password_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `password_placeholder.password_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `password_placeholder.password_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`submit_button_label_${activeLanguageId}`"
                                                        >Submit button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`submit_button_label_${activeLanguageId}`"
                                                    :id="`submit_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'submit_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'submit_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `submit_button_label.submit_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `submit_button_label.submit_button_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`forgot_password_label_${activeLanguageId}`"
                                                        >Forgot password label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`forgot_password_label_${activeLanguageId}`"
                                                    :id="`forgot_password_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'forgot_password_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'forgot_password_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `forgot_password_label.forgot_password_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `forgot_password_label.forgot_password_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`or_label_${activeLanguageId}`"
                                                        >Or label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`or_label_${activeLanguageId}`"
                                                    :id="`or_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'or_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'or_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `or_label.or_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `or_label.or_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`signup_label_${activeLanguageId}`"
                                                        >Signup label</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'signup_label'
                                                        )
                                                    "
                                                    :ref="`signup_label_${language.id}`"
                                                    :id="`signup_label_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `signup_label`
                                                        ][
                                                            `signup_label_${language?.id}`
                                                        ]
                                                    "
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :init="editorConfig"
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `signup_label.signup_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `signup_label.signup_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- main section end -->
                            </div>
                        </template>
                        <button type="submit" class="button-exp-fill mt-5">
                            Submit
                        </button>
                    </form>
                </div>
            </main>
        </section>
    </AppLayout>
</template>
<script>
import Editor from "@tinymce/tinymce-vue";
import axios from "axios";
import ErrorHandling from "../../ErrorHandling.js";
export default {
    data() {
        return {
            activeLanguageId: null,
            languages: [],
            form: {},
            validationErros: new ErrorHandling(),
            collapseStates: [true, false, false, false, false, false, false],
            loading: false,
            editorConfig: {
                height: 250,
                menubar: false,
                plugins:
                  "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen code",
                toolbar:
                  "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code | fullscreen",
                base_url: "/plugins/tinymce",
                suffix: ".min",
            },
            tinymceScriptSrc: "/plugins/tinymce/tinymce.min.js",
        };
    },
    components: {
        editor: Editor,
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
        handleSelectionChange(language, key) {
            this.handleInput(
                tinymce.get(`${key}_${language.id}`).getContent(),
                language,
                key
            );
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
                        let languages = res?.data?.data;
                        languages.map((language) => {
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "email_label");
                            this.handleInput("", language, "email_placeholder");
                            this.handleInput("", language, "password_label");
                            this.handleInput("", language, "password_placeholder");
                            this.handleInput("", language, "submit_button_label");
                            this.handleInput("", language, "forgot_password_label");
                            this.handleInput("", language, "or_label");
                            this.handleInput("", language, "signup_label");
                        });
                        this.fetchLoginPageSetting();
                    }
                });
        },
        fetchLoginPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-mobile-login-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let mobile_login_setting_detail =
                            res?.data?.data?.mobile_login_setting_detail || [];
                        mobile_login_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.email_label,
                                setting?.language,
                                "email_label"
                            );
                            this.handleInput(
                                setting?.email_placeholder,
                                setting?.language,
                                "email_placeholder"
                            );
                            this.handleInput(
                                setting?.password_label,
                                setting?.language,
                                "password_label"
                            );
                            this.handleInput(
                                setting?.password_placeholder,
                                setting?.language,
                                "password_placeholder"
                            );
                            this.handleInput(
                                setting?.submit_button_label,
                                setting?.language,
                                "submit_button_label"
                            );
                            this.handleInput(
                                setting?.forgot_password_label,
                                setting?.language,
                                "forgot_password_label"
                            );
                            this.handleInput(
                                setting?.or_label,
                                setting?.language,
                                "or_label"
                            );
                            this.handleInput(
                                setting?.signup_label,
                                setting?.language,
                                "signup_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-mobile-login-page-setting`,
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
                validationErros.has(`name.name_${language.id}`) ||
                validationErros.has(
                    `meta_keywords.meta_keywords_${language.id}`
                ) ||
                validationErros.has(
                    `meta_description.meta_description_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `continue_label.continue_label_${language.id}`
                ) ||
                validationErros.has(
                    `or_label.or_label_${language.id}`
                ) ||
                validationErros.has(
                    `email_label.email_label_${language.id}`
                ) ||
                validationErros.has(
                    `email_placeholder.email_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `password_label.password_label_${language.id}`
                ) ||
                validationErros.has(
                    `password_placeholder.password_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `forgot_password_label.forgot_password_label_${language.id}`
                ) ||
                validationErros.has(
                    `submit_button_label.submit_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `signup_label.signup_label_${language.id}`
                )
            );
        },
    },
};
</script>
