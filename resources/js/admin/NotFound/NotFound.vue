<template>
    <AppLayout>
        <div class="relative py-4">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
                    <div class="mb-8">
                        <h1 class="text-9xl font-bold text-primary mb-4">404</h1>
                        <h2 class="text-4xl font-FuturaMdCnBT text-gray-800 mb-4">Page Not Found</h2>
                        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                            The page you're looking for doesn't exist or has been moved.
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <router-link 
                            :to="{ name: 'admin.dashboard' }" 
                            class="button-exp-fill inline-flex items-center justify-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            Go to Dashboard
                        </router-link>
                        
                        <button 
                            @click="$router.go(-1)" 
                            class="button-exp-fill bg-gray-500 hover:bg-gray-600 border-gray-500 hover:border-gray-600 inline-flex items-center justify-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                            </svg>
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    name: 'NotFound',
    mounted() {
        // Update document title
        document.title = '404 - Page Not Found | Admin';
    }
}
</script>

<style scoped>
</style>
