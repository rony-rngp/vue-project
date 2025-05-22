<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";
import {round} from "es-toolkit";

defineProps({
    campaign: Object,
    campaign_calls: Object,
});




</script>

<template>
    <div>
        <Head>
            <title>Campaign Details</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Campaign Details</h5>
                            <div class="me-5">
                                <Link :href="route('admin.campaigns.create')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>
                        <div class="card-body">

                            <div>
                                <li class="mb-2">Campaign : {{ campaign.name }}</li>
                                <li class="mb-2">Number List : {{ campaign.number_list?.name }}</li>
                                <li class="mb-2">Voice File : {{ campaign.voice_file?.name }}</li>
                                <li class="mb-2">Status :
                                    <span style="text-transform: capitalize">{{ campaign.status }}</span>
                                   ( {{ round((campaign.processed_numbers / campaign.total_numbers * 100), 2)}}%)
                                </li>
                            </div>

                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Number</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="(campaign_call, index) in campaign_calls.data" :key="campaign_call.id">
                                    <td>{{ ((campaign_calls.current_page - 1) * campaign_calls.per_page) + index + 1 }}</td>
                                    <td>{{ campaign_call.number }}</td>
                                    <td style="text-transform: capitalize">{{ campaign_call.status }}</td>
                                </tr>

                                <tr v-if="campaign_calls.data.length === 0">
                                    <td class="text-center text-danger" colspan="6">No Data Found</td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="campaign_calls.links" />
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
