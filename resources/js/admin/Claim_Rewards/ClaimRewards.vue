<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="claim_rewards">Claim reward requests <small v-if="claim_rewards">({{ claim_rewards.length }})</small></h3>
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
                        claim reward requests
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
                            placeholder="Search for claim reward requests" v-model="quickSearch" />
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
                                            Type
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Point
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Request date
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="claim_reward in claim_rewards" :key="claim_reward.id"
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
                                                <span>{{ claim_reward.random_id }}</span>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Type</label>
                                            <div>{{ claim_reward . type }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Point</label>
                                            <div class="">{{ claim_reward . point }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Request date</label>
                                            <div class="">{{ claim_reward . request_date }}</div>
                                        </td>
                                        <td v-if="claim_reward . status == 'request'" class="p-2 md:p-3 gap-2 justify-center items-center hidden md:block space-y-2 space-x-2">
                                            <div class="flex items-center space-x-2">
                                                <a href="#"
                                                    class="inline-flex items-center bg-blue-500 hover:bg-blue-600 button-exp-fill cursor-pointer border-blue-500"
                                                    @click.prevent="approveReward(claim_reward)">
                                                    Deliver
                                                </a>
                                            </div>
                                        </td>
                                        <td v-else class="p-2 md:p-3 gap-2 justify-center items-center hidden md:block space-y-2 space-x-2">
                                            <div>{{ capitalizeFirst(claim_reward . status) }}</div>
                                            <div>{{ claim_reward . approved_date }}</div>
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
                                                @click="fetchClaimRewards(pagination.prev_page_url)">
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
                                                @click.prevent="fetchClaimRewards(pagination.next_page_url)">
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
                                            @click.prevent="pagination.prev_page_url && fetchClaimRewards(pagination.prev_page_url)">
                                            Previous
                                        </a>
                                        <template v-for="(link, index) in pagination.links" :key="index">
                                            <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                href="#"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                @click.prevent="fetchClaimRewards(link.url)">
                                                <span v-html="link.label"></span>
                                            </a>
                                        </template>
                                        <a href="#"
                                            class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                            :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                            @click.prevent="pagination.next_page_url && fetchClaimRewards(pagination.next_page_url)">
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
            claim_rewards: (state) => state.claim_rewards.claim_rewards,
            pagination: (state) => state.claim_rewards.pagination,
            searchParam: (state) => state.claim_rewards.searchParam,
            loading: (state) => state.claim_rewards.loading,
            validationErros: (state) => state.claim_rewards.validationErros,
        }),
        limit: {
            get() {
              return this.$store.state.claim_rewards.limit;
            },
            set(value) {
              this.$store.commit('claim_rewards/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
        };
    },
    methods: {
        fetchClaimRewards(page_url) {
            this.$store.dispatch("claim_rewards/fetchClaimRewards", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("claim_rewards/setLimit", value);
            this.$store.dispatch("claim_rewards/fetchClaimRewards");
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("claim_rewards/setSearchParam", this.quickSearch);
            this.$store.dispatch("claim_rewards/fetchClaimRewards");
        }, 500),
        approveReward(claim_reward) {
            this.$store.dispatch("claim_rewards/approveReward", {
                    id: claim_reward.id,
                }).then(() => {
                    this.$store.dispatch("claim_rewards/fetchClaimRewards");
                }).catch((error) => {
                    console.error("Error rejecting withdrawal:", error);
                });
        },
        capitalizeFirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    },
    created() {
        this.$store.commit("claim_rewards/setLimit", 100);
        this.$store.commit("claim_rewards/setSortBy", "id");
        this.$store.commit("claim_rewards/setSortType", "desc");
        this.$store.commit("claim_rewards/setSearchParam", '');
        this.$store.dispatch("claim_rewards/fetchClaimRewards");
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