<template>
    <AppLayout>
        <section class="trips-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    My trips page settings
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
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Trips page setting translations for a specific language.</p>

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
                                    <a :href="`${mixAdminApiUrl}download-trips-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
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
                                            collapseStates[1] =
                                            !collapseStates[1]
                                            ">
                                        <h3 class="text-white">
                                            Main section
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
                                                        :for="`passenger_trips_heading_${activeLanguageId}`">Passenger
                                                        trips heading</label>
                                                </div>
                                                <input type="text" :name="`passenger_trips_heading_${activeLanguageId}`"
                                                    :id="`passenger_trips_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'passenger_trips_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'passenger_trips_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `passenger_trips_heading.passenger_trips_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `passenger_trips_heading.passenger_trips_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_rides_heading_${activeLanguageId}`">Driver
                                                        rides heading</label>
                                                </div>
                                                <input type="text" :name="`driver_rides_heading_${activeLanguageId}`"
                                                    :id="`driver_rides_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_rides_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'driver_rides_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_rides_heading.driver_rides_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `driver_rides_heading.driver_rides_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`upcoming_label_${activeLanguageId}`">Upcoming
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`upcoming_label_${activeLanguageId}`"
                                                    :id="`upcoming_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'upcoming_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'upcoming_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `upcoming_label.upcoming_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `upcoming_label.upcoming_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_upcoming_trips_label_${activeLanguageId}`">No
                                                        upcoming trips label</label>
                                                </div>
                                                <input type="text" :name="`no_upcoming_trips_label_${activeLanguageId}`"
                                                    :id="`no_upcoming_trips_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_upcoming_trips_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_upcoming_trips_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_upcoming_trips_label.no_upcoming_trips_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_upcoming_trips_label.no_upcoming_trips_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_upcoming_rides_label_${activeLanguageId}`">No
                                                        upcoming rides label</label>
                                                </div>
                                                <input type="text" :name="`no_upcoming_rides_label_${activeLanguageId}`"
                                                    :id="`no_upcoming_rides_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_upcoming_rides_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_upcoming_rides_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_upcoming_rides_label.no_upcoming_rides_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_upcoming_rides_label.no_upcoming_rides_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`completed_label_${activeLanguageId}`">Completed
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`completed_label_${activeLanguageId}`"
                                                    :id="`completed_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'completed_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'completed_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `completed_label.completed_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `completed_label.completed_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_completed_trips_label_${activeLanguageId}`">No
                                                        completed trips label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`no_completed_trips_label_${activeLanguageId}`"
                                                    :id="`no_completed_trips_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_completed_trips_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_completed_trips_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_completed_trips_label.no_completed_trips_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_completed_trips_label.no_completed_trips_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_completed_rides_label_${activeLanguageId}`">No
                                                        completed rides label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`no_completed_rides_label_${activeLanguageId}`"
                                                    :id="`no_completed_rides_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_completed_rides_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_completed_rides_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_completed_rides_label.no_completed_rides_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_completed_rides_label.no_completed_rides_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancelled_label_${activeLanguageId}`">Cancelled
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`cancelled_label_${activeLanguageId}`"
                                                    :id="`cancelled_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'cancelled_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'cancelled_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `cancelled_label.cancelled_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `cancelled_label.cancelled_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_cancelled_trips_label_${activeLanguageId}`">No
                                                        cancelled trips label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`no_cancelled_trips_label_${activeLanguageId}`"
                                                    :id="`no_cancelled_trips_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_cancelled_trips_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_cancelled_trips_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_cancelled_trips_label.no_cancelled_trips_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_cancelled_trips_label.no_cancelled_trips_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_cancelled_rides_label_${activeLanguageId}`">No
                                                        cancelled rides label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`no_cancelled_rides_label_${activeLanguageId}`"
                                                    :id="`no_cancelled_rides_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_cancelled_rides_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_cancelled_rides_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_cancelled_rides_label.no_cancelled_rides_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_cancelled_rides_label.no_cancelled_rides_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`timeliness_label_${activeLanguageId}`">Timeliness
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`timeliness_label_${activeLanguageId}`"
                                                    :id="`timeliness_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('timeliness_label')"
                                                    @input="handleInput($event.target.value, language, 'timeliness_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('timeliness_label.timeliness_label_${activeLanguageId}')"
                                                v-text="validationErros.get('timeliness_label.timeliness_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`safety_label_${activeLanguageId}`">Safety
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`safety_label_${activeLanguageId}`"
                                                    :id="`safety_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('safety_label')"
                                                    @input="handleInput($event.target.value, language, 'safety_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('safety_label.safety_label_${activeLanguageId}')"
                                                v-text="validationErros.get('safety_label.safety_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`respect_and_courtesy_label_${activeLanguageId}`">Respect
                                                        and courtesy label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`respect_and_courtesy_label_${activeLanguageId}`"
                                                    :id="`respect_and_courtesy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('respect_and_courtesy_label')"
                                                    @input="handleInput($event.target.value, language, 'respect_and_courtesy_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('respect_and_courtesy_label.respect_and_courtesy_label_${activeLanguageId}')"
                                                v-text="validationErros.get('respect_and_courtesy_label.respect_and_courtesy_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`personal_hygiene_label_${activeLanguageId}`">Personal
                                                        hygiene label</label>
                                                </div>
                                                <input type="text" :name="`personal_hygiene_label_${activeLanguageId}`"
                                                    :id="`personal_hygiene_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('personal_hygiene_label')"
                                                    @input="handleInput($event.target.value, language, 'personal_hygiene_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('personal_hygiene_label.personal_hygiene_label_${activeLanguageId}')"
                                                v-text="validationErros.get('personal_hygiene_label.personal_hygiene_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`overall_attitude_label_${activeLanguageId}`">Overall
                                                        attitude label</label>
                                                </div>
                                                <input type="text" :name="`overall_attitude_label_${activeLanguageId}`"
                                                    :id="`overall_attitude_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('overall_attitude_label')"
                                                    @input="handleInput($event.target.value, language, 'overall_attitude_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('overall_attitude_label.overall_attitude_label_${activeLanguageId}')"
                                                v-text="validationErros.get('overall_attitude_label.overall_attitude_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`communication_label_${activeLanguageId}`">Communication
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`communication_label_${activeLanguageId}`"
                                                    :id="`communication_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('communication_label')"
                                                    @input="handleInput($event.target.value, language, 'communication_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('communication_label.communication_label_${activeLanguageId}')"
                                                v-text="validationErros.get('communication_label.communication_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`comfort_label_${activeLanguageId}`">Comfort
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`comfort_label_${activeLanguageId}`"
                                                    :id="`comfort_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('comfort_label')"
                                                    @input="handleInput($event.target.value, language, 'comfort_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('comfort_label.comfort_label_${activeLanguageId}')"
                                                v-text="validationErros.get('comfort_label.comfort_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`conscious_passenger_wellness_label_${activeLanguageId}`">Conscious
                                                        passenger wellness label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`conscious_passenger_wellness_label_${activeLanguageId}`"
                                                    :id="`conscious_passenger_wellness_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('conscious_passenger_wellness_label')"
                                                    @input="handleInput($event.target.value, language, 'conscious_passenger_wellness_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('conscious_passenger_wellness_label.conscious_passenger_wellness_label_${activeLanguageId}')"
                                                v-text="validationErros.get('conscious_passenger_wellness_label.conscious_passenger_wellness_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`condition_label_${activeLanguageId}`">Condition
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`condition_label_${activeLanguageId}`"
                                                    :id="`condition_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('condition_label')"
                                                    @input="handleInput($event.target.value, language, 'condition_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('condition_label.condition_label_${activeLanguageId}')"
                                                v-text="validationErros.get('condition_label.condition_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`review_criteria_label_${activeLanguageId}`">Review
                                                        criteria label</label>
                                                </div>
                                                <input type="text" :name="`review_criteria_label_${activeLanguageId}`"
                                                    :id="`review_criteria_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('review_criteria_label')"
                                                    @input="handleInput($event.target.value, language, 'review_criteria_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('review_criteria_label.review_criteria_label_${activeLanguageId}')"
                                                v-text="validationErros.get('review_criteria_label.review_criteria_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`main_heading_${activeLanguageId}`">Review Main
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`main_heading_${activeLanguageId}`"
                                                    :id="`main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('main_heading')"
                                                    @input="handleInput($event.target.value, language, 'main_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('main_heading.main_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('main_heading.main_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`average_label_${activeLanguageId}`">Average
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`average_label_${activeLanguageId}`"
                                                    :id="`average_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('average_label')"
                                                    @input="handleInput($event.target.value, language, 'average_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('average_label.average_label_${activeLanguageId}')"
                                                v-text="validationErros.get('average_label.average_label_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`load_more_trips_label_${activeLanguageId}`">Load more
                                                        trips label</label>
                                                </div>
                                                <input type="text" :name="`load_more_trips_label_${activeLanguageId}`"
                                                    :id="`load_more_trips_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('load_more_trips_label')"
                                                    @input="handleInput($event.target.value, language, 'load_more_trips_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('load_more_trips_label.load_more_trips_label_${activeLanguageId}')"
                                                v-text="validationErros.get('load_more_trips_label.load_more_trips_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_more_data_message_${activeLanguageId}`">No more
                                                        data message</label>
                                                </div>
                                                <input type="text" :name="`no_more_data_message_${activeLanguageId}`"
                                                    :id="`no_more_data_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('no_more_data_message')"
                                                    @input="handleInput($event.target.value, language, 'no_more_data_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('no_more_data_message.no_more_data_message_${activeLanguageId}')"
                                                v-text="validationErros.get('no_more_data_message.no_more_data_message_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`load_more_rides_label_${activeLanguageId}`">Load more
                                                        rides label</label>
                                                </div>
                                                <input type="text" :name="`load_more_rides_label_${activeLanguageId}`"
                                                    :id="`load_more_rides_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('load_more_rides_label')"
                                                    @input="handleInput($event.target.value, language, 'load_more_rides_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('load_more_rides_label.load_more_rides_label_${activeLanguageId}')"
                                                v-text="validationErros.get('load_more_rides_label.load_more_rides_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`review_passengers_review_label_${activeLanguageId}`">Review
                                                        passengers review label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`review_passengers_review_label_${activeLanguageId}`"
                                                    :id="`review_passengers_review_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('review_passengers_review_label')"
                                                    @input="handleInput($event.target.value, language, 'review_passengers_review_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('review_passengers_review_label.review_passengers_review_label_${activeLanguageId}')"
                                                v-text="validationErros.get('review_passengers_review_label.review_passengers_review_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`review_passengers_i_review_label_${activeLanguageId}`">Review
                                                        passengers I review label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`review_passengers_i_review_label_${activeLanguageId}`"
                                                    :id="`review_passengers_i_review_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('review_passengers_i_review_label')"
                                                    @input="handleInput($event.target.value, language, 'review_passengers_i_review_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('review_passengers_i_review_label.review_passengers_i_review_label_${activeLanguageId}')"
                                                v-text="validationErros.get('review_passengers_i_review_label.review_passengers_i_review_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`review_passengers_heading_${activeLanguageId}`">Review
                                                        passengers heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`review_passengers_heading_${activeLanguageId}`"
                                                    :id="`review_passengers_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('review_passengers_heading')"
                                                    @input="handleInput($event.target.value, language, 'review_passengers_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('review_passengers_heading.review_passengers_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('review_passengers_heading.review_passengers_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_cancel_ride_btn_label_${activeLanguageId}`">Passenger
                                                        cancel ride button label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_cancel_ride_btn_label_${activeLanguageId}`"
                                                    :id="`passenger_cancel_ride_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('passenger_cancel_ride_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'passenger_cancel_ride_btn_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_cancel_ride_btn_label.passenger_cancel_ride_btn_label_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_cancel_ride_btn_label.passenger_cancel_ride_btn_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`booking_cancel_btn_label_${activeLanguageId}`">Booking
                                                        cancel button label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`booking_cancel_btn_label_${activeLanguageId}`"
                                                    :id="`booking_cancel_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('booking_cancel_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'booking_cancel_btn_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('booking_cancel_btn_label.booking_cancel_btn_label_${activeLanguageId}')"
                                                v-text="validationErros.get('booking_cancel_btn_label.booking_cancel_btn_label_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_trip_placeholder_${activeLanguageId}`">Cancel
                                                        booking trip placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancel_booking_trip_placeholder_${activeLanguageId}`"
                                                    :id="`cancel_booking_trip_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_trip_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_trip_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_trip_placeholder.cancel_booking_trip_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_trip_placeholder.cancel_booking_trip_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_ride_label_${activeLanguageId}`">Cancel
                                                        ride label</label>
                                                </div>
                                                <input type="text" :name="`cancel_ride_label_${activeLanguageId}`"
                                                    :id="`cancel_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_ride_label')"
                                                    @input="handleInput($event.target.value, language, 'cancel_ride_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_ride_label.cancel_ride_label_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_ride_label.cancel_ride_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_ride_placeholder_${activeLanguageId}`">Cancel
                                                        ride placeholder</label>
                                                </div>
                                                <input type="text" :name="`cancel_ride_placeholder_${activeLanguageId}`"
                                                    :id="`cancel_ride_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_ride_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'cancel_ride_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_ride_placeholder.cancel_ride_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_ride_placeholder.cancel_ride_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_seat_label_${activeLanguageId}`">Cancel seat
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`cancel_seat_label_${activeLanguageId}`"
                                                    :id="`cancel_seat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_seat_label')"
                                                    @input="handleInput($event.target.value, language, 'cancel_seat_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_seat_label.cancel_seat_label_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_seat_label.cancel_seat_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`number_of_seat_booked_${activeLanguageId}`">Number of
                                                        seat booked</label>
                                                </div>
                                                <input type="text" :name="`number_of_seat_booked_${activeLanguageId}`"
                                                    :id="`number_of_seat_booked_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('number_of_seat_booked')"
                                                    @input="handleInput($event.target.value, language, 'number_of_seat_booked')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('number_of_seat_booked.number_of_seat_booked_${activeLanguageId}')"
                                                v-text="validationErros.get('number_of_seat_booked.number_of_seat_booked_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_all_feilds_are_required_${activeLanguageId}`">Cancel page all feilds are required</label>
                                                </div>
                                                <input type="text" :name="`cancel_all_feilds_are_required_${activeLanguageId}`"
                                                    :id="`cancel_all_feilds_are_required_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_all_feilds_are_required')"
                                                    @input="handleInput($event.target.value, language, 'cancel_all_feilds_are_required')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_all_feilds_are_required.cancel_all_feilds_are_required_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_all_feilds_are_required.cancel_all_feilds_are_required_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_booking_heading_${activeLanguageId}`">Cancel
                                                        booking heading</label>
                                                </div>
                                                <input type="text" :name="`cancel_booking_heading_${activeLanguageId}`"
                                                    :id="`cancel_booking_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_booking_heading')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_heading.cancel_booking_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_heading.cancel_booking_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_main_heading_${activeLanguageId}`">Cancel
                                                        booking main heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancel_booking_main_heading_${activeLanguageId}`"
                                                    :id="`cancel_booking_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_main_heading')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_main_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_main_heading.cancel_booking_main_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_main_heading.cancel_booking_main_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_ride_setting_${activeLanguageId}`">Cancel ride
                                                        setting</label>
                                                </div>
                                                <input type="text" :name="`cancel_ride_setting_${activeLanguageId}`"
                                                    :id="`cancel_ride_setting_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_ride_setting')"
                                                    @input="handleInput($event.target.value, language, 'cancel_ride_setting')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_ride_setting.cancel_ride_setting_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_ride_setting.cancel_ride_setting_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`tell_passenger_why_label_${activeLanguageId}`">Tell your passenger why label</label>
                                                </div>
                                                <input type="text" :name="`tell_passenger_why_label_${activeLanguageId}`"
                                                    :id="`tell_passenger_why_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('tell_passenger_why_label')"
                                                    @input="handleInput($event.target.value, language, 'tell_passenger_why_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('tell_passenger_why_label.tell_passenger_why_label_${activeLanguageId}')"
                                                v-text="validationErros.get('tell_passenger_why_label.tell_passenger_why_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`tell_passenger_why_placeholder_${activeLanguageId}`">Tell your passenger why placeholder</label>
                                                </div>
                                                <input type="text" :name="`tell_passenger_why_placeholder_${activeLanguageId}`"
                                                    :id="`tell_passenger_why_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('tell_passenger_why_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'tell_passenger_why_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('tell_passenger_why_placeholder.tell_passenger_why_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('tell_passenger_why_placeholder.tell_passenger_why_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`Confirm_cancel_ride_${activeLanguageId}`">I confirm that I want to cancel this ride label</label>
                                                </div>
                                                <input type="text" :name="`Confirm_cancel_ride_${activeLanguageId}`"
                                                    :id="`Confirm_cancel_ride_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('Confirm_cancel_ride')"
                                                    @input="handleInput($event.target.value, language, 'Confirm_cancel_ride')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('Confirm_cancel_ride.Confirm_cancel_ride_${activeLanguageId}')"
                                                v-text="validationErros.get('Confirm_cancel_ride.Confirm_cancel_ride_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`remove_from_this_ride_message_${activeLanguageId}`">Remove
                                                        from this ride message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`remove_from_this_ride_message_${activeLanguageId}`"
                                                    :id="`remove_from_this_ride_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('remove_from_this_ride_message')"
                                                    @input="handleInput($event.target.value, language, 'remove_from_this_ride_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_from_this_ride_message.remove_from_this_ride_message_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_from_this_ride_message.remove_from_this_ride_message_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`remove_passenger_and_block_message_${activeLanguageId}`">Remove
                                                        passenger and block message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`remove_passenger_and_block_message_${activeLanguageId}`"
                                                    :id="`remove_passenger_and_block_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('remove_passenger_and_block_message')"
                                                    @input="handleInput($event.target.value, language, 'remove_passenger_and_block_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_passenger_and_block_message.remove_passenger_and_block_message_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_passenger_and_block_message.remove_passenger_and_block_message_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`remove_day_label_${activeLanguageId}`">Remove day
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`remove_day_label_${activeLanguageId}`"
                                                    :id="`remove_day_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('remove_day_label')"
                                                    @input="handleInput($event.target.value, language, 'remove_day_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_day_label.remove_day_label_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_day_label.remove_day_label_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`remove_day_error_${activeLanguageId}`">Remove day
                                                        error</label>
                                                </div>
                                                <input type="text" :name="`remove_day_error_${activeLanguageId}`"
                                                    :id="`remove_day_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('remove_day_error')"
                                                    @input="handleInput($event.target.value, language, 'remove_day_error')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_day_error.remove_day_error_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_day_error.remove_day_error_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_remove_reason_placeholder_${activeLanguageId}`">Driver
                                                        remove reason placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`driver_remove_reason_placeholder_${activeLanguageId}`"
                                                    :id="`driver_remove_reason_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('driver_remove_reason_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'driver_remove_reason_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('driver_remove_reason_placeholder.driver_remove_reason_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('driver_remove_reason_placeholder.driver_remove_reason_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_remove_reason_placeholder_${activeLanguageId}`">Passenger
                                                        remove reason placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_remove_reason_placeholder_${activeLanguageId}`"
                                                    :id="`passenger_remove_reason_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('passenger_remove_reason_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'passenger_remove_reason_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_remove_reason_placeholder.passenger_remove_reason_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_remove_reason_placeholder.passenger_remove_reason_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_review_heading_${activeLanguageId}`">Passenger
                                                        review heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_review_heading_${activeLanguageId}`"
                                                    :id="`passenger_review_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('passenger_review_heading')"
                                                    @input="handleInput($event.target.value, language, 'passenger_review_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_review_heading.passenger_review_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_review_heading.passenger_review_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_review_heading_${activeLanguageId}`">Driver
                                                        review heading</label>
                                                </div>
                                                <input type="text" :name="`driver_review_heading_${activeLanguageId}`"
                                                    :id="`driver_review_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('driver_review_heading')"
                                                    @input="handleInput($event.target.value, language, 'driver_review_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('driver_review_heading.driver_review_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('driver_review_heading.driver_review_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_review_placeholder_${activeLanguageId}`">Passenger
                                                        review placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_review_placeholder_${activeLanguageId}`"
                                                    :id="`passenger_review_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('passenger_review_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'passenger_review_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_review_placeholder.passenger_review_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_review_placeholder.passenger_review_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_review_placeholder_${activeLanguageId}`">Driver
                                                        review placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`driver_review_placeholder_${activeLanguageId}`"
                                                    :id="`driver_review_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('driver_review_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'driver_review_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('driver_review_placeholder.driver_review_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('driver_review_placeholder.driver_review_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`review_submit_btn_label_${activeLanguageId}`">Review
                                                        submit button label</label>
                                                </div>
                                                <input type="text" :name="`review_submit_btn_label_${activeLanguageId}`"
                                                    :id="`review_submit_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('review_submit_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'review_submit_btn_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('review_submit_btn_label.review_submit_btn_label_${activeLanguageId}')"
                                                v-text="validationErros.get('review_submit_btn_label.review_submit_btn_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`remove_passenger_heading_${activeLanguageId}`">Remove
                                                        passenger heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`remove_passenger_heading_${activeLanguageId}`"
                                                    :id="`remove_passenger_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('remove_passenger_heading')"
                                                    @input="handleInput($event.target.value, language, 'remove_passenger_heading')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_passenger_heading.remove_passenger_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_passenger_heading.remove_passenger_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`remove_passenger_text_${activeLanguageId}`">Remove
                                                        passenger text</label>
                                                </div>
                                                <input type="text" :name="`remove_passenger_text_${activeLanguageId}`"
                                                    :id="`remove_passenger_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('remove_passenger_text')"
                                                    @input="handleInput($event.target.value, language, 'remove_passenger_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_passenger_text.remove_passenger_text_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_passenger_text.remove_passenger_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`block_temporarily_label_${activeLanguageId}`">Block
                                                        temporarily label</label>
                                                </div>
                                                <input type="text" :name="`block_temporarily_label_${activeLanguageId}`"
                                                    :id="`block_temporarily_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('block_temporarily_label')"
                                                    @input="handleInput($event.target.value, language, 'block_temporarily_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('block_temporarily_label.block_temporarily_label_${activeLanguageId}')"
                                                v-text="validationErros.get('block_temporarily_label.block_temporarily_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`block_permanently_label_${activeLanguageId}`">Block
                                                        permanently label</label>
                                                </div>
                                                <input type="text" :name="`block_permanently_label_${activeLanguageId}`"
                                                    :id="`block_permanently_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('block_permanently_label')"
                                                    @input="handleInput($event.target.value, language, 'block_permanently_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('block_permanently_label.block_permanently_label_${activeLanguageId}')"
                                                v-text="validationErros.get('block_permanently_label.block_permanently_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`remove_day_placeholder_${activeLanguageId}`">Remove
                                                        day placeholder</label>
                                                </div>
                                                <input type="text" :name="`remove_day_placeholder_${activeLanguageId}`"
                                                    :id="`remove_day_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('remove_day_placeholder')"
                                                    @input="handleInput($event.target.value, language, 'remove_day_placeholder')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('remove_day_placeholder.remove_day_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('remove_day_placeholder.remove_day_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_remove_reason_label_${activeLanguageId}`">Driver
                                                        remove reason label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`driver_remove_reason_label_${activeLanguageId}`"
                                                    :id="`driver_remove_reason_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('driver_remove_reason_label')"
                                                    @input="handleInput($event.target.value, language, 'driver_remove_reason_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('driver_remove_reason_label.driver_remove_reason_label_${activeLanguageId}')"
                                                v-text="validationErros.get('driver_remove_reason_label.driver_remove_reason_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_remove_reason_error_${activeLanguageId}`">Driver
                                                        remove reason error</label>
                                                </div>
                                                <input type="text"
                                                    :name="`driver_remove_reason_error_${activeLanguageId}`"
                                                    :id="`driver_remove_reason_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('driver_remove_reason_error')"
                                                    @input="handleInput($event.target.value, language, 'driver_remove_reason_error')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('driver_remove_reason_error.driver_remove_reason_error_${activeLanguageId}')"
                                                v-text="validationErros.get('driver_remove_reason_error.driver_remove_reason_error_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_remove_reason_label_${activeLanguageId}`">Passenger
                                                        remove reason label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_remove_reason_label_${activeLanguageId}`"
                                                    :id="`passenger_remove_reason_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('passenger_remove_reason_label')"
                                                    @input="handleInput($event.target.value, language, 'passenger_remove_reason_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_remove_reason_label.passenger_remove_reason_label_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_remove_reason_label.passenger_remove_reason_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_remove_reason_error_${activeLanguageId}`">Passenger
                                                        remove reason error</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_remove_reason_error_${activeLanguageId}`"
                                                    :id="`passenger_remove_reason_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('passenger_remove_reason_error')"
                                                    @input="handleInput($event.target.value, language, 'passenger_remove_reason_error')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_remove_reason_error.passenger_remove_reason_error_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_remove_reason_error.passenger_remove_reason_error_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`passenger_cancel_sure_message_${activeLanguageId}`">Passenger
                                                        cancel sure message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`passenger_cancel_sure_message_${activeLanguageId}`"
                                                    :id="`passenger_cancel_sure_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('passenger_cancel_sure_message')"
                                                    @input="handleInput($event.target.value, language, 'passenger_cancel_sure_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('passenger_cancel_sure_message.passenger_cancel_sure_message_${activeLanguageId}')"
                                                v-text="validationErros.get('passenger_cancel_sure_message.passenger_cancel_sure_message_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancel_message_title_${activeLanguageId}`">Cancel
                                                        message title</label>
                                                </div>
                                                <input type="text" :name="`cancel_message_title_${activeLanguageId}`"
                                                    :id="`cancel_message_title_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancel_message_title')"
                                                    @input="handleInput($event.target.value, language, 'cancel_message_title')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_message_title.cancel_message_title_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_message_title.cancel_message_title_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_confirm_message_${activeLanguageId}`">Cancel
                                                        booking confirm message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancel_booking_confirm_message_${activeLanguageId}`"
                                                    :id="`cancel_booking_confirm_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_confirm_message')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_confirm_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_confirm_message.cancel_booking_confirm_message_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_confirm_message.cancel_booking_confirm_message_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`booking_cancel_btn_yes_label_${activeLanguageId}`">Booking
                                                        cancel btn yes label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`booking_cancel_btn_yes_label_${activeLanguageId}`"
                                                    :id="`booking_cancel_btn_yes_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('booking_cancel_btn_yes_label')"
                                                    @input="handleInput($event.target.value, language, 'booking_cancel_btn_yes_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('booking_cancel_btn_yes_label.booking_cancel_btn_yes_label_${activeLanguageId}')"
                                                v-text="validationErros.get('booking_cancel_btn_yes_label.booking_cancel_btn_yes_label_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`booking_cancel_btn_no_label_${activeLanguageId}`">Booking
                                                        cancel btn no label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`booking_cancel_btn_no_label_${activeLanguageId}`"
                                                    :id="`booking_cancel_btn_no_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('booking_cancel_btn_no_label')"
                                                    @input="handleInput($event.target.value, language, 'booking_cancel_btn_no_label')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('booking_cancel_btn_no_label.booking_cancel_btn_no_label_${activeLanguageId}')"
                                                v-text="validationErros.get('booking_cancel_btn_no_label.booking_cancel_btn_no_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_confirm_firm_message_${activeLanguageId}`">Cancel
                                                        booking confirm firm message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancel_booking_confirm_firm_message_${activeLanguageId}`"
                                                    :id="`cancel_booking_confirm_firm_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_confirm_firm_message')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_confirm_firm_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_confirm_firm_message.cancel_booking_confirm_firm_message_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_confirm_firm_message.cancel_booking_confirm_firm_message_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_confirm_48_hour_message_${activeLanguageId}`">Cancel
                                                        booking confirm 48 hour message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancel_booking_confirm_48_hour_message_${activeLanguageId}`"
                                                    :id="`cancel_booking_confirm_48_hour_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_confirm_48_hour_message')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_confirm_48_hour_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_confirm_48_hour_message.cancel_booking_confirm_48_hour_message_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_confirm_48_hour_message.cancel_booking_confirm_48_hour_message_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_confirm_12_to_48_hour_message_${activeLanguageId}`">Cancel
                                                        booking confirm 12 to 48 hour message</label>
                                                </div>
                                                <editor @selectionChange="
                                                    handleSelectionChange(
                                                        language,
                                                        'cancel_booking_confirm_12_to_48_hour_message'
                                                    )
                                                    "
                                                    :ref="`cancel_booking_confirm_12_to_48_hour_message_${language.id}`"
                                                    :id="`cancel_booking_confirm_12_to_48_hour_message_${language.id}`"
                                                    :initial-value="form[
                                                        `cancel_booking_confirm_12_to_48_hour_message`
                                                        ][
                                                        `cancel_booking_confirm_12_to_48_hour_message_${language?.id}`
                                                        ]
                                                        " :tinymce-script-src="tinymceScriptSrc" :init="editorConfig" />
                                                <!-- <input
                                                    type="text"
                                                    :name="`cancel_booking_confirm_12_to_48_hour_message_${activeLanguageId}`"
                                                    :id="`cancel_booking_confirm_12_to_48_hour_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_confirm_12_to_48_hour_message')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_confirm_12_to_48_hour_message')"
                                                /> -->
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_confirm_12_to_48_hour_message.cancel_booking_confirm_12_to_48_hour_message_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_confirm_12_to_48_hour_message.cancel_booking_confirm_12_to_48_hour_message_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancel_booking_confirm_less_12_hour_message_${activeLanguageId}`">Cancel
                                                        booking confirm less 12 hour message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancel_booking_confirm_less_12_hour_message_${activeLanguageId}`"
                                                    :id="`cancel_booking_confirm_less_12_hour_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancel_booking_confirm_less_12_hour_message')"
                                                    @input="handleInput($event.target.value, language, 'cancel_booking_confirm_less_12_hour_message')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancel_booking_confirm_less_12_hour_message.cancel_booking_confirm_less_12_hour_message_${activeLanguageId}')"
                                                v-text="validationErros.get('cancel_booking_confirm_less_12_hour_message.cancel_booking_confirm_less_12_hour_message_${activeLanguageId}')">
                                            </p>
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
                bullist numlist outdent indent | removeformat | table | image | code | help",
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-trips-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (response?.data?.status === 'Success') {
                    window.helper.swalSuccessMessage(response.data.message);
                    this.excelForm.selectedLanguageId = '';
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    setTimeout(() => { this.fetchTermsOfUsePageSetting && this.fetchTermsOfUsePageSetting(); }, 1000);
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
                            this.handleInput("", language, "passenger_trips_heading");
                            this.handleInput("", language, "driver_rides_heading");
                            this.handleInput("", language, "upcoming_label");
                            this.handleInput("", language, "no_upcoming_trips_label");
                            this.handleInput("", language, "no_upcoming_rides_label");
                            this.handleInput("", language, "completed_label");
                            this.handleInput("", language, "no_completed_trips_label");
                            this.handleInput("", language, "no_completed_rides_label");
                            this.handleInput("", language, "cancelled_label");
                            this.handleInput("", language, "no_cancelled_trips_label");
                            this.handleInput("", language, "no_cancelled_rides_label");
                            this.handleInput("", language, "timeliness_label");
                            this.handleInput("", language, "safety_label");
                            this.handleInput("", language, "respect_and_courtesy_label");
                            this.handleInput("", language, "personal_hygiene_label");
                            this.handleInput("", language, "overall_attitude_label");
                            this.handleInput("", language, "communication_label");
                            this.handleInput("", language, "comfort_label");
                            this.handleInput("", language, "conscious_passenger_wellness_label");
                            this.handleInput("", language, "condition_label");
                            this.handleInput("", language, "review_criteria_label");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "average_label");
                            this.handleInput("", language, "load_more_trips_label");
                            this.handleInput("", language, "no_more_data_message");
                            this.handleInput("", language, "load_more_rides_label");
                            this.handleInput("", language, "review_passengers_review_label");
                            this.handleInput("", language, "review_passengers_i_review_label");
                            this.handleInput("", language, "review_passengers_heading");
                            this.handleInput("", language, "passenger_cancel_ride_btn_label");
                            this.handleInput("", language, "booking_cancel_btn_label");
                            this.handleInput("", language, "cancel_booking_trip_placeholder");
                            this.handleInput("", language, "cancel_ride_label");
                            this.handleInput("", language, "cancel_ride_placeholder");
                            this.handleInput("", language, "cancel_seat_label");
                            this.handleInput("", language, "number_of_seat_booked");
                            this.handleInput("", language, "cancel_all_feilds_are_required");
                            this.handleInput("", language, "cancel_booking_heading");
                            this.handleInput("", language, "cancel_booking_main_heading");
                            this.handleInput("", language, "cancel_ride_setting");
                            this.handleInput("", language, "tell_passenger_why_label");
                            this.handleInput("", language, "tell_passenger_why_placeholder");
                            this.handleInput("", language, "Confirm_cancel_ride");
                            this.handleInput("", language, "remove_from_this_ride_message");
                            this.handleInput("", language, "remove_passenger_and_block_message");
                            this.handleInput("", language, "remove_day_label");
                            this.handleInput("", language, "remove_day_error");
                            this.handleInput("", language, "driver_remove_reason_placeholder");
                            this.handleInput("", language, "passenger_remove_reason_placeholder");
                            this.handleInput("", language, "passenger_review_heading");
                            this.handleInput("", language, "driver_review_heading");
                            this.handleInput("", language, "passenger_review_placeholder");
                            this.handleInput("", language, "driver_review_placeholder");
                            this.handleInput("", language, "review_submit_btn_label");
                            this.handleInput("", language, "remove_passenger_heading");
                            this.handleInput("", language, "remove_passenger_text");
                            this.handleInput("", language, "block_temporarily_label");
                            this.handleInput("", language, "block_permanently_label");
                            this.handleInput("", language, "remove_day_placeholder");
                            this.handleInput("", language, "driver_remove_reason_label");
                            this.handleInput("", language, "driver_remove_reason_error");
                            this.handleInput("", language, "passenger_remove_reason_label");
                            this.handleInput("", language, "passenger_remove_reason_error");
                            this.handleInput("", language, "passenger_cancel_sure_message");
                            this.handleInput("", language, "cancel_message_title");
                            this.handleInput("", language, "cancel_booking_confirm_message");
                            this.handleInput("", language, "booking_cancel_btn_yes_label");
                            this.handleInput("", language, "booking_cancel_btn_no_label");
                            this.handleInput("", language, "cancel_booking_confirm_firm_message");
                            this.handleInput("", language, "cancel_booking_confirm_48_hour_message");
                            this.handleInput("", language, "cancel_booking_confirm_12_to_48_hour_message");
                            this.handleInput("", language, "cancel_booking_confirm_less_12_hour_message");

                        });
                        this.fetchTermsOfUsePageSetting();
                    }
                });
        },
        fetchTermsOfUsePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-trips-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let trips_page_setting_detail =
                            res?.data?.data?.trips_page_setting_detail || [];
                        trips_page_setting_detail.map((setting) => {
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
                                setting?.passenger_trips_heading,
                                setting?.language,
                                "passenger_trips_heading"
                            );
                            this.handleInput(
                                setting?.driver_rides_heading,
                                setting?.language,
                                "driver_rides_heading"
                            );
                            this.handleInput(
                                setting?.upcoming_label,
                                setting?.language,
                                "upcoming_label"
                            );
                            this.handleInput(
                                setting?.no_upcoming_trips_label,
                                setting?.language,
                                "no_upcoming_trips_label"
                            );
                            this.handleInput(
                                setting?.no_upcoming_rides_label,
                                setting?.language,
                                "no_upcoming_rides_label"
                            );
                            this.handleInput(
                                setting?.completed_label,
                                setting?.language,
                                "completed_label"
                            );
                            this.handleInput(
                                setting?.no_completed_trips_label,
                                setting?.language,
                                "no_completed_trips_label"
                            );
                            this.handleInput(
                                setting?.no_completed_rides_label,
                                setting?.language,
                                "no_completed_rides_label"
                            );
                            this.handleInput(
                                setting?.cancelled_label,
                                setting?.language,
                                "cancelled_label"
                            );
                            this.handleInput(
                                setting?.no_cancelled_trips_label,
                                setting?.language,
                                "no_cancelled_trips_label"
                            );
                            this.handleInput(
                                setting?.no_cancelled_rides_label,
                                setting?.language,
                                "no_cancelled_rides_label"
                            );
                            this.handleInput(setting?.timeliness_label, setting?.language, "timeliness_label");
                            this.handleInput(setting?.safety_label, setting?.language, "safety_label");
                            this.handleInput(setting?.respect_and_courtesy_label, setting?.language, "respect_and_courtesy_label");
                            this.handleInput(setting?.personal_hygiene_label, setting?.language, "personal_hygiene_label");
                            this.handleInput(setting?.overall_attitude_label, setting?.language, "overall_attitude_label");
                            this.handleInput(setting?.communication_label, setting?.language, "communication_label");
                            this.handleInput(setting?.comfort_label, setting?.language, "comfort_label");
                            this.handleInput(setting?.conscious_passenger_wellness_label, setting?.language, "conscious_passenger_wellness_label");
                            this.handleInput(setting?.condition_label, setting?.language, "condition_label");
                            this.handleInput(setting?.review_criteria_label, setting?.language, "review_criteria_label");
                            this.handleInput(setting?.main_heading, setting?.language, "main_heading");
                            this.handleInput(setting?.average_label, setting?.language, "average_label");
                            this.handleInput(setting?.load_more_trips_label, setting?.language, "load_more_trips_label");
                            this.handleInput(setting?.no_more_data_message, setting?.language, "no_more_data_message");
                            this.handleInput(setting?.load_more_rides_label, setting?.language, "load_more_rides_label");
                            this.handleInput(setting?.review_passengers_review_label, setting?.language, "review_passengers_review_label");
                            this.handleInput(setting?.review_passengers_i_review_label, setting?.language, "review_passengers_i_review_label");
                            this.handleInput(setting?.review_passengers_heading, setting?.language, "review_passengers_heading");
                            this.handleInput(setting?.passenger_cancel_ride_btn_label, setting?.language, "passenger_cancel_ride_btn_label");
                            this.handleInput(setting?.booking_cancel_btn_label, setting?.language, "booking_cancel_btn_label");
                            this.handleInput(setting?.cancel_booking_trip_placeholder, setting?.language, "cancel_booking_trip_placeholder");
                            this.handleInput(setting?.cancel_ride_label, setting?.language, "cancel_ride_label");
                            this.handleInput(setting?.cancel_ride_placeholder, setting?.language, "cancel_ride_placeholder");
                            this.handleInput(setting?.cancel_seat_label, setting?.language, "cancel_seat_label");
                            this.handleInput(setting?.number_of_seat_booked, setting?.language, "number_of_seat_booked");
                            this.handleInput(setting?.cancel_all_feilds_are_required, setting?.language, "cancel_all_feilds_are_required");
                            this.handleInput(setting?.cancel_booking_heading, setting?.language, "cancel_booking_heading");
                            this.handleInput(setting?.cancel_booking_main_heading, setting?.language, "cancel_booking_main_heading");
                            this.handleInput(setting?.cancel_ride_setting, setting?.language, "cancel_ride_setting");
                            this.handleInput(setting?.tell_passenger_why_label, setting?.language, "tell_passenger_why_label");
                            this.handleInput(setting?.tell_passenger_why_placeholder, setting?.language, "tell_passenger_why_placeholder");
                            this.handleInput(setting?.Confirm_cancel_ride, setting?.language, "Confirm_cancel_ride");
                            this.handleInput(setting?.remove_from_this_ride_message, setting?.language, "remove_from_this_ride_message");
                            this.handleInput(setting?.remove_passenger_and_block_message, setting?.language, "remove_passenger_and_block_message");
                            this.handleInput(setting?.remove_day_label, setting?.language, "remove_day_label");
                            this.handleInput(setting?.remove_day_error, setting?.language, "remove_day_error");
                            this.handleInput(setting?.driver_remove_reason_placeholder, setting?.language, "driver_remove_reason_placeholder");
                            this.handleInput(setting?.passenger_remove_reason_placeholder, setting?.language, "passenger_remove_reason_placeholder");
                            this.handleInput(setting?.passenger_review_heading, setting?.language, "passenger_review_heading");
                            this.handleInput(setting?.driver_review_heading, setting?.language, "driver_review_heading");
                            this.handleInput(setting?.passenger_review_placeholder, setting?.language, "passenger_review_placeholder");
                            this.handleInput(setting?.driver_review_placeholder, setting?.language, "driver_review_placeholder");
                            this.handleInput(setting?.review_submit_btn_label, setting?.language, "review_submit_btn_label");
                            this.handleInput(setting?.remove_passenger_heading, setting?.language, "remove_passenger_heading");
                            this.handleInput(setting?.remove_passenger_text, setting?.language, "remove_passenger_text");
                            this.handleInput(setting?.block_temporarily_label, setting?.language, "block_temporarily_label");
                            this.handleInput(setting?.block_permanently_label, setting?.language, "block_permanently_label");
                            this.handleInput(setting?.remove_day_placeholder, setting?.language, "remove_day_placeholder");
                            this.handleInput(setting?.driver_remove_reason_label, setting?.language, "driver_remove_reason_label");
                            this.handleInput(setting?.driver_remove_reason_error, setting?.language, "driver_remove_reason_error");
                            this.handleInput(setting?.passenger_remove_reason_label, setting?.language, "passenger_remove_reason_label");
                            this.handleInput(setting?.passenger_remove_reason_error, setting?.language, "passenger_remove_reason_error");
                            this.handleInput(setting?.passenger_cancel_sure_message, setting?.language, "passenger_cancel_sure_message");
                            this.handleInput(setting?.cancel_message_title, setting?.language, "cancel_message_title");
                            this.handleInput(setting?.cancel_booking_confirm_message, setting?.language, "cancel_booking_confirm_message");
                            this.handleInput(setting?.booking_cancel_btn_yes_label, setting?.language, "booking_cancel_btn_yes_label");
                            this.handleInput(setting?.booking_cancel_btn_no_label, setting?.language, "booking_cancel_btn_no_label");
                            this.handleInput(setting?.cancel_booking_confirm_firm_message, setting?.language, "cancel_booking_confirm_firm_message");
                            this.handleInput(setting?.cancel_booking_confirm_48_hour_message, setting?.language, "cancel_booking_confirm_48_hour_message");
                            this.handleInput(setting?.cancel_booking_confirm_12_to_48_hour_message, setting?.language, "cancel_booking_confirm_12_to_48_hour_message");
                            this.handleInput(setting?.cancel_booking_confirm_less_12_hour_message, setting?.language, "cancel_booking_confirm_less_12_hour_message");

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-trips-page-setting`,
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
                    `driver_rides_heading.driver_rides_heading_${language.id}`
                ) ||
                validationErros.has(
                    `upcoming_label.upcoming_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_upcoming_trips_label.no_upcoming_trips_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_upcoming_rides_label.no_upcoming_rides_label_${language.id}`
                ) ||
                validationErros.has(
                    `completed_label.completed_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_completed_trips_label.no_completed_trips_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_completed_rides_label.no_completed_rides_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancelled_label.cancelled_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_cancelled_trips_label.no_cancelled_trips_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_cancelled_rides_label.no_cancelled_rides_label_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_trips_heading.passenger_trips_heading_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_message_title.cancel_message_title_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_booking_confirm_message.cancel_booking_confirm_message_${language.id}`
                ) ||
                validationErros.has(
                    `booking_cancel_btn_yes_label.booking_cancel_btn_yes_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_cancel_btn_no_label.booking_cancel_btn_no_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_booking_confirm_firm_message.cancel_booking_confirm_firm_message_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_booking_confirm_48_hour_message.cancel_booking_confirm_48_hour_message_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_booking_confirm_12_to_48_hour_message.cancel_booking_confirm_12_to_48_hour_message_${language.id}`
                ) ||
                validationErros.has(
                    `cancel_booking_confirm_less_12_hour_message.cancel_booking_confirm_less_12_hour_message_${language.id}`
                )
            );
        },
    },
};
</script>
