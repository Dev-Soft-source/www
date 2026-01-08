import ErrorHandling from "../../ErrorHandling";

const videos = {
    namespaced: true,
    state: {
        validationErros: new ErrorHandling(),
        error: null,
        form: {
            id: null,
            link: {},
            page: "",
        },
        videos: null,
        loading: false,
        sortBy: "id",
        sortType: "desc",
        searchParam: null,
        pagination: {},
        limit: 10,
        param: "withFlagIcon=1",
        isFormEdit: false,
    },
    mutations: {
        setState(state, payload) {
            state.form[payload.key] = payload.value;
        },
        updateState(state, payload) {
            state.form[payload.key][`${payload.key}_${payload.id}`] =
                payload.value;
        },
        setIsFormEdit(state, payload) {
            state.isFormEdit = payload;
        },
        setVideos(state, payload) {
            state.videos = payload;
        },
        setPagination(state, pagination) {
            if (pagination.meta) {
                state.pagination = {
                    current_page: pagination.meta.current_page,
                    last_page: pagination.meta.last_page,
                    next_page_url: pagination.links
                        ? pagination.links.next
                        : null,
                    prev_page_url: pagination.links
                        ? pagination.links.prev
                        : null,
                        links: pagination.meta.links || [],
                };
            }
        },
        setSearchParam(state, payload) {
            state.searchParam = payload;
            state.param = helper.updateUrlParameter(
                state.param,
                "searchParam",
                payload
            );
        },
        setLimit(state, payload) {
            state.limit = payload;
            state.param = helper.updateUrlParameter(
                state.param,
                "limit",
                payload
            );
        },
        setSortBy(state, payload) {
            state.sortBy = payload;
            state.param = helper.updateUrlParameter(
                state.param,
                "sortBy",
                payload
            );
        },
        setSortType(state, payload) {
            state.sortType = payload;
            state.param = helper.updateUrlParameter(
                state.param,
                "sortType",
                payload
            );
        },
        setLoading(state, payload) {
            state.loading = payload ? payload : !state.loading;
        },
        resetForm(state) {
            state.form = {
                id: null,
                link: {},
                page: "",
            };
            state.validationErros = new ErrorHandling();
            state.error = null;
            state.isFormEdit = false;
        },
        setUrl(state, payload) {
            state.form.url = payload;
        },
        setForm(state, payload) {
            console.log(payload);
            state.form.id = payload.id;
            state.form.image = payload.image;
        },
        setValidationErros(state, payload) {
            state.validationErros.record(payload);
        },
        setEmptyError(state) {
            state.validationErros = new ErrorHandling();
        },
        setError(state, payload) {
            state.error = payload;
        },
    },
    actions: {
        addUpdateForm({ commit, state }) {
            let method = state.isFormEdit ? "put" : "post";
            let url = state.isFormEdit
                ? `${process.env.MIX_ADMIN_API_URL}videos/${state.form.id}`
                : `${process.env.MIX_ADMIN_API_URL}videos`;
            commit("setLoading");
            console.log(state.form);
            return new Promise(function (resolve, reject) {
                axios[method](url, state.form)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            helper.swalSuccessMessage(res.data.message);
                            commit("resetForm");
                            resolve(res);
                        } else {
                            helper.swalErrorMessage(res.data.message);
                        }
                    })
                    .catch((error) => {
                        commit("setEmptyError");
                        if (error.response && error.response.status == 422) {
                            commit(
                                "setValidationErros",
                                error.response.data.errors
                            );
                        } else if (
                            error.response &&
                            error.response.data &&
                            error.response.data.status == "Error"
                        ) {
                            helper.swalErrorMessage(
                                error.response.data.message
                            );
                        }
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        fetchVideos({ commit, state }, payload) {
            let url =
                payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}videos?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios
                    .get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            commit("setPagination", res.data);
                            commit("setVideos", res.data.data);
                            resolve(res);
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    })
                    .finally(() => commit("setLoading"));
            });
        },
        fetchVideo({ commit, state }, payload) {
            let url =
                payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}videos/${payload.id}?q=1`;
            url = `${url}&${state.param}`;
            commit("setLoading");
            return new Promise(function (resolve, reject) {
                axios
                    .get(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
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
        deleteVideo({ commit }, payload) {
            commit("setLoading");
            let url =
                payload && payload.url
                    ? payload.url
                    : `${process.env.MIX_ADMIN_API_URL}videos/${payload.id}`;
            return new Promise(function (resolve, reject) {
                axios
                    .delete(url)
                    .then((res) => {
                        if (res.data.status == "Success") {
                            helper.swalSuccessMessage(res.data.message);
                            resolve(res);
                        } else if (res.data.status == "Error") {
                            helper.swalErrorMessage(res.data.message);
                            resolve(res);
                        }
                    })
                    .finally(() => commit("setLoading"));
            });
        },
    },
};

export default videos;
