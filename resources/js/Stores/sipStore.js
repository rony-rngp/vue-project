import {defineStore} from 'pinia'
import {ref} from 'vue'

export const useSipStore = defineStore('sip', () => {

    const isDND = ref(false);
    const callHistoryList = ref([])
    const activeCall = ref(null);


    const loadDND = () => {
        isDND.value =  isDND.value = localStorage.getItem('isDND') === 'true';
    }
    const toggleDND = () => {
        isDND.value = !isDND.value;
        localStorage.setItem('isDND', isDND.value)
    };


    const saveCallHistory = () => {
        localStorage.setItem('callHistory', JSON.stringify(callHistoryList.value))
    }

    const loadCallHistory = () => {
        try {
            const history = JSON.parse(localStorage.getItem('callHistory') || '[]');
            // Ensure we have valid data
            callHistoryList.value = history
                .filter(item => item && item.time && item.number)
                .slice(0, 100); // Keep reasonable limit
        } catch (e) {
            console.error('Failed to load call history:', e);
            callHistoryList.value = [];
        }
    }

    // Add new call record
    const addCallRecord = (record) => {
        if (!record || !record.time) return;

        // Set default values if missing
        record.id = record.id || Date.now();
        record.duration = record.duration || 0;

        callHistoryList.value.unshift(record);
        saveCallHistory();
    }

    // Update existing call record (for duration, status changes)
    const updateCallRecord = (id, updates) => {
        const index = callHistoryList.value.findIndex(call => call.id === id);
        if (index !== -1) {
            callHistoryList.value[index] = {
                ...callHistoryList.value[index],
                ...updates
            };
            saveCallHistory();
        }
    }

    const clearCallHistory = () => {
        if (confirm('Are you sure you want to clear all call history?')) {
            callHistoryList.value = [];
            saveCallHistory();
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
        clearCallHistory
    }
})
