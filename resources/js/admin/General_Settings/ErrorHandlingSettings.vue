<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="can-exp-h3 text-primary">Error messages</h3>
                    </div>
                </div>
                <form class="py-4 px-4 bg-white" @submit.prevent="addUpdateForm()">

                    <div class="w-full">
                        <div class="inline-flex w-full mb-6">
                            <nav class="isolate w-full" aria-label="Tabs">



                                <ul class="flex flex-wrap mb-4 gap-2">
                                    <li v-for="languageError in languageErrors" :key="languageError.id">
                                        <a @click.prevent="changeLanguageTab(languageError)" href="#"
                                        :class="[((activeTab == null && languageError.is_default == '1') || activeTab ==
                                            languageError.id ?
                                            'button-exp-no-fill' :
                                            'button-exp-no-fill'
                                            ), (validationErros.has(`name.name_${languageError.id}`) ?
                                            'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600' : '')]">
                                        <span>{{ languageError . name }}</span>
                                        <span aria-hidden="true" class="bg-primary absolute inset-x-0 bottom-0 h-0.5"
                                            v-if="(activeTab == null && languageError.is_default == '1') || activeTab == languageError.id"></span>
                                        <span aria-hidden="true"
                                            class="bg-transparent absolute inset-x-0 bottom-0 h-0.5" v-else></span>
                                        <span aria-hidden="true" class="bg-red-500 absolute inset-x-0 bottom-0 h-0.5"
                                            v-if="(validationErros.has(`name.name_${languageError.id}`))"></span>
                                    </a>
                                    </li>
                                </ul>
                                <hr class="">

                                <!-- <div v-for="languageError in languageErrors" :key="languageError.id">
                                    <a @click.prevent="changeLanguageTab(languageError)" href="#"
                                        :class="[((activeTab == null && languageError.is_default == '1') || activeTab ==
                                            languageError.id ?
                                            'button-exp-no-fill mr-2' :
                                            'button-exp-no-fill'
                                            ), (validationErros.has(`name.name_${languageError.id}`) ?
                                            'bg-red-600 border-red-600 text-white hover:text-white rounded hover:bg-red-600 hover:border-red-600' : '')]">
                                        <span>{{ languageError . name }}</span>
                                        <span aria-hidden="true" class="bg-primary absolute inset-x-0 bottom-0 h-0.5"
                                            v-if="(activeTab == null && languageError.is_default == '1') || activeTab == languageError.id"></span>
                                        <span aria-hidden="true"
                                            class="bg-transparent absolute inset-x-0 bottom-0 h-0.5" v-else></span>
                                        <span aria-hidden="true" class="bg-red-500 absolute inset-x-0 bottom-0 h-0.5"
                                            v-if="(validationErros.has(`name.name_${languageError.id}`))"></span>
                                    </a>
                                </div> -->
                            </nav>
                        </div>
                    </div>


                    <div class="grid my-3 md:grid-cols-2 md:gap-6" v-for="languageError in languageErrors"
                        :key="languageError.id"
                        :class="(activeTab == null && languageError.is_default == '1') || activeTab == languageError.id ?
                            'block' : 'hidden'">
                        <template v-for="(validation, index) in languageError.validation">
                            <div class="relative z-0 w-full group" v-for="(v, i) in validation" :key="`${i}v`"
                                v-if="typeof validation === 'object'">
                                <label :for="index" class="capitalize">{{ i }}</label>
                                <input type="text" name="name" :id="index"
                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                    placeholder=" " :value="v"
                                    @blur="updateError(languageError, i, $event.target.value)" />
                            </div>
                            <div class="relative z-0 w-full group" :key="index" v-else>
                                <label :for="index" class="capitalize">{{ index }}</label>
                                <input type="text" name="name" :id="index"
                                    class="can-exp-input w-full block border border-gray-300 rounded"
                                    placeholder=" " :value="validation"
                                    @blur="updateError(languageError, index, $event.target.value)" />
                            </div>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script>
    import { mapState } from 'vuex'
    export default {
        computed:{
            ...mapState({
                validationErros: state => state.errors.validationErros,
                languageErrors: state => state.errors.languageErrors,
            }),
        },
        data(){
            return {
                activeTab: null,
            }
        },
        methods: {
            addUpdateForm(){
                this.$store.dispatch('errors/addUpdateForm')
                .then(() => this.$router.go());
            },
            updateError(language, field, value){
                if(value == ''){
                    helper.swalErrorMessage(`The ${field} field is required.`);
                    return;
                }
                else if(!value.includes(':attribute') && !value.includes(':Attribute')){
                    helper.swalErrorMessage(`The ${field} must contains :attribute.`);
                    return;
                }
                this.$store.dispatch('errors/addUpdateForm',{
                    'language_id':language.id,
                    'field':field,
                    'value':value,
                });
            },
            changeLanguageTab(language){
                this.activeTab = language.id;
            },
            fetchLanguageErrors(){
                this.$store.dispatch('errors/fetchLanguageErrors');
            }
        },
        created(){
            this.$store.commit('errors/setEmptyError');
            this.$store.commit('errors/setError');
            this.fetchLanguageErrors();
        }
    };
</script>
