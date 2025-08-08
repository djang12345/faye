<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Request Leave</h5>
                                <Link :href="route('leave.index')" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-arrow-back me-1"></i>Back to My Leave Requests
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <!-- Leave Type and Employee Info -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Leave Type *</label>
                                            <select v-model="form.leave_type" @change="onLeaveTypeChange" class="form-control" required>
                                                <option value="">Select Leave Type</option>
                                                <option v-for="rule in leaveRules" :key="rule.id" :value="rule.leave_type">
                                                    {{ rule.display_name }} 
                                                    <span v-if="availableCredits[rule.leave_type] !== undefined">
                                                        ({{ availableCredits[rule.leave_type] }} credits available)
                                                    </span>
                                                </option>
                                            </select>
                                            <div v-if="errors.leave_type" class="text-danger small mt-1">{{ errors.leave_type }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Employee</label>
                                            <div class="p-3 bg-light rounded">
                                                <strong>{{ $page.props.auth.user.name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $page.props.auth.user.email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                                                <!-- Available Credits Display -->
                                <div v-if="selectedLeaveRule" class="mb-4 p-3 bg-info bg-opacity-10 rounded">
                                    <h6 class="text-info mb-3">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Leave Credit Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body p-3">
                                                    <div class="small font-weight-bold">Total Credits</div>
                                                    <div class="h5 mb-0 text-info">{{ selectedLeaveRule.default_credits }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-success">
                                                <div class="card-body p-3">
                                                    <div class="small font-weight-bold">Available Credits</div>
                                                    <div class="h5 mb-0 text-success">{{ availableCredits[form.leave_type] || 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-warning">
                                                <div class="card-body p-3">
                                                    <div class="small font-weight-bold">Days Requesting</div>
                                                    <div class="h5 mb-0 text-warning">{{ daysRequested }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!hasEnoughCredits" class="alert alert-warning mt-3 mb-0">
                                        <i class="bx bx-warning me-1"></i>
                                        <strong>Insufficient Credits:</strong> You only have {{ availableCredits[form.leave_type] || 0 }} credits available for {{ selectedLeaveRule.display_name }}. You are requesting {{ daysRequested }} days.
                                    </div>
                                </div>

                                <!-- Debug Information (temporary) -->
                                <div class="mb-3 p-2 bg-warning bg-opacity-10 rounded">
                                    <small class="text-muted">
                                        <strong>Debug Info:</strong><br>
                                        Leave Rules Count: {{ leaveRules ? leaveRules.length : 'undefined' }}<br>
                                        Available Credits: {{ JSON.stringify(availableCredits) }}<br>
                                        Selected Leave Type: {{ form.leave_type || 'none' }}
                                    </small>
                                </div>

                                <!-- Available Leave Types Info -->
                                <div v-else class="mb-4 p-3 bg-light rounded">
                                    <h6 class="text-muted mb-3">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Available Leave Types
                                    </h6>
                                    <div v-if="leaveRules && leaveRules.length > 0" class="row">
                                        <div v-for="rule in leaveRules" :key="rule.id" class="col-md-4 mb-2">
                                            <div class="card border-light">
                                                <div class="card-body p-3">
                                                    <div class="small font-weight-bold">{{ rule.display_name }}</div>
                                                    <div class="small text-muted">
                                                        Available: {{ availableCredits[rule.leave_type] || 0 }} credits
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        No leave types available. Please contact HR.
                                    </div>
                                    <small class="text-muted">Select a leave type above to see detailed credit information.</small>
                                </div>

                                <!-- Date Range -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Start Date *</label>
                                            <input v-model="form.start_date" type="date" class="form-control" required>
                                            <div v-if="errors.start_date" class="text-danger small mt-1">{{ errors.start_date }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">End Date *</label>
                                            <input v-model="form.end_date" type="date" class="form-control" required>
                                            <div v-if="errors.end_date" class="text-danger small mt-1">{{ errors.end_date }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Days Requested Display -->
                                <div v-if="daysRequested > 0" class="mb-3">
                                    <label class="form-label">Days Requested</label>
                                    <div class="p-3 bg-light rounded">
                                        <span class="badge bg-secondary fs-6">{{ daysRequested }} days</span>
                                        <span v-if="selectedLeaveRule" class="ms-2 small">
                                            Available credits: {{ availableCredits[form.leave_type] || 0 }}
                                            <span v-if="!hasEnoughCredits" class="text-danger">
                                                ⚠️ Insufficient credits
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Request Details -->
                                <div class="mb-3">
                                    <label class="form-label">Request Details *</label>
                                    <textarea v-model="form.details" rows="6" class="form-control" 
                                        placeholder="Please provide detailed information about your leave request. Include the reason for your leave, any important details, and how your work will be handled during your absence..." required></textarea>
                                    <div v-if="errors.details" class="text-danger small mt-1">{{ errors.details }}</div>
                                    <small class="text-muted">Minimum 10 characters required. Be specific about your reason for leave.</small>
                                </div>

                                <!-- Important Notice -->
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Important:</strong> 
                                    <ul class="mb-0 mt-2">
                                        <li>Leave requests must be submitted at least 3 days in advance for vacation leave</li>
                                        <li>Emergency and sick leave can be submitted with shorter notice</li>
                                        <li>All requests are subject to approval by HR/Admin</li>
                                        <li>You cannot edit or cancel requests once they are approved or rejected</li>
                                    </ul>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <Link :href="route('leave.index')" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </Link>
                                    <button type="submit" :disabled="!hasEnoughCredits || isSubmitting" class="btn btn-primary">
                                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                                        <i v-else class="bx bx-send me-1"></i>
                                        Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    leaveRules: Array,
    availableCredits: Object,
    errors: Object,
})



const form = useForm({
    leave_type: '',
    start_date: '',
    end_date: '',
    details: '',
})

const isSubmitting = ref(false)

// Computed properties
const selectedLeaveRule = computed(() => {
    if (!props.leaveRules || !form.leave_type) return null
    return props.leaveRules.find(rule => rule.leave_type === form.leave_type)
})

const daysRequested = computed(() => {
    if (!form.start_date || !form.end_date) return 0
    
    const start = new Date(form.start_date)
    const end = new Date(form.end_date)
    const diffTime = Math.abs(end - start)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays + 1
})

const hasEnoughCredits = computed(() => {
    if (!form.leave_type) return false
    const available = props.availableCredits[form.leave_type] || 0
    return available >= daysRequested.value
})

// Methods
const onLeaveTypeChange = () => {
    console.log('Leave type changed to:', form.leave_type)
    console.log('Available credits for this type:', props.availableCredits[form.leave_type])
    console.log('Selected rule:', selectedLeaveRule.value)
}

// Watch for date changes
watch([() => form.start_date, () => form.end_date], ([start, end]) => {
    if (start && end && new Date(start) > new Date(end)) {
        form.end_date = start
    }
})

const submitForm = () => {
    if (!hasEnoughCredits.value) {
        alert('You do not have enough credits for this request. Please select a different leave type or reduce the number of days.')
        return
    }

    if (form.details.length < 10) {
        alert('Please provide more detailed information about your leave request (minimum 10 characters).')
        return
    }

    isSubmitting.value = true
    form.post(route('leave.store'), {
        onSuccess: () => {
            isSubmitting.value = false
        },
        onError: () => {
            isSubmitting.value = false
        }
    })
}
</script>
