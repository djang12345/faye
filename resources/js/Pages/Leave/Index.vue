<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-calendar"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Total Requests</h6>
                                <h3 class="mb-0">{{ leaveRequests.total }}</h3>
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
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-x-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Rejected</h6>
                                <h3 class="mb-0">{{ rejectedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Requests Table -->
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Leave Requests</h5>
                                <div>
                                    <button class="btn btn-info btn-sm me-2" @click="openCreditsModal">
                                        <i class="bx bx-info-circle me-1"></i>My Credits
                                    </button>
                                    <Link :href="route('leave.create')" class="btn btn-primary btn-sm">
                                        <i class="bx bx-plus me-1"></i>Request Leave
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
                                            <th class="border-0">Leave Type</th>
                                            <th class="border-0">Date Range</th>
                                            <th class="border-0">Days</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="request in leaveRequests.data" :key="request.id">
                                            <td>{{ request.user?.name || 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ request.leave_type_display || request.leave_type }}</span>
                                            </td>
                                            <td>
                                                {{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}
                                            </td>
                                            <td>{{ request.days_requested }} days</td>
                                            <td>
                                                <span :class="request.status_badge_class">
                                                    {{ request.status_display }}
                                                </span>
                                            </td>
                                            <td>
                                                <Link :href="route('leave.show', request.id)" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="bx bx-show"></i>
                                                </Link>
                                                <!-- Employee Actions -->
                                                <Link v-if="!isAdmin && request.status === 'pending'" :href="route('leave.edit', request.id)" class="btn btn-sm btn-outline-warning me-1">
                                                    <i class="bx bx-edit"></i>
                                                </Link>
                                                <button v-if="!isAdmin && request.status === 'pending'" class="btn btn-sm btn-outline-danger" @click="deleteRequest(request)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                <!-- Admin Actions -->
                                                <button v-if="isAdmin && request.status === 'pending'" class="btn btn-sm btn-outline-success me-1" @click="approveRequest(request)">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                                <button v-if="isAdmin && request.status === 'pending'" class="btn btn-sm btn-outline-danger" @click="rejectRequest(request)">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <Pagination :paginate="leaveRequests" />
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Credits Modal -->
        <div class="modal fade" id="creditsModal" tabindex="-1" ref="creditsModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">My Leave Credits</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="credits" class="row">
                            <div v-for="(credit, type) in credits" :key="type" class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ credit.display_name }}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Total Credits</small>
                                                <p class="mb-0"><strong>{{ credit.total_credits }}</strong></p>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Used Credits</small>
                                                <p class="mb-0"><strong class="text-warning">{{ credit.used_credits }}</strong></p>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Available</small>
                                                <p class="mb-0"><strong class="text-success">{{ credit.available_credits }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve/Reject Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1" ref="approveModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ approvalAction === 'approve' ? 'Approve' : 'Reject' }} Leave Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitApproval">
                            <div class="mb-3">
                                <label class="form-label">Admin Notes</label>
                                <textarea class="form-control" v-model="adminNotes" rows="3" placeholder="Add notes (optional)"></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" :class="approvalAction === 'approve' ? 'btn btn-success' : 'btn btn-danger'">
                                    {{ approvalAction === 'approve' ? 'Approve' : 'Reject' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    leaveRequests: Object,
    isAdmin: Boolean,
})

const showCreditsModal = ref(false)
const credits = ref(null)
const approvalAction = ref('approve')
const adminNotes = ref('')
const requestToApprove = ref(null)

// Computed properties for statistics
const pendingCount = computed(() => {
    return props.leaveRequests.data.filter(r => r.status === 'pending').length
})

const approvedCount = computed(() => {
    return props.leaveRequests.data.filter(r => r.status === 'approved').length
})

const rejectedCount = computed(() => {
    return props.leaveRequests.data.filter(r => r.status === 'rejected').length
})



const approveRequest = (request) => {
    approvalAction.value = 'approve'
    requestToApprove.value = request
    const modal = new bootstrap.Modal(document.getElementById('approveModal'))
    modal.show()
}

const rejectRequest = (request) => {
    approvalAction.value = 'reject'
    requestToApprove.value = request
    const modal = new bootstrap.Modal(document.getElementById('approveModal'))
    modal.show()
}

const deleteRequest = (request) => {
    if (confirm('Are you sure you want to delete this leave request? This action cannot be undone.')) {
        router.delete(route('leave.destroy', request.id), {
            onSuccess: () => {
                // Request will be automatically removed from the list
            },
            onError: (errors) => {
                console.error('Error deleting leave request:', errors)
            }
        })
    }
}

const submitApproval = () => {
    if (!requestToApprove.value) return

    router.patch(route('leave.approve', requestToApprove.value.id), {
        status: approvalAction.value,
        admin_notes: adminNotes.value
    }, {
        onSuccess: () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'))
            modal.hide()
            adminNotes.value = ''
            requestToApprove.value = null
        }
    })
}

const loadCredits = async () => {
    try {
        const response = await fetch(route('leave.my-credits'))
        credits.value = await response.json()
    } catch (error) {
        console.error('Error loading credits:', error)
    }
}

const openCreditsModal = () => {
    loadCredits()
    const modal = new bootstrap.Modal(document.getElementById('creditsModal'))
    modal.show()
}

onMounted(() => {
    // Initialize Bootstrap modals
    if (typeof bootstrap !== 'undefined') {
        // Modal functionality is handled by Bootstrap
    }
    

})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString()
}
</script>
