<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-4">
            <div class="row">
                <div class="col-12">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">Employee Management</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link :href="route('dashboard')" class="text-decoration-none">Dashboard</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Employees</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex gap-2">
                            <Link :href="route('applicant.pending.index')" class="btn btn-outline-primary">
                                <i class="bx bx-user-plus me-1"></i>View Applicants
                            </Link>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 bg-primary text-white">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px">
                                        <i class="bx bx-user fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Total Employees</h6>
                                        <h3 class="mb-0">{{ employees.total }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 bg-success text-white">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px">
                                        <i class="bx bx-check-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Active Employees</h6>
                                        <h3 class="mb-0">{{ activeEmployees }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 bg-info text-white">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px">
                                        <i class="bx bx-calendar fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">This Month</h6>
                                        <h3 class="mb-0">{{ thisMonthEmployees }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 bg-warning text-dark">
                                <div class="d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px">
                                        <i class="bx bx-time fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Recent Hires</h6>
                                        <h3 class="mb-0">{{ recentEmployees }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employees Table -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Employee List</h5>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" placeholder="Search employees..." v-model="searchTerm">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Employee</th>
                                            <th class="border-0">Email</th>
                                            <th class="border-0">Hire Date</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="employee in filteredEmployees" :key="employee.id">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bx bx-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ employee.name }}</h6>
                                                        <small class="text-muted">ID: {{ employee.id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ employee.email }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ formatDate(employee.created_at) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Active</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <Link :href="route('employee.show', employee.id)" class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-show"></i>
                                                    </Link>
                                                    <Link :href="route('user.edit', employee.id)" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bx bx-edit"></i>
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <Pagination :paginate="employees" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    employees: Object,
})

const searchTerm = ref('')

const filteredEmployees = computed(() => {
    if (!searchTerm.value) {
        return props.employees.data
    }
    
    return props.employees.data.filter(employee => 
        employee.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        employee.email.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
})

const activeEmployees = computed(() => {
    return props.employees.data.filter(employee => employee.role === 'employee').length
})

const thisMonthEmployees = computed(() => {
    const thisMonth = new Date().getMonth()
    const thisYear = new Date().getFullYear()
    
    return props.employees.data.filter(employee => {
        const hireDate = new Date(employee.created_at)
        return hireDate.getMonth() === thisMonth && hireDate.getFullYear() === thisYear
    }).length
})

const recentEmployees = computed(() => {
    const oneWeekAgo = new Date()
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)
    
    return props.employees.data.filter(employee => {
        const hireDate = new Date(employee.created_at)
        return hireDate >= oneWeekAgo
    }).length
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.card {
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.btn {
    border-radius: 8px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
}
</style>
