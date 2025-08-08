<script setup>
import { ref, onMounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const isNavToggled = ref(false);

function logOut() {
    Swal.fire({
        title: "Are you sure you want to log out?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("logout"));
        }
    });
}


</script>

<template>
    <div class="d-flex" :class="{ toggled: isNavToggled }" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white" id="sidebar-wrapper">
            <div
                class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom"
            >
                <i class="bx bxs-shield"></i>DPS
            </div>
            <div class="list-group list-group-flush my-3">
                <ul class="side-menu">
                    <li
                        class="tab"
                        :class="{
                            'is-active':
                                $page.props.routeName.includes('dashboard'),
                        }"
                    >
                        <Link
                            :href="route('dashboard')"
                            data-switcher
                            data-tab="dashboard"
                            class="tab is-active list-group-item list-group-item-action bg-transparent text-success-emphasis"
                            ><i class="fas fa-tachometer-alt me-2"></i
                            >Dashboard</Link
                        >
                    </li>

                    <li
                        class="tab"
                        :class="{
                            'is-active': $page.props.routeName.includes('user'),
                        }"
                        v-if="$page.props.auth.user.role === 'admin'"
                    >
                        <Link
                            :href="route('user.index')"
                            data-switcher
                            data-tab="users"
                            class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                            ><i class="bx bxs-user"></i> Users</Link
                        >
                    </li>

                    <li
                        class="tab"
                        :class="{
                            'is-active': $page.props.routeName.includes('attendance'),
                        }"
                    >
                        <Link
                            :href="$page.props.auth.user.role === 'admin' ? route('admin.attendance.index') : route('attendance.index')"
                            data-switcher
                            data-tab="attendance"
                            class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                            ><i class="bx bxs-time"></i> Attendance</Link
                        >
                    </li>

                    <li
                        class="tab"
                        :class="{
                            'is-active': $page.props.routeName.includes('payroll'),
                        }"
                    >
                        <Link
                            :href="$page.props.auth.user.role === 'admin' ? route('admin.payroll.index') : route('payroll.index')"
                            data-switcher
                            data-tab="payroll"
                            class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                            ><i class="bx bxs-dollar-circle"></i> Payroll</Link
                        >
                    </li>

                    <li
                        class="tab"
                        :class="{
                            'is-active': $page.props.routeName.includes('leave'),
                        }"
                    >
                        <Link
                            :href="$page.props.auth.user.role === 'admin' ? route('admin.leave.index') : route('leave.index')"
                            data-switcher
                            data-tab="leave"
                            class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                            ><i class="bx bxs-calendar"></i> Leave</Link
                        >
                    </li>

                    <li
                        class="tab"
                        :class="{
                            'is-active': $page.props.routeName.includes('employee'),
                        }"
                        v-if="$page.props.auth.user.role === 'admin'"
                    >
                        <Link
                            :href="route('employee.index')"
                            data-switcher
                            data-tab="employees"
                            class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                            ><i class="bx bxs-user-detail"></i> Employees</Link
                        >
                    </li>

                    <li class="tab" v-if="$page.props.auth.user.role === 'admin'">
                        <div
                            class="accordion-item list-group-item list-group-item-action bg-transparent text-success-emphasis"
                        >
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseOne"
                                    aria-expanded="false"
                                    aria-controls="flush-collapseOne"
                                >
                                    <i class="bx bxs-file-blank"></i> Applicants
                                </button>
                            </h2>
                            <div
                                id="flush-collapseOne"
                                :class="{'show': $page.props.routeName.includes('applicant')}"
                                class="accordion-collapse collapse"
                                aria-labelledby="flush-headingOne"
                                data-bs-parent="#accordionFlushExample"
                            >
                                <div class="accordion-body">
                                    <ul class="side-menu">
                                        <li class="tab" :class="{'is-active': $page.props.routeName.includes('applicant.pending')}">
                                            <Link
                                                :href="route('applicant.pending.index')"
                                                data-switcher
                                                data-tab="applicants"
                                                class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                                                ><i class='bx bxs-minus-circle'></i>
                                                Pending
                                                <span
                                                    id="pendingCount"
                                                    class="badge text-bg-danger"
                                                > {{ $page.props.auth.pendingCount }}</span>
                                            </Link>
                                        </li>
                                        <li class="tab" :class="{'is-active': $page.props.routeName.includes('applicant.accepted')}">
                                            <Link
                                                :href="route('applicant.accepted.index')"
                                                data-switcher
                                                data-tab="applicants"
                                                class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                                                ><i class='bx bxs-check-circle'></i>
                                                Accepted
                                                <span
                                                    id="acceptedCount"
                                                    class="badge text-bg-danger"
                                                >
                                                {{ $page.props.auth.acceptedCount }}
                                            </span>
                                            </Link>
                                        </li>
                                        <li class="tab" :class="{'is-active': $page.props.routeName.includes('applicant.rejected')}">
                                            <Link
                                                :href="route('applicant.rejected.index')"
                                                data-switcher
                                                data-tab="applicants"
                                                class="list-group-item list-group-item-action bg-transparent text-success-emphasis"
                                                ><i class='bx bxs-x-circle'></i>
                                                Rejected
                                                <span
                                                    id="rejectedCount"
                                                    class="badge text-bg-danger"
                                                >
                                                {{ $page.props.auth.rejectedCount }}</span>
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <button
                        @click="logOut()"
                        class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"
                    >
                        <i class="fas fa-power-off me-2"></i>Logout
                    </button>
                </ul>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav
                class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4"
            >
                <div class="d-flex align-items-center">
                    <i
                        @click="isNavToggled = !isNavToggled"
                        class="fas fa-align-left primary-text fs-4 me-3"
                        id="menu-toggle"
                    ></i>
                    <h2 class="fs-2 m-0"></h2>
                </div>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon text-danger"></span>
                </button>

                <div
                    class="collapse navbar-collapse"
                    id="navbarSupportedContent"
                >
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle second-text fw-bold"
                                href="#"
                                id="navbarDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <i class="fas fa-user me-2"></i>Hi, {{ $page.props.auth.user.name }}
                            </a>
                            <ul
                                class="dropdown-menu"
                                aria-labelledby="navbarDropdown"
                            >
                                <li class="tab is-active">
                                    <Link class="dropdown-item" :href="route('profile.edit')"
                                        >Profile</Link
                                    >
                                </li>
                                <li class="tab">
                                    <a class="dropdown-item" href="#"
                                        >Settings</a
                                    >
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <section class="pages">
                <div class="card shadow custom-card">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>
<style scoped>
:root {
    --main-bg-color: #009d63;
    --main-text-color: #009d63;
    --second-text-color: #bbbec5;
    --second-bg-color: #c1efde;
}

.primary-text {
    color: var(--main-text-color);
}

.second-text {
    color: var(--second-text-color);
}

.primary-bg {
    background-color: var(--main-bg-color);
}

.secondary-bg {
    background-color: var(--second-bg-color);
}

.rounded-full {
    border-radius: 100%;
}

#wrapper {
    overflow-x: hidden;
    background-image: linear-gradient(
        to right,
        #baf3d7,
        #c2f5de,
        #cbf7e4,
        #d4f8ea,
        #ddfaef
    );
}

#sidebar-wrapper {
    min-height: 100vh;
    margin-left: -15rem;
    -webkit-transition: margin 0.25s ease-out;
    -moz-transition: margin 0.25s ease-out;
    -o-transition: margin 0.25s ease-out;
    transition: margin 0.25s ease-out;
}

#sidebar-wrapper .sidebar-heading {
    padding: 0.875rem 1.25rem;
    font-size: 1.2rem;
}

#sidebar-wrapper .list-group {
    width: 15rem;
}

#page-content-wrapper {
    min-width: 100vw;
}

#wrapper.toggled #sidebar-wrapper {
    margin-left: 0;
}

#menu-toggle {
    cursor: pointer;
}

.list-group-item {
    border: none;
    padding: 20px 30px;
}

.list-group-item.active {
    background-color: transparent;
    color: var(--main-text-color);
    font-weight: bold;
    border: none;
}

@media (min-width: 768px) {
    #sidebar-wrapper {
        margin-left: 0;
    }

    #page-content-wrapper {
        min-width: 0;
        width: 100%;
    }

    #wrapper.toggled #sidebar-wrapper {
        margin-left: -15rem;
    }
}

.side-menu .tab.is-active a {
    background-color: #28a745; /* Green background */
    color: #28a745 !important; /* White text color */
    font-weight: bold; /* Bold text */
}

.side-menu {
    list-style-type: none; /* Removes bullets */
    padding-left: 0; /* Removes any default left padding */
}

/* Default tab styling */
.side-menu .tab a {
    background-color: transparent;
    color: #28a745; /* Green text color */
    font-weight: normal;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 123, 255, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99999;
}

.loading-spinner {
    border: white;
    border-radius: 50%;
    margin-top: 50px;
    width: 100px;
    height: 100px;
    margin: 0 auto;
    animation: spin 1s linear infinite;
    z-index: 9999;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.loading-content {
    display: flex;
    flex-direction: column;
    text-align: center;
    color: white;
}

.loading-content p {
    margin-top: 10px;
    font-size: 20px;
}

.custom-card {
    margin-left: 50px;
    margin-right: 50px;
    background-color: white;
    border-radius: 15px;
}

.date-picker-container {
    position: relative;
    display: inline-block;
}

.date-picker {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    width: 100%;
    font-size: 1rem;
    line-height: 1.5;
    background-color: #fff;
}

.date-picker:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.calendar-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}

.time-picker-container {
    position: relative;
    display: inline-block;
}

.time-picker {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    width: 100%;
    font-size: 1rem;
    line-height: 1.5;
    background-color: #fff;
}

.time-picker:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.clock-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}
</style>
