const rides = {
    namespaced: true,
    state: {
        error: null,
        rides: null,
        ride: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        s: '',
        param: "withFlagIcon=1",
    },
    mutations: {
        setRides(state, payload) {
            state.rides = payload;
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
            state.s = payload;
            state.param = helper.updateUrlParameter(state.param, "s", payload);
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
            state.rides = state.rides.filter(ride => ride.id !== rideId);
        },
        updateRideById(state, { id, suspand }) {
            const rideToUpdate = state.rides.find(ride => ride.id === id);
            if (rideToUpdate) {
              rideToUpdate.suspand = suspand;
            }
        },
        updateRideStatusById(state, { id, status }) {
            const rideToUpdate = state.rides.find(ride => ride.id === id);
            if (rideToUpdate) {
              rideToUpdate.status = status;
            }
        },
    },
    actions: {
        fetchRides({ commit, state }, payload) {
          commit("setRides", null);

            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}rides?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setRides", res.data.data);
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
                  commit("updateRideStatusById", { id: payload.id, status: 2 });
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
                // Update the rides from the state
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
                  commit("updateRideById", { id: payload.id, suspand: 1 });
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
                  commit("updateRideById",{ id: payload.id, suspand: 0 });
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
    },
};

export default rides;
