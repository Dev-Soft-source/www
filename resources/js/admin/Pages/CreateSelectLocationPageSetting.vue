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
                        <!-- Excel Upload Section -->
                        <div class="mt-6 mb-6">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    <h4 class="text-xl font-bold text-gray-800">📊 Excel Upload - Bulk Import Translations</h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Select Location page settings for a specific language.</p>

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
                                        <div class="flex items-center text-sm text-gray-600"><svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><span class="font-medium">Need a sample?</span></div>
                                        <a :href="`${mixAdminApiUrl}download-select-location-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
                                    </div>
                                </div>

                                <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded"><div class="flex items-start"><svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div class="flex-1"><h5 class="text-red-800 font-semibold mb-2">Validation Errors in Excel File:</h5><ul class="list-disc list-inside space-y-1"><li v-for="(error, index) in excelErrors" :key="index" class="text-sm text-red-700"><strong>Row {{ error.row }}:</strong> {{ error.attribute }} - {{ error.errors.join(', ') }}</li></ul></div></div></div>
                            </div>
                        </div>
                        <!-- End Excel Upload Section -->
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
            excelForm: { selectedLanguageId: '', selectedFile: null },
            excelValidationErrors: {},
            excelErrors: [],
            excelUploading: false,
        };
    },
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/pages/';
            if (!base.endsWith('/')) base += '/';
            return base;
        },
    },
    components: {
        editor: Editor,
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-select-location-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchTermsOfUsePageSetting && this.fetchTermsOfUsePageSetting(); }, 750);
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
