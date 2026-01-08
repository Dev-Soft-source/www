<template>
    <AppLayout>
        <section class="post-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Post ride page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting()">
                        <!-- Excel Upload Section -->
                        <div class="px-0 mt-6 mb-6">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    <h4 class="text-xl font-bold text-gray-800">📊 Excel Upload - Bulk Import Translations</h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Upload an Excel file with all Post Ride page settings for a specific language.</p>

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
                                        <a :href="`${mixAdminApiUrl}download-post-ride-page-setting-template?format=single_column`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Download Template</a>
                                    </div>
                                </div>

                                <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded"><div class="flex items-start"><svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div class="flex-1"><h5 class="text-red-800 font-semibold mb-2">Validation Errors in Excel File:</h5><ul class="list-disc list-inside space-y-1"><li v-for="(error, index) in excelErrors" :key="index" class="text-sm text-red-700"><strong>Row {{ error.row }}:</strong> {{ error.attribute }} - {{ error.errors.join(', ') }}</li></ul></div></div></div>
                            </div>
                        </div>
                        <!-- End Excel Upload Section -->

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
                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`feilds_required_text_${activeLanguageId}`">Indicate required feilds text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`feilds_required_text_${activeLanguageId}`"
                                                    :id="`feilds_required_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('feilds_required_text')"
                                                    @input="handleInput($event.target.value, language, 'feilds_required_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('feilds_required_text.feilds_required_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('feilds_required_text.feilds_required_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>
                                </div>

                                <!-- top section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[0] =
                                            !collapseStates[0]
                                            ">
                                        <h3 class="text-white">
                                            Top section
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
                                                    <label :for="`main_heading_update_${activeLanguageId}`">Edit page
                                                        main heading</label>
                                                </div>
                                                <input type="text" :name="`main_heading_update_${activeLanguageId}`"
                                                    :id="`main_heading_update_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'main_heading_update'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'main_heading_update'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `main_heading_update.main_heading_update_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `main_heading_update.main_heading_update_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`post_arrived_again_label_${activeLanguageId}`">Post
                                                        arrived again button heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`post_arrived_again_label_${activeLanguageId}`"
                                                    :id="`post_arrived_again_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'post_arrived_again_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'post_arrived_again_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `post_arrived_again_label.post_arrived_again_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `post_arrived_again_label.post_arrived_again_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`indicates_required_field_text_${activeLanguageId}`">Indicates required fields</label>
                                                </div>
                                                <input type="text"
                                                    :name="`indicates_required_field_text_${activeLanguageId}`"
                                                    :id="`indicates_required_field_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'indicates_required_field_text'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'indicates_required_field_text'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `indicates_required_field_text.indicates_required_field_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `indicates_required_field_text.indicates_required_field_text_${activeLanguageId}`
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
                                    </div>
                                </div>
                                <!-- top section end -->

                                <!-- ride details section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[1] =
                                            !collapseStates[1]
                                            ">
                                        <h3 class="text-white">
                                            Ride info section
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
                                                    <label :for="`ride_info_heading_${activeLanguageId}`">Ride info
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`ride_info_heading_${activeLanguageId}`"
                                                    :id="`ride_info_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'ride_info_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'ride_info_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `ride_info_heading.ride_info_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `ride_info_heading.ride_info_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`from_label_${activeLanguageId}`">From label</label>
                                                </div>
                                                <input type="text" :name="`from_label_${activeLanguageId}`"
                                                    :id="`from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'from_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'from_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `from_label.from_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `from_label.from_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`from_placeholder_${activeLanguageId}`">From
                                                        placeholder</label>
                                                </div>
                                                <input type="text" :name="`from_placeholder_${activeLanguageId}`"
                                                    :id="`from_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'from_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'from_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `from_placeholder.from_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `from_placeholder.from_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`to_label_${activeLanguageId}`">To label</label>
                                                </div>
                                                <input type="text" :name="`to_label_${activeLanguageId}`"
                                                    :id="`to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'to_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'to_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `to_label.to_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `to_label.to_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`to_placeholder_${activeLanguageId}`">To
                                                        placeholder</label>
                                                </div>
                                                <input type="text" :name="`to_placeholder_${activeLanguageId}`"
                                                    :id="`to_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'to_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'to_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `to_placeholder.to_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `to_placeholder.to_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pick_up_label_${activeLanguageId}`">Pick up
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`pick_up_label_${activeLanguageId}`"
                                                    :id="`pick_up_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'pick_up_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'pick_up_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `pick_up_label.pick_up_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `pick_up_label.pick_up_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pick_up_placeholder_${activeLanguageId}`">Pick up
                                                        placeholder</label>
                                                </div>
                                                <input type="text" :name="`pick_up_placeholder_${activeLanguageId}`"
                                                    :id="`pick_up_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'pick_up_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'pick_up_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `pick_up_placeholder.pick_up_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `pick_up_placeholder.pick_up_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`drop_off_label_${activeLanguageId}`">drop off
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`drop_off_label_${activeLanguageId}`"
                                                    :id="`drop_off_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'drop_off_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'drop_off_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `drop_off_label.drop_off_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `drop_off_label.drop_off_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`drop_off_placeholder_${activeLanguageId}`">drop off
                                                        placeholder</label>
                                                </div>
                                                <input type="text" :name="`drop_off_placeholder_${activeLanguageId}`"
                                                    :id="`drop_off_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'drop_off_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'drop_off_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `drop_off_placeholder.drop_off_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `drop_off_placeholder.drop_off_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`date_time_label_${activeLanguageId}`">Date time
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`date_time_label_${activeLanguageId}`"
                                                    :id="`date_time_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'date_time_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'date_time_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `date_time_label.date_time_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `date_time_label.date_time_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`at_label_${activeLanguageId}`">At label</label>
                                                </div>
                                                <input type="text" :name="`at_label_${activeLanguageId}`"
                                                    :id="`at_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'at_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'at_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `at_label.at_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `at_label.at_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`recurring_label_${activeLanguageId}`">Recurring
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`recurring_label_${activeLanguageId}`"
                                                    :id="`recurring_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'recurring_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'recurring_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `recurring_label.recurring_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `recurring_label.recurring_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`recurring_type_label_${activeLanguageId}`">Recurring
                                                        type label</label>
                                                </div>
                                                <input type="text" :name="`recurring_type_label_${activeLanguageId}`"
                                                    :id="`recurring_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'recurring_type_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'recurring_type_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `recurring_type_label.recurring_type_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `recurring_type_label.recurring_type_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`recurring_trips_label_${activeLanguageId}`">Recurring
                                                        trips label</label>
                                                </div>
                                                <input type="text" :name="`recurring_trips_label_${activeLanguageId}`"
                                                    :id="`recurring_trips_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'recurring_trips_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'recurring_trips_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `recurring_trips_label.recurring_trips_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `recurring_trips_label.recurring_trips_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`recurring_trips_placeholder_${activeLanguageId}`">Recurring
                                                        trips placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`recurring_trips_placeholder_${activeLanguageId}`"
                                                    :id="`recurring_trips_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'recurring_trips_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'recurring_trips_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `recurring_trips_placeholder.recurring_trips_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `recurring_trips_placeholder.recurring_trips_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`meeting_drop_off_description_label_${activeLanguageId}`">Meeting
                                                        drop-off description label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`meeting_drop_off_description_label_${activeLanguageId}`"
                                                    :id="`meeting_drop_off_description_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'meeting_drop_off_description_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'meeting_drop_off_description_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `meeting_drop_off_description_label.meeting_drop_off_description_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `meeting_drop_off_description_label.meeting_drop_off_description_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`meeting_drop_off_description_placeholder_${activeLanguageId}`">Meeting
                                                        drop-off description placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`meeting_drop_off_description_placeholder_${activeLanguageId}`"
                                                    :id="`meeting_drop_off_description_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'meeting_drop_off_description_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'meeting_drop_off_description_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `meeting_drop_off_description_placeholder.meeting_drop_off_description_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `meeting_drop_off_description_placeholder.meeting_drop_off_description_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`seats_label_${activeLanguageId}`">Seats label</label>
                                                </div>
                                                <input type="text" :name="`seats_label_${activeLanguageId}`"
                                                    :id="`seats_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'seats_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'seats_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `seats_label.seats_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `seats_label.seats_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`seats_middle_label_${activeLanguageId}`">Seats middle
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`seats_middle_label_${activeLanguageId}`"
                                                    :id="`seats_middle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'seats_middle_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'seats_middle_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `seats_middle_label.seats_middle_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `seats_middle_label.seats_middle_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`seats_back_label_${activeLanguageId}`">Seats back
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`seats_back_label_${activeLanguageId}`"
                                                    :id="`seats_back_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'seats_back_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'seats_back_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `seats_back_label.seats_back_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `seats_back_label.seats_back_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`city_not_in_record_${activeLanguageId}`">City name is in not our record</label>
                                                </div>
                                                <input type="text" :name="`city_not_in_record_${activeLanguageId}`"
                                                    :id="`city_not_in_record_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'city_not_in_record'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'city_not_in_record'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `city_not_in_record.city_not_in_record_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `city_not_in_record.city_not_in_record_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`city_not_fount_contact_text_${activeLanguageId}`">City name is not in our record contact us text</label>
                                                </div>
                                                <input type="text" :name="`city_not_fount_contact_text_${activeLanguageId}`"
                                                    :id="`city_not_fount_contact_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'city_not_fount_contact_text'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'city_not_fount_contact_text'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `city_not_fount_contact_text.city_not_fount_contact_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `city_not_fount_contact_text.city_not_fount_contact_text_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                                <!-- ride details section end -->

                                <!-- vehicle details section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[2] =
                                            !collapseStates[2]
                                            ">
                                        <h3 class="text-white">
                                            Vehicle details section
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
                                                    <label :for="`vehicle_label_${activeLanguageId}`">Vehicle
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`vehicle_label_${activeLanguageId}`"
                                                    :id="`vehicle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'vehicle_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'vehicle_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `vehicle_label.vehicle_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `vehicle_label.vehicle_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`skip_label_${activeLanguageId}`">Skip label</label>
                                                </div>
                                                <input type="text" :name="`skip_label_${activeLanguageId}`"
                                                    :id="`skip_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'skip_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'skip_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `skip_label.skip_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `skip_label.skip_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`add_vehicle_label_${activeLanguageId}`">Add vehicle
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`add_vehicle_label_${activeLanguageId}`"
                                                    :id="`add_vehicle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'add_vehicle_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'add_vehicle_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `add_vehicle_label.add_vehicle_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `add_vehicle_label.add_vehicle_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`existing_label_${activeLanguageId}`">Existing
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`existing_label_${activeLanguageId}`"
                                                    :id="`existing_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'existing_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'existing_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `existing_label.existing_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `existing_label.existing_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`make_label_${activeLanguageId}`">Make label</label>
                                                </div>
                                                <input type="text" :name="`make_label_${activeLanguageId}`"
                                                    :id="`make_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'make_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'make_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `make_label.make_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `make_label.make_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`make_placeholder_${activeLanguageId}`">Make
                                                        placeholder</label>
                                                </div>
                                                <input type="text" :name="`make_placeholder_${activeLanguageId}`"
                                                    :id="`make_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'make_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'make_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `make_placeholder.make_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `make_placeholder.make_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`model_label_${activeLanguageId}`">Model label</label>
                                                </div>
                                                <input type="text" :name="`model_label_${activeLanguageId}`"
                                                    :id="`model_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'model_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'model_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `model_label.model_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `model_label.model_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`model_placeholder_${activeLanguageId}`">Model
                                                        placeholder</label>
                                                </div>
                                                <input type="text" :name="`model_placeholder_${activeLanguageId}`"
                                                    :id="`model_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'model_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'model_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `model_placeholder.model_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `model_placeholder.model_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`type_label_${activeLanguageId}`">Type label</label>
                                                </div>
                                                <input type="text" :name="`type_label_${activeLanguageId}`"
                                                    :id="`type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'type_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'type_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `type_label.type_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `type_label.type_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`year_label_${activeLanguageId}`">Year label</label>
                                                </div>
                                                <input type="text" :name="`year_label_${activeLanguageId}`"
                                                    :id="`year_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'year_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'year_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `year_label.year_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `year_label.year_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`color_label_${activeLanguageId}`">Color label</label>
                                                </div>
                                                <input type="text" :name="`color_label_${activeLanguageId}`"
                                                    :id="`color_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'color_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'color_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `color_label.color_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `color_label.color_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`liscense_label_${activeLanguageId}`">License
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`liscense_label_${activeLanguageId}`"
                                                    :id="`liscense_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'liscense_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'liscense_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `liscense_label.liscense_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `liscense_label.liscense_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`car_type_label_${activeLanguageId}`">Car type
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`car_type_label_${activeLanguageId}`"
                                                    :id="`car_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'car_type_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'car_type_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `car_type_label.car_type_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `car_type_label.car_type_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`electric_car_label_${activeLanguageId}`">Electric car
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`electric_car_label_${activeLanguageId}`"
                                                    :id="`electric_car_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'electric_car_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'electric_car_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `electric_car_label.electric_car_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `electric_car_label.electric_car_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`hybrid_car_label_${activeLanguageId}`">Hybrid car
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`hybrid_car_label_${activeLanguageId}`"
                                                    :id="`hybrid_car_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'hybrid_car_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'hybrid_car_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `hybrid_car_label.hybrid_car_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `hybrid_car_label.hybrid_car_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`gas_car_label_${activeLanguageId}`">Gas car
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`gas_car_label_${activeLanguageId}`"
                                                    :id="`gas_car_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'gas_car_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'gas_car_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `gas_car_label.gas_car_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `gas_car_label.gas_car_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- vehicle details section end -->

                                <!-- preferences section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[3] =
                                            !collapseStates[3]
                                            ">
                                        <h3 class="text-white">
                                            Preferences section
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
                                                    <label :for="`preferences_label_${activeLanguageId}`">Preferences
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`preferences_label_${activeLanguageId}`"
                                                    :id="`preferences_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'preferences_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'preferences_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `preferences_label.preferences_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `preferences_label.preferences_label_${activeLanguageId}`
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
                                                    <label :for="`animals_label_${activeLanguageId}`">Animal
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`animals_label_${activeLanguageId}`"
                                                    :id="`animals_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'animals_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'animals_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `animals_label.animals_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `animals_label.animals_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- preferences section end -->

                                <!-- features section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[4] =
                                            !collapseStates[4]
                                            ">
                                        <h3 class="text-white">
                                            Features section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[4]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`features_label_${activeLanguageId}`">Features
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`features_label_${activeLanguageId}`"
                                                    :id="`features_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'features_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'features_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `features_label.features_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `features_label.features_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_female_text_${activeLanguageId}`">Pink
                                                        ride tooltip female text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_female_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_female_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_female_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_female_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_female_text.pink_ride_tooltip_female_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_female_text.pink_ride_tooltip_female_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_complete_profile_text_${activeLanguageId}`">Pink
                                                        ride tooltip complete profile text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_complete_profile_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_complete_profile_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_complete_profile_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_complete_profile_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_complete_profile_text.pink_ride_tooltip_complete_profile_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_complete_profile_text.pink_ride_tooltip_complete_profile_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_driver_text_${activeLanguageId}`">Pink
                                                        ride tooltip driver text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_driver_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_driver_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_driver_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_driver_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_driver_text.pink_ride_tooltip_driver_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_driver_text.pink_ride_tooltip_driver_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pink_ride_tooltip_with_text_${activeLanguageId}`">Pink
                                                        ride tooltip with text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_with_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_with_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_with_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_with_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_with_text.pink_ride_tooltip_with_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_with_text.pink_ride_tooltip_with_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_phone_number_text_${activeLanguageId}`">Pink
                                                        ride tooltip phone number text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_phone_number_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_phone_number_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_phone_number_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_phone_number_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_phone_number_text.pink_ride_tooltip_phone_number_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_phone_number_text.pink_ride_tooltip_phone_number_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_email_text_${activeLanguageId}`">Pink
                                                        ride tooltip email text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_email_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_email_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_email_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_email_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_email_text.pink_ride_tooltip_email_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_email_text.pink_ride_tooltip_email_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_driver_license_text_${activeLanguageId}`">Pink
                                                        ride tooltip driver license text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_driver_license_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_driver_license_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_driver_license_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_driver_license_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_driver_license_text.pink_ride_tooltip_driver_license_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_driver_license_text.pink_ride_tooltip_driver_license_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_verified_text_${activeLanguageId}`">Pink
                                                        ride tooltip verified text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_verified_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_verified_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_verified_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_verified_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_verified_text.pink_ride_tooltip_verified_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_verified_text.pink_ride_tooltip_verified_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_select_this_ride_text_${activeLanguageId}`">Pink
                                                        ride tooltip select this ride text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_select_this_ride_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_select_this_ride_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_select_this_ride_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_select_this_ride_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_select_this_ride_text.pink_ride_tooltip_select_this_ride_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_select_this_ride_text.pink_ride_tooltip_select_this_ride_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`pink_ride_tooltip_only_text_${activeLanguageId}`">Pink
                                                        ride tooltip only text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_only_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_only_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_only_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_only_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_only_text.pink_ride_tooltip_only_text_${activeLanguageId}')"
                                                v-text="validationErros.get('pink_ride_tooltip_only_text.pink_ride_tooltip_only_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_greater_text_${activeLanguageId}`">Extra
                                                        care tooltip greater text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_greater_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_greater_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_greater_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_greater_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_greater_text.extra_care_tooltip_greater_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_greater_text.extra_care_tooltip_greater_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_eligible_text_${activeLanguageId}`">Extra
                                                        care tooltip eligible text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_eligible_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_eligible_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_eligible_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_eligible_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_eligible_text.extra_care_tooltip_eligible_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_eligible_text.extra_care_tooltip_eligible_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>


                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_popup_eligible_text_${activeLanguageId}`">Extra care not eligible popup</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_popup_eligible_text_${activeLanguageId}`"
                                                    :id="`extra_care_popup_eligible_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_popup_eligible_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_popup_eligible_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_popup_eligible_text.extra_care_popup_eligible_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_popup_eligible_text.extra_care_popup_eligible_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>
                                        
                                       
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_complete_profile_text_${activeLanguageId}`">Extra
                                                        care tooltip complete profile text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_complete_profile_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_complete_profile_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_complete_profile_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_complete_profile_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_complete_profile_text.extra_care_tooltip_complete_profile_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_complete_profile_text.extra_care_tooltip_complete_profile_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <!-- For extra_care_tooltip_verified_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_verified_text_${activeLanguageId}`">Extra
                                                        care tooltip verified text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_verified_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_verified_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_verified_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_verified_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_verified_text.extra_care_tooltip_verified_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_verified_text.extra_care_tooltip_verified_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <!-- For extra_care_tooltip_driver_license_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_driver_license_text_${activeLanguageId}`">Extra
                                                        care tooltip driver license text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_driver_license_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_driver_license_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_driver_license_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_driver_license_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_driver_license_text.extra_care_tooltip_driver_license_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_driver_license_text.extra_care_tooltip_driver_license_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <!-- For extra_care_tooltip_phone_number_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_phone_number_text_${activeLanguageId}`">Extra
                                                        care tooltip phone number text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_phone_number_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_phone_number_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_phone_number_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_phone_number_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_phone_number_text.extra_care_tooltip_phone_number_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_phone_number_text.extra_care_tooltip_phone_number_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <!-- For extra_care_tooltip_email_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_email_text_${activeLanguageId}`">Extra
                                                        care tooltip email text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_email_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_email_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_email_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_email_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_email_text.extra_care_tooltip_email_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_email_text.extra_care_tooltip_email_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <!-- For extra_care_tooltip_and_his_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_and_his_text_${activeLanguageId}`">Extra
                                                        care tooltip and his text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_and_his_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_and_his_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_and_his_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_and_his_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_and_his_text.extra_care_tooltip_and_his_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_and_his_text.extra_care_tooltip_and_his_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>
                                        <!-- For extra_care_tooltip_greater_age_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_greater_age_text_${activeLanguageId}`">Extra
                                                        care tooltip greater age text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_greater_age_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_greater_age_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_greater_age_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_greater_age_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_greater_age_text.extra_care_tooltip_greater_age_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_greater_age_text.extra_care_tooltip_greater_age_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>
                                        <!-- For extra_care_tooltip_driver_review_text -->
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_driver_review_text_${activeLanguageId}`">Extra
                                                        care tooltip driver review text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_driver_review_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_driver_review_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_driver_review_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_driver_review_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_driver_review_text.extra_care_tooltip_driver_review_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_driver_review_text.extra_care_tooltip_driver_review_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>


                                    </div>
                                </div>
                                <!-- features section end -->

                                <!-- booking section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[5] =
                                            !collapseStates[5]
                                            ">
                                        <h3 class="text-white">
                                            Booking section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[5]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`booking_label_${activeLanguageId}`">Booking
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`booking_label_${activeLanguageId}`"
                                                    :id="`booking_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'booking_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'booking_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `booking_label.booking_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `booking_label.booking_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`max_back_seats_label_${activeLanguageId}`">Max back
                                                        seats label</label>
                                                </div>
                                                <input type="text" :name="`max_back_seats_label_${activeLanguageId}`"
                                                    :id="`max_back_seats_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'max_back_seats_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'max_back_seats_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `max_back_seats_label.max_back_seats_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `max_back_seats_label.max_back_seats_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- booking section end -->

                                <!-- luggage section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[6] =
                                            !collapseStates[6]
                                            ">
                                        <h3 class="text-white">
                                            Luggage section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[6]">
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
                                                    <label :for="`luggage_checkbox_label1_${activeLanguageId}`">Luggage
                                                        checkbox label1</label>
                                                </div>
                                                <input type="text" :name="`luggage_checkbox_label1_${activeLanguageId}`"
                                                    :id="`luggage_checkbox_label1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'luggage_checkbox_label1'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'luggage_checkbox_label1'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `luggage_checkbox_label1.luggage_checkbox_label1_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `luggage_checkbox_label1.luggage_checkbox_label1_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_checkbox_label1_tooltip_${activeLanguageId}`">Luggage
                                                        checkbox label1 tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`luggage_checkbox_label1_tooltip_${activeLanguageId}`"
                                                    :id="`luggage_checkbox_label1_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'luggage_checkbox_label1_tooltip'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'luggage_checkbox_label1_tooltip'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `luggage_checkbox_label1_tooltip.luggage_checkbox_label1_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `luggage_checkbox_label1_tooltip.luggage_checkbox_label1_tooltip_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <!-- <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`luggage_checkbox_label2_${activeLanguageId}`">Luggage
                                                        checkbox label2</label>
                                                </div>
                                                <input type="text" :name="`luggage_checkbox_label2_${activeLanguageId}`"
                                                    :id="`luggage_checkbox_label2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'luggage_checkbox_label2'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'luggage_checkbox_label2'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `luggage_checkbox_label2.luggage_checkbox_label2_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `luggage_checkbox_label2.luggage_checkbox_label2_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_checkbox_label2_tooltip_${activeLanguageId}`">Luggage
                                                        checkbox label2 tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`luggage_checkbox_label2_tooltip_${activeLanguageId}`"
                                                    :id="`luggage_checkbox_label2_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'luggage_checkbox_label2_tooltip'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'luggage_checkbox_label2_tooltip'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `luggage_checkbox_label2_tooltip.luggage_checkbox_label2_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `luggage_checkbox_label2_tooltip.luggage_checkbox_label2_tooltip_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div> -->
                                    </div>
                                </div>
                                <!-- luggage section end -->

                                <!-- price section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[7] =
                                            !collapseStates[7]
                                            ">
                                        <h3 class="text-white">
                                            Price and payment section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[7]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`price_payment_heading_${activeLanguageId}`">Price and
                                                        payment heading</label>
                                                </div>
                                                <input type="text" :name="`price_payment_heading_${activeLanguageId}`"
                                                    :id="`price_payment_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'price_payment_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'price_payment_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `price_payment_heading.price_payment_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `price_payment_heading.price_payment_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`price_per_seat_label_${activeLanguageId}`">Price per
                                                        seat label</label>
                                                </div>
                                                <input type="text" :name="`price_per_seat_label_${activeLanguageId}`"
                                                    :id="`price_per_seat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'price_per_seat_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'price_per_seat_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `price_per_seat_label.price_per_seat_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `price_per_seat_label.price_per_seat_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`payment_methods_label_${activeLanguageId}`">Payment
                                                        method label</label>
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
                                    </div>
                                </div>
                                <!-- price section end -->

                                <!-- cancellation section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[12] =
                                            !collapseStates[12]
                                            ">
                                        <h3 class="text-white">
                                            Cancellation policy section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[12]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancellation_policy_label_${activeLanguageId}`">Cancellation
                                                        policy heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancellation_policy_label_${activeLanguageId}`"
                                                    :id="`cancellation_policy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'cancellation_policy_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'cancellation_policy_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `cancellation_policy_label.cancellation_policy_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `cancellation_policy_label.cancellation_policy_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- cancellation section end -->

                                <!-- anything to add section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[8] =
                                            !collapseStates[8]
                                            ">
                                        <h3 class="text-white">
                                            Anything to add section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[8]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`anything_to_add_label_${activeLanguageId}`">Anything
                                                        to add label</label>
                                                </div>
                                                <input type="text" :name="`anything_to_add_label_${activeLanguageId}`"
                                                    :id="`anything_to_add_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'anything_to_add_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'anything_to_add_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `anything_to_add_label.anything_to_add_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `anything_to_add_label.anything_to_add_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`anything_to_add_placeholder_${activeLanguageId}`">Anything
                                                        to add placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`anything_to_add_placeholder_${activeLanguageId}`"
                                                    :id="`anything_to_add_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'anything_to_add_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'anything_to_add_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `anything_to_add_placeholder.anything_to_add_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `anything_to_add_placeholder.anything_to_add_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- anything to add section end -->

                                <!-- disclaimers section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[9] =
                                            !collapseStates[9]
                                            ">
                                        <h3 class="text-white">
                                            Disclaimers section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[9]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`disclaimers_label_${activeLanguageId}`">Disclaimers
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`disclaimers_label_${activeLanguageId}`"
                                                    :id="`disclaimers_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'disclaimers_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'disclaimers_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `disclaimers_label.disclaimers_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `disclaimers_label.disclaimers_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`disclaimers_description_${activeLanguageId}`">Disclaimers
                                                        description (Web)</label>
                                                </div>
                                                <editor @selectionChange="
                                                    handleSelectionChange(
                                                        language,
                                                        'disclaimers_description'
                                                    )
                                                    " :ref="`disclaimers_description_${language.id}`"
                                                    :id="`disclaimers_description_${language.id}`" :initial-value="form[
                                                        `disclaimers_description`
                                                    ][
                                                        `disclaimers_description_${language?.id}`
                                                    ]
                                                        " :tinymce-script-src="tinymceScriptSrc"
                                                    :init="editorConfig" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `disclaimers_description.disclaimers_description_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `disclaimers_description.disclaimers_description_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`app_disclaimers_description1_${activeLanguageId}`">Disclaimers
                                                        description point1 (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`app_disclaimers_description1_${activeLanguageId}`"
                                                    :id="`app_disclaimers_description1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'app_disclaimers_description1'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'app_disclaimers_description1'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `app_disclaimers_description1.app_disclaimers_description1_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `app_disclaimers_description1.app_disclaimers_description1_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`app_disclaimers_description2_${activeLanguageId}`">Disclaimers
                                                        description point2 (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`app_disclaimers_description2_${activeLanguageId}`"
                                                    :id="`app_disclaimers_description2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'app_disclaimers_description2'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'app_disclaimers_description2'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `app_disclaimers_description2.app_disclaimers_description2_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `app_disclaimers_description2.app_disclaimers_description2_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`app_disclaimers_description3_${activeLanguageId}`">Disclaimers
                                                        description point3 (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`app_disclaimers_description3_${activeLanguageId}`"
                                                    :id="`app_disclaimers_description3_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'app_disclaimers_description3'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'app_disclaimers_description3'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `app_disclaimers_description3.app_disclaimers_description3_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `app_disclaimers_description3.app_disclaimers_description3_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`app_disclaimers_description4_${activeLanguageId}`">Disclaimers
                                                        description point4 (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`app_disclaimers_description4_${activeLanguageId}`"
                                                    :id="`app_disclaimers_description4_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'app_disclaimers_description4'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'app_disclaimers_description4'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `app_disclaimers_description4.app_disclaimers_description4_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `app_disclaimers_description4.app_disclaimers_description4_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <!-- <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`pink_ride_disclaimers_description_${activeLanguageId}`"
                                                        >Pink ride disclaimers description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'pink_ride_disclaimers_description'
                                                        )
                                                    "
                                                    :ref="`pink_ride_disclaimers_description_${language.id}`"
                                                    :id="`pink_ride_disclaimers_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `pink_ride_disclaimers_description`
                                                        ][
                                                            `pink_ride_disclaimers_description_${language?.id}`
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
                                                        `pink_ride_disclaimers_description.pink_ride_disclaimers_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pink_ride_disclaimers_description.pink_ride_disclaimers_description_${activeLanguageId}`
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
                                                        :for="`extra_care_ride_disclaimers_description_${activeLanguageId}`"
                                                        >Folks ride disclaimers description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'extra_care_ride_disclaimers_description'
                                                        )
                                                    "
                                                    :ref="`extra_care_ride_disclaimers_description_${language.id}`"
                                                    :id="`extra_care_ride_disclaimers_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `extra_care_ride_disclaimers_description`
                                                        ][
                                                            `extra_care_ride_disclaimers_description_${language?.id}`
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
                                                        `extra_care_ride_disclaimers_description.extra_care_ride_disclaimers_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `extra_care_ride_disclaimers_description.extra_care_ride_disclaimers_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div> -->
                                    </div>
                                </div>
                                <!-- disclaimers section end -->

                                <!-- agree section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[10] =
                                            !collapseStates[10]
                                            ">
                                        <h3 class="text-white">
                                            Agree terms section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[10]">
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`agree_terms_label_${activeLanguageId}`">Agree terms
                                                        label (Web)</label>
                                                </div>
                                                <editor @selectionChange="
                                                    handleSelectionChange(
                                                        language,
                                                        'agree_terms_label'
                                                    )
                                                    " :ref="`agree_terms_label_${language.id}`"
                                                    :id="`agree_terms_label_${language.id}`" :initial-value="form[
                                                        `agree_terms_label`
                                                    ][
                                                        `agree_terms_label_${language?.id}`
                                                    ]
                                                        " :tinymce-script-src="tinymceScriptSrc"
                                                    :init="editorConfig" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `agree_terms_label.agree_terms_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `agree_terms_label.agree_terms_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mobile_agree_terms_label_${activeLanguageId}`">Agree
                                                        terms label (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`mobile_agree_terms_label_${activeLanguageId}`"
                                                    :id="`mobile_agree_terms_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_agree_terms_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_agree_terms_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_agree_terms_label.mobile_agree_terms_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `mobile_agree_terms_label.mobile_agree_terms_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`mobile_term_of_service_label_${activeLanguageId}`">Term
                                                        of service label (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`mobile_term_of_service_label_${activeLanguageId}`"
                                                    :id="`mobile_term_of_service_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_term_of_service_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_term_of_service_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_term_of_service_label.mobile_term_of_service_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `mobile_term_of_service_label.mobile_term_of_service_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`mobile_agree_terms_and_label_${activeLanguageId}`">Agree
                                                        terms and label (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`mobile_agree_terms_and_label_${activeLanguageId}`"
                                                    :id="`mobile_agree_terms_and_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_agree_terms_and_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_agree_terms_and_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_agree_terms_and_label.mobile_agree_terms_and_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `mobile_agree_terms_and_label.mobile_agree_terms_and_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mobile_term_of_use_label_${activeLanguageId}`">Terms
                                                        of use label (App)</label>
                                                </div>
                                                <input type="text"
                                                    :name="`mobile_term_of_use_label_${activeLanguageId}`"
                                                    :id="`mobile_term_of_use_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'mobile_term_of_use_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'mobile_term_of_use_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `mobile_term_of_use_label.mobile_term_of_use_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `mobile_term_of_use_label.mobile_term_of_use_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- agree section end -->

                                <!-- submit button section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[11] =
                                            !collapseStates[11]
                                            ">
                                        <h3 class="text-white">
                                            Submit section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[11]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`submit_button_label_${activeLanguageId}`">Submit
                                                        button label</label>
                                                </div>
                                                <input type="text" :name="`submit_button_label_${activeLanguageId}`"
                                                    :id="`submit_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'submit_button_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'submit_button_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `submit_button_label.submit_button_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `submit_button_label.submit_button_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`update_button_label_${activeLanguageId}`">Update
                                                        button label</label>
                                                </div>
                                                <input type="text" :name="`update_button_label_${activeLanguageId}`"
                                                    :id="`update_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'update_button_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'update_button_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `update_button_label.update_button_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `update_button_label.update_button_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`repost_ride_btn_label_${activeLanguageId}`">Repost ride button</label>
                                                </div>
                                                <input type="text" :name="`repost_ride_btn_label_${activeLanguageId}`"
                                                    :id="`repost_ride_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'repost_ride_btn_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'repost_ride_btn_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `repost_ride_btn_label.repost_ride_btn_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `repost_ride_btn_label.repost_ride_btn_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`select_vehicle_type_${activeLanguageId}`">Select
                                                        Vehicle Type</label>
                                                </div>
                                                <input type="text" :name="`select_vehicle_type_${activeLanguageId}`"
                                                    :id="`select_vehicle_type_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('select_vehicle_type')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'select_vehicle_type'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('select_vehicle_type.select_vehicle_type_${activeLanguageId}')"
                                                v-text="validationErros.get('select_vehicle_type.select_vehicle_type_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`vehicle_type_placeholder_${activeLanguageId}`">Vehicle
                                                        Type Placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    :id="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('vehicle_type_placeholder')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`seat_text_${activeLanguageId}`">Seat Text</label>
                                                </div>
                                                <input type="text" :name="`seat_text_${activeLanguageId}`"
                                                    :id="`seat_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('seat_text')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seat_text'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('seat_text.seat_text_${activeLanguageId}')"
                                                v-text="validationErros.get('seat_text.seat_text_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`recurring_type_select_placeholder_${activeLanguageId}`">Recurring
                                                        Type Select Placeholder</label>
                                                </div>
                                                <input type="text"
                                                    :name="`recurring_type_select_placeholder_${activeLanguageId}`"
                                                    :id="`recurring_type_select_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('recurring_type_select_placeholder')"
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recurring_type_select_placeholder'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('recurring_type_select_placeholder.recurring_type_select_placeholder_${activeLanguageId}')"
                                                v-text="validationErros.get('recurring_type_select_placeholder.recurring_type_select_placeholder_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`recurring_type_daily_label_${activeLanguageId}`">Recurring
                                                        Type Daily Label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`recurring_type_daily_label_${activeLanguageId}`"
                                                    :id="`recurring_type_daily_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('recurring_type_daily_label')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recurring_type_daily_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('recurring_type_daily_label.recurring_type_daily_label_${activeLanguageId}')"
                                                v-text="validationErros.get('recurring_type_daily_label.recurring_type_daily_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`recurring_type_weekly_label_${activeLanguageId}`">Recurring
                                                        Type Weekly Label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`recurring_type_weekly_label_${activeLanguageId}`"
                                                    :id="`recurring_type_weekly_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('recurring_type_weekly_label')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recurring_type_weekly_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('recurring_type_weekly_label.recurring_type_weekly_label_${activeLanguageId}')"
                                                v-text="validationErros.get('recurring_type_weekly_label.recurring_type_weekly_label_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`post_ride_again_main_heading_${activeLanguageId}`">Post
                                                        Ride Again Main Heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`post_ride_again_main_heading_${activeLanguageId}`"
                                                    :id="`post_ride_again_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('post_ride_again_main_heading')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'post_ride_again_main_heading'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('post_ride_again_main_heading.post_ride_again_main_heading_${activeLanguageId}')"
                                                v-text="validationErros.get('post_ride_again_main_heading.post_ride_again_main_heading_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`upcoming_label_${activeLanguageId}`">Upcoming
                                                        Label</label>
                                                </div>
                                                <input type="text" :name="`upcoming_label_${activeLanguageId}`"
                                                    :id="`upcoming_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('upcoming_label')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'upcoming_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('upcoming_label.upcoming_label_${activeLanguageId}')"
                                                v-text="validationErros.get('upcoming_label.upcoming_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`completed_label_${activeLanguageId}`">Completed
                                                        Label</label>
                                                </div>
                                                <input type="text" :name="`completed_label_${activeLanguageId}`"
                                                    :id="`completed_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('completed_label')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'completed_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('completed_label.completed_label_${activeLanguageId}')"
                                                v-text="validationErros.get('completed_label.completed_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancelled_label_${activeLanguageId}`">Cancelled
                                                        Label</label>
                                                </div>
                                                <input type="text" :name="`cancelled_label_${activeLanguageId}`"
                                                    :id="`cancelled_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('cancelled_label')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancelled_label'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancelled_label.cancelled_label_${activeLanguageId}')"
                                                v-text="validationErros.get('cancelled_label.cancelled_label_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancelled_ride_no_found_message_${activeLanguageId}`">Cancelled
                                                        Ride No Message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancelled_ride_no_found_message_${activeLanguageId}`"
                                                    :id="`cancelled_ride_no_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('cancelled_ride_no_found_message')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancelled_ride_no_found_message'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('cancelled_ride_no_found_message.cancelled_ride_no_found_message_${activeLanguageId}')"
                                                v-text="validationErros.get('cancelled_ride_no_found_message.cancelled_ride_no_found_message_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`completed_ride_no_found_message_${activeLanguageId}`">Completed
                                                        Ride No Found Message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`completed_ride_no_found_message_${activeLanguageId}`"
                                                    :id="`completed_ride_no_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('completed_ride_no_found_message')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'completed_ride_no_found_message'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('completed_ride_no_found_message.completed_ride_no_found_message_${activeLanguageId}')"
                                                v-text="validationErros.get('completed_ride_no_found_message.completed_ride_no_found_message_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`upcoming_ride_no_found_message_${activeLanguageId}`">Upcoming
                                                        Ride No Found Message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`upcoming_ride_no_found_message_${activeLanguageId}`"
                                                    :id="`upcoming_ride_no_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('upcoming_ride_no_found_message')" @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'upcoming_ride_no_found_message'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('upcoming_ride_no_found_message.upcoming_ride_no_found_message_${activeLanguageId}')"
                                                v-text="validationErros.get('upcoming_ride_no_found_message.upcoming_ride_no_found_message_${activeLanguageId}')">
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_admin_enable_text_${activeLanguageId}`">Extra care tooltip admin enable text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_admin_enable_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_admin_enable_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_admin_enable_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_admin_enable_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_admin_enable_text.extra_care_tooltip_admin_enable_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_admin_enable_text.extra_care_tooltip_admin_enable_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`extra_care_tooltip_admin_disable_text_${activeLanguageId}`">Extra care tooltip admin disable text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`extra_care_tooltip_admin_disable_text_${activeLanguageId}`"
                                                    :id="`extra_care_tooltip_admin_disable_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('extra_care_tooltip_admin_disable_text')"
                                                    @input="handleInput($event.target.value, language, 'extra_care_tooltip_admin_disable_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('extra_care_tooltip_admin_disable_text.extra_care_tooltip_admin_disable_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('extra_care_tooltip_admin_disable_text.extra_care_tooltip_admin_disable_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_admin_enable_text_${activeLanguageId}`">Pink ride tooltip admin enable text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_admin_enable_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_admin_enable_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_admin_enable_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_admin_enable_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_admin_enable_text.pink_ride_tooltip_admin_enable_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('pink_ride_tooltip_admin_enable_text.pink_ride_tooltip_admin_enable_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`pink_ride_tooltip_admin_disable_text_${activeLanguageId}`">Pink ride tooltip admin disable text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`pink_ride_tooltip_admin_disable_text_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_admin_disable_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('pink_ride_tooltip_admin_disable_text')"
                                                    @input="handleInput($event.target.value, language, 'pink_ride_tooltip_admin_disable_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('pink_ride_tooltip_admin_disable_text.pink_ride_tooltip_admin_disable_text_${activeLanguageId}')">
                                                {{
                                                    validationErros.get('pink_ride_tooltip_admin_disable_text.pink_ride_tooltip_admin_disable_text_${activeLanguageId}')
                                                }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                                <!-- submit button section end -->
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
                            this.handleInput("", language, "main_heading_update");
                            this.handleInput("", language, "post_arrived_again_label");
                            this.handleInput("", language, "indicates_required_field_text");
                            this.handleInput("", language, "navbar_icon");
                            this.handleInput("", language, "ride_info_heading");
                            this.handleInput("", language, "from_label");
                            this.handleInput("", language, "from_placeholder");
                            this.handleInput("", language, "to_label");
                            this.handleInput("", language, "to_placeholder");
                            this.handleInput("", language, "pick_up_label");
                            this.handleInput("", language, "pick_up_placeholder");
                            this.handleInput("", language, "drop_off_label");
                            this.handleInput("", language, "drop_off_placeholder");
                            this.handleInput("", language, "date_time_label");
                            this.handleInput("", language, "at_label");
                            this.handleInput("", language, "recurring_label");
                            this.handleInput("", language, "recurring_type_label");
                            this.handleInput("", language, "recurring_trips_label");
                            this.handleInput("", language, "recurring_trips_placeholder");
                            this.handleInput("", language, "meeting_drop_off_description_label");
                            this.handleInput("", language, "meeting_drop_off_description_placeholder");
                            this.handleInput("", language, "seats_label");
                            this.handleInput("", language, "seats_middle_label");
                            this.handleInput("", language, "seats_back_label");
                            this.handleInput("", language, "city_not_in_record");
                            this.handleInput("", language, "city_not_fount_contact_text");
                            this.handleInput("", language, "extra_rides_trip_limit");
                            this.handleInput("", language, "vehicle_label");
                            this.handleInput("", language, "skip_label");
                            this.handleInput("", language, "add_vehicle_label");
                            this.handleInput("", language, "existing_label");
                            this.handleInput("", language, "make_label");
                            this.handleInput("", language, "make_placeholder");
                            this.handleInput("", language, "model_label");
                            this.handleInput("", language, "model_placeholder");
                            this.handleInput("", language, "type_label");
                            this.handleInput("", language, "year_label");
                            this.handleInput("", language, "color_label");
                            this.handleInput("", language, "liscense_label");
                            this.handleInput("", language, "car_type_label");
                            this.handleInput("", language, "electric_car_label");
                            this.handleInput("", language, "hybrid_car_label");
                            this.handleInput("", language, "gas_car_label");
                            this.handleInput("", language, "preferences_label");
                            this.handleInput("", language, "smoking_label");
                            this.handleInput("", language, "smoking_option1");
                            this.handleInput("", language, "smoking_option2");
                            this.handleInput("", language, "animals_label");
                            this.handleInput("", language, "animals_option1");
                            this.handleInput("", language, "animals_option2");
                            this.handleInput("", language, "animals_option3");
                            this.handleInput("", language, "features_label");
                            this.handleInput("", language, "features_option1");
                            this.handleInput("", language, "features_option2");
                            this.handleInput("", language, "features_option3");
                            this.handleInput("", language, "features_option4");
                            this.handleInput("", language, "features_option5");
                            this.handleInput("", language, "features_option6");
                            this.handleInput("", language, "features_option7");
                            this.handleInput("", language, "features_option8");
                            this.handleInput("", language, "features_option9");
                            this.handleInput("", language, "features_option10");
                            this.handleInput("", language, "features_option11");
                            this.handleInput("", language, "features_option12");
                            this.handleInput("", language, "features_option13");
                            this.handleInput("", language, "features_option14");
                            this.handleInput("", language, "features_option15");
                            this.handleInput("", language, "features_option16");
                            this.handleInput("", language, "features_option17");
                            this.handleInput("", language, "booking_label");
                            this.handleInput("", language, "booking_option1");
                            this.handleInput("", language, "booking_option1_tooltip");
                            this.handleInput("", language, "booking_option2");
                            this.handleInput("", language, "booking_option2_tooltip");
                            this.handleInput("", language, "max_back_seats_label");
                            this.handleInput("", language, "luggage_label");
                            this.handleInput("", language, "luggage_option1");
                            this.handleInput("", language, "luggage_option2");
                            this.handleInput("", language, "luggage_option3");
                            this.handleInput("", language, "luggage_option4");
                            this.handleInput("", language, "luggage_option5");
                            this.handleInput("", language, "luggage_checkbox_label1");
                            this.handleInput("", language, "luggage_checkbox_label1_tooltip");
                            // this.handleInput("", language, "luggage_checkbox_label2");
                            // this.handleInput("", language, "luggage_checkbox_label2_tooltip");
                            this.handleInput("", language, "price_payment_heading");
                            this.handleInput("", language, "price_per_seat_label");
                            this.handleInput("", language, "payment_methods_label");
                            this.handleInput("", language, "cancellation_policy_label");
                            this.handleInput("", language, "cancellation_policy_label1");
                            this.handleInput("", language, "cancellation_policy_label1_tooltip");
                            this.handleInput("", language, "cancellation_policy_label2");
                            this.handleInput("", language, "cancellation_policy_label2_tooltip");
                            this.handleInput("", language, "payment_methods_option1");
                            this.handleInput("", language, "payment_methods_option2");
                            this.handleInput("", language, "payment_methods_option3");
                            this.handleInput("", language, "anything_to_add_label");
                            this.handleInput("", language, "anything_to_add_placeholder");
                            this.handleInput("", language, "disclaimers_label");
                            this.handleInput("", language, "app_disclaimers_description1");
                            this.handleInput("", language, "app_disclaimers_description2");
                            this.handleInput("", language, "app_disclaimers_description3");
                            this.handleInput("", language, "app_disclaimers_description4");
                            this.handleInput("", language, "disclaimers_description");
                            // this.handleInput("", language, "pink_ride_disclaimers_description");
                            // this.handleInput("", language, "extra_care_ride_disclaimers_description");
                            this.handleInput("", language, "agree_terms_label");
                            this.handleInput("", language, "mobile_agree_terms_label");
                            this.handleInput("", language, "mobile_term_of_service_label");
                            this.handleInput("", language, "mobile_agree_terms_and_label");
                            this.handleInput("", language, "mobile_term_of_use_label");
                            this.handleInput("", language, "submit_button_label");
                            this.handleInput("", language, "update_button_label");
                            this.handleInput("", language, "repost_ride_btn_label");
                            this.handleInput("", language, "pink_ride_tooltip_female_text");
                            this.handleInput("", language, "pink_ride_tooltip_complete_profile_text");
                            this.handleInput("", language, "pink_ride_tooltip_only_text");
                            this.handleInput("", language, "pink_ride_tooltip_driver_text");
                            this.handleInput("", language, "pink_ride_tooltip_with_text");
                            this.handleInput("", language, "pink_ride_tooltip_phone_number_text");
                            this.handleInput("", language, "pink_ride_tooltip_email_text");
                            this.handleInput("", language, "pink_ride_tooltip_driver_license_text");
                            this.handleInput("", language, "pink_ride_tooltip_verified_text");
                            this.handleInput("", language, "pink_ride_tooltip_select_this_ride_text");
                            this.handleInput("", language, "extra_care_tooltip_greater_text");
                            this.handleInput("", language, "extra_care_tooltip_eligible_text");
                            this.handleInput("", language, "extra_care_popup_eligible_text");
                            this.handleInput("", language, "feilds_required_text");
                            this.handleInput("", language, "extra_care_tooltip_complete_profile_text");
                            this.handleInput("", language, "extra_care_tooltip_verified_text");
                            this.handleInput("", language, "extra_care_tooltip_driver_license_text");
                            this.handleInput("", language, "extra_care_tooltip_phone_number_text");
                            this.handleInput("", language, "extra_care_tooltip_email_text");
                            this.handleInput("", language, "extra_care_tooltip_and_his_text");
                            this.handleInput("", language, "extra_care_tooltip_greater_age_text");
                            this.handleInput("", language, "extra_care_tooltip_driver_review_text");
                            this.handleInput("", language, "select_vehicle_type");
                            this.handleInput("", language, "vehicle_type_placeholder");
                            this.handleInput("", language, "seat_text");
                            this.handleInput("", language, "recurring_type_select_placeholder");
                            this.handleInput("", language, "recurring_type_daily_label");
                            this.handleInput("", language, "recurring_type_weekly_label");
                            this.handleInput("", language, "post_ride_again_main_heading");
                            this.handleInput("", language, "upcoming_label");
                            this.handleInput("", language, "completed_label");
                            this.handleInput("", language, "cancelled_label");
                            this.handleInput("", language, "cancelled_ride_no_found_message");
                            this.handleInput("", language, "completed_ride_no_found_message");
                            this.handleInput("", language, "upcoming_ride_no_found_message");
                            this.handleInput("", language, "extra_care_tooltip_admin_enable_text");
                            this.handleInput("", language, "extra_care_tooltip_admin_disable_text");
                            this.handleInput("", language, "pink_ride_tooltip_admin_enable_text");
                            this.handleInput("", language, "pink_ride_tooltip_admin_disable_text");


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
                            console.log(setting.language)

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
                                setting?.main_heading_update,
                                setting?.language,
                                "main_heading_update"
                            );
                            this.handleInput(
                                setting?.post_arrived_again_label,
                                setting?.language,
                                "post_arrived_again_label"
                            );
                            this.handleInput(
                                setting?.indicates_required_field_text,
                                setting?.language,
                                "indicates_required_field_text"
                            );
                            this.handleInput(
                                setting?.navbar_icon,
                                setting?.language,
                                "navbar_icon"
                            );
                            this.handleInput(
                                setting?.ride_info_heading,
                                setting?.language,
                                "ride_info_heading"
                            );
                            this.handleInput(
                                setting?.from_label,
                                setting?.language,
                                "from_label"
                            );
                            this.handleInput(
                                setting?.from_placeholder,
                                setting?.language,
                                "from_placeholder"
                            );
                            this.handleInput(
                                setting?.to_label,
                                setting?.language,
                                "to_label"
                            );
                            this.handleInput(
                                setting?.to_placeholder,
                                setting?.language,
                                "to_placeholder"
                            );
                            this.handleInput(
                                setting?.pick_up_label,
                                setting?.language,
                                "pick_up_label"
                            );
                            this.handleInput(
                                setting?.pick_up_placeholder,
                                setting?.language,
                                "pick_up_placeholder"
                            );
                            this.handleInput(
                                setting?.drop_off_label,
                                setting?.language,
                                "drop_off_label"
                            );
                            this.handleInput(
                                setting?.drop_off_placeholder,
                                setting?.language,
                                "drop_off_placeholder"
                            );
                            this.handleInput(
                                setting?.date_time_label,
                                setting?.language,
                                "date_time_label"
                            );
                            this.handleInput(
                                setting?.at_label,
                                setting?.language,
                                "at_label"
                            );
                            this.handleInput(
                                setting?.recurring_label,
                                setting?.language,
                                "recurring_label"
                            );
                            this.handleInput(
                                setting?.recurring_type_label,
                                setting?.language,
                                "recurring_type_label"
                            );
                            this.handleInput(
                                setting?.recurring_trips_label,
                                setting?.language,
                                "recurring_trips_label"
                            );
                            this.handleInput(
                                setting?.recurring_trips_placeholder,
                                setting?.language,
                                "recurring_trips_placeholder"
                            );
                            this.handleInput(
                                setting?.meeting_drop_off_description_label,
                                setting?.language,
                                "meeting_drop_off_description_label"
                            );
                            this.handleInput(
                                setting?.meeting_drop_off_description_placeholder,
                                setting?.language,
                                "meeting_drop_off_description_placeholder"
                            );
                            this.handleInput(
                                setting?.seats_label,
                                setting?.language,
                                "seats_label"
                            );
                            this.handleInput(
                                setting?.seats_middle_label,
                                setting?.language,
                                "seats_middle_label"
                            );
                            this.handleInput(
                                setting?.seats_back_label,
                                setting?.language,
                                "seats_back_label"
                            );
                            
                            this.handleInput(
                                setting?.city_not_in_record,
                                setting?.language,
                                "city_not_in_record"
                            );
                            
                           
                            this.handleInput(
                                setting?.vehicle_label,
                                setting?.language,
                                "vehicle_label"
                            );
                            this.handleInput(
                                setting?.skip_label,
                                setting?.language,
                                "skip_label"
                            );
                            this.handleInput(
                                setting?.add_vehicle_label,
                                setting?.language,
                                "add_vehicle_label"
                            );
                            this.handleInput(
                                setting?.existing_label,
                                setting?.language,
                                "existing_label"
                            );
                            this.handleInput(
                                setting?.make_label,
                                setting?.language,
                                "make_label"
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
                                setting?.model_placeholder,
                                setting?.language,
                                "model_placeholder"
                            );
                            this.handleInput(
                                setting?.type_label,
                                setting?.language,
                                "type_label"
                            );
                            this.handleInput(
                                setting?.year_label,
                                setting?.language,
                                "year_label"
                            );
                            this.handleInput(
                                setting?.color_label,
                                setting?.language,
                                "color_label"
                            );
                            this.handleInput(
                                setting?.liscense_label,
                                setting?.language,
                                "liscense_label"
                            );
                            this.handleInput(
                                setting?.car_type_label,
                                setting?.language,
                                "car_type_label"
                            );
                            this.handleInput(
                                setting?.electric_car_label,
                                setting?.language,
                                "electric_car_label"
                            );
                            this.handleInput(
                                setting?.hybrid_car_label,
                                setting?.language,
                                "hybrid_car_label"
                            );
                            this.handleInput(
                                setting?.gas_car_label,
                                setting?.language,
                                "gas_car_label"
                            );
                            this.handleInput(
                                setting?.preferences_label,
                                setting?.language,
                                "preferences_label"
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
                                setting?.animals_label,
                                setting?.language,
                                "animals_label"
                            );
                            this.handleInput(
                                setting?.animals_option1,
                                setting?.language,
                                "animals_option1"
                            );
                            this.handleInput(
                                setting?.animals_option2,
                                setting?.language,
                                "animals_option2"
                            );
                            this.handleInput(
                                setting?.animals_option3,
                                setting?.language,
                                "animals_option3"
                            );
                            this.handleInput(
                                setting?.features_label,
                                setting?.language,
                                "features_label"
                            );
                            this.handleInput(
                                setting?.features_option1,
                                setting?.language,
                                "features_option1"
                            );
                            this.handleInput(
                                setting?.features_option2,
                                setting?.language,
                                "features_option2"
                            );
                            this.handleInput(
                                setting?.features_option3,
                                setting?.language,
                                "features_option3"
                            );
                            this.handleInput(
                                setting?.features_option4,
                                setting?.language,
                                "features_option4"
                            );
                            this.handleInput(
                                setting?.features_option5,
                                setting?.language,
                                "features_option5"
                            );
                            this.handleInput(
                                setting?.features_option6,
                                setting?.language,
                                "features_option6"
                            );
                            this.handleInput(
                                setting?.features_option7,
                                setting?.language,
                                "features_option7"
                            );
                            this.handleInput(
                                setting?.features_option8,
                                setting?.language,
                                "features_option8"
                            );
                            this.handleInput(
                                setting?.features_option9,
                                setting?.language,
                                "features_option9"
                            );
                            this.handleInput(
                                setting?.features_option10,
                                setting?.language,
                                "features_option10"
                            );
                            this.handleInput(
                                setting?.features_option11,
                                setting?.language,
                                "features_option11"
                            );
                            this.handleInput(
                                setting?.features_option12,
                                setting?.language,
                                "features_option12"
                            );
                            this.handleInput(
                                setting?.features_option13,
                                setting?.language,
                                "features_option13"
                            );
                            this.handleInput(
                                setting?.features_option14,
                                setting?.language,
                                "features_option14"
                            );
                            this.handleInput(
                                setting?.features_option15,
                                setting?.language,
                                "features_option15"
                            );
                            this.handleInput(
                                setting?.features_option16,
                                setting?.language,
                                "features_option16"
                            );
                            this.handleInput(
                                setting?.features_option17,
                                setting?.language,
                                "features_option17"
                            );
                            this.handleInput(
                                setting?.booking_label,
                                setting?.language,
                                "booking_label"
                            );
                            this.handleInput(
                                setting?.booking_option1,
                                setting?.language,
                                "booking_option1"
                            );
                            this.handleInput(
                                setting?.booking_option1_tooltip,
                                setting?.language,
                                "booking_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.booking_option2,
                                setting?.language,
                                "booking_option2"
                            );
                            this.handleInput(
                                setting?.booking_option2_tooltip,
                                setting?.language,
                                "booking_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.max_back_seats_label,
                                setting?.language,
                                "max_back_seats_label"
                            );
                            this.handleInput(
                                setting?.luggage_label,
                                setting?.language,
                                "luggage_label"
                            );
                            this.handleInput(
                                setting?.luggage_option1,
                                setting?.language,
                                "luggage_option1"
                            );
                            this.handleInput(
                                setting?.luggage_option2,
                                setting?.language,
                                "luggage_option2"
                            );
                            this.handleInput(
                                setting?.luggage_option3,
                                setting?.language,
                                "luggage_option3"
                            );
                            this.handleInput(
                                setting?.luggage_option4,
                                setting?.language,
                                "luggage_option4"
                            );
                            this.handleInput(
                                setting?.luggage_option5,
                                setting?.language,
                                "luggage_option5"
                            );
                            this.handleInput(
                                setting?.luggage_checkbox_label1,
                                setting?.language,
                                "luggage_checkbox_label1"
                            );
                            this.handleInput(
                                setting?.luggage_checkbox_label1_tooltip,
                                setting?.language,
                                "luggage_checkbox_label1_tooltip"
                            );
                            // this.handleInput(
                            //     setting?.luggage_checkbox_label2,
                            //     setting?.language,
                            //     "luggage_checkbox_label2"
                            // );
                            // this.handleInput(
                            //     setting?.luggage_checkbox_label2_tooltip,
                            //     setting?.language,
                            //     "luggage_checkbox_label2_tooltip"
                            // );
                            this.handleInput(
                                setting?.price_payment_heading,
                                setting?.language,
                                "price_payment_heading"
                            );
                            this.handleInput(
                                setting?.price_per_seat_label,
                                setting?.language,
                                "price_per_seat_label"
                            );
                            this.handleInput(
                                setting?.payment_methods_label,
                                setting?.language,
                                "payment_methods_label"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label,
                                setting?.language,
                                "cancellation_policy_label"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label1,
                                setting?.language,
                                "cancellation_policy_label1"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label1_tooltip,
                                setting?.language,
                                "cancellation_policy_label1_tooltip"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label2,
                                setting?.language,
                                "cancellation_policy_label2"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label2_tooltip,
                                setting?.language,
                                "cancellation_policy_label2_tooltip"
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
                                setting?.anything_to_add_label,
                                setting?.language,
                                "anything_to_add_label"
                            );
                            this.handleInput(
                                setting?.anything_to_add_placeholder,
                                setting?.language,
                                "anything_to_add_placeholder"
                            );
                            this.handleInput(
                                setting?.disclaimers_label,
                                setting?.language,
                                "disclaimers_label"
                            );
                            this.handleInput(
                                setting?.app_disclaimers_description1,
                                setting?.language,
                                "app_disclaimers_description1"
                            );
                            this.handleInput(
                                setting?.app_disclaimers_description2,
                                setting?.language,
                                "app_disclaimers_description2"
                            );
                            this.handleInput(
                                setting?.app_disclaimers_description3,
                                setting?.language,
                                "app_disclaimers_description3"
                            );
                            this.handleInput(
                                setting?.app_disclaimers_description4,
                                setting?.language,
                                "app_disclaimers_description4"
                            );
                            this.handleInput(
                                setting?.disclaimers_description,
                                setting?.language,
                                "disclaimers_description"
                            );
                            // this.handleInput(
                            //     setting?.pink_ride_disclaimers_description,
                            //     setting?.language,
                            //     "pink_ride_disclaimers_description"
                            // );
                            // this.handleInput(
                            //     setting?.extra_care_ride_disclaimers_description,
                            //     setting?.language,
                            //     "extra_care_ride_disclaimers_description"
                            // );
                            this.handleInput(
                                setting?.agree_terms_label,
                                setting?.language,
                                "agree_terms_label"
                            );
                            this.handleInput(
                                setting?.mobile_agree_terms_label,
                                setting?.language,
                                "mobile_agree_terms_label"
                            );
                            this.handleInput(
                                setting?.mobile_term_of_service_label,
                                setting?.language,
                                "mobile_term_of_service_label"
                            );
                            this.handleInput(
                                setting?.mobile_agree_terms_and_label,
                                setting?.language,
                                "mobile_agree_terms_and_label"
                            );
                            this.handleInput(
                                setting?.mobile_term_of_use_label,
                                setting?.language,
                                "mobile_term_of_use_label"
                            );
                            this.handleInput(
                                setting?.submit_button_label,
                                setting?.language,
                                "submit_button_label"
                            );
                            this.handleInput(
                                setting?.update_button_label,
                                setting?.language,
                                "update_button_label"
                            );
                            this.handleInput(
                                setting?.repost_ride_btn_label,
                                setting?.language,
                                "repost_ride_btn_label"
                            );
                            this.handleInput(
                                setting?.extra_care_tooltip_greater_text,
                                setting?.language,
                                "extra_care_tooltip_greater_text"
                            );

                            this.handleInput(
                                setting?.extra_care_tooltip_eligible_text,
                                setting?.language,
                                "extra_care_tooltip_eligible_text"
                            );
                           
                            this.handleInput(
                                setting?.extra_care_tooltip_complete_profile_text,
                                setting?.language,
                                "extra_care_tooltip_complete_profile_text"
                            );

                            this.handleInput(
                                setting?.extra_care_tooltip_verified_text,
                                setting?.language,
                                "extra_care_tooltip_verified_text"
                            );

                            this.handleInput(
                                setting?.extra_care_tooltip_driver_license_text,
                                setting?.language,
                                "extra_care_tooltip_driver_license_text"
                            );

                            this.handleInput(
                                setting?.extra_care_tooltip_phone_number_text,
                                setting?.language,
                                "extra_care_tooltip_phone_number_text"
                            );

                            this.handleInput(
                                setting?.extra_care_tooltip_email_text,
                                setting?.language,
                                "extra_care_tooltip_email_text"
                            );

                            this.handleInput(
                                setting?.extra_care_tooltip_and_his_text,
                                setting?.language,
                                "extra_care_tooltip_and_his_text"
                            );
                            this.handleInput(
                                setting?.pink_ride_tooltip_female_text,
                                setting?.language,
                                "pink_ride_tooltip_female_text"
                            );
                            this.handleInput(
                                setting?.pink_ride_tooltip_complete_profile_text,
                                setting?.language,
                                "pink_ride_tooltip_complete_profile_text"
                            );
                            this.handleInput(
                                setting?.pink_ride_tooltip_only_text,
                                setting?.language,
                                "pink_ride_tooltip_only_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_driver_text,
                                setting?.language,
                                "pink_ride_tooltip_driver_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_with_text,
                                setting?.language,
                                "pink_ride_tooltip_with_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_phone_number_text,
                                setting?.language,
                                "pink_ride_tooltip_phone_number_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_email_text,
                                setting?.language,
                                "pink_ride_tooltip_email_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_driver_license_text,
                                setting?.language,
                                "pink_ride_tooltip_driver_license_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_verified_text,
                                setting?.language,
                                "pink_ride_tooltip_verified_text"
                            );

                            this.handleInput(
                                setting?.pink_ride_tooltip_select_this_ride_text,
                                setting?.language,
                                "pink_ride_tooltip_select_this_ride_text"
                            );
                            this.handleInput(
                                setting?.extra_care_tooltip_driver_review_text,
                                setting?.language,
                                "extra_care_tooltip_driver_review_text"
                            );
                            this.handleInput(
                                setting?.extra_care_tooltip_greater_age_text,
                                setting?.language,
                                "extra_care_tooltip_greater_age_text"
                            );
                            this.handleInput(setting?.select_vehicle_type, setting?.language, "select_vehicle_type");
                            this.handleInput(setting?.vehicle_type_placeholder, setting?.language, "vehicle_type_placeholder");
                            this.handleInput(setting?.seat_text, setting?.language, "seat_text");
                            this.handleInput(setting?.recurring_type_select_placeholder, setting?.language, "recurring_type_select_placeholder");
                            this.handleInput(setting?.recurring_type_daily_label, setting?.language, "recurring_type_daily_label");
                            this.handleInput(setting?.recurring_type_weekly_label, setting?.language, "recurring_type_weekly_label");
                            this.handleInput(setting?.post_ride_again_main_heading, setting?.language, "post_ride_again_main_heading");
                            this.handleInput(setting?.upcoming_label, setting?.language, "upcoming_label");
                            this.handleInput(setting?.completed_label, setting?.language, "completed_label");
                            this.handleInput(setting?.cancelled_label, setting?.language, "cancelled_label");
                            this.handleInput(setting?.cancelled_ride_no_found_message, setting?.language, "cancelled_ride_no_found_message");
                            this.handleInput(setting?.completed_ride_no_found_message, setting?.language, "completed_ride_no_found_message");
                            this.handleInput(setting?.upcoming_ride_no_found_message, setting?.language, "upcoming_ride_no_found_message");
                            this.handleInput(
                                setting?.extra_care_tooltip_admin_enable_text,
                                setting?.language,
                                "extra_care_tooltip_admin_enable_text"
                            );
                            this.handleInput(
                                setting?.extra_care_tooltip_admin_disable_text,
                                setting?.language,
                                "extra_care_tooltip_admin_disable_text"
                            );
                            this.handleInput(
                                setting?.pink_ride_tooltip_admin_enable_text,
                                setting?.language,
                                "pink_ride_tooltip_admin_enable_text"
                            );
                            this.handleInput(
                                setting?.pink_ride_tooltip_admin_disable_text,
                                setting?.language,
                                "pink_ride_tooltip_admin_disable_text"
                            );


                        });
                        let post_ride_page_setting_sub_detail =
                        res?.data?.data?.post_ride_page_setting_sub_detail || [];
                        post_ride_page_setting_sub_detail.map((setting) => {
                            
                            console.log(setting.extra_rides_trip_limit)
                            console.log(setting.language)
                            this.handleInput(
                                setting?.city_not_fount_contact_text,
                                setting?.language,
                                "city_not_fount_contact_text"
                            );
                            this.handleInput(
                                setting?.extra_care_popup_eligible_text,
                                setting?.language,
                                "extra_care_popup_eligible_text"
                            );
                            this.handleInput(
                                setting?.feilds_required_text,
                                setting?.language,
                                "feilds_required_text"
                            );


                        });
                    }
                  
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-post-ride-page-setting`,
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
                    `main_heading_update.main_heading_update_${language.id}`
                ) ||
                validationErros.has(
                    `post_arrived_again_label.post_arrived_again_label_${language.id}`
                ) ||
                validationErros.has(
                    `indicates_required_field_text.indicates_required_field_text_${language.id}`
                ) ||
                validationErros.has(
                    `navbar_icon.navbar_icon_${language.id}`
                ) ||
                validationErros.has(
                    `ride_info_heading.ride_info_heading_${language.id}`
                ) ||
                validationErros.has(
                    `from_label.from_label_${language.id}`
                ) ||
                validationErros.has(
                    `from_placeholder.from_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `to_label.to_label_${language.id}`
                ) ||
                validationErros.has(
                    `to_placeholder.to_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `pick_up_label.pick_up_label_${language.id}`
                ) ||
                validationErros.has(
                    `pick_up_placeholder.pick_up_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `drop_off_label.drop_off_label_${language.id}`
                ) ||
                validationErros.has(
                    `drop_off_placeholder.drop_off_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `date_time_label.date_time_label_${language.id}`
                ) ||
                validationErros.has(
                    `at_label.at_label_${language.id}`
                ) ||
                validationErros.has(
                    `recurring_label.recurring_label_${language.id}`
                ) ||
                validationErros.has(
                    `recurring_type_label.recurring_type_label_${language.id}`
                ) ||
                validationErros.has(
                    `recurring_trips_label.recurring_trips_label_${language.id}`
                ) ||
                validationErros.has(
                    `recurring_trips_placeholder.recurring_trips_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `meeting_drop_off_description_label.meeting_drop_off_description_label_${language.id}`
                ) ||
                validationErros.has(
                    `meeting_drop_off_description_placeholder.meeting_drop_off_description_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `seats_label.seats_label_${language.id}`
                ) ||
                validationErros.has(
                    `seats_middle_label.seats_middle_label_${language.id}`
                ) ||
                validationErros.has(
                    `seats_back_label.seats_back_label_${language.id}`
                ) ||
                validationErros.has(
                    `city_not_in_record.city_not_in_record_${language.id}`
                ) ||
                
                validationErros.has(
                    `city_not_fount_contact_text.city_not_fount_contact_text_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_label.vehicle_label_${language.id}`
                ) ||
                validationErros.has(
                    `skip_label.skip_label_${language.id}`
                ) ||
                validationErros.has(
                    `add_vehicle_label.add_vehicle_label_${language.id}`
                ) ||
                validationErros.has(
                    `existing_label.existing_label_${language.id}`
                ) ||
                validationErros.has(
                    `make_label.make_label_${language.id}`
                ) ||
                validationErros.has(
                    `make_placeholder.make_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `model_label.model_label_${language.id}`
                ) ||
                validationErros.has(
                    `model_placeholder.model_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `type_label.type_label_${language.id}`
                ) ||
                validationErros.has(
                    `year_label.year_label_${language.id}`
                ) ||
                validationErros.has(
                    `color_label.color_label_${language.id}`
                ) ||
                validationErros.has(
                    `liscense_label.liscense_label_${language.id}`
                ) ||
                validationErros.has(
                    `car_type_label.car_type_label_${language.id}`
                ) ||
                validationErros.has(
                    `electric_car_label.electric_car_label_${language.id}`
                ) ||
                validationErros.has(
                    `hybrid_car_label.hybrid_car_label_${language.id}`
                ) ||
                validationErros.has(
                    `gas_car_label.gas_car_label_${language.id}`
                ) ||
                validationErros.has(
                    `preferences_label.preferences_label_${language.id}`
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
                    `animals_label.animals_label_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option1.animals_option1_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option2.animals_option2_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option3.animals_option3_${language.id}`
                ) ||
                validationErros.has(
                    `features_label.features_label_${language.id}`
                ) ||
                validationErros.has(
                    `features_option1.features_option1_${language.id}`
                ) ||
                validationErros.has(
                    `features_option2.features_option2_${language.id}`
                ) ||
                validationErros.has(
                    `features_option3.features_option3_${language.id}`
                ) ||
                validationErros.has(
                    `features_option4.features_option4_${language.id}`
                ) ||
                validationErros.has(
                    `features_option5.features_option5_${language.id}`
                ) ||
                validationErros.has(
                    `features_option6.features_option6_${language.id}`
                ) ||
                validationErros.has(
                    `features_option7.features_option7_${language.id}`
                ) ||
                validationErros.has(
                    `features_option8.features_option8_${language.id}`
                ) ||
                validationErros.has(
                    `features_option9.features_option9_${language.id}`
                ) ||
                validationErros.has(
                    `features_option10.features_option10_${language.id}`
                ) ||
                validationErros.has(
                    `features_option11.features_option11_${language.id}`
                ) ||
                validationErros.has(
                    `features_option12.features_option12_${language.id}`
                ) ||
                validationErros.has(
                    `features_option13.features_option13_${language.id}`
                ) ||
                validationErros.has(
                    `features_option14.features_option14_${language.id}`
                ) ||
                validationErros.has(
                    `features_option15.features_option15_${language.id}`
                ) ||
                validationErros.has(
                    `features_option16.features_option16_${language.id}`
                ) ||
                validationErros.has(
                    `features_option17.features_option17_${language.id}`
                ) ||
                validationErros.has(
                    `booking_label.booking_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option1.booking_option1_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option1_tooltip.booking_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option2.booking_option2_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option2_tooltip.booking_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `max_back_seats_label.max_back_seats_label_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_label.luggage_label_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option1.luggage_option1_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2.luggage_option2_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3.luggage_option3_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4.luggage_option4_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5.luggage_option5_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_checkbox_label1.luggage_checkbox_label1_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_checkbox_label1_tooltip.luggage_checkbox_label1_tooltip_${language.id}`
                ) 
                // ||
                // validationErros.has(
                //     `luggage_checkbox_label2.luggage_checkbox_label2_${language.id}`
                // ) ||
                // validationErros.has(
                //     `luggage_checkbox_label2_tooltip.luggage_checkbox_label2_tooltip_${language.id}`
                // ) 
                ||
                validationErros.has(
                    `price_payment_heading.price_payment_heading_${language.id}`
                ) ||
                validationErros.has(
                    `price_per_seat_label.price_per_seat_label_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_label.payment_methods_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label.cancellation_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label1.cancellation_policy_label1_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label1_tooltip.cancellation_policy_label1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label2.cancellation_policy_label2_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label2_tooltip.cancellation_policy_label2_tooltip_${language.id}`
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
                    `anything_to_add_label.anything_to_add_label_${language.id}`
                ) ||
                validationErros.has(
                    `anything_to_add_placeholder.anything_to_add_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `disclaimers_label.disclaimers_label_${language.id}`
                ) ||
                validationErros.has(
                    `app_disclaimers_description1.app_disclaimers_description1_${language.id}`
                ) ||
                validationErros.has(
                    `app_disclaimers_description2.app_disclaimers_description2_${language.id}`
                ) ||
                validationErros.has(
                    `app_disclaimers_description3.app_disclaimers_description3_${language.id}`
                ) ||
                validationErros.has(
                    `app_disclaimers_description4.app_disclaimers_description4_${language.id}`
                ) ||
                validationErros.has(
                    `disclaimers_description.disclaimers_description_${language.id}`
                ) ||
                validationErros.has(
                    `pink_ride_disclaimers_description.pink_ride_disclaimers_description_${language.id}`
                ) ||
                validationErros.has(
                    `extra_care_ride_disclaimers_description.extra_care_ride_disclaimers_description_${language.id}`
                ) ||
                validationErros.has(
                    `agree_terms_label.agree_terms_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_agree_terms_label.mobile_agree_terms_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_term_of_service_label.mobile_term_of_service_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_agree_terms_and_label.mobile_agree_terms_and_label_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_term_of_use_label.mobile_term_of_use_label_${language.id}`
                ) ||
                validationErros.has(
                    `update_button_label.update_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `repost_ride_btn_label.repost_ride_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `submit_button_label.submit_button_label_${language.id}`
                )
            );
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
                const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}upload-post-ride-page-setting-excel`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
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
    },
};
</script>
