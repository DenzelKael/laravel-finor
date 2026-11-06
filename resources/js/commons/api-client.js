import axios from 'axios';

export class ApiError extends Error {
    constructor(type, status, payload) {
        super(payload?.message ?? 'Ocurrió un error en la petición.');
        this.type = type;
        this.status = status;
        this.data = payload;
    }
}

const STATUS_ERROR_MAP = {
    401: 'auth',
    403: 'forbidden',
    419: 'auth',
    404: 'not_found',
    422: 'validation',
};

function resolveErrorType(status) {
    if (STATUS_ERROR_MAP[status]) return STATUS_ERROR_MAP[status];
    if (status >= 500) return 'server';
    return 'unknown';
}

// Un error en una descarga (responseType 'blob') llega como Blob; hay que leerlo como JSON.
async function parseErrorBody(data) {
    if (data instanceof Blob) {
        try {
            return JSON.parse(await data.text());
        } catch {
            return {};
        }
    }
    return data;
}

const http = axios.create({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        'Accept': 'application/json',
    },
});

http.interceptors.response.use(
    (response) => response.data,
    async (error) => {
        if (!error.response) {
            throw new ApiError('network', 0, { message: 'No se pudo conectar con el servidor.' });
        }
        const { status } = error.response;
        const data = await parseErrorBody(error.response.data);
        throw new ApiError(resolveErrorType(status), status, data);
    }
);

class ApiClient {
    get(url, config = {}) {
        return http.get(url, config);
    }

    download(url, config = {}) {
        return http.get(url, { ...config, responseType: 'blob' });
    }

    post(url, body, config = {}) {
        return http.post(url, body, config);
    }

    put(url, body, config = {}) {
        if (body instanceof FormData) {
            body.append('_method', 'PUT');
            return http.post(url, body, config);
        }
        return http.put(url, body, config);
    }

    patch(url, body, config = {}) {
        if (body instanceof FormData) {
            body.append('_method', 'PATCH');
            return http.post(url, body, config);
        }
        return http.patch(url, body, config);
    }

    delete(url, config = {}) {
        return http.delete(url, config);
    }
}

export default new ApiClient();