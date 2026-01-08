<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            {{ isFormEdit ? "Edit" : "Create" }} state
                        </h3>
                        <router-link
                            :to="{ name: 'admin.states.index' }"
                            class="button-exp-fill"
                        >
                            Back
                        </router-link>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="name" class="">State</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.name"
                            @input="
                                updateForm('name', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('name')"
                            v-text="validationErros.get('name')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="country" class="">Country</label>
                        <select
                            @change="
                                updateForm('country_id', $event.target.value)
                            "
                            class="can-exp-input w-full block border border-gray-300 rounded"
                        >
                            <option value="">Select country...</option>
                            <option
                                v-for="country in countries"
                                :key="country.id"
                                :value="country.id"
                                :selected="country.id == form.country_id"
                            >
                                {{ country.name }}
                            </option>
                        </select>
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('country_id')"
                            v-text="validationErros.get('country_id')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="ride_limit" class="">Ride limit per day</label>
                        <input
                            type="number"
                            name="ride_limit"
                            id="ride_limit"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.ride_limit"
                            @input="
                                updateForm('ride_limit', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('ride_limit')"
                            v-text="validationErros.get('ride_limit')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="tax" class="">Tax</label>
                        <input
                            type="number"
                            step="any"
                            name="tax"
                            id="tax"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.tax"
                            @input="
                                updateForm('tax', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('tax')"
                            v-text="validationErros.get('tax')"
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
    data() {
    return {
        countries: [],
    };
},
    computed: {
        ...mapState({
            loading: (state) => state.states.loading,
            form: (state) => state.states.form,
            isFormEdit: (state) => state.states.isFormEdit,
            validationErros: (state) => state.states.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.states.form.is_default;
            },
            set: function (val) {
                this.$store.commit("states/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateLangName(lang) {
            lang = JSON.parse(lang);
            this.updateForm("name", lang.name);
            this.updateForm("abbreviation", lang.code);
            this.updateForm("native_name", lang.nativeName);
        },
        updateForm(field, value) {
            this.$store.commit("states/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("states/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.states.index" })
                );
        },
        fetchCountries() {
        axios.get(`${process.env.MIX_ADMIN_API_URL}countries`) // Adjust the URL based on your actual API route.
            .then(response => {
                this.countries = response.data.data;
            })
            .catch(error => {
                // Handle any errors if needed.
            });
    },
    },
    created() {
        this.$store.commit("states/resetForm");

        // Fetch the list of countries when the component is created.
        this.fetchCountries();
    
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.commit("states/setIsFormEdit", 1);
            this.$store.dispatch("states/fetchState", { id: id });
        }
    },
};
</script>