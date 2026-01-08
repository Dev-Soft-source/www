<template>
    <AppLayout>
        <section class="post-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Ride preferences settings
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
                                <!-- smoking section start -->
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
                                            Smoking section
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
                                                        :for="`smoking_option1_${activeLanguageId}`"
                                                        >No</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`smoking_option1_${activeLanguageId}`"
                                                    :id="`smoking_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'smoking_option1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_option1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `smoking_option1.smoking_option1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `smoking_option1.smoking_option1_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`smoking_option1_tooltip_${activeLanguageId}`"
                                                        >No tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`smoking_option1_tooltip_${activeLanguageId}`"
                                                    :id="`smoking_option1_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'smoking_option1_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_option1_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `smoking_option1_tooltip.smoking_option1_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `smoking_option1_tooltip.smoking_option1_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`smoking_option1_icon_${activeLanguageId}`"
                                                            >No icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`smoking_option1_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`smoking_option1_icon_${activeLanguageId}`"
                                                        :id="`smoking_option1_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'smoking_option1_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `smoking_option1_icon.smoking_option1_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `smoking_option1_icon.smoking_option1_icon_${activeLanguageId}`
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
                                                            form['smoking_option1_icon'] &&
                                                            form['smoking_option1_icon'][`smoking_option1_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['smoking_option1_icon'] &&
                                                            form['smoking_option1_icon'][`smoking_option1_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['smoking_option1_icon'][`smoking_option1_icon_${activeLanguageId}`]
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
                                                        :for="`smoking_option2_${activeLanguageId}`"
                                                        >Indifferent</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`smoking_option2_${activeLanguageId}`"
                                                    :id="`smoking_option2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'smoking_option2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_option2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `smoking_option2.smoking_option2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `smoking_option2.smoking_option2_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`smoking_option2_tooltip_${activeLanguageId}`"
                                                        >Indifferent tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`smoking_option2_tooltip_${activeLanguageId}`"
                                                    :id="`smoking_option2_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'smoking_option2_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'smoking_option2_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `smoking_option2_tooltip.smoking_option2_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `smoking_option2_tooltip.smoking_option2_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`smoking_option2_icon_${activeLanguageId}`"
                                                            >Indifferent icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`smoking_option2_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`smoking_option2_icon_${activeLanguageId}`"
                                                        :id="`smoking_option2_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'smoking_option2_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `smoking_option2_icon.smoking_option2_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `smoking_option2_icon.smoking_option2_icon_${activeLanguageId}`
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
                                                            form['smoking_option2_icon'] &&
                                                            form['smoking_option2_icon'][`smoking_option2_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['smoking_option2_icon'] &&
                                                            form['smoking_option2_icon'][`smoking_option2_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['smoking_option2_icon'][`smoking_option2_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- smoking section end -->

                                <!-- animals section start -->
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
                                            Animals section
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
                                                        :for="`animals_option1_${activeLanguageId}`"
                                                        >No</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_option1_${activeLanguageId}`"
                                                    :id="`animals_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_option1'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_option1'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_option1.animals_option1_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_option1.animals_option1_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`animals_option1_tooltip_${activeLanguageId}`"
                                                        >No tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_option1_tooltip_${activeLanguageId}`"
                                                    :id="`animals_option1_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_option1_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_option1_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_option1_tooltip.animals_option1_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_option1_tooltip.animals_option1_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`animals_option1_icon_${activeLanguageId}`"
                                                            >No icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`animals_option1_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`animals_option1_icon_${activeLanguageId}`"
                                                        :id="`animals_option1_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'animals_option1_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `animals_option1_icon.animals_option1_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `animals_option1_icon.animals_option1_icon_${activeLanguageId}`
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
                                                            form['animals_option1_icon'] &&
                                                            form['animals_option1_icon'][`animals_option1_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['animals_option1_icon'] &&
                                                            form['animals_option1_icon'][`animals_option1_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['animals_option1_icon'][`animals_option1_icon_${activeLanguageId}`]
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
                                                        :for="`animals_option2_${activeLanguageId}`"
                                                        >Yes</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_option2_${activeLanguageId}`"
                                                    :id="`animals_option2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_option2'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_option2'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_option2.animals_option2_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_option2.animals_option2_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`animals_option2_tooltip_${activeLanguageId}`"
                                                        >Yes tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_option2_tooltip_${activeLanguageId}`"
                                                    :id="`animals_option2_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_option2_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_option2_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_option2_tooltip.animals_option2_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_option2_tooltip.animals_option2_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`animals_option2_icon_${activeLanguageId}`"
                                                            >Yes icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`animals_option2_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`animals_option2_icon_${activeLanguageId}`"
                                                        :id="`animals_option2_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'animals_option2_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `animals_option2_icon.animals_option2_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `animals_option2_icon.animals_option2_icon_${activeLanguageId}`
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
                                                            form['animals_option2_icon'] &&
                                                            form['animals_option2_icon'][`animals_option2_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['animals_option2_icon'] &&
                                                            form['animals_option2_icon'][`animals_option2_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['animals_option2_icon'][`animals_option2_icon_${activeLanguageId}`]
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
                                                        :for="`animals_option3_${activeLanguageId}`"
                                                        >Caged animal only</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_option3_${activeLanguageId}`"
                                                    :id="`animals_option3_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_option3'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_option3'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_option3.animals_option3_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_option3.animals_option3_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`animals_option3_tooltip_${activeLanguageId}`"
                                                        >Caged animal only tooltip</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`animals_option3_tooltip_${activeLanguageId}`"
                                                    :id="`animals_option3_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'animals_option3_tooltip'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'animals_option3_tooltip'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `animals_option3_tooltip.animals_option3_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `animals_option3_tooltip.animals_option3_tooltip_${activeLanguageId}`
                                                    )
                                                "
                                            ></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`animals_option3_icon_${activeLanguageId}`"
                                                            >Caged animal only icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`animals_option3_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`animals_option3_icon_${activeLanguageId}`"
                                                        :id="`animals_option3_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'animals_option3_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `animals_option3_icon.animals_option3_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `animals_option3_icon.animals_option3_icon_${activeLanguageId}`
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
                                                            form['animals_option3_icon'] &&
                                                            form['animals_option3_icon'][`animals_option3_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['animals_option3_icon'] &&
                                                            form['animals_option3_icon'][`animals_option3_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['animals_option3_icon'][`animals_option3_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- animals section end -->
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
                            this.handleInput("", language, "smoking_option1");
                            this.handleInput("", language, "smoking_option1_tooltip");
                            this.handleInput("", language, "smoking_option1_icon");
                            this.handleInput("", language, "smoking_option2");
                            this.handleInput("", language, "smoking_option2_tooltip");
                            this.handleInput("", language, "smoking_option2_icon");
                            this.handleInput("", language, "animals_option1");
                            this.handleInput("", language, "animals_option1_tooltip");
                            this.handleInput("", language, "animals_option1_icon");
                            this.handleInput("", language, "animals_option2");
                            this.handleInput("", language, "animals_option2_tooltip");
                            this.handleInput("", language, "animals_option2_icon");
                            this.handleInput("", language, "animals_option3");
                            this.handleInput("", language, "animals_option3_tooltip");
                            this.handleInput("", language, "animals_option3_icon");
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
                                setting?.smoking_option1,
                                setting?.language,
                                "smoking_option1"
                            );
                            this.handleInput(
                                setting?.smoking_option1_tooltip,
                                setting?.language,
                                "smoking_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.smoking_option1_icon,
                                setting?.language,
                                "smoking_option1_icon"
                            );
                            this.handleInput(
                                setting?.smoking_option2,
                                setting?.language,
                                "smoking_option2"
                            );
                            this.handleInput(
                                setting?.smoking_option2_tooltip,
                                setting?.language,
                                "smoking_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.smoking_option2_icon,
                                setting?.language,
                                "smoking_option2_icon"
                            );
                            this.handleInput(
                                setting?.animals_option1,
                                setting?.language,
                                "animals_option1"
                            );
                            this.handleInput(
                                setting?.animals_option1_tooltip,
                                setting?.language,
                                "animals_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.animals_option1_icon,
                                setting?.language,
                                "animals_option1_icon"
                            );
                            this.handleInput(
                                setting?.animals_option2,
                                setting?.language,
                                "animals_option2"
                            );
                            this.handleInput(
                                setting?.animals_option2_tooltip,
                                setting?.language,
                                "animals_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.animals_option2_icon,
                                setting?.language,
                                "animals_option2_icon"
                            );
                            this.handleInput(
                                setting?.animals_option3,
                                setting?.language,
                                "animals_option3"
                            );
                            this.handleInput(
                                setting?.animals_option3_tooltip,
                                setting?.language,
                                "animals_option3_tooltip"
                            );
                            this.handleInput(
                                setting?.animals_option3_icon,
                                setting?.language,
                                "animals_option3_icon"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-preferences-setting`,
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
                    `smoking_option1.smoking_option1_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option1_tooltip.smoking_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option1_icon.smoking_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option2.smoking_option2_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option2_tooltip.smoking_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `smoking_option2_icon.smoking_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option1.animals_option1_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option1_tooltip.animals_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option1_icon.animals_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option2.animals_option2_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option2_tooltip.animals_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option2_icon.animals_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option3_tooltip.animals_option3_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option3_icon.animals_option3_icon_${language.id}`
                ) ||
                validationErros.has(
                    `animals_option3.animals_option3_${language.id}`
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
