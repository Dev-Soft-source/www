<template>
    <AppLayout>
        <section class="post-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Ride features settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <form
                        class="px-4 md:px-6 lg:px-8"
                        @submit.prevent="updatePageSetting()"
                    >
                        <!-- Excel bulk import (all languages template) -->
                        <ExcelBulkImport
                            title="Ride features settings"
                            mode="all_languages"
                            download-endpoint="download-features-setting-template"
                            upload-endpoint="upload-features-setting-excel"
                            @success="onFeaturesExcelSuccess"
                        />
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
                                                    :for="`features_option1_${activeLanguageId}`"
                                                    >ProximaRide</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option1_${activeLanguageId}`"
                                                :id="`features_option1_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option1'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option1'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option1.features_option1_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option1.features_option1_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option1_tooltip_${activeLanguageId}`"
                                                    >ProximaRide tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option1_tooltip_${activeLanguageId}`"
                                                :id="`features_option1_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option1_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option1_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option1_tooltip.features_option1_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option1_tooltip.features_option1_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option1_icon_${activeLanguageId}`"
                                                        >ProximaRide icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option1_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option1_icon_${activeLanguageId}`"
                                                    :id="`features_option1_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option1_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option1_icon.features_option1_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option1_icon.features_option1_icon_${activeLanguageId}`
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
                                                        form['features_option1_icon'] &&
                                                        form['features_option1_icon'][`features_option1_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option1_icon'] &&
                                                        form['features_option1_icon'][`features_option1_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option1_icon'][`features_option1_icon_${activeLanguageId}`]
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
                                                    :for="`features_option2_${activeLanguageId}`"
                                                    >Extra+ Rides</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option2_${activeLanguageId}`"
                                                :id="`features_option2_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option2'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option2'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option2.features_option2_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option2.features_option2_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option2_tooltip_${activeLanguageId}`"
                                                    >Extra+ Rides tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option2_tooltip_${activeLanguageId}`"
                                                :id="`features_option2_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option2_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option2_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option2_tooltip.features_option2_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option2_tooltip.features_option2_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option2_icon_${activeLanguageId}`"
                                                        >Extra+ Rides icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option2_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option2_icon_${activeLanguageId}`"
                                                    :id="`features_option2_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option2_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option2_icon.features_option2_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option2_icon.features_option2_icon_${activeLanguageId}`
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
                                                        form['features_option2_icon'] &&
                                                        form['features_option2_icon'][`features_option2_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option2_icon'] &&
                                                        form['features_option2_icon'][`features_option2_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option2_icon'][`features_option2_icon_${activeLanguageId}`]
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
                                                    :for="`features_option3_${activeLanguageId}`"
                                                    >WiFi</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option3_${activeLanguageId}`"
                                                :id="`features_option3_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option3'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option3'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option3.features_option3_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option3.features_option3_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option3_tooltip_${activeLanguageId}`"
                                                    >WiFi tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option3_tooltip_${activeLanguageId}`"
                                                :id="`features_option3_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option3_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option3_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option3_tooltip.features_option3_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option3_tooltip.features_option3_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option3_icon_${activeLanguageId}`"
                                                        >WiFi icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option3_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option3_icon_${activeLanguageId}`"
                                                    :id="`features_option3_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option3_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option3_icon.features_option3_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option3_icon.features_option3_icon_${activeLanguageId}`
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
                                                        form['features_option3_icon'] &&
                                                        form['features_option3_icon'][`features_option3_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option3_icon'] &&
                                                        form['features_option3_icon'][`features_option3_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option3_icon'][`features_option3_icon_${activeLanguageId}`]
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
                                                    :for="`driver_features_option4_${activeLanguageId}`"
                                                    >I take infants but I do not provide car baby seats: the passenger must provide then</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option4_${activeLanguageId}`"
                                                :id="`driver_features_option4_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option4'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option4'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option4.driver_features_option4_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option4.driver_features_option4_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option4_tooltip_${activeLanguageId}`"
                                                    >I take infants but I do not provide car baby seats: the passenger must provide then tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option4_tooltip_${activeLanguageId}`"
                                                :id="`driver_features_option4_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option4_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option4_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option4_tooltip.driver_features_option4_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option4_tooltip.driver_features_option4_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_features_option4_icon_${activeLanguageId}`"
                                                        >I take infants but I do not provide car baby seats: the passenger must provide then icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`driver_features_option4_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`driver_features_option4_icon_${activeLanguageId}`"
                                                    :id="`driver_features_option4_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'driver_features_option4_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `driver_features_option4_icon.driver_features_option4_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `driver_features_option4_icon.driver_features_option4_icon_${activeLanguageId}`
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
                                                        form['driver_features_option4_icon'] &&
                                                        form['driver_features_option4_icon'][`driver_features_option4_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['driver_features_option4_icon'] &&
                                                        form['driver_features_option4_icon'][`driver_features_option4_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['driver_features_option4_icon'][`driver_features_option4_icon_${activeLanguageId}`]
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
                                                    :for="`passenger_features_option4_${activeLanguageId}`"
                                                    >I have infants and I provide car baby seats (inform the driver before booking)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_features_option4_${activeLanguageId}`"
                                                :id="`passenger_features_option4_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'passenger_features_option4'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_features_option4'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_features_option4.passenger_features_option4_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_features_option4.passenger_features_option4_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group"></div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option5_${activeLanguageId}`"
                                                    >I take infants and I provide car booster seats</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option5_${activeLanguageId}`"
                                                :id="`driver_features_option5_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option5'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option5'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option5.driver_features_option5_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option5.driver_features_option5_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option5_tooltip_${activeLanguageId}`"
                                                    >I take infants and I provide car booster seats tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option5_tooltip_${activeLanguageId}`"
                                                :id="`driver_features_option5_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option5_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option5_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option5_tooltip.driver_features_option5_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option5_tooltip.driver_features_option5_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_features_option5_icon_${activeLanguageId}`"
                                                        >I take infants and I provide car booster seats icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`driver_features_option5_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`driver_features_option5_icon_${activeLanguageId}`"
                                                    :id="`driver_features_option5_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'driver_features_option5_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `driver_features_option5_icon.driver_features_option5_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `driver_features_option5_icon.driver_features_option5_icon_${activeLanguageId}`
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
                                                        form['driver_features_option5_icon'] &&
                                                        form['driver_features_option5_icon'][`driver_features_option5_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['driver_features_option5_icon'] &&
                                                        form['driver_features_option5_icon'][`driver_features_option5_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['driver_features_option5_icon'][`driver_features_option5_icon_${activeLanguageId}`]
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
                                                    :for="`passenger_features_option5_${activeLanguageId}`"
                                                    >I have infants but I do not provide car baby seats: the driver must provide them (ask the driver before booking)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_features_option5_${activeLanguageId}`"
                                                :id="`passenger_features_option5_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'passenger_features_option5'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_features_option5'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_features_option5.passenger_features_option5_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_features_option5.passenger_features_option5_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group"></div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option6_${activeLanguageId}`"
                                                    >I take children but I do not provide car booster seats: must provide them</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option6_${activeLanguageId}`"
                                                :id="`driver_features_option6_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option6'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option6'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option6.driver_features_option6_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option6.driver_features_option6_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option6_tooltip_${activeLanguageId}`"
                                                    >I take children but I do not provide car booster seats: must provide them tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option6_tooltip_${activeLanguageId}`"
                                                :id="`driver_features_option6_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option6_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option6_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option6_tooltip.driver_features_option6_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option6_tooltip.driver_features_option6_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_features_option6_icon_${activeLanguageId}`"
                                                        >I take children but I do not provide car booster seats: must provide them icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`driver_features_option6_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`driver_features_option6_icon_${activeLanguageId}`"
                                                    :id="`driver_features_option6_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'driver_features_option6_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `driver_features_option6_icon.driver_features_option6_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `driver_features_option6_icon.driver_features_option6_icon_${activeLanguageId}`
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
                                                        form['driver_features_option6_icon'] &&
                                                        form['driver_features_option6_icon'][`driver_features_option6_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['driver_features_option6_icon'] &&
                                                        form['driver_features_option6_icon'][`driver_features_option6_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['driver_features_option6_icon'][`driver_features_option6_icon_${activeLanguageId}`]
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
                                                    :for="`passenger_features_option6_${activeLanguageId}`"
                                                    >I have children and I provide car baby seats (inform the driver before booking)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_features_option6_${activeLanguageId}`"
                                                :id="`passenger_features_option6_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'passenger_features_option6'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_features_option6'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_features_option6.passenger_features_option6_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_features_option6.passenger_features_option6_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group"></div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option7_${activeLanguageId}`"
                                                    >I take children, and I provide car booster seats</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option7_${activeLanguageId}`"
                                                :id="`driver_features_option7_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option7'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option7'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option7.driver_features_option7_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option7.driver_features_option7_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`driver_features_option7_tooltip_${activeLanguageId}`"
                                                    >I take children, and I provide car booster seats tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`driver_features_option7_tooltip_${activeLanguageId}`"
                                                :id="`driver_features_option7_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'driver_features_option7_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'driver_features_option7_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `driver_features_option7_tooltip.driver_features_option7_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `driver_features_option7_tooltip.driver_features_option7_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`driver_features_option7_icon_${activeLanguageId}`"
                                                        >I take children, and I provide car booster seats icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`driver_features_option7_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`driver_features_option7_icon_${activeLanguageId}`"
                                                    :id="`driver_features_option7_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'driver_features_option7_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `driver_features_option7_icon.driver_features_option7_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `driver_features_option7_icon.driver_features_option7_icon_${activeLanguageId}`
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
                                                        form['driver_features_option7_icon'] &&
                                                        form['driver_features_option7_icon'][`driver_features_option7_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['driver_features_option7_icon'] &&
                                                        form['driver_features_option7_icon'][`driver_features_option7_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['driver_features_option7_icon'][`driver_features_option7_icon_${activeLanguageId}`]
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
                                                    :for="`passenger_features_option7_${activeLanguageId}`"
                                                    >I have children but I do not provide car baby seats: the driver must provide them (ask the driver before booking)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_features_option7_${activeLanguageId}`"
                                                :id="`passenger_features_option7_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'passenger_features_option7'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_features_option7'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_features_option7.passenger_features_option7_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_features_option7.passenger_features_option7_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group"></div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option8_${activeLanguageId}`"
                                                    >Heating</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option8_${activeLanguageId}`"
                                                :id="`features_option8_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option8'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option8'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option8.features_option8_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option8.features_option8_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option8_tooltip_${activeLanguageId}`"
                                                    >Heating tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option8_tooltip_${activeLanguageId}`"
                                                :id="`features_option8_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option8_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option8_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option8_tooltip.features_option8_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option8_tooltip.features_option8_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option8_icon_${activeLanguageId}`"
                                                        >Heating icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option8_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option8_icon_${activeLanguageId}`"
                                                    :id="`features_option8_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option8_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option8_icon.features_option8_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option8_icon.features_option8_icon_${activeLanguageId}`
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
                                                        form['features_option8_icon'] &&
                                                        form['features_option8_icon'][`features_option8_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option8_icon'] &&
                                                        form['features_option8_icon'][`features_option8_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option8_icon'][`features_option8_icon_${activeLanguageId}`]
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
                                                    :for="`features_option9_${activeLanguageId}`"
                                                    >Air conditioning</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option9_${activeLanguageId}`"
                                                :id="`features_option9_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option9'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option9'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option9.features_option9_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option9.features_option9_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option9_tooltip_${activeLanguageId}`"
                                                    >Air conditioning tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option9_tooltip_${activeLanguageId}`"
                                                :id="`features_option9_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option9_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option9_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option9_tooltip.features_option9_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option9_tooltip.features_option9_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option9_icon_${activeLanguageId}`"
                                                        >Air conditioning icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option9_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option9_icon_${activeLanguageId}`"
                                                    :id="`features_option9_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option9_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option9_icon.features_option9_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option9_icon.features_option9_icon_${activeLanguageId}`
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
                                                        form['features_option9_icon'] &&
                                                        form['features_option9_icon'][`features_option9_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option9_icon'] &&
                                                        form['features_option9_icon'][`features_option9_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option9_icon'][`features_option9_icon_${activeLanguageId}`]
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
                                                    :for="`features_option10_${activeLanguageId}`"
                                                    >Bike rack</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option10_${activeLanguageId}`"
                                                :id="`features_option10_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option10'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option10'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option10.features_option10_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option10.features_option10_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option10_tooltip_${activeLanguageId}`"
                                                    >Bike rack tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option10_tooltip_${activeLanguageId}`"
                                                :id="`features_option10_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option10_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option10_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option10_tooltip.features_option10_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option10_tooltip.features_option10_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option10_icon_${activeLanguageId}`"
                                                        >Bike rack icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option10_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option10_icon_${activeLanguageId}`"
                                                    :id="`features_option10_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option10_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option10_icon.features_option10_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option10_icon.features_option10_icon_${activeLanguageId}`
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
                                                        form['features_option10_icon'] &&
                                                        form['features_option10_icon'][`features_option10_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option10_icon'] &&
                                                        form['features_option10_icon'][`features_option10_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option10_icon'][`features_option10_icon_${activeLanguageId}`]
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
                                                    :for="`features_option11_${activeLanguageId}`"
                                                    >Ski rack</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option11_${activeLanguageId}`"
                                                :id="`features_option11_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option11'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option11'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option11.features_option11_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option11.features_option11_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div
                                                class="flex justify-between"
                                            >
                                                <label
                                                    :for="`features_option11_tooltip_${activeLanguageId}`"
                                                    >Ski rack tooltip</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`features_option11_tooltip_${activeLanguageId}`"
                                                :id="`features_option11_tooltip_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="
                                                    getCurrentValue(
                                                        'features_option11_tooltip'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'features_option11_tooltip'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `features_option11_tooltip.features_option11_tooltip_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `features_option11_tooltip.features_option11_tooltip_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`features_option11_icon_${activeLanguageId}`"
                                                        >Ski rack icon</label
                                                    >
                                                </div>
                                                <input
                                                    :key="`features_option11_icon_${activeLanguageId}`"
                                                    type="file"
                                                    :name="`features_option11_icon_${activeLanguageId}`"
                                                    :id="`features_option11_icon_${activeLanguageId}`"
                                                    class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                    placeholder=" "
                                                    @input="
                                                        handleImage(
                                                            $event,
                                                            language,
                                                            'features_option11_icon',
                                                        )
                                                    "
                                                />
                                                <p
                                                    class="mt-2 text-sm text-red-400"
                                                    v-if="
                                                        validationErros.has(
                                                            `features_option11_icon.features_option11_icon_${activeLanguageId}`
                                                        )
                                                    "
                                                    v-text="
                                                        validationErros.get(
                                                            `features_option11_icon.features_option11_icon_${activeLanguageId}`
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
                                                        form['features_option11_icon'] &&
                                                        form['features_option11_icon'][`features_option11_icon_${activeLanguageId}`]
                                                    "
                                                    :src="
                                                        form['features_option11_icon'] &&
                                                        form['features_option11_icon'][`features_option11_icon_${activeLanguageId}`]
                                                            ? '/home_page_icons/' + form['features_option11_icon'][`features_option11_icon_${activeLanguageId}`]
                                                            : ''
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                >
                                </div>

                                <!-- tires section start -->
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
                                            Winter tires feature
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
                                                        :for="`features_option12_${activeLanguageId}`"
                                                        >Winter tires</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option12_${activeLanguageId}`"
                                                    :id="`features_option12_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option12'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option12'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option12.features_option12_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option12.features_option12_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`features_option12_tooltip_${activeLanguageId}`"
                                                        >Winter tires tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option12_tooltip_${activeLanguageId}`"
                                                    :id="`features_option12_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option12_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option12_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option12_tooltip.features_option12_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option12_tooltip.features_option12_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`features_option12_icon_${activeLanguageId}`"
                                                            >Winter tires icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`features_option12_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`features_option12_icon_${activeLanguageId}`"
                                                        :id="`features_option12_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'features_option12_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `features_option12_icon.features_option12_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `features_option12_icon.features_option12_icon_${activeLanguageId}`
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
                                                            form['features_option12_icon'] &&
                                                            form['features_option12_icon'][`features_option12_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['features_option12_icon'] &&
                                                            form['features_option12_icon'][`features_option12_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['features_option12_icon'][`features_option12_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- tires section end -->

                                <!-- passenger section start -->
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
                                            Passenger rating features section
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
                                                        :for="`features_option13_${activeLanguageId}`"
                                                        >I want only passengers with 5-star reviews</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option13_${activeLanguageId}`"
                                                    :id="`features_option13_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option13'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option13'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option13.features_option13_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option13.features_option13_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`features_option13_tooltip_${activeLanguageId}`"
                                                        >I want only passengers with 5-star reviews tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option13_tooltip_${activeLanguageId}`"
                                                    :id="`features_option13_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option13_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option13_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option13_tooltip.features_option13_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option13_tooltip.features_option13_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`features_option13_icon_${activeLanguageId}`"
                                                            >I want only passengers with 5-star reviews icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`features_option13_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`features_option13_icon_${activeLanguageId}`"
                                                        :id="`features_option13_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'features_option13_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `features_option13_icon.features_option13_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `features_option13_icon.features_option13_icon_${activeLanguageId}`
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
                                                            form['features_option13_icon'] &&
                                                            form['features_option13_icon'][`features_option13_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['features_option13_icon'] &&
                                                            form['features_option13_icon'][`features_option13_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['features_option13_icon'][`features_option13_icon_${activeLanguageId}`]
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
                                                        :for="`features_option14_${activeLanguageId}`"
                                                        >I want only passengers with 4-star reviews and above</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option14_${activeLanguageId}`"
                                                    :id="`features_option14_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option14'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option14'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option14.features_option14_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option14.features_option14_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`features_option14_tooltip_${activeLanguageId}`"
                                                        >I want only passengers with 4-star reviews and above tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option14_tooltip_${activeLanguageId}`"
                                                    :id="`features_option14_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option14_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option14_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option14_tooltip.features_option14_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option14_tooltip.features_option14_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`features_option14_icon_${activeLanguageId}`"
                                                            >I want only passengers with 4-star reviews and above icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`features_option14_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`features_option14_icon_${activeLanguageId}`"
                                                        :id="`features_option14_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'features_option14_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `features_option14_icon.features_option14_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `features_option14_icon.features_option14_icon_${activeLanguageId}`
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
                                                            form['features_option14_icon'] &&
                                                            form['features_option14_icon'][`features_option14_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['features_option14_icon'] &&
                                                            form['features_option14_icon'][`features_option14_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['features_option14_icon'][`features_option14_icon_${activeLanguageId}`]
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
                                                        :for="`features_option15_${activeLanguageId}`"
                                                        >I want only passengers with-3 star reviews above</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option15_${activeLanguageId}`"
                                                    :id="`features_option15_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option15'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option15'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option15.features_option15_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option15.features_option15_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`features_option15_tooltip_${activeLanguageId}`"
                                                        >I want only passengers with-3 star reviews above tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option15_tooltip_${activeLanguageId}`"
                                                    :id="`features_option15_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option15_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option15_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option15_tooltip.features_option15_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option15_tooltip.features_option15_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`features_option15_icon_${activeLanguageId}`"
                                                            >I want only passengers with-3 star reviews above icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`features_option15_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`features_option15_icon_${activeLanguageId}`"
                                                        :id="`features_option15_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'features_option15_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `features_option15_icon.features_option15_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `features_option15_icon.features_option15_icon_${activeLanguageId}`
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
                                                            form['features_option15_icon'] &&
                                                            form['features_option15_icon'][`features_option15_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['features_option15_icon'] &&
                                                            form['features_option15_icon'][`features_option15_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['features_option15_icon'][`features_option15_icon_${activeLanguageId}`]
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
                                                        :for="`features_option16_${activeLanguageId}`"
                                                        >I only want passengers with reviews; i.e. no new users</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option16_${activeLanguageId}`"
                                                    :id="`features_option16_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option16'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option16'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option16.features_option16_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option16.features_option16_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`features_option16_tooltip_${activeLanguageId}`"
                                                        >I only want passengers with reviews; i.e. no new users tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`features_option16_tooltip_${activeLanguageId}`"
                                                    :id="`features_option16_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'features_option16_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'features_option16_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `features_option16_tooltip.features_option16_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `features_option16_tooltip.features_option16_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`features_option16_icon_${activeLanguageId}`"
                                                            >I only want passengers with reviews; i.e. no new users icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`features_option16_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`features_option16_icon_${activeLanguageId}`"
                                                        :id="`features_option16_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'features_option16_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `features_option16_icon.features_option16_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `features_option16_icon.features_option16_icon_${activeLanguageId}`
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
                                                            form['features_option16_icon'] &&
                                                            form['features_option16_icon'][`features_option16_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['features_option16_icon'] &&
                                                            form['features_option16_icon'][`features_option16_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['features_option16_icon'][`features_option16_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- passenger section end -->
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
import ExcelBulkImport from "../components/ExcelBulkImport.vue";
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
                            this.handleInput("", language, "features_option1");
                            this.handleInput("", language, "features_option1_tooltip");
                            this.handleInput("", language, "features_option1_icon");
                            this.handleInput("", language, "features_option2");
                            this.handleInput("", language, "features_option2_tooltip");
                            this.handleInput("", language, "features_option2_icon");
                            this.handleInput("", language, "features_option3");
                            this.handleInput("", language, "features_option3_tooltip");
                            this.handleInput("", language, "features_option3_icon");
                            this.handleInput("", language, "driver_features_option4");
                            this.handleInput("", language, "driver_features_option4_tooltip");
                            this.handleInput("", language, "driver_features_option4_icon");
                            this.handleInput("", language, "driver_features_option5");
                            this.handleInput("", language, "driver_features_option5_tooltip");
                            this.handleInput("", language, "driver_features_option5_icon");
                            this.handleInput("", language, "driver_features_option6");
                            this.handleInput("", language, "driver_features_option6_tooltip");
                            this.handleInput("", language, "driver_features_option6_icon");
                            this.handleInput("", language, "driver_features_option7");
                            this.handleInput("", language, "driver_features_option7_tooltip");
                            this.handleInput("", language, "driver_features_option7_icon");
                            this.handleInput("", language, "features_option8");
                            this.handleInput("", language, "features_option8_tooltip");
                            this.handleInput("", language, "features_option8_icon");
                            this.handleInput("", language, "features_option9");
                            this.handleInput("", language, "features_option9_tooltip");
                            this.handleInput("", language, "features_option9_icon");
                            this.handleInput("", language, "features_option10");
                            this.handleInput("", language, "features_option10_tooltip");
                            this.handleInput("", language, "features_option10_icon");
                            this.handleInput("", language, "features_option11");
                            this.handleInput("", language, "features_option11_tooltip");
                            this.handleInput("", language, "features_option11_icon");
                            this.handleInput("", language, "features_option12");
                            this.handleInput("", language, "features_option12_tooltip");
                            this.handleInput("", language, "features_option12_icon");
                            this.handleInput("", language, "features_option13");
                            this.handleInput("", language, "features_option13_tooltip");
                            this.handleInput("", language, "features_option13_icon");
                            this.handleInput("", language, "features_option14");
                            this.handleInput("", language, "features_option14_tooltip");
                            this.handleInput("", language, "features_option14_icon");
                            this.handleInput("", language, "features_option15");
                            this.handleInput("", language, "features_option15_tooltip");
                            this.handleInput("", language, "features_option15_icon");
                            this.handleInput("", language, "features_option16");
                            this.handleInput("", language, "features_option16_tooltip");
                            this.handleInput("", language, "features_option16_icon");
                        });
                        this.fetchPostRidePageSetting();
                        languages.map((language) => {
                            this.handleInput("", language, "passenger_features_option4");
                            this.handleInput("", language, "passenger_features_option5");
                            this.handleInput("", language, "passenger_features_option6");
                            this.handleInput("", language, "passenger_features_option7");
                        });
                        this.fetchFindRidePageSetting();
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
                                setting?.features_option1,
                                setting?.language,
                                "features_option1"
                            );
                            this.handleInput(
                                setting?.features_option1_tooltip,
                                setting?.language,
                                "features_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option1_icon,
                                setting?.language,
                                "features_option1_icon"
                            );
                            this.handleInput(
                                setting?.features_option2,
                                setting?.language,
                                "features_option2"
                            );
                            this.handleInput(
                                setting?.features_option2_tooltip,
                                setting?.language,
                                "features_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option2_icon,
                                setting?.language,
                                "features_option2_icon"
                            );
                            this.handleInput(
                                setting?.features_option3,
                                setting?.language,
                                "features_option3"
                            );
                            this.handleInput(
                                setting?.features_option3_tooltip,
                                setting?.language,
                                "features_option3_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option3_icon,
                                setting?.language,
                                "features_option3_icon"
                            );
                            this.handleInput(
                                setting?.features_option4,
                                setting?.language,
                                "driver_features_option4"
                            );
                            this.handleInput(
                                setting?.features_option4_tooltip,
                                setting?.language,
                                "driver_features_option4_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option4_icon,
                                setting?.language,
                                "driver_features_option4_icon"
                            );
                            this.handleInput(
                                setting?.features_option5,
                                setting?.language,
                                "driver_features_option5"
                            );
                            this.handleInput(
                                setting?.features_option5_tooltip,
                                setting?.language,
                                "driver_features_option5_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option5_icon,
                                setting?.language,
                                "driver_features_option5_icon"
                            );
                            this.handleInput(
                                setting?.features_option6,
                                setting?.language,
                                "driver_features_option6"
                            );
                            this.handleInput(
                                setting?.features_option6_tooltip,
                                setting?.language,
                                "driver_features_option6_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option6_icon,
                                setting?.language,
                                "driver_features_option6_icon"
                            );
                            this.handleInput(
                                setting?.features_option7,
                                setting?.language,
                                "driver_features_option7"
                            );
                            this.handleInput(
                                setting?.features_option7_tooltip,
                                setting?.language,
                                "driver_features_option7_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option7_icon,
                                setting?.language,
                                "driver_features_option7_icon"
                            );
                            this.handleInput(
                                setting?.features_option8,
                                setting?.language,
                                "features_option8"
                            );
                            this.handleInput(
                                setting?.features_option8_tooltip,
                                setting?.language,
                                "features_option8_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option8_icon,
                                setting?.language,
                                "features_option8_icon"
                            );
                            this.handleInput(
                                setting?.features_option9,
                                setting?.language,
                                "features_option9"
                            );
                            this.handleInput(
                                setting?.features_option9_tooltip,
                                setting?.language,
                                "features_option9_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option9_icon,
                                setting?.language,
                                "features_option9_icon"
                            );
                            this.handleInput(
                                setting?.features_option10,
                                setting?.language,
                                "features_option10"
                            );
                            this.handleInput(
                                setting?.features_option10_tooltip,
                                setting?.language,
                                "features_option10_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option10_icon,
                                setting?.language,
                                "features_option10_icon"
                            );
                            this.handleInput(
                                setting?.features_option11,
                                setting?.language,
                                "features_option11"
                            );
                            this.handleInput(
                                setting?.features_option11_tooltip,
                                setting?.language,
                                "features_option11_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option11_icon,
                                setting?.language,
                                "features_option11_icon"
                            );
                            this.handleInput(
                                setting?.features_option12,
                                setting?.language,
                                "features_option12"
                            );
                            this.handleInput(
                                setting?.features_option12_tooltip,
                                setting?.language,
                                "features_option12_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option12_icon,
                                setting?.language,
                                "features_option12_icon"
                            );
                            this.handleInput(
                                setting?.features_option13,
                                setting?.language,
                                "features_option13"
                            );
                            this.handleInput(
                                setting?.features_option13_tooltip,
                                setting?.language,
                                "features_option13_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option13_icon,
                                setting?.language,
                                "features_option13_icon"
                            );
                            this.handleInput(
                                setting?.features_option14,
                                setting?.language,
                                "features_option14"
                            );
                            this.handleInput(
                                setting?.features_option14_tooltip,
                                setting?.language,
                                "features_option14_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option14_icon,
                                setting?.language,
                                "features_option14_icon"
                            );
                            this.handleInput(
                                setting?.features_option15,
                                setting?.language,
                                "features_option15"
                            );
                            this.handleInput(
                                setting?.features_option15_tooltip,
                                setting?.language,
                                "features_option15_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option15_icon,
                                setting?.language,
                                "features_option15_icon"
                            );
                            this.handleInput(
                                setting?.features_option16,
                                setting?.language,
                                "features_option16"
                            );
                            this.handleInput(
                                setting?.features_option16_tooltip,
                                setting?.language,
                                "features_option16_tooltip"
                            );
                            this.handleInput(
                                setting?.features_option16_icon,
                                setting?.language,
                                "features_option16_icon"
                            );
                        });
                    }
                });
        },
        fetchFindRidePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-find-ride-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let find_ride_page_setting_detail =
                            res?.data?.data?.find_ride_page_setting_detail || [];
                        find_ride_page_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.ride_features_option4,
                                setting?.language,
                                "passenger_features_option4"
                            );
                            this.handleInput(
                                setting?.ride_features_option5,
                                setting?.language,
                                "passenger_features_option5"
                            );
                            this.handleInput(
                                setting?.ride_features_option6,
                                setting?.language,
                                "passenger_features_option6"
                            );
                            this.handleInput(
                                setting?.ride_features_option7,
                                setting?.language,
                                "passenger_features_option7"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-features-setting`,
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
                    `features_option1.features_option1_${language.id}`
                ) ||
                validationErros.has(
                    `features_option1_tooltip.features_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option1_icon.features_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option2.features_option2_${language.id}`
                ) ||
                validationErros.has(
                    `features_option2_tooltip.features_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option2_icon.features_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option3.features_option3_${language.id}`
                ) ||
                validationErros.has(
                    `features_option3_tooltip.features_option3_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option3_icon.features_option3_icon_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option4.driver_features_option4_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option4_tooltip.driver_features_option4_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option4_icon.driver_features_option4_icon_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_features_option4.passenger_features_option4_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option5.driver_features_option5_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option5_tooltip.driver_features_option5_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option5_icon.driver_features_option5_icon_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_features_option5.passenger_features_option5_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option6.driver_features_option6_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option6_tooltip.driver_features_option6_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option6_icon.driver_features_option6_icon_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_features_option6.passenger_features_option6_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option7.driver_features_option7_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option7_tooltip.driver_features_option7_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `driver_features_option7_icon.driver_features_option7_icon_${language.id}`
                ) ||
                validationErros.has(
                    `passenger_features_option7.passenger_features_option7_${language.id}`
                ) ||
                validationErros.has(
                    `features_option8.features_option8_${language.id}`
                ) ||
                validationErros.has(
                    `features_option8_tooltip.features_option8_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option8_icon.features_option8_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option9.features_option9_${language.id}`
                ) ||
                validationErros.has(
                    `features_option9_tooltip.features_option9_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option9_icon.features_option9_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option10.features_option10_${language.id}`
                ) ||
                validationErros.has(
                    `features_option10_tooltip.features_option10_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option10_icon.features_option10_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option11.features_option11_${language.id}`
                ) ||
                validationErros.has(
                    `features_option11_tooltip.features_option11_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option11_icon.features_option11_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option12.features_option12_${language.id}`
                ) ||
                validationErros.has(
                    `features_option12_tooltip.features_option12_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option12_icon.features_option12_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option13.features_option13_${language.id}`
                ) ||
                validationErros.has(
                    `features_option13_tooltip.features_option13_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option13_icon.features_option13_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option14.features_option14_${language.id}`
                ) ||
                validationErros.has(
                    `features_option14_tooltip.features_option14_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option14_icon.features_option14_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option15.features_option15_${language.id}`
                ) ||
                validationErros.has(
                    `features_option15_tooltip.features_option15_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option15_icon.features_option15_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option16_tooltip.features_option16_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `features_option16_icon.features_option16_icon_${language.id}`
                ) ||
                validationErros.has(
                    `features_option16.features_option16_${language.id}`
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
        onFeaturesExcelSuccess() {
            this.fetchPostRidePageSetting();
            this.fetchFindRidePageSetting();
        },
    },
};
</script>
