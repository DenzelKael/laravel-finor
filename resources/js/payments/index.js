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
        text: 'El pago se conservará para mantener la trazabilidad contable.',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Motivo de la anulación',
        inputPlaceholder: 'Indique el motivo de la anulación...',
        inputAttributes: {
            maxlength: '255',
        },
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular pago',
        cancelButtonText: 'Cancelar',
        preConfirm: (reason) => {
            const trimmedReason = reason.trim();

            if (!trimmedReason) {
                Swal.showValidationMessage(
                    'Debe indicar el motivo de la anulación.'
                );

                return false;
            }

            return trimmedReason;
        },
    }).then((result) => {
        if (result.isConfirmed) {
            cancelPayment(id, url, result.value);
        }
    });
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
        } else {
            showAlert(
                'Ocurrió un error inesperado al anular el pago.',
                'danger'
            );
        }
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
    const flashMessage = sessionStorage.getItem('flash_message');

    if (flashMessage) {
        showAlert(flashMessage, 'success');
        sessionStorage.removeItem('flash_message');
    }
});