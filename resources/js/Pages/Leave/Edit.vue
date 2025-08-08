<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Edit Leave Request</h5>
                                <Link :href="isAdmin ? route('admin.leave.index') : route('leave.index')" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-arrow-back me-1"></i>Back to Leave Requests
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <!-- Employee Information (Admin Only) -->
                                <div v-if="isAdmin" class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Employee</label>
                                        <div class="p-3 bg-light rounded">
                                            <div class="small">
                                                <strong>{{ leaveRequest.user?.name }}</strong>
                                                <div class="text-muted">{{ leaveRequest.user?.email }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Leave Type and Days -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Leave Type</label>
                                            <select v-model="form.leave_type" class="form-control" required>
                                                <option value="">Select leave type</option>
                                                <option v-for="rule in leaveRules" :key="rule.leave_type" :value="rule.leave_type">
                                                    {{ rule.display_name }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.leave_type" class="text-danger small mt-1">{{ form.errors.leave_type }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Days Requested</label>
                                            <div class="p-3 bg-light rounded">
                                                <span class="badge bg-secondary">{{ daysRequested }} days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Range -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Start Date</label>
                                            <input v-model="form.start_date" type="date" class="form-control" required>
                                            <div v-if="form.errors.start_date" class="text-danger small mt-1">{{ form.errors.start_date }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">End Date</label>
                                            <input v-model="form.end_date" type="date" class="form-control" required>
                                            <div v-if="form.errors.end_date" class="text-danger small mt-1">{{ form.errors.end_date }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="mb-3">
                                    <label class="form-label">Request Details</label>
                                    <textarea v-model="form.details" rows="4" class="form-control" placeholder="Please provide detailed reason for the leave request..." required></textarea>
                                    <div v-if="form.errors.details" class="text-danger small mt-1">{{ form.errors.details }}</div>
                                </div>

                                <!-- Credit Information -->
                                <div v-if="selectedLeaveRule" class="mb-4 p-3 bg-info bg-opacity-10 rounded">
                                    <h6 class="text-info mb-3">Credit Information</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body p-2">
                                                    <div class="small text-muted">Total Credits</div>
                                                    <div class="h5 mb-0 text-info">{{ selectedLeaveRule.default_credits }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-success">
                                                <div class="card-body p-2">
                                                    <div class="small text-muted">Available Credits</div>
                                                    <div class="h5 mb-0 text-success">{{ availableCredits }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-warning">
                                                <div class="card-body p-2">
                                                    <div class="small text-muted">Requesting</div>
                                                    <div class="h5 mb-0 text-warning">{{ daysRequested }} days</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!hasEnoughCredits" class="mt-3 text-danger small">
                                        ⚠️ You don't have enough credits for this request.
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <Link :href="isAdmin ? route('admin.leave.index') : route('leave.index')" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </Link>
                                    <button type="submit" :disabled="!hasEnoughCredits" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Update Leave Request
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
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    leaveRequest: Object,
    leaveRules: Array,
    isAdmin: Boolean,
})

const form = useForm({
    leave_type: props.leaveRequest.leave_type,
    start_date: props.leaveRequest.start_date,
    end_date: props.leaveRequest.end_date,
    details: props.leaveRequest.details,
})

// Computed properties
const selectedLeaveRule = computed(() => {
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

const availableCredits = computed(() => {
    if (!selectedLeaveRule.value) return 0
    return selectedLeaveRule.value.default_credits
})

const hasEnoughCredits = computed(() => {
    return availableCredits.value >= daysRequested.value
})

// Watch for date changes to recalculate days
watch([() => form.start_date, () => form.end_date], () => {
    // Days will be recalculated automatically via computed property
})

const submitForm = () => {
    if (!hasEnoughCredits.value) {
        alert('You don\'t have enough credits for this request.')
        return
    }

    const routeName = props.isAdmin ? 'admin.leave.update' : 'leave.update'
    form.put(route(routeName, props.leaveRequest.id))
}
</script>
