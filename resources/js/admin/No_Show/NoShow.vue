<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="no_shows">No shows</h3>
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
                        no shows
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
                        <input type="text" id="table-search-no-shows"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for no shows" v-model="quickSearch" />
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
                                            User
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Ride
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Date & time
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            More Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="no_show in no_shows" :key="no_show.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">User</label>
                                            <div>{{ no_show.user?.first_name || '' }} {{ no_show.user?.last_name || ''
                                                }}<br><small>{{ no_show.user?.email || '' }}</small></div>
                                        </td>
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Ride</label>
                                            <div v-if="no_show?.ride?.ride_detail?.length">{{ no_show.ride.ride_detail[0]?.departure }} to {{ no_show.ride.ride_detail[0]?.destination }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Ride's date</label>
                                            <div>{{ no_show.ride.date }}<br><small>{{ no_show.ride.time
                                                    }}</small></div>
                                        </td>
                                        <td
                                            class="p-2 md:p-3 gap-2 justify-center items-center hidden md:block space-y-2 space-x-2">
                                            <div v-if="no_show.status == '1'" class="flex justify-center flex-col items-center space-x-2">
                                                <div v-if="no_show?.user?.admin_deactive_account == '1'">Deactivate user account</div>
                                                <div v-if="no_show?.user?.block_booking == '1' && $route.query.type == 'passengers'">Block booking</div>
                                                <div v-if="no_show?.user?.block_post_ride == '1' && $route.query.type == 'drivers'">Block post ride</div>
                                                <div v-if="no_show?.user?.block_review_rating == '1' && $route.query.type == 'drivers'">Block review rating</div>
                                                <button class="inline-flex items-center button-exp-fill cursor-pointer"
                                                    type="button" @click.prevent="undoRejectWithdrawal(no_show)">
                                                    Undo Action
                                                </button>
                                            </div>
                                            <div v-else class="flex justify-center items-center space-x-2">
                                                <button class="inline-flex items-center button-exp-fill cursor-pointer"
                                                    type="button" v-on:click="toggleModal2(no_show)">
                                                    Action
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
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
                                                @click.prevent="pagination.prev_page_url && fetchNoShows(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchNoShows(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchNoShows(pagination.next_page_url)">
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
        <!-- Modal -->

        <!-- Modal2 -->
        <div v-if="showModal2"
            class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
            <div class="relative my-6 mx-auto w-1/2 max-w-6xl">
                <!--content-->
                <div
                    class=" animate__animated animate__fadeIn border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                    <!--header-->
                    <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t">
                        <h5 class="modal-title"><b>Add restriction</b></h5>
                    </div>
                    <!--body-->
                    <div class="relative p-6 flex-auto">
                        <h4 class="flex items-center pt-3">
                            Add restriction
                        </h4>
                        <hr style="margin-top:10px;">
                        <select v-model="rejectionReason" required>
                            <option value="" disabled>Select an action</option>
                            <option value="1">Deactive user account</option>
                            <option v-if="$route.query.type == 'passengers'" value="2">Prevent from booking, but allow posting
                            </option>
                            <option v-if="$route.query.type == 'drivers'" value="3">Cannot post a ride but book a ride
                            </option>
                            <option v-if="$route.query.type == 'drivers'" value="4">Cannot post a review</option>
                        </select>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('restriction')"
                            v-text="validationErros.get('restriction')"></p>
                    </div>
                    <!--footer-->
                    <div class="flex items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b">
                        <a href="#"
                            class="inline-flex items-center bg-blue-500 hover:bg-blue-600 button-exp-fill cursor-pointer border-blue-500"
                            @click.prevent="rejectWithdrawal(selectedWithdrawal)">
                            Submit
                        </a>
                        <button
                            class="inline-flex items-center ml-1 text-blue-500 bg-transparent border border-solid border-blue-500 hover:bg-blue-500 hover:text-white active:bg-blue-600 button-exp-fill cursor-pointer"
                            type="button" v-on:click="toggleModal2()">
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
            no_shows: (state) => state.no_shows.no_shows,
            pagination: (state) => state.no_shows.pagination,
            searchParam: (state) => state.no_shows.searchParam,
            loading: (state) => state.no_shows.loading,
            validationErros: (state) => state.no_shows.validationErros,
        }),

        limit: {
            get() {
                return this.$store.state.no_shows.limit;
            },
            set(value) {
                this.$store.commit('no_shows/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
            selectedRide: null,
            showModal2: false,
            selectedWithdrawal: null,
            rejectionReason: '',
        };
    },
    methods: {
        fetchNoShows(page_url) {
            this.$store.dispatch("no_shows/fetchNoShows", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("no_shows/setLimit", value);
            this.$store.dispatch("no_shows/fetchNoShows");
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("no_shows/setSearchParam", this.quickSearch);
            this.$store.dispatch("no_shows/fetchNoShows");
        }, 500),
        getNoShowStatus(rideValue) {
            const rideNumber = parseInt(rideValue);
            if (rideNumber === 0) {
                return "Active";
            } else if (rideNumber === 1) {
                return "Completed";
            } else if (rideNumber === 2) {
                return "Cancelled";
            }
        },
        cancelRide(ride) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, cancel it!",
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'inline-flex items-center button-exp-fill',
                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$store.dispatch("no_shows/cancelRide", {
                            id: ride.id,
                            query: this.$route.query.type,
                        })
                    }
                });
        },
        removeRide(ride) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, remove it!",
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'inline-flex items-center button-exp-fill',
                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$store.dispatch("no_shows/removeRide", {
                            id: ride.id,
                        })
                    }
                });
        },
        suspandRide(ride) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, suspend it!",
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'inline-flex items-center button-exp-fill',
                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$store.dispatch("no_shows/suspandRide", {
                            id: ride.id,
                            query: this.$route.query.type,
                        })
                    }
                });
        },
        unSuspandRide(ride) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Unsuspend it!",
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'inline-flex items-center button-exp-fill',
                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        // Assuming ride.date is a string like "2024-01-03" and ride.time is a string like "13:49:00"
                        const formattedDate = new Date(`${ride.date} ${ride.time}`);
                        let query;
                        if (formattedDate <= new Date()) {
                            query = '2';
                        } else if (formattedDate > new Date()) {
                            query = '1';
                        }

                        this.$store.dispatch("no_shows/unSuspandRide", {
                            id: ride.id,
                            query: query,
                        })
                    }
                });
        },
        toggleModal2(no_show) {
            this.selectedWithdrawal = no_show;
            this.showModal2 = !this.showModal2;
        },
        rejectWithdrawal(withdrawal) {
            let type;
            if (this.$route.query.type == 'passengers') {
                type = 'passenger'
            } else {
                type = 'driver'
            }
            this.$store.dispatch("no_shows/rejectWithdrawal", {
                id: withdrawal.user_id,
                restriction: this.rejectionReason,
                type: type,
            }).then(() => {
                if (this.validationErros.has('restriction')) {
                    this.toggleModal2(withdrawal);
                }
                this.toggleModal2(withdrawal);

                this.$store.dispatch("no_shows/fetchNoShows");
            }).catch((error) => {
                console.error("Error rejecting withdrawal:", error);
            });
        },

        undoRejectWithdrawal(no_show) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You want to undo this action!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, undo it!",
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'inline-flex items-center button-exp-fill',
                        cancelButton: 'inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        let type;
                        if (this.$route.query.type == 'passengers') {
                            type = 'passenger'
                        } else {
                            type = 'driver'
                        }
                        this.$store.dispatch("no_shows/undoRejectWithdrawal", {
                            id: no_show.user_id,
                            type: type,
                        }).then(() => {
                            if (this.validationErros.has('restriction')) {
                            }

                            this.$store.dispatch("no_shows/fetchNoShows");
                        }).catch((error) => {
                            console.error("Error rejecting withdrawal:", error);
                        });
                    }
                });




        },
    },
    created() {
        this.$store.commit("no_shows/setLimit", 100);
        this.$store.commit("no_shows/setSortBy", "id");
        this.$store.commit("no_shows/setSortType", "desc");
        this.$store.commit("no_shows/setSearchParam", '');
        this.$store.commit("no_shows/setS", this.$route.query.type);
        this.$store.dispatch("no_shows/fetchNoShows");
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