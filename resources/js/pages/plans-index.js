import api, { ApiError } from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

document.addEventListener('DOMContentLoaded', () => {
    const flash = sessionStorage.getItem('flash_message');
    if (flash) {
        sessionStorage.removeItem('flash_message');
        SwalMessages.success(flash);
    }

    document.querySelectorAll('.btn-delete-plan').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const confirmed = await SwalMessages.confirmDelete('Este plan');
            if (!confirmed) return;

            const { id, url } = btn.dataset;

            try {
                const data = await api.delete(url);
                document.getElementById(`plan-row-${id}`)?.remove();
                SwalMessages.success(data.message);
            } catch (error) {
                if (!(error instanceof ApiError)) throw error;

                if (error.type === 'not_found') {
                    document.getElementById(`plan-row-${id}`)?.remove();
                    SwalMessages.warning('El plan ya no existía.');
                } else {
                    SwalMessages.error(error.message);
                }
            }
        });
    });
});