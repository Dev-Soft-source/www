const bookings = {
    namespaced: true,
    state: {
        error: null,
        bookings: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        param: "withFlagIcon=1",
    },
    mutations: {
        setBookings(state, payload) {
            state.bookings = payload;
        },
        setPagination(state, pagination) {
            if (pagination.meta) {
                console.log(pagination.meta);
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
    },
    actions: {
        fetchBookings({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}bookings?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setBookings", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        fetchSecuredCashBookings({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}secured-cash-bookings?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setBookings", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        async rejectWithdrawal({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}secured-cash-code/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Display a success message
                helper.swalSuccessMessage(res.data.message);
              } else if (res && res.data && res.data.status === "Error") {
                // Display an error message
                helper.swalErrorMessage(res.data.message);
              }
            } catch (error) {
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
                console.error(error);
            } finally {
              commit("setLoading");
            }
        },
    },
};

export default bookings;
