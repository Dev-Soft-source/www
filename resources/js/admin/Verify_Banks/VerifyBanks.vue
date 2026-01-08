<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="verify_banks">Verify banks <small v-if="verify_banks">({{ verify_banks.length }})</small></h3>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row items-center justify-between gap-4 py-4">
                    <div>
                        show
                       <select class="rounded-md px-3 pr-8 py-1" v-model="limit" @input="updateLimit($event.target.value)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                             <option value="100">100</option>
                        </select>
                        verify banks
                    </div>
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="text" id="table-search-withdrawals"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for verify banks" v-model="quickSearch" />
                    </div>
                </div>
                <div class="container space-y-8 mx-auto">
                    <div class="space-y-2">
                        <div class="bg-white shadow-lg hover:shadow-xl rounded-md overflow-x-auto">
                            <table class="table overflow-x-auto table-auto w-full leading-normal text-base md:text-base lg:text-lg">
                                <thead class="text-white">
                                    <tr class="hidden md:table-row">
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            #ID
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Account title
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Account number
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Branch
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Status
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="verify_bank in verify_banks" :key="verify_bank.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 border-b md:border-none relative">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <label
                                                        class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                        for="">#ID</label>
                                                </div>
                                            </div>
                                            <div class="font-medium text-gray-900 flex mt-1 items-center gap-1">
                                                <span> {{ verify_bank.random_id }}</span>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Account Title</label>
                                            <div>{{ verify_bank . bank_title }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Account Number</label>
                                            <div class="">{{ verify_bank . acc_no }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Branch</label>
                                            <div>{{ verify_bank . branch }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Status</label>
                                            <div>{{ verify_bank . status }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 gap-2 justify-center items-center hidden md:block space-y-2 space-x-2">
                                            <div v-if="verify_bank . status === 'pending'" class="flex items-center space-x-2">
                                                <button class="inline-flex items-center button-exp-fill cursor-pointer" type="button" v-on:click="toggleModal2(verify_bank)">
                                                    Verify
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                                v-if="pagination">
                                <div class="flex flex-col sm:flex-col md:flex-row gap-4 justify-between items-center w-full">
                                    <div>
                                        <p class="text-sm text-gray-700" v-if="pagination.current_page">
                                            Page {{ pagination . current_page }} of {{ pagination . last_page }}
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                            aria-label="Pagination"
                                            v-if="pagination.next_page_url || pagination.prev_page_url">
                                            <a href="#"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                                v-bind:class="[{disabled: !pagination.prev_page_url}]"
                                                @click="fetchVerifyBanks(pagination.prev_page_url)">
                                                <span class="sr-only">Previous</span>
                                                <svg class="h-5 w-5" x-description="Heroicon name: solid/chevron-left"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>

                                            <a href="#"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                                v-bind:class="[{disabled: !pagination.next_page_url}]"
                                                @click.prevent="fetchVerifyBanks(pagination.next_page_url)">
                                                <span class="sr-only">Next</span>
                                                <svg class="h-5 w-5"
                                                    x-description="Heroicon name: solid/chevron-right"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                            </div> -->
                            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6" v-if="pagination && pagination.links && pagination.links.length">
                            <div class="flex flex-col sm:flex-col md:flex-row gap-4 justify-between items-center w-full">
                                <div>
                                    <p class="text-sm text-gray-700" v-if="pagination.current_page">
                                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="#"
                                            class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                            :class="{ 'opacity-50 cursor-not-allowed': !pagination.prev_page_url }"
                                            @click.prevent="pagination.prev_page_url && fetchVerifyBanks(pagination.prev_page_url)">
                                            Previous
                                        </a>
                                        <template v-for="(link, index) in pagination.links" :key="index">
                                            <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                href="#"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                @click.prevent="fetchVerifyBanks(link.url)">
                                                <span v-html="link.label"></span>
                                            </a>
                                        </template>
                                        <a href="#"
                                            class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                            :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                            @click.prevent="pagination.next_page_url && fetchVerifyBanks(pagination.next_page_url)">
                                            Next
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
            <div class="relative my-6 mx-auto w-1/2 max-w-6xl">
                <!--content-->
                <div class=" animate__animated animate__fadeIn border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                    <!--header-->
                    <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t">
                        <h5 class="modal-title"><b>{{ selectedWithdrawal.first_name }} {{ selectedWithdrawal.last_name }}</b></h5>
                    </div>
                    <!--body-->
                    <div class="relative p-6 flex-auto">
                        <table class="table table-striped table-fixed w-full">
                            <tbody>
                                <tr>
                                    <td><b>Payout method:</b></td><td>{{ selectedWithdrawal.method }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="selectedWithdrawal.method == 'paypal_transfer'">
                            <h4 class="flex items-center pt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-paypal" viewBox="0 0 16 16">
                                    <path d="M14.06 3.713c.12-1.071-.093-1.832-.702-2.526C12.628.356 11.312 0 9.626 0H4.734a.7.7 0 0 0-.691.59L2.005 13.509a.42.42 0 0 0 .415.486h2.756l-.202 1.28a.628.628 0 0 0 .62.726H8.14c.429 0 .793-.31.862-.731l.025-.13.48-3.043.03-.164.001-.007a.351.351 0 0 1 .348-.297h.38c1.266 0 2.425-.256 3.345-.91.379-.27.712-.603.993-1.005a4.942 4.942 0 0 0 .88-2.195c.242-1.246.13-2.356-.57-3.154a2.687 2.687 0 0 0-.76-.59l-.094-.061ZM6.543 8.82a.695.695 0 0 1 .321-.079H8.3c2.82 0 5.027-1.144 5.672-4.456l.003-.016c.217.124.4.27.548.438.546.623.679 1.535.45 2.71-.272 1.397-.866 2.307-1.663 2.874-.802.57-1.842.815-3.043.815h-.38a.873.873 0 0 0-.863.734l-.03.164-.48 3.043-.024.13-.001.004a.352.352 0 0 1-.348.296H5.595a.106.106 0 0 1-.105-.123l.208-1.32.845-5.214Z"/>
                                </svg> PayPal Details
                            </h4><hr style="margin-top:10px;">
                            <table class="table table-striped table-fixed w-full">
                                <tbody>
                                    <tr>
                                        <td><b>PayPal email:</b></td><td>{{ selectedWithdrawal.paypal_email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="selectedWithdrawal.method == 'bank_transfer'">
                            <h4 class="flex items-center pt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                </svg> Bank Details
                            </h4><hr style="margin-top:10px;">
                            <table class="table table-striped table-fixed w-full">
                                <tbody>
                                    <tr>
                                        <td><b>Account Number:</b></td><td>{{ selectedWithdrawal.account_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Bank Name:</b></td><td>{{ selectedWithdrawal.bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>IFSC:</b></td><td>{{ selectedWithdrawal.ifsc_code }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Country:</b></td><td>{{ selectedWithdrawal.country }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--footer-->
                    <div class="flex items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b">
                        <button class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold text-sm px-6 py-3 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>

        <!-- Modal2 -->
        <div v-if="showModal2" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
            <div class="relative my-6 mx-auto w-1/2 max-w-6xl">
                <!--content-->
                <div class=" animate__animated animate__fadeIn border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                    <!--header-->
                    <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t">
                        <h5 class="modal-title"><b>Enter Amount</b></h5>
                    </div>
                    <!--body-->
                    <div class="relative p-6 flex-auto">
                        <h4 class="flex items-center pt-3">
                            Enter Amount
                        </h4><hr style="margin-top:10px;">
                        <input type="number" v-model="rejectionReason" required>
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has('admin_verify_amount')"
                            v-text="validationErros.get('admin_verify_amount')"
                        ></p>
                    </div>
                    <!--footer-->
                    <div class="flex items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b">
                        <a href="#"
                            class="inline-flex items-center bg-blue-500 hover:bg-blue-600 button-exp-fill cursor-pointer border-blue-500"
                            @click.prevent="rejectWithdrawal(selectedWithdrawal)">
                            Submit
                        </a>
                        <button class="inline-flex items-center ml-1 text-blue-500 bg-transparent border border-solid border-blue-500 hover:bg-blue-500 hover:text-white active:bg-blue-600 button-exp-fill cursor-pointer" type="button" v-on:click="toggleModal2()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showModal2" class="opacity-25 fixed inset-0 z-40 bg-black"></div>
    </AppLayout>
</template>

<script>
import _ from "lodash";
import { mapState } from "vuex";
import LoadingTable from "../components/LoadingTable.vue";
export default {
    components: {
        LoadingTable,
    },
    computed: {
        ...mapState({
            verify_banks: (state) => state.verify_banks.verify_banks,
            pagination: (state) => state.verify_banks.pagination,
            searchParam: (state) => state.verify_banks.searchParam,
            loading: (state) => state.verify_banks.loading,
            validationErros: (state) => state.verify_banks.validationErros,
        }),
        limit: {
      get() {
        return this.$store.state.verify_banks.limit;
      },
      set(value) {
        this.$store.commit('verify_banks/setLimit', value);
      }
    }
    },
    data() {
        return {
            quickSearch: null,
            showModal: false,
            showModal2: false,
            selectedWithdrawal: null,
            rejectionReason: '',
        };
    },
    methods: {
        addRandomChars(value) {
            if (!value) return "";
            const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            let randomChars = "";

            for (let i = 0; i < 4; i++) {
                randomChars += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return `${randomChars}-${value}`;
        },
        fetchVerifyBanks(page_url) {
            this.$store.dispatch("verify_banks/fetchVerifyBanks", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("verify_banks/setLimit", value);
            this.$store.dispatch("verify_banks/fetchVerifyBanks");
        },
        toggleModal(withdrawal) {
            this.selectedWithdrawal = withdrawal;
            this.showModal = !this.showModal;
        },
        toggleModal2(withdrawal) {
            this.selectedWithdrawal = withdrawal;
            this.showModal2 = !this.showModal2;
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("verify_banks/setSearchParam", this.quickSearch);
            this.$store.dispatch("verify_banks/fetchVerifyBanks");
        }, 500),
        getWithdrawalStatus(withdrawalValue) {
            const withdrawalNumber = parseInt(withdrawalValue);
            if (withdrawalNumber === 0) {
                return "Pending";
            } else if (withdrawalNumber === 1) {
                return "Accepted";
            } else if (withdrawalNumber === 2) {
                return "Rejected";
            }
        },
        acceptWithdrawal(withdrawal) {
            if (withdrawal.status !== 1) {
                this.$swal
                    .fire({
                        title: "Are you sure?",
                        text: "Do you really want to accept this request?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, accept it!",
                        showCloseButton: true,
                        customClass: {
                            confirmButton: 'inline-flex items-center button-exp-fill',
                            cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                        }
                    })
                    .then((result) => {
                        if (result.isConfirmed) {
                            this.$store.dispatch("verify_banks/acceptWithdrawal", {
                                    id: withdrawal.id,
                                })
                        }
                    });
            }
        },
        rejectWithdrawal(withdrawal) {
            this.$store.dispatch("verify_banks/rejectWithdrawal", {
                    id: withdrawal.id,
                    admin_verify_amount: this.rejectionReason,
                }).then(() => {
                    this.toggleModal2(withdrawal);

                    this.$store.dispatch("verify_banks/fetchVerifyBanks");
                }).catch((error) => {
                    console.error("Error rejecting withdrawal:", error);
                });

            if (this.validationErros.has('admin_verify_amount')) {
                this.toggleModal2(withdrawal);
            }
        },
    },
    created() {
        this.$store.commit("verify_banks/setLimit", 100);
        this.$store.commit("verify_banks/setSortBy", "id");
        this.$store.commit("verify_banks/setSortType", "desc");
        this.$store.commit("verify_banks/setSearchParam", '');
        this.$store.dispatch("verify_banks/fetchVerifyBanks");
    },
    watch: {
        quickSearch: function () {
            this.quickSearchFilter();
        },
    },
};
</script>
<style>
    .swal2-styled.swal2-cancel {
        border: 0;
        border-radius: .25em;
        background: initial;
        background-color: #6e7881;
        color: #fff;
        font-size: 1em;
        width: fit-content !important;
    }
</style>