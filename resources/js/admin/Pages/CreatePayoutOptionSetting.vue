<template>
    <AppLayout>
        <section class="phone-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Payout Option settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Payout Option settings for a specific language.</p>

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
                                    <div class="flex items-center text-sm text-gray-600"><svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><span class="font-medium">Need a sample?</span></div>
                                    <a :href="`${mixAdminApiUrl}download-payout-option-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                    class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
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
                                                        :for="`bank_detail_heading_${activeLanguageId}`">
                                                        Bank Detail Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_detail_heading_${activeLanguageId}`"
                                                    :id="`bank_detail_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_detail_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_detail_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_detail_heading.bank_detail_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_detail_heading.bank_detail_heading_${activeLanguageId}`
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
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
                                        v-if="collapseStates[1]"
                                    >


                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    >Indicate Required Field (mobile)</label
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
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`mobile_paypal_indicate_required_label_${activeLanguageId}`"
                                                    >Indicate Required Field Paypal (mobile)</label
                                                >
                                            </div>
                                            <input
                                                    type="text"
                                                    :name="`mobile_paypal_indicate_required_label_${activeLanguageId}`"
                                                    :id="`mobile_paypal_indicate_required_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_paypal_indicate_required_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_paypal_indicate_required_label'
                                                        )
                                                    "
                                                />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `mobile_paypal_indicate_required_label.mobile_paypal_indicate_required_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `mobile_paypal_indicate_required_label.mobile_paypal_indicate_required_label_${activeLanguageId}`
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
                                                        :for="`paypal_detail_heading_${activeLanguageId}`"
                                                        >Paypal Detail Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_detail_heading_${activeLanguageId}`"
                                                    :id="`paypal_detail_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_detail_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_detail_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_detail_heading.paypal_detail_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_detail_heading.paypal_detail_heading_${activeLanguageId}`
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
                                                        :for="`web_bank_transfer_description_${activeLanguageId}`"
                                                        >Bank Transfer Description (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_bank_transfer_description_${activeLanguageId}`"
                                                    :id="`web_bank_transfer_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_bank_transfer_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_bank_transfer_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_bank_transfer_description.web_bank_transfer_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_bank_transfer_description.web_bank_transfer_description_${activeLanguageId}`
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
                                                        :for="`web_paypal_transfer_description_${activeLanguageId}`"
                                                        >Paypal Transfer Description (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_paypal_transfer_description_${activeLanguageId}`"
                                                    :id="`web_paypal_transfer_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_paypal_transfer_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_paypal_transfer_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_paypal_transfer_description.web_paypal_transfer_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_paypal_transfer_description.web_paypal_transfer_description_${activeLanguageId}`
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
                                                        :for="`paypal_email_label_${activeLanguageId}`"
                                                        >Paypal Email Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_email_label_${activeLanguageId}`"
                                                    :id="`paypal_email_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_email_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_email_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_email_label.paypal_email_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_email_label.paypal_email_label_${activeLanguageId}`
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
                                                        :for="`paypal_email_placeholder_${activeLanguageId}`"
                                                        >Paypal Email Palceholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_email_placeholder_${activeLanguageId}`"
                                                    :id="`paypal_email_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_email_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_email_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_email_placeholder.paypal_email_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_email_placeholder.paypal_email_placeholder_${activeLanguageId}`
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
                                                        :for="`paypal_set_default_checkbox_label_${activeLanguageId}`"
                                                        >Paypal Set Default label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_set_default_checkbox_label_${activeLanguageId}`"
                                                    :id="`paypal_set_default_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_set_default_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_set_default_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_set_default_checkbox_label.paypal_set_default_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_set_default_checkbox_label.paypal_set_default_checkbox_label_${activeLanguageId}`
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
                                                        :for="`web_payout_method_label_${activeLanguageId}`"
                                                        >Payout Method Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_payout_method_label_${activeLanguageId}`"
                                                    :id="`web_payout_method_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_payout_method_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_payout_method_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_payout_method_label.web_payout_method_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_payout_method_label.web_payout_method_label_${activeLanguageId}`
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
                                                        >Cousntry Code Placeholder</label
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
                                                        :for="`web_payout_method_placeholder_${activeLanguageId}`"
                                                        >Payout Method (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_payout_method_placeholder_${activeLanguageId}`"
                                                    :id="`web_payout_method_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_payout_method_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_payout_method_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_payout_method_placeholder.web_payout_method_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_payout_method_placeholder.web_payout_method_placeholder_${activeLanguageId}`
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
                                                        :for="`bank_name_label_${activeLanguageId}`"
                                                        >Bank Name Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_name_label_${activeLanguageId}`"
                                                    :id="`bank_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_name_label.bank_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_name_label.bank_name_label_${activeLanguageId}`
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
                                                        :for="`bank_name_placeholder_${activeLanguageId}`"
                                                        >Bank Name Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_name_placeholder_${activeLanguageId}`"
                                                    :id="`bank_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_name_placeholder.bank_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_name_placeholder.bank_name_placeholder_${activeLanguageId}`
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
                                                        :for="`bank_title_label_${activeLanguageId}`"
                                                        >Bank Title Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_title_label_${activeLanguageId}`"
                                                    :id="`bank_title_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_title_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_title_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_title_label.bank_title_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_title_label.bank_title_label_${activeLanguageId}`
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
                                                        :for="`bank_title_placeholder_${activeLanguageId}`"
                                                        >Bank Title Placheholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_title_placeholder_${activeLanguageId}`"
                                                    :id="`bank_title_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_title_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_title_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_title_placeholder.bank_title_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_title_placeholder.bank_title_placeholder_${activeLanguageId}`
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
                                                        :for="`branch_label_${activeLanguageId}`"
                                                        >Branch label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_label_${activeLanguageId}`"
                                                    :id="`branch_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_label.branch_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_label.branch_label_${activeLanguageId}`
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
                                                        :for="`branch_placeholder_${activeLanguageId}`"
                                                        >Branch Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_placeholder_${activeLanguageId}`"
                                                    :id="`branch_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_placeholder.branch_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_placeholder.branch_placeholder_${activeLanguageId}`
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
                                                        :for="`account_number_label_${activeLanguageId}`"
                                                        >Account Number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`account_number_label_${activeLanguageId}`"
                                                    :id="`account_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'account_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'account_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `account_number_label.account_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `account_number_label.account_number_label_${activeLanguageId}`
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
                                                        :for="`account_number_placeholder_${activeLanguageId}`"
                                                        >Account Number Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`account_number_placeholder_${activeLanguageId}`"
                                                    :id="`account_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'account_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'account_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `account_number_placeholder.account_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `account_number_placeholder.account_number_placeholder_${activeLanguageId}`
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
                                                        :for="`address_label_${activeLanguageId}`"
                                                        >Address Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`address_label_${activeLanguageId}`"
                                                    :id="`address_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'address_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'address_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `address_label.address_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `address_label.address_label_${activeLanguageId}`
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
                                                        :for="`address_placeholder_${activeLanguageId}`"
                                                        >Address Placheholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`address_placeholder_${activeLanguageId}`"
                                                    :id="`address_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'address_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'address_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `address_placeholder.address_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `address_placeholder.address_placeholder_${activeLanguageId}`
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
                                                        :for="`admin_sent_amount_placeholder_${activeLanguageId}`"
                                                        >Admin Sent Amount Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`admin_sent_amount_placeholder_${activeLanguageId}`"
                                                    :id="`admin_sent_amount_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'admin_sent_amount_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'admin_sent_amount_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `admin_sent_amount_placeholder.admin_sent_amount_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `admin_sent_amount_placeholder.admin_sent_amount_placeholder_${activeLanguageId}`
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
                                                        :for="`set_default_checkbox_label_${activeLanguageId}`"
                                                        >Set default Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`set_default_checkbox_label_${activeLanguageId}`"
                                                    :id="`set_default_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'set_default_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'set_default_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `set_default_checkbox_label.set_default_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `set_default_checkbox_label.set_default_checkbox_label_${activeLanguageId}`
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
                                                        :for="`verify_button_text_${activeLanguageId}`"
                                                        >verify button Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_button_text_${activeLanguageId}`"
                                                    :id="`verify_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verify_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verify_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verify_button_text.verify_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verify_button_text.verify_button_text_${activeLanguageId}`
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
                                                        :for="`paypal_account_heading_${activeLanguageId}`"
                                                        >Paypal Account Heading </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_account_heading_${activeLanguageId}`"
                                                    :id="`paypal_account_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_account_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_account_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_account_heading.paypal_account_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_account_heading.paypal_account_heading_${activeLanguageId}`
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
                                                        :for="`paypal_account_sub_heading_${activeLanguageId}`"
                                                        >Paypal Account SubHeading </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_account_sub_heading_${activeLanguageId}`"
                                                    :id="`paypal_account_sub_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_account_sub_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_account_sub_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_account_sub_heading.paypal_account_sub_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_account_sub_heading.paypal_account_sub_heading_${activeLanguageId}`
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
                                                        :for="`bank_account_heading_${activeLanguageId}`"
                                                        >Bank Account Heading </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_account_heading_${activeLanguageId}`"
                                                    :id="`bank_account_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_account_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_account_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_account_heading.bank_account_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_account_heading.bank_account_heading_${activeLanguageId}`
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
                                                        :for="`update_btn_label_${activeLanguageId}`"
                                                        >Update Button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`update_btn_label_${activeLanguageId}`"
                                                    :id="`update_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'update_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'update_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `update_btn_label.update_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `update_btn_label.update_btn_label_${activeLanguageId}`
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
                                                        :for="`save_btn_label_${activeLanguageId}`"
                                                        >Save Button Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`save_btn_label_${activeLanguageId}`"
                                                    :id="`save_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'save_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'save_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `save_btn_label.save_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `save_btn_label.save_btn_label_${activeLanguageId}`
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
                                                        :for="`institution_number_label_${activeLanguageId}`"
                                                        >Institution number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`institution_number_label_${activeLanguageId}`"
                                                    :id="`institution_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'institution_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'institution_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `institution_number_label.institution_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `institution_number_label.institution_number_label_${activeLanguageId}`
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
                                                        :for="`institution_number_placeholder_${activeLanguageId}`"
                                                        >Institution number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`institution_number_placeholder_${activeLanguageId}`"
                                                    :id="`institution_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'institution_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'institution_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `institution_number_placeholder.institution_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `institution_number_placeholder.institution_number_placeholder_${activeLanguageId}`
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
                                                        :for="`branch_address_label_${activeLanguageId}`"
                                                        >Branch address label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_address_label_${activeLanguageId}`"
                                                    :id="`branch_address_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_address_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_address_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_address_label.branch_address_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_address_label.branch_address_label_${activeLanguageId}`
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
                                                        :for="`branch_number_label_${activeLanguageId}`"
                                                        >Branch number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_number_label_${activeLanguageId}`"
                                                    :id="`branch_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_number_label.branch_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_number_label.branch_number_label_${activeLanguageId}`
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
                                                        :for="`branch_number_placeholder_${activeLanguageId}`"
                                                        >Branch number placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_number_placeholder_${activeLanguageId}`"
                                                    :id="`branch_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_number_placeholder.branch_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_number_placeholder.branch_number_placeholder_${activeLanguageId}`
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
                                                        :for="`branch_address_placeholder_${activeLanguageId}`"
                                                        >Branch address placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_address_placeholder_${activeLanguageId}`"
                                                    :id="`branch_address_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_address_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_address_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_address_placeholder.branch_address_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_address_placeholder.branch_address_placeholder_${activeLanguageId}`
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
                                                        :for="`account_address_placeholder_${activeLanguageId}`"
                                                        >Account address placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`account_address_placeholder_${activeLanguageId}`"
                                                    :id="`account_address_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'account_address_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'account_address_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `account_address_placeholder.account_address_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `account_address_placeholder.account_address_placeholder_${activeLanguageId}`
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
                                                        :for="`bank_error_${activeLanguageId}`"
                                                        >Bank name error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_error_${activeLanguageId}`"
                                                    :id="`bank_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_error.bank_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_error.bank_error_${activeLanguageId}`
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
                                                        :for="`institute_no_error_${activeLanguageId}`"
                                                        >Institution number error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`institute_no_error_${activeLanguageId}`"
                                                    :id="`institute_no_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'institute_no_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'institute_no_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `institute_no_error.institute_no_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `institute_no_error.institute_no_error_${activeLanguageId}`
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
                                                        :for="`branch_error_${activeLanguageId}`"
                                                        >Branch error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_error_${activeLanguageId}`"
                                                    :id="`branch_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_error.branch_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_error.branch_error_${activeLanguageId}`
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
                                                        :for="`branch_address_error_${activeLanguageId}`"
                                                        >Branch address error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_address_error_${activeLanguageId}`"
                                                    :id="`branch_address_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_address_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_address_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_address_error.branch_address_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_address_error.branch_address_error_${activeLanguageId}`
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
                                                        :for="`branch_no_error_${activeLanguageId}`"
                                                        >Branch number error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`branch_no_error_${activeLanguageId}`"
                                                    :id="`branch_no_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'branch_no_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'branch_no_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `branch_no_error.branch_no_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `branch_no_error.branch_no_error_${activeLanguageId}`
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
                                                        :for="`bank_title_error_${activeLanguageId}`"
                                                        >Account holder's name error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`bank_title_error_${activeLanguageId}`"
                                                    :id="`bank_title_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'bank_title_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'bank_title_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `bank_title_error.bank_title_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `bank_title_error.bank_title_error_${activeLanguageId}`
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
                                                        :for="`acc_no_error_${activeLanguageId}`"
                                                        >Account number error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`acc_no_error_${activeLanguageId}`"
                                                    :id="`acc_no_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'acc_no_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'acc_no_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `acc_no_error.acc_no_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `acc_no_error.acc_no_error_${activeLanguageId}`
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
                                                        :for="`address_error_${activeLanguageId}`"
                                                        >Account holder's address error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`address_error_${activeLanguageId}`"
                                                    :id="`address_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'address_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'address_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `address_error.address_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `address_error.address_error_${activeLanguageId}`
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
            excelForm: { selectedLanguageId: '', selectedFile: null },
            excelValidationErrors: {},
            excelErrors: [],
            excelUploading: false,
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
        },
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-payout-option-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchProfilePageSetting && this.fetchProfilePageSetting(); }, 1000);
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
                            this.handleInput("", language, "mobile_paypal_indicate_required_label");
                            this.handleInput("", language, "bank_detail_heading");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "paypal_detail_heading");
                            this.handleInput("", language, "web_bank_transfer_description");
                            this.handleInput("", language, "web_paypal_transfer_description");
                            this.handleInput("", language, "web_payout_method_label");
                            this.handleInput("", language, "country_code_placeholder");
                            this.handleInput("", language, "web_payout_method_placeholder");
                            this.handleInput("", language, "bank_name_label");
                            this.handleInput("", language, "bank_name_placeholder");
                            this.handleInput("", language, "bank_title_label");
                            this.handleInput("", language, "paypal_account_heading");
                            this.handleInput("", language, "paypal_account_sub_heading");
                            this.handleInput("", language, "verify_button_text");
                            this.handleInput("", language, "set_default_checkbox_label");
                            this.handleInput("", language, "admin_sent_amount_placeholder");
                            this.handleInput("", language, "address_placeholder");
                            this.handleInput("", language, "address_label");
                            this.handleInput("", language, "branch_placeholder");
                            this.handleInput("", language, "branch_label");
                            this.handleInput("", language, "account_number_placeholder");
                            this.handleInput("", language, "account_number_label");
                            this.handleInput("", language, "bank_title_placeholder");
                            this.handleInput("", language, "save_btn_label");
                            this.handleInput("", language, "bank_account_heading");
                            this.handleInput("", language, "update_btn_label");
                            this.handleInput("", language, "paypal_email_label");
                            this.handleInput("", language, "paypal_email_placeholder");
                            this.handleInput("", language, "paypal_set_default_checkbox_label");
                            this.handleInput("", language, "institution_number_label");
                            this.handleInput("", language, "institution_number_placeholder");
                            this.handleInput("", language, "branch_address_label");
                            this.handleInput("", language, "branch_number_label");
                            this.handleInput("", language, "branch_number_placeholder");
                            this.handleInput("", language, "branch_address_placeholder");
                            this.handleInput("", language, "account_address_placeholder");
                            this.handleInput("", language, "bank_error");
                            this.handleInput("", language, "institute_no_error");
                            this.handleInput("", language, "branch_error");
                            this.handleInput("", language, "branch_address_error");
                            this.handleInput("", language, "branch_no_error");
                            this.handleInput("", language, "bank_title_error");
                            this.handleInput("", language, "acc_no_error");
                            this.handleInput("", language, "address_error");
                        });
                        this.fetchProfilePageSetting();
                    }
                });
        },
        fetchProfilePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-payout-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let payout_option_setting_detail =
                            res?.data?.data?.payout_option_setting_detail || [];
                        payout_option_setting_detail.map((setting) => {
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
                                setting?.bank_detail_heading,
                                setting?.language,
                                "bank_detail_heading"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.paypal_detail_heading,
                                setting?.language,
                                "paypal_detail_heading"
                            );
                            this.handleInput(
                                setting?.web_bank_transfer_description,
                                setting?.language,
                                "web_bank_transfer_description"
                            );
                             this.handleInput(
                                setting?.web_paypal_transfer_description,
                                setting?.language,
                                "web_paypal_transfer_description"
                            );

                            this.handleInput(
                                setting?.country_code_placeholder,
                                setting?.language,
                                "country_code_placeholder"
                            );
                            this.handleInput(
                                setting?.web_payout_method_placeholder,
                                setting?.language,
                                "web_payout_method_placeholder"
                            );
                             this.handleInput(
                                setting?.bank_name_label,
                                setting?.language,
                                "bank_name_label"
                            );
                              this.handleInput(
                                setting?.bank_title_label,
                                setting?.language,
                                "bank_title_label"
                            );
                            this.handleInput(
                                setting?.bank_name_placeholder,
                                setting?.language,
                                "bank_name_placeholder"
                            );
                            this.handleInput(
                                setting?.paypal_account_heading,
                                setting?.language,
                                "paypal_account_heading"
                            );
                            this.handleInput(
                                setting?.paypal_account_sub_heading,
                                setting?.language,
                                "paypal_account_sub_heading"
                            );
                            this.handleInput(
                                setting?.verify_button_text,
                                setting?.language,
                                "verify_button_text"
                            );
                            this.handleInput(
                                setting?.set_default_checkbox_label,
                                setting?.language,
                                "set_default_checkbox_label"
                            );
                            this.handleInput(
                                setting?.admin_sent_amount_placeholder,
                                setting?.language,
                                "admin_sent_amount_placeholder"
                            );
                            this.handleInput(
                                setting?.address_placeholder,
                                setting?.language,
                                "address_placeholder"
                            );
                            this.handleInput(
                                setting?.address_label,
                                setting?.language,
                                "address_label"
                            );
                            this.handleInput(
                                setting?.branch_placeholder,
                                setting?.language,
                                "branch_placeholder"
                            );
                            this.handleInput(
                                setting?.branch_label,
                                setting?.language,
                                "branch_label"
                            );
                            this.handleInput(
                                setting?.account_number_placeholder,
                                setting?.language,
                                "account_number_placeholder"
                            );
                            this.handleInput(
                                setting?.account_number_label,
                                setting?.language,
                                "account_number_label"
                            );
                            this.handleInput(
                                setting?.bank_title_placeholder,
                                setting?.language,
                                "bank_title_placeholder"
                            );
                            this.handleInput(
                                setting?.bank_account_heading,
                                setting?.language,
                                "bank_account_heading"
                            );
                            this.handleInput(
                                setting?.update_btn_label,
                                setting?.language,
                                "update_btn_label"
                            );
                            this.handleInput(
                                setting?.save_btn_label,
                                setting?.language,
                                "save_btn_label"
                            );
                            this.handleInput(
                                setting?.mobile_paypal_indicate_required_label,
                                setting?.language,
                                "mobile_paypal_indicate_required_label"
                            );
                            this.handleInput(
                                setting?.paypal_email_label,
                                setting?.language,
                                "paypal_email_label"
                            );
                            this.handleInput(
                                setting?.paypal_email_placeholder,
                                setting?.language,
                                "paypal_email_placeholder"
                            );
                             this.handleInput(
                                setting?.paypal_set_default_checkbox_label,
                                setting?.language,
                                "paypal_set_default_checkbox_label"
                            );
                            this.handleInput(
                                setting?.web_payout_method_label,
                                setting?.language,
                                "web_payout_method_label"
                            );
                            this.handleInput(
                                setting?.country_code_placeholder,
                                setting?.language,
                                "country_code_placeholder"
                            );

                            this.handleInput(
                                setting?.institution_number_label,
                                setting?.language,
                                "institution_number_label"
                            );

                            this.handleInput(
                                setting?.institution_number_placeholder,
                                setting?.language,
                                "institution_number_placeholder"
                            );

                            this.handleInput(
                                setting?.branch_address_label,
                                setting?.language,
                                "branch_address_label"
                            );

                            this.handleInput(
                                setting?.branch_number_label,
                                setting?.language,
                                "branch_number_label"
                            );
                            
                            this.handleInput(
                                setting?.branch_number_placeholder,
                                setting?.language,
                                "branch_number_placeholder"
                            );

                            this.handleInput(
                                setting?.branch_address_placeholder,
                                setting?.language,
                                "branch_address_placeholder"
                            );

                            this.handleInput(
                                setting?.account_address_placeholder,
                                setting?.language,
                                "account_address_placeholder"
                            );
                            
                            
                            this.handleInput(
                                setting?.bank_error,
                                setting?.language,
                                "bank_error"
                            );
                            this.handleInput(
                                setting?.institute_no_error,
                                setting?.language,
                                "institute_no_error"
                            );
                            this.handleInput(
                                setting?.branch_error,
                                setting?.language,
                                "branch_error"
                            );
                            this.handleInput(
                                setting?.branch_address_error,
                                setting?.language,
                                "branch_address_error"
                            );
                            this.handleInput(
                                setting?.branch_no_error,
                                setting?.language,
                                "branch_no_error"
                            );
                            this.handleInput(
                                setting?.bank_title_error,
                                setting?.language,
                                "bank_title_error"
                            );
                            this.handleInput(
                                setting?.acc_no_error,
                                setting?.language,
                                "acc_no_error"
                            );
                            this.handleInput(
                                setting?.address_error,
                                setting?.language,
                                "address_error"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-payout-setting`,
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
                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${language.id}`
                ) ||
                validationErros.has(
                    `bank_detail_heading.bank_detail_heading_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `paypal_detail_heading.paypal_detail_heading_${language.id}`
                ) ||
                validationErros.has(
                    `web_bank_transfer_description.web_bank_transfer_description_${language.id}`
                ) ||
                validationErros.has(
                    `web_paypal_transfer_description.web_paypal_transfer_description_${language.id}`
                ) ||
                validationErros.has(
                    `web_payout_method_label.web_payout_method_label_${language.id}`
                ) ||
                validationErros.has(
                    `web_payout_method_placeholder.web_payout_method_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `bank_name_label.bank_name_label_${language.id}`
                )
                ||
                validationErros.has(
                    `bank_name_placeholder.bank_name_placeholder_${language.id}`
                )||
                validationErros.has(
                    `bank_title_label.bank_title_label_${language.id}`
                )
                ||
                validationErros.has(
                    `bank_title_placeholder.bank_title_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `account_number_label.account_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `account_number_placeholder.account_number_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `branch_label.branch_label_${language.id}`
                ) ||
                validationErros.has(
                    `branch_placeholder.branch_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `address_label.address_label_${language.id}`
                ) ||
                validationErros.has(
                    `address_placeholder.address_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `admin_sent_amount_placeholder.admin_sent_amount_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `set_default_checkbox_label.set_default_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `verify_button_text.verify_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `paypal_account_heading.paypal_account_heading_${language.id}`
                ) ||
                validationErros.has(
                    `paypal_account_sub_heading.paypal_account_sub_heading_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_paypal_indicate_required_label.mobile_paypal_indicate_required_label_${language.id}`
                ) ||
                validationErros.has(
                    `paypal_email_label.paypal_email_label_${language.id}`
                ) ||
                validationErros.has(
                    `paypal_email_placeholder.paypal_email_placeholder_${language.id}`
                )||
                validationErros.has(
                    `paypal_set_default_checkbox_label.paypal_set_default_checkbox_label_${language.id}`
                )
                ||
                validationErros.has(
                    `bank_account_heading.bank_account_heading_${language.id}`
                )
                ||
                validationErros.has(
                    `update_btn_label.update_btn_label_${language.id}`
                )
                ||
                validationErros.has(
                    `save_btn_label.save_btn_label_${language.id}`
                )
                ||
                validationErros.has(
                    `institution_number_label.institution_number_label_${language.id}`
                )
                ||
                validationErros.has(
                    `institution_number_placeholder.institution_number_placeholder_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_address_label.branch_address_label_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_number_label.branch_number_label_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_number_placeholder.branch_number_placeholder_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_address_placeholder.branch_address_placeholder_${language.id}`
                )
                ||
                validationErros.has(
                    `account_address_placeholder.account_address_placeholder_${language.id}`
                )
                


                ||
                validationErros.has(
                    `bank_error.bank_error_${language.id}`
                )
                ||
                validationErros.has(
                    `institute_no_error.institute_no_error_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_error.branch_error_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_address_error.branch_address_error_${language.id}`
                )
                ||
                validationErros.has(
                    `branch_no_error.branch_no_error_${language.id}`
                )
                ||
                validationErros.has(
                    `bank_title_error.bank_title_error_${language.id}`
                )
                ||
                validationErros.has(
                    `acc_no_error.acc_no_error_${language.id}`
                )
                ||
                validationErros.has(
                    `address_error.address_error_${language.id}`
                )
            );
        },
    },
};
</script>
