<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Payroll Record
                </h2>
                <Link :href="route('admin.payroll.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <i class="bx bx-arrow-back mr-1"></i>Back to Payroll
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Employee Information (Read-only) -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="bx bx-user mr-2 text-blue-600"></i>
                                    Employee Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ payroll.user?.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Employee Email</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ payroll.user?.email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pay Period -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="bx bx-calendar mr-2 text-blue-600"></i>
                                    Pay Period Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel for="start_date" value="Start Date" />
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="bx bx-calendar text-gray-400"></i>
                                            </div>
                                            <input
                                                id="start_date"
                                                type="date"
                                                class="mt-1 block w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                :class="{ 'border-red-500': form.errors.start_date }"
                                                v-model="form.start_date"
                                                required
                                                @change="calculateWorkingDays"
                                                :max="form.end_date || ''"
                                            />
                                        </div>
                                        <InputError :message="form.errors.start_date" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="end_date" value="End Date" />
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="bx bx-calendar text-gray-400"></i>
                                            </div>
                                            <input
                                                id="end_date"
                                                type="date"
                                                class="mt-1 block w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                :class="{ 'border-red-500': form.errors.end_date }"
                                                v-model="form.end_date"
                                                required
                                                @change="calculateWorkingDays"
                                                :min="form.start_date || ''"
                                            />
                                        </div>
                                        <InputError :message="form.errors.end_date" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="period" value="Pay Period" />
                                        <select 
                                            id="period"
                                            v-model="form.period"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            :class="{ 'border-red-500': form.errors.period }"
                                            required
                                        >
                                            <option value="">Choose period...</option>
                                            <option value="1st Half">1st Half (1-15)</option>
                                            <option value="2nd Half">2nd Half (16-31)</option>
                                        </select>
                                        <InputError :message="form.errors.period" class="mt-2" />
                                    </div>
                                </div>
                                <div v-if="workingDays > 0" class="mt-3 bg-blue-100 p-3 rounded-lg">
                                    <div class="flex items-center text-blue-800">
                                        <i class="bx bx-calculator mr-2"></i>
                                        <span class="font-semibold">Calculated:</span>
                                        <span class="ml-2">{{ workingDays }} working days ({{ workingDays * 8 }} hours)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Salary -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="basic_salary" value="Monthly Basic Salary" class="flex items-center">
                                        <i class="bx bx-money mr-2 text-green-600"></i>
                                        Basic Salary
                                        <span class="text-red-500 ml-1">*</span>
                                    </InputLabel>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₱</span>
                                        </div>
                                        <TextInput
                                            id="basic_salary"
                                            type="number"
                                            step="0.01"
                                            class="pl-8 block w-full"
                                            :class="{ 'border-red-500': form.errors.basic_salary }"
                                            v-model="form.basic_salary"
                                            required
                                            @input="calculateSalary"
                                        />
                                    </div>
                                    <InputError :message="form.errors.basic_salary" class="mt-2" />
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h4 class="text-green-800 font-semibold mb-2 flex items-center">
                                        <i class="bx bx-calculator mr-2"></i>
                                        Salary Breakdown
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Monthly:</p>
                                            <p class="font-semibold text-green-700">₱{{ formatNumber(basicSalary) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Half Month:</p>
                                            <p class="font-semibold text-blue-600">₱{{ formatNumber(grossSalary) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Information -->
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-yellow-900 mb-4 flex items-center">
                                    <i class="bx bx-time mr-2"></i>
                                    Attendance Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel for="overtime_hours" value="Overtime Hours" class="flex items-center">
                                            <i class="bx bx-plus-circle mr-2 text-yellow-600"></i>
                                            Overtime Hours
                                        </InputLabel>
                                        <TextInput
                                            id="overtime_hours"
                                            type="number"
                                            step="0.5"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': form.errors.overtime_hours }"
                                            v-model="form.overtime_hours"
                                            @input="calculateSalary"
                                        />
                                        <InputError :message="form.errors.overtime_hours" class="mt-2" />
                                        <p class="mt-1 text-sm text-gray-500">Extra hours worked (1.25x rate)</p>
                                    </div>
                                    <div>
                                        <InputLabel for="leave_days" value="Leave Days" class="flex items-center">
                                            <i class="bx bx-calendar-minus mr-2 text-blue-600"></i>
                                            Leave Days
                                        </InputLabel>
                                        <TextInput
                                            id="leave_days"
                                            type="number"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': form.errors.leave_days }"
                                            v-model="form.leave_days"
                                        />
                                        <InputError :message="form.errors.leave_days" class="mt-2" />
                                        <p class="mt-1 text-sm text-gray-500">Days on leave (paid)</p>
                                    </div>
                                    <div>
                                        <InputLabel for="absent_days" value="Absent Days" class="flex items-center">
                                            <i class="bx bx-calendar-x mr-2 text-red-600"></i>
                                            Absent Days
                                        </InputLabel>
                                        <TextInput
                                            id="absent_days"
                                            type="number"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': form.errors.absent_days }"
                                            v-model="form.absent_days"
                                        />
                                        <InputError :message="form.errors.absent_days" class="mt-2" />
                                        <p class="mt-1 text-sm text-gray-500">Unpaid absences</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Allowances -->
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                                    <i class="bx bx-plus-circle mr-2"></i>
                                    Allowances & Benefits
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="housing_allowance" value="Housing Allowance" class="flex items-center">
                                            <i class="bx bx-home mr-2 text-green-600"></i>
                                            Housing Allowance
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="housing_allowance"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.housing_allowance }"
                                                v-model="form.housing_allowance"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.housing_allowance" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="transport_allowance" value="Transport Allowance" class="flex items-center">
                                            <i class="bx bx-car mr-2 text-blue-600"></i>
                                            Transport Allowance
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="transport_allowance"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.transport_allowance }"
                                                v-model="form.transport_allowance"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.transport_allowance" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="meal_allowance" value="Meal Allowance" class="flex items-center">
                                            <i class="bx bx-restaurant mr-2 text-yellow-600"></i>
                                            Meal Allowance
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="meal_allowance"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.meal_allowance }"
                                                v-model="form.meal_allowance"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.meal_allowance" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="other_allowance" value="Other Allowance" class="flex items-center">
                                            <i class="bx bx-gift mr-2 text-purple-600"></i>
                                            Other Allowance
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="other_allowance"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.other_allowance }"
                                                v-model="form.other_allowance"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.other_allowance" class="mt-2" />
                                    </div>
                                </div>
                                <div v-if="totalAllowances > 0" class="mt-4 bg-green-100 p-3 rounded-lg">
                                    <div class="flex items-center text-green-800">
                                        <i class="bx bx-calculator mr-2"></i>
                                        <span class="font-semibold">Total Allowances:</span>
                                        <span class="ml-2">₱{{ formatNumber(totalAllowances) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Deductions -->
                            <div class="bg-red-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-red-900 mb-4 flex items-center">
                                    <i class="bx bx-minus-circle mr-2"></i>
                                    Deductions & Taxes
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="sss_deduction" value="SSS Contribution" class="flex items-center">
                                            <i class="bx bx-shield mr-2 text-blue-600"></i>
                                            SSS Contribution
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="sss_deduction"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.sss_deduction }"
                                                v-model="form.sss_deduction"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.sss_deduction" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="philhealth_deduction" value="PhilHealth" class="flex items-center">
                                            <i class="bx bx-plus-medical mr-2 text-blue-600"></i>
                                            PhilHealth
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="philhealth_deduction"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.philhealth_deduction }"
                                                v-model="form.philhealth_deduction"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.philhealth_deduction" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="pagibig_deduction" value="Pag-IBIG" class="flex items-center">
                                            <i class="bx bx-home-heart mr-2 text-yellow-600"></i>
                                            Pag-IBIG
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="pagibig_deduction"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.pagibig_deduction }"
                                                v-model="form.pagibig_deduction"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.pagibig_deduction" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="advance_deduction" value="Salary Advance" class="flex items-center">
                                            <i class="bx bx-money-withdraw mr-2 text-red-600"></i>
                                            Salary Advance
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="advance_deduction"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.advance_deduction }"
                                                v-model="form.advance_deduction"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.advance_deduction" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="other_deduction" value="Other Deductions" class="flex items-center">
                                            <i class="bx bx-minus-circle mr-2 text-gray-600"></i>
                                            Other Deductions
                                        </InputLabel>
                                        <div class="mt-1 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <TextInput
                                                id="other_deduction"
                                                type="number"
                                                step="0.01"
                                                class="pl-8 block w-full"
                                                :class="{ 'border-red-500': form.errors.other_deduction }"
                                                v-model="form.other_deduction"
                                                @input="calculateSalary"
                                            />
                                        </div>
                                        <InputError :message="form.errors.other_deduction" class="mt-2" />
                                    </div>
                                </div>
                                <div v-if="totalDeductions > 0" class="mt-4 bg-red-100 p-3 rounded-lg">
                                    <div class="flex items-center text-red-800">
                                        <i class="bx bx-calculator mr-2"></i>
                                        <span class="font-semibold">Total Deductions:</span>
                                        <span class="ml-2">₱{{ formatNumber(totalDeductions) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status and Notes -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="status" value="Status" class="flex items-center">
                                        <i class="bx bx-check-circle mr-2 text-blue-600"></i>
                                        Payroll Status
                                    </InputLabel>
                                    <select 
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        :class="{ 'border-red-500': form.errors.status }"
                                        required
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="paid">Paid</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="notes" value="Additional Notes" class="flex items-center">
                                        <i class="bx bx-note mr-2 text-blue-600"></i>
                                        Notes
                                    </InputLabel>
                                    <textarea
                                        id="notes"
                                        v-model="form.notes"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        :class="{ 'border-red-500': form.errors.notes }"
                                        placeholder="Enter any additional notes or comments..."
                                    ></textarea>
                                    <InputError :message="form.errors.notes" class="mt-2" />
                                </div>
                            </div>

                            <!-- Salary Summary -->
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                                    <i class="bx bx-calculator mr-2"></i>
                                    Salary Summary
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-green-100 p-4 rounded-lg">
                                        <h6 class="text-green-800 font-semibold mb-3 flex items-center">
                                            <i class="bx bx-plus-circle mr-2"></i>
                                            Earnings
                                        </h6>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">Gross Salary:</span>
                                                <strong class="text-green-700">₱{{ formatNumber(grossSalary) }}</strong>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">Overtime Pay:</span>
                                                <strong class="text-green-700">₱{{ formatNumber(overtimePay) }}</strong>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">Total Allowances:</span>
                                                <strong class="text-green-700">₱{{ formatNumber(totalAllowances) }}</strong>
                                            </div>
                                            <hr class="my-2">
                                            <div class="flex justify-between font-bold">
                                                <span class="text-gray-700">Total Earnings:</span>
                                                <strong class="text-green-700">₱{{ formatNumber(grossSalary + overtimePay + totalAllowances) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-red-100 p-4 rounded-lg">
                                        <h6 class="text-red-800 font-semibold mb-3 flex items-center">
                                            <i class="bx bx-minus-circle mr-2"></i>
                                            Deductions
                                        </h6>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">SSS:</span>
                                                <strong class="text-red-700">₱{{ formatNumber(parseFloat(form.sss_deduction) || 0) }}</strong>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">PhilHealth:</span>
                                                <strong class="text-red-700">₱{{ formatNumber(parseFloat(form.philhealth_deduction) || 0) }}</strong>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">Pag-IBIG:</span>
                                                <strong class="text-red-700">₱{{ formatNumber(parseFloat(form.pagibig_deduction) || 0) }}</strong>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-700">Other Deductions:</span>
                                                <strong class="text-red-700">₱{{ formatNumber(parseFloat(form.advance_deduction) + parseFloat(form.other_deduction) || 0) }}</strong>
                                            </div>
                                            <hr class="my-2">
                                            <div class="flex justify-between font-bold">
                                                <span class="text-gray-700">Total Deductions:</span>
                                                <strong class="text-red-700">₱{{ formatNumber(totalDeductions) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 bg-blue-100 p-4 rounded-lg text-center">
                                    <h4 class="text-blue-900 font-bold">
                                        <i class="bx bx-money mr-2"></i>
                                        Net Salary: <span class="text-green-600">₱{{ formatNumber(netSalary) }}</span>
                                    </h4>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-between pt-6">
                                <SecondaryButton 
                                    type="button" 
                                    @click="cancel"
                                    class="flex items-center"
                                >
                                    <i class="bx bx-arrow-back mr-1"></i>
                                    Cancel
                                </SecondaryButton>
                                <div class="flex space-x-3">
                                    <button 
                                        type="button" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        @click="previewPayroll"
                                        :disabled="!isFormValid"
                                    >
                                        <i class="bx bx-show mr-1"></i>
                                        Preview
                                    </button>
                                    <PrimaryButton 
                                        type="submit"
                                        :disabled="form.processing || !isFormValid"
                                        class="flex items-center"
                                    >
                                        <i class="bx bx-save mr-1" v-if="!form.processing"></i>
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Updating...' : 'Update Payroll Record' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useForm, router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
    payroll: Object,
    isAdmin: Boolean,
})

const workingDays = ref(0)

const form = useForm({
    start_date: props.payroll.start_date,
    end_date: props.payroll.end_date,
    period: props.payroll.period,
    basic_salary: props.payroll.basic_salary,
    overtime_hours: props.payroll.overtime_hours,
    leave_days: props.payroll.leave_days,
    absent_days: props.payroll.absent_days,
    housing_allowance: props.payroll.housing_allowance,
    transport_allowance: props.payroll.transport_allowance,
    meal_allowance: props.payroll.meal_allowance,
    other_allowance: props.payroll.other_allowance,
    sss_deduction: props.payroll.sss_deduction,
    philhealth_deduction: props.payroll.philhealth_deduction,
    pagibig_deduction: props.payroll.pagibig_deduction,
    advance_deduction: props.payroll.advance_deduction,
    other_deduction: props.payroll.other_deduction,
    status: props.payroll.status,
    notes: props.payroll.notes,
})

// Computed properties
const basicSalary = computed(() => parseFloat(form.basic_salary) || 0)
const grossSalary = computed(() => basicSalary.value / 2)
const overtimePay = computed(() => {
    const overtimeHours = parseFloat(form.overtime_hours) || 0
    const hourlyRate = basicSalary.value / 160 // Assuming 160 hours per month
    return overtimeHours * hourlyRate * 1.25 // 1.25x rate for overtime
})
const totalAllowances = computed(() => {
    return (parseFloat(form.housing_allowance) || 0) +
           (parseFloat(form.transport_allowance) || 0) +
           (parseFloat(form.meal_allowance) || 0) +
           (parseFloat(form.other_allowance) || 0)
})
const totalDeductions = computed(() => {
    return (parseFloat(form.sss_deduction) || 0) +
           (parseFloat(form.philhealth_deduction) || 0) +
           (parseFloat(form.pagibig_deduction) || 0) +
           (parseFloat(form.advance_deduction) || 0) +
           (parseFloat(form.other_deduction) || 0)
})
const netSalary = computed(() => {
    return grossSalary.value + overtimePay.value + totalAllowances.value - totalDeductions.value
})

const isFormValid = computed(() => {
    return form.start_date && form.end_date && form.period && form.basic_salary
})

// Methods
const calculateWorkingDays = () => {
    if (form.start_date && form.end_date) {
        const start = new Date(form.start_date)
        const end = new Date(form.end_date)
        const diffTime = Math.abs(end - start)
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
        
        // Calculate working days (excluding weekends)
        let workingDaysCount = 0
        const current = new Date(start)
        while (current <= end) {
            if (current.getDay() !== 0 && current.getDay() !== 6) { // Not Sunday or Saturday
                workingDaysCount++
            }
            current.setDate(current.getDate() + 1)
        }
        workingDays.value = workingDaysCount
    }
}

const calculateSalary = () => {
    // This will trigger reactive updates to computed properties
    // No additional logic needed as computed properties handle the calculations
}

const previewPayroll = () => {
    if (!isFormValid.value) {
        alert('Please fill in all required fields before previewing.')
        return
    }
    
    // Show a preview modal or alert with the payroll summary
    const summary = `
Payroll Preview:
Employee: ${props.payroll.user?.name}
Period: ${form.period} (${form.start_date} to ${form.end_date})
Working Days: ${workingDays.value}

Salary Breakdown:
- Basic Salary: ₱${formatNumber(basicSalary.value)}
- Gross Salary (Half Month): ₱${formatNumber(grossSalary.value)}
- Overtime Pay: ₱${formatNumber(overtimePay.value)}
- Total Allowances: ₱${formatNumber(totalAllowances.value)}
- Total Deductions: ₱${formatNumber(totalDeductions.value)}
- Net Salary: ₱${formatNumber(netSalary.value)}
    `
    
    alert(summary)
}

const submit = () => {
    if (!isFormValid.value) {
        alert('Please fill in all required fields.')
        return
    }
    
    form.put(`/admin/payroll/${props.payroll.id}`, {
        onSuccess: () => {
            // Redirect to payroll index
            router.get('/admin/payroll')
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors)
        }
    })
}

const cancel = () => {
    router.get('/admin/payroll')
}

const formatNumber = (number) => {
    return number.toLocaleString()
}

// Initialize working days calculation
calculateWorkingDays()
</script>
