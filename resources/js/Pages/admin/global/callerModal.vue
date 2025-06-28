<script setup>
import { onMounted, ref } from 'vue'
import { Modal } from 'bootstrap'
import {useSipStore} from "../../../Stores/sipStore.js";

const sipStore = useSipStore()
let modal = null

/*onMounted(() => {
    const modalEl = document.getElementById('callerModal')
    if (modalEl) {
        modal = new Modal(modalEl)

        sipStore.$subscribe(() => {
            if (sipStore.showCallerModal) {
                modal.show()
            } else {
                modal.hide()
            }
        })
    }
})*/

const handleSave = async () => {
    const success = await sipStore.saveContact()
    if (success) {
        //modal.hide()
    }
}
</script>

<template>
    <div class="modal fade" id="callerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header border-bottom" style="padding-top: 13px">
                    <h5 class="modal-title" style="margin-bottom: 9px;">
                        {{ sipStore.currentCaller ? `Contact: ${sipStore.currentCaller.name}` : 'New Contact' }}
                    </h5>
                    <button type="button" class="btn-close" @click="sipStore.closeCallerModal"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Existing Contact Info -->
                    <div v-if="sipStore.currentCaller && !sipStore.showContactForm">
                        <div class="mb-3">
                            <p><strong>Caller ID:</strong> {{ sipStore.currentCaller.caller_id }}</p>
                            <p><strong>Name:</strong> {{ sipStore.currentCaller.name }}</p>
                            <p v-if="sipStore.currentCaller.email"><strong>Email:</strong> {{ sipStore.currentCaller.email }}</p>
                            <p v-if="sipStore.currentCaller.description"><strong>Notes:</strong> {{ sipStore.currentCaller.description }}</p>
                        </div>
                    </div>

                    <!-- Contact Form (for new or editing existing) -->
                    <div v-if="sipStore.showContactForm || !sipStore.currentCaller">
                        <div class="mb-3">
                            <label class="form-label">Name*</label>
                            <input
                                v-model="sipStore.newContact.name"
                                type="text"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Caller ID*</label>
                            <input readonly
                                v-model="sipStore.newContact.caller_id"
                                type="text"
                                class="form-control"
                                required
                                :readonly="!!sipStore.currentCaller"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                v-model="sipStore.newContact.email"
                                type="email"
                                class="form-control"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea
                                v-model="sipStore.newContact.description"
                                class="form-control"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="sipStore.closeCallerModal"
                    >
                        Close
                    </button>

                    <button
                        v-if="sipStore.showContactForm || !sipStore.currentCaller"
                        type="button"
                        class="btn btn-primary"
                        @click="handleSave"
                        :disabled="!sipStore.newContact.name || !sipStore.newContact.caller_id"
                    >
                        {{ sipStore.currentCaller ? 'Update' : 'Save' }} Contact
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
