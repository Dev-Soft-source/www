import ErrorHandling from "../../ErrorHandling";

const no_shows = {
    namespaced: true,
    state: {
        error: null,
        validationErros: new ErrorHandling(),
        no_shows: null,
        no_show_count: null,
        cancellation_count: null,
        ride: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        type: '',
        param: "withFlagIcon=1",
    },
    mutations: {
        setNoShows(state, payload) {
            state.no_shows = payload;
        },
        setNoShowCount(state, payload) {
            state.no_show_count = payload.no_show_count;
        },
        setCancellationCount(state, payload) {
            state.cancellation_count = payload.cancellation_count;
        },
        setRide(state, payload) {
            state.ride = payload;
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
        setS(state, payload) {
            state.type = payload;
            state.param = helper.updateUrlParameter(state.param, "type", payload);
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
        removeRideById(state, rideId) {
            state.no_shows = state.no_shows.filter(ride => ride.id !== rideId);
        },
        updateRideById(state, { id, suspand }) {
            const rideToUpdate = state.no_shows.find(ride => ride.id === id);
            if (rideToUpdate) {
              rideToUpdate.suspand = suspand;
            }
        },
        updateNoShowStatusById(state, { id, status }) {
            const rideToUpdate = state.no_shows.find(ride => ride.id === id);
            if (rideToUpdate) {
              rideToUpdate.status = status;
            }
        },
        setEmptyError(state) {
          state.validationErros = new ErrorHandling();
        },
        setValidationErros(state, payload) {
          state.validationErros.record(payload);
        },
    },
    actions: {
        fetchNoShows({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}no-shows?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setNoShows", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        noShowsCount({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}no-shows-count/${payload.id}?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setNoShowCount", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        cancellationCount({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}cancellation-count/${payload.id}?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setCancellationCount", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        fetchRide({ commit, state }, payload) {
          let url = payload && payload.url
                  ? payload.url
                  : `${process.env.MIX_ADMIN_API_URL}ride/${payload.id}`;
          commit("setLoading");
          return new Promise(function (resolve, reject) {
              axios.get(url)
                  .then((res) => {
                      if (res.data.status == "Success") {
                          commit("setRide", res.data.data);
                          resolve(res);
                      }
                  })
                  .catch((error) => {
                      reject(error);
                  })
                  .finally(() => commit("setLoading"));
          });
        },
        async cancelRide({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}cancel-ride/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the user from the state only if the query parameter is 2
                if (payload.query === '2') {
                  commit("updateNoShowStatusById", { id: payload.id, status: 2 });
                }
                // Update the user from the state only if the query parameter is 1
                if (payload.query === '1') {
                  commit("removeRideById", payload.id);
                }

                // Display a success message
                helper.swalSuccessMessage(res.data.message);
              } else if (res && res.data && res.data.status === "Error") {
                // Display an error message
                helper.swalErrorMessage("The ride has aldeady cancelled.");
              }
            } catch (error) {
              console.error(error);
            } finally {
              commit("setLoading");
            }
        },
        async removeRide({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}remove-ride/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the no_shows from the state
                commit("removeRideById", payload.id);

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
        async suspandRide({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}suspand-ride/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the user from the state only if the query parameter is 2
                if (payload.query === '2') {
                  commit("updateRideById", { id: payload.id, suspand: 1 });
                }
                // Update the user from the state only if the query parameter is 1
                if (payload.query === '1') {
                  commit("removeRideById", payload.id);
                }

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
        async unSuspandRide({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}unsuspand-ride/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the user from the state
                commit("updateRideById", { id: payload.id, suspand: 0 });
                // Update the user from the state only if the query parameter is 2
                if (payload.query === '2') {
                  commit("updateRideById", { id: payload.id, suspand: 0 });
                }
                // Update the user from the state only if the query parameter is 1
                if (payload.query === '1') {
                  commit("removeRideById", payload.id);
                }

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
        async rejectWithdrawal({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}update-status/${payload.id}`;
              const restriction = payload.restriction;
              const type = payload.type;
              const res = await axios.put(url, { restriction, type });

              if (res && res.data && res.data.status === "Success") {
                commit("setEmptyError");
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
        
        
        async undoRejectWithdrawal({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}undo-update-status/${payload.id}`;
              const restriction = payload.restriction;
              const type = payload.type;
              const res = await axios.put(url, { restriction, type });

              if (res && res.data && res.data.status === "Success") {
                commit("setEmptyError");
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

export default no_shows;
