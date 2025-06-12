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
        const index = callHistoryList.value.findIndex(call => call.id === id)
        if (index !== -1) {
            callHistoryList.value[index] = {
                ...callHistoryList.value[index],
                ...updates,
                userId: currentUserId.value // Ensure user ID stays current
            }
            saveCallHistory()
        }
    }

    const clearCallHistory = () => {
        if (confirm('Are you sure you want to clear all call history for this user?')) {
            callHistoryList.value = []
            localStorage.removeItem(getStorageKey('callHistory'))
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
