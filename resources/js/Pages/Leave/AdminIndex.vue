<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-calendar-check"></i>
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
                                <h5 class="mb-0">All Leave Requests</h5>
                                <button class="btn btn-primary btn-sm" @click="openCreateModal">
                                    <i class="bx bx-plus me-1"></i>Create Request
                                </button>
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
                                            <th class="border-0">Submitted</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="request in leaveRequests.data" :key="request.id">
                                            <td>
                                                <div>
                                                    <strong>{{ request.user?.name || 'N/A' }}</strong>
                                                    <div class="text-muted small">{{ request.user?.email || 'N/A' }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ request.leave_type_display || request.leave_type }}</span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div><strong>From:</strong> {{ formatDate(request.start_date) }}</div>
                                                    <div><strong>To:</strong> {{ formatDate(request.end_date) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ request.days_requested }} days</span>
                                            </td>
                                            <td>
                                                <span :class="getStatusClass(request)">
                                                    {{ getStatusText(request) }}
                                                </span>
                                            </td>
                                            <td class="small text-muted">
                                                {{ formatDate(request.created_at) }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" @click="viewRequest(request)">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button v-if="request.status === 'pending'" class="btn btn-sm btn-outline-success me-1" @click="approveRequest(request)">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                                <button v-if="request.status === 'pending'" class="btn btn-sm btn-outline-danger me-1" @click="rejectRequest(request)">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                                <button v-if="request.status === 'pending'" class="btn btn-sm btn-outline-warning" @click="editRequest(request)">
                                                    <i class="bx bx-edit"></i>
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

        <!-- View Leave Request Modal -->
        <div v-if="showViewModal" class="modal-overlay" @click="showViewModal = false">
            <div class="modal-container" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Leave Request Details</h5>
                        <button type="button" class="btn-close" @click="showViewModal = false"></button>
                    </div>
                    <div class="modal-body" v-if="selectedRequest">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Employee Information</h6>
                                <p><strong>Name:</strong> {{ selectedRequest.user?.name }}</p>
                                <p><strong>Email:</strong> {{ selectedRequest.user?.email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Leave Information</h6>
                                <p><strong>Type:</strong> <span class="badge bg-info">{{ selectedRequest.leave_type_display || selectedRequest.leave_type }}</span></p>
                                <p><strong>Days:</strong> <span class="badge bg-secondary">{{ selectedRequest.days_requested }} days</span></p>
                                <p><strong>Status:</strong> 
                                    <span :class="getStatusClass(selectedRequest)">
                                        {{ getStatusText(selectedRequest) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">Date Range</h6>
                                <p><strong>From:</strong> {{ formatDate(selectedRequest.start_date) }}</p>
                                <p><strong>To:</strong> {{ formatDate(selectedRequest.end_date) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Timeline</h6>
                                <p><strong>Submitted:</strong> {{ formatDateTime(selectedRequest.created_at) }}</p>
                                <p v-if="selectedRequest.approved_at"><strong>Processed:</strong> {{ formatDateTime(selectedRequest.approved_at) }}</p>
                                <p v-if="selectedRequest.approved_by"><strong>By:</strong> {{ selectedRequest.approved_by?.name }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">Request Details</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ selectedRequest.details }}
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3" v-if="selectedRequest.admin_notes">
                            <div class="col-12">
                                <h6 class="text-primary">Admin Notes</h6>
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    {{ selectedRequest.admin_notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showViewModal = false">Close</button>
                        <button v-if="selectedRequest?.status === 'pending'" @click="openApprovalModal" type="button" class="btn btn-primary">Process Request</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Modal -->
        <div v-if="showApprovalModal" class="modal-overlay" @click="showApprovalModal = false">
            <div class="modal-container" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Process Leave Request</h5>
                        <button type="button" class="btn-close" @click="showApprovalModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitApproval">
                            <div class="mb-3">
                                <label class="form-label">Decision</label>
                                <select v-model="approvalForm.status" class="form-control" required>
                                    <option value="">Select decision</option>
                                    <option value="approved">✅ Approve</option>
                                    <option value="rejected">❌ Reject</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea v-model="approvalForm.admin_notes" class="form-control" rows="3" placeholder="Add any notes or comments..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showApprovalModal = false">Cancel</button>
                        <button @click="submitApproval" type="button" class="btn btn-primary">Submit Decision</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Leave Request Modal -->
        <div v-if="showCreateModal" class="modal-overlay" @click="showCreateModal = false">
            <div class="modal-container modal-lg" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Leave Request</h5>
                        <button type="button" class="btn-close" @click="showCreateModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitCreateForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee</label>
                                        <select v-model="createForm.user_id" @change="onEmployeeChange" class="form-control" required>
                                            <option value="">Select employee</option>
                                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                                {{ employee.name }} ({{ employee.email }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Leave Type</label>
                                        <select v-model="createForm.leave_type" @change="onLeaveTypeChange" class="form-control" required>
                                            <option value="">Select leave type</option>
                                            <option v-for="rule in leaveRules" :key="rule.leave_type" :value="rule.leave_type">
                                                {{ rule.display_name }}
                                                <span v-if="selectedEmployeeCredits && selectedEmployeeCredits[rule.leave_type]">
                                                    ({{ selectedEmployeeCredits[rule.leave_type].available_credits }} credits left)
                                                </span>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Credits Display -->
                            <div v-if="selectedEmployeeCredits" class="mb-3 p-3 bg-info bg-opacity-10 rounded">
                                <h6 class="text-info mb-2">Available Leave Credits</h6>
                                <div class="row">
                                    <div v-for="(credits, leaveType) in selectedEmployeeCredits" :key="leaveType" class="col-md-4">
                                        <div class="card border-info">
                                            <div class="card-body p-2">
                                                <div class="small font-weight-bold">{{ getLeaveTypeDisplayName(leaveType) }}</div>
                                                <div class="small">
                                                    <span class="text-success">Available: {{ credits.available_credits }}</span> / 
                                                    <span class="text-muted">Total: {{ credits.total_credits }}</span>
                                                </div>
                                                <div class="small text-muted">
                                                    Used: {{ credits.used_credits }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input v-model="createForm.start_date" type="date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">End Date</label>
                                        <input v-model="createForm.end_date" type="date" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Days Requested Display -->
                            <div v-if="daysRequested > 0" class="mb-3">
                                <label class="form-label">Days Requested</label>
                                <div class="p-2 bg-light rounded">
                                    <span class="badge bg-secondary">{{ daysRequested }} days</span>
                                    <span v-if="selectedLeaveTypeCredits" class="ms-2 small">
                                        Available credits: {{ selectedLeaveTypeCredits.available_credits }}
                                        <span v-if="daysRequested > selectedLeaveTypeCredits.available_credits" class="text-danger">
                                            ⚠️ Insufficient credits
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Request Details</label>
                                <textarea v-model="createForm.details" class="form-control" rows="3" placeholder="Please provide detailed reason for the leave request..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select v-model="createForm.status" class="form-control" required>
                                            <option value="pending">⏳ Pending</option>
                                            <option value="approved">✅ Approved</option>
                                            <option value="rejected">❌ Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Admin Notes (Optional)</label>
                                        <textarea v-model="createForm.admin_notes" class="form-control" rows="2" placeholder="Add any admin notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
                                                    <button @click="submitCreateForm" type="button" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Create Request
                            </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    leaveRequests: Object,
    employees: Array,
    leaveRules: Array,
    employeeCredits: Object,
})

const selectedRequest = ref(null)
const showViewModal = ref(false)
const showApprovalModal = ref(false)
const showCreateModal = ref(false)

const approvalForm = ref({
    status: '',
    admin_notes: ''
})

const createForm = ref({
    user_id: '',
    leave_type: '',
    start_date: '',
    end_date: '',
    details: '',
    status: 'pending',
    admin_notes: ''
})

// Computed properties for statistics
const pendingCount = computed(() => {
    return props.leaveRequests.data.filter(req => req.status === 'pending').length
})

const approvedCount = computed(() => {
    return props.leaveRequests.data.filter(req => req.status === 'approved').length
})

const rejectedCount = computed(() => {
    return props.leaveRequests.data.filter(req => req.status === 'rejected').length
})

// Employee credits computed properties
const selectedEmployeeCredits = computed(() => {
    if (!createForm.value.user_id || !props.employeeCredits[createForm.value.user_id]) return null
    return props.employeeCredits[createForm.value.user_id]
})

const selectedLeaveTypeCredits = computed(() => {
    if (!selectedEmployeeCredits.value || !createForm.value.leave_type) return null
    return selectedEmployeeCredits.value[createForm.value.leave_type]
})

const daysRequested = computed(() => {
    if (!createForm.value.start_date || !createForm.value.end_date) return 0
    const start = new Date(createForm.value.start_date)
    const end = new Date(createForm.value.end_date)
    const diffTime = Math.abs(end - start)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays + 1
})

// Methods
const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (datetime) => {
    if (!datetime) return 'N/A'
    return new Date(datetime).toLocaleString()
}

const getStatusClass = (request) => {
    const classes = {
        pending: 'badge bg-warning',
        approved: 'badge bg-success',
        rejected: 'badge bg-danger'
    }
    return classes[request.status] || 'badge bg-secondary'
}

const getStatusText = (request) => {
    const texts = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected'
    }
    return texts[request.status] || request.status
}

const getLeaveTypeDisplayName = (leaveType) => {
    const rule = props.leaveRules.find(r => r.leave_type === leaveType)
    return rule ? rule.display_name : leaveType
}

const viewRequest = (request) => {
    selectedRequest.value = request
    showViewModal.value = true
}

const editRequest = (request) => {
    window.location.href = route('admin.leave.edit', request.id)
}

const approveRequest = (request) => {
    selectedRequest.value = request
    approvalForm.value = {
        status: 'approved',
        admin_notes: ''
    }
    showApprovalModal.value = true
}

const rejectRequest = (request) => {
    selectedRequest.value = request
    approvalForm.value = {
        status: 'rejected',
        admin_notes: ''
    }
    showApprovalModal.value = true
}

const openApprovalModal = () => {
    showViewModal.value = false
    approvalForm.value = {
        status: '',
        admin_notes: ''
    }
    showApprovalModal.value = true
}

const submitApproval = () => {
    if (!approvalForm.value.status) {
        alert('Please select a decision')
        return
    }
    
    router.patch(route('admin.leave.approve', selectedRequest.value.id), approvalForm.value, {
        onSuccess: () => {
            showApprovalModal.value = false
            showViewModal.value = false
            window.location.reload()
        }
    })
}

const openCreateModal = () => {
    createForm.value = {
        user_id: '',
        leave_type: '',
        start_date: '',
        end_date: '',
        details: '',
        status: 'pending',
        admin_notes: ''
    }
    showCreateModal.value = true
}

const onEmployeeChange = () => {
    createForm.value.leave_type = '' // Reset leave type when employee changes
}

const onLeaveTypeChange = () => {
    // Leave type changed, credits will update automatically
}

const submitCreateForm = () => {
    console.log('Submitting form data:', createForm.value)
    
    // Validate required fields
    if (!createForm.value.user_id) {
        alert('Please select an employee')
        return
    }
    
    if (!createForm.value.leave_type) {
        alert('Please select a leave type')
        return
    }
    
    if (!createForm.value.start_date) {
        alert('Please select a start date')
        return
    }
    
    if (!createForm.value.end_date) {
        alert('Please select an end date')
        return
    }
    
    if (!createForm.value.details) {
        alert('Please provide request details')
        return
    }
    
    useForm(createForm.value).post(route('admin.leave.store'), {
        onSuccess: () => {
            console.log('Form submitted successfully')
            showCreateModal.value = false
            window.location.reload()
        },
        onError: (errors) => {
            console.error('Form errors:', errors)
            alert('Please check the form and try again')
        }
    })
}

onMounted(() => {
    // Initialize any necessary setup
})
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.modal-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-container.modal-lg {
    max-width: 800px;
}

.modal-content {
    padding: 0;
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 1rem;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close:hover {
    background-color: #f8f9fa;
    border-radius: 4px;
}
</style>
