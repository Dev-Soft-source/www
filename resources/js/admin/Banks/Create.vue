<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            {{ isFormEdit ? "Edit" : "Create" }} bank
                        </h3>
                        <router-link
                            :to="{ name: 'admin.banks.index' }"
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
                        <label for="abbreviation" class="">Name</label>
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
                        <label for="bank_to_bank_fee" class="">Same bank fee</label>
                        <input
                            type="number"
                            min="0"
                            name="bank_to_bank_fee"
                            id="bank_to_bank_fee"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.bank_to_bank_fee"
                            @input="
                                updateForm('bank_to_bank_fee', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('bank_to_bank_fee')"
                            v-text="validationErros.get('bank_to_bank_fee')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="other_bank_fee" class="">Other bank fee</label>
                        <input
                            type="number"
                            min="0"
                            name="other_bank_fee"
                            id="other_bank_fee"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.other_bank_fee"
                            @input="
                                updateForm('other_bank_fee', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('other_bank_fee')"
                            v-text="validationErros.get('other_bank_fee')"
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
            loading: (state) => state.banks.loading,
            form: (state) => state.banks.form,
            isFormEdit: (state) => state.banks.isFormEdit,
            validationErros: (state) => state.banks.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.banks.form.is_default;
            },
            set: function (val) {
                this.$store.commit("banks/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("banks/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("banks/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.banks.index" })
                );
        },
    },
    created() {
        this.$store.commit("banks/resetForm");
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.commit("banks/setIsFormEdit", 1);
            this.$store
                .dispatch("banks/fetchBank", { id: id });
        }
    },
};
</script>