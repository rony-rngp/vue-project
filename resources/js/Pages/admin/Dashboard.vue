<script setup>
import {Head} from "@inertiajs/vue3";

import { ref, onMounted, onBeforeUnmount } from 'vue';
import { io } from 'socket.io-client';

const transcription = ref('');
let socket;

onMounted(() => {
    socket = io('http://115.127.135.9:4001');
    socket.on('connect', () => {
        console.log('Socket.io connected:', socket.id);
    });

    socket.on('transcription', (text) => {
        console.log('New Transcription:', text);
        transcription.value = text;
    });
});

onBeforeUnmount(() => {
    if (socket) {
        socket.disconnect();
    }
});

</script>

<template>
<div>
    <Head>
        <title> Dashboard</title>
    </Head>

    <div>
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-md-12 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2>Live Translate</h2>
                            <p v-if="!transcription">No Transcription </p>
                            <p v-else>{{ transcription }}</p>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

</div>
</template>

<style scoped>

</style>
