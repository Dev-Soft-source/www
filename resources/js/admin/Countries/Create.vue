<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            {{ isFormEdit ? "Edit" : "Create" }} country
                        </h3>
                        <router-link
                            :to="{ name: 'admin.countries.index' }"
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
                        <label for="abbreviation" class="">Country</label>
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
            loading: (state) => state.countries.loading,
            form: (state) => state.countries.form,
            isFormEdit: (state) => state.countries.isFormEdit,
            validationErros: (state) => state.countries.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.countries.form.is_default;
            },
            set: function (val) {
                this.$store.commit("countries/setForm", {
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
            this.$store.commit("countries/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("countries/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.countries.index" })
                );
        },
    },
    created() {
        this.$store.commit("countries/resetForm");
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.commit("countries/setIsFormEdit", 1);
            this.$store
                .dispatch("countries/fetchCountry", { id: id });
        }
    },
};
</script>