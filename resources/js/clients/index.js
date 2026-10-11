import api, { ApiError } from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

const tableBody = document.getElementById('clients-table-body');

async function deleteClient(id, url) {
    const confirmed = await SwalMessages.confirmDelete('Este cliente');
    if (!confirmed) return;

    try {
        const data = await api.delete(url);
        document.getElementById(`client-row-${id}`)?.remove();
        SwalMessages.success(data.message);
    } catch (error) {
        if (!(error instanceof ApiError)) throw error;

        if (error.type === 'not_found') {
            document.getElementById(`client-row-${id}`)?.remove();
            SwalMessages.warning('El cliente ya no existía.');
        } else {
            SwalMessages.error(error.message);
        }
    }
}

tableBody?.addEventListener('click', (e) => {
    const button = e.target.closest('[data-action="delete-client"]');
    if (!button) return;

    deleteClient(button.dataset.id, button.dataset.url);
});

document.addEventListener('DOMContentLoaded', () => {
    const flashMessage = sessionStorage.getItem('flash_message');
    if (flashMessage) {
        SwalMessages.success(flashMessage);
        sessionStorage.removeItem('flash_message');
    }
});
