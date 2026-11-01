import ApexCharts from 'apexcharts';
import api from '../commons/api-client.js';

document.addEventListener('DOMContentLoaded', async () => {
    const el = document.getElementById('mainChart');
    if (!el) return;

    const url = el.dataset.url;
    if (!url) return;

    try {
        const data = await api.get(url);

        new ApexCharts(el, {
            chart: { type: 'area', height: 250, toolbar: { show: false } },
            series: [{ name: 'Ingresos (Bs)', data: data.data ?? [] }],
            xaxis: { categories: data.labels ?? [] },
            colors: ['#3c8dbc'],
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
        }).render();
    } catch (error) {
        console.error('Error al cargar la gráfica:', error);
    }
});