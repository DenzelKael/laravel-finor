import Chart from 'chart.js/auto';
import axios from 'axios'; 

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await axios.get('/api/chart-data');
        const data = response.data;

        const ctx = document.getElementById('mainChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line', 
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue ($)',
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
        console.error('Error fetching chart data:', error);
    }
});