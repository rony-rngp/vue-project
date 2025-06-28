<script setup>
import {Head, useForm} from "@inertiajs/vue3";

defineProps({
    contacts: Object
});

const form = useForm({
    contact_id: null,
    subject: null,
    description: null,
});


const submit = ()=>{
    form.post(route('admin.tickets.store'));
}



</script>

<template>
    <div>
        <Head>
            <title>Create Ticket</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center border-bottom" >
                            <h5 class="card-header">Create Ticket</h5>
                            <div class="me-5">
                                <Link :href="route('admin.tickets.index')" class="btn btn-primary"> Back</Link>
                            </div>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="submit" method="post">
                                <div class="row">


                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="contact_id">Contact <i class="text-danger">*</i></label>
                                        <select v-model="form.contact_id" :class="{'is-invalid' : form.errors.contact_id}" id="contact_id" class="form-control">
                                            <option :value="null">Select One</option>
                                            <option v-for="item in contacts" :value="item.id" :key="item.id">
                                                {{ item.name +' ('+item.caller_id+')' }}
                                            </option>
                                        </select>
                                        <span class="invalid-feedback">{{ form.errors.contact_id }}</span>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label" for="subject">Subject <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="form.subject" :class="{'is-invalid' : form.errors.subject}" id="subject" >
                                        <span class="invalid-feedback">{{ form.errors.subject }}</span>
                                    </div>

                                    <div class="mb-4 col-md-12">
                                        <label class="form-label" for="subject">Description <i class="text-danger">*</i></label>
                                        <textarea class="form-control" v-model="form.description" rows="4" :class="{'is-invalid' : form.errors.description}" id="description"></textarea>
                                        <span class="invalid-feedback">{{ form.errors.description }}</span>
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
