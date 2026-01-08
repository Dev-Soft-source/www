<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            {{ isFormEdit ? "Edit" : "Create" }} city
                        </h3>
                        <router-link :to="{ name: 'admin.cities.index' }" class="button-exp-fill">
                            Back
                        </router-link>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="country" class="">Country</label>
                        <select v-model="form.country_id" @change="fetchStates"
                            class="can-exp-input w-full block border border-gray-300 rounded">
                            <option value="">Select country...</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('country_id')"
                            v-text="validationErros.get('country_id')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="state" class="">State</label>
                        <select @change="
                            updateForm('state_id', $event.target.value)
                            " class="can-exp-input w-full block border border-gray-300 rounded">
                            <option value="">Select state...</option>
                            <option v-for="state in states" :key="state.id" :value="state.id"
                                :selected="state.id == form.state_id">
                                {{ state.name }}
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('state_id')"
                            v-text="validationErros.get('state_id')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="abbreviation" class="">Name</label>
                        <input type="text" name="name" id="name"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.name" @input="
                                updateForm('name', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('name')"
                            v-text="validationErros.get('name')"></p>
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
            states: [],
        };
    },
    computed: {
        ...mapState({
            loading: (state) => state.cities.loading,
            form: (state) => state.cities.form,
            isFormEdit: (state) => state.cities.isFormEdit,
            validationErros: (state) => state.cities.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.cities.form.is_default;
            },
            set: function (val) {
                this.$store.commit("cities/setForm", {
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
            this.$store.commit("cities/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("cities/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.cities.index" })
                );
        },
        // fetchStates() {
        //     axios.get(`${process.env.MIX_ADMIN_API_URL}states`)
        //         .then(response => {
        //             this.states = response.data.data;
        //         })
        // },
        fetchCountries() {
            axios.get(`${process.env.MIX_ADMIN_API_URL}countries`)
                .then(response => {
                    this.countries = response.data.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },

        fetchStates() {
            if (!this.form.country_id) {
                this.states = [];
                return;
            }

            axios.get(`${process.env.MIX_ADMIN_API_URL}countries/${this.form.country_id}/states`)
                .then(response => {
                    this.states = response.data.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
    created() {
        // Fetch the list of states when the component is created.
        this.fetchStates();
        this.fetchCountries();

        this.$store.commit("cities/resetForm");
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.commit("cities/setIsFormEdit", 1);
            this.$store
                .dispatch("cities/fetchCity", { id: id });
        }
    },
};
</script>
