<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PasswordInput from "@/Components/PasswordInput.vue";

const props = defineProps({
    errors: Object,
    user: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "", 
});

const updateUser = () => {
        // Check if passwords match
        if (form.password !== form.password_confirmation) {
        Swal.fire({
            icon: "error",
            title: "Passwords Do Not Match",
            text: "Please ensure both password fields match.",
            confirmButtonText: "OK",
            confirmButtonColor: "#d33",
        });
        return; // Stop the form submission if passwords don't match
    }
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this user's information?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Updating...",
                text: "Please wait while we update the user data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Prepare form data
            let data = { name: form.name, email: form.email };
            if (form.password) {
                data.password = form.password; // Only include password if it's been changed
            }

            form.put(route("user.update", props.user.id), {
                data: data, // Pass only necessary data
                onSuccess: () => {
                    Swal.fire(
                        "Updated!",
                        "User information has been updated.",
                        "success"
                    );
                    form.reset(); // Reset form on success
                },
                onError: () =>
                    Swal.fire("Error", "Failed to update the user.", "error"),
            });
        }
    });
};

</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <div class="container py-4">
            <h2 class="mb-4">Edit User Data</h2>
            
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="updateUser()">
                        <div class="mb-3 row">
                            <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input
                                    type="text"
                                    id="inputName"
                                    class="form-control"
                                    placeholder="Preferred Name"
                                    v-model="form.name"
                                />
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input
                                    type="email"
                                    id="inputEmail"
                                    class="form-control"
                                    placeholder="Your Email Address"
                                    v-model="form.email"
                                />
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <PasswordInput
                                    type="password"
                                    id="inputPassword"
                                    class="form-control"
                                    placeholder="Password"
                                    v-model="form.password"
                                />
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="inputConfirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
                            <div class="col-sm-10">
                                <PasswordInput
                                    type="password"
                                    id="inputConfirmPassword"
                                    class="form-control"
                                    placeholder="Confirm Password"
                                    v-model="form.password_confirmation"
                                />
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="btn btn-primary w-100"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>