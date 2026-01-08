<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Referral system settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div
                    class="border rounded w-full"
                    :class="
                        collapseStates[0] ? 'bg-gray-50' : ''
                    "
                >
                    <div
                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                        @click.prevent="
                            collapseStates[0] =
                                !collapseStates[0]
                        "
                    >
                        <h3 class="text-white">
                            Passenger Referral Settings
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
                        v-if="collapseStates[0]"
                    >
                        <div class="relative z-0 w-full group">
                            <label for="p_2_p_booking_credit" class="">Passenger referred Passenger</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="p_2_p_booking_credit"
                                id="p_2_p_booking_credit"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.p_2_p_booking_credit"
                                @input="
                                    updateForm('p_2_p_booking_credit', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('p_2_p_booking_credit')"
                                v-text="validationErros.get('p_2_p_booking_credit')"
                            ></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="p_2_s_booking_credit" class="">Passenger referred Student</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="p_2_s_booking_credit"
                                id="p_2_s_booking_credit"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.p_2_s_booking_credit"
                                @input="
                                    updateForm('p_2_s_booking_credit', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('p_2_s_booking_credit')"
                                v-text="validationErros.get('p_2_s_booking_credit')"
                            ></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="p_2_d_booking_credit" class="">Passenger referred Driver</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="p_2_d_booking_credit"
                                id="p_2_d_booking_credit"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.p_2_d_booking_credit"
                                @input="
                                    updateForm('p_2_d_booking_credit', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('p_2_d_booking_credit')"
                                v-text="validationErros.get('p_2_d_booking_credit')"
                            ></p>
                        </div>
                    </div>
                </div>

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
                            Driver Referral Settings
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
                            <label for="d_2_p_reward_point" class="">Driver referred Passenger</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="d_2_p_reward_point"
                                id="d_2_p_reward_point"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.d_2_p_reward_point"
                                @input="
                                    updateForm('d_2_p_reward_point', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('d_2_p_reward_point')"
                                v-text="validationErros.get('d_2_p_reward_point')"
                            ></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="d_2_s_reward_point" class="">Driver referred Student</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="d_2_s_reward_point"
                                id="d_2_s_reward_point"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.d_2_s_reward_point"
                                @input="
                                    updateForm('d_2_s_reward_point', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('d_2_s_reward_point')"
                                v-text="validationErros.get('d_2_s_reward_point')"
                            ></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="d_2_d_rewad_point" class="">Driver referred Driver</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="d_2_d_rewad_point"
                                id="d_2_d_rewad_point"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.d_2_d_rewad_point"
                                @input="
                                    updateForm('d_2_d_rewad_point', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('d_2_d_rewad_point')"
                                v-text="validationErros.get('d_2_d_rewad_point')"
                            ></p>
                        </div>
                    </div>
                </div>

                <div
                    class="border rounded w-full"
                    :class="
                        collapseStates[0] ? 'bg-gray-50' : ''
                    "
                >
                    <div
                        class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                        @click.prevent="
                            collapseStates[2] =
                                !collapseStates[2]
                        "
                    >
                        <h3 class="text-white">
                            Student Referral Settings
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
                        v-if="collapseStates[2]"
                    >
                        <div class="relative z-0 w-full group">
                            <label for="s_2_p_reward_point" class="">Student referred Passenger</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="s_2_p_reward_point"
                                id="s_2_p_reward_point"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.s_2_p_reward_point"
                                @input="
                                    updateForm('s_2_p_reward_point', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('s_2_p_reward_point')"
                                v-text="validationErros.get('s_2_p_reward_point')"
                            ></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="s_2_s_reward_point" class="">Student referred Student</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="s_2_s_reward_point"
                                id="s_2_s_reward_point"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.s_2_s_reward_point"
                                @input="
                                    updateForm('s_2_s_reward_point', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('s_2_s_reward_point')"
                                v-text="validationErros.get('s_2_s_reward_point')"
                            ></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="s_2_d_reward_point" class="">Student referred Driver</label>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="s_2_d_reward_point"
                                id="s_2_d_reward_point"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                placeholder=" "
                                :value="form.s_2_d_reward_point"
                                @input="
                                    updateForm('s_2_d_reward_point', $event.target.value)
                                "
                                tabindex="-1"
                            />
                            <p
                                class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('s_2_d_reward_point')"
                                v-text="validationErros.get('s_2_d_reward_point')"
                            ></p>
                        </div>
                    </div>
                </div>
                <button type="submit" class="button-exp-fill mt-3" :disabled="loading">Save changes</button>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import { mapState } from "vuex";
export default {
    data() {
        return {
            collapseStates: [true],
        };
    },
    computed: {
        ...mapState({
            loading: (state) => state.referral_system_settings.loading,
            form: (state) => state.referral_system_settings.form,
            validationErros: (state) => state.referral_system_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.referral_system_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("referral_system_settings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("referral_system_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("referral_system_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.referral-system-settings.index", params: { id: 1 } })
                );
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store
                .dispatch("referral_system_settings/fetchSetting", { id: id });
        }
    },
};
</script>
