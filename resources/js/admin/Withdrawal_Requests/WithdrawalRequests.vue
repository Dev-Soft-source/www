<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="withdrawals">All withdrawals <small v-if="withdrawals">({{ withdrawals.length }})</small></h3>
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
                        withdrawals
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
                        <form autocomplete="off" @submit.prevent>
                        <input type="text" id="table-search-withdrawals"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for withdrawals" v-model="quickSearch" />
                        </form>
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
                                            Requested by
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Amount requested
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="withdrawal in withdrawals" :key="withdrawal.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Requested by</label>
                                            <div>{{ withdrawal . first_name }} {{ withdrawal . last_name }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Amount requested</label>
                                            <div>Requested: ${{ withdrawal . amount }} CAD</div>
                                            <div v-if="withdrawal.bank_detail">
                                                <div v-if="withdrawal.bank_detail.payment_status === 'pending'">Paid for verification: ${{ withdrawal.bank_detail.admin_verify_amount }} CAD</div>
                                                <div v-if="adminSetting && adminSetting.bank">
                                                    <span v-if="adminSetting.bank_id === withdrawal.bank_detail.bank_id">Bank to bank fee: ${{ adminSetting.bank.bank_to_bank_fee }} CAD</span>
                                                    <span v-else>Other bank fee: {{ adminSetting.bank.other_bank_fee }}</span>
                                                </div>
                                            </div>
                                            <div>Total Amount to transfer: ${{ calculateTotal(withdrawal) }} CAD</div>
                                        </td>
                                        <td class="p-2 md:p-3 gap-2 justify-center items-center hidden md:block space-y-2 space-x-2">
                                            <div class="flex items-center space-x-2">
                                                <button class="inline-flex items-center button-exp-fill" type="button" v-on:click="toggleModal(withdrawal)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button class="inline-flex items-center button-exp-fill cursor-pointer" type="button" v-on:click="toggleModal2(withdrawal)">
                                                    Transfer
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
                                                @click="fetchWithdrawals(pagination.prev_page_url)">
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
                                                @click.prevent="fetchWithdrawals(pagination.next_page_url)">
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
                                            @click.prevent="pagination.prev_page_url && fetchWithdrawals(pagination.prev_page_url)">
                                            Previous
                                        </a>
                                        <template v-for="(link, index) in pagination.links" :key="index">
                                            <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                href="#"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                @click.prevent="fetchWithdrawals(link.url)">
                                                <span v-html="link.label"></span>
                                            </a>
                                        </template>
                                        <a href="#"
                                            class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                            :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                            @click.prevent="pagination.next_page_url && fetchWithdrawals(pagination.next_page_url)">
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
            <div class="relative my-6 mx-auto max-w-4xl w-full">
                <!--content-->
                <div class=" animate__animated animate__fadeIn border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none w-full">
                    <!--header-->
                    <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t w-full">
                        <h5 class="modal-title"><b>{{ selectedWithdrawal.first_name }} {{ selectedWithdrawal.last_name }}</b></h5>
                    </div>
                    <!--body-->
                    <div class="relative p-6 flex-auto h-80 overflow-auto">
                        <table class="table table-striped table-fixed w-full">
                            <tbody>
                                <tr>
                                    <td><b>Payout Summary:</b></td><td>{{ selectedWithdrawal.method }}</td>
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
                        <table class="table table-striped table-fixed w-full">
                                <tbody v-for="booking in selectedWithdrawal.bookings" :key="booking.id">
                                <tr>
                                    <td><b>Booking user:</b></td><td>{{ booking.passenger.first_name }}</td>
                                    <td><b>seats:</b></td><td>{{ booking.seats }}</td>
                                    <td><b>Total amount:</b></td><td>{{ Number(booking.fare).toFixed(2) }}</td>
                                    <td><b>Ride ID:</b></td><td>{{ booking.ride_id }}</td>
                                    <td><b>From:</b></td><td>{{ booking.ride.date }}</td>
                                    <td><b>To:</b></td><td>{{ booking.ride.completed_date }}</td>
                                </tr>
                                
                            </tbody>
                        </table>
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
                        <h5 class="modal-title"><b>Payout method:</b></h5>
                    </div>
                    <!--body-->
                    <div v-if="selectedWithdrawal.bank_detail && selectedWithdrawal.bank_detail.set_default === 'bank'" class="relative p-6 flex-auto h-96 ove">
                        <table class="table table-striped table-fixed w-full">
                            <tbody>
                                <tr>
                                    <td><b>Account holder’s name:</b></td><td>{{ selectedWithdrawal.bank_detail.bank_title }}</td>
                                </tr>
                                <tr>
                                    <td><b>Account holder’s number:</b></td><td>{{ selectedWithdrawal.bank_detail.acc_no }}</td>
                                </tr>
                                <tr>
                                    <td><b>Account holder’s address:</b></td><td>{{ selectedWithdrawal.bank_detail.address }}</td>
                                </tr>
                                <tr>
                                    <td><b>Branch name:</b></td><td>{{ selectedWithdrawal.bank_detail.branch }}</td>
                                </tr>
                                <tr>
                                    <td><b>Branch number:</b></td><td>{{ selectedWithdrawal.bank_detail.branch_number }}</td>
                                </tr>
                                <tr>
                                    <td><b>Branch address:</b></td><td>{{ selectedWithdrawal.bank_detail.branch_address }}</td>
                                </tr>
                                <tr>
                                    <td><b>Bank name:</b></td><td>{{ selectedWithdrawal.bank_detail.bank.name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="selectedWithdrawal.bank_detail && selectedWithdrawal.bank_detail.set_default === 'paypal'" class="relative p-6 flex-auto">
                        <table class="table table-striped table-fixed w-full">
                            <tbody>
                                <tr>
                                    <td><b>PayPal email:</b></td><td>{{ selectedWithdrawal.bank_detail.paypal_email }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="!selectedWithdrawal.bank_detail" class="relative p-6 flex-auto">
                        <p>The user has not uploaded any bank detail</p>
                    </div>
                    <!--footer-->
                    <div class="flex items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b">
                        <div v-if="selectedWithdrawal.bank_detail">
                            <a href="#"
                                class="inline-flex items-center bg-blue-500 hover:bg-blue-600 button-exp-fill cursor-pointer border-blue-500"
                                @click.prevent="rejectWithdrawal(selectedWithdrawal)">
                                Transfer
                            </a>
                        </div>
                        <button class="inline-flex items-center ml-1 text-blue-500 bg-transparent border border-solid border-blue-500 hover:bg-blue-500 hover:text-white active:bg-red-600 button-exp-fill cursor-pointer" type="button" v-on:click="toggleModal2()">
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
import axios from 'axios';
export default {
    components: {
        LoadingTable,
    },
    computed: {
        ...mapState({
            withdrawals: (state) => state.withdrawals.withdrawals,
            pagination: (state) => state.withdrawals.pagination,
            searchParam: (state) => state.withdrawals.searchParam,
            loading: (state) => state.withdrawals.loading,
            validationErros: (state) => state.withdrawals.validationErros,
        }),
        limit: {
      get() {
        return this.$store.state.withdrawals.limit;
      },
      set(value) {
        this.$store.commit('withdrawals/setLimit', value);
      }
    }
    },
    data() {
        return {
            quickSearch: null,
            showModal: false,
            showModal2: false,
            selectedWithdrawal: null,
            adminSetting: null,
        };
    },
    methods: {
        fetchWithdrawals(page_url) {
            this.$store.dispatch("withdrawals/fetchWithdrawals", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("withdrawals/setLimit", value);
            this.$store.dispatch("withdrawals/fetchWithdrawals");
        },
        toggleModal(withdrawal) {
            console.log(withdrawal);
            this.selectedWithdrawal = withdrawal;
            console.log(this.selectedWithdrawal);
            this.showModal = !this.showModal;
        },
        toggleModal2(withdrawal) {
            this.selectedWithdrawal = withdrawal;
            this.showModal2 = !this.showModal2;
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("withdrawals/setSearchParam", this.quickSearch);
            this.$store.dispatch("withdrawals/fetchWithdrawals");
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
        // acceptWithdrawal(withdrawal) {
        //     if (withdrawal.status !== 1) {
        //         this.$swal
        //             .fire({
        //                 title: "Are you sure?",
        //                 text: "Do you really want to accept this request?",
        //                 icon: "warning",
        //                 showCancelButton: true,
        //                 confirmButtonColor: "#3085d6",
        //                 cancelButtonColor: "#d33",
        //                 confirmButtonText: "Yes, accept it!",
        //                 showCloseButton: true,
        //                 customClass: {
        //                     confirmButton: 'inline-flex items-center button-exp-fill',
        //                     cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
        //                 }
        //             })
        //             .then((result) => {
        //                 if (result.isConfirmed) {
        //                     this.$store.dispatch("withdrawals/acceptWithdrawal", {
        //                             id: withdrawal.id,
        //                         })
        //                 }
        //             });
        //     }
        // },
        rejectWithdrawal(withdrawal) {
    this.$swal.fire({
        title: "This is irreversible, are you sure?",
        html: `
            <div class="text-left">
                <p>Do you really want to transfer this amount?</p>
                <label for="admin-password" class="block mt-2">Admin Password:</label>
                <input id="admin-password" type="password" class="swal2-input" placeholder="Enter your password">
            </div>
        `,
        // icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, transfer it!",
        showCloseButton: true,
        customClass: {
            confirmButton: 'inline-flex items-center button-exp-fill',
            cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
        },
        preConfirm: () => {
            const password = document.getElementById('admin-password').value;
            if (!password) {
                this.$swal.showValidationMessage('Password is required');
                return false;
            }
            return { password };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading indicator
            this.$swal.showLoading();

            // Verify password first
            axios.post(`${process.env.MIX_ADMIN_API_URL}verify-password`, {
                password: result.value.password
            })
            .then(response => {
                if (response.data.valid) {
                    // Password is correct, proceed with transfer
                    this.$store.dispatch("withdrawals/rejectWithdrawal", {
                        id: withdrawal.bank_detail.user_id,
                    }).then(() => {
                        this.$swal.fire(
                            'Transferred!',
                            'The amount has been transferred.',
                            'success'
                        );
                        this.toggleModal2(withdrawal);
                        this.$store.dispatch("withdrawals/fetchWithdrawals");
                    }).catch((error) => {
                        console.error("Error rejecting withdrawal:", error);
                        this.$swal.fire(
                            'Error!',
                            'There was an error processing the transfer.',
                            'error'
                        );
                    });
                } else {
                    this.$swal.fire(
                        'Error!',
                        'Incorrect password. Please try again.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error("Password verification error:", error);
                this.$swal.fire(
                    'Error!',
                    'There was an error verifying your password.',
                    'error'
                );
            });
        }
    });
},
        // rejectWithdrawal(withdrawal) {
        //     console.log(withdrawal);
        //     this.$store.dispatch("withdrawals/rejectWithdrawal", {
        //             id: withdrawal.bank_detail.user_id,
        //         }).then(() => {
        //             this.toggleModal2(withdrawal);

        //             this.$store.dispatch("withdrawals/fetchWithdrawals");
        //         }).catch((error) => {
        //             console.error("Error rejecting withdrawal:", error);
        //         });

        //     if (this.validationErros.has('reason')) {
        //         this.toggleModal2(withdrawal);
        //     }
        // },
        fetchSetting() {
            axios.get(`${process.env.MIX_ADMIN_API_URL}bank-settings`) // Adjust the URL based on your actual API route.
                .then(response => {
                    this.adminSetting = response.data.data;
                })
                .catch(error => {
                    // Handle any errors if needed.
                });
        },
        calculateTotal(withdrawal) {
            if (!withdrawal || !withdrawal.amount) {
                return 0;
            }

            let total = withdrawal.amount;

            if (withdrawal.bank_detail) {
                // Subtract paid for verification
                if (withdrawal.bank_detail.payment_status === 'pending' && withdrawal.bank_detail.admin_verify_amount) {
                    total -= withdrawal.bank_detail.admin_verify_amount;
                }

                // Subtract bank fees
                if (this.adminSetting && this.adminSetting.bank) {
                    if (this.adminSetting.bank_id === withdrawal.bank_detail.bank_id) {
                        total -= this.adminSetting.bank.bank_to_bank_fee || 0;
                    } else {
                        total -= this.adminSetting.bank.other_bank_fee || 0;
                    }
                }
            }

            return total;
        },
    },
    created() {
        this.fetchSetting();
        this.$store.commit("withdrawals/setLimit", 100);
        this.$store.commit("withdrawals/setSortBy", "id");
        this.$store.commit("withdrawals/setSortType", "desc");
        this.$store.commit("withdrawals/setSearchParam", '');
        this.$store.dispatch("withdrawals/fetchWithdrawals");
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