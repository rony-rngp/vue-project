import {defineStore} from 'pinia'
import {ref} from 'vue'

export const useSipStore = defineStore('sip', () => {

    const isDND = ref(false);

    const loadDND = () => {
        isDND.value =  isDND.value = localStorage.getItem('isDND') === 'true';
    }
    const toggleDND = () => {
        console.log(typeof isDND.value);
        isDND.value = !isDND.value;
        console.log(isDND.value);
        localStorage.setItem('isDND', isDND.value)
        /*if (isDND.value) {
            callStatus.value = 'DND enabled: Incoming calls will be rejected';
        } else {
            callStatus.value = 'DND disabled';
        }*/
    };
    return {
        isDND,
        loadDND,
        toggleDND,
    }
})
