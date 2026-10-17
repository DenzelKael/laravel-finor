import api, { ApiError } from '../commons/api-client.js';

function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
        });

        return;
    }

    console.error(message);
}

function downloadBlob(blob, filename) {
    const objectUrl = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = objectUrl;
    link.download = filename;

    document.body.appendChild(link);
    link.click();
    link.remove();

    URL.revokeObjectURL(objectUrl);
}

async function downloadReceipt(button) {
    const url = button.dataset.url;
    const filename =
        button.dataset.filename ?? 'recibo-pago.pdf';

    if (!url) {
        return;
    }

    button.disabled = true;

    try {
        const blob = await api.get(url, {
            responseType: 'blob',
        });

        downloadBlob(blob, filename);
    } catch (error) {
        if (error instanceof ApiError) {
            showError(error.message);
        } else {
            showError(
                'Ocurrió un error inesperado al descargar el recibo.'
            );
        }
    } finally {
        button.disabled = false;
    }
}

document.addEventListener('click', function (event) {
    const printButton = event.target.closest(
        '[data-action="print-receipt"]'
    );

    if (printButton) {
        window.print();
        return;
    }

    const downloadButton = event.target.closest(
        '[data-action="download-receipt"]'
    );

    if (downloadButton) {
        downloadReceipt(downloadButton);
    }
});