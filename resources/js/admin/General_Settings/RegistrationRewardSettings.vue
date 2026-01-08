<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Registration reward settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="passenger_credit_booking" class="">Passenger Credit Booking</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            name="passenger_credit_booking"
                            id="passenger_credit_booking"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.passenger_credit_booking"
                            @input="
                                updateForm('passenger_credit_booking', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('passenger_credit_booking')"
                            v-text="validationErros.get('passenger_credit_booking')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="driver_reward_point" class="">Driver reward point</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            name="driver_reward_point"
                            id="driver_reward_point"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.driver_reward_point"
                            @input="
                                updateForm('driver_reward_point', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('driver_reward_point')"
                            v-text="validationErros.get('driver_reward_point')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="student_reward_point" class="">Student reward point</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            name="student_reward_point"
                            id="student_reward_point"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.student_reward_point"
                            @input="
                                updateForm('student_reward_point', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('student_reward_point')"
                            v-text="validationErros.get('student_reward_point')"
                        ></p>
                    </div>
                </div>

                <button type="submit" class="button-exp-fill" :disabled="loading">Save changes</button>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState({
            loading: (state) => state.registration_reward_settings.loading,
            form: (state) => state.registration_reward_settings.form,
            validationErros: (state) => state.registration_reward_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.registration_reward_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("registration_reward_settings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("registration_reward_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("registration_reward_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.registration-reward-settings.index", params: { id: 1 } })
                );
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store
                .dispatch("registration_reward_settings/fetchSetting", { id: id });
        }
    },
};
</script>