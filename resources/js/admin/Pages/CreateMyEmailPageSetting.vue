<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    My Email settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <ExcelBulkImport
                        title="My Email"
                        mode="all_languages"
                        download-endpoint="download-my-email-setting-template"
                        upload-endpoint="upload-my-email-setting-excel"
                        @success="fetchMyEmailSetting"
                    />

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
                                <div
                                    class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                >
                                <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`main_heading_${activeLanguageId}`"
                                                        >Main heading </label
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


                                </div>

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
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_description_text_${activeLanguageId}`"
                                                    >Email Description</label
                                                >
                                            </div>
                                            <input
                                                    type="text"
                                                    :name="`email_description_text_${activeLanguageId}`"
                                                    :id="`email_description_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'email_description_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'email_description_text'
                                                        )
                                                    "
                                                />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_description_text.email_description_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_description_text.email_description_text_${activeLanguageId}`
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
                                                        :for="`email_label_${activeLanguageId}`">
                                                       Email Label</label
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

                                    <!-- <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`email_placeholder_${activeLanguageId}`"
                                                        >Email Placeholder</label
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
                                        </div> -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`current_email_label_${activeLanguageId}`"
                                                        >Current Email Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`current_email_label_${activeLanguageId}`"
                                                    :id="`current_email_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'current_email_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'current_email_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `current_email_label.current_email_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `current_email_label.current_email_label_${activeLanguageId}`
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
                                                        :for="`new_email_label_${activeLanguageId}`"
                                                        >New Email Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`new_email_label_${activeLanguageId}`"
                                                    :id="`new_email_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'new_email_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'new_email_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `new_email_label.new_email_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `new_email_label.new_email_label_${activeLanguageId}`
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
                                                        :for="`confirm_email_label_${activeLanguageId}`"
                                                        >Confirm Email Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`confirm_email_label_${activeLanguageId}`"
                                                    :id="`confirm_email_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'confirm_email_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'confirm_email_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `confirm_email_label.confirm_email_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `confirm_email_label.confirm_email_label_${activeLanguageId}`
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
                                                        :for="`save_btn_label_${activeLanguageId}`"
                                                        >Save Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`save_btn_label_${activeLanguageId}`"
                                                    :id="`save_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'save_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'save_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `save_btn_label.save_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `save_btn_label.save_btn_label_${activeLanguageId}`
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
                                                        :for="`update_button_text_${activeLanguageId}`"
                                                        >Update Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`update_button_text_${activeLanguageId}`"
                                                    :id="`update_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'update_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'update_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `update_button_text.update_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `update_button_text.update_button_text_${activeLanguageId}`
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
                                </div>    </AppLayout>
</template>
<script>
import Editor from "@tinymce/tinymce-vue";
import axios from "axios";
import ErrorHandling from "../../ErrorHandling.js";
import ExcelBulkImport from "../components/ExcelBulkImport.vue";
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
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor image code table",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table paste code help wordcount",
                ],
                toolbar:
                    "undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | table | image | code | link | help",
            },
        };
    },
    components: {
        editor: Editor,
        ExcelBulkImport,
    },
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/pages/';
            if (!base.endsWith('/')) base += '/';
            return base;
        }
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
                            this.handleInput("", language, "name");
                            this.handleInput("", language, "email_description_text");
                            this.handleInput("", language, "email_label");
                            this.handleInput("", language, "main_heading");
                            // this.handleInput("", language, "email_placeholder");
                            this.handleInput("", language, "update_button_text");
                            this.handleInput("", language, "new_email_label");
                            this.handleInput("", language, "save_btn_label");
                            this.handleInput("", language, "confirm_email_label");
                            this.handleInput("", language, "current_email_label");
                        });
                        this.fetchMyEmailSetting();
                    }
                });
        },
        fetchMyEmailSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-my-email-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let 	my_email_address_setting_detail =
                            res?.data?.data?.my_email_address_setting_detail || [];
                        	my_email_address_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.email_description_text,
                                setting?.language,
                                "email_description_text"
                            );
                            this.handleInput(
                                setting?.email_label,
                                setting?.language,
                                "email_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            // this.handleInput(
                            //     setting?.email_placeholder,
                            //     setting?.language,
                            //     "email_placeholder"
                            // );
                            this.handleInput(
                                setting?.update_button_text,
                                setting?.language,
                                "update_button_text"
                            );
                            this.handleInput(
                                setting?.new_email_label,
                                setting?.language,
                                "new_email_label"
                            );
                            this.handleInput(
                                setting?.current_email_label,
                                setting?.language,
                                "current_email_label"
                            );
                            this.handleInput(
                                setting?.save_btn_label,
                                setting?.language,
                                "save_btn_label"
                            );
                            this.handleInput(
                                setting?.confirm_email_label,
                                setting?.language,
                                "confirm_email_label"
                            );


                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-my-email-setting`,
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
                    `email_description_text.email_description_text_${language.id}`
                ) ||
                validationErros.has(
                    `email_label.email_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `update_button_text.update_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `new_email_label.new_email_label_${language.id}`
                ) ||
                validationErros.has(
                    `current_email_label.current_email_label_${language.id}`
                ) ||
                validationErros.has(
                    `save_btn_label.save_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `confirm_email_label.confirm_email_label_${language.id}`
                )
            );
        },
    },
};
</script>
