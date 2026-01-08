<template>
    <AppLayout>
        <section class="signup-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Signup page settings
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
                                                        :for="`or_label_${activeLanguageId}`"
                                                        >Or label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`or_label_${activeLanguageId}`"
                                                    :id="`or_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'or_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'or_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `or_label.or_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `or_label.or_label_${activeLanguageId}`
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
                                                        :for="`first_name_label_${activeLanguageId}`"
                                                        >First name label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_label_${activeLanguageId}`"
                                                    :id="`first_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_label.first_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_label.first_name_label_${activeLanguageId}`
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
                                                        :for="`first_name_placeholder_${activeLanguageId}`"
                                                        >First name placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`first_name_placeholder_${activeLanguageId}`"
                                                    :id="`first_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'first_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'first_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `first_name_placeholder.first_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `first_name_placeholder.first_name_placeholder_${activeLanguageId}`
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
                                                        :for="`last_name_label_${activeLanguageId}`"
                                                        >Last name label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_label_${activeLanguageId}`"
                                                    :id="`last_name_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_label.last_name_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_label.last_name_label_${activeLanguageId}`
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
                                                        :for="`last_name_placeholder_${activeLanguageId}`"
                                                        >Last name placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`last_name_placeholder_${activeLanguageId}`"
                                                    :id="`last_name_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'last_name_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'last_name_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `last_name_placeholder.last_name_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `last_name_placeholder.last_name_placeholder_${activeLanguageId}`
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
                                                        :for="`email_label_${activeLanguageId}`"
                                                        >Email label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`email_label_${activeLanguageId}`"
                                                    :id="`email_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'email_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'email_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `email_label.email_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `email_label.email_label_${activeLanguageId}`
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
                                                        :for="`email_placeholder_${activeLanguageId}`"
                                                        >Email placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`email_placeholder_${activeLanguageId}`"
                                                    :id="`email_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'email_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'email_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `email_placeholder.email_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `email_placeholder.email_placeholder_${activeLanguageId}`
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
                                                        :for="`password_label_${activeLanguageId}`"
                                                        >Password label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`password_label_${activeLanguageId}`"
                                                    :id="`password_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'password_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'password_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `password_label.password_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `password_label.password_label_${activeLanguageId}`
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
                                                        :for="`password_placeholder_${activeLanguageId}`"
                                                        >Password placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`password_placeholder_${activeLanguageId}`"
                                                    :id="`password_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'password_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'password_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `password_placeholder.password_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `password_placeholder.password_placeholder_${activeLanguageId}`
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
                                                        :for="`confirm_password_label_${activeLanguageId}`"
                                                        >Confirm password label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`confirm_password_label_${activeLanguageId}`"
                                                    :id="`confirm_password_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'confirm_password_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'confirm_password_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `confirm_password_label.confirm_password_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `confirm_password_label.confirm_password_label_${activeLanguageId}`
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
                                                        :for="`confirm_password_placeholder_${activeLanguageId}`"
                                                        >Confirm password placeholder</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`confirm_password_placeholder_${activeLanguageId}`"
                                                    :id="`confirm_password_placeholder_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'confirm_password_placeholder'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'confirm_password_placeholder'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `confirm_password_placeholder.confirm_password_placeholder_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `confirm_password_placeholder.confirm_password_placeholder_${activeLanguageId}`
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
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <label
                                                        :for="`button_label_${activeLanguageId}`"
                                                        >Button label</label
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    :name="`button_label_${activeLanguageId}`"
                                                    :id="`button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                                    placeholder=" "
                                                    :value="
                                                        getCurrentValue(
                                                            'button_label'
                                                        )
                                                    "
                                                    @input="
                                                        handleInput(
                                                            $event.target.value,
                                                            language,
                                                            'button_label'
                                                        )
                                                    "
                                                />
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-red-400"
                                                v-if="
                                                    validationErros.has(
                                                        `button_label.button_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `button_label.button_label_${activeLanguageId}`
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
                                                        :for="`signin_label_${activeLanguageId}`"
                                                        >Signin label</label
                                                    >
                                                </div>
                                                <editor
                                                    @selectionChange="
                                                        handleSelectionChange(
                                                            language,
                                                            'signin_label'
                                                        )
                                                    "
                                                    :ref="`signin_label_${language.id}`"
                                                    :id="`signin_label_${language.id}`"
                                                    :initial-value="
                                                        form[
                                                            `signin_label`
                                                        ][
                                                            `signin_label_${language?.id}`
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
                                                        `signin_label.signin_label_${activeLanguageId}`
                                                    )
                                                "
                                                v-text="
                                                    validationErros.get(
                                                        `signin_label.signin_label_${activeLanguageId}`
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
                            this.handleInput("", language, "or_label");
                            this.handleInput("", language, "first_name_label");
                            this.handleInput("", language, "first_name_placeholder");
                            this.handleInput("", language, "last_name_label");
                            this.handleInput("", language, "last_name_placeholder");
                            this.handleInput("", language, "email_label");
                            this.handleInput("", language, "email_placeholder");
                            this.handleInput("", language, "password_label");
                            this.handleInput("", language, "password_placeholder");
                            this.handleInput("", language, "confirm_password_label");
                            this.handleInput("", language, "confirm_password_placeholder");
                            this.handleInput("", language, "agree_terms_label");
                            this.handleInput("", language, "button_label");
                            this.handleInput("", language, "signin_label");
                        });
                        this.fetchSignupPageSetting();
                    }
                });
        },
        fetchSignupPageSetting() {
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-mobile-signup-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let mobile_signup_setting_detail =
                            res?.data?.data?.mobile_signup_setting_detail || [];
                        mobile_signup_setting_detail.map((setting) => {
                            this.handleInput(
                                setting?.main_heading,
                                setting?.language,
                                "main_heading"
                            );
                            this.handleInput(
                                setting?.or_label,
                                setting?.language,
                                "or_label"
                            );
                            this.handleInput(
                                setting?.first_name_label,
                                setting?.language,
                                "first_name_label"
                            );
                            this.handleInput(
                                setting?.first_name_placeholder,
                                setting?.language,
                                "first_name_placeholder"
                            );
                            this.handleInput(
                                setting?.last_name_label,
                                setting?.language,
                                "last_name_label"
                            );
                            this.handleInput(
                                setting?.last_name_placeholder,
                                setting?.language,
                                "last_name_placeholder"
                            );
                            this.handleInput(
                                setting?.email_label,
                                setting?.language,
                                "email_label"
                            );
                            this.handleInput(
                                setting?.email_placeholder,
                                setting?.language,
                                "email_placeholder"
                            );
                            this.handleInput(
                                setting?.password_label,
                                setting?.language,
                                "password_label"
                            );
                            this.handleInput(
                                setting?.password_placeholder,
                                setting?.language,
                                "password_placeholder"
                            );
                            this.handleInput(
                                setting?.confirm_password_label,
                                setting?.language,
                                "confirm_password_label"
                            );
                            this.handleInput(
                                setting?.confirm_password_placeholder,
                                setting?.language,
                                "confirm_password_placeholder"
                            );
                            this.handleInput(
                                setting?.agree_terms_label,
                                setting?.language,
                                "agree_terms_label"
                            );
                            this.handleInput(
                                setting?.button_label,
                                setting?.language,
                                "button_label"
                            );
                            this.handleInput(
                                setting?.signin_label,
                                setting?.language,
                                "signin_label"
                            );
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios
                .post(
                    `${process.env.MIX_ADMIN_API_URL}update-mobile-signup-page-setting`,
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
                    `or_label.or_label_${language.id}`
                ) ||
                validationErros.has(
                    `first_name_label.first_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `first_name_placeholder.first_name_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `last_name_label.last_name_label_${language.id}`
                ) ||
                validationErros.has(
                    `last_name_placeholder.last_name_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `email_label.email_label_${language.id}`
                ) ||
                validationErros.has(
                    `email_placeholder.email_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `password_label.password_label_${language.id}`
                ) ||
                validationErros.has(
                    `password_placeholder.password_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `confirm_password_label.confirm_password_label_${language.id}`
                ) ||
                validationErros.has(
                    `confirm_password_placeholder.confirm_password_placeholder_${language.id}`
                ) ||
                validationErros.has(
                    `agree_terms_label.agree_terms_label_${language.id}`
                ) ||
                validationErros.has(
                    `button_label.button_label_${language.id}`
                ) ||
                validationErros.has(
                    `signin_label.signin_label_${language.id}`
                )
            );
        },
    },
};
</script>
