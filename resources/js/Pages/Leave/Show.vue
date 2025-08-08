<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-4">
            <div class="row">
                <div class="col-12">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">Leave Request Details</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link :href="route('leave.index')" class="text-decoration-none">Leave Requests</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">View Details</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex gap-2">
                            <Link :href="route('leave.index')" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Back to List
                            </Link>
                            <button class="btn btn-outline-primary" @click="printRequest">
                                <i class="bx bx-printer me-1"></i>Print
                            </button>
                            <button v-if="isAdmin && leaveRequest.status === 'pending'" 
                                    class="btn btn-success" 
                                    @click="approveRequest">
                                <i class="bx bx-check me-1"></i>Approve
                            </button>
                            <button v-if="isAdmin && leaveRequest.status === 'pending'" 
                                    class="btn btn-danger" 
                                    @click="rejectRequest">
                                <i class="bx bx-x me-1"></i>Reject
                            </button>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="row">
                        <!-- Left Column - Employee & Request Info -->
                        <div class="col-lg-8">
                            <!-- Employee Information Card -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bx bx-user me-2"></i>Employee Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="bx bx-user fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ leaveRequest.user?.name }}</h6>
                                                    <small class="text-muted">{{ leaveRequest.user?.email }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-end">
                                                <span :class="getStatusBadgeClass(leaveRequest.status)" class="fs-6 px-3 py-2">
                                                    {{ getStatusDisplay(leaveRequest.status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Leave Request Details Card -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="bx bx-calendar me-2"></i>Leave Request Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">Leave Type</label>
                                                <div class="p-3 bg-light rounded">
                                                    <span class="badge bg-info fs-6">{{ leaveRequest.leave_type_display }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">Days Requested</label>
                                                <div class="p-3 bg-light rounded">
                                                    <span class="fs-5 fw-bold text-primary">{{ leaveRequest.days_requested }} days</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">Start Date</label>
                                                <div class="p-3 bg-light rounded">
                                                    <i class="bx bx-calendar me-2"></i>
                                                    {{ formatDate(leaveRequest.start_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">End Date</label>
                                                <div class="p-3 bg-light rounded">
                                                    <i class="bx bx-calendar me-2"></i>
                                                    {{ formatDate(leaveRequest.end_date) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Leave Details</label>
                                        <div class="p-4 bg-light rounded">
                                            <p class="mb-0">{{ leaveRequest.details }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Notes Section -->
                            <div v-if="leaveRequest.admin_notes" class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bx bx-message-square-dots me-2"></i>Admin Notes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <p class="mb-0">{{ leaveRequest.admin_notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Timeline & Actions -->
                        <div class="col-lg-4">
                            <!-- Timeline Card -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i class="bx bx-time me-2"></i>Request Timeline
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Request Submitted</h6>
                                                <small class="text-muted">{{ formatDateTime(leaveRequest.created_at) }}</small>
                                            </div>
                                        </div>
                                        
                                        <div v-if="leaveRequest.approved_at" class="timeline-item">
                                            <div class="timeline-marker" :class="leaveRequest.status === 'approved' ? 'bg-success' : 'bg-danger'"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ leaveRequest.status === 'approved' ? 'Request Approved' : 'Request Rejected' }}</h6>
                                                <small class="text-muted">{{ formatDateTime(leaveRequest.approved_at) }}</small>
                                                <div v-if="leaveRequest.approved_by" class="mt-1">
                                                    <small class="text-muted">by {{ leaveRequest.approved_by?.name }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions Card -->
                            <div v-if="isAdmin && leaveRequest.status === 'pending'" class="card shadow-sm border-0">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="bx bx-cog me-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success" @click="approveRequest">
                                            <i class="bx bx-check me-2"></i>Approve Request
                                        </button>
                                        <button class="btn btn-danger" @click="rejectRequest">
                                            <i class="bx bx-x me-2"></i>Reject Request
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Stats Card -->
                            <div v-if="isAdmin" class="card shadow-sm border-0 mt-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="bx bx-stats me-2"></i>Employee Leave Stats
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-1">{{ leaveRequest.user?.total_approved_requests || 0 }}</h4>
                                                <small class="text-muted">Approved</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h4 class="text-warning mb-1">{{ leaveRequest.user?.total_pending_requests || 0 }}</h4>
                                                <small class="text-muted">Pending</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div>
                                                <h4 class="text-danger mb-1">{{ leaveRequest.user?.total_rejected_requests || 0 }}</h4>
                                                <small class="text-muted">Rejected</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Leave Credits Info -->
                            <div v-if="isAdmin" class="card shadow-sm border-0 mt-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bx bx-calendar-check me-2"></i>Leave Credits Info
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div class="p-3 bg-light rounded">
                                                <h6 class="text-muted mb-1">Emergency Leave</h6>
                                                <h4 class="text-primary mb-0">{{ leaveRequest.user?.emergency_credits || 0 }}</h4>
                                                <small class="text-muted">Available</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="p-3 bg-light rounded">
                                                <h6 class="text-muted mb-1">Sick Leave</h6>
                                                <h4 class="text-primary mb-0">{{ leaveRequest.user?.sick_credits || 0 }}</h4>
                                                <small class="text-muted">Available</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="p-3 bg-light rounded">
                                                <h6 class="text-muted mb-1">Vacation Leave</h6>
                                                <h4 class="text-primary mb-0">{{ leaveRequest.user?.vacation_credits || 0 }}</h4>
                                                <small class="text-muted">Available</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                <textarea class="form-control" v-model="adminNotes" rows="3" 
                                          :placeholder="approvalAction === 'approve' ? 'Add approval notes (optional)' : 'Add rejection reason (optional)'"></textarea>
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
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    leaveRequest: Object,
    isAdmin: Boolean,
})

const approvalAction = ref('approve')
const adminNotes = ref('')

const approveRequest = () => {
    approvalAction.value = 'approve'
    const modal = new bootstrap.Modal(document.getElementById('approveModal'))
    modal.show()
}

const rejectRequest = () => {
    approvalAction.value = 'reject'
    const modal = new bootstrap.Modal(document.getElementById('approveModal'))
    modal.show()
}

const submitApproval = () => {
    router.patch(route('leave.approve', props.leaveRequest.id), {
        status: approvalAction.value,
        admin_notes: adminNotes.value
    }, {
        onSuccess: () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'))
            modal.hide()
            adminNotes.value = ''
            // Redirect back to leave index
            router.visit(route('leave.index'))
        }
    })
}

const getStatusBadgeClass = (status) => {
    const classes = {
        'pending': 'badge bg-warning',
        'approved': 'badge bg-success',
        'rejected': 'badge bg-danger',
    }
    return classes[status] || classes['pending']
}

const getStatusDisplay = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString()
}

const printRequest = () => {
    window.print()
}

onMounted(() => {
    // Initialize Bootstrap modals
    if (typeof bootstrap !== 'undefined') {
        // Modal functionality is handled by Bootstrap
    }
})
</script>

<style scoped>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    padding-left: 10px;
}

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

/* Print styles */
@media print {
    .btn, .breadcrumb {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background: #f8f9fa !important;
        color: #000 !important;
    }
    
    .timeline::before {
        background: #000 !important;
    }
    
    .timeline-marker {
        border: 2px solid #000 !important;
        box-shadow: 0 0 0 2px #fff !important;
    }
}
</style>
