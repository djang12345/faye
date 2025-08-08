<script setup>
import { onMounted, ref } from "vue";


const props = defineProps({
    error: String,
    placeholder: String,
});
const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="input-group flex-nowrap">
        <input
            v-model="model"
            :class="{ 'is-invalid': error }"
            :placeholder="placeholder"
            ref="input"
            type="text"
            class="form-control"
            aria-label="Username"
            aria-describedby="addon-wrapping"
        />
    </div>
</template>
