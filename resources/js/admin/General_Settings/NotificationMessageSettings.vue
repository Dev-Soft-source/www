<template>
    <AppLayout>
        <section class="site-text-section relative md:top-16">
            <main class="flex-1 max-h-full p-3 bg-gray-200 pb-2 min-h-60 h-full">
                <div class="relative shadow-md sm:rounded-lg bg-white py-4">
                    <header class="pt-4">
                        <div class="max-w-full mx-auto px-4">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <h3 class="can-exp-h2 text-primary">Notification messages</h3>
                                <button
                                    type="button"
                                    @click="openCreateModal()"
                                    class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm rounded-lg hover:opacity-90 transition-colors duration-200"
                                >
                                    Add notification message
                                </button>
                            </div>
                        </div>
                    </header>

                    <div class="px-4 md:px-6 lg:px-8 mt-6">
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
                            <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                                <li v-for="language in languages" :key="language.id" class="mr-2">
                                    <a
                                        href="#"
                                        @click.prevent="activeLanguageId = language.id"
                                        :class="[
                                            'inline-block rounded font-FuturaMdCnBT px-5 py-2 text-base hover:bg-blue-100 border border-primary text-center hover:border-blue-500 hover:text-blue-600',
                                            (activeLanguageId == null && (language.is_default == 1 || language.is_default == '1')) || activeLanguageId == language.id ? 'bg-primary text-white' : ''
                                        ]"
                                    >{{ language.name }}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Search by slug or name..."
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent w-64 text-sm"
                            />
                            <span class="text-sm text-gray-500">Showing {{ filteredRows.length }} messages</span>
                        </div>

                        <div class="overflow-x-auto">
                            <div v-if="loading" class="text-center py-8 text-gray-500">Loading...</div>
                            <table v-else class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Slug</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Placeholders</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ activeLanguageName }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="row in filteredRows" :key="row.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">{{ row.no }}</td>
                                        <td class="px-4 py-3 text-sm font-medium">{{ row.slug }}</td>
                                        <td class="px-4 py-3 text-sm">{{ row.name || '—' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ (row.placeholders || []).join(', ') || '—' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ row.languages?.[activeLanguageId] || '—' }}</td>
                                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                                            <button type="button" @click="openEditModal(row)" class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                                            <button type="button" @click="confirmDelete(row)" class="text-red-600 hover:text-red-800">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!filteredRows.length && !loading">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No notification messages found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </section>

        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <form @submit.prevent="submitForm()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ isEdit ? 'Edit notification message' : 'Add notification message' }}</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input v-model="form.slug" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" :readonly="isEdit" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Placeholders</label>
                                <input v-model="placeholdersInput" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="first_name, seats, code" />
                                <p class="text-xs text-gray-500 mt-1">Use placeholders in templates like <code>{first_name}</code>.</p>
                            </div>
                            <div v-for="lang in languages" :key="lang.id" class="border-t pt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ lang.name }}</label>
                                <textarea v-model="form.languages[lang.id]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" :disabled="saving" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-white text-base font-medium sm:w-auto sm:text-sm disabled:opacity-50">
                                {{ saving ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
                            </button>
                            <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
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
    name: "NotificationMessageSettings",
    data() {
        return {
            languages: [],
            rows: [],
            loading: true,
            showModal: false,
            isEdit: false,
            saving: false,
            searchQuery: "",
            activeLanguageId: null,
            placeholdersInput: "",
            form: {
                id: null,
                slug: "",
                name: "",
                placeholders: [],
                languages: {},
            },
        };
    },
    computed: {
        activeLanguageName() {
            const lang = this.languages.find((item) => item.id == this.activeLanguageId);
            return lang ? lang.name : "Language";
        },
        filteredRows() {
            const query = (this.searchQuery || "").trim().toLowerCase();
            if (!query) return this.rows;
            return this.rows.filter((row) =>
                (row.slug || "").toLowerCase().includes(query) ||
                (row.name || "").toLowerCase().includes(query)
            );
        },
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            this.loading = true;
            axios.get(`${process.env.MIX_ADMIN_API_URL}get-notification-message-setting`)
                .then((res) => {
                    if (res?.data?.status === "Success") {
                        this.languages = res?.data?.data?.languages || [];
                        this.rows = res?.data?.data?.rows || [];
                        if (!this.activeLanguageId && this.languages.length) {
                            const defaultLang = this.languages.find((lang) => lang.is_default == 1 || lang.is_default == "1");
                            this.activeLanguageId = defaultLang ? defaultLang.id : this.languages[0].id;
                        }
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        openCreateModal() {
            const languages = {};
            this.languages.forEach((lang) => {
                languages[lang.id] = "";
            });
            this.form = { id: null, slug: "", name: "", placeholders: [], languages };
            this.placeholdersInput = "";
            this.isEdit = false;
            this.showModal = true;
        },
        openEditModal(row) {
            this.form = {
                id: row.id,
                slug: row.slug,
                name: row.name || "",
                placeholders: row.placeholders || [],
                languages: { ...(row.languages || {}) },
            };
            this.placeholdersInput = (row.placeholders || []).join(", ");
            this.isEdit = true;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        submitForm() {
            this.saving = true;
            const payload = {
                ...this.form,
                placeholders: this.placeholdersInput
                    .split(",")
                    .map((item) => item.trim())
                    .filter(Boolean),
            };
            const url = this.isEdit
                ? `${process.env.MIX_ADMIN_API_URL}update-notification-message-setting/${this.form.id}`
                : `${process.env.MIX_ADMIN_API_URL}store-notification-message-setting`;

            axios.post(url, payload)
                .then((res) => {
                    if (res?.data?.status === "Success" && window.helper) {
                        window.helper.swalSuccessMessage(res.data.message);
                    }
                    this.fetchData();
                    this.closeModal();
                })
                .catch((error) => {
                    const message = error?.response?.data?.message || "Failed to save notification message.";
                    if (window.helper) {
                        window.helper.swalErrorMessage(message);
                    }
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        confirmDelete(row) {
            if (!confirm(`Delete ${row.slug}?`)) return;
            axios.delete(`${process.env.MIX_ADMIN_API_URL}delete-notification-message-setting/${row.id}`)
                .then((res) => {
                    if (res?.data?.status === "Success" && window.helper) {
                        window.helper.swalSuccessMessage(res.data.message);
                    }
                    this.fetchData();
                });
        },
    },
};
</script>
