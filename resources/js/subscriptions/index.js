import ApiClient from '../commons/api-client';

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('subscriptions-table-body');

    function loadSubscriptions() {
        // Obtenemos los datos desde el servidor en formato JSON
        ApiClient.get('/subscriptions')
            .then((subscriptions) => {
                tableBody.innerHTML = ''; // Limpiamos el mensaje de "Cargando..."

                if (subscriptions.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay suscripciones registradas.</td></tr>';
                    return;
                }

                subscriptions.forEach((sub) => {
                    const row = document.createElement('tr');
                    
                    // Manejo seguro por si el cliente o el plan fueron eliminados de la BBDD
                    const clientName = sub.client ? sub.client.name : 'Desconocido';
                    const planName = sub.plan ? sub.plan.nombre : 'Desconocido';
                    
                    // Clases para darle estilo al badge según el estado
                    let statusBadgeClass = 'badge-secondary';
                    const statusText = sub.status.toUpperCase();
                    if (statusText === 'ACTIVE' || statusText === 'ACTIVA') statusBadgeClass = 'badge-success';
                    else if (statusText === 'EXPIRED' || statusText === 'VENCIDA') statusBadgeClass = 'badge-warning';
                    else if (statusText === 'CANCELLED' || statusText === 'CANCELADA') statusBadgeClass = 'badge-danger';
                    
                    const statusHtml = `<span class="badge ${statusBadgeClass}">${sub.status}</span>`;

                    row.innerHTML = `
                        <td>${clientName}</td>
                        <td>${planName}</td>
                        <td>${sub.start_date || '-'}</td>
                        <td>${sub.end_date || '-'}</td>
                        <td>${statusHtml}</td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch((error) => {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Ocurrió un error al cargar las suscripciones.</td></tr>';
                console.error('Error fetching subscriptions:', error);
            });
    }

    // Ejecutamos la carga inicial
    loadSubscriptions();
});
