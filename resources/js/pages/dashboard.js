import api, { ApiError } from '../commons/api-client.js';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const data = await api.get('/api/chart-data');

        const canvas = document.getElementById('mainChart');

        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: data.data,
                        backgroundColor: 'rgba(60,141,188,0.2)',
                        borderColor: 'rgba(60,141,188,1)',
                        borderWidth: 2,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    } catch (error) {
        if (error instanceof ApiError) {
            console.error(
                'Error al cargar los datos del dashboard:',
                error.message
            );

            return;
        }

        throw error;
    }
});