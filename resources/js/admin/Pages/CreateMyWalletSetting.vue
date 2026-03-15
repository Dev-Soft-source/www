<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                My Wallet Page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <ExcelBulkImport
                        title="My Wallet"
                        mode="all_languages"
                        download-endpoint="download-my-wallet-setting-template"
                        upload-endpoint="upload-my-wallet-setting-excel"
                        @success="fetchMyWalletSetting"
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
                                                    :for="`card_heading_${activeLanguageId}`"
                                                    >Card Heading</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`card_heading_${activeLanguageId}`"
                                                :id="`card_heading_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('card_heading')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'card_heading'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `card_heading.card_heading_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `card_heading.card_heading_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`card_heading_${activeLanguageId}`"
                                                    >Passenger Heading</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_heading_${activeLanguageId}`"
                                                :id="`passenger_heading_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('passenger_heading')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_heading'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_heading.passenger_heading_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_heading.passenger_heading_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`driver_heading_${activeLanguageId}`"
                                                    >Driver Heading</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_heading_${activeLanguageId}`"
                                                :id="`driver_heading_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_heading'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_heading'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_heading.driver_heading_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_heading.driver_heading_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`balance_heading_${activeLanguageId}`"
                                                    >Balance Heading</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`balance_heading_${activeLanguageId}`"
                                                :id="`balance_heading_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'balance_heading'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'balance_heading'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `balance_heading.balance_heading_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `balance_heading.balance_heading_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
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
                                    </div>

                                    <!-- copy here -->

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`balance_id_label_${activeLanguageId}`"
                                                        >Balance Id Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`balance_id_label_${activeLanguageId}`"
                                                    :id="`balance_id_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'balance_id_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'balance_id_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `balance_id_label.balance_id_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `balance_id_label.balance_id_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`balance_amount_label_${activeLanguageId}`"
                                                        >Balance Amount Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`balance_amount_label_${activeLanguageId}`"
                                                    :id="`balance_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'balance_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'balance_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `balance_amount_label.balance_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `balance_amount_label.balance_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`balance_date_label_${activeLanguageId}`"
                                                        >Balance Date Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`balance_date_label_${activeLanguageId}`"
                                                    :id="`balance_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'balance_date_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'balance_date_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `balance_date_label.balance_date_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `balance_date_label.balance_date_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`balance_buy_more_button_text_${activeLanguageId}`"
                                                        >Balance Buy More Button Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`balance_buy_more_button_text_${activeLanguageId}`"
                                                    :id="`balance_buy_more_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'balance_buy_more_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'balance_buy_more_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `balance_buy_more_button_text.balance_buy_more_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `balance_buy_more_button_text.balance_buy_more_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_more_data_message_${activeLanguageId}`">
                                                                    No More Data Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_more_data_message_${activeLanguageId}`"
                                                    :id="`no_more_data_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_more_data_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_more_data_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_more_data_message.no_more_data_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_more_data_message.no_more_data_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_my_ride_message_${activeLanguageId}`"
                                                        >No My Ride Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_my_ride_message_${activeLanguageId}`"
                                                    :id="`no_my_ride_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_my_ride_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_my_ride_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_my_ride_message.no_my_ride_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_my_ride_message.no_my_ride_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_reward_found_message_${activeLanguageId}`"
                                                        >No Reward Found Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_reward_found_message_${activeLanguageId}`"
                                                    :id="`no_reward_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_reward_found_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_reward_found_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_reward_found_message.no_reward_found_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_reward_found_message.no_reward_found_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_paid_out_message_${activeLanguageId}`"
                                                        >No Paid Out Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_paid_out_message_${activeLanguageId}`"
                                                    :id="`no_paid_out_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_paid_out_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_paid_out_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_paid_out_message.no_paid_out_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_paid_out_message.no_paid_out_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_balance_found_message_${activeLanguageId}`"
                                                        >No Balance Found Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_balance_found_message_${activeLanguageId}`"
                                                    :id="`no_balance_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_balance_found_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_balance_found_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_balance_found_message.no_balance_found_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_balance_found_message.no_balance_found_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_driver_found_message_${activeLanguageId}`"
                                                        >No Driver Found Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_driver_found_message_${activeLanguageId}`"
                                                    :id="`no_driver_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_driver_found_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_driver_found_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_driver_found_message.no_driver_found_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_driver_found_message.no_driver_found_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`request_transfer_label_${activeLanguageId}`"
                                                        >Request Transfer Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`request_transfer_label_${activeLanguageId}`"
                                                    :id="`request_transfer_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'request_transfer_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'request_transfer_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `request_transfer_label.request_transfer_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `request_transfer_label.request_transfer_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`top_up_main_heading_${activeLanguageId}`"
                                                        >Top Up Balance Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`top_up_main_heading_${activeLanguageId}`"
                                                    :id="`top_up_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'top_up_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'top_up_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `top_up_main_heading.top_up_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `top_up_main_heading.top_up_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div><div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`ride_fare_main_heading_${activeLanguageId}`"
                                                        >Ride Fair Detail Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_fare_main_heading_${activeLanguageId}`"
                                                    :id="`ride_fare_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_fare_main_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_fare_main_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_fare_main_heading.ride_fare_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_fare_main_heading.ride_fare_main_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`purchase_top_up_label_${activeLanguageId}`"
                                                        >Purchase Top Up Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`purchase_top_up_label_${activeLanguageId}`"
                                                    :id="`purchase_top_up_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'purchase_top_up_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'purchase_top_up_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `purchase_top_up_label.purchase_top_up_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `purchase_top_up_label.purchase_top_up_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`purchase_top_up_placeholder_${activeLanguageId}`"
                                                        >Purchase Top Up Placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`purchase_top_up_placeholder_${activeLanguageId}`"
                                                    :id="`purchase_top_up_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'purchase_top_up_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'purchase_top_up_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `purchase_top_up_placeholder.purchase_top_up_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `purchase_top_up_placeholder.purchase_top_up_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`purchase_top_up_error_${activeLanguageId}`"
                                                        >Please select the number of booking credits you want to purchase</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`purchase_top_up_error_${activeLanguageId}`"
                                                    :id="`purchase_top_up_error_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'purchase_top_up_error'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'purchase_top_up_error'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `purchase_top_up_error.purchase_top_up_error_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `purchase_top_up_error.purchase_top_up_error_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`pay_with_label_${activeLanguageId}`"
                                                        >Pay With Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pay_with_label_${activeLanguageId}`"
                                                    :id="`pay_with_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pay_with_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pay_with_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pay_with_label.pay_with_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pay_with_label.pay_with_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`must_add_amount_toltip_${activeLanguageId}`"
                                                        >Add Amount ToolTip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`must_add_amount_toltip_${activeLanguageId}`"
                                                    :id="`must_add_amount_toltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'must_add_amount_toltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'must_add_amount_toltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `must_add_amount_toltip.must_add_amount_toltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `must_add_amount_toltip.must_add_amount_toltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_label_${activeLanguageId}`"
                                                        >Passenger Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_label_${activeLanguageId}`"
                                                    :id="`passenger_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_label.passenger_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_label.passenger_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`fare_label_${activeLanguageId}`"
                                                        >Fare Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`fare_label_${activeLanguageId}`"
                                                    :id="`fare_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'fare_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'fare_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `fare_label.fare_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `fare_label.fare_label_${activeLanguageId}`
                                                    )
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
                                                        >Booking Fee Label</label
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
                                                        :for="`credit_card_label_${activeLanguageId}`"
                                                        >Credit Card Label</label
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
                                                        :for="`total_label_${activeLanguageId}`"
                                                        >Total Label</label
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
                                                        :for="`claim_my_reward_button_text_${activeLanguageId}`"
                                                        >Claim my reward button text</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`claim_my_reward_button_text_${activeLanguageId}`"
                                                    :id="`claim_my_reward_button_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'claim_my_reward_button_text'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'claim_my_reward_button_text'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `claim_my_reward_button_text.claim_my_reward_button_text_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `claim_my_reward_button_text.claim_my_reward_button_text_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>






                                    </div>
                                </div>
                                <!-- main section end -->

                                <!-- Passenger / My rides section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[1] ? 'bg-gray-50' : ''
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
                                            Passenger / My rides
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
                                        v-if="collapseStates[2]"
                                    >

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_heading_${activeLanguageId}`"
                                                        >Passenger My Ride Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_heading_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_heading.passenger_my_ride_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_heading.passenger_my_ride_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_ride_id_label_${activeLanguageId}`"
                                                        >Passenger Ride Id Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_ride_id_label_${activeLanguageId}`"
                                                    :id="`passenger_ride_id_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_ride_id_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_ride_id_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_ride_id_label.passenger_ride_id_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_ride_id_label.passenger_ride_id_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_from_label_${activeLanguageId}`"
                                                        >Passenger Ride Form Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_from_label_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_from_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_from_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_from_label.passenger_my_ride_from_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_from_label.passenger_my_ride_from_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_to_label_${activeLanguageId}`"
                                                        >Passenger Ride To Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_to_label_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_to_label.passenger_my_ride_to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_to_label.passenger_my_ride_to_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_date_label_${activeLanguageId}`"
                                                        >Passenger Ride Date Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_date_label_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_date_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_date_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_date_label.passenger_my_ride_date_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_date_label.passenger_my_ride_date_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_booking_fee_label_${activeLanguageId}`"
                                                        >Passenger Ride Booking Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_booking_fee_label_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_booking_fee_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_booking_fee_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_booking_fee_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_booking_fee_label.passenger_my_ride_booking_fee_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_booking_fee_label.passenger_my_ride_booking_fee_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_fare_label_${activeLanguageId}`"
                                                        >Passenger Ride Fare Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_fare_label_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_fare_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_fare_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_fare_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_fare_label.passenger_my_ride_fare_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_fare_label.passenger_my_ride_fare_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_ride_total_amount_label_${activeLanguageId}`"
                                                        >Passenger Ride Total Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_ride_total_amount_label_${activeLanguageId}`"
                                                    :id="`passenger_my_ride_total_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_ride_total_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_ride_total_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_ride_total_amount_label.passenger_my_ride_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_ride_total_amount_label.passenger_my_ride_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                    </div>
                                </div>
                                <!-- Passenger / My rides section end -->

                                <!-- Passenger / My rewards section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[2] ? 'bg-gray-50' : ''
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
                                            Passenger / My rewards
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
                                        v-if="collapseStates[3]"
                                    >

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_reward_heading_${activeLanguageId}`"
                                                        >Passenger Reward Heading </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_reward_heading_${activeLanguageId}`"
                                                    :id="`passenger_my_reward_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_reward_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_reward_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_reward_heading.passenger_my_reward_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_reward_heading.passenger_my_reward_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_reward_description_${activeLanguageId}`"
                                                        >Passenger Reward Description Label(You have)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_reward_description_${activeLanguageId}`"
                                                    :id="`passenger_my_reward_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_reward_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_reward_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_reward_description.passenger_my_reward_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_reward_description.passenger_my_reward_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_reward_description1_${activeLanguageId}`"
                                                        >Passenger Reward Description Label (Point as passenger)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_reward_description1_${activeLanguageId}`"
                                                    :id="`passenger_my_reward_description1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_reward_description1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_reward_description1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_reward_description1.passenger_my_reward_description1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_reward_description1.passenger_my_reward_description1_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_reward_points_table_label_${activeLanguageId}`"
                                                        >Passenger My Reward Point label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_reward_points_table_label_${activeLanguageId}`"
                                                    :id="`passenger_my_reward_points_table_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_reward_points_table_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_reward_points_table_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_reward_points_table_label.passenger_my_reward_points_table_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_reward_points_table_label.passenger_my_reward_points_table_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_reward_reward_table_label_${activeLanguageId}`"
                                                        >Passenger My Reward Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_reward_reward_table_label_${activeLanguageId}`"
                                                    :id="`passenger_my_reward_reward_table_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_reward_reward_table_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_reward_reward_table_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_reward_reward_table_label.passenger_my_reward_reward_table_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_reward_reward_table_label.passenger_my_reward_reward_table_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`passenger_my_reward_to_label_${activeLanguageId}`"
                                                        >Passenger My Reward To Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passenger_my_reward_to_label_${activeLanguageId}`"
                                                    :id="`passenger_my_reward_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passenger_my_reward_to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passenger_my_reward_to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passenger_my_reward_to_label.passenger_my_reward_to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passenger_my_reward_to_label.passenger_my_reward_to_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                    </div>
                                </div>
                                <!-- Passenger / My rides section end -->

                                <!-- Driver / Paid out section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[4] ? 'bg-gray-50' : ''
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
                                            Driver / Paid out
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
                                        v-if="collapseStates[5]"
                                    >

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_paid_out_heading_${activeLanguageId}`"
                                                    >Driver Paid Out Heading</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_paid_out_heading_${activeLanguageId}`"
                                                :id="`driver_paid_out_heading_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_paid_out_heading'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_paid_out_heading'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_paid_out_heading.driver_paid_out_heading_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_paid_out_heading.driver_paid_out_heading_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_paid_ride_id_label_${activeLanguageId}`"
                                                        >Driver Paid Ride Id Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_paid_ride_id_label_${activeLanguageId}`"
                                                    :id="`driver_paid_ride_id_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_paid_ride_id_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_paid_ride_id_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_paid_ride_id_label.driver_paid_ride_id_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_paid_ride_id_label.driver_paid_ride_id_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_paid_from_label_${activeLanguageId}`"
                                                        >Driver Paid Form Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_paid_from_label_${activeLanguageId}`"
                                                    :id="`driver_paid_from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_paid_from_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_paid_from_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_paid_from_label.driver_paid_from_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_paid_from_label.driver_paid_from_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_paid_to_label_${activeLanguageId}`"
                                                        >Driver Paid To Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_paid_to_label_${activeLanguageId}`"
                                                    :id="`driver_paid_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_paid_to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_paid_to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_paid_to_label.driver_paid_to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_paid_to_label.driver_paid_to_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_paid_paid_out_date_label_${activeLanguageId}`"
                                                        >Driver paid out date label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_paid_paid_out_date_label_${activeLanguageId}`"
                                                    :id="`driver_paid_paid_out_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_paid_paid_out_date_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_paid_paid_out_date_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_paid_paid_out_date_label.driver_paid_paid_out_date_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_paid_paid_out_date_label.driver_paid_paid_out_date_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_paid_total_amount_label_${activeLanguageId}`"
                                                        >Driver Paid Total Amount Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_paid_total_amount_label_${activeLanguageId}`"
                                                    :id="`driver_paid_total_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_paid_total_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_paid_total_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_paid_total_amount_label.driver_paid_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_paid_total_amount_label.driver_paid_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_paid_out_found_${activeLanguageId}`"
                                                        >No Paid Out Found</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_paid_out_found_${activeLanguageId}`"
                                                    :id="`no_paid_out_found_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_paid_out_found'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_paid_out_found'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_paid_out_found.no_paid_out_found_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_paid_out_found.no_paid_out_found_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_pending_found_message_${activeLanguageId}`"
                                                        >No Paid Out Found</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_pending_found_message_${activeLanguageId}`"
                                                    :id="`no_pending_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_pending_found_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_pending_found_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_pending_found_message.no_pending_found_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_pending_found_message.no_pending_found_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Driver / Paid out section end -->

                                <!-- Driver / Available section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[5] ? 'bg-gray-50' : ''
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
                                            Driver / Available
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
                                        v-if="collapseStates[6]"
                                    >

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_availabe_heading_${activeLanguageId}`"
                                                        >Driver Available Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_availabe_heading_${activeLanguageId}`"
                                                    :id="`driver_availabe_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_availabe_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_availabe_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_availabe_heading.driver_availabe_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_availabe_heading.driver_availabe_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`driver_available_ride_id_label_${activeLanguageId}`"
                                                    >Driver Available Ride Label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_available_ride_id_label_${activeLanguageId}`"
                                                :id="`driver_available_ride_id_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('driver_available_ride_id_label')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_available_ride_id_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_available_ride_id_label.driver_available_ride_id_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_available_ride_id_label.driver_available_ride_id_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_available_from_label_${activeLanguageId}`"
                                                        >Driver Form Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_available_from_label_${activeLanguageId}`"
                                                    :id="`driver_available_from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_available_from_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_available_from_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_available_from_label.driver_available_from_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_available_from_label.driver_available_from_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_available_to_label_${activeLanguageId}`"
                                                        >Driver To Label </label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_available_to_label_${activeLanguageId}`"
                                                    :id="`driver_available_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_available_to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_available_to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_available_to_label.driver_available_to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_available_to_label.driver_available_to_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_available_date_label_${activeLanguageId}`"
                                                        >Driver Available Date Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_available_date_label_${activeLanguageId}`"
                                                    :id="`driver_available_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_available_date_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_available_date_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_available_date_label.driver_available_date_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_available_date_label.driver_available_date_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_available_total_amount_label_${activeLanguageId}`"
                                                        >Driver Available Amount</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_available_total_amount_label_${activeLanguageId}`"
                                                    :id="`driver_available_total_amount_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_available_total_amount_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_available_total_amount_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_available_total_amount_label.driver_available_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_available_total_amount_label.driver_available_total_amount_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                    </div>
                                </div>
                                <!-- Driver / Available section end -->

                                <!-- Driver / Pending section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[6] ? 'bg-gray-50' : ''
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
                                            Driver / Pending
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
                                        v-if="collapseStates[7]"
                                    >

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_pending_heading_${activeLanguageId}`"
                                                        >Driver's Pending Heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_pending_heading_${activeLanguageId}`"
                                                    :id="`driver_pending_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_pending_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_pending_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_pending_heading.driver_pending_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_pending_heading.driver_pending_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_pending_date_label_${activeLanguageId}`"
                                                        >Driver Pending Date Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_pending_date_label_${activeLanguageId}`"
                                                    :id="`driver_pending_date_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_pending_date_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_pending_date_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_pending_date_label.driver_pending_date_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_pending_date_label.driver_pending_date_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                         <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_pending_data_description_${activeLanguageId}`"
                                                        >Driver Pending Data Description</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_pending_data_description_${activeLanguageId}`"
                                                    :id="`driver_pending_data_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_pending_data_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_pending_data_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_pending_data_description.driver_pending_data_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_pending_data_description.driver_pending_data_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`no_pending_found_message_${activeLanguageId}`"
                                                        >No Pending Found Message</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`no_pending_found_message_${activeLanguageId}`"
                                                    :id="`no_pending_found_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'no_pending_found_message'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'no_pending_found_message'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `no_pending_found_message.no_pending_found_message_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `no_pending_found_message.no_pending_found_message_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Driver / Pending section end -->

                                <!-- Driver / My rewards section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[7] ? 'bg-gray-50' : ''
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
                                            Driver / My rewards
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
                                        v-if="collapseStates[8]"
                                    >

                                    <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_reward_heading_${activeLanguageId}`"
                                                        >Driver Reward Haading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_reward_heading_${activeLanguageId}`"
                                                    :id="`driver_reward_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_reward_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_reward_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_reward_heading.driver_reward_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_reward_heading.driver_reward_heading_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_reward_description_${activeLanguageId}`"
                                                        >Driver Reward Description (You have)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_reward_description_${activeLanguageId}`"
                                                    :id="`driver_reward_description_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_reward_description'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_reward_description'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_reward_description.driver_reward_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_reward_description.driver_reward_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_my_reward_description1_${activeLanguageId}`"
                                                        >Driver Reward Description (Points as driver)</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_my_reward_description1_${activeLanguageId}`"
                                                    :id="`driver_my_reward_description1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_my_reward_description1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_my_reward_description1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_my_reward_description1.driver_my_reward_description1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_my_reward_description1.driver_my_reward_description1_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_reward_points_table_label_${activeLanguageId}`"
                                                        >Driver Reward Point Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_reward_points_table_label_${activeLanguageId}`"
                                                    :id="`driver_reward_points_table_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_reward_points_table_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_reward_points_table_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_reward_points_table_label.driver_reward_points_table_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_reward_points_table_label.driver_reward_points_table_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_reward_reward_table_label_${activeLanguageId}`"
                                                        >Driver Reward Table Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_reward_reward_table_label_${activeLanguageId}`"
                                                    :id="`driver_reward_reward_table_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_reward_reward_table_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_reward_reward_table_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_reward_reward_table_label.driver_reward_reward_table_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_reward_reward_table_label.driver_reward_reward_table_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`driver_reward_to_label_${activeLanguageId}`"
                                                        >Driver Reward To Label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_reward_to_label_${activeLanguageId}`"
                                                    :id="`driver_reward_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_reward_to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_reward_to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_reward_to_label.driver_reward_to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_reward_to_label.driver_reward_to_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>

                                    </div>
                                </div>
                                <!-- Driver / My rewards section end -->
                                
                            </div>
                        </template>
                        <button type="submit" class="button-exp-fill mt-5">
                            Submit
                        </button>
                    </form>
                </div>
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
            collapseStates: [true, false, false, false, false, false, false, false, false],
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
        },
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
                            this.handleInput("", language, "card_heading");
                            this.handleInput("", language, "driver_heading");
                            this.handleInput("", language, "passenger_heading");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "balance_heading");
                            this.handleInput("", language, "mobile_indicate_field_label");
                            this.handleInput("", language, "passenger_my_ride_heading");
                            this.handleInput("", language, "passenger_ride_id_label");
                            this.handleInput("", language, "passenger_my_ride_from_label");
                            this.handleInput("", language, "passenger_my_ride_to_label");
                            this.handleInput("", language, "passenger_my_ride_date_label");
                            this.handleInput("", language, "passenger_my_ride_booking_fee_label");
                            this.handleInput("", language, "passenger_my_ride_fare_label");
                            this.handleInput("", language, "passenger_my_ride_total_amount_label");
                            this.handleInput("", language, "passenger_my_reward_heading");
                            this.handleInput("", language, "passenger_my_reward_description");
                            this.handleInput("", language, "passenger_my_reward_description1");
                            this.handleInput("", language, "passenger_my_reward_points_table_label");
                            this.handleInput("", language, "passenger_my_reward_reward_table_label");
                            this.handleInput("", language, "passenger_my_reward_to_label");
                            this.handleInput("", language, "driver_paid_out_heading");
                            this.handleInput("", language, "driver_availabe_heading");
                            this.handleInput("", language, "driver_paid_ride_id_label");
                            this.handleInput("", language, "driver_paid_from_label");
                            this.handleInput("", language, "driver_paid_to_label");
                            this.handleInput("", language, "driver_paid_paid_out_date_label");
                            this.handleInput("", language, "driver_paid_total_amount_label");
                            this.handleInput("", language, "no_paid_out_found");
                            this.handleInput("", language, "no_pending_found_message");
                            this.handleInput("", language, "driver_available_ride_id_label");
                            this.handleInput("", language, "driver_available_from_label");
                            this.handleInput("", language, "driver_available_to_label");
                            this.handleInput("", language, "driver_available_date_label");
                            this.handleInput("", language, "driver_available_total_amount_label");
                            this.handleInput("", language, "driver_pending_heading");
                            this.handleInput("", language, "driver_pending_data_description");
                            this.handleInput("", language, "driver_reward_heading");
                            this.handleInput("", language, "driver_reward_description");
                            this.handleInput("", language, "driver_my_reward_description1");
                            this.handleInput("", language, "driver_reward_points_table_label");
                            this.handleInput("", language, "driver_reward_reward_table_label");
                            this.handleInput("", language, "driver_reward_to_label");
                            this.handleInput("", language, "balance_id_label");
                            this.handleInput("", language, "balance_amount_label");
                            this.handleInput("", language, "credit_card_label");
                            this.handleInput("", language, "balance_date_label");
                            this.handleInput("", language, "balance_buy_more_button_text");
                            this.handleInput("", language, "no_more_data_message");
                            this.handleInput("", language, "no_my_ride_message");
                            this.handleInput("", language, "no_reward_found_message");
                            this.handleInput("", language, "no_paid_out_message");
                            this.handleInput("", language, "no_balance_found_message");
                            this.handleInput("", language, "request_transfer_label");
                            this.handleInput("", language, "driver_pending_date_label");
                            this.handleInput("", language, "no_pending_found_message");
                            this.handleInput("", language, "no_driver_found_message");
                            this.handleInput("", language, "top_up_main_heading");
                            this.handleInput("", language, "purchase_top_up_label");
                            this.handleInput("", language, "purchase_top_up_placeholder");
                            this.handleInput("", language, "purchase_top_up_error");
                            this.handleInput("", language, "pay_with_label");
                            this.handleInput("", language, "must_add_amount_toltip");
                            this.handleInput("", language, "ride_fare_main_heading");
                            this.handleInput("", language, "passenger_label");
                            this.handleInput("", language, "fare_label");
                            this.handleInput("", language, "booking_fee_label");
                            this.handleInput("", language, "total_label");
                            this.handleInput("", language, "claim_my_reward_button_text");
                        });
                    this.fetchMyWalletSetting();
                }
            });
        },
        fetchMyWalletSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-my-wallet-setting`)
                .then((res) => {
                    console.log(res);
                    if (res?.data?.status == "Success") {
                        let my_wallet_setting_detail =
                            res?.data?.data?.my_wallet_setting_detail || [];
                        my_wallet_setting_detail.map((setting) => {

                            this.handleInput(
                                setting?.card_heading,
                                setting?.language,
                                "card_heading"
                            );
                            this.handleInput(
                                setting?.passenger_heading,
                                setting?.language,
                                "passenger_heading"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.driver_heading,
                                setting?.language,
                                "driver_heading"
                            );
                            this.handleInput(
                                setting?.balance_heading,
                                setting?.language,
                                "balance_heading"
                            );
                             this.handleInput(
                                setting?.mobile_indicate_field_label,
                                setting?.language,
                                "mobile_indicate_field_label"
                            );

                            this.handleInput(
                                setting?.no_pending_found_message,
                                setting?.language,
                                "no_pending_found_message"
                            );

                            this.handleInput(
                                setting?.passenger_my_ride_heading,
                                setting?.language,
                                "passenger_my_ride_heading"
                            );
                            this.handleInput(
                                setting?.passenger_ride_id_label,
                                setting?.language,
                                "passenger_ride_id_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_ride_from_label,
                                setting?.language,
                                "passenger_my_ride_from_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_ride_to_label,
                                setting?.language,
                                "passenger_my_ride_to_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_ride_date_label,
                                setting?.language,
                                "passenger_my_ride_date_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_ride_booking_fee_label,
                                setting?.language,
                                "passenger_my_ride_booking_fee_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_ride_fare_label,
                                setting?.language,
                                "passenger_my_ride_fare_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_ride_total_amount_label,
                                setting?.language,
                                "passenger_my_ride_total_amount_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_reward_heading,
                                setting?.language,
                                "passenger_my_reward_heading"
                            );

                            this.handleInput(
                                setting?.passenger_my_reward_description,
                                setting?.language,
                                "passenger_my_reward_description"
                            );
                            this.handleInput(
                                setting?.passenger_my_reward_description1,
                                setting?.language,
                                "passenger_my_reward_description1"
                            );
                            this.handleInput(
                                setting?.passenger_my_reward_points_table_label,
                                setting?.language,
                                "passenger_my_reward_points_table_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_reward_reward_table_label,
                                setting?.language,
                                "passenger_my_reward_reward_table_label"
                            );
                            this.handleInput(
                                setting?.passenger_my_reward_to_label,
                                setting?.language,
                                "passenger_my_reward_to_label"
                            );
                             this.handleInput(
                                setting?.driver_paid_out_heading,
                                setting?.language,
                                "driver_paid_out_heading"
                            );
                            this.handleInput(
                                setting?.driver_availabe_heading,
                                setting?.language,
                                "driver_availabe_heading"
                            );
                            this.handleInput(
                                setting?.driver_paid_ride_id_label,
                                setting?.language,
                                "driver_paid_ride_id_label"
                            );
                             this.handleInput(
                                setting?.driver_paid_from_label,
                                setting?.language,
                                "driver_paid_from_label"
                            );
                            this.handleInput(
                                setting?.driver_paid_to_label,
                                setting?.language,
                                "driver_paid_to_label"
                            );
                            this.handleInput(
                                setting?.driver_paid_paid_out_date_label,
                                setting?.language,
                                "driver_paid_paid_out_date_label"
                            );
                            this.handleInput(
                                setting?.driver_paid_total_amount_label,
                                setting?.language,
                                "driver_paid_total_amount_label"
                            );
                            this.handleInput(
                                setting?.driver_available_ride_id_label,
                                setting?.language,
                                "driver_available_ride_id_label"
                            );
                            this.handleInput(
                                setting?.driver_available_from_label,
                                setting?.language,
                                "driver_available_from_label"
                            );
                            this.handleInput(
                                setting?.driver_available_to_label,
                                setting?.language,
                                "driver_available_to_label"
                            );
                            this.handleInput(
                                setting?.driver_available_date_label,
                                setting?.language,
                                "driver_available_date_label"
                            );
                            this.handleInput(
                                setting?.driver_available_total_amount_label,
                                setting?.language,
                                "driver_available_total_amount_label"
                            );
                            this.handleInput(
                                setting?.driver_pending_heading,
                                setting?.language,
                                "driver_pending_heading"
                            );
                            this.handleInput(
                                setting?.driver_pending_data_description,
                                setting?.language,
                                "driver_pending_data_description"
                            );
                            this.handleInput(
                                setting?.driver_reward_heading,
                                setting?.language,
                                "driver_reward_heading"
                            );
                            this.handleInput(
                                setting?.driver_reward_description,
                                setting?.language,
                                "driver_reward_description"
                            );
                            this.handleInput(
                                setting?.no_paid_out_found,
                                setting?.language,
                                "no_paid_out_found"
                            );
                            this.handleInput(
                                setting?.driver_my_reward_description1,
                                setting?.language,
                                "driver_my_reward_description1"
                            );
                            this.handleInput(
                                setting?.driver_reward_points_table_label,
                                setting?.language,
                                "driver_reward_points_table_label"
                            );
                            this.handleInput(
                                setting?.driver_reward_reward_table_label,
                                setting?.language,
                                "driver_reward_reward_table_label"
                            );
                            this.handleInput(
                                setting?.driver_reward_to_label,
                                setting?.language,
                                "driver_reward_to_label"
                            );
                            this.handleInput(
                                setting?.balance_id_label,
                                setting?.language,
                                "balance_id_label"
                            );
                            this.handleInput(
                                setting?.balance_amount_label,
                                setting?.language,
                                "balance_amount_label"
                            );
                            this.handleInput(
                                setting?.balance_date_label,
                                setting?.language,
                                "balance_date_label"
                            );
                            this.handleInput(
                                setting?.balance_buy_more_button_text,
                                setting?.language,
                                "balance_buy_more_button_text"
                            );
                            this.handleInput(
                                setting?.no_more_data_message,
                                setting?.language,
                                "no_more_data_message"
                            );
                            this.handleInput(
                                setting?.no_my_ride_message,
                                setting?.language,
                                "no_my_ride_message"
                            );
                            this.handleInput(
                                setting?.no_reward_found_message,
                                setting?.language,
                                "no_reward_found_message"
                            );
                            this.handleInput(
                                setting?.no_paid_out_message,
                                setting?.language,
                                "no_paid_out_message"
                            );
                            this.handleInput(
                                setting?.no_balance_found_message,
                                setting?.language,
                                "no_balance_found_message"
                            );
                            this.handleInput(
                                setting?.request_transfer_label,
                                setting?.language,
                                "request_transfer_label"
                            );
                            this.handleInput(
                                setting?.driver_pending_date_label,
                                setting?.language,
                                "driver_pending_date_label"
                            );
                            this.handleInput(
                                setting?.no_pending_found_message,
                                setting?.language,
                                "no_pending_found_message"
                            );
                             this.handleInput(
                                setting?.no_driver_found_message,
                                setting?.language,
                                "no_driver_found_message"
                            ); this.handleInput(
                                setting?.top_up_main_heading,
                                setting?.language,
                                "top_up_main_heading"
                            ); this.handleInput(
                                setting?.purchase_top_up_label,
                                setting?.language,
                                "purchase_top_up_label"
                            ); this.handleInput(
                                setting?.purchase_top_up_placeholder,
                                setting?.language,
                                "purchase_top_up_placeholder"
                            ); this.handleInput(
                                setting?.purchase_top_up_error,
                                setting?.language,
                                "purchase_top_up_error"
                            ); this.handleInput(
                                setting?.pay_with_label,
                                setting?.language,
                                "pay_with_label"
                            ); this.handleInput(
                                setting?.passenger_label,
                                setting?.language,
                                "passenger_label"
                            ); this.handleInput(
                                setting?.must_add_amount_toltip,
                                setting?.language,
                                "must_add_amount_toltip"
                            ); this.handleInput(
                                setting?.fare_label,
                                setting?.language,
                                "fare_label"
                            ); this.handleInput(
                                setting?.booking_fee_label,
                                setting?.language,
                                "booking_fee_label"
                            ); this.handleInput(
                                setting?.ride_fare_main_heading,
                                setting?.language,
                                "ride_fare_main_heading"
                            );
                            this.handleInput(
                                setting?.total_label,
                                setting?.language,
                                "total_label"
                            );this.handleInput(
                                setting?.credit_card_label,
                                setting?.language,
                                "credit_card_label"
                            );

                            this.handleInput(
                                setting?.claim_my_reward_button_text,
                                setting?.language,
                                "claim_my_reward_button_text"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-my-wallet-setting`,
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
                    `card_heading.card_heading_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_heading.passenger_heading_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `driver_heading.driver_heading_${language.id}`
                ) ||
                validationErros.has(
                    `balance_heading.balance_heading_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_my_ride_heading.passenger_my_ride_heading_${language.id}`
                )||
                validationErros.has(
                    `passenger_ride_id_label.passenger_ride_id_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_ride_from_label.passenger_my_ride_from_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_ride_to_label.passenger_my_ride_to_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_ride_date_label.passenger_my_ride_date_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_ride_booking_fee_label.passenger_my_ride_booking_fee_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_ride_fare_label.passenger_my_ride_fare_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_ride_total_amount_label.passenger_my_ride_total_amount_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_reward_heading.passenger_my_reward_heading_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_reward_description.passenger_my_reward_description_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_reward_description1.passenger_my_reward_description1_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_reward_points_table_label.passenger_my_reward_points_table_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_reward_reward_table_label.passenger_my_reward_reward_table_label_${language.id}`
                )||
                validationErros.has(
                    `passenger_my_reward_to_label.passenger_my_reward_to_label_${language.id}`
                )||
                validationErros.has(
                    `driver_paid_out_heading.driver_paid_out_heading_${language.id}`
                )||
                validationErros.has(
                    `driver_availabe_heading.driver_availabe_heading_${language.id}`
                )||
                validationErros.has(
                    `driver_paid_ride_id_label.driver_paid_ride_id_label_${language.id}`
                )||
                validationErros.has(
                    `driver_paid_from_label.driver_paid_from_label_${language.id}`
                )||
                validationErros.has(
                    `driver_paid_to_label.driver_paid_to_label_${language.id}`
                )||
                validationErros.has(
                    `driver_paid_paid_out_date_label.driver_paid_paid_out_date_label_${language.id}`
                )||
                validationErros.has(
                    `driver_paid_total_amount_label.driver_paid_total_amount_label_${language.id}`
                )||
                validationErros.has(
                    `no_paid_out_found.no_paid_out_found_${language.id}`
                )||
                validationErros.has(
                    `no_pending_found_message.no_pending_found_message_${language.id}`
                )||
                validationErros.has(
                    `driver_reward_heading.driver_reward_heading_${language.id}`
                )||
                validationErros.has(
                    `driver_available_ride_id_label.driver_available_ride_id_label_${language.id}`
                )||
                validationErros.has(
                    `driver_available_from_label.driver_available_from_label_${language.id}`
                )||
                validationErros.has(
                    `driver_available_to_label.driver_available_to_label_${language.id}`
                )||
                validationErros.has(
                    `driver_available_date_label.driver_available_date_label_${language.id}`
                )||
                validationErros.has(
                    `driver_available_total_amount_label.driver_available_total_amount_label_${language.id}`
                )||
                validationErros.has(
                    `driver_pending_heading.driver_pending_heading_${language.id}`
                )||
                validationErros.has(
                    `driver_pending_data_description.driver_pending_data_description_${language.id}`
                )||
                validationErros.has(
                    `driver_reward_description.driver_reward_description_${language.id}`
                )||
                validationErros.has(
                    `driver_my_reward_description1.driver_my_reward_description1_${language.id}`
                )||
                validationErros.has(
                    `driver_reward_reward_table_label.driver_reward_reward_table_label_${language.id}`
                )||
                validationErros.has(
                    `driver_reward_to_label.driver_reward_to_label_${language.id}`
                )||
                validationErros.has(
                    `balance_id_label.balance_id_label_${language.id}`
                )||
                validationErros.has(
                    `balance_amount_label.balance_amount_label_${language.id}`
                )||
                validationErros.has(
                    `balance_date_label.balance_date_label_${language.id}`
                )||
                validationErros.has(
                    `balance_buy_more_button_text.balance_buy_more_button_text_${language.id}`
                )||
                validationErros.has(
                    `no_more_data_message.no_more_data_message_${language.id}`
                )||
                validationErros.has(
                    `no_my_ride_message.no_my_ride_message_${language.id}`
                )||
                validationErros.has(
                    `no_reward_found_message.no_reward_found_message_${language.id}`
                )||
                validationErros.has(
                    `no_paid_out_message.no_paid_out_message_${language.id}`
                )||
                validationErros.has(
                    `no_balance_found_message.no_balance_found_message_${language.id}`
                )||
                validationErros.has(
                    `request_transfer_label.request_transfer_label_${language.id}`
                )||
                validationErros.has(
                    `driver_pending_date_label.driver_pending_date_label_${language.id}`
                )||
                validationErros.has(
                    `no_pending_found_message.no_pending_found_message_${language.id}`
                )||
                validationErros.has(
                    `claim_my_reward_button_text.claim_my_reward_button_text_${language.id}`
                )||
                validationErros.has(
                    `no_driver_found_message.no_driver_found_message_${language.id}`
                )
            );
        },
    },
};
</script>
