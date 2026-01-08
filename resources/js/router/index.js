import { createRouter, createWebHistory } from "vue-router";

import Dashboard from '../admin/Dashboard/Dashboard.vue'
import Profile from '../admin/Profile/Profile.vue'
import Languages from '../admin/Languages/Languages.vue'
import CreateLanguages from '../admin/Languages/Create.vue'
import Countries from '../admin/Countries/Countries.vue'
import CreateCountries from '../admin/Countries/Create.vue'
import States from '../admin/States/States.vue'
import CreateStates from '../admin/States/Create.vue'
import Cities from '../admin/Cities/Cities.vue'
import CreateCities from '../admin/Cities/Create.vue'
import Packages from '../admin/Packages/Packages.vue'
import Banks from '../admin/Banks/Banks.vue'
import CreateBanks from '../admin/Banks/Create.vue'
import ExtraCareFaqs from '../admin/Extra_Care_Faqs/ExtraCareFaqs.vue'
import CreateExtraCareFaqs from '../admin/Extra_Care_Faqs/Create.vue'
import PinkRideFaqs from '../admin/Pink_Ride_Faqs/PinkRideFaqs.vue'
import CreatePinkRideFaqs from '../admin/Pink_Ride_Faqs/Create.vue'
import DriverRewardPoints from '../admin/Driver_Reward_Points/DriverRewardPoints.vue'
import CreateDriverRewardPoints from '../admin/Driver_Reward_Points/Create.vue'
import StudentRewardPoints from '../admin/Student_Reward_Points/StudentRewardPoints.vue'
import CreateStudentRewardPoints from '../admin/Student_Reward_Points/Create.vue'
import CreatePackages from '../admin/Packages/Create.vue'
import BankSettings from '../admin/General_Settings/BankSettings.vue'
import ReviewSettings from '../admin/General_Settings/ReviewSettings.vue'
import PinkRideSettings from '../admin/General_Settings/PinkRideSettings.vue'
import FolkRideSettings from '../admin/General_Settings/FolkRideSettings.vue'
import CancelRideSettings from '../admin/General_Settings/CancelRideSettings.vue'
import ReferralSystemSettings from '../admin/General_Settings/ReferralSystemSettings.vue'
import RegistrationRewardSettings from '../admin/General_Settings/RegistrationRewardSettings.vue'
import CreateSuccessMessagesSetting from '../admin/General_Settings/SuccessMessagesSettings.vue'
import CreateErrorHandlingSetting from '../admin/General_Settings/ErrorHandlingSettings.vue'
import ContactMessages from '../admin/Form_Submissions/ContactMessages.vue'
import ClosedAccountMessages from '../admin/Form_Submissions/ClosedAccountMessages.vue'
import CreateLoginSetting from '../admin/Mobile_Screens/CreateLoginSetting.vue'
import CreateSignupSetting from '../admin/Mobile_Screens/CreateSignupSetting.vue'
import CreateForgotSetting from '../admin/Mobile_Screens/CreateForgotSetting.vue'
import CreateResetPasswordSetting from '../admin/Mobile_Screens/CreateResetPasswordSetting.vue'
import CreatePostRideSetting from '../admin/Mobile_Screens/CreatePostRideSetting.vue'
import CreateFindRideSetting from '../admin/Mobile_Screens/CreateFindRideSetting.vue'
import CreateHomePageSetting from '../admin/Pages/CreateHomePageSetting.vue'
import CreateStudentPageSetting from '../admin/Pages/CreateStudentPageSetting.vue'
import CreateDriverPageSetting from '../admin/Pages/CreateDriverPageSetting.vue'
import CreatePassengerPageSetting from '../admin/Pages/CreatePassengerPageSetting.vue'
import CreateFindRidePageSetting from '../admin/Pages/CreateFindRidePageSetting.vue'
import CreateRideDetailPageSetting from '../admin/Pages/CreateRideDetailPageSetting.vue'
import CreateBookingPageSetting from '../admin/Pages/CreateBookingPageSetting.vue'
import CreateReferralPageSetting from '../admin/Pages/CreateReferralPageSetting.vue'
import CreatePostRidePageSetting from '../admin/Pages/CreatePostRidePageSetting.vue'
import CreateLoginPageSetting from '../admin/Pages/CreateLoginPageSetting.vue'
import CreateForgotPasswordPageSetting from '../admin/Pages/CreateForgotPasswordPageSetting.vue'
import CreateResetPasswordPageSetting from '../admin/Pages/CreateResetPasswordPageSetting.vue'
import CreateSignupPageSetting from '../admin/Pages/CreateSignupPageSetting.vue'
import CreateStep1PageSetting from '../admin/Pages/CreateStep1PageSetting.vue'
import CreateStep2PageSetting from '../admin/Pages/CreateStep2PageSetting.vue'
import CreateStep3PageSetting from '../admin/Pages/CreateStep3PageSetting.vue'
import CreateStep4PageSetting from '../admin/Pages/CreateStep4PageSetting.vue'
import CreateStep5PageSetting from '../admin/Pages/CreateStep5PageSetting.vue'
import CreateContactUsPageSetting from '../admin/Pages/CreateContactUsPageSetting.vue'
import CreateTermsAndConditionPageSetting from '../admin/Pages/CreateTermsAndConditionPageSetting.vue'
import CreatePrivacyPolicyPageSetting from '../admin/Pages/CreatePrivacyPolicyPageSetting.vue'
import CreateTermsOfUsePageSetting from '../admin/Pages/CreateTermsOfUsePageSetting.vue'
import CreateRefundPolicyPageSetting from '../admin/Pages/CreateRefundPolicyPageSetting.vue'
import CreateCancellationPageSetting from '../admin/Pages/CreateCancellationPageSetting.vue'
import CreateFirmCancellationPageSetting from '../admin/Pages/CreateFirmCancellationPageSetting.vue'
import CreateDisputePageSetting from '../admin/Pages/CreateDisputePageSetting.vue'
import CreateChatsPageSetting from '../admin/Pages/CreateChatsPageSetting.vue'
import CreateSelectLocationPageSetting from '../admin/Pages/CreateSelectLocationPageSetting.vue'
import CreateCoffeeWallPageSetting from '../admin/Pages/CreateCoffeeWallPageSetting.vue'
import CreateTripsPageSetting from '../admin/Pages/CreateTripsPageSetting.vue'
import CreateFeaturesSetting from '../admin/Pages/CreateFeaturesSetting.vue'
import CreatePreferencesSetting from '../admin/Pages/CreatePreferencesSetting.vue'
import CreatePaymentMethodsSetting from '../admin/Pages/CreatePaymentMethodsSetting.vue'
import CreateLuggageOptionsSetting from '../admin/Pages/CreateLuggageOptionsSetting.vue'
import CreateProfilePageSetting from '../admin/Pages/CreateProfilePageSetting.vue'
import CreateProfilePhotoSetting from '../admin/Pages/CreateProfilePhotoSetting.vue'
import CreateMyVehicleSetting from '../admin/Pages/CreateMyVehicleSetting.vue'
import CreatePasswordSetting from '../admin/Pages/CreatePasswordSetting.vue'
import CreateMyPhoneSetting from '../admin/Pages/CreateMyPhoneSetting.vue'
import CreateMyEmailPageSetting from '../admin/Pages/CreateMyEmailPageSetting.vue'
import CreateMyDriverLicenseSetting from '../admin/Pages/CreateMyDriverLicenseSetting.vue'
import CreateMyStudentCardPageSetting from '../admin/Pages/CreateMyStudentCardSetting.vue'
import CreateEditProfilePage from '../admin/Pages/CreateEditProfilePageSetting.vue'
import CreateMyWalletSettingPage from '../admin/Pages/CreateMyWalletSetting.vue'
import CreatePaymentOptionSettingPage from '../admin/Pages/CreatePaymentOptionSetting.vue'
import CreatePayoutOptionSettingPage from '../admin/Pages/CreatePayoutOptionSetting.vue'
import CreateReviewSettingPage from '../admin/Pages/CreateReviewSettingPage.vue'
import CreateContactProximaRideSetting from '../admin/Pages/CreateContactProximaRideSetting.vue'
import CreateLogoutSetting from '../admin/Pages/CreateLogoutPageSetting.vue'
import CreateBillingAddressSetting from '../admin/Pages/CreateBillingAddressSetting.vue'
import CreateCloseAccountSetting from '../admin/Pages/CreateCloseAccountSetting.vue'
import CreateProfileSetting from '../admin/Pages/CreateProfileSetting.vue'
import CreateMyPassengerSetting from '../admin/Pages/CreateMyPassengerSetting.vue'

import Users from '../admin/Users/Users.vue'
import User from '../admin/Users/User.vue'
import Passengers from '../admin/Passengers/Passengers.vue'
import Passenger from '../admin/Passengers/Passenger.vue'
import DriverVerification from '../admin/Driver_Verification/DriverVerification.vue'
import Driver from '../admin/Driver_Verification/Driver.vue'
import StudentVerification from '../admin/Student_Verification/StudentVerification.vue'
import Student from '../admin/Student_Verification/Student.vue'
import NoShow from '../admin/No_Show/NoShow.vue'
import Rides from '../admin/Rides/Rides.vue'
import Ride from '../admin/Rides/Ride.vue'
import Bookings from '../admin/Bookings/Bookings.vue'
import SecuredCashBookings from '../admin/Bookings/SecuredCashBookings.vue'
import Reviews from '../admin/Reviews/Reviews.vue'
import Review from '../admin/Reviews/Review.vue'
import Transactions from '../admin/Transactions/Transactions.vue'
import WithdrawalRequests from '../admin/Withdrawal_Requests/WithdrawalRequests.vue'
import CoffeeWallets from '../admin/Coffee_Wallets/CoffeeWallets.vue'
import VerifyBanks from '../admin/Verify_Banks/VerifyBanks.vue'
import VerifyPhones from '../admin/Verify_Phones/VerifyPhones.vue'
import ClaimRewards from '../admin/Claim_Rewards/ClaimRewards.vue'
import BookingCredits from '../admin/Booking_Credits/BookingCredits.vue'
import CreateCredits from '../admin/Booking_Credits/Create.vue'
import News from '../admin/News/News.vue'
import Videos from  '../admin/Videos/Videos.vue'
import SiteSettings from '../admin/Site_Settings/SiteSettings.vue'
import CreateNews from '../admin/News/Create.vue'
import CreateVideos from '../admin/Videos/Create.vue'
const routes = [
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/languages',
        name: 'admin.languages.index',
        component: Languages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Languages', 'routeName': 'admin.languages.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/languages/create',
        name: 'admin.languages.create',
        component: CreateLanguages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Languages', 'routeName': 'admin.languages.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.languages.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/languages/:id/edit',
        name: 'admin.languages.edit',
        component: CreateLanguages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Languages', 'routeName': 'admin.languages.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.languages.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/countries',
        name: 'admin.countries.index',
        component: Countries,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Countries', 'routeName': 'admin.countries.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/countries/create',
        name: 'admin.countries.create',
        component: CreateCountries,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Countries', 'routeName': 'admin.countries.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.countries.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/countries/:id/edit',
        name: 'admin.countries.edit',
        component: CreateCountries,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Countries', 'routeName': 'admin.countries.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.countries.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/states',
        name: 'admin.states.index',
        component: States,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'States', 'routeName': 'admin.states.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/states/create',
        name: 'admin.states.create',
        component: CreateStates,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'States', 'routeName': 'admin.states.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.states.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/states/:id/edit',
        name: 'admin.states.edit',
        component: CreateStates,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'States', 'routeName': 'admin.states.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.states.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/cities',
        name: 'admin.cities.index',
        component: Cities,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Cities', 'routeName': 'admin.cities.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/cities/create',
        name: 'admin.cities.create',
        component: CreateCities,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Cities', 'routeName': 'admin.cities.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.cities.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/cities/:id/edit',
        name: 'admin.cities.edit',
        component: CreateCities,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Cities', 'routeName': 'admin.cities.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.cities.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/banks',
        name: 'admin.banks.index',
        component: Banks,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Banks', 'routeName': 'admin.banks.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/banks/create',
        name: 'admin.banks.create',
        component: CreateBanks,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Banks', 'routeName': 'admin.banks.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.banks.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/banks/:id/edit',
        name: 'admin.banks.edit',
        component: CreateBanks,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Banks', 'routeName': 'admin.banks.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.banks.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/extra-care-faqs',
        name: 'admin.extra-care-faqs.index',
        component: ExtraCareFaqs,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Extra-Care FAQs', 'routeName': 'admin.extra-care-faqs.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/extra-care-faqs/create',
        name: 'admin.extra-care-faqs.create',
        component: CreateExtraCareFaqs,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Extra-Care FAQs', 'routeName': 'admin.extra-care-faqs.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.extra-care-faqs.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/extra-care-faqs/:id/edit',
        name: 'admin.extra-care-faqs.edit',
        component: CreateExtraCareFaqs,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Extra-Care FAQs', 'routeName': 'admin.extra-care-faqs.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.extra-care-faqs.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pink-ride-faqs',
        name: 'admin.pink-ride-faqs.index',
        component: PinkRideFaqs,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Pink-Ride FAQs', 'routeName': 'admin.pink-ride-faqs.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pink-ride-faqs/create',
        name: 'admin.pink-ride-faqs.create',
        component: CreatePinkRideFaqs,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Pink-Ride FAQs', 'routeName': 'admin.pink-ride-faqs.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.pink-ride-faqs.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pink-ride-faqs/:id/edit',
        name: 'admin.pink-ride-faqs.edit',
        component: CreatePinkRideFaqs,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Pink-Ride FAQs', 'routeName': 'admin.pink-ride-faqs.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.pink-ride-faqs.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/driver-reward-points',
        name: 'admin.driver-reward-points.index',
        component: DriverRewardPoints,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'DriverRewardPoints', 'routeName': 'admin.driver-reward-points.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/driver-reward-points/create',
        name: 'admin.driver-reward-points.create',
        component: CreateDriverRewardPoints,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'DriverRewardPoints', 'routeName': 'admin.driver-reward-points.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.driver-reward-points.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/driver-reward-points/:id/edit',
        name: 'admin.driver-reward-points.edit',
        component: CreateDriverRewardPoints,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'DriverRewardPoints', 'routeName': 'admin.driver-reward-points.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.driver-reward-points.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/student-reward-points',
        name: 'admin.student-reward-points.index',
        component: StudentRewardPoints,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'StudentRewardPoints', 'routeName': 'admin.student-reward-points.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/student-reward-points/create',
        name: 'admin.student-reward-points.create',
        component: CreateStudentRewardPoints,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'StudentRewardPoints', 'routeName': 'admin.student-reward-points.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.student-reward-points.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/student-reward-points/:id/edit',
        name: 'admin.student-reward-points.edit',
        component: CreateStudentRewardPoints,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'StudentRewardPoints', 'routeName': 'admin.student-reward-points.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.student-reward-points.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/packages',
        name: 'admin.packages.index',
        component: Packages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Packages', 'routeName': 'admin.packages.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/packages/create',
        name: 'admin.packages.create',
        component: CreatePackages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Packages', 'routeName': 'admin.packages.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.packages.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/packages/:id?/edit',
        name: 'admin.packages.edit',
        component: CreatePackages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Packages', 'routeName': 'admin.packages.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.packages.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/bank-settings',
        name: 'admin.bank-settings.index',
        component: BankSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Bank settings', 'routeName': 'admin.bank-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/review-settings/:id/edit',
        name: 'admin.review-settings.index',
        component: ReviewSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Review settings', 'routeName': 'admin.review-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pink-ride-settings/:id/edit',
        name: 'admin.pink-ride-settings.index',
        component: PinkRideSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Pink ride settings', 'routeName': 'admin.pink-ride-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/folk-ride-settings/:id/edit',
        name: 'admin.folk-ride-settings.index',
        component: FolkRideSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Extra-Care Rides', 'routeName': 'admin.folk-ride-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/cancel-ride-settings/:id/edit',
        name: 'admin.cancel-ride-settings.index',
        component: CancelRideSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Cancel ride settings', 'routeName': 'admin.cancel-ride-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/referral-system-settings/:id/edit',
        name: 'admin.referral-system-settings.index',
        component: ReferralSystemSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'referral system settings', 'routeName': 'admin.referral-system-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/registration-reward-settings/:id/edit',
        name: 'admin.registration-reward-settings.index',
        component: RegistrationRewardSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Cancel ride settings', 'routeName': 'admin.registration-reward-settings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/contact-messages',
        name: 'admin.contact-messages.index',
        component: ContactMessages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Contact messages', 'routeName': 'admin.contact-messages.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/closed-account-messages',
        name: 'admin.closed-account-messages.index',
        component: ClosedAccountMessages,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Closed account messages', 'routeName': 'admin.closed-account-messages.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/mobile-login-page-setting',
        name: 'admin.mobile-login-page-setting.index',
        component: CreateLoginSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Login page setting', 'routeName': 'admin.mobile-login-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/mobile-signup-page-setting',
        name: 'admin.mobile-signup-page-setting.index',
        component: CreateSignupSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Signup page setting', 'routeName': 'admin.mobile-signup-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/mobile-forgot-password-setting',
        name: 'admin.mobile-forgot-password-setting.index',
        component: CreateForgotSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Forgot password page setting', 'routeName': 'admin.mobile-forgot-password-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/mobile-reset-password-setting',
        name: 'admin.mobile-reset-password-setting.index',
        component: CreateResetPasswordSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Reset password page setting', 'routeName': 'admin.mobile-reset-password-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/mobile-post-ride-page-setting',
        name: 'admin.mobile-post-ride-page-setting.index',
        component: CreatePostRideSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Post ride page setting', 'routeName': 'admin.mobile-post-ride-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/mobile-find-ride-page-setting',
        name: 'admin.mobile-find-ride-page-setting.index',
        component: CreateFindRideSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Find ride page setting', 'routeName': 'admin.mobile-find-ride-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/success-error-messages-setting',
        name: 'admin.success-messages-setting.index',
        component: CreateSuccessMessagesSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Home page setting', 'routeName': 'admin.success-messages-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/error-messages-setting',
        name: 'admin.error-messages-setting.index',
        component: CreateErrorHandlingSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Home page setting', 'routeName': 'admin.error-messages-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/home-page-settings',
        name: 'admin.home-page-setting.index',
        component: CreateHomePageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Home page setting', 'routeName': 'admin.home-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/student-page-settings',
        name: 'admin.student-page-setting.index',
        component: CreateStudentPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Students page setting', 'routeName': 'admin.student-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/driver-page-settings',
        name: 'admin.driver-page-setting.index',
        component: CreateDriverPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Drivers page setting', 'routeName': 'admin.driver-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/passenger-page-settings',
        name: 'admin.passenger-page-setting.index',
        component: CreatePassengerPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Passengers page settings', 'routeName': 'admin.passenger-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/find-ride-page-settings',
        name: 'admin.find-ride-page-setting.index',
        component: CreateFindRidePageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Find ride page setting', 'routeName': 'admin.find-ride-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/ride-details-page-settings',
        name: 'admin.ride-detail-page-setting.index',
        component: CreateRideDetailPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Trip detail page setting', 'routeName': 'admin.ride-detail-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/booking-page-settings',
        name: 'admin.booking-page-setting.index',
        component: CreateBookingPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Booking page settings', 'routeName': 'admin.booking-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/post-ride-page-settings',
        name: 'admin.post-ride-page-setting.index',
        component: CreatePostRidePageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Post ride page setting', 'routeName': 'admin.post-ride-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/login-page-settings',
        name: 'admin.login-page-setting.index',
        component: CreateLoginPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Login page settings', 'routeName': 'admin.login-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/profile-page-settings',
        name: 'admin.profile-page-setting.index',
        component: CreateProfilePageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Profile page settings', 'routeName': 'admin.profile-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/profile-settings',
        name: 'admin.profile-setting.index',
        component: CreateProfileSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Profile settings', 'routeName': 'admin.profile-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/my-passenger-settings',
        name: 'admin.my-passenger-setting.index',
        component: CreateMyPassengerSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My Passenger setting', 'routeName': 'admin.my-passenger-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/profile-photo-settings',
        name: 'admin.profile-photo-setting.index',
        component: CreateProfilePhotoSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Profile photo settings', 'routeName': 'admin.profile-photo-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/my-vehicle-settings',
        name: 'admin.my-vehicle-setting.index',
        component: CreateMyVehicleSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My vehcile settings', 'routeName': 'admin.my-vehicle-setting.index', 'isCurrentRoute': 1}],
        },
    },
     {
        path: '/admin/pages/password-settings',
        name: 'admin.password-setting.index',
        component: CreatePasswordSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Password settings', 'routeName': 'admin.password-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/my-phone-settings',
        name: 'admin.my-phone-setting.index',
        component: CreateMyPhoneSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My Phone settings', 'routeName': 'admin.my-phone-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/my-email-settings',
        name: 'admin.my-email-setting.index',
        component: CreateMyEmailPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My Email settings', 'routeName': 'admin.my-email-setting.index', 'isCurrentRoute': 1}],
        },
    },

    {
        path: '/admin/pages/my-driver-license-settings',
        name: 'admin.my-driver-license-setting.index',
        component: CreateMyDriverLicenseSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My Email settings', 'routeName': 'admin.my-driver-license-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/my-student-card-settings',
        name: 'admin.my-student-card-setting.index',
        component: CreateMyStudentCardPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My Students Card settings', 'routeName': 'admin.my-student-card-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/edit-profile-page-settings',
        name: 'admin.edit-profile-page-setting.index',
        component: CreateEditProfilePage,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Edit Profile Page settings', 'routeName': 'admin.edit-profile-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
     {
        path: '/admin/pages/my-wallet-settings',
        name: 'admin.my-wallet-setting.index',
        component: CreateMyWalletSettingPage,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'My Wallet  settings', 'routeName': 'admin.my-wallet-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/payment-settings',
        name: 'admin.payment-setting.index',
        component: CreatePaymentOptionSettingPage,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Payment Option  settings', 'routeName': 'admin.payment-setting.index', 'isCurrentRoute': 1}],
        },
    },
     {
        path: '/admin/pages/payout-settings',
        name: 'admin.payout-setting.index',
        component: CreatePayoutOptionSettingPage,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Payout Option   setting', 'routeName': 'admin.payout-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/reviews-settings',
        name: 'admin.review-setting.index',
        component: CreateReviewSettingPage,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'my Review settings', 'routeName': 'admin.review-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/contact-proximaride-settings',
        name: 'admin.contact-proximaride-setting.index',
        component: CreateContactProximaRideSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'my Review settings', 'routeName': 'admin.contact-proximaride-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/close-account-settings',
        name: 'admin.close-account-setting.index',
        component: CreateCloseAccountSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'my Review setting', 'routeName': 'admin.close-account-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/billing-address-settings',
        name: 'admin.billing-address-setting.index',
        component: CreateBillingAddressSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'my Review settings', 'routeName': 'admin.billing-address-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/logout-settings',
        name: 'admin.logout-setting.index',
        component: CreateLogoutSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'my Review settings', 'routeName': 'admin.logout-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/forgot-password-settings',
        name: 'admin.forgot-password-setting.index',
        component: CreateForgotPasswordPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Forgot password page settings', 'routeName': 'admin.forgot-password-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/reset-password-settings',
        name: 'admin.reset-password-setting.index',
        component: CreateResetPasswordPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Reset password page settings', 'routeName': 'admin.reset-password-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/signup-page-settings',
        name: 'admin.signup-page-setting.index',
        component: CreateSignupPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Signup page setting', 'routeName': 'admin.signup-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/step1-page-settings',
        name: 'admin.step1-page-setting.index',
        component: CreateStep1PageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Step 1 of 5 page setting', 'routeName': 'admin.step1-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/step2-page-settings',
        name: 'admin.step2-page-setting.index',
        component: CreateStep2PageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Step 2 of 5 page setting', 'routeName': 'admin.step2-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/step3-page-settings',
        name: 'admin.step3-page-setting.index',
        component: CreateStep3PageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Step 3 of 5 page setting', 'routeName': 'admin.step3-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/step4-page-settings',
        name: 'admin.step4-page-setting.index',
        component: CreateStep4PageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Step 4 of 5 page setting', 'routeName': 'admin.step4-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/step5-page-settings',
        name: 'admin.step5-page-setting.index',
        component: CreateStep5PageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Step 5 of 5 page setting', 'routeName': 'admin.step5-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/contact-us-page-settings',
        name: 'admin.contact-us-page-setting.index',
        component: CreateContactUsPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Contact us page settings', 'routeName': 'admin.contact-us-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/terms-and-condition-page-settings',
        name: 'admin.terms-and-condition-page-setting.index',
        component: CreateTermsAndConditionPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Terms and condition page settings', 'routeName': 'admin.terms-and-condition-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/privacy-policy-page-settings',
        name: 'admin.privacy-policy-page-setting.index',
        component: CreatePrivacyPolicyPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Privacy policy page settings', 'routeName': 'admin.privacy-policy-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/terms-of-use-page-settings',
        name: 'admin.terms-of-use-page-setting.index',
        component: CreateTermsOfUsePageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Terms of use page settings', 'routeName': 'admin.terms-of-use-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/refund-policy-page-settings',
        name: 'admin.refund-policy-page-setting.index',
        component: CreateRefundPolicyPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Refund policy page settings', 'routeName': 'admin.refund-policy-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/cancellation-page-settings',
        name: 'admin.cancellation-page-setting.index',
        component: CreateCancellationPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Cancellation page settings', 'routeName': 'admin.cancellation-page-setting.index', 'isCurrentRoute': 1}],
        },
    }, {
        path: '/admin/pages/firm-cancellation-page-settings',
        name: 'admin.firm-cancellation-page-setting.index',
        component: CreateFirmCancellationPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Firm cancellation page settings', 'routeName': 'admin.firm-cancellation-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/dispute-page-settings',
        name: 'admin.dispute-page-setting.index',
        component: CreateDisputePageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Dispute page settings', 'routeName': 'admin.dispute-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/chats-page-settings',
        name: 'admin.chats-page-setting.index',
        component: CreateChatsPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Chats page settings', 'routeName': 'admin.chats-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/location-page-settings',
        name: 'admin.location-page-setting.index',
        component: CreateSelectLocationPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Select location page settings', 'routeName': 'admin.location-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/coffee-wall-page-settings',
        name: 'admin.coffee-wall-page-setting.index',
        component: CreateCoffeeWallPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Select coffee on wall page settings', 'routeName': 'admin.coffee-wall-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/my-trips-page-settings',
        name: 'admin.trips-page-setting.index',
        component: CreateTripsPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Trips page settings', 'routeName': 'admin.trips-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/ride-features-settings',
        name: 'admin.features-setting.index',
        component: CreateFeaturesSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Features settings', 'routeName': 'admin.features-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/ride-preferences-settings ',
        name: 'admin.preferences-setting.index',
        component: CreatePreferencesSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Preferences settings', 'routeName': 'admin.preferences-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/payment-methods-settings',
        name: 'admin.payment-methods-setting.index',
        component: CreatePaymentMethodsSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Payment methods settings', 'routeName': 'admin.payment-methods-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/luggage-options-settings',
        name: 'admin.luggage-options-setting.index',
        component: CreateLuggageOptionsSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Luggage options settings', 'routeName': 'admin.luggage-options-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/pages/referral-page-settings',
        name: 'admin.referral-page-setting.index',
        component: CreateReferralPageSetting,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Referral page setting', 'routeName': 'admin.referral-page-setting.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/profile',
        name: 'admin.profile.edit',
        component: Profile,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Profile', 'routeName': 'admin.profile.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/users',
        name: 'admin.users.index',
        component: Users,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All members', 'routeName': 'admin.users.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/user/:id',
        name: 'admin.user.index',
        component: User,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Users', 'routeName': 'admin.users.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/all-drivers',
        name: 'admin.drivers-verification.index',
        component: DriverVerification,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Drivers', 'routeName': 'admin.drivers-verification.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/driver/:id',
        name: 'admin.driver.index',
        component: Driver,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Drivers', 'routeName': 'admin.drivers-verification.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/all-students',
        name: 'admin.students-verification.index',
        component: StudentVerification,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Students', 'routeName': 'admin.students-verification.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/student/:id',
        name: 'admin.student.index',
        component: Student,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Students', 'routeName': 'admin.students-verification.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/passengers',
        name: 'admin.passengers.index',
        component: Passengers,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All passengers', 'routeName': 'admin.passengers.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/passenger/:id',
        name: 'admin.passenger.index',
        component: Passenger,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Passengers', 'routeName': 'admin.passengers.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/rides',
        name: 'admin.rides.index',
        component: Rides,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Manage rides', 'routeName': 'admin.rides.index', 'isCurrentRoute': 1}],
        },
    },
     {
        path: '/admin/past-rides',
        name: 'admin.rides.past',
        component: Rides,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Manage rides', 'routeName': 'admin.rides.index', 'isCurrentRoute': 1}],
        },
    },
      {
        path: '/admin/upcoming-rides',
        name: 'admin.rides.upcoming',
        component: Rides,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Manage rides', 'routeName': 'admin.rides.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/rides/no-show',
        name: 'admin.no_show.index',
        component: NoShow,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'No show history', 'routeName': 'admin.no_show.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/ride/:id',
        name: 'admin.ride.index',
        component: Ride,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Rides', 'routeName': 'admin.rides.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/bookings',
        name: 'admin.bookings.index',
        component: Bookings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All bookings', 'routeName': 'admin.bookings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/secured-cash-bookings',
        name: 'admin.secured-cash-bookings.index',
        component: SecuredCashBookings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All bookings', 'routeName': 'admin.secured-cash-bookings.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/reviews',
        name: 'admin.reviews.index',
        component: Reviews,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All reviews', 'routeName': 'admin.reviews.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/rating/:id',
        name: 'admin.review.index',
        component: Review,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All Reviews', 'routeName': 'admin.reviews.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/transactions',
        name: 'admin.transactions.index',
        component: Transactions,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'All transactions', 'routeName': 'admin.transactions.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/claim-reward-requests',
        name: 'admin.claim-reward-requests.index',
        component: ClaimRewards,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Claim reward requests', 'routeName': 'admin.claim-reward-requests.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/verify-phone-numbers',
        name: 'admin.verify-phone-numbers.index',
        component: VerifyPhones,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Verify phone numbers', 'routeName': 'admin.verify-phone-numbers.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/verify-banks',
        name: 'admin.verify-banks.index',
        component: VerifyBanks,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Verify banks', 'routeName': 'admin.verify-banks.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/withdrawal-requests',
        name: 'admin.withdrawal-requests.index',
        component: WithdrawalRequests,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Withdrawal requests', 'routeName': 'admin.withdrawal-requests.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/coffee-wallet',
        name: 'admin.coffee-wallet.index',
        component: CoffeeWallets,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Withdrawal requests', 'routeName': 'admin.withdrawal-requests.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/booking-credits',
        name: 'admin.booking-credits.index',
        component: BookingCredits,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Booking credits', 'routeName': 'admin.booking-credits.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/booking-credits/create',
        name: 'admin.booking-credits.create',
        component: CreateCredits,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Booking credits', 'routeName': 'admin.booking-credits.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.booking-credits.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/news',
        name: 'admin.news.index',
        component: News,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Manage news', 'routeName': 'admin.news.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/news/create',
        name: 'admin.news.create',
        component: CreateNews,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Manage news', 'routeName': 'admin.news.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.news.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/news/edit/:id?',
        name: 'admin.news.edit',
        component: CreateNews,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Manage news', 'routeName': 'admin.news.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.news.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/videos',
        name: 'admin.videos.index',
        component: Videos,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Videos', 'routeName': 'admin.videos.index', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/videos/create',
        name: 'admin.videos.create',
        component: CreateVideos,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Videos', 'routeName': 'admin.videos.index', 'isCurrentRoute': 0}, {'name': 'Create', 'routeName': 'admin.videos.create', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/videos/edit/:id?',
        name: 'admin.videos.edit',
        component: CreateVideos,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Videos', 'routeName': 'admin.videos.index', 'isCurrentRoute': 0}, {'name': 'Edit', 'routeName': 'admin.videos.edit', 'isCurrentRoute': 1}],
        },
    },
    {
        path: '/admin/site-settings/:id/edit',
        name: 'admin.site-settings.index',
        component: SiteSettings,
        meta: {
            breadcrumbs: [{'name': 'Dashboard', 'routeName': 'admin.dashboard', 'isCurrentRoute': 0}, {'name': 'Site settings', 'routeName': 'admin.site-settings.index', 'isCurrentRoute': 1}],
        },
    },
]

export default createRouter({
    history: createWebHistory(),
    routes
})
