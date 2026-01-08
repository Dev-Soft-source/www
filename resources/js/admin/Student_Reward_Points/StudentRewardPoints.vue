<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left">Student Reward Points</h3>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-16 flex justify-center">
                        <router-link :to="{ name: 'admin.student-reward-points.create' }"
                            class="block button-exp-fill w-full">
                            Add new student reward point
                        </router-link>
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
                        Reward Point Settings
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
                        <input type="text" id="table-search-banks"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input"
                            placeholder="Search for student reward point" v-model="quickSearch" />
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
                                            Point
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Reward Name
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="rewardPointSetting in rewardPointSettings" :key="rewardPointSetting.id"
                                        class="border-t first:border-t-0 relative flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="py-2 md:p-3 md:border-none relative">
                                            <!-- <div class="flex justify-between items-center">
                                                <div>
                                                    <label
                                                        class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                        for="">Name</label>
                                                </div>
                                            </div> -->
                                            <div class="font-medium text-gray-900 flex mt-1 items-center gap-1">
                                                <span>{{ rewardPointSetting.point }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2 md:p-3  md:border-none">
                                            <!-- <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Bank to bank fee</label> -->
                                            <div class="">{{ rewardPointSetting.reward_point_setting_detail[0]?.
                                                reward_name }}</div>
                                        </td>
                                        <td
                                            class="p-2 md:p-3 gap-1 md:gap-2 justify-center items-center absolute top-4 md:top-0 right-0 md:relative flex">
                                            <!-- <router-link
                                                :to="{ name: 'admin.student-reward-points.edit', params: { id: rewardPointSetting.id } }"
                                                class="inline-flex items-center md:button-exp-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="-ml-0.5 w-5 md:w-4 h-5 md:h-4 md:mr-2 text-gray-500 md:text-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                <span class="hidden md:block">Edit</span>
                                            </router-link> -->
                                            <a :href="$router.resolve({ name: 'admin.student-reward-points.edit', params: { id: rewardPointSetting.id } }).href"
                                                target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center md:button-exp-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="-ml-0.5 w-5 md:w-4 h-5 md:h-4 md:mr-2 text-gray-500 md:text-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                <span class="hidden md:block">Edit</span>
                                            </a>

                                            <a href="#"
                                                class="inline-flex items-center md:bg-red-500 hover:bg-red-600 border-red-500 md:button-exp-fill cursor-pointer"
                                                style="border-color: #ef4444 !important;"
                                                @click.prevent="deleteStudentRewardPoint(rewardPointSetting)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="-ml-0.5 w-5 md:w-4 h-5 md:h-4 md:mr-2 text-red-500 md:text-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                <span class="hidden md:block">Delete</span>
                                            </a>
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
                                                @click="fetchStudentRewardPoints(pagination.prev_page_url)">
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
                                                @click.prevent="fetchStudentRewardPoints(pagination.next_page_url)">
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
                                                @click.prevent="pagination.prev_page_url && fetchStudentRewardPoints(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchStudentRewardPoints(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchStudentRewardPoints(pagination.next_page_url)">
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
            rewardPointSettings: (state) => state.studentsRewardPointSettings.rewardPointSettings,
            pagination: (state) => state.studentsRewardPointSettings.pagination,
            validationErros: (state) => state.studentsRewardPointSettings.validationErros,
            searchParam: (state) => state.studentsRewardPointSettings.searchParam,
            loading: (state) => state.studentsRewardPointSettings.loading,
        }),
        limit: {
            get() {
                return this.$store.state.studentsRewardPointSettings.limit;
            },
            set(value) {
                this.$store.commit('studentsRewardPointSettings/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
        };
    },
    methods: {
        fetchStudentRewardPoints(page_url) {
            this.$store.dispatch("studentsRewardPointSettings/fetchStudentRewardPoints", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("studentsRewardPointSettings/setLimit", value);
            this.$store.dispatch("studentsRewardPointSettings/fetchStudentRewardPoints");
        },
        deleteStudentRewardPoint(rewardPointSetting) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
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
                        this.$store.dispatch("studentsRewardPointSettings/deleteStudentRewardPoint", {
                            id: rewardPointSetting.id,
                        })
                            .then((res) => {
                                if (res.data.status == 'Success') {
                                    this.$store.dispatch("studentsRewardPointSettings/fetchStudentRewardPoints");
                                }
                            });
                    }
                });
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("studentsRewardPointSettings/setSearchParam", this.quickSearch);
            this.$store.dispatch("studentsRewardPointSettings/fetchStudentRewardPoints");
        }, 500),
    },
    created() {
        this.$store.commit("studentsRewardPointSettings/setLimit", 100);
        this.$store.commit("studentsRewardPointSettings/setSortBy", "id");
        this.$store.commit("studentsRewardPointSettings/setSortType", "desc");
        this.$store.commit("studentsRewardPointSettings/setSearchParam", '');
        this.$store.dispatch("studentsRewardPointSettings/fetchStudentRewardPoints");
    },
    watch: {
        quickSearch: function () {
            this.quickSearchFilter();
        },
    },
};
</script>
