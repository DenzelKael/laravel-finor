import api, { ApiError } from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

document.addEventListener('DOMContentLoaded', () => {
    // Mensaje de éxito al volver de crear/editar
    const flash = sessionStorage.getItem('flash_message');
    if (flash) {
        sessionStorage.removeItem('flash_message');
        SwalMessages.success(flash);
    }

    document.querySelectorAll('.btn-delete-plan').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const confirmed = await SwalMessages.confirmDelete('Este plan');
            if (!confirmed) return;

            try {
                const data = await api.delete(`/plans/${btn.dataset.id}`);
                btn.closest('tr').remove();
                SwalMessages.success(data.message);
            } catch (error) {
                if (error instanceof ApiError) {
                    SwalMessages.error(error.message);
                } else {
                    throw error;
                }
            }
        });
    });
});