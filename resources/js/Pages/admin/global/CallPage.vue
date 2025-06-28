<script setup>
import {useSipStore} from "../../../Stores/sipStore.js";
import {onMounted, ref, watch} from "vue";
import {useToast} from "vue-toast-notification";
import {useTicketStore} from "../../../Stores/ticketStore.js";
import Pagination from "../../Pagination.vue";
import {dateFormatYmd} from "../../helper.js";

const props = defineProps({
    callerId: String // This will come from URL parameter
})


const toast = useToast()
const sipStore = useSipStore()
const ticketStore = useTicketStore()


onMounted(async () => {
    if (sipStore.fromCall === false){
        if (props.callerId) {
            await sipStore.showCallerInfo(props.callerId)
        }
    }
})

const name = ref('');
const caller_id = ref(props.callerId);
const email = ref('');
const description = ref('');

const handleSave = async () => {

    if (name.value === ''){
        toast.error('The name filed is required',{
            position: 'top-right',
            duration: 5000
        });
        return false;
    }

    if (caller_id.value === ''){
        toast.error('The caller id filed is required',{
            position: 'top-right',
            duration: 5000
        });
        return false;
    }

    const userData = {
        name: name.value,
        caller_id: caller_id.value,
        email: email.value,
        description: description.value,
    };

    const response = await sipStore.saveContact(userData)

    if (response.status === true) {
        toast.success(response.message,{
            position: 'top-right',
            duration: 5000
        })
    }else{
        toast.error(response.message,{
            position: 'top-right',
            duration: 5000
        })
    }
}



//for ticket
const subject = ref('');
const ticket_des = ref('');

const storeTicket = async () => {

    if (subject.value === ''){
        toast.error('The subject filed is required',{
            position: 'top-right',
            duration: 5000
        });
        return false;
    }

    const ticketData = {
        subject: subject.value,
        contact_id: sipStore.currentCaller.id,
        description: ticket_des.value
    };

    const response = await ticketStore.saveTicket(ticketData)

    if (response.status === true) {
        toast.success(response.message,{
            position: 'top-right',
            duration: 5000
        });
        subject.value = null;
        ticket_des.value = null;
    }else{
        toast.error(response.message,{
            position: 'top-right',
            duration: 5000
        })
    }
}


//for updated status
const selectedStatus = ref('');
watch(() => ticketStore.ticketDetails, (newTicket) => {
    if (newTicket) {
        selectedStatus.value = newTicket.status;
    }
}, { immediate: true });

const handleStatusChange = async () => {
    if (!selectedStatus.value) return;

    const result = await ticketStore.updateTicketStatus(selectedStatus.value);

    if (result.status) {
        toast.success(result.message, {
            position: 'top-right',
            duration: 5000
        });
    } else {
        toast.error(result.message, {
            position: 'top-right',
            duration: 5000
        });
        // Revert to previous status if update failed
        selectedStatus.value = ticketStore.ticketDetails?.status || '';
    }
};

</script>

<template>
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-md-12 mb-6">
                <div class="card" v-if="!sipStore.findContactLoading">

                    <div class="d-flex justify-content-between align-items-center border-bottom">
                         <h5 class="card-header">{{ sipStore.currentCaller ? `Contact: ${sipStore.currentCaller.name}` : 'New Contact' }}</h5>
                    </div>

                    <!-- Modal Body -->
                    <div class="card-body">
                        <!-- Existing Contact Info -->
                        <div v-if="sipStore.currentCaller != null">
                            <div class="mb-3">
                                <p><strong> ID:</strong> {{ sipStore.currentCaller.id }}</p>
                                <p><strong>Caller ID:</strong> {{ sipStore.currentCaller.caller_id }}</p>
                                <p><strong>Name:</strong> {{ sipStore.currentCaller.name }}</p>
                                <p v-if="sipStore.currentCaller.email"><strong>Email:</strong> {{ sipStore.currentCaller.email }}</p>
                                <p v-if="sipStore.currentCaller.description"><strong>Notes:</strong> {{ sipStore.currentCaller.description }}</p>
                            </div>
                        </div>

                        <!-- Contact Form (for new or editing existing) -->
                        <form @submit.prevent="handleSave" v-if="sipStore.currentCaller == null">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name*</label>
                                    <input
                                        v-model="name"
                                        type="text"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Caller ID*</label>
                                    <input readonly
                                           v-model="caller_id"
                                           type="text"
                                           class="form-control"
                                           required
                                           :readonly="!!caller_id"
                                    >
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Email</label>
                                    <input
                                        v-model="email"
                                        type="email"
                                        class="form-control"
                                    >
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Notes</label>
                                    <textarea
                                        v-model="description"
                                        class="form-control"
                                        rows="3"
                                    ></textarea>
                                </div>

                                <div>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="!name || !caller_id"
                                    >
                                        Save Contact
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
                <div class="card" v-else>
                    <div class="card-body" style="height: 300px; display: flex; align-items: center">
                       <div class="loader"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-12" v-if="sipStore.currentCaller">
                <div class="card">

                    <div class="d-flex  align-items-center" >
                        <h5 class="card-header">Ticket List</h5>
                        <div class="me-5">
                            <button v-if="sipStore.currentCaller"
                                type="button"
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#addTicketModal"
                            >
                                Add Ticket
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Ticket No</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            <tr v-for="ticket in ticketStore.callerTicketList">
                                <td>{{ ticket.ticket_no }}</td>
                                <td>{{ ticket.subject }}</td>
                                <td>{{ ticket.status }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#ticketModal"
                                        @click="ticketStore.getTicketDetails(ticket.id)"
                                    >
                                        View Ticket
                                    </button>
                                </td>
                            </tr>


                            <tr v-if="ticketStore.callerTicketList == null || ticketStore.callerTicketList?.length === 0">
                                <td colspan="4" class="text">
                                    No ticket found
                                </td>
                            </tr>

                            <tr v-if="ticketStore.showTicketLoading">
                                <td colspan="4">
                                    <div class="loader"></div>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                    </div>


                </div>
            </div>

        </div>

        <div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom" style="padding-top: 13px">
                        <h5 class="modal-title" style="margin-bottom: 9px;">Ticket Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                       <div v-if="ticketStore.ticketDetails">
                           <p><strong>Subject:</strong> {{ ticketStore.ticketDetails.subject }}</p>

                           <div class="d-flex align-items-center mb-6" style="gap: 20px">
                               <strong>Status:</strong>

                               <select class="form-control " v-model="selectedStatus"  @change="handleStatusChange" style="width: 200px">
                                   <option value="Open">Open</option>
                                   <option value="Closed">Closed</option>
                               </select>
                           </div>
                           <p v-if="ticketStore.ticketDetails.created_at"><strong>Created At:</strong> {{ dateFormatYmd(ticketStore.ticketDetails.created_at) }}</p>
                           <p><strong>Description:</strong> {{ ticketStore.ticketDetails.description }}</p>

                       </div>
                        <div v-else>
                            <div class="loader"></div>
                        </div>
                    </div>


                </div>
            </div>
        </div>


        <div class="modal fade" v-if="sipStore.currentCaller" id="addTicketModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom" style="padding-top: 13px">
                        <h5 class="modal-title" style="margin-bottom: 9px;">Add Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form @submit.prevent="storeTicket">
                            <div >
                                <div class="mb-3">
                                    <label class="form-label">Subject*</label>
                                    <input
                                        v-model="subject"
                                        type="text"
                                        class="form-control"
                                        required
                                    >
                                </div>


                                <div class="mb-3 ">
                                    <label class="form-label">Description</label>
                                    <textarea
                                        v-model="ticket_des"
                                        class="form-control"
                                        rows="3"
                                    ></textarea>
                                </div>

                                <div>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="!subject">
                                        <span v-if="!ticketStore.isLoading">Submit</span>
                                        <span v-else>Please wait</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>


    </div>

</template>

<style scoped>
.loader {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #3498db;
    border-radius: 50%;
    width: 46px;
    height: 46px;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
