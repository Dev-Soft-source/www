<template>
    <AppLayout>
        <section class="step4-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Step 5 of 5 page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <!-- Excel Upload Section - all languages (download template + upload) -->
                    <ExcelBulkImport
                        title="Step 5 of 5 Page"
                        mode="all_languages"
                        download-endpoint="download-step5-page-setting-template"
                        upload-endpoint="upload-step5-page-setting-excel"
                        @success="fetchStep5PageSetting"
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
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`main_label_${activeLanguageId}`"
                                                        >Main label</label
                                                    >
                                                </div>
                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`main_label_${activeLanguageId}`"
                                                    v-model="form.main_label[`main_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`main_label_${activeLanguageId}`"
                                                    :value="
                                                        getCurrentValue(
                                                            'main_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event,
                                                            language,
                                                            'main_label'
                                                        )
                                                    "
                                                ></editor>
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `main_label.main_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `main_label.main_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`sub_main_label_${activeLanguageId}`">Sub main label</label>
                                                </div>
                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`sub_main_label_${activeLanguageId}`"
                                                    v-model="form.sub_main_label[`sub_main_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`sub_main_label_${activeLanguageId}`"
                                                    :value="getCurrentValue('sub_main_label')"
                                                    @input="handleInput($event, language, 'sub_main_label')"
                                                ></editor>
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`sub_main_label.sub_main_label_${activeLanguageId}`)" v-text="validationErros.get(`sub_main_label.sub_main_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`required_label_${activeLanguageId}`">Required label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`required_label_${activeLanguageId}`"
                                                    :id="`required_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('required_label')"
                                                    @input="handleInput($event.target.value, language, 'required_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`required_label.required_label_${activeLanguageId}`)" v-text="validationErros.get(`required_label.required_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_license_label_${activeLanguageId}`">Driver license label</label>
                                                </div>
                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`driver_license_label_${activeLanguageId}`"
                                                    v-model="form.driver_license_label[`driver_license_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`driver_license_label_${activeLanguageId}`"
                                                    :value="getCurrentValue('driver_license_label')"
                                                    @input="handleInput($event, language, 'driver_license_label')"
                                                ></editor>
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`driver_license_label.driver_license_label_${activeLanguageId}`)" v-text="validationErros.get(`driver_license_label.driver_license_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_license_sub_label_${activeLanguageId}`">Driver license sub label</label>
                                                </div>
                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`driver_license_sub_label_${activeLanguageId}`"
                                                    v-model="form.driver_license_sub_label[`driver_license_sub_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`driver_license_sub_label_${activeLanguageId}`"
                                                    :value="getCurrentValue('driver_license_sub_label')"
                                                    @input="handleInput($event, language, 'driver_license_sub_label')"
                                                ></editor>
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`driver_license_sub_label.driver_license_sub_label_${activeLanguageId}`)" v-text="validationErros.get(`driver_license_sub_label.driver_license_sub_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_license_error_${activeLanguageId}`">Driver license error</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_license_error_${activeLanguageId}`"
                                                    :id="`driver_license_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('driver_license_error')"
                                                    @input="handleInput($event.target.value, language, 'driver_license_error')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`driver_license_error.driver_license_error_${activeLanguageId}`)" v-text="validationErros.get(`driver_license_error.driver_license_error_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`photo_detail_label_${activeLanguageId}`">Photo sub label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`photo_detail_label_${activeLanguageId}`"
                                                    :id="`photo_detail_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('photo_detail_label')"
                                                    @input="handleInput($event.target.value, language, 'photo_detail_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`photo_detail_label.photo_detail_label_${activeLanguageId}`)" v-text="validationErros.get(`photo_detail_label.photo_detail_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mobile_photo_choose_file_label_${activeLanguageId}`">Choose file label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_photo_choose_file_label_${activeLanguageId}`"
                                                    :id="`mobile_photo_choose_file_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('mobile_photo_choose_file_label')"
                                                    @input="handleInput($event.target.value, language, 'mobile_photo_choose_file_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`mobile_photo_choose_file_label.mobile_photo_choose_file_label_${activeLanguageId}`)" v-text="validationErros.get(`mobile_photo_choose_file_label.mobile_photo_choose_file_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`skip_license_${activeLanguageId}`">Skip license button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_license_${activeLanguageId}`"
                                                    :id="`skip_license_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('skip_license')"
                                                    @input="handleInput($event.target.value, language, 'skip_license')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`skip_license.skip_license_${activeLanguageId}`)" v-text="validationErros.get(`skip_license.skip_license_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`next_button_label_${activeLanguageId}`">Next button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`next_button_label_${activeLanguageId}`"
                                                    :id="`next_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('next_button_label')"
                                                    @input="handleInput($event.target.value, language, 'next_button_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`next_button_label.next_button_label_${activeLanguageId}`)" v-text="validationErros.get(`next_button_label.next_button_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`liecense_section_heading_${activeLanguageId}`">License section heading</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`liecense_section_heading_${activeLanguageId}`"
                                                    :id="`liecense_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('liecense_section_heading')"
                                                    @input="handleInput($event.target.value, language, 'liecense_section_heading')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`liecense_section_heading.liecense_section_heading_${activeLanguageId}`)" v-text="validationErros.get(`liecense_section_heading.liecense_section_heading_${activeLanguageId}`)"></p>
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
        ExcelBulkImport,
    },
    computed: {
        mixAdminApiUrl() {
            return process.env.MIX_ADMIN_API_URL || '/admin/';
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
                .get(`${this.mixAdminApiUrl}languages?getAll=1`)
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
                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "main_label");
                            this.handleInput("", language, "sub_main_label");
                            this.handleInput("", language, "required_label");
                            this.handleInput("", language, "driver_license_label");
                            this.handleInput("", language, "driver_license_sub_label");
                            this.handleInput("", language, "driver_license_error");
                            this.handleInput("", language, "photo_detail_label");
                            this.handleInput("", language, "mobile_photo_choose_file_label");
                            this.handleInput("", language, "skip_license");
                            this.handleInput("", language, "next_button_label");
                            this.handleInput("", language, "liecense_section_heading");
                        });
                        this.fetchStep5PageSetting();
                    }
                });
        },
        fetchStep5PageSetting() {
            axios
                .get(`${this.mixAdminApiUrl}get-step5-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let step5_page_setting_detail =
                            res?.data?.data?.step5_page_setting_detail || [];
                        step5_page_setting_detail.map((setting) => {
                            const lang = setting?.language || { id: setting?.language_id };
                            this.handleInput(setting?.name ?? "", lang, "name");
                            this.handleInput(setting?.meta_keywords ?? "", lang, "meta_keywords");
                            this.handleInput(setting?.meta_description ?? "", lang, "meta_description");
                            this.handleInput(setting?.main_heading ?? "", lang, "main_heading");
                            this.handleInput(setting?.main_label ?? "", lang, "main_label");
                            this.handleInput(setting?.sub_main_label ?? "", lang, "sub_main_label");
                            this.handleInput(setting?.required_label ?? "", lang, "required_label");
                            this.handleInput(setting?.driver_license_label ?? "", lang, "driver_license_label");
                            this.handleInput(setting?.driver_license_sub_label ?? "", lang, "driver_license_sub_label");
                            this.handleInput(setting?.driver_license_error ?? "", lang, "driver_license_error");
                            this.handleInput(setting?.photo_detail_label ?? "", lang, "photo_detail_label");
                            this.handleInput(setting?.mobile_photo_choose_file_label ?? "", lang, "mobile_photo_choose_file_label");
                            this.handleInput(setting?.skip_license ?? "", lang, "skip_license");
                            this.handleInput(setting?.next_button_label ?? "", lang, "next_button_label");
                            this.handleInput(setting?.liecense_section_heading ?? "", lang, "liecense_section_heading");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios.post(`${this.mixAdminApiUrl}update-step5-page-setting`, this.form)
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
            const id = language.id;
            return (
                validationErros.has(`name.name_${id}`) ||
                validationErros.has(`meta_keywords.meta_keywords_${id}`) ||
                validationErros.has(`meta_description.meta_description_${id}`) ||
                validationErros.has(`main_heading.main_heading_${id}`) ||
                validationErros.has(`main_label.main_label_${id}`) ||
                validationErros.has(`sub_main_label.sub_main_label_${id}`) ||
                validationErros.has(`required_label.required_label_${id}`) ||
                validationErros.has(`driver_license_label.driver_license_label_${id}`) ||
                validationErros.has(`driver_license_sub_label.driver_license_sub_label_${id}`) ||
                validationErros.has(`driver_license_error.driver_license_error_${id}`) ||
                validationErros.has(`photo_detail_label.photo_detail_label_${id}`) ||
                validationErros.has(`mobile_photo_choose_file_label.mobile_photo_choose_file_label_${id}`) ||
                validationErros.has(`skip_license.skip_license_${id}`) ||
                validationErros.has(`next_button_label.next_button_label_${id}`) ||
                validationErros.has(`liecense_section_heading.liecense_section_heading_${id}`)
            );
        },
    },
};
</script>
