<template>
    <AppLayout>
        <section class="vehicle-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                 Edit Profile Page settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Edit Profile page setting translations for a specific language.</p>

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
                                    <a :href="`${mixAdminApiUrl}download-edit-profile-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                                    :for="`min_bio_label_${activeLanguageId}`"
                                                    >Mini Bio Label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`min_bio_label_${activeLanguageId}`"
                                                :id="`min_bio_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('min_bio_label')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'min_bio_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `min_bio_label.min_bio_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `min_bio_label.min_bio_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`passenger_driven_label_${activeLanguageId}`"
                                                    >Passenger Driven Label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_driven_label_${activeLanguageId}`"
                                                :id="`passenger_driven_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('passenger_driven_label')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_driven_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_driven_label.passenger_driven_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_driven_label.passenger_driven_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div class="w-full">
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_driven_icon_${activeLanguageId}`"
                                                        >Passenger Driven Icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`passenger_driven_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`passenger_driven_icon_${activeLanguageId}`"
                                                    :id="`passenger_driven_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'passenger_driven_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `passenger_driven_icon.passenger_driven_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `passenger_driven_icon.passenger_driven_icon_${activeLanguageId}`
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
                                                        form['passenger_driven_icon'] &&
                                                        form['passenger_driven_icon'][`passenger_driven_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['passenger_driven_icon'] &&
                                                        form['passenger_driven_icon'][`passenger_driven_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['passenger_driven_icon'][`passenger_driven_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`mini_bio_placeholder_${activeLanguageId}`"
                                                    >Mini Bio Placeholder</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`mini_bio_placeholder_${activeLanguageId}`"
                                                :id="`mini_bio_placeholder_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('mini_bio_placeholder')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'mini_bio_placeholder'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `mini_bio_placeholder.mini_bio_placeholder_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `mini_bio_placeholder.mini_bio_placeholder_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`rides_taken_label_${activeLanguageId}`"
                                                    >Ride Taken Labels</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`rides_taken_label_${activeLanguageId}`"
                                                :id="`rides_taken_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'rides_taken_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'rides_taken_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `rides_taken_label.rides_taken_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `rides_taken_label.rides_taken_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div class="w-full">
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`rides_taken_icon_${activeLanguageId}`"
                                                        >Ride Taken Icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`rides_taken_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`rides_taken_icon_${activeLanguageId}`"
                                                    :id="`rides_taken_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'rides_taken_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `rides_taken_icon.rides_taken_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `rides_taken_icon.rides_taken_icon_${activeLanguageId}`
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
                                                        form['rides_taken_icon'] &&
                                                        form['rides_taken_icon'][`rides_taken_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['rides_taken_icon'] &&
                                                        form['rides_taken_icon'][`rides_taken_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['rides_taken_icon'][`rides_taken_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`km_shared_label_${activeLanguageId}`"
                                                    >Km Shared Label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`km_shared_label_${activeLanguageId}`"
                                                :id="`km_shared_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'km_shared_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'km_shared_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `km_shared_label.km_shared_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `km_shared_label.km_shared_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div class="w-full">
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`km_shared_icon_${activeLanguageId}`"
                                                        >Km Shared Icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`km_shared_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`km_shared_icon_${activeLanguageId}`"
                                                    :id="`km_shared_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'km_shared_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `km_shared_icon.km_shared_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `km_shared_icon.km_shared_icon_${activeLanguageId}`
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
                                                        form['km_shared_icon'] &&
                                                        form['km_shared_icon'][`km_shared_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['km_shared_icon'] &&
                                                        form['km_shared_icon'][`km_shared_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['km_shared_icon'][`km_shared_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="relative z-0 w-full group">
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
                                    </div> -->

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`review_label_${activeLanguageId}`"
                                                        >Review Label</label
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
                                                        :for="`reply_label_${activeLanguageId}`"
                                                        >Reply Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reply_label_${activeLanguageId}`"
                                                    :id="`reply_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reply_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reply_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reply_label.reply_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reply_label.reply_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`link_review_label_${activeLanguageId}`"
                                                        >Link Review Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`link_review_label_${activeLanguageId}`"
                                                    :id="`link_review_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'link_review_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'link_review_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `link_review_label.link_review_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `link_review_label.link_review_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`review_heading_${activeLanguageId}`"
                                                        >Review Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`review_heading_${activeLanguageId}`"
                                                    :id="`review_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `review_heading.review_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `review_heading.review_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`edit_profile_text_${activeLanguageId}`"
                                                        >Edit Profile Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`edit_profile_text_${activeLanguageId}`"
                                                    :id="`edit_profile_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'edit_profile_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'edit_profile_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `edit_profile_text.edit_profile_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `edit_profile_text.edit_profile_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`first_name_label_${activeLanguageId}`"
                                                        >First Name Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_label_${activeLanguageId}`"
                                                    :id="`first_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_label.first_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_label.first_name_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`first_name_placeholder_${activeLanguageId}`"
                                                        >First Name Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_placeholder_${activeLanguageId}`"
                                                    :id="`first_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_placeholder.first_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_placeholder.first_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`last_name_label_${activeLanguageId}`"
                                                        >Last Name Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_label_${activeLanguageId}`"
                                                    :id="`last_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_label.last_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_label.last_name_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`last_name_placeholder_${activeLanguageId}`"
                                                        >Last Name Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_placeholder_${activeLanguageId}`"
                                                    :id="`last_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_placeholder.last_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_placeholder.last_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`gender_label_${activeLanguageId}`"
                                                        >Gender Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`gender_label_${activeLanguageId}`"
                                                    :id="`gender_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'gender_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'gender_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `gender_label.gender_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `gender_label.gender_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`male_label_${activeLanguageId}`"
                                                        >Male Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`male_label_${activeLanguageId}`"
                                                    :id="`male_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'male_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'male_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `male_label.male_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `male_label.male_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`female_label_${activeLanguageId}`"
                                                        >Female Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`female_label_${activeLanguageId}`"
                                                    :id="`female_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'female_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'female_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `female_label.female_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `female_label.female_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`prefer_no_to_say_label_${activeLanguageId}`"
                                                        >Prefer not to say Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`prefer_no_to_say_label_${activeLanguageId}`"
                                                    :id="`prefer_no_to_say_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'prefer_no_to_say_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'prefer_no_to_say_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `prefer_no_to_say_label.prefer_no_to_say_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `prefer_no_to_say_label.prefer_no_to_say_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`state_label_${activeLanguageId}`"
                                                        >State label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`state_label_${activeLanguageId}`"
                                                    :id="`state_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'state_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'state_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `state_label.state_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `state_label.state_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`state_placeholder_${activeLanguageId}`"
                                                        >State Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`state_placeholder_${activeLanguageId}`"
                                                    :id="`state_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'state_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'state_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `state_placeholder.state_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `state_placeholder.state_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`dob_label_${activeLanguageId}`"
                                                        >DOB Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`dob_label_${activeLanguageId}`"
                                                    :id="`dob_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'dob_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'dob_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `dob_label.dob_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `dob_label.dob_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`dob_placeholder_${activeLanguageId}`"
                                                        >DOB Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`dob_placeholder_${activeLanguageId}`"
                                                    :id="`dob_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'dob_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'dob_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `dob_placeholder.dob_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `dob_placeholder.dob_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`country_label_${activeLanguageId}`"
                                                        >Country Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_label_${activeLanguageId}`"
                                                    :id="`country_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_label.country_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_label.country_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`country_placeholder_${activeLanguageId}`"
                                                        >Country Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_placeholder_${activeLanguageId}`"
                                                    :id="`country_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_placeholder.country_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_placeholder.country_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`city_label_${activeLanguageId}`"
                                                        >City label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`city_label_${activeLanguageId}`"
                                                    :id="`city_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'city_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'city_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `city_label.city_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `city_label.city_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`city_placeholder_${activeLanguageId}`"
                                                        >City Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`city_placeholder_${activeLanguageId}`"
                                                    :id="`city_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'city_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'city_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `city_placeholder.city_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `city_placeholder.city_placeholder_${activeLanguageId}`
                                                    )
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
                                                        >Address Placeholder</label
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
                                                        :for="`zip_label_${activeLanguageId}`"
                                                        >Zip Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`zip_label_${activeLanguageId}`"
                                                    :id="`zip_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'zip_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'zip_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `zip_label.zip_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `zip_label.zip_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`mini_bio_label_${activeLanguageId}`"
                                                        >Mini Bio Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mini_bio_label_${activeLanguageId}`"
                                                    :id="`mini_bio_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mini_bio_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mini_bio_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mini_bio_label.mini_bio_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mini_bio_label.mini_bio_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`govt_id_label_${activeLanguageId}`"
                                                        >Govt Id Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`govt_id_label_${activeLanguageId}`"
                                                    :id="`govt_id_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'govt_id_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'govt_id_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `govt_id_label.govt_id_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `govt_id_label.govt_id_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`govt_id_text_${activeLanguageId}`"
                                                        >Govt Id text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`govt_id_text_${activeLanguageId}`"
                                                    :id="`govt_id_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'govt_id_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'govt_id_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `govt_id_text.govt_id_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `govt_id_text.govt_id_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`image_placeholder_${activeLanguageId}`"
                                                        >Image Placeholder </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`image_placeholder_${activeLanguageId}`"
                                                    :id="`image_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'image_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'image_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `image_placeholder.image_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `image_placeholder.image_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`choose_file_placeholder_${activeLanguageId}`"
                                                        >Choose File Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`choose_file_placeholder_${activeLanguageId}`"
                                                    :id="`choose_file_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'choose_file_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'choose_file_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `choose_file_placeholder.choose_file_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `choose_file_placeholder.choose_file_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`image_option_placeholder_${activeLanguageId}`"
                                                        >Image Option Placheholder </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`image_option_placeholder_${activeLanguageId}`"
                                                    :id="`image_option_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'image_option_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'image_option_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `image_option_placeholder.image_option_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `image_option_placeholder.image_option_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                         <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`new_image_button_text_${activeLanguageId}`"
                                                        >New Image  Button Text </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`new_image_button_text_${activeLanguageId}`"
                                                    :id="`new_image_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'new_image_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'new_image_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `new_image_button_text.new_image_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `new_image_button_text.new_image_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reply_heading_label_${activeLanguageId}`"
                                                        >Reply Heading Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reply_heading_label_${activeLanguageId}`"
                                                    :id="`reply_heading_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reply_heading_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reply_heading_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reply_heading_label.reply_heading_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reply_heading_label.reply_heading_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reply_placeholder_${activeLanguageId}`"
                                                        >Reply Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reply_placeholder_${activeLanguageId}`"
                                                    :id="`reply_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reply_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reply_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reply_placeholder.reply_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reply_placeholder.reply_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`reply_submit_button_label_${activeLanguageId}`"
                                                        >Reply submit Button Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reply_submit_button_label_${activeLanguageId}`"
                                                    :id="`reply_submit_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reply_submit_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reply_submit_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reply_submit_button_label.reply_submit_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reply_submit_button_label.reply_submit_button_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`profile_label_${activeLanguageId}`"
                                                        >Profile Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`profile_label_${activeLanguageId}`"
                                                    :id="`profile_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'profile_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'profile_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `profile_label.profile_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `profile_label.profile_label_${activeLanguageId}`
                                                    )
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
                                                        >Save Button Text </label
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
                                                        :for="`vehicle_info_label_${activeLanguageId}`"
                                                        >Vehicle Info Label</label
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-edit-profile-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchEditProfilePageSetting && this.fetchEditProfilePageSetting(); }, 1000);
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
                            this.handleInput("", language, "min_bio_label");
                            this.handleInput("", language, "rides_taken_label");
                            this.handleInput("", language, "rides_taken_icon");
                            this.handleInput("", language, "passenger_driven_label");
                            this.handleInput("", language, "passenger_driven_icon");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "km_shared_label");
                            this.handleInput("", language, "km_shared_icon");
                            this.handleInput("", language, "mobile_indicate_field_label");
                            this.handleInput("", language, "review_label");
                            this.handleInput("", language, "reply_label");
                            this.handleInput("", language, "link_review_label");
                            this.handleInput("", language, "review_heading");
                            this.handleInput("", language, "edit_profile_text");
                            this.handleInput("", language, "first_name_label");
                            this.handleInput("", language, "first_name_placeholder");
                            this.handleInput("", language, "last_name_label");
                            this.handleInput("", language, "last_name_placeholder");
                            this.handleInput("", language, "gender_label");
                            this.handleInput("", language, "prefer_no_to_say_label");
                            this.handleInput("", language, "male_label");
                            this.handleInput("", language, "female_label");
                            this.handleInput("", language, "state_placeholder");
                            this.handleInput("", language, "dob_label");
                            this.handleInput("", language, "dob_placeholder");
                            this.handleInput("", language, "country_label");
                            this.handleInput("", language, "country_placeholder");
                            this.handleInput("", language, "state_label");
                            this.handleInput("", language, "city_label");
                            this.handleInput("", language, "city_placeholder");
                            this.handleInput("", language, "address_label");
                            this.handleInput("", language, "address_placeholder");
                            this.handleInput("", language, "zip_label");
                            this.handleInput("", language, "mini_bio_label");
                            this.handleInput("", language, "mini_bio_placeholder");
                            this.handleInput("", language, "govt_id_label");
                            this.handleInput("", language, "govt_id_text");
                            this.handleInput("", language, "image_placeholder");
                            this.handleInput("", language, "choose_file_placeholder");
                            this.handleInput("", language, "image_option_placeholder");
                            this.handleInput("", language, "new_image_button_text");
                            this.handleInput("", language, "save_button_text");
                            this.handleInput("", language, "reply_heading_label");
                            this.handleInput("", language, "reply_placeholder");
                            this.handleInput("", language, "reply_submit_button_label");
                            this.handleInput("", language, "profile_label");
                            this.handleInput("", language, "vehicle_info_label");
                        });
                        this.fetchProfilePageSetting();
                    }
                });
        },
        fetchProfilePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-edit-profile-setting`)
                .then((res) => {
                    console.log(res);
                    if (res?.data?.status == "Success") {
                        let edit_profile_page_setting_detail =
                            res?.data?.data?.edit_profile_page_setting_detail || [];
                        edit_profile_page_setting_detail.map((setting) => {

                            this.handleInput(
                                setting?.min_bio_label,
                                setting?.language,
                                "min_bio_label"
                            );
                            this.handleInput(
                                setting?.passenger_driven_label,
                                setting?.language,
                                "passenger_driven_label"
                            );
                            this.handleInput(
                                setting?.passenger_driven_icon,
                                setting?.language,
                                "passenger_driven_icon"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.rides_taken_label,
                                setting?.language,
                                "rides_taken_label"
                            );
                            this.handleInput(
                                setting?.rides_taken_icon,
                                setting?.language,
                                "rides_taken_icon"
                            );
                            this.handleInput(
                                setting?.km_shared_label,
                                setting?.language,
                                "km_shared_label"
                            );
                            this.handleInput(
                                setting?.km_shared_icon,
                                setting?.language,
                                "km_shared_icon"
                            );

                            this.handleInput(
                                setting?.review_label,
                                setting?.language,
                                "review_label"
                            );
                            this.handleInput(
                                setting?.reply_label,
                                setting?.language,
                                "reply_label"
                            );
                            this.handleInput(
                                setting?.link_review_label,
                                setting?.language,
                                "link_review_label"
                            );
                            this.handleInput(
                                setting?.review_heading,
                                setting?.language,
                                "review_heading"
                            );
                            this.handleInput(
                                setting?.edit_profile_text,
                                setting?.language,
                                "edit_profile_text"
                            );
                            this.handleInput(
                                setting?.first_name_label,
                                setting?.language,
                                "first_name_label"
                            );
                            this.handleInput(
                                setting?.first_name_placeholder,
                                setting?.language,
                                "first_name_placeholder"
                            );
                            this.handleInput(
                                setting?.last_name_label,
                                setting?.language,
                                "last_name_label"
                            );
                            this.handleInput(
                                setting?.last_name_placeholder,
                                setting?.language,
                                "last_name_placeholder"
                            );
                            this.handleInput(
                                setting?.gender_label,
                                setting?.language,
                                "gender_label"
                            );
                            this.handleInput(
                                setting?.male_label,
                                setting?.language,
                                "male_label"
                            );this.handleInput(
                                setting?.female_label,
                                setting?.language,
                                "female_label"
                            );this.handleInput(
                                setting?.prefer_no_to_say_label,
                                setting?.language,
                                "prefer_no_to_say_label"
                            );
                            this.handleInput(
                                setting?.state_placeholder,
                                setting?.language,
                                "state_placeholder"
                            );
                            this.handleInput(
                                setting?.dob_label,
                                setting?.language,
                                "dob_label"
                            );
                            this.handleInput(
                                setting?.dob_placeholder,
                                setting?.language,
                                "dob_placeholder"
                            );
                            this.handleInput(
                                setting?.country_label,
                                setting?.language,
                                "country_label"
                            );
                            this.handleInput(
                                setting?.country_placeholder,
                                setting?.language,
                                "country_placeholder"
                            );
                             this.handleInput(
                                setting?.state_label,
                                setting?.language,
                                "state_label"
                            );
                            this.handleInput(
                                setting?.city_label,
                                setting?.language,
                                "city_label"
                            );
                            this.handleInput(
                                setting?.city_placeholder,
                                setting?.language,
                                "city_placeholder"
                            );
                             this.handleInput(
                                setting?.address_label,
                                setting?.language,
                                "address_label"
                            );
                            this.handleInput(
                                setting?.address_placeholder,
                                setting?.language,
                                "address_placeholder"
                            );
                            this.handleInput(
                                setting?.zip_label,
                                setting?.language,
                                "zip_label"
                            );
                            this.handleInput(
                                setting?.mini_bio_label,
                                setting?.language,
                                "mini_bio_label"
                            );
                            this.handleInput(
                                setting?.mini_bio_placeholder,
                                setting?.language,
                                "mini_bio_placeholder"
                            );
                            this.handleInput(
                                setting?.govt_id_label,
                                setting?.language,
                                "govt_id_label"
                            );
                            this.handleInput(
                                setting?.govt_id_text,
                                setting?.language,
                                "govt_id_text"
                            );
                            this.handleInput(
                                setting?.image_placeholder,
                                setting?.language,
                                "image_placeholder"
                            );
                            this.handleInput(
                                setting?.choose_file_placeholder,
                                setting?.language,
                                "choose_file_placeholder"
                            );
                            this.handleInput(
                                setting?.image_option_placeholder,
                                setting?.language,
                                "image_option_placeholder"
                            );
                            this.handleInput(
                                setting?.new_image_button_text,
                                setting?.language,
                                "new_image_button_text"
                            );
                            this.handleInput(
                                setting?.save_button_text,
                                setting?.language,
                                "save_button_text"
                            );
                            this.handleInput(
                                setting?.reply_submit_button_label,
                                setting?.language,
                                "reply_submit_button_label"
                            );
                            this.handleInput(
                                setting?.reply_placeholder,
                                setting?.language,
                                "reply_placeholder"
                            );
                            this.handleInput(
                                setting?.reply_heading_label,
                                setting?.language,
                                "reply_heading_label"
                            );
                            this.handleInput(
                                setting?.profile_label,
                                setting?.language,
                                "profile_label"
                            );
                             this.handleInput(
                                setting?.vehicle_info_label,
                                setting?.language,
                                "vehicle_info_label"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-edit-profile-setting`,
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
                    `min_bio_label.min_bio_label_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_driven_label.passenger_driven_label_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_driven_icon.passenger_driven_icon_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `rides_taken_label.rides_taken_label_${language.id}`
                ) ||
                validationErros.has(
                    `rides_taken_icon.rides_taken_icon_${language.id}`
                ) ||
                validationErros.has(
                    `km_shared_label.km_shared_label_${language.id}`
                ) ||
                validationErros.has(
                    `km_shared_icon.km_shared_icon_${language.id}`
                ) ||
                validationErros.has(
                    `review_label.review_label_${language.id}`
                ) ||
                validationErros.has(
                    `reply_label.reply_label_${language.id}`
                ) ||
                validationErros.has(
                    `link_review_label.link_review_label_${language.id}`
                )||
                validationErros.has(
                    `review_heading.review_heading_${language.id}`
                )||
                validationErros.has(
                    `edit_profile_text.edit_profile_text_${language.id}`
                )||
                validationErros.has(
                    `first_name_label.first_name_label_${language.id}`
                )||
                validationErros.has(
                    `first_name_placeholder.first_name_placeholder_${language.id}`
                )||
                validationErros.has(
                    `last_name_label.last_name_label_${language.id}`
                )||
                validationErros.has(
                    `last_name_placeholder.last_name_placeholder_${language.id}`
                )||
                validationErros.has(
                    `gender_label.gender_label_${language.id}`
                )||
                validationErros.has(
                    `state_placeholder.state_placeholder_${language.id}`
                )||
                validationErros.has(
                    `dob_label.dob_label_${language.id}`
                )||
                validationErros.has(
                    `dob_placeholder.dob_placeholder_${language.id}`
                )||
                validationErros.has(
                    `country_label.country_label_${language.id}`
                )||
                validationErros.has(
                    `country_placeholder.country_placeholder_${language.id}`
                )||
                validationErros.has(
                    `state_label.state_label_${language.id}`
                )||
                validationErros.has(
                    `city_label.city_label_${language.id}`
                )||
                validationErros.has(
                    `city_placeholder.city_placeholder_${language.id}`
                )||
                validationErros.has(
                    `address_label.address_label_${language.id}`
                )||
                validationErros.has(
                    `address_placeholder.address_placeholder_${language.id}`
                )||
                validationErros.has(
                    `zip_label.zip_label_${language.id}`
                )||
                validationErros.has(
                    `mini_bio_label.mini_bio_label_${language.id}`
                )||
                validationErros.has(
                    `mini_bio_placeholder.mini_bio_placeholder_${language.id}`
                )||
                validationErros.has(
                    `govt_id_label.govt_id_label_${language.id}`
                )||
                validationErros.has(
                    `govt_id_text.govt_id_text_${language.id}`
                )||
                validationErros.has(
                    `image_placeholder.image_placeholder_${language.id}`
                )||
                validationErros.has(
                    `choose_file_placeholder.choose_file_placeholder_${language.id}`
                )||
                validationErros.has(
                    `image_option_placeholder.image_option_placeholder_${language.id}`
                )||
                validationErros.has(
                    `new_image_button_text.new_image_button_text_${language.id}`
                )||
                validationErros.has(
                    `save_button_text.save_button_text_${language.id}`
                )
            );
        },
    },
};
</script>
