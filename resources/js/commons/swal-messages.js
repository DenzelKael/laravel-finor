// resources/js/commons/swal-messages.js
import Swal from 'sweetalert2';

export default class SwalMessages {
    static success(message) {
        return Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 2500,
            showConfirmButton: false,
        });
    }

    static error(message) {
        return Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
        });
    }

    static warning(message) {
        return Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: message,
        });
    }

    static async confirm(text = '¿Estás seguro?', title = 'Confirmar', confirmText = 'Sí, continuar') {
        const result = await Swal.fire({
            title,
            text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancelar',
        });
        return result.isConfirmed;
    }
    static async confirmWithReason(
    text,
    title = 'Confirmar',
    confirmText = 'Sí, continuar'
) {
    const result = await Swal.fire({
        title,
        text,
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Motivo de anulación',
        inputPlaceholder: 'Ingrese el motivo de anulación...',
        inputAttributes: {
            maxlength: 255,
        },
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        preConfirm: (value) => {
            const reason = value?.trim();

            if (!reason) {
                Swal.showValidationMessage(
                    'Debe ingresar el motivo de anulación.'
                );

                return false;
            }

            if (reason.length > 255) {
                Swal.showValidationMessage(
                    'El motivo no puede superar los 255 caracteres.'
                );

                return false;
            }

            return reason;
        },
    });

    return result.isConfirmed
        ? result.value
        : null;
}

    static async confirmDelete(entityName = 'este registro') {
        return this.confirm(
            `${entityName} será eliminado permanentemente.`,
            'Estas seguro'
        );
    }
}