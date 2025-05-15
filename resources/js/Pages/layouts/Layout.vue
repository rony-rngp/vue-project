<script setup>

import Header from "./Header.vue";

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
            duration: 5000
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
    <Header/>
    <main>
        <slot></slot>
    </main>
</div>
</template>

<style scoped>

</style>
