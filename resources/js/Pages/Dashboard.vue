<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import { onMounted, ref } from "vue";
import axios from "axios";
import { formatMonth } from "@/functions";

const props = defineProps({
    isAdmin: Boolean,
    // Admin props
    totalApplicants: String,
    acceptedApplicants: String,
    rejectedApplicants: String,
    pendingApplicants: String,
    totalEmployees: String,
    totalAttendance: String,
    totalPayrolls: String,
    // Employee props
    todayAttendance: Object,
    recentAttendance: Array,
    recentPayrolls: Array,
    totalDaysWorked: Number,
    totalHoursWorked: Number,
});

const selectedMonth = ref("");
const selectedYear = ref("");
const isLoading = ref(false);

const lineGraphData = ref([]);
const years = ref([]);

const getLineGraphData = async () => {
    if (!props.isAdmin) return; // Only for admin
    
    isLoading.value = true;
    try {
        const response = await axios.get(
            route("dashboard.lineGraph", {
                year: selectedYear.value,
                month: selectedMonth.value,
            })
        );
        lineGraphData.value = response.data;
        isLoading.value = false;
        await renderLineChart();
    } catch (error) {
        console.error(error);
    }
};

const getYears = async () => {
    if (!props.isAdmin) return; // Only for admin
    
    try {
        const response = await axios.get(route("dashboard.years"));
        years.value = response.data;
        selectedYear.value = years.value[0].year;
        await getLineGraphData();
    } catch (error) {
        console.error(error);
    }
};

let chartInstance = null;

const renderLineChart = () => {
    const ctx = document.getElementById("myChart").getContext("2d");

    // Destroy the existing chart instance if it exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Extract month and count data from lineGraphData
    const labels = lineGraphData.value.map((data) =>
        data.month ? formatMonth(data.month) : `Day ${data.day}`
    );
    const dataCounts = lineGraphData.value.map((data) => data.count);

    // Create a new chart instance and store it in chartInstance
    chartInstance = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Applicants",
                    data: dataCounts,
                    fill: false,
                    borderColor: "rgb(75, 192, 192)",
                    tension: 0.1,
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
};

onMounted(() => {
    if (props.isAdmin) {
        getYears();
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Admin Dashboard -->
        <div v-if="isAdmin" class="container-fluid p-2">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-user"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Total Applicants</h6>
                                <h3 class="mb-0">{{ totalApplicants }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-user-check"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Accepted</h6>
                                <h3 class="mb-0">{{ acceptedApplicants }}</h3>
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
                                <h3 class="mb-0">{{ pendingApplicants }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-user-x"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Rejected</h6>
                                <h3 class="mb-0">{{ rejectedApplicants }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-group"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Employees</h6>
                                <h3 class="mb-0">{{ totalEmployees }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-time"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Attendance Records</h6>
                                <h3 class="mb-0">{{ totalAttendance }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px">
                                <i class="bx bxs-dollar-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Payroll Records</h6>
                                <h3 class="mb-0">{{ totalPayrolls }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Chart -->
            <div v-if="selectedYear != ''" class="mt-4">
                <div class="p-3">
                    <select v-model="selectedYear" @change="getLineGraphData" class="form-select mb-2">
                        <option v-for="year in years" :key="year" :value="year.year">{{ year.year }}</option>
                    </select>
                    <select @change="getLineGraphData" v-model="selectedMonth" class="form-select">
                        <option value="" selected>Select month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <div class="d-flex justify-content-center align-items-center flex-column">
                    <div v-if="isLoading" class="mb-3">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Employee Dashboard -->
        <div v-else class="container-fluid p-2">
            <div class="row g-4">
                <!-- Today's Status -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <h5 class="card-title mb-3">Today's Status</h5>
                        <div v-if="todayAttendance" class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px">
                                <i class="bx bxs-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Clocked In</h6>
                                <p class="mb-0 text-muted">{{ todayAttendance.clock_in ? new Date(todayAttendance.clock_in).toLocaleTimeString() : 'Not clocked in' }}</p>
                            </div>
                        </div>
                        <div v-else class="d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px">
                                <i class="bx bxs-time"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Not Clocked In</h6>
                                <p class="mb-0 text-muted">Please clock in for today</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <h5 class="card-title mb-3">Work Summary</h5>
                        <div class="row">
                            <div class="col-6">
                                <h4 class="text-primary">{{ totalDaysWorked }}</h4>
                                <small class="text-muted">Days Worked</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ totalHoursWorked }}</h4>
                                <small class="text-muted">Hours Worked</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <h5 class="card-title mb-3">Recent Attendance</h5>
                        <div v-if="recentAttendance.length > 0">
                            <div v-for="attendance in recentAttendance" :key="attendance.id" class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ new Date(attendance.date).toLocaleDateString() }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ attendance.clock_in ? new Date(attendance.clock_in).toLocaleTimeString() : 'Not clocked in' }} - 
                                        {{ attendance.clock_out ? new Date(attendance.clock_out).toLocaleTimeString() : 'Not clocked out' }}
                                    </small>
                                </div>
                                <span :class="attendance.clock_out ? 'badge bg-success' : 'badge bg-warning'">
                                    {{ attendance.clock_out ? 'Completed' : 'In Progress' }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="text-center text-muted">
                            <i class="bx bxs-time mb-2" style="font-size: 2rem;"></i>
                            <p>No recent attendance records</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Payroll -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <h5 class="card-title mb-3">Recent Payroll</h5>
                        <div v-if="recentPayrolls.length > 0">
                            <div v-for="payroll in recentPayrolls" :key="payroll.id" class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ payroll.period_display }}</strong>
                                    <br>
                                    <small class="text-muted">Net: ₱{{ parseFloat(payroll.net_salary).toLocaleString() }}</small>
                                </div>
                                <span :class="'badge bg-' + (payroll.status === 'paid' ? 'success' : payroll.status === 'approved' ? 'primary' : 'warning')">
                                    {{ payroll.status.charAt(0).toUpperCase() + payroll.status.slice(1) }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="text-center text-muted">
                            <i class="bx bxs-dollar-circle mb-2" style="font-size: 2rem;"></i>
                            <p>No recent payroll records</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
