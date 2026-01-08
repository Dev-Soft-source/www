<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="coffeewallets">All Coffee
                            Wallet <small v-if="coffeewallets">({{ coffeewallets.length }})</small></h3>
                    </div>
                </div>
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <div class="bg-primary px-2 py-0 w-fit">
                            <h3 class="text-white can-exp-h2 text-center sm:text-left">Total balance: <small>{{
                                totalAmount }}</small></h3>
                        </div>
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
                        coffee wallets
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
                        <input type="text" id="table-search-coffeewallets"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for coffee wallets" v-model="quickSearch" />
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
                                            Name
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Email
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Phone
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Ride ID
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Booking ID
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            User ID
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Donation
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Usage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="coffeewallet in coffeewallets" :key="coffeewallet.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div>{{ coffeewallet.name }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div>{{ coffeewallet.email }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div>{{ coffeewallet.phone }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div v-if="coffeewallet.ride_id">
                                                <!-- <router-link
                                                    :to="{ name: 'admin.ride.index', params: { id: coffeewallet.ride_id } }"
                                                    class="inline-flex items-center button-exp-fill">
                                                    {{ addRandomChars(coffeewallet.ride_id) }}
                                                </router-link> -->
                                                <a :href="$router.resolve({ name: 'admin.ride.index', params: { id: coffeewallet.ride_id } }).href"
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="inline-flex items-center button-exp-fill">
                                                    {{ addRandomChars(coffeewallet.ride_id) }}
                                                </a>

                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div v-if="coffeewallet.booking_id">
                                                <!-- <router-link :to="{ name: 'admin.bookings.index' }"
                                                    class="inline-flex items-center button-exp-fill">
                                                    {{ addRandomChars(coffeewallet.booking_id) }}
                                                </router-link> -->
                                                <a :href="$router.resolve({ name: 'admin.bookings.index' }).href"
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="inline-flex items-center button-exp-fill">
                                                    {{ addRandomChars(coffeewallet.booking_id) }}
                                                </a>

                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div v-if="coffeewallet.user_id">
                                                <!-- <router-link
                                                    :to="{ name: 'admin.user.index', params: { id: coffeewallet.user_id } }"
                                                    class="inline-flex items-center button-exp-fill bg-green-500 hover:bg-green-600 cursor-pointer border-green-500">
                                                    {{ addRandomChars(coffeewallet.user_id) }}
                                                </router-link> -->
                                                <a :href="$router.resolve({ name: 'admin.user.index', params: { id: coffeewallet.user_id } }).href"
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="inline-flex items-center button-exp-fill bg-greenXS hover:bg-greenXS cursor-pointer border-greenXS">
                                                    {{ addRandomChars(coffeewallet.user_id) }}
                                                </a>

                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div>{{ coffeewallet.dr_amount }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <div>{{ coffeewallet.cr_amount }}</div>
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
                                                @click="fetchCoffeewallets(pagination.prev_page_url)">
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
                                                @click.prevent="fetchCoffeewallets(pagination.next_page_url)">
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
                                                @click.prevent="pagination.prev_page_url && fetchCoffeewallets(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchCoffeewallets(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchCoffeewallets(pagination.next_page_url)">
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
            coffeewallets: (state) => state.coffeewallets.coffeewallets,
            pagination: (state) => state.coffeewallets.pagination,
            searchParam: (state) => state.coffeewallets.searchParam,
            loading: (state) => state.coffeewallets.loading,
            validationErros: (state) => state.coffeewallets.validationErros,
        }),
        limit: {
            get() {
                return this.$store.state.coffeewallets.limit;
            },
            set(value) {
                this.$store.commit('coffeewallets/setLimit', value);
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
            totalAmount: 0,
        };
    },
    methods: {
        fetchCoffeewallets(page_url) {
            this.$store.dispatch("coffeewallets/fetchCoffeewallets", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("coffeewallets/setLimit", value);
            this.$store.dispatch("coffeewallets/fetchCoffeewallets");
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
            this.$store.commit("coffeewallets/setSearchParam", this.quickSearch);
            this.$store.dispatch("coffeewallets/fetchCoffeewallets");
        }, 500),
        fetchTotalAmount() {
            axios.get(`${process.env.MIX_ADMIN_API_URL}total-amount`) // Adjust the URL based on your actual API route.
                .then(response => {
                    this.totalAmount = response.data.data;
                })
                .catch(error => {
                    // Handle any errors if needed.
                });
        },
        addRandomChars(value) {
            if (!value) return "";
            const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            let randomChars = "";

            for (let i = 0; i < 4; i++) {
                randomChars += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return `${randomChars}-${value}`;
        }
    },
    created() {
        // this.fetchSetting();
        this.fetchTotalAmount();
        this.$store.commit("coffeewallets/setLimit", 100);
        this.$store.commit("coffeewallets/setSortBy", "id");
        this.$store.commit("coffeewallets/setSortType", "desc");
        this.$store.commit("coffeewallets/setSearchParam", '');
        this.$store.dispatch("coffeewallets/fetchCoffeewallets");
    },
    watch: {
        quickSearch: function () {
            this.quickSearchFilter();
        },
    },
};
</script>
