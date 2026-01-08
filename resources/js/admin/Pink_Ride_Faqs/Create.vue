<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <header class="py-4">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h3 class="can-exp-h2 text-primary">
                            {{ isFormEdit ? "Edit" : "Create" }} pink-ride FAQ
                        </h3>
                        <router-link
                            :to="{ name: 'admin.pink-ride-faqs.index' }"
                            class="button-exp-fill"
                        >
                            Back
                        </router-link>
                    </div>
                </div>
            </header>
            <form class="py-4 px-4" @submit.prevent="addUpdateForm()">

                <div
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
                                        `question.question_${language.id}`
                                    ) || validationErros.has(
                                        `answer.answer_${language.id}`
                                    )
                                        ? 'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600'
                                        : '',
                                ]"
                                >{{ language.name }}</a
                            >
                        </li>
                    </ul>
                </div>

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
                        <label for="question" class="">Question</label>
                        <input
                            type="text"
                            name="question"
                            id="question"
                            class="can-exp-input w-full block border border-gray-300 rounded"
                            placeholder=" "
                            @input="
                                handleMultipleInput(
                                    'question',
                                    $event.target.value,
                                    language
                                )
                            "
                            :value="
                                form['question'] && form['question'][`question_${language.id}`]
                                    ? form['question'][`question_${language.id}`]
                                    : ''
                            "
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`question.question_${language.id}`)"
                            v-text="validationErros.get(`question.question_${language.id}`)"
                        ></p>
                    </div>
                    <div v-if="isDataLoaded" class="relative z-0 w-full group col-span-2">
                        <label for="answer" class="">Answer</label>
                        <editor
                            @selectionChange="
                                handleSelectionChange(
                                    'answer',
                                    language
                                )
                            "
                            :ref="`answer_${language.id}`"
                            :id="`answer_${language.id}`"
                            :initial-value="
                                form['answer'] && form['answer'][`answer_${language.id}`]
                                    ? form['answer'][`answer_${language.id}`]
                                    : ''
                            "
                            :tinymce-script-src="tinymceScriptSrc"
                            :init="editorConfig"
                        />
                        <p
                            class="mt-2 text-sm text-red-400"
                            v-if="validationErros.has(`answer.answer_${language.id}`)"
                            v-text="validationErros.get(`answer.answer_${language.id}`)"
                        ></p>
                    </div>
                </div>

                <button type="submit" class="button-exp-fill" :disabled="loading">Submit</button>
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
            loading: (state) => state.pinkRideFaqs.loading,
            form: (state) => state.pinkRideFaqs.form,
            isFormEdit: (state) => state.pinkRideFaqs.isFormEdit,
            validationErros: (state) => state.pinkRideFaqs.validationErros,
            languages: (state) => state.languages.languages,
        }),
        is_default: {
            get: function () {
                return this.$store.state.languages.form.is_default;
            },
            set: function (val) {
                this.$store.commit("pinkRideFaqs/setForm", {
                    is_default: val,
                });
            },
        },
    },
    data() {
        return {
            activeTab: null,
            isDataLoaded: false,
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
        handleSelectionChange(key, language) {
            this.handleMultipleInput(
                key,
                tinymce.get(`${key}_${language.id}`).getContent(),
                language
            );
        },
        handleInput(value, key) {
            this.$store.commit("pinkRideFaqs/setState", { key, value });
        },
        handleMultipleInput(key, value, language) {
            this.$store.commit("pinkRideFaqs/updateState", {
                value: value,
                id: language.id,
                key,
            });
        },
        addUpdateForm() {
            this.$store
                .dispatch("pinkRideFaqs/addUpdateForm")
                .then(() =>
                    this.$router.push({ name: "admin.pink-ride-faqs.index" })
                );
        },
        changeLanguageTab(language) {
            this.activeTab = language.id;
        },
        fetchPinkRideFaq() {
            if (this.$route.params.id) {
                let id = this.$route.params.id;

                this.$store.commit("pinkRideFaqs/setIsFormEdit", 1);
                this.$store
                    .dispatch("pinkRideFaqs/fetchPinkRideFaq", {
                        id: id,
                        url: `${process.env.MIX_ADMIN_API_URL}pink-ride-faqs/${id}?withPinkRideFaqDetail=1`,
                    })
                    .then((res) => {
                        let keys = [
                            "page",
                        ];
                        this.$store.commit("pinkRideFaqs/setState", {
                            key: "id",
                            value: id,
                        });
                        for (var i = 0; i < keys.length; i++) {
                            this.$store.commit("pinkRideFaqs/setState", {
                                key: keys[i],
                                value: res.data.data[keys[i]],
                            });
                        }
                        let data =
                            res.data.data && res.data.data.pink_ride_faq_detail
                                ? res.data.data.pink_ride_faq_detail
                                : [];
                        let obj = {};

                        data.map((res) => {
                            obj["question_" + res.language_id] = res.question;
                        });
                        this.$store.commit("pinkRideFaqs/setState", {
                            key: "question",
                            value: obj,
                        });

                        let obj1 = {};

                        data.map((res) => {
                            obj1["answer_" + res.language_id] = res.answer;
                        });
                        this.$store.commit("pinkRideFaqs/setState", {
                            key: "answer",
                            value: obj1,
                        });
                        this.isDataLoaded = 1;
                    });
            }
        },
    },
    created() {
        this.$store.commit("pinkRideFaqs/resetForm");
        this.$store
            .dispatch("languages/fetchLanguages", {
                url: `${process.env.MIX_ADMIN_API_URL}languages?getAll=1`,
            })
            .then((res) => {
                let data = res.data.data;
                let obj = {};
                data.map((res) => {
                    obj["question_" + res.id] = "";
                });
                this.$store.commit("pinkRideFaqs/setState", {
                    key: "question",
                    value: obj,
                });

                let obj1 = {};
                data.map((res) => {
                    obj1["answer_" + res.id] = "";
                });
                this.$store.commit("pinkRideFaqs/setState", {
                    key: "answer",
                    value: obj1,
                });
                if (this.$route.params.id) {
                    this.fetchPinkRideFaq();
                } else {
                    this.isDataLoaded = 1;
                }
            });
    },
};
</script>
