<script setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";


import {useSettings} from "./useSettings.js";

const {getSettings} = useSettings();


const form = useForm({
    website_name: getSettings('website_name'),
    email: getSettings('email'),
    phone: getSettings('phone'),
    address: getSettings('address'),
    copyright_text: getSettings('copyright_text'),
    email_verification: getSettings('email_verification'),
    phone_verification: getSettings('phone_verification'),
});

const submit = ()=>{
    form.post('/admin/settings');
}



</script>

<template>
<div>
    <Head>
        <title> Settings</title>
    </Head>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-6">
                    <div class="d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="card-header">Website Settings</h5>
                    </div>

                    <div class="card-body">
                        <div v-if="$page.props.flash.success" class="alert bg-success text-dark">
                            {{ $page.props.flash.success }}
                        </div>

                        <form @submit.prevent="submit" enctype="multipart/form-data" method="post">

                            <div class="row">

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="website_name">Website Name <i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : form.errors.website_name}" v-model="form.website_name" id="website_name">
                                    <span class="invalid-feedback">{{ form.errors.website_name }}</span>
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="email">Email <i class="text-danger">*</i></label>
                                    <input type="email" class="form-control" v-model="form.email" :class="{'is-invalid' : form.errors.website_name}" id="email">
                                    <span class="invalid-feedback">{{ form.errors.email }}</span>
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="phone">Phone <i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" v-model="form.phone" :class="{'is-invalid' : form.errors.email}" id="phone">
                                    <span class="invalid-feedback">{{ form.errors.email }}</span>
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="address">Address <i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" v-model="form.address" :class="{'is-invalid' : form.errors.address}" id="phone">
                                    <span class="invalid-feedback">{{ form.errors.address }}</span>
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="copyright_text">Copyright Text <i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" v-model="form.copyright_text" :class="{'is-invalid' : form.errors.copyright_text}" id="copyright_text">
                                    <span class="invalid-feedback">{{ form.errors.copyright_text }}</span>
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="email_verification">Email Verification <i class="text-danger">*</i></label>
                                    <select class="form-control" v-model="form.email_verification" :class="{'is-invalid' : form.errors.email_verification}" id="email_verification">
                                        <option :selected="form.email_verification === 1" value="1">Enable</option>
                                        <option :selected="form.email_verification === 0" value="0">Disable</option>
                                    </select>
                                    <span class="invalid-feedback">{{ form.errors.email_verification }}</span>
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label" for="phone_verification">Phone Verification <i class="text-danger">*</i></label>
                                    <select class="form-control" v-model="form.phone_verification" :class="{'is-invalid' : form.errors.phone_verification}" id="phone_verification">
                                        <option :selected="form.phone_verification === 1" value="1">Enable</option>
                                        <option :selected="form.phone_verification === 0" value="0">Disable</option>
                                    </select>
                                    <span class="invalid-feedback">{{ form.errors.phone_verification }}</span>
                                </div>


                            </div>

                            <button type="submit" class="btn btn-primary validate" :disabled="form.processing">
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
