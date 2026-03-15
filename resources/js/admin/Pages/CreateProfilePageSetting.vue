<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Profile page settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <!-- Excel Upload Section (all languages template) -->
                    <ExcelBulkImport
                        title="Profile Page"
                        mode="all_languages"
                        download-endpoint="download-profile-page-setting-template"
                        upload-endpoint="upload-profile-page-setting-excel"
                        @success="fetchProfilePageSetting"
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
                                                    :for="`profile_setting_label_${activeLanguageId}`"
                                                    >Profile Setting</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`profile_setting_label_${activeLanguageId}`"
                                                :id="`profile_setting_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'profile_setting_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'profile_setting_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `profile_setting_label.profile_setting_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `profile_setting_label.profile_setting_label_${activeLanguageId}`
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
                                                        >Main heading (Web)</label
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
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`my_wallet_label_${activeLanguageId}`"
                                                    >My Wallet</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`my_wallet_label_${activeLanguageId}`"
                                                :id="`my_wallet_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'my_wallet_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'my_wallet_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `my_wallet_label.my_wallet_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `my_wallet_label.my_wallet_label_${activeLanguageId}`
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
                                                        :for="`payment_options_label_${activeLanguageId}`"
                                                        >Payment Option</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payment_options_label_${activeLanguageId}`"
                                                    :id="`payment_options_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payment_options_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_options_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payment_options_label.payment_options_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payment_options_label.payment_options_label_${activeLanguageId}`
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
                                                        :for="`refund_policy_label_${activeLanguageId}`"
                                                        >Refund Policy</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`refund_policy_label_${activeLanguageId}`"
                                                    :id="`refund_policy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'refund_policy_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'refund_policy_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `refund_policy_label.refund_policy_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `refund_policy_label.refund_policy_label_${activeLanguageId}`
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
                                                        >Cancellation Policy</label
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
                                                        :for="`dispute_policy_label_${activeLanguageId}`"
                                                        >Dispute Policy</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`dispute_policy_label_${activeLanguageId}`"
                                                    :id="`dispute_policy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'dispute_policy_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'dispute_policy_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `dispute_policy_label.dispute_policy_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `dispute_policy_label.dispute_policy_label_${activeLanguageId}`
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
                                                        :for="`contact_proximaride_label_${activeLanguageId}`"
                                                        >Contact ProximaRide</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`contact_proximaride_label_${activeLanguageId}`"
                                                    :id="`contact_proximaride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'contact_proximaride_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'contact_proximaride_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `contact_proximaride_label.contact_proximaride_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `contact_proximaride_label.contact_proximaride_label_${activeLanguageId}`
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
                                                        :for="`colse_your_contact_label_${activeLanguageId}`"
                                                        >Close Your Account</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`colse_your_contact_label_${activeLanguageId}`"
                                                    :id="`colse_your_contact_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'colse_your_contact_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'colse_your_contact_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `colse_your_contact_label.colse_your_contact_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `colse_your_contact_label.colse_your_contact_label_${activeLanguageId}`
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
                                                        :for="`logout_label_${activeLanguageId}`"
                                                        >Logout</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`logout_label_${activeLanguageId}`"
                                                    :id="`logout_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'logout_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'logout_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `logout_label.logout_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `logout_label.logout_label_${activeLanguageId}`
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
                                                        :for="`payout_options_label_${activeLanguageId}`"
                                                        >Payout Option</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payout_options_label_${activeLanguageId}`"
                                                    :id="`payout_options_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payout_options_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payout_options_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payout_options_label.payout_options_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payout_options_label.payout_options_label_${activeLanguageId}`
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
                                                        :for="`my_reviews_label_${activeLanguageId}`"
                                                        >My Reviews</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`my_reviews_label_${activeLanguageId}`"
                                                    :id="`my_reviews_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'my_reviews_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'my_reviews_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `my_reviews_label.my_reviews_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `my_reviews_label.my_reviews_label_${activeLanguageId}`
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
                                                        :for="`terms_condition_label_${activeLanguageId}`"
                                                        >Terms & Condition</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`terms_condition_label_${activeLanguageId}`"
                                                    :id="`terms_condition_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'terms_condition_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'terms_condition_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `terms_condition_label.terms_condition_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `terms_condition_label.terms_condition_label_${activeLanguageId}`
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
                                                        :for="`privacy_policy_label_${activeLanguageId}`"
                                                        >Privacy Policy</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`privacy_policy_label_${activeLanguageId}`"
                                                    :id="`privacy_policy_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'privacy_policy_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'privacy_policy_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `privacy_policy_label.privacy_policy_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `privacy_policy_label.privacy_policy_label_${activeLanguageId}`
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
                                                        :for="`terms_of_use_label_${activeLanguageId}`"
                                                        >Term of Use</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`terms_of_use_label_${activeLanguageId}`"
                                                    :id="`terms_of_use_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'terms_of_use_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'terms_of_use_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `terms_of_use_label.terms_of_use_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `terms_of_use_label.terms_of_use_label_${activeLanguageId}`
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
                            this.handleInput("", language, "profile_setting_label");
                            this.handleInput("", language, "my_wallet_label");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "payment_options_label");
                            this.handleInput("", language, "payout_options_label");
                            this.handleInput("", language, "refund_policy_label");
                            this.handleInput("", language, "cancellation_policy_label");
                            this.handleInput("", language, "dispute_policy_label");
                            this.handleInput("", language, "contact_proximaride_label");
                            this.handleInput("", language, "logout_label");
                            this.handleInput("", language, "colse_your_contact_label");
                            // this.handleInput("", language, "profile_page_setting");
                            this.handleInput("", language, "my_reviews_label");
                            this.handleInput("", language, "terms_condition_label");
                            this.handleInput("", language, "privacy_policy_label");
                            this.handleInput("", language, "terms_of_use_label");
                        });
                        this.fetchProfilePageSetting();
                    }
                });
        },
        fetchProfilePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-profile-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let profile_page_setting_detail =
                            res?.data?.data?.profile_page_setting_detail || [];
                        profile_page_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.name,
                                setting?.language,
                                "name"
                            );
                            this.handleInput(
                                setting?.profile_setting_label,
                                setting?.language,
                                "profile_setting_label"
                            );
                            this.handleInput(
                                setting?.my_wallet_label,
                                setting?.language,
                                "my_wallet_label"
                            );
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.payment_options_label,
                                setting?.language,
                                "payment_options_label"
                            );
                            this.handleInput(
                                setting?.payout_options_label,
                                setting?.language,
                                "payout_options_label"
                            );
                            this.handleInput(
                                setting?.refund_policy_label,
                                setting?.language,
                                "refund_policy_label"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label,
                                setting?.language,
                                "cancellation_policy_label"
                            );
                            this.handleInput(
                                setting?.dispute_policy_label,
                                setting?.language,
                                "dispute_policy_label"
                            );
                            this.handleInput(
                                setting?.contact_proximaride_label,
                                setting?.language,
                                "contact_proximaride_label"
                            );
                            this.handleInput(
                                setting?.logout_label,
                                setting?.language,
                                "logout_label"
                            );
                            this.handleInput(
                                setting?.colse_your_contact_label,
                                setting?.language,
                                "colse_your_contact_label"
                            );
                            // this.handleInput(
                            //     setting?.profile_page_setting,
                            //     setting?.language,
                            //     "profile_page_setting"
                            // );
                            this.handleInput(
                                setting?.my_reviews_label,
                                setting?.language,
                                "my_reviews_label"
                            );
                            this.handleInput(
                                setting?.terms_condition_label,
                                setting?.language,
                                "terms_condition_label"
                            );
                            this.handleInput(
                                setting?.privacy_policy_label,
                                setting?.language,
                                "privacy_policy_label"
                            );
                            this.handleInput(
                                setting?.terms_of_use_label,
                                setting?.language,
                                "terms_of_use_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-profile-page-setting`,
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
                    `profile_setting_label.profile_setting_label_${language.id}`
                ) ||
                validationErros.has(
                    `my_wallet_label.my_wallet_label_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `payment_options_label.payment_options_label_${language.id}`
                ) ||
                validationErros.has(
                    `payout_options_label.payout_options_label_${language.id}`
                ) ||
                validationErros.has(
                    `my_reviews_label.my_reviews_label_${language.id}`
                ) ||
                validationErros.has(
                    `terms_condition_label.terms_condition_label_${language.id}`
                ) ||
                validationErros.has(
                    `privacy_policy_label.privacy_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `terms_of_use_label.terms_of_use_label_${language.id}`
                ) ||
                validationErros.has(
                    `refund_policy_label.refund_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label.cancellation_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `dispute_policy_label.dispute_policy_label_${language.id}`
                ) ||
                validationErros.has(
                    `contact_proximaride_label.contact_proximaride_label_${language.id}`
                ) ||
                validationErros.has(
                    `logout_label.logout_label_${language.id}`
                ) ||
                validationErros.has(
                    `colse_your_contact_label.colse_your_contact_label_${language.id}`
                )
            );
        },
    },
};
</script>
