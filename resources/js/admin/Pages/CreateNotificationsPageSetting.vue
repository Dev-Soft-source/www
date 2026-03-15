<template>
    <AppLayout>
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Notifications Page Settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <form class="px-4" @submit.prevent="updatePageSetting()">
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                            <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                                <li class="mr-2" v-for="language in languages" :key="language.id">
                                    <a href="#" @click.prevent="updateLanguageId(language)"
                                        :class="[
                                            'inline-block rounded font-FuturaMdCnBT px-5 py-2 lg:text-lg md:text-base sm:text-base text-base hover:bg-blue-100 border border-primary text-center hover:border-blue-500 hover:text-blue-600',
                                            (activeLanguageId == null && language.is_default) || activeLanguageId == language.id
                                                ? 'bg-primary text-white'
                                                : '',
                                            checkValidationError(validationErros, language)
                                                ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                                : '',
                                        ]">{{ language.name }}</a>
                                </li>
                            </ul>
                        </div>
                        <template v-for="language in languages" :key="language.id">
                            <div v-if="(activeLanguageId == null && language.is_default) || language.id == activeLanguageId">
                                <!-- Info Bar Section -->
                                <div class="border rounded w-full my-5" :class="collapseStates[0] ? 'bg-gray-50' : ''">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="collapseStates[0] = !collapseStates[0]">
                                        <h3 class="text-white">Info Bar Section</h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>
                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[0]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`info_bar_title_${activeLanguageId}`">Info Bar Title</label>
                                                </div>
                                                <input type="text" :name="`info_bar_title_${activeLanguageId}`" :id="`info_bar_title_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('info_bar_title')"
                                                    @input="handleInput($event.target.value, language, 'info_bar_title')" />
                                            </div>
                                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`info_bar_title.info_bar_title_${activeLanguageId}`)"
                                                v-text="validationErros.get(`info_bar_title.info_bar_title_${activeLanguageId}`)"></p>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`info_paragraph_ride_${activeLanguageId}`">Info Paragraph (Ride)</label>
                                                </div>
                                                <textarea :name="`info_paragraph_ride_${activeLanguageId}`" :id="`info_paragraph_ride_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('info_paragraph_ride')"
                                                    @input="handleInput($event.target.value, language, 'info_paragraph_ride')"></textarea>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`info_paragraph_inbox_${activeLanguageId}`">Info Paragraph (Inbox)</label>
                                                </div>
                                                <textarea :name="`info_paragraph_inbox_${activeLanguageId}`" :id="`info_paragraph_inbox_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('info_paragraph_inbox')"
                                                    @input="handleInput($event.target.value, language, 'info_paragraph_inbox')"></textarea>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`info_paragraph_general_${activeLanguageId}`">Info Paragraph (General)</label>
                                                </div>
                                                <textarea :name="`info_paragraph_general_${activeLanguageId}`" :id="`info_paragraph_general_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('info_paragraph_general')"
                                                    @input="handleInput($event.target.value, language, 'info_paragraph_general')"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Labels Section -->
                                <div class="border rounded w-full" :class="collapseStates[1] ? 'bg-gray-50' : ''">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="collapseStates[1] = !collapseStates[1]">
                                        <h3 class="text-white">Labels Section</h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>
                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[1]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`mark_all_as_read_button_label_${activeLanguageId}`">Mark All As Read Button Label</label>
                                                </div>
                                                <input type="text" :name="`mark_all_as_read_button_label_${activeLanguageId}`" :id="`mark_all_as_read_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('mark_all_as_read_button_label')"
                                                    @input="handleInput($event.target.value, language, 'mark_all_as_read_button_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`unread_label_${activeLanguageId}`">Unread Label</label>
                                                </div>
                                                <input type="text" :name="`unread_label_${activeLanguageId}`" :id="`unread_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('unread_label')"
                                                    @input="handleInput($event.target.value, language, 'unread_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`no_notifications_found_label_${activeLanguageId}`">No Notifications Found Label</label>
                                                </div>
                                                <input type="text" :name="`no_notifications_found_label_${activeLanguageId}`" :id="`no_notifications_found_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('no_notifications_found_label')"
                                                    @input="handleInput($event.target.value, language, 'no_notifications_found_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`caught_up_label_${activeLanguageId}`">Caught Up Label</label>
                                                </div>
                                                <input type="text" :name="`caught_up_label_${activeLanguageId}`" :id="`caught_up_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('caught_up_label')"
                                                    @input="handleInput($event.target.value, language, 'caught_up_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`delete_button_label_${activeLanguageId}`">Delete Button Label</label>
                                                </div>
                                                <input type="text" :name="`delete_button_label_${activeLanguageId}`" :id="`delete_button_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('delete_button_label')"
                                                    @input="handleInput($event.target.value, language, 'delete_button_label')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="submit" class="button-exp-fill mt-5">Submit</button>
                    </form>
                                </div>    </AppLayout>
</template>

<script>
import axios from "axios";
import ErrorHandling from "../../ErrorHandling.js";
import helper from "../../helper.js";

export default {
    data() {
        return {
            activeLanguageId: null,
            languages: [],
            form: {},
            validationErros: new ErrorHandling(),
            collapseStates: [true, true],
            loading: false,
        };
    },
    computed: {
        mixAdminApiUrl() {
            let base = process.env.MIX_ADMIN_API_URL || '/admin/';
            return base.endsWith('/') ? base : base + '/';
        },
    },
    created() {
        this.fetchLanguages();
    },
    methods: {
        getCurrentValue(name) {
            return this.form[name] && this.form[name][`${name}_${this.activeLanguageId}`]
                ? this.form[name][`${name}_${this.activeLanguageId}`]
                : "";
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
            axios.get(`${this.mixAdminApiUrl}languages?getAll=1`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        this.languages = res?.data?.data;
                        let defaultLang = this.languages.filter((x) => x.is_default == "1");
                        this.activeLanguageId = defaultLang?.[0]?.id || null;
                        let languages = res?.data?.data;
                        languages.map((language) => {
                            this.handleInput("", language, "info_bar_title");
                            this.handleInput("", language, "info_paragraph_ride");
                            this.handleInput("", language, "info_paragraph_inbox");
                            this.handleInput("", language, "info_paragraph_general");
                            this.handleInput("", language, "mark_all_as_read_button_label");
                            this.handleInput("", language, "unread_label");
                            this.handleInput("", language, "no_notifications_found_label");
                            this.handleInput("", language, "caught_up_label");
                            this.handleInput("", language, "delete_button_label");
                        });
                        this.fetchNotificationsPageSetting();
                    }
                });
        },
        fetchNotificationsPageSetting() {
            axios.get(`${this.mixAdminApiUrl}get-notifications-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let notifications_page_setting_detail = res?.data?.data?.notifications_page_setting_detail || [];
                        notifications_page_setting_detail.map((setting) => {
                            const lang = setting?.language || { id: setting?.language_id };
                            this.handleInput(setting?.info_bar_title ?? "", lang, "info_bar_title");
                            this.handleInput(setting?.info_paragraph_ride ?? "", lang, "info_paragraph_ride");
                            this.handleInput(setting?.info_paragraph_inbox ?? "", lang, "info_paragraph_inbox");
                            this.handleInput(setting?.info_paragraph_general ?? "", lang, "info_paragraph_general");
                            this.handleInput(setting?.mark_all_as_read_button_label ?? "", lang, "mark_all_as_read_button_label");
                            this.handleInput(setting?.unread_label ?? "", lang, "unread_label");
                            this.handleInput(setting?.no_notifications_found_label ?? "", lang, "no_notifications_found_label");
                            this.handleInput(setting?.caught_up_label ?? "", lang, "caught_up_label");
                            this.handleInput(setting?.delete_button_label ?? "", lang, "delete_button_label");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios.post(`${this.mixAdminApiUrl}update-notifications-page-setting`, this.form)
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
                    } else if (error.response && error.response.data && error.response.data.status == "Error") {
                        helper.swalErrorMessage(error.response.data.message);
                    }
                    this.loading = false;
                })
                .finally(() => (this.loading = false));
        },
        checkValidationError(validationErros, language) {
            return validationErros.has(`info_bar_title.info_bar_title_${language.id}`);
        },
    },
};
</script>
