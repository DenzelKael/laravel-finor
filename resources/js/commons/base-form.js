// resources/js/commons/base-form.js
import api, { ApiError } from './api-client.js';

export default class BaseForm {
    constructor(form) {
        this.form = form;
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    getFormData() {
        return new FormData(this.form);
    }

    showData(data) {
        //
    }

    clearErrors() {
        this.form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    showValidationErrors(errors) {
        for (const field in errors) {
            const input = this.form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                input.nextElementSibling.textContent = errors[field][0];
            }
        }
    }

    async createResource(data) {
        return api.post(this.form.dataset.storeUrl, data);
    }

    async updateResource(data) {
        return api.put(this.form.dataset.updateUrl, data);
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

            sessionStorage.setItem('flash_message', response.message);
            window.location.href = this.form.dataset.indexUrl;
        } catch (error) {
            if (!(error instanceof ApiError)) throw error;

            switch (error.type) {
                case 'validation':
                    this.showValidationErrors(error.data.errors);
                    break;
                case 'auth':
                    window.location.href = '/login';
                    break;
                case 'not_found':
                    alert('Este registro ya no existe.');
                    window.location.href = this.form.dataset.indexUrl;
                    break;
                case 'server':
                case 'network':
                    alert('No se pudo guardar el registro. Intenta de nuevo.');
                    break;
                default:
                    alert(error.message);
            }
        }
    }
}
