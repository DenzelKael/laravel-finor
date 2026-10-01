// resources/js/commons/api-client.js

/**
 * ApiError — encapsula el error con su tipo y status, para que quien
 * consuma la API pueda reaccionar distinto según qué falló.
 */
export class ApiError extends Error {
    constructor(type, status, payload) {
        super(payload?.message ?? 'Ocurrió un error en la petición.');
        this.type = type;       // 'validation' | 'auth' | 'not_found' | 'server' | 'network' | 'unknown'
        this.status = status;
        this.data = payload;
    }
}

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
        if (body) options.body = body;

        let response;
        try {
            response = await fetch(url, options);
        } catch {
            throw new ApiError('network', 0, { message: 'No se pudo conectar con el servidor.' });
        }

        let payload = null;
        if (response.status !== 204) {
            try {
                payload = await response.json();
            } catch {
                throw new ApiError('invalid_response', response.status, { message: 'Respuesta inesperada del servidor.' });
            }
        }

        switch (response.status) {
            case 422:
                throw new ApiError('validation', 422, payload);
            case 401:
            case 419:
                throw new ApiError('auth', response.status, payload ?? { message: 'Tu sesión expiró.' });
            case 404:
                throw new ApiError('not_found', 404, payload ?? { message: 'El recurso no existe.' });
        }

        if (response.status >= 500) {
            throw new ApiError('server', response.status, payload ?? { message: 'Error interno del servidor.' });
        }

        if (!response.ok) {
            throw new ApiError('unknown', response.status, payload);
        }

        return payload; // la entidad ("data"), sin envoltorio success
    }

    get(url)             { return this.request(url, 'GET'); }
    post(url, formData)  { return this.request(url, 'POST', formData); }
    put(url, formData)   { formData.append('_method', 'PUT'); return this.request(url, 'POST', formData); }
    delete(url)          { return this.request(url, 'DELETE'); }
}

// Instancia única (singleton). ES Modules cachea por URL, así que
// cualquier archivo que importe este módulo recibe el MISMO objeto —
// ya no se necesita `window.api`.
export default new ApiClient();
