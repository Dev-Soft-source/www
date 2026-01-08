<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Cancel ride settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="driver_cancel_hours" class="">Hours to cancellation (For driver)</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            name="driver_cancel_hours"
                            id="driver_cancel_hours"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.driver_cancel_hours"
                            @input="
                                updateForm('driver_cancel_hours', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('driver_cancel_hours')"
                            v-text="validationErros.get('driver_cancel_hours')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="passenger_cancel_hours" class="">Hours to cancel a booking (For passenger)</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            name="passenger_cancel_hours"
                            id="passenger_cancel_hours"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.passenger_cancel_hours"
                            @input="
                                updateForm('passenger_cancel_hours', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('passenger_cancel_hours')"
                            v-text="validationErros.get('passenger_cancel_hours')"
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
            loading: (state) => state.cancel_ride_settings.loading,
            form: (state) => state.cancel_ride_settings.form,
            validationErros: (state) => state.cancel_ride_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.cancel_ride_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("cancel_ride_settings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("cancel_ride_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("cancel_ride_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.cancel-ride-settings.index", params: { id: 1 } })
                );
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store
                .dispatch("cancel_ride_settings/fetchSetting", { id: id });
        }
    },
};
</script>