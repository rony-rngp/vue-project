export function getImageUrl(path) {
    return path ? `${window.location.origin}/storage/${path}` : '';
}

export function formatTime(time) {
    return new Date(time).toLocaleString()
}
