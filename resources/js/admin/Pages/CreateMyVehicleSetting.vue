<template>
    <AppLayout>
        <section class="vehicle-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    My Vehicle settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all My Vehicle page setting translations for a specific language.</p>

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
                                    <a :href="`${mixAdminApiUrl}download-my-vehicle-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                                        :for="`edit_vehicle_button_text_${activeLanguageId}`"
                                                        >Edit Vehicle Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_vehicle_button_text_${activeLanguageId}`"
                                                    :id="`edit_vehicle_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('edit_vehicle_button_text')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_vehicle_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_vehicle_button_text.edit_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_vehicle_button_text.edit_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`edit_vehicle_button_text_${activeLanguageId}`"
                                                        >Remove Vehicle Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`remove_vehicle_button_text_${activeLanguageId}`"
                                                    :id="`remove_vehicle_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('remove_vehicle_button_text')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'remove_vehicle_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `remove_vehicle_button_text.remove_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `remove_vehicle_button_text.remove_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`add_vehicle_button_text_${activeLanguageId}`"
                                                        >Add Vehicle Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`add_vehicle_button_text_${activeLanguageId}`"
                                                    :id="`add_vehicle_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('add_vehicle_button_text')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_vehicle_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `add_vehicle_button_text.add_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `add_vehicle_button_text.add_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`add_main_heading_${activeLanguageId}`"
                                                        >Add Vehicle</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`add_main_heading_${activeLanguageId}`"
                                                    :id="`add_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'add_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `add_main_heading.add_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `add_main_heading.add_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`edit_main_heading_${activeLanguageId}`"
                                                        >Edit Vehicle</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_main_heading_${activeLanguageId}`"
                                                    :id="`edit_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'edit_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_main_heading.edit_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_main_heading.edit_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`mobile_indicate_field_label_${activeLanguageId}`"
                                                        >Indicate Required (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_indicate_field_label_${activeLanguageId}`"
                                                    :id="`mobile_indicate_field_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_indicate_field_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_indicate_field_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_indicate_field_label.mobile_indicate_field_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_indicate_field_label.mobile_indicate_field_label_${activeLanguageId}`
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
                                                        >Make Label</label
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
                                                        >Make Placeholder</label
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
                                                        >Model Label</label
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
                                                        >Model Placeholder</label
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
                                                        :for="`license_plate_number_label_${activeLanguageId}`"
                                                        >License Plate label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`license_plate_number_label_${activeLanguageId}`"
                                                    :id="`license_plate_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'license_plate_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'license_plate_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `license_plate_number_label.license_plate_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `license_plate_number_label.license_plate_number_label_${activeLanguageId}`
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
                                                        >License Plate error</label
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
                                                        :for="`license_plate_number_placeholder_${activeLanguageId}`"
                                                        >License Plate Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`license_plate_number_placeholder_${activeLanguageId}`"
                                                    :id="`license_plate_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'license_plate_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'license_plate_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `license_plate_number_placeholder.license_plate_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `license_plate_number_placeholder.license_plate_number_placeholder_${activeLanguageId}`
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
                                                        >Color Label</label
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
                                                        :for="`color_placeholder_${activeLanguageId}`"
                                                        >Color Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`color_placeholder_${activeLanguageId}`"
                                                    :id="`color_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'color_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'color_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `color_placeholder.color_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `color_placeholder.color_placeholder_${activeLanguageId}`
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
                                                        :for="`year_placeholder_${activeLanguageId}`"
                                                        >Year Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`year_placeholder_${activeLanguageId}`"
                                                    :id="`year_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'year_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'year_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `year_placeholder.year_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `year_placeholder.year_placeholder_${activeLanguageId}`
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
                                                        >Vehicle Type Label</label
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
                                                        >Vehicle Type error</label
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
                                                        :for="`vehicle_type_placeholder_${activeLanguageId}`"
                                                        >Vehicle Type Placeholder</label
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
                                                        :for="`fuel_label_${activeLanguageId}`"
                                                        >Fuel Label</label
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
                                                        :for="`electric_checkbox_label_${activeLanguageId}`"
                                                        >Electric Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`electric_checkbox_label_${activeLanguageId}`"
                                                    :id="`electric_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'electric_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'electric_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `electric_checkbox_label.electric_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `electric_checkbox_label.electric_checkbox_label_${activeLanguageId}`
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
                                                        :for="`hybrid_checkbox_label_${activeLanguageId}`"
                                                        >Hybrid Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`hybrid_checkbox_label_${activeLanguageId}`"
                                                    :id="`hybrid_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'hybrid_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hybrid_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `hybrid_checkbox_label.hybrid_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `hybrid_checkbox_label.hybrid_checkbox_label_${activeLanguageId}`
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
                                                        :for="`gas_checkbox_label_${activeLanguageId}`"
                                                        >Gas Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`gas_checkbox_label_${activeLanguageId}`"
                                                    :id="`gas_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'gas_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'gas_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `gas_checkbox_label.gas_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `gas_checkbox_label.gas_checkbox_label_${activeLanguageId}`
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
                                                        :for="`set_primary_vehicle_label_${activeLanguageId}`"
                                                        >Primary Vehicle label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`set_primary_vehicle_label_${activeLanguageId}`"
                                                    :id="`set_primary_vehicle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'set_primary_vehicle_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'set_primary_vehicle_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `set_primary_vehicle_label.set_primary_vehicle_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `set_primary_vehicle_label.set_primary_vehicle_label_${activeLanguageId}`
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
                                                        :for="`set_primary_error_${activeLanguageId}`"
                                                        >Primary Vehicle error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`set_primary_error_${activeLanguageId}`"
                                                    :id="`set_primary_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'set_primary_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'set_primary_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `set_primary_error.set_primary_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `set_primary_error.set_primary_error_${activeLanguageId}`
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
                                                        :for="`yes_checkbox_label_${activeLanguageId}`"
                                                        >Yes label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`yes_checkbox_label_${activeLanguageId}`"
                                                    :id="`yes_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'yes_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'yes_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `yes_checkbox_label.yes_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `yes_checkbox_label.yes_checkbox_label_${activeLanguageId}`
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
                                                        :for="`no_checkbox_label_${activeLanguageId}`"
                                                        >No Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_checkbox_label_${activeLanguageId}`"
                                                    :id="`no_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_checkbox_label.no_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_checkbox_label.no_checkbox_label_${activeLanguageId}`
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
                                                        :for="`image_description_label_${activeLanguageId}`"
                                                        >Image Description Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`image_description_label_${activeLanguageId}`"
                                                    :id="`image_description_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'image_description_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'image_description_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `image_description_label.image_description_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `image_description_label.image_description_label_${activeLanguageId}`
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
                                                        :for="`upload_profile_photo_image_placeholder_${activeLanguageId}`"
                                                        >Upload Profile Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`upload_profile_photo_image_placeholder_${activeLanguageId}`"
                                                    :id="`upload_profile_photo_image_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'upload_profile_photo_image_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'upload_profile_photo_image_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `upload_profile_photo_image_placeholder.upload_profile_photo_image_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `upload_profile_photo_image_placeholder.upload_profile_photo_image_placeholder_${activeLanguageId}`
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
                                                        :for="`choose_file_image_placeholder_${activeLanguageId}`"
                                                        >Choose File Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`choose_file_image_placeholder_${activeLanguageId}`"
                                                    :id="`choose_file_image_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'choose_file_image_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'choose_file_image_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `choose_file_image_placeholder.choose_file_image_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `choose_file_image_placeholder.choose_file_image_placeholder_${activeLanguageId}`
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
                                                        :for="`images_option_placeholder_${activeLanguageId}`"
                                                        >Image Option Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`images_option_placeholder_${activeLanguageId}`"
                                                    :id="`images_option_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'images_option_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'images_option_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `images_option_placeholder.images_option_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `images_option_placeholder.images_option_placeholder_${activeLanguageId}`
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
                                                        :for="`car_photo_label_${activeLanguageId}`"
                                                        >Image label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`car_photo_label_${activeLanguageId}`"
                                                    :id="`car_photo_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'car_photo_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'car_photo_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `car_photo_label.car_photo_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `car_photo_label.car_photo_label_${activeLanguageId}`
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
                                                        >Image error</label
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
                                                        :for="`no_vehicle_message_${activeLanguageId}`"
                                                        >No Vehicle Message </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_vehicle_message_${activeLanguageId}`"
                                                    :id="`no_vehicle_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_vehicle_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_vehicle_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_vehicle_message.no_vehicle_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_vehicle_message.no_vehicle_message_${activeLanguageId}`
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
                                                        :for="`update_vehicle_button_text_${activeLanguageId}`"
                                                        >Update Vehicle Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`update_vehicle_button_text_${activeLanguageId}`"
                                                    :id="`update_vehicle_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'update_vehicle_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'update_vehicle_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `update_vehicle_button_text.update_vehicle_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `update_vehicle_button_text.update_vehicle_button_text_${activeLanguageId}`
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
                                                        :for="`remove_car_photo_label_${activeLanguageId}`"
                                                        >Remove Car Photo label (edit vehicle) </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`remove_car_photo_label_${activeLanguageId}`"
                                                    :id="`remove_car_photo_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'remove_car_photo_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'remove_car_photo_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `remove_car_photo_label.remove_car_photo_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `remove_car_photo_label.remove_car_photo_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`delete_photo_message_${activeLanguageId}`"
                                                        >Delete photo message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`delete_photo_message_${activeLanguageId}`"
                                                    :id="`delete_photo_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('delete_photo_message')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'delete_photo_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `delete_photo_message.delete_photo_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `delete_photo_message.delete_photo_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`edit_photo_label_${activeLanguageId}`"
                                                        >Edit photo message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_photo_label_${activeLanguageId}`"
                                                    :id="`edit_photo_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('edit_photo_label')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_photo_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_photo_label.edit_photo_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_photo_label.edit_photo_label_${activeLanguageId}`
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-my-vehicle-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchMyVehicleSetting && this.fetchMyVehicleSetting(); }, 1000);
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
                            this.handleInput("", language, "edit_vehicle_button_text");
                            this.handleInput("", language, "add_main_heading");
                            this.handleInput("", language, "remove_vehicle_button_text");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "edit_main_heading");
                            this.handleInput("", language, "mobile_indicate_field_label");
                            this.handleInput("", language, "make_label");
                            this.handleInput("", language, "make_error");
                            this.handleInput("", language, "make_placeholder");
                            this.handleInput("", language, "model_label");
                            this.handleInput("", language, "model_error");
                            this.handleInput("", language, "model_placeholder");
                            this.handleInput("", language, "license_plate_number_label");
                            this.handleInput("", language, "license_error");
                            this.handleInput("", language, "license_plate_number_placeholder");
                            this.handleInput("", language, "color_label");
                            this.handleInput("", language, "color_error");
                            this.handleInput("", language, "color_placeholder");
                            this.handleInput("", language, "year_label");
                            this.handleInput("", language, "year_error");
                            this.handleInput("", language, "year_placeholder");
                            this.handleInput("", language, "vehicle_type_label");
                            this.handleInput("", language, "vehicle_type_error");
                            this.handleInput("", language, "vehicle_type_placeholder");
                            this.handleInput("", language, "fuel_label");
                            this.handleInput("", language, "fuel_error");
                            this.handleInput("", language, "electric_checkbox_label");
                            this.handleInput("", language, "hybrid_checkbox_label");
                            this.handleInput("", language, "gas_checkbox_label");
                            this.handleInput("", language, "set_primary_vehicle_label");
                            this.handleInput("", language, "set_primary_error");
                            this.handleInput("", language, "yes_checkbox_label");
                            this.handleInput("", language, "no_checkbox_label");
                            this.handleInput("", language, "image_description_label");
                            this.handleInput("", language, "upload_profile_photo_image_placeholder");
                            this.handleInput("", language, "choose_file_image_placeholder");
                            this.handleInput("", language, "images_option_placeholder");
                            this.handleInput("", language, "add_vehicle_button_text");
                            this.handleInput("", language, "car_photo_label");
                            this.handleInput("", language, "photo_error");
                            this.handleInput("", language, "no_vehicle_message");
                            this.handleInput("", language, "update_vehicle_button_text");
                            this.handleInput("", language, "remove_car_photo_label");
                        });
                        this.fetchMyVehicleSetting();
                    }
                });
        },
        fetchMyVehicleSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-my-vehicle-setting`)
                .then((res) => {
                    console.log(res);
                    if (res?.data?.status == "Success") {
                        let my_vehicle_setting_detail =
                            res?.data?.data?.my_vehicle_setting_detail || [];
                        my_vehicle_setting_detail.map((setting) => {

                            this.handleInput(
                                setting?.edit_vehicle_button_text,
                                setting?.language,
                                "edit_vehicle_button_text"
                            );
                            this.handleInput(
                                setting?.remove_vehicle_button_text,
                                setting?.language,
                                "remove_vehicle_button_text"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.add_main_heading,
                                setting?.language,
                                "add_main_heading"
                            );
                            this.handleInput(
                                setting?.edit_main_heading,
                                setting?.language,
                                "edit_main_heading"
                            );
                             this.handleInput(
                                setting?.mobile_indicate_field_label,
                                setting?.language,
                                "mobile_indicate_field_label"
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
                                setting?.license_plate_number_label,
                                setting?.language,
                                "license_plate_number_label"
                            );
                            this.handleInput(
                                setting?.license_error,
                                setting?.language,
                                "license_error"
                            );
                            this.handleInput(
                                setting?.license_plate_number_placeholder,
                                setting?.language,
                                "license_plate_number_placeholder"
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
                                setting?.color_placeholder,
                                setting?.language,
                                "color_placeholder"
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
                                setting?.year_placeholder,
                                setting?.language,
                                "year_placeholder"
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
                                setting?.vehicle_type_placeholder,
                                setting?.language,
                                "vehicle_type_placeholder"
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
                                setting?.electric_checkbox_label,
                                setting?.language,
                                "electric_checkbox_label"
                            );
                            this.handleInput(
                                setting?.hybrid_checkbox_label,
                                setting?.language,
                                "hybrid_checkbox_label"
                            );
                            this.handleInput(
                                setting?.gas_checkbox_label,
                                setting?.language,
                                "gas_checkbox_label"
                            );
                            this.handleInput(
                                setting?.set_primary_vehicle_label,
                                setting?.language,
                                "set_primary_vehicle_label"
                            );
                            this.handleInput(
                                setting?.set_primary_error,
                                setting?.language,
                                "set_primary_error"
                            );
                            this.handleInput(
                                setting?.yes_checkbox_label,
                                setting?.language,
                                "yes_checkbox_label"
                            );
                            this.handleInput(
                                setting?.no_checkbox_label,
                                setting?.language,
                                "no_checkbox_label"
                            );
                             this.handleInput(
                                setting?.image_description_label,
                                setting?.language,
                                "image_description_label"
                            );
                            this.handleInput(
                                setting?.upload_profile_photo_image_placeholder,
                                setting?.language,
                                "upload_profile_photo_image_placeholder"
                            );
                            this.handleInput(
                                setting?.choose_file_image_placeholder,
                                setting?.language,
                                "choose_file_image_placeholder"
                            );
                            this.handleInput(
                                setting?.images_option_placeholder,
                                setting?.language,
                                "images_option_placeholder"
                            );
                            this.handleInput(
                                setting?.add_vehicle_button_text,
                                setting?.language,
                                "add_vehicle_button_text"
                            );
                            this.handleInput(
                                setting?.car_photo_label,
                                setting?.language,
                                "car_photo_label"
                            );
                            this.handleInput(
                                setting?.photo_error,
                                setting?.language,
                                "photo_error"
                            );
                            this.handleInput(
                                setting?.no_vehicle_message,
                                setting?.language,
                                "no_vehicle_message"
                            );
                            this.handleInput(
                                setting?.update_vehicle_button_text,
                                setting?.language,
                                "update_vehicle_button_text"
                            );
                            this.handleInput(
                                setting?.remove_car_photo_label,
                                setting?.language,
                                "remove_car_photo_label"
                            );
                            this.handleInput(
                                setting?.delete_photo_message,
                                setting?.language,
                                "delete_photo_message"
                            );
                            this.handleInput(
                                setting?.edit_photo_label,
                                setting?.language,
                                "edit_photo_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-my-vehicle-setting`,
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
                    `edit_vehicle_button_text.edit_vehicle_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `remove_vehicle_button_text.remove_vehicle_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `add_main_heading.add_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `edit_main_heading.edit_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `make_label.make_label_${language.id}`
                ) ||
                validationErros.has(`edit_photo_label.edit_photo_label_${language.id}`) ||
                validationErros.has(`delete_photo_message.delete_photo_message_${language.id}`) ||
                validationErros.has(
                    `make_error.make_error_${language.id}`
                ) ||
                validationErros.has(
                    `make_placeholder.make_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `model_label.model_label_${language.id}`
                )||
                validationErros.has(
                    `model_error.model_error_${language.id}`
                )||
                validationErros.has(
                    `model_placeholder.model_placeholder_${language.id}`
                )||
                validationErros.has(
                    `vehicle_type_label.vehicle_type_label_${language.id}`
                )||
                validationErros.has(
                    `vehicle_type_error.vehicle_type_error_${language.id}`
                )||
                validationErros.has(
                    `color_error.color_error_${language.id}`
                )||
                validationErros.has(
                    `license_error.license_error_${language.id}`
                )||
                validationErros.has(
                    `year_error.year_error_${language.id}`
                )||
                validationErros.has(
                    `vehicle_type_placeholder.vehicle_type_placeholder_${language.id}`
                )||
                validationErros.has(
                    `fuel_label.fuel_label_${language.id}`
                )||
                validationErros.has(
                    `fuel_error.fuel_error_${language.id}`
                )||
                validationErros.has(
                    `electric_checkbox_label.electric_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `hybrid_checkbox_label.hybrid_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `gas_checkbox_label.gas_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `set_primary_vehicle_label.set_primary_vehicle_label_${language.id}`
                )||
                validationErros.has(
                    `set_primary_error.set_primary_error_${language.id}`
                )||
                validationErros.has(
                    `yes_checkbox_label.yes_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `no_checkbox_label.no_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `image_description_label.image_description_label_${language.id}`
                )||
                validationErros.has(
                    `upload_profile_photo_image_placeholder.upload_profile_photo_image_placeholder_${language.id}`
                )||
                validationErros.has(
                    `choose_file_image_placeholder.choose_file_image_placeholder_${language.id}`
                )||
                validationErros.has(
                    `images_option_placeholder.images_option_placeholder_${language.id}`
                )||
                validationErros.has(
                    `add_vehicle_button_text.add_vehicle_button_text_${language.id}`
                )||
                validationErros.has(
                    `car_photo_label.car_photo_label_${language.id}`
                )||
                validationErros.has(
                    `photo_error.photo_error_${language.id}`
                )||
                validationErros.has(
                    `no_vehicle_message.no_vehicle_message_${language.id}`
                )||
                validationErros.has(
                    `update_vehicle_button_text.update_vehicle_button_text_${language.id}`
                )||
                validationErros.has(
                    `remove_car_photo_label.remove_car_photo_label_${language.id}`
                )||
                validationErros.has(
                    `mobile_indicate_field_label.mobile_indicate_field_label_${language.id}`
                )
            );
        },
    },
};
</script>
