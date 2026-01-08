<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left">Contact messages</h3>
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
                        messages
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
                        <input type="text" id="table-search-messages"
                            class="block pl-10 w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for messages" v-model="quickSearch" />
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
                                            Sender
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Phone
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Message
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Send date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="message in messages" :key="message.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="py-2 md:p-3 md:border-none">
                                            <!-- <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Sender</label> -->
                                            <div class="">{{ message . name }}</div>
                                            <div class=""><small>{{ message . email }}</small></div>
                                        </td>
                                        <td class="py-2 md:p-3 md:border-none">
                                            <!-- <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Phone</label> -->
                                            <div class="">{{ message . phone }}</div>
                                        </td>
                                        <td class="py-2 md:p-3 md:border-none">
                                            <!-- <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Message</label> -->
                                            <div>{{ message . message }}</div>
                                        </td>
                                        <td class="py-2 md:p-3 md:border-none">
                                            <!-- <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Send date</label> -->
                                            <div>{{ message . created_at }}</div>
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
                                                @click="fetchMessages(pagination.prev_page_url)">
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
                                                @click.prevent="fetchMessages(pagination.next_page_url)">
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
                                            @click.prevent="pagination.prev_page_url && fetchMessages(pagination.prev_page_url)">
                                            Previous
                                        </a>
                                        <template v-for="(link, index) in pagination.links" :key="index">
                                            <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                href="#"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                @click.prevent="fetchMessages(link.url)">
                                                <span v-html="link.label"></span>
                                            </a>
                                        </template>
                                        <a href="#"
                                            class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                            :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                            @click.prevent="pagination.next_page_url && fetchMessages(pagination.next_page_url)">
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
            messages: (state) => state.messages.messages,
            pagination: (state) => state.messages.pagination,
            searchParam: (state) => state.messages.searchParam,
            loading: (state) => state.messages.loading,
        }),
        limit: {
            get() {
              return this.$store.state.messages.limit;
            },
            set(value) {
              this.$store.commit('messages/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
        };
    },
    methods: {
        fetchMessages(page_url) {
            this.$store.dispatch("messages/fetchMessages", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("messages/setLimit", value);
            this.$store.dispatch("messages/fetchMessages");
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("messages/setSearchParam", this.quickSearch);
            this.$store.dispatch("messages/fetchMessages");
        }, 500),
    },
    created() {
        this.$store.commit("messages/setLimit", 100);
        this.$store.commit("messages/setSortBy", "id");
        this.$store.commit("messages/setSortType", "desc");
        this.$store.commit("messages/setSearchParam", '');
        this.$store.dispatch("messages/fetchMessages");
    },
    watch: {
        quickSearch: function () {
            this.quickSearchFilter();
        },
    },
};
</script>
