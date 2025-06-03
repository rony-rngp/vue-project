<script setup>

import {Head} from "@inertiajs/vue3";
import AdminHeader from "./partial/AdminHeader.vue";
import AdminSidebar from "./partial/AdminSidebar.vue";

import {onMounted, ref, watch} from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toast-notification'
import Sip from "../../admin/Sip.vue";
import GlobalSipDailer from "../../admin/global/GlobalSipDailer.vue";

const page = usePage()
const toast = useToast()

const initMenu = () => {
    const menuEl = document.querySelector('#layout-menu');
    if (menuEl) {
        const menuInstance = new Menu(menuEl, {
            accordion: true,
            animate: true,
        });
        window.MenuInstance = menuInstance;
    }

    let menuTogglers = document.querySelectorAll('.layout-menu-toggle');
    menuTogglers.forEach(toggler => {
        toggler.addEventListener('click', e => {
            e.preventDefault();
            if (window.Helpers && window.Helpers.toggleCollapsed) {
                window.Helpers.toggleCollapsed();
            }
        });
    });
}

onMounted(() => {
    initMenu();
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(() => console.log('✅ SW registered'))
            .catch(err => console.error('SW failed:', err));
    }
});

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


const hasIncomingCall = ref(false);

const handleIncomingCall = (isIncoming) => {
    hasIncomingCall.value = isIncoming;
    if (isIncoming) {
        $('#check').prop('checked', true)
    }
};

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

    <input type="checkbox" id="check" />
    <label class="chat-btn" for="check">
        <i class="bx bx-comment comment"></i>
        <i class="bx bx-exit  close"></i>
    </label>

    <div class="wrapper">
        <GlobalSipDailer @incoming-call="handleIncomingCall" />
    </div>


</div>
</template>

<style scoped>
.chat-btn {
    position: absolute;
    right: 14px;
    bottom: 30px;
    cursor: pointer
}

.chat-btn .close {
    display: none
}

.chat-btn i {
    transition: all 0.9s ease
}

#check:checked~.chat-btn i {
    display: block;
    pointer-events: auto;
    transform: rotate(180deg)
}

#check:checked~.chat-btn .comment {
    display: none
}

.chat-btn i {
    font-size: 22px;
    color: #fff !important
}

.chat-btn {
    width: 50px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50px;
    background-color: blue;
    color: #fff;
    font-size: 22px;
    border: none
}

.wrapper {
    position: absolute;
    right: 20px;
    bottom: 100px;
    width: 300px;
    background-color: #fff;
    border-radius: 5px;
    display: none;
    transition: all 0.4s
}

#check:checked~.wrapper {
    display: block;
}

.header {
    padding: 13px;
    background-color: blue;
    border-radius: 5px 5px 0px 0px;
    margin-bottom: 10px;
    color: #fff
}

.chat-form {
    padding: 15px
}

.chat-form input,
textarea,
button {
    margin-bottom: 10px
}

.chat-form textarea {
    resize: none
}

.form-control:focus,
.btn:focus {
    box-shadow: none
}

.btn,
.btn:focus,
.btn:hover {
    background-color: blue;
    border: blue
}

#check {
    display: none !important
}
</style>
