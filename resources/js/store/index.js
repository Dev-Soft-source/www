import { createStore } from "vuex";
import menus from "./admin/menus.js";
import languages from "./admin/languages.js";
import countries from "./admin/countries.js";
import states from "./admin/states.js";
import cities from "./admin/cities.js"
import banks from "./admin/banks.js"
import extraCareFaqs from "./admin/extra_care_faqs.js"
import pinkRideFaqs from "./admin/pink_ride_faqs.js"
import rewardPointSettings from "./admin/driver_reward_points.js"
import studentsRewardPointSettings from "./admin/student_reward_points.js"
import packages from "./admin/packages.js"
import coffeewallets from "./admin/coffee_wallet.js"
import bank_settings from "./admin/bank_settings.js";
import review_settings from "./admin/review_settings.js";
import pink_ride_settings from "./admin/pink_ride_settings.js"
import folk_ride_settings from "./admin/folk_ride_settings.js"
import cancel_ride_settings from "./admin/cancel_ride_settings.js"
import referral_system_settings from "./admin/referral_system_settings.js"
import messages from "./admin/messages.js"
import closed_account_messages from "./admin/closed_account_messages.js"
import registration_reward_settings from "./admin/registration_reward_settings.js"
import pages from "./admin/pages.js";
import users from "./admin/users.js";
import auth from "./admin/auth.js";
import drivers from "./admin/driver_verification.js";
import students from "./admin/student_verification.js";
import passengers from "./admin/passengers.js";
import rides from "./admin/rides.js";
import no_shows from "./admin/no_shows.js";
import bookings from "./admin/bookings.js";
import ratings from "./admin/reviews.js";
import transactions from "./admin/transactions.js";
import withdrawals from "./admin/withdrawal_requests.js";
import verify_banks from "./admin/verify_banks.js";
import verify_phones from "./admin/verify_phones.js";
import claim_rewards from "./admin/claim_rewards.js";
import credits from "./admin/booking_credits.js";
import articles from "./admin/news.js";
import videos from "./admin/videos.js";
import settings from "./admin/site_settings.js";
import errors from "./admin/errors.js";

export default new createStore({
    strict: true,
    modules: {
        errors,
        menus,
        languages,
        countries,
        states,
        cities,
        banks,
        extraCareFaqs,
        pinkRideFaqs,
        rewardPointSettings,
        studentsRewardPointSettings,
        packages,
        coffeewallets,
        bank_settings,
        review_settings,
        pink_ride_settings,
        folk_ride_settings,
        cancel_ride_settings,
        referral_system_settings,
        registration_reward_settings,
        messages,
        closed_account_messages,
        pages,
        users,
        drivers,
        students,
        passengers,
        rides,
        no_shows,
        bookings,
        ratings,
        transactions,
        withdrawals,
        verify_banks,
        verify_phones,
        claim_rewards,
        credits,
        articles,
        videos,
        settings,
        auth,
    },
});
