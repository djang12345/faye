<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});
</script>

<template>
    <Head title="Welcome" />

    <!-- Page Background Wrapper with White Background -->
    <div style="background-color: white; min-height: 100vh; color: #333;">

        <!-- Navbar with Logo and Title, Imitating Tailwind Layout with Bootstrap -->
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color: white; padding: 20px 2rem; margin: auto; max-width: 1140px;">
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center fw-bold" href="#" style="color: #4cda6d;">
                    <img src="images/shield.jpg" alt="Icon" class="rounded-circle me-3" style="width: 60px; height: 60px;" />
                    DPS - Data Processing System
                </a>
                <div class="d-flex">
                    <Link :href="route('login')" class="btn me-2" style="background: linear-gradient(115deg, #4cda6d 10%, #70e78c 90%); color: white; border: none; padding: 12px 20px; font-size: 1rem;">
                        Admin Login
                    </Link>
                    <Link :href="route('application.create')" class="btn" style="background: linear-gradient(115deg, #4cda6d 10%, #70e78c 90%); color: white; border: none; padding: 12px 20px; font-size: 1rem;">
                        Applicant Form
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Main content container with split columns -->
        <div class="container d-flex align-items-center" style="min-height: calc(100vh - 80px); max-width: 1300px;">
            <!-- Left Column: Text Content -->
            <div class="display-5 fw-bold text-start" style="color: #333;">
                <h2 class="display-5 fw-bold" style="color: #333;">
                    HERO SECURITY AND <span style="background: linear-gradient(115deg, #4cda6d 10%, #70e78c 90%); -webkit-background-clip: text; color: transparent;">
                        INVESTIGATION SERVICES
                    </span>
                </h2>
                <p class="lead" style="color: #555; text-align: justify;">
                    This platform is designed to streamline the process of submitting and reviewing applications for security and investigative services.
                    As an applicant, you can easily submit your credentials and service requests through our secure system. Each application will be
                    carefully reviewed by our dedicated administration team. You'll be promptly notified when your application is accepted or if further
                    action is required.
                </p>
            </div>

            <!-- Right Column: Image Placeholder -->
            <div class="col-lg-6 text-center">
                <img src="images/system.png" alt="Application Illustration" class="img-fluid rounded" style="max-width: 100%; height: auto;" />
            </div>
        </div>
    </div>
</template>
