<script setup>

import {Head} from "@inertiajs/vue3";
import AdminHeader from "./partial/AdminHeader.vue";
import AdminSidebar from "./partial/AdminSidebar.vue";

import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toast-notification'

const page = usePage()
const toast = useToast()

watch(
    () => page.props.flash,
    (flash) => {
        const success = typeof flash.success === 'function' ? flash.success() : flash.success
        const error = typeof flash.error === 'function' ? flash.error() : flash.error

        if (success) toast.success(success,{
            position: 'top-right',
            duration: 5000,
        })
        if (error) toast.error(error,{
            position: 'top-right',
            duration: 5000
        })
    },
    { immediate: true }
)

</script>

<template>
<div>
    <Head>
        <title>Admin Dashboard</title>
    </Head>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <AdminSidebar v-if="$page.props.auth.user.user_type === 'admin'"  />
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <AdminHeader v-if="$page.props.auth.user.user_type === 'admin'" />
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <slot></slot>
                    <!-- / Content -->


                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->


</div>
</template>

<style scoped>

</style>
