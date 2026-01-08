<template>
    <AppLayout>
        <section class="phone-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Contact ProximaRdie settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Contact Proxima Ride setting translations for a specific language.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Language <span class="text-red-500">*</span></label>
                                    <select v-model="excelForm.selectedLanguageId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" :class="{'border-red-500': excelValidationErrors.language_id}">
                                        <option value="">Choose Language</option>
                                        <option v-for="lang in languages" :key="lang.id" :value="lang.id">{{ lang.name }}</option>
                                    </select>
                                    <p v-if="excelValidationErrors.language_id" class="text-red-500 text-xs mt-1">{{ excelValidationErrors.language_id }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Excel File <span class="text-red-500">*</span></label>
                                    <input type="file" ref="excelFile" @change="handleFileChange" accept=".xlsx,.xls,.csv" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" :class="{'border-red-500': excelValidationErrors.excel_file}" />
                                    <p v-if="excelValidationErrors.excel_file" class="text-red-500 text-xs mt-1">{{ excelValidationErrors.excel_file }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Supported: .xlsx, .xls, .csv (Max: 5MB)</p>
                                </div>

                                <div class="flex items-end">
                                    <button type="button" @click="uploadExcelFile" :disabled="excelUploading" class="w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center">
                                        <svg v-if="excelUploading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span v-if="excelUploading">Uploading...</span>
                                        <span v-else>
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                            Upload Excel
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span class="font-medium">Need help formatting your Excel file?</span>
                                    </div>
                                    <a :href="`${mixAdminApiUrl}download-contact-proxima-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Download Template
                                    </a>
                                </div>
                            </div>

                            <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
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
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    >Required Indicate</label
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
                                                        :for="`your_full_name_label_${activeLanguageId}`">
                                                       Your Full Name Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_full_name_label_${activeLanguageId}`"
                                                    :id="`your_full_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_full_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_full_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_full_name_label.your_full_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_full_name_label.your_full_name_label_${activeLanguageId}`
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
                                                        :for="`placeholder_name_${activeLanguageId}`"
                                                        >Name placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`placeholder_name_${activeLanguageId}`"
                                                    :id="`placeholder_name_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'placeholder_name'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'placeholder_name'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `placeholder_name.placeholder_name_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `placeholder_name.placeholder_name_${activeLanguageId}`
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
                                                        :for="`your_full_name_placeholder_${activeLanguageId}`"
                                                        >Your Full Name Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_full_name_placeholder_${activeLanguageId}`"
                                                    :id="`your_full_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_full_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_full_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_full_name_placeholder.your_full_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_full_name_placeholder.your_full_name_placeholder_${activeLanguageId}`
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
                                                        :for="`your_message_label_${activeLanguageId}`"
                                                        >Your Message Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_message_label_${activeLanguageId}`"
                                                    :id="`your_message_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_message_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_message_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_message_label.your_message_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_message_label.your_message_label_${activeLanguageId}`
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
                                                        :for="`placeholder_message_${activeLanguageId}`"
                                                        >Message placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`placeholder_message_${activeLanguageId}`"
                                                    :id="`placeholder_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'placeholder_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'placeholder_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `placeholder_message.placeholder_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `placeholder_message.placeholder_message_${activeLanguageId}`
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
                                                        :for="`your_email_address_label_${activeLanguageId}`"
                                                        >Your Email Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_email_address_label_${activeLanguageId}`"
                                                    :id="`your_email_address_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_email_address_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_email_address_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_email_address_label.your_email_address_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_email_address_label.your_email_address_label_${activeLanguageId}`
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
                                                        :for="`placeholder_email_${activeLanguageId}`"
                                                        >Email placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`placeholder_email_${activeLanguageId}`"
                                                    :id="`placeholder_email_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'placeholder_email'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'placeholder_email'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `placeholder_email.placeholder_email_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `placeholder_email.placeholder_email_${activeLanguageId}`
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
                                                        :for="`your_email_address_placeholder_${activeLanguageId}`"
                                                        >Your Email Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_email_address_placeholder_${activeLanguageId}`"
                                                    :id="`your_email_address_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_email_address_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_email_address_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_email_address_placeholder.your_email_address_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_email_address_placeholder.your_email_address_placeholder_${activeLanguageId}`
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
                                                        :for="`your_phone_label_${activeLanguageId}`"
                                                        >Your Phone Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_phone_label_${activeLanguageId}`"
                                                    :id="`your_phone_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_phone_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_phone_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_phone_label.your_phone_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_phone_label.your_phone_label_${activeLanguageId}`
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
                                                        :for="`placeholder_phone_${activeLanguageId}`"
                                                        >Phone placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`placeholder_phone_${activeLanguageId}`"
                                                    :id="`placeholder_phone_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'placeholder_phone'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'placeholder_phone'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `placeholder_phone.placeholder_phone_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `placeholder_phone.placeholder_phone_${activeLanguageId}`
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
                                                        :for="`your_phone_placeholder_${activeLanguageId}`"
                                                        >Your Phone Placheholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`your_phone_placeholder_${activeLanguageId}`"
                                                    :id="`your_phone_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'your_phone_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'your_phone_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `your_phone_placeholder.your_phone_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `your_phone_placeholder.your_phone_placeholder_${activeLanguageId}`
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
                                                        :for="`submit_button_text_${activeLanguageId}`"
                                                        >Submit Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`submit_button_text_${activeLanguageId}`"
                                                    :id="`submit_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'submit_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'submit_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `submit_button_text.submit_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `submit_button_text.submit_button_text_${activeLanguageId}`
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
            excelForm: { selectedLanguageId: '', selectedFile: null },
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
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.excelForm.selectedFile = file;
                this.excelValidationErrors.excel_file = '';
            }
        },
        async uploadExcelFile() {
            this.excelValidationErrors = {};
            this.excelErrors = [];
            if (!this.excelForm.selectedLanguageId) {
                this.excelValidationErrors.language_id = 'Please select a language';
                window.helper.swalErrorMessage('Please select a language');
                return;
            }
            if (!this.excelForm.selectedFile) {
                this.excelValidationErrors.excel_file = 'Please select an Excel file';
                window.helper.swalErrorMessage('Please select an Excel file');
                return;
            }
            if (this.excelForm.selectedFile.size > 5242880) {
                this.excelValidationErrors.excel_file = 'File size must not exceed 5MB';
                return;
            }
            const formData = new FormData();
            formData.append('language_id', this.excelForm.selectedLanguageId);
            formData.append('excel_file', this.excelForm.selectedFile);
            this.excelUploading = true;
            try {
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-contact-proxima-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.getPageSettingData && this.getPageSettingData(); }, 1000);
                } else {
                    window.helper.swalErrorMessage(response?.data?.message || 'Upload failed');
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    if (error.response.data.errors) this.excelErrors = error.response.data.errors;
                    if (error.response.data.errors?.language_id) this.excelValidationErrors.language_id = error.response.data.errors.language_id[0];
                    if (error.response.data.errors?.excel_file) this.excelValidationErrors.excel_file = error.response.data.errors.excel_file[0];
                    if (this.excelErrors.length > 0) window.helper.swalErrorMessage(error.response.data.message || 'Validation errors in Excel file');
                } else {
                    window.helper.swalErrorMessage(error.response?.data?.message || 'Failed to upload Excel file');
                }
            } finally {
                this.excelUploading = false;
            }
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
                            this.handleInput("", language, "your_full_name_label");
                            this.handleInput("", language, "main_heading");
                            // this.handleInput("", language, "your_full_name_placeholder");
                            this.handleInput("", language, "your_phone_label");
                            // this.handleInput("", language, "your_phone_placeholder");
                            // this.handleInput("", language, "your_email_address_placeholder");
                            this.handleInput("", language, "your_message_label");
                            this.handleInput("", language, "your_email_address_label");
                            this.handleInput("", language, "submit_button_text");


                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "main_heading");

                            this.handleInput("", language, "mailing_address_label");
                            this.handleInput("", language, "telephone_label");
                            this.handleInput("", language, "email_label");
                            this.handleInput("", language, "message_placeholder");
                            this.handleInput("", language, "submit_button_label");

                            this.handleInput("", language, "placeholder_name");
                            this.handleInput("", language, "placeholder_email");
                            this.handleInput("", language, "placeholder_phone");
                            this.handleInput("", language, "placeholder_message");
                            this.handleInput("", language, "required_feilds_text");

                        });
                        this.fetchProfilePageSetting();
                        this.fetchContactUsPageSetting();

                    }
                });
        },
        fetchContactUsPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-contact-us-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let contact_us_page_setting_detail =
                            res?.data?.data?.contact_us_page_setting_detail || [];
                        contact_us_page_setting_detail.map((setting) => {
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
                                setting?.mailing_address_label,
                                setting?.language,
                                "mailing_address_label"
                            );
                            this.handleInput(
                                setting?.telephone_label,
                                setting?.language,
                                "telephone_label"
                            );
                            this.handleInput(
                                setting?.name_email_placeholder,
                                setting?.language,
                                "name_email_placeholder"
                            );
                            this.handleInput(
                                setting?.message_placeholder,
                                setting?.language,
                                "message_placeholder"
                            );
                            this.handleInput(
                                setting?.submit_button_label,
                                setting?.language,
                                "submit_button_label"
                            );
                            
                            this.handleInput(
                                setting?.placeholder_name,
                                setting?.language,
                                "placeholder_name"
                            );
                            this.handleInput(
                                setting?.placeholder_email,
                                setting?.language,
                                "placeholder_email"
                            );
                            this.handleInput(
                                setting?.placeholder_phone,
                                setting?.language,
                                "placeholder_phone"
                            );
                            this.handleInput(
                                setting?.required_feilds_text,
                                setting?.language,
                                "required_feilds_text"
                            );
                            this.handleInput(
                                setting?.placeholder_message,
                                setting?.language,
                                "placeholder_message"
                            );
                            
                          
                        });
                    }
                });
        },
        fetchProfilePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-contact-proxima-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let 	contact_proximaride_setting_detail =
                            res?.data?.data?.contact_proximaride_setting_detail || [];
                        	contact_proximaride_setting_detail.map((setting) => {
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
                            this.handleInput(
                                setting?.your_full_name_label,
                                setting?.language,
                                "your_full_name_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            // this.handleInput(
                            //     setting?.your_full_name_placeholder,
                            //     setting?.language,
                            //     "your_full_name_placeholder"
                            // );
                            this.handleInput(
                                setting?.your_phone_label,
                                setting?.language,
                                "your_phone_label"
                            );
                            // this.handleInput(
                            //     setting?.your_phone_placeholder,
                            //     setting?.language,
                            //     "your_phone_placeholder"
                            // );
                            this.handleInput(
                                setting?.your_email_address_label,
                                setting?.language,
                                "your_email_address_label"
                            );
                            // this.handleInput(
                            //     setting?.your_email_address_placeholder,
                            //     setting?.language,
                            //     "your_email_address_placeholder"
                            // );
                            this.handleInput(
                                setting?.your_message_label,
                                setting?.language,
                                "your_message_label"
                            );
                            this.handleInput(
                                setting?.submit_button_text,
                                setting?.language,
                                "submit_button_text"
                            );


                        });
                    }
                });
        },
        // updatePageSetting() {
        //     this.loading = true;
        //     axios
        //         .post(
        //             `${process.env.MIX_ADMIN_API_URL}update-contact-proxima-setting`,
        //             this.form
        //         )
        //         .then((res) => {
        //             if (res?.data?.status == "Success") {
        //                 this.validationErros = new ErrorHandling();
        //                 helper.swalSuccessMessage(res.data.message);
        //             } else {
        //                 helper.swalErrorMessage(res.data.message);
        //             }
        //             this.loading = false;
        //         })
        //         .catch((error) => {
        //             this.validationErros = new ErrorHandling();
        //             if (error.response && error.response.status == 422) {
        //                 this.validationErros.record(error.response.data.errors);
        //             } else if (
        //                 error.response &&
        //                 error.response.data &&
        //                 error.response.data.status == "Error"
        //             ) {
        //                 helper.swalErrorMessage(error.response.data.message);
        //             }
        //             this.loading = false;
        //         })
        //         .finally(() => (this.loading = false));



        //     axios
        //         .post(
        //             `${process.env.MIX_ADMIN_API_URL}update-contact-us-page-setting`,
        //             this.form
        //         )
        //         .then((res) => {
        //             if (res?.data?.status == "Success") {
        //                 this.validationErros = new ErrorHandling();
        //                 helper.swalSuccessMessage(res.data.message);
        //             } else {
        //                 helper.swalErrorMessage(res.data.message);
        //             }
        //             this.loading = false;
        //         })
        //         .catch((error) => {
        //             this.validationErros = new ErrorHandling();
        //             if (error.response && error.response.status == 422) {
        //                 this.validationErros.record(error.response.data.errors);
        //             } else if (
        //                 error.response &&
        //                 error.response.data &&
        //                 error.response.data.status == "Error"
        //             ) {
        //                 helper.swalErrorMessage(error.response.data.message);
        //             }
        //             this.loading = false;
        //         })
        //         .finally(() => (this.loading = false));
        // },
        
        
        updatePageSetting() {
    this.loading = true;
    
    // Create both API requests
    const request1 = axios.post(
        `${process.env.MIX_ADMIN_API_URL}update-contact-proxima-setting`,
        this.form
    );
    
    const request2 = axios.post(
        `${process.env.MIX_ADMIN_API_URL}update-contact-us-page-setting`,
        this.form
    );

    // Execute both requests in parallel
    Promise.all([request1, request2])
        .then((responses) => {
            const allSuccess = responses.every(res => res?.data?.status === "Success");
            
            if (allSuccess) {
                this.validationErros = new ErrorHandling();
                helper.swalSuccessMessage("Both settings updated successfully");
            } else {
                // Find the first error message if any request failed
                const errorResponse = responses.find(res => res?.data?.status !== "Success");
                helper.swalErrorMessage(errorResponse?.data?.message || "An error occurred");
            }
        })
        .catch((error) => {
            this.validationErros = new ErrorHandling();
            
            if (error.response && error.response.status == 422) {
                this.validationErros.record(error.response.data.errors);
            } else if (error.response?.data?.status == "Error") {
                helper.swalErrorMessage(error.response.data.message);
            } else {
                // Handle network errors or other issues
                helper.swalErrorMessage("Failed to update settings");
            }
        })
        .finally(() => {
            this.loading = false;
        });
},
        checkValidationError(validationErros, language) {
            return (
                validationErros.has(`name.name_${language.id}`) ||
                validationErros.has(
                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${language.id}`
                ) ||
                validationErros.has(
                    `your_full_name_label.your_full_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `your_phone_label.your_phone_label_${language.id}`
                )  ||
                validationErros.has(
                    `your_email_address_label.your_email_address_label_${language.id}`
                )||
                validationErros.has(
                    `your_message_label.your_message_label_${language.id}`
                )||
                validationErros.has(
                    `submit_button_text.submit_button_text_${language.id}`
                )



                ||validationErros.has(`name.name_${language.id}`) ||
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
                    `mailing_address_label.mailing_address_label_${language.id}`
                ) ||
                validationErros.has(
                    `telephone_label.telephone_label_${language.id}`
                ) ||
                validationErros.has(
                    `name_email_placeholder.name_email_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `message_placeholder.message_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `submit_button_label.submit_button_label_${language.id}`
                )
                
                ||
                validationErros.has(
                    `placeholder_name.placeholder_name_${language.id}`
                )||
                validationErros.has(
                    `placeholder_email.placeholder_email_${language.id}`
                )||
                validationErros.has(
                    `placeholder_phone.placeholder_phone_${language.id}`
                )||
                validationErros.has(
                    `placeholder_message.placeholder_message_${language.id}`
                )||
                validationErros.has(
                    `required_feilds_text.required_feilds_text_${language.id}`
                )
            );
        },
    },
};
</script>
