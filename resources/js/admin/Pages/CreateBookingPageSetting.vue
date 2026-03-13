<template>
    <AppLayout>
        <section class="booking-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Booking page settings
                                </h3>
                            </div>
                        </div>
                    </header>

                    <!-- Excel Upload Section -->
                    <ExcelBulkImport
                        title="Booking Page"
                        mode="all_languages"
                        download-endpoint="download-booking-page-setting-template"
                        upload-endpoint="upload-booking-page-setting-excel"
                        @success="getPageSettingData"
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
                                                        :for="`user_declarations_label_${activeLanguageId}`"
                                                        >User declarations label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`user_declarations_label_${activeLanguageId}`"
                                                    :id="`user_declarations_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'user_declarations_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'user_declarations_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `user_declarations_label.user_declarations_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `user_declarations_label.user_declarations_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`seats_available_label_${activeLanguageId}`"
                                                        >Seats available label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_available_label_${activeLanguageId}`"
                                                    :id="`seats_available_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_available_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_available_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_available_label.seats_available_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_available_label.seats_available_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`seats_available_tooltip_${activeLanguageId}`"
                                                        >Seats available tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_available_tooltip_${activeLanguageId}`"
                                                    :id="`seats_available_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_available_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_available_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_available_tooltip.seats_available_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_available_tooltip.seats_available_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`chat_with_driver_tooltip_${activeLanguageId}`"
                                                        >chat with driver tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`chat_with_driver_tooltip_${activeLanguageId}`"
                                                    :id="`chat_with_driver_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'chat_with_driver_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'chat_with_driver_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `chat_with_driver_tooltip.chat_with_driver_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `chat_with_driver_tooltip.chat_with_driver_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`aggreement_tooltip_${activeLanguageId}`"
                                                        >Eligibility & Agreement tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`aggreement_tooltip_${activeLanguageId}`"
                                                    :id="`aggreement_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'aggreement_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'aggreement_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `aggreement_tooltip.aggreement_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `aggreement_tooltip.aggreement_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`pink_ride_tooltip_${activeLanguageId}`"
                                                        >Pink ride error tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pink_ride_tooltip_${activeLanguageId}`"
                                                    :id="`pink_ride_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pink_ride_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pink_ride_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pink_ride_tooltip.pink_ride_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pink_ride_tooltip.pink_ride_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`extra_care_ride_tooltip_${activeLanguageId}`"
                                                        >Extra+ ride error tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`extra_care_ride_tooltip_${activeLanguageId}`"
                                                    :id="`extra_care_ride_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'extra_care_ride_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'extra_care_ride_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `extra_care_ride_tooltip.extra_care_ride_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `extra_care_ride_tooltip.extra_care_ride_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`seats_available_info_text_${activeLanguageId}`"
                                                        >Seats available label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_available_info_text_${activeLanguageId}`"
                                                    :id="`seats_available_info_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_available_info_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_available_info_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_available_info_text.seats_available_info_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_available_info_text.seats_available_info_text_${activeLanguageId}`
                                                    )
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
                                                        :for="`pricing_label_${activeLanguageId}`"
                                                        >Pricing label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pricing_label_${activeLanguageId}`"
                                                    :id="`pricing_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pricing_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pricing_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pricing_label.pricing_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pricing_label.pricing_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`seat_label_${activeLanguageId}`"
                                                        >Seat label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seat_label_${activeLanguageId}`"
                                                    :id="`seat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seat_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seat_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seat_label.seat_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seat_label.seat_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_fee_label_${activeLanguageId}`"
                                                        >Booking fee label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_fee_label_${activeLanguageId}`"
                                                    :id="`booking_fee_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_fee_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_fee_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_fee_label.booking_fee_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_fee_label.booking_fee_label_${activeLanguageId}`
                                                    )
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
                                                        >Ride Features label</label
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
                                                        :for="`booking_label_${activeLanguageId}`"
                                                        >Booking label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_label_${activeLanguageId}`"
                                                    :id="`booking_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_label.booking_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_label.booking_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`required_fields_${activeLanguageId}`"
                                                        >Required Fields</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`required_fields_${activeLanguageId}`"
                                                    :id="`required_fields_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'required_fields'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'required_fields'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `required_fields.required_fields_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `required_fields.required_fields_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`total_label_${activeLanguageId}`"
                                                        >Total label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`total_label_${activeLanguageId}`"
                                                    :id="`total_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'total_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'total_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `total_label.total_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `total_label.total_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`message_to_driver_label_${activeLanguageId}`"
                                                        >Message to driver label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`message_to_driver_label_${activeLanguageId}`"
                                                    :id="`message_to_driver_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'message_to_driver_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'message_to_driver_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `message_to_driver_label.message_to_driver_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `message_to_driver_label.message_to_driver_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`message_driver_placeholder_${activeLanguageId}`"
                                                        >Message to driver placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`message_driver_placeholder_${activeLanguageId}`"
                                                    :id="`message_driver_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'message_driver_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'message_driver_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `message_driver_placeholder.message_driver_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `message_driver_placeholder.message_driver_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`book_seat_button_label_${activeLanguageId}`"
                                                        >Book seat button (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`book_seat_button_label_${activeLanguageId}`"
                                                    :id="`book_seat_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'book_seat_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'book_seat_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `book_seat_button_label.book_seat_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `book_seat_button_label.book_seat_button_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`like_to_pay_label_${activeLanguageId}`"
                                                        >Like to pay label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`like_to_pay_label_${activeLanguageId}`"
                                                    :id="`like_to_pay_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'like_to_pay_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'like_to_pay_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `like_to_pay_label.like_to_pay_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `like_to_pay_label.like_to_pay_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
   
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`credit_card_label_${activeLanguageId}`"
                                                        >Credit card label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`credit_card_label_${activeLanguageId}`"
                                                    :id="`credit_card_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'credit_card_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'credit_card_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `credit_card_label.credit_card_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `credit_card_label.credit_card_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
  
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`paypal_label_${activeLanguageId}`"
                                                        >Paypal label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`paypal_label_${activeLanguageId}`"
                                                    :id="`paypal_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'paypal_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'paypal_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `paypal_label.paypal_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `paypal_label.paypal_label_${activeLanguageId}`
                                                    )
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
                                                        >luggage label</label
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
                                                        :for="`co_passenger_label_${activeLanguageId}`"
                                                        >Co-passenger label</label
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
                                                        :for="`select_card_label_${activeLanguageId}`"
                                                        >Select card label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`select_card_label_${activeLanguageId}`"
                                                    :id="`select_card_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'select_card_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'select_card_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `select_card_label.select_card_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `select_card_label.select_card_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`add_card_label_${activeLanguageId}`"
                                                        >Add card label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`add_card_label_${activeLanguageId}`"
                                                    :id="`add_card_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'add_card_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_card_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `add_card_label.add_card_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `add_card_label.add_card_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`pay_label_${activeLanguageId}`"
                                                        >Pay label (App)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pay_label_${activeLanguageId}`"
                                                    :id="`pay_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pay_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pay_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pay_label.pay_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pay_label.pay_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`coffee_from_wall_label_${activeLanguageId}`"
                                                        >coffee from wall label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`coffee_from_wall_label_${activeLanguageId}`"
                                                    :id="`coffee_from_wall_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'coffee_from_wall_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'coffee_from_wall_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `coffee_from_wall_label.coffee_from_wall_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `coffee_from_wall_label.coffee_from_wall_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`coffee_from_wall_tooltip_${activeLanguageId}`"
                                                        >Coffee from wall tooltip</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'coffee_from_wall_tooltip'
                                                        )
                                                    "
                                                    :ref="`coffee_from_wall_tooltip_${language.id}`"
                                                    :id="`coffee_from_wall_tooltip_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `coffee_from_wall_tooltip`
                                                        ][
                                                            `coffee_from_wall_tooltip_${language?.id}`
                                                        ]
                                                    "
                                                    :tinymce-script-src="tinymceScriptSrc"
                                                    :init="editorConfig"
                                                />
                                                <!-- <input
                                                    type="text"
                                                    :name="`coffee_from_wall_tooltip_${activeLanguageId}`"
                                                    :id="`coffee_from_wall_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'coffee_from_wall_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'coffee_from_wall_tooltip'
                                                        )
                                                    "
                                                /> -->
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `coffee_from_wall_tooltip.coffee_from_wall_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `coffee_from_wall_tooltip.coffee_from_wall_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`coffee_from_amount_wall_tooltip_${activeLanguageId}`"
                                                        >Coffee from wall amount tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`coffee_from_amount_wall_tooltip_${activeLanguageId}`"
                                                    :id="`coffee_from_amount_wall_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'coffee_from_amount_wall_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'coffee_from_amount_wall_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `coffee_from_amount_wall_tooltip.coffee_from_amount_wall_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `coffee_from_amount_wall_tooltip.coffee_from_amount_wall_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`payable_amount_label_${activeLanguageId}`"
                                                        >Payable amount label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payable_amount_label_${activeLanguageId}`"
                                                    :id="`payable_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payable_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payable_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payable_amount_label.payable_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payable_amount_label.payable_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`tax_label_${activeLanguageId}`"
                                                        >Tax label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`tax_label_${activeLanguageId}`"
                                                    :id="`tax_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'tax_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'tax_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `tax_label.tax_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `tax_label.tax_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_disclaimer_on_time_${activeLanguageId}`"
                                                        >Booking disclaimer on time text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_disclaimer_on_time_${activeLanguageId}`"
                                                    :id="`booking_disclaimer_on_time_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_disclaimer_on_time'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_disclaimer_on_time'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_disclaimer_on_time.booking_disclaimer_on_time_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_disclaimer_on_time.booking_disclaimer_on_time_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_disclaimer_pink_ride_${activeLanguageId}`"
                                                        >Booking disclaimer pink ride text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_disclaimer_pink_ride_${activeLanguageId}`"
                                                    :id="`booking_disclaimer_pink_ride_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_disclaimer_pink_ride'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_disclaimer_pink_ride'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_disclaimer_pink_ride.booking_disclaimer_pink_ride_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_disclaimer_pink_ride.booking_disclaimer_pink_ride_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_disclaimer_extra_care_ride_${activeLanguageId}`"
                                                        >Booking disclaimer extra care ride text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_disclaimer_extra_care_ride_${activeLanguageId}`"
                                                    :id="`booking_disclaimer_extra_care_ride_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_disclaimer_extra_care_ride'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_disclaimer_extra_care_ride'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_disclaimer_extra_care_ride.booking_disclaimer_extra_care_ride_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_disclaimer_extra_care_ride.booking_disclaimer_extra_care_ride_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_disclaimer_firm_${activeLanguageId}`"
                                                        >Booking disclaimer firm</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_disclaimer_firm_${activeLanguageId}`"
                                                    :id="`booking_disclaimer_firm_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_disclaimer_firm'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_disclaimer_firm'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_disclaimer_firm.booking_disclaimer_firm_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_disclaimer_firm.booking_disclaimer_firm_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_term_agree_text_${activeLanguageId}`"
                                                        >Booking term agree text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_term_agree_text_${activeLanguageId}`"
                                                    :id="`booking_term_agree_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_term_agree_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_term_agree_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_term_agree_text.booking_term_agree_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_term_agree_text.booking_term_agree_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_pink_ride_term_agree_text_${activeLanguageId}`"
                                                        >Booking pink ride agree term text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_pink_ride_term_agree_text_${activeLanguageId}`"
                                                    :id="`booking_pink_ride_term_agree_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_pink_ride_term_agree_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_pink_ride_term_agree_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_pink_ride_term_agree_text.booking_pink_ride_term_agree_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_pink_ride_term_agree_text.booking_pink_ride_term_agree_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
 
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_extra_care_ride_term_agree_text_${activeLanguageId}`"
                                                        >Booking extra care ride agree term text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_extra_care_ride_term_agree_text_${activeLanguageId}`"
                                                    :id="`booking_extra_care_ride_term_agree_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_extra_care_ride_term_agree_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_extra_care_ride_term_agree_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_extra_care_ride_term_agree_text.booking_extra_care_ride_term_agree_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_extra_care_ride_term_agree_text.booking_extra_care_ride_term_agree_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`firm_cancellation_label_price_section_${activeLanguageId}`"
                                                        >Firm cancellation label price section</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_cancellation_label_price_section_${activeLanguageId}`"
                                                    :id="`firm_cancellation_label_price_section_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_cancellation_label_price_section'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_cancellation_label_price_section'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_cancellation_label_price_section.firm_cancellation_label_price_section_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_cancellation_label_price_section.firm_cancellation_label_price_section_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`firm_discount_label_price_section_${activeLanguageId}`"
                                                        >Firm discount label price section</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_discount_label_price_section_${activeLanguageId}`"
                                                    :id="`firm_discount_label_price_section_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_discount_label_price_section'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_discount_label_price_section'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_discount_label_price_section.firm_discount_label_price_section_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_discount_label_price_section.firm_discount_label_price_section_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`firm_your_price_label_price_section_${activeLanguageId}`"
                                                        >Firm your price label price section</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`firm_your_price_label_price_section_${activeLanguageId}`"
                                                    :id="`firm_your_price_label_price_section_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'firm_your_price_label_price_section'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'firm_your_price_label_price_section'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `firm_your_price_label_price_section.firm_your_price_label_price_section_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `firm_your_price_label_price_section.firm_your_price_label_price_section_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                         
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`booking_cancellation_limit_exceed_${activeLanguageId}`"
                                                        >Booking cancellation limit exceed text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_cancellation_limit_exceed_${activeLanguageId}`"
                                                    :id="`booking_cancellation_limit_exceed_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_cancellation_limit_exceed'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_cancellation_limit_exceed'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_cancellation_limit_exceed.booking_cancellation_limit_exceed_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_cancellation_limit_exceed.booking_cancellation_limit_exceed_${activeLanguageId}`
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
        ExcelBulkImport,
    },
    computed: {
        mixAdminApiUrl() {
            return process.env.MIX_ADMIN_API_URL;
        }
    },
    created() {
        this.fetchLanguages();
    },
    methods: {
        getPageSettingData() {
            this.fetchBookingPageSetting();
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
                            this.handleInput("", language, "seats_available_label");
                            this.handleInput("", language, "seats_available_info_text");
                            this.handleInput("", language, "seats_available_tooltip");
                            this.handleInput("", language, "chat_with_driver_tooltip");
                            this.handleInput("", language, "aggreement_tooltip");
                            this.handleInput("", language, "pink_ride_tooltip");
                            this.handleInput("", language, "extra_care_ride_tooltip");
                            this.handleInput("", language, "cancellation_policy_label");
                            this.handleInput("", language, "pricing_label");
                            this.handleInput("", language, "seat_label");
                            this.handleInput("", language, "booking_fee_label");
                            this.handleInput("", language, "ride_features_label");
                            this.handleInput("", language, "booking_label");
                            this.handleInput("", language, "paypal_label");
                            this.handleInput("", language, "co_passenger_label");
                            this.handleInput("", language, "payment_method_label");
                            this.handleInput("", language, "luggage_label");
                            this.handleInput("", language, "required_fields");
                            this.handleInput("", language, "tax_label");
                            this.handleInput("", language, "total_label");
                            this.handleInput("", language, "message_to_driver_label");
                            this.handleInput("", language, "message_driver_placeholder");
                            this.handleInput("", language, "book_seat_button_label");
                            this.handleInput("", language, "like_to_pay_label");
                            this.handleInput("", language, "credit_card_label");
                            this.handleInput("", language, "select_card_label");
                            this.handleInput("", language, "add_card_label");
                            this.handleInput("", language, "pay_label");
                            this.handleInput("", language, "coffee_from_wall_label");
                            this.handleInput("", language, "coffee_from_wall_tooltip");
                            this.handleInput("", language, "payable_amount_label");
                            this.handleInput("", language, "coffee_from_amount_wall_tooltip");

                            this.handleInput("", language, "booking_disclaimer_on_time");
                            this.handleInput("", language, "booking_disclaimer_pink_ride");
                            this.handleInput("", language, "booking_disclaimer_extra_care_ride");
                            this.handleInput("", language, "booking_disclaimer_firm");
                            this.handleInput("", language, "booking_term_agree_text");
                            this.handleInput("", language, "booking_pink_ride_term_agree_text");
                            this.handleInput("", language, "booking_extra_care_ride_term_agree_text");
                            this.handleInput("", language, "user_declarations_label");
                            this.handleInput("", language, "firm_cancellation_label_price_section");
                            this.handleInput("", language, "firm_discount_label_price_section");
                            this.handleInput("", language, "firm_your_price_label_price_section");
                            this.handleInput("", language, "booking_cancellation_limit_exceed");
                        });
                        this.fetchBookingPageSetting();
                    }
                });
        },
        fetchBookingPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-booking-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let booking_page_setting_detail =
                            res?.data?.data?.booking_page_setting_detail || [];
                        booking_page_setting_detail.map((setting) => {
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
                                setting?.seats_available_label,
                                setting?.language,
                                "seats_available_label"
                            );
                            this.handleInput(
                                setting?.seats_available_info_text,
                                setting?.language,
                                "seats_available_info_text"
                            );
                            this.handleInput(
                                setting?.seats_available_tooltip,
                                setting?.language,
                                "seats_available_tooltip"
                            );
                            this.handleInput(
                                setting?.chat_with_driver_tooltip,
                                setting?.language,
                                "chat_with_driver_tooltip"
                            );
                            this.handleInput(
                                setting?.aggreement_tooltip,
                                setting?.language,
                                "aggreement_tooltip"
                            );
                            this.handleInput(
                                setting?.pink_ride_tooltip,
                                setting?.language,
                                "pink_ride_tooltip"
                            );
                            this.handleInput(
                                setting?.extra_care_ride_tooltip,
                                setting?.language,
                                "extra_care_ride_tooltip"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label,
                                setting?.language,
                                "cancellation_policy_label"
                            );
                            this.handleInput(
                                setting?.pricing_label,
                                setting?.language,
                                "pricing_label"
                            );
                            this.handleInput(
                                setting?.seat_label,
                                setting?.language,
                                "seat_label"
                            );
                            this.handleInput(
                                setting?.booking_fee_label,
                                setting?.language,
                                "booking_fee_label"
                            );
                            this.handleInput(
                                setting?.ride_features_label,
                                setting?.language,
                                "ride_features_label"
                            );
                            this.handleInput(
                                setting?.booking_label,
                                setting?.language,
                                "booking_label"
                            );
                            this.handleInput(
                                setting?.paypal_label,
                                setting?.language,
                                "paypal_label"
                            );
                            this.handleInput(
                                setting?.co_passenger_label,
                                setting?.language,
                                "co_passenger_label"
                            );
                            this.handleInput(
                                setting?.payment_method_label,
                                setting?.language,
                                "payment_method_label"
                            );
                            this.handleInput(
                                setting?.luggage_label,
                                setting?.language,
                                "luggage_label"
                            );
                            this.handleInput(
                                setting?.required_fields,
                                setting?.language,
                                "required_fields"
                            );
                            this.handleInput(
                                setting?.tax_label,
                                setting?.language,
                                "tax_label"
                            );
                            this.handleInput(
                                setting?.total_label,
                                setting?.language,
                                "total_label"
                            );
                            this.handleInput(
                                setting?.message_to_driver_label,
                                setting?.language,
                                "message_to_driver_label"
                            );
                            this.handleInput(
                                setting?.message_driver_placeholder,
                                setting?.language,
                                "message_driver_placeholder"
                            );
                            this.handleInput(
                                setting?.book_seat_button_label,
                                setting?.language,
                                "book_seat_button_label"
                            );
                            this.handleInput(
                                setting?.like_to_pay_label,
                                setting?.language,
                                "like_to_pay_label"
                            );
                            this.handleInput(
                                setting?.credit_card_label,
                                setting?.language,
                                "credit_card_label"
                            );
                            this.handleInput(
                                setting?.select_card_label,
                                setting?.language,
                                "select_card_label"
                            );
                            this.handleInput(
                                setting?.add_card_label,
                                setting?.language,
                                "add_card_label"
                            );
                            this.handleInput(
                                setting?.pay_label,
                                setting?.language,
                                "pay_label"
                            );
                            this.handleInput(
                                setting?.coffee_from_wall_label,
                                setting?.language,
                                "coffee_from_wall_label"
                            );
                            this.handleInput(
                                setting?.coffee_from_wall_tooltip,
                                setting?.language,
                                "coffee_from_wall_tooltip"
                            );

                            this.handleInput(
                                setting?.payable_amount_label,
                                setting?.language,
                                "payable_amount_label"
                            );
                            
                            this.handleInput(
                                setting?.coffee_from_amount_wall_tooltip,
                                setting?.language,
                                "coffee_from_amount_wall_tooltip"
                            );

                            this.handleInput(
                                setting?.booking_term_agree_text,
                                setting?.language,
                                "booking_term_agree_text"
                            );
                            this.handleInput(
                                setting?.booking_pink_ride_term_agree_text,
                                setting?.language,
                                "booking_pink_ride_term_agree_text"
                            );
                            this.handleInput(
                                setting?.booking_extra_care_ride_term_agree_text,
                                setting?.language,
                                "booking_extra_care_ride_term_agree_text"
                            );
                            this.handleInput(
                                setting?.user_declarations_label,
                                setting?.language,
                                "user_declarations_label"
                            );
                            this.handleInput(
                                setting?.booking_disclaimer_on_time,
                                setting?.language,
                                "booking_disclaimer_on_time"
                            );
                            this.handleInput(
                                setting?.booking_disclaimer_pink_ride,
                                setting?.language,
                                "booking_disclaimer_pink_ride"
                            );
                            this.handleInput(
                                setting?.booking_disclaimer_extra_care_ride,
                                setting?.language,
                                "booking_disclaimer_extra_care_ride"
                            );
                            this.handleInput(
                                setting?.booking_disclaimer_firm,
                                setting?.language,
                                "booking_disclaimer_firm"
                            );

                            this.handleInput(
                                setting?.firm_cancellation_label_price_section,
                                setting?.language,
                                "firm_cancellation_label_price_section"
                            );
                            this.handleInput(
                                setting?.firm_discount_label_price_section,
                                setting?.language,
                                "firm_discount_label_price_section"
                            );
                            this.handleInput(
                                setting?.firm_your_price_label_price_section,
                                setting?.language,
                                "firm_your_price_label_price_section"
                            );
                            
                            this.handleInput(
                                setting?.booking_cancellation_limit_exceed,
                                setting?.language,
                                "booking_cancellation_limit_exceed"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-booking-page-setting`,
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
                    `cancellation_policy_label.cancellation_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `pricing_label.pricing_label_${language.id}`
                ) ||
                validationErros.has(
                    `seat_label.seat_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_fee_label.booking_fee_label_${language.id}`
                ) ||
                validationErros.has(
                    `ride_features_label.ride_features_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_label.booking_label_${language.id}`
                ) ||
                validationErros.has(
                    `paypal_label.paypal_label_${language.id}`
                ) ||
                validationErros.has(
                    `co_passenger_label.co_passenger_label_${language.id}`
                ) ||
                validationErros.has(
                    `payment_method_label.payment_method_label_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_label.luggage_label_${language.id}`
                ) ||
                validationErros.has(
                    `required_fields.required_fields_${language.id}`
                ) ||
                validationErros.has(
                    `tax_label.tax_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_disclaimer_on_time.booking_disclaimer_on_time_${language.id}`
                ) ||
                validationErros.has(
                    `booking_disclaimer_pink_ride.booking_disclaimer_pink_ride_${language.id}`
                ) ||
                validationErros.has(
                    `booking_disclaimer_extra_care_ride.booking_disclaimer_extra_care_ride_${language.id}`
                ) ||
                validationErros.has(
                    `booking_disclaimer_firm.booking_disclaimer_firm_${language.id}`
                ) ||
                validationErros.has(
                    `booking_term_agree_text.booking_term_agree_text_${language.id}`
                ) ||
                validationErros.has(
                    `booking_pink_ride_term_agree_text.booking_pink_ride_term_agree_text_${language.id}`
                ) ||
                validationErros.has(
                    `booking_extra_care_ride_term_agree_text.booking_extra_care_ride_term_agree_text_${language.id}`
                ) ||
                validationErros.has(
                    `user_declarations_label.user_declarations_label_${language.id}`
                ) ||
                validationErros.has(
                    `firm_cancellation_label_price_section.firm_cancellation_label_price_section_${language.id}`
                ) ||
                validationErros.has(
                    `firm_discount_label_price_section.firm_discount_label_price_section_${language.id}`
                ) ||
                validationErros.has(
                    `firm_your_price_label_price_section.firm_your_price_label_price_section_${language.id}`
                ) ||
                validationErros.has(
                    `booking_cancellation_limit_exceed.booking_cancellation_limit_exceed_${language.id}`
                ) ||
                validationErros.has(
                    `total_label.total_label_${language.id}`
                ) ||
                validationErros.has(
                    `message_to_driver_label.message_to_driver_label_${language.id}`
                ) ||
                validationErros.has(
                    `message_driver_placeholder.message_driver_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `book_seat_button_label.book_seat_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `like_to_pay_label.like_to_pay_label_${language.id}`
                ) ||
                validationErros.has(
                    `credit_card_label.credit_card_label_${language.id}`
                ) ||
                validationErros.has(
                    `select_card_label.select_card_label_${language.id}`
                ) ||
                validationErros.has(
                    `add_card_label.add_card_label_${language.id}`
                ) ||
                validationErros.has(
                    `pay_label.pay_label_${language.id}`
                ) ||
                validationErros.has(
                    `coffee_from_wall_label.coffee_from_wall_label_${language.id}`
                ) ||
                validationErros.has(
                    `coffee_from_wall_tooltip.coffee_from_wall_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `payable_amount_label.payable_amount_label_${language.id}`
                ) ||
                validationErros.has(
                    `coffee_from_amount_wall_tooltip.coffee_from_amount_wall_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `seats_available_label.seats_available_label_${language.id}`
                )||
                validationErros.has(
                    `seats_available_info_text.seats_available_info_text_${language.id}`
                ) ||
                validationErros.has(
                    `seats_available_tooltip.seats_available_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `chat_with_driver_tooltip.chat_with_driver_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `aggreement_tooltip.aggreement_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `pink_ride_tooltip.pink_ride_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `extra_care_ride_tooltip.extra_care_ride_tooltip_${language.id}`
                )
                
            );
        },

    },
};
</script>
