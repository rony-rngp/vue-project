<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

defineProps({
    conversations: Object
});

const deleteForm = useForm({});
const confirmDelete = (id) => {
    if (confirm('Are you sure?')){
        //deleteForm.delete(route('admin.tickets.destroy', id));
    }
};



</script>

<template>
    <div>
        <Head>
            <title>Conversation List</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Conversation List</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Caller Info</th>
                                    <th>Total Conversation</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="conversation in conversations.data">
                                    <td>{{ conversation.id }}</td>
                                    <td>
                                        <template v-if="conversation.contact">
                                            <span>Name: {{ conversation.contact?.name}}</span>
                                            <br>
                                            <span>Number: {{ conversation.contact?.caller_id}}</span>
                                        </template>
                                        <template v-else>
                                            <span>{{ conversation.caller }}</span>
                                        </template>

                                    </td>
                                    <td>
                                        <p class="badge bg-primary">{{ conversation.call_records_count }}</p>
                                    </td>

                                    <td>

                                        <Link title="Details"
                                            :href="route('admin.conversations.show', conversation.id)"
                                            class="btn btn-sm btn-info me-2">
                                            Details
                                        </Link>


<!--                                        <Link
                                            @click="confirmDelete(conversation.id)"
                                            href="#"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>-->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="conversations.links" />
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
