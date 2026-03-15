<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Close My Account settings
                                </h3>
                            </div>
                        </div>
                    </header>

                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <ExcelBulkImport
                            title="Close My Account"
                            mode="all_languages"
                            download-endpoint="download-close-account-setting-template"
                            upload-endpoint="upload-close-account-setting-excel"
                            @success="fetchCloseAccountPageSetting"
                        />
                    </div>

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
                                                        :for="`closing_account_label_${activeLanguageId}`">
                                                        Closing Account label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`closing_account_label_${activeLanguageId}`"
                                                    :id="`closing_account_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'closing_account_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'closing_account_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `closing_account_label.closing_account_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `closing_account_label.closing_account_label_${activeLanguageId}`
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
                                                    :for="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    >Required Indicate Field (mobile)</label
                                                >
                                            </div>
                                            <input
                                                    type="text"
                                                    :name="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    :id="`mobile_indicate_required_field_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'mobile_indicate_required_field_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'mobile_indicate_required_field_label'
                                                        )
                                                    "
                                                />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${activeLanguageId}`
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
                                                        :for="`apply_reason_label_${activeLanguageId}`"
                                                        >Apply Reason Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`apply_reason_label_${activeLanguageId}`"
                                                    :id="`apply_reason_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'apply_reason_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'apply_reason_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `apply_reason_label.apply_reason_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `apply_reason_label.apply_reason_label_${activeLanguageId}`
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
                                                        :for="`reason_label_${activeLanguageId}`"
                                                        >Reason Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`reason_label_${activeLanguageId}`"
                                                    :id="`reason_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'reason_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'reason_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `reason_label.reason_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `reason_label.reason_label_${activeLanguageId}`
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
                                                        :for="`not_say_checkbox_label_${activeLanguageId}`"
                                                        >Not To Say Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`not_say_checkbox_label_${activeLanguageId}`"
                                                    :id="`not_say_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'not_say_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'not_say_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `not_say_checkbox_label.not_say_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `not_say_checkbox_label.not_say_checkbox_label_${activeLanguageId}`
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
                                                        :for="`check_box_validation_message_${activeLanguageId}`"
                                                        >Not To Say Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`check_box_validation_message_${activeLanguageId}`"
                                                    :id="`check_box_validation_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'check_box_validation_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'check_box_validation_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `check_box_validation_message.check_box_validation_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `check_box_validation_message.check_box_validation_message_${activeLanguageId}`
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
                                                        :for="`customer_service_checkbox_label_${activeLanguageId}`"
                                                        >Customer Service Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`customer_service_checkbox_label_${activeLanguageId}`"
                                                    :id="`customer_service_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'customer_service_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'customer_service_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `customer_service_checkbox_label.customer_service_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `customer_service_checkbox_label.customer_service_checkbox_label_${activeLanguageId}`
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
                                                        :for="`technical_issue_checkbox_label_${activeLanguageId}`"
                                                        >Technical Issue Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`technical_issue_checkbox_label_${activeLanguageId}`"
                                                    :id="`technical_issue_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'technical_issue_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'technical_issue_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `technical_issue_checkbox_label.technical_issue_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `technical_issue_checkbox_label.technical_issue_checkbox_label_${activeLanguageId}`
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
                                                        :for="`dont_use_checkbox_label_${activeLanguageId}`"
                                                        >Dont't Use Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`dont_use_checkbox_label_${activeLanguageId}`"
                                                    :id="`dont_use_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'dont_use_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'dont_use_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `dont_use_checkbox_label.dont_use_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `dont_use_checkbox_label.dont_use_checkbox_label_${activeLanguageId}`
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
                                                        :for="`another_account_checkbox_label_${activeLanguageId}`"
                                                        >Another Account Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`another_account_checkbox_label_${activeLanguageId}`"
                                                    :id="`another_account_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'another_account_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'another_account_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `another_account_checkbox_label.another_account_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `another_account_checkbox_label.another_account_checkbox_label_${activeLanguageId}`
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
                                                        :for="`did_not_get_booking_checkbox_label_${activeLanguageId}`"
                                                        >Did Not Get Booking Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`did_not_get_booking_checkbox_label_${activeLanguageId}`"
                                                    :id="`did_not_get_booking_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'did_not_get_booking_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'did_not_get_booking_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `did_not_get_booking_checkbox_label.did_not_get_booking_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `did_not_get_booking_checkbox_label.did_not_get_booking_checkbox_label_${activeLanguageId}`
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
                                                        :for="`did_not_find_ride_checkbox_label_${activeLanguageId}`"
                                                        >Did Not Find Ride Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`did_not_find_ride_checkbox_label_${activeLanguageId}`"
                                                    :id="`did_not_find_ride_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'did_not_find_ride_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'did_not_find_ride_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `did_not_find_ride_checkbox_label.did_not_find_ride_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `did_not_find_ride_checkbox_label.did_not_find_ride_checkbox_label_${activeLanguageId}`
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
                                                        :for="`why_closing_account_label_${activeLanguageId}`"
                                                        >Why Closing Account Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`why_closing_account_label_${activeLanguageId}`"
                                                    :id="`why_closing_account_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'why_closing_account_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'why_closing_account_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `why_closing_account_label.why_closing_account_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `why_closing_account_label.why_closing_account_label_${activeLanguageId}`
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
                                                        :for="`why_closing_account_placeholder_${activeLanguageId}`"
                                                        >Why Closing Account placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`why_closing_account_placeholder_${activeLanguageId}`"
                                                    :id="`why_closing_account_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'why_closing_account_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'why_closing_account_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `why_closing_account_placeholder.why_closing_account_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `why_closing_account_placeholder.why_closing_account_placeholder_${activeLanguageId}`
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
                                                        :for="`prefer_not_checkbox_label_${activeLanguageId}`"
                                                        >Prefer Not To Say Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`prefer_not_checkbox_label_${activeLanguageId}`"
                                                    :id="`prefer_not_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'prefer_not_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'prefer_not_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `prefer_not_checkbox_label.prefer_not_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `prefer_not_checkbox_label.prefer_not_checkbox_label_${activeLanguageId}`
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
                                                        :for="`yes_checkbox_label_${activeLanguageId}`"
                                                        >Yes Checkbox Label</label
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
                                                        :for="`recommend_heading_${activeLanguageId}`"
                                                        >Recommend Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`recommend_heading_${activeLanguageId}`"
                                                    :id="`recommend_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'recommend_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recommend_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `recommend_heading.recommend_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `recommend_heading.recommend_heading_${activeLanguageId}`
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
                                                        :for="`other_checkbox_label_${activeLanguageId}`"
                                                        >Other Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`other_checkbox_label_${activeLanguageId}`"
                                                    :id="`other_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'other_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'other_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `other_checkbox_label.other_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `other_checkbox_label.other_checkbox_label_${activeLanguageId}`
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
                                                        :for="`did_not_find_destination_checkbox_label_${activeLanguageId}`"
                                                        >Did Not Find Destination Checkbox Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`did_not_find_destination_checkbox_label_${activeLanguageId}`"
                                                    :id="`did_not_find_destination_checkbox_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'did_not_find_destination_checkbox_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'did_not_find_destination_checkbox_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `did_not_find_destination_checkbox_label.did_not_find_destination_checkbox_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `did_not_find_destination_checkbox_label.did_not_find_destination_checkbox_label_${activeLanguageId}`
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
                                                        :for="`warning_text_${activeLanguageId}`"
                                                        > Warning Text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`warning_text_${activeLanguageId}`"
                                                    :id="`warning_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'warning_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'warning_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `warning_text.warning_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `warning_text.warning_text_${activeLanguageId}`
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
                                                        :for="`improve_label_${activeLanguageId}`"
                                                        >Improve Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`improve_label_${activeLanguageId}`"
                                                    :id="`improve_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'improve_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'improve_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `improve_label.improve_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `improve_label.improve_label_${activeLanguageId}`
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
                                                        :for="`improve_placeholder_${activeLanguageId}`"
                                                        >Improve Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`improve_placeholder_${activeLanguageId}`"
                                                    :id="`improve_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'improve_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'improve_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `improve_placeholder.improve_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `improve_placeholder.improve_placeholder_${activeLanguageId}`
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
                                                        :for="`close_my_account_checkbox_${activeLanguageId}`"
                                                        >Close My Account Checkbox </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`close_my_account_checkbox_${activeLanguageId}`"
                                                    :id="`close_my_account_checkbox_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'close_my_account_checkbox'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'close_my_account_checkbox'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `close_my_account_checkbox.close_my_account_checkbox_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `close_my_account_checkbox.close_my_account_checkbox_${activeLanguageId}`
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
                                                        :for="`close_my_account_checkbox_error_${activeLanguageId}`"
                                                        >Close My Account Checkbox error</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`close_my_account_checkbox_error_${activeLanguageId}`"
                                                    :id="`close_my_account_checkbox_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'close_my_account_checkbox_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'close_my_account_checkbox_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `close_my_account_checkbox_error.close_my_account_checkbox_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `close_my_account_checkbox_error.close_my_account_checkbox_error_${activeLanguageId}`
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
                                                        :for="`close_account_button_text_${activeLanguageId}`"
                                                        >Close Account Button Text </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`close_account_button_text_${activeLanguageId}`"
                                                    :id="`close_account_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'close_account_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'close_account_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `close_account_button_text.close_account_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `close_account_button_text.close_account_button_text_${activeLanguageId}`
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
                                                        :for="`difficulties_making_receiving_payments_label_${activeLanguageId}`"
                                                        >Difficulties Making Revceiving Payments Label </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`difficulties_making_receiving_payments_label_${activeLanguageId}`"
                                                    :id="`difficulties_making_receiving_payments_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'difficulties_making_receiving_payments_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'difficulties_making_receiving_payments_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `difficulties_making_receiving_payments_label.difficulties_making_receiving_payments_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `difficulties_making_receiving_payments_label.difficulties_making_receiving_payments_label_${activeLanguageId}`
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
                                                        :for="`web_closing_account_reason_label_${activeLanguageId}`"
                                                        >Web closing account reason label </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_closing_account_reason_label_${activeLanguageId}`"
                                                    :id="`web_closing_account_reason_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_closing_account_reason_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_closing_account_reason_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_closing_account_reason_label.web_closing_account_reason_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_closing_account_reason_label.web_closing_account_reason_label_${activeLanguageId}`
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
                                                        :for="`web_irreversible_label_${activeLanguageId}`"
                                                        >Web irreversible label </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`web_irreversible_label_${activeLanguageId}`"
                                                    :id="`web_irreversible_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'web_irreversible_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'web_irreversible_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `web_irreversible_label.web_irreversible_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `web_irreversible_label.web_irreversible_label_${activeLanguageId}`
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
                                                        :for="`close_account_sure_message_text_${activeLanguageId}`"
                                                        >Close account sure message text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`close_account_sure_message_text_${activeLanguageId}`"
                                                    :id="`close_account_sure_message_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'close_account_sure_message_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'close_account_sure_message_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `close_account_sure_message_text.close_account_sure_message_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `close_account_sure_message_text.close_account_sure_message_text_${activeLanguageId}`
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
                                                        :for="`close_it_button_label_${activeLanguageId}`"
                                                        >Close it button label </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`close_it_button_label_${activeLanguageId}`"
                                                    :id="`close_it_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'close_it_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'close_it_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `close_it_button_label.close_it_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `close_it_button_label.close_it_button_label_${activeLanguageId}`
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
                                                        :for="`take_me_back_button_label_${activeLanguageId}`"
                                                        >Take me back button label </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`take_me_back_button_label_${activeLanguageId}`"
                                                    :id="`take_me_back_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'take_me_back_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'take_me_back_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `take_me_back_button_label.take_me_back_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `take_me_back_button_label.take_me_back_button_label_${activeLanguageId}`
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
    components: {
        editor: Editor,
        ExcelBulkImport,
    },
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
                bullist numlist outdent indent | removeformat | table | image | code | link | help",
            },
        };
    },
    computed: {
        mixAdminApiUrl() { return process.env.MIX_ADMIN_API_URL; }
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
                            this.handleInput("", language, "mobile_indicate_required_field_label");
                            this.handleInput("", language, "closing_account_label");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "apply_reason_label");
                            this.handleInput("", language, "reason_label");
                            this.handleInput("", language, "not_say_checkbox_label");
                            this.handleInput("", language, "check_box_validation_message");
                            
                            this.handleInput("", language, "customer_service_checkbox_label");
                            this.handleInput("", language, "technical_issue_checkbox_label");
                            this.handleInput("", language, "dont_use_checkbox_label");
                            this.handleInput("", language, "another_account_checkbox_label");
                            this.handleInput("", language, "did_not_get_booking_checkbox_label");
                            this.handleInput("", language, "did_not_find_ride_checkbox_label");
                            this.handleInput("", language, "did_not_find_destination_checkbox_label");
                            this.handleInput("", language, "other_checkbox_label");
                            this.handleInput("", language, "recommend_heading");
                            this.handleInput("", language, "yes_checkbox_label");
                            this.handleInput("", language, "no_checkbox_label");
                            this.handleInput("", language, "prefer_not_checkbox_label");
                            this.handleInput("", language, "why_closing_account_label");
                            this.handleInput("", language, "why_closing_account_placeholder");
                            this.handleInput("", language, "improve_label");
                            this.handleInput("", language, "improve_placeholder");
                            this.handleInput("", language, "close_my_account_checkbox");
                            this.handleInput("", language, "close_my_account_checkbox_error");
                            this.handleInput("", language, "close_account_button_text");
                            this.handleInput("", language, "warning_text");
                            this.handleInput("", language, "difficulties_making_receiving_payments_label");
                            this.handleInput("", language, "web_closing_account_reason_label");
                            this.handleInput("", language, "web_irreversible_label");
                            this.handleInput("", language, "close_account_sure_message_text");
                            this.handleInput("", language, "close_it_button_label");
                            this.handleInput("", language, "take_me_back_button_label");
                        });
                        this.fetchCloseAccountPageSetting();
                    }
                });
        },
        fetchCloseAccountPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-close-account-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let close_account_setting_detail =
                            res?.data?.data?.close_account_setting_detail || [];
                        close_account_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.mobile_indicate_required_field_label,
                                setting?.language,
                                "mobile_indicate_required_field_label"
                            );
                            this.handleInput(
                                setting?.closing_account_label,
                                setting?.language,
                                "closing_account_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.apply_reason_label,
                                setting?.language,
                                "apply_reason_label"
                            );
                            this.handleInput(
                                setting?.reason_label,
                                setting?.language,
                                "reason_label"
                            );
                             this.handleInput(
                                setting?.not_say_checkbox_label,
                                setting?.language,
                                "not_say_checkbox_label"
                            );

                            this.handleInput(
                                setting?.check_box_validation_message,
                                setting?.language,
                                "check_box_validation_message"
                            );

                            this.handleInput(
                                setting?.technical_issue_checkbox_label,
                                setting?.language,
                                "technical_issue_checkbox_label"
                            );
                            this.handleInput(
                                setting?.dont_use_checkbox_label,
                                setting?.language,
                                "dont_use_checkbox_label"
                            );
                             this.handleInput(
                                setting?.another_account_checkbox_label,
                                setting?.language,
                                "another_account_checkbox_label"
                            );
                              this.handleInput(
                                setting?.did_not_find_ride_checkbox_label,
                                setting?.language,
                                "did_not_find_ride_checkbox_label"
                            );
                            this.handleInput(
                                setting?.did_not_get_booking_checkbox_label,
                                setting?.language,
                                "did_not_get_booking_checkbox_label"
                            );
                            this.handleInput(
                                setting?.did_not_find_destination_checkbox_label,
                                setting?.language,
                                "did_not_find_destination_checkbox_label"
                            );
                            this.handleInput(
                                setting?.other_checkbox_label,
                                setting?.language,
                                "other_checkbox_label"
                            );
                            this.handleInput(
                                setting?.recommend_heading,
                                setting?.language,
                                "recommend_heading"
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
                                setting?.prefer_not_checkbox_label,
                                setting?.language,
                                "prefer_not_checkbox_label"
                            );
                            this.handleInput(
                                setting?.why_closing_account_label,
                                setting?.language,
                                "why_closing_account_label"
                            );
                            this.handleInput(
                                setting?.why_closing_account_placeholder,
                                setting?.language,
                                "why_closing_account_placeholder"
                            );
                            this.handleInput(
                                setting?.improve_label,
                                setting?.language,
                                "improve_label"
                            );
                            this.handleInput(
                                setting?.improve_placeholder,
                                setting?.language,
                                "improve_placeholder"
                            );
                            this.handleInput(
                                setting?.close_my_account_checkbox,
                                setting?.language,
                                "close_my_account_checkbox"
                            );
                            this.handleInput(
                                setting?.close_my_account_checkbox_error,
                                setting?.language,
                                "close_my_account_checkbox_error"
                            );
                            this.handleInput(
                                setting?.close_account_button_text,
                                setting?.language,
                                "close_account_button_text"
                            );
                            this.handleInput(
                                setting?.warning_text,
                                setting?.language,
                                "warning_text"
                            );
                            this.handleInput(
                                setting?.difficulties_making_receiving_payments_label,
                                setting?.language,
                                "difficulties_making_receiving_payments_label"
                            );
                            this.handleInput(
                                setting?.customer_service_checkbox_label,
                                setting?.language,
                                "customer_service_checkbox_label"
                            );
                            this.handleInput(
                                setting?.take_me_back_button_label,
                                setting?.language,
                                "take_me_back_button_label"
                            );
                            this.handleInput(
                                setting?.close_it_button_label,
                                setting?.language,
                                "close_it_button_label"
                            );
                            this.handleInput(
                                setting?.close_account_sure_message_text,
                                setting?.language,
                                "close_account_sure_message_text"
                            );
                            this.handleInput(
                                setting?.web_irreversible_label,
                                setting?.language,
                                "web_irreversible_label"
                            );
                            this.handleInput(
                                setting?.web_closing_account_reason_label,
                                setting?.language,
                                "web_closing_account_reason_label"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-close-account-setting`,
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
                    `mobile_indicate_required_field_label.mobile_indicate_required_field_label_${language.id}`
                ) ||
                validationErros.has(
                    `closing_account_label.closing_account_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `apply_reason_label.apply_reason_label_${language.id}`
                ) ||
                validationErros.has(
                    `reason_label.reason_label_${language.id}`
                ) ||
                validationErros.has(
                    `not_say_checkbox_label.not_say_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `check_box_validation_message.check_box_validation_message_${language.id}`
                ) ||
                validationErros.has(
                    `customer_service_checkbox_label.customer_service_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `dont_use_checkbox_label.dont_use_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `another_account_checkbox_label.another_account_checkbox_label_${language.id}`
                )
                ||
                validationErros.has(
                    `did_not_get_booking_checkbox_label.did_not_get_booking_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `did_not_find_ride_checkbox_label.did_not_find_ride_checkbox_label_${language.id}`
                )
                ||
                validationErros.has(
                    `warning_text.warning_text_${language.id}`
                ) ||
                validationErros.has(
                    `close_account_button_text.close_account_button_text_${language.id}`
                ) ||
                validationErros.has(
                    `close_my_account_checkbox.close_my_account_checkbox_${language.id}`
                ) ||
                validationErros.has(
                    `close_my_account_checkbox_error.close_my_account_checkbox_error_${language.id}`
                ) ||
                validationErros.has(
                    `improve_label.improve_label_${language.id}`
                ) ||
                validationErros.has(
                    `improve_placeholder.improve_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `why_closing_account_label.why_closing_account_label_${language.id}`
                ) ||
                validationErros.has(
                    `why_closing_account_placeholder.why_closing_account_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `prefer_not_checkbox_label.prefer_not_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_checkbox_label.no_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `yes_checkbox_label.yes_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `recommend_heading.recommend_heading_${language.id}`
                ) ||
                validationErros.has(
                    `other_checkbox_label.other_checkbox_label_${language.id}`
                ) ||
                validationErros.has(
                    `did_not_find_destination_checkbox_label.did_not_find_destination_checkbox_label_${language.id}`
                )||
                validationErros.has(
                    `difficulties_making_receiving_payments_label.difficulties_making_receiving_payments_label_${language.id}`
                )||
                validationErros.has(
                    `web_closing_account_reason_label.web_closing_account_reason_label_${language.id}`
                )||
                validationErros.has(
                    `web_irreversible_label.web_irreversible_label_${language.id}`
                )||
                validationErros.has(
                    `close_account_sure_message_text.close_account_sure_message_text_${language.id}`
                )||
                validationErros.has(
                    `close_it_button_label.close_it_button_label_${language.id}`
                )||
                validationErros.has(
                    `take_me_back_button_label.take_me_back_button_label_${language.id}`
                )||
                validationErros.has(
                    `mobile_country_code_label.mobile_country_code_label_${language.id}`
                )
            );
        },
    },
};
</script>
