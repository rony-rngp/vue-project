<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";

import {useSettings} from "../../admin/useSettings.js";
const {getSettings} = useSettings();

import { useToast } from 'vue-toast-notification'
const toast = useToast()


const form = useForm({
    email: null,
    password: null,
    remember: null
});

const submit = ()=>{
    form.post(route('login'));
}

</script>

<template>
    <div>
        <Head>
            <title> Login</title>
        </Head>
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-md-4 m-auto">
                    <div class="authentication-wrapper  container-p-y">
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
                                    <h4 class="mb-1">Welcome to {{ getSettings('website_name') }} 👋🚀</h4>
                                    <p class="mb-6">Please login here to start your journey</p>

                                    <form class="mb-4" @submit.prevent="submit" method="post">

                                        <div class="mb-6">
                                            <label class="form-label" for="email">Email <i class="text-danger">*</i></label>
                                            <input type="email" class="form-control" v-model="form.email" :class="{'is-invalid' : form.errors.email}" id="email"  placeholder="Enter your email">
                                            <span class="invalid-feedback">{{ form.errors.email }}</span>
                                        </div>


                                        <div class="mb-6 form-password-toggle">
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

                                        <div class="mb-6">
                                            <div class="d-flex justify-content-between">
                                                <div class="form-check mb-0 ms-2">
                                                    <input class="form-check-input" name="remember" v-model="form.remember" value="1" type="checkbox" id="remember-me">
                                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary d-grid w-100" :disabled="form.processing">
                                            <span v-if="!form.processing">Login</span>
                                            <i v-if="form.processing" class=" bx bx-loader bx-spin"></i>
                                        </button>
                                    </form>


                                    <p class="text-center">
                                        <span>New on our platform?</span>
                                        <Link style="margin-left: 5px" :href="route('register')">
                                            <span>Create an account</span>
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
