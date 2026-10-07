import api, { ApiError } from '../commons/api-client.js';

function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');

    if (!alertContainer) {
        return;
    }

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
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

function confirmCancelPayment(id, url) {
    Swal.fire({
        title: '¿Está seguro de anular este pago?',
        text: 'Esta acción cambiará el estado del pago a ANULADO y conservará el registro contable.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular pago',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            cancelPayment(id, url);
        }
    });
}

async function cancelPayment(id, url) {
    try {
        const data = await api.patch(url);
        showAlert(data.message || 'Pago anulado correctamente.', 'success');

        // Actualizar visualmente la fila del pago
        const badgeEl = document.getElementById(`payment-status-${id}`);
        if (badgeEl) {
            badgeEl.className = 'badge badge-danger';
            badgeEl.textContent = 'Anulado';
        }

        const actionBtn = document.getElementById(`payment-action-${id}`);
        if (actionBtn) {
            actionBtn.remove();
        }
    } catch (error) {
        if (error instanceof ApiError) {
            showAlert(error.message, 'danger');
        } else {
            showAlert('Ocurrió un error inesperado al anular el pago.', 'danger');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const flashMessage = sessionStorage.getItem('flash_message');
    if (flashMessage) {
        showAlert(flashMessage, 'success');
        sessionStorage.removeItem('flash_message');
    }
});


