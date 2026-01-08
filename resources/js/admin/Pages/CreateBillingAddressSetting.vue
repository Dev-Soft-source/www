<template>
    <AppLayout>
        <section class="phone-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Billing Address settings
                                </h3>
                            </div>
                        </div>
                    </header>

                    <!-- Excel Upload Section -->
                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <h4 class="text-xl font-bold text-gray-800">📊 Excel Upload - Bulk Import Translations</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">
                                Upload an Excel file with all billing address setting translations for a specific language. This will save or update all fields at once.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Language Selector -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Select Language <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="excelForm.selectedLanguageId"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{'border-red-500': excelValidationErrors.language_id}"
                                    >
                                        <option value="">Choose Language</option>
                                        <option
                                            v-for="lang in languages"
                                            :key="lang.id"
                                            :value="lang.id"
                                        >
                                            {{ lang.name }}
                                        </option>
                                    </select>
                                    <p v-if="excelValidationErrors.language_id" class="text-red-500 text-xs mt-1">
                                        {{ excelValidationErrors.language_id }}
                                    </p>
                                </div>

                                <!-- File Upload -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Upload Excel File <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="file"
                                        ref="excelFile"
                                        @change="handleFileChange"
                                        accept=".xlsx,.xls,.csv"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                        :class="{'border-red-500': excelValidationErrors.excel_file}"
                                    />
                                    <p v-if="excelValidationErrors.excel_file" class="text-red-500 text-xs mt-1">
                                        {{ excelValidationErrors.excel_file }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Supported: .xlsx, .xls, .csv (Max: 5MB)</p>
                                </div>

                                <!-- Upload Button -->
                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        @click="uploadExcelFile"
                                        :disabled="excelUploading"
                                        class="w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center"
                                    >
                                        <svg v-if="excelUploading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span v-if="excelUploading">Uploading...</span>
                                        <span v-else>
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            Upload Excel
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Download Template Link -->
                            <div class="mt-4 pt-4 border-t border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="font-medium">Need help formatting your Excel file?</span>
                                    </div>
                                    <a
                                        :href="`${mixAdminApiUrl}download-billing-address-setting-template?format=single_column`"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download Template
                                    </a>
                                </div>
                            </div>

                            <!-- Excel Validation Errors Display -->
                            <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="flex-1">
                                        <h5 class="text-red-800 font-semibold mb-2">Validation Errors in Excel File:</h5>
                                        <ul class="list-disc list-inside space-y-1">
                                            <li v-for="(error, index) in excelErrors" :key="index" class="text-sm text-red-700">
                                                <strong>Row {{ error.row }}:</strong> {{ error.attribute }} - {{ error.errors.join(', ') }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Excel Upload Section -->

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

                                        <!-- <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`edit_card_button_text_${activeLanguageId}`">
                                                        Edit Card Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_card_button_text_${activeLanguageId}`"
                                                    :id="`edit_card_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'edit_card_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_card_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_card_button_text.edit_card_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_card_button_text.edit_card_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div> -->
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
                                                    :for="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    >Required Indicate Field (mobile)</label
                                                >
                                            </div>
                                            <input
                                                    type="text"
                                                    :name="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    :id="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_indicate_required_field_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_indicate_required_field_label'
                                                        )
                                                    "
                                                />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${activeLanguageId}`
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
                                                        :for="`name_on_card_label_${activeLanguageId}`"
                                                        >Name On Card Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`name_on_card_label_${activeLanguageId}`"
                                                    :id="`name_on_card_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'name_on_card_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'name_on_card_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `name_on_card_label.name_on_card_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `name_on_card_label.name_on_card_label_${activeLanguageId}`
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
                                                        :for="`name_on_card_placeholder_${activeLanguageId}`"
                                                        >Name On Card Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`name_on_card_placeholder_${activeLanguageId}`"
                                                    :id="`name_on_card_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'name_on_card_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'name_on_card_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `name_on_card_placeholder.name_on_card_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `name_on_card_placeholder.name_on_card_placeholder_${activeLanguageId}`
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
                                                        :for="`card_number_label_${activeLanguageId}`"
                                                        >Card Number Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_number_label_${activeLanguageId}`"
                                                    :id="`card_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_number_label.card_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_number_label.card_number_label_${activeLanguageId}`
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
                                                        :for="`card_number_placeholder_${activeLanguageId}`"
                                                        >Card Number Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_number_placeholder_${activeLanguageId}`"
                                                    :id="`card_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_number_placeholder.card_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_number_placeholder.card_number_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_card_type_label_${activeLanguageId}`"
                                                        >Card Type Label Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_card_type_label_${activeLanguageId}`"
                                                    :id="`mobile_card_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_card_type_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_card_type_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_card_type_label.mobile_card_type_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_card_type_label.mobile_card_type_label_${activeLanguageId}`
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
                                                        :for="`mobile_card_type_placholder_${activeLanguageId}`"
                                                        >Card Type Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_card_type_placholder_${activeLanguageId}`"
                                                    :id="`mobile_card_type_placholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_card_type_placholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_card_type_placholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_card_type_placholder.mobile_card_type_placholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_card_type_placholder.mobile_card_type_placholder_${activeLanguageId}`
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
                                                        :for="`mobile_expiry_date_label_${activeLanguageId}`"
                                                        >Expiry Date Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_expiry_date_label_${activeLanguageId}`"
                                                    :id="`mobile_expiry_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_expiry_date_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_expiry_date_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_expiry_date_label.mobile_expiry_date_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_expiry_date_label.mobile_expiry_date_label_${activeLanguageId}`
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
                                                        :for="`mobile_month_placeholder_${activeLanguageId}`"
                                                        >Month Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_month_placeholder_${activeLanguageId}`"
                                                    :id="`mobile_month_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_month_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_month_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_month_placeholder.mobile_month_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_month_placeholder.mobile_month_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_year_placeholder_${activeLanguageId}`"
                                                        >Year Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_year_placeholder_${activeLanguageId}`"
                                                    :id="`mobile_year_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_year_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_year_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_year_placeholder.mobile_year_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_year_placeholder.mobile_year_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_street_name_placeholder_${activeLanguageId}`"
                                                        >Street Name Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_street_name_placeholder_${activeLanguageId}`"
                                                    :id="`mobile_street_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_street_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_street_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_street_name_placeholder.mobile_street_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_street_name_placeholder.mobile_street_name_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_street_name_label_${activeLanguageId}`"
                                                        >Street Name Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_street_name_label_${activeLanguageId}`"
                                                    :id="`mobile_street_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_street_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_street_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_street_name_label.mobile_street_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_street_name_label.mobile_street_name_label_${activeLanguageId}`
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
                                                        :for="`mobile_billing_address_label_${activeLanguageId}`"
                                                        >Billing Address Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_billing_address_label_${activeLanguageId}`"
                                                    :id="`mobile_billing_address_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_billing_address_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_billing_address_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_billing_address_label.mobile_billing_address_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_billing_address_label.mobile_billing_address_label_${activeLanguageId}`
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
                                                        :for="`security_code_palceholder_${activeLanguageId}`"
                                                        >Security Code Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`security_code_palceholder_${activeLanguageId}`"
                                                    :id="`security_code_palceholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'security_code_palceholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'security_code_palceholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `security_code_palceholder.security_code_palceholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `security_code_palceholder.security_code_palceholder_${activeLanguageId}`
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
                                                        :for="`security_code_label_${activeLanguageId}`"
                                                        >Security Code Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`security_code_label_${activeLanguageId}`"
                                                    :id="`security_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'security_code_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'security_code_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `security_code_label.security_code_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `security_code_label.security_code_label_${activeLanguageId}`
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
                                                        :for="`web_expiry_month_placeholder_${activeLanguageId}`"
                                                        >Expiry Month Placeholder (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_expiry_month_placeholder_${activeLanguageId}`"
                                                    :id="`web_expiry_month_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_expiry_month_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_expiry_month_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_expiry_month_placeholder.web_expiry_month_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_expiry_month_placeholder.web_expiry_month_placeholder_${activeLanguageId}`
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
                                                        :for="`web_expiry_month_label_${activeLanguageId}`"
                                                        >Expiry Month Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_expiry_month_label_${activeLanguageId}`"
                                                    :id="`web_expiry_month_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_expiry_month_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_expiry_month_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_expiry_month_label.web_expiry_month_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_expiry_month_label.web_expiry_month_label_${activeLanguageId}`
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
                                                        :for="`mobile_house_number_label_${activeLanguageId}`"
                                                        >House Number Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_house_number_label_${activeLanguageId}`"
                                                    :id="`mobile_house_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_house_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_house_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_house_number_label.mobile_house_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_house_number_label.mobile_house_number_label_${activeLanguageId}`
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
                                                        :for="`mobile_house_number_placeholder_${activeLanguageId}`"
                                                        >House Number Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_house_number_placeholder_${activeLanguageId}`"
                                                    :id="`mobile_house_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_house_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_house_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_house_number_placeholder.mobile_house_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_house_number_placeholder.mobile_house_number_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_city_label_${activeLanguageId}`"
                                                        >City Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_city_label_${activeLanguageId}`"
                                                    :id="`mobile_city_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_city_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_city_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_city_label.mobile_city_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_city_label.mobile_city_label_${activeLanguageId}`
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
                                                        :for="`mobile_city_placeholder_${activeLanguageId}`"
                                                        >City Placeholder (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_city_placeholder_${activeLanguageId}`"
                                                    :id="`mobile_city_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_city_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_city_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_city_placeholder.mobile_city_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_city_placeholder.mobile_city_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_province_label_${activeLanguageId}`"
                                                        >Province Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_province_label_${activeLanguageId}`"
                                                    :id="`mobile_province_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_province_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_province_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_province_label.mobile_province_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_province_label.mobile_province_label_${activeLanguageId}`
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
                                                            :for="`mobile_province_placeholder_${activeLanguageId}`"
                                                            >Province Placeholder (mobile)</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`mobile_province_placeholder_${activeLanguageId}`"
                                                        :id="`mobile_province_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'mobile_province_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_province_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `mobile_province_placeholder.mobile_province_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `mobile_province_placeholder.mobile_province_placeholder_${activeLanguageId}`
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
                                                            :for="`mobile_country_label_${activeLanguageId}`"
                                                            >Country label (mobile)</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`mobile_country_label_${activeLanguageId}`"
                                                        :id="`mobile_country_label_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'mobile_country_label'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_country_label'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `mobile_country_label.mobile_country_label_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `mobile_country_label.mobile_country_label_${activeLanguageId}`
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
                                                            :for="`mobile_country_placeholder_${activeLanguageId}`"
                                                            >Country Placeholder (mobile)</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`mobile_country_placeholder_${activeLanguageId}`"
                                                        :id="`mobile_country_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'mobile_country_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_country_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `mobile_country_placeholder.mobile_country_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `mobile_country_placeholder.mobile_country_placeholder_${activeLanguageId}`
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
                                                            :for="`mobile_postal_code_label_${activeLanguageId}`"
                                                            >Postal Code Label (mobile)</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`mobile_postal_code_label_${activeLanguageId}`"
                                                        :id="`mobile_postal_code_label_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'mobile_postal_code_label'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_postal_code_label'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `mobile_postal_code_label.mobile_postal_code_label_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `mobile_postal_code_label.mobile_postal_code_label_${activeLanguageId}`
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
                                                            :for="`mobile_postal_code_placeholder_${activeLanguageId}`"
                                                            >Postal Code Placeholder (mobile)</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`mobile_postal_code_placeholder_${activeLanguageId}`"
                                                        :id="`mobile_postal_code_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'mobile_postal_code_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_postal_code_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `mobile_postal_code_placeholder.mobile_postal_code_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `mobile_postal_code_placeholder.mobile_postal_code_placeholder_${activeLanguageId}`
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
                                                            :for="`mobile_primary_card_placeholder_${activeLanguageId}`"
                                                            >Primary Card Placeholder (mobile)</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`mobile_primary_card_placeholder_${activeLanguageId}`"
                                                        :id="`mobile_primary_card_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'mobile_primary_card_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_primary_card_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `mobile_primary_card_placeholder.mobile_primary_card_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `mobile_primary_card_placeholder.mobile_primary_card_placeholder_${activeLanguageId}`
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
                                                            :for="`save_button_text_${activeLanguageId}`"
                                                            >Save Button</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`save_button_text_${activeLanguageId}`"
                                                        :id="`save_button_text_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'save_button_text'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'save_button_text'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `save_button_text.save_button_text_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `save_button_text.save_button_text_${activeLanguageId}`
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
                                                            :for="`select_card_type_text_${activeLanguageId}`"
                                                            >Select Card Type</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`select_card_type_text_${activeLanguageId}`"
                                                        :id="`select_card_type_text_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'select_card_type_text'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'select_card_type_text'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `select_card_type_text.select_card_type_text_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `select_card_type_text.select_card_type_text_${activeLanguageId}`
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
                                                            :for="`indicate_field_label_${activeLanguageId}`"
                                                            >Indicate field label</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`indicate_field_label_${activeLanguageId}`"
                                                        :id="`indicate_field_label_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'indicate_field_label'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'indicate_field_label'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `indicate_field_label.indicate_field_label_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `indicate_field_label.indicate_field_label_${activeLanguageId}`
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
                                                            :for="`expiry_month_placeholder_${activeLanguageId}`"
                                                            >Expiry month placeholder</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`expiry_month_placeholder_${activeLanguageId}`"
                                                        :id="`expiry_month_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'expiry_month_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'expiry_month_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `expiry_month_placeholder.expiry_month_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `expiry_month_placeholder.expiry_month_placeholder_${activeLanguageId}`
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
                                                            :for="`card_number_placeholder_${activeLanguageId}`"
                                                            >Card number placeholder</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`card_number_placeholder_${activeLanguageId}`"
                                                        :id="`card_number_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'card_number_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'card_number_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `card_number_placeholder.card_number_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `card_number_placeholder.card_number_placeholder_${activeLanguageId}`
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
                                                            :for="`cvc_placeholder_${activeLanguageId}`"
                                                            >Cvc placeholder</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`cvc_placeholder_${activeLanguageId}`"
                                                        :id="`cvc_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'cvc_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'cvc_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `cvc_placeholder.cvc_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `cvc_placeholder.cvc_placeholder_${activeLanguageId}`
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
                                                            :for="`card_name_placeholder_${activeLanguageId}`"
                                                            >Card name placeholder</label
                                                        >
                                                    </div>
                                                    <input
                                                        type="text"
                                                        :name="`card_name_placeholder_${activeLanguageId}`"
                                                        :id="`card_name_placeholder_${activeLanguageId}`"
                                                        class="can-exp-input w-full block border border-gray-300 rounded"
                                                        placeholder=" "
                                                        :value="
                                                            getCurrentValue(
                                                                'card_name_placeholder'
                                                            )
                                                        "
                                                        @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'card_name_placeholder'
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `card_name_placeholder.card_name_placeholder_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `card_name_placeholder.card_name_placeholder_${activeLanguageId}`
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
            // Excel Upload Data
            excelForm: {
                selectedLanguageId: '',
                selectedFile: null
            },
            excelValidationErrors: {},
            excelErrors: [],
            excelUploading: false,
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
    },
    computed: {
        mixAdminApiUrl() {
            return process.env.MIX_ADMIN_API_URL;
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
                            this.handleInput("", language, "mobile_indicate_required_field_label");
                            // this.handleInput("", language, "edit_card_button_text");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "name_on_card_label");
                            this.handleInput("", language, "name_on_card_placeholder");
                            this.handleInput("", language, "card_number_label");
                            this.handleInput("", language, "card_number_placeholder");
                            this.handleInput("", language, "mobile_card_type_label");
                            this.handleInput("", language, "mobile_card_type_placholder");
                            this.handleInput("", language, "mobile_expiry_date_label");
                            this.handleInput("", language, "mobile_month_placeholder");
                            this.handleInput("", language, "mobile_year_placeholder");
                            this.handleInput("", language, "web_expiry_month_label");
                            this.handleInput("", language, "web_expiry_month_placeholder");
                            this.handleInput("", language, "security_code_label");
                            this.handleInput("", language, "security_code_palceholder");
                            this.handleInput("", language, "mobile_billing_address_label");
                            this.handleInput("", language, "mobile_street_name_label");
                            this.handleInput("", language, "mobile_street_name_placeholder");
                            this.handleInput("", language, "mobile_house_number_label");
                            this.handleInput("", language, "mobile_house_number_placeholder");
                            this.handleInput("", language, "mobile_city_label");
                            this.handleInput("", language, "mobile_city_placeholder");
                            this.handleInput("", language, "mobile_province_label");
                            this.handleInput("", language, "mobile_province_placeholder");
                            this.handleInput("", language, "mobile_country_label");
                            this.handleInput("", language, "mobile_country_placeholder");
                            this.handleInput("", language, "mobile_postal_code_label");
                            this.handleInput("", language, "mobile_postal_code_placeholder");
                            this.handleInput("", language, "mobile_primary_card_placeholder");
                            this.handleInput("", language, "save_button_text");
                            this.handleInput("", language, "select_card_type_text");
                            this.handleInput("", language, "indicate_field_label");

                            this.handleInput("", language, "expiry_month_placeholder");
                            this.handleInput("", language, "card_number_placeholder");
                            this.handleInput("", language, "cvc_placeholder");
                            this.handleInput("", language, "card_name_placeholder");
                            
                        });
                        this.fetchBillingAddressPageSetting();
                    }
                });
        },
        fetchBillingAddressPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-billing-address-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let billing_address_setting_detail =
                            res?.data?.data?.billing_address_setting_detail || [];
                        billing_address_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.mobile_indicate_required_field_label,
                                setting?.language,
                                "mobile_indicate_required_field_label"
                            );
                            // this.handleInput(
                            //     setting?.edit_card_button_text,
                            //     setting?.language,
                            //     "edit_card_button_text"
                            // );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.name_on_card_label,
                                setting?.language,
                                "name_on_card_label"
                            );

                            this.handleInput(
                                setting?.name_on_card_placeholder,
                                setting?.language,
                                "name_on_card_placeholder"
                            );
                             this.handleInput(
                                setting?.card_number_label,
                                setting?.language,
                                "card_number_label"
                            );

                            this.handleInput(
                                setting?.mobile_card_type_label,
                                setting?.language,
                                "mobile_card_type_label"
                            );
                            this.handleInput(
                                setting?.mobile_card_type_placholder,
                                setting?.language,
                                "mobile_card_type_placholder"
                            );
                             this.handleInput(
                                setting?.mobile_expiry_date_label,
                                setting?.language,
                                "mobile_expiry_date_label"
                            );
                              this.handleInput(
                                setting?.mobile_year_placeholder,
                                setting?.language,
                                "mobile_year_placeholder"
                            );
                            this.handleInput(
                                setting?.mobile_month_placeholder,
                                setting?.language,
                                "mobile_month_placeholder"
                            );
                            this.handleInput(
                                setting?.web_expiry_month_label,
                                setting?.language,
                                "web_expiry_month_label"
                            );
                            this.handleInput(
                                setting?.web_expiry_month_placeholder,
                                setting?.language,
                                "web_expiry_month_placeholder"
                            );
                            this.handleInput(
                                setting?.security_code_label,
                                setting?.language,
                                "security_code_label"
                            );
                            this.handleInput(
                                setting?.security_code_palceholder,
                                setting?.language,
                                "security_code_palceholder"
                            );
                            this.handleInput(
                                setting?.mobile_billing_address_label,
                                setting?.language,
                                "mobile_billing_address_label"
                            );
                            this.handleInput(
                                setting?.mobile_street_name_label,
                                setting?.language,
                                "mobile_street_name_label"
                            );
                            this.handleInput(
                                setting?.mobile_street_name_placeholder,
                                setting?.language,
                                "mobile_street_name_placeholder"
                            );
                            this.handleInput(
                                setting?.mobile_house_number_label,
                                setting?.language,
                                "mobile_house_number_label"
                            );
                            this.handleInput(
                                setting?.card_number_placeholder,
                                setting?.language,
                                "card_number_placeholder"
                            );
                            this.handleInput(
                                setting?.mobile_house_number_placeholder,
                                setting?.language,
                                "mobile_house_number_placeholder"
                            );
                            this.handleInput(
                                setting?.mobile_city_label,
                                setting?.language,
                                "mobile_city_label"
                            );
                            this.handleInput(
                                setting?.mobile_city_placeholder,
                                setting?.language,
                                "mobile_city_placeholder"
                            );
                             this.handleInput(
                                setting?.mobile_province_label,
                                setting?.language,
                                "mobile_province_label"
                            );
                             this.handleInput(
                                setting?.mobile_province_placeholder,
                                setting?.language,
                                "mobile_province_placeholder"
                            );
                             this.handleInput(
                                setting?.mobile_country_label,
                                setting?.language,
                                "mobile_country_label"
                            );
                             this.handleInput(
                                setting?.mobile_country_placeholder,
                                setting?.language,
                                "mobile_country_placeholder"
                            );
                             this.handleInput(
                                setting?.mobile_postal_code_label,
                                setting?.language,
                                "mobile_postal_code_label"
                            );
                             this.handleInput(
                                setting?.mobile_postal_code_placeholder,
                                setting?.language,
                                "mobile_postal_code_placeholder"
                            );
                             this.handleInput(
                                setting?.mobile_primary_card_placeholder,
                                setting?.language,
                                "mobile_primary_card_placeholder"
                            );
                             this.handleInput(
                                setting?.save_button_text,
                                setting?.language,
                                "save_button_text"
                            );
                             this.handleInput(
                                setting?.select_card_type_text,
                                setting?.language,
                                "select_card_type_text"
                            );
                            this.handleInput(
                                setting?.indicate_field_label,
                                setting?.language,
                                "indicate_field_label"
                            );
                            this.handleInput(
                                setting?.card_name_placeholder,
                                setting?.language,
                                "card_name_placeholder"
                            );
                            this.handleInput(
                                setting?.cvc_placeholder,
                                setting?.language,
                                "cvc_placeholder"
                            );
                            this.handleInput(
                                setting?.card_number_placeholder,
                                setting?.language,
                                "card_number_placeholder"
                            );
                            this.handleInput(
                                setting?.expiry_month_placeholder,
                                setting?.language,
                                "expiry_month_placeholder"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-billing-address-setting`,
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
        // Excel Upload Methods
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.excelForm.selectedFile = file;
                this.excelValidationErrors.excel_file = '';
            }
        },
        async uploadExcelFile() {
            // Reset errors
            this.excelValidationErrors = {};
            this.excelErrors = [];

            // Client-side validation
            if (!this.excelForm.selectedLanguageId) {
                this.excelValidationErrors.language_id = 'Please select a language';
                return;
            }

            if (!this.excelForm.selectedFile) {
                this.excelValidationErrors.excel_file = 'Please select an Excel file';
                return;
            }

            // Check file size (5MB = 5242880 bytes)
            if (this.excelForm.selectedFile.size > 5242880) {
                this.excelValidationErrors.excel_file = 'File size must not exceed 5MB';
                return;
            }

            // Check file extension
            const allowedExtensions = ['xlsx', 'xls', 'csv'];
            const fileName = this.excelForm.selectedFile.name;
            const fileExtension = fileName.split('.').pop().toLowerCase();
            
            if (!allowedExtensions.includes(fileExtension)) {
                this.excelValidationErrors.excel_file = 'File must be .xlsx, .xls, or .csv';
                return;
            }

            // Prepare FormData
            const formData = new FormData();
            formData.append('language_id', this.excelForm.selectedLanguageId);
            formData.append('excel_file', this.excelForm.selectedFile);

            // Upload
            this.excelUploading = true;

            try {
                const response = await axios.post(
                    `${process.env.MIX_ADMIN_API_URL}upload-billing-address-setting-excel`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                );

                if (response?.data?.status === 'Success') {
                    helper.swalSuccessMessage(response.data.message);
                    
                    // Reset form
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    this.$refs.excelFile.value = '';
                    
                    // Refresh the page data
                    this.fetchBillingAddressPageSetting();
                } else {
                    helper.swalErrorMessage(response.data.message || 'Upload failed');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Validation errors
                    if (error.response.data.errors) {
                        // Laravel style validation errors (from request validation)
                        this.excelValidationErrors = {};
                        Object.keys(error.response.data.errors).forEach(key => {
                            this.excelValidationErrors[key] = error.response.data.errors[key][0];
                        });
                    } else if (error.response.data.errors && Array.isArray(error.response.data.errors)) {
                        // Excel row validation errors (from Excel import validation)
                        this.excelErrors = error.response.data.errors;
                    }
                    
                    if (error.response.data.message) {
                        helper.swalErrorMessage(error.response.data.message);
                    }
                } else if (error.response && error.response.data) {
                    helper.swalErrorMessage(error.response.data.message || 'An error occurred during upload');
                } else {
                    helper.swalErrorMessage('Network error. Please check your connection and try again.');
                }
            } finally {
                this.excelUploading = false;
            }
        },
        checkValidationError(validationErros, language) {
            return (
                validationErros.has(`name.name_${language.id}`) ||
                validationErros.has(
                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `name_on_card_label.name_on_card_label_${language.id}`
                ) ||
                validationErros.has(
                    `name_on_card_placeholder.name_on_card_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `card_number_label.card_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `card_number_placeholder.card_number_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_card_type_placholder.mobile_card_type_placholder_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_expiry_date_label.mobile_expiry_date_label_${language.id}`
                )
                ||
                validationErros.has(
                    `mobile_month_placeholder.mobile_month_placeholder_${language.id}`
                )||
                validationErros.has(
                    `mobile_year_placeholder.mobile_year_placeholder_${language.id}`
                )
                ||
                validationErros.has(
                    `mobile_city_placeholder.mobile_city_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_city_label.mobile_city_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_house_number_placeholder.mobile_house_number_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_house_number_label.mobile_house_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_street_name_placeholder.mobile_street_name_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_street_name_label.mobile_street_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_billing_address_label.mobile_billing_address_label_${language.id}`
                ) ||
                validationErros.has(
                    `security_code_palceholder.security_code_palceholder_${language.id}`
                ) ||
                validationErros.has(
                    `security_code_label.security_code_label_${language.id}`
                ) ||
                validationErros.has(
                    `web_expiry_month_placeholder.web_expiry_month_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `web_expiry_month_label.web_expiry_month_label_${language.id}`
                )||
                validationErros.has(
                    `mobile_province_label.mobile_province_label_${language.id}`
                )||
                validationErros.has(
                    `mobile_province_placeholder.mobile_province_placeholder_${language.id}`
                )||
                validationErros.has(
                    `mobile_country_label.mobile_country_label_${language.id}`
                )||
                validationErros.has(
                    `mobile_country_placeholder.mobile_country_placeholder_${language.id}`
                )||
                validationErros.has(
                    `mobile_postal_code_label.mobile_postal_code_label_${language.id}`
                )||
                validationErros.has(
                    `mobile_postal_code_placeholder.mobile_postal_code_placeholder_${language.id}`
                )||
                validationErros.has(
                    `mobile_primary_card_placeholder.mobile_primary_card_placeholder_${language.id}`
                )||
                validationErros.has(
                    `save_button_text.save_button_text_${language.id}`
                )||
                validationErros.has(
                    `mobile_card_type_label.mobile_card_type_label_${language.id}`
                )
            );
        },
    },
};
</script>
