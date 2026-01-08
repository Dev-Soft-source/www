<template>
    <AppLayout>
        <section class="find-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Find ride page settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Find Ride page setting translations for a specific language.</p>

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
                                    <a :href="`${mixAdminApiUrl}download-find-ride-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
                                </div>
                            </div>

                            <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded"><div class="flex items-start"><svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div class="flex-1"><h5 class="text-red-800 font-semibold mb-2">Validation Errors in Excel File:</h5><ul class="list-disc list-inside space-y-1"><li v-for="(error, index) in excelErrors" :key="index" class="text-sm text-red-700"><strong>Row {{ error.row }}:</strong> {{ error.attribute }} - {{ error.errors.join(', ') }}</li></ul></div></div></div>
                        </div>
                    </div>
                    <!-- End Excel Upload Section -->

                    <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting()">
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                            <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                                <li class="mr-2" v-for="language in languages" :key="language.id">
                                    <a href="#" @click.prevent="
                                        updateLanguageId(language)
                                        " :class="[
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
                                        ]">{{ language.name }}</a>
                                </li>
                            </ul>
                        </div>
                        <template v-for="language in languages" :key="language.id">
                            <div v-if="
                                (activeLanguageId == null &&
                                    language.is_default) ||
                                language.id == activeLanguageId
                            ">
                                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`name_${activeLanguageId}`">Name</label>
                                            </div>
                                            <input type="text" :name="`name_${activeLanguageId}`"
                                                :id="`name_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue('name')" @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'name'
                                                    )
                                                    " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `name.name_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                                `name.name_${activeLanguageId}`
                                            )
                                                "></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`meta_description_${activeLanguageId}`">Meta
                                                    description</label>
                                            </div>
                                            <input type="text" :name="`meta_description_${activeLanguageId}`"
                                                :id="`meta_description_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue(
                                                    'meta_description'
                                                )
                                                    " @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'meta_description'
                                                    )
                                                    " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `meta_description.meta_description_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                                `meta_description.meta_description_${activeLanguageId}`
                                            )
                                                "></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`meta_keywords_${activeLanguageId}`">Meta keywords</label>
                                            </div>
                                            <input type="text" :name="`meta_keywords_${activeLanguageId}`"
                                                :id="`meta_keywords_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue(
                                                    'meta_keywords'
                                                )
                                                    " @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'meta_keywords'
                                                    )
                                                    " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `meta_keywords.meta_keywords_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                                `meta_keywords.meta_keywords_${activeLanguageId}`
                                            )
                                                "></p>
                                    </div>
                                </div>

                                <!-- main section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[0] =
                                            !collapseStates[0]
                                            ">
                                        <h3 class="text-white">
                                            Title section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[0]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`main_heading_${activeLanguageId}`">Main
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`main_heading_${activeLanguageId}`"
                                                    :id="`main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'main_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'main_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `main_heading.main_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `main_heading.main_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`extra_care_ride_page_label_${activeLanguageId}`">
                                                        Search extra-care ride only page label
                                                    </label>
                                                </div>
                                                <input type="text" :name="`extra_care_ride_page_label_${activeLanguageId}`"
                                                    :id="`extra_care_ride_page_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'extra_care_ride_page_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'extra_care_ride_page_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `extra_care_ride_page_label.extra_care_ride_page_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `extra_care_ride_page_label.extra_care_ride_page_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pink_ride_page_heading_${activeLanguageId}`">
                                                        Search pink ride only page heading
                                                    </label>
                                                </div>
                                                <input type="text" :name="`pink_ride_page_heading_${activeLanguageId}`"
                                                    :id="`pink_ride_page_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'pink_ride_page_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pink_ride_page_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `pink_ride_page_heading.pink_ride_page_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `pink_ride_page_heading.pink_ride_page_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pink_ride_page_label_${activeLanguageId}`">
                                                        Search pink ride only page label
                                                    </label>
                                                </div>
                                                <input type="text" :name="`pink_ride_page_label_${activeLanguageId}`"
                                                    :id="`pink_ride_page_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'pink_ride_page_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pink_ride_page_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `pink_ride_page_label.pink_ride_page_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `pink_ride_page_label.pink_ride_page_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`search_results_pink_ride_label_${activeLanguageId}`">
                                                        Search results for ProximaRide label
                                                    </label>
                                                </div>
                                                <input type="text" :name="`search_results_pink_ride_label_${activeLanguageId}`"
                                                    :id="`search_results_pink_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_results_pink_ride_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_results_pink_ride_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_results_pink_ride_label.search_results_pink_ride_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_results_pink_ride_label.search_results_pink_ride_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`search_results_extra_care_ride_label_${activeLanguageId}`">
                                                        Search results for extra care rides label
                                                    </label>
                                                </div>
                                                <input type="text" :name="`search_results_extra_care_ride_label_${activeLanguageId}`"
                                                    :id="`search_results_extra_care_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_results_extra_care_ride_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_results_extra_care_ride_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_results_extra_care_ride_label.search_results_extra_care_ride_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_results_extra_care_ride_label.search_results_extra_care_ride_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`more_rides_pink_ride_label_${activeLanguageId}`">
                                                        More rides label pink ride & extra care ride search page
                                                    </label>
                                                </div>
                                                <input type="text" :name="`more_rides_pink_ride_label_${activeLanguageId}`"
                                                    :id="`more_rides_pink_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'more_rides_pink_ride_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'more_rides_pink_ride_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `more_rides_pink_ride_label.more_rides_pink_ride_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `more_rides_pink_ride_label.more_rides_pink_ride_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`to_pink_ride_label_${activeLanguageId}`">
                                                        To label pink ride & extra care ride search page
                                                    </label>
                                                </div>
                                                <input type="text" :name="`to_pink_ride_label_${activeLanguageId}`"
                                                    :id="`to_pink_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'to_pink_ride_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'to_pink_ride_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `to_pink_ride_label.to_pink_ride_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `to_pink_ride_label.to_pink_ride_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`imp_pink_ride_label_${activeLanguageId}`">
                                                        Important label pink ride search page
                                                    </label>
                                                </div>
                                                <input type="text" :name="`imp_pink_ride_label_${activeLanguageId}`"
                                                    :id="`imp_pink_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'imp_pink_ride_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'imp_pink_ride_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `imp_pink_ride_label.imp_pink_ride_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `imp_pink_ride_label.imp_pink_ride_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`imp_extra_care_ride_label_${activeLanguageId}`">
                                                        Important label extra care ride search page
                                                    </label>
                                                </div>
                                                <input type="text" :name="`imp_extra_care_ride_label_${activeLanguageId}`"
                                                    :id="`imp_extra_care_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'imp_extra_care_ride_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'imp_extra_care_ride_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `imp_extra_care_ride_label.imp_extra_care_ride_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `imp_extra_care_ride_label.imp_extra_care_ride_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`navbar_icon_${activeLanguageId}`"
                                                            >Top menu Icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`navbar_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`navbar_icon_${activeLanguageId}`"
                                                        :id="`navbar_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'navbar_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `navbar_icon.navbar_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `navbar_icon.navbar_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['navbar_icon'] &&
                                                            form['navbar_icon'][`navbar_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['navbar_icon'] &&
                                                            form['navbar_icon'][`navbar_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['navbar_icon'][`navbar_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`from_field_icon_${activeLanguageId}`"
                                                            >From field Icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`from_field_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`from_field_icon_${activeLanguageId}`"
                                                        :id="`from_field_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'from_field_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `from_field_icon.from_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `from_field_icon.from_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['from_field_icon'] &&
                                                            form['from_field_icon'][`from_field_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['from_field_icon'] &&
                                                            form['from_field_icon'][`from_field_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['from_field_icon'][`from_field_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`swap_field_icon_${activeLanguageId}`"
                                                            >Swap field Icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`swap_field_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`swap_field_icon_${activeLanguageId}`"
                                                        :id="`swap_field_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'swap_field_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `swap_field_icon.swap_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `swap_field_icon.swap_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['swap_field_icon'] &&
                                                            form['swap_field_icon'][`swap_field_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['swap_field_icon'] &&
                                                            form['swap_field_icon'][`swap_field_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['swap_field_icon'][`swap_field_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`to_field_icon_${activeLanguageId}`"
                                                            >To field Icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`to_field_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`to_field_icon_${activeLanguageId}`"
                                                        :id="`to_field_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'to_field_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `to_field_icon.to_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `to_field_icon.to_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['to_field_icon'] &&
                                                            form['to_field_icon'][`to_field_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['to_field_icon'] &&
                                                            form['to_field_icon'][`to_field_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['to_field_icon'][`to_field_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`date_field_icon_${activeLanguageId}`"
                                                            >Date field Icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`date_field_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`date_field_icon_${activeLanguageId}`"
                                                        :id="`date_field_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'date_field_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `date_field_icon.date_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `date_field_icon.date_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['date_field_icon'] &&
                                                            form['date_field_icon'][`date_field_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['date_field_icon'] &&
                                                            form['date_field_icon'][`date_field_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['date_field_icon'][`date_field_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`search_field_icon_${activeLanguageId}`"
                                                            >Search field Icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`search_field_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`search_field_icon_${activeLanguageId}`"
                                                        :id="`search_field_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'search_field_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `search_field_icon.search_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `search_field_icon.search_field_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['search_field_icon'] &&
                                                            form['search_field_icon'][`search_field_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['search_field_icon'] &&
                                                            form['search_field_icon'][`search_field_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['search_field_icon'][`search_field_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- main section end -->

                                <!-- search section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[1] =
                                            !collapseStates[1]
                                            ">
                                        <h3 class="text-white">
                                            Search section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[1]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_from_placeholder_${activeLanguageId}`">From
                                                        placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_from_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_from_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_from_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_from_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_from_placeholder.search_section_from_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_from_placeholder.search_section_from_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`search_section_to_placeholder_${activeLanguageId}`">To
                                                        placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_to_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_to_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_to_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_to_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_to_placeholder.search_section_to_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_to_placeholder.search_section_to_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_date_placeholder_${activeLanguageId}`">Date
                                                        placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_date_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_date_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_date_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_date_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_date_placeholder.search_section_date_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_date_placeholder.search_section_date_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`search_section_pink_ride_label_${activeLanguageId}`"
                                                        >Search section pink ride label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_pink_ride_label_${activeLanguageId}`"
                                                    :id="`search_section_pink_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_pink_ride_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_pink_ride_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_pink_ride_label.search_section_pink_ride_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_pink_ride_label.search_section_pink_ride_label_${activeLanguageId}`
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
                                                        :for="`search_section_extra_care_label_${activeLanguageId}`"
                                                        >Search section extra care label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_extra_care_label_${activeLanguageId}`"
                                                    :id="`search_section_extra_care_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_extra_care_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_extra_care_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_extra_care_label.search_section_extra_care_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_extra_care_label.search_section_extra_care_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_required_error_${activeLanguageId}`">Required
                                                        error</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_required_error_${activeLanguageId}`"
                                                    :id="`search_section_required_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_required_error'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_required_error'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_required_error.search_section_required_error_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_required_error.search_section_required_error_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_keyword_label_${activeLanguageId}`">Keyword
                                                        label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_keyword_label_${activeLanguageId}`"
                                                    :id="`search_section_keyword_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_keyword_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_keyword_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_keyword_label.search_section_keyword_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_keyword_label.search_section_keyword_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_keyword_placeholder_${activeLanguageId}`">Keyword
                                                        placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_keyword_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_keyword_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_keyword_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_keyword_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_keyword_placeholder.search_section_keyword_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_keyword_placeholder.search_section_keyword_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_button_label_${activeLanguageId}`">Search
                                                        button label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_button_label_${activeLanguageId}`"
                                                    :id="`search_section_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_button_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_button_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_button_label.search_section_button_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_button_label.search_section_button_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_section_recent_searches_${activeLanguageId}`">Recent
                                                        Searches heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_section_recent_searches_${activeLanguageId}`"
                                                    :id="`search_section_recent_searches_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'search_section_recent_searches'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_recent_searches'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `search_section_recent_searches.search_section_recent_searches_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `search_section_recent_searches.search_section_recent_searches_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- search section end -->

                                <!-- ride card section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[2] =
                                            !collapseStates[2]
                                            ">
                                        <h3 class="text-white">
                                            Ride card section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[2]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`heading_ride_card_section_${activeLanguageId}`">
                                                        Heading</label>
                                                </div>
                                                <input type="text" :name="`heading_ride_card_section_${activeLanguageId}`"
                                                    :id="`heading_ride_card_section_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'heading_ride_card_section'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'heading_ride_card_section'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `heading_ride_card_section.heading_ride_card_section_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `heading_ride_card_section.heading_ride_card_section_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_from_label_${activeLanguageId}`">From
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_from_label_${activeLanguageId}`"
                                                    :id="`card_section_from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_from_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_from_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_from_label.card_section_from_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_from_label.card_section_from_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_to_label_${activeLanguageId}`">To
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_to_label_${activeLanguageId}`"
                                                    :id="`card_section_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_to_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_to_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_to_label.card_section_to_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_to_label.card_section_to_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_at_label_${activeLanguageId}`">At
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_at_label_${activeLanguageId}`"
                                                    :id="`card_section_at_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_at_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_at_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_at_label.card_section_at_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_at_label.card_section_at_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_seats_left_${activeLanguageId}`">Seats
                                                        left</label>
                                                </div>
                                                <input type="text" :name="`card_section_seats_left_${activeLanguageId}`"
                                                    :id="`card_section_seats_left_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_seats_left'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_seats_left'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_seats_left.card_section_seats_left_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_seats_left.card_section_seats_left_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_per_seat_${activeLanguageId}`">Per
                                                        seat</label>
                                                </div>
                                                <input type="text" :name="`card_section_per_seat_${activeLanguageId}`"
                                                    :id="`card_section_per_seat_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_per_seat'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_per_seat'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_per_seat.card_section_per_seat_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_per_seat.card_section_per_seat_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_booked_${activeLanguageId}`">Booked
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_booked_${activeLanguageId}`"
                                                    :id="`card_section_booked_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_booked'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_booked'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_booked.card_section_booked_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_booked.card_section_booked_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_seats_${activeLanguageId}`">Seats
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_seats_${activeLanguageId}`"
                                                    :id="`card_section_seats_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_seats'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_seats'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_seats.card_section_seats_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_seats.card_section_seats_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_booking_fee_${activeLanguageId}`">Total
                                                        booking fee</label>
                                                </div>
                                                <input type="text"
                                                    :name="`card_section_booking_fee_${activeLanguageId}`"
                                                    :id="`card_section_booking_fee_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_booking_fee'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_booking_fee'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_booking_fee.card_section_booking_fee_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_booking_fee.card_section_booking_fee_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_seats_fee_${activeLanguageId}`">Total
                                                        seats fee</label>
                                                </div>
                                                <input type="text" :name="`card_section_seats_fee_${activeLanguageId}`"
                                                    :id="`card_section_seats_fee_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_seats_fee'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_seats_fee'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_seats_fee.card_section_seats_fee_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_seats_fee.card_section_seats_fee_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_amount_${activeLanguageId}`">Total
                                                        amount</label>
                                                </div>
                                                <input type="text" :name="`card_section_amount_${activeLanguageId}`"
                                                    :id="`card_section_amount_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_amount'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_amount'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_amount.card_section_amount_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_amount.card_section_amount_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_driver_${activeLanguageId}`">Driver label
                                                        (Web)</label>
                                                </div>
                                                <input type="text" :name="`card_section_driver_${activeLanguageId}`"
                                                    :id="`card_section_driver_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_driver'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_driver'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_driver.card_section_driver_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_driver.card_section_driver_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_age_${activeLanguageId}`">Age
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_age_${activeLanguageId}`"
                                                    :id="`card_section_age_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_age'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_age'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_age.card_section_age_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_age.card_section_age_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_driven_${activeLanguageId}`">Driven
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_driven_${activeLanguageId}`"
                                                    :id="`card_section_driven_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_driven'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_driven'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_driven.card_section_driven_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_driven.card_section_driven_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`card_section_passengers_${activeLanguageId}`">Passengers
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_passengers_${activeLanguageId}`"
                                                    :id="`card_section_passengers_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_passengers'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_passengers'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_passengers.card_section_passengers_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_passengers.card_section_passengers_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_review_${activeLanguageId}`">Review
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_review_${activeLanguageId}`"
                                                    :id="`card_section_review_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_review'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_review'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_review.card_section_review_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_review.card_section_review_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_completed_${activeLanguageId}`">Completed
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`card_section_completed_${activeLanguageId}`"
                                                    :id="`card_section_completed_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_completed'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_completed'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_completed.card_section_completed_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_completed.card_section_completed_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`trips_card_section_seat_booked_${activeLanguageId}`">Seat
                                                        booked label (My trips)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`trips_card_section_seat_booked_${activeLanguageId}`"
                                                    :id="`trips_card_section_seat_booked_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'trips_card_section_seat_booked'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'trips_card_section_seat_booked'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `trips_card_section_seat_booked.trips_card_section_seat_booked_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `trips_card_section_seat_booked.trips_card_section_seat_booked_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`trips_card_section_seat_available_${activeLanguageId}`">Seat
                                                        available label (My trips)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`trips_card_section_seat_available_${activeLanguageId}`"
                                                    :id="`trips_card_section_seat_available_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'trips_card_section_seat_available'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'trips_card_section_seat_available'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `trips_card_section_seat_available.trips_card_section_seat_available_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `trips_card_section_seat_available.trips_card_section_seat_available_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`trips_card_section_review_driver_${activeLanguageId}`">Review
                                                        your driver button label (My trips)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`trips_card_section_review_driver_${activeLanguageId}`"
                                                    :id="`trips_card_section_review_driver_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'trips_card_section_review_driver'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'trips_card_section_review_driver'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `trips_card_section_review_driver.trips_card_section_review_driver_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `trips_card_section_review_driver.trips_card_section_review_driver_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`firm_cancellation_tooltip_${activeLanguageId}`">Firm cancellation policy tooltip label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`firm_cancellation_tooltip_${activeLanguageId}`"
                                                    :id="`firm_cancellation_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'firm_cancellation_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `firm_cancellation_tooltip.firm_cancellation_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `firm_cancellation_tooltip.firm_cancellation_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`hide_ride_popup_heading_${activeLanguageId}`">Hide ride confirmation popup heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`hide_ride_popup_heading_${activeLanguageId}`"
                                                    :id="`hide_ride_popup_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'hide_ride_popup_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hide_ride_popup_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `hide_ride_popup_heading.hide_ride_popup_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `hide_ride_popup_heading.hide_ride_popup_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`hide_ride_popup_text_${activeLanguageId}`">Hide ride confirmation popup text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`hide_ride_popup_text_${activeLanguageId}`"
                                                    :id="`hide_ride_popup_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'hide_ride_popup_text'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hide_ride_popup_text'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `hide_ride_popup_text.hide_ride_popup_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `hide_ride_popup_text.hide_ride_popup_text_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`hide_ride_popup_confirm_button_${activeLanguageId}`">Hide ride confirmation popup confirm button</label>
                                                </div>
                                                <input type="text"
                                                    :name="`hide_ride_popup_confirm_button_${activeLanguageId}`"
                                                    :id="`hide_ride_popup_confirm_button_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'hide_ride_popup_confirm_button'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hide_ride_popup_confirm_button'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `hide_ride_popup_confirm_button.hide_ride_popup_confirm_button_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `hide_ride_popup_confirm_button.hide_ride_popup_confirm_button_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`hide_ride_popup_take_me_back_button_${activeLanguageId}`">Hide ride confirmation popup take me back button</label>
                                                </div>
                                                <input type="text"
                                                    :name="`hide_ride_popup_take_me_back_button_${activeLanguageId}`"
                                                    :id="`hide_ride_popup_take_me_back_button_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'hide_ride_popup_take_me_back_button'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hide_ride_popup_take_me_back_button'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `hide_ride_popup_take_me_back_button.hide_ride_popup_take_me_back_button_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `hide_ride_popup_take_me_back_button.hide_ride_popup_take_me_back_button_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- ride card section end -->

                                <!-- filter section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[3] =
                                            !collapseStates[3]
                                            ">
                                        <h3 class="text-white">
                                            Filters section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[3]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`filter_section_heading_${activeLanguageId}`">Main
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`filter_section_heading_${activeLanguageId}`"
                                                    :id="`filter_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'filter_section_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter_section_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `filter_section_heading.filter_section_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `filter_section_heading.filter_section_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`filter1_driver_heading_${activeLanguageId}`">Driver
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`filter1_driver_heading_${activeLanguageId}`"
                                                    :id="`filter1_driver_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'filter1_driver_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter1_driver_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `filter1_driver_heading.filter1_driver_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `filter1_driver_heading.filter1_driver_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_age_label_${activeLanguageId}`">Driver age
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`driver_age_label_${activeLanguageId}`"
                                                    :id="`driver_age_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_age_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_age_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_age_label.driver_age_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_age_label.driver_age_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_age_placeholder_${activeLanguageId}`">Driver
                                                        age placeholder</label>
                                                </div>
                                                <input type="text" :name="`driver_age_placeholder_${activeLanguageId}`"
                                                    :id="`driver_age_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_age_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_age_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_age_placeholder.driver_age_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_age_placeholder.driver_age_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_rating_label_${activeLanguageId}`">Driver
                                                        rating label</label>
                                                </div>
                                                <input type="text" :name="`driver_rating_label_${activeLanguageId}`"
                                                    :id="`driver_rating_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_rating_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_rating_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_rating_label.driver_rating_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_rating_label.driver_rating_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_rating_placeholder_${activeLanguageId}`">Driver
                                                        rating placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`driver_rating_placeholder_${activeLanguageId}`"
                                                    :id="`driver_rating_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_rating_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_rating_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_rating_placeholder.driver_rating_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_rating_placeholder.driver_rating_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_phone_access_label_${activeLanguageId}`">Driver
                                                        phone access label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`driver_phone_access_label_${activeLanguageId}`"
                                                    :id="`driver_phone_access_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_phone_access_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_phone_access_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_phone_access_label.driver_phone_access_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_phone_access_label.driver_phone_access_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_know_label_${activeLanguageId}`">Driver know
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`driver_know_label_${activeLanguageId}`"
                                                    :id="`driver_know_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_know_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_know_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_know_label.driver_know_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_know_label.driver_know_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_know_placeholder_${activeLanguageId}`">Driver
                                                        know placeholder</label>
                                                </div>
                                                <input type="text" :name="`driver_know_placeholder_${activeLanguageId}`"
                                                    :id="`driver_know_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_know_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_know_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_know_placeholder.driver_know_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `driver_know_placeholder.driver_know_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`filter2_passengers_heading_${activeLanguageId}`">Passengers
                                                        heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`filter2_passengers_heading_${activeLanguageId}`"
                                                    :id="`filter2_passengers_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'filter2_passengers_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter2_passengers_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `filter2_passengers_heading.filter2_passengers_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `filter2_passengers_heading.filter2_passengers_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passengers_rating_label_${activeLanguageId}`">Passengers
                                                        rating label</label>
                                                </div>
                                                <input type="text" :name="`passengers_rating_label_${activeLanguageId}`"
                                                    :id="`passengers_rating_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'passengers_rating_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passengers_rating_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `passengers_rating_label.passengers_rating_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `passengers_rating_label.passengers_rating_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passengers_rating_placeholder_${activeLanguageId}`">Passengers
                                                        rating placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passengers_rating_placeholder_${activeLanguageId}`"
                                                    :id="`passengers_rating_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'passengers_rating_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passengers_rating_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `passengers_rating_placeholder.passengers_rating_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `passengers_rating_placeholder.passengers_rating_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`filter3_payment_methods_heading_${activeLanguageId}`">Payment
                                                        methods heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`filter3_payment_methods_heading_${activeLanguageId}`"
                                                    :id="`filter3_payment_methods_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'filter3_payment_methods_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter3_payment_methods_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `filter3_payment_methods_heading.filter3_payment_methods_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `filter3_payment_methods_heading.filter3_payment_methods_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`payment_methods_label_${activeLanguageId}`">Payment
                                                        methods label</label>
                                                </div>
                                                <input type="text" :name="`payment_methods_label_${activeLanguageId}`"
                                                    :id="`payment_methods_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_label.payment_methods_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_label.payment_methods_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`payment_methods_option1_${activeLanguageId}`">Payment
                                                        methods option1</label>
                                                </div>
                                                <input type="text" :name="`payment_methods_option1_${activeLanguageId}`"
                                                    :id="`payment_methods_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option1'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option1'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option1.payment_methods_option1_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option1.payment_methods_option1_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`filter4_vehicle_heading_${activeLanguageId}`">Vehicle
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`filter4_vehicle_heading_${activeLanguageId}`"
                                                    :id="`filter4_vehicle_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'filter4_vehicle_heading'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter4_vehicle_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `filter4_vehicle_heading.filter4_vehicle_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `filter4_vehicle_heading.filter4_vehicle_heading_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`vehicle_type_label_${activeLanguageId}`">Vehicle type
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`vehicle_type_label_${activeLanguageId}`"
                                                    :id="`vehicle_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'vehicle_type_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `vehicle_type_label.vehicle_type_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `vehicle_type_label.vehicle_type_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`vehicle_type_placeholder_${activeLanguageId}`">Vehicle
                                                        type placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    :id="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'vehicle_type_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`luggage_placeholder_${activeLanguageId}`">Ride
                                                        preferences label</label>
                                                </div>
                                                <input type="text" :name="`luggage_placeholder_${activeLanguageId}`"
                                                    :id="`luggage_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'luggage_placeholder'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'luggage_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `luggage_placeholder.luggage_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `luggage_placeholder.luggage_placeholder_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`luggage_label_${activeLanguageId}`">Luggage
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`luggage_label_${activeLanguageId}`"
                                                    :id="`luggage_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'luggage_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'luggage_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `luggage_label.luggage_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `luggage_label.luggage_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`smoking_label_${activeLanguageId}`">Smoking
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`smoking_label_${activeLanguageId}`"
                                                    :id="`smoking_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'smoking_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `smoking_label.smoking_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `smoking_label.smoking_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pets_allowed_label_${activeLanguageId}`">Pets
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`pets_allowed_label_${activeLanguageId}`"
                                                    :id="`pets_allowed_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'pets_allowed_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pets_allowed_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `pets_allowed_label.pets_allowed_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `pets_allowed_label.pets_allowed_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`apply_button_label_${activeLanguageId}`">Apply Button
                                                        label (app)</label>
                                                </div>
                                                <input type="text" :name="`apply_button_label_${activeLanguageId}`"
                                                    :id="`apply_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'apply_button_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'apply_button_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `apply_button_label.apply_button_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `apply_button_label.apply_button_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`clear_button_label_${activeLanguageId}`">Clear Button
                                                        label (app)</label>
                                                </div>
                                                <input type="text" :name="`clear_button_label_${activeLanguageId}`"
                                                    :id="`clear_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'clear_button_label'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'clear_button_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `clear_button_label.clear_button_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `clear_button_label.clear_button_label_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_no_review_${activeLanguageId}`">Card No
                                                        Review label (app)</label>
                                                </div>
                                                <input type="text" :name="`card_section_no_review_${activeLanguageId}`"
                                                    :id="`card_section_no_review_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_no_review'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_no_review'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_no_review.card_section_no_review_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_no_review.card_section_no_review_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_cancelled_${activeLanguageId}`">Card
                                                        Section Cancelled</label>
                                                </div>
                                                <input type="text" :name="`card_section_cancelled_${activeLanguageId}`"
                                                    :id="`card_section_cancelled_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'card_section_cancelled'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_cancelled'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `card_section_cancelled.card_section_cancelled_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `card_section_cancelled.card_section_cancelled_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_result_load_more_btn_${activeLanguageId}`">Search
                                                        Result Load More Button</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_result_load_more_btn_${activeLanguageId}`"
                                                    :id="`search_result_load_more_btn_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('search_result_load_more_btn')"
                                                    @input="handleInput($event.target.value, language, 'search_result_load_more_btn')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_result_load_more_btn.search_result_load_more_btn_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_result_load_more_btn.search_result_load_more_btn_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_result_no_more_data_message_${activeLanguageId}`">Search
                                                        Result No More Data Message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_result_no_more_data_message_${activeLanguageId}`"
                                                    :id="`search_result_no_more_data_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('search_result_no_more_data_message')"
                                                    @input="handleInput($event.target.value, language, 'search_result_no_more_data_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_result_no_more_data_message.search_result_no_more_data_message_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_result_no_more_data_message.search_result_no_more_data_message_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_result_no_found_message_${activeLanguageId}`">Search
                                                        Result No Found Message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_result_no_found_message_${activeLanguageId}`"
                                                    :id="`search_result_no_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('search_result_no_found_message')"
                                                    @input="handleInput($event.target.value, language, 'search_result_no_found_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_result_no_found_message.search_result_no_found_message_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_result_no_found_message.search_result_no_found_message_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`search_result_label_${activeLanguageId}`">Search
                                                        Result Label</label>
                                                </div>
                                                <input type="text" :name="`search_result_label_${activeLanguageId}`"
                                                    :id="`search_result_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('search_result_label')"
                                                    @input="handleInput($event.target.value, language, 'search_result_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_result_label.search_result_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_result_label.search_result_label_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <!-- Repeat the same structure for the other fields -->

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`filter_what_label_${activeLanguageId}`">Filter What
                                                        Label</label>
                                                </div>
                                                <input type="text" :name="`filter_what_label_${activeLanguageId}`"
                                                    :id="`filter_what_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('filter_what_label')"
                                                    @input="handleInput($event.target.value, language, 'filter_what_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`filter_what_label.filter_what_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`filter_what_label.filter_what_label_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`search_and_above_label_${activeLanguageId}`">Search
                                                        And Above Label</label>
                                                </div>
                                                <input type="text" :name="`search_and_above_label_${activeLanguageId}`"
                                                    :id="`search_and_above_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('search_and_above_label')"
                                                    @input="handleInput($event.target.value, language, 'search_and_above_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_and_above_label.search_and_above_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_and_above_label.search_and_above_label_${activeLanguageId}`)">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`search_filter_all_label_${activeLanguageId}`">Search
                                                        Filter All Label</label>
                                                </div>
                                                <input type="text" :name="`search_filter_all_label_${activeLanguageId}`"
                                                    :id="`search_filter_all_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('search_filter_all_label')"
                                                    @input="handleInput($event.target.value, language, 'search_filter_all_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_filter_all_label.search_filter_all_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_filter_all_label.search_filter_all_label_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`search_filter_select_vehicle_label_${activeLanguageId}`">Search
                                                        Filter Select Vehicle Label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`search_filter_select_vehicle_label_${activeLanguageId}`"
                                                    :id="`search_filter_select_vehicle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('search_filter_select_vehicle_label')"
                                                    @input="handleInput($event.target.value, language, 'search_filter_select_vehicle_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`search_filter_select_vehicle_label.search_filter_select_vehicle_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`search_filter_select_vehicle_label.search_filter_select_vehicle_label_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`card_section_not_live_${activeLanguageId}`">Card
                                                        Section Not Live</label>
                                                </div>
                                                <input type="text" :name="`card_section_not_live_${activeLanguageId}`"
                                                    :id="`card_section_not_live_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('card_section_not_live')"
                                                    @input="handleInput($event.target.value, language, 'card_section_not_live')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`card_section_not_live.card_section_not_live_${activeLanguageId}`)"
                                                v-text="validationErros.get(`card_section_not_live.card_section_not_live_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`card_section_booking_request_${activeLanguageId}`">Card
                                                        Section Booking Request</label>
                                                </div>
                                                <input type="text"
                                                    :name="`card_section_booking_request_${activeLanguageId}`"
                                                    :id="`card_section_booking_request_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('card_section_booking_request')"
                                                    @input="handleInput($event.target.value, language, 'card_section_booking_request')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`card_section_booking_request.card_section_booking_request_${activeLanguageId}`)"
                                                v-text="validationErros.get(`card_section_booking_request.card_section_booking_request_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`trips_card_section_reviewed_${activeLanguageId}`">Trips
                                                        Card Section Reviewed</label>
                                                </div>
                                                <input type="text"
                                                    :name="`trips_card_section_reviewed_${activeLanguageId}`"
                                                    :id="`trips_card_section_reviewed_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('trips_card_section_reviewed')"
                                                    @input="handleInput($event.target.value, language, 'trips_card_section_reviewed')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`trips_card_section_reviewed.trips_card_section_reviewed_${activeLanguageId}`)"
                                                v-text="validationErros.get(`trips_card_section_reviewed.trips_card_section_reviewed_${activeLanguageId}`)">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`ride_preferences_label_${activeLanguageId}`">
                                                        Ride Preference Label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`ride_preferences_label_${activeLanguageId}`"
                                                    :id="`ride_preferences_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('ride_preferences_label')"
                                                    @input="handleInput($event.target.value, language, 'ride_preferences_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`ride_preferences_label.ride_preferences_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`ride_preferences_label.ride_preferences_label_${activeLanguageId}`)">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`trips_card_section_reviewed_${activeLanguageId}`">Trips
                                                        Card Section Reviewed</label>
                                                </div>
                                                <input type="text"
                                                    :name="`trips_card_section_reviewed_${activeLanguageId}`"
                                                    :id="`trips_card_section_reviewed_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('trips_card_section_reviewed')"
                                                    @input="handleInput($event.target.value, language, 'trips_card_section_reviewed')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`trips_card_section_reviewed.trips_card_section_reviewed_${activeLanguageId}`)"
                                                v-text="validationErros.get(`trips_card_section_reviewed.trips_card_section_reviewed_${activeLanguageId}`)">
                                            </p>
                                        </div>


                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`filter_search_btn_label_${activeLanguageId}`">
                                                        Filter Search Button Label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`filter_search_btn_label_${activeLanguageId}`"
                                                    :id="`filter_search_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('filter_search_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'filter_search_btn_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`filter_search_btn_label.filter_search_btn_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`filter_search_btn_label.filter_search_btn_label_${activeLanguageId}`)">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`filter_close_btn_label_${activeLanguageId}`">
                                                        Close Button Label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`filter_close_btn_label_${activeLanguageId}`"
                                                    :id="`filter_close_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('filter_close_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'filter_close_btn_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has(`filter_close_btn_label.filter_close_btn_label_${activeLanguageId}`)"
                                                v-text="validationErros.get(`filter_close_btn_label.filter_close_btn_label_${activeLanguageId}`)">
                                            </p>
                                        </div>


                                    </div>
                                </div>
                                <!-- filter section end -->
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
                bullist numlist outdent indent | removeformat | table | image | code | help",
            },
        };
    },
    components: {
        editor: Editor,
    },
    created() {
        this.fetchLanguages();
    },
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/pages/';
            if (!base.endsWith('/')) base += '/';
            return base;
        }
    },
    methods: {
        handleImage(e, language, key) {
            // console.log(e.target.files[0], key, language);
            var file = new FormData();
            file.append("file", e.target.files[0]);
            axios.post("/api/admin/media/image_again_upload", file).then((res) => {
                this.handleInput(res?.data, language, key);
            });
        },
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-find-ride-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
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
                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "pink_ride_page_label");
                            this.handleInput("", language, "search_results_pink_ride_label");
                            this.handleInput("", language, "search_results_extra_care_ride_label");
                            this.handleInput("", language, "more_rides_pink_ride_label");
                            this.handleInput("", language, "to_pink_ride_label");
                            this.handleInput("", language, "imp_pink_ride_label");
                            this.handleInput("", language, "imp_extra_care_ride_label");
                            this.handleInput("", language, "extra_care_ride_page_label");
                            this.handleInput("", language, "pink_ride_page_heading");
                            this.handleInput("", language, "navbar_icon");
                            this.handleInput("", language, "from_field_icon");
                            this.handleInput("", language, "swap_field_icon");
                            this.handleInput("", language, "to_field_icon");
                            this.handleInput("", language, "date_field_icon");
                            this.handleInput("", language, "search_field_icon");
                            this.handleInput("", language, "search_section_from_placeholder");
                            this.handleInput("", language, "search_section_to_placeholder");
                            this.handleInput("", language, "search_section_pink_ride_label");
                            this.handleInput("", language, "search_section_extra_care_label");
                            this.handleInput("", language, "search_section_date_placeholder");
                            this.handleInput("", language, "search_section_required_error");
                            this.handleInput("", language, "search_section_keyword_label");
                            this.handleInput("", language, "search_section_keyword_placeholder");
                            this.handleInput("", language, "search_section_button_label");
                            this.handleInput("", language, "search_section_recent_searches");
                            this.handleInput("", language, "card_section_from_label");
                            this.handleInput("", language, "card_section_to_label");
                            this.handleInput("", language, "card_section_at_label");
                            this.handleInput("", language, "card_section_seats_left");
                            this.handleInput("", language, "card_section_per_seat");
                            this.handleInput("", language, "heading_ride_card_section");
                            this.handleInput("", language, "card_section_booked");
                            this.handleInput("", language, "card_section_seats");
                            this.handleInput("", language, "card_section_booking_fee");
                            this.handleInput("", language, "clear_button_label");
                            this.handleInput("", language, "apply_button_label");
                            this.handleInput("", language, "card_section_driver");
                            this.handleInput("", language, "card_section_age");
                            this.handleInput("", language, "card_section_driven");
                            this.handleInput("", language, "card_section_passengers");
                            this.handleInput("", language, "card_section_review");
                            this.handleInput("", language, "card_section_completed");
                            this.handleInput("", language, "trips_card_section_seat_booked");
                            this.handleInput("", language, "trips_card_section_seat_available");
                            this.handleInput("", language, "trips_card_section_review_driver");
                            this.handleInput("", language, "firm_cancellation_tooltip");
                            this.handleInput("", language, "filter_section_heading");
                            this.handleInput("", language, "filter1_driver_heading");
                            this.handleInput("", language, "driver_age_label");
                            this.handleInput("", language, "driver_age_placeholder");
                            this.handleInput("", language, "driver_rating_label");
                            this.handleInput("", language, "driver_rating_placeholder");
                            this.handleInput("", language, "driver_phone_access_label");
                            this.handleInput("", language, "driver_know_label");
                            this.handleInput("", language, "driver_know_placeholder");
                            this.handleInput("", language, "filter2_passengers_heading");
                            this.handleInput("", language, "passengers_rating_label");
                            this.handleInput("", language, "passengers_rating_placeholder");
                            this.handleInput("", language, "filter3_payment_methods_heading");
                            this.handleInput("", language, "payment_methods_label");
                            this.handleInput("", language, "payment_methods_option1");
                            this.handleInput("", language, "payment_methods_option2");
                            this.handleInput("", language, "payment_methods_option3");
                            this.handleInput("", language, "payment_methods_option4");
                            this.handleInput("", language, "filter4_vehicle_heading");
                            this.handleInput("", language, "vehicle_type_label");
                            this.handleInput("", language, "vehicle_type_placeholder");
                            this.handleInput("", language, "ride_features_option1");
                            this.handleInput("", language, "ride_features_option2");
                            this.handleInput("", language, "ride_features_option3");
                            this.handleInput("", language, "ride_features_option4");
                            this.handleInput("", language, "ride_features_option5");
                            this.handleInput("", language, "ride_features_option6");
                            this.handleInput("", language, "ride_features_option7");
                            this.handleInput("", language, "ride_features_option8");
                            this.handleInput("", language, "ride_preferences_label");
                            this.handleInput("", language, "ride_features_option9");
                            this.handleInput("", language, "ride_features_option10");
                            this.handleInput("", language, "ride_features_option11");
                            this.handleInput("", language, "ride_features_option12");
                            this.handleInput("", language, "ride_features_option13");
                            this.handleInput("", language, "ride_features_option14");
                            this.handleInput("", language, "ride_features_option15");
                            this.handleInput("", language, "ride_features_option16");
                            this.handleInput("", language, "ride_features_option17");
                            this.handleInput("", language, "luggage_label");
                            this.handleInput("", language, "luggage_placeholder");
                            this.handleInput("", language, "smoking_label");
                            this.handleInput("", language, "smoking_option1");
                            this.handleInput("", language, "smoking_option2");
                            this.handleInput("", language, "pets_allowed_label");
                            this.handleInput("", language, "pets_allowed_option1");
                            this.handleInput("", language, "pets_allowed_option2");
                            this.handleInput("", language, "pets_allowed_option3");
                            this.handleInput("", language, "card_section_no_review");
                            this.handleInput("", language, "card_section_seats_fee");
                            this.handleInput("", language, "card_section_amount");
                            this.handleInput("", language, "card_section_cancelled");
                            this.handleInput("", language, "search_result_load_more_btn");
                            this.handleInput("", language, "search_result_no_more_data_message");
                            this.handleInput("", language, "search_result_no_found_message");
                            this.handleInput("", language, "search_result_label");
                            this.handleInput("", language, "filter_what_label");
                            this.handleInput("", language, "search_and_above_label");
                            this.handleInput("", language, "search_filter_all_label");
                            this.handleInput("", language, "search_filter_select_vehicle_label");
                            this.handleInput("", language, "card_section_not_live");
                            this.handleInput("", language, "card_section_booking_request");
                            this.handleInput("", language, "trips_card_section_reviewed");
                            this.handleInput("", language, "filter_search_btn_label");
                            this.handleInput("", language, "filter_close_btn_label");
                            this.handleInput("", language, "hide_ride_popup_heading");
                            this.handleInput("", language, "hide_ride_popup_text");
                            this.handleInput("", language, "hide_ride_popup_confirm_button");
                            this.handleInput("", language, "hide_ride_popup_take_me_back_button");

                        });
                        this.fetchFindRidePageSetting();
                    }
                });
        },
        fetchFindRidePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-find-ride-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let find_ride_page_setting_detail =
                            res?.data?.data?.find_ride_page_setting_detail || [];
                        find_ride_page_setting_detail.map((setting) => {
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
                                setting?.pink_ride_page_label,
                                setting?.language,
                                "pink_ride_page_label"
                            );
                            this.handleInput(
                                setting?.search_results_pink_ride_label,
                                setting?.language,
                                "search_results_pink_ride_label"
                            );
                            this.handleInput(
                                setting?.search_results_extra_care_ride_label,
                                setting?.language,
                                "search_results_extra_care_ride_label"
                            );
                            this.handleInput(
                                setting?.more_rides_pink_ride_label,
                                setting?.language,
                                "more_rides_pink_ride_label"
                            );
                            this.handleInput(
                                setting?.to_pink_ride_label,
                                setting?.language,
                                "to_pink_ride_label"
                            );
                            this.handleInput(
                                setting?.imp_pink_ride_label,
                                setting?.language,
                                "imp_pink_ride_label"
                            );
                            this.handleInput(
                                setting?.imp_extra_care_ride_label,
                                setting?.language,
                                "imp_extra_care_ride_label"
                            );
                            this.handleInput(
                                setting?.pink_ride_page_heading,
                                setting?.language,
                                "pink_ride_page_heading"
                            );
                            this.handleInput(
                                setting?.extra_care_ride_page_label,
                                setting?.language,
                                "extra_care_ride_page_label"
                            );
                            this.handleInput(
                                setting?.navbar_icon,
                                setting?.language,
                                "navbar_icon"
                            );
                            this.handleInput(
                                setting?.from_field_icon,
                                setting?.language,
                                "from_field_icon"
                            );
                            this.handleInput(
                                setting?.swap_field_icon,
                                setting?.language,
                                "swap_field_icon"
                            );
                            this.handleInput(
                                setting?.to_field_icon,
                                setting?.language,
                                "to_field_icon"
                            );
                            this.handleInput(
                                setting?.date_field_icon,
                                setting?.language,
                                "date_field_icon"
                            );
                            this.handleInput(
                                setting?.search_field_icon,
                                setting?.language,
                                "search_field_icon"
                            );
                            this.handleInput(
                                setting?.search_section_from_placeholder,
                                setting?.language,
                                "search_section_from_placeholder"
                            );
                            this.handleInput(
                                setting?.card_section_seats_fee,
                                setting?.language,
                                "card_section_seats_fee"
                            );
                            this.handleInput(
                                setting?.card_section_amount,
                                setting?.language,
                                "card_section_amount"
                            );
                            this.handleInput(
                                setting?.search_section_to_placeholder,
                                setting?.language,
                                "search_section_to_placeholder"
                            );
                            this.handleInput(
                                setting?.search_section_pink_ride_label,
                                setting?.language,
                                "search_section_pink_ride_label"
                            );
                            this.handleInput(
                                setting?.search_section_extra_care_label,
                                setting?.language,
                                "search_section_extra_care_label"
                            );
                            this.handleInput(
                                setting?.search_section_date_placeholder,
                                setting?.language,
                                "search_section_date_placeholder"
                            );
                            this.handleInput(
                                setting?.search_section_required_error,
                                setting?.language,
                                "search_section_required_error"
                            );
                            this.handleInput(
                                setting?.search_section_keyword_label,
                                setting?.language,
                                "search_section_keyword_label"
                            );
                            this.handleInput(
                                setting?.search_section_keyword_placeholder,
                                setting?.language,
                                "search_section_keyword_placeholder"
                            );
                            this.handleInput(
                                setting?.search_section_button_label,
                                setting?.language,
                                "search_section_button_label"
                            );
                            this.handleInput(
                                setting?.search_section_recent_searches,
                                setting?.language,
                                "search_section_recent_searches"
                            );
                            this.handleInput(
                                setting?.card_section_from_label,
                                setting?.language,
                                "card_section_from_label"
                            );
                            this.handleInput(
                                setting?.card_section_to_label,
                                setting?.language,
                                "card_section_to_label"
                            );
                            this.handleInput(
                                setting?.card_section_at_label,
                                setting?.language,
                                "card_section_at_label"
                            );
                            this.handleInput(
                                setting?.card_section_seats_left,
                                setting?.language,
                                "card_section_seats_left"
                            );
                            this.handleInput(
                                setting?.card_section_per_seat,
                                setting?.language,
                                "card_section_per_seat"
                            );
                            this.handleInput(
                                setting?.heading_ride_card_section,
                                setting?.language,
                                "heading_ride_card_section"
                            );
                            this.handleInput(
                                setting?.card_section_booked,
                                setting?.language,
                                "card_section_booked"
                            );
                            this.handleInput(
                                setting?.card_section_seats,
                                setting?.language,
                                "card_section_seats"
                            );
                            this.handleInput(
                                setting?.card_section_booking_fee,
                                setting?.language,
                                "card_section_booking_fee"
                            );
                            this.handleInput(
                                setting?.apply_button_label,
                                setting?.language,
                                "apply_button_label"
                            );
                            this.handleInput(
                                setting?.clear_button_label,
                                setting?.language,
                                "clear_button_label"
                            );
                            this.handleInput(
                                setting?.card_section_driver,
                                setting?.language,
                                "card_section_driver"
                            );
                            this.handleInput(
                                setting?.card_section_age,
                                setting?.language,
                                "card_section_age"
                            );
                            this.handleInput(
                                setting?.card_section_driven,
                                setting?.language,
                                "card_section_driven"
                            );
                            this.handleInput(
                                setting?.card_section_passengers,
                                setting?.language,
                                "card_section_passengers"
                            );
                            this.handleInput(
                                setting?.card_section_review,
                                setting?.language,
                                "card_section_review"
                            );
                            this.handleInput(
                                setting?.card_section_completed,
                                setting?.language,
                                "card_section_completed"
                            );
                            this.handleInput(
                                setting?.trips_card_section_seat_booked,
                                setting?.language,
                                "trips_card_section_seat_booked"
                            );
                            this.handleInput(
                                setting?.trips_card_section_seat_available,
                                setting?.language,
                                "trips_card_section_seat_available"
                            );
                            this.handleInput(
                                setting?.trips_card_section_review_driver,
                                setting?.language,
                                "trips_card_section_review_driver"
                            );

                            this.handleInput(
                                setting?.firm_cancellation_tooltip,
                                setting?.language,
                                "firm_cancellation_tooltip"
                            );
                            this.handleInput(
                                setting?.filter_section_heading,
                                setting?.language,
                                "filter_section_heading"
                            );
                            this.handleInput(
                                setting?.filter1_driver_heading,
                                setting?.language,
                                "filter1_driver_heading"
                            );
                            this.handleInput(
                                setting?.driver_age_label,
                                setting?.language,
                                "driver_age_label"
                            );
                            this.handleInput(
                                setting?.driver_age_placeholder,
                                setting?.language,
                                "driver_age_placeholder"
                            );
                            this.handleInput(
                                setting?.driver_rating_label,
                                setting?.language,
                                "driver_rating_label"
                            );
                            this.handleInput(
                                setting?.driver_rating_placeholder,
                                setting?.language,
                                "driver_rating_placeholder"
                            );
                            this.handleInput(
                                setting?.driver_phone_access_label,
                                setting?.language,
                                "driver_phone_access_label"
                            );
                            this.handleInput(
                                setting?.driver_know_label,
                                setting?.language,
                                "driver_know_label"
                            );
                            this.handleInput(
                                setting?.driver_know_placeholder,
                                setting?.language,
                                "driver_know_placeholder"
                            );
                            this.handleInput(
                                setting?.filter2_passengers_heading,
                                setting?.language,
                                "filter2_passengers_heading"
                            );
                            this.handleInput(
                                setting?.passengers_rating_label,
                                setting?.language,
                                "passengers_rating_label"
                            );
                            this.handleInput(
                                setting?.passengers_rating_placeholder,
                                setting?.language,
                                "passengers_rating_placeholder"
                            );
                            this.handleInput(
                                setting?.filter3_payment_methods_heading,
                                setting?.language,
                                "filter3_payment_methods_heading"
                            );
                            this.handleInput(
                                setting?.payment_methods_label,
                                setting?.language,
                                "payment_methods_label"
                            );
                            this.handleInput(
                                setting?.payment_methods_option1,
                                setting?.language,
                                "payment_methods_option1"
                            );
                            this.handleInput(
                                setting?.payment_methods_option2,
                                setting?.language,
                                "payment_methods_option2"
                            );
                            this.handleInput(
                                setting?.payment_methods_option3,
                                setting?.language,
                                "payment_methods_option3"
                            );
                            this.handleInput(
                                setting?.payment_methods_option4,
                                setting?.language,
                                "payment_methods_option4"
                            );
                            this.handleInput(
                                setting?.filter4_vehicle_heading,
                                setting?.language,
                                "filter4_vehicle_heading"
                            );
                            this.handleInput(
                                setting?.vehicle_type_label,
                                setting?.language,
                                "vehicle_type_label"
                            );
                            this.handleInput(
                                setting?.vehicle_type_placeholder,
                                setting?.language,
                                "vehicle_type_placeholder"
                            );
                            this.handleInput(
                                setting?.ride_features_option1,
                                setting?.language,
                                "ride_features_option1"
                            );
                            this.handleInput(
                                setting?.ride_features_option2,
                                setting?.language,
                                "ride_features_option2"
                            );
                            this.handleInput(
                                setting?.ride_features_option3,
                                setting?.language,
                                "ride_features_option3"
                            );
                            this.handleInput(
                                setting?.ride_features_option4,
                                setting?.language,
                                "ride_features_option4"
                            );
                            this.handleInput(
                                setting?.ride_features_option5,
                                setting?.language,
                                "ride_features_option5"
                            );
                            this.handleInput(
                                setting?.ride_features_option6,
                                setting?.language,
                                "ride_features_option6"
                            );
                            this.handleInput(
                                setting?.ride_features_option7,
                                setting?.language,
                                "ride_features_option7"
                            );
                            this.handleInput(
                                setting?.ride_features_option8,
                                setting?.language,
                                "ride_features_option8"
                            );
                            this.handleInput(
                                setting?.ride_features_option9,
                                setting?.language,
                                "ride_features_option9"
                            );
                            this.handleInput(
                                setting?.ride_features_option10,
                                setting?.language,
                                "ride_features_option10"
                            );
                            this.handleInput(
                                setting?.ride_features_option11,
                                setting?.language,
                                "ride_features_option11"
                            );
                            this.handleInput(
                                setting?.ride_features_option12,
                                setting?.language,
                                "ride_features_option12"
                            );
                            this.handleInput(
                                setting?.ride_features_option13,
                                setting?.language,
                                "ride_features_option13"
                            );
                            this.handleInput(
                                setting?.ride_features_option14,
                                setting?.language,
                                "ride_features_option14"
                            );
                            this.handleInput(
                                setting?.ride_features_option15,
                                setting?.language,
                                "ride_features_option15"
                            );
                            this.handleInput(
                                setting?.ride_features_option16,
                                setting?.language,
                                "ride_features_option16"
                            );
                            this.handleInput(
                                setting?.ride_features_option17,
                                setting?.language,
                                "ride_features_option17"
                            );
                            this.handleInput(
                                setting?.luggage_label,
                                setting?.language,
                                "luggage_label"
                            );
                            this.handleInput(
                                setting?.luggage_placeholder,
                                setting?.language,
                                "luggage_placeholder"
                            );
                            this.handleInput(
                                setting?.smoking_label,
                                setting?.language,
                                "smoking_label"
                            );
                            this.handleInput(
                                setting?.smoking_option1,
                                setting?.language,
                                "smoking_option1"
                            );
                            this.handleInput(
                                setting?.smoking_option2,
                                setting?.language,
                                "smoking_option2"
                            );
                            this.handleInput(
                                setting?.pets_allowed_label,
                                setting?.language,
                                "pets_allowed_label"
                            );
                            this.handleInput(
                                setting?.pets_allowed_option1,
                                setting?.language,
                                "pets_allowed_option1"
                            );
                            this.handleInput(
                                setting?.pets_allowed_option2,
                                setting?.language,
                                "pets_allowed_option2"
                            );
                            this.handleInput(
                                setting?.pets_allowed_option3,
                                setting?.language,
                                "pets_allowed_option3"
                            );
                            this.handleInput(
                                setting?.card_section_no_review,
                                setting?.language,
                                "card_section_no_review"
                            );
                            this.handleInput(
                                setting?.card_section_cancelled,
                                setting?.language,
                                "card_section_cancelled"
                            );
                            this.handleInput(setting?.search_result_load_more_btn, setting?.language, "search_result_load_more_btn");
                            this.handleInput(setting?.search_result_no_more_data_message, setting?.language, "search_result_no_more_data_message");
                            this.handleInput(setting?.search_result_no_found_message, setting?.language, "search_result_no_found_message");
                            this.handleInput(setting?.search_result_label, setting?.language, "search_result_label");
                            this.handleInput(setting?.filter_what_label, setting?.language, "filter_what_label");
                            this.handleInput(setting?.search_and_above_label, setting?.language, "search_and_above_label");
                            this.handleInput(setting?.search_filter_all_label, setting?.language, "search_filter_all_label");
                            this.handleInput(setting?.search_filter_select_vehicle_label, setting?.language, "search_filter_select_vehicle_label");
                            this.handleInput(setting?.card_section_not_live, setting?.language, "card_section_not_live");
                            this.handleInput(setting?.card_section_booking_request, setting?.language, "card_section_booking_request");
                            this.handleInput(setting?.trips_card_section_reviewed, setting?.language, "trips_card_section_reviewed");
                            this.handleInput(setting?.filter_search_btn_label, setting?.language, "filter_search_btn_label");
                            this.handleInput(setting?.ride_preferences_label, setting?.language, "ride_preferences_label");
                            this.handleInput(setting?.filter_close_btn_label, setting?.language, "filter_close_btn_label");
                            this.handleInput(setting?.hide_ride_popup_take_me_back_button, setting?.language, "hide_ride_popup_take_me_back_button");
                            this.handleInput(setting?.hide_ride_popup_confirm_button, setting?.language, "hide_ride_popup_confirm_button");
                            this.handleInput(setting?.hide_ride_popup_text, setting?.language, "hide_ride_popup_text");
                            this.handleInput(setting?.hide_ride_popup_heading, setting?.language, "hide_ride_popup_heading");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-find-ride-page-setting`,
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
                    `pink_ride_page_heading.pink_ride_page_heading_${language.id}`
                ) ||
                validationErros.has(
                    `extra_care_ride_page_label.extra_care_ride_page_label_${language.id}`
                ) ||
                validationErros.has(
                    `pink_ride_page_label.pink_ride_page_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_results_pink_ride_label.search_results_pink_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_results_extra_care_ride_label.search_results_extra_care_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `more_rides_pink_ride_label.more_rides_pink_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `to_pink_ride_label.to_pink_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `imp_pink_ride_label.imp_pink_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `imp_extra_care_ride_label.imp_extra_care_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `navbar_icon.navbar_icon_${language.id}`
                ) ||
                validationErros.has(
                    `from_field_icon.from_field_icon_${language.id}`
                ) ||
                validationErros.has(
                    `swap_field_icon.swap_field_icon_${language.id}`
                ) ||
                validationErros.has(
                    `to_field_icon.to_field_icon_${language.id}`
                ) ||
                validationErros.has(
                    `date_field_icon.date_field_icon_${language.id}`
                ) ||
                validationErros.has(
                    `search_field_icon.search_field_icon_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_from_placeholder.search_section_from_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_to_placeholder.search_section_to_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_date_placeholder.search_section_date_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_required_error.search_section_required_error_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_keyword_label.search_section_keyword_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_keyword_placeholder.search_section_keyword_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_button_label.search_section_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_recent_searches.search_section_recent_searches_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_from_label.card_section_from_label_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_to_label.card_section_to_label_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_at_label.card_section_at_label_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_seats_left.card_section_seats_left_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_per_seat.card_section_per_seat_${language.id}`
                ) ||
                validationErros.has(
                    `heading_ride_card_section.heading_ride_card_section_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_driver.card_section_driver_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_age.card_section_age_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_driven.card_section_driven_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_passengers.card_section_passengers_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_review.card_section_review_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_completed.card_section_completed_${language.id}`
                ) ||
                validationErros.has(
                    `trips_card_section_seat_booked.trips_card_section_seat_booked_${language.id}`
                ) ||
                validationErros.has(
                    `trips_card_section_seat_available.trips_card_section_seat_available_${language.id}`
                ) ||
                validationErros.has(
                    `trips_card_section_review_driver.trips_card_section_review_driver_${language.id}`
                ) ||
                validationErros.has(
                    `firm_cancellation_tooltip.firm_cancellation_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `filter_section_heading.filter_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `filter1_driver_heading.filter1_driver_heading_${language.id}`
                ) ||
                validationErros.has(
                    `driver_age_label.driver_age_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_age_placeholder.driver_age_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `driver_rating_label.driver_rating_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_rating_placeholder.driver_rating_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `driver_phone_access_label.driver_phone_access_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_know_label.driver_know_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_know_placeholder.driver_know_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `filter2_passengers_heading.filter2_passengers_heading_${language.id}`
                ) ||
                validationErros.has(
                    `passengers_rating_label.passengers_rating_label_${language.id}`
                ) ||
                validationErros.has(
                    `passengers_rating_placeholder.passengers_rating_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `filter3_payment_methods_heading.filter3_payment_methods_heading_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_label.payment_methods_label_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option1.payment_methods_option1_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option2.payment_methods_option2_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option3.payment_methods_option3_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option4.payment_methods_option4_${language.id}`
                ) ||
                validationErros.has(
                    `filter4_vehicle_heading.filter4_vehicle_heading_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_type_label.vehicle_type_label_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_type_placeholder.vehicle_type_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option1.ride_features_option1_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option2.ride_features_option2_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option3.ride_features_option3_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option4.ride_features_option4_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option5.ride_features_option5_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option6.ride_features_option6_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option7.ride_features_option7_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option8.ride_features_option8_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option9.ride_features_option9_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option10.ride_features_option10_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option11.ride_features_option11_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option12.ride_features_option12_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option13.ride_features_option13_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option14.ride_features_option14_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option15.ride_features_option15_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option16.ride_features_option16_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_option17.ride_features_option17_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_label.luggage_label_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_placeholder.luggage_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_label.smoking_label_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option1.smoking_option1_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option2.smoking_option2_${language.id}`
                ) ||
                validationErros.has(
                    `pets_allowed_label.pets_allowed_label_${language.id}`
                ) ||
                validationErros.has(
                    `pets_allowed_option1.pets_allowed_option1_${language.id}`
                ) ||
                validationErros.has(
                    `pets_allowed_option2.pets_allowed_option2_${language.id}`
                ) ||
                validationErros.has(
                    `pets_allowed_option3.pets_allowed_option3_${language.id}`
                )
            );
        },
    },
};
</script>
