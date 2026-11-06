import api, { ApiError } from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');

    if (!alertContainer) {
        return;
    }

    const alert = document.createElement('div');
    alert.className =
        `alert alert-${type} alert-dismissible fade show m-3`;

    alert.setAttribute('role', 'alert');

    const messageElement = document.createElement('span');
    messageElement.textContent = message;

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'close';
    closeButton.setAttribute('data-dismiss', 'alert');
    closeButton.setAttribute('aria-label', 'Close');

    const closeIcon = document.createElement('span');
    closeIcon.setAttribute('aria-hidden', 'true');
    closeIcon.textContent = '×';

    closeButton.appendChild(closeIcon);

    alert.appendChild(messageElement);
    alert.appendChild(closeButton);

    alertContainer.replaceChildren(alert);

    setTimeout(() => {
        alertContainer.replaceChildren();
    }, 3500);
}

async function confirmCancelPayment(id, url) {
    const reason = await SwalMessages.confirmWithReason(
        'El pago conservará su registro contable.',
        '¿Está seguro de anular este pago?',
        'Sí, anular pago'
    );

    if (!reason) {
        return;
    }

    await cancelPayment(id, url, reason);
}

async function cancelPayment(id, url, reason) {
    try {
        const data = await api.patch(url, {
            void_reason: reason,
        });

        showAlert(
            data.message || 'Pago anulado correctamente.',
            'success'
        );

        const badgeEl = document.getElementById(
            `payment-status-${id}`
        );

        if (badgeEl) {
            badgeEl.className = 'badge badge-danger';
            badgeEl.textContent = 'Anulado';
        }

        const actionBtn = document.getElementById(
            `payment-action-${id}`
        );

        if (actionBtn) {
            actionBtn.remove();
        }
    } catch (error) {
        if (error instanceof ApiError) {
            showAlert(error.message, 'danger');
            return;
        }

        showAlert(
            'Ocurrió un error inesperado al anular el pago.',
            'danger'
        );
    }
}

document.addEventListener('click', function (event) {
    const cancelButton = event.target.closest(
        '[data-action="cancel-payment"]'
    );

    if (!cancelButton) {
        return;
    }

    const id = cancelButton.dataset.paymentId;
    const url = cancelButton.dataset.url;

    confirmCancelPayment(id, url);
});

document.addEventListener('DOMContentLoaded', function () {
    const flashMessage =
        sessionStorage.getItem('flash_message');

    if (!flashMessage) {
        return;
    }

    showAlert(flashMessage, 'success');
    sessionStorage.removeItem('flash_message');
});