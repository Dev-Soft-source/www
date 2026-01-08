<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Site settings
                        </h3>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">
                <div class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6 ">
                    <div class="relative z-0 w-full group">
                        <label for="frim_discount" class="">Firm cancellation discount</label>
                        <input type="number" name="frim_discount" id="frim_discount"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.frim_discount" @input="
                                updateForm('frim_discount', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('frim_discount')"
                            v-text="validationErros.get('frim_discount')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="ride_completed_hours" class="">Ride complete hours</label>
                        <input type="number" name="ride_completed_hours" id="ride_completed_hours"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.ride_completed_hours" @input="
                                updateForm('ride_completed_hours', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('ride_completed_hours')"
                            v-text="validationErros.get('ride_completed_hours')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="destination_hours" class="">Ride destination hours</label>
                        <input type="number" name="destination_hours" id="destination_hours"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.destination_hours" @input="
                                updateForm('destination_hours', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('destination_hours')"
                            v-text="validationErros.get('destination_hours')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="booking_price" class="">Booking fee</label>
                        <input type="number" name="booking_price" id="booking_price"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.booking_price" @input="
                                updateForm('booking_price', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('booking_price')"
                            v-text="validationErros.get('booking_price')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="booking_per" class="">Per booking</label>
                        <input type="number" name="booking_per" id="booking_per"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.booking_per" @input="
                                updateForm('booking_per', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('booking_per')"
                            v-text="validationErros.get('booking_per')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="gas_cost" class="">Gas cost</label>
                        <input type="number" step="0.01" name="gas_cost" id="gas_cost"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.gas_cost" @input="
                                updateForm('gas_cost', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('gas_cost')"
                            v-text="validationErros.get('gas_cost')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="secured_cash_attempt" class="">Secured-cash attempt</label>
                        <input type="number" step="0.01" name="secured_cash_attempt" id="secured_cash_attempt"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.secured_cash_attempt" @input="
                                updateForm('secured_cash_attempt', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('secured_cash_attempt')"
                            v-text="validationErros.get('secured_cash_attempt')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="price_per_km" class="">Price per kilometer</label>
                        <input type="number" step="any" name="price_per_km" id="price_per_km"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.price_per_km" @input="
                                updateForm('price_per_km', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('price_per_km')"
                            v-text="validationErros.get('price_per_km')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="keywords" class="">Keywords</label>
                        <input type="text" name="keywords" id="keywords"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.keywords" @input="
                                updateForm('keywords', $event.target.value)
                                " tabindex="-1" />
                        <p class="text-sm">Separate multiple by comma.</p>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('keywords')"
                            v-text="validationErros.get('keywords')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="description" class="">Description</label>
                        <input type="text" name="description" id="description"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.description" @input="
                                updateForm('description', $event.target.value)
                                " tabindex="-1" />
                        <p class="text-sm">In a few words, explain what this site is about.</p>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('description')"
                            v-text="validationErros.get('description')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="facebook" class="">Facebook</label>
                        <input type="url" name="facebook" id="facebook"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.facebook" @input="
                                updateForm('facebook', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('facebook')"
                            v-text="validationErros.get('facebook')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="instagram" class="">Instagram</label>
                        <input type="url" name="instagram" id="instagram"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.instagram" @input="
                                updateForm('instagram', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('instagram')"
                            v-text="validationErros.get('instagram')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="youtube" class="">Youtube</label>
                        <input type="url" name="youtube" id="youtube"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.youtube" @input="
                                updateForm('youtube', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('youtube')"
                            v-text="validationErros.get('youtube')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="twitter" class="">Twitter</label>
                        <input type="url" name="twitter" id="twitter"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.twitter" @input="
                                updateForm('twitter', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('twitter')"
                            v-text="validationErros.get('twitter')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="tax_type" class="">Tax type</label>
                        <div class="flex space-x-3">
                            <div>
                                <input id="bordered-radio-flat-tax" type="radio" name="tax_type" v-model="form.tax_type"
                                    :value="'flat_tax'" @change="updateForm('tax_type', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-flat-tax">Flat tax</label>
                            </div>
                            <div>
                                <input id="bordered-radio-state-wise" type="radio" name="tax_type"
                                    v-model="form.tax_type" :value="'state_wise_tax'"
                                    @change="updateForm('tax_type', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-state-wise">State wise tax</label>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('tax_type')"
                            v-text="validationErros.get('tax_type')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="deduct_tax" class="">Deduct tax from</label>
                        <div class="flex space-x-3">
                            <div>
                                <input id="bordered-radio-deduct-from-passenger" type="radio" name="deduct_tax"
                                    v-model="form.deduct_tax" :value="'deduct_from_passenger'"
                                    @change="updateForm('deduct_tax', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-deduct-from-passenger">Deduct from passenger</label>
                            </div>
                            <div>
                                <input id="bordered-radio-deduct-from-driver" type="radio" name="deduct_tax"
                                    v-model="form.deduct_tax" :value="'deduct_from_driver'"
                                    @change="updateForm('deduct_tax', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-deduct-from-driver">Deduct from driver</label>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('deduct_tax')"
                            v-text="validationErros.get('deduct_tax')"></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="tax" class="">Tax</label>
                        <input type="number" step="any" name="tax" id="tax"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.tax" @input="
                                updateForm('tax', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('tax')"
                            v-text="validationErros.get('tax')"></p>
                    </div>

                    <div class="relative z-0 w-full group">
                        <label for="user_per_day_limit" class="">User per day contact limit</label>
                        <input type="number" step="any" name="user_per_day_limit" id="user_per_day_limit"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.user_per_day_limit" @input="
                                updateForm('user_per_day_limit', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('user_per_day_limit')"
                            v-text="validationErros.get('user_per_day_limit')"></p>
                    </div>

                    <div class="relative z-0 w-full group">
                        <label for="booking_cancel_limit" class="">Booking cancellation limit</label>
                        <input type="number" step="any" name="booking_cancel_limit" id="booking_cancel_limit"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.booking_cancel_limit" @input="
                                updateForm('booking_cancel_limit', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('booking_cancel_limit')"
                            v-text="validationErros.get('booking_cancel_limit')"></p>
                    </div>

                    <div class="relative z-0 w-full group">
                        <label for="booking_cancel_duration" class="">Booking cancellation duration (Months)</label>
                        <input type="number" step="any" name="booking_cancel_duration" id="booking_cancel_duration"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.booking_cancel_duration" @input="
                                updateForm('booking_cancel_duration', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('booking_cancel_duration')"
                            v-text="validationErros.get('booking_cancel_duration')"></p>
                    </div>
                    
                    <div class="relative z-0 w-full group">
                        <label for="ride_post_dead_time" class="">Ride post dead time</label>
                        <input type="number" step="any" name="ride_post_dead_time" id="ride_post_dead_time"
                            class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                            :value="form.ride_post_dead_time" @input="
                                updateForm('ride_post_dead_time', $event.target.value)
                                " tabindex="-1" />
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('ride_post_dead_time')"
                            v-text="validationErros.get('ride_post_dead_time')"></p>
                    </div>

                    <div class="relative z-0 w-full group mt-3">

                        <label for="deduct_tax" class="">Booking fee give to driver</label>
                        <div class="flex space-x-3 mt-3">
                            <div>
                                <input id="bordered-radio-enable" type="radio" name="booking_fee_give_to_driver"
                                    v-model="form.booking_fee_give_to_driver" :value="1"
                                    @change="updateForm('booking_fee_give_to_driver', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-enable">Enable</label>
                            </div>
                            <div>
                                <input id="bordered-radio-state-wise" type="radio" name="booking_fee_give_to_driver"
                                    v-model="form.booking_fee_give_to_driver" :value="0"
                                    @change="updateForm('booking_fee_give_to_driver', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-state-wise">Disable</label>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('booking_fee_give_to_driver')"
                            v-text="validationErros.get('booking_fee_give_to_driver')"></p>
                    </div>

                    <div class="relative z-0 w-full group mt-3">

                        <label for="deduct_tax" class="">Booking fee give to student</label>
                        <div class="flex space-x-3 mt-3">
                            <div>
                                <input id="bordered-radio-enable" type="radio" name="booking_fee_give_to_student"
                                    v-model="form.booking_fee_give_to_student" :value="1"
                                    @change="updateForm('booking_fee_give_to_student', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-enable">Enable</label>
                            </div>
                            <div>
                                <input id="bordered-radio-state-wise" type="radio" name="booking_fee_give_to_student"
                                    v-model="form.booking_fee_give_to_student" :value="0"
                                    @change="updateForm('booking_fee_give_to_student', $event.target.value)"
                                    class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="bordered-radio-state-wise">Disable</label>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('booking_fee_give_to_student')"
                            v-text="validationErros.get('booking_fee_give_to_student')"></p>
                    </div>


                </div>
                <!-- <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                    ">
                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer" @click.prevent="
                        collapseStates[0] =
                        !collapseStates[0]
                        ">
                        <h3 class="text-white">
                            Top Menu section
                        </h3>
                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                            <path d="M6 9l4 4 4-4"></path>
                        </svg>
                    </div>
                    <div class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6"
                        v-if="collapseStates[0]">
                        <div class="relative z-0 w-full group">
                            <label for="top_menu_notification" class="">Top Menu Notification</label>
                            <input type="File" name="top_menu_notification" id="top_menu_notification"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'top_menu_notification')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('top_menu_notification')"
                                v-text="validationErros.get('top_menu_notification')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['top_menu_notification']
                                        "
                                        :src="
                                            form['top_menu_notification']
                                                ? '/home_page_icons/' + form['top_menu_notification']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="top_menu_search" class="">Top Menu Search</label>
                            <input type="file" name="top_menu_search" id="top_menu_search"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'top_menu_search')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('top_menu_search')"
                                v-text="validationErros.get('top_menu_search')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['top_menu_search']
                                        "
                                        :src="
                                            form['top_menu_search']
                                                ? '/home_page_icons/' + form['top_menu_search']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="top_menu_add" class="">Top Menu Add</label>
                            <input type="file" name="top_menu_add" id="top_menu_add"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'top_menu_add')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('top_menu_add')"
                                v-text="validationErros.get('top_menu_add')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['top_menu_add']
                                        "
                                        :src="
                                            form['top_menu_add']
                                                ? '/home_page_icons/' + form['top_menu_add']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                    ">
                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer" @click.prevent="
                        collapseStates[1] =
                        !collapseStates[1]
                        ">
                        <h3 class="text-white">
                            Profile Setting
                        </h3>
                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                            <path d="M6 9l4 4 4-4"></path>
                        </svg>
                    </div>

                    <div class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6" v-if="collapseStates[1]">
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_profile_photo" class="">Profile Photo</label>
                            <input type="file" name="profile_setting_profile_photo" id="profile_setting_profile_photo"
                                class="can-exp-input w-full block border border-gray-300 rounded"
                                @change="handleImage($event, 'profile_setting_profile_photo')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('profile_setting_profile_photo')"
                                v-text="validationErros.get('profile_setting_profile_photo')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_profile_photo']
                                        "
                                        :src="
                                            form['profile_setting_profile_photo']
                                                ? '/home_page_icons/' + form['profile_setting_profile_photo']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_my_vehicle" class="">My Vehicle</label>
                            <input type="file" name="profile_setting_my_vehicle" id="profile_setting_my_vehicle"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_my_vehicle')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('profile_setting_my_vehicle')"
                                v-text="validationErros.get('profile_setting_my_vehicle')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_my_vehicle']
                                        "
                                        :src="
                                            form['profile_setting_my_vehicle']
                                                ? '/home_page_icons/' + form['profile_setting_my_vehicle']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_password" class="">Password</label>
                            <input type="file" name="profile_setting_password" id="profile_setting_password"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_password')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('profile_setting_password')"
                                v-text="validationErros.get('profile_setting_password')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_password']
                                        "
                                        :src="
                                            form['profile_setting_password']
                                                ? '/home_page_icons/' + form['profile_setting_password']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_my_phone_number" class="">My Phone Number</label>
                            <input type="file" name="profile_setting_my_phone_number"
                                id="profile_setting_my_phone_number"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_my_phone_number')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('profile_setting_my_phone_number')"
                                v-text="validationErros.get('profile_setting_my_phone_number')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_my_phone_number']
                                        "
                                        :src="
                                            form['profile_setting_my_phone_number']
                                                ? '/home_page_icons/' + form['profile_setting_my_phone_number']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_my_email_address" class="">My Email Address</label>
                            <input type="file" name="profile_setting_my_email_address"
                                id="profile_setting_my_email_address"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_my_email_address')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('profile_setting_my_email_address')"
                                v-text="validationErros.get('profile_setting_my_email_address')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_my_email_address']
                                        "
                                        :src="
                                            form['profile_setting_my_email_address']
                                                ? '/home_page_icons/' + form['profile_setting_my_email_address']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_my_drivers_license" class="">My Drivers License</label>
                            <input type="file" name="profile_setting_my_drivers_license"
                                id="profile_setting_my_drivers_license"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_my_drivers_license')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('profile_setting_my_drivers_license')"
                                v-text="validationErros.get('profile_setting_my_drivers_license')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_my_drivers_license']
                                        "
                                        :src="
                                            form['profile_setting_my_drivers_license']
                                                ? '/home_page_icons/' + form['profile_setting_my_drivers_license']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_my_student_card" class="">My Student Card</label>
                            <input type="file" name="profile_setting_my_student_card"
                                id="profile_setting_my_student_card"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_my_student_card')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('profile_setting_my_student_card')"
                                v-text="validationErros.get('profile_setting_my_student_card')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_my_student_card']
                                        "
                                        :src="
                                            form['profile_setting_my_student_card']
                                                ? '/home_page_icons/' + form['profile_setting_my_student_card']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="profile_setting_referrals" class="">Referrals</label>
                            <input type="file" name="profile_setting_referrals" id="profile_setting_referrals"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'profile_setting_referrals')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('profile_setting_referrals')"
                                v-text="validationErros.get('profile_setting_referrals')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['profile_setting_referrals']
                                        "
                                        :src="
                                            form['profile_setting_referrals']
                                                ? '/home_page_icons/' + form['profile_setting_referrals']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border rounded w-full" :class="collapseStates[0] ? 'bg-gray-50' : ''
                    ">
                    <div class="flex justify-between bg-primary text-white p-4 cursor-pointer" @click.prevent="
                        collapseStates[2] =
                        !collapseStates[2]
                        ">
                        <h3 class="text-white">
                            Menu Icon
                        </h3>
                        <svg class="w-5 h-5 fill-current text-gray-500" viewBox="0 0 20 20">
                            <path d="M6 9l4 4 4-4"></path>
                        </svg>
                    </div>

                    <div class="p-4 bg-gray-100 border-t grid grid-cols-2 gap-4 md:gap-6" v-if="collapseStates[2]">
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_profile_setting" class="">Profile Setting</label>
                            <input type="file" name="menu_icon_profile_setting" id="menu_icon_profile_setting"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_profile_setting')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_profile_setting')"
                                v-text="validationErros.get('menu_icon_profile_setting')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_profile_setting']
                                        "
                                        :src="
                                            form['menu_icon_profile_setting']
                                                ? '/home_page_icons/' + form['menu_icon_profile_setting']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_my_wallet" class="">My Wallet</label>
                            <input type="file" name="menu_icon_my_wallet" id="menu_icon_my_wallet"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_my_wallet')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_my_wallet')"
                                v-text="validationErros.get('menu_icon_my_wallet')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_my_wallet']
                                        "
                                        :src="
                                            form['menu_icon_my_wallet']
                                                ? '/home_page_icons/' + form['menu_icon_my_wallet']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_payment_option" class="">Payment Option</label>
                            <input type="file" name="menu_icon_payment_option" id="menu_icon_payment_option"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_payment_option')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_payment_option')"
                                v-text="validationErros.get('menu_icon_payment_option')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_payment_option']
                                        "
                                        :src="
                                            form['menu_icon_payment_option']
                                                ? '/home_page_icons/' + form['menu_icon_payment_option']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_my_reviews" class="">My Reviews</label>
                            <input type="file" name="menu_icon_my_reviews" id="menu_icon_my_reviews"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_my_reviews')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_my_reviews')"
                                v-text="validationErros.get('menu_icon_my_reviews')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_my_reviews']
                                        "
                                        :src="
                                            form['menu_icon_my_reviews']
                                                ? '/home_page_icons/' + form['menu_icon_my_reviews']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_terms_condition" class="">Terms Condition</label>
                            <input type="file" name="menu_icon_terms_condition" id="menu_icon_terms_condition"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_terms_condition')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_terms_condition')"
                                v-text="validationErros.get('menu_icon_terms_condition')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_terms_condition']
                                        "
                                        :src="
                                            form['menu_icon_terms_condition']
                                                ? '/home_page_icons/' + form['menu_icon_terms_condition']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_privacy_policy" class="">Privacy Policy</label>
                            <input type="file" name="menu_icon_privacy_policy" id="menu_icon_privacy_policy"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_privacy_policy')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_privacy_policy')"
                                v-text="validationErros.get('menu_icon_privacy_policy')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_privacy_policy']
                                        "
                                        :src="
                                            form['menu_icon_privacy_policy']
                                                ? '/home_page_icons/' + form['menu_icon_privacy_policy']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_term_of_use" class="">Term Of Use</label>
                            <input type="file" name="menu_icon_term_of_use" id="menu_icon_term_of_use"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_term_of_use')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_term_of_use')"
                                v-text="validationErros.get('menu_icon_term_of_use')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_term_of_use']
                                        "
                                        :src="
                                            form['menu_icon_term_of_use']
                                                ? '/home_page_icons/' + form['menu_icon_term_of_use']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_cancellation_policy" class="">Cancellation Policy</label>
                            <input type="file" name="menu_icon_cancellation_policy" id="menu_icon_cancellation_policy"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_cancellation_policy')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('menu_icon_cancellation_policy')"
                                v-text="validationErros.get('menu_icon_cancellation_policy')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_cancellation_policy']
                                        "
                                        :src="
                                            form['menu_icon_cancellation_policy']
                                                ? '/home_page_icons/' + form['menu_icon_cancellation_policy']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_dispute_policy" class="">Pispute Policy</label>
                            <input type="file" name="menu_icon_dispute_policy" id="menu_icon_dispute_policy"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_dispute_policy')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_dispute_policy')"
                                v-text="validationErros.get('menu_icon_dispute_policy')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_dispute_policy']
                                        "
                                        :src="
                                            form['menu_icon_dispute_policy']
                                                ? '/home_page_icons/' + form['menu_icon_dispute_policy']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_contact_proximaride" class="">Contact Proximaride</label>
                            <input type="file" name="menu_icon_contact_proximaride" id="menu_icon_contact_proximaride"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_contact_proximaride')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('menu_icon_contact_proximaride')"
                                v-text="validationErros.get('menu_icon_contact_proximaride')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_contact_proximaride']
                                        "
                                        :src="
                                            form['menu_icon_contact_proximaride']
                                                ? '/home_page_icons/' + form['menu_icon_contact_proximaride']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_coffee_on_the_wall" class="">Coffee on the Wall</label>
                            <input type="file" name="menu_icon_coffee_on_the_wall" id="menu_icon_coffee_on_the_wall"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_coffee_on_the_wall')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('menu_icon_coffee_on_the_wall')"
                                v-text="validationErros.get('menu_icon_coffee_on_the_wall')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_coffee_on_the_wall']
                                        "
                                        :src="
                                            form['menu_icon_coffee_on_the_wall']
                                                ? '/home_page_icons/' + form['menu_icon_coffee_on_the_wall']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="menu_icon_log_out" class="">Log Out</label>
                            <input type="file" name="menu_icon_log_out" id="menu_icon_log_out"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'menu_icon_log_out')" />
                            <p class="mt-2 text-sm text-red-400" v-if="validationErros.has('menu_icon_log_out')"
                                v-text="validationErros.get('menu_icon_log_out')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['menu_icon_log_out']
                                        "
                                        :src="
                                            form['menu_icon_log_out']
                                                ? '/home_page_icons/' + form['menu_icon_log_out']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="meanu_icon_close_your_account" class="">Close Your Account</label>
                            <input type="file" name="meanu_icon_close_your_account" id="meanu_icon_close_your_account"
                                class="can-exp-input w-full block border border-gray-300 rounded" placeholder=" "
                                @change="handleImage($event, 'meanu_icon_close_your_account')" />
                            <p class="mt-2 text-sm text-red-400"
                                v-if="validationErros.has('meanu_icon_close_your_account')"
                                v-text="validationErros.get('meanu_icon_close_your_account')"></p>
                        </div>
                        <div class="relative z-0 w-full group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img
                                        class="w-auto sm:w-96 h-36 rounded-md object-cover"
                                        v-if="
                                            form['meanu_icon_close_your_account']
                                        "
                                        :src="
                                            form['meanu_icon_close_your_account']
                                                ? '/home_page_icons/' + form['meanu_icon_close_your_account']
                                                : ''
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="button-exp-fill mt-5" :disabled="loading">Save changes</button>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import { mapState } from "vuex";
export default {
    data() {
        return {
            collapseStates: [false, false, false],
            imagePreviews: {},
        };
    },
    computed: {
        ...mapState({
            loading: (state) => state.settings.loading,
            form: (state) => state.settings.form,
            validationErros: (state) => state.settings.validationErros,
        }),
        is_default: {
            get: function () {
                return this.$store.state.settings.form.is_default;
            },
            set: function (val) {
                this.$store.commit("settings/setForm", {
                    is_default: val,
                });
            },
        },
    },
    methods: {
        handleImage(e, key) {
            var file = new FormData();
            file.append("file", e.target.files[0]);
            axios.post("/api/admin/media/image_again_upload", file).then((res) => {
                this.updateForm(key, res?.data);
            });
        },

        showError(message) {
            // You can implement your error display logic here
            alert(message); // Or use a more sophisticated notification system
        },

        updateForm(field, value) {
            // debugger;
            this.$store.commit("settings/setForm", {
                [field]: value,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("settings/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.site-settings.index", params: { id: 1 } })
                );
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store
                .dispatch("settings/fetchSetting", { id: id });
        }
    },
};
</script>
