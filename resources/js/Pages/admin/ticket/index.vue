<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

defineProps({
    tickets: Object
});

const deleteForm = useForm({});
const confirmDelete = (id) => {
    if (confirm('Are you sure?')){
        deleteForm.delete(route('admin.tickets.destroy', id));
    }
};



</script>

<template>
    <div>
        <Head>
            <title>Ticket List</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Ticket List</h5>
                            <div class="me-5">
                                <Link :href="route('admin.tickets.create')" class="btn btn-primary"> Create Ticket</Link>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Caller</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="ticket in tickets.data">
                                    <td>{{ ticket.ticket_no }}</td>
                                    <td>{{ ticket.contact ? ticket.contact?.name +' ('+ ticket.contact?.caller_id +')' : '-'}}</td>
                                    <td>{{ ticket.subject }}</td>
                                    <td>{{ ticket.status }}</td>

                                    <td>

                                        <Link
                                            :href="route('admin.tickets.show', ticket.id)"
                                            class="btn btn-sm btn-info me-2">
                                            <i class="bx bx-detail"></i>
                                        </Link>

                                        <Link
                                            :href="route('admin.tickets.edit', ticket.id)"
                                            class="btn btn-sm btn-primary me-2">
                                            <i class="bx bx-edit"></i>
                                        </Link>

                                        <Link
                                            @click="confirmDelete(ticket.id)"
                                            href="#"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="tickets.links" />
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
