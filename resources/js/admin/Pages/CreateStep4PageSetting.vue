<template>
    <AppLayout>
        <section class="step4-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Step 4 of 5 Page Settings
                                </h3>
                            </div>
                        </div>
                    </header>

                    <!-- Excel Upload Section - all languages (download template + upload) -->
                    <ExcelBulkImport
                        title="Step 4 of 5 Page"
                        mode="all_languages"
                        download-endpoint="download-step4-page-setting-template"
                        upload-endpoint="upload-step4-page-setting-excel"
                        @success="fetchStep4PageSetting"
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
                                                ? 'bg-primary text-white'
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
                                        <h3 class="text-white">Main section</h3>
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
                                                    <label :for="`country_code_label_${activeLanguageId}`">Country code label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_code_label_${activeLanguageId}`"
                                                    :id="`country_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('country_code_label')"
                                                    @input="handleInput($event.target.value, language, 'country_code_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`country_code_label.country_code_label_${activeLanguageId}`)" v-text="validationErros.get(`country_code_label.country_code_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`country_code_error_${activeLanguageId}`">Country code error</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_code_error_${activeLanguageId}`"
                                                    :id="`country_code_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('country_code_error')"
                                                    @input="handleInput($event.target.value, language, 'country_code_error')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`country_code_error.country_code_error_${activeLanguageId}`)" v-text="validationErros.get(`country_code_error.country_code_error_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`phone_label_${activeLanguageId}`">Phone label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_label_${activeLanguageId}`"
                                                    :id="`phone_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('phone_label')"
                                                    @input="handleInput($event.target.value, language, 'phone_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`phone_label.phone_label_${activeLanguageId}`)" v-text="validationErros.get(`phone_label.phone_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`phone_error_${activeLanguageId}`">Phone error</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_error_${activeLanguageId}`"
                                                    :id="`phone_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('phone_error')"
                                                    @input="handleInput($event.target.value, language, 'phone_error')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`phone_error.phone_error_${activeLanguageId}`)" v-text="validationErros.get(`phone_error.phone_error_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`skip_button_label_${activeLanguageId}`">Skip button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_button_label_${activeLanguageId}`"
                                                    :id="`skip_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('skip_button_label')"
                                                    @input="handleInput($event.target.value, language, 'skip_button_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`skip_button_label.skip_button_label_${activeLanguageId}`)" v-text="validationErros.get(`skip_button_label.skip_button_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`skip_phone_number_label_${activeLanguageId}`">Skip phone number label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_phone_number_label_${activeLanguageId}`"
                                                    :id="`skip_phone_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('skip_phone_number_label')"
                                                    @input="handleInput($event.target.value, language, 'skip_phone_number_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`skip_phone_number_label.skip_phone_number_label_${activeLanguageId}`)" v-text="validationErros.get(`skip_phone_number_label.skip_phone_number_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`verify_button_label_${activeLanguageId}`">Verify button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_button_label_${activeLanguageId}`"
                                                    :id="`verify_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('verify_button_label')"
                                                    @input="handleInput($event.target.value, language, 'verify_button_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`verify_button_label.verify_button_label_${activeLanguageId}`)" v-text="validationErros.get(`verify_button_label.verify_button_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`verify_code_label_${activeLanguageId}`">Verify code label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_code_label_${activeLanguageId}`"
                                                    :id="`verify_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('verify_code_label')"
                                                    @input="handleInput($event.target.value, language, 'verify_code_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`verify_code_label.verify_code_label_${activeLanguageId}`)" v-text="validationErros.get(`verify_code_label.verify_code_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`enter_code_label_${activeLanguageId}`">Enter code label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`enter_code_label_${activeLanguageId}`"
                                                    :id="`enter_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('enter_code_label')"
                                                    @input="handleInput($event.target.value, language, 'enter_code_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`enter_code_label.enter_code_label_${activeLanguageId}`)" v-text="validationErros.get(`enter_code_label.enter_code_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`request_code_label_${activeLanguageId}`">Request code label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`request_code_label_${activeLanguageId}`"
                                                    :id="`request_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('request_code_label')"
                                                    @input="handleInput($event.target.value, language, 'request_code_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`request_code_label.request_code_label_${activeLanguageId}`)" v-text="validationErros.get(`request_code_label.request_code_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`second_label_${activeLanguageId}`">Second label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`second_label_${activeLanguageId}`"
                                                    :id="`second_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('second_label')"
                                                    @input="handleInput($event.target.value, language, 'second_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`second_label.second_label_${activeLanguageId}`)" v-text="validationErros.get(`second_label.second_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`save_button_label_${activeLanguageId}`">Save button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`save_button_label_${activeLanguageId}`"
                                                    :id="`save_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('save_button_label')"
                                                    @input="handleInput($event.target.value, language, 'save_button_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`save_button_label.save_button_label_${activeLanguageId}`)" v-text="validationErros.get(`save_button_label.save_button_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`send_button_label_${activeLanguageId}`">Send button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`send_button_label_${activeLanguageId}`"
                                                    :id="`send_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('send_button_label')"
                                                    @input="handleInput($event.target.value, language, 'send_button_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`send_button_label.send_button_label_${activeLanguageId}`)" v-text="validationErros.get(`send_button_label.send_button_label_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`logout_button_label_${activeLanguageId}`">Logout button label</label>
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`logout_button_label_${activeLanguageId}`"
                                                    :id="`logout_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('logout_button_label')"
                                                    @input="handleInput($event.target.value, language, 'logout_button_label')"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`logout_button_label.logout_button_label_${activeLanguageId}`)" v-text="validationErros.get(`logout_button_label.logout_button_label_${activeLanguageId}`)"></p>
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
            collapseStates: [true, true],
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
                            this.handleInput("", language, "country_code_label");
                            this.handleInput("", language, "country_code_error");
                            this.handleInput("", language, "phone_label");
                            this.handleInput("", language, "phone_error");
                            this.handleInput("", language, "skip_button_label");
                            this.handleInput("", language, "skip_phone_number_label");
                            this.handleInput("", language, "verify_button_label");
                            this.handleInput("", language, "verify_code_label");
                            this.handleInput("", language, "enter_code_label");
                            this.handleInput("", language, "request_code_label");
                            this.handleInput("", language, "second_label");
                            this.handleInput("", language, "save_button_label");
                            this.handleInput("", language, "send_button_label");
                            this.handleInput("", language, "logout_button_label");
                        });
                        this.fetchStep4PageSetting();
                    }
                });
        },
        fetchStep4PageSetting() {
            axios
                .get(`${this.mixAdminApiUrl}get-step4-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let step4_page_setting_detail =
                            res?.data?.data?.step4_page_setting_detail || [];
                        step4_page_setting_detail.map((setting) => {
                            const lang = setting?.language || { id: setting?.language_id };
                            this.handleInput(setting?.name ?? "", lang, "name");
                            this.handleInput(setting?.meta_keywords ?? "", lang, "meta_keywords");
                            this.handleInput(setting?.meta_description ?? "", lang, "meta_description");
                            this.handleInput(setting?.main_heading ?? "", lang, "main_heading");
                            this.handleInput(setting?.main_label ?? "", lang, "main_label");
                            this.handleInput(setting?.country_code_label ?? "", lang, "country_code_label");
                            this.handleInput(setting?.country_code_error ?? "", lang, "country_code_error");
                            this.handleInput(setting?.phone_label ?? "", lang, "phone_label");
                            this.handleInput(setting?.phone_error ?? "", lang, "phone_error");
                            this.handleInput(setting?.skip_button_label ?? "", lang, "skip_button_label");
                            this.handleInput(setting?.skip_phone_number_label ?? "", lang, "skip_phone_number_label");
                            this.handleInput(setting?.verify_button_label ?? "", lang, "verify_button_label");
                            this.handleInput(setting?.verify_code_label ?? "", lang, "verify_code_label");
                            this.handleInput(setting?.enter_code_label ?? "", lang, "enter_code_label");
                            this.handleInput(setting?.request_code_label ?? "", lang, "request_code_label");
                            this.handleInput(setting?.second_label ?? "", lang, "second_label");
                            this.handleInput(setting?.save_button_label ?? "", lang, "save_button_label");
                            this.handleInput(setting?.send_button_label ?? "", lang, "send_button_label");
                            this.handleInput(setting?.logout_button_label ?? "", lang, "logout_button_label");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(`${this.mixAdminApiUrl}update-step4-page-setting`, this.form)
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
                validationErros.has(`country_code_label.country_code_label_${id}`) ||
                validationErros.has(`country_code_error.country_code_error_${id}`) ||
                validationErros.has(`phone_label.phone_label_${id}`) ||
                validationErros.has(`phone_error.phone_error_${id}`) ||
                validationErros.has(`skip_button_label.skip_button_label_${id}`) ||
                validationErros.has(`skip_phone_number_label.skip_phone_number_label_${id}`) ||
                validationErros.has(`verify_button_label.verify_button_label_${id}`) ||
                validationErros.has(`verify_code_label.verify_code_label_${id}`) ||
                validationErros.has(`enter_code_label.enter_code_label_${id}`) ||
                validationErros.has(`request_code_label.request_code_label_${id}`) ||
                validationErros.has(`second_label.second_label_${id}`) ||
                validationErros.has(`save_button_label.save_button_label_${id}`) ||
                validationErros.has(`send_button_label.send_button_label_${id}`) ||
                validationErros.has(`logout_button_label.logout_button_label_${id}`)
            );
        },
    },
};
</script>
