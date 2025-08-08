<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="d-flex justify-content-center align-items-center min-vh-100" style="background: linear-gradient(115deg, #4cda6d 10%, #70e78c 90%)">
        <div class="card p-5 text-center" style="max-width: 400px; width: 100%;">
            <!-- Logo at the top -->
            <div class="mb-4">
                <Link :href="route('welcome')">
                    <ApplicationLogo />
                </Link>
            </div>

            <!-- Centered slot content -->
            <div>
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}
</style>
