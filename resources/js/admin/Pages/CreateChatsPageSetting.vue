<template>
    <AppLayout>
        <section class="chats-section relative">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Chats & notifications page settings
                                </h3>
                            </div>
                        </div>
                    </header>

                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <ExcelBulkImport
                            title="Chats & Notifications Page"
                            mode="all_languages"
                            download-endpoint="download-chats-page-setting-template"
                            upload-endpoint="upload-chats-page-setting-excel"
                            @success="fetchTermsOfUsePageSetting"
                        />
                    </div>

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
                                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`name_${activeLanguageId}`">Name</label>
                                            </div>
                                            <input type="text" :name="`name_${activeLanguageId}`"
                                                :id="`name_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue('name')" @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'name'
                                                    )
                                                    " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `name.name_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                            `name.name_${activeLanguageId}`
                                        )
                                            "></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`meta_description_${activeLanguageId}`">Meta
                                                    description</label>
                                            </div>
                                            <input type="text" :name="`meta_description_${activeLanguageId}`"
                                                :id="`meta_description_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue(
                                                    'meta_description'
                                                )
                                                    " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'meta_description'
                                                        )
                                                        " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `meta_description.meta_description_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                            `meta_description.meta_description_${activeLanguageId}`
                                        )
                                            "></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`meta_keywords_${activeLanguageId}`">Meta keywords</label>
                                            </div>
                                            <input type="text" :name="`meta_keywords_${activeLanguageId}`"
                                                :id="`meta_keywords_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="getCurrentValue(
                                                    'meta_keywords'
                                                )
                                                    " @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'meta_keywords'
                                                        )
                                                        " />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="
                                            validationErros.has(
                                                `meta_keywords.meta_keywords_${activeLanguageId}`
                                            )
                                        " v-text="validationErros.get(
                                            `meta_keywords.meta_keywords_${activeLanguageId}`
                                        )
                                            "></p>
                                    </div>
                                </div>

                                <!-- chats section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[1] =
                                            !collapseStates[1]
                                            ">
                                        <h3 class="text-white">
                                            Chats page
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[1]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`main_heading_${activeLanguageId}`">Main
                                                        heading</label>
                                                </div>
                                                <input type="text" :name="`main_heading_${activeLanguageId}`"
                                                    :id="`main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'main_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'main_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `main_heading.main_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `main_heading.main_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`old_messages_heading_${activeLanguageId}`">Old
                                                        messages heading</label>
                                                </div>
                                                <input type="text" :name="`old_messages_heading_${activeLanguageId}`"
                                                    :id="`old_messages_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'old_messages_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'old_messages_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `old_messages_heading.old_messages_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `old_messages_heading.old_messages_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_messages_label_${activeLanguageId}`">No message
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`no_messages_label_${activeLanguageId}`"
                                                    :id="`no_messages_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'no_messages_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'no_messages_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `no_messages_label.no_messages_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `no_messages_label.no_messages_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`driver_chat_with_${activeLanguageId}`">Driver chat with</label>
                                                </div>
                                                <input type="text" :name="`driver_chat_with_${activeLanguageId}`"
                                                    :id="`driver_chat_with_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'driver_chat_with'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'driver_chat_with'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `driver_chat_with.driver_chat_with_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `driver_chat_with.driver_chat_with_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`empty_chat_placeholder_${activeLanguageId}`">Empty chat placeholder</label>
                                                </div>
                                                <input type="text" :name="`empty_chat_placeholder_${activeLanguageId}`"
                                                    :id="`empty_chat_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'empty_chat_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'empty_chat_placeholder'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `empty_chat_placeholder.empty_chat_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `empty_chat_placeholder.empty_chat_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`ride_detail_header_${activeLanguageId}`">Ride detail header</label>
                                                </div>
                                                <input type="text" :name="`ride_detail_header_${activeLanguageId}`"
                                                    :id="`ride_detail_header_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'ride_detail_header'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'ride_detail_header'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `ride_detail_header.ride_detail_header_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `ride_detail_header.ride_detail_header_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`chat_start_mark_${activeLanguageId}`">Chat start mark</label>
                                                </div>
                                                <input type="text" :name="`chat_start_mark_${activeLanguageId}`"
                                                    :id="`chat_start_mark_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'chat_start_mark'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'chat_start_mark'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `chat_start_mark.chat_start_mark_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `chat_start_mark.chat_start_mark_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`delete_messages_label_${activeLanguageId}`">Delete
                                                        message confirmation text</label>
                                                </div>
                                                <input type="text" :name="`delete_messages_label_${activeLanguageId}`"
                                                    :id="`delete_messages_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'delete_messages_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'delete_messages_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `delete_messages_label.delete_messages_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `delete_messages_label.delete_messages_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`type_message_placeholder_${activeLanguageId}`">Type
                                                        message placeholder</label>
                                                </div>
                                                <textarea 
                                                    :name="`type_message_placeholder_${activeLanguageId}`"
                                                    :id="`type_message_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'type_message_placeholder'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'type_message_placeholder'
                                                            )
                                                            "></textarea>
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `type_message_placeholder.type_message_placeholder_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `type_message_placeholder.type_message_placeholder_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>




                                    </div>
                                </div>
                                <!-- chats section end -->

                                <!-- old chats section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[2] =
                                            !collapseStates[2]
                                            ">
                                        <h3 class="text-white">
                                            Old chats page
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[2]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`old_chat_page_main_heading_${activeLanguageId}`">Main
                                                        heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`old_chat_page_main_heading_${activeLanguageId}`"
                                                    :id="`old_chat_page_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'old_chat_page_main_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'old_chat_page_main_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `old_chat_page_main_heading.old_chat_page_main_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `old_chat_page_main_heading.old_chat_page_main_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`old_chat_page_no_messages_label_${activeLanguageId}`">No
                                                        message label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`old_chat_page_no_messages_label_${activeLanguageId}`"
                                                    :id="`old_chat_page_no_messages_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'old_chat_page_no_messages_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'old_chat_page_no_messages_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `old_chat_page_no_messages_label.old_chat_page_no_messages_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `old_chat_page_no_messages_label.old_chat_page_no_messages_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- old chats section end -->

                                <!-- notifications section start -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                                    ">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="
                                            collapseStates[3] =
                                            !collapseStates[3]
                                            ">
                                        <h3 class="text-white">
                                            Notifications page
                                        </h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>

                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[3]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`notification_page_main_heading_${activeLanguageId}`">Main
                                                        heading</label>
                                                </div>
                                                <input type="text"
                                                    :name="`notification_page_main_heading_${activeLanguageId}`"
                                                    :id="`notification_page_main_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'notification_page_main_heading'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'notification_page_main_heading'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `notification_page_main_heading.notification_page_main_heading_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `notification_page_main_heading.notification_page_main_heading_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`notification_page_no_messages_label_${activeLanguageId}`">No
                                                        notification label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`notification_page_no_messages_label_${activeLanguageId}`"
                                                    :id="`notification_page_no_messages_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'notification_page_no_messages_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'notification_page_no_messages_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `notification_page_no_messages_label.notification_page_no_messages_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `notification_page_no_messages_label.notification_page_no_messages_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`navigation_chat_label_${activeLanguageId}`">Navigation
                                                        Chat label</label>
                                                </div>
                                                <input type="text" :name="`navigation_chat_label_${activeLanguageId}`"
                                                    :id="`navigation_chat_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'navigation_chat_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'navigation_chat_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `navigation_chat_label.navigation_chat_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `navigation_chat_label.navigation_chat_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`navigation_my_trip_label_${activeLanguageId}`">Navigation
                                                        Trip label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`navigation_my_trip_label_${activeLanguageId}`"
                                                    :id="`navigation_my_trip_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'navigation_my_trip_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'navigation_my_trip_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `navigation_my_trip_label.navigation_my_trip_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `navigation_my_trip_label.navigation_my_trip_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`navigation_my_profile_label_${activeLanguageId}`">Navigation
                                                        Profile label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`navigation_my_profile_label_${activeLanguageId}`"
                                                    :id="`navigation_my_profile_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'navigation_my_profile_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'navigation_my_profile_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `navigation_my_profile_label.navigation_my_profile_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `navigation_my_profile_label.navigation_my_profile_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`exit_app_label_${activeLanguageId}`">Exit App
                                                        label</label>
                                                </div>
                                                <input type="text" :name="`exit_app_label_${activeLanguageId}`"
                                                    :id="`exit_app_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'exit_app_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'exit_app_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `exit_app_label.exit_app_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `exit_app_label.exit_app_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`notification_filter_btn_label_${activeLanguageId}`">Notification
                                                        filter btn label</label>
                                                </div>
                                                <input type="text"
                                                    :name="`notification_filter_btn_label_${activeLanguageId}`"
                                                    :id="`notification_filter_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'notification_filter_btn_label'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'notification_filter_btn_label'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `notification_filter_btn_label.notification_filter_btn_label_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `notification_filter_btn_label.notification_filter_btn_label_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label
                                                        :for="`notification_confirm_message_${activeLanguageId}`">Notification
                                                        confirm message</label>
                                                </div>
                                                <input type="text"
                                                    :name="`notification_confirm_message_${activeLanguageId}`"
                                                    :id="`notification_confirm_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'notification_confirm_message'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'notification_confirm_message'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `notification_confirm_message.notification_confirm_message_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `notification_confirm_message.notification_confirm_message_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>

                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`notification_delete_text_${activeLanguageId}`">Are you
                                                        sure you want to delete this notification text</label>
                                                </div>
                                                <input type="text"
                                                    :name="`notification_delete_text_${activeLanguageId}`"
                                                    :id="`notification_delete_text_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" " :value="getCurrentValue(
                                                        'notification_delete_text'
                                                    )
                                                        " @input="
                                                            handleInput(
                                                                $event.target.value,
                                                                language,
                                                                'notification_delete_text'
                                                            )
                                                            " />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="
                                                validationErros.has(
                                                    `notification_delete_text.notification_delete_text_${activeLanguageId}`
                                                )
                                            " v-text="validationErros.get(
                                                `notification_delete_text.notification_delete_text_${activeLanguageId}`
                                            )
                                                "></p>
                                        </div>


                                    </div>
                                </div>
                                <!-- notifications section end -->
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
    components: {
        editor: Editor,
        ExcelBulkImport,
    },
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
    computed: {
        mixAdminApiUrl() { return process.env.MIX_ADMIN_API_URL; }
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
                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "main_heading");
                            this.handleInput("", language, "old_messages_heading");
                            this.handleInput("", language, "no_messages_label");
                            this.handleInput("", language, "driver_chat_with");
                            this.handleInput("", language, "empty_chat_placeholder");
                            this.handleInput("", language, "ride_detail_header");
                            this.handleInput("", language, "chat_start_mark");
                            this.handleInput("", language, "old_chat_page_main_heading");
                            this.handleInput("", language, "old_chat_page_no_messages_label");
                            this.handleInput("", language, "delete_messages_label");
                            this.handleInput("", language, "notification_page_main_heading");
                            this.handleInput("", language, "notification_page_no_messages_label");
                            this.handleInput("", language, "navigation_chat_label");
                            this.handleInput("", language, "exit_app_label");
                            this.handleInput("", language, "navigation_my_profile_label");
                            this.handleInput("", language, "navigation_chat_label");
                            this.handleInput("", language, "notification_filter_btn_label");
                            this.handleInput("", language, "notification_delete_text");
                            this.handleInput("", language, "notification_confirm_message");
                            this.handleInput("", language, "type_message_placeholder");
                        });
                        this.fetchTermsOfUsePageSetting();
                    }
                });
        },
        fetchTermsOfUsePageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-chats-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let chats_page_setting_detail =
                            res?.data?.data?.chats_page_setting_detail || [];
                        chats_page_setting_detail.map((setting) => {
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
                                setting?.old_messages_heading,
                                setting?.language,
                                "old_messages_heading"
                            );
                            this.handleInput(
                                setting?.no_messages_label,
                                setting?.language,
                                "no_messages_label"
                            );
                            this.handleInput(
                                setting?.driver_chat_with,
                                setting?.language,
                                "driver_chat_with"
                            );
                            this.handleInput(
                                setting?.empty_chat_placeholder,
                                setting?.language,
                                "empty_chat_placeholder"
                            );
                            this.handleInput(
                                setting?.ride_detail_header,
                                setting?.language,
                                "ride_detail_header"
                            );
                            this.handleInput(
                                setting?.chat_start_mark,
                                setting?.language,
                                "chat_start_mark"
                            );
                            this.handleInput(
                                setting?.old_chat_page_no_messages_label,
                                setting?.language,
                                "old_chat_page_no_messages_label"
                            );
                            this.handleInput(
                                setting?.delete_messages_label,
                                setting?.language,
                                "delete_messages_label"
                            );
                            this.handleInput(
                                setting?.old_chat_page_main_heading,
                                setting?.language,
                                "old_chat_page_main_heading"
                            );
                            this.handleInput(
                                setting?.notification_page_main_heading,
                                setting?.language,
                                "notification_page_main_heading"
                            );
                            this.handleInput(
                                setting?.notification_page_no_messages_label,
                                setting?.language,
                                "notification_page_no_messages_label"
                            );
                            this.handleInput(
                                setting?.exit_app_label,
                                setting?.language,
                                "exit_app_label"
                            );
                            this.handleInput(
                                setting?.type_message_placeholder,
                                setting?.language,
                                "type_message_placeholder"
                            );

                            this.handleInput(
                                setting?.navigation_my_profile_label,
                                setting?.language,
                                "navigation_my_profile_label"
                            );
                            this.handleInput(
                                setting?.navigation_my_trip_label,
                                setting?.language,
                                "navigation_my_trip_label"
                            );
                            this.handleInput(
                                setting?.navigation_chat_label,
                                setting?.language,
                                "navigation_chat_label"
                            );
                            this.handleInput(
                                setting?.notification_filter_btn_label,
                                setting?.language,
                                "notification_filter_btn_label"
                            );
                            this.handleInput(
                                setting?.notification_delete_text,
                                setting?.language,
                                "notification_delete_text"
                            );
                            this.handleInput(
                                setting?.notification_confirm_message,
                                setting?.language,
                                "notification_confirm_message"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-chats-page-setting`,
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
                    `old_messages_heading.old_messages_heading_${language.id}`
                ) ||
                validationErros.has(
                    `no_messages_label.no_messages_label_${language.id}`
                ) ||
                validationErros.has(
                    `driver_chat_with.driver_chat_with_${language.id}`
                ) ||
                validationErros.has(
                    `empty_chat_placeholder.empty_chat_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `ride_detail_header.ride_detail_header_${language.id}`
                ) ||
                validationErros.has(
                    `chat_start_mark.chat_start_mark_${language.id}`
                ) ||
                validationErros.has(
                    `old_chat_page_main_heading.old_chat_page_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `old_chat_page_no_messages_label.old_chat_page_no_messages_label_${language.id}`
                ) ||

                validationErros.has(
                    `delete_messages_label.delete_messages_label_${language.id}`
                ) ||
                validationErros.has(
                    `notification_page_main_heading.notification_page_main_heading_${language.id}`
                ) ||
                validationErros.has(
                    `notification_page_no_messages_label.notification_page_no_messages_label_${language.id}`
                ) ||
                validationErros.has(
                    `notification_filter_btn_label.notification_filter_btn_label_${language.id}`
                ) ||
                validationErros.has(
                    `notification_delete_text.notification_delete_text_${language.id}`
                ) ||
                validationErros.has(
                    `notification_confirm_message.notification_confirm_message_${language.id}`
                ) ||
                validationErros.has(
                    `type_message_placeholder.type_message_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `main_heading.main_heading_${language.id}`
                )
            );
        },
    },
};
</script>
