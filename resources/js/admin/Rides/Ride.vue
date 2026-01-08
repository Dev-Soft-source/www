<template>
    <AppLayout>
        <div v-if="ride" class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 v-if="ride.departure_city || ride.destination_city" class="can-exp-h2 text-primary">
                            {{ ride.departure_city }} to
                            {{ ride.destination_city }}
                        </h3>

                        <button @click="goBack" class="button-exp-fill">
                            Back
                        </button>
                    </div>
                    <h3 v-if="ride.date || ride.time" class="can-exp-h4 text-secondary">
                        {{ ride.date }} @
                        {{ ride.time }}
                    </h3>
                </div>
            </header>
            <div
                class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                <div class="relative p-6 flex-auto">
                    <h4 class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        Ride history
                    </h4>
                    <hr style="margin-top: 10px" />
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <tr>
                                <td><b>Meeting and Drop off:</b></td>
                                <td>{{ ride.details }}</td>
                            </tr>
                            <tr>
                                <td class="content-start"><b>Vehicle:</b></td>
                                <td>
                                    <ul>
                                        <li>Model - {{ ride.model }}</li>
                                        <li>
                                            Vehicle type -
                                            {{ ride.vehicle_type }}
                                        </li>
                                        <li>
                                            Year - {{ ride.year }}, Color -
                                            {{ ride.color }}
                                        </li>
                                        <li>
                                            License No. - {{ ride.license_no }}
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Features:</b></td>
                                <td>{{ ride.features }}</td>
                            </tr>
                            <tr>
                                <td><b>Luggage:</b></td>
                                <td>{{ ride.luggage }}</td>
                            </tr>
                            <tr>
                                <td><b>Smoke:</b></td>
                                <td>{{ ride.smoke }}</td>
                            </tr>
                            <tr>
                                <td><b>Pet-friendly:</b></td>
                                <td>{{ ride.animal_friendly }}</td>
                            </tr>
                            <tr>
                                <td><b>Booking method:</b></td>
                                <td>{{ ride.booking_method }}</td>
                            </tr>
                            <tr>
                                <td><b>Notes:</b></td>
                                <td>{{ ride.notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <h4 class="flex items-center mt-10">Driver</h4>
                    <hr style="margin-top: 10px" />
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <tr>
                                <td><b>Name:</b></td>
                                <td>
                                    {{ ride.driver_first_name }}
                                    {{ ride.driver_last_name }}
                                </td>
                            </tr>
                            <tr>
                                <td class="content-start"><b>Email:</b></td>
                                <td>{{ ride.driver_email }}</td>
                            </tr>
                            <tr>
                                <td><b>Phone number:</b></td>
                                <td>{{ ride.driver_phone }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Ride Logs -->
                    <div>
                        <h4 class="flex items-center pt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            Ride logs
                        </h4>
                        <hr style="margin-top: 10px" />
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
                                <thead class="text-xs text-gray-700 bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            Changes
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Action
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Created at
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="log in ride.post_ride_logs.slice().reverse()" :key="log.id"
                                        class="bg-white border-b">
                                        <td class="px-6 py-4">
                                            <ul>
                                                <li v-for="(value, key) in parseChanges(log.changes)" :key="key">
                                                    <template v-if="key === 'skip_vehicle'">
                                                        <div v-if="value === 0">
                                                        </div>
                                                        <div v-else>
                                                            Vehicle: No vehicle selected
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <template v-if="key == 'random_id'">
                                                            ID: {{ value }}
                                                        </template>
                                                        <template v-else-if="key !== 'id'">
                                                            <template v-if="key == 'completed_time'">
                                                                Ride time: {{ value }}
                                                            </template>
                                                            <template v-if="key == 'car_image'">
                                                                <span class="capitalize"> {{ key }}:</span>
                                                                <a target="_blank" :href="getCarImageUrl(value)"
                                                                    class="text-blue-600 underline">
                                                                    {{ value }}
                                                                </a>

                                                            </template>
                                                            <template v-else>

                                                                <span class="capitalize"> {{ key }}:</span> {{ value }}
                                                            </template>
                                                        </template>
                                                    </template>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4">{{ log.action }}</td>
                                        <td class="px-6 py-4">{{ formatDate(log.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="ride.bookings.length > 0">
                        <h4 class="flex items-center pt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Bookings
                        </h4>
                        <hr style="margin-top: 10px" />
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
                                <thead class="text-xs text-gray-700 bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            Passenger
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Payment
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Booked on
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Cancelled on
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="booking in ride.bookings.slice().reverse()" :key="booking.id"
                                        class="bg-white border-b">
                                        <td class="px-6 py-4">{{ booking.passenger_first_name }} {{
                                            booking.passenger_last_name
                                            }}<br><small>{{ booking.passenger_email }}</small><br><small
                                                v-if="booking.passenger_gender == 'female'">{{ booking.passenger_gender
                                                }}</small><br><small
                                                v-if="booking.passenger_student == '1'">Student</small></td>
                                        <td class="px-6 py-4">Price/Seat: ${{ booking.price }} CAD<br>
                                            Seats booked: {{ booking.seats }}<br>
                                            Booking fee: {{ booking.booking_credit }}<br>
                                            Total cost: ${{ parseFloat(booking.price) +
                                                parseFloat(booking.booking_credit) }}<br><br>
                                            Payment method: {{ booking.payment_method }}</td>
                                        <td class="px-6 py-4">{{ getBookingStatus(booking.status) }}</td>
                                        <td class="px-6 py-4">{{ booking.booked_on }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
            ride: (state) => state.rides.ride,
            loading: (state) => state.rides.loading,
        }),
    },
    methods: {
        getCarImageUrl(fileName) {
            // Replace 'config('app.url')' with your base URL (you can replace this with `process.env.VUE_APP_BASE_URL` if needed)
            const baseUrl = process.env.VUE_APP_BASE_URL || window.location.origin;  // You can set the base URL as an environment variable
            return `${baseUrl}/car_images/${fileName}`;
        },
        goBack() {
            this.$router.go(-1);
        },
        getBookingStatus(bookingValue) {
            const bookingNumber = parseInt(bookingValue);
            if (bookingNumber === 0) {
                return "Booking requested";
            } else if (bookingNumber === 1) {
                return "Seat booked";
            } else if (bookingNumber === 2) {
                return "Ride completed";
            } else if (bookingNumber === 3) {
                return "Booking denied";
            }
        },
        parseChanges(changes) {
            try {
                const parsedChanges = JSON.parse(changes);
                // Filter out fields with empty values
                return Object.fromEntries(
                    Object.entries(parsedChanges)
                        .filter(([key, value]) => {
                            // Remove "skip_vehicle=0" and "add_vehicle=0"
                            if ((key === "added_vehicle" || key === "add_vehicle")) {
                                return false;
                            }
                            return value !== "" && value !== null;
                        })
                );
            } catch (e) {
                console.error("Failed to parse changes:", changes);
                return {};
            }
        },
        formatDate(date) {
            const options = { year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit" };
            return new Date(date).toLocaleDateString(undefined, options);
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.dispatch("rides/fetchRide", { id: id });
        }
    },
};
</script>