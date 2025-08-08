<template>
    <div class="personal-info-page">
        <slot />
    </div>
</template>
<style scoped>
.personal-info-page .navbar {
    background-color: #28a745;
    /* Light blue navbar */
}
.personal-info-page .container {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}
</style>
