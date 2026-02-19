<template>
    <AppLayout>
        <section class="site-text-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2 min-h-60 h-full">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <h3 class="can-exp-h2 text-primary">
                                    Setting site text
                                </h3>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <button
                                        type="button"
                                        @click="openCreateModal()"
                                        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm rounded-lg hover:opacity-90 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add site text
                                    </button>
                                    <a
                                        :href="`${mixAdminApiUrl}export-site-text-setting`"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Export to Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Excel Import -->
                    <div class="px-4 md:px-6 lg:px-8 mt-6 mb-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <h4 class="text-xl font-bold text-gray-800">Import from Excel</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Upload an Excel file with columns: No, slug, display text, and one column per language. The database will be updated according to the file.</p>
                            <div class="flex flex-wrap items-end gap-4">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excel file (.xlsx, .xls, .csv)</label>
                                    <input
                                        type="file"
                                        ref="excelFile"
                                        @change="handleFileChange"
                                        accept=".xlsx,.xls,.csv"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                        :class="{ 'border-red-500': excelValidationErrors.excel_file }"
                                    />
                                    <p v-if="excelValidationErrors.excel_file" class="text-red-500 text-xs mt-1">{{ excelValidationErrors.excel_file }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="uploadExcelFile"
                                    :disabled="excelUploading"
                                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center"
                                >
                                    <svg v-if="excelUploading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span v-if="excelUploading">Uploading...</span>
                                    <span v-else>Upload Excel</span>
                                </button>
                            </div>
                            <div v-if="excelErrors.length > 0" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                <h5 class="text-red-800 font-semibold mb-2">Validation errors:</h5>
                                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                                    <li v-for="(err, idx) in excelErrors" :key="idx">Row {{ err.row }}: {{ err.attribute }} — {{ Array.isArray(err.errors) ? err.errors.join(', ') : err.errors }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="px-4 md:px-6 lg:px-8 overflow-x-auto">
                        <div v-if="loading" class="text-center py-8 text-gray-500">Loading...</div>
                        <table v-else class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-12">No</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">slug</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">display text</th>
                                    <th
                                        v-for="lang in languages"
                                        :key="lang.id"
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider min-w-[120px]"
                                    >
                                        {{ lang.name }}
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-28">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="row in rows" :key="row.id || row.slug" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ row.no }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">{{ row.slug }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ row.display_text }}</td>
                                    <td
                                        v-for="lang in languages"
                                        :key="lang.id"
                                        class="px-4 py-3 text-sm text-gray-700"
                                    >
                                        {{ row.languages && row.languages[lang.id] != null ? row.languages[lang.id] : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                                        <button type="button" @click="openEditModal(row)" class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">Edit</button>
                                        <button type="button" @click="confirmDelete(row)" class="text-red-600 hover:text-red-800" title="Delete">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="rows.length === 0 && !loading">
                                    <td :colspan="4 + (languages && languages.length)" class="px-4 py-8 text-center text-gray-500">No site texts found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </section>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form @submit.prevent="submitForm()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modal-title">{{ isEdit ? 'Edit site text' : 'Add site text' }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                                    <input v-model="form.slug" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. footer_tagline" :readonly="isEdit" />
                                    <p v-if="formErrors.slug" class="text-red-500 text-xs mt-1">{{ formErrors.slug }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Display text</label>
                                    <input v-model="form.text" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Default / display text" />
                                </div>
                                <div v-for="lang in languages" :key="lang.id" class="border-t pt-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ lang.name }}</label>
                                    <input v-model="form.languages[lang.id]" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" :disabled="saving" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-white text-base font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:w-auto sm:text-sm disabled:opacity-50">
                                {{ saving ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
                            </button>
                            <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    name: "SiteTextSettings",
    data() {
        return {
            languages: [],
            rows: [],
            loading: true,
            mixAdminApiUrl: process.env.MIX_ADMIN_API_URL || "",
            excelForm: {
                selectedFile: null,
            },
            excelUploading: false,
            excelValidationErrors: {},
            excelErrors: [],
            showModal: false,
            isEdit: false,
            saving: false,
            form: {
                id: null,
                slug: "",
                text: "",
                languages: {},
            },
            formErrors: {},
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            this.loading = true;
            axios
                .get(`${process.env.MIX_ADMIN_API_URL}get-site-text-setting`)
                .then((res) => {
                    if (res?.data?.status === "Success") {
                        this.languages = res?.data?.data?.languages || [];
                        this.rows = res?.data?.data?.rows || [];
                    }
                })
                .catch(() => {
                    if (window.helper && window.helper.swalErrorMessage) {
                        window.helper.swalErrorMessage("Failed to load site texts.");
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.excelForm.selectedFile = file;
                this.excelValidationErrors.excel_file = "";
                this.excelErrors = [];
            }
        },
        async uploadExcelFile() {
            this.excelValidationErrors = {};
            this.excelErrors = [];
            if (!this.excelForm.selectedFile) {
                this.excelValidationErrors.excel_file = "Please select an Excel file";
                if (window.helper && window.helper.swalErrorMessage) {
                    window.helper.swalErrorMessage("Please select an Excel file");
                }
                return;
            }
            if (this.excelForm.selectedFile.size > 5242880) {
                this.excelValidationErrors.excel_file = "File size must not exceed 5MB";
                return;
            }
            const formData = new FormData();
            formData.append("excel_file", this.excelForm.selectedFile);
            this.excelUploading = true;
            try {
                const response = await axios.post(
                    `${process.env.MIX_ADMIN_API_URL}import-site-text-setting`,
                    formData,
                    { headers: { "Content-Type": "multipart/form-data" } }
                );
                if (response?.data?.status === "Success") {
                    if (window.helper && window.helper.swalSuccessMessage) {
                        window.helper.swalSuccessMessage(response.data.message);
                    }
                    this.excelForm.selectedFile = null;
                    if (this.$refs.excelFile) this.$refs.excelFile.value = "";
                    this.fetchData();
                } else {
                    if (window.helper && window.helper.swalErrorMessage) {
                        window.helper.swalErrorMessage(response?.data?.message || "Upload failed");
                    }
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    const data = error.response.data || {};
                    if (Array.isArray(data.errors)) this.excelErrors = data.errors;
                    if (window.helper && window.helper.swalErrorMessage) {
                        window.helper.swalErrorMessage(data.message || "Validation errors in Excel file");
                    }
                } else {
                    if (window.helper && window.helper.swalErrorMessage) {
                        window.helper.swalErrorMessage(error.response?.data?.message || "Failed to import Excel");
                    }
                }
            } finally {
                this.excelUploading = false;
            }
        },
        openCreateModal() {
            this.isEdit = false;
            this.form = {
                id: null,
                slug: "",
                text: "",
                languages: {},
            };
            this.languages.forEach((lang) => {
                this.form.languages[lang.id] = "";
            });
            this.formErrors = {};
            this.showModal = true;
        },
        openEditModal(row) {
            this.isEdit = true;
            this.form = {
                id: row.id,
                slug: row.slug,
                text: row.display_text || "",
                languages: { ...(row.languages || {}) },
            };
            this.languages.forEach((lang) => {
                if (this.form.languages[lang.id] === undefined) this.form.languages[lang.id] = "";
            });
            this.formErrors = {};
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        submitForm() {
            this.formErrors = {};
            this.saving = true;
            const url = this.isEdit
                ? `${process.env.MIX_ADMIN_API_URL}update-site-text-setting/${this.form.id}`
                : `${process.env.MIX_ADMIN_API_URL}store-site-text-setting`;
            const payload = {
                slug: this.form.slug.trim(),
                text: this.form.text,
                languages: this.form.languages,
            };
            const method = this.isEdit ? "post" : "post";
            axios[method](url, payload)
                .then((res) => {
                    if (res?.data?.status === "Success") {
                        if (window.helper && window.helper.swalSuccessMessage) {
                            window.helper.swalSuccessMessage(res.data.message);
                        }
                        this.closeModal();
                        this.fetchData();
                    } else {
                        if (window.helper && window.helper.swalErrorMessage) {
                            window.helper.swalErrorMessage(res?.data?.message || "Request failed");
                        }
                    }
                })
                .catch((err) => {
                    const data = err.response?.data || {};
                    if (err.response?.status === 422) {
                        this.formErrors = data.errors || {};
                        if (data.message) {
                            if (window.helper && window.helper.swalErrorMessage) {
                                window.helper.swalErrorMessage(data.message);
                            }
                        }
                    } else {
                        if (window.helper && window.helper.swalErrorMessage) {
                            window.helper.swalErrorMessage(data.message || "Request failed");
                        }
                    }
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        confirmDelete(row) {
            if (!window.helper || !window.helper.swalConfirm) {
                if (confirm("Delete this site text?")) this.deleteRow(row);
                return;
            }
            window.helper.swalConfirm("Are you sure you want to delete this site text?", () => {
                this.deleteRow(row);
            });
        },
        deleteRow(row) {
            axios
                .delete(`${process.env.MIX_ADMIN_API_URL}delete-site-text-setting/${row.id}`)
                .then((res) => {
                    if (res?.data?.status === "Success") {
                        if (window.helper && window.helper.swalSuccessMessage) {
                            window.helper.swalSuccessMessage(res.data.message);
                        }
                        this.fetchData();
                    } else {
                        if (window.helper && window.helper.swalErrorMessage) {
                            window.helper.swalErrorMessage(res?.data?.message || "Delete failed");
                        }
                    }
                })
                .catch((err) => {
                    const msg = err.response?.data?.message || "Delete failed";
                    if (window.helper && window.helper.swalErrorMessage) {
                        window.helper.swalErrorMessage(msg);
                    }
                });
        },
    },
};
</script>
