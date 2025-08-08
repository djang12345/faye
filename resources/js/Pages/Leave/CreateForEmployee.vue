<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Create Leave Request for Employee</h5>
                                <Link :href="route('admin.leave.index')" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-arrow-back me-1"></i>Back to Leave Management
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <div class="row">
                                    <!-- Employee Selection -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Employee</label>
                                            <select v-model="form.user_id" @change="onEmployeeChange" class="form-control" required>
                                                <option value="">Select employee</option>
                                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                                    {{ employee.name }} ({{ employee.email }})
                                                </option>
                                            </select>
                                            <div v-if="form.errors.user_id" class="text-danger small mt-1">{{ form.errors.user_id }}</div>
                                        </div>
                                    </div>

                                    <!-- Leave Type Selection -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Leave Type</label>
                                            <select v-model="form.leave_type" @change="onLeaveTypeChange" class="form-control" required>
                                                <option value="">Select leave type</option>
                                                <option v-for="rule in leaveRules" :key="rule.leave_type" :value="rule.leave_type">
                                                    {{ rule.display_name }} 
                                                    <span v-if="selectedEmployeeCredits && selectedEmployeeCredits[rule.leave_type]">
                                                        ({{ selectedEmployeeCredits[rule.leave_type].available_credits }} credits left)
                                                    </span>
                                                </option>
                                            </select>
                                            <div v-if="form.errors.leave_type" class="text-danger small mt-1">{{ form.errors.leave_type }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employee Credits Display -->
                                <div v-if="selectedEmployeeCredits" class="mb-4 p-3 bg-info bg-opacity-10 rounded">
                                    <h6 class="text-info mb-3">Available Leave Credits</h6>
                                    <div class="row">
                                        <div v-for="(credits, leaveType) in selectedEmployeeCredits" :key="leaveType" class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body p-3">
                                                    <div class="small font-weight-bold">{{ getLeaveTypeDisplayName(leaveType) }}</div>
                                                    <div class="small mt-2">
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

                                <!-- Days Requested Display -->
                                <div v-if="daysRequested > 0" class="mb-3">
                                    <label class="form-label">Days Requested</label>
                                    <div class="p-3 bg-light rounded">
                                        <span class="badge bg-secondary">{{ daysRequested }} days</span>
                                        <span v-if="selectedLeaveTypeCredits" class="ms-2 small">
                                            Available credits: {{ selectedLeaveTypeCredits.available_credits }}
                                            <span v-if="daysRequested > selectedLeaveTypeCredits.available_credits" class="text-danger">
                                                ⚠️ Insufficient credits
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="mb-3">
                                    <label class="form-label">Request Details</label>
                                    <textarea v-model="form.details" rows="4" class="form-control" placeholder="Please provide detailed reason for the leave request..." required></textarea>
                                    <div v-if="form.errors.details" class="text-danger small mt-1">{{ form.errors.details }}</div>
                                </div>

                                <!-- Status and Notes -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select v-model="form.status" class="form-control" required>
                                                <option value="pending">⏳ Pending</option>
                                                <option value="approved">✅ Approved</option>
                                                <option value="rejected">❌ Rejected</option>
                                            </select>
                                            <div v-if="form.errors.status" class="text-danger small mt-1">{{ form.errors.status }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Admin Notes (Optional)</label>
                                            <textarea v-model="form.admin_notes" rows="2" class="form-control" placeholder="Add any admin notes..."></textarea>
                                            <div v-if="form.errors.admin_notes" class="text-danger small mt-1">{{ form.errors.admin_notes }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <Link :href="route('admin.leave.index')" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </Link>
                                    <button type="submit" :disabled="!hasEnoughCredits" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>Create Leave Request
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
    employees: Array,
    leaveRules: Array,
    employeeCredits: Object,
})

const form = useForm({
    user_id: '',
    leave_type: '',
    start_date: '',
    end_date: '',
    details: '',
    status: 'pending',
    admin_notes: ''
})

// Computed properties
const selectedEmployeeCredits = computed(() => {
    if (!form.user_id || !props.employeeCredits[form.user_id]) return null
    return props.employeeCredits[form.user_id]
})

const selectedLeaveTypeCredits = computed(() => {
    if (!selectedEmployeeCredits.value || !form.leave_type) return null
    return selectedEmployeeCredits.value[form.leave_type]
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
    if (!selectedLeaveTypeCredits.value) return true // Allow if no credits info
    return selectedLeaveTypeCredits.value.available_credits >= daysRequested.value
})

// Methods
const getLeaveTypeDisplayName = (leaveType) => {
    const rule = props.leaveRules.find(r => r.leave_type === leaveType)
    return rule ? rule.display_name : leaveType
}

const onEmployeeChange = () => {
    form.leave_type = '' // Reset leave type when employee changes
}

const onLeaveTypeChange = () => {
    // Leave type changed, credits will update automatically
}

// Watch for date changes
watch([() => form.start_date, () => form.end_date], () => {
    // Days will be recalculated automatically via computed property
})

const submitForm = () => {
    if (!hasEnoughCredits.value) {
        alert('The employee doesn\'t have enough credits for this request.')
        return
    }

    form.post(route('admin.leave.store'))
}
</script>
