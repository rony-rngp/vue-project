<script setup>
import {Head} from "@inertiajs/vue3";

import {ref, onMounted, onBeforeUnmount, watch} from 'vue';
import { io } from 'socket.io-client';

const transcription = ref('');   // full accumulating transcript
const transcriptBox = ref(null); // div container ref for auto scroll

let socket;

onMounted(() => {
    socket = io('https://crm.asteriskbd.com:4001', {
        transports: ['websocket'],
        secure: true,
        rejectUnauthorized: false, // only for dev/self-signed cert
    });

    socket.on('connect', () => {
        console.log('Socket connected:', socket.id);
    });

    socket.on('transcription', (text) => {
        // Append new text to existing transcript, separated by space
        transcription.value += (transcription.value ? ' ' : '') + text;
    });

    socket.on('disconnect', () => {
        console.log('Socket disconnected');
    });
});

// Auto-scroll down when transcription updates
watch(transcription, () => {
    if (transcriptBox.value) {
        transcriptBox.value.scrollTop = transcriptBox.value.scrollHeight;
    }
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
                            <h2>Live Translation</h2>
                            <div
                                ref="transcriptBox"
                                style="height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; white-space: pre-wrap; font-family: monospace; background: #fafafa;"
                            >
                                {{ transcription }}
                            </div>
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
