<template>
    <AppLayout>
        <section class="post-ride-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Booking & payment & cancellation methods settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <ExcelBulkImport
                        title="Booking & payment & cancellation methods"
                        mode="all_languages"
                        download-endpoint="download-payment-methods-setting-template"
                        upload-endpoint="upload-payment-methods-setting-excel"
                        @success="fetchPostRideSetting"
                    />

                    <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting()">
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                            <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                                <li class="mr-2" v-for="language in languages" :key="language.id">
                                    <a href="#" @click.prevent="
                                        updateLanguageId(language)
                                        " :class="[
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
                                        ]">{{ language.name }}</a>
                                </li>
                            </ul>
                        </div>
                        <template v-for="language in languages" :key="language.id">
                            <div v-if="
                                (activeLanguageId == null &&
                                    language.is_default) ||
                                language.id == activeLanguageId
                            ">
                                <!-- booking section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[0] =
                                            !collapseStates[0]
                                            ">
                                        <h3 class="text-white">
                                            Booking methods section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
                                        v-if="collapseStates[0]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`booking_option1_${activeLanguageId}`">Instant
                                                        booking</label>
                                                </div>
                                                <input type="text" :name="`booking_option1_${activeLanguageId}`"
                                                    :id="`booking_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'booking_option1'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_option1'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `booking_option1.booking_option1_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `booking_option1.booking_option1_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`booking_option1_tooltip_${activeLanguageId}`">Instant
                                                        booking tooltip</label>
                                                </div>
                                                <input type="text" :name="`booking_option1_tooltip_${activeLanguageId}`"
                                                    :id="`booking_option1_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'booking_option1_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_option1_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `booking_option1_tooltip.booking_option1_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `booking_option1_tooltip.booking_option1_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`booking_option1_icon_${activeLanguageId}`"
                                                            >Instant booking icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`booking_option1_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`booking_option1_icon_${activeLanguageId}`"
                                                        :id="`booking_option1_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'booking_option1_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `booking_option1_icon.booking_option1_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `booking_option1_icon.booking_option1_icon_${activeLanguageId}`
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
                                                            form['booking_option1_icon'] &&
                                                            form['booking_option1_icon'][`booking_option1_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['booking_option1_icon'] &&
                                                            form['booking_option1_icon'][`booking_option1_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['booking_option1_icon'][`booking_option1_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`booking_option2_${activeLanguageId}`">Request to
                                                        book</label>
                                                </div>
                                                <input type="text" :name="`booking_option2_${activeLanguageId}`"
                                                    :id="`booking_option2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'booking_option2'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_option2'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `booking_option2.booking_option2_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `booking_option2.booking_option2_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`booking_option2_tooltip_${activeLanguageId}`">Request
                                                        to book tooltip</label>
                                                </div>
                                                <input type="text" :name="`booking_option2_tooltip_${activeLanguageId}`"
                                                    :id="`booking_option2_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'booking_option2_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'booking_option2_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `booking_option2_tooltip.booking_option2_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `booking_option2_tooltip.booking_option2_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`booking_option2_icon_${activeLanguageId}`"
                                                            >Request to booking icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`booking_option2_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`booking_option2_icon_${activeLanguageId}`"
                                                        :id="`booking_option2_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'booking_option2_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `booking_option2_icon.booking_option2_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `booking_option2_icon.booking_option2_icon_${activeLanguageId}`
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
                                                            form['booking_option2_icon'] &&
                                                            form['booking_option2_icon'][`booking_option2_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['booking_option2_icon'] &&
                                                            form['booking_option2_icon'][`booking_option2_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['booking_option2_icon'][`booking_option2_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- booking section end -->

                                <!-- payment section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[1] =
                                            !collapseStates[1]
                                            ">
                                        <h3 class="text-white">
                                            Payment methods section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
                                        v-if="collapseStates[1]">
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`payment_methods_option1_${activeLanguageId}`">Cash</label>
                                                </div>
                                                <input type="text" :name="`payment_methods_option1_${activeLanguageId}`"
                                                    :id="`payment_methods_option1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option1'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option1'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option1.payment_methods_option1_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option1.payment_methods_option1_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`payment_methods_option1_tooltip_${activeLanguageId}`">Cash
                                                        tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`payment_methods_option1_tooltip_${activeLanguageId}`"
                                                    :id="`payment_methods_option1_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option1_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option1_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option1_tooltip.payment_methods_option1_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option1_tooltip.payment_methods_option1_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_methods_option1_icon_${activeLanguageId}`"
                                                            >Cash icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_methods_option1_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_methods_option1_icon_${activeLanguageId}`"
                                                        :id="`payment_methods_option1_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_methods_option1_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_methods_option1_icon.payment_methods_option1_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_methods_option1_icon.payment_methods_option1_icon_${activeLanguageId}`
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
                                                            form['payment_methods_option1_icon'] &&
                                                            form['payment_methods_option1_icon'][`payment_methods_option1_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_methods_option1_icon'] &&
                                                            form['payment_methods_option1_icon'][`payment_methods_option1_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_methods_option1_icon'][`payment_methods_option1_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`payment_methods_option2_${activeLanguageId}`">Online
                                                        payment</label>
                                                </div>
                                                <input type="text" :name="`payment_methods_option2_${activeLanguageId}`"
                                                    :id="`payment_methods_option2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option2'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option2'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option2.payment_methods_option2_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option2.payment_methods_option2_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`payment_methods_option2_tooltip_${activeLanguageId}`">Online
                                                        payment tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`payment_methods_option2_tooltip_${activeLanguageId}`"
                                                    :id="`payment_methods_option2_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option2_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option2_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option2_tooltip.payment_methods_option2_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option2_tooltip.payment_methods_option2_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_methods_option2_icon_${activeLanguageId}`"
                                                            >Online payment icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_methods_option2_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_methods_option2_icon_${activeLanguageId}`"
                                                        :id="`payment_methods_option2_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_methods_option2_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_methods_option2_icon.payment_methods_option2_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_methods_option2_icon.payment_methods_option2_icon_${activeLanguageId}`
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
                                                            form['payment_methods_option2_icon'] &&
                                                            form['payment_methods_option2_icon'][`payment_methods_option2_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_methods_option2_icon'] &&
                                                            form['payment_methods_option2_icon'][`payment_methods_option2_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_methods_option2_icon'][`payment_methods_option2_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`payment_methods_option3_${activeLanguageId}`">Secured-cash</label>
                                                </div>
                                                <input type="text" :name="`payment_methods_option3_${activeLanguageId}`"
                                                    :id="`payment_methods_option3_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option3'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option3'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option3.payment_methods_option3_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option3.payment_methods_option3_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`payment_methods_option3_tooltip_${activeLanguageId}`">Secured-cash tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`payment_methods_option3_tooltip_${activeLanguageId}`"
                                                    :id="`payment_methods_option3_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'payment_methods_option3_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'payment_methods_option3_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `payment_methods_option3_tooltip.payment_methods_option3_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `payment_methods_option3_tooltip.payment_methods_option3_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <label
                                                            :for="`payment_methods_option3_icon_${activeLanguageId}`"
                                                            >Secured-cash icon</label
                                                        >
                                                    </div>
                                                    <input
                                                        :key="`payment_methods_option3_icon_${activeLanguageId}`"
                                                        type="file"
                                                        :name="`payment_methods_option3_icon_${activeLanguageId}`"
                                                        :id="`payment_methods_option3_icon_${activeLanguageId}`"
                                                        class="block w-full rounded-md border-0 px-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
                                                        placeholder=" "
                                                        @input="
                                                            handleImage(
                                                                $event,
                                                                language,
                                                                'payment_methods_option3_icon',
                                                            )
                                                        "
                                                    />
                                                    <p
                                                        class="mt-2 text-sm text-red-400"
                                                        v-if="
                                                            validationErros.has(
                                                                `payment_methods_option3_icon.payment_methods_option3_icon_${activeLanguageId}`
                                                            )
                                                        "
                                                        v-text="
                                                            validationErros.get(
                                                                `payment_methods_option3_icon.payment_methods_option3_icon_${activeLanguageId}`
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
                                                            form['payment_methods_option3_icon'] &&
                                                            form['payment_methods_option3_icon'][`payment_methods_option3_icon_${activeLanguageId}`]
                                                        "
                                                        :src="
                                                            form['payment_methods_option3_icon'] &&
                                                            form['payment_methods_option3_icon'][`payment_methods_option3_icon_${activeLanguageId}`]
                                                                ? '/home_page_icons/' + form['payment_methods_option3_icon'][`payment_methods_option3_icon_${activeLanguageId}`]
                                                                : ''
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- payment section end -->

                                <!-- cancellation section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[2] =
                                            !collapseStates[2]
                                            ">
                                        <h3 class="text-white">
                                            Cancellation methods section
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
                                        v-if="collapseStates[2]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancellation_policy_label1_${activeLanguageId}`">Standard
                                                        booking</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancellation_policy_label1_${activeLanguageId}`"
                                                    :id="`cancellation_policy_label1_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'cancellation_policy_label1'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_label1'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `cancellation_policy_label1.cancellation_policy_label1_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `cancellation_policy_label1.cancellation_policy_label1_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancellation_policy_label1_tooltip_${activeLanguageId}`">Standard
                                                        booking tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancellation_policy_label1_tooltip_${activeLanguageId}`"
                                                    :id="`cancellation_policy_label1_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'cancellation_policy_label1_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_label1_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `cancellation_policy_label1_tooltip.cancellation_policy_label1_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `cancellation_policy_label1_tooltip.cancellation_policy_label1_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`cancellation_policy_label2_${activeLanguageId}`">Firm
                                                        booking</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancellation_policy_label2_${activeLanguageId}`"
                                                    :id="`cancellation_policy_label2_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'cancellation_policy_label2'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_label2'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `cancellation_policy_label2.cancellation_policy_label2_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `cancellation_policy_label2.cancellation_policy_label2_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`cancellation_policy_label2_tooltip_${activeLanguageId}`">Firm
                                                        booking tooltip</label>
                                                </div>
                                                <input type="text"
                                                    :name="`cancellation_policy_label2_tooltip_${activeLanguageId}`"
                                                    :id="`cancellation_policy_label2_tooltip_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'cancellation_policy_label2_tooltip'
                                                    )
                                                        " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'cancellation_policy_label2_tooltip'
                                                        )
                                                        " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `cancellation_policy_label2_tooltip.cancellation_policy_label2_tooltip_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                    `cancellation_policy_label2_tooltip.cancellation_policy_label2_tooltip_${activeLanguageId}`
                                                )
                                                    "></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[3] =
                                            !collapseStates[3]
                                            ">
                                        <h3 class="text-white">
                                            Vehicle Types
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6"
                                        v-if="collapseStates[3]">

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_convertible_text_${activeLanguageId}`">Convertible</label>
                                                </div>
                                                <input type="text"
                                                    :name="`vehicle_type_convertible_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_convertible_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('vehicle_type_convertible_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_convertible_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_convertible_text.vehicle_type_convertible_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_convertible_text.vehicle_type_convertible_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_hatchback_text_${activeLanguageId}`">Hatchback</label>
                                                </div>
                                                <input type="text"
                                                    :name="`vehicle_type_hatchback_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_hatchback_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('vehicle_type_hatchback_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_hatchback_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_hatchback_text.vehicle_type_hatchback_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_hatchback_text.vehicle_type_hatchback_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_coupe_text_${activeLanguageId}`">Coupe</label>
                                                </div>
                                                <input type="text" :name="`vehicle_type_coupe_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_coupe_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('vehicle_type_coupe_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_coupe_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_coupe_text.vehicle_type_coupe_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_coupe_text.vehicle_type_coupe_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_minivan_text_${activeLanguageId}`">Minivan</label>
                                                </div>
                                                <input type="text"
                                                    :name="`vehicle_type_minivan_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_minivan_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('vehicle_type_minivan_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_minivan_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_minivan_text.vehicle_type_minivan_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_minivan_text.vehicle_type_minivan_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_sedan_text_${activeLanguageId}`">Sedan</label>
                                                </div>
                                                <input type="text" :name="`vehicle_type_sedan_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_sedan_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('vehicle_type_sedan_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_sedan_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_sedan_text.vehicle_type_sedan_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_sedan_text.vehicle_type_sedan_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_station_wagon_text_${activeLanguageId}`">Station
                                                        Wagon</label>
                                                </div>
                                                <input type="text"
                                                    :name="`vehicle_type_station_wagon_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_station_wagon_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="getCurrentValue('vehicle_type_station_wagon_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_station_wagon_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_station_wagon_text.vehicle_type_station_wagon_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_station_wagon_text.vehicle_type_station_wagon_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_suv_text_${activeLanguageId}`">SUV</label>
                                                </div>
                                                <input type="text" :name="`vehicle_type_suv_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_suv_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('vehicle_type_suv_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_suv_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_suv_text.vehicle_type_suv_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_suv_text.vehicle_type_suv_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_truck_text_${activeLanguageId}`">Truck</label>
                                                </div>
                                                <input type="text" :name="`vehicle_type_truck_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_truck_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('vehicle_type_truck_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_truck_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_truck_text.vehicle_type_truck_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_truck_text.vehicle_type_truck_text_${activeLanguageId}')">
                                            </p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`vehicle_type_van_text_${activeLanguageId}`">Van</label>
                                                </div>
                                                <input type="text" :name="`vehicle_type_van_text_${activeLanguageId}`"
                                                    :id="`vehicle_type_van_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue('vehicle_type_van_text')"
                                                    @input="handleInput($event.target.value, language, 'vehicle_type_van_text')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400"
                                                v-if="validationErros.has('vehicle_type_van_text.vehicle_type_van_text_${activeLanguageId}')"
                                                v-text="validationErros.get('vehicle_type_van_text.vehicle_type_van_text_${activeLanguageId}')">
                                            </p>
                                        </div>


                                    </div>
                                </div>
                                <!-- cancellation section end -->
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
            collapseStates: [true, false, false, false, false, false, false, false, false, false],
            loading: false,
        };
    },
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/pages/';
            if (!base.endsWith('/')) base += '/';
            return base;
        },
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
            if (!language || language.id == null) return;
            const safeValue = value ?? "";
            if (this.form.hasOwnProperty(key)) {
                this.form[key][`${key}_${language.id}`] = safeValue;
            } else {
                this.form[key] = {};
                this.form[key][`${key}_${language.id}`] = safeValue;
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
                            this.handleInput("", language, "booking_option1");
                            this.handleInput("", language, "booking_option1_tooltip");
                            this.handleInput("", language, "booking_option1_icon");
                            this.handleInput("", language, "booking_option2");
                            this.handleInput("", language, "booking_option2_tooltip");
                            this.handleInput("", language, "booking_option2_icon");
                            this.handleInput("", language, "payment_methods_option1");
                            this.handleInput("", language, "payment_methods_option1_tooltip");
                            this.handleInput("", language, "payment_methods_option1_icon");
                            this.handleInput("", language, "payment_methods_option2");
                            this.handleInput("", language, "payment_methods_option2_tooltip");
                            this.handleInput("", language, "payment_methods_option2_icon");
                            this.handleInput("", language, "payment_methods_option3");
                            this.handleInput("", language, "payment_methods_option3_tooltip");
                            this.handleInput("", language, "payment_methods_option3_icon");
                            this.handleInput("", language, "cancellation_policy_label1");
                            this.handleInput("", language, "cancellation_policy_label1_tooltip");
                            this.handleInput("", language, "cancellation_policy_label2");
                            this.handleInput("", language, "cancellation_policy_label2_tooltip");
                            this.handleInput("", language, "vehicle_type_convertible_text");
                            this.handleInput("", language, "vehicle_type_hatchback_text");
                            this.handleInput("", language, "vehicle_type_coupe_text");
                            this.handleInput("", language, "vehicle_type_minivan_text");
                            this.handleInput("", language, "vehicle_type_sedan_text");
                            this.handleInput("", language, "vehicle_type_station_wagon_text");
                            this.handleInput("", language, "vehicle_type_suv_text");
                            this.handleInput("", language, "vehicle_type_truck_text");
                            this.handleInput("", language, "vehicle_type_van_text");

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
                                setting?.booking_option1,
                                setting?.language,
                                "booking_option1"
                            );
                            this.handleInput(
                                setting?.booking_option1_tooltip,
                                setting?.language,
                                "booking_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.booking_option1_icon,
                                setting?.language,
                                "booking_option1_icon"
                            );
                            this.handleInput(
                                setting?.booking_option2,
                                setting?.language,
                                "booking_option2"
                            );
                            this.handleInput(
                                setting?.booking_option2_tooltip,
                                setting?.language,
                                "booking_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.booking_option2_icon,
                                setting?.language,
                                "booking_option2_icon"
                            );
                            this.handleInput(
                                setting?.payment_methods_option1,
                                setting?.language,
                                "payment_methods_option1"
                            );
                            this.handleInput(
                                setting?.payment_methods_option1_tooltip,
                                setting?.language,
                                "payment_methods_option1_tooltip"
                            );
                            this.handleInput(
                                setting?.payment_methods_option1_icon,
                                setting?.language,
                                "payment_methods_option1_icon"
                            );
                            this.handleInput(
                                setting?.payment_methods_option2,
                                setting?.language,
                                "payment_methods_option2"
                            );
                            this.handleInput(
                                setting?.payment_methods_option2_tooltip,
                                setting?.language,
                                "payment_methods_option2_tooltip"
                            );
                            this.handleInput(
                                setting?.payment_methods_option2_icon,
                                setting?.language,
                                "payment_methods_option2_icon"
                            );
                            this.handleInput(
                                setting?.payment_methods_option3,
                                setting?.language,
                                "payment_methods_option3"
                            );
                            this.handleInput(
                                setting?.payment_methods_option3_tooltip,
                                setting?.language,
                                "payment_methods_option3_tooltip"
                            );
                            this.handleInput(
                                setting?.payment_methods_option3_icon,
                                setting?.language,
                                "payment_methods_option3_icon"
                            );                            
                            this.handleInput(
                                setting?.cancellation_policy_label1,
                                setting?.language,
                                "cancellation_policy_label1"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label1_tooltip,
                                setting?.language,
                                "cancellation_policy_label1_tooltip"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label2,
                                setting?.language,
                                "cancellation_policy_label2"
                            );
                            this.handleInput(
                                setting?.cancellation_policy_label2_tooltip,
                                setting?.language,
                                "cancellation_policy_label2_tooltip"
                            );
                            this.handleInput(
                                setting?.vehicle_type_convertible_text,
                                setting?.language,
                                "vehicle_type_convertible_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_hatchback_text,
                                setting?.language,
                                "vehicle_type_hatchback_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_coupe_text,
                                setting?.language,
                                "vehicle_type_coupe_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_minivan_text,
                                setting?.language,
                                "vehicle_type_minivan_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_sedan_text,
                                setting?.language,
                                "vehicle_type_sedan_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_station_wagon_text,
                                setting?.language,
                                "vehicle_type_station_wagon_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_suv_text,
                                setting?.language,
                                "vehicle_type_suv_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_truck_text,
                                setting?.language,
                                "vehicle_type_truck_text"
                            );

                            this.handleInput(
                                setting?.vehicle_type_van_text,
                                setting?.language,
                                "vehicle_type_van_text"
                            );

                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-payment-methods-setting`,
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
                    `booking_option1.booking_option1_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option1_tooltip.booking_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option1_icon.booking_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option2.booking_option2_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option2_tooltip.booking_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `booking_option2_icon.booking_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option1.payment_methods_option1_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option1_tooltip.payment_methods_option1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option1_icon.payment_methods_option1_icon_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option2.payment_methods_option2_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option2_tooltip.payment_methods_option2_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option2_icon.payment_methods_option2_icon_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option3_tooltip.payment_methods_option3_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option3_icon.payment_methods_option3_icon_${language.id}`
                ) ||
                validationErros.has(
                    `payment_methods_option3.payment_methods_option3_${language.id}`
                ) ||                
                validationErros.has(
                    `cancellation_policy_label1.cancellation_policy_label1_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label1_tooltip.cancellation_policy_label1_tooltip_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label2.cancellation_policy_label2_${language.id}`
                ) ||
                validationErros.has(
                    `cancellation_policy_label2_tooltip.cancellation_policy_label2_tooltip_${language.id}`
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
