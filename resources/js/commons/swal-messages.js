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

    static async confirm(text = '¿Estás seguro?', title = 'Confirmar') {
        const result = await Swal.fire({
            title,
            text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
        });
        return result.isConfirmed;
    }

    static async confirmDelete(entityName = 'este registro') {
        return this.confirm(
            `${entityName} será eliminado permanentemente.`,
            'Estas seguro'
        );
    }
}