<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="bookings">Secured-Cash
                            bookings <small v-if="bookings">({{ bookings.length }})</small></h3>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row items-center justify-between gap-4 py-4">
                    <div>
                        show
                        <select class="rounded-md px-3 pr-8 py-1" v-model="limit"
                            @input="updateLimit($event.target.value)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        bookings
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
                        <input type="text" id="table-search-bookings"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for bookings" v-model="quickSearch" />
                    </div>
                </div>
                <div class="container space-y-8 mx-auto">
                    <div class="space-y-2">
                        <div class="bg-white shadow-lg hover:shadow-xl rounded-md overflow-x-auto">
                            <table
                                class="table overflow-x-auto table-auto w-full leading-normal text-base md:text-base lg:text-lg">
                                <thead class="text-white">
                                    <tr class="hidden md:table-row">
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Ride
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Drivers
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Passenger
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Payment
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Status
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Booked on
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="booking in bookings" :key="booking.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Ride</label>
                                            <div v-if="booking.departure_city || booking.destination_city">{{ booking.
                                                departure_city }} to {{ booking.destination_city }}<br><small>{{
                                                    booking.departure_date }} at {{ booking.departure_time
                                                    }}</small></div>
                                            <div>{{ getRideType(booking.ride_features) }}</div>
                                            <div>Seats available: {{ booking.ride_seats }}</div>
                                            <div>Seats booked: {{ booking.ride_booked_seats }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Driver</label>
                                            <div>
                                                {{ booking.driver_first_name }} {{ booking.driver_last_name }}
                                                <span v-if="booking.driver_gender == 'female'">(F)</span>
                                                <span v-if="booking.driver_gender == 'male'">(M)</span>
                                                <span v-if="booking.driver_gender == 'prefer not to say'">()</span>
                                                <br>{{ booking.driver_email }}
                                                <span class="flex">
                                                    {{ Number(booking.driver_average_rating).toFixed(1) }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor"
                                                        class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                                        <path fill-rule="evenodd"
                                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <span v-if="booking.driver_suspand == 1">Suspended</span>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Passengers</label>
                                            <div>{{ booking.passenger_first_name }} {{ booking.passenger_last_name
                                            }}
                                                <span v-if="booking.passenger_gender == 'female'">(F)</span>
                                                <span v-if="booking.passenger_gender == 'male'">(M)</span>
                                                <span v-if="booking.passenger_gender == 'prefer not to say'">()</span>
                                                <br>{{ booking.passenger_email }}
                                                <span class="flex">
                                                    {{ booking.passenger_average_rating }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor"
                                                        class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                                        <path fill-rule="evenodd"
                                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <span v-if="booking.passenger_suspand == 1">Suspended</span>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Payment</label>
                                            <div>Price/Seat: ${{ booking.price }} CAD<br>
                                                Seats booked: {{ booking.seats }}<br>
                                                Booking fee: {{ booking . booking_credit }}<br>
                                                Total cost: {{ booking . total }}<br><br>
                                                Payment method: {{ booking.payment_method == "secured" ?
                                                    "Secured-Cash" : booking.payment_method }}
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Status</label>
                                            <div class="whitespace-nowrap">{{ getBookingStatus(booking.status) }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Booked on</label>
                                            <div>{{ booking.booked_on }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div class="flex items-center space-x-2">
                                                <a href="#"
                                                    class="inline-flex items-center bg-blue-500 hover:bg-blue-600 button-exp-fill cursor-pointer border-blue-500"
                                                    @click.prevent="rejectWithdrawal(booking)">
                                                    Refund
                                                </a>
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
                                                @click="fetchSecuredCashBookings(pagination.prev_page_url)">
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
                                                @click.prevent="fetchSecuredCashBookings(pagination.next_page_url)">
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
                            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                                v-if="pagination && pagination.links && pagination.links.length">
                                <div
                                    class="flex flex-col sm:flex-col md:flex-row gap-4 justify-between items-center w-full">
                                    <div>
                                        <p class="text-sm text-gray-700" v-if="pagination.current_page">
                                            Page {{ pagination.current_page }} of {{ pagination.last_page }}
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                            aria-label="Pagination">
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.prev_page_url }"
                                                @click.prevent="pagination.prev_page_url && fetchSecuredCashBookings(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchSecuredCashBookings(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchSecuredCashBookings(pagination.next_page_url)">
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
            bookings: (state) => state.bookings.bookings,
            pagination: (state) => state.bookings.pagination,
            searchParam: (state) => state.bookings.searchParam,
            loading: (state) => state.bookings.loading,
        }),
        limit: {
            get() {
                return this.$store.state.bookings.limit;
            },
            set(value) {
                this.$store.commit('bookings/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
        };
    },
    methods: {
        getRideType(feature) {
            if (!feature) return '';

            let features = feature.split('=');
            let rideLabels = [];

            if (features.includes('1')) {
                rideLabels.push('Pink Ride');
            }
            if (features.includes('2')) {
                rideLabels.push('Extra-Care Ride');
            }

            return rideLabels.join(', ');
        },
        fetchSecuredCashBookings(page_url) {
            this.$store.dispatch("bookings/fetchSecuredCashBookings", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("bookings/setLimit", value);
            this.$store.dispatch("bookings/fetchSecuredCashBookings");
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("bookings/setSearchParam", this.quickSearch);
            this.$store.dispatch("bookings/fetchSecuredCashBookings");
        }, 500),
        getBookingStatus(bookingValue) {
            const bookingNumber = parseInt(bookingValue);
            if (bookingNumber === 0) {
                return "Pending";
            } else if (bookingNumber === 1) {
                return "Seats booked";
            } else if (bookingNumber === 2) {
                return "Ride completed";
            } else if (bookingNumber === 3) {
                return "Booking rejected";
            }
        },
        rejectWithdrawal(booking) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    //icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, refund!",
                    cancelButtonText: "No",
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'inline-flex items-center button-exp-fill',
                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$swal
                            .fire({
                                title: 'Enter your password',
                                html: `<input type="password" id="admin-password" class="swal2-input" placeholder="Enter your password">`,
                                showCancelButton: true,
                                confirmButtonText: 'Verify',
                                cancelButtonText: 'Cancel',
                                showCloseButton: true,
                                customClass: {
                                        confirmButton: 'inline-flex items-center button-exp-fill',
                                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                                        input: 'can-exp-input',
                                    },
                                didOpen: () => {
                                    const input = document.getElementById('admin-password');
                                    if (input) input.focus();
                                },
                            })
                            .then(async (verifyResult) => {
                                console.log(verifyResult);
                                if (verifyResult.isConfirmed) {
                                    const password = document.getElementById('admin-password')?.value;

                                    if (!password) {
                                        this.$swal.fire('Error', 'Password is required', 'error');
                                        return;
                                    }

                                    try {
                                        const response = await axios.post(`${process.env.MIX_ADMIN_API_URL}verify-password`, {
                                            password,
                                        });

                                        if (!response.data.valid) {
                                            return this.$swal.fire('Error', 'Incorrect password', 'error');
                                        }

                                        // Proceed with rejecting the withdrawal
                                        await this.$store.dispatch("bookings/rejectWithdrawal", {
                                            id: booking.id,
                                        });

                                        await this.$store.dispatch("bookings/fetchSecuredCashBookings");

                                    } catch (error) {
                                        this.$swal.fire('Error', error.response?.data?.message || 'Something went wrong', 'error');
                                    }
                                }
                            });
                    }



                });
            }



    },
created() {
    this.$store.commit("bookings/setLimit", 100);
    this.$store.commit("bookings/setSortBy", "id");
    this.$store.commit("bookings/setSortType", "desc");
    this.$store.commit("bookings/setSearchParam", '');
    this.$store.dispatch("bookings/fetchSecuredCashBookings");
},
watch: {
    quickSearch: function () {
        this.quickSearchFilter();
    },
},
};
</script>
<style>

    .swal2-cancel {
      background-color: #f87171 !important; /* Red background for "Yes, cancel it!" and "Close" */
      border-color: #f87171 !important; /* Red border */
      min-width: 60px !important;
      width: 100% !important;
    }

    .swal2-confirm {
      background-color: #106BC7 !important; /* Blue background for "No, take me back" */
      border-color: #106BC7 !important; /* Blue border */
    }
    .swal2-file, .swal2-input, .swal2-textarea {
    box-sizing: border-box;
    width: auto;
    transition: border-color .1s, box-shadow .1s;
    border: 1px solid #d9d9d9;
    border-radius: 4px !important;
    background: inherit;
    box-shadow: none !important;
    color: inherit;
    font-size: 18px !important;
}
.swal2-html-container {
    z-index: 1;
    justify-content: center;
    margin:0px !important;
    padding: 0;
    overflow: auto;
    color: inherit;
    font-size: 1.125em;
    font-weight: 400;
    line-height: normal;
    text-align: center;
    word-wrap: break-word;
    word-break: break-word;
}

  </style>