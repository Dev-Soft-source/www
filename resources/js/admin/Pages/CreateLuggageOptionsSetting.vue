<template>
    <AppLayout>
        <section class="post-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Ride luggage options settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Luggage Options translations and icons for a specific language.</p>

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
                                    <a :href="`${mixAdminApiUrl}download-luggage-options-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                                    :for="`luggage_option1_${activeLanguageId}`"
                                                    >No luggage</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option1_${activeLanguageId}`"
                                                :id="`luggage_option1_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option1'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option1'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option1.luggage_option1_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option1.luggage_option1_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option1_tooltip_${activeLanguageId}`"
                                                    >No luggage tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option1_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option1_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option1_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option1_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option1_tooltip.luggage_option1_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option1_tooltip.luggage_option1_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option1_icon_${activeLanguageId}`"
                                                        >No luggage icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option1_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option1_icon_${activeLanguageId}`"
                                                    :id="`luggage_option1_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option1_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option1_icon.luggage_option1_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option1_icon.luggage_option1_icon_${activeLanguageId}`
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
                                                        form['luggage_option1_icon'] &&
                                                        form['luggage_option1_icon'][`luggage_option1_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option1_icon'] &&
                                                        form['luggage_option1_icon'][`luggage_option1_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option1_icon'][`luggage_option1_icon_${activeLanguageId}`]
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
                                                    :for="`luggage_option2_${activeLanguageId}`"
                                                    >Small</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option2_${activeLanguageId}`"
                                                :id="`luggage_option2_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option2'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option2'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option2.luggage_option2_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option2.luggage_option2_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option2_tooltip_${activeLanguageId}`"
                                                    >Small tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option2_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option2_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option2_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option2_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option2_tooltip.luggage_option2_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option2_tooltip.luggage_option2_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option2_icon_${activeLanguageId}`"
                                                        >Small icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option2_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option2_icon_${activeLanguageId}`"
                                                    :id="`luggage_option2_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option2_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option2_icon.luggage_option2_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option2_icon.luggage_option2_icon_${activeLanguageId}`
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
                                                        form['luggage_option2_icon'] &&
                                                        form['luggage_option2_icon'][`luggage_option2_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option2_icon'] &&
                                                        form['luggage_option2_icon'][`luggage_option2_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option2_icon'][`luggage_option2_icon_${activeLanguageId}`]
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
                                                    :for="`luggage_option3_${activeLanguageId}`"
                                                    >Medium</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option3_${activeLanguageId}`"
                                                :id="`luggage_option3_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option3'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option3'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option3.luggage_option3_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option3.luggage_option3_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option3_tooltip_${activeLanguageId}`"
                                                    >Medium tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option3_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option3_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option3_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option3_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option3_tooltip.luggage_option3_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option3_tooltip.luggage_option3_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option3_icon_${activeLanguageId}`"
                                                        >Medium icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option3_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option3_icon_${activeLanguageId}`"
                                                    :id="`luggage_option3_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option3_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option3_icon.luggage_option3_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option3_icon.luggage_option3_icon_${activeLanguageId}`
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
                                                        form['luggage_option3_icon'] &&
                                                        form['luggage_option3_icon'][`luggage_option3_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option3_icon'] &&
                                                        form['luggage_option3_icon'][`luggage_option3_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option3_icon'][`luggage_option3_icon_${activeLanguageId}`]
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
                                                    :for="`luggage_option4_${activeLanguageId}`"
                                                    >Large</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option4_${activeLanguageId}`"
                                                :id="`luggage_option4_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option4'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option4'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option4.luggage_option4_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option4.luggage_option4_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option4_tooltip_${activeLanguageId}`"
                                                    >Large tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option4_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option4_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option4_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option4_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option4_tooltip.luggage_option4_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option4_tooltip.luggage_option4_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option4_icon_${activeLanguageId}`"
                                                        >Large icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option4_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option4_icon_${activeLanguageId}`"
                                                    :id="`luggage_option4_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option4_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option4_icon.luggage_option4_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option4_icon.luggage_option4_icon_${activeLanguageId}`
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
                                                        form['luggage_option4_icon'] &&
                                                        form['luggage_option4_icon'][`luggage_option4_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option4_icon'] &&
                                                        form['luggage_option4_icon'][`luggage_option4_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option4_icon'][`luggage_option4_icon_${activeLanguageId}`]
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
                                                    :for="`luggage_option5_${activeLanguageId}`"
                                                    >Xl and multiple</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option5_${activeLanguageId}`"
                                                :id="`luggage_option5_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option5'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option5'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option5.luggage_option5_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option5.luggage_option5_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option5_tooltip_${activeLanguageId}`"
                                                    >Xl and multiple tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option5_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option5_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option5_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option5_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option5_tooltip.luggage_option5_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option5_tooltip.luggage_option5_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option5_label_${activeLanguageId}`"
                                                    >Text below Xl and multiple</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option5_label_${activeLanguageId}`"
                                                :id="`luggage_option5_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option5_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option5_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option5_label.luggage_option5_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option5_label.luggage_option5_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option5_icon_${activeLanguageId}`"
                                                        >Xl and multiple icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option5_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option5_icon_${activeLanguageId}`"
                                                    :id="`luggage_option5_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option5_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option5_icon.luggage_option5_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option5_icon.luggage_option5_icon_${activeLanguageId}`
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
                                                        form['luggage_option5_icon'] &&
                                                        form['luggage_option5_icon'][`luggage_option5_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option5_icon'] &&
                                                        form['luggage_option5_icon'][`luggage_option5_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option5_icon'][`luggage_option5_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-luggage-options-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchPostRidePageSetting && this.fetchPostRidePageSetting(); }, 1000);
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
                            this.handleInput("", language, "luggage_option1");
                            this.handleInput("", language, "luggage_option1_tooltip");
                            this.handleInput("", language, "luggage_option1_icon");
                            this.handleInput("", language, "luggage_option2");
                            this.handleInput("", language, "luggage_option2_tooltip");
                            this.handleInput("", language, "luggage_option2_icon");
                            this.handleInput("", language, "luggage_option3");
                            this.handleInput("", language, "luggage_option3_tooltip");
                            this.handleInput("", language, "luggage_option3_icon");
                            this.handleInput("", language, "luggage_option4");
                            this.handleInput("", language, "luggage_option4_tooltip");
                            this.handleInput("", language, "luggage_option4_icon");
                            this.handleInput("", language, "luggage_option5");
                            this.handleInput("", language, "luggage_option5_tooltip");
                            this.handleInput("", language, "luggage_option5_label");
                            this.handleInput("", language, "luggage_option5_icon");
                        });
                        this.fetchPostRidePageSetting();
                    }
                });
        },
        fetchPostRidePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-post-ride-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let post_ride_page_setting_detail =
                            res?.data?.data?.post_ride_page_setting_detail || [];
                        post_ride_page_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.luggage_option1,
                                setting?.language,
                                "luggage_option1"
                            );
                            this.handleInput(
                                setting?.luggage_option1_tooltip,
                                setting?.language,
                                "luggage_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option1_icon,
                                setting?.language,
                                "luggage_option1_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option2,
                                setting?.language,
                                "luggage_option2"
                            );
                            this.handleInput(
                                setting?.luggage_option2_tooltip,
                                setting?.language,
                                "luggage_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option2_icon,
                                setting?.language,
                                "luggage_option2_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option3,
                                setting?.language,
                                "luggage_option3"
                            );
                            this.handleInput(
                                setting?.luggage_option3_tooltip,
                                setting?.language,
                                "luggage_option3_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option3_icon,
                                setting?.language,
                                "luggage_option3_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option4,
                                setting?.language,
                                "luggage_option4"
                            );
                            this.handleInput(
                                setting?.luggage_option4_tooltip,
                                setting?.language,
                                "luggage_option4_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option4_icon,
                                setting?.language,
                                "luggage_option4_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option5,
                                setting?.language,
                                "luggage_option5"
                            );
                            this.handleInput(
                                setting?.luggage_option5_tooltip,
                                setting?.language,
                                "luggage_option5_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option5_icon,
                                setting?.language,
                                "luggage_option5_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option5_label,
                                setting?.language,
                                "luggage_option5_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-luggage-options-setting`,
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
                    `luggage_option1.luggage_option1_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option1_tooltip.luggage_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option1_icon.luggage_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2.luggage_option2_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2_tooltip.luggage_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2_icon.luggage_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3.luggage_option3_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3_tooltip.luggage_option3_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3_icon.luggage_option3_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4.luggage_option4_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4_tooltip.luggage_option4_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4_icon.luggage_option4_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5.luggage_option5_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5_tooltip.luggage_option5_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5_icon.luggage_option5_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5_label.luggage_option5_label_${language.id}`
                )
            );
        },
        handleImage(e, language, key) {
            console.log(e.target.files[0], key, language);
            var file = new FormData();
            file.append("file", e.target.files[0]);
            axios.post("/api/admin/media/image_again_upload", file).then((res) => {
                this.handleInput(res?.data, language, key);
            });
        },
    },
};
</script>
