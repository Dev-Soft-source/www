<template>
    <AppLayout>
        <section class="signup-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Signup page settings
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
                            title="Signup page settings"
                            mode="all_languages"
                            download-endpoint="download-signup-page-setting-template"
                            upload-endpoint="upload-signup-page-setting-excel"
                            @success="onSignupExcelSuccess"
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
                                                        >Main heading (Web)</label
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
                                                        :for="`app_main_heading_${activeLanguageId}`"
                                                        >Main heading (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_main_heading_${activeLanguageId}`"
                                                    :id="`app_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_main_heading.app_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_main_heading.app_main_heading_${activeLanguageId}`
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
                                                        :for="`first_name_label_${activeLanguageId}`"
                                                        >First name label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_label_${activeLanguageId}`"
                                                    :id="`first_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_label.first_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_label.first_name_label_${activeLanguageId}`
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
                                                        :for="`first_name_error_${activeLanguageId}`"
                                                        >First name error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_error_${activeLanguageId}`"
                                                    :id="`first_name_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_error.first_name_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_error.first_name_error_${activeLanguageId}`
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
                                                        :for="`first_name_placeholder_${activeLanguageId}`"
                                                        >First name placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_placeholder_${activeLanguageId}`"
                                                    :id="`first_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_placeholder.first_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_placeholder.first_name_placeholder_${activeLanguageId}`
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
                                                        :for="`last_name_label_${activeLanguageId}`"
                                                        >Last name label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_label_${activeLanguageId}`"
                                                    :id="`last_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_label.last_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_label.last_name_label_${activeLanguageId}`
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
                                                        :for="`last_name_error_${activeLanguageId}`"
                                                        >Last name error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_error_${activeLanguageId}`"
                                                    :id="`last_name_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_error.last_name_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_error.last_name_error_${activeLanguageId}`
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
                                                        :for="`last_name_placeholder_${activeLanguageId}`"
                                                        >Last name placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_placeholder_${activeLanguageId}`"
                                                    :id="`last_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_placeholder.last_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_placeholder.last_name_placeholder_${activeLanguageId}`
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
                                                        :for="`email_error_${activeLanguageId}`"
                                                        >Email error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`email_error_${activeLanguageId}`"
                                                    :id="`email_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'email_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'email_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `email_error.email_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `email_error.email_error_${activeLanguageId}`
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
                                        </div> -->
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
                                                        :for="`password_error_${activeLanguageId}`"
                                                        >Password error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`password_error_${activeLanguageId}`"
                                                    :id="`password_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'password_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'password_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `password_error.password_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `password_error.password_error_${activeLanguageId}`
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
                                                        >Password tooltip</label
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
                                                        :for="`confirm_password_label_${activeLanguageId}`"
                                                        >Confirm password label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`confirm_password_label_${activeLanguageId}`"
                                                    :id="`confirm_password_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'confirm_password_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'confirm_password_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `confirm_password_label.confirm_password_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `confirm_password_label.confirm_password_label_${activeLanguageId}`
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
                                                        :for="`confirm_password_error_${activeLanguageId}`"
                                                        >Confirm password error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`confirm_password_error_${activeLanguageId}`"
                                                    :id="`confirm_password_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'confirm_password_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'confirm_password_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `confirm_password_error.confirm_password_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `confirm_password_error.confirm_password_error_${activeLanguageId}`
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
                                                        :for="`confirm_password_placeholder_${activeLanguageId}`"
                                                        >Password placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`confirm_password_placeholder_${activeLanguageId}`"
                                                    :id="`confirm_password_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'confirm_password_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'confirm_password_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `confirm_password_placeholder.confirm_password_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `confirm_password_placeholder.confirm_password_placeholder_${activeLanguageId}`
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
                                                        :for="`agree_terms_error_${activeLanguageId}`"
                                                        >Agree terms error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`agree_terms_error_${activeLanguageId}`"
                                                    :id="`agree_terms_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'agree_terms_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'agree_terms_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `agree_terms_error.agree_terms_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `agree_terms_error.agree_terms_error_${activeLanguageId}`
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
                                                        :for="`agree_terms_label_${activeLanguageId}`"
                                                        >Agree terms label</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'agree_terms_label'
                                                        )
                                                    "
                                                    :ref="`agree_terms_label_${language.id}`"
                                                    :id="`agree_terms_label_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `agree_terms_label`
                                                        ][
                                                            `agree_terms_label_${language?.id}`
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
                                                        `agree_terms_label.agree_terms_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `agree_terms_label.agree_terms_label_${activeLanguageId}`
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
                                                        :for="`app_agree_terms_part1_label_${activeLanguageId}`"
                                                        >Agree terms label part1 (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_agree_terms_part1_label_${activeLanguageId}`"
                                                    :id="`app_agree_terms_part1_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_agree_terms_part1_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_agree_terms_part1_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_agree_terms_part1_label.app_agree_terms_part1_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_agree_terms_part1_label.app_agree_terms_part1_label_${activeLanguageId}`
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
                                                        :for="`app_agree_terms_link1_label_${activeLanguageId}`"
                                                        >Agree terms link1 label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_agree_terms_link1_label_${activeLanguageId}`"
                                                    :id="`app_agree_terms_link1_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_agree_terms_link1_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_agree_terms_link1_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_agree_terms_link1_label.app_agree_terms_link1_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_agree_terms_link1_label.app_agree_terms_link1_label_${activeLanguageId}`
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
                                                        :for="`app_agree_terms_link2_label_${activeLanguageId}`"
                                                        >Agree terms link2 label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_agree_terms_link2_label_${activeLanguageId}`"
                                                    :id="`app_agree_terms_link2_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_agree_terms_link2_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_agree_terms_link2_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_agree_terms_link2_label.app_agree_terms_link2_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_agree_terms_link2_label.app_agree_terms_link2_label_${activeLanguageId}`
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
                                                        :for="`app_agree_terms_part2_label_${activeLanguageId}`"
                                                        >Agree terms label part2 (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_agree_terms_part2_label_${activeLanguageId}`"
                                                    :id="`app_agree_terms_part2_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_agree_terms_part2_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_agree_terms_part2_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_agree_terms_part2_label.app_agree_terms_part2_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_agree_terms_part2_label.app_agree_terms_part2_label_${activeLanguageId}`
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
                                                        :for="`app_agree_terms_link3_label_${activeLanguageId}`"
                                                        >Agree terms link3 label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_agree_terms_link3_label_${activeLanguageId}`"
                                                    :id="`app_agree_terms_link3_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_agree_terms_link3_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_agree_terms_link3_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_agree_terms_link3_label.app_agree_terms_link3_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_agree_terms_link3_label.app_agree_terms_link3_label_${activeLanguageId}`
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
                                                        :for="`app_agree_terms_part3_label_${activeLanguageId}`"
                                                        >Agree terms label part3 (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`app_agree_terms_part3_label_${activeLanguageId}`"
                                                    :id="`app_agree_terms_part3_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'app_agree_terms_part3_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'app_agree_terms_part3_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `app_agree_terms_part3_label.app_agree_terms_part3_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `app_agree_terms_part3_label.app_agree_terms_part3_label_${activeLanguageId}`
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
                                                        :for="`button_label_${activeLanguageId}`"
                                                        >Button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`button_label_${activeLanguageId}`"
                                                    :id="`button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `button_label.button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `button_label.button_label_${activeLanguageId}`
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
                                                        :for="`signin_label_${activeLanguageId}`"
                                                        >Signin label (Web)</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'signin_label'
                                                        )
                                                    "
                                                    :ref="`signin_label_${language.id}`"
                                                    :id="`signin_label_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `signin_label`
                                                        ][
                                                            `signin_label_${language?.id}`
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
                                                        `signin_label.signin_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `signin_label.signin_label_${activeLanguageId}`
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
                                                        :for="`no_account_label_${activeLanguageId}`"
                                                        >No account label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_account_label_${activeLanguageId}`"
                                                    :id="`no_account_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_account_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_account_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_account_label.no_account_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_account_label.no_account_label_${activeLanguageId}`
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
                                                        :for="`signin_link_label_${activeLanguageId}`"
                                                        >Signin link label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`signin_link_label_${activeLanguageId}`"
                                                    :id="`signin_link_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'signin_link_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'signin_link_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `signin_link_label.signin_link_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `signin_link_label.signin_link_label_${activeLanguageId}`
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
                                                        :for="`now_label_${activeLanguageId}`"
                                                        >Now label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`now_label_${activeLanguageId}`"
                                                    :id="`now_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'now_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'now_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `now_label.now_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `now_label.now_label_${activeLanguageId}`
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
                                                        :for="`language_label_${activeLanguageId}`"
                                                        >Language label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`language_label_${activeLanguageId}`"
                                                    :id="`language_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'language_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'language_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `language_label.language_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `language_label.language_label_${activeLanguageId}`
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
        onSignupExcelSuccess() {
            this.fetchSignupPageSetting();
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
                            this.handleInput("", language, "app_main_heading");
                            this.handleInput("", language, "or_label");
                            this.handleInput("", language, "required_label");
                            this.handleInput("", language, "first_name_label");
                            this.handleInput("", language, "first_name_error");
                            this.handleInput("", language, "first_name_placeholder");
                            this.handleInput("", language, "last_name_label");
                            this.handleInput("", language, "last_name_error");
                            this.handleInput("", language, "last_name_placeholder");
                            this.handleInput("", language, "email_label");
                            this.handleInput("", language, "email_error");
                            this.handleInput("", language, "email_placeholder");
                            this.handleInput("", language, "password_label");
                            this.handleInput("", language, "password_error");
                            this.handleInput("", language, "password_placeholder");
                            this.handleInput("", language, "confirm_password_label");
                            this.handleInput("", language, "confirm_password_error");
                            this.handleInput("", language, "confirm_password_placeholder");
                            this.handleInput("", language, "agree_terms_error");
                            this.handleInput("", language, "phone_number_label");
                            this.handleInput("", language, "phone_number_option1");
                            this.handleInput("", language, "phone_number_option2");
                            this.handleInput("", language, "agree_terms_label");
                            this.handleInput("", language, "app_agree_terms_part1_label");
                            this.handleInput("", language, "app_agree_terms_link1_label");
                            this.handleInput("", language, "app_agree_terms_link2_label");
                            this.handleInput("", language, "app_agree_terms_part2_label");
                            this.handleInput("", language, "app_agree_terms_link3_label");
                            this.handleInput("", language, "app_agree_terms_part3_label");
                            this.handleInput("", language, "button_label");
                            this.handleInput("", language, "after_button_label");
                            this.handleInput("", language, "signin_label");
                            this.handleInput("", language, "no_account_label");
                            this.handleInput("", language, "signin_link_label");
                            this.handleInput("", language, "now_label");
                            this.handleInput("", language, "language_label");
                        });
                        this.fetchSignupPageSetting();
                    }
                });
        },
        fetchSignupPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-signup-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let signup_page_setting_detail =
                            res?.data?.data?.signup_page_setting_detail || [];
                        signup_page_setting_detail.map((setting) => {
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
                                setting?.app_main_heading,
                                setting?.language,
                                "app_main_heading"
                            );
                            this.handleInput(
                                setting?.or_label,
                                setting?.language,
                                "or_label"
                            );
                            this.handleInput(
                                setting?.required_label,
                                setting?.language,
                                "required_label"
                            );
                            this.handleInput(
                                setting?.first_name_label,
                                setting?.language,
                                "first_name_label"
                            );
                            this.handleInput(
                                setting?.first_name_error,
                                setting?.language,
                                "first_name_error"
                            );
                            this.handleInput(
                                setting?.first_name_placeholder,
                                setting?.language,
                                "first_name_placeholder"
                            );
                            this.handleInput(
                                setting?.last_name_label,
                                setting?.language,
                                "last_name_label"
                            );
                            this.handleInput(
                                setting?.last_name_error,
                                setting?.language,
                                "last_name_error"
                            );
                            this.handleInput(
                                setting?.last_name_placeholder,
                                setting?.language,
                                "last_name_placeholder"
                            );
                            this.handleInput(
                                setting?.email_label,
                                setting?.language,
                                "email_label"
                            );
                            this.handleInput(
                                setting?.email_error,
                                setting?.language,
                                "email_error"
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
                                setting?.password_error,
                                setting?.language,
                                "password_error"
                            );
                            this.handleInput(
                                setting?.password_placeholder,
                                setting?.language,
                                "password_placeholder"
                            );
                            this.handleInput(
                                setting?.confirm_password_label,
                                setting?.language,
                                "confirm_password_label"
                            );
                            this.handleInput(
                                setting?.confirm_password_error,
                                setting?.language,
                                "confirm_password_error"
                            );
                            this.handleInput(
                                setting?.confirm_password_placeholder,
                                setting?.language,
                                "confirm_password_placeholder"
                            );
                            this.handleInput(
                                setting?.agree_terms_error,
                                setting?.language,
                                "agree_terms_error"
                            );
                            this.handleInput(
                                setting?.phone_number_label,
                                setting?.language,
                                "phone_number_label"
                            );
                            this.handleInput(
                                setting?.phone_number_option1,
                                setting?.language,
                                "phone_number_option1"
                            );
                            this.handleInput(
                                setting?.phone_number_option2,
                                setting?.language,
                                "phone_number_option2"
                            );
                            this.handleInput(
                                setting?.agree_terms_label,
                                setting?.language,
                                "agree_terms_label"
                            );
                            this.handleInput(
                                setting?.app_agree_terms_part1_label,
                                setting?.language,
                                "app_agree_terms_part1_label"
                            );
                            this.handleInput(
                                setting?.app_agree_terms_link1_label,
                                setting?.language,
                                "app_agree_terms_link1_label"
                            );
                            this.handleInput(
                                setting?.app_agree_terms_link2_label,
                                setting?.language,
                                "app_agree_terms_link2_label"
                            );
                            this.handleInput(
                                setting?.app_agree_terms_part2_label,
                                setting?.language,
                                "app_agree_terms_part2_label"
                            );
                            this.handleInput(
                                setting?.app_agree_terms_link3_label,
                                setting?.language,
                                "app_agree_terms_link3_label"
                            );
                            this.handleInput(
                                setting?.app_agree_terms_part3_label,
                                setting?.language,
                                "app_agree_terms_part3_label"
                            );
                            this.handleInput(
                                setting?.button_label,
                                setting?.language,
                                "button_label"
                            );
                            this.handleInput(
                                setting?.after_button_label,
                                setting?.language,
                                "after_button_label"
                            );
                            this.handleInput(
                                setting?.signin_label,
                                setting?.language,
                                "signin_label"
                            );
                            this.handleInput(
                                setting?.no_account_label,
                                setting?.language,
                                "no_account_label"
                            );
                            this.handleInput(
                                setting?.signin_link_label,
                                setting?.language,
                                "signin_link_label"
                            );
                            this.handleInput(
                                setting?.now_label,
                                setting?.language,
                                "now_label"
                            );
                            this.handleInput(
                                setting?.language_label,
                                setting?.language,
                                "language_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-signup-page-setting`,
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
                    `app_main_heading.app_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `or_label.or_label_${language.id}`
                ) ||
                validationErros.has(
                    `required_label.required_label_${language.id}`
                ) ||
                validationErros.has(
                    `first_name_label.first_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `first_name_error.first_name_error_${language.id}`
                ) ||
                validationErros.has(
                    `first_name_placeholder.first_name_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `last_name_label.last_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `last_name_error.last_name_error_${language.id}`
                ) ||
                validationErros.has(
                    `last_name_placeholder.last_name_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `email_label.email_label_${language.id}`
                ) ||
                validationErros.has(
                    `email_error.email_error_${language.id}`
                ) ||
                validationErros.has(
                    `email_placeholder.email_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `password_label.password_label_${language.id}`
                ) ||
                validationErros.has(
                    `password_error.password_error_${language.id}`
                ) ||
                validationErros.has(
                    `password_placeholder.password_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `confirm_password_label.confirm_password_label_${language.id}`
                ) ||
                validationErros.has(
                    `confirm_password_error.confirm_password_error_${language.id}`
                ) ||
                validationErros.has(
                    `confirm_password_placeholder.confirm_password_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `agree_terms_error.agree_terms_error_${language.id}`
                ) ||
                validationErros.has(
                    `phone_number_label.phone_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `phone_number_option1.phone_number_option1_${language.id}`
                ) ||
                validationErros.has(
                    `phone_number_option2.phone_number_option2_${language.id}`
                ) ||
                validationErros.has(
                    `agree_terms_label.agree_terms_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_agree_terms_part1_label.app_agree_terms_part1_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_agree_terms_link1_label.app_agree_terms_link1_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_agree_terms_link2_label.app_agree_terms_link2_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_agree_terms_part2_label.app_agree_terms_part2_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_agree_terms_link3_label.app_agree_terms_link3_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_agree_terms_part3_label.app_agree_terms_part3_label_${language.id}`
                ) ||
                validationErros.has(
                    `button_label.button_label_${language.id}`
                ) ||
                validationErros.has(
                    `after_button_label.after_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_account_label.no_account_label_${language.id}`
                ) ||
                validationErros.has(
                    `now_label.now_label_${language.id}`
                ) ||
                validationErros.has(
                    `language_label.language_label_${language.id}`
                ) ||
                validationErros.has(
                    `signin_link_label.signin_link_label_${language.id}`
                ) ||
                validationErros.has(
                    `signin_label.signin_label_${language.id}`
                )
            );
        },
    },
};
</script>
