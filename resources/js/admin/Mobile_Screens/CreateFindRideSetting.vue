<template>
    <AppLayout>
        <section class="find-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Find ride page settings
                                </h3>
                            </div>
                        </div>
                    </header>
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
                                            collapseStates[0] =
                                                !collapseStates[0]
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
                                        v-if="collapseStates[0]"
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
                                    </div>
                                </div>
                                <!-- main section end -->

                                <!-- search section start -->
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
                                            Search section
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
                                                        :for="`search_section_from_label_${activeLanguageId}`"
                                                        >From label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_from_label_${activeLanguageId}`"
                                                    :id="`search_section_from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_from_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_from_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_from_label.search_section_from_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_from_label.search_section_from_label_${activeLanguageId}`
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
                                                        :for="`search_section_from_placeholder_${activeLanguageId}`"
                                                        >From placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_from_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_from_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_from_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_from_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_from_placeholder.search_section_from_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_from_placeholder.search_section_from_placeholder_${activeLanguageId}`
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
                                                        :for="`search_section_to_label_${activeLanguageId}`"
                                                        >To label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_to_label_${activeLanguageId}`"
                                                    :id="`search_section_to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_to_label.search_section_to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_to_label.search_section_to_label_${activeLanguageId}`
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
                                                        :for="`search_section_to_placeholder_${activeLanguageId}`"
                                                        >To placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_to_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_to_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_to_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_to_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_to_placeholder.search_section_to_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_to_placeholder.search_section_to_placeholder_${activeLanguageId}`
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
                                                        :for="`search_section_keyword_label_${activeLanguageId}`"
                                                        >Keyword label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_keyword_label_${activeLanguageId}`"
                                                    :id="`search_section_keyword_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_keyword_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_keyword_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_keyword_label.search_section_keyword_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_keyword_label.search_section_keyword_label_${activeLanguageId}`
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
                                                        :for="`search_section_date_placeholder_${activeLanguageId}`"
                                                        >Date placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_date_placeholder_${activeLanguageId}`"
                                                    :id="`search_section_date_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_date_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_date_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_date_placeholder.search_section_date_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_date_placeholder.search_section_date_placeholder_${activeLanguageId}`
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
                                                        :for="`search_section_pink_ride_label_${activeLanguageId}`"
                                                        >Search section pink ride label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_pink_ride_label_${activeLanguageId}`"
                                                    :id="`search_section_pink_ride_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_pink_ride_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_pink_ride_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_pink_ride_label.search_section_pink_ride_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_pink_ride_label.search_section_pink_ride_label_${activeLanguageId}`
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
                                                        :for="`search_section_extra_care_label_${activeLanguageId}`"
                                                        >Search section extra care label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_extra_care_label_${activeLanguageId}`"
                                                    :id="`search_section_extra_care_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_extra_care_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_extra_care_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_extra_care_label.search_section_extra_care_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_extra_care_label.search_section_extra_care_label_${activeLanguageId}`
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
                                                        :for="`search_section_button_label_${activeLanguageId}`"
                                                        >Search button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_button_label_${activeLanguageId}`"
                                                    :id="`search_section_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_button_label.search_section_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_button_label.search_section_button_label_${activeLanguageId}`
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
                                                        :for="`search_section_recent_searches_${activeLanguageId}`"
                                                        >Recent Searches heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`search_section_recent_searches_${activeLanguageId}`"
                                                    :id="`search_section_recent_searches_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'search_section_recent_searches'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'search_section_recent_searches'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `search_section_recent_searches.search_section_recent_searches_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `search_section_recent_searches.search_section_recent_searches_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- search section end -->

                                <!-- ride card section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
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
                                            Ride card section
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
                                                        :for="`card_section_at_label_${activeLanguageId}`"
                                                        >At label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_section_at_label_${activeLanguageId}`"
                                                    :id="`card_section_at_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_section_at_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_at_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_section_at_label.card_section_at_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_section_at_label.card_section_at_label_${activeLanguageId}`
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
                                                        :for="`card_section_per_seat_${activeLanguageId}`"
                                                        >Per seat</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_section_per_seat_${activeLanguageId}`"
                                                    :id="`card_section_per_seat_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_section_per_seat'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_per_seat'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_section_per_seat.card_section_per_seat_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_section_per_seat.card_section_per_seat_${activeLanguageId}`
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
                                                        :for="`card_section_age_${activeLanguageId}`"
                                                        >Age label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_section_age_${activeLanguageId}`"
                                                    :id="`card_section_age_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_section_age'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_age'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_section_age.card_section_age_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_section_age.card_section_age_${activeLanguageId}`
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
                                                        :for="`card_section_driven_${activeLanguageId}`"
                                                        >Driven label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_section_driven_${activeLanguageId}`"
                                                    :id="`card_section_driven_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_section_driven'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_driven'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_section_driven.card_section_driven_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_section_driven.card_section_driven_${activeLanguageId}`
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
                                                        :for="`card_section_review_${activeLanguageId}`"
                                                        >Review label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`card_section_review_${activeLanguageId}`"
                                                    :id="`card_section_review_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'card_section_review'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'card_section_review'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `card_section_review.card_section_review_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `card_section_review.card_section_review_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- ride card section end -->

                                <!-- filter section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
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
                                            Filters section
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
                                                        :for="`filter_section_heading_${activeLanguageId}`"
                                                        >Main heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`filter_section_heading_${activeLanguageId}`"
                                                    :id="`filter_section_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'filter_section_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter_section_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `filter_section_heading.filter_section_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `filter_section_heading.filter_section_heading_${activeLanguageId}`
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
                                                        :for="`filter1_driver_heading_${activeLanguageId}`"
                                                        >Driver heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`filter1_driver_heading_${activeLanguageId}`"
                                                    :id="`filter1_driver_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'filter1_driver_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter1_driver_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `filter1_driver_heading.filter1_driver_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `filter1_driver_heading.filter1_driver_heading_${activeLanguageId}`
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
                                                        :for="`driver_age_label_${activeLanguageId}`"
                                                        >Driver age label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_age_label_${activeLanguageId}`"
                                                    :id="`driver_age_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_age_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_age_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_age_label.driver_age_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_age_label.driver_age_label_${activeLanguageId}`
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
                                                        :for="`driver_age_placeholder_${activeLanguageId}`"
                                                        >Driver age placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_age_placeholder_${activeLanguageId}`"
                                                    :id="`driver_age_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_age_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_age_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_age_placeholder.driver_age_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_age_placeholder.driver_age_placeholder_${activeLanguageId}`
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
                                                        :for="`driver_rating_label_${activeLanguageId}`"
                                                        >Driver rating label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_rating_label_${activeLanguageId}`"
                                                    :id="`driver_rating_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_rating_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_rating_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_rating_label.driver_rating_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_rating_label.driver_rating_label_${activeLanguageId}`
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
                                                        :for="`driver_rating_placeholder_${activeLanguageId}`"
                                                        >Driver rating placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_rating_placeholder_${activeLanguageId}`"
                                                    :id="`driver_rating_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_rating_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_rating_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_rating_placeholder.driver_rating_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_rating_placeholder.driver_rating_placeholder_${activeLanguageId}`
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
                                                        :for="`driver_phone_access_label_${activeLanguageId}`"
                                                        >Driver phone access label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_phone_access_label_${activeLanguageId}`"
                                                    :id="`driver_phone_access_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_phone_access_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_phone_access_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_phone_access_label.driver_phone_access_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_phone_access_label.driver_phone_access_label_${activeLanguageId}`
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
                                                        :for="`driver_know_label_${activeLanguageId}`"
                                                        >Driver know label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_know_label_${activeLanguageId}`"
                                                    :id="`driver_know_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_know_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_know_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_know_label.driver_know_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_know_label.driver_know_label_${activeLanguageId}`
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
                                                        :for="`driver_know_placeholder_${activeLanguageId}`"
                                                        >Driver know placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`driver_know_placeholder_${activeLanguageId}`"
                                                    :id="`driver_know_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'driver_know_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'driver_know_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `driver_know_placeholder.driver_know_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `driver_know_placeholder.driver_know_placeholder_${activeLanguageId}`
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
                                                        :for="`filter2_passengers_heading_${activeLanguageId}`"
                                                        >Passengers heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`filter2_passengers_heading_${activeLanguageId}`"
                                                    :id="`filter2_passengers_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'filter2_passengers_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter2_passengers_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `filter2_passengers_heading.filter2_passengers_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `filter2_passengers_heading.filter2_passengers_heading_${activeLanguageId}`
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
                                                        :for="`passengers_rating_label_${activeLanguageId}`"
                                                        >Passengers rating label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passengers_rating_label_${activeLanguageId}`"
                                                    :id="`passengers_rating_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passengers_rating_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passengers_rating_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passengers_rating_label.passengers_rating_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passengers_rating_label.passengers_rating_label_${activeLanguageId}`
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
                                                        :for="`passengers_rating_placeholder_${activeLanguageId}`"
                                                        >Passengers rating placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`passengers_rating_placeholder_${activeLanguageId}`"
                                                    :id="`passengers_rating_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'passengers_rating_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'passengers_rating_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `passengers_rating_placeholder.passengers_rating_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `passengers_rating_placeholder.passengers_rating_placeholder_${activeLanguageId}`
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
                                                        :for="`filter3_payment_methods_heading_${activeLanguageId}`"
                                                        >Payment methods heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`filter3_payment_methods_heading_${activeLanguageId}`"
                                                    :id="`filter3_payment_methods_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'filter3_payment_methods_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter3_payment_methods_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `filter3_payment_methods_heading.filter3_payment_methods_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `filter3_payment_methods_heading.filter3_payment_methods_heading_${activeLanguageId}`
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
                                                        :for="`payment_methods_option1_${activeLanguageId}`"
                                                        >Payment methods option1</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payment_methods_option1_${activeLanguageId}`"
                                                    :id="`payment_methods_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payment_methods_option1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payment_methods_option1.payment_methods_option1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payment_methods_option1.payment_methods_option1_${activeLanguageId}`
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
                                                        :for="`filter4_vehicle_heading_${activeLanguageId}`"
                                                        >Vehicle heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`filter4_vehicle_heading_${activeLanguageId}`"
                                                    :id="`filter4_vehicle_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'filter4_vehicle_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'filter4_vehicle_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `filter4_vehicle_heading.filter4_vehicle_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `filter4_vehicle_heading.filter4_vehicle_heading_${activeLanguageId}`
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
                                                        :for="`vehicle_type_placeholder_${activeLanguageId}`"
                                                        >Vehicle type placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    :id="`vehicle_type_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_type_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_type_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_type_placeholder.vehicle_type_placeholder_${activeLanguageId}`
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
                                                        :for="`ride_preferences_label_${activeLanguageId}`"
                                                        >Ride preferences label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_preferences_label_${activeLanguageId}`"
                                                    :id="`ride_preferences_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_preferences_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_preferences_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_preferences_label.ride_preferences_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_preferences_label.ride_preferences_label_${activeLanguageId}`
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
                                                        >Luggage label</label
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
                                                        :for="`smoking_label_${activeLanguageId}`"
                                                        >Smoking label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`smoking_label_${activeLanguageId}`"
                                                    :id="`smoking_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'smoking_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `smoking_label.smoking_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `smoking_label.smoking_label_${activeLanguageId}`
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
                                                        :for="`pets_allowed_label_${activeLanguageId}`"
                                                        >Pets label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pets_allowed_label_${activeLanguageId}`"
                                                    :id="`pets_allowed_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pets_allowed_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pets_allowed_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pets_allowed_label.pets_allowed_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pets_allowed_label.pets_allowed_label_${activeLanguageId}`
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
                                                        :for="`clear_button_label_${activeLanguageId}`"
                                                        >Clear button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`clear_button_label_${activeLanguageId}`"
                                                    :id="`clear_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'clear_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'clear_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `clear_button_label.clear_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `clear_button_label.clear_button_label_${activeLanguageId}`
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
                                                        :for="`apply_button_label_${activeLanguageId}`"
                                                        >Apply button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`apply_button_label_${activeLanguageId}`"
                                                    :id="`apply_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'apply_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'apply_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `apply_button_label.apply_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `apply_button_label.apply_button_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- filter section end -->
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
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "search_section_from_label");
                            this.handleInput("", language, "search_section_from_placeholder");
                            this.handleInput("", language, "search_section_to_label");
                            this.handleInput("", language, "search_section_to_placeholder");
                            this.handleInput("", language, "search_section_keyword_label");
                            this.handleInput("", language, "search_section_date_placeholder");
                            this.handleInput("", language, "search_section_pink_ride_label");
                            this.handleInput("", language, "search_section_extra_care_label");
                            this.handleInput("", language, "search_section_button_label");
                            this.handleInput("", language, "search_section_recent_searches");
                            this.handleInput("", language, "card_section_at_label");
                            this.handleInput("", language, "card_section_per_seat");
                            this.handleInput("", language, "card_section_age");
                            this.handleInput("", language, "card_section_driven");
                            this.handleInput("", language, "card_section_review");
                            this.handleInput("", language, "filter_section_heading");
                            this.handleInput("", language, "filter1_driver_heading");
                            this.handleInput("", language, "driver_age_label");
                            this.handleInput("", language, "driver_age_placeholder");
                            this.handleInput("", language, "driver_rating_label");
                            this.handleInput("", language, "driver_rating_placeholder");
                            this.handleInput("", language, "driver_phone_access_label");
                            this.handleInput("", language, "driver_know_label");
                            this.handleInput("", language, "driver_know_placeholder");
                            this.handleInput("", language, "filter2_passengers_heading");
                            this.handleInput("", language, "passengers_rating_label");
                            this.handleInput("", language, "passengers_rating_placeholder");
                            this.handleInput("", language, "filter3_payment_methods_heading");
                            this.handleInput("", language, "payment_methods_option1");
                            this.handleInput("", language, "filter4_vehicle_heading");
                            this.handleInput("", language, "vehicle_type_placeholder");
                            this.handleInput("", language, "ride_preferences_label");
                            this.handleInput("", language, "luggage_label");
                            this.handleInput("", language, "smoking_label");
                            this.handleInput("", language, "pets_allowed_label");
                            this.handleInput("", language, "clear_button_label");
                            this.handleInput("", language, "apply_button_label");
                        });
                        this.fetchFindRidePageSetting();
                    }
                });
        },
        fetchFindRidePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-mobile-find-ride-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let mobile_find_ride_setting_detail =
                            res?.data?.data?.mobile_find_ride_setting_detail || [];
                        mobile_find_ride_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.search_section_from_label,
                                setting?.language,
                                "search_section_from_label"
                            );
                            this.handleInput(
                                setting?.search_section_from_placeholder,
                                setting?.language,
                                "search_section_from_placeholder"
                            );
                            this.handleInput(
                                setting?.search_section_to_label,
                                setting?.language,
                                "search_section_to_label"
                            );
                            this.handleInput(
                                setting?.search_section_to_placeholder,
                                setting?.language,
                                "search_section_to_placeholder"
                            );
                            this.handleInput(
                                setting?.search_section_keyword_label,
                                setting?.language,
                                "search_section_keyword_label"
                            );
                            this.handleInput(
                                setting?.search_section_date_placeholder,
                                setting?.language,
                                "search_section_date_placeholder"
                            );
                            this.handleInput(
                                setting?.search_section_pink_ride_label,
                                setting?.language,
                                "search_section_pink_ride_label"
                            );
                            this.handleInput(
                                setting?.search_section_extra_care_label,
                                setting?.language,
                                "search_section_extra_care_label"
                            );
                            this.handleInput(
                                setting?.search_section_button_label,
                                setting?.language,
                                "search_section_button_label"
                            );
                            this.handleInput(
                                setting?.search_section_recent_searches,
                                setting?.language,
                                "search_section_recent_searches"
                            );
                            this.handleInput(
                                setting?.card_section_at_label,
                                setting?.language,
                                "card_section_at_label"
                            );
                            this.handleInput(
                                setting?.card_section_per_seat,
                                setting?.language,
                                "card_section_per_seat"
                            );
                            this.handleInput(
                                setting?.card_section_age,
                                setting?.language,
                                "card_section_age"
                            );
                            this.handleInput(
                                setting?.card_section_driven,
                                setting?.language,
                                "card_section_driven"
                            );
                            this.handleInput(
                                setting?.card_section_review,
                                setting?.language,
                                "card_section_review"
                            );
                            this.handleInput(
                                setting?.filter_section_heading,
                                setting?.language,
                                "filter_section_heading"
                            );
                            this.handleInput(
                                setting?.filter1_driver_heading,
                                setting?.language,
                                "filter1_driver_heading"
                            );
                            this.handleInput(
                                setting?.driver_age_label,
                                setting?.language,
                                "driver_age_label"
                            );
                            this.handleInput(
                                setting?.driver_age_placeholder,
                                setting?.language,
                                "driver_age_placeholder"
                            );
                            this.handleInput(
                                setting?.driver_rating_label,
                                setting?.language,
                                "driver_rating_label"
                            );
                            this.handleInput(
                                setting?.driver_rating_placeholder,
                                setting?.language,
                                "driver_rating_placeholder"
                            );
                            this.handleInput(
                                setting?.driver_phone_access_label,
                                setting?.language,
                                "driver_phone_access_label"
                            );
                            this.handleInput(
                                setting?.driver_know_label,
                                setting?.language,
                                "driver_know_label"
                            );
                            this.handleInput(
                                setting?.driver_know_placeholder,
                                setting?.language,
                                "driver_know_placeholder"
                            );
                            this.handleInput(
                                setting?.filter2_passengers_heading,
                                setting?.language,
                                "filter2_passengers_heading"
                            );
                            this.handleInput(
                                setting?.passengers_rating_label,
                                setting?.language,
                                "passengers_rating_label"
                            );
                            this.handleInput(
                                setting?.passengers_rating_placeholder,
                                setting?.language,
                                "passengers_rating_placeholder"
                            );
                            this.handleInput(
                                setting?.filter3_payment_methods_heading,
                                setting?.language,
                                "filter3_payment_methods_heading"
                            );
                            this.handleInput(
                                setting?.payment_methods_option1,
                                setting?.language,
                                "payment_methods_option1"
                            );
                            this.handleInput(
                                setting?.filter4_vehicle_heading,
                                setting?.language,
                                "filter4_vehicle_heading"
                            );
                            this.handleInput(
                                setting?.vehicle_type_placeholder,
                                setting?.language,
                                "vehicle_type_placeholder"
                            );
                            this.handleInput(
                                setting?.ride_preferences_label,
                                setting?.language,
                                "ride_preferences_label"
                            );
                            this.handleInput(
                                setting?.luggage_label,
                                setting?.language,
                                "luggage_label"
                            );
                            this.handleInput(
                                setting?.smoking_label,
                                setting?.language,
                                "smoking_label"
                            );
                            this.handleInput(
                                setting?.pets_allowed_label,
                                setting?.language,
                                "pets_allowed_label"
                            );
                            this.handleInput(
                                setting?.clear_button_label,
                                setting?.language,
                                "clear_button_label"
                            );
                            this.handleInput(
                                setting?.apply_button_label,
                                setting?.language,
                                "apply_button_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-mobile-find-ride-page-setting`,
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
                    `main_heading.main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_from_label.search_section_from_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_from_placeholder.search_section_from_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_to_label.search_section_to_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_to_placeholder.search_section_to_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_keyword_label.search_section_keyword_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_date_placeholder.search_section_date_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_extra_care_label.search_section_extra_care_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_pink_ride_label.search_section_pink_ride_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_button_label.search_section_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `search_section_recent_searches.search_section_recent_searches_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_at_label.card_section_at_label_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_per_seat.card_section_per_seat_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_age.card_section_age_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_driven.card_section_driven_${language.id}`
                ) ||
                validationErros.has(
                    `card_section_review.card_section_review_${language.id}`
                ) ||
                validationErros.has(
                    `filter_section_heading.filter_section_heading_${language.id}`
                ) ||
                validationErros.has(
                    `filter1_driver_heading.filter1_driver_heading_${language.id}`
                ) ||
                validationErros.has(
                    `driver_age_label.driver_age_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_age_placeholder.driver_age_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `driver_rating_label.driver_rating_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_rating_placeholder.driver_rating_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `driver_phone_access_label.driver_phone_access_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_know_label.driver_know_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_know_placeholder.driver_know_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `filter2_passengers_heading.filter2_passengers_heading_${language.id}`
                ) ||
                validationErros.has(
                    `passengers_rating_label.passengers_rating_label_${language.id}`
                ) ||
                validationErros.has(
                    `passengers_rating_placeholder.passengers_rating_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `filter3_payment_methods_heading.filter3_payment_methods_heading_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option1.payment_methods_option1_${language.id}`
                ) ||
                validationErros.has(
                    `filter4_vehicle_heading.filter4_vehicle_heading_${language.id}`
                ) ||
                validationErros.has(
                    `vehicle_type_placeholder.vehicle_type_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_label.luggage_label_${language.id}`
                ) ||
                validationErros.has(
                    `ride_preferences_label.ride_preferences_label_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_label.smoking_label_${language.id}`
                ) ||
                validationErros.has(
                    `pets_allowed_label.pets_allowed_label_${language.id}`
                ) ||
                validationErros.has(
                    `clear_button_label.clear_button_label_${language.id}`
                ) ||
                validationErros.has(
                    `apply_button_label.apply_button_label_${language.id}`
                )
            );
        },
    },
};
</script>
