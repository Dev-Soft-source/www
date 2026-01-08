<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Create package
                        </h3>
                        <router-link
                            :to="{ name: 'admin.booking-credits.index' }"
                            class="button-exp-fill"
                        >
                            Back
                        </router-link>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="credits_buy" class="">Buy credits</label>
                        <input
                            type="number"
                            name="credits_buy"
                            id="credits_buy"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.credits_buy"
                            @input="
                                updateForm('credits_buy', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('credits_buy')"
                            v-text="validationErros.get('credits_buy')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="credits_get" class="">Get credits</label>
                        <input
                            type="number"
                            name="credits_get"
                            id="credits_get"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.credits_get"
                            @input="
                                updateForm('credits_get', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('credits_get')"
                            v-text="validationErros.get('credits_get')"
                        ></p>
                    </div>
                </div>
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="credits_price" class="">Price</label>
                        <input
                            type="number"
                            name="credits_price"
                            id="credits_price"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.credits_price"
                            @input="
                                updateForm('credits_price', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('credits_price')"
                            v-text="validationErros.get('credits_price')"
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
            loading: (state) => state.credits.loading,
            form: (state) => state.credits.form,
            validationErros: (state) => state.credits.validationErros,
        }),
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("credits/setForm", {
                [field]: value,
            });
        },
        addForm() {
            this.$store
                .dispatch("credits/addForm")
                .then(() =>
                    this.$router.push({ name: "admin.booking-credits.index" })
                );
        },
    },
    created() {
        this.$store.commit("credits/resetForm");
    },
};
</script>