<script setup>

import {Head} from "@inertiajs/vue3";
import AdminHeader from "./partial/AdminHeader.vue";
import AdminSidebar from "./partial/AdminSidebar.vue";

import {onMounted, ref, watch} from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toast-notification'
import Sip from "../../admin/Sip.vue";
import GlobalSipDailer from "../../admin/global/GlobalSipDailer.vue";
import CallerModal from "../../admin/global/callerModal.vue";

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




const draggableWrapper = ref(null)
let startX, startY, initialX, initialY

const startDrag = (e) => {
    if (!e.target.matches('input, select, textarea, button')) {
        e.preventDefault();
        startX = e.clientX;
        startY = e.clientY;
        initialX = draggableWrapper.value.offsetLeft;
        initialY = draggableWrapper.value.offsetTop;

        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDrag);
        draggableWrapper.value.style.cursor = 'grabbing';
    }
}

const drag = (e) => {
    const dx = e.clientX - startX;
    const dy = e.clientY - startY;

    // Calculate new position
    let newX = initialX + dx;
    let newY = initialY + dy;


    //herader
    let header = document.querySelector('.layout-navbar');
    const sidebar = document.querySelector('#layout-menu');

    // Define your boundaries
    const headerHeight = header.offsetHeight +16 ?? 0; // Adjust to your header height
    const sidebarWidth = sidebar.offsetWidth ?? 0; // Adjust to your sidebar width
    const wrapperWidth = draggableWrapper.value.offsetWidth;
    const wrapperHeight = draggableWrapper.value.offsetHeight;

    // Constrain within viewport boundaries
    newX = Math.max(sidebarWidth, Math.min(newX, window.innerWidth - wrapperWidth));
    newY = Math.max(headerHeight, Math.min(newY, window.innerHeight - wrapperHeight));

    // Apply constrained position
    draggableWrapper.value.style.left = `${newX}px`;
    draggableWrapper.value.style.top = `${newY}px`;
};

const stopDrag = () => {
    document.removeEventListener('mousemove', drag)
    document.removeEventListener('mouseup', stopDrag)
    draggableWrapper.value.style.cursor = 'grab'
}


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
        <i class="bx bx-phone-call comment"></i>
        <i class="<!--bx bx-exit-->  close">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </i>

    </label>

    <div class="wrapper" ref="draggableWrapper">

        <GlobalSipDailer  @mousedown="startDrag" @incoming-call="handleIncomingCall" />
    </div>

    <caller-modal></caller-modal>


</div>
</template>

<style scoped>
.chat-btn {
    position: fixed;
    right: 14px;
    bottom: 30px;
    cursor: pointer;

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
    position: fixed;
    right: 20px;
    bottom: 90px;
    width: 300px;
    //background-color: #fff;
    border-radius: 5px;
    display: none;
    transition: left 0.1s, top 0.1s;
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
