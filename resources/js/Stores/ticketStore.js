import { defineStore } from 'pinia'
import {computed, ref} from 'vue'
import { usePage } from '@inertiajs/vue3'
import ApiErrorHandler from "../utilis/apiErrorHandaler.blade.js";

export const useTicketStore = defineStore('ticket', () => {
    const page = usePage()

    const currentUserId = computed(() => page.props.auth?.user?.id ?? null)


    const callerTicketList = ref(null);
    const ticketDetails = ref(null);

    const isLoading = ref(false);
    const showTicketLoading = ref(false);

    // Find contact by caller ID
    const getCallerTicketList = async (contact_id) => {
        showTicketLoading.value = true;
        callerTicketList.value = null;
        try {
            const response = await axios.get(route('admin.getCallerTicketList', contact_id))
            callerTicketList.value = response.data
        } catch (error) {
            callerTicketList.value = [];
            console.error('Error finding contact:', error)
        }finally {
            showTicketLoading.value = false;
        }
    }

    // Find contact by caller ID
    const getTicketDetails = async (id) => {
        ticketDetails.value = null;
        try {
            const response = await axios.get(route('admin.getTicketDetails', id))
            ticketDetails.value = response.data
        } catch (error) {
            ticketDetails.value = null;
            console.error('Error finding contact:', error)
        }

    }

    const saveTicket = async (data) => {
        isLoading.value = true;
        try {
            const response = await axios.post(route('admin.ticketStore', data))

            await getCallerTicketList(data.contact_id);
            return { status: true, message: 'Ticket added successfully', ticket_id: response.data };
        } catch (error) {
            let regErrorMsg = ApiErrorHandler.getMessage(error);
            return { status: false, message: regErrorMsg };
        }finally {
            isLoading.value = false;
        }
    }

    const updateTicketStatus = async (status) => {
        try {

            const data = {
                ticket_id: ticketDetails.value.id,
                status: status,
            };

            const response = await axios.post(route('admin.updateTicketStatus', data))

            // Update in ticketDetails if it's the current ticket
            ticketDetails.value.status = status;

            // Update in callerTicketList array
            if (callerTicketList.value) {
                callerTicketList.value = callerTicketList.value.map(ticket => {
                    if (ticket.id === ticketDetails.value.id) {
                        return { ...ticket, status: status };
                    }
                    return ticket;
                });
            }

            return { status: true, message: 'Status has been updated' };
        } catch (error) {
            let regErrorMsg = ApiErrorHandler.getMessage(error);
            return { status: false, message: regErrorMsg };
        }
    }

    const assignTicket = async (ticketID, current_contact) => {
        isLoading.value = true;
        try {
            const response = await axios.get(route('admin.assignTicket', ticketID))
            return { status: true, message: response.data };
        } catch (error) {
            let regErrorMsg = ApiErrorHandler.getMessage(error);
            return { status: false, message: regErrorMsg };
        }finally {
            isLoading.value = false;
        }
    }



    return {
        callerTicketList,
        getCallerTicketList,
        getTicketDetails,
        ticketDetails,
        saveTicket,
        isLoading,
        showTicketLoading,
        updateTicketStatus,
        assignTicket
    }
})
