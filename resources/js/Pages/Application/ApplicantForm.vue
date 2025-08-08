<script setup>
import { Link, useForm, Head } from "@inertiajs/vue3";
import ApplicationLayout from "@/Layouts/ApplicationLayout.vue";
import { onMounted, ref, computed } from "vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    errors: Object,
});
const currentStep = ref("personalInfo");
const barangays = ref([]);
const municipalities = ref([]);
const provinces = ref([]);

const personalForm = useForm({
    firstname: "",
    middlename: "",
    lastname: "",
    age: "",
    sex: "",
    birthdate: "",
    height: "",
    weight: "",
    status: "",
    citizenship: "",
    country: "Philippines",
    province: "",
    municipality: "",
    barangay: "",
    email: "",
});

const parentsForm = useForm({
    mother_firstname: "",
    mother_middlename: "",
    mother_lastname: "",
    mother_occupation: "",
    father_firstname: "",
    father_middlename: "",
    father_lastname: "",
    father_occupation: "",
});

const validatePersonalInfo = () => {
    personalForm.post(route("application.validate.personalInfo"), {
        onSuccess: () => {
            currentStep.value = "parentsBackground";
        },
    });
};

const validateParentsBg = () => {
    parentsForm.post(route("application.validate.parentsBackground"), {
        onSuccess: () => {
            currentStep.value = "previewInfo";
        },
    });
};

const submitInfo = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to submit your application!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, submit!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Submitting...",
                text: "Please wait while we submit your application.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            const mergedForm = useForm({
                ...personalForm.data(),
                ...parentsForm.data(),
            });
            mergedForm.post(route("application.submit"), {
                onSuccess: () => {
                    currentStep.value = "success";
                    Swal.close();
                },
            });
        }
    });
};

const getProvinces = () => {
    axios.get("https://psgc.gitlab.io/api/provinces.json").then((response) => {
        provinces.value = response.data;
    });
};

const getCitiesMunicipalities = () => {
    const provincesData = document.getElementById("provincesData");

    const selectedOption = provincesData.options[provincesData.selectedIndex];

    const code = selectedOption.getAttribute("data-id");
    axios
        .get(
            `https://psgc.gitlab.io/api/provinces/${code}/cities-municipalities.json`
        )
        .then((response) => {
            municipalities.value = response.data;
        });
};

const getBarangays = () => {
    const provincesData = document.getElementById("citiesData");

    const selectedOption = provincesData.options[provincesData.selectedIndex];

    const code = selectedOption.getAttribute("data-id");
    axios
        .get(
            `https://psgc.gitlab.io/api/cities-municipalities/${code}/barangays.json`
        )
        .then((response) => {
            barangays.value = response.data;
        });
};

const progressWidth = computed(() => {
    switch (currentStep.value) {
        case "personalInfo":
            return 25;
        case "parentsBackground":
            return 50;
        case "previewInfo":
            return 75;
        case "success":
            return 100;
        default:
            return 0;
    }
});

onMounted(() => {
    getProvinces();
});
</script>
<template>
    <Head title="Application form" />
    <ApplicationLayout>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <Link class="navbar-brand" :href="route('welcome')"
                    >HERO SECURITY AND INVESTIGATION SERVICES</Link
                >
            </div>
        </nav>

        <div class="progress">
            <div
                class="progress-bar"
                role="progressbar"
                :style="{ width: progressWidth + '%' }"
                :aria-valuenow="progressWidth"
                aria-valuemin="0"
                aria-valuemax="100"
            >
                <span v-if="currentStep === 'personalInfo'">25%</span>
                <span v-if="currentStep === 'parentsBackground'">50%</span>
                <span v-if="currentStep === 'previewInfo'">75%</span>
                <span v-if="currentStep === 'success'">100%</span>
            </div>
        </div>
        <div class="container-application mt-5">
            <!-- Personal infor -->
            <div v-if="currentStep == 'personalInfo'">
                <h2 class="text-center mb-4">Personal Information Form</h2>
                <form @submit.prevent="validatePersonalInfo">
                    <!-- Form fields in 3 columns -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="firstname" class="form-label"
                                >First Name:</label
                            >
                            <input
                                v-model="personalForm.firstname"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errors.firstname }"
                                id="firstname"
                                name="firstname"
                                value=""
                            />
                            <InputError
                                v-if="errors.firstname"
                                :message="errors.firstname"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="middlename" class="form-label"
                                >Middle Name:</label
                            >
                            <input
                                v-model="personalForm.middlename"
                                :class="{ 'is-invalid': errors.middlename }"
                                value=""
                                type="text"
                                class="form-control"
                                id="middlename"
                                name="middlename"
                            />
                            <InputError
                                v-if="errors.middlename"
                                :message="errors.middlename"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="lastname" class="form-label"
                                >Last Name:</label
                            >
                            <input
                                v-model="personalForm.lastname"
                                :class="{ 'is-invalid': errors.lastname }"
                                value=""
                                type="text"
                                class="form-control"
                                id="lastname"
                                name="lastname"
                            />
                            <InputError
                                v-if="errors.lastname"
                                :message="errors.lastname"
                            />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email:</label>
                            <input
                                :class="{ 'is-invalid': errors.email }"
                                v-model="personalForm.email"
                                type="email"
                                value=""
                                class="form-control"
                                id="email"
                                name="email"
                            />
                            <InputError
                                v-if="errors.email"
                                :message="errors.email"
                            />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="age" class="form-label">Age:</label>
                            <input
                                :class="{ 'is-invalid': errors.age }"
                                v-model="personalForm.age"
                                value=""
                                type="number"
                                class="form-control"
                                id="age"
                                name="age"
                            />
                            <InputError
                                v-if="errors.age"
                                :message="errors.age"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="sex" class="form-label">Sex:</label>
                            <select
                                :class="{ 'is-invalid': errors.sex }"
                                v-model="personalForm.sex"
                                class="form-select input-erro"
                                id="sex"
                                name="sex"
                            >
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <InputError
                                v-if="errors.sex"
                                :message="errors.sex"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="birthdate" class="form-label"
                                >Birthdate:</label
                            >
                            <input
                                :class="{ 'is-invalid': errors.birthdate }"
                                v-model="personalForm.birthdate"
                                value=" "
                                type="date"
                                class="form-control"
                                id="birthdate"
                                name="birthdate"
                            />
                            <InputError
                                v-if="errors.birthdate"
                                :message="errors.birthdate"
                            />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="height" class="form-label"
                                >Height (cm):</label
                            >
                            <input
                                :class="{ 'is-invalid': errors.height }"
                                v-model="personalForm.height"
                                type="number"
                                value=""
                                class="form-control"
                                id="height"
                                name="height"
                            />
                            <InputError
                                v-if="errors.height"
                                :message="errors.height"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="weight" class="form-label"
                                >Weight (kg):</label
                            >
                            <input
                                :class="{ 'is-invalid': errors.weight }"
                                v-model="personalForm.weight"
                                type="number"
                                value=""
                                class="form-control"
                                id="weight"
                                name="weight"
                            />
                            <InputError
                                v-if="errors.weight"
                                :message="errors.weight"
                            />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label"
                                >Marital Status:</label
                            >
                            <select
                                :class="{ 'is-invalid': errors.status }"
                                v-model="personalForm.status"
                                class="form-select"
                                id="status"
                                name="status"
                            >
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                            <InputError
                                v-if="errors.status"
                                :message="errors.status"
                            />
                        </div>
                        <div class="col-md-4">
                            <label
                                for="citizenship"
                                class="form-label"
                                placeholder="ex. Filipino"
                                >Citizenship:</label
                            >
                            <input
                                :class="{ 'is-invalid': errors.citizenship }"
                                v-model="personalForm.citizenship"
                                type="text"
                                value=""
                                class="form-control"
                                id="citizenship"
                                name="citizenship"
                            />
                            <InputError
                                v-if="errors.citizenship"
                                :message="errors.citizenship"
                            />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <span class="text-muted"
                            >Please provide your current address.</span
                        >
                        <div class="col-md-4">
                            <label for="country" class="form-label"
                                >Country:</label
                            >
                            <input
                                :class="{ 'is-invalid': errors.country }"
                                v-model="personalForm.country"
                                type="text"
                                class="form-control"
                                id="country"
                                name="country"
                                value=""
                            />
                            <InputError
                                v-if="errors.country"
                                :message="errors.country"
                            />
                        </div>

                        <div class="col-md-4">
                            <label for="barangay" class="form-label"
                                >Province:</label
                            >
                            <select
                                :class="{ 'is-invalid': errors.province }"
                                v-model="personalForm.province"
                                name="province"
                                class="js-example-basic-single form-select"
                                @change="getCitiesMunicipalities()"
                                id="provincesData"
                                aria-label="Default select example"
                            >
                                <option value="" selected>
                                    Please select province
                                </option>
                                <option
                                    v-for="province in provinces"
                                    :value="province.name"
                                    :data-id="province.code"
                                >
                                    {{ province.name }}
                                </option>
                            </select>
                            <InputError
                                v-if="errors.province"
                                :message="errors.province"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="barangay" class="form-label"
                                >City/Municipality:</label
                            >
                            <select
                                :class="{ 'is-invalid': errors.municipality }"
                                v-model="personalForm.municipality"
                                name="municipality"
                                class="js-example-basic-single form-select"
                                @change="getBarangays()"
                                aria-label="Default select example"
                                id="citiesData"
                            >
                                <option
                                    v-if="provinces == []"
                                    value=""
                                    selected
                                >
                                    Please select province first
                                </option>
                                <option v-else value="" selected>
                                    Please select province first
                                </option>
                                <option
                                    v-for="municipality in municipalities"
                                    :value="municipality.name"
                                    :data-id="municipality.code"
                                >
                                    {{ municipality.name }}
                                </option>
                            </select>
                            <InputError
                                v-if="errors.municipality"
                                :message="errors.municipality"
                            />
                        </div>

                        <div class="col-md-4">
                            <label for="province" class="form-label"
                                >Barangay:</label
                            >
                            <select
                                :class="{ 'is-invalid': errors.barangay }"
                                v-model="personalForm.barangay"
                                name="barangay"
                                class="js-example-basic-single form-select"
                                aria-label="Default select example"
                                id="barangaysData"
                            >
                                <option
                                    v-if="municipalities == []"
                                    value=""
                                    selected
                                >
                                    Please select City/Municipality first
                                </option>
                                <option v-else value="" selected>
                                    Please select barangay first
                                </option>
                                <option
                                    v-for="barangay in barangays"
                                    :value="barangay.name"
                                    :data-id="barangay.code"
                                >
                                    {{ barangay.name }}
                                </option>
                            </select>
                            <InputError
                                v-if="errors.barangay"
                                :message="errors.barangay"
                            />
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <Link
                                :href="route('welcome')"
                                class="btn btn-danger m-2"
                                >Cancel</Link
                            >
                            <button type="submit" class="btn btn-success">
                                Next
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Parents background -->
            <div v-else-if="currentStep == 'parentsBackground'">
                <h2 class="text-center mb-4">Parents/Guardian Background</h2>
                <form @submit.prevent="validateParentsBg">
                    <span class="text-muted"
                        >Please provide the following information about your
                        parents.</span
                    >
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="mother_firstname" class="form-label"
                                >Mother's First Name:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.mother_firstname,
                                }"
                                v-model="parentsForm.mother_firstname"
                                type="text"
                                value=""
                                class="form-control"
                                id="mother_firstname"
                                name="mother_firstname"
                            />
                            <InputError
                                v-if="errors.mother_firstname"
                                :message="errors.mother_firstname"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="mother_middlename" class="form-label"
                                >Mother's Middle Name:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.mother_middlename,
                                }"
                                v-model="parentsForm.mother_middlename"
                                type="text"
                                value=""
                                class="form-control"
                                id="mother_middlename"
                                name="mother_middlename"
                            />
                            <InputError
                                v-if="errors.mother_middlename"
                                :message="errors.mother_middlename"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="mother_lastname" class="form-label"
                                >Mother's Last Name:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.mother_lastname,
                                }"
                                v-model="parentsForm.mother_lastname"
                                type="text"
                                value=""
                                class="form-control"
                                id="mother_lastname"
                                name="mother_lastname"
                            />
                            <InputError
                                v-if="errors.mother_lastname"
                                :message="errors.mother_lastname"
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="mother_occupation" class="form-label"
                                >Mother's Occupation:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.mother_occupation,
                                }"
                                v-model="parentsForm.mother_occupation"
                                type="text"
                                value=""
                                class="form-control"
                                id="mother_occupation"
                                name="mother_occupation"
                            />
                            <InputError
                                v-if="errors.mother_occupation"
                                :message="errors.mother_occupation"
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="father_firstname" class="form-label"
                                >Father's First Name:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.father_firstname,
                                }"
                                v-model="parentsForm.father_firstname"
                                type="text"
                                value=""
                                class="form-control"
                                id="father_firstname"
                                name="father_firstname"
                            />
                            <InputError
                                v-if="errors.father_firstname"
                                :message="errors.father_firstname"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="father_middlename" class="form-label"
                                >Father's Middle Name:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.father_middlename,
                                }"
                                v-model="parentsForm.father_middlename"
                                type="text"
                                value=""
                                class="form-control"
                                id="father_middlename"
                                name="father_middlename"
                            />
                            <InputError
                                v-if="errors.father_middlename"
                                :message="errors.father_middlename"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="father_lastname" class="form-label"
                                >Father's Last Name:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.father_lastname,
                                }"
                                v-model="parentsForm.father_lastname"
                                type="text"
                                value=""
                                class="form-control"
                                id="father_lastname"
                                name="father_lastname"
                            />
                            <InputError
                                v-if="errors.father_lastname"
                                :message="errors.father_lastname"
                            />
                        </div>
                        <div class="col-md-4">
                            <label for="father_occupation" class="form-label"
                                >Father's Occupation:</label
                            >
                            <input
                                :class="{
                                    'is-invalid': errors.father_occupation,
                                }"
                                v-model="parentsForm.father_occupation"
                                type="text"
                                value=""
                                class="form-control"
                                id="father_occupation"
                                name="father_occupation"
                            />
                            <InputError
                                v-if="errors.father_occupation"
                                :message="errors.father_occupation"
                            />
                        </div>
                    </div>

                    <!-- Save button -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button
                                @click="currentStep = 'personalInfo'"
                                class="btn btn-secondary m-2"
                            >
                                Back
                            </button>
                            <button type="submit" class="btn btn-success">
                                Next
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Preview info -->
            <div v-else-if="currentStep == 'previewInfo'">
                <h2 class="text-center mb-4">Review Information</h2>

                <div class="row">
                    <!-- Applicant Information Column -->
                    <div class="col-md-6">
                        <h3 class="section-header">Applicant Information</h3>
                        <div class="border p-2 rounded review-info">
                            <p>
                                <strong>First Name:</strong>
                                {{ personalForm.lastname }},
                                {{ personalForm.firstname }}
                                {{ personalForm.middlename }}
                            </p>
                            <p>
                                <strong>Email:</strong>
                                {{ personalForm.email }}
                            </p>
                            <p>
                                <strong>Age:</strong>
                                {{ personalForm.age }}
                            </p>
                            <p>
                                <strong>Sex:</strong>
                                {{ personalForm.sex }}
                            </p>
                            <p>
                                <strong>Birthdate:</strong>
                                {{ personalForm.birthdate }}
                            </p>
                            <p>
                                <strong>Height:</strong>
                                {{ personalForm.height }}
                            </p>
                            <p>
                                <strong>Weight:</strong>
                                {{ personalForm.weight }}
                            </p>
                            <p>
                                <strong>Status:</strong>
                                {{ personalForm.status }}
                            </p>
                            <p>
                                <strong>Citizenship:</strong>
                                {{ personalForm.citizenship }}
                            </p>
                            <p>
                                <strong>Address:</strong>
                                {{ personalForm.barangay }},
                                {{ personalForm.municipality }},
                                {{ personalForm.province }},
                                {{ personalForm.country }}
                            </p>
                        </div>
                    </div>

                    <!-- Parents/Guardian Information Column -->
                    <div class="col-md-6">
                        <h3 class="section-header">
                            Parents/Guardian Information
                        </h3>
                        <div class="border p-2 rounded review-info">
                            <p>
                                <strong>Mother's Name:</strong>
                                {{ parentsForm.mother_lastname }},
                                {{ parentsForm.mother_firstname }}
                                {{ parentsForm.mother_middlename }}
                            </p>
                            <p>
                                <strong>Mother's Occupation:</strong>
                                {{ parentsForm.mother_occupation }}
                            </p>
                            <p>
                                <strong>Father's Name:</strong>
                                {{ parentsForm.father_lastname }},
                                {{ parentsForm.father_firstname }}
                                {{ parentsForm.father_middlename }}
                            </p>
                            <p>
                                <strong>Father's Middle Name:</strong>
                                {{ parentsForm.father_occupation }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Save button -->
                <div class="text-center mt-4">
                    <!-- Save button -->
                    <div class="text-center mt-4">
                        <button
                            @click="currentStep = 'parentsBackground'"
                            class="btn btn-danger"
                        >
                            Back
                        </button>
                        <button
                            type="button"
                            id="saveBtn"
                            class="btn btn-success m-2"
                            @click="submitInfo"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
            <div v-else-if="currentStep == 'success'">
                <div class="container mt-5">
                    <div class="thank-you-message">
                        <h2>Thank You for Submitting Your Application!</h2>
                        <p>
                            Your application has been successfully submitted. We
                            will review your information and get back to you
                            soon.
                        </p>
                        <p>
                            If you have any questions, feel free to contact us.
                        </p>
                    </div>

                    <!-- Back to Home Button -->
                    <Link
                        :href="route('welcome')"
                        class="btn btn-success back-home-btn"
                        >Back to Home</Link
                    >
                </div>
            </div>
        </div>
    </ApplicationLayout>
</template>
<style scoped>
.navbar {
    background-color: #28a745;
    /* Light blue navbar */
}

.navbar-brand {
    color: black;
    font-weight: bold;
}

.container-application {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    /* Light shadow for form */
}

h2 {
    color: #343a40;
    /* Text color */
}

.form-label {
    font-weight: 600;
    /* Bold labels */
}

.btn {
    font-size: 16px;
    /* Larger button text */
}

.form-check-label {
    font-weight: 600;
}
</style>
