<template>
    <AppLayout>
        <section class="phone-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    My Phone settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <!-- Excel Upload Section -->
                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                <h4 class="text-xl font-bold text-gray-800">📊 Excel Upload - Bulk Import Translations</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all My Phone page setting translations for a specific language.</p>

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
                                    <div class="flex items-center text-sm text-gray-600"><svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><span class="font-medium">Need help formatting your Excel file?</span></div>
                                    <a :href="`${mixAdminApiUrl}download-my-phone-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
                                </div>
                            </div>

                            <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded"><div class="flex items-start"><svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div class="flex-1"><h5 class="text-red-800 font-semibold mb-2">Validation Errors in Excel File:</h5><ul class="list-disc list-inside space-y-1"><li v-for="(error, index) in excelErrors" :key="index" class="text-sm text-red-700"><strong>Row {{ error.row }}:</strong> {{ error.attribute }} - {{ error.errors.join(', ') }}</li></ul></div></div></div>
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
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`unverified_number_label_${activeLanguageId}`">
                                                        Unverified Number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`unverified_number_label_${activeLanguageId}`"
                                                    :id="`unverified_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'unverified_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'unverified_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `unverified_number_label.unverified_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `unverified_number_label.unverified_number_label_${activeLanguageId}`
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
                                                        :for="`verified_number_label_${activeLanguageId}`">
                                                        Verified Number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verified_number_label_${activeLanguageId}`"
                                                    :id="`verified_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verified_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verified_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verified_number_label.verified_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verified_number_label.verified_number_label_${activeLanguageId}`
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
                                                    :for="`phone_no_description_text_${activeLanguageId}`"
                                                    >Phone No Description</label
                                                >
                                            </div>
                                            <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'phone_no_description_text'
                                                        )
                                                    "
                                                    :ref="`phone_no_description_text_${language.id}`"
                                                    :id="`phone_no_description_text_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `phone_no_description_text`
                                                        ][
                                                            `phone_no_description_text_${language?.id}`
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
                                                    `phone_no_description_text.phone_no_description_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_no_description_text.phone_no_description_text_${activeLanguageId}`
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
                                                        :for="`mobile_verify_button_text_${activeLanguageId}`"
                                                        >Verify Button Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_verify_button_text_${activeLanguageId}`"
                                                    :id="`mobile_verify_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_verify_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_verify_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_verify_button_text.mobile_verify_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_verify_button_text.mobile_verify_button_text_${activeLanguageId}`
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
                                                        :for="`web_send_verification_code_button_text_${activeLanguageId}`"
                                                        >Send Verification Code (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_send_verification_code_button_text_${activeLanguageId}`"
                                                    :id="`web_send_verification_code_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_send_verification_code_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_send_verification_code_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_send_verification_code_button_text.web_send_verification_code_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_send_verification_code_button_text.web_send_verification_code_button_text_${activeLanguageId}`
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
                                                        :for="`delete_button_text_${activeLanguageId}`"
                                                        >Delete Button Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`delete_button_text_${activeLanguageId}`"
                                                    :id="`delete_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'delete_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'delete_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `delete_button_text.delete_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `delete_button_text.delete_button_text_${activeLanguageId}`
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
                                                        :for="`mobile_country_code_label_${activeLanguageId}`"
                                                        >Country Code Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_country_code_label_${activeLanguageId}`"
                                                    :id="`mobile_country_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_country_code_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_country_code_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_country_code_label.mobile_country_code_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_country_code_label.mobile_country_code_label_${activeLanguageId}`
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
                                                        :for="`country_code_label_web_${activeLanguageId}`"
                                                        >Country Code Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_code_label_web_${activeLanguageId}`"
                                                    :id="`country_code_label_web_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_code_label_web'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_code_label_web'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_code_label_web.country_code_label_web_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_code_label_web.country_code_label_web_${activeLanguageId}`
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
                                                        :for="`country_id_label_web_${activeLanguageId}`"
                                                        >Country Name Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_id_label_web_${activeLanguageId}`"
                                                    :id="`country_id_label_web_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_id_label_web'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_id_label_web'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_id_label_web.country_id_label_web_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_id_label_web.country_id_label_web_${activeLanguageId}`
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
                                                        :for="`country_code_placeholder_${activeLanguageId}`"
                                                        >Contry Code Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_code_placeholder_${activeLanguageId}`"
                                                    :id="`country_code_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_code_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_code_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_code_placeholder.country_code_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_code_placeholder.country_code_placeholder_${activeLanguageId}`
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
                                                        :for="`mobile_phone_number_label_${activeLanguageId}`"
                                                        >Phone Number Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_phone_number_label_${activeLanguageId}`"
                                                    :id="`mobile_phone_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_phone_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_phone_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_phone_number_label.mobile_phone_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_phone_number_label.mobile_phone_number_label_${activeLanguageId}`
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
                                                        :for="`phone_number_label_web_${activeLanguageId}`"
                                                        >Phone Number Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_number_label_web_${activeLanguageId}`"
                                                    :id="`phone_number_label_web_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'phone_number_label_web'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'phone_number_label_web'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `phone_number_label_web.phone_number_label_web_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_number_label_web.phone_number_label_web_${activeLanguageId}`
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
                                                        :for="`phone_number_placeholder_${activeLanguageId}`"
                                                        >Phone Number Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_number_placeholder_${activeLanguageId}`"
                                                    :id="`phone_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'phone_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'phone_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `phone_number_placeholder.phone_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_number_placeholder.phone_number_placeholder_${activeLanguageId}`
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
                                                        :for="`save_phoneno_button_text_${activeLanguageId}`"
                                                        >Save Phone button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`save_phoneno_button_text_${activeLanguageId}`"
                                                    :id="`save_phoneno_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'save_phoneno_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'save_phoneno_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `save_phoneno_button_text.save_phoneno_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `save_phoneno_button_text.save_phoneno_button_text_${activeLanguageId}`
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
                                                        :for="`send_verification_code_button_text_${activeLanguageId}`"
                                                        >Verification Code Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`send_verification_code_button_text_${activeLanguageId}`"
                                                    :id="`send_verification_code_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'send_verification_code_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'send_verification_code_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `send_verification_code_button_text.send_verification_code_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `send_verification_code_button_text.send_verification_code_button_text_${activeLanguageId}`
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
                                                        :for="`resend_code_btn_label_${activeLanguageId}`"
                                                        >Resend Code Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`resend_code_btn_label_${activeLanguageId}`"
                                                    :id="`resend_code_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'resend_code_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'resend_code_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `resend_code_btn_label.resend_code_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `resend_code_btn_label.resend_code_btn_label_${activeLanguageId}`
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
                                                        :for="`request_code_text_${activeLanguageId}`"
                                                        >Request Code Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`request_code_text_${activeLanguageId}`"
                                                    :id="`request_code_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'request_code_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'request_code_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `request_code_text.request_code_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `request_code_text.request_code_text_${activeLanguageId}`
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
                                                        :for="`second_text_${activeLanguageId}`"
                                                        >Second Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`second_text_${activeLanguageId}`"
                                                    :id="`second_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'second_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'second_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `second_text.second_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `second_text.second_text_${activeLanguageId}`
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
                                                        :for="`verify_phone_number_label_${activeLanguageId}`"
                                                        >Verify Phone Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_phone_number_label_${activeLanguageId}`"
                                                    :id="`verify_phone_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verify_phone_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verify_phone_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verify_phone_number_label.verify_phone_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verify_phone_number_label.verify_phone_number_label_${activeLanguageId}`
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
                                                        :for="`enter_code_label_${activeLanguageId}`"
                                                        >Enter Code Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`enter_code_label_${activeLanguageId}`"
                                                    :id="`enter_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'enter_code_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'enter_code_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `enter_code_label.enter_code_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `enter_code_label.enter_code_label_${activeLanguageId}`
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
                                                        :for="`otp_code_description_${activeLanguageId}`"
                                                        >Opt Code Description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`otp_code_description_${activeLanguageId}`"
                                                    :id="`otp_code_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'otp_code_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'otp_code_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `otp_code_description.otp_code_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `otp_code_description.otp_code_description_${activeLanguageId}`
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
                                                        :for="`verify_phone_number_heading_${activeLanguageId}`"
                                                        >Verify Phone Number </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_phone_number_heading_${activeLanguageId}`"
                                                    :id="`verify_phone_number_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verify_phone_number_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verify_phone_number_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verify_phone_number_heading.verify_phone_number_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verify_phone_number_heading.verify_phone_number_heading_${activeLanguageId}`
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
                                                        :for="`phone_no_description_text1_${activeLanguageId}`"
                                                        > Phone Number Description 1 </label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'phone_no_description_text1'
                                                        )
                                                    "
                                                    :ref="`phone_no_description_text1_${language.id}`"
                                                    :id="`phone_no_description_text1_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `phone_no_description_text1`
                                                        ][
                                                            `phone_no_description_text1_${language?.id}`
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
                                                        `phone_no_description_text1.phone_no_description_text1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_no_description_text1.phone_no_description_text1_${activeLanguageId}`
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
                                                        :for="`set_as_default_label_${activeLanguageId}`"
                                                        >Set As Default </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`set_as_default_label_${activeLanguageId}`"
                                                    :id="`set_as_default_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'set_as_default_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'set_as_default_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `set_as_default_label.set_as_default_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `set_as_default_label.set_as_default_label_${activeLanguageId}`
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
                                                        :for="`default_verified_number_label_${activeLanguageId}`"
                                                        >Default Verified  Number </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`default_verified_number_label_${activeLanguageId}`"
                                                    :id="`default_verified_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'default_verified_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'default_verified_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `default_verified_number_label.default_verified_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `default_verified_number_label.default_verified_number_label_${activeLanguageId}`
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
                                                        :for="`add_another_phone_number_title_${activeLanguageId}`"
                                                        >Add another phone number title</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`add_another_phone_number_title_${activeLanguageId}`"
                                                    :id="`add_another_phone_number_title_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'add_another_phone_number_title'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_another_phone_number_title'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `add_another_phone_number_title.add_another_phone_number_title_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `add_another_phone_number_title.add_another_phone_number_title_${activeLanguageId}`
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
    },
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/pages/';
            if (!base.endsWith('/')) base += '/';
            return base;
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-my-phone-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchMyPhoneSetting && this.fetchMyPhoneSetting(); }, 1000);
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
                            this.handleInput("", language, "phone_no_description_text");
                            this.handleInput("", language, "unverified_number_label");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "mobile_verify_button_text");
                            this.handleInput("", language, "web_send_verification_code_button_text");
                            this.handleInput("", language, "delete_button_text");
                            this.handleInput("", language, "mobile_country_code_label");
                            this.handleInput("", language, "country_code_label_web");
                            this.handleInput("", language, "country_id_label_web");
                            this.handleInput("", language, "country_code_placeholder");
                            this.handleInput("", language, "mobile_phone_number_label");
                            this.handleInput("", language, "phone_number_label_web");
                            this.handleInput("", language, "phone_number_placeholder");
                            this.handleInput("", language, "save_phoneno_button_text");
                            this.handleInput("", language, "send_verification_code_button_text");
                            this.handleInput("", language, "verify_phone_number_heading");
                            this.handleInput("", language, "otp_code_description");
                            this.handleInput("", language, "enter_code_label");
                            this.handleInput("", language, "verify_phone_number_label");
                            this.handleInput("", language, "second_text");
                            this.handleInput("", language, "request_code_text");
                            this.handleInput("", language, "resend_code_btn_label");
                            this.handleInput("", language, "set_as_default_label");
                            this.handleInput("", language, "default_verified_number_label");
                            this.handleInput("", language, "verified_number_label");
                            this.handleInput("", language, "phone_no_description_text1");
                            this.handleInput("", language, "add_another_phone_number_title");
                        });
                        this.fetchMyPhoneSetting();
                    }
                });
        },
        fetchMyPhoneSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-my-phone-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let my_phone_no_setting_detail =
                            res?.data?.data?.my_phone_no_setting_detail || [];
                        my_phone_no_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.phone_no_description_text,
                                setting?.language,
                                "phone_no_description_text"
                            );
                            this.handleInput(
                                setting?.unverified_number_label,
                                setting?.language,
                                "unverified_number_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.mobile_verify_button_text,
                                setting?.language,
                                "mobile_verify_button_text"
                            );
                            this.handleInput(
                                setting?.web_send_verification_code_button_text,
                                setting?.language,
                                "web_send_verification_code_button_text"
                            );
                             this.handleInput(
                                setting?.delete_button_text,
                                setting?.language,
                                "delete_button_text"
                            );

                            this.handleInput(
                                setting?.country_code_placeholder,
                                setting?.language,
                                "country_code_placeholder"
                            );
                            this.handleInput(
                                setting?.mobile_phone_number_label,
                                setting?.language,
                                "mobile_phone_number_label"
                            );
                            this.handleInput(
                                setting?.phone_number_label_web,
                                setting?.language,
                                "phone_number_label_web"
                            );
                             this.handleInput(
                                setting?.phone_number_placeholder,
                                setting?.language,
                                "phone_number_placeholder"
                            );
                              this.handleInput(
                                setting?.send_verification_code_button_text,
                                setting?.language,
                                "send_verification_code_button_text"
                            );
                            this.handleInput(
                                setting?.save_phoneno_button_text,
                                setting?.language,
                                "save_phoneno_button_text"
                            );
                            this.handleInput(
                                setting?.verify_phone_number_heading,
                                setting?.language,
                                "verify_phone_number_heading"
                            );
                            this.handleInput(
                                setting?.otp_code_description,
                                setting?.language,
                                "otp_code_description"
                            );
                            this.handleInput(
                                setting?.enter_code_label,
                                setting?.language,
                                "enter_code_label"
                            );
                            this.handleInput(
                                setting?.verify_phone_number_label,
                                setting?.language,
                                "verify_phone_number_label"
                            );
                            this.handleInput(
                                setting?.second_text,
                                setting?.language,
                                "second_text"
                            );
                            this.handleInput(
                                setting?.request_code_text,
                                setting?.language,
                                "request_code_text"
                            );
                            this.handleInput(
                                setting?.resend_code_btn_label,
                                setting?.language,
                                "resend_code_btn_label"
                            );
                            this.handleInput(
                                setting?.set_as_default_label,
                                setting?.language,
                                "set_as_default_label"
                            );
                            this.handleInput(
                                setting?.default_verified_number_label,
                                setting?.language,
                                "default_verified_number_label"
                            );
                            this.handleInput(
                                setting?.verified_number_label,
                                setting?.language,
                                "verified_number_label"
                            );
                            this.handleInput(
                                setting?.phone_no_description_text1,
                                setting?.language,
                                "phone_no_description_text1"
                            );
                            this.handleInput(
                                setting?.mobile_country_code_label,
                                setting?.language,
                                "mobile_country_code_label"
                            );
                            this.handleInput(
                                setting?.country_code_label_web,
                                setting?.language,
                                "country_code_label_web"
                            );
                            this.handleInput(
                                setting?.country_id_label_web,
                                setting?.language,
                                "country_id_label_web"
                            );

                            this.handleInput(
                                setting?.add_another_phone_number_title,
                                setting?.language,
                                "add_another_phone_number_title"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-my-phone-setting`,
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
                    `unverified_number_label.unverified_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_verify_button_text.mobile_verify_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `web_send_verification_code_button_text.web_send_verification_code_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `add_another_phone_number_title.add_another_phone_number_title_${language.id}`
                ) ||
                validationErros.has(
                    `delete_button_text.delete_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_country_code_label.mobile_country_code_label_${language.id}`
                ) ||
                validationErros.has(
                    `country_code_label_web.country_code_label_web_${language.id}`
                ) ||
                validationErros.has(
                    `country_id_label_web.country_id_label_web_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_phone_number_label.mobile_phone_number_label_${language.id}`
                ) ||

                validationErros.has(
                    `phone_number_label_web.phone_number_label_web_${language.id}`
                ) ||
                validationErros.has(
                    `phone_number_placeholder.phone_number_placeholder_${language.id}`
                )
                ||
                validationErros.has(
                    `save_phoneno_button_text.save_phoneno_button_text_${language.id}`
                )||
                validationErros.has(
                    `send_verification_code_button_text.send_verification_code_button_text_${language.id}`
                )
                  ||
                validationErros.has(
                    `verified_number_label.verified_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `default_verified_number_label.default_verified_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `set_as_default_label.set_as_default_label_${language.id}`
                ) ||
                validationErros.has(
                    `resend_code_btn_label.resend_code_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `request_code_text.request_code_text_${language.id}`
                ) ||
                validationErros.has(
                    `second_text.second_text_${language.id}`
                ) ||
                validationErros.has(
                    `verify_phone_number_label.verify_phone_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `enter_code_label.enter_code_label_${language.id}`
                ) ||
                validationErros.has(
                    `otp_code_description.otp_code_description_${language.id}`
                ) ||
                validationErros.has(
                    `verify_phone_number_heading.verify_phone_number_heading_${language.id}`
                )||
                validationErros.has(
                    `country_code_placeholder.country_code_placeholder_${language.id}`
                )
            );
        },
    },
};
</script>
