<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PasswordInput from "@/Components/PasswordInput.vue";
import { formatTime } from "@/functions";

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    acceptedApplicants: {
        type: Array,
        required: true,
    },
    auth: {
        type: Object,
        required: true,
    }
});

// Debug logging
console.log('User Index props:', props);
console.log('Users data:', props.users);
console.log('Users data type:', typeof props.users);
console.log('Users data keys:', Object.keys(props.users || {}));
console.log('Users.data:', props.users?.data);
console.log('Users.data length:', props.users?.data?.length);
console.log('Accepted applicants:', props.acceptedApplicants);
console.log('Accepted applicants length:', props.acceptedApplicants?.length);

// Extract the current user's name from the auth prop
const currentUserName = props.auth.user.name;

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "employee",
});

const createFromApplicantForm = useForm({
    applicant_id: "",
    role: "employee",
});

const submitForm = () => {
    // Check if passwords match
    if (form.password !== form.password_confirmation) {
        Swal.fire({
            icon: "error",
            title: "Passwords Do Not Match",
            text: "Please ensure both password fields match.",
            confirmButtonText: "OK",
            confirmButtonColor: "#d33",
        });
        return;
    }
    
    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to register this user!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, register!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Registering...",
                text: "Please wait while we register the user.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            // Proceed with form submission if confirmed
            form.post(route("user.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "User Registered",
                        text: "The user has been successfully registered!",
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    form.reset("password", "password_confirmation");
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem registering the user. Please try again.",
                    });
                },
            });
        }
    });
};

const createFromApplicant = () => {
    console.log('createFromApplicant called');
    console.log('Form data:', createFromApplicantForm.data());
    
    if (!createFromApplicantForm.applicant_id) {
        Swal.fire({
            icon: "error",
            title: "No Applicant Selected",
            text: "Please select an applicant to create an employee account.",
            confirmButtonText: "OK",
            confirmButtonColor: "#d33",
        });
        return;
    }

    // Find the selected applicant to show their name in the confirmation
    const selectedApplicant = props.acceptedApplicants.find(app => app.applicant.id == createFromApplicantForm.applicant_id);
    const applicantName = selectedApplicant ? `${selectedApplicant.applicant.firstname} ${selectedApplicant.applicant.lastname}` : 'the applicant';

    Swal.fire({
        title: "Create Employee Account?",
        text: `This will create an employee account for ${applicantName} using their email and send credentials.`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, create account!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('User confirmed, submitting form...');
            Swal.fire({
                title: "Creating Account...",
                text: "Please wait while we create the employee account.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            
            createFromApplicantForm.post(route("user.create-from-applicant"), {
                onSuccess: () => {
                    console.log('Success: Account created');
                    Swal.fire({
                        icon: "success",
                        title: "Account Created",
                        text: "Employee account created successfully! Credentials have been sent.",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    createFromApplicantForm.reset();
                },
                onError: (errors) => {
                    console.log('Error:', errors);
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: errors.email || "There was a problem creating the account.",
                    });
                },
            });
        }
    });
};

const resendCredentials = (user) => {
    Swal.fire({
        title: "Resend Credentials?",
        text: `This will send new login credentials to ${user.email}`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, send credentials!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Sending Credentials...",
                text: "Please wait while we send the credentials.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            
            router.post(route("user.resend-credentials", user.id), {}, {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Credentials Sent",
                        text: "New credentials have been sent to the user's email.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem sending the credentials.",
                    });
                },
            });
        }
    });
};

const DeleteUser = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Deleting...",
                text: "Please wait while we delete the registered user data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            router.delete(route("user.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The user has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire(
                        "Error!",
                        "Failed to delete the registered user.",
                        "error"
                    );
                },
            });
        }
    });
};

const getRoleBadgeClass = (role) => {
    return role === 'admin' ? 'badge bg-danger' : 'badge bg-success';
};

const getRoleText = (role) => {
    return role === 'admin' ? 'Admin' : 'Employee';
};
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <!-- Debug info -->
        <div v-if="false" class="alert alert-info">
            Debug: Users data received - Total: {{ users?.total || 'N/A' }}, Data length: {{ users?.data?.length || 'N/A' }}
        </div>
        <div class="container p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>User Management</h2>
                <div class="d-flex gap-2">
                    <button
                        type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#createFromApplicantModal"
                    >
                        <i class="bx bx-user-plus me-1"></i>Create from Applicant
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addUserModal"
                    >
                        <i class="bx bx-plus me-1"></i>Add User
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <h3>{{ users?.total || 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Employees</h5>
                            <h3>{{ users?.data?.filter(u => u.role === 'employee').length || 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Admins</h5>
                            <h3>{{ users?.data?.filter(u => u.role === 'admin').length || 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Users</h5>
                            <h3>{{ users?.data?.length || 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!users?.data?.length" class="text-center">
                                    <td colspan="5">No users found</td>
                                </tr>
                                <tr v-for="user in users?.data || []" :key="user.id">
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>
                                        <span :class="getRoleBadgeClass(user.role)">
                                            {{ getRoleText(user.role) }}
                                        </span>
                                    </td>
                                    <td>{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <Link
                                                :href="route('user.edit', user.id)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit User"
                                            >
                                                <i class="bx bx-edit"></i>
                                            </Link>
                                            <button
                                                @click="resendCredentials(user)"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Resend Credentials"
                                            >
                                                <i class="bx bx-envelope"></i>
                                            </button>
                                            <button
                                                v-if="user.name !== currentUserName"
                                                @click="DeleteUser(user.id)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete User"
                                            >
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accepted Applicants Section -->
        <div class="card mt-4" v-if="acceptedApplicants.length > 0">
            <div class="card-header">
                <h5 class="mb-0">Accepted Applicants (Ready for Employee Account Creation)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Interview Date</th>
                                <th>Interview Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="application in acceptedApplicants" :key="application.id">
                                <td>{{ application.applicant.firstname }} {{ application.applicant.lastname }}</td>
                                <td>{{ application.applicant.email }}</td>
                                <td>{{ new Date(application.interview_date).toLocaleDateString() }}</td>
                                <td>{{ application.interview_time ? formatTime(application.interview_time) : 'Not scheduled' }}</td>
                                <td>
                                    <button
                                        @click="createFromApplicantForm.applicant_id = application.applicant.id; createFromApplicant()"
                                        class="btn btn-sm btn-success"
                                        title="Create Employee Account"
                                    >
                                        <i class="bx bx-user-plus"></i> Create Account
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForm">
                            <div class="row mb-3">
                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="inputName"
                                        placeholder="Full Name"
                                        v-model="form.name"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="inputEmail"
                                        placeholder="Email Address"
                                        v-model="form.email"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputRole" class="col-sm-2 col-form-label">Role</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="inputRole" v-model="form.role" required>
                                        <option value="employee">Employee</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <PasswordInput
                                        type="password"
                                        class="form-control"
                                        id="inputPassword"
                                        placeholder="Password"
                                        v-model="form.password"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputConfirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <PasswordInput
                                        type="password"
                                        class="form-control"
                                        id="inputConfirmPassword"
                                        placeholder="Confirm Password"
                                        v-model="form.password_confirmation"
                                        required
                                    />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="submitForm">Create User</button>
                    </div>
                </div>
            </div>
        </div>

                    <!-- Create from Applicant Modal -->
            <div class="modal fade" id="createFromApplicantModal" tabindex="-1" aria-labelledby="createFromApplicantModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createFromApplicantModalLabel">Create Employee from Accepted Applicant</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="applicantSelect" class="form-label">Select Accepted Applicant</label>
                                <select class="form-control" id="applicantSelect" v-model="createFromApplicantForm.applicant_id" required>
                                    <option value="">Choose an accepted applicant...</option>
                                    <option v-for="application in acceptedApplicants" :key="application.id" :value="application.applicant.id">
                                        {{ application.applicant.firstname }} {{ application.applicant.lastname }} ({{ application.applicant.email }})
                                    </option>
                                </select>
                                <small class="form-text text-muted">Select an accepted applicant to create an employee account using their email.</small>
                            </div>
                            <div class="mb-3">
                                <label for="roleSelect" class="form-label">Role</label>
                                <select class="form-control" id="roleSelect" v-model="createFromApplicantForm.role" required>
                                    <option value="employee">Employee</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" @click="createFromApplicant">Create Employee</button>
                        </div>
                    </div>
                </div>
            </div>
    </AuthenticatedLayout>
</template>
