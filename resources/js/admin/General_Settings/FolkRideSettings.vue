<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Extra-Care settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="average_rating" class="">Average rating</label>
                        <input
                            type="number"
                            min="0"
                            max="5"
                            step="0.5"
                            name="average_rating"
                            id="average_rating"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.average_rating"
                            @input="
                                updateForm('average_rating', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('average_rating')"
                            v-text="validationErros.get('average_rating')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="driver_age" class="">Driver Age</label>
                        <input
                            type="number"
                            min="18"
                            name="driver_age"
                            id="driver_age"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.driver_age"
                            @input="
                                updateForm('driver_age', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('driver_age')"
                            v-text="validationErros.get('driver_age')"
                        ></p>
                    </div>
                    
                    <div class="relative z-0 w-full group">
                        <label for="extra_rides_trip_limit" class="">Minimum rides limit for extra care rides</label>
                        <input
                            type="number"
                            name="extra_rides_trip_limit"
                            id="extra_rides_trip_limit"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.extra_rides_trip_limit"
                            @input="
                                updateForm('extra_rides_trip_limit', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('extra_rides_trip_limit')"
                            v-text="validationErros.get('extra_rides_trip_limit')"
                        ></p>
                    </div>
                </div>

                <div class="relative z-0 w-full group mt-8">
                    <fieldset>
                        <legend class="sr-only">Phone verify</legend>
                        <div class="flex items-center mb-4">
                            <input
                                id="verfiy_phone"
                                name="verfiy_phone"
                                type="checkbox"
                                value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                :checked="phoneVerified"
                                @change="updateCheckbox('verfiy_phone')"
                            />
                            <label
                                for="verfiy_phone"
                                class="ml-2 text-sm font-medium text-gray-900"
                                >Phone verify</label
                            >
                        </div>
                    </fieldset>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has('verfiy_phone')"
                        v-text="validationErros.get('verfiy_phone')"
                    ></p>
                </div>
                <div class="relative z-0 w-full group">
                    <fieldset>
                        <legend class="sr-only">Email verify</legend>
                        <div class="flex items-center mb-4">
                            <input
                                id="verify_email"
                                name="verify_email"
                                type="checkbox"
                                value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                :checked="emailVerified"
                                @change="updateCheckbox('verify_email')"
                            />
                            <label
                                for="verify_email"
                                class="ml-2 text-sm font-medium text-gray-900"
                                >Email verify</label
                            >
                        </div>
                    </fieldset>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has('verify_email')"
                        v-text="validationErros.get('verify_email')"
                    ></p>
                </div>
                <div class="relative z-0 w-full group">
                    <fieldset>
                        <legend class="sr-only">Driver license</legend>
                        <div class="flex items-center mb-4">
                            <input
                                id="driver_license"
                                name="driver_license"
                                type="checkbox"
                                value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                :checked="licenseVerified"
                                @change="updateCheckbox('driver_license')"
                            />
                            <label
                                for="driver_license"
                                class="ml-2 text-sm font-medium text-gray-900"
                                >Driver license</label
                            >
                        </div>
                    </fieldset>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has('driver_license')"
                        v-text="validationErros.get('driver_license')"
                    ></p>
                </div>
                <div class="relative z-0 w-full group">
                    <fieldset>
                        <legend class="sr-only">Profile complete</legend>
                        <div class="flex items-center mb-4">
                            <input
                                id="profile_complete"
                                name="profile_complete"
                                type="checkbox"
                                value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                :checked="profileComplete"
                                @change="updateCheckbox('profile_complete')"
                            />
                            <label
                                for="profile_complete"
                                class="ml-2 text-sm font-medium text-gray-900"
                                >Profile complete</label
                            >
                        </div>
                    </fieldset>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has('profile_complete')"
                        v-text="validationErros.get('profile_complete')"
                    ></p>
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
            loading: (state) => state.folk_ride_settings.loading,
            form: (state) => state.folk_ride_settings.form,
            validationErros: (state) => state.folk_ride_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.folk_ride_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("folk_ride_settings/setForm", {
                    is_default: val,
                });
            },
        },
        phoneVerified: function() {
            return this.form.verfiy_phone === '1'; // Check if value is '1'
        },
        emailVerified: function() {
            return this.form.verify_email === '1'; // Check if value is '1'
        },
        licenseVerified: function() {
            return this.form.driver_license === '1'; // Check if value is '1'
        },
        profileComplete: function() {
            return this.form.profile_complete === '1'; // Check if value is '1'
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("folk_ride_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("folk_ride_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.folk-ride-settings.index", params: { id: 1 } })
                );
        },
        updateCheckbox(field) {
            // Toggle the value of the checkbox in the form data
            this.form[field] = this.form[field] === '1' ? '0' : '1';
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store
                .dispatch("folk_ride_settings/fetchSetting", { id: id });
        }
    },
};
</script>