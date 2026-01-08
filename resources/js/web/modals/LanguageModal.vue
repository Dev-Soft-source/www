<template>
    <div>
        <div class="fixed bottom-14 right-3 z-50">
            <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-secondary bg-opacity-40 cursor-pointer" @click="toggleLanguageModal">
                <span class="font-semibold text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802" />
                    </svg>
                </span>
            </span>
        </div>
        <!-- Main modal -->
        <div
            id="defaultModal"
            tabindex="-1"
            aria-hidden="true"
            class="fixed top-0 left-0 right-0 bottom-0 m-auto z-10 overflow-y-auto"
            v-if="showModal"
        >
            <div class="fixed inset-0 z-100 bg-gray-500 bg-opacity-75 transition-opacity"  @click="toggleLanguageModal"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <!-- Modal content -->
                <div
                    class="relative bg-white rounded-lg shadow w-full sm:max-w-2xl top-0 left-0 right-0 bottom-0 m-auto"
                >
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between py-3 px-3 border-b rounded-t"
                    >
                        <h3
                            class="card-heading text-primary text-gray-900"
                        >Select website language</h3>
                        <button
                            type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center"
                            data-modal-hide="defaultModal"
                            @click="toggleLanguageModal"
                        >
                            <img class="h-6" src="/assets/icons/19-X-inside-circle-2.png" alt="">
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                            <div
                                v-for="(language, key) in languages"
                                :key="key"
                            >
                            <a :href="`/set-language/${language?.id}?url=${current_url}&url_params=${url_params}`">
                                <img :src="language?.flag_icon?.full_path" style="width: 32px; height: 32px" class="mx-auto"/>
                                {{ language?.name }} <span v-if="language.is_default != 1">,{{ language?.native_name }} </span>
                               </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["languages", "current_url", "url_params"],
    data() {
        return {
            showModal: false,
        };
    },
    methods: {
        toggleLanguageModal() {
            this.showModal = !this.showModal;
        },
    },
};
</script>
