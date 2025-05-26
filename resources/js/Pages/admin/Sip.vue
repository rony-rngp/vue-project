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
const transferNumber = ref('')
const remoteAudio = ref(null)
const callStatus = ref('')
const showTransferInput = ref(false)
const showConferenceInput = ref(false)
const dtmfSequence = ref('')

const isCalling = ref(false)
const isOnHold = ref(false)

let userAgent = null
let registerer = null
let currentSession = null
let conferenceSession = null
let conferenceStream = new MediaStream()

const appendNumber = (num) => {
    if (showTransferInput.value || showConferenceInput.value) {
        transferNumber.value += num
    } else {
        targetNumber.value += num
        if (callStatus.value === 'In call') sendDTMF(num)
    }
}

const deleteLastDigit = () => {
    if (showTransferInput.value || showConferenceInput.value) {
        transferNumber.value = transferNumber.value.slice(0, -1)
    } else {
        targetNumber.value = targetNumber.value.slice(0, -1)
    }
}

const sendDTMF = (tone) => {
    if (!currentSession) return
    try {
        currentSession.sessionDescriptionHandler.sendDtmf(tone)
        dtmfSequence.value += tone
        callStatus.value = `Sent DTMF: ${dtmfSequence.value}`
        setTimeout(() => {
            if (callStatus.value.includes('Sent DTMF')) callStatus.value = 'In call'
        }, 1000)
    } catch (error) {
        console.error('DTMF failed:', error)
        callStatus.value = 'DTMF failed'
    }
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
    dtmfSequence.value = ''

    try {
        await inviter.invite()
    } catch (error) {
        console.error('Call failed:', error)
        callStatus.value = 'Call failed'
        currentSession = null
        return
    }

    inviter.stateChange.addListener((state) => {
        if (state === SessionState.Established) {
            callStatus.value = 'In call'
            playRemoteAudio(inviter)
        } else if (state === SessionState.Terminated) {
            callStatus.value = 'Call ended'
            currentSession = null
            showTransferInput.value = false
            showConferenceInput.value = false
            dtmfSequence.value = ''
            isOnHold.value = false
        }
    })
}

const hangupCall = async () => {
    try {
        if (currentSession) await currentSession.bye()
        if (conferenceSession) await conferenceSession.bye()
    } catch (e) {
        console.error('Hangup failed', e)
    } finally {
        currentSession = null
        conferenceSession = null
        callStatus.value = 'Call ended'
        showTransferInput.value = false
        showConferenceInput.value = false
        isOnHold.value = false
        conferenceStream = new MediaStream()
    }
}

const toggleHold = async () => {
    if (!currentSession) return
    try {
        const pc = currentSession.sessionDescriptionHandler.peerConnection
        const enabled = !isOnHold.value
        pc.getSenders().forEach(sender => {
            if (sender.track && sender.track.kind === 'audio') sender.track.enabled = enabled
        })
        await currentSession.invite()
        isOnHold.value = !isOnHold.value
        callStatus.value = isOnHold.value ? 'Call on hold' : 'In call'
    } catch (e) {
        console.error('Hold failed', e)
        callStatus.value = 'Hold failed'
    }
}

const transferCall = async () => {
    if (!currentSession || !transferNumber.value) return
    try {
        const targetURI = UserAgent.makeURI(`sip:${transferNumber.value}@${props.sipDomain}`)
        if (!targetURI) {
            callStatus.value = 'Invalid transfer number'
            return
        }
        await currentSession.refer(targetURI)
        callStatus.value = `Transferring to ${transferNumber.value}`
        transferNumber.value = ''
        showTransferInput.value = false
    } catch (error) {
        console.error('Transfer failed:', error)
        callStatus.value = 'Transfer failed'
    }
}

const startConference = async () => {
    if (!currentSession || !transferNumber.value || !userAgent) return
    try {
        const targetURI = UserAgent.makeURI(`sip:${transferNumber.value}@${props.sipDomain}`)
        if (!targetURI) {
            callStatus.value = 'Invalid conference number'
            return
        }

        const inviter = new Inviter(userAgent, targetURI)
        conferenceSession = inviter
        callStatus.value = `Calling ${transferNumber.value} to join conference...`

        await inviter.invite()

        inviter.stateChange.addListener((state) => {
            if (state === SessionState.Established) {
                callStatus.value = `Conference ongoing`
                mixConferenceAudio(currentSession, inviter)
            } else if (state === SessionState.Terminated) {
                callStatus.value = 'Conference call ended'
                conferenceSession = null
            }
        })

        transferNumber.value = ''
        showConferenceInput.value = false
    } catch (error) {
        console.error('Conference failed:', error)
        callStatus.value = 'Conference failed'
    }
}

const mixConferenceAudio = (session1, session2) => {
    try {
        conferenceStream = new MediaStream()
        const peer1 = session1.sessionDescriptionHandler.peerConnection
        const peer2 = session2.sessionDescriptionHandler.peerConnection

        peer1.getReceivers().forEach(receiver => {
            if (receiver.track) conferenceStream.addTrack(receiver.track)
        })
        peer2.getReceivers().forEach(receiver => {
            if (receiver.track) conferenceStream.addTrack(receiver.track)
        })

        remoteAudio.value.srcObject = conferenceStream
    } catch (e) {
        console.error('Conference audio mix failed', e)
    }
}

const playRemoteAudio = (session) => {
    try {
        const peer = session.sessionDescriptionHandler.peerConnection
        const remoteStream = new MediaStream()
        peer.getReceivers().forEach(receiver => {
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
            delegate: {
                onInvite: async (invitation) => {
                    callStatus.value = `Incoming call from ${invitation.remoteIdentity.uri.user}`
                    currentSession = invitation
                    try {
                        await invitation.accept()
                        playRemoteAudio(invitation)
                        callStatus.value = 'In call'
                    } catch (e) {
                        callStatus.value = 'Failed to accept call'
                    }
                }
            },
            sessionDescriptionHandlerFactoryOptions: {
                constraints: { audio: true, video: false },
                dtmfType: 'info',
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
        if (currentSession) await currentSession.bye()
        if (conferenceSession) await conferenceSession.bye()
        await registerer?.unregister()
        await userAgent?.stop()
    } catch (error) {
        console.error('Cleanup error:', error)
    }
})
</script>



<template>
    <div class="dialer-container">
        <div class="dial-display" style="display: flex; width: 100%; gap: 10px">
            <div style="width: 100%">
                {{ showTransferInput || showConferenceInput ?
                (transferNumber !== '' ? transferNumber : 'Enter Number') :
                (targetNumber !== '' ? targetNumber : 'Enter Number') }}
            </div>
            <div class="">
                <button style="background: #0E1B2B; color: white; border: 0" @click="deleteLastDigit">x</button>
            </div>
        </div>

        <div v-if="showTransferInput && callStatus === 'In call'" class="transfer-ui">
            <input
                v-model="transferNumber"
                placeholder="Enter number to transfer to"
                class="transfer-input"
                @keyup.enter="transferCall"
            />
            <div class="transfer-buttons">
                <button @click="transferCall" class="btn-transfer">Transfer</button>
                <button @click="showTransferInput = false" class="btn-cancel">Cancel</button>
            </div>
        </div>

        <div v-if="showConferenceInput && callStatus === 'In call'" class="conference-ui">
            <input
                v-model="transferNumber"
                placeholder="Enter number to conference"
                class="conference-input"
                @keyup.enter="startConference"
            />
            <div class="conference-buttons">
                <button @click="startConference" class="btn-conference">Add to Conference</button>
                <button @click="showConferenceInput = false" class="btn-cancel">Cancel</button>
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

        <div class="action-buttons">
            <button v-if="callStatus !== 'Calling...' && callStatus !== 'In call' && callStatus !== 'Call on hold'"
                    class="call-button" @click="makeCall">📞</button>

            <div v-if="callStatus === 'In call'" class="call-controls">
                <button class="btn-transfer" @click="showTransferInput = true">Transfer</button>
                <button class="btn-conference" @click="showConferenceInput = true">Conference</button>
                <button
                    class="btn-hold"
                    @click="toggleHold"
                    :class="{ 'btn-active': isOnHold }"
                >
                    {{ isOnHold ? 'Resume' : 'Hold' }}
                </button>
                <button class="btn-hangup" @click="hangupCall">Hang Up</button>
            </div>

            <div v-if="callStatus === 'Call on hold'" class="call-controls">
                <button
                    class="btn-hold"
                    @click="toggleHold"
                    :class="{ 'btn-active': isOnHold }"
                >
                    {{ isOnHold ? 'Resume' : 'Hold' }}
                </button>
                <button class="btn-hangup" @click="hangupCall">Hang Up</button>
            </div>

            <button v-if="callStatus === 'Calling...'"
                    class="btn-hangup" @click="hangupCall">Hang Up</button>
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

.dial-display {
    font-size: 28px;
    text-align: center;
    margin: 10px 0;
    color: #b0ff1a;
    background: #15273f;
    padding: 10px;
    border-radius: 10px;
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
    background: #15273f;
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

.transfer-ui, .conference-ui {
    background: #15273f;
    padding: 15px;
    border-radius: 10px;
    margin: 10px 0;
}

.transfer-input, .conference-input {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #33475b;
    background: #0e1b2b;
    color: white;
    margin-bottom: 10px;
}

.transfer-buttons, .conference-buttons {
    display: flex;
    gap: 10px;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.call-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
    justify-content: center;
}

.btn-transfer {
    background: #4a6da7;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-conference {
    background: #5a9e5f;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-hold {
    background: #f0ad4e;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-active {
    background: #5bc0de;
}

.btn-hangup {
    background: #d9534f;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.footer {
    color: #b0ff1a;
}
</style>
