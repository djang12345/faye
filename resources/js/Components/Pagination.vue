<script setup>
import { router } from "@inertiajs/vue3";

defineProps({
    paginate: Object,
});

const goToPage = (link) => {
    router.visit(link.url, {
        method: 'get',
        preserveState: true,
    });
};
</script>

<template>
    <div class="d-flex justify-content-end mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Button -->
                <li class="page-item" :class="{ 'disabled': !paginate.prev_page_url }">
                    <button
                        @click="paginate.prev_page_url && goToPage(paginate.links[0])"
                        class="page-link"
                        :disabled="!paginate.prev_page_url"
                    >
                        Prev
                    </button>
                </li>

                <!-- Page Numbers -->
                <li
                    v-for="(page, index) in paginate.links.slice(1, -1)"
                    :key="index"
                    :class="['page-item', { 'active': page.active }]"
                >
                    <button @click="goToPage(page)" class="page-link">
                        {{ page.label }}
                    </button>
                </li>

                <!-- Next Button -->
                <li class="page-item" :class="{ 'disabled': !paginate.next_page_url }">
                    <button
                        @click="paginate.next_page_url && goToPage(paginate.links[paginate.links.length - 1])"
                        class="page-link"
                        :disabled="!paginate.next_page_url"
                    >
                        Next
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.page-item.disabled .page-link {
    cursor: not-allowed;
    color: #6c757d; /* Bootstrap gray */
    pointer-events: none;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
