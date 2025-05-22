<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";
import {round} from "es-toolkit";

defineProps({
    campaigns: Object
});

const deleteForm = useForm({});
const confirmDelete = (id) => {
    if (confirm('Are you sure?')){
        deleteForm.delete(route('admin.campaigns.destroy', id));
    }
};



</script>

<template>
    <div>
        <Head>
            <title>Campaign List</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Campaign List</h5>
                            <div class="me-5">
                                <Link :href="route('admin.campaigns.create')" class="btn btn-primary"> Create Campaign</Link>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Number List</th>
                                    <th>Voice File</th>
                                    <th>Number Status</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="campaign in campaigns.data">
                                    <td>{{ campaign.id }}</td>
                                    <td>{{ campaign.name }}</td>
                                    <td>{{ campaign.number_list?.name }}</td>
                                    <td>{{ campaign.voice_file?.name }}</td>
                                    <td>
                                        <p v-if="campaign.process_status === 'Processing'" class="badge bg-primary">Processing ({{campaign.progress}}%)</p>
                                        <p v-if="campaign.process_status === 'Failed'" class="badge bg-warning">Failed</p>
                                        <p v-if="campaign.process_status === 'Processed'" class="badge bg-success">Processed</p>
                                    </td>

                                    <td class="text-capitalize">
                                        {{ campaign.status }}
                                        <span v-if="campaign.status==='running'">
                                            ({{ campaign.total_numbers > 0
                                            ? round((campaign.processed_numbers / campaign.total_numbers * 100), 2)
                                            : 0 }}%)
                                        </span>
                                        <span v-if="campaign.status==='paused'" class="d-block">
                                            ({{ campaign.total_numbers > 0
                                            ? round((campaign.processed_numbers / campaign.total_numbers * 100), 2)
                                            : 0 }}% Completed)
                                        </span>
                                    </td>
                                    <td v-if="(campaign.process_status !== 'Processing') && (campaign.status !== 'running')">

                                        <Link method="post" v-if="(campaign.status !== 'completed') && (campaign.status !== 'paused')"
                                            :href="route('admin.campaigns.launch', campaign.id)"
                                            class="btn btn-sm btn-primary me-2">
                                            Launch
                                        </Link>

                                        <Link method="post" v-if="(campaign.status !== 'completed') && (campaign.status === 'paused')"
                                              :href="route('admin.campaigns.resume', campaign.id)"
                                              class="btn btn-sm btn-primary me-2">
                                            Resume
                                        </Link>

                                        <Link
                                              :href="route('admin.campaigns.show', campaign.id)"
                                              class="btn btn-sm btn-success me-2">
                                            View
                                        </Link>

                                        <Link
                                            @click="confirmDelete(campaign.id)"
                                            href="#"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>
                                    </td>
                                    <td v-else>

                                        <Link method="post" v-if="(campaign.status === 'running')"
                                              :href="route('admin.campaigns.pause', campaign.id)"
                                              class="btn btn-sm btn-warning me-2">
                                            Pause
                                        </Link>

                                        <Link v-if="(campaign.process_status === 'Processed')"
                                              :href="route('admin.campaigns.show', campaign.id)"
                                              class="btn btn-sm btn-success me-2">
                                            View
                                        </Link>

                                        <Link v-if="(campaign.process_status === 'Processed')"
                                            @click="confirmDelete(campaign.id)"
                                            href="#"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>

                                    </td>
                                </tr>

                                <tr v-if="campaigns.data.length === 0">
                                    <td class="text-center text-danger" colspan="6">No Data Found</td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="campaigns.links" />
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
