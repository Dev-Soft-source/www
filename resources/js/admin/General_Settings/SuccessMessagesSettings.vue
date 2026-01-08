<template>
    <AppLayout>
        <section class="success-messages-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2 min-h-60 h-full">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Success & error messages settings
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
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`profile_update_message_${activeLanguageId}`"
                                                    >1. Profile updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`profile_update_message_${activeLanguageId}`"
                                                :id="`profile_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('profile_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'profile_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `profile_update_message.profile_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `profile_update_message.profile_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`vehicle_removed_message_${activeLanguageId}`"
                                                    >2. The vehicle has been removed</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`vehicle_removed_message_${activeLanguageId}`"
                                                :id="`vehicle_removed_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('vehicle_removed_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'vehicle_removed_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `vehicle_removed_message.vehicle_removed_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `vehicle_removed_message.vehicle_removed_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`vehicle_update_message_${activeLanguageId}`"
                                                    >3. Vehicle updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`vehicle_update_message_${activeLanguageId}`"
                                                :id="`vehicle_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('vehicle_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'vehicle_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `vehicle_update_message.vehicle_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `vehicle_update_message.vehicle_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`vehicle_add_message_${activeLanguageId}`"
                                                    >4. Vehicle added successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`vehicle_add_message_${activeLanguageId}`"
                                                :id="`vehicle_add_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('vehicle_add_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'vehicle_add_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `vehicle_add_message.vehicle_add_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `vehicle_add_message.vehicle_add_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`password_update_message_${activeLanguageId}`"
                                                    >5. Password updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`password_update_message_${activeLanguageId}`"
                                                :id="`password_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('password_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'password_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `password_update_message.password_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `password_update_message.password_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`phone_delete_message_${activeLanguageId}`"
                                                    >6. Phone number has been deleted successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`phone_delete_message_${activeLanguageId}`"
                                                :id="`phone_delete_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('phone_delete_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'phone_delete_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `phone_delete_message.phone_delete_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_delete_message.phone_delete_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`phone_add_message_${activeLanguageId}`"
                                                    >7. Phone number added successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`phone_add_message_${activeLanguageId}`"
                                                :id="`phone_add_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('phone_add_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'phone_add_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `phone_add_message.phone_add_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_add_message.phone_add_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`phone_verified_message_${activeLanguageId}`"
                                                    >8. Phone number is verified successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`phone_verified_message_${activeLanguageId}`"
                                                :id="`phone_verified_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('phone_verified_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'phone_verified_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `phone_verified_message.phone_verified_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_verified_message.phone_verified_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`license_upload_message_${activeLanguageId}`"
                                                    >9. Driver license uploaded successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`license_upload_message_${activeLanguageId}`"
                                                :id="`license_upload_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('license_upload_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'license_upload_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `license_upload_message.license_upload_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `license_upload_message.license_upload_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div v-if="isDataLoaded" class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`student_card_upload_message_${activeLanguageId}`"
                                                    >10. Student card uploaded successfully</label
                                                >
                                            </div>
                                            <editor
                                                @selectionChange="
                                                    handleSelectionChange(
                                                        language,
                                                        'student_card_upload_message'
                                                    )
                                                "
                                                :ref="`student_card_upload_message_${language.id}`"
                                                :id="`student_card_upload_message_${language.id}`"
                                                :initial-value="
                                                    form[
                                                        `student_card_upload_message`
                                                    ][
                                                        `student_card_upload_message_${language?.id}`
                                                    ]
                                                "
                                                :tinymce-script-src="tinymceScriptSrc"
                                                :init="editorConfig"
                                            />
                                            <!-- <input
                                                type="text"
                                                :name="`student_card_upload_message_${activeLanguageId}`"
                                                :id="`student_card_upload_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('student_card_upload_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'student_card_upload_message'
                                                    )
                                                "
                                            /> -->
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `student_card_upload_message.student_card_upload_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `student_card_upload_message.student_card_upload_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`card_add_message_${activeLanguageId}`"
                                                    >11. Card added successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`card_add_message_${activeLanguageId}`"
                                                :id="`card_add_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('card_add_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'card_add_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `card_add_message.card_add_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `card_add_message.card_add_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`card_primary_message_${activeLanguageId}`"
                                                    >12. Card set primary successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`card_primary_message_${activeLanguageId}`"
                                                :id="`card_primary_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('card_primary_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'card_primary_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `card_primary_message.card_primary_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `card_primary_message.card_primary_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`card_delete_message_${activeLanguageId}`"
                                                    >13. Card deleted successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`card_delete_message_${activeLanguageId}`"
                                                :id="`card_delete_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('card_delete_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'card_delete_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `card_delete_message.card_delete_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `card_delete_message.card_delete_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`ride_cancel_message_${activeLanguageId}`"
                                                    >14. Ride cancelled successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`ride_cancel_message_${activeLanguageId}`"
                                                :id="`ride_cancel_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('ride_cancel_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'ride_cancel_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `ride_cancel_message.ride_cancel_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `ride_cancel_message.ride_cancel_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`bank_save_message_${activeLanguageId}`"
                                                    >15. Bank detail successfully saved</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`bank_save_message_${activeLanguageId}`"
                                                :id="`bank_save_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('bank_save_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'bank_save_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `bank_save_message.bank_save_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `bank_save_message.bank_save_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`paypal_update_message_${activeLanguageId}`"
                                                    >16. PayPal detail successfully updated</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`paypal_update_message_${activeLanguageId}`"
                                                :id="`paypal_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('paypal_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'paypal_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `paypal_update_message.paypal_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `paypal_update_message.paypal_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`paypal_saved_message_${activeLanguageId}`"
                                                    >PayPal detail successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`paypal_saved_message_${activeLanguageId}`"
                                                :id="`paypal_saved_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('paypal_saved_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'paypal_saved_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `paypal_saved_message.paypal_saved_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `paypal_saved_message.paypal_saved_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`replied_message_${activeLanguageId}`"
                                                    >17. Replied successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`replied_message_${activeLanguageId}`"
                                                :id="`replied_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('replied_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'replied_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `replied_message.replied_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `replied_message.replied_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`ride_post_message_${activeLanguageId}`"
                                                    >18. Ride posted successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`ride_post_message_${activeLanguageId}`"
                                                :id="`ride_post_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('ride_post_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'ride_post_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `ride_post_message.ride_post_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `ride_post_message.ride_post_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`book_seat_message_${activeLanguageId}`"
                                                    >19. You have successfully booked (booking success message initial part)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`book_seat_message_${activeLanguageId}`"
                                                :id="`book_seat_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('book_seat_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'book_seat_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `book_seat_message.book_seat_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `book_seat_message.book_seat_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`book_seat_message_end_part_${activeLanguageId}`"
                                                    >20. seat on the ride (booking success message end part)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`book_seat_message_end_part_${activeLanguageId}`"
                                                :id="`book_seat_message_end_part_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('book_seat_message_end_part')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'book_seat_message_end_part'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `book_seat_message_end_part.book_seat_message_end_part_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `book_seat_message_end_part.book_seat_message_end_part_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_verified_message_${activeLanguageId}`"
                                                    >21. Your email has been verified</label
                                                >
                                            </div>
                                            <editor
                                                :tinymce-script-src="tinymceScriptSrc"
                                                :id="`email_verified_message_${activeLanguageId}`"
                                                v-model="form.email_verified_message[`email_verified_message_${activeLanguageId}`]"
                                                :init="editorConfig"
                                                placeholder=" "
                                                :name="`email_verified_message_${activeLanguageId}`"
                                                :value="
                                                    getCurrentValue(
                                                        'email_verified_message'
                                                    )
                                                "
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_verified_message'
                                                    )
                                                "
                                                
                                            ></editor>
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_verified_message.email_verified_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_verified_message.email_verified_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`continue_with_app_btn_label_${activeLanguageId}`"
                                                    >Continue with app btn label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`continue_with_app_btn_label_${activeLanguageId}`"
                                                :id="`continue_with_app_btn_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('continue_with_app_btn_label')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'continue_with_app_btn_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `continue_with_app_btn_label.continue_with_app_btn_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `continue_with_app_btn_label.continue_with_app_btn_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`create_my_profile_btn_label_${activeLanguageId}`"
                                                    >Create my profile btn label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`create_my_profile_btn_label_${activeLanguageId}`"
                                                :id="`create_my_profile_btn_label_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('create_my_profile_btn_label')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'create_my_profile_btn_label'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `create_my_profile_btn_label.create_my_profile_btn_label_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `create_my_profile_btn_label.create_my_profile_btn_label_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`profile_photo_update_message_${activeLanguageId}`"
                                                    >Profile photo is uploaded successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`profile_photo_update_message_${activeLanguageId}`"
                                                :id="`profile_photo_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('profile_photo_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'profile_photo_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `profile_photo_update_message.profile_photo_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `profile_photo_update_message.profile_photo_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`welcome_message_${activeLanguageId}`"
                                                    >22. Welcome</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`welcome_message_${activeLanguageId}`"
                                                :id="`welcome_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('welcome_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'welcome_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `welcome_message.welcome_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `welcome_message.welcome_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_sent_message_${activeLanguageId}`"
                                                    >23. we have just sent you a verification email. PLease check your inbox. If its not there, check your junk and spam folder</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`email_sent_message_${activeLanguageId}`"
                                                :id="`email_sent_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('email_sent_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_sent_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_sent_message.email_sent_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_sent_message.email_sent_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`hey_message_${activeLanguageId}`"
                                                    >24. Hey</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`hey_message_${activeLanguageId}`"
                                                :id="`hey_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('hey_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'hey_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `hey_message.hey_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `hey_message.hey_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`complete_profile_message_${activeLanguageId}`"
                                                    >25. nice to meet you Please completed your profile, it only takes a couple of minutes</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`complete_profile_message_${activeLanguageId}`"
                                                :id="`complete_profile_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('complete_profile_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'complete_profile_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `complete_profile_message.complete_profile_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `complete_profile_message.complete_profile_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_user_found_message_${activeLanguageId}`"
                                                    >26. We can not find a user with that email address</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_user_found_message_${activeLanguageId}`"
                                                :id="`no_user_found_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('no_user_found_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_user_found_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_user_found_message.no_user_found_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_user_found_message.no_user_found_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_user_match_message_${activeLanguageId}`"
                                                    >27. The provided credentials do not match our records</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_user_match_message_${activeLanguageId}`"
                                                :id="`no_user_match_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('no_user_match_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_user_match_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_user_match_message.no_user_match_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_user_match_message.no_user_match_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`image_size_error_message_${activeLanguageId}`"
                                                    >28. Can not upload image size greater than 10 MB</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`image_size_error_message_${activeLanguageId}`"
                                                :id="`image_size_error_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('image_size_error_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'image_size_error_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `image_size_error_message.image_size_error_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `image_size_error_message.image_size_error_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`incorrect_password_message_${activeLanguageId}`"
                                                    >29. Current password is incorrect</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`incorrect_password_message_${activeLanguageId}`"
                                                :id="`incorrect_password_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('incorrect_password_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'incorrect_password_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `incorrect_password_message.incorrect_password_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `incorrect_password_message.incorrect_password_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`incorrect_code_message_${activeLanguageId}`"
                                                    >30. Incorrect code</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`incorrect_code_message_${activeLanguageId}`"
                                                :id="`incorrect_code_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('incorrect_code_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'incorrect_code_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `incorrect_code_message.incorrect_code_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `incorrect_code_message.incorrect_code_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`phone_code_error_message_${activeLanguageId}`"
                                                    >Phone code error message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`phone_code_error_message_${activeLanguageId}`"
                                                :id="`phone_code_error_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('phone_code_error_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'phone_code_error_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `phone_code_error_message.phone_code_error_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_code_error_message.phone_code_error_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`already_added_card_message_${activeLanguageId}`"
                                                    >31. This card is already added</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`already_added_card_message_${activeLanguageId}`"
                                                :id="`already_added_card_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('already_added_card_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'already_added_card_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `already_added_card_message.already_added_card_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `already_added_card_message.already_added_card_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`ride_schedule_message_${activeLanguageId}`"
                                                    >32. You already have a ride scheduled for the same date and time</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`ride_schedule_message_${activeLanguageId}`"
                                                :id="`ride_schedule_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('ride_schedule_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'ride_schedule_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `ride_schedule_message.ride_schedule_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `ride_schedule_message.ride_schedule_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`female_user_message_${activeLanguageId}`"
                                                    >33. User should be a female (pink ride restriction)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`female_user_message_${activeLanguageId}`"
                                                :id="`female_user_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('female_user_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'female_user_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `female_user_message.female_user_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `female_user_message.female_user_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`star5_passenger_message_${activeLanguageId}`"
                                                    >34. Driver only wants passengers with reviews of 5 star </label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`star5_passenger_message_${activeLanguageId}`"
                                                :id="`star5_passenger_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('star5_passenger_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'star5_passenger_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `star5_passenger_message.star5_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `star5_passenger_message.star5_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`star4_passenger_message_${activeLanguageId}`"
                                                    >35. Driver only wants passengers with reviews of 4 star or higher are accepted</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`star4_passenger_message_${activeLanguageId}`"
                                                :id="`star4_passenger_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('star4_passenger_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'star4_passenger_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `star4_passenger_message.star4_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `star4_passenger_message.star4_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`star3_passenger_message_${activeLanguageId}`"
                                                    >36. Driver only wants passengers with reviews of 3 star or higher are accepted</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`star3_passenger_message_${activeLanguageId}`"
                                                :id="`star3_passenger_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('star3_passenger_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'star3_passenger_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `star3_passenger_message.star3_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `star3_passenger_message.star3_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`passenger_with_review_message_${activeLanguageId}`"
                                                    >37. Driver only wants passengers with reviews</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`passenger_with_review_message_${activeLanguageId}`"
                                                :id="`passenger_with_review_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('passenger_with_review_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'passenger_with_review_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `passenger_with_review_message.passenger_with_review_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `passenger_with_review_message.passenger_with_review_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`past_time_message_${activeLanguageId}`"
                                                    >38. Can not pick a time in the past</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`past_time_message_${activeLanguageId}`"
                                                :id="`past_time_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('past_time_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'past_time_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `past_time_message.past_time_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `past_time_message.past_time_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`past_date_message_${activeLanguageId}`"
                                                    >39. Select date first</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`past_date_message_${activeLanguageId}`"
                                                :id="`past_date_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('past_date_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'past_date_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `past_date_message.past_date_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `past_date_message.past_date_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`enter_code_message_${activeLanguageId}`"
                                                    >40. Enter code first</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`enter_code_message_${activeLanguageId}`"
                                                :id="`enter_code_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('enter_code_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'enter_code_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `enter_code_message.enter_code_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `enter_code_message.enter_code_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`url_not_allowed_message_${activeLanguageId}`"
                                                    >41. Url not allowed</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`url_not_allowed_message_${activeLanguageId}`"
                                                :id="`url_not_allowed_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('url_not_allowed_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'url_not_allowed_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `url_not_allowed_message.url_not_allowed_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `url_not_allowed_message.url_not_allowed_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_not_allowed_message_${activeLanguageId}`"
                                                    >42. Email not allowed</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`email_not_allowed_message_${activeLanguageId}`"
                                                :id="`email_not_allowed_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('email_not_allowed_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_not_allowed_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_not_allowed_message.email_not_allowed_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_not_allowed_message.email_not_allowed_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`phone_number_not_allowed_message_${activeLanguageId}`"
                                                    >43. Phone number not allowed</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`phone_number_not_allowed_message_${activeLanguageId}`"
                                                :id="`phone_number_not_allowed_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('phone_number_not_allowed_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'phone_number_not_allowed_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `phone_number_not_allowed_message.phone_number_not_allowed_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_number_not_allowed_message.phone_number_not_allowed_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`need_to_select_card_message_${activeLanguageId}`"
                                                    >44. Select card first</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`need_to_select_card_message_${activeLanguageId}`"
                                                :id="`need_to_select_card_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('need_to_select_card_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'need_to_select_card_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `need_to_select_card_message.need_to_select_card_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `need_to_select_card_message.need_to_select_card_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`paypal_not_completed_message_${activeLanguageId}`"
                                                    >45. PayPal not completed</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`paypal_not_completed_message_${activeLanguageId}`"
                                                :id="`paypal_not_completed_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('paypal_not_completed_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'paypal_not_completed_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `paypal_not_completed_message.paypal_not_completed_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `paypal_not_completed_message.paypal_not_completed_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`search_result_clear_message_${activeLanguageId}`"
                                                    >46. Search result clear</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`search_result_clear_message_${activeLanguageId}`"
                                                :id="`search_result_clear_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('search_result_clear_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'search_result_clear_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `search_result_clear_message.search_result_clear_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `search_result_clear_message.search_result_clear_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`delete_card_message_${activeLanguageId}`"
                                                    >47. Delete card</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`delete_card_message_${activeLanguageId}`"
                                                :id="`delete_card_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('delete_card_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'delete_card_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `delete_card_message.delete_card_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `delete_card_message.delete_card_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`withdraw_message_${activeLanguageId}`"
                                                    >48. Withdraw</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`withdraw_message_${activeLanguageId}`"
                                                :id="`withdraw_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('withdraw_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'withdraw_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `withdraw_message.withdraw_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `withdraw_message.withdraw_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`delete_vehicle_message_${activeLanguageId}`"
                                                    >49. Delete vehicle</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`delete_vehicle_message_${activeLanguageId}`"
                                                :id="`delete_vehicle_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('delete_vehicle_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'delete_vehicle_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `delete_vehicle_message.delete_vehicle_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `delete_vehicle_message.delete_vehicle_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`remove_driver_license_message_${activeLanguageId}`"
                                                    >50. Remove driver license</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`remove_driver_license_message_${activeLanguageId}`"
                                                :id="`remove_driver_license_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('remove_driver_license_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'remove_driver_license_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `remove_driver_license_message.remove_driver_license_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `remove_driver_license_message.remove_driver_license_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`popup_close_btn_text_${activeLanguageId}`"
                                                    >51. Close button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`popup_close_btn_text_${activeLanguageId}`"
                                                :id="`popup_close_btn_text_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('popup_close_btn_text')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'popup_close_btn_text'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `popup_close_btn_text.popup_close_btn_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `popup_close_btn_text.popup_close_btn_text_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                 
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_reason_${activeLanguageId}`"
                                                    >52. Please select 1 or more reason(s)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_reason_${activeLanguageId}`"
                                                :id="`select_reason_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('select_reason')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_reason'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_reason.select_reason_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_reason.select_reason_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`select_recommend_${activeLanguageId}`"
                                                    >53. Please select if you recommend us</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`select_recommend_${activeLanguageId}`"
                                                :id="`select_recommend_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('select_recommend')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'select_recommend'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `select_recommend.select_recommend_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `select_recommend.select_recommend_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`check_box_${activeLanguageId}`"
                                                    >54. Check the box</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`check_box_${activeLanguageId}`"
                                                :id="`check_box_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('check_box')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'check_box'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `check_box.check_box_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `check_box.check_box_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`reviewed_driver_message_${activeLanguageId}`"
                                                    >55. Reviewed a driver successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`reviewed_driver_message_${activeLanguageId}`"
                                                :id="`reviewed_driver_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('reviewed_driver_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'reviewed_driver_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `reviewed_driver_message.reviewed_driver_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `reviewed_driver_message.reviewed_driver_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`reviewed_passenger_message_${activeLanguageId}`"
                                                    >56. Reviewed a passenger successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`reviewed_passenger_message_${activeLanguageId}`"
                                                :id="`reviewed_passenger_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('reviewed_passenger_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'reviewed_passenger_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `reviewed_passenger_message.reviewed_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `reviewed_passenger_message.reviewed_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`reject_booking_message_${activeLanguageId}`"
                                                    >57. You have rejected the request successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`reject_booking_message_${activeLanguageId}`"
                                                :id="`reject_booking_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('reject_booking_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'reject_booking_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `reject_booking_message.reject_booking_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `reject_booking_message.reject_booking_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`cancel_booking_message_${activeLanguageId}`"
                                                    >58. You have successfully cancel booking in this ride</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`cancel_booking_message_${activeLanguageId}`"
                                                :id="`cancel_booking_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('cancel_booking_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'cancel_booking_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `cancel_booking_message.cancel_booking_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `cancel_booking_message.cancel_booking_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`contact_form_message_${activeLanguageId}`"
                                                    >59. Contact us form submitted</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`contact_form_message_${activeLanguageId}`"
                                                :id="`contact_form_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('contact_form_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'contact_form_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `contact_form_message.contact_form_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `contact_form_message.contact_form_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`acc_suspend_message_${activeLanguageId}`"
                                                    >60. Your account has been suspended by the admin</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`acc_suspend_message_${activeLanguageId}`"
                                                :id="`acc_suspend_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('acc_suspend_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'acc_suspend_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `acc_suspend_message.acc_suspend_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `acc_suspend_message.acc_suspend_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`seat_unavailable_message_${activeLanguageId}`"
                                                    >61. Oops, this seat is no longer available. Looks like another passenger has just booked it. We apologize for the inconvenience. Here are more rides for your route</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`seat_unavailable_message_${activeLanguageId}`"
                                                :id="`seat_unavailable_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('seat_unavailable_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'seat_unavailable_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `seat_unavailable_message.seat_unavailable_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `seat_unavailable_message.seat_unavailable_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`verified_number_message_${activeLanguageId}`"
                                                    >62. You have not verified your number yet</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`verified_number_message_${activeLanguageId}`"
                                                :id="`verified_number_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('verified_number_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'verified_number_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `verified_number_message.verified_number_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `verified_number_message.verified_number_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`seat_hold_message_${activeLanguageId}`"
                                                    >63. Seat on hold please select another seat</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`seat_hold_message_${activeLanguageId}`"
                                                :id="`seat_hold_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('seat_hold_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'seat_hold_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `seat_hold_message.seat_hold_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `seat_hold_message.seat_hold_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`verified_email_message_${activeLanguageId}`"
                                                    >64. Your email is not verified yet. Check your inbox and log in from there</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`verified_email_message_${activeLanguageId}`"
                                                :id="`verified_email_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('verified_email_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'verified_email_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `verified_email_message.verified_email_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `verified_email_message.verified_email_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_not_verify_message_${activeLanguageId}`"
                                                    >65. Email is not verified yet</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`email_not_verify_message_${activeLanguageId}`"
                                                :id="`email_not_verify_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('email_not_verify_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_not_verify_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_not_verify_message.email_not_verify_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_not_verify_message.email_not_verify_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`reset_password_message_${activeLanguageId}`"
                                                    >66. We have just sent you an email with a password reset link. Please follow the instructions in it</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`reset_password_message_${activeLanguageId}`"
                                                :id="`reset_password_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('reset_password_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'reset_password_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `reset_password_message.reset_password_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `reset_password_message.reset_password_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_update_message_${activeLanguageId}`"
                                                    >67. Email updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`email_update_message_${activeLanguageId}`"
                                                :id="`email_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('email_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_update_message.email_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_update_message.email_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`general_error_message${activeLanguageId}`"
                                                    >68. Not found (General error message)</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`general_error_message${activeLanguageId}`"
                                                :id="`general_error_message${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('general_error_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'general_error_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `general_error_message.general_error_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `general_error_message.general_error_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`current_email_not_match_${activeLanguageId}`"
                                                    >69. Current email not match updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`current_email_not_match_${activeLanguageId}`"
                                                :id="`current_email_not_match_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('current_email_not_match')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'current_email_not_match'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `current_email_not_match.current_email_not_match_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `current_email_not_match.current_email_not_match_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_not_found_message_${activeLanguageId}`"
                                                    >70. Email not found updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`email_not_found_message_${activeLanguageId}`"
                                                :id="`email_not_found_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('email_not_found_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_not_found_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_not_found_message.email_not_found_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_not_found_message.email_not_found_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`password_reset_success_message_${activeLanguageId}`"
                                                    >71. Password reset updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`password_reset_success_message_${activeLanguageId}`"
                                                :id="`password_reset_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('password_reset_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'password_reset_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `password_reset_success_message.password_reset_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `password_reset_success_message.password_reset_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`overlap_ride_message_${activeLanguageId}`"
                                                    >72. Overlap message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`overlap_ride_message_${activeLanguageId}`"
                                                :id="`overlap_ride_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('overlap_ride_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'overlap_ride_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `overlap_ride_message.overlap_ride_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `overlap_ride_message.overlap_ride_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`topup_balance_success_message_${activeLanguageId}`"
                                                    >73. TopUp balance updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`topup_balance_success_message_${activeLanguageId}`"
                                                :id="`topup_balance_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('topup_balance_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'topup_balance_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `topup_balance_success_message.topup_balance_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `topup_balance_success_message.topup_balance_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`card_expiry_message_${activeLanguageId}`"
                                                    >74. Card expiry updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`card_expiry_message_${activeLanguageId}`"
                                                :id="`card_expiry_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('card_expiry_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'card_expiry_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `card_expiry_message.card_expiry_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `card_expiry_message.card_expiry_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`verify_amount_not_match_message_${activeLanguageId}`"
                                                    >75. Verify amount not match updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`verify_amount_not_match_message_${activeLanguageId}`"
                                                :id="`verify_amount_not_match_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('verify_amount_not_match_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'verify_amount_not_match_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `verify_amount_not_match_message.verify_amount_not_match_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `verify_amount_not_match_message.verify_amount_not_match_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`bank_verified_message_${activeLanguageId}`"
                                                    >76. Bank verified updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`bank_verified_message_${activeLanguageId}`"
                                                :id="`bank_verified_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('bank_verified_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'bank_verified_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `bank_verified_message.bank_verified_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `bank_verified_message.bank_verified_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`bank_already_verified_message_${activeLanguageId}`"
                                                    >77. Bank already verified updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`bank_already_verified_message_${activeLanguageId}`"
                                                :id="`bank_already_verified_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('bank_already_verified_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'bank_already_verified_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `bank_already_verified_message.bank_already_verified_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `bank_already_verified_message.bank_already_verified_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`admin_sent_verify_amount_message_${activeLanguageId}`"
                                                    >78. Sent verify amount updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`admin_sent_verify_amount_message_${activeLanguageId}`"
                                                :id="`admin_sent_verify_amount_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('admin_sent_verify_amount_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'admin_sent_verify_amount_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `admin_sent_verify_amount_message.admin_sent_verify_amount_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `admin_sent_verify_amount_message.admin_sent_verify_amount_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`email_already_exist_message_${activeLanguageId}`"
                                                    >79. Email already updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`email_already_exist_message_${activeLanguageId}`"
                                                :id="`email_already_exist_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('email_already_exist_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'email_already_exist_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `email_already_exist_message.email_already_exist_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `email_already_exist_message.email_already_exist_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`request_expired_message_${activeLanguageId}`"
                                                    >80. Request expired updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`request_expired_message_${activeLanguageId}`"
                                                :id="`request_expired_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('request_expired_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'request_expired_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `request_expired_message.request_expired_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `request_expired_message.request_expired_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`request_accept_message_${activeLanguageId}`"
                                                    >81. Request accept updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`request_accept_message_${activeLanguageId}`"
                                                :id="`request_accept_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('request_accept_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'request_accept_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `request_accept_message.request_accept_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `request_accept_message.request_accept_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`seat_booked_message_${activeLanguageId}`"
                                                    >82. Seat booked updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`seat_booked_message_${activeLanguageId}`"
                                                :id="`seat_booked_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('seat_booked_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'seat_booked_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `seat_booked_message.seat_booked_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `seat_booked_message.seat_booked_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`seat_hold_success_message_${activeLanguageId}`"
                                                    >83. Seat hold updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`seat_hold_success_message_${activeLanguageId}`"
                                                :id="`seat_hold_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('seat_hold_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'seat_hold_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `seat_hold_success_message.seat_hold_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `seat_hold_success_message.seat_hold_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`account_closed_message_${activeLanguageId}`"
                                                    >84. Account is closed message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`account_closed_message_${activeLanguageId}`"
                                                :id="`account_closed_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('account_closed_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'account_closed_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `account_closed_message.account_closed_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `account_closed_message.account_closed_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`bank_detail_update_message_${activeLanguageId}`"
                                                    >85. Bank detail updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`bank_detail_update_message_${activeLanguageId}`"
                                                :id="`bank_detail_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('bank_detail_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'bank_detail_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `bank_detail_update_message.bank_detail_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `bank_detail_update_message.bank_detail_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`booking_not_update_message_${activeLanguageId}`"
                                                    >86. Booking not updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`booking_not_update_message_${activeLanguageId}`"
                                                :id="`booking_not_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('booking_not_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'booking_not_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `booking_not_update_message.booking_not_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `booking_not_update_message.booking_not_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div v-if="isDataLoaded" class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`closed_account_success_message_${activeLanguageId}`"
                                                    >87. You have successfully closed your account message</label
                                                >
                                            </div>
                                            <editor
                                                @selectionChange="
                                                    handleSelectionChange(
                                                        language,
                                                        'closed_account_success_message'
                                                    )
                                                "
                                                :ref="`closed_account_success_message_${language.id}`"
                                                :id="`closed_account_success_message_${language.id}`"
                                                :initial-value="
                                                    form[
                                                        `closed_account_success_message`
                                                    ][
                                                        `closed_account_success_message_${language?.id}`
                                                    ]
                                                "
                                                :tinymce-script-src="tinymceScriptSrc"
                                                :init="editorConfig"
                                            />
                                            <!-- <input
                                                type="text"
                                                :name="`closed_account_success_message_${activeLanguageId}`"
                                                :id="`closed_account_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('closed_account_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'closed_account_success_message'
                                                    )
                                                "
                                            /> -->
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `closed_account_success_message.closed_account_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `closed_account_success_message.closed_account_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`reply_already_exist_message_${activeLanguageId}`"
                                                    >88. Reply exist updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`reply_already_exist_message_${activeLanguageId}`"
                                                :id="`reply_already_exist_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('reply_already_exist_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'reply_already_exist_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `reply_already_exist_message.reply_already_exist_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `reply_already_exist_message.reply_already_exist_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`post_ride_update_message_${activeLanguageId}`"
                                                    >89. Post ride update updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`post_ride_update_message_${activeLanguageId}`"
                                                :id="`post_ride_update_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('post_ride_update_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'post_ride_update_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `post_ride_update_message.post_ride_update_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `post_ride_update_message.post_ride_update_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`license_delete_message_${activeLanguageId}`"
                                                    >90. License delete successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`license_delete_message_${activeLanguageId}`"
                                                :id="`license_delete_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('license_delete_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'license_delete_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `license_delete_message.license_delete_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `license_delete_message.license_delete_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`block_booking_message_${activeLanguageId}`"
                                                    >91. Block booking message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`block_booking_message_${activeLanguageId}`"
                                                :id="`block_booking_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('block_booking_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'block_booking_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `block_booking_message.block_booking_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `block_booking_message.block_booking_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`block_review_rating_message_${activeLanguageId}`"
                                                    >92. Block review rating message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`block_review_rating_message_${activeLanguageId}`"
                                                :id="`block_review_rating_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('block_review_rating_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'block_review_rating_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `block_review_rating_message.block_review_rating_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `block_review_rating_message.block_review_rating_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`block_post_ride_message_${activeLanguageId}`"
                                                    >93. Block post ride message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`block_post_ride_message_${activeLanguageId}`"
                                                :id="`block_post_ride_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('block_post_ride_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'block_post_ride_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `block_post_ride_message.block_post_ride_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `block_post_ride_message.block_post_ride_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`profile_photo_required_message_${activeLanguageId}`"
                                                    >93. Profile photo required</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`profile_photo_required_message_${activeLanguageId}`"
                                                :id="`profile_photo_required_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('profile_photo_required_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'profile_photo_required_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `profile_photo_required_message.profile_photo_required_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `profile_photo_required_message.profile_photo_required_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`password_token_invalid_message_${activeLanguageId}`"
                                                    >94. Password token invalid message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`password_token_invalid_message_${activeLanguageId}`"
                                                :id="`password_token_invalid_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('password_token_invalid_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'password_token_invalid_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `password_token_invalid_message.password_token_invalid_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `password_token_invalid_message.password_token_invalid_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`phone_set_default_message_${activeLanguageId}`"
                                                    >95. Phone set default message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`phone_set_default_message_${activeLanguageId}`"
                                                :id="`phone_set_default_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('phone_set_default_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'phone_set_default_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `phone_set_default_message.phone_set_default_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `phone_set_default_message.phone_set_default_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`verfification_code_sent_message_${activeLanguageId}`"
                                                    >96. Verification code sent message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`verfification_code_sent_message_${activeLanguageId}`"
                                                :id="`verfification_code_sent_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('verfification_code_sent_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'verfification_code_sent_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `verfification_code_sent_message.verfification_code_sent_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `verfification_code_sent_message.verfification_code_sent_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`removed_passenger_message_${activeLanguageId}`"
                                                    >97. Removed passenger message updated successfully</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`removed_passenger_message_${activeLanguageId}`"
                                                :id="`removed_passenger_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('removed_passenger_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'removed_passenger_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `removed_passenger_message.removed_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `removed_passenger_message.removed_passenger_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`popup_submit_btn_text_${activeLanguageId}`"
                                                    >98. Submit button label</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`popup_submit_btn_text_${activeLanguageId}`"
                                                :id="`popup_submit_btn_text_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('popup_submit_btn_text')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'popup_submit_btn_text'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `popup_submit_btn_text.popup_submit_btn_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `popup_submit_btn_text.popup_submit_btn_text_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`reward_not_found_message_${activeLanguageId}`"
                                                    >99. Reward not found message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`reward_not_found_message_${activeLanguageId}`"
                                                :id="`reward_not_found_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('reward_not_found_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'reward_not_found_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `reward_not_found_message.reward_not_found_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `reward_not_found_message.reward_not_found_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`claim_reward_student_success_message_${activeLanguageId}`"
                                                    >100. Claim reward student success message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`claim_reward_student_success_message_${activeLanguageId}`"
                                                :id="`claim_reward_student_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('claim_reward_student_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'claim_reward_student_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `claim_reward_student_success_message.claim_reward_student_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `claim_reward_student_success_message.claim_reward_student_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`claim_reward_driver_success_message_${activeLanguageId}`"
                                                    >101. Claim reward driver success message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`claim_reward_driver_success_message_${activeLanguageId}`"
                                                :id="`claim_reward_driver_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('claim_reward_driver_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'claim_reward_driver_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `claim_reward_driver_success_message.claim_reward_driver_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `claim_reward_driver_success_message.claim_reward_driver_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`coffee_wall_heading_success_message_${activeLanguageId}`"
                                                    >102. Coffee wall payment successful heading</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`coffee_wall_heading_success_message_${activeLanguageId}`"
                                                :id="`coffee_wall_heading_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('coffee_wall_heading_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'coffee_wall_heading_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `coffee_wall_heading_success_message.coffee_wall_heading_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `coffee_wall_heading_success_message.coffee_wall_heading_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`coffee_wall_text_success_message_${activeLanguageId}`"
                                                    >103. Coffee wall payment success message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`coffee_wall_text_success_message_${activeLanguageId}`"
                                                :id="`coffee_wall_text_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('coffee_wall_text_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'coffee_wall_text_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `coffee_wall_text_success_message.coffee_wall_text_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `coffee_wall_text_success_message.coffee_wall_text_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`too_many_secured_cash_attempt_message_${activeLanguageId}`"
                                                    >104. Too many secured-cash attempt message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`too_many_secured_cash_attempt_message_${activeLanguageId}`"
                                                :id="`too_many_secured_cash_attempt_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('too_many_secured_cash_attempt_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'too_many_secured_cash_attempt_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `too_many_secured_cash_attempt_message.too_many_secured_cash_attempt_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `too_many_secured_cash_attempt_message.too_many_secured_cash_attempt_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`secured_cash_success_message_${activeLanguageId}`"
                                                    >104. Too many secured-cash attempt message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`secured_cash_success_message_${activeLanguageId}`"
                                                :id="`secured_cash_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('secured_cash_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'secured_cash_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `secured_cash_success_message.secured_cash_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `secured_cash_success_message.secured_cash_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`admin_block_account_message_${activeLanguageId}`"
                                                    >105. Admin block account message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`admin_block_account_message_${activeLanguageId}`"
                                                :id="`admin_block_account_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('admin_block_account_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'admin_block_account_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `admin_block_account_message.admin_block_account_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `admin_block_account_message.admin_block_account_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`not_allowed_post_ride_state_wise_message_${activeLanguageId}`"
                                                    >106. Not allowed post ride state wise message</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`not_allowed_post_ride_state_wise_message_${activeLanguageId}`"
                                                :id="`not_allowed_post_ride_state_wise_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('not_allowed_post_ride_state_wise_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'not_allowed_post_ride_state_wise_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `not_allowed_post_ride_state_wise_message.not_allowed_post_ride_state_wise_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `not_allowed_post_ride_state_wise_message.not_allowed_post_ride_state_wise_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                     <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`add_your_phone_${activeLanguageId}`"
                                                    >107. Add your phone number text</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`add_your_phone_${activeLanguageId}`"
                                                :id="`add_your_phone_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('add_your_phone')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'add_your_phone'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `add_your_phone.add_your_phone_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `add_your_phone.add_your_phone_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`cancel_noshow_are_you_sure_${activeLanguageId}`"
                                                    >108. Are you sure you want to cancel arbitration</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`cancel_noshow_are_you_sure_${activeLanguageId}`"
                                                :id="`cancel_noshow_are_you_sure_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('cancel_noshow_are_you_sure')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'cancel_noshow_are_you_sure'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `cancel_noshow_are_you_sure.cancel_noshow_are_you_sure_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `cancel_noshow_are_you_sure.cancel_noshow_are_you_sure_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`cancel_noshow_take_me_back_${activeLanguageId}`"
                                                    >109. Cancel arbitration, No take me back</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`cancel_noshow_take_me_back_${activeLanguageId}`"
                                                :id="`cancel_noshow_take_me_back_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('cancel_noshow_take_me_back')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'cancel_noshow_take_me_back'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `cancel_noshow_take_me_back.cancel_noshow_take_me_back_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `cancel_noshow_take_me_back.cancel_noshow_take_me_back_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`confirm_cancel_noshow_${activeLanguageId}`"
                                                    >110. Confirm cancel arbitration</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`confirm_cancel_noshow_${activeLanguageId}`"
                                                :id="`confirm_cancel_noshow_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('confirm_cancel_noshow')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'confirm_cancel_noshow'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `confirm_cancel_noshow.confirm_cancel_noshow_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `confirm_cancel_noshow.confirm_cancel_noshow_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`popup_signup_btn_text_${activeLanguageId}`"
                                                    >111. Signup button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`popup_signup_btn_text_${activeLanguageId}`"
                                                :id="`popup_signup_btn_text_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('popup_signup_btn_text')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'popup_signup_btn_text'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `popup_signup_btn_text.popup_signup_btn_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `popup_signup_btn_text.popup_signup_btn_text_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`popup_login_btn_text_${activeLanguageId}`"
                                                    >112. Login button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`popup_login_btn_text_${activeLanguageId}`"
                                                :id="`popup_login_btn_text_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('popup_login_btn_text')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'popup_login_btn_text'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `popup_login_btn_text.popup_login_btn_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `popup_login_btn_text.popup_login_btn_text_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    
                                    
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`arbitration_success_message_${activeLanguageId}`"
                                                    >113. Arbitration request submitted</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`arbitration_success_message_${activeLanguageId}`"
                                                :id="`arbitration_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('arbitration_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'arbitration_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `arbitration_success_message.arbitration_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `arbitration_success_message.arbitration_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`no_show_driver_button_${activeLanguageId}`"
                                                    >114. No show driver button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`no_show_driver_button_${activeLanguageId}`"
                                                :id="`no_show_driver_button_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('no_show_driver_button')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'no_show_driver_button'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `no_show_driver_button.no_show_driver_button_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `no_show_driver_button.no_show_driver_button_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                               
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`revert_arbitration_button_${activeLanguageId}`"
                                                    >115. Cancel arbitration button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`revert_arbitration_button_${activeLanguageId}`"
                                                :id="`revert_arbitration_button_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('revert_arbitration_button')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'revert_arbitration_button'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `revert_arbitration_button.revert_arbitration_button_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `revert_arbitration_button.revert_arbitration_button_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    
                                    
                                    
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`cancel_button_${activeLanguageId}`"
                                                    >116. Cancel button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`cancel_button_${activeLanguageId}`"
                                                :id="`cancel_button_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('cancel_button')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'cancel_button'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `cancel_button.cancel_button_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `cancel_button.cancel_button_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                     <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`delete_button_${activeLanguageId}`"
                                                    >117. Delete button</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`delete_button_${activeLanguageId}`"
                                                :id="`delete_button_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('delete_button')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'delete_button'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `delete_button.delete_button_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `delete_button.delete_button_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`ride_dead_time_text_${activeLanguageId}`"
                                                    >118. Ride dead time limit text</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`ride_dead_time_text_${activeLanguageId}`"
                                                :id="`ride_dead_time_text_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('ride_dead_time_text')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'ride_dead_time_text'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `ride_dead_time_text.ride_dead_time_text_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `ride_dead_time_text.ride_dead_time_text_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`payout_request_success_message_${activeLanguageId}`"
                                                    >119. Payout request send successfully to admin</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`payout_request_success_message_${activeLanguageId}`"
                                                :id="`payout_request_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('payout_request_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'payout_request_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `payout_request_success_message.payout_request_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `payout_request_success_message.payout_request_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`suspended_account_phone_number_message_${activeLanguageId}`"
                                                    >120. This phone number belongs to a suspended or deactivated account</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`suspended_account_phone_number_message_${activeLanguageId}`"
                                                :id="`suspended_account_phone_number_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('suspended_account_phone_number_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'suspended_account_phone_number_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `suspended_account_phone_number_message.suspended_account_phone_number_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `suspended_account_phone_number_message.suspended_account_phone_number_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`booking_request_success_message_${activeLanguageId}`"
                                                    >121. Your request has been successfully sent to the driver</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`booking_request_success_message_${activeLanguageId}`"
                                                :id="`booking_request_success_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('booking_request_success_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'booking_request_success_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `booking_request_success_message.booking_request_success_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `booking_request_success_message.booking_request_success_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label
                                                    :for="`login_before_booking_message_${activeLanguageId}`"
                                                    >121. You must have to log in to your account before booking</label
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                :name="`login_before_booking_message_${activeLanguageId}`"
                                                :id="`login_before_booking_message_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" "
                                                :value="getCurrentValue('login_before_booking_message')"
                                                @input="
                                                    handleInput(
                                                        $event.target.value,
                                                        language,
                                                        'login_before_booking_message'
                                                    )
                                                "
                                            />
                                        </div>
                                        <p
                                            class="mt-2 text-sm text-red-400"
                                            v-if="
                                                validationErros.has(
                                                    `login_before_booking_message.login_before_booking_message_${activeLanguageId}`
                                                )
                                            "
                                            v-text="
                                                validationErros.get(
                                                    `login_before_booking_message.login_before_booking_message_${activeLanguageId}`
                                                )
                                            "
                                        ></p>
                                    </div>
                                </div>
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
            isDataLoaded: false,
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
                            this.handleInput("", language, "profile_update_message");
                            this.handleInput("", language, "vehicle_removed_message");
                            this.handleInput("", language, "vehicle_update_message");
                            this.handleInput("", language, "vehicle_add_message");
                            this.handleInput("", language, "password_update_message");
                            this.handleInput("", language, "phone_delete_message");
                            this.handleInput("", language, "phone_add_message");
                            this.handleInput("", language, "phone_verified_message");
                            this.handleInput("", language, "license_upload_message");
                            this.handleInput("", language, "student_card_upload_message");
                            this.handleInput("", language, "card_add_message");
                            this.handleInput("", language, "card_primary_message");
                            this.handleInput("", language, "card_delete_message");
                            this.handleInput("", language, "ride_cancel_message");
                            this.handleInput("", language, "bank_save_message");
                            this.handleInput("", language, "paypal_update_message");
                            this.handleInput("", language, "paypal_saved_message");
                            this.handleInput("", language, "replied_message");
                            this.handleInput("", language, "ride_post_message");
                            this.handleInput("", language, "book_seat_message");
                            this.handleInput("", language, "book_seat_message_end_part");
                            this.handleInput("", language, "email_verified_message");
                            this.handleInput("", language, "continue_with_app_btn_label");
                            this.handleInput("", language, "create_my_profile_btn_label");
                            this.handleInput("", language, "profile_photo_update_message");
                            this.handleInput("", language, "welcome_message");
                            this.handleInput("", language, "email_sent_message");
                            this.handleInput("", language, "hey_message");
                            this.handleInput("", language, "complete_profile_message");
                            this.handleInput("", language, "no_user_found_message");
                            this.handleInput("", language, "no_user_match_message");
                            this.handleInput("", language, "image_size_error_message");
                            this.handleInput("", language, "incorrect_password_message");
                            this.handleInput("", language, "incorrect_code_message");
                            this.handleInput("", language, "phone_code_error_message");
                            this.handleInput("", language, "already_added_card_message");
                            this.handleInput("", language, "ride_schedule_message");
                            this.handleInput("", language, "female_user_message");
                            this.handleInput("", language, "star5_passenger_message");
                            this.handleInput("", language, "star4_passenger_message");
                            this.handleInput("", language, "star3_passenger_message");
                            this.handleInput("", language, "passenger_with_review_message");
                            this.handleInput("", language, "past_time_message");
                            this.handleInput("", language, "paypal_not_completed_message");
                            this.handleInput("", language, "search_result_clear_message");
                            this.handleInput("", language, "delete_card_message");
                            this.handleInput("", language, "withdraw_message");
                            this.handleInput("", language, "delete_vehicle_message");
                            this.handleInput("", language, "remove_driver_license_message");
                            this.handleInput("", language, "popup_close_btn_text");
                            this.handleInput("", language, "popup_signup_btn_text");
                            this.handleInput("", language, "popup_login_btn_text");
                            this.handleInput("", language, "arbitration_success_message");
                            this.handleInput("", language, "revert_arbitration_button");
                            this.handleInput("", language, "cancel_button");
                            this.handleInput("", language, "delete_button");
                            this.handleInput("", language, "ride_dead_time_text");
                            this.handleInput("", language, "payout_request_success_message");
                            this.handleInput("", language, "suspended_account_phone_number_message");
                            this.handleInput("", language, "booking_request_success_message");
                            this.handleInput("", language, "login_before_booking_message");
                            this.handleInput("", language, "no_show_driver_button");
                            this.handleInput("", language, "no_show_passanger_button");
                            this.handleInput("", language, "select_reason");
                            this.handleInput("", language, "select_recommend");
                            this.handleInput("", language, "check_box");
                            this.handleInput("", language, "reviewed_driver_message");
                            this.handleInput("", language, "reviewed_passenger_message");
                            this.handleInput("", language, "reject_booking_message");
                            this.handleInput("", language, "cancel_booking_message");
                            this.handleInput("", language, "contact_form_message");
                            this.handleInput("", language, "acc_suspend_message");
                            this.handleInput("", language, "seat_unavailable_message");
                            this.handleInput("", language, "verified_number_message");
                            this.handleInput("", language, "seat_hold_message");
                            this.handleInput("", language, "verified_email_message");
                            this.handleInput("", language, "email_not_verify_message");
                            this.handleInput("", language, "reset_password_message");
                            this.handleInput("", language, "email_update_message");
                            this.handleInput("", language, "need_to_select_card_message");
                            this.handleInput("", language, "phone_number_not_allowed_message");
                            this.handleInput("", language, "email_not_allowed_message");
                            this.handleInput("", language, "url_not_allowed_message");
                            this.handleInput("", language, "enter_code_message");
                            this.handleInput("", language, "past_date_message");
                            this.handleInput("", language, "general_error_message");
                            this.handleInput("", language, "current_email_not_match");
                            this.handleInput("", language, "email_not_found_message");
                            this.handleInput("", language, "password_reset_success_message");
                            this.handleInput("", language, "overlap_ride_message");
                            this.handleInput("", language, "topup_balance_success_message");
                            this.handleInput("", language, "card_expiry_message");
                            this.handleInput("", language, "verify_amount_not_match_message");
                            this.handleInput("", language, "bank_verified_message");
                            this.handleInput("", language, "bank_already_verified_message");
                            this.handleInput("", language, "admin_sent_verify_amount_message");
                            this.handleInput("", language, "email_already_exist_message");
                            this.handleInput("", language, "request_expired_message");
                            this.handleInput("", language, "request_accept_message");
                            this.handleInput("", language, "seat_booked_message");
                            this.handleInput("", language, "seat_hold_success_message");
                            this.handleInput("", language, "account_closed_message");
                            this.handleInput("", language, "bank_detail_update_message");
                            this.handleInput("", language, "booking_not_update_message");
                            this.handleInput("", language, "closed_account_success_message");
                            this.handleInput("", language, "reply_already_exist_message");
                            this.handleInput("", language, "post_ride_update_message");
                            this.handleInput("", language, "license_delete_message");
                            this.handleInput("", language, "block_booking_message");
                            this.handleInput("", language, "block_review_rating_message");
                            this.handleInput("", language, "block_post_ride_message");
                            this.handleInput("", language, "profile_photo_required_message");
                            this.handleInput("", language, "password_token_invalid_message");
                            this.handleInput("", language, "phone_set_default_message");
                            this.handleInput("", language, "verfification_code_sent_message");
                            this.handleInput("", language, "removed_passenger_message");
                            this.handleInput("", language, "popup_submit_btn_text");
                            this.handleInput("", language, "reward_not_found_message");
                            this.handleInput("", language, "claim_reward_student_success_message");
                            this.handleInput("", language, "claim_reward_driver_success_message");
                            this.handleInput("", language, "coffee_wall_heading_success_message");
                            this.handleInput("", language, "coffee_wall_text_success_message");
                            this.handleInput("", language, "too_many_secured_cash_attempt_message");
                            this.handleInput("", language, "secured_cash_success_message");
                            this.handleInput("", language, "admin_block_account_message");
                            this.handleInput("", language, "not_allowed_post_ride_state_wise_message");
                            this.handleInput("", language, "add_your_phone");
                            this.handleInput("", language, "cancel_noshow_are_you_sure");
                            this.handleInput("", language, "cancel_noshow_take_me_back");
                            this.handleInput("", language, "confirm_cancel_noshow");
                        });
                        this.fetchSuccessMessagesSetting();
                    }
                });
        },
        fetchSuccessMessagesSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-success-messages-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let success_messages_setting_detail =
                            res?.data?.data?.success_messages_setting_detail || [];
                        success_messages_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.profile_update_message,
                                setting?.language,
                                "profile_update_message"
                            );
                            this.handleInput(
                                setting?.vehicle_removed_message,
                                setting?.language,
                                "vehicle_removed_message"
                            );
                            this.handleInput(
                                setting?.vehicle_update_message,
                                setting?.language,
                                "vehicle_update_message"
                            );
                            this.handleInput(
                                setting?.vehicle_add_message,
                                setting?.language,
                                "vehicle_add_message"
                            );
                            this.handleInput(
                                setting?.password_update_message,
                                setting?.language,
                                "password_update_message"
                            );
                            this.handleInput(
                                setting?.phone_delete_message,
                                setting?.language,
                                "phone_delete_message"
                            );
                            this.handleInput(
                                setting?.phone_add_message,
                                setting?.language,
                                "phone_add_message"
                            );
                            this.handleInput(
                                setting?.phone_verified_message,
                                setting?.language,
                                "phone_verified_message"
                            );
                            this.handleInput(
                                setting?.license_upload_message,
                                setting?.language,
                                "license_upload_message"
                            );
                            this.handleInput(
                                setting?.student_card_upload_message,
                                setting?.language,
                                "student_card_upload_message"
                            );
                            this.handleInput(
                                setting?.card_add_message,
                                setting?.language,
                                "card_add_message"
                            );
                            this.handleInput(
                                setting?.card_primary_message,
                                setting?.language,
                                "card_primary_message"
                            );
                            this.handleInput(
                                setting?.card_delete_message,
                                setting?.language,
                                "card_delete_message"
                            );
                            this.handleInput(
                                setting?.ride_cancel_message,
                                setting?.language,
                                "ride_cancel_message"
                            );
                            this.handleInput(
                                setting?.bank_save_message,
                                setting?.language,
                                "bank_save_message"
                            );
                            this.handleInput(
                                setting?.paypal_update_message,
                                setting?.language,
                                "paypal_update_message"
                            );
                            this.handleInput(
                                setting?.paypal_saved_message,
                                setting?.language,
                                "paypal_saved_message"
                            );
                            this.handleInput(
                                setting?.replied_message,
                                setting?.language,
                                "replied_message"
                            );
                            this.handleInput(
                                setting?.ride_post_message,
                                setting?.language,
                                "ride_post_message"
                            );
                            this.handleInput(
                                setting?.book_seat_message,
                                setting?.language,
                                "book_seat_message"
                            );
                            this.handleInput(
                                setting?.book_seat_message_end_part,
                                setting?.language,
                                "book_seat_message_end_part"
                            );
                            this.handleInput(
                                setting?.email_verified_message,
                                setting?.language,
                                "email_verified_message"
                            );
                            this.handleInput(
                                setting?.continue_with_app_btn_label,
                                setting?.language,
                                "continue_with_app_btn_label"
                            );
                            this.handleInput(
                                setting?.create_my_profile_btn_label,
                                setting?.language,
                                "create_my_profile_btn_label"
                            );
                            this.handleInput(
                                setting?.profile_photo_update_message,
                                setting?.language,
                                "profile_photo_update_message"
                            );
                            this.handleInput(
                                setting?.welcome_message,
                                setting?.language,
                                "welcome_message"
                            );
                            this.handleInput(
                                setting?.email_sent_message,
                                setting?.language,
                                "email_sent_message"
                            );
                            this.handleInput(
                                setting?.hey_message,
                                setting?.language,
                                "hey_message"
                            );
                            this.handleInput(
                                setting?.complete_profile_message,
                                setting?.language,
                                "complete_profile_message"
                            );
                            this.handleInput(
                                setting?.no_user_found_message,
                                setting?.language,
                                "no_user_found_message"
                            );
                            this.handleInput(
                                setting?.no_user_match_message,
                                setting?.language,
                                "no_user_match_message"
                            );
                            this.handleInput(
                                setting?.image_size_error_message,
                                setting?.language,
                                "image_size_error_message"
                            );
                            this.handleInput(
                                setting?.incorrect_password_message,
                                setting?.language,
                                "incorrect_password_message"
                            );
                            this
                            .handleInput(
                                setting?.incorrect_code_message,
                                setting?.language,
                                "incorrect_code_message"
                            );

                            this
                            .handleInput(
                                setting?.phone_code_error_message,
                                setting?.language,
                                "phone_code_error_message"
                            );

                            this.handleInput(
                                setting?.too_many_secured_cash_attempt_message,
                                setting?.language,
                                "too_many_secured_cash_attempt_message"
                            );
                            this.handleInput(
                                setting?.secured_cash_success_message,
                                setting?.language,
                                "secured_cash_success_message"
                            );
                            this.handleInput(
                                setting?.not_allowed_post_ride_state_wise_message,
                                setting?.language,
                                "not_allowed_post_ride_state_wise_message"
                            );
                            
                            this.handleInput(
                                setting?.add_your_phone,
                                setting?.language,
                                "add_your_phone"
                            );
                              this.handleInput(
                                setting?.cancel_noshow_are_you_sure,
                                setting?.language,
                                "cancel_noshow_are_you_sure"
                            );
                              this.handleInput(
                                setting?.cancel_noshow_take_me_back,
                                setting?.language,
                                "cancel_noshow_take_me_back"
                            );
                              this.handleInput(
                                setting?.confirm_cancel_noshow,
                                setting?.language,
                                "confirm_cancel_noshow"
                            );
                            this.handleInput(
                                setting?.admin_block_account_message,
                                setting?.language,
                                "admin_block_account_message"
                            );
                            this.handleInput(
                                setting?.already_added_card_message,
                                setting?.language,
                                "already_added_card_message"
                            );
                            this.handleInput(
                                setting?.ride_schedule_message,
                                setting?.language,
                                "ride_schedule_message"
                            );
                            this.handleInput(
                                setting?.female_user_message,
                                setting?.language,
                                "female_user_message"
                            );
                            this.handleInput(
                                setting?.star5_passenger_message,
                                setting?.language,
                                "star5_passenger_message"
                            );
                            this.handleInput(
                                setting?.star4_passenger_message,
                                setting?.language,
                                "star4_passenger_message"
                            );
                            this.handleInput(
                                setting?.star3_passenger_message,
                                setting?.language,
                                "star3_passenger_message"
                            );
                            this.handleInput(
                                setting?.passenger_with_review_message,
                                setting?.language,
                                "passenger_with_review_message"
                            );
                            this.handleInput(
                                setting?.past_time_message,
                                setting?.language,
                                "past_time_message"
                            );
                            this.handleInput(
                                setting?.paypal_not_completed_message,
                                setting?.language,
                                "paypal_not_completed_message"
                            );
                            this.handleInput(
                                setting?.search_result_clear_message,
                                setting?.language,
                                "search_result_clear_message"
                            );
                            this.handleInput(
                                setting?.delete_card_message,
                                setting?.language,
                                "delete_card_message"
                            );
                            this.handleInput(
                                setting?.withdraw_message,
                                setting?.language,
                                "withdraw_message"
                            );
                            this.handleInput(
                                setting?.delete_vehicle_message,
                                setting?.language,
                                "delete_vehicle_message"
                            );
                            this.handleInput(
                                setting?.remove_driver_license_message,
                                setting?.language,
                                "remove_driver_license_message"
                            );
                            this.handleInput(
                                setting?.popup_close_btn_text,
                                setting?.language,
                                "popup_close_btn_text"
                            );
                            this.handleInput(
                                setting?.popup_signup_btn_text,
                                setting?.language,
                                "popup_signup_btn_text"
                            );
                            this.handleInput(
                                setting?.popup_login_btn_text,
                                setting?.language,
                                "popup_login_btn_text"
                            );
                            this.handleInput(
                                setting?.arbitration_success_message,
                                setting?.language,
                                "arbitration_success_message"
                            );
                            this.handleInput(
                                setting?.no_show_driver_button,
                                setting?.language,
                                "no_show_driver_button"
                            );
                            
                            this.handleInput(
                                setting?.no_show_passanger_button,
                                setting?.language,
                                "no_show_passanger_button"
                            );
                            
                            this.handleInput(
                                setting?.revert_arbitration_button,
                                setting?.language,
                                "revert_arbitration_button"
                            );
                            
                            this.handleInput(
                                setting?.cancel_button,
                                setting?.language,
                                "cancel_button"
                            );

                            this.handleInput(
                                setting?.delete_button,
                                setting?.language,
                                "delete_button"
                            );
                            this.handleInput(
                                setting?.ride_dead_time_text,
                                setting?.language,
                                "ride_dead_time_text"
                            );
                            this.handleInput(
                                setting?.payout_request_success_message,
                                setting?.language,
                                "payout_request_success_message"
                            );
                            this.handleInput(
                                setting?.suspended_account_phone_number_message,
                                setting?.language,
                                "suspended_account_phone_number_message"
                            );
                            this.handleInput(
                                setting?.booking_request_success_message,
                                setting?.language,
                                "booking_request_success_message"
                            );
                            this.handleInput(
                                setting?.login_before_booking_message,
                                setting?.language,
                                "login_before_booking_message"
                            );
                            this.handleInput(
                                setting?.select_reason,
                                setting?.language,
                                "select_reason"
                            );
                            this.handleInput(
                                setting?.select_recommend,
                                setting?.language,
                                "select_recommend"
                            );
                            this.handleInput(
                                setting?.check_box,
                                setting?.language,
                                "check_box"
                            );
                            this.handleInput(
                                setting?.reviewed_driver_message,
                                setting?.language,
                                "reviewed_driver_message"
                            );
                            this.handleInput(
                                setting?.reviewed_passenger_message,
                                setting?.language,
                                "reviewed_passenger_message"
                            );
                            this.handleInput(
                                setting?.reject_booking_message,
                                setting?.language,
                                "reject_booking_message"
                            );
                            this.handleInput(
                                setting?.cancel_booking_message,
                                setting?.language,
                                "cancel_booking_message"
                            );
                            this.handleInput(
                                setting?.contact_form_message,
                                setting?.language,
                                "contact_form_message"
                            );
                            this.handleInput(
                                setting?.acc_suspend_message,
                                setting?.language,
                                "acc_suspend_message"
                            );
                            this.handleInput(
                                setting?.seat_unavailable_message,
                                setting?.language,
                                "seat_unavailable_message"
                            );
                            this.handleInput(
                                setting?.verified_number_message,
                                setting?.language,
                                "verified_number_message"
                            );
                            this.handleInput(
                                setting?.seat_hold_message,
                                setting?.language,
                                "seat_hold_message"
                            );
                            this.handleInput(
                                setting?.verified_email_message,
                                setting?.language,
                                "verified_email_message"
                            );
                            this.handleInput(
                                setting?.email_not_verify_message,
                                setting?.language,
                                "email_not_verify_message"
                            );
                            this.handleInput(
                                setting?.reset_password_message,
                                setting?.language,
                                "reset_password_message"
                            );
                            this.handleInput(
                                setting?.email_update_message,
                                setting?.language,
                                "email_update_message"
                            );
                            this.handleInput(
                                setting?.need_to_select_card_message,
                                setting?.language,
                                "need_to_select_card_message"
                            );
                            this.handleInput(
                                setting?.phone_number_not_allowed_message,
                                setting?.language,
                                "phone_number_not_allowed_message"
                            );
                            this.handleInput(
                                setting?.email_not_allowed_message,
                                setting?.language,
                                "email_not_allowed_message"
                            );
                            this.handleInput(
                                setting?.url_not_allowed_message,
                                setting?.language,
                                "url_not_allowed_message"
                            );
                            this.handleInput(
                                setting?.enter_code_message,
                                setting?.language,
                                "enter_code_message"
                            );
                            this.handleInput(
                                setting?.past_date_message,
                                setting?.language,
                                "past_date_message"
                            );
                            this.handleInput(
                                setting?.general_error_message,
                                setting?.language,
                                "general_error_message"
                            );
                            this.handleInput(
                                setting?.current_email_not_match,
                                setting?.language,
                                "current_email_not_match"
                            );
                            this.handleInput(
                                setting?.email_not_found_message,
                                setting?.language,
                                "email_not_found_message"
                            );
                            this.handleInput(
                                setting?.password_reset_success_message,
                                setting?.language,
                                "password_reset_success_message"
                            );
                            this.handleInput(
                                setting?.overlap_ride_message,
                                setting?.language,
                                "overlap_ride_message"
                            );
                            this.handleInput(
                                setting?.topup_balance_success_message,
                                setting?.language,
                                "topup_balance_success_message"
                            );
                            this.handleInput(
                                setting?.card_expiry_message,
                                setting?.language,
                                "card_expiry_message"
                            );
                            this.handleInput(
                                setting?.verify_amount_not_match_message,
                                setting?.language,
                                "verify_amount_not_match_message"
                            );
                            this.handleInput(
                                setting?.bank_verified_message,
                                setting?.language,
                                "bank_verified_message"
                            );
                            this.handleInput(
                                setting?.bank_already_verified_message,
                                setting?.language,
                                "bank_already_verified_message"
                            );
                            this.handleInput(
                                setting?.admin_sent_verify_amount_message,
                                setting?.language,
                                "admin_sent_verify_amount_message"
                            );
                            this.handleInput(
                                setting?.email_already_exist_message,
                                setting?.language,
                                "email_already_exist_message"
                            );
                            this.handleInput(
                                setting?.request_expired_message,
                                setting?.language,
                                "request_expired_message"
                            );
                            this.handleInput(
                                setting?.request_accept_message,
                                setting?.language,
                                "request_accept_message"
                            );
                            this.handleInput(
                                setting?.seat_booked_message,
                                setting?.language,
                                "seat_booked_message"
                            );
                            this.handleInput(
                                setting?.seat_hold_success_message,
                                setting?.language,
                                "seat_hold_success_message"
                            );
                            this.handleInput(
                                setting?.account_closed_message,
                                setting?.language,
                                "account_closed_message"
                            );
                            this.handleInput(
                                setting?.bank_detail_update_message,
                                setting?.language,
                                "bank_detail_update_message"
                            );
                            this.handleInput(
                                setting?.booking_not_update_message,
                                setting?.language,
                                "booking_not_update_message"
                            );
                            this.handleInput(
                                setting?.closed_account_success_message,
                                setting?.language,
                                "closed_account_success_message"
                            );
                            this.handleInput(
                                setting?.reply_already_exist_message,
                                setting?.language,
                                "reply_already_exist_message"
                            );
                            this.handleInput(
                                setting?.post_ride_update_message,
                                setting?.language,
                                "post_ride_update_message"
                            );
                            this.handleInput(
                                setting?.license_delete_message,
                                setting?.language,
                                "license_delete_message"
                            );
                            this.handleInput(
                                setting?.block_booking_message,
                                setting?.language,
                                "block_booking_message"
                            );
                            this.handleInput(
                                setting?.block_review_rating_message,
                                setting?.language,
                                "block_review_rating_message"
                            );
                            this.handleInput(
                                setting?.block_post_ride_message,
                                setting?.language,
                                "block_post_ride_message"
                            );
                            this.handleInput(
                                setting?.profile_photo_required_message,
                                setting?.language,
                                "profile_photo_required_message"
                            );
                            this.handleInput(
                                setting?.password_token_invalid_message,
                                setting?.language,
                                "password_token_invalid_message"
                            );
                            this.handleInput(
                                setting?.phone_set_default_message,
                                setting?.language,
                                "phone_set_default_message"
                            );
                            this.handleInput(
                                setting?.verfification_code_sent_message,
                                setting?.language,
                                "verfification_code_sent_message"
                            );
                            this.handleInput(
                                setting?.removed_passenger_message,
                                setting?.language,
                                "removed_passenger_message"
                            );
                            this.handleInput(
                                setting?.popup_submit_btn_text,
                                setting?.language,
                                "popup_submit_btn_text"
                            );
                            this.handleInput(
                                setting?.reward_not_found_message,
                                setting?.language,
                                "reward_not_found_message"
                            );
                            this.handleInput(
                                setting?.claim_reward_student_success_message,
                                setting?.language,
                                "claim_reward_student_success_message"
                            );
                            this.handleInput(
                                setting?.claim_reward_driver_success_message,
                                setting?.language,
                                "claim_reward_driver_success_message"
                            );
                            this.handleInput(
                                setting?.coffee_wall_heading_success_message,
                                setting?.language,
                                "coffee_wall_heading_success_message"
                            );
                            this.handleInput(
                                setting?.coffee_wall_text_success_message,
                                setting?.language,
                                "coffee_wall_text_success_message"
                            );
                            this.isDataLoaded = 1;
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-success-messages-setting`,
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
                validationErros.has(`profile_update_message.profile_update_message_${language.id}`) ||
                validationErros.has(`vehicle_update_message.vehicle_update_message_${language.id}`) ||
                validationErros.has(`vehicle_add_message.vehicle_add_message_${language.id}`) ||
                validationErros.has(`password_update_message.password_update_message_${language.id}`) ||
                validationErros.has(`phone_delete_message.phone_delete_message_${language.id}`) ||
                validationErros.has(`phone_add_message.phone_add_message_${language.id}`) ||
                validationErros.has(`phone_verified_message.phone_verified_message_${language.id}`) ||
                validationErros.has(`license_upload_message.license_upload_message_${language.id}`) ||
                validationErros.has(`student_card_upload_message.student_card_upload_message_${language.id}`) ||
                validationErros.has(`card_add_message.card_add_message_${language.id}`) ||
                validationErros.has(`card_primary_message.card_primary_message_${language.id}`) ||
                validationErros.has(`card_delete_message.card_delete_message_${language.id}`) ||
                validationErros.has(`ride_cancel_message.ride_cancel_message_${language.id}`) ||
                validationErros.has(`bank_save_message.bank_save_message_${language.id}`) ||
                validationErros.has(`paypal_update_message.paypal_update_message_${language.id}`) ||
                validationErros.has(`paypal_saved_message.paypal_saved_message_${language.id}`) ||
                validationErros.has(`replied_message.replied_message_${language.id}`) ||
                validationErros.has(`ride_post_message.ride_post_message_${language.id}`) ||
                validationErros.has(`book_seat_message.book_seat_message_${language.id}`) ||
                validationErros.has(`book_seat_message_end_part.book_seat_message_end_part_${language.id}`) ||
                validationErros.has(`email_verified_message.email_verified_message_${language.id}`) ||
                validationErros.has(`continue_with_app_btn_label.continue_with_app_btn_label_${language.id}`) ||
                validationErros.has(`create_my_profile_btn_label.create_my_profile_btn_label_${language.id}`) ||
                validationErros.has(`profile_photo_update_message.profile_photo_update_message_${language.id}`) ||
                validationErros.has(`welcome_message.welcome_message_${language.id}`) ||
                validationErros.has(`email_sent_message.email_sent_message_${language.id}`) ||
                validationErros.has(`hey_message.hey_message_${language.id}`) ||
                validationErros.has(`complete_profile_message.complete_profile_message_${language.id}`) ||
                validationErros.has(`no_user_found_message.no_user_found_message_${language.id}`) ||
                validationErros.has(`no_user_match_message.no_user_match_message_${language.id}`) ||
                validationErros.has(`image_size_error_message.image_size_error_message_${language.id}`) ||
                validationErros.has(`incorrect_password_message.incorrect_password_message_${language.id}`) ||
                validationErros.has(`incorrect_code_message.incorrect_code_message_${language.id}`) ||
                validationErros.has(`phone_code_error_message.phone_code_error_message_${language.id}`) ||
                validationErros.has(`too_many_secured_cash_attempt_message.too_many_secured_cash_attempt_message_${language.id}`) ||
                validationErros.has(`secured_cash_success_message.secured_cash_success_message_${language.id}`) ||
                validationErros.has(`not_allowed_post_ride_state_wise_message.not_allowed_post_ride_state_wise_message_${language.id}`) ||
                validationErros.has(`add_your_phone.add_your_phone_${language.id}`) ||
                validationErros.has(`confirm_cancel_noshow.confirm_cancel_noshow_${language.id}`) ||
                validationErros.has(`cancel_noshow_take_me_back.cancel_noshow_take_me_back_${language.id}`) ||
                validationErros.has(`cancel_noshow_are_you_sure.cancel_noshow_are_you_sure_${language.id}`) ||
                validationErros.has(`admin_block_account_message.admin_block_account_message_${language.id}`) ||
                validationErros.has(`already_added_card_message.already_added_card_message_${language.id}`) ||
                validationErros.has(`ride_schedule_message.ride_schedule_message_${language.id}`) ||
                validationErros.has(`female_user_message.female_user_message_${language.id}`) ||
                validationErros.has(`star5_passenger_message.star5_passenger_message_${language.id}`) ||
                validationErros.has(`star4_passenger_message.star4_passenger_message_${language.id}`) ||
                validationErros.has(`star3_passenger_message.star3_passenger_message_${language.id}`) ||
                validationErros.has(`passenger_with_review_message.passenger_with_review_message_${language.id}`) ||
                validationErros.has(`past_time_message.past_time_message_${language.id}`) ||
                validationErros.has(`paypal_not_completed_message.paypal_not_completed_message_${language.id}`) ||
                validationErros.has(`search_result_clear_message.search_result_clear_message_${language.id}`) ||
                validationErros.has(`delete_card_message.delete_card_message_${language.id}`) ||
                validationErros.has(`withdraw_message.withdraw_message_${language.id}`) ||
                validationErros.has(`delete_vehicle_message.delete_vehicle_message_${language.id}`) ||
                validationErros.has(`remove_driver_license_message.remove_driver_license_message_${language.id}`) ||
                validationErros.has(`popup_close_btn_text.popup_close_btn_text_${language.id}`) ||
                validationErros.has(`popup_signup_btn_text.popup_signup_btn_text_${language.id}`) ||
                validationErros.has(`popup_login_btn_text.popup_login_btn_text_${language.id}`) ||
                validationErros.has(`arbitration_success_message.arbitration_success_message_${language.id}`) ||
                validationErros.has(`revert_arbitration_button.revert_arbitration_button_${language.id}`) ||
                validationErros.has(`cancel_button.cancel_button_${language.id}`) ||
                validationErros.has(`delete_button.delete_button_${language.id}`) ||
                validationErros.has(`ride_dead_time_text.ride_dead_time_text_${language.id}`) ||
                validationErros.has(`payout_request_success_message.payout_request_success_message_${language.id}`) ||
                validationErros.has(`suspended_account_phone_number_message.suspended_account_phone_number_message_${language.id}`) ||
                validationErros.has(`booking_request_success_message.booking_request_success_message_${language.id}`) ||
                validationErros.has(`login_before_booking_message.login_before_booking_message_${language.id}`) ||
                validationErros.has(`no_show_passanger_button.no_show_passanger_button_${language.id}`) ||
                validationErros.has(`no_show_driver_button.no_show_driver_button_${language.id}`) ||
                validationErros.has(`select_reason.select_reason_${language.id}`) ||
                validationErros.has(`select_recommend.select_recommend_${language.id}`) ||
                validationErros.has(`check_box.check_box_${language.id}`) ||
                validationErros.has(`reviewed_driver_message.reviewed_driver_message_${language.id}`) ||
                validationErros.has(`reviewed_passenger_message.reviewed_passenger_message_${language.id}`) ||
                validationErros.has(`reject_booking_message.reject_booking_message_${language.id}`) ||
                validationErros.has(`cancel_booking_message.cancel_booking_message_${language.id}`) ||
                validationErros.has(`contact_form_message.contact_form_message_${language.id}`) ||
                validationErros.has(`acc_suspend_message.acc_suspend_message_${language.id}`) ||
                validationErros.has(`seat_unavailable_message.seat_unavailable_message_${language.id}`) ||
                validationErros.has(`verified_number_message.verified_number_message_${language.id}`) ||
                validationErros.has(`seat_hold_message.seat_hold_message_${language.id}`) ||
                validationErros.has(`verified_email_message.verified_email_message_${language.id}`) ||
                validationErros.has(`email_not_verify_message.email_not_verify_message_${language.id}`) ||
                validationErros.has(`reset_password_message.reset_password_message_${language.id}`) ||
                validationErros.has(`email_update_message.email_update_message_${language.id}`) ||
                validationErros.has(`need_to_select_card_message.need_to_select_card_message_${language.id}`) ||
                validationErros.has(`phone_number_not_allowed_message.phone_number_not_allowed_message_${language.id}`) ||
                validationErros.has(`email_not_allowed_message.email_not_allowed_message_${language.id}`) ||
                validationErros.has(`url_not_allowed_message.url_not_allowed_message_${language.id}`) ||
                validationErros.has(`enter_code_message.enter_code_message_${language.id}`) ||
                validationErros.has(`past_date_message.past_date_message_${language.id}`) ||
                validationErros.has(`general_error_message.general_error_message_${language.id}`) ||
                validationErros.has(`current_email_not_match.current_email_not_match_${language.id}`) ||
                validationErros.has(`email_not_found_message.email_not_found_message_${language.id}`) ||
                validationErros.has(`password_reset_success_message.password_reset_success_message_${language.id}`) ||
                validationErros.has(`overlap_ride_message.overlap_ride_message_${language.id}`) ||
                validationErros.has(`topup_balance_success_message.topup_balance_success_message_${language.id}`) ||
                validationErros.has(`card_expiry_message.card_expiry_message_${language.id}`) ||
                validationErros.has(`verify_amount_not_match_message.verify_amount_not_match_message_${language.id}`) ||
                validationErros.has(`bank_verified_message.bank_verified_message_${language.id}`) ||
                validationErros.has(`bank_already_verified_message.bank_already_verified_message_${language.id}`) ||
                validationErros.has(`admin_sent_verify_amount_message.admin_sent_verify_amount_message_${language.id}`) ||
                validationErros.has(`email_already_exist_message.email_already_exist_message_${language.id}`) ||
                validationErros.has(`request_expired_message.request_expired_message_${language.id}`) ||
                validationErros.has(`request_accept_message.request_accept_message_${language.id}`) ||
                validationErros.has(`seat_booked_message.seat_booked_message_${language.id}`) ||
                validationErros.has(`seat_hold_success_message.seat_hold_success_message_${language.id}`) ||
                validationErros.has(`account_closed_message.account_closed_message_${language.id}`) ||
                validationErros.has(`bank_detail_update_message.bank_detail_update_message_${language.id}`) ||
                validationErros.has(`booking_not_update_message.booking_not_update_message_${language.id}`) ||
                validationErros.has(`closed_account_success_message.closed_account_success_message_${language.id}`) ||
                validationErros.has(`reply_already_exist_message.reply_already_exist_message_${language.id}`) ||
                validationErros.has(`post_ride_update_message.post_ride_update_message_${language.id}`) ||
                validationErros.has(`license_delete_message.license_delete_message_${language.id}`) ||
                validationErros.has(`block_booking_message.block_booking_message_${language.id}`) ||
                validationErros.has(`block_review_rating_message.block_review_rating_message_${language.id}`) ||
                validationErros.has(`block_post_ride_message.block_post_ride_message_${language.id}`) ||
                validationErros.has(`profile_photo_required_message.profile_photo_required_message_${language.id}`) ||
                validationErros.has(`password_token_invalid_message.password_token_invalid_message_${language.id}`) ||
                validationErros.has(`phone_set_default_message.phone_set_default_message_${language.id}`) ||
                validationErros.has(`verfification_code_sent_message.verfification_code_sent_message_${language.id}`) ||
                validationErros.has(`removed_passenger_message.removed_passenger_message_${language.id}`) ||
                validationErros.has(`popup_submit_btn_text.popup_submit_btn_text_${language.id}`) ||
                validationErros.has(`reward_not_found_message.reward_not_found_message_${language.id}`) ||
                validationErros.has(`claim_reward_student_success_message.claim_reward_student_success_message_${language.id}`) ||
                validationErros.has(`claim_reward_driver_success_message.claim_reward_driver_success_message_${language.id}`) ||
                validationErros.has(`coffee_wall_heading_success_message.coffee_wall_heading_success_message_${language.id}`) ||
                validationErros.has(`coffee_wall_text_success_message.coffee_wall_text_success_message_${language.id}`) ||
                validationErros.has(
                    `vehicle_removed_message.vehicle_removed_message_${language.id}`
                )
            );
        },
    },
};
</script>
