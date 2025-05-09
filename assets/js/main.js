document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de ventas por tipo de material
    const ventasCtx = document.getElementById('ventasChart');
    if (ventasCtx) {
        fetch('api/ventas_por_material.php')
            .then(response => response.json())
            .then(data => {
                new Chart(ventasCtx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });
    }
}); 