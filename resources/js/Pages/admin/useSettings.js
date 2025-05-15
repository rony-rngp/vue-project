import { usePage } from '@inertiajs/vue3'

export function useSettings() {
    const page = usePage()

    function getSettings(key) {
        const settings = page.props.settings || {}
        const setting = settings[key]

        if (!setting) return null

        try {
            const parsed = JSON.parse(setting.value)
            return parsed ?? setting.value
        } catch {
            return setting.value
        }
    }

    return { getSettings }
}
