import api, { ApiError } from '../commons/api-client.js';
import SwalMessages from '../commons/swal-messages.js';

const clientsTable = document.getElementById('clients-table');
const tableBody = document.getElementById('clients-table-body');
const baseUrl = clientsTable?.dataset.baseUrl;
const searchInput = document.getElementById('client-search');
const statusSelect = document.getElementById('client-status');
const exportButton = document.getElementById('client-export');
const pagination = document.getElementById('clients-pagination');

const COLUMNS = 7; // debe coincidir con el <thead> de la tabla
const state = { page: 1 };
let requestId = 0;
let debounceTimer;

function filterParams() {
    const params = {};
    if (searchInput?.value.trim()) params.q = searchInput.value.trim();
    if (statusSelect?.value) params.status = statusSelect.value;
    return params;
}

function textCell(value) {
    const td = document.createElement('td');
    td.textContent = value ?? '-';
    return td;
}

function withIcon(element, iconClass, label) {
    const icon = document.createElement('i');
    icon.className = iconClass;
    element.append(icon, ` ${label}`);
    return element;
}

function buildRow(client) {
    const tr = document.createElement('tr');
    tr.id = `client-row-${client.id}`;

    const statusTd = document.createElement('td');
    const badge = document.createElement('span');
    badge.className = `badge text-white ${client.status === 'active' ? 'badge-success bg-success' : 'badge-secondary bg-secondary'}`;
    badge.textContent = client.status_label;
    statusTd.append(badge);

    const view = document.createElement('a');
    view.href = `${baseUrl}/${client.id}`;
    view.className = 'btn btn-info btn-sm';
    withIcon(view, 'fas fa-eye', 'Ver');

    const edit = document.createElement('a');
    edit.href = `${baseUrl}/${client.id}/edit`;
    edit.className = 'btn btn-warning btn-sm';
    withIcon(edit, 'fas fa-edit', 'Editar');

    const del = document.createElement('button');
    del.type = 'button';
    del.className = 'btn btn-danger btn-sm';
    del.dataset.action = 'delete-client';
    del.dataset.id = client.id;
    del.dataset.url = `${baseUrl}/${client.id}`;
    withIcon(del, 'fas fa-trash', 'Eliminar');

    const actionsTd = document.createElement('td');
    actionsTd.append(view, ' ', edit, ' ', del);

    tr.append(
        textCell(client.id),
        textCell(client.name),
        textCell(client.email),
        textCell(client.phone),
        textCell(client.address),
        statusTd,
        actionsTd
    );
    return tr;
}

function buildEmptyRow() {
    const tr = document.createElement('tr');
    const td = document.createElement('td');
    td.colSpan = COLUMNS;
    td.className = 'text-center';
    td.textContent = 'No se encontraron clientes.';
    tr.append(td);
    return tr;
}

function pageButton(label, page, disabled) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn btn-sm btn-outline-secondary';
    button.textContent = label;
    button.disabled = disabled;
    button.dataset.page = page;
    return button;
}

function renderPagination(meta) {
    if (!pagination) return;

    const info = document.createElement('span');
    info.className = 'mx-2';
    info.textContent = `Página ${meta.current_page} de ${meta.last_page} (${meta.total} clientes)`;

    pagination.replaceChildren(
        pageButton('Anterior', meta.current_page - 1, meta.current_page <= 1),
        info,
        pageButton('Siguiente', meta.current_page + 1, meta.current_page >= meta.last_page)
    );
}

async function loadClients() {
    const current = ++requestId; // descarta respuestas viejas si el usuario sigue escribiendo
    try {
        const { data, meta } = await api.get(baseUrl, {
            params: { ...filterParams(), page: state.page },
        });
        if (current !== requestId) return;

        // Si la página quedó vacía (por ejemplo al borrar el último), vuelve a la última válida
        if (data.length === 0 && meta.current_page > 1) {
            state.page = meta.last_page;
            return loadClients();
        }

        tableBody.replaceChildren(...(data.length ? data.map(buildRow) : [buildEmptyRow()]));
        renderPagination(meta);
    } catch (error) {
        if (!(error instanceof ApiError)) throw error;
        SwalMessages.error(error.message);
    }
}

async function deleteClient(id, url) {
    const confirmed = await SwalMessages.confirmDelete('Este cliente');
    if (!confirmed) return;

    try {
        const data = await api.delete(url);
        SwalMessages.success(data.message);
        await loadClients();
    } catch (error) {
        if (!(error instanceof ApiError)) throw error;

        if (error.type === 'not_found') {
            SwalMessages.warning('El cliente ya no existía.');
            await loadClients();
        } else {
            SwalMessages.error(error.message);
        }
    }
}

async function exportClients() {
    try {
        const blob = await api.download(`${baseUrl}/export`, { params: filterParams() });
        const objectUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = objectUrl;
        link.download = `clientes-${new Date().toISOString().slice(0, 10)}.csv`;
        link.click();
        URL.revokeObjectURL(objectUrl);
    } catch (error) {
        if (!(error instanceof ApiError)) throw error;
        SwalMessages.error(error.message);
    }
}

tableBody?.addEventListener('click', (e) => {
    const button = e.target.closest('[data-action="delete-client"]');
    if (!button) return;

    deleteClient(button.dataset.id, button.dataset.url);
});

searchInput?.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        state.page = 1;
        loadClients();
    }, 300);
});

statusSelect?.addEventListener('change', () => {
    state.page = 1;
    loadClients();
});

pagination?.addEventListener('click', (e) => {
    const button = e.target.closest('button[data-page]');
    if (!button || button.disabled) return;

    state.page = Number(button.dataset.page);
    loadClients();
});

exportButton?.addEventListener('click', exportClients);

document.addEventListener('DOMContentLoaded', () => {
    if (clientsTable) loadClients();

    const flashMessage = sessionStorage.getItem('flash_message');
    if (flashMessage) {
        SwalMessages.success(flashMessage);
        sessionStorage.removeItem('flash_message');
    }
});