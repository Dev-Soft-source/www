<template>
    <AppLayout>
        <section class="coffee-wall-section relative">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Coffee on wall page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <ExcelBulkImport
                            title="Coffee on Wall Page"
                            mode="all_languages"
                            download-endpoint="download-coffee-wall-page-setting-template"
                            upload-endpoint="upload-coffee-wall-page-setting-excel"
                            @success="fetchTermsOfUsePageSetting"
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
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="w-full">
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
                                            <div class="w-full">
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`required_field_label_${activeLanguageId}`"
                                                        >Indicate required field label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`required_field_label_${activeLanguageId}`"
                                                    :id="`required_field_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'required_field_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'required_field_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `required_field_label.required_field_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `required_field_label.required_field_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div
                                            class="relative z-0 w-full group col-span-2"
                                        >
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`main_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'main_text'
                                                        )
                                                    "
                                                    :ref="`main_text_${language.id}`"
                                                    :id="`main_text_${language.id}`"
                                                    :initial-value="
                                                        form[`main_text`][
                                                            `main_text_${language?.id}`
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
                                                        `main_text.main_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `main_text.main_text_${activeLanguageId}`
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
                                                        :for="`custom_amount_label_${activeLanguageId}`"
                                                        >Custom amount label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`custom_amount_label_${activeLanguageId}`"
                                                    :id="`custom_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'custom_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'custom_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `custom_amount_label.custom_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `custom_amount_label.custom_amount_label_${activeLanguageId}`
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
                                                        :for="`pay_button_label_${activeLanguageId}`"
                                                        >Pay button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pay_button_label_${activeLanguageId}`"
                                                    :id="`pay_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pay_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pay_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pay_button_label.pay_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pay_button_label.pay_button_label_${activeLanguageId}`
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
                                                        :for="`frequency_label_${activeLanguageId}`"
                                                        >Packages label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`frequency_label_${activeLanguageId}`"
                                                    :id="`frequency_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'frequency_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'frequency_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `frequency_label.frequency_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `frequency_label.frequency_label_${activeLanguageId}`
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
                                                        :for="`monthly_label_${activeLanguageId}`"
                                                        >Monthly label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`monthly_label_${activeLanguageId}`"
                                                    :id="`monthly_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'monthly_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'monthly_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `monthly_label.monthly_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `monthly_label.monthly_label_${activeLanguageId}`
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
                                                        :for="`quarterly_label_${activeLanguageId}`"
                                                        >One time label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`quarterly_label_${activeLanguageId}`"
                                                    :id="`quarterly_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'quarterly_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'quarterly_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `quarterly_label.quarterly_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `quarterly_label.quarterly_label_${activeLanguageId}`
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
                                                        :for="`semi_annually_label_${activeLanguageId}`"
                                                        >Weekly label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`semi_annually_label_${activeLanguageId}`"
                                                    :id="`semi_annually_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'semi_annually_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'semi_annually_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `semi_annually_label.semi_annually_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `semi_annually_label.semi_annually_label_${activeLanguageId}`
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
                                                        :for="`annually_label_${activeLanguageId}`"
                                                        >Make donation anonymous label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`annually_label_${activeLanguageId}`"
                                                    :id="`annually_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'annually_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'annually_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `annually_label.annually_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `annually_label.annually_label_${activeLanguageId}`
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
                                                        :for="`name_label_${activeLanguageId}`"
                                                        >Name label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`name_label_${activeLanguageId}`"
                                                    :id="`name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `name_label.name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `name_label.name_label_${activeLanguageId}`
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
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`phone_label_${activeLanguageId}`"
                                                        >Phone label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_label_${activeLanguageId}`"
                                                    :id="`phone_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'phone_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'phone_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `phone_label.phone_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_label.phone_label_${activeLanguageId}`
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
                                                        :for="`designation_label_${activeLanguageId}`"
                                                        >Designation label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`designation_label_${activeLanguageId}`"
                                                    :id="`designation_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'designation_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'designation_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `designation_label.designation_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `designation_label.designation_label_${activeLanguageId}`
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
                                                        :for="`designation_option1_${activeLanguageId}`"
                                                        >All (Designation option1)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`designation_option1_${activeLanguageId}`"
                                                    :id="`designation_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'designation_option1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'designation_option1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `designation_option1.designation_option1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `designation_option1.designation_option1_${activeLanguageId}`
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
                                                        :for="`designation_option2_${activeLanguageId}`"
                                                        >Students (Designation option2)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`designation_option2_${activeLanguageId}`"
                                                    :id="`designation_option2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'designation_option2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'designation_option2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `designation_option2.designation_option2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `designation_option2.designation_option2_${activeLanguageId}`
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
                                                        :for="`designation_option3_${activeLanguageId}`"
                                                        >Female passengers (Designation option3)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`designation_option3_${activeLanguageId}`"
                                                    :id="`designation_option3_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'designation_option3'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'designation_option3'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `designation_option3.designation_option3_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `designation_option3.designation_option3_${activeLanguageId}`
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
                                                        :for="`designation_option4_${activeLanguageId}`"
                                                        >Visible minorities (Designation option4)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`designation_option4_${activeLanguageId}`"
                                                    :id="`designation_option4_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'designation_option4'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'designation_option4'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `designation_option4.designation_option4_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `designation_option4.designation_option4_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div
                                            class="relative z-0 w-full group col-span-2"
                                        >
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`agree_terms_label_${activeLanguageId}`"
                                                        >Agree terms text</label
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
                                                        form[`agree_terms_label`][
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
                            this.handleInput(
                                "",
                                language,
                                "name"
                            );
                            this.handleInput(
                                "",
                                language,
                                "meta_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "meta_keywords"
                            );
                            this.handleInput(
                                "",
                                language,
                                "main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "required_field_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "custom_amount_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "pay_button_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "frequency_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "monthly_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "quarterly_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "semi_annually_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "annually_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "email_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "name_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "phone_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "designation_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "designation_option1"
                            );
                            this.handleInput(
                                "",
                                language,
                                "designation_option2"
                            );
                            this.handleInput(
                                "",
                                language,
                                "designation_option3"
                            );
                            this.handleInput(
                                "",
                                language,
                                "designation_option4"
                            );
                            this.handleInput(
                                "",
                                language,
                                "main_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "agree_terms_label"
                            );
                        });
                        this.fetchTermsOfUsePageSetting();
                    }
                });
        },
        fetchTermsOfUsePageSetting() {
            axios
                .get(
                    `${process.env.MIX_ADMIN_API_URL}get-coffee-wall-page-setting`
                )
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let coffee_wall_page_setting_detail =
                            res?.data?.data?.coffee_wall_page_setting_detail ||
                            [];
                        coffee_wall_page_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.meta_description,
                                setting?.language,
                                "meta_description"
                            );
                            this.handleInput(
                                setting?.meta_keywords,
                                setting?.language,
                                "meta_keywords"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.required_field_label,
                                setting?.language,
                                "required_field_label"
                            );
                            this.handleInput(
                                setting?.custom_amount_label,
                                setting?.language,
                                "custom_amount_label"
                            );
                            this.handleInput(
                                setting?.pay_button_label,
                                setting?.language,
                                "pay_button_label"
                            );
                            this.handleInput(
                                setting?.frequency_label,
                                setting?.language,
                                "frequency_label"
                            );
                            this.handleInput(
                                setting?.monthly_label,
                                setting?.language,
                                "monthly_label"
                            );
                            this.handleInput(
                                setting?.quarterly_label,
                                setting?.language,
                                "quarterly_label"
                            );
                            this.handleInput(
                                setting?.semi_annually_label,
                                setting?.language,
                                "semi_annually_label"
                            );
                            this.handleInput(
                                setting?.annually_label,
                                setting?.language,
                                "annually_label"
                            );
                            this.handleInput(
                                setting?.name_label,
                                setting?.language,
                                "name_label"
                            );
                            this.handleInput(
                                setting?.email_label,
                                setting?.language,
                                "email_label"
                            );
                            this.handleInput(
                                setting?.phone_label,
                                setting?.language,
                                "phone_label"
                            );
                            this.handleInput(
                                setting?.designation_label,
                                setting?.language,
                                "designation_label"
                            );
                            this.handleInput(
                                setting?.designation_option1,
                                setting?.language,
                                "designation_option1"
                            );
                            this.handleInput(
                                setting?.designation_option2,
                                setting?.language,
                                "designation_option2"
                            );
                            this.handleInput(
                                setting?.designation_option3,
                                setting?.language,
                                "designation_option3"
                            );
                            this.handleInput(
                                setting?.designation_option4,
                                setting?.language,
                                "designation_option4"
                            );
                            this.handleInput(
                                setting?.main_text,
                                setting?.language,
                                "main_text"
                            );
                            this.handleInput(
                                setting?.agree_terms_label,
                                setting?.language,
                                "agree_terms_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-coffee-wall-page-setting`,
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
                    `name.name_${language.id}`
                ) ||
                validationErros.has(
                    `meta_keywords.meta_keywords_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `required_field_label.required_field_label_${language.id}`
                ) ||
                validationErros.has(
                    `custom_amount_label.custom_amount_label_${language.id}`
                ) ||
                validationErros.has(
                    `pay_button_label.pay_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `frequency_label.frequency_label_${language.id}`
                ) ||
                validationErros.has(
                    `monthly_label.monthly_label_${language.id}`
                ) ||
                validationErros.has(
                    `quarterly_label.quarterly_label_${language.id}`
                ) ||
                validationErros.has(
                    `semi_annually_label.semi_annually_label_${language.id}`
                ) ||
                validationErros.has(
                    `annually_label.annually_label_${language.id}`
                ) ||
                validationErros.has(
                    `name_label.name_label_${language.id}`
                ) ||
                validationErros.has(
                    `email_label.email_label_${language.id}`
                ) ||
                validationErros.has(
                    `phone_label.phone_label_${language.id}`
                ) ||
                validationErros.has(
                    `designation_label.designation_label_${language.id}`
                ) ||
                validationErros.has(
                    `designation_option1.designation_option1_${language.id}`
                ) ||
                validationErros.has(
                    `designation_option2.designation_option2_${language.id}`
                ) ||
                validationErros.has(
                    `designation_option3.designation_option3_${language.id}`
                ) ||
                validationErros.has(
                    `designation_option4.designation_option4_${language.id}`
                ) ||
                validationErros.has(
                    `main_text.main_text_${language.id}`
                ) ||
                validationErros.has(
                    `agree_terms_label.agree_terms_label_${language.id}`
                ) ||
                validationErros.has(
                    `meta_description.meta_description_${language.id}`
                )
            );
        },
    },
};
</script>
