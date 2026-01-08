<template>
    <div class="top-20 sidebar " :class="isBurgerMenu ? 'open' : ''">
        <div class="text-white mt-2 mr-2 flex justify-end lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-7 h-7 float-right cursor-pointer" @click.prevent="burgerMenu()">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <ul class="nav-list accordion text-base md:text-base lg:text-lg font-FuturaMdCnBT" id="accordion-collapse" data-accordion="collapse">
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.dashboard' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.dashboard' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Dashboard</span>
                </router-link>
            </li>
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'supporting-forms' ? updateSubMenuName(null) : updateSubMenuName('supporting-forms')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>

                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Supporting forms</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'supporting-forms'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'supporting-forms'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="supporting-forms-body" class="ml-6"
                :class="subMenuName == 'supporting-forms' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.banks.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.banks.index' || $route.name == 'admin.banks.create' || $route.name == 'admin.banks.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Banks</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.cities.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.cities.index' || $route.name == 'admin.cities.create' || $route.name == 'admin.cities.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Cities</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.countries.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.countries.index' || $route.name == 'admin.countries.create' || $route.name == 'admin.countries.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Countries</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.driver-reward-points.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.driver-reward-points.index' || $route.name == 'admin.driver-reward-points.create' || $route.name == 'admin.driver-reward-points.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Driver reward point</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.extra-care-faqs.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.extra-care-faqs.index' || $route.name == 'admin.extra-care-faqs.create' || $route.name == 'admin.extra-care-faqs.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Extra-Care FAQs</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.languages.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.languages.index' || $route.name == 'admin.languages.create' || $route
                            .name ==
                            'admin.languages.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Languages</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.packages.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.packages.index' || $route.name == 'admin.banks.create' || $route.name == 'admin.banks.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Packages</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.pink-ride-faqs.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.pink-ride-faqs.index' || $route.name == 'admin.pink-ride-faqs.create' || $route.name == 'admin.pink-ride-faqs.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Pink-Ride FAQs</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.states.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.states.index' || $route.name == 'admin.states.create' || $route.name == 'admin.states.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">States</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.student-reward-points.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.student-reward-points.index' || $route.name == 'admin.student-reward-points.create' || $route.name == 'admin.student-reward-points.edit' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Student reward point</span>
                    </router-link>
                </li>
            </ul>
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'settings' ? updateSubMenuName(null) : updateSubMenuName('settings')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">General settings</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'settings'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'settings'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="settings-body" class="ml-6"
                :class="subMenuName == 'settings' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.bank-settings.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.bank-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Bank settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.cancel-ride-settings.index', params: { id: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.cancel-ride-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Cancel ride settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.error-messages-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.error-messages-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Error handling settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.folk-ride-settings.index', params: { id: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.folk-ride-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Extra-Care settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.pink-ride-settings.index', params: { id: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.pink-ride-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Pink ride settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.referral-system-settings.index', params: { id: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.referral-system-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Referral system settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.registration-reward-settings.index', params: { id: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.registration-reward-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Registration reward settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.review-settings.index', params: { id: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.review-settings.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Review settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.success-messages-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.success-messages-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Success and error messages settings</span>
                    </router-link>
                </li>
            </ul>
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'submissions' ? updateSubMenuName(null) : updateSubMenuName('submissions')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Messages received</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'submissions'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'settings'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="submissions-body" class="ml-6"
                :class="subMenuName == 'submissions' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.closed-account-messages.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.closed-account-messages.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Closed account messages</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.contact-messages.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.contact-messages.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Contact messages</span>
                    </router-link>
                </li>
            </ul>
            <!-- <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'screens' ? updateSubMenuName(null) : updateSubMenuName('screens')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Mobile screens</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'screens'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'screens'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="screens-body" class="ml-6"
                :class="subMenuName == 'screens' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.mobile-login-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.mobile-login-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Login page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.mobile-signup-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.mobile-signup-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Signup page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.mobile-forgot-password-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.mobile-forgot-password-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Forgot password page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.mobile-reset-password-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.mobile-reset-password-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Reset password page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.mobile-post-ride-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.mobile-post-ride-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Post ride page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.mobile-find-ride-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.mobile-find-ride-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Find ride page</span>
                    </router-link>
                </li>
            </ul> -->
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'pages' ? updateSubMenuName(null) : updateSubMenuName('pages')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Site pages</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'pages'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'pages'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="pages-body" class="ml-6"
                :class="subMenuName == 'pages' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.billing-address-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.billing-address-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Billing address settings page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.booking-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.booking-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Booking page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.cancellation-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.cancellation-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Cancellation policy page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.firm-cancellation-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.firm-cancellation-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Firm Cancellation policy page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.chats-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.chats-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Chats & notifications page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.close-account-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.close-account-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Close my account page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.coffee-wall-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.coffee-wall-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Coffee on wall page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.contact-proximaride-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.contact-proximaride-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Contact proximaRide page settings </span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.referral-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.referral-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Referral page settings</span>
                    </router-link>
                </li>
                <!-- <li>
                    <router-link :to="{ name: 'admin.contact-us-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.contact-us-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Contact us page</span>
                    </router-link>
                </li> -->
                <li>
                    <router-link :to="{ name: 'admin.dispute-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.dispute-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Dispute policy page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.driver-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.driver-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Drivers page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.edit-profile-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.edit-profile-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Edit profile page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.find-ride-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.find-ride-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Find ride page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.forgot-password-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.forgot-password-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Forgot password page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.home-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.home-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Home page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.login-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.login-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Login page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.logout-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.logout-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Logout page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.luggage-options-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.luggage-options-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Luggage options</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-driver-license-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-driver-license-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My driver license page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-email-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-email-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My email page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-passenger-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-passenger-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My passenger page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-phone-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-phone-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My phone page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.review-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.review-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My review page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-student-card-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-student-card-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My students card page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.trips-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.trips-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My trips page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-vehicle-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-vehicle-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My vehicle page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.my-wallet-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.my-wallet-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">My wallet page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.passenger-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.passenger-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Passengers page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.password-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.password-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Password page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.payment-methods-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.payment-methods-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Payment methods</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.payment-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.payment-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Payment option page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.payout-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.payout-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Payout Option page settings </span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.post-ride-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.post-ride-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Post ride page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.privacy-policy-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.privacy-policy-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Privacy policy page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.profile-photo-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.profile-photo-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Profile photo page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.profile-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.profile-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Profile settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.profile-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.profile-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Profile page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.refund-policy-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.refund-policy-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Refund policy page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.reset-password-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.reset-password-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Reset password page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.features-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.features-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Ride features</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.preferences-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.preferences-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Ride preferences</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.location-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.location-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Select location page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.signup-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.signup-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Signup page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.step1-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.step1-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Step 1 of 5 page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.step2-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.step2-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Step 2 of 5 page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.step3-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.step3-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Step 3 of 5 page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.step4-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.step4-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Step 4 of 5 page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.step5-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.step5-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Step 5 of 5 page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.student-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.student-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Students page settings</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.terms-and-condition-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.terms-and-condition-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Terms and condition page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.terms-of-use-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.terms-of-use-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Terms of use page</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.ride-detail-page-setting.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.ride-detail-page-setting.index' ?
                            'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                            ''">
                        <div class="sidebar-links-icon flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Trip details page settings</span>
                    </router-link>
                </li>
            </ul>
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'users' ? updateSubMenuName(null) : updateSubMenuName('users')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">All members</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'users'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'users'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="users-body" class="ml-6"
                :class="subMenuName == 'users' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.users.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.users.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">All members</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.drivers-verification.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.drivers-verification.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                                <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679c.033.161.049.325.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.807.807 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2H6ZM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17 1.247 0 3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Drivers</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.passengers.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.passengers.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Passengers</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.students-verification.index' }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.name == 'admin.students-verification.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Students</span>
                    </router-link>
                </li>
            </ul>
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'rides' ? updateSubMenuName(null) : updateSubMenuName('rides')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Manage rides</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'rides'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'rides'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="rides-body" class="ml-6"
                :class="subMenuName == 'rides' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.rides.upcoming', query: { s: 1 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.query.s === '1' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Upcoming rides</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.rides.past', query: { s: 2 } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.query.s === '2' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">Past rides</span>
                    </router-link>
                </li>
            </ul>
            <li class="cursor-pointer flex items-center justify-between pr-2"
                @click.prevent="subMenuName == 'no-shows' ? updateSubMenuName(null) : updateSubMenuName('no-shows')">
                <a href="#"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">No show history</span>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white"
                    v-if="subMenuName == null || subMenuName != 'no-shows'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-white" v-else-if="subMenuName == 'no-shows'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </li>
            <ul id="no-shows-body" class="ml-6"
                :class="subMenuName == 'no-shows' ? 'block' : 'hidden'">
                <li>
                    <router-link :to="{ name: 'admin.no_show.index', query: { type: 'drivers' } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.query.type === 'drivers' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">No show drivers</span>
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'admin.no_show.index', query: { type: 'passengers' } }"
                        class="py-2 flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                        :class="$route.query.type === 'passengers' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                        <div class="sidebar-links-icon flex justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            </svg>
                        </div>
                        <span class="links_name pointer-events-auto opacity-0 text-white ">No show passengers</span>
                    </router-link>
                </li>
            </ul>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.bookings.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.bookings.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">All bookings</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.secured-cash-bookings.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.secured-cash-bookings.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Secured-cash bookings</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.reviews.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.reviews.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">All reviews</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.transactions.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.transactions.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">All transactions</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.claim-reward-requests.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.claim-reward-requests.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Claim reward requests</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.verify-phone-numbers.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.verify-phone-numbers.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Verify Phone numbers</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.verify-banks.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.verify-banks.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Verify banks</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.withdrawal-requests.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.withdrawal-requests.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Withdrawal requests</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.coffee-wallet.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.coffee-wallet.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Coffee wallet</span>
                </router-link>
            </li>
            <!-- <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.booking-credits.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.booking-credits.index' || $route.name == 'admin.booking-credits.create' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Booking credits</span>
                </router-link>
            </li> -->
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.news.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.news.index' || $route.name == 'admin.news.create' ||
                        $route.name == 'admin.news.edit' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Manage news</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.videos.index' }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.videos.index' || $route.name == 'admin.videos.create' ||
                        $route.name == 'admin.videos.edit' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Videos</span>
                </router-link>
            </li>
            <li>
                <router-link @click.prevent="updateSubMenuName(null)" :to="{ name: 'admin.site-settings.index', params: { id: 1 } }"
                    class="py-2 hover:border-l-4 border-white flex items-center h-full w-full hover:bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group"
                    :class="$route.name == 'admin.site-settings.index' ?
                        'border-l-4 border-white bg-gradient-to-r from-secondarywhite to-transparent bg-opacity-50 group' :
                        ''">
                    <div class="sidebar-links-icon flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy text-white" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6ZM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2Z"/>
                        </svg>
                    </div>
                    <span class="links_name pointer-events-auto opacity-0 text-white ">Site settings</span>
                </router-link>
            </li>
        </ul>
    </div>
</template>


<script>
import { mapState } from 'vuex'

export default {
    data() {
        return {
            isMobileView: false,
            mobileBreakpoint: 768 // You can adjust this value as needed
        }
    },
    computed: {
        ...mapState({
            isBurgerMenu: state => state.menus.isBurgerMenu,
            subMenuName: state => state.menus.subMenuName
        })
    },
    methods: {
        updateSubMenuName(menuName) {
            this.$store.commit('menus/setSubMenuName', menuName);
        },
        burgerMenu() {
            // Don't allow opening in mobile view
            if (this.isMobileView) {
                this.$store.commit('menus/setIsBurgerMenu', false);
                return;
            }
            this.$store.commit('menus/setIsBurgerMenu');
        },
        checkViewport() {
            this.isMobileView = window.innerWidth <= this.mobileBreakpoint;
            
            // Auto-close sidebar if mobile view detected
            if (this.isMobileView && this.isBurgerMenu) {
                this.$store.commit('menus/setIsBurgerMenu', false);
            }
        },
        handleRouteChange() {
            const routeName = this.$route.name;
            
            // Supporting Forms
            if ([
                'admin.languages.index', 'admin.languages.create', 'admin.languages.edit',
                'admin.countries.index', 'admin.countries.create', 'admin.countries.edit',
                'admin.states.index', 'admin.states.create', 'admin.states.edit',
                'admin.cities.index', 'admin.cities.create', 'admin.cities.edit',
                'admin.banks.index', 'admin.packages.index', 'admin.banks.create', 'admin.banks.edit'
            ].includes(routeName)) {
                this.updateSubMenuName('supporting-forms');
            }
            // Users
            else if ([
                'admin.users.index', 'admin.drivers-verification.index', 'admin.students-verification.index',
                'admin.passengers.index', 'admin.user.index'
            ].includes(routeName)) {
                this.updateSubMenuName('users');
            }
            // Settings
            else if ([
                'admin.review-settings.index', 'admin.folk-ride-settings.index', 'admin.cancel-ride-settings.index',
                'admin.pink-ride-settings.index', 'admin.referral-system-settings.index', 'admin.success-messages-setting.index',
                'admin.error-messages-setting.index', 'admin.registration-reward-settings.index'
            ].includes(routeName)) {
                this.updateSubMenuName('settings');
            }
            // Submissions
            else if ([
                'admin.contact-messages.index', 'admin.closed-account-messages.index'
            ].includes(routeName)) {
                this.updateSubMenuName('submissions');
            }
            // Screens
            else if ([
                'admin.mobile-login-page-setting.index', 'admin.mobile-signup-page-setting.index', 
                'admin.mobile-forgot-password-setting.index', 'admin.mobile-reset-password-setting.index', 
                'admin.mobile-post-ride-page-setting.index', 'admin.mobile-find-ride-page-setting.index'
            ].includes(routeName)) {
                this.updateSubMenuName('screens');
            }
            // Pages
            else if ([
                'admin.home-page-setting.index', 'admin.student-page-setting.index', 'admin.terms-of-use-page-setting.index',
                'admin.driver-page-setting.index', 'admin.passenger-page-setting.index', 'admin.find-ride-page-setting.index',
                'admin.post-ride-page-setting.index', 'admin.login-page-setting.index', 'admin.signup-page-setting.index',
                'admin.contact-us-page-setting.index', 'admin.features-setting.index', 'admin.preferences-setting.index',
                'admin.forgot-password-setting.index', 'admin.payment-methods-setting.index', 'admin.luggage-options-setting.index',
                'admin.ride-detail-page-setting.index', 'admin.terms-and-condition-page-setting.index', 'admin.privacy-policy-page-setting.index',
                'admin.reset-password-setting.index', 'admin.refund-policy-page-setting.index', 'admin.cancellation-page-setting.index',
                'admin.firm-cancellation-page-setting.index', 'admin.dispute-page-setting.index', 'admin.step1-page-setting.index',
                'admin.step2-page-setting.index', 'admin.step3-page-setting.index', 'admin.step4-page-setting.index',
                'admin.chats-page-setting.index', 'admin.trips-page-setting.index', 'admin.booking-page-setting.index',
                'admin.referral-page-setting.index', 'admin.location-page-setting.index', 'admin.coffee-wall-page-setting.index'
            ].includes(routeName)) {
                this.updateSubMenuName('pages');
            }
            // Rides
            else if ([
                'admin.rides.index', 'admin.ride.index'
            ].includes(routeName)) {
                this.updateSubMenuName('rides');
            }
            // No Shows
            else if (routeName == 'admin.no_show.index') {
                this.updateSubMenuName('no-shows');
            }
        }
    },
    created() {
        this.handleRouteChange();
        this.checkViewport();
    },
    mounted() {
        window.addEventListener('resize', this.checkViewport);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.checkViewport);
    },
    watch: {
        '$route'() {
            this.handleRouteChange();
        }
    }
};
</script>
