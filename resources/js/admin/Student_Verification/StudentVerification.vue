<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="students">Students</h3>
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
                        members
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
                        <input type="text" id="table-search-students"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input" placeholder="Search for members"
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
                                            Student card expiry date
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal">
                                            Status
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="student in students" :key="student.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 border-b md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Name</label>
                                            <div class="">{{ student.first_name }} {{ student.last_name }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Email</label>
                                            <div>{{ student.email }}</div>
                                            <div v-if="student.email_verified == 0">Pending</div>
                                            <div v-else>Verified</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Phone</label>
                                            <div>{{ student.phone }}</div>
                                            <div v-if="student.phone_verified == 0">Pending</div>
                                            <div v-else>Verified</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Student card expiry date</label>
                                            <div>{{ formatExpDate(student.student_card_exp_date) }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Status</label>
                                            <div>{{ getStudentStatus(student.student) }}</div>
                                        </td>
                                        <td
                                            class="p-2 md:p-3 gap-2 justify-center items-center hidden md:block space-y-2 space-x-2">
                                            <div class="flex items-center space-x-2">
                                                <a href="#"
                                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-500 button-exp-fill cursor-pointer border-blue-600"
                                                    @click.prevent="openNewTab('/access-portal/' + student.email)">
                                                    <span class="whitespace-nowrap">Access portal</span>
                                                </a>
                                                <!-- <router-link
                                                    :to="{ name: 'admin.student.index', params: { id: student.id } }"
                                                    class="inline-flex items-center button-exp-fill bg-green-500 hover:bg-green-600 cursor-pointer border-green-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </router-link> -->
                                                <a :href="$router.resolve({ name: 'admin.student.index', params: { id: student.id } }).href"
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="inline-flex items-center button-exp-fill bg-greenXS hover:bg-greenXS cursor-pointer border-greenXS">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </a>

                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a v-if="student.suspand == 0" href="#"
                                                    class="inline-flex items-center bg-gray-100 hover:bg-gray-200 hover:border-gray-200 hover:text-black button-exp-fill cursor-pointer border-gray-100 text-black"
                                                    @click.prevent="suspandUser(student)">
                                                    Suspend
                                                </a>
                                                <a v-if="student.suspand == 1" href="#"
                                                    class="inline-flex items-center bg-gray-100 hover:bg-gray-200 hover:border-gray-200 hover:text-black button-exp-fill cursor-pointer border-gray-100 text-black"
                                                    @click.prevent="unSuspandUser(student)">
                                                    Unsuspend
                                                </a>
                                                <a href="#"
                                                    class="inline-flex items-center bg-red-500 hover:bg-red-600 button-exp-fill cursor-pointer border-red-500 hover:border-red-500"
                                                    @click.prevent="deleteStudent(student)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="-ml-0.5 w-4 h-4 mr-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0">
                                                        </path>
                                                    </svg>
                                                    <span>Delete</span>
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
                                                @click="fetchStudents(pagination.prev_page_url)">
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
                                                @click.prevent="fetchStudents(pagination.next_page_url)">
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
                                                @click.prevent="pagination.prev_page_url && fetchStudents(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchStudents(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchStudents(pagination.next_page_url)">
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
            students: (state) => state.students.students,
            pagination: (state) => state.students.pagination,
            searchParam: (state) => state.students.searchParam,
            loading: (state) => state.students.loading,
        }),
        limit: {
            get() {
                return this.$store.state.students.limit;
            },
            set(value) {
                this.$store.commit('students/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
        };
    },
    methods: {
        formatExpDate(expDate) {
            if (!expDate) {
                return ''; // or return 'N/A' or any other default value
            }

            // Convert the string to a Date object
            const dateObject = new Date(expDate);

            // Use the toLocaleDateString method to format the date
            const formattedDate = dateObject.toLocaleDateString('en-US', {
                month: 'long', // Display full month name
                day: 'numeric', // Display day of the month
                year: 'numeric' // Display full year
            });

            return formattedDate;
        },
        fetchStudents(page_url) {
            this.$store.dispatch("students/fetchStudents", { url: page_url });
        },
        updateLimit(value) {
            this.$store.commit("students/setLimit", value);
            this.$store.dispatch("students/fetchStudents");
        },
        suspandUser(student) {
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
                        this.$store.dispatch("students/suspandUser", {
                            id: student.id,
                        })
                    }
                });
        },
        unSuspandUser(student) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    // icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, unsuspend it!",
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
                        this.$store.dispatch("students/unSuspandUser", {
                            id: student.id,
                        })
                    }
                });
        },
        deleteStudent(student) {
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
                        this.$store.dispatch("students/deleteStudent", {
                            id: student.id,
                        })
                    }
                });
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("students/setSearchParam", this.quickSearch);
            this.$store.dispatch("students/fetchStudents");
        }, 500),
        getStudentStatus(studentValue) {
            const studentNumber = parseInt(studentValue);
            if (studentNumber === 0) {
                return "No";
            } else if (studentNumber === 1) {
                return "Active";
            } else if (studentNumber === 2) {
                return "Pending";
            } else if (studentNumber === 3) {
                return "Non-active";
            }
        },
        openNewTab(url) {
            window.open(url, '_blank');
        },
    },
    created() {
        this.$store.commit("students/setLimit", 100);
        this.$store.commit("students/setSortBy", "student_card_upload");
        this.$store.commit("students/setSortType", "desc");
        this.$store.commit("students/setSearchParam", '');
        this.$store.dispatch("students/fetchStudents");
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