import Chart from 'chart.js/auto';
import api from '../commons/api-client.js'; // 🟡 Regla académica: usar ApiClient

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const canvas = document.getElementById('mainChart');
        if (!canvas) return;

        const url = canvas.dataset.url; 

        const response = await api.get(url);
        const data = response.data;

        const ctx = canvas.getContext('2d');
        
        new Chart(ctx, {
            type: 'line', 
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Ingresos (Bs)', 
                    data: data.data,
                    backgroundColor: 'rgba(60,141,188,0.2)',
                    borderColor: 'rgba(60,141,188,1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } catch (error) {
        console.error('Error al cargar la gráfica:', error);
    }
});