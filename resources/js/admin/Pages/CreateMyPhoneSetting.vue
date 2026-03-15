<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    My Phone settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <ExcelBulkImport
                        title="My Phone"
                        mode="all_languages"
                        download-endpoint="download-my-phone-setting-template"
                        upload-endpoint="upload-my-phone-setting-excel"
                        @success="fetchMyPhoneSetting"
                    />

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

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`unverified_number_label_${activeLanguageId}`">
                                                        Unverified Number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`unverified_number_label_${activeLanguageId}`"
                                                    :id="`unverified_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'unverified_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'unverified_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `unverified_number_label.unverified_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `unverified_number_label.unverified_number_label_${activeLanguageId}`
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
                                                        :for="`verified_number_label_${activeLanguageId}`">
                                                        Verified Number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verified_number_label_${activeLanguageId}`"
                                                    :id="`verified_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verified_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verified_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verified_number_label.verified_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verified_number_label.verified_number_label_${activeLanguageId}`
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
                                                        :for="`primary_number_label_${activeLanguageId}`">
                                                        Primary Number label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`primary_number_label_${activeLanguageId}`"
                                                    :id="`primary_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'primary_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'primary_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `primary_number_label.primary_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `primary_number_label.primary_number_label_${activeLanguageId}`
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
                                                    :for="`phone_no_description_text_${activeLanguageId}`"
                                                    >Phone No Description</label
                                                >
                                            </div>
                                            <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'phone_no_description_text'
                                                        )
                                                    "
                                                    :ref="`phone_no_description_text_${language.id}`"
                                                    :id="`phone_no_description_text_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `phone_no_description_text`
                                                        ][
                                                            `phone_no_description_text_${language?.id}`
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
                                                    `phone_no_description_text.phone_no_description_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_no_description_text.phone_no_description_text_${activeLanguageId}`
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
                                                        :for="`mobile_verify_button_text_${activeLanguageId}`"
                                                        >Verify Button Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_verify_button_text_${activeLanguageId}`"
                                                    :id="`mobile_verify_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_verify_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_verify_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_verify_button_text.mobile_verify_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_verify_button_text.mobile_verify_button_text_${activeLanguageId}`
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
                                                        :for="`web_send_verification_code_button_text_${activeLanguageId}`"
                                                        >Send Verification Code (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_send_verification_code_button_text_${activeLanguageId}`"
                                                    :id="`web_send_verification_code_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_send_verification_code_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_send_verification_code_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_send_verification_code_button_text.web_send_verification_code_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_send_verification_code_button_text.web_send_verification_code_button_text_${activeLanguageId}`
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
                                                        :for="`delete_button_text_${activeLanguageId}`"
                                                        >Delete Button Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`delete_button_text_${activeLanguageId}`"
                                                    :id="`delete_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'delete_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'delete_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `delete_button_text.delete_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `delete_button_text.delete_button_text_${activeLanguageId}`
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
                                                        :for="`mobile_country_code_label_${activeLanguageId}`"
                                                        >Country Code Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_country_code_label_${activeLanguageId}`"
                                                    :id="`mobile_country_code_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_country_code_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_country_code_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_country_code_label.mobile_country_code_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_country_code_label.mobile_country_code_label_${activeLanguageId}`
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
                                                        :for="`country_code_label_web_${activeLanguageId}`"
                                                        >Country Code Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_code_label_web_${activeLanguageId}`"
                                                    :id="`country_code_label_web_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_code_label_web'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_code_label_web'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_code_label_web.country_code_label_web_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_code_label_web.country_code_label_web_${activeLanguageId}`
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
                                                        :for="`country_id_label_web_${activeLanguageId}`"
                                                        >Country Name Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`country_id_label_web_${activeLanguageId}`"
                                                    :id="`country_id_label_web_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'country_id_label_web'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'country_id_label_web'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `country_id_label_web.country_id_label_web_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `country_id_label_web.country_id_label_web_${activeLanguageId}`
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
                                                        >Contry Code Placeholder</label
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
                                                        :for="`mobile_phone_number_label_${activeLanguageId}`"
                                                        >Phone Number Label (mobile)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`mobile_phone_number_label_${activeLanguageId}`"
                                                    :id="`mobile_phone_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_phone_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_phone_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `mobile_phone_number_label.mobile_phone_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `mobile_phone_number_label.mobile_phone_number_label_${activeLanguageId}`
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
                                                        :for="`phone_number_label_web_${activeLanguageId}`"
                                                        >Phone Number Label (web)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_number_label_web_${activeLanguageId}`"
                                                    :id="`phone_number_label_web_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'phone_number_label_web'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'phone_number_label_web'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `phone_number_label_web.phone_number_label_web_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_number_label_web.phone_number_label_web_${activeLanguageId}`
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
                                                        :for="`phone_number_placeholder_${activeLanguageId}`"
                                                        >Phone Number Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`phone_number_placeholder_${activeLanguageId}`"
                                                    :id="`phone_number_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'phone_number_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'phone_number_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `phone_number_placeholder.phone_number_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_number_placeholder.phone_number_placeholder_${activeLanguageId}`
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
                                                        :for="`save_phoneno_button_text_${activeLanguageId}`"
                                                        >Save Phone button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`save_phoneno_button_text_${activeLanguageId}`"
                                                    :id="`save_phoneno_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'save_phoneno_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'save_phoneno_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `save_phoneno_button_text.save_phoneno_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `save_phoneno_button_text.save_phoneno_button_text_${activeLanguageId}`
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
                                                        :for="`send_verification_code_button_text_${activeLanguageId}`"
                                                        >Verification Code Button</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`send_verification_code_button_text_${activeLanguageId}`"
                                                    :id="`send_verification_code_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'send_verification_code_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'send_verification_code_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `send_verification_code_button_text.send_verification_code_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `send_verification_code_button_text.send_verification_code_button_text_${activeLanguageId}`
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
                                                        :for="`resend_code_btn_label_${activeLanguageId}`"
                                                        >Resend Code Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`resend_code_btn_label_${activeLanguageId}`"
                                                    :id="`resend_code_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'resend_code_btn_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'resend_code_btn_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `resend_code_btn_label.resend_code_btn_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `resend_code_btn_label.resend_code_btn_label_${activeLanguageId}`
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
                                                        :for="`request_code_text_${activeLanguageId}`"
                                                        >Request Code Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`request_code_text_${activeLanguageId}`"
                                                    :id="`request_code_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'request_code_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'request_code_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `request_code_text.request_code_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `request_code_text.request_code_text_${activeLanguageId}`
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
                                                        :for="`second_text_${activeLanguageId}`"
                                                        >Second Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`second_text_${activeLanguageId}`"
                                                    :id="`second_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'second_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'second_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `second_text.second_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `second_text.second_text_${activeLanguageId}`
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
                                                        :for="`verify_phone_number_label_${activeLanguageId}`"
                                                        >Verify Phone Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_phone_number_label_${activeLanguageId}`"
                                                    :id="`verify_phone_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verify_phone_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verify_phone_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verify_phone_number_label.verify_phone_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verify_phone_number_label.verify_phone_number_label_${activeLanguageId}`
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
                                                        >Enter Code Label</label
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
                                                        :for="`otp_code_description_${activeLanguageId}`"
                                                        >Opt Code Description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`otp_code_description_${activeLanguageId}`"
                                                    :id="`otp_code_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'otp_code_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'otp_code_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `otp_code_description.otp_code_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `otp_code_description.otp_code_description_${activeLanguageId}`
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
                                                        :for="`verify_phone_number_heading_${activeLanguageId}`"
                                                        >Verify Phone Number </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`verify_phone_number_heading_${activeLanguageId}`"
                                                    :id="`verify_phone_number_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'verify_phone_number_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'verify_phone_number_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `verify_phone_number_heading.verify_phone_number_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `verify_phone_number_heading.verify_phone_number_heading_${activeLanguageId}`
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
                                                        :for="`phone_no_description_text1_${activeLanguageId}`"
                                                        > Phone Number Description 1 </label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'phone_no_description_text1'
                                                        )
                                                    "
                                                    :ref="`phone_no_description_text1_${language.id}`"
                                                    :id="`phone_no_description_text1_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `phone_no_description_text1`
                                                        ][
                                                            `phone_no_description_text1_${language?.id}`
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
                                                        `phone_no_description_text1.phone_no_description_text1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `phone_no_description_text1.phone_no_description_text1_${activeLanguageId}`
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
                                                        :for="`set_as_default_label_${activeLanguageId}`"
                                                        >Set As Default </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`set_as_default_label_${activeLanguageId}`"
                                                    :id="`set_as_default_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'set_as_default_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'set_as_default_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `set_as_default_label.set_as_default_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `set_as_default_label.set_as_default_label_${activeLanguageId}`
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
                                                        :for="`default_verified_number_label_${activeLanguageId}`"
                                                        >Default Verified  Number </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`default_verified_number_label_${activeLanguageId}`"
                                                    :id="`default_verified_number_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'default_verified_number_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'default_verified_number_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `default_verified_number_label.default_verified_number_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `default_verified_number_label.default_verified_number_label_${activeLanguageId}`
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
                                                        :for="`remove_description_${activeLanguageId}`"
                                                        >Remove Description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`remove_description_${activeLanguageId}`"
                                                    :id="`remove_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'remove_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'remove_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `remove_description.remove_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `remove_description.remove_description_${activeLanguageId}`
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
                                                        :for="`add_another_phone_number_title_${activeLanguageId}`"
                                                        >Add another phone number title</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`add_another_phone_number_title_${activeLanguageId}`"
                                                    :id="`add_another_phone_number_title_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'add_another_phone_number_title'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_another_phone_number_title'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `add_another_phone_number_title.add_another_phone_number_title_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `add_another_phone_number_title.add_another_phone_number_title_${activeLanguageId}`
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
                                </div>    </AppLayout>
</template>
<script>
import Editor from "@tinymce/tinymce-vue";
import axios from "axios";
import ErrorHandling from "../../ErrorHandling.js";
import ExcelBulkImport from "../components/ExcelBulkImport.vue";
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
        ExcelBulkImport,
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
                            this.handleInput("", language, "phone_no_description_text");
                            this.handleInput("", language, "unverified_number_label");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "mobile_verify_button_text");
                            this.handleInput("", language, "web_send_verification_code_button_text");
                            this.handleInput("", language, "delete_button_text");
                            this.handleInput("", language, "mobile_country_code_label");
                            this.handleInput("", language, "country_code_label_web");
                            this.handleInput("", language, "country_id_label_web");
                            this.handleInput("", language, "country_code_placeholder");
                            this.handleInput("", language, "mobile_phone_number_label");
                            this.handleInput("", language, "phone_number_label_web");
                            this.handleInput("", language, "phone_number_placeholder");
                            this.handleInput("", language, "save_phoneno_button_text");
                            this.handleInput("", language, "send_verification_code_button_text");
                            this.handleInput("", language, "verify_phone_number_heading");
                            this.handleInput("", language, "otp_code_description");
                            this.handleInput("", language, "enter_code_label");
                            this.handleInput("", language, "verify_phone_number_label");
                            this.handleInput("", language, "second_text");
                            this.handleInput("", language, "request_code_text");
                            this.handleInput("", language, "resend_code_btn_label");
                            this.handleInput("", language, "set_as_default_label");
                            this.handleInput("", language, "primary_number_label");
                            this.handleInput("", language, "remove_description");
                            this.handleInput("", language, "default_verified_number_label");
                            this.handleInput("", language, "verified_number_label");
                            this.handleInput("", language, "phone_no_description_text1");
                            this.handleInput("", language, "add_another_phone_number_title");
                        });
                        this.fetchMyPhoneSetting();
                    }
                });
        },
        fetchMyPhoneSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-my-phone-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let my_phone_no_setting_detail =
                            res?.data?.data?.my_phone_no_setting_detail || [];
                        my_phone_no_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.phone_no_description_text,
                                setting?.language,
                                "phone_no_description_text"
                            );
                            this.handleInput(
                                setting?.unverified_number_label,
                                setting?.language,
                                "unverified_number_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.mobile_verify_button_text,
                                setting?.language,
                                "mobile_verify_button_text"
                            );
                            this.handleInput(
                                setting?.web_send_verification_code_button_text,
                                setting?.language,
                                "web_send_verification_code_button_text"
                            );
                             this.handleInput(
                                setting?.delete_button_text,
                                setting?.language,
                                "delete_button_text"
                            );

                            this.handleInput(
                                setting?.country_code_placeholder,
                                setting?.language,
                                "country_code_placeholder"
                            );
                            this.handleInput(
                                setting?.mobile_phone_number_label,
                                setting?.language,
                                "mobile_phone_number_label"
                            );
                            this.handleInput(
                                setting?.phone_number_label_web,
                                setting?.language,
                                "phone_number_label_web"
                            );
                             this.handleInput(
                                setting?.phone_number_placeholder,
                                setting?.language,
                                "phone_number_placeholder"
                            );
                              this.handleInput(
                                setting?.send_verification_code_button_text,
                                setting?.language,
                                "send_verification_code_button_text"
                            );
                            this.handleInput(
                                setting?.save_phoneno_button_text,
                                setting?.language,
                                "save_phoneno_button_text"
                            );
                            this.handleInput(
                                setting?.verify_phone_number_heading,
                                setting?.language,
                                "verify_phone_number_heading"
                            );
                            this.handleInput(
                                setting?.otp_code_description,
                                setting?.language,
                                "otp_code_description"
                            );
                            this.handleInput(
                                setting?.enter_code_label,
                                setting?.language,
                                "enter_code_label"
                            );
                            this.handleInput(
                                setting?.verify_phone_number_label,
                                setting?.language,
                                "verify_phone_number_label"
                            );
                            this.handleInput(
                                setting?.second_text,
                                setting?.language,
                                "second_text"
                            );
                            this.handleInput(
                                setting?.request_code_text,
                                setting?.language,
                                "request_code_text"
                            );
                            this.handleInput(
                                setting?.resend_code_btn_label,
                                setting?.language,
                                "resend_code_btn_label"
                            );
                            this.handleInput(
                                setting?.set_as_default_label,
                                setting?.language,
                                "set_as_default_label"
                            );
                            this.handleInput(
                                setting?.primary_number_label,
                                setting?.language,
                                "primary_number_label"
                            );
                            this.handleInput(
                                setting?.remove_description,
                                setting?.language,
                                "remove_description"
                            );
                            this.handleInput(
                                setting?.default_verified_number_label,
                                setting?.language,
                                "default_verified_number_label"
                            );
                            this.handleInput(
                                setting?.verified_number_label,
                                setting?.language,
                                "verified_number_label"
                            );
                            this.handleInput(
                                setting?.phone_no_description_text1,
                                setting?.language,
                                "phone_no_description_text1"
                            );
                            this.handleInput(
                                setting?.mobile_country_code_label,
                                setting?.language,
                                "mobile_country_code_label"
                            );
                            this.handleInput(
                                setting?.country_code_label_web,
                                setting?.language,
                                "country_code_label_web"
                            );
                            this.handleInput(
                                setting?.country_id_label_web,
                                setting?.language,
                                "country_id_label_web"
                            );

                            this.handleInput(
                                setting?.add_another_phone_number_title,
                                setting?.language,
                                "add_another_phone_number_title"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-my-phone-setting`,
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
                    `unverified_number_label.unverified_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_verify_button_text.mobile_verify_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `web_send_verification_code_button_text.web_send_verification_code_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `add_another_phone_number_title.add_another_phone_number_title_${language.id}`
                ) ||
                validationErros.has(
                    `delete_button_text.delete_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_country_code_label.mobile_country_code_label_${language.id}`
                ) ||
                validationErros.has(
                    `country_code_label_web.country_code_label_web_${language.id}`
                ) ||
                validationErros.has(
                    `country_id_label_web.country_id_label_web_${language.id}`
                ) ||
                validationErros.has(
                    `mobile_phone_number_label.mobile_phone_number_label_${language.id}`
                ) ||

                validationErros.has(
                    `phone_number_label_web.phone_number_label_web_${language.id}`
                ) ||
                validationErros.has(
                    `phone_number_placeholder.phone_number_placeholder_${language.id}`
                )
                ||
                validationErros.has(
                    `save_phoneno_button_text.save_phoneno_button_text_${language.id}`
                )||
                validationErros.has(
                    `send_verification_code_button_text.send_verification_code_button_text_${language.id}`
                )
                  ||
                validationErros.has(
                    `verified_number_label.verified_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `default_verified_number_label.default_verified_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `set_as_default_label.set_as_default_label_${language.id}`
                ) ||
                validationErros.has(
                    `primary_number_label.primary_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `remove_description.remove_description_${language.id}`
                ) ||
                validationErros.has(
                    `resend_code_btn_label.resend_code_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `request_code_text.request_code_text_${language.id}`
                ) ||
                validationErros.has(
                    `second_text.second_text_${language.id}`
                ) ||
                validationErros.has(
                    `verify_phone_number_label.verify_phone_number_label_${language.id}`
                ) ||
                validationErros.has(
                    `enter_code_label.enter_code_label_${language.id}`
                ) ||
                validationErros.has(
                    `otp_code_description.otp_code_description_${language.id}`
                ) ||
                validationErros.has(
                    `verify_phone_number_heading.verify_phone_number_heading_${language.id}`
                )||
                validationErros.has(
                    `country_code_placeholder.country_code_placeholder_${language.id}`
                )
            );
        },
    },
};
</script>
