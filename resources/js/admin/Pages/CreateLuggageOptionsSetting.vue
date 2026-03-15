<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Ride luggage options settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <ExcelBulkImport
                            title="Luggage Options"
                            mode="all_languages"
                            download-endpoint="download-luggage-options-setting-template"
                            upload-endpoint="upload-luggage-options-setting-excel"
                            @success="fetchPostRidePageSetting"
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
                                                    :for="`luggage_option1_${activeLanguageId}`"
                                                    >No luggage</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option1_${activeLanguageId}`"
                                                :id="`luggage_option1_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option1'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option1'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option1.luggage_option1_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option1.luggage_option1_${activeLanguageId}`
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
                                                    :for="`luggage_option1_tooltip_${activeLanguageId}`"
                                                    >No luggage tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option1_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option1_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option1_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option1_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option1_tooltip.luggage_option1_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option1_tooltip.luggage_option1_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option1_icon_${activeLanguageId}`"
                                                        >No luggage icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option1_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option1_icon_${activeLanguageId}`"
                                                    :id="`luggage_option1_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option1_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option1_icon.luggage_option1_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option1_icon.luggage_option1_icon_${activeLanguageId}`
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
                                                        form['luggage_option1_icon'] &&
                                                        form['luggage_option1_icon'][`luggage_option1_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option1_icon'] &&
                                                        form['luggage_option1_icon'][`luggage_option1_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option1_icon'][`luggage_option1_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option2_${activeLanguageId}`"
                                                    >Small</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option2_${activeLanguageId}`"
                                                :id="`luggage_option2_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option2'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option2'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option2.luggage_option2_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option2.luggage_option2_${activeLanguageId}`
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
                                                    :for="`luggage_option2_tooltip_${activeLanguageId}`"
                                                    >Small tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option2_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option2_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option2_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option2_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option2_tooltip.luggage_option2_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option2_tooltip.luggage_option2_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option2_icon_${activeLanguageId}`"
                                                        >Small icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option2_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option2_icon_${activeLanguageId}`"
                                                    :id="`luggage_option2_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option2_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option2_icon.luggage_option2_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option2_icon.luggage_option2_icon_${activeLanguageId}`
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
                                                        form['luggage_option2_icon'] &&
                                                        form['luggage_option2_icon'][`luggage_option2_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option2_icon'] &&
                                                        form['luggage_option2_icon'][`luggage_option2_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option2_icon'][`luggage_option2_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option3_${activeLanguageId}`"
                                                    >Medium</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option3_${activeLanguageId}`"
                                                :id="`luggage_option3_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option3'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option3'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option3.luggage_option3_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option3.luggage_option3_${activeLanguageId}`
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
                                                    :for="`luggage_option3_tooltip_${activeLanguageId}`"
                                                    >Medium tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option3_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option3_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option3_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option3_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option3_tooltip.luggage_option3_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option3_tooltip.luggage_option3_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option3_icon_${activeLanguageId}`"
                                                        >Medium icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option3_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option3_icon_${activeLanguageId}`"
                                                    :id="`luggage_option3_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option3_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option3_icon.luggage_option3_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option3_icon.luggage_option3_icon_${activeLanguageId}`
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
                                                        form['luggage_option3_icon'] &&
                                                        form['luggage_option3_icon'][`luggage_option3_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option3_icon'] &&
                                                        form['luggage_option3_icon'][`luggage_option3_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option3_icon'][`luggage_option3_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option4_${activeLanguageId}`"
                                                    >Large</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option4_${activeLanguageId}`"
                                                :id="`luggage_option4_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option4'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option4'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option4.luggage_option4_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option4.luggage_option4_${activeLanguageId}`
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
                                                    :for="`luggage_option4_tooltip_${activeLanguageId}`"
                                                    >Large tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option4_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option4_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option4_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option4_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option4_tooltip.luggage_option4_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option4_tooltip.luggage_option4_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option4_icon_${activeLanguageId}`"
                                                        >Large icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option4_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option4_icon_${activeLanguageId}`"
                                                    :id="`luggage_option4_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option4_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option4_icon.luggage_option4_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option4_icon.luggage_option4_icon_${activeLanguageId}`
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
                                                        form['luggage_option4_icon'] &&
                                                        form['luggage_option4_icon'][`luggage_option4_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option4_icon'] &&
                                                        form['luggage_option4_icon'][`luggage_option4_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option4_icon'][`luggage_option4_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`luggage_option5_${activeLanguageId}`"
                                                    >Xl and multiple</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option5_${activeLanguageId}`"
                                                :id="`luggage_option5_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option5'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option5'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option5.luggage_option5_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option5.luggage_option5_${activeLanguageId}`
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
                                                    :for="`luggage_option5_tooltip_${activeLanguageId}`"
                                                    >Xl and multiple tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option5_tooltip_${activeLanguageId}`"
                                                :id="`luggage_option5_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option5_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option5_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option5_tooltip.luggage_option5_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option5_tooltip.luggage_option5_tooltip_${activeLanguageId}`
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
                                                    :for="`luggage_option5_label_${activeLanguageId}`"
                                                    >Text below Xl and multiple</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`luggage_option5_label_${activeLanguageId}`"
                                                :id="`luggage_option5_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'luggage_option5_label'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'luggage_option5_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `luggage_option5_label.luggage_option5_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `luggage_option5_label.luggage_option5_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`luggage_option5_icon_${activeLanguageId}`"
                                                        >Xl and multiple icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`luggage_option5_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`luggage_option5_icon_${activeLanguageId}`"
                                                    :id="`luggage_option5_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'luggage_option5_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `luggage_option5_icon.luggage_option5_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `luggage_option5_icon.luggage_option5_icon_${activeLanguageId}`
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
                                                        form['luggage_option5_icon'] &&
                                                        form['luggage_option5_icon'][`luggage_option5_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['luggage_option5_icon'] &&
                                                        form['luggage_option5_icon'][`luggage_option5_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['luggage_option5_icon'][`luggage_option5_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                bullist numlist outdent indent | removeformat | table | image | code | help",
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
                            this.handleInput("", language, "luggage_option1");
                            this.handleInput("", language, "luggage_option1_tooltip");
                            this.handleInput("", language, "luggage_option1_icon");
                            this.handleInput("", language, "luggage_option2");
                            this.handleInput("", language, "luggage_option2_tooltip");
                            this.handleInput("", language, "luggage_option2_icon");
                            this.handleInput("", language, "luggage_option3");
                            this.handleInput("", language, "luggage_option3_tooltip");
                            this.handleInput("", language, "luggage_option3_icon");
                            this.handleInput("", language, "luggage_option4");
                            this.handleInput("", language, "luggage_option4_tooltip");
                            this.handleInput("", language, "luggage_option4_icon");
                            this.handleInput("", language, "luggage_option5");
                            this.handleInput("", language, "luggage_option5_tooltip");
                            this.handleInput("", language, "luggage_option5_label");
                            this.handleInput("", language, "luggage_option5_icon");
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
                            this.handleInput(
                                setting?.luggage_option1,
                                setting?.language,
                                "luggage_option1"
                            );
                            this.handleInput(
                                setting?.luggage_option1_tooltip,
                                setting?.language,
                                "luggage_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option1_icon,
                                setting?.language,
                                "luggage_option1_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option2,
                                setting?.language,
                                "luggage_option2"
                            );
                            this.handleInput(
                                setting?.luggage_option2_tooltip,
                                setting?.language,
                                "luggage_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option2_icon,
                                setting?.language,
                                "luggage_option2_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option3,
                                setting?.language,
                                "luggage_option3"
                            );
                            this.handleInput(
                                setting?.luggage_option3_tooltip,
                                setting?.language,
                                "luggage_option3_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option3_icon,
                                setting?.language,
                                "luggage_option3_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option4,
                                setting?.language,
                                "luggage_option4"
                            );
                            this.handleInput(
                                setting?.luggage_option4_tooltip,
                                setting?.language,
                                "luggage_option4_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option4_icon,
                                setting?.language,
                                "luggage_option4_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option5,
                                setting?.language,
                                "luggage_option5"
                            );
                            this.handleInput(
                                setting?.luggage_option5_tooltip,
                                setting?.language,
                                "luggage_option5_tooltip"
                            );
                            this.handleInput(
                                setting?.luggage_option5_icon,
                                setting?.language,
                                "luggage_option5_icon"
                            );
                            this.handleInput(
                                setting?.luggage_option5_label,
                                setting?.language,
                                "luggage_option5_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-luggage-options-setting`,
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
                    `luggage_option1.luggage_option1_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option1_tooltip.luggage_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option1_icon.luggage_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2.luggage_option2_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2_tooltip.luggage_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option2_icon.luggage_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3.luggage_option3_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3_tooltip.luggage_option3_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option3_icon.luggage_option3_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4.luggage_option4_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4_tooltip.luggage_option4_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option4_icon.luggage_option4_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5.luggage_option5_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5_tooltip.luggage_option5_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5_icon.luggage_option5_icon_${language.id}`
                ) ||
                validationErros.has(
                    `luggage_option5_label.luggage_option5_label_${language.id}`
                )
            );
        },
        handleImage(e, language, key) {
            console.log(e.target.files[0], key, language);
            var file = new FormData();
            file.append("file", e.target.files[0]);
            axios.post("/api/admin/media/image_again_upload", file).then((res) => {
                this.handleInput(res?.data, language, key);
            });
        },
    },
};
</script>
