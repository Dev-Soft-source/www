<template>
    <AppLayout>
        <div v-if="rating" class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3
                            class="can-exp-h2 text-primary"
                        >
                            Review
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
                            <tr>
                                <td><b>Review:</b></td>
                                <td>{{ rating.review }}</td>
                            </tr>
                            <tr>
                                <td v-if="rating.reply"><b>Reply:</b></td>
                                <td>{{ rating.reply }}</td>
                            </tr>
                            <tr>
                                <td><b>Appear on home page:</b></td>
                                <td><input type="checkbox" :checked="rating.is_disply" @click="updateDisplyCheck($event.target.checked)" /></td>
                            </tr>
                        </tbody>
                    </table>
                    <hr style="margin-top: 10px" />
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
                        Review Criteria
                    </h4>
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <tr>
                                <td><b>Condition of the vehicle:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.vehicle_condition" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Conscious to passengers wellness:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.conscious" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Comfort:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.comfort" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Communication:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.communication" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Overall attitude:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.attitude" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Personal hygiene:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.hygiene" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Respect and courtesy:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.respect" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Safety:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.safety" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Timeliness:</b></td>
                                <td class="flex items-center gap-1">
                                    <div v-for="i in 5" :key="i" class="relative">
                                        <img v-if="i <= rating.timeliness" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                        <img v-else src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Average:</b></td>
                                <td class="flex items-center gap-1">
                                    <img v-for="i in averageRating.fullStars" :key="'fullStar-' + i" src="/assets/11-review-full-star.png" class="w-5 h-5 mt-0.5" alt="">
                                    <!-- Display fractional star if present -->
                                    <img v-if="averageRating.fraction === 0.25" src="/assets/11-review-4.25-stars.png" class="w-5 h-5 mt-0.5" alt="">
                                    <img v-if="averageRating.fraction === 0.5" src="/assets/11-review-4.5-stars.png" class="w-5 h-5 mt-0.5" alt="">
                                    <img v-if="averageRating.fraction === 0.75" src="/assets/11-review-4.75-stars.png" class="w-5 h-5 mt-0.5" alt="">
                                    <!-- Display remaining grey stars -->
                                    <img v-for="i in 5 - averageRating.fullStars - (averageRating.fraction !== 0 ? 1 : 0)" :key="'greyStar-' + i" src="/assets/11-review-full-star-grey.png" class="w-5 h-5 mt-0.5" alt="">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr style="margin-top: 10px" />
                    <table class="table table-striped table-fixed w-full">
                        <tbody>
                            <tr>
                                <td><b>Added on:</b></td>
                                <td class="flex items-center gap-1">{{ rating.added_on }}</td>
                            </tr>
                            <tr>
                                <td><b>Status:</b></td>
                                <td v-if="rating.status === '0'" class="flex items-center gap-1">Not live</td>
                                <td v-if="rating.status === '1'" class="flex items-center gap-1">Live</td>
                                <td v-if="rating.status === '2'" class="flex items-center gap-1">Suspend</td>
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
            ratings: (state) => state.ratings.ratings,
            rating: (state) => state.ratings.rating,
            loading: (state) => state.ratings.loading,
        }),
        averageRating() {
            let sum = 0;
            let count = 0;

            // Define the columns to calculate the average from
            const columns = [
                this.rating.vehicle_condition,
                this.rating.timeliness,
                this.rating.safety,
                this.rating.conscious,
                this.rating.comfort,
                this.rating.communication,
                this.rating.attitude,
                this.rating.respect,
                this.rating.hygiene
            ];

            // Calculate sum and count of non-null values
            columns.forEach(column => {
              if (column !== null && !isNaN(column)) {
                sum += Number(column);
                count++;
              }
            });

            // Calculate average rating
            const average = count > 0 ? sum / count : null;

            // Calculate full stars and fractional part
            const fullStars = Math.floor(average);
            const fraction = average - fullStars;

            // Adjust fraction to 0.25, 0.5, 0.75
            let adjustedFraction = 0;
            if (fraction >= 0 && fraction < 0.25) {
                adjustedFraction = 0;
            } else if (fraction >= 0.25 && fraction < 0.5) {
                adjustedFraction = 0.25;
            } else if (fraction >= 0.5 && fraction < 0.75) {
                adjustedFraction = 0.5;
            } else if (fraction >= 0.75) {
                adjustedFraction = 0.75;
            }

            return {
                fullStars: fullStars,
                fraction: adjustedFraction
            };
        }
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
        updateDisplyCheck(val) {
            if (this.$route.params.id) {
                let id = this.$route.params.id;
                this.$store.dispatch("ratings/updateDisplyCheckbox", { id: id, val:val });
            }
        },
    },
    created() {
        if (this.$route.params.id) {
            let id = this.$route.params.id;
            this.$store.dispatch("ratings/fetchRating", { id: id });
        }
    },
};
</script>