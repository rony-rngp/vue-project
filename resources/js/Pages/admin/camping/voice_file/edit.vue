<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    voice_file: Object
});

const form = useForm({
    name: props.voice_file.name ?? '',
    file: null
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submit = ()=>{
    form.post(route('admin.voice-file.update', props.voice_file.id));
}



</script>

<template>
    <div>
        <Head>
            <title>Create Voice Files</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Edit File List</h5>
                            <div class="me-5">
                                <Link :href="route('admin.voice-file.index')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="submit" method="post">
                                <div class="row">
                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="name">Name <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.name" :class="{'is-invalid' : form.errors.name}" id="name" >
                                        <span class="invalid-feedback">{{ form.errors.name }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="file">Voice File </label>
                                        <input type="file" class="form-control" accept=".mp3,.wav" @change="handleFileUpload" :class="{'is-invalid' : form.errors.file}" id="file" >
                                        <span class="invalid-feedback">{{ form.errors.file }}</span>
                                    </div>

                                </div>

                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <span v-if="!form.processing">Update</span>
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
