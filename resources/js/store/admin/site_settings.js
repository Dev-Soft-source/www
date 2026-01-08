import ErrorHandling from "../../ErrorHandling";

const settings = {
    namespaced: true,
    state: {
        validationErros: new ErrorHandling(),
        error: null,
        form: {
            id: null,
            frim_discount: null,
            ride_completed_hours: null,
            destination_hours: null,
            booking_price: null,
            booking_per: null,
            gas_cost: null,
            secured_cash_attempt: null,
            keywords: null,
            description: null,
            facebook: null,
            instagram: null,
            youtube: null,
            twitter: null,
            price_per_km: null,
            tax_type: null,
            deduct_tax: null,
            top_menu_notification:null,
        top_menu_search:null,
        top_menu_add:null,
        profile_setting_profile_photo:null,
        profile_setting_my_vehicle:null,
        profile_setting_password:null,
        profile_setting_my_phone_number:null,
        profile_setting_my_email_address:null,
        profile_setting_my_drivers_license:null,
        profile_setting_my_student_card:null,
        profile_setting_referrals:null,
        menu_icon_profile_setting:null,
        menu_icon_my_wallet:null,
        menu_icon_payment_option:null,
        menu_icon_my_reviews:null,
        menu_icon_terms_condition:null,
        menu_icon_privacy_policy:null,
        menu_icon_term_of_use:null,
        menu_icon_cancellation_policy:null,
        menu_icon_dispute_policy:null,
        menu_icon_contact_proximaride:null,
        menu_icon_coffee_on_the_wall:null,
        menu_icon_log_out:null,
        meanu_icon_close_your_account:null,
            tax: null,
            booking_fee_give_to_driver: 0,
            booking_fee_give_to_student: 0
        },
        settings: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        param: "withFlagIcon=1",
    },
    mutations: {
        setPagination(state, pagination) {
            if (pagination.meta) {
                state.pagination = {
                    current_page: pagination.meta.current_page,
                    last_page: pagination.meta.last_page,
                    next_page_url: pagination.links ? pagination.links.next : null,
                    prev_page_url: pagination.links ? pagination.links.prev : null,
                    links: pagination.meta.links || [],
                };
            }
        },
        setSearchParam(state, payload) {
            state.searchParam = payload;
            state.param = helper.updateUrlParameter(state.param, "searchParam", payload);
        },
        setLimit(state, payload) {
            state.limit = payload;
            state.param = helper.updateUrlParameter(state.param, "limit", payload);
        },
        setSortBy(state, payload) {
            state.sortBy = payload;
            state.param = helper.updateUrlParameter(state.param, "sortBy", payload);
        },
        setSortType(state, payload) {
            state.sortType = payload;
            state.param = helper.updateUrlParameter(state.param, "sortType", payload);
        },
        setLoading(state, payload) {
            state.loading = payload ? payload : !state.loading;
        },
        setForm(state, payload) {
            Object.assign(state.form, payload);
        },
        setValidationErros(state, payload) {
            state.validationErros.record(payload);
        },
        setEmptyError(state) {
            state.validationErros = new ErrorHandling();
        },
    },
    actions: {
        addUpdateForm({ commit, state }) {
            let method = "put";
            let url = `${process.env.MIX_ADMIN_API_URL}site-settings/${state.form.id}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios[method](url, state.form)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            helper.swalSuccessMessage(res.data.message);
                            commit("setForm");
                            resolve(res);
                        } else {
                            helper.swalErrorMessage(res.data.message);
                        }
                    })
                    .catch((error) => {
                        commit("setEmptyError");
                        if (error.response && error.response.status == 422) {
                            commit("setValidationErros", error.response.data.errors);
                        } else if (
                            error.response &&
                            error.response.data &&
                            error.response.data.status == "Error"
                        ) {
                            helper.swalErrorMessage(error.response.data.message);
                        }
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        fetchSetting({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}site-settings/${payload.id}?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            console.log(res.data.data);
                            commit("setForm", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
    },
};

export default settings;
