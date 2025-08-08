<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-dollar-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ isAdmin ? 'Total Payrolls' : 'My Payrolls' }}</h6>
                                <h3 class="mb-0">{{ payrolls.total }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Approved</h6>
                                <h3 class="mb-0">{{ approvedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-time"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Pending</h6>
                                <h3 class="mb-0">{{ pendingCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-calendar"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">This Month</h6>
                                <h3 class="mb-0">{{ thisMonthCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Records Table -->
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ isAdmin ? 'All Payroll Records' : 'My Payroll Records' }}</h5>
                                <div v-if="isAdmin">
                                    <Link :href="route('admin.payroll.create')" class="btn btn-primary btn-sm">
                                        <i class="bx bx-plus me-1"></i>Create Payroll
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Employee</th>
                                            <th class="border-0">Period</th>
                                            <th class="border-0">Basic Salary</th>
                                            <th class="border-0">Gross Salary</th>
                                            <th class="border-0">Allowances</th>
                                            <th class="border-0">Deductions</th>
                                            <th class="border-0">Net Salary</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">{{ isAdmin ? 'Actions' : 'View' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="payroll in payrolls.data" :key="payroll.id">
                                            <td>{{ payroll.user?.name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ payroll.period_display }}</span>
                                                <br>
                                                <small class="text-muted">{{ formatDate(payroll.start_date) }} - {{ formatDate(payroll.end_date) }}</small>
                                            </td>
                                            <td>₱{{ formatNumber(payroll.basic_salary) }}</td>
                                            <td>₱{{ formatNumber(payroll.gross_salary) }}</td>
                                            <td>₱{{ formatNumber(payroll.total_allowances) }}</td>
                                            <td>₱{{ formatNumber(payroll.total_deductions) }}</td>
                                            <td>
                                                <strong class="text-success">₱{{ formatNumber(payroll.net_salary) }}</strong>
                                            </td>
                                            <td>
                                                <span :class="getStatusClass(payroll.status)">
                                                    {{ payroll.status_badge_class }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" @click="viewPayroll(payroll)">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button v-if="isAdmin" class="btn btn-sm btn-outline-warning me-1" @click="editPayroll(payroll)">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button v-if="isAdmin" class="btn btn-sm btn-success me-1" @click="sendEmail(payroll)" title="Send Payroll Email">
                                                    <i class="bx bx-envelope"></i> Send Email
                                                </button>
                                                <button v-if="isAdmin" class="btn btn-sm btn-outline-danger" @click="deletePayroll(payroll)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <Pagination :paginate="payrolls" />
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    payrolls: Object,
    isAdmin: Boolean,
})

// Debug logging
console.log('Payroll Index - isAdmin:', props.isAdmin)
console.log('Payroll Index - payrolls:', props.payrolls)



// Computed properties for statistics
const approvedCount = computed(() => {
    return props.payrolls.data.filter(p => p.status === 'approved' || p.status === 'paid').length
})

const pendingCount = computed(() => {
    return props.payrolls.data.filter(p => p.status === 'pending').length
})

const thisMonthCount = computed(() => {
    const currentMonth = new Date().getMonth()
    const currentYear = new Date().getFullYear()
    return props.payrolls.data.filter(p => {
        const date = new Date(p.start_date)
        return date.getMonth() === currentMonth && date.getFullYear() === currentYear
    }).length
})

const viewPayroll = (payroll) => {
    // Navigate to the show page
    const route = props.isAdmin ? `/admin/payroll/${payroll.id}` : `/payroll/${payroll.id}`
    router.get(route)
}

const editPayroll = (payroll) => {
    // Only allow admin users to edit
    if (!props.isAdmin) {
        console.error('Non-admin user attempted to edit payroll')
        return
    }
    
    // Navigate to the edit page
    const route = `/admin/payroll/${payroll.id}/edit`
    router.get(route)
}



const deletePayroll = (payroll) => {
    // Only allow admin users to delete
    if (!props.isAdmin) {
        console.error('Non-admin user attempted to delete payroll')
        return
    }
    
    if (confirm('Are you sure you want to delete this payroll record?')) {
        // Force admin route for admin users
        const route = `/admin/payroll/${payroll.id}`
        
        console.log('Deleting payroll with route:', route)
        console.log('Current pathname:', window.location.pathname)
        console.log('Is admin user:', props.isAdmin)
        
        router.delete(route, {
            onSuccess: () => {
                console.log('Payroll deleted successfully')
                // The page will automatically refresh with the updated data
            },
            onError: (errors) => {
                console.error('Error deleting payroll:', errors)
                console.error('Error details:', errors)
            }
        })
    }
}

const approvePayroll = (payroll) => {
    // Implementation for approving payroll
    console.log('Approve payroll:', payroll.id)
}

const sendEmail = (payroll) => {
    console.log('Send email function called for payroll:', payroll.id)
    console.log('Is admin user:', props.isAdmin)
    
    // Only allow admin users to send email
    if (!props.isAdmin) {
        console.error('Non-admin user attempted to send email')
        alert('You need admin privileges to send payroll emails.')
        return
    }
    
    if (confirm('Are you sure you want to send the payroll statement to ' + payroll.user?.email + '?')) {
        const route = `/admin/payroll/${payroll.id}/send-email`
        console.log('Sending email to route:', route)
        
        router.post(route, {}, {
            onSuccess: () => {
                console.log('Email sent successfully')
                alert('Payroll email sent successfully to ' + payroll.user?.email)
            },
            onError: (errors) => {
                console.error('Error sending email:', errors)
                alert('Error sending email. Please try again.')
            }
        })
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString()
}

const formatNumber = (number) => {
    return new Intl.NumberFormat('en-PH').format(number)
}

const getStatusClass = (status) => {
    switch (status) {
        case 'draft': return 'badge bg-secondary'
        case 'pending': return 'badge bg-warning'
        case 'approved': return 'badge bg-success'
        case 'paid': return 'badge bg-primary'
        case 'cancelled': return 'badge bg-danger'
        default: return 'badge bg-secondary'
    }
}
</script>
