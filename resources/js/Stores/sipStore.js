import { defineStore } from 'pinia'
import {computed, ref} from 'vue'
import { usePage } from '@inertiajs/vue3'

export const useSipStore = defineStore('sip', () => {
    const page = usePage()

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
    const showCallerModal = ref(false)
    const currentCaller = ref(null)
    const showContactForm = ref(false)
    const newContact = ref({
        name: '',
        caller_id: '',
        email: '',
        description: ''
    })


    // Find contact by caller ID
    const findContact = async (callerId) => {
        try {
            const response = await axios.get(`/admin/contacts-info-api/${callerId}`)
            return response.data
        } catch (error) {
            console.error('Error finding contact:', error)
            return null
        }
    }

    // Show caller modal with contact info or add form
    const showCallerInfo = async (callerId) => {
        // Clear previous state
        currentCaller.value = null;
        showContactForm.value = false;

        // Search for contact
        const res = await findContact(callerId);
        const contact = res.contact;

        if (contact != null) {
            // Existing contact found
            currentCaller.value = {
                name: contact.name,
                caller_id: contact.caller_id,
                email: contact.email,
                description: contact.description
            };
            showContactForm.value = false;
            showCallerModal.value = true;
            return contact;

        } else {
            currentCaller.value = null;
            newContact.value = {
                name: '',
                caller_id: callerId,
                email: '',
                description: ''
            };
            showContactForm.value = true;
            showCallerModal.value = true;
            return  null;
        }


    };

    // Save new contact
    const saveContact = async () => {
        try {
            const response = await axios.post('/admin/contacts-store-api', newContact.value)
            currentCaller.value = response.data
            showContactForm.value = false
            return true
        } catch (error) {
            console.error('Error saving contact:', error)
            return false
        }
    }

    // Close modal
    const closeCallerModal = () => {
        showCallerModal.value = false
        showContactForm.value = false
        currentCaller.value = null
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


        showCallerModal,
        currentCaller,
        showContactForm,
        newContact,
        showCallerInfo,
        saveContact,
        closeCallerModal,
        findContact
    }
})
