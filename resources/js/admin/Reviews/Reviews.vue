<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="ratings">All reviews <small
                                v-if="ratings">({{ ratings.length }})</small></h3>
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
                        ratings
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
                        <input type="text" id="table-search-ratings"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input" placeholder="Search for ratings"
                            v-model="quickSearch" />
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
                                            Review
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Ride
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            From
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            To
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="rating in ratings" :key="rating.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Review</label>
                                            <div>
                                                {{ expandedReviews[rating.id] ? rating.review :
                                                getShortText(rating.review) }}
                                                <span
                                                    v-if="rating.review.split(' ').length > maxWords && !expandedReviews[rating.id]">...</span>

                                                <button v-if="rating.review.split(' ').length > maxWords"
                                                    @click="toggleReadMore(rating.id)" class="text-blue-500 ml-2">
                                                    {{ expandedReviews[rating.id] ? 'Read Less' : 'Read More' }}
                                                </button>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Ride</label>
                                            <div class="">{{ rating.departure_city }} to {{ rating.destination_city
                                                }} <br><small>{{ rating.ride_date }}</small> <br><small>{{ rating.
                                                    ride_time }}</small></div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">From</label>
                                            <div>
                                                {{ rating.from_first_name }} {{ rating.from_last_name }}<br>
                                                <small v-if="rating.from_id == rating.driver_id">Driver</small>
                                                <small v-else>Passenger</small>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">To</label>
                                            <div v-if="rating.from_id == rating.driver_id">
                                                {{ rating.passenger_first_name }}<br>
                                                <small v-if="rating.passenger_first_name">Passenger</small>
                                            </div>
                                            <div v-else>
                                                {{ rating.driver_first_name }} {{ rating.driver_last_name }}<br>
                                                <small>Driver</small>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- <router-link
                                                    :to="{ name: 'admin.review.index', params: { id: rating.id } }"
                                                    class="inline-flex items-center button-exp-fill bg-green-500 h-10 hover:bg-green-600 cursor-pointer border-green-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </router-link> -->
                                                <a :href="$router.resolve({ name: 'admin.review.index', params: { id: rating.id } }).href"
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="inline-flex items-center button-exp-fill bg-greenXS h-10 hover:bg-greenXS cursor-pointer border-greenXS">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </a>

                                                <div v-if="rating.status == '2'">
                                                    <a href="#"
                                                        class="inline-flex items-center bg-red-500 hover:bg-red-600 h-10 button-exp-fill cursor-pointer border-red-500"
                                                        @click.prevent="unSuspendRating(rating)">
                                                        <span>Suspended</span>
                                                    </a>
                                                </div>
                                                <div v-else>
                                                    <a href="#"
                                                        class="inline-flex items-center bg-red-500 hover:bg-red-600 h-10 button-exp-fill cursor-pointer border-red-500"
                                                        @click.prevent="deleteRating(rating)">
                                                        <span>Suspend</span>
                                                    </a>
                                                </div>
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
                                                @click="fetchratings(pagination.prev_page_url)">
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
                                                @click.prevent="fetchRatings(pagination.next_page_url)">
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
                                                @click.prevent="pagination.prev_page_url && fetchRatings(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchRatings(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchRatings(pagination.next_page_url)">
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
export default {
    components: {
        LoadingTable,
    },
    computed: {
        ...mapState({
            ratings: (state) => state.ratings.ratings,
            pagination: (state) => state.ratings.pagination,
            searchParam: (state) => state.ratings.searchParam,
            loading: (state) => state.ratings.loading,
        }),
        limit: {
            get() {
                return this.$store.state.ratings.limit;
            },
            set(value) {
                this.$store.commit('ratings/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
            maxWords: 15,
            expandedReviews: {}
        };
    },
    methods: {
        getShortText(review) {
            if (!review) return "";
            let words = review.split(" ");
            return words.length <= this.maxWords ? review : words.slice(0, this.maxWords).join(" ");
        },
        toggleReadMore(id) {
            this.expandedReviews[id] = !this.expandedReviews[id]; // Direct update reactive object
        },
        fetchRatings(page_url) {
            this.$store.dispatch("ratings/fetchRatings", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("ratings/setLimit", value);
            this.$store.dispatch("ratings/fetchRatings");
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("ratings/setSearchParam", this.quickSearch);
            this.$store.dispatch("ratings/fetchRatings");
        }, 500),
        unSuspendRating(rating) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, unSuspend it!",
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
                        this.$store.dispatch("ratings/unSuspendRating", {
                            id: rating.id,
                        })
                    }
                });
        },
        deleteRating(rating) {
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
                        this.$store.dispatch("ratings/deleteRating", {
                            id: rating.id,
                        })
                    }
                });
        },
    },
    created() {
        this.$store.commit("ratings/setLimit", 100);
        this.$store.commit("ratings/setSortBy", "id");
        this.$store.commit("ratings/setSortType", "desc");
        this.$store.commit("ratings/setSearchParam", '');
        this.$store.dispatch("ratings/fetchRatings");
    },
    watch: {
        quickSearch: function () {
            this.quickSearchFilter();
        },
    },
};
</script>
