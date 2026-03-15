<template>
    <AppLayout>
        <div class="relative shadow-md sm:rounded-lg bg-white py-4">
            <div class="px-4">
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


                    <div v-for="languageError in languageErrors"
                        :key="languageError.id"
                        :class="(activeTab == null && languageError.is_default == '1') || activeTab == languageError.id ?
                            'block' : 'hidden'">
                        
                        <!-- Main Errors Section -->
                        <div
                            class="border rounded w-full my-3"
                            :class="collapseStates[0] ? 'bg-gray-50' : ''"
                        >
                            <div
                                class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                @click.prevent="collapseStates[0] = !collapseStates[0]"
                            >
                                <h3 class="text-white">
                                    Main errors
                                </h3>
                                <svg
                                    class="w-5 h-5 fill-current text-white transform transition-transform"
                                    :class="collapseStates[0] ? 'rotate-180' : ''"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M6 9l4 4 4-4"></path>
                                </svg>
                            </div>

                            <div
                                class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                v-if="collapseStates[0]"
                            >
                                <template v-for="(validation, index) in languageError.validation" :key="index">
                                    <div class="relative z-0 w-full group" v-for="(v, i) in validation" :key="`${i}v`"
                                        v-if="typeof validation === 'object'">
                                        <label :for="`main_${index}_${i}`" class="capitalize">{{ index }} - {{ i }}</label>
                                        <input type="text" name="name" :id="`main_${index}_${i}`"
                                            class="can-exp-input w-full block border border-gray-300 rounded"
                                            placeholder=" " :value="v"
                                            @blur="updateError(languageError, i, $event.target.value, index)" />
                                    </div>
                                    <div class="relative z-0 w-full group" :key="index" v-else>
                                        <label :for="`main_${index}`" class="capitalize">{{ index }}</label>
                                        <input type="text" name="name" :id="`main_${index}`"
                                            class="can-exp-input w-full block border border-gray-300 rounded"
                                            placeholder=" " :value="validation"
                                            @blur="updateError(languageError, index, $event.target.value)" />
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Custom Errors Section -->
                        <div
                            v-if="languageError.custom_validation && Object.keys(languageError.custom_validation).length > 0"
                            class="border rounded w-full my-3"
                            :class="collapseStates[1] ? 'bg-gray-50' : ''"
                        >
                            <div
                                class="flex justify-between bg-primary text-white p-4 cursor-pointer"
                                @click.prevent="collapseStates[1] = !collapseStates[1]"
                            >
                                <h3 class="text-white">
                                    Custom errors
                                </h3>
                                <svg
                                    class="w-5 h-5 fill-current text-white transform transition-transform"
                                    :class="collapseStates[1] ? 'rotate-180' : ''"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M6 9l4 4 4-4"></path>
                                </svg>
                            </div>

                            <div
                                class="p-4 bg-gray-100 border-t grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                                v-if="collapseStates[1]"
                            >
                                <template v-for="(customField, fieldName) in languageError.custom_validation" :key="fieldName">
                                    <template v-if="typeof customField === 'object' && customField !== null && !Array.isArray(customField)">
                                        <div class="relative z-0 w-full group" v-for="(ruleValue, ruleName) in customField" :key="`${fieldName}_${ruleName}`">
                                            <label :for="`custom_${fieldName}_${ruleName}`" class="capitalize">{{ fieldName }} - {{ ruleName }}</label>
                                            <input type="text" name="name" :id="`custom_${fieldName}_${ruleName}`"
                                                class="can-exp-input w-full block border border-gray-300 rounded"
                                                placeholder=" " :value="ruleValue"
                                                @blur="updateError(languageError, `${fieldName}.${ruleName}`, $event.target.value, null, true)" />
                                        </div>
                                    </template>
                                    <div class="relative z-0 w-full group" v-else :key="fieldName">
                                        <label :for="`custom_${fieldName}`" class="capitalize">{{ fieldName }}</label>
                                        <input type="text" name="name" :id="`custom_${fieldName}`"
                                            class="can-exp-input w-full block border border-gray-300 rounded"
                                            placeholder=" " :value="customField"
                                            @blur="updateError(languageError, fieldName, $event.target.value, null, true)" />
                                    </div>
                                </template>
                            </div>
                        </div>
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
                collapseStates: [true, true], // [main errors, custom errors]
            }
        },
        methods: {
            addUpdateForm(){
                this.$store.dispatch('errors/addUpdateForm')
                .then(() => this.$router.go());
            },
            updateError(language, field, value, parentField = null, isCustom = false){
                if(value == ''){
                    helper.swalErrorMessage(`The ${field} field is required.`);
                    return;
                }
                
                // Build the full field path
                let fullField = field;
                if (isCustom) {
                    fullField = `custom.${field}`;
                } else if (parentField) {
                    // For nested main errors like 'between.array'
                    fullField = `${parentField}.${field}`;
                }
                
                // Only check for :attribute in main errors, not custom errors
                if (!isCustom && !value.includes(':attribute') && !value.includes(':Attribute')){
                    helper.swalErrorMessage(`The ${fullField} must contains :attribute.`);
                    return;
                }
                
                this.$store.dispatch('errors/addUpdateForm',{
                    'language_id':language.id,
                    'field':fullField,
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
