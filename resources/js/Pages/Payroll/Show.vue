<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Payroll Details
                </h2>
                <SecondaryButton @click="goBack">
                    Back to Payroll
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Payroll Header -->
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Payroll for {{ payroll.user?.name }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                Period: {{ formatPeriod(payroll) }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Date Range: {{ formatDate(payroll.start_date) }} - {{ formatDate(payroll.end_date) }}
                            </p>
                        </div>

                        <!-- Payroll Details -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Employee Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-3">Employee Information</h4>
                                <div class="space-y-2">
                                    <p><span class="font-medium">Name:</span> {{ payroll.user?.name }}</p>
                                    <p><span class="font-medium">Email:</span> {{ payroll.user?.email }}</p>
                                    <p><span class="font-medium">Period:</span> {{ formatPeriod(payroll) }}</p>
                                    <p><span class="font-medium">Status:</span> 
                                        <span :class="getStatusClass(payroll.status)">
                                            {{ getStatusText(payroll.status) }}
                                        </span>
                                    </p>
                                    <p v-if="payroll.approved_by"><span class="font-medium">Approved By:</span> {{ payroll.approved_by?.name }}</p>
                                    <p v-if="payroll.approved_at"><span class="font-medium">Approved At:</span> {{ formatDateTime(payroll.approved_at) }}</p>
                                    <p v-if="payroll.payment_date"><span class="font-medium">Payment Date:</span> {{ formatDateTime(payroll.payment_date) }}</p>
                                </div>
                            </div>

                            <!-- Attendance Summary -->
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-900 mb-3">Attendance Summary</h4>
                                <div class="space-y-2">
                                    <p><span class="font-medium">Total Days Worked:</span> {{ payroll.total_days_worked }}</p>
                                    <p><span class="font-medium">Total Hours Worked:</span> {{ payroll.total_hours_worked }} hrs</p>
                                    <p><span class="font-medium">Overtime Hours:</span> {{ payroll.overtime_hours }} hrs</p>
                                    <p><span class="font-medium">Leave Days:</span> {{ payroll.leave_days }}</p>
                                    <p><span class="font-medium">Absent Days:</span> {{ payroll.absent_days }}</p>
                                </div>
                            </div>

                            <!-- Salary Breakdown -->
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-900 mb-3">Salary Breakdown</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span>Basic Salary:</span>
                                        <span class="font-medium">₱{{ formatNumber(payroll.basic_salary) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Gross Salary:</span>
                                        <span class="font-medium">₱{{ formatNumber(payroll.gross_salary) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Overtime Pay:</span>
                                        <span class="font-medium text-green-600">+₱{{ formatNumber(payroll.overtime_pay) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Allowances:</span>
                                        <span class="font-medium text-green-600">+₱{{ formatNumber(payroll.total_allowances) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Deductions:</span>
                                        <span class="font-medium text-red-600">-₱{{ formatNumber(payroll.total_deductions) }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span>Net Salary:</span>
                                        <span class="text-green-600">₱{{ formatNumber(payroll.net_salary) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Allowances -->
                        <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-3">Allowances Breakdown</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Housing Allowance:</p>
                                    <p class="font-semibold">₱{{ formatNumber(payroll.housing_allowance) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Transport Allowance:</p>
                                    <p class="font-semibold">₱{{ formatNumber(payroll.transport_allowance) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Meal Allowance:</p>
                                    <p class="font-semibold">₱{{ formatNumber(payroll.meal_allowance) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Other Allowance:</p>
                                    <p class="font-semibold">₱{{ formatNumber(payroll.other_allowance) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Deductions -->
                        <div class="mt-6 bg-red-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-red-900 mb-3">Deductions Breakdown</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">SSS:</p>
                                    <p class="font-semibold text-red-600">₱{{ formatNumber(payroll.sss_deduction) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">PhilHealth:</p>
                                    <p class="font-semibold text-red-600">₱{{ formatNumber(payroll.philhealth_deduction) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Pag-IBIG:</p>
                                    <p class="font-semibold text-red-600">₱{{ formatNumber(payroll.pagibig_deduction) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Advance:</p>
                                    <p class="font-semibold text-red-600">₱{{ formatNumber(payroll.advance_deduction) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Other Deductions:</p>
                                    <p class="font-semibold text-red-600">₱{{ formatNumber(payroll.other_deduction) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="payroll.notes" class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-2">Notes</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-700">{{ payroll.notes }}</p>
                            </div>
                        </div>

                        <!-- Admin Actions -->
                        <div v-if="isAdmin" class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">Admin Actions</h4>
                            <div class="flex space-x-3">
                                <PrimaryButton 
                                    @click="updateStatus"
                                    class="bg-blue-600 hover:bg-blue-700"
                                >
                                    Update Status
                                </PrimaryButton>
                                <SecondaryButton @click="sendEmail" class="bg-green-600 hover:bg-green-700 text-white">
                                    Send Email
                                </SecondaryButton>
                                <SecondaryButton @click="printPayroll">
                                    Print Payslip
                                </SecondaryButton>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <p>Created: {{ formatDateTime(payroll.created_at) }}</p>
                                <p>Last Updated: {{ formatDateTime(payroll.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
    payroll: Object,
    isAdmin: Boolean,
})

const goBack = () => {
    const route = props.isAdmin ? '/admin/payroll' : '/payroll'
    router.get(route)
}

const updateStatus = () => {
    const newStatus = prompt('Enter new status (draft/pending/approved/paid/cancelled):')
    if (newStatus && ['draft', 'pending', 'approved', 'paid', 'cancelled'].includes(newStatus)) {
        router.patch(`/admin/payroll/${props.payroll.id}/status`, { status: newStatus })
    }
}

const printPayroll = () => {
    window.print()
}

const sendEmail = () => {
    if (confirm('Are you sure you want to send the payroll statement to ' + props.payroll.user?.email + '?')) {
        router.post(`/admin/payroll/${props.payroll.id}/send-email`, {}, {
            onSuccess: () => {
                console.log('Email sent successfully')
            },
            onError: (errors) => {
                console.error('Error sending email:', errors)
            }
        })
    }
}

const formatPeriod = (payroll) => {
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ]
    const monthName = monthNames[payroll.month - 1]
    return `${monthName} ${payroll.year} - ${payroll.period}`
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatNumber = (number) => {
    return parseFloat(number).toLocaleString()
}

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString()
}

const getStatusClass = (status) => {
    const classes = {
        draft: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800',
        pending: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800',
        approved: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800',
        paid: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
        cancelled: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'
    }
    return classes[status] || classes.draft
}

const getStatusText = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1)
}
</script>
