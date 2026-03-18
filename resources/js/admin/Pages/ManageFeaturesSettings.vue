<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="pt-4">
                <div class="max-w-full mx-auto px-4">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Manage ride features (names & tooltips)
                        </h3>
                    </div>
                </div>
            </header>

            <ExcelBulkImport
                title="Features (names & tooltips)"
                mode="all_languages"
                download-endpoint="download-all-features-setting-template"
                upload-endpoint="upload-all-features-setting-excel"
                @success="fetchFeatures"
            />

            <div class="px-4 md:px-6 lg:px-8 mt-4">
                <div
                    v-if="loading"
                    class="py-8 text-center text-gray-500"
                >
                    Loading features...
                </div>

                <div v-else>
                    <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
                        <ul class="flex flex-wrap mb-2 overflow-x-auto gap-1">
                            <li
                                class="mr-2"
                                v-for="language in languages"
                                :key="language.id"
                            >
                                <a
                                    href="#"
                                    @click.prevent="activeLanguageId = language.id"
                                    :class="[
                                        'inline-block rounded font-FuturaMdCnBT px-5 py-2 lg:text-lg md:text-base sm:text-base text-base hover:bg-blue-100 border border-primary text-center hover:border-blue-500 hover:text-blue-600',
                                        activeLanguageId === language.id
                                            ? 'bg-primary text-white'
                                            : '',
                                    ]"
                                >
                                    {{ language.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div v-if="activeLanguageId" class="space-y-8">
                        <div
                            v-for="section in groupedSections"
                            :key="section.key"
                            class="border rounded-md"
                        >
                            <div
                                class="flex items-center justify-between bg-primary px-4 py-2 text-white cursor-pointer"
                                @click="toggleSection(section.key)"
                            >
                                <h4 class="font-semibold">
                                    {{ section.title }}
                                </h4>
                                <span class="flex items-center text-xs opacity-80 gap-2">
                                    <span>{{ section.features.length }} items</span>
                                    <svg
                                        class="w-4 h-4 transform transition-transform duration-150"
                                        :class="isSectionOpen(section.key) ? 'rotate-180' : ''"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>

                            <div
                                v-if="isSectionOpen(section.key)"
                                class="divide-y"
                            >
                                <div
                                    v-for="feature in section.features"
                                    :key="feature.id"
                                    class="border-b last:border-b-0"
                                >
                                    <div class="flex items-center justify-between bg-gray-50 px-4 py-2">
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ feature.slug }}
                                            </p>
                                            
                                        </div>
                                    </div>

                                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Name
                                            </label>
                                            <input
                                                type="text"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                v-model="feature._editing.name"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Tooltip
                                            </label>
                                            <input
                                                type="text"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                v-model="feature._editing.tooltip"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="button"
                                class="px-6 py-2 text-sm rounded bg-primary text-white hover:bg-blue-700 disabled:opacity-50"
                                :disabled="savingAll"
                                @click="saveAll"
                            >
                                {{ savingAll ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </div>

                    <div v-else class="py-8 text-center text-gray-500">
                        Please select a language to edit feature names and tooltips.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import axios from "axios";
import ExcelBulkImport from "../components/ExcelBulkImport.vue";

export default {
    components: {
        ExcelBulkImport,
    },
    data() {
        return {
            languages: [],
            activeLanguageId: null,
            featuresByLanguage: {},
            loading: false,
            savingAll: false,
            openSectionKeys: [],
        };
    },
    computed: {
        filteredFeatures() {
            if (!this.activeLanguageId) return [];
            return this.featuresByLanguage[this.activeLanguageId] || [];
        },
        groupedSections() {
            let remaining = [...this.filteredFeatures];
            if (!remaining.length) return [];

            const baseSections = [
                {
                    key: "core",
                    title: "Core ride types",
                    match: (slug) =>
                        ["pink_rides", "extra_care_rides", "wi_fi"].includes(
                            slug
                        ),
                },
                {
                    key: "driver_children",
                    title: "Driver – infants & children",
                    match: (slug) =>
                        slug.startsWith("driver_features_option"),
                },
                {
                    key: "vehicle_equipment",
                    title: "Vehicle comfort & equipment",
                    match: (slug) =>
                        ["heating", "ac", "bike_rack", "ski_rack", "winter_tires"].includes(
                            slug
                        ),
                },
                {
                    key: "rating_filters",
                    title: "Passenger rating filters",
                    match: (slug) =>
                        [
                            "star5_passenger",
                            "star4_passenger",
                            "star3_passenger",
                            "with_review_passenger",
                        ].includes(slug),
                },
                {
                    key: "passenger_children",
                    title: "Passenger – infants & children",
                    match: (slug) =>
                        slug.startsWith("passenger_features_option"),
                },
            ];

            const sections = [];

            baseSections.forEach((section) => {
                const features = remaining.filter((f) =>
                    section.match(f.slug)
                );
                if (features.length) {
                    sections.push({ ...section, features });
                    const featureIds = new Set(features.map((f) => f.id));
                    remaining = remaining.filter(
                        (f) => !featureIds.has(f.id)
                    );
                }
            });

            if (remaining.length) {
                sections.push({
                    key: "other",
                    title: "Other features",
                    features: remaining,
                });
            }

            return sections;
        },
    },
    created() {
        this.bootstrap();
    },
    methods: {
        async bootstrap() {
            this.loading = true;
            try {
                await this.fetchLanguages();
                await this.fetchFeatures();
            } finally {
                this.loading = false;
            }
        },
        async fetchLanguages() {
            const res = await axios.get(
                `${process.env.MIX_ADMIN_API_URL}languages?getAll=1`
            );
            if (res?.data?.status === "Success") {
                this.languages = res.data.data;
                const def = this.languages.find((l) => l.is_default === "1");
                this.activeLanguageId = def?.id || this.languages[0]?.id || null;
            }
        },
        async fetchFeatures() {
            const res = await axios.get(
                `${process.env.MIX_ADMIN_API_URL}get-features-setting`
            );
            if (res?.data?.status !== "Success") {
                return;
            }
            const features = res.data.data || [];

            const byLang = {};
            features.forEach((feature) => {
                (feature.features_setting_detail || []).forEach((detail) => {
                    const langId = detail.language_id;
                    if (!byLang[langId]) {
                        byLang[langId] = [];
                    }
                    byLang[langId].push({
                        id: detail.id,
                        features_setting_id: detail.features_setting_id,
                        slug: feature.slug,
                        name: detail.name,
                        tooltip: detail.tooltip ?? "",
                        _editing: {
                            name: detail.name,
                            tooltip:
                                detail.display_tooltip ??
                                detail.tooltip ??
                                "",
                        },
                        _saving: false,
                    });
                });
            });

            Object.keys(byLang).forEach((langId) => {
                byLang[langId].sort((a, b) =>
                    a.slug.localeCompare(b.slug)
                );
            });

            this.featuresByLanguage = byLang;
        },
        isSectionOpen(key) {
            return this.openSectionKeys.includes(key);
        },
        toggleSection(key) {
            if (this.isSectionOpen(key)) {
                this.openSectionKeys = this.openSectionKeys.filter(
                    (k) => k !== key
                );
            } else {
                this.openSectionKeys.push(key);
            }
        },
        async saveAll() {
            const items = [];

            Object.values(this.featuresByLanguage).forEach((list) => {
                list.forEach((feature) => {
                    items.push({
                        id: feature.id,
                        name: feature._editing.name,
                        tooltip: feature._editing.tooltip,
                    });
                });
            });

            if (!items.length) {
                return;
            }

            this.savingAll = true;
            try {
                const res = await axios.post(
                    `${process.env.MIX_ADMIN_API_URL}features-setting-details-bulk`,
                    { items }
                );
                if (res?.data?.status === "Success") {
                    Object.values(this.featuresByLanguage).forEach((list) => {
                        list.forEach((feature) => {
                            feature.name = feature._editing.name;
                            feature.tooltip = feature._editing.tooltip;
                        });
                    });
                    if (window?.helper?.swalSuccessMessage) {
                        helper.swalSuccessMessage(
                            "All feature changes saved successfully."
                        );
                    }
                } else if (window?.helper?.swalErrorMessage) {
                    helper.swalErrorMessage(
                        res?.data?.message || "Unable to save changes."
                    );
                }
            } catch (e) {
                if (window?.helper?.swalErrorMessage) {
                    helper.swalErrorMessage("Unable to save changes.");
                }
            } finally {
                this.savingAll = false;
            }
        },
    },
};
</script>

