<template>
    <AppLayout>
        <section class="thankyou-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h3 class="can-exp-h2 text-primary">
                                    Thank You Page Settings
                                </h3>
                            </div>
                        </div>
                    </header>
                    <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting()">
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
                                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`name_${activeLanguageId}`">Name</label>
                                            </div>
                                            <input type="text" :name="`name_${activeLanguageId}`" :id="`name_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                :value="getCurrentValue('name')"
                                                @input="handleInput($event.target.value, language, 'name')" />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`name.name_${activeLanguageId}`)"
                                            v-text="validationErros.get(`name.name_${activeLanguageId}`)"></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`meta_description_${activeLanguageId}`">Meta description</label>
                                            </div>
                                            <input type="text" :name="`meta_description_${activeLanguageId}`" :id="`meta_description_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                :value="getCurrentValue('meta_description')"
                                                @input="handleInput($event.target.value, language, 'meta_description')" />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`meta_description.meta_description_${activeLanguageId}`)"
                                            v-text="validationErros.get(`meta_description.meta_description_${activeLanguageId}`)"></p>
                                    </div>
                                    <div class="relative z-0 w-full group">
                                        <div>
                                            <div class="flex justify-between">
                                                <label :for="`meta_keywords_${activeLanguageId}`">Meta keywords</label>
                                            </div>
                                            <input type="text" :name="`meta_keywords_${activeLanguageId}`" :id="`meta_keywords_${activeLanguageId}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                :value="getCurrentValue('meta_keywords')"
                                                @input="handleInput($event.target.value, language, 'meta_keywords')" />
                                        </div>
                                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has(`meta_keywords.meta_keywords_${activeLanguageId}`)"
                                            v-text="validationErros.get(`meta_keywords.meta_keywords_${activeLanguageId}`)"></p>
                                    </div>
                                </div>

                                <!-- Messages Section -->
                                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''">
                                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                        @click.prevent="collapseStates[0] = !collapseStates[0]">
                                        <h3 class="text-white">Messages Section</h3>
                                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                                            <path d="M6 9l4 4 4-4"></path>
                                        </svg>
                                    </div>
                                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                        v-if="collapseStates[0]">
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`forget_close_btn_label_${activeLanguageId}`">Forget Close Button Label</label>
                                                </div>
                                                <input type="text" :name="`forget_close_btn_label_${activeLanguageId}`" :id="`forget_close_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('forget_close_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'forget_close_btn_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`forget_password_message_${activeLanguageId}`">Forget Password Message</label>
                                                </div>
                                                <textarea :name="`forget_password_message_${activeLanguageId}`" :id="`forget_password_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('forget_password_message')"
                                                    @input="handleInput($event.target.value, language, 'forget_password_message')"></textarea>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`rest_password_btn_label_${activeLanguageId}`">Reset Password Button Label</label>
                                                </div>
                                                <input type="text" :name="`rest_password_btn_label_${activeLanguageId}`" :id="`rest_password_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('rest_password_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'rest_password_btn_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`good_bye_btn_label_${activeLanguageId}`">Good Bye Button Label</label>
                                                </div>
                                                <input type="text" :name="`good_bye_btn_label_${activeLanguageId}`" :id="`good_bye_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('good_bye_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'good_bye_btn_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`close_account_message_${activeLanguageId}`">Close Account Message</label>
                                                </div>
                                                <textarea :name="`close_account_message_${activeLanguageId}`" :id="`close_account_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('close_account_message')"
                                                    @input="handleInput($event.target.value, language, 'close_account_message')"></textarea>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`account_close_heading_${activeLanguageId}`">Account Close Heading</label>
                                                </div>
                                                <input type="text" :name="`account_close_heading_${activeLanguageId}`" :id="`account_close_heading_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('account_close_heading')"
                                                    @input="handleInput($event.target.value, language, 'account_close_heading')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`login_btn_label_${activeLanguageId}`">Login Button Label</label>
                                                </div>
                                                <input type="text" :name="`login_btn_label_${activeLanguageId}`" :id="`login_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('login_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'login_btn_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`done_btn_label_${activeLanguageId}`">Done Button Label</label>
                                                </div>
                                                <input type="text" :name="`done_btn_label_${activeLanguageId}`" :id="`done_btn_label_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('done_btn_label')"
                                                    @input="handleInput($event.target.value, language, 'done_btn_label')" />
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`instant_booking_message_${activeLanguageId}`">Instant Booking Message</label>
                                                </div>
                                                <textarea :name="`instant_booking_message_${activeLanguageId}`" :id="`instant_booking_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('instant_booking_message')"
                                                    @input="handleInput($event.target.value, language, 'instant_booking_message')"></textarea>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`manual_booking_message_${activeLanguageId}`">Manual Booking Message</label>
                                                </div>
                                                <textarea :name="`manual_booking_message_${activeLanguageId}`" :id="`manual_booking_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('manual_booking_message')"
                                                    @input="handleInput($event.target.value, language, 'manual_booking_message')"></textarea>
                                            </div>
                                        </div>
                                        <div class="relative z-0 w-full group">
                                            <div>
                                                <div class="flex justify-between">
                                                    <label :for="`top_up_message_${activeLanguageId}`">Top Up Message</label>
                                                </div>
                                                <textarea :name="`top_up_message_${activeLanguageId}`" :id="`top_up_message_${activeLanguageId}`"
                                                    class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                                    :value="getCurrentValue('top_up_message')"
                                                    @input="handleInput($event.target.value, language, 'top_up_message')"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="submit" class="button-exp-fill mt-5">Submit</button>
                    </form>
                </div>
            </main>
        </section>
    </AppLayout>
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
            collapseStates: [true],
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
                            this.handleInput("", language, "name");
                            this.handleInput("", language, "meta_keywords");
                            this.handleInput("", language, "meta_description");
                            this.handleInput("", language, "forget_close_btn_label");
                            this.handleInput("", language, "forget_password_message");
                            this.handleInput("", language, "rest_password_btn_label");
                            this.handleInput("", language, "good_bye_btn_label");
                            this.handleInput("", language, "close_account_message");
                            this.handleInput("", language, "account_close_heading");
                            this.handleInput("", language, "login_btn_label");
                            this.handleInput("", language, "done_btn_label");
                            this.handleInput("", language, "instant_booking_message");
                            this.handleInput("", language, "manual_booking_message");
                            this.handleInput("", language, "top_up_message");
                        });
                        this.fetchThankyouPageSetting();
                    }
                });
        },
        fetchThankyouPageSetting() {
            axios.get(`${this.mixAdminApiUrl}get-thankyou-page-setting`)
                .then((res) => {
                    if (res?.data?.status == "Success") {
                        let thankyou_page_setting_detail = res?.data?.data?.thankyou_page_setting_detail || [];
                        thankyou_page_setting_detail.map((setting) => {
                            const lang = setting?.language || { id: setting?.language_id };
                            this.handleInput(setting?.name ?? "", lang, "name");
                            this.handleInput(setting?.meta_keywords ?? "", lang, "meta_keywords");
                            this.handleInput(setting?.meta_description ?? "", lang, "meta_description");
                            this.handleInput(setting?.forget_close_btn_label ?? "", lang, "forget_close_btn_label");
                            this.handleInput(setting?.forget_password_message ?? "", lang, "forget_password_message");
                            this.handleInput(setting?.rest_password_btn_label ?? "", lang, "rest_password_btn_label");
                            this.handleInput(setting?.good_bye_btn_label ?? "", lang, "good_bye_btn_label");
                            this.handleInput(setting?.close_account_message ?? "", lang, "close_account_message");
                            this.handleInput(setting?.account_close_heading ?? "", lang, "account_close_heading");
                            this.handleInput(setting?.login_btn_label ?? "", lang, "login_btn_label");
                            this.handleInput(setting?.done_btn_label ?? "", lang, "done_btn_label");
                            this.handleInput(setting?.instant_booking_message ?? "", lang, "instant_booking_message");
                            this.handleInput(setting?.manual_booking_message ?? "", lang, "manual_booking_message");
                            this.handleInput(setting?.top_up_message ?? "", lang, "top_up_message");
                        });
                    }
                });
        },
        updatePageSetting() {
            this.loading = true;
            axios.post(`${this.mixAdminApiUrl}update-thankyou-page-setting`, this.form)
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
            return (
                validationErros.has(`name.name_${language.id}`) ||
                validationErros.has(`meta_keywords.meta_keywords_${language.id}`) ||
                validationErros.has(`meta_description.meta_description_${language.id}`)
            );
        },
    },
};
</script>
