<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Pink ride settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="relative z-0 w-full group">
                    <fieldset>
                        <legend class="sr-only">Phone verify - passenger</legend>
                        <div class="flex items-center mb-4">
                            <input
                                id="verfiy_phone_passenger"
                                name="verfiy_phone_passenger"
                                type="checkbox"
                                value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                :checked="phoneVerifiedPassenger"
                                @change="updateCheckbox('verfiy_phone_passenger')"
                            />
                            <label
                                for="verfiy_phone_passenger"
                                class="ml-2 text-sm font-medium text-gray-900"
                                >Phone verify - passenger</label
                            >
                        </div>
                    </fieldset>
                    <p
                        class="mt-2 text-sm text-red-400"
                        v-if="validationErros.has('verfiy_phone_passenger')"
                        v-text="validationErros.get('verfiy_phone_passenger')"
                    ></p>
                </div>
                <div class="relative z-0 w-full group">
                    <fieldset>
                        <legend class="sr-only">Phone verify - driver</legend>
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
                                >Phone verify - driver</label
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
                        <legend class="sr-only">Email verify - driver</legend>
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
                                >Email verify - driver</label
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
                        <legend class="sr-only">Driver license - driver</legend>
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
                                >Driver license - driver</label
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
            loading: (state) => state.pink_ride_settings.loading,
            form: (state) => state.pink_ride_settings.form,
            validationErros: (state) => state.pink_ride_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.pink_ride_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("pink_ride_settings/setForm", {
                    is_default: val,
                });
            },
        },
        phoneVerifiedPassenger: function() {
            return this.form.verfiy_phone_passenger === '1'; // Check if value is '1'
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
            this.$store.commit("pink_ride_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("pink_ride_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.pink-ride-settings.index", params: { id: 1 } })
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
                .dispatch("pink_ride_settings/fetchSetting", { id: id });
        }
    },
};
</script>