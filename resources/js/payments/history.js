import api, { ApiError } from '../commons/api-client.js';

const historyBody = document.getElementById('payments-history-body');
const previousButton = document.getElementById('history-prev');
const nextButton = document.getElementById('history-next');
const pageInfo = document.getElementById('history-page-info');
const alertContainer = document.getElementById('history-alert-container');

let currentPage = 1;
let lastPage = 1;

function createCell(text, className = '') {
    const cell = document.createElement('td');

    if (className) {
        cell.className = className;
    }

    cell.textContent = text;

    return cell;
}

function formatDate(date) {
    if (!date) {
        return '—';
    }

    const [year, month, day] = date.split('-');

    return `${day}/${month}/${year}`;
}

function showError(message) {
    if (!alertContainer) {
        return;
    }

    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.setAttribute('role', 'alert');
    alert.textContent = message;

    alertContainer.replaceChildren(alert);
}

function clearError() {
    if (alertContainer) {
        alertContainer.replaceChildren();
    }
}

function renderEmptyState() {
    if (!historyBody) {
        return;
    }

    const row = document.createElement('tr');
    const cell = document.createElement('td');

    cell.colSpan = 6;
    cell.className = 'text-center text-muted py-4';
    cell.textContent = 'No existen pagos registrados.';

    row.appendChild(cell);
    historyBody.replaceChildren(row);
}

function renderPayments(payments) {
    if (!historyBody) {
        return;
    }

    if (!payments.length) {
        renderEmptyState();
        return;
    }

    const rows = payments.map((payment) => {
        const row = document.createElement('tr');

        row.appendChild(
            createCell(`#${payment.id}`)
        );

        row.appendChild(
            createCell(formatDate(payment.payment_date))
        );

        row.appendChild(
            createCell(payment.payment_method_label)
        );

        row.appendChild(
            createCell(
                payment.formatted_amount,
                'text-right'
            )
        );

        const statusCell = document.createElement('td');
        const badge = document.createElement('span');

        badge.className = payment.status === 'VOIDED'
            ? 'badge badge-danger'
            : 'badge badge-success';

        badge.textContent = payment.status_label;

        statusCell.appendChild(badge);
        row.appendChild(statusCell);

        row.appendChild(
            createCell(payment.void_reason || '—')
        );

        return row;
    });

    historyBody.replaceChildren(...rows);
}

function updatePagination(meta) {
    currentPage = meta.current_page;
    lastPage = meta.last_page;

    if (pageInfo) {
        pageInfo.textContent =
            `Página ${currentPage} de ${lastPage}`;
    }

    if (previousButton) {
        previousButton.disabled = currentPage <= 1;
    }

    if (nextButton) {
        nextButton.disabled = currentPage >= lastPage;
    }
}

async function loadHistory(page = 1) {
    if (!historyBody?.dataset.url) {
        return;
    }

    clearError();

    try {
        const response = await api.get(
            historyBody.dataset.url,
            {
                params: {
                    page,
                },
            }
        );

        renderPayments(response.data ?? []);
        updatePagination(response.meta);
    } catch (error) {
        if (error instanceof ApiError) {
            showError(error.message);
        } else {
            showError(
                'Ocurrió un error inesperado al consultar los pagos.'
            );
        }
    }
}

previousButton?.addEventListener('click', function () {
    if (currentPage > 1) {
        loadHistory(currentPage - 1);
    }
});

nextButton?.addEventListener('click', function () {
    if (currentPage < lastPage) {
        loadHistory(currentPage + 1);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    loadHistory();
});