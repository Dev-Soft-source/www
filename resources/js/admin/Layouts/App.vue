<template>
    <Navbar />
    <Sidebar />
    <section class="home-section relative lg:top-20">
        <main class="flex-1 max-h-full p-4 bg-gray-200">
            <slot />
        </main>
    </section>
</template>

<script>
import Sidebar from "./Sidebar.vue";
import Navbar from "./Navbar.vue";
export default {
    components: {
        Sidebar,
        Navbar,
    },
};
</script>
