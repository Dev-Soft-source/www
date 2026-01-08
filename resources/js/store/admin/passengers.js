const passengers = {
    namespaced: true,
    state: {
        error: null,
        passengers: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        param: "withFlagIcon=1",
    },
    mutations: {
        setPassengers(state, payload) {
            state.passengers = payload;
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
        removePassengerById(state, passengerId) {
            state.passengers = state.passengers.filter(passenger => passenger.id !== passengerId);
        },
        updateUserStatus(state, { id, suspand }) {
            const userToUpdate = state.passengers.find(passenger => passenger.id === id);
            if (userToUpdate) {
              userToUpdate.suspand = suspand;
            }
        },
    },
    actions: {
        fetchPassengers({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}passengers?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setPassengers", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        async suspandUser({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}suspand-user/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the user from the state only if the query parameter is 2
                commit("updateUserStatus", { id: payload.id, suspand: 1 });

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
        async unSuspandUser({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}unsuspand-user/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the user from the state only if the query parameter is 2
                commit("updateUserStatus", { id: payload.id, suspand: 0 });

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
        async deletePassenger({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}users/${payload.id}`;
              const res = await axios.delete(url);

              if (res && res.data && res.data.status === "Success") {
                // Remove the deleted passenger from the state
                commit("removePassengerById", payload.id);

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

export default passengers;
