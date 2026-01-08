<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            {{ isFormEdit ? "Edit" : "Create" }} student reward point
                        </h3>
                        <router-link
                            :to="{ name: 'admin.student-reward-points.index' }"
                            class="button-exp-fill"
                        >
                            Back
                        </router-link>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">

                <div
                class="text-sm font-medium text-center text-gray-500 border-b border-gray-200"
                >
                    <ul class="flex gap-2 flex-wrap my-4">
                        <li
                            class="mr-2 mb-2"
                            v-for="language in languages"
                            :key="language.id"
                        >
                            <a
                                @click.prevent="changeLanguageTab(language)"
                                href="#"
                                :class="[
                                    'inline-block py-2 px-4 text-primary border border-primary rounded  font-FuturaMdCnBT text-base lg:text-lg font-medium hover:text-white hover:bg-primary active:text-white active:bg-primary',
                                    (activeTab == null && language.is_default) ||
                                    activeTab == language.id
                                        ? 'bg-primary  text-white'
                                        : '',
                                    validationErros.has(
                                        `name.name_${language.id}`
                                    ) || validationErros.has(
                                        `short_description.short_description_${language.id}`
                                    ) || validationErros.has(
                                        'price'
                                    )
                                        ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                        : '',
                                ]"
                                >{{ language.name }}</a
                            >
                        </li>
                    </ul>
                </div>

                <div
                class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6"
                v-for="language in languages"
                :key="language.id"
                :class="
                    (activeTab == null && language.is_default) ||
                    activeTab == language.id
                        ? 'block'
                        : 'hidden'
                    "
                >
                    <div class="relative z-0 w-full group">
                        <label for="reward_name" class="">Reward name</label>
                        <input
                            type="text"
                            name="reward_name"
                            id="reward_name"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            @input="
                                handleMultipleInput(
                                    'reward_name',
                                    $event.target.value,
                                    language
                                )
                            "
                            :value="
                                form['reward_name'] && form['reward_name'][`reward_name_${language.id}`]
                                    ? form['reward_name'][`reward_name_${language.id}`]
                                    : ''
                            "
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`reward_name.reward_name_${language.id}`)"
                            v-text="validationErros.get(`reward_name.reward_name_${language.id}`)"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="point" class="">Point</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            name="point"
                            id="point"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            @input="
                                handleInput(
                                    $event.target.value,
                                    'point'
                                )
                            "
                            :value="
                                form['point'] ? form['point']
                                    : ''
                            "
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`point`)"
                            v-text="validationErros.get(`point`)"
                        ></p>
                    </div>
                </div>

                <button type="submit" class="button-exp-fill" :disabled="loading">Submit</button>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState({
            loading: (state) => state.studentsRewardPointSettings.loading,
            form: (state) => state.studentsRewardPointSettings.form,
            isFormEdit: (state) => state.studentsRewardPointSettings.isFormEdit,
            validationErros: (state) => state.studentsRewardPointSettings.validationErros,
            languages: (state) => state.languages.languages,
        }),
        is_default: {
            get: function () {
                return this.$store.state.languages.form.is_default;
            },
            set: function (val) {
                this.$store.commit("studentsRewardPointSettings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    data() {
        return {
            activeTab: null,
        };
    },
    methods: {
        handleInput(value, key) {
            this.$store.commit("studentsRewardPointSettings/setState", { key, value });
        },
        handleMultipleInput(key, value, language) {
            this.$store.commit("studentsRewardPointSettings/updateState", {
                value: value,
                id: language.id,
                key,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("studentsRewardPointSettings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.student-reward-points.index" })
                );
        },
        changeLanguageTab(language) {
            this.activeTab = language.id;
        },
        fetchStudentRewardPoint() {
            if (this.$route.params.id) {
                let id = this.$route.params.id;

                this.$store.commit("studentsRewardPointSettings/setIsFormEdit", 1);
                this.$store
                    .dispatch("studentsRewardPointSettings/fetchStudentRewardPoint", {
                        id: id,
                        url: `${process.env.MIX_ADMIN_API_URL}student-reward-points/${id}?withRewardPointSettingDetail=1`,
                    })
                    .then((res) => {
                        let keys = [
                            "page",
                        ];
                        this.$store.commit("studentsRewardPointSettings/setState", {
                            key: "id",
                            value: id,
                        });
                        for (var i = 0; i < keys.length; i++) {
                            this.$store.commit("studentsRewardPointSettings/setState", {
                                key: keys[i],
                                value: res.data.data[keys[i]],
                            });
                        }
                        let data =
                            res.data.data && res.data.data.reward_point_setting_detail
                                ? res.data.data.reward_point_setting_detail
                                : [];
                        let obj = {};

                        data.map((res) => {
                            obj["reward_name_" + res.language_id] = res.reward_name;
                        });
                        this.$store.commit("studentsRewardPointSettings/setState", {
                            key: "reward_name",
                            value: obj,
                        });
                    });
            }
        },
    },
    created() {
        this.$store.commit("studentsRewardPointSettings/resetForm");
        this.$store
            .dispatch("languages/fetchLanguages", {
                url: `${process.env.MIX_ADMIN_API_URL}languages?getAll=1`,
            })
            .then((res) => {
                let data = res.data.data;
                let obj = {};
                data.map((res) => {
                    obj["reward_name_" + res.id] = "";
                });
                this.$store.commit("studentsRewardPointSettings/setState", {
                    key: "reward_name",
                    value: obj,
                });
                this.fetchStudentRewardPoint();
            });
    },
};
</script>
