<template>
    <AppLayout>
        <div v-if="user" class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3
                            v-if="user.first_name || user.last_name"
                            class="can-exp-h2 text-primary"
                        >
                            {{ user.first_name }} {{ user.last_name }} details
                        </h3>
                        <button @click="goBack" class="button-exp-fill">
                            Back
                        </button>
                    </div>
                </div>
            </header>
            <div
                class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none"
            >
                <div class="relative p-6 flex-auto">
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <!-- <tr v-if="user.student !== '0'">
                                <td><b>Charge booking fee:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-1-yes" type="radio" name="charge_booking_fee" v-model="user.charge_booking" :value="'1'" disabled :checked="user.charge_booking !== '2' || !isStudentCardExpDateFuture()" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-1-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-1-no" type="radio" name="charge_booking_fee" v-model="user.charge_booking" :value="'2'" disabled :checked="user.charge_booking === '2' && isStudentCardExpDateFuture()" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-1-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td><b>Charge booking fee:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-1-yes" type="radio" name="charge_booking_fee" v-model="user.charge_booking" :value="'1'" @change="updateChargeBooking(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-1-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-1-no" type="radio" name="charge_booking_fee" v-model="user.charge_booking" :value="'2'" @change="updateChargeBooking(2)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-1-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr> -->
                            <tr>
                                <td><b>Email verified:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-3-yes" type="radio" name="email_verified" v-model="user.email_verified" :value="'1'" @change="updateEmailVerified(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-3-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-3-no" type="radio" name="email_verified" v-model="user.email_verified" :value="'0'" @change="updateEmailVerified(0)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-3-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="user.driver !== '0'">
                                <td><b>Driver's license verified:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-5-yes" type="radio" name="driver_verified" v-model="user.driver" :value="'1'" @change="approveDriver(user)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-5-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-5-no" type="radio" name="driver_verified" v-model="user.driver" :value="'3'" @change="rejectDriver(user)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-5-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td><b>Driver's license verified:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-5-yes" type="radio" name="driver_verified" disabled class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-5-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-5-no" type="radio" name="driver_verified" disabled class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-5-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="user.student !== '0'">
                                <td><b>Student card verified:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-2-yes" type="radio" name="student_verification" v-model="user.student" :value="'1'" @change="updateStudentVerification(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-2-no" type="radio" name="student_verification" v-model="user.student" :value="'3'" @change="updateStudentVerification(3)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td><b>Student card verified:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-2-yes" type="radio" name="student_verification" v-model="user.student" :value="'1'" disabled @change="updateStudentVerification(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-2-no" type="radio" name="student_verification" v-model="user.student" :value="'3'" disabled @change="updateStudentVerification(3)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>ProximaRide:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-2-yes" type="radio" name="pink_ride" v-model="user.pink_ride" :value="'1'" :checked="user.pink_ride === '1'" @change="updatePinkRideStatus(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-yes">Enable</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-2-no" type="radio" name="pink_ride" v-model="user.pink_ride" :value="'0'" :checked="user.pink_ride === '0'" @change="updatePinkRideStatus(0)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-no">Disable</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="bordered-radio-2-none" type="radio" name="pink_ride" v-model="user.pink_ride" :value="''" :checked="user.pink_ride === ''" @change="updatePinkRideStatus('')" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-none">Default</label>
                                            <p class="text-sm font-semibold"
                                               :class="isPinkRideDisabled ? 'text-red-600' : 'text-green-600'">
                                                ({{ isPinkRideDisabled ? 'Disabled' : 'Enabled' }})
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Extra-Care Rides:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-2-yes" type="radio" name="folks_ride" v-model="user.folks_ride" :value="'1'" :checked="user.folks_ride === '1'" @change="updateFolkRideStatus(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-yes">Enable</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-2-no" type="radio" name="folks_ride" v-model="user.folks_ride" :value="'0'" :checked="user.folks_ride === '0'" @change="updateFolkRideStatus(0)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-no">Disable</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="bordered-radio-2-none" type="radio" name="folks_ride" v-model="user.folks_ride" :value="''" :checked="user.folks_ride === ''" @change="updateFolkRideStatus('')" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-none">Default</label>
                                            <p class="text-sm font-semibold"
                                               :class="isFolksRideDisabled ? 'text-red-600' : 'text-green-600'">
                                                ({{ isFolksRideDisabled ? 'Disabled' : 'Enabled' }})
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Government issued id:</b></td>
                                <td>
                                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                                        <div>
                                            <input id="bordered-radio-2-yes" type="radio" name="government_issued_id" v-model="user.government_id" :value="'0'" :checked="user.government_issued_id !== '' && user.government_id !== '1'" @change="updateGovernmentId(0)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-yes">Yes</label>
                                        </div>
                                        <div>
                                            <input id="bordered-radio-2-no" type="radio" name="government_issued_id" v-model="user.government_id" :value="'1'" :checked="user.government_issued_id === '' || user.government_id === '1'" @change="updateGovernmentId(1)" class="w-4 h-4 me-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                            <label for="bordered-radio-2-no">No</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h4 class="flex items-center mt-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 mr-2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"
                            />
                        </svg>
                        About
                    </h4>
                    <hr style="margin-top: 10px" />
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <tr>
                                <td><b>Gender:</b></td>
                                <td>{{ user.gender }}</td>
                            </tr>
                            <tr>
                                <td><b>Email:</b></td>
                                <td>{{ user.email }}</td>
                            </tr>
                            <tr>
                                <td><b>Phone:</b></td>
                                <td>{{ user.phone }}</td>
                            </tr>
                            <tr>
                                <td><b>Country:</b></td>
                                <td>{{ countryName }}</td>
                            </tr>
                            <tr>
                                <td><b>City:</b></td>
                                <td>{{ cityName }}</td>
                            </tr>
                            <tr>
                                <td><b>Referral:</b></td>
                                <td>{{ user.referral }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <h4 class="flex items-center mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg> Documents</h4>
                    <hr style="margin-top: 10px" />
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <tr v-if="user.driver_liscense">
                                <td><b>Driver License:</b></td>
                                <td><a :href="user.driver_liscense" target="_blank">{{ user.driver_liscense }}</a></td>
                            </tr>
                            <tr v-if="user.student_card">
                                <td class="content-start"><b>Student card:</b></td>
                                <td><a :href="user.student_card" target="_blank">{{ user.student_card }}</a></td>
                            </tr>
                            <tr v-if="user.government_issued_id">
                                <td class="content-start"><b>Government issued id:</b></td>
                                <td><a :href="user.government_issued_id" target="_blank">{{ user.government_issued_id }}</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState({
            users: (state) => state.users.users,
            user: (state) => state.users.user,
            rating: (state) => state.users.rating,
            loading: (state) => state.users.loading,
            pinkRideSetting: (state) => state.pink_ride_settings.form,
            setting: (state) => state.folk_ride_settings.form,
            no_show_count: (state) => state.no_shows.no_show_count,
            cancellation_count: (state) => state.no_shows.cancellation_count,
        }),
        shouldCheckYesOption() {
            return this.user.student !== '1';
        },
        isPinkRideDisabled() {
            if (this.pinkRideSetting) {
                if (this.user.gender == 'female' && (!this.user.government_issued_id || !this.user.address) ) {

                    return true;
                }
                if (this.user.gender !== 'female') {
                    return true;
                }
                if (this.pinkRideSetting.verfiy_phone === '1' && this.user.phone_verified !== '1') {
                    return true;
                }
                if (this.pinkRideSetting.verify_email === '1' && this.user.email_verified !== '1') {
                    return true;
                }
                if (this.pinkRideSetting.driver_license === '1' && this.user.driver !== '1') {
                    return true;
                }
            }
            return false;
        },
        isFolksRideDisabled() {
            let disabled = false;

            // Age Calculation
            let age = this.calculateAge(this.user.dob);
            console.log("DOB value:", this.user.dob); 
            console.log("Calculated Age:", age);  

            if (this.setting) {
                if (this.setting.verfiy_phone == '1' && 
                    !this.user.phone_verified == '1') {
                    disabled = true;
                } else if (this.setting.verify_email === '1' && 
                           this.user.email_verified !== '1') {
                    disabled = true;
                } else if (this.setting.driver_license === '1' && 
                           this.user.driver !== '1') {
                    disabled = true;
                } else if (this.rating < this.setting.average_rating || 
                           age < this.setting.driver_age) {
                    disabled = true;
                } else if (this.no_show_count > 0) {
                    disabled = true;
                } else if (this.cancellation_count > 0) {
                    disabled = true;
                }
                else if (!this.user.government_issued_id || !this.user.address ) {
                    disabled = true;
                }
            }
            return disabled;
        },
    },
    data() {
        return {
            countryName: null,
            cityName: null,
        };
    },
    methods: {
        goBack() {
            this.$router.go(-1);
        },
        isStudentCardExpDateFuture() {
            // Assuming user.student_card_exp_date is a valid Date string or Date object
            const expDate = new Date(this.user.student_card_exp_date);
            const currentDate = new Date();
            return expDate > currentDate;
        },
        updateStudentVerification(value) {
            this.$store.dispatch("users/updateStudentVerification", { id: this.user.id, student: value, isStudentCardExpDateFuture: this.isStudentCardExpDateFuture });
        },
        updateChargeBooking(value) {
            this.$store.dispatch("users/updateChargeBooking", { id: this.user.id, charge_booking: value });
        },
        updateEmailVerified(value) {
            this.$store.dispatch("users/updateEmailVerified", { id: this.user.id, email_verified: value });
        },
        updateGovernmentId(value) {
            this.$store.dispatch("users/updateGovernmentId", { id: this.user.id, government_id: value });
        },
        updatePinkRideStatus(value) {
            this.$store.dispatch("users/updatePinkRideStatus", { id: this.user.id, pink_ride: value });
        },
        updateFolkRideStatus(value) {
            this.$store.dispatch("users/updateFolkRideStatus", { id: this.user.id, folks_ride: value });
        },
        approveDriver(driver) {
            this.$store.dispatch("users/approveDriver", { id: driver.id });
        },
        rejectDriver(driver) {
            this.$store.dispatch("users/rejectDriver", { id: driver.id });
        },
        async getCountryName(countryCode) {
            try {
                const response = await axios.get(`${process.env.MIX_ADMIN_API_URL}countries/${countryCode}`);
                this.countryName = response.data.data.name;
            } catch (error) {
                console.error('Error fetching country name:', error);
            }
        },
        async getCityName(countryCode) {
            try {
                const response = await axios.get(`${process.env.MIX_ADMIN_API_URL}cities/${countryCode}`);
                this.cityName = response.data.data.name;
            } catch (error) {
                console.error('Error fetching city name:', error);
            }
        },
        calculateAge(dob) {
            if (!dob) return 0; // Handle case where DOB is missing

            let birthDate = new Date(dob);
            let today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            let monthDiff = today.getMonth() - birthDate.getMonth();

            // Adjust age if birthday hasn't occurred yet this year
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age;
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.dispatch("users/fetchUser", { id: id });
            this.$store
                .dispatch("pink_ride_settings/fetchSetting", { id: 1 });
            this.$store
                .dispatch("folk_ride_settings/fetchSetting", { id: 1 });
            this.$store
                .dispatch("no_shows/noShowsCount", { id: id });
            this.$store
                .dispatch("no_shows/cancellationCount", { id: id });

            // Call getCountryName when the user data is available
            this.$watch('user', (newUser) => {
                if (newUser) {
                    this.getCountryName(newUser.country);
                    this.getCityName(newUser.city);
                }
            });
        }
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