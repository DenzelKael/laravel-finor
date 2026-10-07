import api, { ApiError } from './api-client.js';
import SwalMessages from './swal-messages.js';

export default class BaseForm {
    constructor(form) {
        this.form = form;
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    getFormData() {
        return new FormData(this.form);
    }

    clearErrors() {
        this.form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        this.form.querySelectorAll('.invalid-feedback').forEach((el) => (el.textContent = ''));
    }

    showValidationErrors(errors) {
        for (const field in errors) {
            const input = this.form.querySelector(`[name="${field}"]`);
            if (!input) continue;

            input.classList.add('is-invalid');

            const group = input.closest('.form-group');
            let feedback = group?.querySelector('.invalid-feedback');

            if (!feedback) {
                feedback = document.createElement('span');
                feedback.className = 'invalid-feedback';
                (group ?? input.parentElement).appendChild(feedback);
            }

            feedback.textContent = errors[field][0];
        }
    }

    createResource(data) {
        return api.post(this.form.dataset.storeUrl, data);
    }

    updateResource(data) {
        return api.put(this.form.dataset.updateUrl, data);
    }

    onSuccess(response) {
        sessionStorage.setItem('flash_message', response.message);
        window.location.href = this.form.dataset.indexUrl;
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.clearErrors();

        const data = this.getFormData();
        const isEdit = !!this.form.dataset.updateUrl;

        try {
            const response = isEdit
                ? await this.updateResource(data)
                : await this.createResource(data);

            this.onSuccess(response);
        } catch (error) {
            if (!(error instanceof ApiError)) throw error;

            switch (error.type) {
                case 'validation':
                    this.showValidationErrors(error.data.errors);
                    break;
               case 'auth':
                    SwalMessages.error('Tu sesión expiró.');
                    window.location.href = '/login';
                    break;
                case 'forbidden':
                    SwalMessages.error('No tienes permiso para realizar esta acción.');
                    break;
                case 'not_found':
                    SwalMessages.warning('Este registro ya no existe.');
                    window.location.href = this.form.dataset.indexUrl;
                    break;
                default:
                    SwalMessages.error(error.message);
            }
        }
    }
}
