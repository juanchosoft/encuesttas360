$(function () {
    secretariasData.forEach(item => {
        const labels = [
            'Suspendido',
            'Terminado',
            'Ejecutado',
            'En Contratación',
            'En Formulación',
            'Entregado',
            'En Ejecución'
        ];

        const series = [
            item.suspendido,
            item.terminado,
            item.ejecutado,
            item.en_contratacion,
            item.en_formulacion,
            item.entregado,
            item.en_ejecucion
        ];

        const options = {
            chart: {
                height: 320,
                type: 'pie'
            },
            labels: labels,
            series: series,
            colors: ["#1abc9c", "#0e9e4a", "#00acc1", "#f1c40f", "#e74c3c", "#9b59b6", "#3498db"],
            legend: {
                show: true,
                position: 'bottom'
            },
            dataLabels: {
                enabled: true,
                dropShadow: {
                    enabled: false
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        const chart = new ApexCharts(document.querySelector(`#chart_${item.secretaria_id}`), options);
        chart.render();
    });

   const acciones = secretariasDataHacienda.map(item => item.accion);

    // Gráfica 1 - Cantidades por acción
    const seriesCantidad = [{
        name: 'Cantidad',
        data: secretariasDataHacienda.map(item => item.cantidad || 0)
    }];

    const chartCantidad = new ApexCharts(document.querySelector("#grafica_cantidad"), {
    chart: {
        type: 'bar',
        height: 350
    },
    title: {
    text: 'Cantidad de acciones por tipo',
        align: 'left',
        style: {
            fontSize: '18px',
            fontWeight: 'bold'
        }
    },
        xaxis: {
            categories: acciones,
            labels: {
                rotate: 0,
                style: {
                    fontSize: '12px'
                }
            }
        },
        series: seriesCantidad,
        dataLabels: {
            enabled: true
        },
        legend: {
            position: 'bottom'
        }
    });


    chartCantidad.render();

    // Gráfica 2 - Detalles por acción
    const seriesValores = [
        {
            name: 'Incautación Licores',
            data: secretariasDataHacienda.map(item => item.incautacion_licores || 0)
        },
        {
            name: 'Valor Licores',
            data: secretariasDataHacienda.map(item => parseFloat(item.valor_licores) || 0)
        },
        {
            name: 'Incautación Cigarrillos',
            data: secretariasDataHacienda.map(item => item.incautacion_cigarrillos || 0)
        },
        {
            name: 'Valor Cigarrillos',
            data: secretariasDataHacienda.map(item => parseFloat(item.valor_cigarrillos) || 0)
        },
        {
            name: 'Capacitación Programada',
            data: secretariasDataHacienda.map(item => item.capacitacion_programada || 0)
        },
        {
            name: 'Cantidad Personas',
            data: secretariasDataHacienda.map(item => item.cantidad_personas || 0)
        }
    ];

    const chartValores = new ApexCharts(document.querySelector("#grafica_valores"), {
    chart: {
        type: 'bar',
        height: 400
    },
    title: {
        text: 'Detalles por acción',
            align: 'left',
            style: {
                fontSize: '18px',
                fontWeight: 'bold'
            }
        },
        xaxis: {
            categories: acciones,
            labels: {
                rotate: 0,
                style: {
                    fontSize: '12px'
                }
            }
        },
        series: seriesValores,
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'bottom'
        }
    });


    chartValores.render();
});
