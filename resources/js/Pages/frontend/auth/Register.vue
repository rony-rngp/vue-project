<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";

import {useSettings} from "../../admin/useSettings.js";
const {getSettings} = useSettings();

const { props } = usePage();
const countries = props.countries;

const form = useForm({
    name: null,
    email: null,
    phone: null,
    company_name: null,
    address: null,
    city: null,
    state: null,
    zip_code: null,
    country: null,
    password: null,
    password_confirmation: null
});

const submit = ()=>{
    form.post(route('register'));
}

</script>

<template>
    <div>
        <Head>
            <title> Register</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-md-6 m-auto">
                    <div class="authentication-wrapper container-p-y">
                        <div class="authentication-inner">
                            <!-- Register Card -->
                            <div class="card px-sm-6 px-0">
                                <div class="card-body">
                                    <!-- Logo -->
                                    <div class="app-brand justify-content-center mb-6">
                                        <Link :href="route('home_page')" class="app-brand-link gap-2">
                                        <span class="app-brand-logo demo">
                                          </span>
                                            <span class="app-brand-text demo text-heading fw-bold">{{ getSettings('website_name') }}</span>
                                        </Link>
                                    </div>
                                    <!-- /Logo -->
                                    <h4 class="mb-1">Adventure starts here  👋🚀</h4>
                                    <p class="mb-6">Please register here to start your journey</p>

                                    <form class="mb-4" @submit.prevent="submit" method="post">

                                        <div class="row">
                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="email">Name <i class="text-danger">*</i></label>
                                                <input type="text" class="form-control" v-model="form.name" :class="{'is-invalid' : form.errors.name}" id="name"  placeholder="Enter your name">
                                                <span class="invalid-feedback">{{ form.errors.name }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="email">Email <i class="text-danger">*</i></label>
                                                <input type="email" class="form-control" v-model="form.email" :class="{'is-invalid' : form.errors.email}" id="email"  placeholder="Enter your email">
                                                <span class="invalid-feedback">{{ form.errors.email }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="phone">Phone <i class="text-danger">*</i></label>
                                                <input type="number" class="form-control" v-model="form.phone" :class="{'is-invalid' : form.errors.phone}" id="phone"  placeholder="Enter your phone number">
                                                <span class="invalid-feedback">{{ form.errors.phone }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="email">Company Name <i class="text-danger">*</i></label>
                                                <input type="text" class="form-control" v-model="form.company_name" :class="{'is-invalid' : form.errors.company_name}" id="company_name"  placeholder="Enter your company name">
                                                <span class="invalid-feedback">{{ form.errors.company_name }}</span>
                                            </div>

                                            <div class="mb-6 col-md-12">
                                                <label class="form-label" for="address">Street Address <i class="text-danger">*</i></label>
                                                <input type="text" class="form-control" v-model="form.address" :class="{'is-invalid' : form.errors.address}" id="address"  placeholder="Enter your address">
                                                <span class="invalid-feedback">{{ form.errors.address }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="city">City <i class="text-danger">*</i></label>
                                                <input type="text" class="form-control" v-model="form.city" :class="{'is-invalid' : form.errors.city}" id="city"  placeholder="Enter your city">
                                                <span class="invalid-feedback">{{ form.errors.city }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="state">State <i class="text-danger">*</i></label>
                                                <input type="text" class="form-control" v-model="form.state" :class="{'is-invalid' : form.errors.state}" id="state"  placeholder="Enter your state">
                                                <span class="invalid-feedback">{{ form.errors.state }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="state">Zip Code <i class="text-danger">*</i></label>
                                                <input type="text" class="form-control" v-model="form.zip_code" :class="{'is-invalid' : form.errors.zip_code}" id="zip_code"  placeholder="Enter your zip code">
                                                <span class="invalid-feedback">{{ form.errors.zip_code }}</span>
                                            </div>

                                            <div class="mb-6 col-md-6">
                                                <label class="form-label" for="country">Country <i class="text-danger">*</i></label>
                                                <select v-model="form.country" class="form-control" :class="{'is-invalid' : form.errors.country}" id="country" >
                                                    <option :value="null" disabled>Select Country</option>
                                                    <option :value="country.name" v-for="country in countries">{{ country.name }}</option>
                                                </select>
                                                <span class="invalid-feedback">{{ form.errors.country }}</span>
                                            </div>

                                            <div class="mb-6 form-password-toggle col-md-6">
                                                <label class="form-label" for="password">Password <i class="text-danger">*</i></label>
                                                <input
                                                    type="password"
                                                    id="password"
                                                    class="form-control"
                                                    name="password"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    v-model="form.password"
                                                    :class="{'is-invalid' : form.errors.password}"
                                                    aria-describedby="password" />
                                                <span class="invalid-feedback">{{ form.errors.password }}</span>
                                            </div>

                                            <div class="mb-6 form-password-toggle col-md-6">
                                                <label class="form-label" for="password">Confirm Password <i class="text-danger">*</i></label>
                                                <input
                                                    type="password"
                                                    id="password_confirmation"
                                                    class="form-control"
                                                    name="password_confirmation"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    v-model="form.password_confirmation"
                                                    :class="{'is-invalid' : form.errors.password_confirmation}"
                                                    aria-describedby="password_confirmation" />
                                                <span class="invalid-feedback">{{ form.errors.password_confirmation }}</span>
                                            </div>
                                        </div>


                                        <button type="submit" class="btn btn-primary d-grid w-100" :disabled="form.processing">
                                            <span v-if="!form.processing">Register</span>
                                            <i v-if="form.processing" class=" bx bx-loader bx-spin"></i>
                                        </button>
                                    </form>


                                    <p class="text-center">
                                        <span>Already have an account?</span>

                                        <Link style="margin-left: 5px" :href="route('login')">
                                            <span>Login</span>
                                        </Link>
                                    </p>
                                </div>
                            </div>
                            <!-- Register Card -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>

</style>
