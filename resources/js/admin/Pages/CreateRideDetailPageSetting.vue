<template>
    <AppLayout>
        <section class="ride-detail-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Trip details page settings
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
                                <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Ride Detail page settings for a specific language. This will save or update all fields at once.</p>

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
                                        <div class="flex items-center text-sm text-gray-600"><svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><span class="font-medium">Need help formatting your Excel file?</span></div>
                                        <a :href="`${mixAdminApiUrl}download-ride-detail-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                            collapseStates[0] =
                                                !collapseStates[0]
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
                                        v-if="collapseStates[0]"
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
                                                        :for="`from_label_${activeLanguageId}`"
                                                        >From label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`from_label_${activeLanguageId}`"
                                                    :id="`from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'from_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'from_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `from_label.from_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `from_label.from_label_${activeLanguageId}`
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
                                                        :for="`to_label_${activeLanguageId}`"
                                                        >To label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`to_label_${activeLanguageId}`"
                                                    :id="`to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `to_label.to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `to_label.to_label_${activeLanguageId}`
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
                                                        :for="`at_label_${activeLanguageId}`"
                                                        >At label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`at_label_${activeLanguageId}`"
                                                    :id="`at_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'at_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'at_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `at_label.at_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `at_label.at_label_${activeLanguageId}`
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
                                                        :for="`seats_left_label_${activeLanguageId}`"
                                                        >Seats left label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_left_label_${activeLanguageId}`"
                                                    :id="`seats_left_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_left_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_left_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_left_label.seats_left_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_left_label.seats_left_label_${activeLanguageId}`
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
                                                        :for="`per_seat_label_${activeLanguageId}`"
                                                        >Per seat label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`per_seat_label_${activeLanguageId}`"
                                                    :id="`per_seat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'per_seat_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'per_seat_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `per_seat_label.per_seat_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `per_seat_label.per_seat_label_${activeLanguageId}`
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
                                                        :for="`pickup_dropoff_info_heading_${activeLanguageId}`"
                                                        >Pick up & drop off info heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pickup_dropoff_info_heading_${activeLanguageId}`"
                                                    :id="`pickup_dropoff_info_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pickup_dropoff_info_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pickup_dropoff_info_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pickup_dropoff_info_heading.pickup_dropoff_info_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pickup_dropoff_info_heading.pickup_dropoff_info_heading_${activeLanguageId}`
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
                                                        :for="`pickup_label_${activeLanguageId}`"
                                                        >Pick up label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pickup_label_${activeLanguageId}`"
                                                    :id="`pickup_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pickup_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pickup_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pickup_label.pickup_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pickup_label.pickup_label_${activeLanguageId}`
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
                                                        :for="`dropoff_label_${activeLanguageId}`"
                                                        >Drop off label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`dropoff_label_${activeLanguageId}`"
                                                    :id="`dropoff_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'dropoff_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'dropoff_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `dropoff_label.dropoff_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `dropoff_label.dropoff_label_${activeLanguageId}`
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
                                                        :for="`description_label_${activeLanguageId}`"
                                                        >Description label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`description_label_${activeLanguageId}`"
                                                    :id="`description_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'description_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'description_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `description_label.description_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `description_label.description_label_${activeLanguageId}`
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
                                                        :for="`co_passenger_label_${activeLanguageId}`"
                                                        >My co-passengers label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`co_passenger_label_${activeLanguageId}`"
                                                    :id="`co_passenger_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'co_passenger_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'co_passenger_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `co_passenger_label.co_passenger_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `co_passenger_label.co_passenger_label_${activeLanguageId}`
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
                                                        :for="`ride_co_passenger_heading_${activeLanguageId}`"
                                                        >My co-passengers label ride (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_co_passenger_heading_${activeLanguageId}`"
                                                    :id="`ride_co_passenger_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_co_passenger_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_co_passenger_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_co_passenger_heading.ride_co_passenger_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_co_passenger_heading.ride_co_passenger_heading_${activeLanguageId}`
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
                                                        :for="`trip_co_passenger_heading_${activeLanguageId}`"
                                                        >My co-passengers label trip (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`trip_co_passenger_heading_${activeLanguageId}`"
                                                    :id="`trip_co_passenger_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'trip_co_passenger_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'trip_co_passenger_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `trip_co_passenger_heading.trip_co_passenger_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `trip_co_passenger_heading.trip_co_passenger_heading_${activeLanguageId}`
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
                                                        :for="`driver_info_label_${activeLanguageId}`"
                                                        >Driver info label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_info_label_${activeLanguageId}`"
                                                    :id="`driver_info_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_info_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_info_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_info_label.driver_info_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_info_label.driver_info_label_${activeLanguageId}`
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
                                                        :for="`review_driver_info_label_${activeLanguageId}`"
                                                        >Review driver info label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`review_driver_info_label_${activeLanguageId}`"
                                                    :id="`review_driver_info_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_driver_info_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_driver_info_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `review_driver_info_label.review_driver_info_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `review_driver_info_label.review_driver_info_label_${activeLanguageId}`
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
                                                        :for="`review_passanger_label_${activeLanguageId}`"
                                                        >Review passanger</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`review_passanger_label_${activeLanguageId}`"
                                                    :id="`review_passanger_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_passanger_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_passanger_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `review_passanger_label.review_passanger_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `review_passanger_label.review_passanger_label_${activeLanguageId}`
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
                                                        :for="`driver_label_${activeLanguageId}`"
                                                        >Driver label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_label_${activeLanguageId}`"
                                                    :id="`driver_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_label.driver_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_label.driver_label_${activeLanguageId}`
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
                                                        :for="`cancellation_policy_${activeLanguageId}`"
                                                        >Cancellation Policy label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancellation_policy_${activeLanguageId}`"
                                                    :id="`cancellation_policy_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancellation_policy'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancellation_policy.cancellation_policy_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancellation_policy.cancellation_policy_${activeLanguageId}`
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
                                                        :for="`passengers_driven_label_${activeLanguageId}`"
                                                        >Passengers label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passengers_driven_label_${activeLanguageId}`"
                                                    :id="`passengers_driven_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passengers_driven_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passengers_driven_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passengers_driven_label.passengers_driven_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passengers_driven_label.passengers_driven_label_${activeLanguageId}`
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
                                                        :for="`driver_age_label_${activeLanguageId}`"
                                                        >Driver age label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_age_label_${activeLanguageId}`"
                                                    :id="`driver_age_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_age_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_age_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_age_label.driver_age_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_age_label.driver_age_label_${activeLanguageId}`
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
                                                        :for="`driver_chat_heading_${activeLanguageId}`"
                                                        >Driver chat heading (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_chat_heading_${activeLanguageId}`"
                                                    :id="`driver_chat_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_chat_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_chat_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_chat_heading.driver_chat_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_chat_heading.driver_chat_heading_${activeLanguageId}`
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
                                                        :for="`driver_chat_with_${activeLanguageId}`"
                                                        >chat with text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_chat_with_${activeLanguageId}`"
                                                    :id="`driver_chat_with_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_chat_with'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_chat_with'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_chat_with.driver_chat_with_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_chat_with.driver_chat_with_${activeLanguageId}`
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
                                                        :for="`driver_chat_label_${activeLanguageId}`"
                                                        >Driver chat label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_chat_label_${activeLanguageId}`"
                                                    :id="`driver_chat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_chat_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_chat_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_chat_label.driver_chat_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_chat_label.driver_chat_label_${activeLanguageId}`
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
                                                        :for="`driver_chat_button_label_${activeLanguageId}`"
                                                        >Driver chat button label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_chat_button_label_${activeLanguageId}`"
                                                    :id="`driver_chat_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_chat_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_chat_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_chat_button_label.driver_chat_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_chat_button_label.driver_chat_button_label_${activeLanguageId}`
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
                                                        :for="`vehicle_info_label_${activeLanguageId}`"
                                                        >Vehicle info label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_info_label_${activeLanguageId}`"
                                                    :id="`vehicle_info_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_info_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_info_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_info_label.vehicle_info_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_info_label.vehicle_info_label_${activeLanguageId}`
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
                                                        :for="`payment_method_label_${activeLanguageId}`"
                                                        >Payment method label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payment_method_label_${activeLanguageId}`"
                                                    :id="`payment_method_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payment_method_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_method_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payment_method_label.payment_method_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payment_method_label.payment_method_label_${activeLanguageId}`
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
                                                        :for="`booking_type_label_${activeLanguageId}`"
                                                        >Booking type label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_type_label_${activeLanguageId}`"
                                                    :id="`booking_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_type_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_type_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_type_label.booking_type_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_type_label.booking_type_label_${activeLanguageId}`
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
                                                        :for="`cancellation_policy_label_${activeLanguageId}`"
                                                        >Cancellation policy label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancellation_policy_label_${activeLanguageId}`"
                                                    :id="`cancellation_policy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancellation_policy_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancellation_policy_label.cancellation_policy_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancellation_policy_label.cancellation_policy_label_${activeLanguageId}`
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
                                                        :for="`luggage_label_${activeLanguageId}`"
                                                        >Luggage label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`luggage_label_${activeLanguageId}`"
                                                    :id="`luggage_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'luggage_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'luggage_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `luggage_label.luggage_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `luggage_label.luggage_label_${activeLanguageId}`
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
                                                        :for="`smoking_label_${activeLanguageId}`"
                                                        >Smoking label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`smoking_label_${activeLanguageId}`"
                                                    :id="`smoking_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'smoking_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `smoking_label.smoking_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `smoking_label.smoking_label_${activeLanguageId}`
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
                                                        :for="`pets_label_${activeLanguageId}`"
                                                        >Pets label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pets_label_${activeLanguageId}`"
                                                    :id="`pets_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pets_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pets_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pets_label.pets_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pets_label.pets_label_${activeLanguageId}`
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
                                                        :for="`ride_features_label_${activeLanguageId}`"
                                                        >Ride features label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_features_label_${activeLanguageId}`"
                                                    :id="`ride_features_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_features_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_features_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_features_label.ride_features_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_features_label.ride_features_label_${activeLanguageId}`
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
                                                        :for="`ride_seat_label_${activeLanguageId}`"
                                                        >Ride seat label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_seat_label_${activeLanguageId}`"
                                                    :id="`ride_seat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_seat_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_seat_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_seat_label.ride_seat_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_seat_label.ride_seat_label_${activeLanguageId}`
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
                                                        :for="`all_seats_booked_label_${activeLanguageId}`"
                                                        >All seats are booked label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`all_seats_booked_label_${activeLanguageId}`"
                                                    :id="`all_seats_booked_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'all_seats_booked_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'all_seats_booked_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `all_seats_booked_label.all_seats_booked_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `all_seats_booked_label.all_seats_booked_label_${activeLanguageId}`
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
                                                        :for="`ride_canceller_by_driver_${activeLanguageId}`"
                                                        >Ride cancelled by driver</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_canceller_by_driver_${activeLanguageId}`"
                                                    :id="`ride_canceller_by_driver_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_canceller_by_driver'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_canceller_by_driver'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_canceller_by_driver.ride_canceller_by_driver_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_canceller_by_driver.ride_canceller_by_driver_${activeLanguageId}`
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
                                                        :for="`ride_completed_text_${activeLanguageId}`"
                                                        >Ride completed text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_completed_text_${activeLanguageId}`"
                                                    :id="`ride_completed_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_completed_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_completed_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_completed_text.ride_completed_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_completed_text.ride_completed_text_${activeLanguageId}`
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
                                                        :for="`book_seat_btn_label_${activeLanguageId}`"
                                                        >Book your seats label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`book_seat_btn_label_${activeLanguageId}`"
                                                    :id="`book_seat_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'book_seat_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'book_seat_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `book_seat_btn_label.book_seat_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `book_seat_btn_label.book_seat_btn_label_${activeLanguageId}`
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
                                                        :for="`instant_btn_label_${activeLanguageId}`"
                                                        >Instant button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`instant_btn_label_${activeLanguageId}`"
                                                    :id="`instant_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'instant_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'instant_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `instant_btn_label.instant_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `instant_btn_label.instant_btn_label_${activeLanguageId}`
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
                                                        :for="`no_seat_available_label_${activeLanguageId}`"
                                                        >No seat available (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_seat_available_label_${activeLanguageId}`"
                                                    :id="`no_seat_available_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_seat_available_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_seat_available_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_seat_available_label.no_seat_available_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_seat_available_label.no_seat_available_label_${activeLanguageId}`
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
                                                        :for="`no_ride_found_message_${activeLanguageId}`"
                                                        >No ride found message (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_ride_found_message_${activeLanguageId}`"
                                                    :id="`no_ride_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_ride_found_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_ride_found_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_ride_found_message.no_ride_found_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_ride_found_message.no_ride_found_message_${activeLanguageId}`
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
                                                        :for="`cancel_booking_btn_label_${activeLanguageId}`"
                                                        >Cancel booking button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancel_booking_btn_label_${activeLanguageId}`"
                                                    :id="`cancel_booking_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancel_booking_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancel_booking_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancel_booking_btn_label.cancel_booking_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancel_booking_btn_label.cancel_booking_btn_label_${activeLanguageId}`
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
                                                        :for="`cancel_ride_btn_label_${activeLanguageId}`"
                                                        >Cancel ride button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancel_ride_btn_label_${activeLanguageId}`"
                                                    :id="`cancel_ride_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancel_ride_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancel_ride_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancel_ride_btn_label.cancel_ride_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancel_ride_btn_label.cancel_ride_btn_label_${activeLanguageId}`
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
                                                        :for="`cancel_ride_confirmation_${activeLanguageId}`"
                                                        >Cancel ride confirmation text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancel_ride_confirmation_${activeLanguageId}`"
                                                    :id="`cancel_ride_confirmation_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancel_ride_confirmation'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancel_ride_confirmation'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancel_ride_confirmation.cancel_ride_confirmation_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancel_ride_confirmation.cancel_ride_confirmation_${activeLanguageId}`
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
                                                        :for="`cancel_ride_yes_btn_${activeLanguageId}`"
                                                        >Yes cancel it button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancel_ride_yes_btn_${activeLanguageId}`"
                                                    :id="`cancel_ride_yes_btn_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancel_ride_yes_btn'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancel_ride_yes_btn'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancel_ride_yes_btn.cancel_ride_yes_btn_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancel_ride_yes_btn.cancel_ride_yes_btn_${activeLanguageId}`
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
                                                        :for="`cancel_ride_no_btn_${activeLanguageId}`"
                                                        >No take me back button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancel_ride_no_btn_${activeLanguageId}`"
                                                    :id="`cancel_ride_no_btn_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancel_ride_no_btn'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancel_ride_no_btn'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancel_ride_no_btn.cancel_ride_no_btn_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancel_ride_no_btn.cancel_ride_no_btn_${activeLanguageId}`
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
                                                        :for="`edit_ride_btn_label_${activeLanguageId}`"
                                                        >Edit ride button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_ride_btn_label_${activeLanguageId}`"
                                                    :id="`edit_ride_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'edit_ride_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_ride_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_ride_btn_label.edit_ride_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_ride_btn_label.edit_ride_btn_label_${activeLanguageId}`
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
                                                        :for="`review_label_${activeLanguageId}`"
                                                        >Review label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`review_label_${activeLanguageId}`"
                                                    :id="`review_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `review_label.review_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `review_label.review_label_${activeLanguageId}`
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
                                                        :for="`booking_request_heading_${activeLanguageId}`"
                                                        >Booking request heading (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_request_heading_${activeLanguageId}`"
                                                    :id="`booking_request_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_request_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_request_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_request_heading.booking_request_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_request_heading.booking_request_heading_${activeLanguageId}`
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
                                                        :for="`seat_requested_label_${activeLanguageId}`"
                                                        >Seat requested label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seat_requested_label_${activeLanguageId}`"
                                                    :id="`seat_requested_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seat_requested_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seat_requested_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seat_requested_label.seat_requested_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seat_requested_label.seat_requested_label_${activeLanguageId}`
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
                                                        :for="`request_accept_label_${activeLanguageId}`"
                                                        >Request accept label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`request_accept_label_${activeLanguageId}`"
                                                    :id="`request_accept_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'request_accept_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'request_accept_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `request_accept_label.request_accept_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `request_accept_label.request_accept_label_${activeLanguageId}`
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
                                                        :for="`request_reject_label_${activeLanguageId}`"
                                                        >Request reject label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`request_reject_label_${activeLanguageId}`"
                                                    :id="`request_reject_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'request_reject_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'request_reject_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `request_reject_label.request_reject_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `request_reject_label.request_reject_label_${activeLanguageId}`
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
                                                        :for="`secured_cash_heading_${activeLanguageId}`"
                                                        >Secured-cash heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`secured_cash_heading_${activeLanguageId}`"
                                                    :id="`secured_cash_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'secured_cash_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'secured_cash_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `secured_cash_heading.secured_cash_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `secured_cash_heading.secured_cash_heading_${activeLanguageId}`
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
                                                        >Enter code label</label
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
                                                        :for="`mobile_seat_booked_heading_${activeLanguageId}`"
                                                        >Seat booked heading (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_seat_booked_heading_${activeLanguageId}`"
                                                    :id="`mobile_seat_booked_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_seat_booked_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_seat_booked_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_seat_booked_heading.mobile_seat_booked_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_seat_booked_heading.mobile_seat_booked_heading_${activeLanguageId}`
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
                                                        :for="`mobile_seat_booked_label_${activeLanguageId}`"
                                                        >Seats booked label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_seat_booked_label_${activeLanguageId}`"
                                                    :id="`mobile_seat_booked_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_seat_booked_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_seat_booked_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_seat_booked_label.mobile_seat_booked_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_seat_booked_label.mobile_seat_booked_label_${activeLanguageId}`
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
                                                        :for="`mobile_seat_fare_label_${activeLanguageId}`"
                                                        >Seat fare label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_seat_fare_label_${activeLanguageId}`"
                                                    :id="`mobile_seat_fare_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_seat_fare_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_seat_fare_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_seat_fare_label.mobile_seat_fare_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_seat_fare_label.mobile_seat_fare_label_${activeLanguageId}`
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
                                                        :for="`mobile_seat_booking_fee_label_${activeLanguageId}`"
                                                        >Seat booking fee label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_seat_booking_fee_label_${activeLanguageId}`"
                                                    :id="`mobile_seat_booking_fee_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_seat_booking_fee_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_seat_booking_fee_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_seat_booking_fee_label.mobile_seat_booking_fee_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_seat_booking_fee_label.mobile_seat_booking_fee_label_${activeLanguageId}`
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
                                                        :for="`mobile_seat_total_amount_label_${activeLanguageId}`"
                                                        >Seat total amount label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_seat_total_amount_label_${activeLanguageId}`"
                                                    :id="`mobile_seat_total_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_seat_total_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_seat_total_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_seat_total_amount_label.mobile_seat_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_seat_total_amount_label.mobile_seat_total_amount_label_${activeLanguageId}`
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
                                                        :for="`booking_table_heading_${activeLanguageId}`"
                                                        >Booking table heading (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_table_heading_${activeLanguageId}`"
                                                    :id="`booking_table_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_table_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_table_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_table_heading.booking_table_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_table_heading.booking_table_heading_${activeLanguageId}`
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
                                                        :for="`passenger_column_label_${activeLanguageId}`"
                                                        >Passenger column label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_column_label_${activeLanguageId}`"
                                                    :id="`passenger_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_column_label.passenger_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_column_label.passenger_column_label_${activeLanguageId}`
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
                                                        :for="`seat_booked_column_label_${activeLanguageId}`"
                                                        >Seat booked column label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seat_booked_column_label_${activeLanguageId}`"
                                                    :id="`seat_booked_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seat_booked_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seat_booked_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seat_booked_column_label.seat_booked_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seat_booked_column_label.seat_booked_column_label_${activeLanguageId}`
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
                                                        :for="`total_cost_column_label_${activeLanguageId}`"
                                                        >Total cost column label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`total_cost_column_label_${activeLanguageId}`"
                                                    :id="`total_cost_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'total_cost_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'total_cost_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `total_cost_column_label.total_cost_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `total_cost_column_label.total_cost_column_label_${activeLanguageId}`
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
                                                        :for="`booked_on_column_label_${activeLanguageId}`"
                                                        >Booked on column label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booked_on_column_label_${activeLanguageId}`"
                                                    :id="`booked_on_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booked_on_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booked_on_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booked_on_column_label.booked_on_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booked_on_column_label.booked_on_column_label_${activeLanguageId}`
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
                                                        :for="`status_column_label_${activeLanguageId}`"
                                                        >Status column label (Web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`status_column_label_${activeLanguageId}`"
                                                    :id="`status_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'status_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'status_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `status_column_label.status_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `status_column_label.status_column_label_${activeLanguageId}`
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
                                                        :for="`booking_requested_status_label_${activeLanguageId}`"
                                                        >Booking requested status label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_requested_status_label_${activeLanguageId}`"
                                                    :id="`booking_requested_status_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_requested_status_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_requested_status_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_requested_status_label.booking_requested_status_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_requested_status_label.booking_requested_status_label_${activeLanguageId}`
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
                                                        :for="`seat_booked_status_label_${activeLanguageId}`"
                                                        >Seat booked status label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seat_booked_status_label_${activeLanguageId}`"
                                                    :id="`seat_booked_status_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seat_booked_status_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seat_booked_status_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seat_booked_status_label.seat_booked_status_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seat_booked_status_label.seat_booked_status_label_${activeLanguageId}`
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
                                                        :for="`booking_denied_status_label_${activeLanguageId}`"
                                                        >Booked denied status label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_denied_status_label_${activeLanguageId}`"
                                                    :id="`booking_denied_status_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_denied_status_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_denied_status_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_denied_status_label.booking_denied_status_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_denied_status_label.booking_denied_status_label_${activeLanguageId}`
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
                                                        :for="`actions_column_label_${activeLanguageId}`"
                                                        >Actions column label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`actions_column_label_${activeLanguageId}`"
                                                    :id="`actions_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'actions_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'actions_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `actions_column_label.actions_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `actions_column_label.actions_column_label_${activeLanguageId}`
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
                                                        :for="`edit_button_actions_label_${activeLanguageId}`"
                                                        >Partial booking button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_button_actions_label_${activeLanguageId}`"
                                                    :id="`edit_button_actions_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'edit_button_actions_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_button_actions_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_button_actions_label.edit_button_actions_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_button_actions_label.edit_button_actions_label_${activeLanguageId}`
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
                                                        :for="`review_button_label_${activeLanguageId}`"
                                                        >Review button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`review_button_label_${activeLanguageId}`"
                                                    :id="`review_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `review_button_label.review_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `review_button_label.review_button_label_${activeLanguageId}`
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
                                                        :for="`i_reviewed_label_${activeLanguageId}`"
                                                        >I Reviewed label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`i_reviewed_label_${activeLanguageId}`"
                                                    :id="`i_reviewed_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'i_reviewed_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'i_reviewed_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `i_reviewed_label.i_reviewed_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `i_reviewed_label.i_reviewed_label_${activeLanguageId}`
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
                                                        :for="`driver_note_label_${activeLanguageId}`"
                                                        >Important notes label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_note_label_${activeLanguageId}`"
                                                    :id="`driver_note_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_note_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_note_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_note_label.driver_note_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_note_label.driver_note_label_${activeLanguageId}`
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
                                                        :for="`midnight_label_${activeLanguageId}`"
                                                        >Midnight label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`midnight_label_${activeLanguageId}`"
                                                    :id="`midnight_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'midnight_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'midnight_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `midnight_label.midnight_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `midnight_label.midnight_label_${activeLanguageId}`
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
                                                        :for="`noon_label_${activeLanguageId}`"
                                                        >Noon label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`noon_label_${activeLanguageId}`"
                                                    :id="`noon_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'noon_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'noon_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `noon_label.noon_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `noon_label.noon_label_${activeLanguageId}`
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
                                                        :for="`trip_main_heading_${activeLanguageId}`"
                                                        >Trip Heading (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`trip_main_heading_${activeLanguageId}`"
                                                    :id="`trip_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'trip_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'trip_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `trip_main_heading.trip_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `trip_main_heading.trip_main_heading_${activeLanguageId}`
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
                                                        :for="`ride_main_heading_${activeLanguageId}`"
                                                        >Ride Heading (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_main_heading_${activeLanguageId}`"
                                                    :id="`ride_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_main_heading.ride_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_main_heading.ride_main_heading_${activeLanguageId}`
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
                                                        :for="`discount_label_${activeLanguageId}`"
                                                        >Discount Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`discount_label_${activeLanguageId}`"
                                                    :id="`discount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'discount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'discount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `discount_label.discount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `discount_label.discount_label_${activeLanguageId}`
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
                                                        :for="`booking_request_main_heading_${activeLanguageId}`"
                                                        >Booking request main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_request_main_heading_${activeLanguageId}`"
                                                    :id="`booking_request_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_request_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_request_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_request_main_heading.booking_request_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_request_main_heading.booking_request_main_heading_${activeLanguageId}`
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
                                                        :for="`passenger_age_label_${activeLanguageId}`"
                                                        >Passenger age label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_age_label_${activeLanguageId}`"
                                                    :id="`passenger_age_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_age_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_age_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_age_label.passenger_age_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_age_label.passenger_age_label_${activeLanguageId}`
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
                                                        :for="`passenger_gender_label_${activeLanguageId}`"
                                                        >Passenger gender label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_gender_label_${activeLanguageId}`"
                                                    :id="`passenger_gender_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_gender_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_gender_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_gender_label.passenger_gender_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_gender_label.passenger_gender_label_${activeLanguageId}`
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
                                                        :for="`seat_on_column_label_${activeLanguageId}`"
                                                        >Seat on column label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seat_on_column_label_${activeLanguageId}`"
                                                    :id="`seat_on_column_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seat_on_column_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seat_on_column_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seat_on_column_label.seat_on_column_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seat_on_column_label.seat_on_column_label_${activeLanguageId}`
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
                                                        :for="`cancellation_policy_tooltip_${activeLanguageId}`"
                                                        >Cancellation Policy Tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancellation_policy_tooltip_${activeLanguageId}`"
                                                    :id="`cancellation_policy_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancellation_policy_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancellation_policy_tooltip.cancellation_policy_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancellation_policy_tooltip.cancellation_policy_tooltip_${activeLanguageId}`
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
                                                        :for="`cancellation_policy_tooltip_url_url_${activeLanguageId}`"
                                                        >Cancellation Policy Tooltip URL</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`cancellation_policy_tooltip_url_${activeLanguageId}`"
                                                    :id="`cancellation_policy_tooltip_url_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'cancellation_policy_tooltip_url'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_tooltip_url'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `cancellation_policy_tooltip_url.cancellation_policy_tooltip_url_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `cancellation_policy_tooltip_url.cancellation_policy_tooltip_url_${activeLanguageId}`
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
                                                        :for="`firm_cancellation_confirm_poup_heading_${activeLanguageId}`"
                                                        >Firm cancellation confirm popup heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_cancellation_confirm_poup_heading_${activeLanguageId}`"
                                                    :id="`firm_cancellation_confirm_poup_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_cancellation_confirm_poup_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_confirm_poup_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_cancellation_confirm_poup_heading.firm_cancellation_confirm_poup_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_cancellation_confirm_poup_heading.firm_cancellation_confirm_poup_heading_${activeLanguageId}`
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
                                                        :for="`firm_cancellation_confirm_poup_text_${activeLanguageId}`"
                                                        >Firm cancellation confirm popup text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_cancellation_confirm_poup_text_${activeLanguageId}`"
                                                    :id="`firm_cancellation_confirm_poup_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_cancellation_confirm_poup_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_confirm_poup_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_cancellation_confirm_poup_text.firm_cancellation_confirm_poup_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_cancellation_confirm_poup_text.firm_cancellation_confirm_poup_text_${activeLanguageId}`
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
                                                        :for="`firm_cancellation_confirm_poup_sub_text_${activeLanguageId}`"
                                                        >Firm cancellation confirm popup text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_cancellation_confirm_poup_sub_text_${activeLanguageId}`"
                                                    :id="`firm_cancellation_confirm_poup_sub_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_cancellation_confirm_poup_sub_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_confirm_poup_sub_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_cancellation_confirm_poup_sub_text.firm_cancellation_confirm_poup_sub_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_cancellation_confirm_poup_sub_text.firm_cancellation_confirm_poup_sub_text_${activeLanguageId}`
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
                                                        :for="`firm_cancellation_confirm_poup_yes_label_${activeLanguageId}`"
                                                        >Firm cancellation confirm popup yes label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_cancellation_confirm_poup_yes_label_${activeLanguageId}`"
                                                    :id="`firm_cancellation_confirm_poup_yes_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_cancellation_confirm_poup_yes_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_confirm_poup_yes_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_cancellation_confirm_poup_yes_label.firm_cancellation_confirm_poup_yes_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_cancellation_confirm_poup_yes_label.firm_cancellation_confirm_poup_yes_label_${activeLanguageId}`
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
                                                        :for="`firm_cancellation_confirm_poup_no_label_${activeLanguageId}`"
                                                        >Firm cancellation confirm popup no label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_cancellation_confirm_poup_no_label_${activeLanguageId}`"
                                                    :id="`firm_cancellation_confirm_poup_no_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_cancellation_confirm_poup_no_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_confirm_poup_no_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_cancellation_confirm_poup_no_label.firm_cancellation_confirm_poup_no_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_cancellation_confirm_poup_no_label.firm_cancellation_confirm_poup_no_label_${activeLanguageId}`
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
                                                        :for="`chat_error_message_${activeLanguageId}`"
                                                        >Chat error message text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`chat_error_message_${activeLanguageId}`"
                                                    :id="`chat_error_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'chat_error_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'chat_error_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `chat_error_message.chat_error_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `chat_error_message.chat_error_message_${activeLanguageId}`
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
                                                        :for="`empty_chat_placeholder_${activeLanguageId}`"
                                                        >Empty chat placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`empty_chat_placeholder_${activeLanguageId}`"
                                                    :id="`empty_chat_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'empty_chat_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'empty_chat_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `empty_chat_placeholder.empty_chat_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `empty_chat_placeholder.empty_chat_placeholder_${activeLanguageId}`
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
                                                        :for="`verified_phone_${activeLanguageId}`"
                                                        >Verified phone number popup text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verified_phone_${activeLanguageId}`"
                                                    :id="`verified_phone_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verified_phone'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verified_phone'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verified_phone.verified_phone_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verified_phone.verified_phone_${activeLanguageId}`
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
                                                        :for="`verified_email_${activeLanguageId}`"
                                                        >Verified email popup text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verified_email_${activeLanguageId}`"
                                                    :id="`verified_email_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verified_email'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verified_email'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verified_email.verified_email_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verified_email.verified_email_${activeLanguageId}`
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
            excelForm: { selectedLanguageId: '', file: null },
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
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/';
            return base.endsWith('/') ? base : base + '/';
        },
    },
    created() {
        this.fetchLanguages();
    },
    methods: {
        handleFileChange(event) {
            const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
            this.excelForm.file = file;
        },
        uploadExcelFile() {
            this.excelValidationErrors = {};
            this.excelErrors = [];
            if (!this.excelForm.selectedLanguageId) {
                this.excelValidationErrors.language_id = 'Language is required';
            }
            if (!this.excelForm.file) {
                this.excelValidationErrors.excel_file = 'Excel file is required';
            }
            if (Object.keys(this.excelValidationErrors).length > 0) return;

            const formData = new FormData();
            formData.append('language_id', this.excelForm.selectedLanguageId);
            formData.append('excel_file', this.excelForm.file);
            this.excelUploading = true;
            axios
                .post(`${this.mixAdminApiUrl}upload-ride-detail-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    this.fetchRideDetailPageSetting();
                    this.$refs.excelFile && (this.$refs.excelFile.value = '');
                    this.excelForm.file = null;
                })
                .catch((err) => {
                    if (err?.response?.status === 422) {
                        const data = err?.response?.data;
                        if (data?.errors) {
                            this.excelErrors = data.errors;
                        }
                    }
                })
                .finally(() => {
                    this.excelUploading = false;
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
                            this.handleInput("", language, "from_label");
                            this.handleInput("", language, "to_label");
                            this.handleInput("", language, "at_label");
                            this.handleInput("", language, "co_passenger_label");
                            this.handleInput("", language, "ride_co_passenger_heading");
                            this.handleInput("", language, "trip_co_passenger_heading");
                            this.handleInput("", language, "payment_method_label");
                            this.handleInput("", language, "booking_type_label");
                            this.handleInput("", language, "cancellation_policy_label");
                            this.handleInput("", language, "luggage_label");
                            this.handleInput("", language, "smoking_label");
                            this.handleInput("", language, "pets_label");
                            this.handleInput("", language, "seats_left_label");
                            this.handleInput("", language, "per_seat_label");
                            this.handleInput("", language, "pickup_dropoff_info_heading");
                            this.handleInput("", language, "pickup_label");
                            this.handleInput("", language, "dropoff_label");
                            this.handleInput("", language, "description_label");
                            this.handleInput("", language, "ride_features_label");
                            this.handleInput("", language, "ride_seat_label");
                            this.handleInput("", language, "all_seats_booked_label");
                            this.handleInput("", language, "ride_canceller_by_driver");
                            this.handleInput("", language, "ride_completed_text");
                            this.handleInput("", language, "book_seat_btn_label");
                            this.handleInput("", language, "no_seat_available_label");
                            this.handleInput("", language, "no_ride_found_message");
                            this.handleInput("", language, "cancel_booking_btn_label");
                            this.handleInput("", language, "cancel_ride_btn_label");
                            this.handleInput("", language, "cancel_ride_confirmation");
                            this.handleInput("", language, "cancel_ride_yes_btn");
                            this.handleInput("", language, "cancel_ride_no_btn");
                            this.handleInput("", language, "edit_ride_btn_label");
                            this.handleInput("", language, "review_label");
                            this.handleInput("", language, "booking_request_heading");
                            this.handleInput("", language, "seat_requested_label");
                            this.handleInput("", language, "request_accept_label");
                            this.handleInput("", language, "request_reject_label");
                            this.handleInput("", language, "secured_cash_heading");
                            this.handleInput("", language, "enter_code_label");
                            this.handleInput("", language, "mobile_seat_booked_heading");
                            this.handleInput("", language, "mobile_seat_booked_label");
                            this.handleInput("", language, "mobile_seat_fare_label");
                            this.handleInput("", language, "mobile_seat_booking_fee_label");
                            this.handleInput("", language, "mobile_seat_total_amount_label");
                            this.handleInput("", language, "vehicle_info_label");
                            this.handleInput("", language, "driver_info_label");
                            this.handleInput("", language, "review_driver_info_label");
                            this.handleInput("", language, "review_passanger_label");
                            this.handleInput("", language, "driver_label");
                            this.handleInput("", language, "cancellation_policy");
                            this.handleInput("", language, "passengers_driven_label");
                            this.handleInput("", language, "driver_age_label");
                            this.handleInput("", language, "driver_chat_heading");
                            this.handleInput("", language, "driver_chat_with");
                            this.handleInput("", language, "driver_chat_label");
                            this.handleInput("", language, "driver_chat_button_label");
                            this.handleInput("", language, "booking_table_heading");
                            this.handleInput("", language, "passenger_column_label");
                            this.handleInput("", language, "seat_booked_column_label");
                            this.handleInput("", language, "total_cost_column_label");
                            this.handleInput("", language, "booked_on_column_label");
                            this.handleInput("", language, "status_column_label");
                            this.handleInput("", language, "booking_requested_status_label");
                            this.handleInput("", language, "seat_booked_status_label");
                            this.handleInput("", language, "booking_denied_status_label");
                            this.handleInput("", language, "actions_column_label");
                            this.handleInput("", language, "edit_button_actions_label");
                            this.handleInput("", language, "review_button_label");
                            this.handleInput("", language, "i_reviewed_label");
                            this.handleInput("", language, "driver_note_label");
                            this.handleInput("", language, "midnight_label");
                            this.handleInput("", language, "noon_label");
                            this.handleInput("", language, "ride_main_heading");
                            this.handleInput("", language, "trip_main_heading");
                            this.handleInput("", language, "discount_label");
                            this.handleInput("", language, "booking_request_main_heading");
                            this.handleInput("", language, "passenger_age_label");
                            this.handleInput("", language, "passenger_gender_label");
                            this.handleInput("", language, "seat_on_column_label");
                            this.handleInput("", language, "cancellation_policy_tooltip");
                            this.handleInput("", language, "cancellation_policy_tooltip_url");
                            this.handleInput("", language, "firm_cancellation_confirm_poup_heading");
                            this.handleInput("", language, "firm_cancellation_confirm_poup_text");
                            this.handleInput("", language, "firm_cancellation_confirm_poup_sub_text");
                            this.handleInput("", language, "firm_cancellation_confirm_poup_yes_label");
                            this.handleInput("", language, "firm_cancellation_confirm_poup_no_label");
                            this.handleInput("", language, "chat_error_message");
                            this.handleInput("", language, "empty_chat_placeholder");
                            this.handleInput("", language, "instant_btn_label");
                            this.handleInput("", language, "verified_phone");
                            this.handleInput("", language, "verified_email");
                        });
                        this.fetchRideDetailPageSetting();
                    }
                });
        },
        fetchRideDetailPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-ride-detail-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let ride_detail_page_setting_detail =
                            res?.data?.data?.ride_detail_page_setting_detail || [];
                        ride_detail_page_setting_detail.map((setting) => {
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
                                setting?.from_label,
                                setting?.language,
                                "from_label"
                            );
                            this.handleInput(
                                setting?.to_label,
                                setting?.language,
                                "to_label"
                            );
                            this.handleInput(
                                setting?.at_label,
                                setting?.language,
                                "at_label"
                            );
                            this.handleInput(
                                setting?.co_passenger_label,
                                setting?.language,
                                "co_passenger_label"
                            );
                            this.handleInput(
                                setting?.ride_co_passenger_heading,
                                setting?.language,
                                "ride_co_passenger_heading"
                            );
                            this.handleInput(
                                setting?.trip_co_passenger_heading,
                                setting?.language,
                                "trip_co_passenger_heading"
                            );
                            this.handleInput(
                                setting?.payment_method_label,
                                setting?.language,
                                "payment_method_label"
                            );
                            this.handleInput(
                                setting?.booking_type_label,
                                setting?.language,
                                "booking_type_label"
                            );  this.handleInput(
                                setting?.discount_label,
                                setting?.language,
                                "discount_label"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label,
                                setting?.language,
                                "cancellation_policy_label"
                            );
                            this.handleInput(
                                setting?.luggage_label,
                                setting?.language,
                                "luggage_label"
                            );
                            this.handleInput(
                                setting?.smoking_label,
                                setting?.language,
                                "smoking_label"
                            );
                            this.handleInput(
                                setting?.pets_label,
                                setting?.language,
                                "pets_label"
                            );
                            this.handleInput(
                                setting?.seats_left_label,
                                setting?.language,
                                "seats_left_label"
                            );
                            this.handleInput(
                                setting?.per_seat_label,
                                setting?.language,
                                "per_seat_label"
                            );
                            this.handleInput(
                                setting?.pickup_dropoff_info_heading,
                                setting?.language,
                                "pickup_dropoff_info_heading"
                            );
                            this.handleInput(
                                setting?.pickup_label,
                                setting?.language,
                                "pickup_label"
                            );
                            this.handleInput(
                                setting?.dropoff_label,
                                setting?.language,
                                "dropoff_label"
                            );
                            this.handleInput(
                                setting?.description_label,
                                setting?.language,
                                "description_label"
                            );
                            this.handleInput(
                                setting?.ride_features_label,
                                setting?.language,
                                "ride_features_label"
                            );
                            this.handleInput(
                                setting?.ride_seat_label,
                                setting?.language,
                                "ride_seat_label"
                            );
                            this.handleInput(
                                setting?.all_seats_booked_label,
                                setting?.language,
                                "all_seats_booked_label"
                            );
                            this.handleInput(
                                setting?.ride_canceller_by_driver,
                                setting?.language,
                                "ride_canceller_by_driver"
                            );
                            this.handleInput(
                                setting?.ride_completed_text,
                                setting?.language,
                                "ride_completed_text"
                            );
                            this.handleInput(
                                setting?.book_seat_btn_label,
                                setting?.language,
                                "book_seat_btn_label"
                            );
                            this.handleInput(
                                setting?.instant_btn_label,
                                setting?.language,
                                "instant_btn_label"
                            );
                            
                            this.handleInput(
                                setting?.verified_email,
                                setting?.language,
                                "verified_email"
                            );
                            
                            this.handleInput(
                                setting?.verified_phone,
                                setting?.language,
                                "verified_phone"
                            );
                            this.handleInput(
                                setting?.no_seat_available_label,
                                setting?.language,
                                "no_seat_available_label"
                            );
                            this.handleInput(
                                setting?.no_ride_found_message,
                                setting?.language,
                                "no_ride_found_message"
                            );
                            this.handleInput(
                                setting?.cancel_booking_btn_label,
                                setting?.language,
                                "cancel_booking_btn_label"
                            );
                            this.handleInput(
                                setting?.cancel_ride_btn_label,
                                setting?.language,
                                "cancel_ride_btn_label"
                            );
                            this.handleInput(
                                setting?.cancel_ride_confirmation,
                                setting?.language,
                                "cancel_ride_confirmation"
                            );
                            this.handleInput(
                                setting?.cancel_ride_yes_btn,
                                setting?.language,
                                "cancel_ride_yes_btn"
                            );
                            this.handleInput(
                                setting?.cancel_ride_no_btn,
                                setting?.language,
                                "cancel_ride_no_btn"
                            );
                            this.handleInput(
                                setting?.edit_ride_btn_label,
                                setting?.language,
                                "edit_ride_btn_label"
                            );
                            this.handleInput(
                                setting?.review_label,
                                setting?.language,
                                "review_label"
                            );
                            this.handleInput(
                                setting?.booking_request_heading,
                                setting?.language,
                                "booking_request_heading"
                            );
                            this.handleInput(
                                setting?.seat_requested_label,
                                setting?.language,
                                "seat_requested_label"
                            );
                            this.handleInput(
                                setting?.request_accept_label,
                                setting?.language,
                                "request_accept_label"
                            );
                            this.handleInput(
                                setting?.request_reject_label,
                                setting?.language,
                                "request_reject_label"
                            );
                            this.handleInput(
                                setting?.secured_cash_heading,
                                setting?.language,
                                "secured_cash_heading"
                            );
                            this.handleInput(
                                setting?.enter_code_label,
                                setting?.language,
                                "enter_code_label"
                            );
                            this.handleInput(
                                setting?.mobile_seat_booked_heading,
                                setting?.language,
                                "mobile_seat_booked_heading"
                            );
                            this.handleInput(
                                setting?.mobile_seat_booked_label,
                                setting?.language,
                                "mobile_seat_booked_label"
                            );
                            this.handleInput(
                                setting?.mobile_seat_fare_label,
                                setting?.language,
                                "mobile_seat_fare_label"
                            );
                            this.handleInput(
                                setting?.mobile_seat_booking_fee_label,
                                setting?.language,
                                "mobile_seat_booking_fee_label"
                            );
                            this.handleInput(
                                setting?.mobile_seat_total_amount_label,
                                setting?.language,
                                "mobile_seat_total_amount_label"
                            );
                            this.handleInput(
                                setting?.vehicle_info_label,
                                setting?.language,
                                "vehicle_info_label"
                            );
                            this.handleInput(
                                setting?.driver_info_label,
                                setting?.language,
                                "driver_info_label"
                            );
                            this.handleInput(
                                setting?.review_driver_info_label,
                                setting?.language,
                                "review_driver_info_label"
                            );
                            this.handleInput(
                                setting?.review_passanger_label,
                                setting?.language,
                                "review_passanger_label"
                            );
                            this.handleInput(
                                setting?.driver_label,
                                setting?.language,
                                "driver_label"
                            );
                            this.handleInput(
                                setting?.cancellation_policy,
                                setting?.language,
                                "cancellation_policy"
                            );
                            this.handleInput(
                                setting?.passengers_driven_label,
                                setting?.language,
                                "passengers_driven_label"
                            );
                            this.handleInput(
                                setting?.driver_age_label,
                                setting?.language,
                                "driver_age_label"
                            );
                            this.handleInput(
                                setting?.driver_chat_heading,
                                setting?.language,
                                "driver_chat_heading"
                            );
                            this.handleInput(
                                setting?.driver_chat_with,
                                setting?.language,
                                "driver_chat_with"
                            );
                            this.handleInput(
                                setting?.driver_chat_label,
                                setting?.language,
                                "driver_chat_label"
                            );
                            this.handleInput(
                                setting?.driver_chat_button_label,
                                setting?.language,
                                "driver_chat_button_label"
                            );
                            this.handleInput(
                                setting?.booking_table_heading,
                                setting?.language,
                                "booking_table_heading"
                            );
                            this.handleInput(
                                setting?.passenger_column_label,
                                setting?.language,
                                "passenger_column_label"
                            );
                            this.handleInput(
                                setting?.seat_booked_column_label,
                                setting?.language,
                                "seat_booked_column_label"
                            );
                            this.handleInput(
                                setting?.total_cost_column_label,
                                setting?.language,
                                "total_cost_column_label"
                            );
                            this.handleInput(
                                setting?.booked_on_column_label,
                                setting?.language,
                                "booked_on_column_label"
                            );
                            this.handleInput(
                                setting?.status_column_label,
                                setting?.language,
                                "status_column_label"
                            );
                            this.handleInput(
                                setting?.booking_requested_status_label,
                                setting?.language,
                                "booking_requested_status_label"
                            );
                            this.handleInput(
                                setting?.seat_booked_status_label,
                                setting?.language,
                                "seat_booked_status_label"
                            );
                            this.handleInput(
                                setting?.booking_denied_status_label,
                                setting?.language,
                                "booking_denied_status_label"
                            );
                            this.handleInput(
                                setting?.actions_column_label,
                                setting?.language,
                                "actions_column_label"
                            );
                            this.handleInput(
                                setting?.edit_button_actions_label,
                                setting?.language,
                                "edit_button_actions_label"
                            );
                            this.handleInput(
                                setting?.review_button_label,
                                setting?.language,
                                "review_button_label"
                            );
                            this.handleInput(
                                setting?.i_reviewed_label,
                                setting?.language,
                                "i_reviewed_label"
                            );
                            this.handleInput(
                                setting?.driver_note_label,
                                setting?.language,
                                "driver_note_label"
                            ); this.handleInput(
                                setting?.trip_main_heading,
                                setting?.language,
                                "trip_main_heading"
                            ); this.handleInput(
                                setting?.ride_main_heading,
                                setting?.language,
                                "ride_main_heading"
                            );
                            this.handleInput(
                                setting?.midnight_label,
                                setting?.language,
                                "midnight_label"
                            );
                            this.handleInput(
                                setting?.noon_label,
                                setting?.language,
                                "noon_label"
                            );

                            this.handleInput(
                                setting?.booking_request_main_heading,
                                setting?.language,
                                "booking_request_main_heading"
                            );
                            this.handleInput(
                                setting?.passenger_age_label,
                                setting?.language,
                                "passenger_age_label"
                            );
                            this.handleInput(
                                setting?.passenger_gender_label,
                                setting?.language,
                                "passenger_gender_label"
                            );
                            this.handleInput(
                                setting?.seat_on_column_label,
                                setting?.language,
                                "seat_on_column_label"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_tooltip,
                                setting?.language,
                                "cancellation_policy_tooltip"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_tooltip_url,
                                setting?.language,
                                "cancellation_policy_tooltip_url"
                            );
                            this.handleInput(
                                setting?.firm_cancellation_confirm_poup_heading,
                                setting?.language,
                                "firm_cancellation_confirm_poup_heading"
                            );
                            this.handleInput(
                                setting?.firm_cancellation_confirm_poup_text,
                                setting?.language,
                                "firm_cancellation_confirm_poup_text"
                            );
                            this.handleInput(
                                setting?.firm_cancellation_confirm_poup_sub_text,
                                setting?.language,
                                "firm_cancellation_confirm_poup_sub_text"
                            );
                            this.handleInput(
                                setting?.firm_cancellation_confirm_poup_yes_label,
                                setting?.language,
                                "firm_cancellation_confirm_poup_yes_label"
                            );
                            this.handleInput(
                                setting?.firm_cancellation_confirm_poup_no_label,
                                setting?.language,
                                "firm_cancellation_confirm_poup_no_label"
                            );
                            
                            this.handleInput(
                                setting?.chat_error_message,
                                setting?.language,
                                "chat_error_message"
                            );
                            
                            this.handleInput(
                                setting?.empty_chat_placeholder,
                                setting?.language,
                                "empty_chat_placeholder"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-ride-detail-page-setting`,
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
                    `from_label.from_label_${language.id}`
                ) ||
                validationErros.has(
                    `to_label.to_label_${language.id}`
                ) ||
                validationErros.has(
                    `at_label.at_label_${language.id}`
                ) ||
                validationErros.has(
                    `co_passenger_label.co_passenger_label_${language.id}`
                ) ||
                validationErros.has(
                    `ride_co_passenger_heading.ride_co_passenger_heading_${language.id}`
                ) ||
                validationErros.has(
                    `trip_co_passenger_heading.trip_co_passenger_heading_${language.id}`
                ) ||
                validationErros.has(
                    `payment_method_label.payment_method_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_type_label.booking_type_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label.cancellation_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_label.luggage_label_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_label.smoking_label_${language.id}`
                ) ||
                validationErros.has(
                    `pets_label.pets_label_${language.id}`
                ) ||
                validationErros.has(
                    `seats_left_label.seats_left_label_${language.id}`
                ) ||
                validationErros.has(
                    `per_seat_label.per_seat_label_${language.id}`
                ) ||
                validationErros.has(
                    `pickup_dropoff_info_heading.pickup_dropoff_info_heading_${language.id}`
                ) ||
                validationErros.has(
                    `pickup_label.pickup_label_${language.id}`
                ) ||
                validationErros.has(
                    `dropoff_label.dropoff_label_${language.id}`
                ) ||
                validationErros.has(
                    `description_label.description_label_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_label.ride_features_label_${language.id}`
                ) ||
                validationErros.has(
                    `ride_seat_label.ride_seat_label_${language.id}`
                ) ||
                validationErros.has(
                    `all_seats_booked_label.all_seats_booked_label_${language.id}`
                ) ||
                validationErros.has(
                    `ride_canceller_by_driver.ride_canceller_by_driver_${language.id}`
                ) ||
                validationErros.has(
                    `ride_completed_text.ride_completed_text_${language.id}`
                ) ||
                validationErros.has(
                    `book_seat_btn_label.book_seat_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `instant_btn_label.instant_btn_label_${language.id}`
                ) ||
                
                validationErros.has(
                    `verified_phone.verified_phone_${language.id}`
                ) ||
                
                validationErros.has(
                    `verified_email.verified_email_${language.id}`
                ) ||
                validationErros.has(
                    `no_seat_available_label.no_seat_available_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_ride_found_message.no_ride_found_message_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_booking_btn_label.cancel_booking_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_ride_btn_label.cancel_ride_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_ride_confirmation.cancel_ride_confirmation_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_ride_yes_btn.cancel_ride_yes_btn_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_ride_no_btn.cancel_ride_no_btn_${language.id}`
                ) ||
                validationErros.has(
                    `edit_ride_btn_label.edit_ride_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `review_label.review_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_request_heading.booking_request_heading_${language.id}`
                ) ||
                validationErros.has(
                    `seat_requested_label.seat_requested_label_${language.id}`
                ) ||
                validationErros.has(
                    `request_accept_label.request_accept_label_${language.id}`
                ) ||
                validationErros.has(
                    `request_reject_label.request_reject_label_${language.id}`
                ) ||
                validationErros.has(
                    `secured_cash_heading.secured_cash_heading_${language.id}`
                ) ||
                validationErros.has(
                    `enter_code_label.enter_code_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_seat_booked_heading.mobile_seat_booked_heading_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_seat_booked_label.mobile_seat_booked_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_seat_fare_label.mobile_seat_fare_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_seat_booking_fee_label.mobile_seat_booking_fee_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_seat_total_amount_label.mobile_seat_total_amount_label_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_info_label.vehicle_info_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_info_label.driver_info_label_${language.id}`
                ) ||
                validationErros.has(
                    `review_driver_info_label.review_driver_info_label_${language.id}`
                ) ||
                validationErros.has(
                    `review_passanger_label.review_passanger_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_label.driver_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy.cancellation_policy_${language.id}`
                ) ||
                validationErros.has(
                    `passengers_driven_label.passengers_driven_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_age_label.driver_age_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_chat_heading.driver_chat_heading_${language.id}`
                ) ||
                validationErros.has(
                    `driver_chat_with.driver_chat_with_${language.id}`
                ) ||
                validationErros.has(
                    `driver_chat_label.driver_chat_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_chat_button_label.driver_chat_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_table_heading.booking_table_heading_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_column_label.passenger_column_label_${language.id}`
                ) ||
                validationErros.has(
                    `seat_booked_column_label.seat_booked_column_label_${language.id}`
                ) ||
                validationErros.has(
                    `total_cost_column_label.total_cost_column_label_${language.id}`
                ) ||
                validationErros.has(
                    `booked_on_column_label.booked_on_column_label_${language.id}`
                ) ||
                validationErros.has(
                    `status_column_label.status_column_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_requested_status_label.booking_requested_status_label_${language.id}`
                ) ||
                validationErros.has(
                    `seat_booked_status_label.seat_booked_status_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_denied_status_label.booking_denied_status_label_${language.id}`
                ) ||
                validationErros.has(
                    `actions_column_label.actions_column_label_${language.id}`
                ) ||
                validationErros.has(
                    `edit_button_actions_label.edit_button_actions_label_${language.id}`
                ) ||
                validationErros.has(
                    `review_button_label.review_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `i_reviewed_label.i_reviewed_label_${language.id}`
                ) ||
                validationErros.has(
                    `midnight_label.midnight_label_${language.id}`
                ) ||
                validationErros.has(
                    `noon_label.noon_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_note_label.driver_note_label_${language.id}`
                )
                ||
                validationErros.has(
                    `cancellation_policy_tooltip.cancellation_policy_tooltip_${language.id}`
                )
                ||
                validationErros.has(
                    `cancellation_policy_tooltip_url.cancellation_policy_tooltip_url_${language.id}`
                )
                ||
                validationErros.has(
                    `firm_cancellation_confirm_poup_heading.firm_cancellation_confirm_poup_heading_${language.id}`
                )||
                validationErros.has(
                    `firm_cancellation_confirm_poup_text.firm_cancellation_confirm_poup_text_${language.id}`
                )||
                validationErros.has(
                    `firm_cancellation_confirm_poup_sub_text.firm_cancellation_confirm_poup_sub_text_${language.id}`
                )||
                validationErros.has(
                    `firm_cancellation_confirm_poup_yes_label.firm_cancellation_confirm_poup_yes_label_${language.id}`
                )||
                validationErros.has(
                    `firm_cancellation_confirm_poup_no_label.firm_cancellation_confirm_poup_no_label_${language.id}`
                )||
                validationErros.has(
                    `chat_error_message.chat_error_message_${language.id}`
                )||
                validationErros.has(
                    `empty_chat_placeholder.empty_chat_placeholder_${language.id}`
                )
            );
        },
    },
};
</script>
