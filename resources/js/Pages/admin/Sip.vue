<script setup>
import {Head} from "@inertiajs/vue3";

import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
    UserAgent,
    Registerer,
    Inviter,
    Invitation,
    SessionState,
} from 'sip.js'

const props = defineProps({
    sipUser: String,
    sipPassword: String,
    sipServer: String,
    sipDomain: String,
})

const targetNumber = ref('')
const remoteAudio = ref(null)
const callStatus = ref('')

let userAgent = null
let registerer = null
let currentSession = null

const makeCall = async () => {
    if (!targetNumber.value || !userAgent) return

    const targetURI = `sip:${targetNumber.value}@${props.sipDomain}`
    const inviter = new Inviter(userAgent, targetURI)

    currentSession = inviter

    callStatus.value = 'Calling...'
    try {
        await inviter.invite()
    } catch (error) {
        console.error('Call failed:', error)
        callStatus.value = 'Call failed'
        return
    }

    inviter.stateChange.addListener((state) => {
        if (state === SessionState.Established) {
            callStatus.value = 'In call'
            playRemoteAudio(inviter)
        } else if (state === SessionState.Terminated) {
            callStatus.value = 'Call ended'
            currentSession = null
        }
    })
}

const hangupCall = async () => {
    if (currentSession) {
        try {
            await currentSession.bye?.()
        } catch (error) {
            console.error('Hangup failed:', error)
        }
        callStatus.value = 'Call ended'
        currentSession = null
    }
}

const handleIncomingCall = async (invitation) => {
    callStatus.value = `Incoming call from ${invitation.remoteIdentity.uri.user}`
    currentSession = invitation

    invitation.stateChange.addListener((state) => {
        if (state === SessionState.Established) {
            callStatus.value = 'In call'
            playRemoteAudio(invitation)
        } else if (state === SessionState.Terminated) {
            callStatus.value = 'Call ended'
            currentSession = null
        }
    })

    try {
        await invitation.accept()
    } catch (error) {
        console.error('Failed to accept call:', error)
        callStatus.value = 'Failed to accept call'
    }
}

const playRemoteAudio = (session) => {
    try {
        const peer = session.sessionDescriptionHandler.peerConnection
        const remoteStream = new MediaStream()
        peer.getReceivers().forEach((receiver) => {
            if (receiver.track) remoteStream.addTrack(receiver.track)
        })
        remoteAudio.value.srcObject = remoteStream
    } catch (error) {
        console.error('Audio setup failed:', error)
    }
}

onMounted(async () => {
    try {
        const uri = UserAgent.makeURI(`sip:${props.sipUser}@${props.sipDomain}`)
        userAgent = new UserAgent({
            uri,
            transportOptions: { server: props.sipServer },
            authorizationUsername: props.sipUser,
            authorizationPassword: props.sipPassword,
            delegate: { onInvite: handleIncomingCall },
            sessionDescriptionHandlerFactoryOptions: {
                constraints: { audio: true, video: false },
            },
        })

        await userAgent.start()
        registerer = new Registerer(userAgent)
        await registerer.register()

        callStatus.value = 'Ready to call'
    } catch (error) {
        console.error('SIP initialization failed:', error)
        callStatus.value = 'SIP connection failed'
    }
})

onBeforeUnmount(async () => {
    try {
        await registerer?.unregister()
        await userAgent?.stop()
    } catch (error) {
        console.error('Cleanup failed:', error)
    }
})

</script>

<template>
    <div>
        <Head>
            <title> Sip</title>
        </Head>

        <div>
            <div class="container-xxl flex-grow-1 container-p-y">

                <div class="row">

                    <div class="col-md-12">

                        <div class="p-4 max-w-md mx-auto">
                            <h2 class="text-xl font-bold mb-4">Browser Phone</h2>

                            <div class="col-md-6">
                                <input
                                    v-model="targetNumber"
                                    placeholder="Enter number (e.g. 1002)"
                                    class="form-control mb-4"
                                />
                                <button @click="makeCall" class="btn btn-sm btn-success me-3">
                                    Call
                                </button>
                                <button @click="hangupCall" class="btn btn-sm btn-danger">
                                    Hang Up
                                </button>
                            </div>

                            <p v-if="callStatus" class="mt-2 mb-2 text-danger">Status: {{ callStatus }}</p>

                            <audio ref="remoteAudio" autoplay controls class="w-full"></audio>
                        </div>


                    </div>

                </div>



            </div>
        </div>

    </div>
</template>

<style scoped>

</style>
