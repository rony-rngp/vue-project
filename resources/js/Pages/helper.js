export function getImageUrl(path) {
    return path ? `${window.location.origin}/storage/${path}` : '';
}
