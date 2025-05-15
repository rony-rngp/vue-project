<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../../Pagination.vue";
import {ref} from "vue";

defineProps({
    number_list: Object
});

const csvInput = ref(null)

const form = useForm({
    name: null,
    csv_file: null,
});

const handleFileUpload = (e) => {
    form.csv_file = e.target.files[0];
};

const submit = ()=>{
    form.post(route('admin.number-list.store'),{
        onSuccess: ()=>{
            form.reset();
            if (csvInput.value) {
                csvInput.value.value = '';  // Manually reset the file input
            }
        }
    });
}

const deleteForm = useForm({});
const confirmDelete = (id) => {
    if (confirm('Are you sure?')){
        deleteForm.delete(route('admin.number-list.destroy', id));
    }
};

</script>

<template>
    <div>
        <Head>
            <title>Number List</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">

                <div class="col-md-12 mb-4">
                    <div class="card mb-6">
                        <div class="d-flex justify-content-between align-items-center border-bottom">
                            <h5 class="card-header">Add Number List
                            </h5>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="submit" method="post">

                                <div class="row">
                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="name">Name <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.name" :class="{'is-invalid' : form.errors.name}" id="name"   >
                                        <span class="invalid-feedback">{{ form.errors.name }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="title">Upload csv/xlsx <i class="text-danger">*</i></label>
                                        <input type="file" class="form-control" id="csv" :class="{'is-invalid' : form.errors.csv_file}" ref="csvInput" @change="handleFileUpload" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"  >
                                        <span class="invalid-feedback">{{ form.errors.csv_file }}</span>
                                    </div>


                                </div>

                                <button type="submit" class="btn btn-primary mt-2" :disabled="form.processing">
                                    <span v-if="!form.processing">Submit</span>
                                    <i v-if="form.processing" class=" bx bx-loader bx-spin"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>


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
                                    <th>Name</th>
                                    <th>Total Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="number_data in number_list.data">
                                    <td>{{ number_data.id }}</td>
                                    <td>{{ number_data.name }}</td>
                                    <td>{{ number_data.numbers_count }}</td>
                                    <td>
                                        <p v-if="number_data.status === 'Processing'" class="badge bg-primary">Processing ({{number_data.progress}}%)</p>
                                        <p v-if="number_data.status === 'Failed'" class="badge bg-warning">Failed</p>
                                        <p v-if="number_data.status === 'Completed'" class="badge bg-success">Uploaded</p>
                                    </td>
                                    <td>
                                        <Link v-if="number_data.status !== 'Processing'"
                                            @click="confirmDelete(number_data.id)"
                                           href="#"

                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>
                                        <p v-else class="badge bg-primary">Please Wait</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="number_list.links" />
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
