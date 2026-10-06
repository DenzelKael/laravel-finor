import api, { ApiError } from '../commons/api-client.js';

const tableBody = document.getElementById('clients-table-body');

function showAlert(message, type = 'success') {
    const container = document.getElementById('alert-container');
    if (!container) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} m-3`;
    alert.textContent = message;

    container.replaceChildren(alert);
    setTimeout(() => container.replaceChildren(), 3000);
}

async function deleteClient(id, url) {
    const result = await Swal.fire({
        title: 'Estas seguro',
        text: 'Este cliente sera eliminado permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
    });

    if (!result.isConfirmed) return;

    try {
        const data = await api.delete(url);
        document.getElementById(`client-row-${id}`)?.remove();
        showAlert(data.message);
    } catch (error) {
        if (!(error instanceof ApiError)) throw error;

        if (error.type === 'not_found') {
            document.getElementById(`client-row-${id}`)?.remove();
            showAlert('El cliente ya no existía.', 'warning');
        } else {
            showAlert(error.message, 'danger');
        }
    }
}

// Delegación de eventos: un solo listener en el contenedor,
// sin onclick inline por botón ni exponer nada en window.
tableBody?.addEventListener('click', (e) => {
    const button = e.target.closest('[data-action="delete-client"]');
    if (!button) return;

    deleteClient(button.dataset.id, button.dataset.url);
});

document.addEventListener('DOMContentLoaded', () => {
    const flashMessage = sessionStorage.getItem('flash_message');
    if (flashMessage) {
        showAlert(flashMessage);
        sessionStorage.removeItem('flash_message');
    }
});
