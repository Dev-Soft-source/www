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
                    <div class="mb-4 flex flex-wrap gap-2">
                        <button
                            v-for="language in languages"
                            :key="language.id"
                            type="button"
                            @click="activeLanguageId = language.id"
                            :class="[
                                'px-3 py-1 text-sm rounded border',
                                activeLanguageId === language.id
                                    ? 'bg-primary text-white border-primary'
                                    : 'bg-white text-gray-700 border-gray-300',
                            ]"
                        >
                            {{ language.name }}
                        </button>
                    </div>

                    <div v-if="activeLanguageId" class="space-y-8">
                        <div
                            v-for="section in groupedSections"
                            :key="section.key"
                            class="border rounded-md"
                        >
                            <div class="flex items-center justify-between bg-primary px-4 py-2 text-white">
                                <h4 class="font-semibold">
                                    {{ section.title }}
                                </h4>
                                <span class="text-xs opacity-80">
                                    {{ section.features.length }} items
                                </span>
                            </div>

                            <div class="divide-y">
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
                                            <p class="text-xs text-gray-500">
                                                ID: {{ feature.id }}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            class="text-xs text-primary hover:underline"
                                            @click="toggleFeature(feature.id)"
                                        >
                                            {{ isOpen(feature.id) ? 'Hide' : 'Show' }} details
                                        </button>
                                    </div>

                                    <div
                                        v-if="isOpen(feature.id)"
                                        class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
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

                                        <div class="md:col-span-2 flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="px-3 py-1 text-sm border border-gray-300 rounded text-gray-700 hover:bg-gray-100"
                                                @click="resetFeature(feature.id)"
                                            >
                                                Reset
                                            </button>
                                            <button
                                                type="button"
                                                class="px-4 py-1.5 text-sm rounded bg-primary text-white hover:bg-blue-700 disabled:opacity-50"
                                                :disabled="feature._saving"
                                                @click="saveFeature(feature)"
                                            >
                                                {{ feature._saving ? 'Saving...' : 'Save' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            openFeatureIds: [],
            loading: false,
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
        isOpen(id) {
            return this.openFeatureIds.includes(id);
        },
        toggleFeature(id) {
            if (this.isOpen(id)) {
                this.openFeatureIds = this.openFeatureIds.filter(
                    (x) => x !== id
                );
            } else {
                this.openFeatureIds.push(id);
            }
        },
        resetFeature(id) {
            const list = this.filteredFeatures;
            const feature = list.find((f) => f.id === id);
            if (!feature) return;
            feature._editing.name = feature.name;
            feature._editing.tooltip = feature.tooltip ?? "";
        },
        async saveFeature(feature) {
            feature._saving = true;
            try {
                const payload = {
                    name: feature._editing.name,
                    tooltip: feature._editing.tooltip,
                };
                const res = await axios.post(
                    `${process.env.MIX_ADMIN_API_URL}features-setting-details/${feature.id}`,
                    payload
                );
                if (res?.data?.status === "Success") {
                    feature.name = feature._editing.name;
                    feature.tooltip = feature._editing.tooltip;
                    if (window?.helper?.swalSuccessMessage) {
                        helper.swalSuccessMessage("Feature updated successfully.");
                    }
                } else if (window?.helper?.swalErrorMessage) {
                    helper.swalErrorMessage(
                        res?.data?.message || "Unable to update feature."
                    );
                }
            } catch (e) {
                if (window?.helper?.swalErrorMessage) {
                    helper.swalErrorMessage("Unable to update feature.");
                }
            } finally {
                feature._saving = false;
            }
        },
    },
};
</script>

