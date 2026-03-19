<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    My Review settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <ExcelBulkImport
                        title="My Review"
                        mode="all_languages"
                        download-endpoint="download-review-setting-template"
                        upload-endpoint="upload-review-setting-excel"
                        @success="fetchReviewSetting"
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
                                                    :for="`review_left_label_${activeLanguageId}`"
                                                    >Review left Label</label
                                                >
                                            </div>
                                            <input
                                                    type="text"
                                                    :name="`review_left_label_${activeLanguageId}`"
                                                    :id="`review_left_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_left_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_left_label'
                                                        )
                                                    "
                                                />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `review_left_label.review_left_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `review_left_label.review_left_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`already_reveiwed_label_${activeLanguageId}`"
                                                    >Already reviewed label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`already_reveiwed_label_${activeLanguageId}`"
                                                :id="`already_reveiwed_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'already_reveiwed_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'already_reveiwed_label'
                                                    )
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_received_message_${activeLanguageId}`">
                                                       No Received  Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_received_message_${activeLanguageId}`"
                                                    :id="`no_received_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_received_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_received_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_received_message.no_received_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_received_message.no_received_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`review_received_label_${activeLanguageId}`"
                                                        >Review Received Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`review_received_label_${activeLanguageId}`"
                                                    :id="`review_received_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'review_received_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'review_received_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `review_received_label.review_received_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `review_received_label.review_received_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_left_message_${activeLanguageId}`"
                                                        >No Left Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_left_message_${activeLanguageId}`"
                                                    :id="`no_left_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_left_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_left_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_left_message.no_left_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_left_message.no_left_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`replied_label_${activeLanguageId}`"
                                                        >Replied Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`replied_label_${activeLanguageId}`"
                                                    :id="`replied_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'replied_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'replied_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `replied_label.replied_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `replied_label.replied_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_more_data_label_${activeLanguageId}`"
                                                        >No More Data Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_more_data_label_${activeLanguageId}`"
                                                    :id="`no_more_data_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_more_data_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_more_data_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_more_data_label.no_more_data_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_more_data_label.no_more_data_label_${activeLanguageId}`
                                                    )
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
                                                        :for="`response_label_${activeLanguageId}`"
                                                        >Response Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`response_label_${activeLanguageId}`"
                                                    :id="`response_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'response_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'response_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `response_label.response_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `response_label.response_label_${activeLanguageId}`
                                                    )
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
                                                        >Reply Submit Label</label
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
                                                        :for="`see_all_review_label_${activeLanguageId}`"
                                                        >Reply Heading Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`see_all_review_label_${activeLanguageId}`"
                                                    :id="`see_all_review_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'see_all_review_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'see_all_review_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `see_all_review_label.see_all_review_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `see_all_review_label.see_all_review_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <!-- Passenger review page (passenger_review_*) -->
                                        <div class="relative z-0 w-full group col-span-2 border-t pt-4 mt-2">
                                            <p class="text-gray-600 font-medium mb-2">Passenger review page labels</p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_heading_${activeLanguageId}`">Passenger review heading</label>
                                            <input type="text" :name="`passenger_review_heading_${activeLanguageId}`" :id="`passenger_review_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Review" :value="getCurrentValue('passenger_review_heading')" @input="handleInput($event.target.value, language, 'passenger_review_heading')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_placeholder_${activeLanguageId}`">Passenger review placeholder</label>
                                            <input type="text" :name="`passenger_review_placeholder_${activeLanguageId}`" :id="`passenger_review_placeholder_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. In the 'Passenger Remarks' section..." :value="getCurrentValue('passenger_review_placeholder')" @input="handleInput($event.target.value, language, 'passenger_review_placeholder')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_submit_button_label_${activeLanguageId}`">Passenger review submit label</label>
                                            <input type="text" :name="`passenger_review_submit_button_label_${activeLanguageId}`" :id="`passenger_review_submit_button_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Save" :value="getCurrentValue('passenger_review_submit_button_label')" @input="handleInput($event.target.value, language, 'passenger_review_submit_button_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_criteria_heading_${activeLanguageId}`">Review criteria heading</label>
                                            <input type="text" :name="`passenger_review_criteria_heading_${activeLanguageId}`" :id="`passenger_review_criteria_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Review criteria" :value="getCurrentValue('passenger_review_criteria_heading')" @input="handleInput($event.target.value, language, 'passenger_review_criteria_heading')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_condition_label_${activeLanguageId}`">Condition of vehicle</label>
                                            <input type="text" :name="`passenger_review_condition_label_${activeLanguageId}`" :id="`passenger_review_condition_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder="e.g. Condition of the vehicle" :value="getCurrentValue('passenger_review_condition_label')" @input="handleInput($event.target.value, language, 'passenger_review_condition_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_conscious_label_${activeLanguageId}`">Conscious to passengers wellness</label>
                                            <input type="text" :name="`passenger_review_conscious_label_${activeLanguageId}`" :id="`passenger_review_conscious_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_conscious_label')" @input="handleInput($event.target.value, language, 'passenger_review_conscious_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_comfort_label_${activeLanguageId}`">Comfort</label>
                                            <input type="text" :name="`passenger_review_comfort_label_${activeLanguageId}`" :id="`passenger_review_comfort_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_comfort_label')" @input="handleInput($event.target.value, language, 'passenger_review_comfort_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_communication_label_${activeLanguageId}`">Communication</label>
                                            <input type="text" :name="`passenger_review_communication_label_${activeLanguageId}`" :id="`passenger_review_communication_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_communication_label')" @input="handleInput($event.target.value, language, 'passenger_review_communication_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_attitude_label_${activeLanguageId}`">Overall attitude</label>
                                            <input type="text" :name="`passenger_review_attitude_label_${activeLanguageId}`" :id="`passenger_review_attitude_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_attitude_label')" @input="handleInput($event.target.value, language, 'passenger_review_attitude_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_hygiene_label_${activeLanguageId}`">Personal hygiene</label>
                                            <input type="text" :name="`passenger_review_hygiene_label_${activeLanguageId}`" :id="`passenger_review_hygiene_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_hygiene_label')" @input="handleInput($event.target.value, language, 'passenger_review_hygiene_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_respect_label_${activeLanguageId}`">Respect and courtesy</label>
                                            <input type="text" :name="`passenger_review_respect_label_${activeLanguageId}`" :id="`passenger_review_respect_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_respect_label')" @input="handleInput($event.target.value, language, 'passenger_review_respect_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_safety_label_${activeLanguageId}`">Safety</label>
                                            <input type="text" :name="`passenger_review_safety_label_${activeLanguageId}`" :id="`passenger_review_safety_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_safety_label')" @input="handleInput($event.target.value, language, 'passenger_review_safety_label')" />
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <label :for="`passenger_review_timeliness_label_${activeLanguageId}`">Timeliness</label>
                                            <input type="text" :name="`passenger_review_timeliness_label_${activeLanguageId}`" :id="`passenger_review_timeliness_label_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" " :value="getCurrentValue('passenger_review_timeliness_label')" @input="handleInput($event.target.value, language, 'passenger_review_timeliness_label')" />
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
                            this.handleInput("", language, "review_left_label");
                            this.handleInput("", language, "no_received_message");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "review_received_label");
                            this.handleInput("", language, "response_label");
                            this.handleInput("", language, "replied_label");
                            this.handleInput("", language, "reply_label");
                            this.handleInput("", language, "no_more_data_label");
                            this.handleInput("", language, "no_left_message");
                            this.handleInput("", language, "reply_submit_button_label");
                            this.handleInput("", language, "reply_placeholder");
                            this.handleInput("", language, "reply_heading_label");
                            this.handleInput("", language, "review_label");
                            this.handleInput("", language, "already_reveiwed_label");
                            this.handleInput("", language, "see_all_review_label");
                            this.handleInput("", language, "passenger_review_heading");
                            this.handleInput("", language, "passenger_review_placeholder");
                            this.handleInput("", language, "passenger_review_submit_button_label");
                            this.handleInput("", language, "passenger_review_criteria_heading");
                            this.handleInput("", language, "passenger_review_condition_label");
                            this.handleInput("", language, "passenger_review_conscious_label");
                            this.handleInput("", language, "passenger_review_comfort_label");
                            this.handleInput("", language, "passenger_review_communication_label");
                            this.handleInput("", language, "passenger_review_attitude_label");
                            this.handleInput("", language, "passenger_review_hygiene_label");
                            this.handleInput("", language, "passenger_review_respect_label");
                            this.handleInput("", language, "passenger_review_safety_label");
                            this.handleInput("", language, "passenger_review_timeliness_label");
                        });
                        this.fetchReviewSetting();
                    }
                });
        },
        fetchReviewSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-my-review-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let 	my_review_setting_detail =
                            res?.data?.data?.my_review_setting_detail || [];
                        	my_review_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.review_left_label,
                                setting?.language,
                                "review_left_label"
                            );
                            this.handleInput(
                                setting?.no_received_message,
                                setting?.language,
                                "no_received_message"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.review_received_label,
                                setting?.language,
                                "review_received_label"
                            );
                            this.handleInput(
                                setting?.response_label,
                                setting?.language,
                                "response_label"
                            );
                            this.handleInput(
                                setting?.replied_label,
                                setting?.language,
                                "replied_label"
                            );
                            this.handleInput(
                                setting?.no_left_message,
                                setting?.language,
                                "no_left_message"
                            );
                            this.handleInput(
                                setting?.reply_label,
                                setting?.language,
                                "reply_label"
                            );
                            this.handleInput(
                                setting?.no_more_data_label,
                                setting?.language,
                                "no_more_data_label"
                            );
                             this.handleInput(
                                setting?.reply_heading_label,
                                setting?.language,
                                "reply_heading_label"
                            );
                            this.handleInput(
                                setting?.see_all_review_label,
                                setting?.language,
                                "see_all_review_label"
                            );
                            this.handleInput(
                                setting?.reply_placeholder,
                                setting?.language,
                                "reply_placeholder"
                            );
                            this.handleInput(
                                setting?.reply_submit_button_label,
                                setting?.language,
                                "reply_submit_button_label"
                            );
                            this.handleInput(
                                setting?.review_label,
                                setting?.language,
                                "review_label"
                            );
                            this.handleInput(
                                setting?.already_reveiwed_label ??
                                    setting?.already_reviewed_label,
                                setting?.language,
                                "already_reveiwed_label"
                            );
                            this.handleInput(setting?.passenger_review_heading, setting?.language, "passenger_review_heading");
                            this.handleInput(setting?.passenger_review_placeholder, setting?.language, "passenger_review_placeholder");
                            this.handleInput(setting?.passenger_review_submit_button_label, setting?.language, "passenger_review_submit_button_label");
                            this.handleInput(setting?.passenger_review_criteria_heading, setting?.language, "passenger_review_criteria_heading");
                            this.handleInput(setting?.passenger_review_condition_label, setting?.language, "passenger_review_condition_label");
                            this.handleInput(setting?.passenger_review_conscious_label, setting?.language, "passenger_review_conscious_label");
                            this.handleInput(setting?.passenger_review_comfort_label, setting?.language, "passenger_review_comfort_label");
                            this.handleInput(setting?.passenger_review_communication_label, setting?.language, "passenger_review_communication_label");
                            this.handleInput(setting?.passenger_review_attitude_label, setting?.language, "passenger_review_attitude_label");
                            this.handleInput(setting?.passenger_review_hygiene_label, setting?.language, "passenger_review_hygiene_label");
                            this.handleInput(setting?.passenger_review_respect_label, setting?.language, "passenger_review_respect_label");
                            this.handleInput(setting?.passenger_review_safety_label, setting?.language, "passenger_review_safety_label");
                            this.handleInput(setting?.passenger_review_timeliness_label, setting?.language, "passenger_review_timeliness_label");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-my-review-setting`,
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
                    `review_left_label.review_left_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_received_message.no_received_message_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `review_received_label.review_received_label_${language.id}`
                ) ||
                validationErros.has(
                    `response_label.response_label_${language.id}`
                ) ||
                validationErros.has(
                    `replied_label.replied_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_left_message.no_left_message_${language.id}`
                ) ||
                validationErros.has(
                    `reply_label.reply_label_${language.id}`
                ) ||
                validationErros.has(
                    `no_more_data_label.no_more_data_label_${language.id}`
                )
            );
        },
    },
};
</script>
