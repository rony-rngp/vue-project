<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import Pagination from "../../../Pagination.vue";
import {getImageUrl} from "../../../helper.js";

defineProps({
    voice_files: Object
});

const deleteForm = useForm({});
const confirmDelete = (id) => {
    if (confirm('Are you sure?')){
        deleteForm.delete(route('admin.voice-file.destroy', id));
    }
};



</script>

<template>
    <div>
        <Head>
            <title>Voice Files</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center" >
                            <h5 class="card-header">Voice File List</h5>
                            <div class="me-5">
                                <Link :href="route('admin.voice-file.create')" class="btn btn-primary"> Upload Voice File</Link>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Voice File</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr v-for="voice_file in voice_files.data">
                                    <td>{{ voice_file.id }}</td>
                                    <td>{{ voice_file.name }}</td>
                                    <td>
                                        <audio controls oncontextmenu="return false;">
                                            <source :src="getImageUrl(voice_file.file)" type="audio/mp3" >
                                        </audio>
                                    </td>
                                    <td>

                                        <Link
                                            :href="route('admin.voice-file.edit', voice_file.id)"
                                            class="btn btn-sm btn-primary me-2">
                                            <i class="bx bx-edit"></i>
                                        </Link>

                                        <Link
                                            @click="confirmDelete(voice_file.id)"
                                            href="#"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="mt-3 float-end me-5" >
                                <Pagination :links="voice_files.links" />
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
