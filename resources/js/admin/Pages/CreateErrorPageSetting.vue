<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="pt-4">
                <div class="max-w-full mx-auto px-4">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">Error page settings (404)</h3>
                    </div>
                </div>
            </header>
            <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                <ExcelBulkImport
                    title="Error Page (404)"
                    mode="all_languages"
                    download-endpoint="download-error-page-setting-template"
                    upload-endpoint="upload-error-page-setting-excel"
                    @success="fetchErrorPageSetting"
                />
            </div>
            <form class="px-4 md:px-6 lg:px-8" @submit.prevent="updatePageSetting()">
                <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
                    <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                        <li class="mr-2" v-for="language in languages" :key="language.id">
                            <a href="#" @click.prevent="updateLanguageId(language)"
                                :class="['inline-block rounded font-FuturaMdCnBT px-5 py-2 lg:text-lg border border-primary text-center hover:border-blue-500',
                                    (activeLanguageId == null && language.is_default) || activeLanguageId == language.id ? 'bg-primary text-white' : 'hover:bg-blue-100']">
                                {{ language.name }}
                            </a>
                        </li>
                    </ul>
                </div>
                <template v-for="language in languages" :key="language.id">
                    <div v-if="(activeLanguageId == null && language.is_default) || language.id == activeLanguageId" class="grid my-5 grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative z-0 w-full group md:col-span-2">
                            <label :for="`error_404_heading_${activeLanguageId}`">404 heading</label>
                            <input type="text" :name="`error_404_heading_${activeLanguageId}`" :id="`error_404_heading_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" :value="getCurrentValue('error_404_heading')" @input="handleInput($event.target.value, language, 'error_404_heading')" />
                        </div>
                        <div class="relative z-0 w-full group md:col-span-2">
                            <label :for="`error_404_paragraph_1_${activeLanguageId}`">404 paragraph 1</label>
                            <textarea :name="`error_404_paragraph_1_${activeLanguageId}`" :id="`error_404_paragraph_1_${activeLanguageId}`" rows="2" class="can-exp-input w-full block border border-gray-300 rounded" :value="getCurrentValue('error_404_paragraph_1')" @input="handleInput($event.target.value, language, 'error_404_paragraph_1')"></textarea>
                        </div>
                        <div class="relative z-0 w-full group md:col-span-2">
                            <label :for="`error_404_paragraph_2_${activeLanguageId}`">404 paragraph 2</label>
                            <textarea :name="`error_404_paragraph_2_${activeLanguageId}`" :id="`error_404_paragraph_2_${activeLanguageId}`" rows="2" class="can-exp-input w-full block border border-gray-300 rounded" :value="getCurrentValue('error_404_paragraph_2')" @input="handleInput($event.target.value, language, 'error_404_paragraph_2')"></textarea>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label :for="`error_404_back_home_btn_${activeLanguageId}`">Back to Homepage button</label>
                            <input type="text" :name="`error_404_back_home_btn_${activeLanguageId}`" :id="`error_404_back_home_btn_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" :value="getCurrentValue('error_404_back_home_btn')" @input="handleInput($event.target.value, language, 'error_404_back_home_btn')" />
                        </div>
                        <div class="relative z-0 w-full group">
                            <label :for="`error_404_contact_btn_${activeLanguageId}`">Contact us button</label>
                            <input type="text" :name="`error_404_contact_btn_${activeLanguageId}`" :id="`error_404_contact_btn_${activeLanguageId}`" class="can-exp-input w-full block border border-gray-300 rounded" :value="getCurrentValue('error_404_contact_btn')" @input="handleInput($event.target.value, language, 'error_404_contact_btn')" />
                        </div>
                    </div>
                </template>
                <button type="submit" class="button-exp-fill mt-5">Submit</button>
            </form>
        </div>
    </AppLayout>
</template>
<script>
import axios from "axios";
import ExcelBulkImport from "../components/ExcelBulkImport.vue";
export default {
    components: { ExcelBulkImport },
    data() {
        return { activeLanguageId: null, languages: [], form: {}, loading: false };
    },
    created() {
        this.fetchLanguages();
    },
    methods: {
        getCurrentValue(name) {
            return this.form[name] && this.form[name][`${name}_${this.activeLanguageId}`] ? this.form[name][`${name}_${this.activeLanguageId}`] : "";
        },
        handleInput(value, language, key) {
            if (!this.form[key]) this.form[key] = {};
            this.form[key][`${key}_${language.id}`] = value;
        },
        updateLanguageId(language) {
            this.activeLanguageId = language.id;
        },
        fetchLanguages() {
            axios.get(`${process.env.MIX_ADMIN_API_URL}languages?getAll=1`).then((res) => {
                if (res?.data?.status === "Success") {
                    this.languages = res?.data?.data || [];
                    const defaultLang = this.languages.find((x) => x.is_default == "1");
                    this.activeLanguageId = defaultLang?.id || (this.languages[0]?.id) || null;
                    this.languages.forEach((language) => {
                        this.handleInput("", language, "error_404_heading");
                        this.handleInput("", language, "error_404_paragraph_1");
                        this.handleInput("", language, "error_404_paragraph_2");
                        this.handleInput("", language, "error_404_back_home_btn");
                        this.handleInput("", language, "error_404_contact_btn");
                    });
                    this.fetchErrorPageSetting();
                }
            });
        },
        fetchErrorPageSetting() {
            axios.get(`${process.env.MIX_ADMIN_API_URL}get-error-page-setting`).then((res) => {
                if (res?.data?.status === "Success") {
                    const details = res?.data?.data?.error_page_setting_detail || [];
                    details.forEach((setting) => {
                        this.handleInput(setting?.error_404_heading, setting?.language, "error_404_heading");
                        this.handleInput(setting?.error_404_paragraph_1, setting?.language, "error_404_paragraph_1");
                        this.handleInput(setting?.error_404_paragraph_2, setting?.language, "error_404_paragraph_2");
                        this.handleInput(setting?.error_404_back_home_btn, setting?.language, "error_404_back_home_btn");
                        this.handleInput(setting?.error_404_contact_btn, setting?.language, "error_404_contact_btn");
                    });
                }
            });
        },
        updatePageSetting() {
            this.loading = true;
            axios.post(`${process.env.MIX_ADMIN_API_URL}update-error-page-setting`, this.form)
                .then((res) => {
                    if (res?.data?.status === "Success") helper.swalSuccessMessage(res.data.message);
                    else helper.swalErrorMessage(res.data.message);
                })
                .catch(() => helper.swalErrorMessage("Update failed"))
                .finally(() => (this.loading = false));
        },
    },
};
</script>
