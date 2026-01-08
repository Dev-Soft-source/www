import ErrorHandling from "../../ErrorHandling";

const claim_rewards = {
    namespaced: true,
    state: {
        validationErros: new ErrorHandling(),
        error: null,
        claim_rewards: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        param: "withFlagIcon=1",
    },
    mutations: {
        setClaimRewards(state, payload) {
            state.claim_rewards = payload;
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
        updateWithdrawalById(state, { id, status }) {
            const withdrawalToUpdate = state.claim_rewards.find(withdrawal => withdrawal.id === id);
            if (withdrawalToUpdate) {
              withdrawalToUpdate.status = status;
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
        fetchClaimRewards({ commit, state }, payload) {
            let url = payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}claim-rewards?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios.get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setClaimRewards", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        async acceptWithdrawal({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}accept-withdrawal-request/${payload.id}`;
              const res = await axios.put(url);

              if (res && res.data && res.data.status === "Success") {
                // Update the user from the state
                commit("updateWithdrawalById", { id: payload.id, status: 1 });

                // Display a success message
                helper.swalSuccessMessage(res.data.message);
              } else if (res && res.data && res.data.status === "Error") {
                // Display an error message
                helper.swalErrorMessage('The request has already been rejected.');
              }
            } catch (error) {
              console.error(error);
            } finally {
              commit("setLoading");
            }
        },
        async approveReward({ commit }, payload) {
            commit("setLoading");

            try {
              const url = payload && payload.url
                ? payload.url
                : `${process.env.MIX_ADMIN_API_URL}claim-reward-approve/${payload.id}`;
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

export default claim_rewards;
