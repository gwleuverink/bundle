export default function outputToId(id, message) {
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById(id).innerHTML = message
    })
}
