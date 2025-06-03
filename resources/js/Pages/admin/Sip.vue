<script setup>
import {ref, onMounted, onBeforeUnmount, computed, reactive} from 'vue'
import {
    UserAgent,
    Registerer,
    Inviter,
    SessionState,
} from 'sip.js'
import Pagination from "../Pagination.vue";
import {formatTime} from "../helper.js";

const props = defineProps({
    sipUser: String,
    sipPassword: String,
    sipServer: String,
    sipDomain: String,
})

/*// State variables
const targetNumber = ref('')
const transferNumber = ref('')
const remoteAudio = ref(null)
const callStatus = ref('Ready')
const dtmfSequence = ref('')

// Boolean state flags
const showTransferInput = ref(false)
const showConferenceInput = ref(false)
const isCalling = ref(false)
const isIncomingCall = ref(false)
const isActiveCall = ref(false)
const isOnHold = ref(false)
const isInConference = ref(false)
const isMuted = ref(false);


let userAgent = null
let registerer = null
let currentSession = null
let conferenceSession = null
let conferenceStream = new MediaStream()

// Computed properties for button states
const showCallButton = computed(() =>
    !isCalling.value && !isActiveCall.value && !isIncomingCall.value && targetNumber.value.length > 0
)

const showHangupButton = computed(() =>
    isCalling.value || isActiveCall.value || isIncomingCall.value
)

const showTransferButton = computed(() =>
    isActiveCall.value && !showTransferInput.value && !isInConference.value
)

const showConferenceButton = computed(() =>
    isActiveCall.value && !showConferenceInput.value && !isInConference.value
)

const showHoldButton = computed(() =>
    isActiveCall.value && !showTransferInput.value && !showConferenceInput.value
)


let callLogEntry = null
const callHistory = ref([])
const saveCallHistory = () => {
    localStorage.setItem('callHistory', JSON.stringify(callHistory.value))
}

const loadCallHistory = () => {
    const fullHistory = JSON.parse(localStorage.getItem('callHistory') || '[]')
    callHistory.value = fullHistory.slice(0, 50)
}

const clearCallHistory = () => {
    if (confirm('Are you sure?')){
        localStorage.removeItem('callHistory')
        callHistory.value = []
    }
}

const appendNumber = (num) => {
    if (showTransferInput.value || showConferenceInput.value) {
        transferNumber.value += num
    } else {
        targetNumber.value += num
        if (isActiveCall.value) sendDTMF(num)
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

    if (targetNumber.value === props.sipUser) {
        callStatus.value = 'You cannot call your own number';
        return;
    }

    const targetURI = UserAgent.makeURI(`sip:${targetNumber.value}@${props.sipDomain}`)
    if (!targetURI) {
        callStatus.value = 'Invalid number'
        return
    }

    isCalling.value = true
    const inviter = new Inviter(userAgent, targetURI)
    currentSession = inviter
    callStatus.value = 'Calling...'
    dtmfSequence.value = ''

    //for call history
    callLogEntry = {
        type: 'outgoing',
        number: targetNumber.value,
        status: 'pending',
        time: new Date().toISOString()
    }
    callHistory.value.unshift(callLogEntry)
    saveCallHistory()
    //for call history

    // Setup state listeners before sending invite
    inviter.stateChange.addListener((state) => {
        console.log('Call state changed to:', state)
        if (state === SessionState.Established) {
            isCalling.value = false
            isActiveCall.value = true
            callStatus.value = 'In call'
            playRemoteAudio(inviter)

            //for call history
            callLogEntry.status = 'answered'
            saveCallHistory()

        } else if (state === SessionState.Terminated) {
            callStatus.value = 'Call ended'
            resetCallState()
            //for call history
            if (callLogEntry.status === 'pending') {
                callLogEntry.status = 'missed'
                saveCallHistory()
                loadCallHistory()
            }
        }
    })

    try {
        await inviter.invite({
            sessionDescriptionHandlerOptions: {
                constraints: {audio: true, video: false}
            }
        })
    } catch (error) {
        console.error('Call failed:', error)
        callStatus.value = 'Call failed'
        resetCallState()
    }
}

const answerCall = async () => {
    if (!currentSession) return
    try {
        await currentSession.accept({
            sessionDescriptionHandlerOptions: {
                constraints: {audio: true, video: false}
            }
        })
        isIncomingCall.value = false
        isActiveCall.value = true
        callStatus.value = 'In call'
        playRemoteAudio(currentSession)
    } catch (e) {
        console.error('Answer failed', e)
        callStatus.value = 'Failed to answer call'
        resetCallState()
    }
}

const cancelCall = async () => {
    try {
        if (!currentSession) return;

        const state = currentSession.state;

        if (state === SessionState.Initial || state === SessionState.Establishing) {
            // Cancel the outgoing INVITE (before call is established)
            await currentSession.cancel();
        } else if (state === SessionState.Established) {
            // Call fully established, end the call with BYE
            await currentSession.bye();
        } else {
            console.warn('Session is in unexpected state:', state);
        }
    } catch (error) {
        console.error('Cancel/hangup call failed:', error);
    } finally {
        resetCallState();
        callStatus.value = 'Call ended';
    }
};

const hangupCall = async () => {
    try {
        if (currentSession) {
            if (currentSession.state === 'Established') {
                await currentSession.bye()
            } else if (currentSession.state === 'Initial') {
                await currentSession.reject()
            }
        }

        if (conferenceSession) {
            if (conferenceSession.state === 'Established') {
                await conferenceSession.bye()
            } else if (conferenceSession.state === 'Initial') {
                await conferenceSession.reject()
            }
        }
    } catch (e) {
        console.error('Hangup failed', e)
    } finally {
        callStatus.value = 'Call ended'
        resetCallState()
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

const toggleMute = () => {
    if (!currentSession) return;

    const pc = currentSession.sessionDescriptionHandler.peerConnection;
    if (!pc) return;

    // Get all local audio tracks
    const senders = pc.getSenders();
    senders.forEach(sender => {
        if (sender.track && sender.track.kind === 'audio') {
            sender.track.enabled = isMuted.value; // Toggle mute
        }
    });

    isMuted.value = !isMuted.value;
};

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
        isInConference.value = true
        callStatus.value = `Calling ${transferNumber.value} to join conference...`

        inviter.stateChange.addListener((state) => {
            if (state === SessionState.Established) {
                callStatus.value = `Conference ongoing`
                mixConferenceAudio(currentSession, inviter)
            } else if (state === SessionState.Terminated) {
                callStatus.value = 'Conference call ended'
                conferenceSession = null
                isInConference.value = false
            }
        })

        await inviter.invite()
        transferNumber.value = ''
        showConferenceInput.value = false
    } catch (error) {
        console.error('Conference failed:', error)
        callStatus.value = 'Conference failed'
        isInConference.value = false
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
        remoteAudio.value.play().catch(e => console.error('Audio play failed:', e))
    } catch (error) {
        console.error('Audio setup failed:', error)
    }
}

const resetCallState = () => {
    isCalling.value = false
    isIncomingCall.value = false
    isActiveCall.value = false
    isOnHold.value = false
    isInConference.value = false
    showTransferInput.value = false
    showConferenceInput.value = false
    currentSession = null
    conferenceSession = null
    dtmfSequence.value = ''
    conferenceStream = new MediaStream()
}

onMounted(async () => {

    loadCallHistory();

    try {
        const uri = UserAgent.makeURI(`sip:${props.sipUser}@${props.sipDomain}`)
        userAgent = new UserAgent({
            uri,
            transportOptions: {
                server: props.sipServer
            },
            authorizationUsername: props.sipUser,
            authorizationPassword: props.sipPassword,
            delegate: {
                onInvite: async (invitation) => {

                    const fromNumber = invitation.remoteIdentity.uri.user

                    if (isDND.value) {
                        // Automatically reject the call
                        await invitation.reject();
                        callStatus.value = `Call from ${invitation.remoteIdentity.uri.user} rejected due to DND`;
                        return;
                    }

                    currentSession = invitation
                    isIncomingCall.value = true
                    callStatus.value = `Incoming call from ${invitation.remoteIdentity.uri.user}`

                    //add call history
                    let incomingCallEntry = {
                        type: 'incoming',
                        number: fromNumber,
                        status: 'pending',
                        time: new Date().toISOString()
                    }
                    callHistory.value.unshift(incomingCallEntry)
                    saveCallHistory()

                    // Add onCancel to react if caller cancels the call
                    invitation.delegate = {
                        onCancel: () => {
                            console.log('Caller canceled the call')
                            callStatus.value = 'Caller canceled the call'
                            isIncomingCall.value = false
                            resetCallState()

                            //add call history
                            incomingCallEntry.status = 'missed'
                            saveCallHistory()

                        }
                    }

                    // Attach session state listener
                    invitation.stateChange.addListener((state) => {
                        console.log('Incoming call state:', state)
                        if (state === SessionState.Established) {
                            isIncomingCall.value = false
                            isActiveCall.value = true
                            callStatus.value = 'In call'
                            playRemoteAudio(invitation)

                            incomingCallEntry.status = 'answered'
                            saveCallHistory()

                        } else if (state === SessionState.Terminated) {

                            if (incomingCallEntry.status === 'pending') {
                                incomingCallEntry.status = 'missed'
                                saveCallHistory()
                            }

                            callStatus.value = 'Call ended'
                            resetCallState()
                        }
                    })
                }
            },
            sessionDescriptionHandlerFactoryOptions: {
                constraints: {audio: true, video: false},
                peerConnectionConfiguration: {
                    iceServers: [{urls: 'stun:stun.l.google.com:19302'}]
                },
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
})*/

const isDND = ref(false);

const toggleDND = () => {
    isDND.value = !isDND.value;
    if (isDND.value) {
        callStatus.value = 'DND enabled: Incoming calls will be rejected';
    } else {
        callStatus.value = 'DND disabled';
    }
};

onMounted(async () => {

    loadCallHistory();

});

let callLogEntry = null
const callHistory = ref([])
const saveCallHistory = () => {
    localStorage.setItem('callHistory', JSON.stringify(callHistory.value))
}

const loadCallHistory = () => {
    const fullHistory = JSON.parse(localStorage.getItem('callHistory') || '[]')
    callHistory.value = fullHistory.slice(0, 50)
}

const clearCallHistory = () => {
    if (confirm('Are you sure?')){
        localStorage.removeItem('callHistory')
        callHistory.value = []
    }
}



</script>

<template>
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center" >
                        <h5 class="card-header">Call History</h5>
                        <div class="me-5">
                            <button @click="clearCallHistory" class="btn btn-primary btn-sm"> Clear History</button>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Number</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            <tr v-for="call in callHistory">
                                <td>{{ call.number }}</td>
                                <td>{{ call.type }}</td>
                                <td>{{ call.status }}</td>
                                <td>{{ formatTime(call.time) }}</td>
                            </tr>

                            <tr v-if="callHistory.length === 0">
                                <td colspan="4" class="text-center text-danger">No Call Found</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

<!--            <div class="col-md-5">
                <div class="dialer-container" style="margin-top: 0 !important;">
                    <div class="dial-display" style="display: flex; width: 100%; gap: 10px">
                        <div style="width: 100%">
                            {{
                                showTransferInput || showConferenceInput ?
                                    (transferNumber !== '' ? transferNumber : 'Enter Number') :
                                    (targetNumber !== '' ? targetNumber : 'Enter Number')
                            }}
                        </div>
                        <div class="">
                            <button style="background: #0E1B2B; color: white; border: 0" @click="deleteLastDigit">x</button>
                        </div>
                    </div>

                    <div v-if="showTransferInput && isActiveCall" class="transfer-ui">
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

                    <div v-if="showConferenceInput && isActiveCall" class="conference-ui">
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
                        &lt;!&ndash; Call button &ndash;&gt;
                        <button
                            v-if="showCallButton"
                            class="call-button"
                            @click="makeCall"
                        >
                            📞
                        </button>

                        &lt;!&ndash; Incoming call buttons &ndash;&gt;
                        <div v-if="isIncomingCall" class="call-controls">
                            <button class="btn-accept" @click="answerCall">Answer</button>
                            <button class="btn-hangup" @click="hangupCall">Decline</button>
                        </div>

                        &lt;!&ndash; Active call controls &ndash;&gt;
                        <div v-if="isActiveCall" class="call-controls">
                            <button
                                v-if="showTransferButton"
                                class="btn-transfer"
                                @click="showTransferInput = true; showConferenceInput = false"
                            >
                                Transfer
                            </button>
                            <button
                                v-if="showConferenceButton"
                                class="btn-conference"
                                @click="showConferenceInput = true; showTransferInput = false"
                            >
                                Conference
                            </button>
                            <button
                                v-if="showHoldButton"
                                class="btn-hold"
                                @click="toggleHold"
                                :class="{ 'btn-active': isOnHold }"
                            >
                                {{ isOnHold ? 'Resume' : 'Hold' }}
                            </button>

                            <button class="btn-transfer" @click="toggleMute">{{ isMuted ? 'Unmute' : 'Mute' }}</button>

                            <button class="btn-hangup" @click="hangupCall">Hang Up</button>
                        </div>

                        &lt;!&ndash; Outgoing call hangup &ndash;&gt;
                        <button
                            v-if="isCalling"
                            class="btn-hangup"
                            @click="cancelCall"
                        >
                            Cancel Call
                        </button>
                    </div>

                    <p class="footer mt-3 text-center">
                        <small>Status: {{ callStatus }}</small>
                    </p>

                    <audio ref="remoteAudio" autoplay hidden></audio>
                </div>
            </div>-->

        </div>


    </div>

</template>

<style scoped>
/* Add this new style for the answer button */
.btn-accept {
    background: #5a9e5f;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

/* Keep all your existing styles */
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
