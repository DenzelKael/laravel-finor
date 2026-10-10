import api, { ApiError } from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

document.addEventListener('DOMContentLoaded', () => {
    const flashMessage = sessionStorage.getItem('flash_message');

    if (flashMessage) {
        sessionStorage.removeItem('flash_message');
        SwalMessages.success(flashMessage);
    }

    document.querySelectorAll('.btn-renew-subscription').forEach((button) => {
        button.addEventListener('click', async () => {
            const confirmed = await SwalMessages.confirm(
                'Se actualizará el período de vigencia de esta suscripción.',
                '¿Deseas renovar la suscripción?'
            );

            if (!confirmed) return;

            button.disabled = true;

            try {
                const response = await api.patch(button.dataset.renewUrl, {});
                await SwalMessages.success(response.message);
                window.location.reload();
            } catch (error) {
                if (error instanceof ApiError) {
                    await SwalMessages.error(error.message);
                } else {
                    await SwalMessages.error('Ocurrió un error al renovar la suscripción.');
                }
            } finally {
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('.btn-cancel-subscription').forEach((button) => {
        button.addEventListener('click', async () => {
            const confirmed = await SwalMessages.confirm(
                'La suscripción quedará marcada como cancelada.',
                '¿Deseas cancelar la suscripción?'
            );

            if (!confirmed) return;

            button.disabled = true;

            try {
                const response = await api.patch(button.dataset.cancelUrl, {});
                await SwalMessages.success(response.message);
                window.location.reload();
            } catch (error) {
                if (error instanceof ApiError) {
                    await SwalMessages.error(error.message);
                } else {
                    await SwalMessages.error('Ocurrió un error al cancelar la suscripción.');
                }
            } finally {
                button.disabled = false;
            }
        });
    });
});