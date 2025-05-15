<script setup>
import {Head} from "@inertiajs/vue3";
import Pagination from "../../Pagination.vue";

defineProps({
    users: Object
});



</script>

<template>
<div>
    <Head>
        <title>Users</title>
    </Head>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center" >
                        <h5 class="card-header">User List</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Type</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr v-for="user in users.data">
                                    <td>{{ user.id }}</td>
                                    <td class="text-capitalize">{{ user.user_type }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.phone }}</td>
                                    <td>{{ user.company_name }}</td>
                                    <td>{{ user.address }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <Link class="dropdown-item" :href="route('admin.users.edit', user.id)"><i class="bx bx-edit-alt me-1"></i> Edit</Link>
                                                <Link class="dropdown-item" :href="route('admin.users.destroy', user.id)" methods="post"><i class="bx bx-trash me-1"></i> Delete</Link>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3 float-end me-5" >
                            <Pagination :links="users.links" />
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
