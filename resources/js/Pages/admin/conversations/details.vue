<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

defineProps({
    conversation: Object,
    call_records: Object
});


</script>

<template>
    <div>
        <Head>
            <title>Conversation List</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">

                    <div class="card mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Conversation Details</h5>
                        </div>
                        <div class="card-body">
                            <div>
                                <li class="mb-2">Conversation ID : {{ conversation.id }}</li>
                                <li class="mb-2" v-if="conversation.contact">Caller Name: {{ conversation.contact?.name ?? '-' }}</li>
                                <li class="mb-2">Caller Number: {{ conversation.contact?.caller_id ?? conversation.caller }}</li>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Call History</h5>
                        </div>
                        <div class="table-responsive ">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="max-width: 8%">ID</th>
                                    <th>Transcription</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="call_record in call_records.data">
                                    <td>{{ call_record.id }}</td>
                                    <td>
                                        <div>
                                            {{ call_record.transcription }}
                                        </div>
                                    </td>


                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="call_records.links" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
.card-header{
    padding: 12px 17px;
}
</style>
