<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

defineProps({
    ticket: Object,
    ticket_conversations: Object,
});




</script>

<template>
    <div>
        <Head>
            <title>Ticket Details</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Ticket Details</h5>
                            <div class="me-5">
                                <Link :href="route('admin.tickets.index')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>
                        <div class="card-body">

                            <div>
                                <li class="mb-2">Ticket No : {{ ticket.ticket_no }}</li>
                                <li class="mb-2">Caller Name: {{ ticket.contact?.name ?? '-' }}</li>
                                <li class="mb-2">Caller Number: {{ ticket.contact?.caller_id ?? '-' }}</li>
                                <li class="mb-2">Subject: {{ ticket.subject }}</li>
                                <li class="mb-2">Status: {{ ticket.status }}</li>
                                <li class="mb-2">Description: {{ ticket.description }}</li>

                            </div>

                        </div>



                    </div>

                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Conversation History</h5>
                        </div>
                        <div class="table-responsive ">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="max-width: 8%">ID</th>
                                    <th>Transcription</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="conversation in ticket_conversations.data">
                                    <td>{{ conversation.id }}</td>
                                    <td>
                                        <div class="conversation_data" v-if="JSON.parse(conversation.transcription)">
                                            <div v-for="(chunk, index) in JSON.parse(conversation.transcription)" :key="index" class="d-flex mb-3">
                                                <div>
                                                    <div :class="chunk.speaker === 0 ? 'bg-primary text-white p-3 rounded-3' : 'bg-light text-dark p-3 rounded-3'">
                                                        <strong>Speaker {{ chunk.speaker }} : </strong>
                                                        <span>{{ chunk.text }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>
                                            {{ conversation.transcription }}
                                        </div>
                                    </td>


                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="ticket_conversations.links" />
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
.conversation_data{
    max-height: 500px;
    overflow: auto;
}
</style>
