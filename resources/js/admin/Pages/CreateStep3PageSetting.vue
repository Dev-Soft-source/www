<template>
    <AppLayout>
        <section class="step3-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Step 3 of 5 page settings
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
                            title="Step 3 of 5 page settings"
                            mode="all_languages"
                            download-endpoint="download-step3-page-setting-template"
                            upload-endpoint="upload-step3-page-setting-excel"
                            @success="onStep3ExcelSuccess"
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
                                                            $event.target.value,
                                                            language,
                                                            'main_label'
                                                        )
                                                    "
                                                    
                                                ></editor>
                                                <!-- <input
                                                    type="text"
                                                    :name="`main_label_${activeLanguageId}`"
                                                    :id="`main_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'main_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'main_label'
                                                        )
                                                    "
                                                /> -->
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
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`required_label_${activeLanguageId}`"
                                                        >Required label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`required_label_${activeLanguageId}`"
                                                    :id="`required_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'required_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'required_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `required_label.required_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `required_label.required_label_${activeLanguageId}`
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
                                                        :for="`make_label_${activeLanguageId}`"
                                                        >Make label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`make_label_${activeLanguageId}`"
                                                    :id="`make_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'make_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'make_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `make_label.make_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `make_label.make_label_${activeLanguageId}`
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
                                                        :for="`make_error_${activeLanguageId}`"
                                                        >Make error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`make_error_${activeLanguageId}`"
                                                    :id="`make_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'make_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'make_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `make_error.make_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `make_error.make_error_${activeLanguageId}`
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
                                                        :for="`make_placeholder_${activeLanguageId}`"
                                                        >Make placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`make_placeholder_${activeLanguageId}`"
                                                    :id="`make_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'make_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'make_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `make_placeholder.make_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `make_placeholder.make_placeholder_${activeLanguageId}`
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
                                                        :for="`model_label_${activeLanguageId}`"
                                                        >Model label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`model_label_${activeLanguageId}`"
                                                    :id="`model_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'model_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'model_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `model_label.model_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `model_label.model_label_${activeLanguageId}`
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
                                                        :for="`model_error_${activeLanguageId}`"
                                                        >Model error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`model_error_${activeLanguageId}`"
                                                    :id="`model_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'model_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'model_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `model_error.model_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `model_error.model_error_${activeLanguageId}`
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
                                                        :for="`model_placeholder_${activeLanguageId}`"
                                                        >Model
                                                        placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`model_placeholder_${activeLanguageId}`"
                                                    :id="`model_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'model_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'model_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `model_placeholder.model_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `model_placeholder.model_placeholder_${activeLanguageId}`
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
                                                        :for="`vehicle_type_label_${activeLanguageId}`"
                                                        >Vehicle type
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_type_label_${activeLanguageId}`"
                                                    :id="`vehicle_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_type_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_type_label.vehicle_type_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_type_label.vehicle_type_label_${activeLanguageId}`
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
                                                        :for="`vehicle_type_error_${activeLanguageId}`"
                                                        >Vehicle type
                                                        error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_type_error_${activeLanguageId}`"
                                                    :id="`vehicle_type_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_type_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_type_error.vehicle_type_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_type_error.vehicle_type_error_${activeLanguageId}`
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
                                                        :for="`color_label_${activeLanguageId}`"
                                                        >Color label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`color_label_${activeLanguageId}`"
                                                    :id="`color_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'color_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'color_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `color_label.color_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `color_label.color_label_${activeLanguageId}`
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
                                                        :for="`color_error_${activeLanguageId}`"
                                                        >Color error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`color_error_${activeLanguageId}`"
                                                    :id="`color_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'color_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'color_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `color_error.color_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `color_error.color_error_${activeLanguageId}`
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
                                                        :for="`license_label_${activeLanguageId}`"
                                                        >License label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`license_label_${activeLanguageId}`"
                                                    :id="`license_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'license_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'license_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `license_label.license_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `license_label.license_label_${activeLanguageId}`
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
                                                        :for="`license_error_${activeLanguageId}`"
                                                        >License error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`license_error_${activeLanguageId}`"
                                                    :id="`license_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'license_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'license_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `license_error.license_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `license_error.license_error_${activeLanguageId}`
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
                                                        :for="`year_label_${activeLanguageId}`"
                                                        >Year label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`year_label_${activeLanguageId}`"
                                                    :id="`year_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'year_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'year_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `year_label.year_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `year_label.year_label_${activeLanguageId}`
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
                                                        :for="`year_error_${activeLanguageId}`"
                                                        >Year error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`year_error_${activeLanguageId}`"
                                                    :id="`year_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'year_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'year_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `year_error.year_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `year_error.year_error_${activeLanguageId}`
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
                                                        :for="`fuel_label_${activeLanguageId}`"
                                                        >Fuel label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`fuel_label_${activeLanguageId}`"
                                                    :id="`fuel_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'fuel_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'fuel_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `fuel_label.fuel_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `fuel_label.fuel_label_${activeLanguageId}`
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
                                                        :for="`fuel_error_${activeLanguageId}`"
                                                        >Fuel error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`fuel_error_${activeLanguageId}`"
                                                    :id="`fuel_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'fuel_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'fuel_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `fuel_error.fuel_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `fuel_error.fuel_error_${activeLanguageId}`
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
                                                        :for="`electric_option_label_${activeLanguageId}`"
                                                        >Electric option
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`electric_option_label_${activeLanguageId}`"
                                                    :id="`electric_option_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'electric_option_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'electric_option_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `electric_option_label.electric_option_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `electric_option_label.electric_option_label_${activeLanguageId}`
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
                                                        :for="`hybrid_option_label_${activeLanguageId}`"
                                                        >Hybrid option
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`hybrid_option_label_${activeLanguageId}`"
                                                    :id="`hybrid_option_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'hybrid_option_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hybrid_option_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `hybrid_option_label.hybrid_option_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `hybrid_option_label.hybrid_option_label_${activeLanguageId}`
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
                                                        :for="`gas_option_label_${activeLanguageId}`"
                                                        >Gas option label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`gas_option_label_${activeLanguageId}`"
                                                    :id="`gas_option_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'gas_option_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'gas_option_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `gas_option_label.gas_option_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `gas_option_label.gas_option_label_${activeLanguageId}`
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
                                                        :for="`driver_license_label_${activeLanguageId}`"
                                                        >Driver license
                                                        label</label
                                                    >
                                                </div>
                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`driver_license_label_${activeLanguageId}`"
                                                    v-model="form.driver_license_label[`driver_license_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`driver_license_label_${activeLanguageId}`"
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_license_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_license_label'
                                                        )
                                                    "
                                                    
                                                ></editor>
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_license_label.driver_license_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_license_label.driver_license_label_${activeLanguageId}`
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
                                                        :for="`driver_license_error_${activeLanguageId}`"
                                                        >Driver license
                                                        error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_license_error_${activeLanguageId}`"
                                                    :id="`driver_license_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_license_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_license_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_license_error.driver_license_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_license_error.driver_license_error_${activeLanguageId}`
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
                                                        :for="`driver_license_sub_label_${activeLanguageId}`"
                                                        >Driver license sub
                                                        label</label
                                                    >
                                                </div>
                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`driver_license_sub_label_${activeLanguageId}`"
                                                    v-model="form.driver_license_sub_label[`driver_license_sub_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`driver_license_sub_label_${activeLanguageId}`"
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_license_sub_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_license_sub_label'
                                                        )
                                                    "
                                                    
                                                ></editor>
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_license_sub_label.driver_license_sub_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_license_sub_label.driver_license_sub_label_${activeLanguageId}`
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
                                                        :for="`mobile_driver_choose_file_label_${activeLanguageId}`"
                                                        >Choose file label
                                                        (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_driver_choose_file_label_${activeLanguageId}`"
                                                    :id="`mobile_driver_choose_file_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_driver_choose_file_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_driver_choose_file_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_driver_choose_file_label.mobile_driver_choose_file_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_driver_choose_file_label.mobile_driver_choose_file_label_${activeLanguageId}`
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
                                                        :for="`photo_label_${activeLanguageId}`"
                                                        >Photo label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`photo_label_${activeLanguageId}`"
                                                    :id="`photo_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'photo_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'photo_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `photo_label.photo_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `photo_label.photo_label_${activeLanguageId}`
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
                                                        :for="`photo_error_${activeLanguageId}`"
                                                        >Photo error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`photo_error_${activeLanguageId}`"
                                                    :id="`photo_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'photo_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'photo_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `photo_error.photo_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `photo_error.photo_error_${activeLanguageId}`
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
                                                        :for="`photo_detail_label_${activeLanguageId}`"
                                                        >Photo sub label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`photo_detail_label_${activeLanguageId}`"
                                                    :id="`photo_detail_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'photo_detail_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'photo_detail_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `photo_detail_label.photo_detail_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `photo_detail_label.photo_detail_label_${activeLanguageId}`
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
                                                        :for="`mobile_photo_choose_file_label_${activeLanguageId}`"
                                                        >Choose file
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_photo_choose_file_label_${activeLanguageId}`"
                                                    :id="`mobile_photo_choose_file_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_photo_choose_file_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_photo_choose_file_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_photo_choose_file_label.mobile_photo_choose_file_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_photo_choose_file_label.mobile_photo_choose_file_label_${activeLanguageId}`
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
                                                        :for="`skip_button_label_${activeLanguageId}`"
                                                        >Skip button
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_button_label_${activeLanguageId}`"
                                                    :id="`skip_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'skip_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'skip_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `skip_button_label.skip_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `skip_button_label.skip_button_label_${activeLanguageId}`
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
                                                        :for="`skip_vehicle_info_${activeLanguageId}`"
                                                        >Skip vehicle
                                                        information button
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_vehicle_info_${activeLanguageId}`"
                                                    :id="`skip_vehicle_info_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'skip_vehicle_info'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'skip_vehicle_info'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `skip_vehicle_info.skip_vehicle_info_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `skip_vehicle_info.skip_vehicle_info_${activeLanguageId}`
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
                                                        :for="`skip_license_${activeLanguageId}`"
                                                        >Skip license button
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_license_${activeLanguageId}`"
                                                    :id="`skip_license_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'skip_license'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'skip_license'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `skip_license.skip_license_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `skip_license.skip_license_${activeLanguageId}`
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
                                                        :for="`next_button_label_${activeLanguageId}`"
                                                        >Add vehicle button
                                                        label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`next_button_label_${activeLanguageId}`"
                                                    :id="`next_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'next_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'next_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `next_button_label.next_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `next_button_label.next_button_label_${activeLanguageId}`
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
                                                        :for="`vehicle_type_placeholder_${activeLanguageId}`"
                                                        >Vehicle Type
                                                        Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    :id="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_type_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}`
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
                                                        :for="`logout_button_label_${activeLanguageId}`"
                                                        >Logout button label
                                                        (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`logout_button_label_${activeLanguageId}`"
                                                    :id="`logout_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'logout_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'logout_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `logout_button_label.logout_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `logout_button_label.logout_button_label_${activeLanguageId}`
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
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`sub_main_label_${activeLanguageId}`"
                                                        >Sub main label</label
                                                    >
                                                </div>


                                                <editor
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :id="`sub_main_label_${activeLanguageId}`"
                                                    v-model="form.sub_main_label[`sub_main_label_${activeLanguageId}`]"
                                                    :init="editorConfig"
                                                    placeholder=" "
                                                    :name="`sub_main_label_${activeLanguageId}`"
                                                    :value="
                                                        getCurrentValue(
                                                            'sub_main_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'sub_main_label'
                                                        )
                                                    "
                                                ></editor>
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `sub_main_label.sub_main_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `sub_main_label.sub_main_label_${activeLanguageId}`
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
                                                        :for="`liecense_section_heading_${activeLanguageId}`"
                                                        >Liecense section
                                                        heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`liecense_section_heading_${activeLanguageId}`"
                                                    :id="`liecense_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'liecense_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'liecense_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `liecense_section_heading.liecense_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `liecense_section_heading.liecense_section_heading_${activeLanguageId}`
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
                                                        :for="`vehicle_section_heading_${activeLanguageId}`"
                                                        >Vehicle section
                                                        heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_section_heading_${activeLanguageId}`"
                                                    :id="`vehicle_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_section_heading.vehicle_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_section_heading.vehicle_section_heading_${activeLanguageId}`
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
        onStep3ExcelSuccess() {
            this.fetchStep3PageSetting();
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
                            this.handleInput("", language, "main_label");
                            this.handleInput("", language, "required_label");
                            this.handleInput("", language, "make_label");
                            this.handleInput("", language, "make_error");
                            this.handleInput("", language, "make_placeholder");
                            this.handleInput("", language, "model_label");
                            this.handleInput("", language, "model_error");
                            this.handleInput("", language, "model_placeholder");
                            this.handleInput(
                                "",
                                language,
                                "vehicle_type_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "vehicle_type_error"
                            );
                            this.handleInput("", language, "color_label");
                            this.handleInput("", language, "color_error");
                            this.handleInput("", language, "license_label");
                            this.handleInput("", language, "license_error");
                            this.handleInput("", language, "year_label");
                            this.handleInput("", language, "year_error");
                            this.handleInput("", language, "fuel_label");
                            this.handleInput("", language, "fuel_error");
                            this.handleInput(
                                "",
                                language,
                                "electric_option_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "hybrid_option_label"
                            );
                            this.handleInput("", language, "gas_option_label");
                            this.handleInput(
                                "",
                                language,
                                "driver_license_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "driver_license_error"
                            );
                            // this.handleInput(
                            //     "",
                            //     language,
                            //     "driver_license_sub_label"
                            // );
                            this.handleInput(
                                "",
                                language,
                                "mobile_driver_choose_file_label"
                            );
                            this.handleInput("", language, "photo_label");
                            this.handleInput("", language, "photo_error");
                            this.handleInput(
                                "",
                                language,
                                "photo_detail_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "mobile_photo_choose_file_label"
                            );
                            this.handleInput("", language, "skip_button_label");
                            this.handleInput("", language, "skip_license");
                            this.handleInput("", language, "skip_vehicle_info");
                            this.handleInput("", language, "next_button_label");
                            this.handleInput(
                                "",
                                language,
                                "logout_button_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "vehicle_type_placeholder"
                            );
                            this.handleInput("", language, "sub_heading");
                            this.handleInput("", language, "sub_main_label");
                            this.handleInput(
                                "",
                                language,
                                "liecense_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "vehicle_section_heading"
                            );
                        });
                        this.fetchStep3PageSetting();
                    }
                });
        },
        fetchStep3PageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-step3-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let step3_page_setting_detail =
                            res?.data?.data?.step3_page_setting_detail || [];
                        step3_page_setting_detail.map((setting) => {
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
                                setting?.main_label,
                                setting?.language,
                                "main_label"
                            );
                            this.handleInput(
                                setting?.required_label,
                                setting?.language,
                                "required_label"
                            );
                            this.handleInput(
                                setting?.make_label,
                                setting?.language,
                                "make_label"
                            );
                            this.handleInput(
                                setting?.make_error,
                                setting?.language,
                                "make_error"
                            );
                            this.handleInput(
                                setting?.make_placeholder,
                                setting?.language,
                                "make_placeholder"
                            );
                            this.handleInput(
                                setting?.model_label,
                                setting?.language,
                                "model_label"
                            );
                            this.handleInput(
                                setting?.model_error,
                                setting?.language,
                                "model_error"
                            );
                            this.handleInput(
                                setting?.model_placeholder,
                                setting?.language,
                                "model_placeholder"
                            );
                            this.handleInput(
                                setting?.vehicle_type_label,
                                setting?.language,
                                "vehicle_type_label"
                            );
                            this.handleInput(
                                setting?.vehicle_type_error,
                                setting?.language,
                                "vehicle_type_error"
                            );
                            this.handleInput(
                                setting?.color_label,
                                setting?.language,
                                "color_label"
                            );
                            this.handleInput(
                                setting?.color_error,
                                setting?.language,
                                "color_error"
                            );
                            this.handleInput(
                                setting?.license_label,
                                setting?.language,
                                "license_label"
                            );
                            this.handleInput(
                                setting?.license_error,
                                setting?.language,
                                "license_error"
                            );
                            this.handleInput(
                                setting?.year_label,
                                setting?.language,
                                "year_label"
                            );
                            this.handleInput(
                                setting?.year_error,
                                setting?.language,
                                "year_error"
                            );
                            this.handleInput(
                                setting?.fuel_label,
                                setting?.language,
                                "fuel_label"
                            );
                            this.handleInput(
                                setting?.fuel_error,
                                setting?.language,
                                "fuel_error"
                            );
                            this.handleInput(
                                setting?.electric_option_label,
                                setting?.language,
                                "electric_option_label"
                            );
                            this.handleInput(
                                setting?.hybrid_option_label,
                                setting?.language,
                                "hybrid_option_label"
                            );
                            this.handleInput(
                                setting?.gas_option_label,
                                setting?.language,
                                "gas_option_label"
                            );
                            this.handleInput(
                                setting?.driver_license_label,
                                setting?.language,
                                "driver_license_label"
                            );
                            this.handleInput(
                                setting?.driver_license_error,
                                setting?.language,
                                "driver_license_error"
                            );
                            this.handleInput(
                                setting?.mobile_driver_choose_file_label,
                                setting?.language,
                                "mobile_driver_choose_file_label"
                            );
                            this.handleInput(
                                setting?.photo_label,
                                setting?.language,
                                "photo_label"
                            );
                            this.handleInput(
                                setting?.photo_error,
                                setting?.language,
                                "photo_error"
                            );
                            this.handleInput(
                                setting?.photo_detail_label,
                                setting?.language,
                                "photo_detail_label"
                            );
                            this.handleInput(
                                setting?.mobile_photo_choose_file_label,
                                setting?.language,
                                "mobile_photo_choose_file_label"
                            );
                            this.handleInput(
                                setting?.skip_button_label,
                                setting?.language,
                                "skip_button_label"
                            );
                            this.handleInput(
                                setting?.skip_vehicle_info,
                                setting?.language,
                                "skip_vehicle_info"
                            );
                            this.handleInput(
                                setting?.skip_license,
                                setting?.language,
                                "skip_license"
                            );
                            this.handleInput(
                                setting?.next_button_label,
                                setting?.language,
                                "next_button_label"
                            );
                            this.handleInput(
                                setting?.logout_button_label,
                                setting?.language,
                                "logout_button_label"
                            );
                            this.handleInput(
                                setting?.vehicle_type_placeholder,
                                setting?.language,
                                "vehicle_type_placeholder"
                            );
                            this.handleInput(
                                setting?.sub_heading,
                                setting?.language,
                                "sub_heading"
                            );
                            this.handleInput(
                                setting?.sub_main_label,
                                setting?.language,
                                "sub_main_label"
                            );
                            this.handleInput(
                                setting?.liecense_section_heading,
                                setting?.language,
                                "liecense_section_heading"
                            );
                            this.handleInput(
                                setting?.vehicle_section_heading,
                                setting?.language,
                                "vehicle_section_heading"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-step3-page-setting`,
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
                validationErros.has(`main_label.main_label_${language.id}`) ||
                validationErros.has(
                    `required_label.required_label_${language.id}`
                ) ||
                validationErros.has(`make_label.make_label_${language.id}`) ||
                validationErros.has(`make_error.make_error_${language.id}`) ||
                validationErros.has(
                    `make_placeholder.make_placeholder_${language.id}`
                ) ||
                validationErros.has(`model_label.model_label_${language.id}`) ||
                validationErros.has(`model_error.model_error_${language.id}`) ||
                validationErros.has(
                    `model_placeholder.model_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_type_label.vehicle_type_label_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_type_error.vehicle_type_error_${language.id}`
                ) ||
                validationErros.has(`color_label.color_label_${language.id}`) ||
                validationErros.has(`color_error.color_error_${language.id}`) ||
                validationErros.has(
                    `license_label.license_label_${language.id}`
                ) ||
                validationErros.has(
                    `license_error.license_error_${language.id}`
                ) ||
                validationErros.has(`year_label.year_label_${language.id}`) ||
                validationErros.has(`year_error.year_error_${language.id}`) ||
                validationErros.has(`fuel_label.fuel_label_${language.id}`) ||
                validationErros.has(`fuel_error.fuel_error_${language.id}`) ||
                validationErros.has(
                    `electric_option_label.electric_option_label_${language.id}`
                ) ||
                validationErros.has(
                    `hybrid_option_label.hybrid_option_label_${language.id}`
                ) ||
                validationErros.has(
                    `gas_option_label.gas_option_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_license_label.driver_license_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_license_error.driver_license_error_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_driver_choose_file_label.mobile_driver_choose_file_label_${language.id}`
                ) ||
                validationErros.has(`photo_label.photo_label_${language.id}`) ||
                validationErros.has(`photo_error.photo_error_${language.id}`) ||
                validationErros.has(
                    `photo_detail_label.photo_detail_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_photo_choose_file_label.mobile_photo_choose_file_label_${language.id}`
                ) ||
                validationErros.has(
                    `next_button_label.next_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `logout_button_label.logout_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `skip_button_label.skip_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `skip_license.skip_license_${language.id}`
                ) ||
                validationErros.has(
                    `skip_vehicle_info.skip_vehicle_info_${language.id}`
                ) ||
                validationErros.has(`sub_heading.sub_heading_${language.id}`) ||
                validationErros.has(
                    `sub_main_label.sub_main_label_${language.id}`
                ) ||
                validationErros.has(
                    `liecense_section_heading.liecense_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_section_heading.vehicle_section_heading_${language.id}`
                )
            );
        },
    },
};
</script>
