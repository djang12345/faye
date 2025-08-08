<template>
    <AuthenticatedLayout>
        <div class="container-fluid p-2">
            <div class="row g-4">
                <!-- Clock In/Out Section for Employees -->
                <div v-if="!isAdmin" class="col-md-12">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-0">Today's Attendance</h5>
                                <p class="text-muted mb-0">Manage your daily attendance</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button 
                                    @click="clockIn" 
                                    :disabled="!canClockIn"
                                    class="btn btn-success"
                                    :class="{ 'opacity-50': !canClockIn }"
                                >
                                    <i class="bx bx-log-in me-2"></i>Clock In
                                </button>
                                <button 
                                    @click="clockOut" 
                                    :disabled="!canClockOut"
                                    class="btn btn-danger"
                                    :class="{ 'opacity-50': !canClockOut }"
                                >
                                    <i class="bx bx-log-out me-2"></i>Clock Out
                                </button>
                            </div>
                        </div>
                        <div v-if="todayAttendance" class="mt-3 p-3 bg-white rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Date:</strong> {{ formatDate(todayAttendance.date) }}
                                </div>
                                <div class="col-md-4" v-if="todayAttendance.clock_in">
                                    <strong>Clock In:</strong> {{ formatTime(todayAttendance.clock_in) }}
                                </div>
                                <div class="col-md-4" v-if="todayAttendance.clock_out">
                                    <strong>Clock Out:</strong> {{ formatTime(todayAttendance.clock_out) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-time"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ isAdmin ? 'Total Records' : 'My Records' }}</h6>
                                <h3 class="mb-0">{{ attendances.total }}</h3>
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
                                <h6 class="mb-0">{{ isAdmin ? 'Completed' : 'My Completed' }}</h6>
                                <h3 class="mb-0">{{ completedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-time-five"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ isAdmin ? 'In Progress' : 'My In Progress' }}</h6>
                                <h3 class="mb-0">{{ inProgressCount }}</h3>
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
                                <h6 class="mb-0">{{ isAdmin ? 'This Month' : 'My This Month' }}</h6>
                                <h3 class="mb-0">{{ thisMonthCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Records Table -->
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ isAdmin ? 'All Attendance Records' : 'My Attendance Records' }}</h5>
                                <button class="btn btn-primary btn-sm" @click="showAddAttendanceModal" v-if="isAdmin">
                                    <i class="bx bx-plus me-1"></i>Add Record
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Date</th>
                                            <th v-if="isAdmin" class="border-0">Employee</th>
                                            <th class="border-0">Clock In</th>
                                            <th class="border-0">Clock Out</th>
                                            <th class="border-0">Hours</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">{{ isAdmin ? 'Actions' : 'View' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="attendance in attendances.data" :key="attendance.id">
                                            <td>{{ formatDate(attendance.date) }}</td>
                                            <td v-if="isAdmin">{{ attendance.user?.name }}</td>
                                            <td>{{ attendance.clock_in ? formatTime(attendance.clock_in) : '-' }}</td>
                                            <td>{{ attendance.clock_out ? formatTime(attendance.clock_out) : '-' }}</td>
                                            <td>{{ calculateHours(attendance.clock_in, attendance.clock_out) }}</td>
                                            <td>
                                                <span :class="getStatusClass(attendance)">
                                                    {{ getStatusText(attendance) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" @click="viewAttendance(attendance)">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button v-if="isAdmin" class="btn btn-sm btn-outline-warning me-1" @click="editAttendance(attendance)">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button v-if="isAdmin" class="btn btn-sm btn-outline-danger" @click="deleteAttendance(attendance)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <Pagination :paginate="attendances" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Attendance Modal -->
        <div v-if="showViewModal" class="modal-overlay" @click="showViewModal = false">
            <div class="modal-container" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Attendance Details</h5>
                        <button type="button" class="btn-close" @click="showViewModal = false"></button>
                    </div>
                    <div class="modal-body" v-if="selectedAttendance">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Date:</strong> {{ formatDate(selectedAttendance.date) }}
                            </div>
                            <div class="col-md-6" v-if="isAdmin">
                                <strong>Employee:</strong> {{ selectedAttendance.user?.name }}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Clock In:</strong> {{ selectedAttendance.clock_in ? formatTime(selectedAttendance.clock_in) : 'Not clocked in' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Clock Out:</strong> {{ selectedAttendance.clock_out ? formatTime(selectedAttendance.clock_out) : 'Not clocked out' }}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Hours Worked:</strong> {{ calculateHours(selectedAttendance.clock_in, selectedAttendance.clock_out) }}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong> 
                                <span :class="getStatusClass(selectedAttendance)">
                                    {{ getStatusText(selectedAttendance) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-3" v-if="selectedAttendance.notes">
                            <div class="col-12">
                                <strong>Notes:</strong> {{ selectedAttendance.notes }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showViewModal = false">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Attendance Modal -->
        <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
            <div class="modal-container" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attendance</h5>
                        <button type="button" class="btn-close" @click="showEditModal = false"></button>
                    </div>
                    <div class="modal-body" v-if="editingAttendance">
                        <form @submit.prevent="updateAttendance">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" v-model="editingAttendance.date" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Clock In</label>
                                <input type="time" class="form-control" v-model="editingAttendance.clock_in">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Clock Out</label>
                                <input type="time" class="form-control" v-model="editingAttendance.clock_out">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" v-model="editingAttendance.notes" rows="3"></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Attendance Modal -->
        <div v-if="showAddModal && isAdmin" class="modal-overlay" @click="showAddModal = false">
            <div class="modal-container" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Attendance Record</h5>
                        <button type="button" class="btn-close" @click="showAddModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="createAttendance">
                            <div class="mb-3">
                                <label class="form-label">Employee</label>
                                <select class="form-control" v-model="newAttendanceForm.user_id" required>
                                    <option value="">Select an employee...</option>
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                        {{ employee.name }} ({{ employee.email }})
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" v-model="newAttendanceForm.date" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Clock In</label>
                                <input type="time" class="form-control" v-model="newAttendanceForm.clock_in">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Clock Out</label>
                                <input type="time" class="form-control" v-model="newAttendanceForm.clock_out">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" v-model="newAttendanceForm.notes" rows="3"></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" @click="showAddModal = false">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    attendances: Object,
    isAdmin: Boolean,
    employees: Array,
})

const todayAttendance = ref(null)
const canClockIn = ref(true)
const canClockOut = ref(false)
const selectedAttendance = ref(null)
const editingAttendance = ref(null)
const showAddModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)

// Form for adding new attendance record
const newAttendanceForm = ref({
    user_id: '',
    date: new Date().toISOString().split('T')[0],
    clock_in: '',
    clock_out: '',
    notes: ''
})

// Computed properties for statistics
const completedCount = computed(() => {
    return props.attendances.data.filter(a => a.clock_in && a.clock_out).length
})

const inProgressCount = computed(() => {
    return props.attendances.data.filter(a => a.clock_in && !a.clock_out).length
})

const thisMonthCount = computed(() => {
    const currentMonth = new Date().getMonth()
    const currentYear = new Date().getFullYear()
    return props.attendances.data.filter(a => {
        const date = new Date(a.date)
        return date.getMonth() === currentMonth && date.getFullYear() === currentYear
    }).length
})

onMounted(() => {
    if (!props.isAdmin) {
        loadTodayAttendance()
    }
})

const loadTodayAttendance = async () => {
    try {
        const route = props.isAdmin ? '/admin/attendance/today' : '/attendance/today'
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        
        const response = await fetch(route, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
            },
            credentials: 'same-origin'
        })
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        
        todayAttendance.value = data.attendance
        canClockIn.value = data.canClockIn
        canClockOut.value = data.canClockOut
    } catch (error) {
        console.error('Error loading today\'s attendance:', error)
        // Set default values if there's an error
        canClockIn.value = true
        canClockOut.value = false
    }
}

const clockIn = () => {
    const route = props.isAdmin ? '/admin/attendance/clock-in' : '/attendance/clock-in'
    router.post(route, {}, {
        onSuccess: () => {
            loadTodayAttendance()
        }
    })
}

const clockOut = () => {
    const route = props.isAdmin ? '/admin/attendance/clock-out' : '/attendance/clock-out'
    router.post(route, {}, {
        onSuccess: () => {
            loadTodayAttendance()
        },
        onError: (errors) => {
            console.error('Clock out error:', errors)
        }
    })
}

const viewAttendance = (attendance) => {
    selectedAttendance.value = attendance
    showViewModal.value = true
}

const editAttendance = (attendance) => {
    // Extract time from attendance record
    const getTimeString = (timeValue) => {
        if (!timeValue) return ''
        
        // If it's already a string in HH:MM format
        if (typeof timeValue === 'string' && timeValue.match(/^\d{2}:\d{2}$/)) {
            return timeValue
        }
        
        // If it's a string in HH:MM:SS format
        if (typeof timeValue === 'string' && timeValue.match(/^\d{2}:\d{2}:\d{2}$/)) {
            return timeValue.substring(0, 5)
        }
        
        // If it's a Date object
        if (timeValue instanceof Date) {
            return timeValue.toTimeString().slice(0, 5)
        }
        
        // Try to parse as Date
        try {
            const date = new Date(timeValue)
            if (!isNaN(date.getTime())) {
                return date.toTimeString().slice(0, 5)
            }
        } catch (e) {
            console.error('Error parsing time for edit:', timeValue, e)
        }
        
        return ''
    }
    
    editingAttendance.value = { 
        ...attendance,
        clock_in: getTimeString(attendance.clock_in),
        clock_out: getTimeString(attendance.clock_out)
    }
    showEditModal.value = true
}

const updateAttendance = () => {
    // Check if we're on the admin page by looking at the URL
    const isAdminPage = window.location.pathname.includes('/admin/attendance')
    const route = isAdminPage ? `/admin/attendance/${editingAttendance.value.id}` : `/attendance/${editingAttendance.value.id}`
    
    console.log('Updating attendance with route:', route)
    
    router.put(route, editingAttendance.value, {
        onSuccess: () => {
            console.log('Attendance updated successfully')
            showEditModal.value = false
        },
        onError: (errors) => {
            console.error('Error updating attendance:', errors)
        }
    })
}

const showAddAttendanceModal = () => {
    showAddModal.value = true
    // Reset form
    newAttendanceForm.value = {
        user_id: '',
        date: new Date().toISOString().split('T')[0],
        clock_in: '',
        clock_out: '',
        notes: ''
    }
}

const createAttendance = () => {
    if (!newAttendanceForm.value.user_id) {
        alert('Please select an employee.')
        return
    }

    if (!newAttendanceForm.value.date) {
        alert('Please select a date.')
        return
    }

    // Check if we're on the admin page by looking at the URL
    const isAdminPage = window.location.pathname.includes('/admin/attendance')
    const route = isAdminPage ? '/admin/attendance' : '/attendance'
    
    console.log('Creating attendance with route:', route)
    console.log('Is admin page:', isAdminPage)
    console.log('Form data:', newAttendanceForm.value)
    
    router.post(route, newAttendanceForm.value, {
        onSuccess: () => {
            console.log('Attendance created successfully')
            showAddModal.value = false
            newAttendanceForm.value = {
                user_id: '',
                date: new Date().toISOString().split('T')[0],
                clock_in: '',
                clock_out: '',
                notes: ''
            }
        },
        onError: (errors) => {
            console.error('Error creating attendance:', errors)
        }
    })
}

const deleteAttendance = (attendance) => {
    if (confirm('Are you sure you want to delete this attendance record?')) {
        // Check if we're on the admin page by looking at the URL
        const isAdminPage = window.location.pathname.includes('/admin/attendance')
        const route = isAdminPage ? `/admin/attendance/${attendance.id}` : `/attendance/${attendance.id}`
        
        console.log('Deleting attendance with route:', route)
        
        router.delete(route, {
            onSuccess: () => {
                console.log('Attendance deleted successfully')
                // The page will automatically refresh with the updated data
            },
            onError: (errors) => {
                console.error('Error deleting attendance:', errors)
            }
        })
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const formatTime = (time) => {
    if (!time) return '-'
    
    // If time is already a string in HH:MM format, return it directly
    if (typeof time === 'string' && time.match(/^\d{2}:\d{2}$/)) {
        return time
    }
    
    // If time is a string in HH:MM:SS format, format it
    if (typeof time === 'string' && time.match(/^\d{2}:\d{2}:\d{2}$/)) {
        return time.substring(0, 5) // Return HH:MM
    }
    
    // If time is a Date object, format it
    if (time instanceof Date) {
        return time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }
    
    // Try to parse as Date if it's a datetime string
    try {
        const date = new Date(time)
        if (!isNaN(date.getTime())) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }
    } catch (e) {
        console.error('Error parsing time:', time, e)
    }
    
    return time || '-'
}

const calculateHours = (clockIn, clockOut) => {
    if (!clockIn || !clockOut) return '-'
    
    // Convert time strings to minutes for calculation
    const timeToMinutes = (timeStr) => {
        if (!timeStr) return 0
        
        // Handle different time formats
        let time = timeStr
        if (typeof time === 'string') {
            // If it's HH:MM:SS, take only HH:MM
            if (time.match(/^\d{2}:\d{2}:\d{2}$/)) {
                time = time.substring(0, 5)
            }
            // If it's HH:MM, use as is
            if (time.match(/^\d{2}:\d{2}$/)) {
                const [hours, minutes] = time.split(':').map(Number)
                return hours * 60 + minutes
            }
        }
        
        // Try to parse as Date if it's a datetime
        try {
            const date = new Date(time)
            if (!isNaN(date.getTime())) {
                return date.getHours() * 60 + date.getMinutes()
            }
        } catch (e) {
            console.error('Error parsing time for hours calculation:', time, e)
        }
        
        return 0
    }
    
    const startMinutes = timeToMinutes(clockIn)
    const endMinutes = timeToMinutes(clockOut)
    
    if (startMinutes === 0 || endMinutes === 0) return '-'
    
    const diffMinutes = endMinutes - startMinutes
    const hours = diffMinutes / 60
    
    return hours.toFixed(2) + ' hrs'
}

const getStatusClass = (attendance) => {
    if (!attendance.clock_in) return 'badge bg-secondary'
    if (!attendance.clock_out) return 'badge bg-warning'
    return 'badge bg-success'
}

const getStatusText = (attendance) => {
    if (!attendance.clock_in) return 'Not Clocked In'
    if (!attendance.clock_out) return 'Clocked In'
    return 'Completed'
}
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
