<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";
import {ref} from "vue";

const props = defineProps({
    leads: Array,
    filters: Object,
    offset: String,
    nextOffset: String,
    message: String,
    error_msg: String,
    missing_audio: Array,
});

const offsetStack = ref([]);

function goToNextPage() {

    router.get(route('admin.leads.index'), {
        ...props.filters,
        offset: props.nextOffset,
    }, {
        preserveState: true,
        preserveScroll: true
    })

    offsetStack.value.push(props.offset)
}

function goToPreviousPage() {
    if (offsetStack.value.length > 0) {
        const prevOffset = offsetStack.value.pop()
        router.get(route('admin.leads.index'), {
            ...props.filters,
            offset: prevOffset,
        }, {
            preserveState: true,
            preserveScroll: true,
        })
    }
}

function goToFirstPage() {
    router.get(route('admin.leads.index'), {
        ...props.filters,
        offset: null,
    }, {
        preserveState: true,
        preserveScroll: true
    })
    offsetStack.value = []
}

</script>

<template>
    <div>
        <Head>
            <title>Airtable Leads</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Airtable Leads</h5>
                            <div class="me-5">
<!--                                <Link :href="route('admin.tickets.create')" class="btn btn-primary"> Create Ticket</Link>-->
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="error_msg" class="alert alert-danger me-3 mx-3">
                            {{ error_msg }}
                        </div>

                        <!-- Info Message -->
                        <div v-if="message" class="alert alert-warning me-3 mx-3">
                            {{ message }}
                        </div>

                        <div v-if="missing_audio" class="alert alert-danger me-3 mx-3">
                            <li v-for="missing in missing_audio">{{ missing}}</li>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Batch</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody  v-if="leads.length > 0" class="table-border-bottom-0">
                                <tr v-for="lead in leads" :key="lead.id">
                                    <td>{{ lead.fields?.Name || 'N/A' }}</td>
                                    <td>{{ lead.fields?.Phone || 'N/A' }}</td>
                                    <td>{{ lead.fields?.['Batch Name'] || 'N/A' }}</td>
                                    <td> {{ lead.fields?.Status || 'N/A' }}</td>
                                </tr>
                                </tbody>

                                <tbody v-else>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No Lead Found</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >

                                <nav class="mt-4 d-flex justify-content-center">
                                    <ul class="pagination">
                                        <li class="page-item me-3" :class="{ disabled: !props.offset }">
                                            <button class="btn btn-sm btn-primary" @click="goToFirstPage" :disabled="!props.offset">
                                                &laquo; First
                                            </button>
                                        </li>

                                        <li class="page-item me-3" :class="{ disabled: offsetStack.length === 0 }">
                                            <button class="btn btn-sm btn-primary" @click="goToPreviousPage" :disabled="offsetStack.length === 0">
                                                &lsaquo; Previous
                                            </button>
                                        </li>

                                        <li class="page-item" :class="{ disabled: !props.nextOffset }">
                                            <button class=" btn btn-primary btn-sm" @click="goToNextPage" :disabled="!props.nextOffset">
                                                Next &rsaquo;
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>

</style>
