<template>
    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <h4 class="text-xl font-bold text-gray-800">{{ headingText }}</h4>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                {{ description }}
            </p>

            <div class="" :class="mode === 'single_language' ? 'md:grid-cols-3' : 'md:grid-cols-2'">
                <!-- Language Selector (single_language only) -->
                <div v-if="mode === 'single_language'">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Language <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="excelForm.selectedLanguageId"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{'border-red-500': excelValidationErrors.language_id}"
                    >
                        <option value="">Choose Language</option>
                        <option
                            v-for="lang in languages"
                            :key="lang.id"
                            :value="lang.id"
                        >
                            {{ lang.name }}
                        </option>
                    </select>
                    <p v-if="excelValidationErrors.language_id" class="text-red-500 text-xs mt-1">
                        {{ excelValidationErrors.language_id }}
                    </p>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Upload Excel File <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">

                        <input
                            type="file"
                            ref="excelFile"
                            @change="handleFileChange"
                            accept=".xlsx,.xls,.csv"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            :class="{'border-red-500': excelValidationErrors.excel_file}"
                        />
                        <!-- Upload Button -->
                        <div class="flex items-end">
                            <button
                                type="button"
                                @click="uploadExcelFile"
                                :disabled="excelUploading"
                                class="w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center"
                            >
                                <svg v-if="excelUploading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span v-if="excelUploading">Uploading...</span>
                                <span v-else>
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Excel
                                </span>
                            </button>
                        </div>
                    </div>
                    <p v-if="excelValidationErrors.excel_file" class="text-red-500 text-xs mt-1">
                        {{ excelValidationErrors.excel_file }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Supported: .xlsx, .xls, .csv (Max: 5MB)</p>
                </div>

                
            </div>

            <!-- Download Template Link -->
            <div class="mt-4 pt-4 border-t border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-medium">Need help formatting your Excel file?</span>
                    </div>
                    <a
                        :href="downloadTemplateUrl"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template
                    </a>
                </div>
            </div>

            <!-- Excel Validation Errors Display -->
            <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <h5 class="text-red-800 font-semibold mb-2">Validation Errors in Excel File:</h5>
                        <ul class="list-disc list-inside space-y-1">
                            <li v-for="(error, index) in excelErrors" :key="index" class="text-sm text-red-700">
                                <strong>Row {{ error.row }}:</strong> {{ error.attribute }} - {{ error.errors.join(', ') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ExcelBulkImport',
    props: {
        /** Page/setting name for titles (e.g. "Billing Address", "Booking Page") */
        title: {
            type: String,
            required: true,
        },
        /** 'all_languages' = template with all language columns, no language selector. 'single_language' = one language per upload, with language selector. */
        mode: {
            type: String,
            default: 'single_language',
            validator: (v) => ['all_languages', 'single_language'].includes(v),
        },
        /** API path for download (e.g. "download-billing-address-setting-template") - base URL is prepended. */
        downloadEndpoint: {
            type: String,
            required: true,
        },
        /** API path for upload (e.g. "upload-billing-address-setting-excel") */
        uploadEndpoint: {
            type: String,
            required: true,
        },
        /** Optional override for download format (default: all_languages when mode is all_languages, else single_column) */
        downloadFormat: {
            type: String,
            default: '',
        },
        /** List of languages for single_language mode (required when mode === 'single_language') */
        languages: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            excelForm: {
                selectedFile: null,
                selectedLanguageId: '',
            },
            excelValidationErrors: {},
            excelErrors: [],
            excelUploading: false,
        };
    },
    computed: {
        baseUrl() {
            return process.env.MIX_ADMIN_API_URL || '';
        },
        downloadTemplateUrl() {
            const format = this.downloadFormat || (this.mode === 'all_languages' ? 'all_languages' : 'single_column');
            return `${this.baseUrl}${this.downloadEndpoint}?format=${format}`;
        },
        headingText() {
            return this.mode === 'all_languages'
                ? `📊 Excel Upload - Bulk Import All Languages`
                : `📊 Excel Upload - Bulk Import Translations`;
        },
        description() {
            if (this.mode === 'all_languages') {
                return `Download the template (Field Name + one column per language), fill in translations for each language, then upload. This will update the ${this.title.toLowerCase()} settings for all languages at once.`;
            }
            return `Upload an Excel file with all ${this.title.toLowerCase()} setting translations for a specific language. This will save or update all fields at once.`;
        },
    },
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.excelForm.selectedFile = file;
                this.excelValidationErrors.excel_file = '';
            }
        },
        async uploadExcelFile() {
            this.excelValidationErrors = {};
            this.excelErrors = [];

            if (this.mode === 'single_language' && !this.excelForm.selectedLanguageId) {
                this.excelValidationErrors.language_id = 'Please select a language';
                this.helperMessage('error', 'Please select a language');
                return;
            }

            if (!this.excelForm.selectedFile) {
                this.excelValidationErrors.excel_file = 'Please select an Excel file';
                this.helperMessage('error', 'Please select an Excel file');
                return;
            }

            if (this.excelForm.selectedFile.size > 5242880) {
                this.excelValidationErrors.excel_file = 'File size must not exceed 5MB';
                return;
            }

            const allowedExtensions = ['xlsx', 'xls', 'csv'];
            const fileExtension = this.excelForm.selectedFile.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                this.excelValidationErrors.excel_file = 'File must be .xlsx, .xls, or .csv';
                return;
            }

            const formData = new FormData();
            formData.append('excel_file', this.excelForm.selectedFile);
            if (this.mode === 'single_language') {
                formData.append('language_id', this.excelForm.selectedLanguageId);
            }

            this.excelUploading = true;

            try {
                const response = await axios.post(`${this.baseUrl}${this.uploadEndpoint}`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                if (response?.data?.status === 'Success') {
                    this.helperMessage('success', response.data.message);
                    this.excelForm.selectedFile = null;
                    this.excelForm.selectedLanguageId = '';
                    if (this.$refs.excelFile) this.$refs.excelFile.value = '';
                    this.$emit('success');
                } else {
                    this.helperMessage('error', response?.data?.message || 'Upload failed');
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    const err = error.response.data;
                    if (err.errors) {
                        if (Array.isArray(err.errors)) {
                            this.excelErrors = err.errors;
                        } else {
                            this.excelValidationErrors = {};
                            Object.keys(err.errors).forEach((key) => {
                                this.excelValidationErrors[key] = err.errors[key][0];
                            });
                        }
                    }
                    if (err.errors?.language_id) this.excelValidationErrors.language_id = err.errors.language_id[0];
                    if (err.errors?.excel_file) this.excelValidationErrors.excel_file = err.errors.excel_file[0];
                    if (error.response.data.message) this.helperMessage('error', error.response.data.message);
                } else {
                    this.helperMessage('error', error.response?.data?.message || 'An error occurred during upload');
                }
            } finally {
                this.excelUploading = false;
            }
        },
        helperMessage(type, message) {
            const h = window.helper || this.$root?.helper;
            if (h) {
                if (type === 'success') h.swalSuccessMessage(message);
                else h.swalErrorMessage(message);
            }
        },
    },
};
</script>
