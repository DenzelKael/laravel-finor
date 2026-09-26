import api, { ApiError } from '../commons/api-client.js';

function showAlert(message, type = 'success') {
    document.getElementById('alert-container').innerHTML =
        `<div class="alert alert-${type} m-3">${message}</div>`;
    setTimeout(() => document.getElementById('alert-container').innerHTML = '', 3000);
}

function confirmDelete(id, url) {
    Swal.fire({
        title: 'Estas seguro',
        text: 'Este cliente sera eliminado permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
    }).then((result) => {
        if (result.isConfirmed) deleteClient(id, url);
    });
}

async function deleteClient(id, url) {
    try {
        const data = await api.delete(url);
        document.getElementById(`client-row-${id}`).remove();
        showAlert(data.message);
    } catch (error) {
        if (!(error instanceof ApiError)) throw error;

        switch (error.type) {
            case 'not_found':
                document.getElementById(`client-row-${id}`)?.remove();
                showAlert('El cliente ya no existía.', 'warning');
                break;
            default:
                showAlert(error.message, 'danger');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const flashMessage = sessionStorage.getItem('flash_message');
    if (flashMessage) {
        showAlert(flashMessage);
        sessionStorage.removeItem('flash_message');
    }
});

window.confirmDelete = confirmDelete; // necesario para el onclick inline del botón
