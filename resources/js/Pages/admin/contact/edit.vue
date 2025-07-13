<script setup>
import {Head, useForm} from "@inertiajs/vue3";

const props = defineProps({
    contact: Object
})

const form = useForm({
    name: props.contact.name,
    caller_id: props.contact.caller_id,
    email: props.contact.email,
    description: props.contact.description,
});


const submit = ()=>{
    form.post(route('admin.contacts.update', props.contact.id));
}



</script>

<template>
    <div>
        <Head>
            <title>Edit Contact</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Edit Contact</h5>
                            <div class="me-5">
                                <Link :href="route('admin.contacts.index')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="submit" method="post">
                                <div class="row">

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="subject">Name <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.name" :class="{'is-invalid' : form.errors.name}" id="name" >
                                        <span class="invalid-feedback">{{ form.errors.name }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="caller_id">Phone Number <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.caller_id" :class="{'is-invalid' : form.errors.caller_id}" id="caller_id" >
                                        <span class="invalid-feedback">{{ form.errors.caller_id }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="email">Email <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.email" :class="{'is-invalid' : form.errors.email}" id="email" >
                                        <span class="invalid-feedback">{{ form.errors.email }}</span>
                                    </div>

                                    <div class="mb-4 col-md-12">
                                        <label class="form-label" for="subject">Description <i class="text-danger">*</i></label>
                                        <textarea class="form-control" v-model="form.description" rows="4" :class="{'is-invalid' : form.errors.description}" id="description"></textarea>
                                        <span class="invalid-feedback">{{ form.errors.description }}</span>
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
