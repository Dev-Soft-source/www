<template>
    <AppLayout>
        <section class="select-location-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Select location page settings (App)
                                </h3>
                            </div>
                        </div>
                    </header>
                    <form
                        class="px-4 md:px-6 lg:px-8"
                        @submit.prevent="updatePageSetting()"
                    >
                        <!-- Excel bulk import (all languages template) -->
                        <ExcelBulkImport
                            title="Select location page settings (App)"
                            mode="all_languages"
                            download-endpoint="download-select-location-page-setting-template"
                            upload-endpoint="upload-select-location-page-setting-excel"
                            @success="onSelectLocationExcelSuccess"
                        />
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
                                                    :for="`select_origin_label_${activeLanguageId}`"
                                                    >Select origin label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_origin_label_${activeLanguageId}`"
                                                :id="`select_origin_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'select_origin_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_origin_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_origin_label.select_origin_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_origin_label.select_origin_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`search_origin_label_${activeLanguageId}`"
                                                    >Search origin label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`search_origin_label_${activeLanguageId}`"
                                                :id="`search_origin_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'search_origin_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'search_origin_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `search_origin_label.search_origin_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `search_origin_label.search_origin_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_origin_label_${activeLanguageId}`"
                                                    >No origin label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_origin_label_${activeLanguageId}`"
                                                :id="`no_origin_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'no_origin_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_origin_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_origin_label.no_origin_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_origin_label.no_origin_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_destination_label_${activeLanguageId}`"
                                                    >Select destination label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_destination_label_${activeLanguageId}`"
                                                :id="`select_destination_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'select_destination_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_destination_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_destination_label.select_destination_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_destination_label.select_destination_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`search_destination_label_${activeLanguageId}`"
                                                    >Search destination label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`search_destination_label_${activeLanguageId}`"
                                                :id="`search_destination_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'search_destination_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'search_destination_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `search_destination_label.search_destination_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `search_destination_label.search_destination_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_destination_label_${activeLanguageId}`"
                                                    >No destination label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_destination_label_${activeLanguageId}`"
                                                :id="`no_destination_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'no_destination_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_destination_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_destination_label.no_destination_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_destination_label.no_destination_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_country_label_${activeLanguageId}`"
                                                    >Select country label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_country_label_${activeLanguageId}`"
                                                :id="`select_country_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'select_country_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_country_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_country_label.select_country_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_country_label.select_country_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`search_country_label_${activeLanguageId}`"
                                                    >Search country label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`search_country_label_${activeLanguageId}`"
                                                :id="`search_country_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'search_country_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'search_country_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `search_country_label.search_country_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `search_country_label.search_country_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_country_label_${activeLanguageId}`"
                                                    >No country label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_country_label_${activeLanguageId}`"
                                                :id="`no_country_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'no_country_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_country_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_country_label.no_country_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_country_label.no_country_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_state_label_${activeLanguageId}`"
                                                    >Select state label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_state_label_${activeLanguageId}`"
                                                :id="`select_state_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'select_state_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_state_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_state_label.select_state_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_state_label.select_state_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`search_state_label_${activeLanguageId}`"
                                                    >Search state label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`search_state_label_${activeLanguageId}`"
                                                :id="`search_state_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'search_state_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'search_state_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `search_state_label.search_state_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `search_state_label.search_state_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_state_label_${activeLanguageId}`"
                                                    >No state label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_state_label_${activeLanguageId}`"
                                                :id="`no_state_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'no_state_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_state_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_state_label.no_state_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_state_label.no_state_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_city_label_${activeLanguageId}`"
                                                    >Select city label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_city_label_${activeLanguageId}`"
                                                :id="`select_city_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'select_city_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_city_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_city_label.select_city_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_city_label.select_city_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`search_city_label_${activeLanguageId}`"
                                                    >Search city label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`search_city_label_${activeLanguageId}`"
                                                :id="`search_city_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'search_city_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'search_city_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `search_city_label.search_city_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `search_city_label.search_city_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_city_label_${activeLanguageId}`"
                                                    >No city label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_city_label_${activeLanguageId}`"
                                                :id="`no_city_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'no_city_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_city_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_city_label.no_city_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_city_label.no_city_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_state_first_label_${activeLanguageId}`"
                                                    >Please select a province / state first</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_state_first_label_${activeLanguageId}`"
                                                :id="`select_state_first_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'select_state_first_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_state_first_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_state_first_label.select_state_first_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_state_first_label.select_state_first_label_${activeLanguageId}`
                                                )
                                            "
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
            </main>
        </section>
    </AppLayout>
</template>
<script>
import Editor from "@tinymce/tinymce-vue";
import ExcelBulkImport from "../components/ExcelBulkImport.vue";
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
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor image code table",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table paste code help wordcount",
                ],
                toolbar:
                    "undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | table | image | code | help",
            },
        };
    },
    components: {
        editor: Editor,
        ExcelBulkImport,
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
        onSelectLocationExcelSuccess() {
            this.fetchTermsOfUsePageSetting();
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
                            this.handleInput("", language, "select_origin_label");
                            this.handleInput("", language, "search_origin_label");
                            this.handleInput("", language, "no_origin_label");
                            this.handleInput("", language, "select_destination_label");
                            this.handleInput("", language, "search_destination_label");
                            this.handleInput("", language, "no_destination_label");
                            this.handleInput("", language, "select_country_label");
                            this.handleInput("", language, "search_country_label");
                            this.handleInput("", language, "no_country_label");
                            this.handleInput("", language, "select_state_label");
                            this.handleInput("", language, "select_state_first_label");
                            this.handleInput("", language, "search_state_label");
                            this.handleInput("", language, "no_state_label");
                            this.handleInput("", language, "select_city_label");
                            this.handleInput("", language, "search_city_label");
                            this.handleInput("", language, "no_city_label");
                        });
                        this.fetchTermsOfUsePageSetting();
                    }
                });
        },
        fetchTermsOfUsePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-select-location-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let select_location_page_setting_detail =
                            res?.data?.data?.select_location_page_setting_detail || [];
                        select_location_page_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.select_origin_label,
                                setting?.language,
                                "select_origin_label"
                            );
                            this.handleInput(
                                setting?.search_origin_label,
                                setting?.language,
                                "search_origin_label"
                            );
                            this.handleInput(
                                setting?.no_origin_label,
                                setting?.language,
                                "no_origin_label"
                            );
                            this.handleInput(
                                setting?.select_destination_label,
                                setting?.language,
                                "select_destination_label"
                            );
                            this.handleInput(
                                setting?.search_destination_label,
                                setting?.language,
                                "search_destination_label"
                            );
                            this.handleInput(
                                setting?.no_destination_label,
                                setting?.language,
                                "no_destination_label"
                            );
                            this.handleInput(
                                setting?.select_country_label,
                                setting?.language,
                                "select_country_label"
                            );
                            this.handleInput(
                                setting?.search_country_label,
                                setting?.language,
                                "search_country_label"
                            );
                            this.handleInput(
                                setting?.no_country_label,
                                setting?.language,
                                "no_country_label"
                            );
                            this.handleInput(
                                setting?.select_state_label,
                                setting?.language,
                                "select_state_label"
                            );
                            this.handleInput(
                                setting?.select_state_first_label,
                                setting?.language,
                                "select_state_first_label"
                            );
                            this.handleInput(
                                setting?.search_state_label,
                                setting?.language,
                                "search_state_label"
                            );
                            this.handleInput(
                                setting?.no_state_label,
                                setting?.language,
                                "no_state_label"
                            );
                            this.handleInput(
                                setting?.select_city_label,
                                setting?.language,
                                "select_city_label"
                            );
                            this.handleInput(
                                setting?.search_city_label,
                                setting?.language,
                                "search_city_label"
                            );
                            this.handleInput(
                                setting?.no_city_label,
                                setting?.language,
                                "no_city_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-select-location-page-setting`,
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
                    `select_origin_label.select_origin_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_origin_label.no_origin_label_${language.id}`
                ) ||
                validationErros.has(
                    `select_destination_label.select_destination_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_destination_label.search_destination_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_destination_label.no_destination_label_${language.id}`
                ) ||
                validationErros.has(
                    `select_country_label.select_country_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_country_label.search_country_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_country_label.no_country_label_${language.id}`
                ) ||
                validationErros.has(
                    `select_state_label.select_state_label_${language.id}`
                ) ||
                validationErros.has(
                    `select_state_first_label.select_state_first_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_state_label.search_state_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_state_label.no_state_label_${language.id}`
                ) ||
                validationErros.has(
                    `select_city_label.select_city_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_city_label.search_city_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_city_label.no_city_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_origin_label.search_origin_label_${language.id}`
                )
            );
        },
    },
};
</script>
