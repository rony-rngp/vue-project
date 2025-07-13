<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

defineProps({
    contacts: Object
});

const deleteForm = useForm({});
const confirmDelete = (id) => {
    if (confirm('Are you sure?')){
        deleteForm.delete(route('admin.contacts.destroy', id));
    }
};



</script>

<template>
    <div>
        <Head>
            <title>Contact List</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Contact List</h5>
                            <div class="me-5">
                                <Link :href="route('admin.contacts.create')" class="btn btn-primary"> Create Contact</Link>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="contact in contacts.data">
                                    <td>{{ contact.id }}</td>
                                    <td>{{ contact.name }}</td>
                                    <td>{{ contact.caller_id }}</td>
                                    <td>{{ contact.email }}</td>
                                    <td>

                                        <Link
                                            :href="route('admin.contacts.edit', contact.id)"
                                            class="btn btn-sm btn-primary me-2">
                                            <i class="bx bx-edit"></i>
                                        </Link>

                                        <Link
                                            @click="confirmDelete(contact.id)"
                                            href="#"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="contacts.links" />
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
