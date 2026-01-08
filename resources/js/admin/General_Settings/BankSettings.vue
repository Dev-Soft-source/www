<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Bank settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="bank" class="">Bank name</label>
                        <select
                            @change="
                                updateForm('bank_id', $event.target.value)
                            "
                            class="can-exp-input w-full block border border-gray-300 rounded"
                        >
                            <option value="">Select bank...</option>
                            <option
                                v-for="bank in banks"
                                :key="bank.id"
                                :value="bank.id"
                                :selected="bank.id == form.bank_id"
                            >
                                {{ bank.name }}
                            </option>
                        </select>
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('bank_id')"
                            v-text="validationErros.get('bank_id')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="bank_title" class="">Account title</label>
                        <input
                            type="text"
                            name="bank_title"
                            id="bank_title"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.bank_title"
                            @input="
                                updateForm('bank_title', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('bank_title')"
                            v-text="validationErros.get('bank_title')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="acc_no" class="">Account number</label>
                        <input
                            type="text"
                            name="acc_no"
                            id="acc_no"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.acc_no"
                            @input="
                                updateForm('acc_no', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('acc_no')"
                            v-text="validationErros.get('acc_no')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="iban" class="">Financial institution number</label>
                        <input
                            type="text"
                            name="iban"
                            id="iban"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.iban"
                            @input="
                                updateForm('iban', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('iban')"
                            v-text="validationErros.get('iban')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="address" class="">Bank address</label>
                        <input
                            type="text"
                            name="address"
                            id="address"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.address"
                            @input="
                                updateForm('address', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('address')"
                            v-text="validationErros.get('address')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="branch" class="">Transit (Branch) number</label>
                        <input
                            type="text"
                            name="branch"
                            id="branch"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.branch"
                            @input="
                                updateForm('branch', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('branch')"
                            v-text="validationErros.get('branch')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="paypal_email" class="">PayPal email</label>
                        <input
                            type="text"
                            name="paypal_email"
                            id="paypal_email"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            :value="form.paypal_email"
                            @input="
                                updateForm('paypal_email', $event.target.value)
                            "
                            tabindex="-1"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('paypal_email')"
                            v-text="validationErros.get('paypal_email')"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="set_default" class="">Set default</label>
                        <div class="flex space-x-3">
                            <div>
                                <input id="bordered-radio-bank" type="radio" name="set_default" v-model="form.set_default" :value="'bank'" @change="updateForm('set_default', $event.target.value)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-bank">Bank</label>
                            </div>
                            <div>
                                <input id="bordered-radio-paypal" type="radio" name="set_default" v-model="form.set_default" :value="'paypal'" @change="updateForm('set_default', $event.target.value)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-paypal">PayPal</label>
                            </div>
                        </div>
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('set_default')"
                            v-text="validationErros.get('set_default')"
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
    data() {
        return {
            banks: [],
        };
    },
    computed: {
        ...mapState({
            loading: (state) => state.bank_settings.loading,
            form: (state) => state.bank_settings.form,
            validationErros: (state) => state.bank_settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.bank_settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("bank_settings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        updateForm(field, value) {
            this.$store.commit("bank_settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("bank_settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.bank-settings.index" })
                );
        },
        fetchBanks() {
            axios.get(`${process.env.MIX_ADMIN_API_URL}banks`) // Adjust the URL based on your actual API route.
                .then(response => {
                    this.banks = response.data.data;
                })
                .catch(error => {
                    // Handle any errors if needed.
                });
        },
    },
    created() {
        // Fetch the list of banks when the component is created.
        this.fetchBanks();

        this.$store
            .dispatch("bank_settings/fetchSetting");
    },
};
</script>