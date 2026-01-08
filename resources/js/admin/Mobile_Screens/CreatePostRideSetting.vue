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
                                </div>

                                <!-- top section start -->
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
                                            Top section
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
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`post_arrived_again_label_${activeLanguageId}`"
                                                        >Post arrived again button heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`post_arrived_again_label_${activeLanguageId}`"
                                                    :id="`post_arrived_again_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'post_arrived_again_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'post_arrived_again_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `post_arrived_again_label.post_arrived_again_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `post_arrived_again_label.post_arrived_again_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- top section end -->

                                <!-- ride details section start -->
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
                                            Ride info section
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
                                                        :for="`ride_info_heading_${activeLanguageId}`"
                                                        >Ride info heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`ride_info_heading_${activeLanguageId}`"
                                                    :id="`ride_info_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'ride_info_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'ride_info_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `ride_info_heading.ride_info_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `ride_info_heading.ride_info_heading_${activeLanguageId}`
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
                                                        :for="`from_label_${activeLanguageId}`"
                                                        >From label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`from_label_${activeLanguageId}`"
                                                    :id="`from_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'from_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'from_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `from_label.from_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `from_label.from_label_${activeLanguageId}`
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
                                                        :for="`from_placeholder_${activeLanguageId}`"
                                                        >From placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`from_placeholder_${activeLanguageId}`"
                                                    :id="`from_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'from_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'from_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `from_placeholder.from_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `from_placeholder.from_placeholder_${activeLanguageId}`
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
                                                        :for="`to_label_${activeLanguageId}`"
                                                        >To label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`to_label_${activeLanguageId}`"
                                                    :id="`to_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'to_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'to_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `to_label.to_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `to_label.to_label_${activeLanguageId}`
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
                                                        :for="`to_placeholder_${activeLanguageId}`"
                                                        >To placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`to_placeholder_${activeLanguageId}`"
                                                    :id="`to_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'to_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'to_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `to_placeholder.to_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `to_placeholder.to_placeholder_${activeLanguageId}`
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
                                                        :for="`pick_up_label_${activeLanguageId}`"
                                                        >Pick up label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pick_up_label_${activeLanguageId}`"
                                                    :id="`pick_up_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pick_up_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pick_up_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pick_up_label.pick_up_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pick_up_label.pick_up_label_${activeLanguageId}`
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
                                                        :for="`pick_up_placeholder_${activeLanguageId}`"
                                                        >Pick up placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`pick_up_placeholder_${activeLanguageId}`"
                                                    :id="`pick_up_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'pick_up_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'pick_up_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `pick_up_placeholder.pick_up_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `pick_up_placeholder.pick_up_placeholder_${activeLanguageId}`
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
                                                        :for="`drop_off_label_${activeLanguageId}`"
                                                        >drop off label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`drop_off_label_${activeLanguageId}`"
                                                    :id="`drop_off_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'drop_off_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'drop_off_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `drop_off_label.drop_off_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `drop_off_label.drop_off_label_${activeLanguageId}`
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
                                                        :for="`drop_off_placeholder_${activeLanguageId}`"
                                                        >drop off placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`drop_off_placeholder_${activeLanguageId}`"
                                                    :id="`drop_off_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'drop_off_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'drop_off_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `drop_off_placeholder.drop_off_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `drop_off_placeholder.drop_off_placeholder_${activeLanguageId}`
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
                                                        :for="`date_time_label_${activeLanguageId}`"
                                                        >Date time label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`date_time_label_${activeLanguageId}`"
                                                    :id="`date_time_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'date_time_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'date_time_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `date_time_label.date_time_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `date_time_label.date_time_label_${activeLanguageId}`
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
                                                        :for="`at_label_${activeLanguageId}`"
                                                        >At label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`at_label_${activeLanguageId}`"
                                                    :id="`at_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'at_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'at_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `at_label.at_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `at_label.at_label_${activeLanguageId}`
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
                                                        :for="`recurring_label_${activeLanguageId}`"
                                                        >Recurring label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`recurring_label_${activeLanguageId}`"
                                                    :id="`recurring_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'recurring_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recurring_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `recurring_label.recurring_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `recurring_label.recurring_label_${activeLanguageId}`
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
                                                        :for="`recurring_type_label_${activeLanguageId}`"
                                                        >Recurring type label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`recurring_type_label_${activeLanguageId}`"
                                                    :id="`recurring_type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'recurring_type_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recurring_type_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `recurring_type_label.recurring_type_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `recurring_type_label.recurring_type_label_${activeLanguageId}`
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
                                                        :for="`recurring_trips_label_${activeLanguageId}`"
                                                        >Recurring trips label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`recurring_trips_label_${activeLanguageId}`"
                                                    :id="`recurring_trips_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'recurring_trips_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'recurring_trips_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `recurring_trips_label.recurring_trips_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `recurring_trips_label.recurring_trips_label_${activeLanguageId}`
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
                                                        :for="`meeting_drop_off_description_label_${activeLanguageId}`"
                                                        >Meeting drop-off description label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`meeting_drop_off_description_label_${activeLanguageId}`"
                                                    :id="`meeting_drop_off_description_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'meeting_drop_off_description_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'meeting_drop_off_description_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `meeting_drop_off_description_label.meeting_drop_off_description_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `meeting_drop_off_description_label.meeting_drop_off_description_label_${activeLanguageId}`
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
                                                        :for="`meeting_drop_off_description_placeholder_${activeLanguageId}`"
                                                        >Meeting drop-off description placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`meeting_drop_off_description_placeholder_${activeLanguageId}`"
                                                    :id="`meeting_drop_off_description_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'meeting_drop_off_description_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'meeting_drop_off_description_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `meeting_drop_off_description_placeholder.meeting_drop_off_description_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `meeting_drop_off_description_placeholder.meeting_drop_off_description_placeholder_${activeLanguageId}`
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
                                                        :for="`seats_label_${activeLanguageId}`"
                                                        >Seats label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_label_${activeLanguageId}`"
                                                    :id="`seats_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_label.seats_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_label.seats_label_${activeLanguageId}`
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
                                                        :for="`seats_middle_label_${activeLanguageId}`"
                                                        >Seats middle label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_middle_label_${activeLanguageId}`"
                                                    :id="`seats_middle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_middle_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_middle_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_middle_label.seats_middle_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_middle_label.seats_middle_label_${activeLanguageId}`
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
                                                        :for="`seats_back_label_${activeLanguageId}`"
                                                        >Seats back label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`seats_back_label_${activeLanguageId}`"
                                                    :id="`seats_back_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'seats_back_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'seats_back_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `seats_back_label.seats_back_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `seats_back_label.seats_back_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- ride details section end -->

                                <!-- vehicle details section start -->
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
                                            Vehicle details section
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
                                                        :for="`vehicle_label_${activeLanguageId}`"
                                                        >Vehicle label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`vehicle_label_${activeLanguageId}`"
                                                    :id="`vehicle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'vehicle_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'vehicle_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `vehicle_label.vehicle_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `vehicle_label.vehicle_label_${activeLanguageId}`
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
                                                        :for="`skip_label_${activeLanguageId}`"
                                                        >Skip label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`skip_label_${activeLanguageId}`"
                                                    :id="`skip_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'skip_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'skip_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `skip_label.skip_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `skip_label.skip_label_${activeLanguageId}`
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
                                                        :for="`add_vehicle_label_${activeLanguageId}`"
                                                        >Add vehicle label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`add_vehicle_label_${activeLanguageId}`"
                                                    :id="`add_vehicle_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'add_vehicle_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'add_vehicle_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `add_vehicle_label.add_vehicle_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `add_vehicle_label.add_vehicle_label_${activeLanguageId}`
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
                                                        :for="`make_label_${activeLanguageId}`"
                                                        >Make label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`make_label_${activeLanguageId}`"
                                                    :id="`make_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'make_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'make_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `make_label.make_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `make_label.make_label_${activeLanguageId}`
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
                                                        :for="`make_placeholder_${activeLanguageId}`"
                                                        >Make placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`make_placeholder_${activeLanguageId}`"
                                                    :id="`make_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'make_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'make_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `make_placeholder.make_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `make_placeholder.make_placeholder_${activeLanguageId}`
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
                                                        :for="`model_label_${activeLanguageId}`"
                                                        >Model label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`model_label_${activeLanguageId}`"
                                                    :id="`model_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'model_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'model_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `model_label.model_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `model_label.model_label_${activeLanguageId}`
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
                                                        :for="`model_placeholder_${activeLanguageId}`"
                                                        >Model placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`model_placeholder_${activeLanguageId}`"
                                                    :id="`model_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'model_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'model_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `model_placeholder.model_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `model_placeholder.model_placeholder_${activeLanguageId}`
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
                                                        :for="`type_label_${activeLanguageId}`"
                                                        >Type label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`type_label_${activeLanguageId}`"
                                                    :id="`type_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'type_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'type_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `type_label.type_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `type_label.type_label_${activeLanguageId}`
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
                                                        :for="`year_label_${activeLanguageId}`"
                                                        >Year label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`year_label_${activeLanguageId}`"
                                                    :id="`year_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'year_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'year_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `year_label.year_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `year_label.year_label_${activeLanguageId}`
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
                                                        :for="`color_label_${activeLanguageId}`"
                                                        >Color label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`color_label_${activeLanguageId}`"
                                                    :id="`color_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'color_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'color_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `color_label.color_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `color_label.color_label_${activeLanguageId}`
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
                                                        :for="`liscense_label_${activeLanguageId}`"
                                                        >License label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`liscense_label_${activeLanguageId}`"
                                                    :id="`liscense_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'liscense_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'liscense_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `liscense_label.liscense_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `liscense_label.liscense_label_${activeLanguageId}`
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
                                                        :for="`electric_car_label_${activeLanguageId}`"
                                                        >Electric car label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`electric_car_label_${activeLanguageId}`"
                                                    :id="`electric_car_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'electric_car_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'electric_car_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `electric_car_label.electric_car_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `electric_car_label.electric_car_label_${activeLanguageId}`
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
                                                        :for="`hybrid_car_label_${activeLanguageId}`"
                                                        >Hybrid car label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`hybrid_car_label_${activeLanguageId}`"
                                                    :id="`hybrid_car_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'hybrid_car_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'hybrid_car_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `hybrid_car_label.hybrid_car_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `hybrid_car_label.hybrid_car_label_${activeLanguageId}`
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
                                                        :for="`car_photo_label_${activeLanguageId}`"
                                                        >Car photo label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`car_photo_label_${activeLanguageId}`"
                                                    :id="`car_photo_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'car_photo_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'car_photo_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `car_photo_label.car_photo_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `car_photo_label.car_photo_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- vehicle details section end -->

                                <!-- preferences section start -->
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
                                            Preferences section
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
                                                        :for="`preferences_label_${activeLanguageId}`"
                                                        >Preferences label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`preferences_label_${activeLanguageId}`"
                                                    :id="`preferences_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'preferences_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'preferences_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `preferences_label.preferences_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `preferences_label.preferences_label_${activeLanguageId}`
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
                                                        :for="`animals_label_${activeLanguageId}`"
                                                        >Animal label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_label_${activeLanguageId}`"
                                                    :id="`animals_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_label.animals_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_label.animals_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- preferences section end -->

                                <!-- booking section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
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
                                            Booking section
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
                                                        :for="`booking_option1_${activeLanguageId}`"
                                                        >Booking option1</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_option1_${activeLanguageId}`"
                                                    :id="`booking_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_option1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_option1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_option1.booking_option1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_option1.booking_option1_${activeLanguageId}`
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
                                                        :for="`booking_option2_${activeLanguageId}`"
                                                        >Booking option2</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`booking_option2_${activeLanguageId}`"
                                                    :id="`booking_option2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'booking_option2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_option2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `booking_option2.booking_option2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `booking_option2.booking_option2_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- booking section end -->

                                <!-- luggage section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
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
                                            Luggage section
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
                                                        :for="`luggage_checkbox_label1_${activeLanguageId}`"
                                                        >Luggage checkbox label1</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`luggage_checkbox_label1_${activeLanguageId}`"
                                                    :id="`luggage_checkbox_label1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'luggage_checkbox_label1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'luggage_checkbox_label1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `luggage_checkbox_label1.luggage_checkbox_label1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `luggage_checkbox_label1.luggage_checkbox_label1_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <!-- <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`luggage_checkbox_label2_${activeLanguageId}`"
                                                        >Luggage checkbox label2</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`luggage_checkbox_label2_${activeLanguageId}`"
                                                    :id="`luggage_checkbox_label2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'luggage_checkbox_label2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'luggage_checkbox_label2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `luggage_checkbox_label2.luggage_checkbox_label2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `luggage_checkbox_label2.luggage_checkbox_label2_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div> -->
                                    </div>
                                </div>
                                <!-- luggage section end -->

                                <!-- price section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
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
                                            Price and payment section
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
                                                        :for="`price_payment_heading_${activeLanguageId}`"
                                                        >Price and payment heading</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`price_payment_heading_${activeLanguageId}`"
                                                    :id="`price_payment_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'price_payment_heading'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'price_payment_heading'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `price_payment_heading.price_payment_heading_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `price_payment_heading.price_payment_heading_${activeLanguageId}`
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
                                                        :for="`price_per_seat_label_${activeLanguageId}`"
                                                        >Price per seat label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`price_per_seat_label_${activeLanguageId}`"
                                                    :id="`price_per_seat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'price_per_seat_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'price_per_seat_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `price_per_seat_label.price_per_seat_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `price_per_seat_label.price_per_seat_label_${activeLanguageId}`
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
                                                        :for="`payment_methods_label_${activeLanguageId}`"
                                                        >Payment method label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`payment_methods_label_${activeLanguageId}`"
                                                    :id="`payment_methods_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'payment_methods_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `payment_methods_label.payment_methods_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `payment_methods_label.payment_methods_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- price section end -->

                                <!-- anything to add section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
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
                                            Anything to add section
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
                                                        :for="`anything_to_add_label_${activeLanguageId}`"
                                                        >Anything to add label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`anything_to_add_label_${activeLanguageId}`"
                                                    :id="`anything_to_add_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'anything_to_add_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'anything_to_add_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `anything_to_add_label.anything_to_add_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `anything_to_add_label.anything_to_add_label_${activeLanguageId}`
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
                                                        :for="`anything_to_add_placeholder_${activeLanguageId}`"
                                                        >Anything to add placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`anything_to_add_placeholder_${activeLanguageId}`"
                                                    :id="`anything_to_add_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'anything_to_add_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'anything_to_add_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `anything_to_add_placeholder.anything_to_add_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `anything_to_add_placeholder.anything_to_add_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- anything to add section end -->

                                <!-- disclaimers section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[9] =
                                                !collapseStates[9]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Disclaimers section
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
                                        v-if="collapseStates[9]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`disclaimers_label_${activeLanguageId}`"
                                                        >Disclaimers label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`disclaimers_label_${activeLanguageId}`"
                                                    :id="`disclaimers_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'disclaimers_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'disclaimers_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `disclaimers_label.disclaimers_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `disclaimers_label.disclaimers_label_${activeLanguageId}`
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
                                                        :for="`disclaimers_description_${activeLanguageId}`"
                                                        >Disclaimers description</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'disclaimers_description'
                                                        )
                                                    "
                                                    :ref="`disclaimers_description_${language.id}`"
                                                    :id="`disclaimers_description_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `disclaimers_description`
                                                        ][
                                                            `disclaimers_description_${language?.id}`
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
                                                        `disclaimers_description.disclaimers_description_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `disclaimers_description.disclaimers_description_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- disclaimers section end -->

                                <!-- agree section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[10] =
                                                !collapseStates[10]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Agree terms section
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
                                        v-if="collapseStates[10]"
                                    >
                                        <div class="relative z-0 w-full group col-span-2">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`agree_terms_label_${activeLanguageId}`"
                                                        >Agree terms label</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'agree_terms_label'
                                                        )
                                                    "
                                                    :ref="`agree_terms_label_${language.id}`"
                                                    :id="`agree_terms_label_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `agree_terms_label`
                                                        ][
                                                            `agree_terms_label_${language?.id}`
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
                                                        `agree_terms_label.agree_terms_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `agree_terms_label.agree_terms_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- agree section end -->

                                <!-- submit button section start -->
                                <div
                                    class="border rounded w-full"
                                    :class="
                                        collapseStates[0] ? 'bg-gray-50' : ''
                                    "
                                >
                                    <div
                                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[11] =
                                                !collapseStates[11]
                                        "
                                    >
                                        <h3 class="text-white">
                                            Submit section
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
                                        v-if="collapseStates[11]"
                                    >
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`submit_button_label_${activeLanguageId}`"
                                                        >Submit button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`submit_button_label_${activeLanguageId}`"
                                                    :id="`submit_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'submit_button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'submit_button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `submit_button_label.submit_button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `submit_button_label.submit_button_label_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
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
                            this.handleInput("", language, "post_arrived_again_label");
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
                            this.handleInput("", language, "meeting_drop_off_description_label");
                            this.handleInput("", language, "meeting_drop_off_description_placeholder");
                            this.handleInput("", language, "seats_label");
                            this.handleInput("", language, "seats_middle_label");
                            this.handleInput("", language, "seats_back_label");
                            this.handleInput("", language, "vehicle_label");
                            this.handleInput("", language, "skip_label");
                            this.handleInput("", language, "add_vehicle_label");
                            this.handleInput("", language, "make_label");
                            this.handleInput("", language, "make_placeholder");
                            this.handleInput("", language, "model_label");
                            this.handleInput("", language, "model_placeholder");
                            this.handleInput("", language, "type_label");
                            this.handleInput("", language, "year_label");
                            this.handleInput("", language, "color_label");
                            this.handleInput("", language, "liscense_label");
                            this.handleInput("", language, "electric_car_label");
                            this.handleInput("", language, "hybrid_car_label");
                            this.handleInput("", language, "car_photo_label");
                            this.handleInput("", language, "preferences_label");
                            this.handleInput("", language, "smoking_label");
                            this.handleInput("", language, "animals_label");
                            this.handleInput("", language, "booking_label");
                            this.handleInput("", language, "booking_option1");
                            this.handleInput("", language, "booking_option2");
                            this.handleInput("", language, "luggage_label");
                            this.handleInput("", language, "luggage_checkbox_label1");
                            this.handleInput("", language, "price_payment_heading");
                            this.handleInput("", language, "price_per_seat_label");
                            this.handleInput("", language, "payment_methods_label");
                            this.handleInput("", language, "anything_to_add_label");
                            this.handleInput("", language, "anything_to_add_placeholder");
                            this.handleInput("", language, "disclaimers_label");
                            this.handleInput("", language, "disclaimers_description");
                            this.handleInput("", language, "agree_terms_label");
                            this.handleInput("", language, "submit_button_label");
                        });
                        this.fetchPostRidePageSetting();
                    }
                });
        },
        fetchPostRidePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-mobile-post-ride-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        console.log(res);
                        let mobile_post_ride_setting_detail =
                            res?.data?.data?.mobile_post_ride_setting_detail || [];
                        mobile_post_ride_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.post_arrived_again_label,
                                setting?.language,
                                "post_arrived_again_label"
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
                                setting?.car_photo_label,
                                setting?.language,
                                "car_photo_label"
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
                                setting?.animals_label,
                                setting?.language,
                                "animals_label"
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
                                setting?.booking_option2,
                                setting?.language,
                                "booking_option2"
                            );
                            this.handleInput(
                                setting?.luggage_label,
                                setting?.language,
                                "luggage_label"
                            );
                            this.handleInput(
                                setting?.luggage_checkbox_label1,
                                setting?.language,
                                "luggage_checkbox_label1"
                            );
                            // this.handleInput(
                            //     setting?.luggage_checkbox_label2,
                            //     setting?.language,
                            //     "luggage_checkbox_label2"
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
                                setting?.disclaimers_description,
                                setting?.language,
                                "disclaimers_description"
                            );
                            this.handleInput(
                                setting?.agree_terms_label,
                                setting?.language,
                                "agree_terms_label"
                            );
                            this.handleInput(
                                setting?.submit_button_label,
                                setting?.language,
                                "submit_button_label"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-mobile-post-ride-page-setting`,
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
                    `post_arrived_again_label.post_arrived_again_label_${language.id}`
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
                    `vehicle_label.vehicle_label_${language.id}`
                ) ||
                validationErros.has(
                    `skip_label.skip_label_${language.id}`
                ) ||
                validationErros.has(
                    `add_vehicle_label.add_vehicle_label_${language.id}`
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
                    `electric_car_label.electric_car_label_${language.id}`
                ) ||
                validationErros.has(
                    `hybrid_car_label.hybrid_car_label_${language.id}`
                ) ||
                validationErros.has(
                    `car_photo_label.car_photo_label_${language.id}`
                ) ||
                validationErros.has(
                    `preferences_label.preferences_label_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_label.smoking_label_${language.id}`
                ) ||
                validationErros.has(
                    `animals_label.animals_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_label.booking_label_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option1.booking_option1_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option2.booking_option2_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_label.luggage_label_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_checkbox_label1.luggage_checkbox_label1_${language.id}`
                ) ||
                
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
                    `anything_to_add_label.anything_to_add_label_${language.id}`
                ) ||
                validationErros.has(
                    `anything_to_add_placeholder.anything_to_add_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `disclaimers_label.disclaimers_label_${language.id}`
                ) ||
                validationErros.has(
                    `disclaimers_description.disclaimers_description_${language.id}`
                ) ||
                validationErros.has(
                    `agree_terms_label.agree_terms_label_${language.id}`
                ) ||
                validationErros.has(
                    `submit_button_label.submit_button_label_${language.id}`
                )
            );
        },
    },
};
</script>
