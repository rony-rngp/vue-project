import { defineStore } from 'pinia'
import {computed, ref} from 'vue'
import { usePage } from '@inertiajs/vue3'
import ApiErrorHandler from "../utilis/apiErrorHandaler.blade.js";
import {useTicketStore} from "./ticketStore.js";

export const useSipStore = defineStore('sip', () => {
    const page = usePage()

    const ticketStore = useTicketStore();

    const currentUserId = computed(() => page.props.auth?.user?.id ?? null)

    const isDND = ref(false)
    const callHistoryList = ref([])
    const activeCall = ref(null)

    // Get user-specific storage key
    const getStorageKey = (key) => `user_${currentUserId.value}_${key}`

    const loadDND = () => {
        isDND.value = localStorage.getItem(getStorageKey('isDND')) === 'true'
    }

    const toggleDND = () => {
        isDND.value = !isDND.value
        localStorage.setItem(getStorageKey('isDND'), isDND.value)
    }

    const saveCallHistory = () => {
        localStorage.setItem(
            getStorageKey('callHistory'),
            JSON.stringify(callHistoryList.value)
        )
    }

    const loadCallHistory = () => {
        try {
            const history = JSON.parse(
                localStorage.getItem(getStorageKey('callHistory')) || '[]'
            )
            callHistoryList.value = history
                .filter(item => item && item.time && item.number)
                .slice(0, 50)
        } catch (e) {
            console.error('Failed to load call history:', e)
            callHistoryList.value = []
        }
    }

    const addCallRecord = (record) => {
        if (!record || !record.time) return

        record.id = record.id || Date.now()
        record.duration = record.duration || 0
        record.userId = currentUserId.value // Add user ID to record

        callHistoryList.value.unshift(record)
        saveCallHistory()
    }

    const updateCallRecord = (id, updates) => {
        const index = callHistoryList.value.findIndex(call => call.id === id);
        if (index !== -1) {
            // Merge updates with existing record
            callHistoryList.value[index] = {
                ...callHistoryList.value[index],
                ...updates,
                // Preserve these fields if not in updates
                userId: updates.userId || callHistoryList.value[index].userId,
                type: updates.type || callHistoryList.value[index].type,
                number: updates.number || callHistoryList.value[index].number
            };
            saveCallHistory();
        } else {
            console.warn(`Call record ${id} not found for update`);
        }
    };

    const clearCallHistory = () => {
        if (confirm('Are you sure you want to clear all call history for this user?')) {
            callHistoryList.value = []
            localStorage.removeItem(getStorageKey('callHistory'))
        }
    }


    //for contacts
    // Modal state
    const currentCaller = ref(null)
    const fromCall = ref(false)
    const findContactLoading = ref(false)

    const newContact = ref({
        name: '',
        caller_id: '',
        email: '',
        description: ''
    })


    // Find contact by caller ID
    const findContact = async (callerId) => {
        try {
            const response = await axios.get(route('admin.contacts.caller_api', callerId))
            return response.data
        } catch (error) {
            console.error('Error finding contact:', error)
            return null
        }
    }

    // Show caller modal with contact info or add form
    const showCallerInfo = async (callerId) => {

        fromCall.value = true;
        findContactLoading.value = true;

        // Clear previous state
        currentCaller.value = null;

        // Search for contact
        const res = await findContact(callerId);
        const contact = res.contact;

        if (contact != null) {
            // Existing contact found
            currentCaller.value = contact;
            findContactLoading.value = false;

            await ticketStore.getCallerTicketList(contact.id);

            return contact;

        } else {
            currentCaller.value = null;
            findContactLoading.value = false;
            return  null;
        }


    };

    // Save new contact

    const isLoading = ref(false);
    const saveContact = async (data) => {

        isLoading.value = true;

        try {
            const response = await axios.post(route('admin.contacts.caller_store_api'), data)
            const res =  response.data;
            if (res.status === true){
                currentCaller.value = res.contact;
                return { status: true, message: 'Contact added successfully' };
            }else{
                return { status: false, message: res.message };
            }

        } catch (error) {
            let regErrorMsg = ApiErrorHandler.getMessage(error);
            return { status: false, message: regErrorMsg };
        }finally {
            isLoading.value = false;
        }
    }



    return {
        isDND,
        loadDND,
        toggleDND,
        callHistoryList,
        activeCall,
        loadCallHistory,
        saveCallHistory,
        addCallRecord,
        updateCallRecord,
        clearCallHistory,


        currentCaller,
        newContact,
        showCallerInfo,
        saveContact,
        findContact,
        fromCall,
        findContactLoading,
        isLoading
    }
})
