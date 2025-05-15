<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
})

function goToPage(url) {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true,
        })
    }
}

function sanitizeLabel(label) {
    if (label === '&laquo; Previous') return '<i class="bx bx-chevron-left icon-sm"></i>'
    if (label === 'Next &raquo;') return '<i class="bx bx-chevron-right icon-sm"></i>'
    return label
}
</script>

<template>
    <nav v-if="links.length > 3" aria-label="Pagination">
        <ul class="pagination">
            <li
                v-for="(link, index) in links"
                :key="index"
                class="dt-paging-button page-item"
                :class="{
                  active: link.active,
                  disabled: !link.url
                }"
            >
                <button
                    class="page-link"
                    v-html="sanitizeLabel(link.label)"
                    @click="goToPage(link.url)"
                    :disabled="!link.url"
                />
            </li>
        </ul>
    </nav>
</template>

<style scoped>

</style>
