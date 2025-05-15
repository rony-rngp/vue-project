<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";

const form = useForm({
   email: null,
   password: null,
});

const submit = ()=>{
    form.post('/admin/login');
}

</script>

<template>
<div>
    <Head>
        <title>Admin Login</title>
    </Head>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y ">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-heading fw-bold">Admin Login</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <br>
                        <form class="mb-8" @submit.prevent="submit" method="post">

                            <div class="mb-6">
                                <label for="email" class="form-label">Email or Username</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    :class="{'is-invalid' : form.errors.email}"
                                    id="email"
                                    v-model="form.email"
                                    placeholder="Enter your email or username"
                                    autofocus />
                                <span class="invalid-feedback">{{ form.errors.email }}</span>
                            </div>
                            <div class="mb-6 ">
                                <label class="form-label" for="password">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    :class="{'is-invalid' : form.errors.password}"
                                    v-model="form.password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="invalid-feedback">{{ form.errors.password }}</span>
                            </div>

                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100" :disabled="form.processing" type="submit">
                                    <span v-if="!form.processing">Login</span>
                                    <i v-if="form.processing" class=" bx bx-loader bx-spin"></i>

                                </button>
                            </div>
                        </form>


                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>

</style>
