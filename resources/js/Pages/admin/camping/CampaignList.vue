<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

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
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="campaign in campaigns.data">
                                    <td>{{ campaign.id }}</td>
                                    <td>{{ campaign.name }}</td>
                                    <td>{{ campaign.number_list_id }}</td>
                                    <td>{{ campaign.voice_file_id }}</td>
                                    <td>{{ campaign.status }}</td>
                                    <td>

                                        <Link
                                            :href="route('admin.campaigns.edit', campaign.id)"
                                            class="btn btn-sm btn-primary me-2">
                                            <i class="bx bx-edit"></i>
                                        </Link>

                                        <Link
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
