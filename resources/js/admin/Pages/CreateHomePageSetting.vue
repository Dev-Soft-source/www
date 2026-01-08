<template>
    <AppLayout>
        <section class="home-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Home page settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Home page setting translations for a specific language.</p>

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
                                    <a :href="`${mixAdminApiUrl}download-home-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                    class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
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

                                <!-- slider section start -->
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
                                            Slider section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[0]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`slider_heading_${activeLanguageId}`"
                                                        >Slider heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`slider_heading_${activeLanguageId}`"
                                                    :id="`slider_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'slider_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'slider_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `slider_heading.slider_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `slider_heading.slider_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`slider_from_placeholder_${activeLanguageId}`"
                                                        >From placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`slider_from_placeholder_${activeLanguageId}`"
                                                    :id="`slider_from_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'slider_from_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'slider_from_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `slider_from_placeholder.slider_from_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `slider_from_placeholder.slider_from_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`slider_to_placeholder_${activeLanguageId}`"
                                                        >To placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`slider_to_placeholder_${activeLanguageId}`"
                                                    :id="`slider_to_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'slider_to_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'slider_to_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `slider_to_placeholder.slider_to_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `slider_to_placeholder.slider_to_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`slider_date_placeholder_${activeLanguageId}`"
                                                        >Date placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`slider_date_placeholder_${activeLanguageId}`"
                                                    :id="`slider_date_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'slider_date_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'slider_date_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `slider_date_placeholder.slider_date_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `slider_date_placeholder.slider_date_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`slider_required_error_${activeLanguageId}`"
                                                        >Required error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`slider_required_error_${activeLanguageId}`"
                                                    :id="`slider_required_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'slider_required_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'slider_required_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `slider_required_error.slider_required_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `slider_required_error.slider_required_error_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`slider_image_${activeLanguageId}`"
                                                            >Slider background image</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`slider_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`slider_image_${activeLanguageId}`"
                                                        :id="`slider_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'slider_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `slider_image.slider_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `slider_image.slider_image_${activeLanguageId}`
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
                                                            form['slider_image'] &&
                                                            form['slider_image'][`slider_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['slider_image'] &&
                                                            form['slider_image'][`slider_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['slider_image'][`slider_image_${activeLanguageId}`]
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
                                                            >From field icon</label
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
                                                            >Swap field icon</label
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
                                                            >To field icon</label
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
                                                            >Date field icon</label
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
                                                            >Search field icon</label
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
                                <!-- slider section end -->

                                <!-- About section start -->
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
                                            Rides types
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[1]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section1_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section1_main_heading_${activeLanguageId}`"
                                                    :id="`section1_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section1_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section1_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section1_main_heading.section1_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_main_heading.section1_main_heading_${activeLanguageId}`
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
                                                        :for="`section1_pink_rides_label_${activeLanguageId}`"
                                                        >ProximaRide label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section1_pink_rides_label_${activeLanguageId}`"
                                                    :id="`section1_pink_rides_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section1_pink_rides_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section1_pink_rides_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section1_pink_rides_label.section1_pink_rides_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_pink_rides_label.section1_pink_rides_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section1_pink_rides_image_${activeLanguageId}`"
                                                            >ProximaRide icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section1_pink_rides_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section1_pink_rides_image_${activeLanguageId}`"
                                                        :id="`section1_pink_rides_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section1_pink_rides_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section1_pink_rides_image.section1_pink_rides_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section1_pink_rides_image.section1_pink_rides_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section1_pink_rides_image'] &&
                                                            form['section1_pink_rides_image'][`section1_pink_rides_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section1_pink_rides_image'] &&
                                                            form['section1_pink_rides_image'][`section1_pink_rides_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section1_pink_rides_image'][`section1_pink_rides_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section1_pink_rides_description_${activeLanguageId}`"
                                                        >ProximaRide
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section1_pink_rides_description'
                                                        )
                                                    "
                                                    :ref="`section1_pink_rides_description_${language.id}`"
                                                    :id="`section1_pink_rides_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section1_pink_rides_description`
                                                        ][
                                                            `section1_pink_rides_description_${language?.id}`
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
                                                        `section1_pink_rides_description.section1_pink_rides_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_pink_rides_description.section1_pink_rides_description_${activeLanguageId}`
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
                                                        :for="`section1_folks_rides_label_${activeLanguageId}`"
                                                        >Extra-Care Rides label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section1_folks_rides_label_${activeLanguageId}`"
                                                    :id="`section1_folks_rides_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section1_folks_rides_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section1_folks_rides_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section1_folks_rides_label.section1_folks_rides_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_folks_rides_label.section1_folks_rides_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="w-full">
                                                    <div class="flex justify-between w-full">
                                                        <label
                                                            :for="`section1_folks_rides_image_${activeLanguageId}`"
                                                            >Extra-Care Rides icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section1_folks_rides_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section1_folks_rides_image_${activeLanguageId}`"
                                                        :id="`section1_folks_rides_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section1_folks_rides_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section1_folks_rides_image.section1_folks_rides_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section1_folks_rides_image.section1_folks_rides_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section1_folks_rides_image'] &&
                                                            form['section1_folks_rides_image'][`section1_folks_rides_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section1_folks_rides_image'] &&
                                                            form['section1_folks_rides_image'][`section1_folks_rides_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section1_folks_rides_image'][`section1_folks_rides_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section1_folks_rides_description_${activeLanguageId}`"
                                                        >Extra-Care Rides
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section1_folks_rides_description'
                                                        )
                                                    "
                                                    :ref="`section1_folks_rides_description_${language.id}`"
                                                    :id="`section1_folks_rides_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section1_folks_rides_description`
                                                        ][
                                                            `section1_folks_rides_description_${language?.id}`
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
                                                        `section1_folks_rides_description.section1_folks_rides_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_folks_rides_description.section1_folks_rides_description_${activeLanguageId}`
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
                                                        :for="`section1_customize_label_${activeLanguageId}`"
                                                        >Customize rides label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section1_customize_label_${activeLanguageId}`"
                                                    :id="`section1_customize_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section1_customize_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section1_customize_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section1_customize_label.section1_customize_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_customize_label.section1_customize_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="w-full">
                                                    <div class="flex justify-between w-full">
                                                        <label
                                                            :for="`section1_customize_image_${activeLanguageId}`"
                                                            >Customize rides icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section1_customize_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section1_customize_image_${activeLanguageId}`"
                                                        :id="`section1_customize_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section1_customize_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section1_customize_image.section1_customize_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section1_customize_image.section1_customize_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section1_customize_image'] &&
                                                            form['section1_customize_image'][`section1_customize_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section1_customize_image'] &&
                                                            form['section1_customize_image'][`section1_customize_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section1_customize_image'][`section1_customize_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section1_customize_description_${activeLanguageId}`"
                                                        >Customize rides
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section1_customize_description'
                                                        )
                                                    "
                                                    :ref="`section1_customize_description_${language.id}`"
                                                    :id="`section1_customize_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section1_customize_description`
                                                        ][
                                                            `section1_customize_description_${language?.id}`
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
                                                        `section1_customize_description.section1_customize_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section1_customize_description.section1_customize_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- About section end -->

                                <!-- Safety first section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[2] =
                                                !collapseStates[2]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Safety first section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[2]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section2_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section2_main_heading_${activeLanguageId}`"
                                                    :id="`section2_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section2_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section2_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section2_main_heading.section2_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_main_heading.section2_main_heading_${activeLanguageId}`
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
                                                        :for="`section2_profile_verification_label_${activeLanguageId}`"
                                                        >Profile verification label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section2_profile_verification_label_${activeLanguageId}`"
                                                    :id="`section2_profile_verification_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section2_profile_verification_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section2_profile_verification_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section2_profile_verification_label.section2_profile_verification_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_profile_verification_label.section2_profile_verification_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section2_profile_verification_image_${activeLanguageId}`"
                                                            >Profile verification icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section2_profile_verification_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section2_profile_verification_image_${activeLanguageId}`"
                                                        :id="`section2_profile_verification_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section2_profile_verification_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section2_profile_verification_image.section2_profile_verification_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section2_profile_verification_image.section2_profile_verification_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section2_profile_verification_image'] &&
                                                            form['section2_profile_verification_image'][`section2_profile_verification_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section2_profile_verification_image'] &&
                                                            form['section2_profile_verification_image'][`section2_profile_verification_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section2_profile_verification_image'][`section2_profile_verification_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section2_profile_verification_description_${activeLanguageId}`"
                                                        >Profile verification
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section2_profile_verification_description'
                                                        )
                                                    "
                                                    :ref="`section2_profile_verification_description_${language.id}`"
                                                    :id="`section2_profile_verification_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section2_profile_verification_description`
                                                        ][
                                                            `section2_profile_verification_description_${language?.id}`
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
                                                        `section2_profile_verification_description.section2_profile_verification_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_profile_verification_description.section2_profile_verification_description_${activeLanguageId}`
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
                                                        :for="`section2_policies_label_${activeLanguageId}`"
                                                        >Policies label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section2_policies_label_${activeLanguageId}`"
                                                    :id="`section2_policies_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section2_policies_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section2_policies_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section2_policies_label.section2_policies_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_policies_label.section2_policies_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section2_policies_image_${activeLanguageId}`"
                                                            >Policies icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section2_policies_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section2_policies_image_${activeLanguageId}`"
                                                        :id="`section2_policies_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section2_policies_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section2_policies_image.section2_policies_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section2_policies_image.section2_policies_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section2_policies_image'] &&
                                                            form['section2_policies_image'][`section2_policies_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section2_policies_image'] &&
                                                            form['section2_policies_image'][`section2_policies_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section2_policies_image'][`section2_policies_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section2_policies_description_${activeLanguageId}`"
                                                        >Policies
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section2_policies_description'
                                                        )
                                                    "
                                                    :ref="`section2_policies_description_${language.id}`"
                                                    :id="`section2_policies_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section2_policies_description`
                                                        ][
                                                            `section2_policies_description_${language?.id}`
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
                                                        `section2_policies_description.section2_policies_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_policies_description.section2_policies_description_${activeLanguageId}`
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
                                                        :for="`section2_car_insurance_label_${activeLanguageId}`"
                                                        >Car insurance label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section2_car_insurance_label_${activeLanguageId}`"
                                                    :id="`section2_car_insurance_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section2_car_insurance_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section2_car_insurance_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section2_car_insurance_label.section2_car_insurance_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_car_insurance_label.section2_car_insurance_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section2_car_insurance_image_${activeLanguageId}`"
                                                            >Car insurance icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section2_car_insurance_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section2_car_insurance_image_${activeLanguageId}`"
                                                        :id="`section2_car_insurance_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section2_car_insurance_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section2_car_insurance_image.section2_car_insurance_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section2_car_insurance_image.section2_car_insurance_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section2_car_insurance_image'] &&
                                                            form['section2_car_insurance_image'][`section2_car_insurance_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section2_car_insurance_image'] &&
                                                            form['section2_car_insurance_image'][`section2_car_insurance_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section2_car_insurance_image'][`section2_car_insurance_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section2_car_insurance_description_${activeLanguageId}`"
                                                        >Car insurance
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section2_car_insurance_description'
                                                        )
                                                    "
                                                    :ref="`section2_car_insurance_description_${language.id}`"
                                                    :id="`section2_car_insurance_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section2_car_insurance_description`
                                                        ][
                                                            `section2_car_insurance_description_${language?.id}`
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
                                                        `section2_car_insurance_description.section2_car_insurance_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_car_insurance_description.section2_car_insurance_description_${activeLanguageId}`
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
                                                        :for="`section2_help_label_${activeLanguageId}`"
                                                        >Help label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section2_help_label_${activeLanguageId}`"
                                                    :id="`section2_help_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section2_help_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section2_help_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section2_help_label.section2_help_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_help_label.section2_help_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section2_help_image_${activeLanguageId}`"
                                                            >Help icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section2_help_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section2_help_image_${activeLanguageId}`"
                                                        :id="`section2_help_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section2_help_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section2_help_image.section2_help_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section2_help_image.section2_help_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section2_help_image'] &&
                                                            form['section2_help_image'][`section2_help_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section2_help_image'] &&
                                                            form['section2_help_image'][`section2_help_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section2_help_image'][`section2_help_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section2_help_description_${activeLanguageId}`"
                                                        >Help
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section2_help_description'
                                                        )
                                                    "
                                                    :ref="`section2_help_description_${language.id}`"
                                                    :id="`section2_help_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section2_help_description`
                                                        ][
                                                            `section2_help_description_${language?.id}`
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
                                                        `section2_help_description.section2_help_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section2_help_description.section2_help_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Safety first section end -->

                                <!-- Love ridesharing section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[3] =
                                                !collapseStates[3]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Love ridesharing section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[3]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section3_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section3_main_heading_${activeLanguageId}`"
                                                    :id="`section3_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section3_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section3_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section3_main_heading.section3_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_main_heading.section3_main_heading_${activeLanguageId}`
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
                                                        :for="`section3_safe_label_${activeLanguageId}`"
                                                        >Safe label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section3_safe_label_${activeLanguageId}`"
                                                    :id="`section3_safe_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section3_safe_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section3_safe_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section3_safe_label.section3_safe_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_safe_label.section3_safe_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section3_safe_image_${activeLanguageId}`"
                                                            >Safe icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section3_safe_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section3_safe_image_${activeLanguageId}`"
                                                        :id="`section3_safe_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section3_safe_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section3_safe_image.section3_safe_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section3_safe_image.section3_safe_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section3_safe_image'] &&
                                                            form['section3_safe_image'][`section3_safe_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section3_safe_image'] &&
                                                            form['section3_safe_image'][`section3_safe_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section3_safe_image'][`section3_safe_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section3_safe_description_${activeLanguageId}`"
                                                        >Safe
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section3_safe_description'
                                                        )
                                                    "
                                                    :ref="`section3_safe_description_${language.id}`"
                                                    :id="`section3_safe_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section3_safe_description`
                                                        ][
                                                            `section3_safe_description_${language?.id}`
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
                                                        `section3_safe_description.section3_safe_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_safe_description.section3_safe_description_${activeLanguageId}`
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
                                                        :for="`section3_affordable_label_${activeLanguageId}`"
                                                        >Affordable label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section3_affordable_label_${activeLanguageId}`"
                                                    :id="`section3_affordable_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section3_affordable_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section3_affordable_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section3_affordable_label.section3_affordable_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_affordable_label.section3_affordable_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section3_affordable_image_${activeLanguageId}`"
                                                            >Affordable icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section3_affordable_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section3_affordable_image_${activeLanguageId}`"
                                                        :id="`section3_affordable_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section3_affordable_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section3_affordable_image.section3_affordable_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section3_affordable_image.section3_affordable_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section3_affordable_image'] &&
                                                            form['section3_affordable_image'][`section3_affordable_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section3_affordable_image'] &&
                                                            form['section3_affordable_image'][`section3_affordable_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section3_affordable_image'][`section3_affordable_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section3_affordable_description_${activeLanguageId}`"
                                                        >Affordable
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section3_affordable_description'
                                                        )
                                                    "
                                                    :ref="`section3_affordable_description_${language.id}`"
                                                    :id="`section3_affordable_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section3_affordable_description`
                                                        ][
                                                            `section3_affordable_description_${language?.id}`
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
                                                        `section3_affordable_description.section3_affordable_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_affordable_description.section3_affordable_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1 ">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section3_reliable_label_${activeLanguageId}`"
                                                        >Reliable label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section3_reliable_label_${activeLanguageId}`"
                                                    :id="`section3_reliable_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section3_reliable_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section3_reliable_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section3_reliable_label.section3_reliable_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_reliable_label.section3_reliable_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`section3_reliable_image_${activeLanguageId}`"
                                                            >Reliable icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`section3_reliable_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`section3_reliable_image_${activeLanguageId}`"
                                                        :id="`section3_reliable_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'section3_reliable_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `section3_reliable_image.section3_reliable_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `section3_reliable_image.section3_reliable_image_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['section3_reliable_image'] &&
                                                            form['section3_reliable_image'][`section3_reliable_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['section3_reliable_image'] &&
                                                            form['section3_reliable_image'][`section3_reliable_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['section3_reliable_image'][`section3_reliable_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section3_reliable_description_${activeLanguageId}`"
                                                        >Reliable
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'section3_reliable_description'
                                                        )
                                                    "
                                                    :ref="`section3_reliable_description_${language.id}`"
                                                    :id="`section3_reliable_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `section3_reliable_description`
                                                        ][
                                                            `section3_reliable_description_${language?.id}`
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
                                                        `section3_reliable_description.section3_reliable_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section3_reliable_description.section3_reliable_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Love ridesharing section end -->

                                <!-- Rides section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[4] =
                                                !collapseStates[4]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Recent rides
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[4]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`section4_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`section4_main_heading_${activeLanguageId}`"
                                                    :id="`section4_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'section4_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'section4_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `section4_main_heading.section4_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `section4_main_heading.section4_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Rides section end -->

                                <!-- Working section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[5] =
                                                !collapseStates[5]
                                        "
                                    >
                                        <h3 class="text-white">
                                            How ridesharing works section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[5]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_main_heading_${activeLanguageId}`"
                                                    :id="`work_section_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_main_heading.work_section_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_main_heading.work_section_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_main_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_main_text_${activeLanguageId}`"
                                                    :id="`work_section_main_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_main_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_main_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_main_text.work_section_main_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_main_text.work_section_main_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_label_${activeLanguageId}`"
                                                        >Passenger label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_label_${activeLanguageId}`"
                                                    :id="`work_section_passenger_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_label.work_section_passenger_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_label.work_section_passenger_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_description_${activeLanguageId}`"
                                                        >Passenger description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_description_${activeLanguageId}`"
                                                    :id="`work_section_passenger_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_description.work_section_passenger_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_description.work_section_passenger_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point1_label_${activeLanguageId}`"
                                                        >Passenger point1 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point1_label_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point1_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point1_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point1_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point1_label.work_section_passenger_point1_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point1_label.work_section_passenger_point1_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_passenger_point1_image_${activeLanguageId}`"
                                                            >Passenger point1 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_passenger_point1_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_passenger_point1_image_${activeLanguageId}`"
                                                        :id="`work_section_passenger_point1_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_passenger_point1_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_passenger_point1_image.work_section_passenger_point1_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_passenger_point1_image.work_section_passenger_point1_image_${activeLanguageId}`
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
                                                            form['work_section_passenger_point1_image'] &&
                                                            form['work_section_passenger_point1_image'][`work_section_passenger_point1_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_passenger_point1_image'] &&
                                                            form['work_section_passenger_point1_image'][`work_section_passenger_point1_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_passenger_point1_image'][`work_section_passenger_point1_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point1_description_${activeLanguageId}`"
                                                        >Passenger point1 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point1_description_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point1_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point1_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point1_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point1_description.work_section_passenger_point1_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point1_description.work_section_passenger_point1_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point2_label_${activeLanguageId}`"
                                                        >Passenger point2 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point2_label_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point2_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point2_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point2_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point2_label.work_section_passenger_point2_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point2_label.work_section_passenger_point2_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_passenger_point2_image_${activeLanguageId}`"
                                                            >Passenger point2 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_passenger_point2_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_passenger_point2_image_${activeLanguageId}`"
                                                        :id="`work_section_passenger_point2_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_passenger_point2_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_passenger_point2_image.work_section_passenger_point2_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_passenger_point2_image.work_section_passenger_point2_image_${activeLanguageId}`
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
                                                            form['work_section_passenger_point2_image'] &&
                                                            form['work_section_passenger_point2_image'][`work_section_passenger_point2_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_passenger_point2_image'] &&
                                                            form['work_section_passenger_point2_image'][`work_section_passenger_point2_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_passenger_point2_image'][`work_section_passenger_point2_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point2_description_${activeLanguageId}`"
                                                        >Passenger point2 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point2_description_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point2_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point2_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point2_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point2_description.work_section_passenger_point2_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point2_description.work_section_passenger_point2_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point3_label_${activeLanguageId}`"
                                                        >Passenger point3 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point3_label_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point3_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point3_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point3_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point3_label.work_section_passenger_point3_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point3_label.work_section_passenger_point3_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_passenger_point3_image_${activeLanguageId}`"
                                                            >Passenger point3 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_passenger_point3_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_passenger_point3_image_${activeLanguageId}`"
                                                        :id="`work_section_passenger_point3_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_passenger_point3_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_passenger_point3_image.work_section_passenger_point3_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_passenger_point3_image.work_section_passenger_point3_image_${activeLanguageId}`
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
                                                            form['work_section_passenger_point3_image'] &&
                                                            form['work_section_passenger_point3_image'][`work_section_passenger_point3_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_passenger_point3_image'] &&
                                                            form['work_section_passenger_point3_image'][`work_section_passenger_point3_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_passenger_point3_image'][`work_section_passenger_point3_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point3_description_${activeLanguageId}`"
                                                        >Passenger point3 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point3_description_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point3_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point3_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point3_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point3_description.work_section_passenger_point3_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point3_description.work_section_passenger_point3_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point4_label_${activeLanguageId}`"
                                                        >Passenger point4 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point4_label_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point4_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point4_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point4_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point4_label.work_section_passenger_point4_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point4_label.work_section_passenger_point4_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_passenger_point4_image_${activeLanguageId}`"
                                                            >Passenger point4 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_passenger_point4_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_passenger_point4_image_${activeLanguageId}`"
                                                        :id="`work_section_passenger_point4_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_passenger_point4_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_passenger_point4_image.work_section_passenger_point4_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_passenger_point4_image.work_section_passenger_point4_image_${activeLanguageId}`
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
                                                            form['work_section_passenger_point4_image'] &&
                                                            form['work_section_passenger_point4_image'][`work_section_passenger_point4_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_passenger_point4_image'] &&
                                                            form['work_section_passenger_point4_image'][`work_section_passenger_point4_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_passenger_point4_image'][`work_section_passenger_point4_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point4_description_${activeLanguageId}`"
                                                        >Passenger point4 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point4_description_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point4_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point4_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point4_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point4_description.work_section_passenger_point4_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point4_description.work_section_passenger_point4_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point5_label_${activeLanguageId}`"
                                                        >Passenger point5 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point5_label_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point5_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point5_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point5_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point5_label.work_section_passenger_point5_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point5_label.work_section_passenger_point5_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_passenger_point5_image_${activeLanguageId}`"
                                                            >Passenger point5 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_passenger_point5_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_passenger_point5_image_${activeLanguageId}`"
                                                        :id="`work_section_passenger_point5_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_passenger_point5_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_passenger_point5_image.work_section_passenger_point5_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_passenger_point5_image.work_section_passenger_point5_image_${activeLanguageId}`
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
                                                            form['work_section_passenger_point5_image'] &&
                                                            form['work_section_passenger_point5_image'][`work_section_passenger_point5_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_passenger_point5_image'] &&
                                                            form['work_section_passenger_point5_image'][`work_section_passenger_point5_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_passenger_point5_image'][`work_section_passenger_point5_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_passenger_point5_description_${activeLanguageId}`"
                                                        >Passenger point5 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_passenger_point5_description_${activeLanguageId}`"
                                                    :id="`work_section_passenger_point5_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_passenger_point5_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_passenger_point5_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_passenger_point5_description.work_section_passenger_point5_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_passenger_point5_description.work_section_passenger_point5_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_label_${activeLanguageId}`"
                                                        >Driver label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_label_${activeLanguageId}`"
                                                    :id="`work_section_driver_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_label.work_section_driver_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_label.work_section_driver_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_description_${activeLanguageId}`"
                                                        >Driver description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_description_${activeLanguageId}`"
                                                    :id="`work_section_driver_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_description.work_section_driver_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_description.work_section_driver_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point1_label_${activeLanguageId}`"
                                                        >Driver point1 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point1_label_${activeLanguageId}`"
                                                    :id="`work_section_driver_point1_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point1_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point1_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point1_label.work_section_driver_point1_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point1_label.work_section_driver_point1_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_driver_point1_image_${activeLanguageId}`"
                                                            >Driver point1 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_driver_point1_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_driver_point1_image_${activeLanguageId}`"
                                                        :id="`work_section_driver_point1_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_driver_point1_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_driver_point1_image.work_section_driver_point1_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_driver_point1_image.work_section_driver_point1_image_${activeLanguageId}`
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
                                                            form['work_section_driver_point1_image'] &&
                                                            form['work_section_driver_point1_image'][`work_section_driver_point1_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_driver_point1_image'] &&
                                                            form['work_section_driver_point1_image'][`work_section_driver_point1_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_driver_point1_image'][`work_section_driver_point1_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point1_description_${activeLanguageId}`"
                                                        >Driver point1 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point1_description_${activeLanguageId}`"
                                                    :id="`work_section_driver_point1_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point1_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point1_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point1_description.work_section_driver_point1_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point1_description.work_section_driver_point1_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point2_label_${activeLanguageId}`"
                                                        >Driver point2 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point2_label_${activeLanguageId}`"
                                                    :id="`work_section_driver_point2_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point2_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point2_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point2_label.work_section_driver_point2_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point2_label.work_section_driver_point2_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_driver_point2_image_${activeLanguageId}`"
                                                            >Driver point2 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_driver_point2_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_driver_point2_image_${activeLanguageId}`"
                                                        :id="`work_section_driver_point2_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_driver_point2_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_driver_point2_image.work_section_driver_point2_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_driver_point2_image.work_section_driver_point2_image_${activeLanguageId}`
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
                                                            form['work_section_driver_point2_image'] &&
                                                            form['work_section_driver_point2_image'][`work_section_driver_point2_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_driver_point2_image'] &&
                                                            form['work_section_driver_point2_image'][`work_section_driver_point2_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_driver_point2_image'][`work_section_driver_point2_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point2_description_${activeLanguageId}`"
                                                        >Driver point2 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point2_description_${activeLanguageId}`"
                                                    :id="`work_section_driver_point2_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point2_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point2_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point2_description.work_section_driver_point2_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point2_description.work_section_driver_point2_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point3_label_${activeLanguageId}`"
                                                        >Driver point3 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point3_label_${activeLanguageId}`"
                                                    :id="`work_section_driver_point3_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point3_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point3_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point3_label.work_section_driver_point3_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point3_label.work_section_driver_point3_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_driver_point3_image_${activeLanguageId}`"
                                                            >Driver point3 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_driver_point3_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_driver_point3_image_${activeLanguageId}`"
                                                        :id="`work_section_driver_point3_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_driver_point3_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_driver_point3_image.work_section_driver_point3_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_driver_point3_image.work_section_driver_point3_image_${activeLanguageId}`
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
                                                            form['work_section_driver_point3_image'] &&
                                                            form['work_section_driver_point3_image'][`work_section_driver_point3_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_driver_point3_image'] &&
                                                            form['work_section_driver_point3_image'][`work_section_driver_point3_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_driver_point3_image'][`work_section_driver_point3_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point3_description_${activeLanguageId}`"
                                                        >Driver point3 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point3_description_${activeLanguageId}`"
                                                    :id="`work_section_driver_point3_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point3_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point3_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point3_description.work_section_driver_point3_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point3_description.work_section_driver_point3_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point4_label_${activeLanguageId}`"
                                                        >Driver point4 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point4_label_${activeLanguageId}`"
                                                    :id="`work_section_driver_point4_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point4_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point4_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point4_label.work_section_driver_point4_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point4_label.work_section_driver_point4_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_driver_point4_image_${activeLanguageId}`"
                                                            >Driver point4 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_driver_point4_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_driver_point4_image_${activeLanguageId}`"
                                                        :id="`work_section_driver_point4_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_driver_point4_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_driver_point4_image.work_section_driver_point4_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_driver_point4_image.work_section_driver_point4_image_${activeLanguageId}`
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
                                                            form['work_section_driver_point4_image'] &&
                                                            form['work_section_driver_point4_image'][`work_section_driver_point4_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_driver_point4_image'] &&
                                                            form['work_section_driver_point4_image'][`work_section_driver_point4_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_driver_point4_image'][`work_section_driver_point4_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point4_description_${activeLanguageId}`"
                                                        >Driver point4 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point4_description_${activeLanguageId}`"
                                                    :id="`work_section_driver_point4_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point4_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point4_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point4_description.work_section_driver_point4_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point4_description.work_section_driver_point4_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point5_label_${activeLanguageId}`"
                                                        >Driver point5 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point5_label_${activeLanguageId}`"
                                                    :id="`work_section_driver_point5_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point5_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point5_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point5_label.work_section_driver_point5_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point5_label.work_section_driver_point5_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`work_section_driver_point5_image_${activeLanguageId}`"
                                                            >Driver point5 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`work_section_driver_point5_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`work_section_driver_point5_image_${activeLanguageId}`"
                                                        :id="`work_section_driver_point5_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'work_section_driver_point5_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `work_section_driver_point5_image.work_section_driver_point5_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `work_section_driver_point5_image.work_section_driver_point5_image_${activeLanguageId}`
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
                                                            form['work_section_driver_point5_image'] &&
                                                            form['work_section_driver_point5_image'][`work_section_driver_point5_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['work_section_driver_point5_image'] &&
                                                            form['work_section_driver_point5_image'][`work_section_driver_point5_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['work_section_driver_point5_image'][`work_section_driver_point5_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`work_section_driver_point5_description_${activeLanguageId}`"
                                                        >Driver point5 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`work_section_driver_point5_description_${activeLanguageId}`"
                                                    :id="`work_section_driver_point5_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'work_section_driver_point5_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'work_section_driver_point5_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `work_section_driver_point5_description.work_section_driver_point5_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `work_section_driver_point5_description.work_section_driver_point5_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Working section end -->

                                <!-- Doing section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[6] =
                                                !collapseStates[6]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Where are you going section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[6]"
                                    >
                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`doing_section_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`doing_section_main_heading_${activeLanguageId}`"
                                                    :id="`doing_section_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'doing_section_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'doing_section_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `doing_section_main_heading.doing_section_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `doing_section_main_heading.doing_section_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`doing_section_main_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`doing_section_main_text_${activeLanguageId}`"
                                                    :id="`doing_section_main_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'doing_section_main_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'doing_section_main_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `doing_section_main_text.doing_section_main_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `doing_section_main_text.doing_section_main_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`doing_section_slider_image_${activeLanguageId}`"
                                                            >Slider image</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`doing_section_slider_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`doing_section_slider_image_${activeLanguageId}`"
                                                        :id="`doing_section_slider_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'doing_section_slider_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `doing_section_slider_image.doing_section_slider_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `doing_section_slider_image.doing_section_slider_image_${activeLanguageId}`
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
                                                            form['doing_section_slider_image'] &&
                                                            form['doing_section_slider_image'][`doing_section_slider_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['doing_section_slider_image'] &&
                                                            form['doing_section_slider_image'][`doing_section_slider_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['doing_section_slider_image'][`doing_section_slider_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`doing_section_label1_${activeLanguageId}`"
                                                        >Button label1</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`doing_section_label1_${activeLanguageId}`"
                                                    :id="`doing_section_label1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'doing_section_label1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'doing_section_label1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `doing_section_label1.doing_section_label1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `doing_section_label1.doing_section_label1_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`doing_section_label2_${activeLanguageId}`"
                                                        >Button label2</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`doing_section_label2_${activeLanguageId}`"
                                                    :id="`doing_section_label2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'doing_section_label2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'doing_section_label2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `doing_section_label2.doing_section_label2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `doing_section_label2.doing_section_label2_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Doing section end -->

                                <!-- Reasons section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[7] =
                                                !collapseStates[7]
                                        "
                                    >
                                        <h3 class="text-white">
                                            More reasons to travel with us
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[7]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_main_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_main_heading_${activeLanguageId}`"
                                                    :id="`reasons_section_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_main_heading.reasons_section_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_main_heading.reasons_section_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_main_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_main_text_${activeLanguageId}`"
                                                    :id="`reasons_section_main_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_main_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_main_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_main_text.reasons_section_main_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_main_text.reasons_section_main_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_members_label_${activeLanguageId}`"
                                                        >Members label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_members_label_${activeLanguageId}`"
                                                    :id="`reasons_section_members_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_members_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_members_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_members_label.reasons_section_members_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_members_label.reasons_section_members_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_members_image_${activeLanguageId}`"
                                                            >Members icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_members_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_members_image_${activeLanguageId}`"
                                                        :id="`reasons_section_members_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_members_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_members_image.reasons_section_members_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_members_image.reasons_section_members_image_${activeLanguageId}`
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
                                                            form['reasons_section_members_image'] &&
                                                            form['reasons_section_members_image'][`reasons_section_members_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_members_image'] &&
                                                            form['reasons_section_members_image'][`reasons_section_members_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_members_image'][`reasons_section_members_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_members_description_${activeLanguageId}`"
                                                        >Members description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_members_description_${activeLanguageId}`"
                                                    :id="`reasons_section_members_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_members_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_members_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_members_description.reasons_section_members_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_members_description.reasons_section_members_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_driver_label_${activeLanguageId}`"
                                                        >Driver label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_driver_label_${activeLanguageId}`"
                                                    :id="`reasons_section_driver_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_driver_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_driver_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_driver_label.reasons_section_driver_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_driver_label.reasons_section_driver_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_driver_image_${activeLanguageId}`"
                                                            >Driver icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_driver_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_driver_image_${activeLanguageId}`"
                                                        :id="`reasons_section_driver_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_driver_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_driver_image.reasons_section_driver_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_driver_image.reasons_section_driver_image_${activeLanguageId}`
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
                                                            form['reasons_section_driver_image'] &&
                                                            form['reasons_section_driver_image'][`reasons_section_driver_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_driver_image'] &&
                                                            form['reasons_section_driver_image'][`reasons_section_driver_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_driver_image'][`reasons_section_driver_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_driver_description_${activeLanguageId}`"
                                                        >Driver description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_driver_description_${activeLanguageId}`"
                                                    :id="`reasons_section_driver_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_driver_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_driver_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_driver_description.reasons_section_driver_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_driver_description.reasons_section_driver_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_quality_label_${activeLanguageId}`"
                                                        >Quality label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_quality_label_${activeLanguageId}`"
                                                    :id="`reasons_section_quality_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_quality_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_quality_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_quality_label.reasons_section_quality_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_quality_label.reasons_section_quality_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_quality_image_${activeLanguageId}`"
                                                            >Quality icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_quality_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_quality_image_${activeLanguageId}`"
                                                        :id="`reasons_section_quality_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_quality_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_quality_image.reasons_section_quality_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_quality_image.reasons_section_quality_image_${activeLanguageId}`
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
                                                            form['reasons_section_quality_image'] &&
                                                            form['reasons_section_quality_image'][`reasons_section_quality_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_quality_image'] &&
                                                            form['reasons_section_quality_image'][`reasons_section_quality_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_quality_image'][`reasons_section_quality_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_quality_description_${activeLanguageId}`"
                                                        >Quality description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_quality_description_${activeLanguageId}`"
                                                    :id="`reasons_section_quality_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_quality_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_quality_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_quality_description.reasons_section_quality_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_quality_description.reasons_section_quality_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_policy_label_${activeLanguageId}`"
                                                        >Policy label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_policy_label_${activeLanguageId}`"
                                                    :id="`reasons_section_policy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_policy_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_policy_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_policy_label.reasons_section_policy_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_policy_label.reasons_section_policy_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_policy_image_${activeLanguageId}`"
                                                            >Policy icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_policy_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_policy_image_${activeLanguageId}`"
                                                        :id="`reasons_section_policy_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_policy_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_policy_image.reasons_section_policy_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_policy_image.reasons_section_policy_image_${activeLanguageId}`
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
                                                            form['reasons_section_policy_image'] &&
                                                            form['reasons_section_policy_image'][`reasons_section_policy_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_policy_image'] &&
                                                            form['reasons_section_policy_image'][`reasons_section_policy_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_policy_image'][`reasons_section_policy_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_policy_description_${activeLanguageId}`"
                                                        >Policy description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_policy_description_${activeLanguageId}`"
                                                    :id="`reasons_section_policy_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_policy_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_policy_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_policy_description.reasons_section_policy_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_policy_description.reasons_section_policy_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_students_label_${activeLanguageId}`"
                                                        >Students label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_students_label_${activeLanguageId}`"
                                                    :id="`reasons_section_students_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_students_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_students_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_students_label.reasons_section_students_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_students_label.reasons_section_students_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_students_image_${activeLanguageId}`"
                                                            >Students icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_students_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_students_image_${activeLanguageId}`"
                                                        :id="`reasons_section_students_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_students_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_students_image.reasons_section_students_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_students_image.reasons_section_students_image_${activeLanguageId}`
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
                                                            form['reasons_section_students_image'] &&
                                                            form['reasons_section_students_image'][`reasons_section_students_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_students_image'] &&
                                                            form['reasons_section_students_image'][`reasons_section_students_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_students_image'][`reasons_section_students_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_students_description_${activeLanguageId}`"
                                                        >Students description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_students_description_${activeLanguageId}`"
                                                    :id="`reasons_section_students_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_students_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_students_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_students_description.reasons_section_students_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_students_description.reasons_section_students_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_safety_label_${activeLanguageId}`"
                                                        >Safety label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_safety_label_${activeLanguageId}`"
                                                    :id="`reasons_section_safety_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_safety_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_safety_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_safety_label.reasons_section_safety_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_safety_label.reasons_section_safety_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_safety_image_${activeLanguageId}`"
                                                            >Safety icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_safety_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_safety_image_${activeLanguageId}`"
                                                        :id="`reasons_section_safety_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_safety_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_safety_image.reasons_section_safety_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_safety_image.reasons_section_safety_image_${activeLanguageId}`
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
                                                            form['reasons_section_safety_image'] &&
                                                            form['reasons_section_safety_image'][`reasons_section_safety_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_safety_image'] &&
                                                            form['reasons_section_safety_image'][`reasons_section_safety_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_safety_image'][`reasons_section_safety_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_safety_description_${activeLanguageId}`"
                                                        >Safety description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_safety_description_${activeLanguageId}`"
                                                    :id="`reasons_section_safety_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_safety_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_safety_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_safety_description.reasons_section_safety_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_safety_description.reasons_section_safety_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_price_label_${activeLanguageId}`"
                                                        >Price label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_price_label_${activeLanguageId}`"
                                                    :id="`reasons_section_price_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_price_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_price_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_price_label.reasons_section_price_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_price_label.reasons_section_price_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_price_image_${activeLanguageId}`"
                                                            >Price icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_price_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_price_image_${activeLanguageId}`"
                                                        :id="`reasons_section_price_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_price_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_price_image.reasons_section_price_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_price_image.reasons_section_price_image_${activeLanguageId}`
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
                                                            form['reasons_section_price_image'] &&
                                                            form['reasons_section_price_image'][`reasons_section_price_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_price_image'] &&
                                                            form['reasons_section_price_image'][`reasons_section_price_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_price_image'][`reasons_section_price_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_price_description_${activeLanguageId}`"
                                                        >Price description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_price_description_${activeLanguageId}`"
                                                    :id="`reasons_section_price_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_price_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_price_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_price_description.reasons_section_price_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_price_description.reasons_section_price_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_use_label_${activeLanguageId}`"
                                                        >Use label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_use_label_${activeLanguageId}`"
                                                    :id="`reasons_section_use_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_use_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_use_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_use_label.reasons_section_use_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_use_label.reasons_section_use_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_use_image_${activeLanguageId}`"
                                                            >Use icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_use_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_use_image_${activeLanguageId}`"
                                                        :id="`reasons_section_use_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_use_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_use_image.reasons_section_use_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_use_image.reasons_section_use_image_${activeLanguageId}`
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
                                                            form['reasons_section_use_image'] &&
                                                            form['reasons_section_use_image'][`reasons_section_use_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_use_image'] &&
                                                            form['reasons_section_use_image'][`reasons_section_use_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_use_image'][`reasons_section_use_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_use_description_${activeLanguageId}`"
                                                        >Use description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_use_description_${activeLanguageId}`"
                                                    :id="`reasons_section_use_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_use_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_use_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_use_description.reasons_section_use_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_use_description.reasons_section_use_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_reliable_label_${activeLanguageId}`"
                                                        >Reliable label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_reliable_label_${activeLanguageId}`"
                                                    :id="`reasons_section_reliable_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_reliable_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_reliable_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_reliable_label.reasons_section_reliable_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_reliable_label.reasons_section_reliable_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_reliable_image_${activeLanguageId}`"
                                                            >Reliable icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_reliable_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_reliable_image_${activeLanguageId}`"
                                                        :id="`reasons_section_reliable_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_reliable_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_reliable_image.reasons_section_reliable_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_reliable_image.reasons_section_reliable_image_${activeLanguageId}`
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
                                                            form['reasons_section_reliable_image'] &&
                                                            form['reasons_section_reliable_image'][`reasons_section_reliable_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_reliable_image'] &&
                                                            form['reasons_section_reliable_image'][`reasons_section_reliable_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_reliable_image'][`reasons_section_reliable_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_reliable_description_${activeLanguageId}`"
                                                        >Reliable description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_reliable_description_${activeLanguageId}`"
                                                    :id="`reasons_section_reliable_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_reliable_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_reliable_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_reliable_description.reasons_section_reliable_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_reliable_description.reasons_section_reliable_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_responsible_label_${activeLanguageId}`"
                                                        >Responsible label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_responsible_label_${activeLanguageId}`"
                                                    :id="`reasons_section_responsible_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_responsible_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_responsible_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_responsible_label.reasons_section_responsible_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_responsible_label.reasons_section_responsible_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`reasons_section_responsible_image_${activeLanguageId}`"
                                                            >Responsible icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`reasons_section_responsible_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`reasons_section_responsible_image_${activeLanguageId}`"
                                                        :id="`reasons_section_responsible_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'reasons_section_responsible_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `reasons_section_responsible_image.reasons_section_responsible_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `reasons_section_responsible_image.reasons_section_responsible_image_${activeLanguageId}`
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
                                                            form['reasons_section_responsible_image'] &&
                                                            form['reasons_section_responsible_image'][`reasons_section_responsible_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['reasons_section_responsible_image'] &&
                                                            form['reasons_section_responsible_image'][`reasons_section_responsible_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['reasons_section_responsible_image'][`reasons_section_responsible_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reasons_section_responsible_description_${activeLanguageId}`"
                                                        >Responsible description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reasons_section_responsible_description_${activeLanguageId}`"
                                                    :id="`reasons_section_responsible_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reasons_section_responsible_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reasons_section_responsible_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reasons_section_responsible_description.reasons_section_responsible_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reasons_section_responsible_description.reasons_section_responsible_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Reasons section end -->

                                <!-- Movement section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[8] =
                                                !collapseStates[8]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Movement section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[8]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`movement_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`movement_section_heading_${activeLanguageId}`"
                                                    :id="`movement_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'movement_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'movement_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `movement_section_heading.movement_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `movement_section_heading.movement_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`movement_section_icon_${activeLanguageId}`"
                                                            >Movement icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`movement_section_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`movement_section_icon_${activeLanguageId}`"
                                                        :id="`movement_section_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'movement_section_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `movement_section_icon.movement_section_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `movement_section_icon.movement_section_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                    ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <img
                                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                                        v-if="
                                                            form['movement_section_icon'] &&
                                                            form['movement_section_icon'][`movement_section_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['movement_section_icon'] &&
                                                            form['movement_section_icon'][`movement_section_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['movement_section_icon'][`movement_section_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`movement_section_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'movement_section_text'
                                                        )
                                                    "
                                                    :ref="`movement_section_text_${language.id}`"
                                                    :id="`movement_section_text_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `movement_section_text`
                                                        ][
                                                            `movement_section_text_${language?.id}`
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
                                                        `movement_section_text.movement_section_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `movement_section_text.movement_section_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Movement section end -->

                                <!-- Members section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[9] =
                                                !collapseStates[9]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Satisfied members
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[9]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`members_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`members_section_heading_${activeLanguageId}`"
                                                    :id="`members_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'members_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'members_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `members_section_heading.members_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `members_section_heading.members_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`members_section_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`members_section_text_${activeLanguageId}`"
                                                    :id="`members_section_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'members_section_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'members_section_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `members_section_text.members_section_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `members_section_text.members_section_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Members section end -->

                                <!-- News section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[10] =
                                                !collapseStates[10]
                                        "
                                    >
                                        <h3 class="text-white">
                                            News section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[10]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`news_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`news_section_heading_${activeLanguageId}`"
                                                    :id="`news_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'news_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'news_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `news_section_heading.news_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `news_section_heading.news_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`news_section_icon1_${activeLanguageId}`"
                                                            >New icon1</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`news_section_icon1_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`news_section_icon1_${activeLanguageId}`"
                                                        :id="`news_section_icon1_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'news_section_icon1',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `news_section_icon1.news_section_icon1_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `news_section_icon1.news_section_icon1_${activeLanguageId}`
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
                                                            form['news_section_icon1'] &&
                                                            form['news_section_icon1'][`news_section_icon1_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['news_section_icon1'] &&
                                                            form['news_section_icon1'][`news_section_icon1_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['news_section_icon1'][`news_section_icon1_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`news_section_icon2_${activeLanguageId}`"
                                                            >New icon2</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`news_section_icon2_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`news_section_icon2_${activeLanguageId}`"
                                                        :id="`news_section_icon2_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'news_section_icon2',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `news_section_icon2.news_section_icon2_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `news_section_icon2.news_section_icon2_${activeLanguageId}`
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
                                                            form['news_section_icon2'] &&
                                                            form['news_section_icon2'][`news_section_icon2_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['news_section_icon2'] &&
                                                            form['news_section_icon2'][`news_section_icon2_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['news_section_icon2'][`news_section_icon2_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`news_section_icon3_${activeLanguageId}`"
                                                            >New icon3</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`news_section_icon3_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`news_section_icon3_${activeLanguageId}`"
                                                        :id="`news_section_icon3_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'news_section_icon3',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `news_section_icon3.news_section_icon3_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `news_section_icon3.news_section_icon3_${activeLanguageId}`
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
                                                            form['news_section_icon3'] &&
                                                            form['news_section_icon3'][`news_section_icon3_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['news_section_icon3'] &&
                                                            form['news_section_icon3'][`news_section_icon3_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['news_section_icon3'][`news_section_icon3_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`news_section_icon4_${activeLanguageId}`"
                                                            >New icon4</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`news_section_icon4_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`news_section_icon4_${activeLanguageId}`"
                                                        :id="`news_section_icon4_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'news_section_icon4',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `news_section_icon4.news_section_icon4_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `news_section_icon4.news_section_icon4_${activeLanguageId}`
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
                                                            form['news_section_icon3'] &&
                                                            form['news_section_icon3'][`news_section_icon3_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['news_section_icon3'] &&
                                                            form['news_section_icon3'][`news_section_icon3_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['news_section_icon3'][`news_section_icon3_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- News section end -->

                                <!-- Use section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[11] =
                                                !collapseStates[11]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Easy to use section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[11]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_heading_${activeLanguageId}`"
                                                    :id="`use_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_heading.use_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_heading.use_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_text_${activeLanguageId}`"
                                                    :id="`use_section_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_text.use_section_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_text.use_section_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point1_label_${activeLanguageId}`"
                                                        >Point1 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point1_label_${activeLanguageId}`"
                                                    :id="`use_section_point1_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point1_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point1_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point1_label.use_section_point1_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point1_label.use_section_point1_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`use_section_point1_image_${activeLanguageId}`"
                                                            >Point1 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`use_section_point1_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`use_section_point1_image_${activeLanguageId}`"
                                                        :id="`use_section_point1_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'use_section_point1_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `use_section_point1_image.use_section_point1_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `use_section_point1_image.use_section_point1_image_${activeLanguageId}`
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
                                                            form['use_section_point1_image'] &&
                                                            form['use_section_point1_image'][`use_section_point1_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['use_section_point1_image'] &&
                                                            form['use_section_point1_image'][`use_section_point1_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['use_section_point1_image'][`use_section_point1_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point1_description_${activeLanguageId}`"
                                                        >Point1 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point1_description_${activeLanguageId}`"
                                                    :id="`use_section_point1_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point1_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point1_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point1_description.use_section_point1_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point1_description.use_section_point1_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point2_label_${activeLanguageId}`"
                                                        >Point2 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point2_label_${activeLanguageId}`"
                                                    :id="`use_section_point2_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point2_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point2_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point2_label.use_section_point2_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point2_label.use_section_point2_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`use_section_point2_image_${activeLanguageId}`"
                                                            >Point2 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`use_section_point2_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`use_section_point2_image_${activeLanguageId}`"
                                                        :id="`use_section_point2_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'use_section_point2_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `use_section_point2_image.use_section_point2_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `use_section_point2_image.use_section_point2_image_${activeLanguageId}`
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
                                                            form['use_section_point2_image'] &&
                                                            form['use_section_point2_image'][`use_section_point2_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['use_section_point2_image'] &&
                                                            form['use_section_point2_image'][`use_section_point2_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['use_section_point2_image'][`use_section_point2_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point2_description_${activeLanguageId}`"
                                                        >Point2 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point2_description_${activeLanguageId}`"
                                                    :id="`use_section_point2_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point2_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point2_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point2_description.use_section_point2_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point2_description.use_section_point2_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point3_label_${activeLanguageId}`"
                                                        >Point3 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point3_label_${activeLanguageId}`"
                                                    :id="`use_section_point3_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point3_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point3_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point3_label.use_section_point3_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point3_label.use_section_point3_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`use_section_point3_image_${activeLanguageId}`"
                                                            >Point3 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`use_section_point3_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`use_section_point3_image_${activeLanguageId}`"
                                                        :id="`use_section_point3_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'use_section_point3_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `use_section_point3_image.use_section_point3_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `use_section_point3_image.use_section_point3_image_${activeLanguageId}`
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
                                                            form['use_section_point3_image'] &&
                                                            form['use_section_point3_image'][`use_section_point3_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['use_section_point3_image'] &&
                                                            form['use_section_point3_image'][`use_section_point3_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['use_section_point3_image'][`use_section_point3_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point3_description_${activeLanguageId}`"
                                                        >Point3 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point3_description_${activeLanguageId}`"
                                                    :id="`use_section_point3_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point3_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point3_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point3_description.use_section_point3_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point3_description.use_section_point3_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point4_label_${activeLanguageId}`"
                                                        >Point4 label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point4_label_${activeLanguageId}`"
                                                    :id="`use_section_point4_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point4_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point4_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point4_label.use_section_point4_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point4_label.use_section_point4_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`use_section_point4_image_${activeLanguageId}`"
                                                            >Point4 icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`use_section_point4_image_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`use_section_point4_image_${activeLanguageId}`"
                                                        :id="`use_section_point4_image_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'use_section_point4_image',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `use_section_point4_image.use_section_point4_image_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `use_section_point4_image.use_section_point4_image_${activeLanguageId}`
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
                                                            form['use_section_point4_image'] &&
                                                            form['use_section_point4_image'][`use_section_point4_image_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['use_section_point4_image'] &&
                                                            form['use_section_point4_image'][`use_section_point4_image_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['use_section_point4_image'][`use_section_point4_image_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`use_section_point4_description_${activeLanguageId}`"
                                                        >Point4 description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`use_section_point4_description_${activeLanguageId}`"
                                                    :id="`use_section_point4_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'use_section_point4_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'use_section_point4_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `use_section_point4_description.use_section_point4_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `use_section_point4_description.use_section_point4_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Use section end -->

                                <!-- Reliability section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[12] =
                                                !collapseStates[12]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Reliability section
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[12]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2 md:col-span-1">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reliability_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reliability_section_heading_${activeLanguageId}`"
                                                    :id="`reliability_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reliability_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reliability_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reliability_section_heading.reliability_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_heading.reliability_section_heading_${activeLanguageId}`
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
                                                        :for="`reliability_section_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reliability_section_text_${activeLanguageId}`"
                                                    :id="`reliability_section_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reliability_section_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reliability_section_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reliability_section_text.reliability_section_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_text.reliability_section_text_${activeLanguageId}`
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
                                                        :for="`reliability_section_passengers_label_${activeLanguageId}`"
                                                        >Passenger label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reliability_section_passengers_label_${activeLanguageId}`"
                                                    :id="`reliability_section_passengers_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reliability_section_passengers_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reliability_section_passengers_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reliability_section_passengers_label.reliability_section_passengers_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_passengers_label.reliability_section_passengers_label_${activeLanguageId}`
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
                                                        :for="`reliability_section_passengers_description_${activeLanguageId}`"
                                                        >Passenger
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'reliability_section_passengers_description'
                                                        )
                                                    "
                                                    :ref="`reliability_section_passengers_description_${language.id}`"
                                                    :id="`reliability_section_passengers_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `reliability_section_passengers_description`
                                                        ][
                                                            `reliability_section_passengers_description_${language?.id}`
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
                                                        `reliability_section_passengers_description.reliability_section_passengers_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_passengers_description.reliability_section_passengers_description_${activeLanguageId}`
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
                                                        :for="`reliability_section_drivers_label_${activeLanguageId}`"
                                                        >Driver label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reliability_section_drivers_label_${activeLanguageId}`"
                                                    :id="`reliability_section_drivers_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reliability_section_drivers_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reliability_section_drivers_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reliability_section_drivers_label.reliability_section_drivers_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_drivers_label.reliability_section_drivers_label_${activeLanguageId}`
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
                                                        :for="`reliability_section_drivers_description_${activeLanguageId}`"
                                                        >Driver
                                                        description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'reliability_section_drivers_description'
                                                        )
                                                    "
                                                    :ref="`reliability_section_drivers_description_${language.id}`"
                                                    :id="`reliability_section_drivers_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `reliability_section_drivers_description`
                                                        ][
                                                            `reliability_section_drivers_description_${language?.id}`
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
                                                        `reliability_section_drivers_description.reliability_section_drivers_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_drivers_description.reliability_section_drivers_description_${activeLanguageId}`
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
                                                        :for="`reliability_section_button_label1_${activeLanguageId}`"
                                                        >Button label1</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reliability_section_button_label1_${activeLanguageId}`"
                                                    :id="`reliability_section_button_label1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reliability_section_button_label1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reliability_section_button_label1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reliability_section_button_label1.reliability_section_button_label1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_button_label1.reliability_section_button_label1_${activeLanguageId}`
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
                                                        :for="`reliability_section_button_label2_${activeLanguageId}`"
                                                        >Button label2</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reliability_section_button_label2_${activeLanguageId}`"
                                                    :id="`reliability_section_button_label2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reliability_section_button_label2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reliability_section_button_label2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reliability_section_button_label2.reliability_section_button_label2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reliability_section_button_label2.reliability_section_button_label2_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Reliability section end -->

                                <!-- Payment section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[13] =
                                                !collapseStates[13]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Payment options
                                        </h3>
                                        <svg
                                            class="w-5 h-5 fill-current text-gray-500"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div
                                        class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                                        v-if="collapseStates[13]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`payment_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payment_section_heading_${activeLanguageId}`"
                                                    :id="`payment_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payment_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payment_section_heading.payment_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payment_section_heading.payment_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`payment_section_text_${activeLanguageId}`"
                                                        >Main text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payment_section_text_${activeLanguageId}`"
                                                    :id="`payment_section_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payment_section_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_section_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payment_section_text.payment_section_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payment_section_text.payment_section_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_section_icon1_${activeLanguageId}`"
                                                            >Icon1</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_section_icon1_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_section_icon1_${activeLanguageId}`"
                                                        :id="`payment_section_icon1_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_section_icon1',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_section_icon1.payment_section_icon1_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_section_icon1.payment_section_icon1_${activeLanguageId}`
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
                                                            form['payment_section_icon1'] &&
                                                            form['payment_section_icon1'][`payment_section_icon1_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_section_icon1'] &&
                                                            form['payment_section_icon1'][`payment_section_icon1_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_section_icon1'][`payment_section_icon1_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_section_icon2_${activeLanguageId}`"
                                                            >Icon2</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_section_icon2_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_section_icon2_${activeLanguageId}`"
                                                        :id="`payment_section_icon2_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_section_icon2',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_section_icon2.payment_section_icon2_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_section_icon2.payment_section_icon2_${activeLanguageId}`
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
                                                            form['payment_section_icon2'] &&
                                                            form['payment_section_icon2'][`payment_section_icon2_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_section_icon2'] &&
                                                            form['payment_section_icon2'][`payment_section_icon2_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_section_icon2'][`payment_section_icon2_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_section_icon3_${activeLanguageId}`"
                                                            >Icon3</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_section_icon3_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_section_icon3_${activeLanguageId}`"
                                                        :id="`payment_section_icon3_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_section_icon3',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_section_icon3.payment_section_icon3_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_section_icon3.payment_section_icon3_${activeLanguageId}`
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
                                                            form['payment_section_icon3'] &&
                                                            form['payment_section_icon3'][`payment_section_icon3_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_section_icon3'] &&
                                                            form['payment_section_icon3'][`payment_section_icon3_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_section_icon3'][`payment_section_icon3_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_section_icon4_${activeLanguageId}`"
                                                            >Icon4</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_section_icon4_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_section_icon4_${activeLanguageId}`"
                                                        :id="`payment_section_icon4_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_section_icon4',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_section_icon4.payment_section_icon4_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_section_icon4.payment_section_icon4_${activeLanguageId}`
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
                                                            form['payment_section_icon4'] &&
                                                            form['payment_section_icon4'][`payment_section_icon4_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_section_icon4'] &&
                                                            form['payment_section_icon4'][`payment_section_icon4_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_section_icon4'][`payment_section_icon4_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Payment section end -->
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-home-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.getHomePageSetting && this.getHomePageSetting(); }, 1000);
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
                            this.handleInput("", language, "slider_heading");
                            this.handleInput(
                                "",
                                language,
                                "slider_from_placeholder"
                            );
                            this.handleInput(
                                "",
                                language,
                                "slider_to_placeholder"
                            );
                            this.handleInput(
                                "",
                                language,
                                "slider_date_placeholder"
                            );
                            this.handleInput(
                                "",
                                language,
                                "slider_required_error"
                            );
                            this.handleInput(
                                "",
                                language,
                                "slider_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "from_field_icon"
                            );
                            this.handleInput(
                                "",
                                language,
                                "swap_field_icon"
                            );
                            this.handleInput(
                                "",
                                language,
                                "to_field_icon"
                            );
                            this.handleInput(
                                "",
                                language,
                                "date_field_icon"
                            );
                            this.handleInput(
                                "",
                                language,
                                "search_field_icon"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_pink_rides_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_pink_rides_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_pink_rides_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_folks_rides_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_folks_rides_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_folks_rides_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_customize_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_customize_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section1_customize_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_profile_verification_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_profile_verification_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_profile_verification_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_policies_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_policies_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_policies_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_car_insurance_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_car_insurance_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_car_insurance_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_help_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_help_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section2_help_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_safe_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_safe_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_safe_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_affordable_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_affordable_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_affordable_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_reliable_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_reliable_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section3_reliable_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "section4_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_main_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point1_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point1_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point1_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point2_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point2_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point2_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point3_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point3_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point3_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point4_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point4_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point4_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point5_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point5_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_passenger_point5_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point1_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point1_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point1_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point2_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point2_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point2_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point3_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point3_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point3_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point4_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point4_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point4_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point5_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point5_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "work_section_driver_point5_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "doing_section_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "doing_section_main_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "doing_section_slider_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "doing_section_label1"
                            );
                            this.handleInput(
                                "",
                                language,
                                "doing_section_label2"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_main_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_main_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_members_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_members_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_members_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_driver_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_driver_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_driver_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_quality_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_quality_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_quality_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_policy_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_policy_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_policy_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_students_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_students_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_students_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_safety_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_safety_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_safety_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_price_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_price_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_price_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_use_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_use_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_use_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_reliable_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_reliable_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_reliable_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_responsible_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_responsible_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reasons_section_responsible_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "movement_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "movement_section_icon"
                            );
                            this.handleInput(
                                "",
                                language,
                                "movement_section_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "members_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "members_section_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "news_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "news_section_icon1"
                            );
                            this.handleInput(
                                "",
                                language,
                                "news_section_icon2"
                            );
                            this.handleInput(
                                "",
                                language,
                                "news_section_icon3"
                            );
                            this.handleInput(
                                "",
                                language,
                                "news_section_icon4"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point1_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point1_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point1_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point2_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point2_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point2_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point3_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point3_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point3_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point4_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point4_image"
                            );
                            this.handleInput(
                                "",
                                language,
                                "use_section_point4_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_passengers_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_passengers_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_drivers_label"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_drivers_description"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_button_label1"
                            );
                            this.handleInput(
                                "",
                                language,
                                "reliability_section_button_label2"
                            );
                            this.handleInput(
                                "",
                                language,
                                "payment_section_heading"
                            );
                            this.handleInput(
                                "",
                                language,
                                "payment_section_text"
                            );
                            this.handleInput(
                                "",
                                language,
                                "payment_section_icon1"
                            );
                            this.handleInput(
                                "",
                                language,
                                "payment_section_icon2"
                            );
                            this.handleInput(
                                "",
                                language,
                                "payment_section_icon3"
                            );
                            this.handleInput(
                                "",
                                language,
                                "payment_section_icon4"
                            );
                        });
                        this.fetchHomePageSetting();
                    }
                });
        },
        fetchHomePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-home-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let home_page_setting_detail =
                            res?.data?.data?.home_page_setting_detail || [];
                        home_page_setting_detail.map((setting) => {
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
                                setting?.slider_heading,
                                setting?.language,
                                "slider_heading"
                            );
                            this.handleInput(
                                setting?.slider_from_placeholder,
                                setting?.language,
                                "slider_from_placeholder"
                            );
                            this.handleInput(
                                setting?.slider_to_placeholder,
                                setting?.language,
                                "slider_to_placeholder"
                            );
                            this.handleInput(
                                setting?.slider_date_placeholder,
                                setting?.language,
                                "slider_date_placeholder"
                            );
                            this.handleInput(
                                setting?.slider_required_error,
                                setting?.language,
                                "slider_required_error"
                            );
                            this.handleInput(
                                setting?.slider_image,
                                setting?.language,
                                "slider_image"
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
                                setting?.section1_main_heading,
                                setting?.language,
                                "section1_main_heading"
                            );
                            this.handleInput(
                                setting?.section1_pink_rides_label,
                                setting?.language,
                                "section1_pink_rides_label"
                            );
                            this.handleInput(
                                setting?.section1_pink_rides_image,
                                setting?.language,
                                "section1_pink_rides_image"
                            );
                            this.handleInput(
                                setting?.section1_pink_rides_description,
                                setting?.language,
                                "section1_pink_rides_description"
                            );
                            this.handleInput(
                                setting?.section1_folks_rides_label,
                                setting?.language,
                                "section1_folks_rides_label"
                            );
                            this.handleInput(
                                setting?.section1_folks_rides_image,
                                setting?.language,
                                "section1_folks_rides_image"
                            );
                            this.handleInput(
                                setting?.section1_folks_rides_description,
                                setting?.language,
                                "section1_folks_rides_description"
                            );
                            this.handleInput(
                                setting?.section1_customize_label,
                                setting?.language,
                                "section1_customize_label"
                            );
                            this.handleInput(
                                setting?.section1_customize_image,
                                setting?.language,
                                "section1_customize_image"
                            );
                            this.handleInput(
                                setting?.section1_customize_description,
                                setting?.language,
                                "section1_customize_description"
                            );
                            this.handleInput(
                                setting?.section2_main_heading,
                                setting?.language,
                                "section2_main_heading"
                            );
                            this.handleInput(
                                setting?.section2_profile_verification_label,
                                setting?.language,
                                "section2_profile_verification_label"
                            );
                            this.handleInput(
                                setting?.section2_profile_verification_image,
                                setting?.language,
                                "section2_profile_verification_image"
                            );
                            this.handleInput(
                                setting?.section2_profile_verification_description,
                                setting?.language,
                                "section2_profile_verification_description"
                            );
                            this.handleInput(
                                setting?.section2_policies_label,
                                setting?.language,
                                "section2_policies_label"
                            );
                            this.handleInput(
                                setting?.section2_policies_image,
                                setting?.language,
                                "section2_policies_image"
                            );
                            this.handleInput(
                                setting?.section2_policies_description,
                                setting?.language,
                                "section2_policies_description"
                            );
                            this.handleInput(
                                setting?.section2_car_insurance_label,
                                setting?.language,
                                "section2_car_insurance_label"
                            );
                            this.handleInput(
                                setting?.section2_car_insurance_image,
                                setting?.language,
                                "section2_car_insurance_image"
                            );
                            this.handleInput(
                                setting?.section2_car_insurance_description,
                                setting?.language,
                                "section2_car_insurance_description"
                            );
                            this.handleInput(
                                setting?.section2_help_label,
                                setting?.language,
                                "section2_help_label"
                            );
                            this.handleInput(
                                setting?.section2_help_image,
                                setting?.language,
                                "section2_help_image"
                            );
                            this.handleInput(
                                setting?.section2_help_description,
                                setting?.language,
                                "section2_help_description"
                            );
                            this.handleInput(
                                setting?.section3_main_heading,
                                setting?.language,
                                "section3_main_heading"
                            );
                            this.handleInput(
                                setting?.section3_safe_label,
                                setting?.language,
                                "section3_safe_label"
                            );
                            this.handleInput(
                                setting?.section3_safe_image,
                                setting?.language,
                                "section3_safe_image"
                            );
                            this.handleInput(
                                setting?.section3_safe_description,
                                setting?.language,
                                "section3_safe_description"
                            );
                            this.handleInput(
                                setting?.section3_affordable_label,
                                setting?.language,
                                "section3_affordable_label"
                            );
                            this.handleInput(
                                setting?.section3_affordable_image,
                                setting?.language,
                                "section3_affordable_image"
                            );
                            this.handleInput(
                                setting?.section3_affordable_description,
                                setting?.language,
                                "section3_affordable_description"
                            );
                            this.handleInput(
                                setting?.section3_reliable_label,
                                setting?.language,
                                "section3_reliable_label"
                            );
                            this.handleInput(
                                setting?.section3_reliable_image,
                                setting?.language,
                                "section3_reliable_image"
                            );
                            this.handleInput(
                                setting?.section3_reliable_description,
                                setting?.language,
                                "section3_reliable_description"
                            );
                            this.handleInput(
                                setting?.section4_main_heading,
                                setting?.language,
                                "section4_main_heading"
                            );
                            this.handleInput(
                                setting?.work_section_main_heading,
                                setting?.language,
                                "work_section_main_heading"
                            );
                            this.handleInput(
                                setting?.work_section_main_text,
                                setting?.language,
                                "work_section_main_text"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_label,
                                setting?.language,
                                "work_section_passenger_label"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_description,
                                setting?.language,
                                "work_section_passenger_description"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point1_label,
                                setting?.language,
                                "work_section_passenger_point1_label"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point1_image,
                                setting?.language,
                                "work_section_passenger_point1_image"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point1_description,
                                setting?.language,
                                "work_section_passenger_point1_description"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point2_label,
                                setting?.language,
                                "work_section_passenger_point2_label"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point2_image,
                                setting?.language,
                                "work_section_passenger_point2_image"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point2_description,
                                setting?.language,
                                "work_section_passenger_point2_description"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point3_label,
                                setting?.language,
                                "work_section_passenger_point3_label"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point3_image,
                                setting?.language,
                                "work_section_passenger_point3_image"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point3_description,
                                setting?.language,
                                "work_section_passenger_point3_description"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point4_label,
                                setting?.language,
                                "work_section_passenger_point4_label"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point4_image,
                                setting?.language,
                                "work_section_passenger_point4_image"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point4_description,
                                setting?.language,
                                "work_section_passenger_point4_description"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point5_label,
                                setting?.language,
                                "work_section_passenger_point5_label"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point5_image,
                                setting?.language,
                                "work_section_passenger_point5_image"
                            );
                            this.handleInput(
                                setting?.work_section_passenger_point5_description,
                                setting?.language,
                                "work_section_passenger_point5_description"
                            );
                            this.handleInput(
                                setting?.work_section_driver_label,
                                setting?.language,
                                "work_section_driver_label"
                            );
                            this.handleInput(
                                setting?.work_section_driver_description,
                                setting?.language,
                                "work_section_driver_description"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point1_label,
                                setting?.language,
                                "work_section_driver_point1_label"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point1_image,
                                setting?.language,
                                "work_section_driver_point1_image"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point1_description,
                                setting?.language,
                                "work_section_driver_point1_description"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point2_label,
                                setting?.language,
                                "work_section_driver_point2_label"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point2_image,
                                setting?.language,
                                "work_section_driver_point2_image"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point2_description,
                                setting?.language,
                                "work_section_driver_point2_description"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point3_label,
                                setting?.language,
                                "work_section_driver_point3_label"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point3_image,
                                setting?.language,
                                "work_section_driver_point3_image"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point3_description,
                                setting?.language,
                                "work_section_driver_point3_description"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point4_label,
                                setting?.language,
                                "work_section_driver_point4_label"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point4_image,
                                setting?.language,
                                "work_section_driver_point4_image"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point4_description,
                                setting?.language,
                                "work_section_driver_point4_description"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point5_label,
                                setting?.language,
                                "work_section_driver_point5_label"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point5_image,
                                setting?.language,
                                "work_section_driver_point5_image"
                            );
                            this.handleInput(
                                setting?.work_section_driver_point5_description,
                                setting?.language,
                                "work_section_driver_point5_description"
                            );
                            this.handleInput(
                                setting?.doing_section_main_heading,
                                setting?.language,
                                "doing_section_main_heading"
                            );
                            this.handleInput(
                                setting?.doing_section_main_text,
                                setting?.language,
                                "doing_section_main_text"
                            );
                            this.handleInput(
                                setting?.doing_section_slider_image,
                                setting?.language,
                                "doing_section_slider_image"
                            );
                            this.handleInput(
                                setting?.doing_section_label1,
                                setting?.language,
                                "doing_section_label1"
                            );
                            this.handleInput(
                                setting?.doing_section_label2,
                                setting?.language,
                                "doing_section_label2"
                            );
                            this.handleInput(
                                setting?.reasons_section_main_heading,
                                setting?.language,
                                "reasons_section_main_heading"
                            );
                            this.handleInput(
                                setting?.reasons_section_main_text,
                                setting?.language,
                                "reasons_section_main_text"
                            );
                            this.handleInput(
                                setting?.reasons_section_members_label,
                                setting?.language,
                                "reasons_section_members_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_members_image,
                                setting?.language,
                                "reasons_section_members_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_members_description,
                                setting?.language,
                                "reasons_section_members_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_driver_label,
                                setting?.language,
                                "reasons_section_driver_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_driver_image,
                                setting?.language,
                                "reasons_section_driver_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_driver_description,
                                setting?.language,
                                "reasons_section_driver_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_quality_label,
                                setting?.language,
                                "reasons_section_quality_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_quality_image,
                                setting?.language,
                                "reasons_section_quality_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_quality_description,
                                setting?.language,
                                "reasons_section_quality_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_policy_label,
                                setting?.language,
                                "reasons_section_policy_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_policy_image,
                                setting?.language,
                                "reasons_section_policy_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_policy_description,
                                setting?.language,
                                "reasons_section_policy_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_students_label,
                                setting?.language,
                                "reasons_section_students_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_students_image,
                                setting?.language,
                                "reasons_section_students_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_students_description,
                                setting?.language,
                                "reasons_section_students_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_safety_label,
                                setting?.language,
                                "reasons_section_safety_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_safety_image,
                                setting?.language,
                                "reasons_section_safety_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_safety_description,
                                setting?.language,
                                "reasons_section_safety_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_price_label,
                                setting?.language,
                                "reasons_section_price_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_price_image,
                                setting?.language,
                                "reasons_section_price_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_price_description,
                                setting?.language,
                                "reasons_section_price_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_use_label,
                                setting?.language,
                                "reasons_section_use_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_use_image,
                                setting?.language,
                                "reasons_section_use_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_use_description,
                                setting?.language,
                                "reasons_section_use_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_reliable_label,
                                setting?.language,
                                "reasons_section_reliable_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_reliable_image,
                                setting?.language,
                                "reasons_section_reliable_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_reliable_description,
                                setting?.language,
                                "reasons_section_reliable_description"
                            );
                            this.handleInput(
                                setting?.reasons_section_responsible_label,
                                setting?.language,
                                "reasons_section_responsible_label"
                            );
                            this.handleInput(
                                setting?.reasons_section_responsible_image,
                                setting?.language,
                                "reasons_section_responsible_image"
                            );
                            this.handleInput(
                                setting?.reasons_section_responsible_description,
                                setting?.language,
                                "reasons_section_responsible_description"
                            );
                            this.handleInput(
                                setting?.movement_section_heading,
                                setting?.language,
                                "movement_section_heading"
                            );
                            this.handleInput(
                                setting?.movement_section_icon,
                                setting?.language,
                                "movement_section_icon"
                            );
                            this.handleInput(
                                setting?.movement_section_text,
                                setting?.language,
                                "movement_section_text"
                            );
                            this.handleInput(
                                setting?.members_section_heading,
                                setting?.language,
                                "members_section_heading"
                            );
                            this.handleInput(
                                setting?.members_section_text,
                                setting?.language,
                                "members_section_text"
                            );
                            this.handleInput(
                                setting?.news_section_heading,
                                setting?.language,
                                "news_section_heading"
                            );
                            this.handleInput(
                                setting?.news_section_icon1,
                                setting?.language,
                                "news_section_icon1"
                            );
                            this.handleInput(
                                setting?.news_section_icon2,
                                setting?.language,
                                "news_section_icon2"
                            );
                            this.handleInput(
                                setting?.news_section_icon3,
                                setting?.language,
                                "news_section_icon3"
                            );
                            this.handleInput(
                                setting?.news_section_icon4,
                                setting?.language,
                                "news_section_icon4"
                            );
                            this.handleInput(
                                setting?.use_section_heading,
                                setting?.language,
                                "use_section_heading"
                            );
                            this.handleInput(
                                setting?.use_section_text,
                                setting?.language,
                                "use_section_text"
                            );
                            this.handleInput(
                                setting?.use_section_point1_label,
                                setting?.language,
                                "use_section_point1_label"
                            );
                            this.handleInput(
                                setting?.use_section_point1_image,
                                setting?.language,
                                "use_section_point1_image"
                            );
                            this.handleInput(
                                setting?.use_section_point1_description,
                                setting?.language,
                                "use_section_point1_description"
                            );
                            this.handleInput(
                                setting?.use_section_point2_label,
                                setting?.language,
                                "use_section_point2_label"
                            );
                            this.handleInput(
                                setting?.use_section_point2_image,
                                setting?.language,
                                "use_section_point2_image"
                            );
                            this.handleInput(
                                setting?.use_section_point2_description,
                                setting?.language,
                                "use_section_point2_description"
                            );
                            this.handleInput(
                                setting?.use_section_point3_label,
                                setting?.language,
                                "use_section_point3_label"
                            );
                            this.handleInput(
                                setting?.use_section_point3_image,
                                setting?.language,
                                "use_section_point3_image"
                            );
                            this.handleInput(
                                setting?.use_section_point3_description,
                                setting?.language,
                                "use_section_point3_description"
                            );
                            this.handleInput(
                                setting?.use_section_point4_label,
                                setting?.language,
                                "use_section_point4_label"
                            );
                            this.handleInput(
                                setting?.use_section_point4_image,
                                setting?.language,
                                "use_section_point4_image"
                            );
                            this.handleInput(
                                setting?.use_section_point4_description,
                                setting?.language,
                                "use_section_point4_description"
                            );
                            this.handleInput(
                                setting?.reliability_section_heading,
                                setting?.language,
                                "reliability_section_heading"
                            );
                            this.handleInput(
                                setting?.reliability_section_text,
                                setting?.language,
                                "reliability_section_text"
                            );
                            this.handleInput(
                                setting?.reliability_section_passengers_label,
                                setting?.language,
                                "reliability_section_passengers_label"
                            );
                            this.handleInput(
                                setting?.reliability_section_passengers_description,
                                setting?.language,
                                "reliability_section_passengers_description"
                            );
                            this.handleInput(
                                setting?.reliability_section_drivers_label,
                                setting?.language,
                                "reliability_section_drivers_label"
                            );
                            this.handleInput(
                                setting?.reliability_section_drivers_description,
                                setting?.language,
                                "reliability_section_drivers_description"
                            );
                            this.handleInput(
                                setting?.reliability_section_button_label1,
                                setting?.language,
                                "reliability_section_button_label1"
                            );
                            this.handleInput(
                                setting?.reliability_section_button_label2,
                                setting?.language,
                                "reliability_section_button_label2"
                            );
                            this.handleInput(
                                setting?.payment_section_heading,
                                setting?.language,
                                "payment_section_heading"
                            );
                            this.handleInput(
                                setting?.payment_section_text,
                                setting?.language,
                                "payment_section_text"
                            );
                            this.handleInput(
                                setting?.payment_section_icon1,
                                setting?.language,
                                "payment_section_icon1"
                            );
                            this.handleInput(
                                setting?.payment_section_icon2,
                                setting?.language,
                                "payment_section_icon2"
                            );
                            this.handleInput(
                                setting?.payment_section_icon3,
                                setting?.language,
                                "payment_section_icon3"
                            );
                            this.handleInput(
                                setting?.payment_section_icon4,
                                setting?.language,
                                "payment_section_icon4"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-home-page-setting`,
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
                    `slider_heading.slider_heading_${language.id}`
                ) ||
                validationErros.has(
                    `slider_from_placeholder.slider_from_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `slider_to_placeholder.slider_to_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `slider_date_placeholder.slider_date_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `slider_required_error.slider_required_error_${language.id}`
                ) ||
                validationErros.has(
                    `slider_image.slider_image_${language.id}`
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
                    `section1_main_heading.section1_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `section1_pink_rides_label.section1_pink_rides_label_${language.id}`
                ) ||
                validationErros.has(
                    `section1_pink_rides_image.section1_pink_rides_image_${language.id}`
                ) ||
                validationErros.has(
                    `section1_pink_rides_description.section1_pink_rides_description_${language.id}`
                ) ||
                validationErros.has(
                    `section1_folks_rides_label.section1_folks_rides_label_${language.id}`
                ) ||
                validationErros.has(
                    `section1_folks_rides_image.section1_folks_rides_image_${language.id}`
                ) ||
                validationErros.has(
                    `section1_folks_rides_description.section1_folks_rides_description_${language.id}`
                ) ||
                validationErros.has(
                    `section1_customize_label.section1_customize_label_${language.id}`
                ) ||
                validationErros.has(
                    `section1_customize_image.section1_customize_image_${language.id}`
                ) ||
                validationErros.has(
                    `section1_customize_description.section1_customize_description_${language.id}`
                ) ||
                validationErros.has(
                    `section2_main_heading.section2_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `section2_profile_verification_label.section2_profile_verification_label_${language.id}`
                ) ||
                validationErros.has(
                    `section2_profile_verification_image.section2_profile_verification_image_${language.id}`
                ) ||
                validationErros.has(
                    `section2_profile_verification_description.section2_profile_verification_description_${language.id}`
                ) ||
                validationErros.has(
                    `section2_policies_label.section2_policies_label_${language.id}`
                ) ||
                validationErros.has(
                    `section2_policies_image.section2_policies_image_${language.id}`
                ) ||
                validationErros.has(
                    `section2_policies_description.section2_policies_description_${language.id}`
                ) ||
                validationErros.has(
                    `section2_car_insurance_label.section2_car_insurance_label_${language.id}`
                ) ||
                validationErros.has(
                    `section2_car_insurance_image.section2_car_insurance_image_${language.id}`
                ) ||
                validationErros.has(
                    `section2_car_insurance_description.section2_car_insurance_description_${language.id}`
                ) ||
                validationErros.has(
                    `section2_help_label.section2_help_label_${language.id}`
                ) ||
                validationErros.has(
                    `section2_help_image.section2_help_image_${language.id}`
                ) ||
                validationErros.has(
                    `section2_help_description.section2_help_description_${language.id}`
                ) ||
                validationErros.has(
                    `section3_main_heading.section3_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `section3_safe_label.section3_safe_label_${language.id}`
                ) ||
                validationErros.has(
                    `section3_safe_image.section3_safe_image_${language.id}`
                ) ||
                validationErros.has(
                    `section3_safe_description.section3_safe_description_${language.id}`
                ) ||
                validationErros.has(
                    `section3_affordable_label.section3_affordable_label_${language.id}`
                ) ||
                validationErros.has(
                    `section3_affordable_image.section3_affordable_image_${language.id}`
                ) ||
                validationErros.has(
                    `section3_affordable_description.section3_affordable_description_${language.id}`
                ) ||
                validationErros.has(
                    `section3_reliable_label.section3_reliable_label_${language.id}`
                ) ||
                validationErros.has(
                    `section3_reliable_image.section3_reliable_image_${language.id}`
                ) ||
                validationErros.has(
                    `section3_reliable_description.section3_reliable_description_${language.id}`
                ) ||
                validationErros.has(
                    `section4_main_heading.section4_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_main_heading.work_section_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_main_text.work_section_main_text_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_label.work_section_passenger_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_description.work_section_passenger_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point1_label.work_section_passenger_point1_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point1_image.work_section_passenger_point1_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point1_description.work_section_passenger_point1_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point2_label.work_section_passenger_point2_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point2_image.work_section_passenger_point2_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point2_description.work_section_passenger_point2_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point3_label.work_section_passenger_point3_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point3_image.work_section_passenger_point3_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point3_description.work_section_passenger_point3_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point4_label.work_section_passenger_point4_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point4_image.work_section_passenger_point4_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point4_description.work_section_passenger_point4_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point5_label.work_section_passenger_point5_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point5_image.work_section_passenger_point5_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_passenger_point5_description.work_section_passenger_point5_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_label.work_section_driver_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_description.work_section_driver_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point1_label.work_section_driver_point1_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point1_image.work_section_driver_point1_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point1_description.work_section_driver_point1_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point2_label.work_section_driver_point2_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point2_image.work_section_driver_point2_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point2_description.work_section_driver_point2_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point3_label.work_section_driver_point3_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point3_image.work_section_driver_point3_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point3_description.work_section_driver_point3_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point4_label.work_section_driver_point4_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point4_image.work_section_driver_point4_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point4_description.work_section_driver_point4_description_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point5_label.work_section_driver_point5_label_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point5_image.work_section_driver_point5_image_${language.id}`
                ) ||
                validationErros.has(
                    `work_section_driver_point5_description.work_section_driver_point5_description_${language.id}`
                ) ||
                validationErros.has(
                    `doing_section_main_heading.doing_section_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `doing_section_main_text.doing_section_main_text_${language.id}`
                ) ||
                validationErros.has(
                    `doing_section_slider_image.doing_section_slider_image_${language.id}`
                ) ||
                validationErros.has(
                    `doing_section_label1.doing_section_label1_${language.id}`
                ) ||
                validationErros.has(
                    `doing_section_label2.doing_section_label2_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_main_heading.reasons_section_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_main_text.reasons_section_main_text_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_members_label.reasons_section_members_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_members_image.reasons_section_members_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_members_description.reasons_section_members_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_driver_label.reasons_section_driver_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_driver_image.reasons_section_driver_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_driver_description.reasons_section_driver_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_quality_label.reasons_section_quality_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_quality_image.reasons_section_quality_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_quality_description.reasons_section_quality_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_policy_label.reasons_section_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_policy_image.reasons_section_policy_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_policy_description.reasons_section_policy_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_students_label.reasons_section_students_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_students_image.reasons_section_students_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_students_description.reasons_section_students_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_safety_label.reasons_section_safety_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_safety_image.reasons_section_safety_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_safety_description.reasons_section_safety_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_price_label.reasons_section_price_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_price_image.reasons_section_price_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_price_description.reasons_section_price_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_use_label.reasons_section_use_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_use_image.reasons_section_use_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_use_description.reasons_section_use_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_reliable_label.reasons_section_reliable_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_reliable_image.reasons_section_reliable_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_reliable_description.reasons_section_reliable_description_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_responsible_label.reasons_section_responsible_label_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_responsible_image.reasons_section_responsible_image_${language.id}`
                ) ||
                validationErros.has(
                    `reasons_section_responsible_description.reasons_section_responsible_description_${language.id}`
                ) ||
                validationErros.has(
                    `movement_section_heading.movement_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `movement_section_icon.movement_section_icon_${language.id}`
                ) ||
                validationErros.has(
                    `movement_section_text.movement_section_text_${language.id}`
                ) ||
                validationErros.has(
                    `members_section_heading.members_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `members_section_text.members_section_text_${language.id}`
                ) ||
                validationErros.has(
                    `news_section_heading.news_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `news_section_icon1.news_section_icon1_${language.id}`
                ) ||
                validationErros.has(
                    `news_section_icon2.news_section_icon2_${language.id}`
                ) ||
                validationErros.has(
                    `news_section_icon3.news_section_icon3_${language.id}`
                ) ||
                validationErros.has(
                    `news_section_icon4.news_section_icon4_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_heading.use_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_text.use_section_text_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point1_label.use_section_point1_label_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point1_image.use_section_point1_image_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point1_description.use_section_point1_description_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point2_label.use_section_point2_label_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point2_image.use_section_point2_image_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point2_description.use_section_point2_description_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point3_label.use_section_point3_label_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point3_image.use_section_point3_image_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point3_description.use_section_point3_description_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point4_label.use_section_point4_label_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point4_image.use_section_point4_image_${language.id}`
                ) ||
                validationErros.has(
                    `use_section_point4_description.use_section_point4_description_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_heading.reliability_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_text.reliability_section_text_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_passengers_label.reliability_section_passengers_label_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_passengers_description.reliability_section_passengers_description_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_drivers_label.reliability_section_drivers_label_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_drivers_description.reliability_section_drivers_description_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_button_label1.reliability_section_button_label1_${language.id}`
                ) ||
                validationErros.has(
                    `reliability_section_button_label2.reliability_section_button_label2_${language.id}`
                ) ||
                validationErros.has(
                    `payment_section_heading.payment_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `payment_section_icon1.payment_section_icon1_${language.id}`
                ) ||
                validationErros.has(
                    `payment_section_icon2.payment_section_icon2_${language.id}`
                ) ||
                validationErros.has(
                    `payment_section_icon3.payment_section_icon3_${language.id}`
                ) ||
                validationErros.has(
                    `payment_section_icon4.payment_section_icon4_${language.id}`
                ) ||
                validationErros.has(
                    `payment_section_text.payment_section_text_${language.id}`
                )
            );
        },
        handleImage(e, language, key) {
            // console.log(e.target.files[0], key, language);
            var file = new FormData();
            file.append("file", e.target.files[0]);
            axios.post("/api/admin/media/image_again_upload", file).then((res) => {
                this.handleInput(res?.data, language, key);
            });
        },
    },
};
</script>
