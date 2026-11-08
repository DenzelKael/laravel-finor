import Chart from 'chart.js/auto';
import api from '../commons/api-client.js';

document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('mainChart');

    if (!canvas) {
        return;
    }

    const url = canvas.dataset.url;

    if (!url) {
        return;
    }

    try {
        const data = await api.get(url);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels ?? [],
                datasets: [
                    {
                        label: 'Ingresos (Bs)',
                        data: data.data ?? [],
                        tension: 0.3,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    } catch (error) {
        console.error('Error al cargar la gráfica:', error);
    }
});