<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h2 text-primary text-center sm:text-left" v-if="drivers">All Drivers</h3>
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
                        <input type="text" id="table-search-drivers"
                            class="block  pl-10  w-full md:w-80 bg-white can-exp-input" placeholder="Search for members"
                            v-model="quickSearch" />
                    </div>
                </div>
                <div class="space-y-8 mx-auto">
                    <div class="space-y-2">
                        <div class="bg-white shadow-lg hover:shadow-xl rounded-md">
                            <table class="table table-fixed w-full leading-normal text-base md:text-base lg:text-lg">
                                <thead class="text-white">
                                    <tr class="hidden md:table-row">
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal w-[12%]">
                                            Name
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal w-[15%]">
                                            Email
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal w-[12%]">
                                            Phone
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal w-[15%]">
                                            Driver's license
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal w-[15%]">
                                            Vehicle
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-3 pr-3 text-left font-FuturaMdCnBT text-white lg:text-xl md:text-lg text-lg font-normal w-[10%]">
                                            Status
                                        </th>
                                        <th
                                            class="sticky top-0 z-10 bg-primary backdrop-blur backdrop-filter py-3.5 pl-4 pr-3 font-FuturaMdCnBT text-white sm:pl-6 lg:text-xl md:text-lg text-lg font-normal w-[21%]">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="flex-1 text-gray-700 sm:flex-none">
                                    <tr v-for="driver in sortedDrivers" :key="driver.id"
                                        class="border-t first:border-t-0 flex p-3 md:p-3  md:table-row flex-col w-full flex-wrap even:bg-gray-50 odd:bg-white">
                                        <td class="p-2 md:p-3 border-b md:border-none break-words">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Name</label>
                                            <div class="break-words">{{ driver.first_name }} {{ driver.last_name }}
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none break-words">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Email</label>
                                            <div class="break-words">{{ driver.email }}</div>
                                            <div v-if="driver.email_verified == 0">Pending</div>
                                            <div v-else>Verified</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none break-words">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Phone</label>
                                            <div class="break-words">{{ driver.phone }}</div>
                                            <div v-if="driver.phone_verified == 0">Pending</div>
                                            <div v-else>Verified</div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none break-words">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Driver's license</label>
                                            <div class="break-words flex flex-row">
                                                <a :href="getDriverLiscenseUrl(driver.driver_liscense)" target="_blank"
                                                    class="break-all mr-2">
                                                    <svg :fill="driver.driver == 1 ? '#00c732' : '#ccc'" height="48px"
                                                        width="48px" version="1.1" id="Layer_1"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                                        xml:space="preserve" stroke="#737373">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <g>
                                                                <g>
                                                                    <g>
                                                                        <path
                                                                            d="M512,341.333c0-8.917-6.635-15.147-16.128-15.147c-5.355,0-14.528,2.752-22.421,7.296 c-10.453-22.016-27.52-41.536-39.723-47.616c-8.085-4.032-35.627-8.533-60.395-8.533c-24.768,0-52.309,4.501-60.416,8.533 c-12.181,6.08-29.248,25.6-39.701,47.616c-7.915-4.544-17.088-7.296-22.421-7.296c-4.053,0-7.552,1.067-10.325,3.157 c-3.733,2.795-5.803,7.061-5.803,11.989c0,16.64,12.907,28.181,14.4,29.44c1.195,1.024,2.56,1.643,3.968,2.048 c-1.344,3.84-2.539,7.744-3.499,11.627C280.789,386.069,320,392.704,320,416c0,5.888-4.779,10.667-10.667,10.667 s-10.667-4.779-10.667-10.667c-3.2-4.203-26.987-8.981-52.757-10.304c-0.32,3.605-0.576,7.211-0.576,10.304 c0,15.936,1.472,43.115,10.667,57.003v17.664c0,11.968,9.365,21.333,21.333,21.333h21.333c11.968,0,21.333-9.365,21.333-21.333 v-7.232c12.885,3.221,32.981,7.232,53.333,7.232c20.352,0,40.448-4.011,53.333-7.232v7.232c0,11.968,9.365,21.333,21.333,21.333 h21.333c11.968,0,21.333-9.365,21.333-21.333v-17.664c9.195-13.888,10.667-41.067,10.667-57.003 c0-3.115-0.256-6.699-0.576-10.304c-25.792,1.344-49.6,6.272-53.035,11.371c0,5.888-4.651,10.155-10.517,10.155 c-5.888,0-10.539-5.312-10.539-11.2c0-23.296,39.211-29.931,70.464-31.552c-0.96-3.904-2.176-7.787-3.499-11.627 c1.408-0.405,2.773-1.024,3.968-2.048C499.093,369.515,512,357.973,512,341.333z M394.667,426.667H352 c-5.888,0-10.667-4.779-10.667-10.667c0-5.888,4.779-10.667,10.667-10.667h42.667c5.888,0,10.667,4.779,10.667,10.667 C405.333,421.888,400.555,426.667,394.667,426.667z M451.264,356.373c-0.043,0-0.085,0-0.107,0.021 c-4.288,0.469-8.725,0.917-13.184,1.387c-0.555,0.064-1.088,0.107-1.643,0.171c-4.565,0.469-9.173,0.917-13.717,1.344 c-1.173,0.107-2.304,0.213-3.456,0.32c-3.349,0.32-6.656,0.619-9.899,0.896c-1.472,0.128-2.88,0.235-4.309,0.363 c-2.859,0.235-5.611,0.448-8.299,0.661c-1.408,0.107-2.795,0.213-4.139,0.299c-2.603,0.171-5.013,0.32-7.36,0.448 c-1.109,0.064-2.304,0.128-3.328,0.171c-3.179,0.128-6.101,0.213-8.491,0.213c-2.389,0-5.312-0.085-8.491-0.235 c-1.045-0.043-2.219-0.128-3.328-0.171c-2.347-0.128-4.757-0.256-7.36-0.448c-1.344-0.085-2.709-0.192-4.117-0.299 c-2.688-0.192-5.44-0.405-8.32-0.661c-1.429-0.128-2.837-0.235-4.309-0.363c-3.221-0.277-6.549-0.576-9.899-0.896 c-1.152-0.107-2.283-0.213-3.456-0.32c-4.544-0.427-9.152-0.896-13.717-1.344c-0.555-0.064-1.088-0.107-1.621-0.171 c-7.381-0.747-14.507-1.515-21.312-2.261c0.064-0.171,0.107-0.299,0.171-0.469c7.083-23.787,26.624-45.952,34.859-50.069 c3.968-1.813,26.731-6.293,50.901-6.293s46.933,4.48,50.88,6.293c8.277,4.117,27.819,26.283,34.901,50.091 c0.064,0.171,0.107,0.299,0.171,0.469C456.619,355.797,454.037,356.096,451.264,356.373z">
                                                                        </path>
                                                                        <path
                                                                            d="M458.667,0H53.333C23.915,0,0,23.915,0,53.333V64h512V53.333C512,23.915,488.085,0,458.667,0z">
                                                                        </path>
                                                                        <path
                                                                            d="M0,330.667C0,360.085,23.915,384,53.333,384h158.251c5.888,0,10.667-4.779,10.667-10.667 c0-2.816-1.088-5.376-2.859-7.275c-4.075-8.128-6.059-16.235-6.059-24.725h-160c-5.888,0-10.667-4.779-10.667-10.667 c0-26.752,18.133-49.963,44.053-56.448l32.021-7.979l1.557-6.272c-7.808-8.96-13.376-20.203-15.765-31.851 c-6.507-2.837-10.923-8.597-11.797-15.552l-2.304-18.56c-0.725-5.632,1.024-11.349,4.821-15.637 c1.28-1.429,2.709-2.667,4.288-3.669c-0.597-5.504-1.237-12.096-1.237-15.616c0-17.856,5.163-41.515,49.067-43.051 c14.891-9.365,30.229-9.365,37.035-9.365c23.104,0,34.603,10.219,40.192,18.752c11.776,17.984,4.331,30.485-1.173,36.309 l-1.941,1.963l-0.555,11.328c1.429,0.96,2.752,2.112,3.904,3.435c3.733,4.245,5.461,9.941,4.757,15.552l-2.304,18.517 c-0.832,6.72-4.992,12.331-10.688,15.275c-2.432,12.16-8.341,23.872-16.64,33.045l1.344,5.376l32.021,7.979 c15.083,3.776,27.371,13.291,35.136,25.771c11.136-15.531,23.829-27.627,34.965-33.195C316.928,260.032,349.589,256,373.333,256 s56.384,4.011,69.888,10.773c11.435,5.717,24.469,18.219,35.797,34.304c2.304,3.243,6.272,4.928,10.197,4.416 c3.712-0.469,5.483-0.875,11.029-0.299c3.029,0.299,5.973-0.661,8.256-2.688c2.219-2.005,3.499-4.885,3.499-7.893V85.333H0 V330.667z M416,128h42.667c5.888,0,10.667,4.779,10.667,10.667s-4.779,10.667-10.667,10.667H416 c-5.888,0-10.667-4.779-10.667-10.667S410.112,128,416,128z M309.333,128h64c5.888,0,10.667,4.779,10.667,10.667 s-4.779,10.667-10.667,10.667h-64c-5.888,0-10.667-4.779-10.667-10.667S303.445,128,309.333,128z M309.333,192h149.333 c5.888,0,10.667,4.779,10.667,10.667s-4.779,10.667-10.667,10.667H309.333c-5.888,0-10.667-4.779-10.667-10.667 S303.445,192,309.333,192z">
                                                                        </path>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </a>
                                                <div v-if="driver.driver == 1">Verified</div>
                                                <div v-if="driver.driver == 2">Pending</div>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none break-words">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Vehicle</label>
                                            <div v-if="driver.vehicles.length === 1">
                                                <ul :key="driver.vehicles[0].id" class="text-sm">
                                                    <li class="truncate">Model - {{ driver.vehicles[0].model }}</li>
                                                    <li class="truncate">Type - {{ driver.vehicles[0].type_label ||
                                                        driver.vehicles[0].type }}</li>
                                                    <li class="truncate">Year - {{ driver.vehicles[0].year }}</li>
                                                    <li class="truncate">Color - {{ driver.vehicles[0].color }}</li>
                                                </ul>
                                            </div>
                                            <div v-if="driver.vehicles.length > 1">
                                                <button
                                                    class="text-primary font-bold text-sm px-4 py-2 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                                                    type="button" @click="toggleModal(driver)">
                                                    Multiple car
                                                </button>
                                            </div>
                                        </td>
                                        <td class="p-2 md:p-3 md:border-none">
                                            <label class="text-gray-500 font-FuturaMdCnBT md:hidden text-xl"
                                                for="">Status</label>
                                            <div>{{ getDriverStatus(driver.driver) }}</div>
                                        </td>
                                        <td class="p-2 md:p-3 hidden md:table-cell">
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                    class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                    title="Access portal"
                                                    @click.prevent="openNewTab('/access-portal/' + driver.email)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v10.5a4.5 4.5 0 009 0V13.5m3-3l-3-3m3 3h-9" />
                                                    </svg>
                                                </button>
                                                <a :href="$router.resolve({ name: 'admin.driver.index', params: { id: driver.id } }).href"
                                                    class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors"
                                                    title="Open details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                                                    </svg>
                                                </a>
                                                <button v-if="driver.suspand == 0" type="button"
                                                    class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors"
                                                    title="Suspend user" @click.prevent="suspandUser(driver)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M18 6L6 18M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <button v-else type="button"
                                                    class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-700 hover:bg-gray-700 hover:text-white transition-colors"
                                                    title="Unsuspend user" @click.prevent="unSuspandUser(driver)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                    title="Delete user" @click.prevent="deleteDriver(driver)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

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
                                                @click.prevent="pagination.prev_page_url && fetchDrivers(pagination.prev_page_url)">
                                                Previous
                                            </a>
                                            <template v-for="(link, index) in pagination.links" :key="index">
                                                <a v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                    href="#"
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                                    :class="{ 'bg-primary text-white': link.active, 'bg-white text-gray-800': !link.active }"
                                                    @click.prevent="fetchDrivers(link.url)">
                                                    <span v-html="link.label"></span>
                                                </a>
                                            </template>
                                            <a href="#"
                                                class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50"
                                                :class="{ 'opacity-50 cursor-not-allowed': !pagination.next_page_url }"
                                                @click.prevent="pagination.next_page_url && fetchDrivers(pagination.next_page_url)">
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
    <div v-if="showModal" class="fixed inset-0 z-50 flex justify-center items-center" style="z-index: 101;">
        <div class="relative w-full max-w-3xl bg-white rounded-lg shadow-lg">
            <div class="p-6 pb-4">
                <h1 class="can-exp-h2 text-primary mb-0">
                    Multiple car
                </h1>
            </div>
            <!--body-->
            <div class="relative p-6 flex-auto max-h-[60vh] overflow-y-auto">
                <ul v-if="selectedDriver && selectedDriver.vehicles">
                    <li v-for="vehicle in selectedDriver.vehicles" :key="vehicle.id">
                        <strong>Model:</strong> {{ vehicle.model }} <br>
                        <strong>Type:</strong> {{ vehicle.type_label || vehicle.type }} <br>
                        <strong>Year:</strong> {{ vehicle.year }} <br>
                        <strong>Color:</strong> {{ vehicle.color }}
                        <hr><br>
                    </li>
                </ul>
            </div>
            <!--footer-->
            <div class="flex items-center justify-end p-6 rounded-b">
                <button class="button-exp-fill" type="button" v-on:click="toggleModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
    <div v-if="showModal" class="bg-opacity-20 fixed inset-0 z-40 bg-black" style="z-index:100;"></div>
</template>

<script>
import _ from "lodash";
import { mapState } from "vuex";
import LoadingTable from "../components/LoadingTable.vue";
export default {
    name: "regular-modal",
    components: {
        LoadingTable,
    },
    computed: {
        ...mapState({
            drivers: (state) => state.drivers.drivers,
            pagination: (state) => state.drivers.pagination,
            searchParam: (state) => state.drivers.searchParam,
            loading: (state) => state.drivers.loading,
        }),
        sortedDrivers() {
            if (!this.drivers) return [];
            return [...this.drivers].sort((a, b) => {
                const firstA = (a.first_name || '').toLowerCase().trim();
                const firstB = (b.first_name || '').toLowerCase().trim();
                const cmp = firstA.localeCompare(firstB);
                if (cmp !== 0) return cmp;
                const lastA = (a.last_name || '').toLowerCase().trim();
                const lastB = (b.last_name || '').toLowerCase().trim();
                return lastA.localeCompare(lastB);
            });
        },
        limit: {
            get() {
                return this.$store.state.drivers.limit;
            },
            set(value) {
                this.$store.commit('drivers/setLimit', value);
            }
        }
    },
    data() {
        return {
            quickSearch: null,
            showModal: false,
            selectedDriver: null,
        };
    },
    methods: {
        toggleModal(driver) {
            this.selectedDriver = driver;
            this.showModal = !this.showModal;
        },
        fetchDrivers(page_url) {
            // Only pass url if it's a valid string
            const payload = page_url && typeof page_url === 'string' ? { url: page_url } : {};
            this.$store.dispatch("drivers/fetchDrivers", payload);
        },
        updateLimit(value) {
            this.$store.commit("drivers/setLimit", value);
            this.$store.dispatch("drivers/fetchDrivers");
        },
        suspandUser(driver) {
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
                        cancelButton: 'inline-flex items-center button-exp-red-fill',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$store.dispatch("drivers/suspandUser", {
                            id: driver.id,
                        })
                    }
                });
        },
        unSuspandUser(driver) {
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
                        cancelButton: 'inline-flex items-center button-exp-red-fill',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$store.dispatch("drivers/unSuspandUser", {
                            id: driver.id,
                        })
                    }
                });
        },
        deleteDriver(driver) {
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
                        cancelButton: 'inline-flex items-center button-exp-red-fill',
                    },
                    didOpen: () => {

                        const cancelButton = document.querySelector('.swal2-cancel');
                        if (cancelButton) cancelButton.focus();
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.$store.dispatch("drivers/deleteDriver", {
                            id: driver.id,
                        })
                    }
                });
        },
        quickSearchFilter: _.debounce(function () {
            this.$store.commit("drivers/setSearchParam", this.quickSearch);
            this.$store.dispatch("drivers/fetchDrivers");
        }, 500),
        getDriverStatus(driverValue) {
            const driverNumber = parseInt(driverValue);
            if (driverNumber === 0) {
                return "No";
            } else if (driverNumber === 1) {
                return "Active";
            } else if (driverNumber === 2) {
                return "Pending";
            } else if (driverNumber === 3) {
                return "Rejected";
            }
        },
        getDriverLiscenseUrl(filename) {
            if (!filename) return '#';
            if (filename.startsWith('http://') || filename.startsWith('https://')) {
                return filename;
            }
            const driverLiscenseFolder = 'driver_liscenses/';
            return `/${driverLiscenseFolder}${filename}`;
        },
        openNewTab(url) {
            window.open(url, '_blank');
        },
    },
    created() {
        this.$store.commit("drivers/setLimit", 100);
        this.$store.commit("drivers/setSortBy", "first_name");
        this.$store.commit("drivers/setSortType", "asc");
        this.$store.commit("drivers/setSearchParam", '');
        this.$store.dispatch("drivers/fetchDrivers");
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