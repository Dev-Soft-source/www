const ratings = {
    namespaced: true,
    state: {
        error: null,
        ratings: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        param: "withFlagIcon=1",
    },
    mutations: {
        setRatings(state, payload) {
            state.ratings = payload;
        },
        setRating(state, payload) {
            state.rating = payload;
        },
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
        removeRatingById(state, { status, ratingId }) {
            const ratingToUpdate = state.ratings.find(rating => rating.id === ratingId);
            if (ratingToUpdate) {
                ratingToUpdate.status = status;
            }
        },
    },
    actions: {
        fetchRatings({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}ratings?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setRatings", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        fetchRating({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}rating/${payload.id}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setRating", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        updateDisplyCheckbox({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}rating/update-display-check`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.post(url, payload)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        async unSuspendRating({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}ratings/unsuspend/${payload.id}`;
              const res = await axios.post(url);

              if (res && res.data && res.data.status === "Success") {
                // Remove the deleted user from the state
                commit("removeRatingById", { status: 1, ratingId: payload.id });

                // Display a success message
                helper.swalSuccessMessage(res.data.message);
              } else if (res && res.data && res.data.status === "Error") {
                // Display an error message
                helper.swalErrorMessage(res.data.message);
              }
            } catch (error) {
              console.error(error);
            } finally {
              commit("setLoading");
            }
        },
        async deleteRating({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}ratings/delete/${payload.id}`;
              const res = await axios.post(url);

              if (res && res.data && res.data.status === "Success") {
                // Remove the deleted user from the state
                commit("removeRatingById", { status: '2', ratingId: payload.id });

                // Display a success message
                helper.swalSuccessMessage(res.data.message);
              } else if (res && res.data && res.data.status === "Error") {
                // Display an error message
                helper.swalErrorMessage(res.data.message);
              }
            } catch (error) {
              console.error(error);
            } finally {
              commit("setLoading");
            }
        },
    },
};

export default ratings;
