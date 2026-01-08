<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Review settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="leave_review_days" class="">Days to leave the review</label>
                        <input
                            type="number"
                            min="1"
                            name="leave_review_days"
                            id="leave_review_days"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.leave_review_days"
                            @input="
                                updateForm('leave_review_days', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('leave_review_days')"
                            v-text="validationErros.get('leave_review_days')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="respond_review_days" class="">Days to respond the review</label>
                        <input
                            type="number"
                            min="1"
                            name="respond_review_days"
                            id="respond_review_days"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.respond_review_days"
                            @input="
                                updateForm('respond_review_days', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('respond_review_days')"
                            v-text="validationErros.get('respond_review_days')"
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
            loading: (state) => state.review_settings.loading,
            form: (state) => state.review_settings.form,
            validationErros: (state) => state.review_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.review_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("review_settings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("review_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("review_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.review-settings.index", params: { id: 1 } })
                );
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store
                .dispatch("review_settings/fetchSetting", { id: id });
        }
    },
};
</script>