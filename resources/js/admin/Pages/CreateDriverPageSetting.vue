<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Drivers page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <ExcelBulkImport
                            title="Drivers Page"
                            mode="all_languages"
                            download-endpoint="download-driver-page-setting-template"
                            upload-endpoint="upload-driver-page-setting-excel"
                            @success="fetchDriverPageSetting"
                        />
                    </div>

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

                                <!-- about section start -->
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
                                            About section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-y-4 md:gap-6"
                                        v-if="collapseStates[1]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
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
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`sub_heading_${activeLanguageId}`"
                                                        >Sub heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`sub_heading_${activeLanguageId}`"
                                                    :id="`sub_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'sub_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'sub_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `sub_heading.sub_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `sub_heading.sub_heading_${activeLanguageId}`
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
                                                        :for="`page_description_${activeLanguageId}`"
                                                        >Description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'page_description'
                                                        )
                                                    "
                                                    :ref="`page_description_${language.id}`"
                                                    :id="`page_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `page_description`
                                                        ][
                                                            `page_description_${language?.id}`
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
                                                        `page_description.page_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `page_description.page_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- about section end -->

                                <!-- driver info page section start -->
                                <div
                                    class="border rounded w-full mt-4"
                                    :class="
                                        collapseStates[2] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[2] =
                                                !collapseStates[2]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Driver info page (labels)
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>
                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-y-4 md:gap-6"
                                        v-if="collapseStates[2]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`driver_info_heading_${activeLanguageId}`">Driver info heading</label>
                                            </div>
                                            <input type="text" :name="`driver_info_heading_${activeLanguageId}`" :id="`driver_info_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Driver info" :value="getCurrentValue('driver_info_heading')" @input="handleInput($event.target.value, language, 'driver_info_heading')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`joined_label_${activeLanguageId}`">Joined label</label>
                                            </div>
                                            <input type="text" :name="`joined_label_${activeLanguageId}`" :id="`joined_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Joined" :value="getCurrentValue('joined_label')" @input="handleInput($event.target.value, language, 'joined_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`age_label_${activeLanguageId}`">Age label</label>
                                            </div>
                                            <input type="text" :name="`age_label_${activeLanguageId}`" :id="`age_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Age" :value="getCurrentValue('age_label')" @input="handleInput($event.target.value, language, 'age_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`mini_bio_heading_${activeLanguageId}`">Mini bio heading</label>
                                            </div>
                                            <input type="text" :name="`mini_bio_heading_${activeLanguageId}`" :id="`mini_bio_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Mini bio" :value="getCurrentValue('mini_bio_heading')" @input="handleInput($event.target.value, language, 'mini_bio_heading')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`passengers_driven_label_${activeLanguageId}`">Passengers driven label</label>
                                            </div>
                                            <input type="text" :name="`passengers_driven_label_${activeLanguageId}`" :id="`passengers_driven_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Passengers driven" :value="getCurrentValue('passengers_driven_label')" @input="handleInput($event.target.value, language, 'passengers_driven_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`rides_taken_label_${activeLanguageId}`">Rides taken label</label>
                                            </div>
                                            <input type="text" :name="`rides_taken_label_${activeLanguageId}`" :id="`rides_taken_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Rides taken" :value="getCurrentValue('rides_taken_label')" @input="handleInput($event.target.value, language, 'rides_taken_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`km_shared_label_${activeLanguageId}`">KM shared label</label>
                                            </div>
                                            <input type="text" :name="`km_shared_label_${activeLanguageId}`" :id="`km_shared_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. KM shared" :value="getCurrentValue('km_shared_label')" @input="handleInput($event.target.value, language, 'km_shared_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`vehicle_info_heading_${activeLanguageId}`">Vehicle info heading</label>
                                            </div>
                                            <input type="text" :name="`vehicle_info_heading_${activeLanguageId}`" :id="`vehicle_info_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Vehicle info" :value="getCurrentValue('vehicle_info_heading')" @input="handleInput($event.target.value, language, 'vehicle_info_heading')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`reviews_heading_${activeLanguageId}`">Reviews heading</label>
                                            </div>
                                            <input type="text" :name="`reviews_heading_${activeLanguageId}`" :id="`reviews_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Reviews" :value="getCurrentValue('reviews_heading')" @input="handleInput($event.target.value, language, 'reviews_heading')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`no_reviews_label_${activeLanguageId}`">No reviews label</label>
                                            </div>
                                            <input type="text" :name="`no_reviews_label_${activeLanguageId}`" :id="`no_reviews_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. No Reviews Yet" :value="getCurrentValue('no_reviews_label')" @input="handleInput($event.target.value, language, 'no_reviews_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex justify-between">
                                                <label :for="`see_all_reviews_btn_${activeLanguageId}`">See all reviews button</label>
                                            </div>
                                            <input type="text" :name="`see_all_reviews_btn_${activeLanguageId}`" :id="`see_all_reviews_btn_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. See all reviews" :value="getCurrentValue('see_all_reviews_btn')" @input="handleInput($event.target.value, language, 'see_all_reviews_btn')" />
                                        </div>
                                    </div>
                                </div>
                                <!-- driver info page section end -->
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
    components: {
        editor: Editor,
        ExcelBulkImport,
    },
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
    computed: {
        mixAdminApiUrl() { return process.env.MIX_ADMIN_API_URL; }
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
                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "main_heading");
                            this.handleInput(
                                "",
                                language,
                                "sub_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "page_description"
                            );
                            this.handleInput("", language, "driver_info_heading");
                            this.handleInput("", language, "joined_label");
                            this.handleInput("", language, "age_label");
                            this.handleInput("", language, "mini_bio_heading");
                            this.handleInput("", language, "passengers_driven_label");
                            this.handleInput("", language, "rides_taken_label");
                            this.handleInput("", language, "km_shared_label");
                            this.handleInput("", language, "vehicle_info_heading");
                            this.handleInput("", language, "reviews_heading");
                            this.handleInput("", language, "no_reviews_label");
                            this.handleInput("", language, "see_all_reviews_btn");
                        });
                        this.fetchDriverPageSetting();
                    }
                });
        },
        fetchDriverPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-driver-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let driver_page_setting_detail =
                            res?.data?.data?.driver_page_setting_detail || [];
                        driver_page_setting_detail.map((setting) => {
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
                                setting?.sub_heading,
                                setting?.language,
                                "sub_heading"
                            );
                            this.handleInput(
                                setting?.page_description,
                                setting?.language,
                                "page_description",
                            );
                            this.handleInput(setting?.driver_info_heading, setting?.language, "driver_info_heading");
                            this.handleInput(setting?.joined_label, setting?.language, "joined_label");
                            this.handleInput(setting?.age_label, setting?.language, "age_label");
                            this.handleInput(setting?.mini_bio_heading, setting?.language, "mini_bio_heading");
                            this.handleInput(setting?.passengers_driven_label, setting?.language, "passengers_driven_label");
                            this.handleInput(setting?.rides_taken_label, setting?.language, "rides_taken_label");
                            this.handleInput(setting?.km_shared_label, setting?.language, "km_shared_label");
                            this.handleInput(setting?.vehicle_info_heading, setting?.language, "vehicle_info_heading");
                            this.handleInput(setting?.reviews_heading, setting?.language, "reviews_heading");
                            this.handleInput(setting?.no_reviews_label, setting?.language, "no_reviews_label");
                            this.handleInput(setting?.see_all_reviews_btn, setting?.language, "see_all_reviews_btn");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-driver-page-setting`,
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
                    `sub_heading.sub_heading_${language.id}`
                ) ||
                validationErros.has(
                    `page_description.page_description_${language.id}`
                )
            );
        },
    },
};
</script>
