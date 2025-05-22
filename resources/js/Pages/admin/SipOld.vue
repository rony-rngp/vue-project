<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
    UserAgent,
    Registerer,
    Inviter,
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

const appendNumber = (num) => {
    targetNumber.value += num
}

const deleteLastDigit = () => {
    targetNumber.value = targetNumber.value.slice(0, -1)
}

const makeCall = async () => {
    if (!targetNumber.value || !userAgent) return

    const targetURI = UserAgent.makeURI(`sip:${targetNumber.value}@${props.sipDomain}`)
    if (!targetURI) {
        callStatus.value = 'Invalid number'
        return
    }

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
            transportOptions: {server: props.sipServer},
            authorizationUsername: props.sipUser,
            authorizationPassword: props.sipPassword,
            delegate: {
                onInvite: async (invitation) => {
                    callStatus.value = `Incoming call from ${invitation.remoteIdentity.uri.user}`
                    currentSession = invitation
                    try {
                        await invitation.accept()
                        playRemoteAudio(invitation)
                        callStatus.value = 'In call'
                    } catch (error) {
                        callStatus.value = 'Failed to accept call'
                    }
                },
            },
            sessionDescriptionHandlerFactoryOptions: {
                constraints: {audio: true, video: false},
            },
        })

        await userAgent.start()
        registerer = new Registerer(userAgent)
        await registerer.register()

        callStatus.value = 'Ready'
    } catch (error) {
        console.error('SIP init error:', error)
        callStatus.value = 'SIP connection failed'
    }
})

onBeforeUnmount(async () => {
    try {
        await registerer?.unregister()
        await userAgent?.stop()
    } catch (error) {
        console.error('Cleanup error:', error)
    }
})
</script>

<template>
    <div class="dialer-container">
<!--        <div class="contact-header">
            <div class="avatar">BS</div>
            <div class="info">
                <strong>Benjamin Schmitt</strong><br>
                <small>+1 831 200 <span class="text-warning">1112</span></small>
            </div>
            <div class="call-icon" @click="makeCall" title="Call">
                📞
            </div>
        </div>-->

        <div class="dial-display" style="display: flex; width: 100%; gap: 10px">

            <div style="width: 100%">
                {{ targetNumber !== '' ? targetNumber : 'Enter Number' }}
            </div>
            <div class="">
                <button style="background: #0E1B2B; color: white; border: 0" @click="deleteLastDigit">x</button>
            </div>

        </div>

        <div class="dialpad">
            <button @click="appendNumber('1')">1</button>
            <button @click="appendNumber('2')">2<br><small>ABC</small></button>
            <button @click="appendNumber('3')">3<br><small>DEF</small></button>
            <button @click="appendNumber('4')">4<br><small>GHI</small></button>
            <button @click="appendNumber('5')">5<br><small>JKL</small></button>
            <button @click="appendNumber('6')">6<br><small>MNO</small></button>
            <button @click="appendNumber('7')">7<br><small>PQRS</small></button>
            <button @click="appendNumber('8')">8<br><small>TUV</small></button>
            <button @click="appendNumber('9')">9<br><small>WXYZ</small></button>
            <button @click="appendNumber('*')">*</button>
            <button @click="appendNumber('0')">0</button>
            <button @click="appendNumber('#')">#</button>
        </div>

        <div class="text-center mt-3">
            <button v-if="callStatus.value !== 'Calling...' && callStatus.value !== 'In call'" class="call-button " @click="makeCall">📞</button>

            <button v-if="callStatus.value === 'Calling...' || callStatus.value ==='In call'" class="btn btn-danger btn-sm mt-2" @click="hangupCall">Hang Up</button>
        </div>

        <p class="footer mt-3 text-center">
            <small>Status: {{ callStatus }}</small>
        </p>

        <audio ref="remoteAudio" autoplay hidden></audio>
    </div>
</template>

<style scoped>

.dialer-container {
    background: #0e1b2b;
    color: white;
    width: 320px;
    border-radius: 20px;
    padding: 20px;
    margin: 60px auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.contact-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    background: #15273f;
    padding: 10px;
    border-radius: 10px;
}

.avatar {
    background: #33475b;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

.info {
    margin-left: 10px;
    flex-grow: 1;
}

.call-icon {
    background: #e4ffcb;
    color: #132f00;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.dial-display {
    font-size: 28px;
    text-align: center;
    margin: 10px 0;
    color: #b0ff1a;
}

.dialpad {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin: 20px 0;
}

.dialpad button {
    background: none;
    border: none;
    color: white;
    font-size: 22px;
    padding: 10px;
    border-radius: 10px;
    transition: background 0.2s;
}

.dialpad button:hover {
    background: rgba(255, 255, 255, 0.1);
}

.call-button {
    background: #c0ff1a;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 24px;
    color: black;
    cursor: pointer;
}
</style>
