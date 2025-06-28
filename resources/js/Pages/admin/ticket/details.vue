<script setup>
import {Head, useForm} from "@inertiajs/vue3";

defineProps({
    ticket: Object,
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
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Ticket Details</h5>
                            <div class="me-5">
                                <Link :href="route('admin.tickets.index')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>
                        <div class="card-body">

                            <div>
                                <li class="mb-2">Ticket No : {{ ticket.ticket_no }}</li>
                                <li class="mb-2">Caller Name: {{ ticket.contact?.name }}</li>
                                <li class="mb-2">Caller Number: {{ ticket.contact?.caller_id }}</li>
                                <li class="mb-2">Subject: {{ ticket.subject }}</li>
                                <li class="mb-2">Status: {{ ticket.status }}</li>
                                <li class="mb-2">Description: {{ ticket.description }}</li>

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
