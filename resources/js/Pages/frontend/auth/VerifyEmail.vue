<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";

import {useSettings} from "../../admin/useSettings.js";
import {onMounted, ref} from "vue";
const {getSettings} = useSettings();

const { props } = usePage();
const email = props.email;

const form = useForm({
    otp: null,
    email: email
});

const submit = ()=>{
    form.post(route('email_verify'));
}

const data = useForm({
    identifier: email,
    type: 'email'
});


const countdown = ref(0);

const startCountdown = () => {
    countdown.value = 60;
    timer = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            clearInterval(timer);
        }
    }, 1000);
};

// Send OTP and start countdown
const resend = () => {
    if (countdown.value > 0) return;
    data.post(route('resend_otp'), {
        onSuccess: () => {
            startCountdown();
        }
    });
};

onMounted(() => {
    startCountdown(); // optional: start on first page load
});

let timer = null;

</script>

<template>
    <div>
        <Head>
            <title> Email Verification</title>
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
                                        <a  class="app-brand-link gap-2">
                                        <span class="app-brand-logo demo">
                                          </span>
                                            <span class="app-brand-text demo text-heading fw-bold">Email Verification</span>
                                        </a>
                                    </div>

                                    <h5 class="text-center">We've send a verification code to {{ email }} </h5>

                                    <!-- /Logo -->
                                    <form class="mb-4" @submit.prevent="submit" method="post">

                                        <div class="mb-2">
                                            <label class="form-label mb-2" for="otp">Enter Otp <i class="text-danger">*</i></label>
                                            <input type="number" class="form-control" v-model="form.otp" :class="{'is-invalid' : form.errors.otp}" id="otp"  placeholder="Enter otp code">
                                            <span class="invalid-feedback">{{ form.errors.otp }}</span>
                                        </div>


                                        <div class="col-lg-6 col-sm-6 mb-3">
                                                <a
                                                    class="forget d-inline-flex align-items-center"
                                                    :style="{
                                                      cursor: countdown > 0 || data.processing ? 'not-allowed' : 'pointer',
                                                      color: countdown > 0 || data.processing ? '#aaa' : '',
                                                      pointerEvents: countdown > 0 || data.processing ? 'none' : 'auto'
                                                    }"
                                                    @click="resend"
                                                >
                                                <span v-if="!data.processing">Resend OTP</span>
                                                <template v-else>
                                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                    Please wait...
                                                </template>
                                                <span v-if="countdown > 0 && !data.processing" class="ms-1">({{ countdown }}s)</span>
                                            </a>
                                        </div>

                                        <button type="submit" class="btn btn-primary d-grid w-100" :disabled="form.processing">
                                            <span v-if="!form.processing">Verify</span>
                                            <i v-if="form.processing" class=" bx bx-loader bx-spin"></i>
                                        </button>

                                    </form>

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
