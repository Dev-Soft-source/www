<template>
    <AppLayout>
        <section class="phone-section relative ">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Payment Option settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <ExcelBulkImport
                        title="Payment Option"
                        mode="all_languages"
                        download-endpoint="download-payment-option-setting-template"
                        upload-endpoint="upload-payment-option-setting-excel"
                        @success="fetchPaymentPageSetting"
                    />

                    <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting()">
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                            <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                                <li class="mr-2" v-for="language in languages" :key="language.id">
                                    <a href="#" @click.prevent="
                                        updateLanguageId(language)
                                        " :class="[
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
                                        ]">{{ language.name }}</a>
                                </li>
                            </ul>
                        </div>
                        <template v-for="language in languages" :key="language.id">
                            <div v-if="
                                (activeLanguageId == null &&
                                    language.is_default) ||
                                language.id == activeLanguageId
                            ">
                                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6">
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`main_heading_${activeLanguageId}`">Main heading </label>
                                            </div>
                                            <input type="text" :name="`main_heading_${activeLanguageId}`"
                                                :id="`main_heading_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue(
                                                    'main_heading'
                                                )
                                                    " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'main_heading'
                                                        )
                                                        " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `main_heading.main_heading_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                                    `main_heading.main_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`mobile_card_name_label_${activeLanguageId}`">
                                                    Card Name Label (mobile)</label>
                                            </div>
                                            <input type="text" :name="`mobile_card_name_label_${activeLanguageId}`"
                                                :id="`mobile_card_name_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue(
                                                    'mobile_card_name_label'
                                                )
                                                    " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_card_name_label'
                                                        )
                                                        " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `mobile_card_name_label.mobile_card_name_label_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                                    `mobile_card_name_label.mobile_card_name_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                    </div>
                                </div>

                                <!-- main section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[1] =
                                            !collapseStates[1]
                                            ">
                                        <h3 class="text-white">
                                            Main section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
                                        v-if="collapseStates[1]">


                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mobile_default_card_tab_${activeLanguageId}`">Default
                                                        Card Tab</label>
                                                </div>
                                                <input type="text" :name="`mobile_default_card_tab_${activeLanguageId}`"
                                                    :id="`mobile_default_card_tab_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_default_card_tab'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_default_card_tab'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_default_card_tab.mobile_default_card_tab_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `mobile_default_card_tab.mobile_default_card_tab_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>


                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mobile_card_number_label_${activeLanguageId}`">Card
                                                        Number Label (mobile)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`mobile_card_number_label_${activeLanguageId}`"
                                                    :id="`mobile_card_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_card_number_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_card_number_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_card_number_label.mobile_card_number_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `mobile_card_number_label.mobile_card_number_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mobile_expiry_date_label_${activeLanguageId}`">Expiry
                                                        Date (mobile)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`mobile_expiry_date_label_${activeLanguageId}`"
                                                    :id="`mobile_expiry_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_expiry_date_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_expiry_date_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_expiry_date_label.mobile_expiry_date_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `mobile_expiry_date_label.mobile_expiry_date_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`delete_card_button_text_${activeLanguageId}`">Delete
                                                        Button Label</label>
                                                </div>
                                                <input type="text" :name="`delete_card_button_text_${activeLanguageId}`"
                                                    :id="`delete_card_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'delete_card_button_text'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'delete_card_button_text'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `delete_card_button_text.delete_card_button_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `delete_card_button_text.delete_card_button_text_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`add_new_card_button_text_${activeLanguageId}`"> Add
                                                        New Button</label>
                                                </div>
                                                <input type="text"
                                                    :name="`add_new_card_button_text_${activeLanguageId}`"
                                                    :id="`add_new_card_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'add_new_card_button_text'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_new_card_button_text'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `add_new_card_button_text.add_new_card_button_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `add_new_card_button_text.add_new_card_button_text_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_payment_message_${activeLanguageId}`">No Payment
                                                        Message</label>
                                                </div>
                                                <input type="text" :name="`no_payment_message_${activeLanguageId}`"
                                                    :id="`no_payment_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_payment_message'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_payment_message'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_payment_message.no_payment_message_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `no_payment_message.no_payment_message_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`set_primary_card_label_${activeLanguageId}`">Set
                                                        Primary Card Label </label>
                                                </div>
                                                <input type="text" :name="`set_primary_card_label_${activeLanguageId}`"
                                                    :id="`set_primary_card_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'set_primary_card_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'set_primary_card_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `set_primary_card_label.set_primary_card_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `set_primary_card_label.set_primary_card_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`select_card_type_text_${activeLanguageId}`">Select
                                                        Card Label </label>
                                                </div>
                                                <input type="text" :name="`select_card_type_text_${activeLanguageId}`"
                                                    :id="`select_card_type_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'select_card_type_text'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'select_card_type_text'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `select_card_type_text.select_card_type_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `select_card_type_text.select_card_type_text_${activeLanguageId}`
                                                )
                                                    "></p>
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
        },
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
                            this.handleInput("", language, "mobile_default_card_tab");
                            this.handleInput("", language, "mobile_card_name_label");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "mobile_card_number_label");
                            this.handleInput("", language, "mobile_expiry_date_label");
                            this.handleInput("", language, "delete_card_button_text");
                            this.handleInput("", language, "add_new_card_button_text");
                            this.handleInput("", language, "no_payment_message");
                            this.handleInput("", language, "set_primary_card_label");
                            this.handleInput("", language, "select_card_type_text");
                        });
                        this.fetchPaymentPageSetting();
                    }
                });
        },
        fetchPaymentPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-payment-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let payment_option_setting_detail =
                            res?.data?.data?.payment_option_setting_detail || [];
                        payment_option_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.mobile_default_card_tab,
                                setting?.language,
                                "mobile_default_card_tab"
                            );
                            this.handleInput(
                                setting?.mobile_card_name_label,
                                setting?.language,
                                "mobile_card_name_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.mobile_card_number_label,
                                setting?.language,
                                "mobile_card_number_label"
                            );
                            this.handleInput(
                                setting?.mobile_expiry_date_label,
                                setting?.language,
                                "mobile_expiry_date_label"
                            );
                            this.handleInput(
                                setting?.delete_card_button_text,
                                setting?.language,
                                "delete_card_button_text"
                            );

                            this.handleInput(
                                setting?.add_new_card_button_text,
                                setting?.language,
                                "add_new_card_button_text"
                            );
                            this.handleInput(
                                setting?.no_payment_message,
                                setting?.language,
                                "no_payment_message"
                            );
                            this.handleInput(
                                setting?.set_primary_card_label,
                                setting?.language,
                                "set_primary_card_label"
                            );
                            this.handleInput(
                                setting?.select_card_type_text,
                                setting?.language,
                                "select_card_type_text"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-payment-setting`,
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
                    `mobile_default_card_tab.mobile_default_card_tab_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_card_name_label.mobile_card_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_card_number_label.mobile_card_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_expiry_date_label.mobile_expiry_date_label_${language.id}`
                ) ||
                validationErros.has(
                    `delete_card_button_text.delete_card_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `add_new_card_button_text.add_new_card_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `no_payment_message.no_payment_message_${language.id}`
                ) ||
                validationErros.has(
                    `set_primary_card_label.set_primary_card_label_${language.id}`
                )
            );
        },
    },
};
</script>
