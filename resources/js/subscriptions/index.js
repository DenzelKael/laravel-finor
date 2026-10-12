import ApiClient from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('subscriptions-table-body');

    // Mensaje de éxito al volver de crear/editar vía BaseForm
    const flash = sessionStorage.getItem('flash_message');
    if (flash) {
        sessionStorage.removeItem('flash_message');
        SwalMessages.success(flash);
    }

    function createEmptyRow(message, isError = false) {
        tableBody.innerHTML = '';
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 6;
        cell.className = isError ? 'text-center text-danger' : 'text-center text-muted';
        cell.textContent = message;
        row.appendChild(cell);
        tableBody.appendChild(row);
    }

    function getBadgeClass(status) {
        const normalized = (status || '').toUpperCase();
        if (normalized === 'ACTIVE' || normalized === 'ACTIVA') {
            return 'badge bg-success';
        }
        if (normalized === 'EXPIRED' || normalized === 'VENCIDA') {
            return 'badge bg-warning text-dark';
        }
        if (normalized === 'CANCELLED' || normalized === 'CANCELADA') {
            return 'badge bg-danger';
        }
        return 'badge bg-secondary';
    }

    function renderActionsCell(sub) {
        const tdActions = document.createElement('td');
        tdActions.className = 'text-center';

        const isCancelled = (sub.status || '').toUpperCase() === 'CANCELLED' || (sub.status || '').toUpperCase() === 'CANCELADA';

        // Botón Renovar
        const btnRenew = document.createElement('button');
        btnRenew.type = 'button';
        btnRenew.className = 'btn btn-outline-primary btn-sm me-1';
        btnRenew.textContent = 'Renovar';
        if (isCancelled) {
            btnRenew.disabled = true;
            btnRenew.title = 'No se puede renovar una suscripción cancelada';
        } else {
            btnRenew.addEventListener('click', async () => {
                const confirmed = await SwalMessages.confirm(
                    '¿Deseas renovar esta suscripción?',
                    'Confirmar Renovación'
                );
                if (!confirmed) return;

                try {
                    const response = await ApiClient.patch(`/subscriptions/${sub.id}/renew`);
                    await SwalMessages.success(response.message || 'Suscripción renovada exitosamente.');
                    loadSubscriptions();
                } catch (error) {
                    SwalMessages.error(error.message || 'No se pudo renovar la suscripción.');
                }
            });
        }
        tdActions.appendChild(btnRenew);

        // Botón Cancelar
        const btnCancel = document.createElement('button');
        btnCancel.type = 'button';
        btnCancel.className = 'btn btn-outline-danger btn-sm';
        btnCancel.textContent = 'Cancelar';
        if (isCancelled) {
            btnCancel.disabled = true;
            btnCancel.title = 'La suscripción ya se encuentra cancelada';
        } else {
            btnCancel.addEventListener('click', async () => {
                const confirmed = await SwalMessages.confirm(
                    '¿Estás seguro de cancelar esta suscripción?',
                    'Confirmar Cancelación'
                );
                if (!confirmed) return;

                try {
                    const response = await ApiClient.patch(`/subscriptions/${sub.id}/cancel`);
                    await SwalMessages.success(response.message || 'Suscripción cancelada exitosamente.');
                    loadSubscriptions();
                } catch (error) {
                    SwalMessages.error(error.message || 'No se pudo cancelar la suscripción.');
                }
            });
        }
        tdActions.appendChild(btnCancel);

        return tdActions;
    }

    function loadSubscriptions() {
        createEmptyRow('Cargando suscripciones...');

        ApiClient.get('/subscriptions/data')
            .then((subscriptions) => {
                tableBody.innerHTML = '';

                if (!Array.isArray(subscriptions) || subscriptions.length === 0) {
                    createEmptyRow('No hay suscripciones registradas.');
                    return;
                }

                subscriptions.forEach((sub) => {
                    const row = document.createElement('tr');

                    // 1. Cliente
                    const tdClient = document.createElement('td');
                    tdClient.textContent = sub.client?.name ?? 'Desconocido';
                    row.appendChild(tdClient);

                    // 2. Plan
                    const tdPlan = document.createElement('td');
                    tdPlan.textContent = sub.plan?.nombre ?? 'Desconocido';
                    row.appendChild(tdPlan);

                    // 3. Inicio
                    const tdStart = document.createElement('td');
                    tdStart.textContent = sub.start_date || '-';
                    row.appendChild(tdStart);

                    // 4. Fin (Vence) usando expiration_date
                    const tdExpiration = document.createElement('td');
                    tdExpiration.textContent = sub.expiration_date || '-';
                    row.appendChild(tdExpiration);

                    // 5. Estado con badge Bootstrap 5
                    const tdStatus = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.className = getBadgeClass(sub.status);
                    badge.textContent = sub.status || '-';
                    tdStatus.appendChild(badge);
                    row.appendChild(tdStatus);

                    // 6. Acciones
                    row.appendChild(renderActionsCell(sub));

                    tableBody.appendChild(row);
                });
            })
            .catch((error) => {
                console.error('Error fetching subscriptions:', error);
                createEmptyRow('Ocurrió un error al cargar las suscripciones.', true);
            });
    }

    loadSubscriptions();
});
