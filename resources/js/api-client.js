/**
 * ApiClient — Cliente centralizado para peticiones AJAX del proyecto.
 * Usado por todos los módulos (Clients, Plans, etc.) para evitar
 * duplicar lógica de fetch, headers y token CSRF.
 */
class ApiClient {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    async request(url, method = 'GET', body = null) {
        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json',
            },
        };

        if (body) {
            options.body = body;
        }

        try {
            const response = await fetch(url, options);
            const data = await response.json();

            return {
                ok: response.ok,
                status: response.status,
                data,
            };
        } catch (error) {
            return {
                ok: false,
                status: 0,
                data: { message: 'Network error or invalid server response.' },
            };
        }
    }

    get(url) {
        return this.request(url, 'GET');
    }

    post(url, formData) {
        return this.request(url, 'POST', formData);
    }

    put(url, formData) {
        formData.append('_method', 'PUT');
        return this.request(url, 'POST', formData);
    }

    delete(url) {
        return this.request(url, 'DELETE');
    }
}

export default ApiClient;
