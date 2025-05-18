<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
   number_list: Object,
    voice_files: Object,
});


const form = useForm({
    name: null,
    number_list_id: null,
    voice_file_id: null,
});

const fileInput = ref(null)

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submit = ()=>{
    form.post(route('admin.campaigns.store'));
}



</script>

<template>
    <div>
        <Head>
            <title>Add Campaign</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Add Campaign</h5>
                            <div class="me-5">
                                <Link :href="route('admin.campaigns.index')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="submit" method="post">
                                <div class="row">
                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="name">Campaign Name <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.name" :class="{'is-invalid' : form.errors.name}" id="name" >
                                        <span class="invalid-feedback">{{ form.errors.name }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="file">Number List <i class="text-danger">*</i></label>
                                        <select v-model="form.number_list_id" :class="{'is-invalid' : form.errors.number_list_id}" id="number_list_id" class="form-control">
                                            <option :value="null">Select One</option>
                                            <option v-for="item in number_list" :value="item.id" :key="item.id">
                                                {{ item.name }}
                                            </option>
                                        </select>
                                        <span class="invalid-feedback">{{ form.errors.number_list_id }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="voice_file_id">Voice File <i class="text-danger">*</i></label>
                                        <select v-model="form.voice_file_id" :class="{'is-invalid' : form.errors.voice_file_id}" id="voice_file_id" class="form-control">
                                            <option :value="null">Select One</option>
                                            <option v-for="item in voice_files" :value="item.id" :key="item.id">
                                                {{ item.name }}
                                            </option>
                                        </select>
                                        <span class="invalid-feedback">{{ form.errors.voice_file_id }}</span>
                                    </div>

                                </div>

                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <span v-if="!form.processing">Submit</span>
                                    <i v-if="form.processing" class=" bx bx-loader bx-spin"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>

</style>
