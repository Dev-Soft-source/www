<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            Create article
                        </h3>
                        <router-link
                            :to="{ name: 'admin.news.index' }"
                            class="button-exp-fill"
                        >
                            Back
                        </router-link>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()" enctype="multipart/form-data">
                <!-- <div
                    class="text-sm font-medium text-center text-gray-500 border-b border-gray-200"
                >
                    <ul class="flex gap-2 flex-wrap my-4">
                        <li
                            class="mr-2 mb-2"
                            v-for="language in languages"
                            :key="language.id"
                        >
                            <a
                                @click.prevent="changeLanguageTab(language)"
                                href="#"
                                :class="[
                                    'inline-block py-2 px-4 text-primary border border-primary rounded  font-FuturaMdCnBT text-base lg:text-lg font-medium hover:text-white hover:bg-primary active:text-white active:bg-primary',
                                    (activeTab == null && language.is_default) ||
                                    activeTab == language.id
                                        ? 'bg-primary  text-white'
                                        : '',
                                    validationErros.has(
                                        `title.title_${language.id}`
                                    ) || validationErros.has(
                                        'agency'
                                    ) || validationErros.has(
                                        'added_by'
                                    ) || validationErros.has(
                                        `description.description_${language.id}`
                                    )
                                        ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                        : '',
                                ]"
                                >{{ language.name }}</a
                            >
                        </li>
                    </ul>
                </div> -->

                <div
                    class="grid my-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2  lg:grid-cols-2 gap-6"
                    v-for="language in languages"
                    :key="language.id"
                    :class="
                        (activeTab == null && language.is_default) ||
                        activeTab == language.id
                            ? 'block'
                            : 'hidden'
                    "
                >
                    <div class="relative z-0 w-full group">
                        <label for="language" class="">Language</label>
                        <select
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            @change="updateLanguageName($event.target.value)"
                        >
                            <option value="">Select language...</option>
                            <option
                                v-for="language in languages"
                                :key="language.id"
                                :value="JSON.stringify(language)"
                                :selected="form.language === language.id"
                            >
                                {{ language.name }}
                            </option>
                        </select>
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`language`)"
                            v-text="validationErros.get(`language`)"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="agency" class="">News agency</label>
                        <select
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            @change="updateAgencyName($event.target.value)"
                        >
                            <option value="">Select agency...</option>
                            <option
                                v-for="agencyCode in agencyCodes"
                                :key="agencyCode.code"
                                :value="JSON.stringify(agencyCode)"
                                :selected="form.agency === agencyCode.name || (form.agency === 'Other' && agencyCode.name === 'Other')"
                            >
                                {{ agencyCode.name }}
                            </option>
                        </select>
                        <input v-if="isOtherSelected()"
                            type="text"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder="Add news agency"
                            @input="
                                updateCustomAgency($event.target.value)
                            "
                            :value="
                                form['agency'] != 'Other'
                                    ? form['agency']
                                    : ''
                            "
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`agency`)"
                            v-text="validationErros.get(`agency`)"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="title" class="">Title</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            @input="
                                handleInput(
                                    $event.target.value,
                                    'title'
                                )
                            "
                            :value="
                                form['title']
                                    ? form['title']
                                    : ''
                            "
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`title`)"
                            v-text="validationErros.get(`title`)"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group">
                        <label for="added_by" class="">By</label>
                        <input
                            type="text"
                            name="added_by"
                            id="added_by"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            @input="
                                handleInput(
                                    $event.target.value,
                                    'added_by'
                                )
                            "
                            :value="
                                form['added_by']
                                    ? form['added_by']
                                    : ''
                            "
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`added_by`)"
                            v-text="validationErros.get(`added_by`)"
                        ></p>
                    </div>
                    <div class="relative z-0 w-full group col-span-2">
                        <label for="description" class="">Description</label>
                        <editor
                            @init="onEditorInit"
                            @selectionChange="
                                handleSelectionChange(
                                    'description'
                                )
                            "
                            ref="description"
                            :id="'description'"
                            :initial-value="
                                form['description']
                                    ? form['description']
                                    : ''
                            "
                            :tinymce-script-src="tinymceScriptSrc"
                            :init="editorConfig"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`description`)"
                            v-text="validationErros.get(`description`)"
                        ></p>
                    </div>
                </div>
                <button
                    type="submit"
                    class="button-exp-fill"
                >
                    Submit
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import Editor from "@tinymce/tinymce-vue";
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState({
            form: (state) => state.articles.form,
            validationErros: (state) => state.articles.validationErros,
            agencyCodes: (state) => state.articles.agencyCodes,
            languages: (state) => state.languages.languages,
        }),
    },
    data() {
        return {
            activeTab: null,
            editorConfig: {
                height: 250,
                menubar: false,
                plugins:
                  "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen code",
                toolbar:
                  "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code | fullscreen",
                base_url: "/plugins/tinymce",
                suffix: ".min",
            },
            tinymceScriptSrc: "/plugins/tinymce/tinymce.min.js",
        };
    },
    components: {
        editor: Editor,
    },
    methods: {
        isOtherSelected() {
            return this.form.agency === 'Other' || (
                this.form.agency !== null &&
                this.form.agency !== '' &&
                !this.agencyCodes.some(agency => agency.name === this.form.agency)
            );
        },
        updateLanguageName(language) {
            language = JSON.parse(language);
            this.handleInput(language.id, "language");
        },
        updateAgencyName(agency) {
            agency = JSON.parse(agency);
            this.handleInput(agency.name, "agency");
        },
        updateCustomAgency(value) {
            this.handleInput(
                value,
                'agency'
            )
        },
        handleSelectionChange(key) {
            this.handleInput(
                tinymce.get(`${key}`).getContent(),
                key
            );
        },
        handleInput(value, key) {
            this.$store.commit("articles/setState", { key, value });
        },
        handleMultipleInput(key, value, language) {
            this.$store.commit("articles/updateState", {
                value: value,
                id: language.id,
                key,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("articles/addUpdateForm")
                .then(() => this.$router.push({ name: "admin.news.index" }));
        },
        changeLanguageTab(language) {
            this.activeTab = language.id;
        },
        async onEditorInit(event, editor) {
            await this.fetchArticles(); // Wait for fetchArticles to complete
            const description = this.form.description || '';
            console.log('hell');
            console.log(this.form);
            console.log(this.form.title);
            console.log(this.form.description);
            if (description) {
                editor.setContent(description);
            }
        },
        fetchArticles() {
            return new Promise((resolve, reject) => {
                if (this.$route.params.id) {
                    let id = this.$route.params.id;

                    this.$store.commit("articles/setIsFormEdit", 1);
                    this.$store
                        .dispatch("articles/fetchArticles", {
                            id: id,
                            url: `${process.env.MIX_ADMIN_API_URL}articles/${id}?withArticleDetail=1`,
                        })
                        .then((res) => {
                            let keys = ["agency", "added_by"];
                            this.$store.commit("articles/setState", {
                                key: "id",
                                value: id,
                            });
                            for (var i = 0; i < keys.length; i++) {
                                this.$store.commit("articles/setState", {
                                    key: keys[i],
                                    value: res.data.data[keys[i]],
                                });
                            }

                            if (res.data.data.image) {
                                this.convertImgUrlToBase64(
                                    res.data.data.image.full_path,
                                    `image/${res.data.data.image.extension}`
                                );
                            }

                            let data =
                                res.data.data && res.data.data.article_detail
                                    ? res.data.data.article_detail
                                    : [];

                            data.map((res) => {
                                this.$store.commit("articles/setState", {
                                    key: "title",
                                    value: res.title,
                                });
                                this.$store.commit("articles/setState", {
                                    key: "language",
                                    value: res.language_id,
                                });
                                this.$store.commit("articles/setState", {
                                    key: "description",
                                    value: res.description,
                                });
                                console.log(res.description);
                            });

                            resolve(); // Resolve the promise after the dispatch completes
                        })
                        .catch((error) => {
                            reject(error); // Reject if there's an error
                        });
                }
            });
        },
    },
    created() {
        this.$store.commit("articles/resetForm");
        this.$store
            .dispatch("languages/fetchLanguages", {
                url: `${process.env.MIX_ADMIN_API_URL}languages?getAll=1`,
            })
            .then(() => {
                this.fetchArticles();
            });
    },
};
</script>
