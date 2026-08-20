/**
 * Dashboard - Estadísticas Reales del Sistema Electoral
 * Carga datos reales mediante AJAX y renderiza gráficos con ApexCharts
 */

const DASHBOARD = {

    // Grilla seleccionada (null = todas)
    grilla_id_seleccionada: null,

    /**
     * Inicializar dashboard con datos reales
     */
    init: function() {
        // Cargar grillas disponibles en el selector
        DASHBOARD.cargarGrillas();

        // Cargar todas las estadísticas
        DASHBOARD.recargarTodasLasGraficas();

        // Evento cuando cambia el filtro de grilla
        $('#filtro_grilla').on('change', function() {
            DASHBOARD.grilla_id_seleccionada = $(this).val() || null;
            DASHBOARD.recargarTodasLasGraficas();
            DASHBOARD.recargarEstadisticasPrincipales();
        });
    },

    /**
     * Cargar lista de grillas disponibles
     */
    cargarGrillas: function() {
        const q = { op: 'dashboardgrillas' };
        UTIL.callAjaxRqstPOST(q, function(response) {
            console.log('Respuesta getGrillas:', response);
            if (response.output.valid && response.output.response.length > 0) {
                const $select = $('#filtro_grilla');
                response.output.response.forEach(function(grilla) {
                    $select.append(
                        $('<option></option>')
                            .attr('value', grilla.id)
                            .text(grilla.nombre)
                    );
                });
                console.log('Grillas cargadas:', response.output.response.length);
            } else {
                console.error('No se encontraron grillas o respuesta inválida:', response);
            }
        });

        UTIL.cursorNormal();
    },

    /**
     * Recargar todas las gráficas con el filtro actual
     */
    recargarTodasLasGraficas: function() {
        // Limpiar contenedores de gráficas antes de recargar
        $('#chartDonutA').html('');
        $('#chartDonutB').html('');
        $('#chartDonutC').html('');
        $('#graficoSeguridadAntioquia').html('');
        $('#graficoFactoresAntioquia').html('');
        $('#radarInstitucional').html('');
        $('#containerSecretarias').html('');

        // Cargar nuevas gráficas
        DASHBOARD.loadVotantesPorIdeologia();
        DASHBOARD.loadVotantesPorGenero();
        DASHBOARD.loadVotantesPorEdad();
        DASHBOARD.loadAnalisisPorMes();
        DASHBOARD.loadTopCandidatos();
        DASHBOARD.loadVotantesPorIngresos();
        DASHBOARD.loadGrillasPorEstado();

        UTIL.cursorNormal();
    },

    /**
     * Recargar estadísticas principales (cards superiores)
     */
    recargarEstadisticasPrincipales: function() {
        const q = {
            op: 'dashboardestadisticas',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid) {
                const stats = response.output.response;
                // Actualizar los valores de las cards dinámicamente
                $('#stat_total_votantes').text(parseInt(stats.total_votantes || 0).toLocaleString('es-ES'));
                $('#stat_total_grillas').text(parseInt(stats.total_grillas || 0).toLocaleString('es-ES'));
                $('#stat_total_analisis').text(parseInt(stats.total_analisis || 0).toLocaleString('es-ES'));
                $('#stat_total_candidatos').text(parseInt(stats.total_candidatos || 0).toLocaleString('es-ES'));
            }
        });

        UTIL.cursorNormal();
    },

    /**
     * Formateador de etiquetas
     */
    formatLabel: function(label) {
        const translations = {
            'izquierda': 'Izquierda',
            'centro_izquierda': 'Centro Izquierda',
            'centro': 'Centro',
            'centro_derecha': 'Centro Derecha',
            'derecha': 'Derecha',
            'independiente': 'Independiente',
            'sin_definir': 'Sin Definir',
            'masculino': 'Masculino',
            'femenino': 'Femenino',
            'otro': 'Otro',
            'prefiero_no_decir': 'Prefiero No Decir',
            '18-25': '18-25 años',
            '26-35': '26-35 años',
            '36-45': '36-45 años',
            '46-55': '46-55 años',
            '56-65': '56-65 años',
            '66+': '66+ años',
            'menos_1_salario': 'Menos de 1 salario',
            '1-2_salarios': '1-2 salarios',
            '3-5_salarios': '3-5 salarios',
            '6-10_salarios': '6-10 salarios',
            'mas_10_salarios': 'Más de 10 salarios'
        };
        return translations[label] || label;
    },

    /**
     * Gráfico 1: Votantes por Ideología Política (Donut Chart A)
     */
    loadVotantesPorIdeologia: function() {
        const q = {
            op: 'dashboardideologia',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const labels = data.map(item => DASHBOARD.formatLabel(item.ideologia || 'sin_definir'));
                const series = data.map(item => parseInt(item.total));

                const options = {
                    chart: {
                        type: 'donut',
                        height: 240
                    },
                    series: series,
                    labels: labels,
                    colors: ['#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800', '#00BCD4'],
                    title: {
                        text: 'Votantes por Ideología Política',
                        align: 'center',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val.toFixed(1) + '%';
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Votantes',
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                const chart = new ApexCharts(document.querySelector("#chartDonutA"), options);
                chart.render();
            } else {
                document.querySelector("#chartDonutA").innerHTML = '<p class="text-center text-muted">No hay datos de ideología política</p>';
            }
        });
    },

    /**
     * Gráfico 2: Votantes por Género (Donut Chart B)
     */
    loadVotantesPorGenero: function() {
        const q = {
            op: 'dashboardgenero',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const labels = data.map(item => DASHBOARD.formatLabel(item.genero || 'otro'));
                const series = data.map(item => parseInt(item.total));

                const options = {
                    chart: {
                        type: 'donut',
                        height: 240
                    },
                    series: series,
                    labels: labels,
                    colors: ['#008FFB', '#FF4560', '#775DD0'],
                    title: {
                        text: 'Distribución por Género',
                        align: 'center',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val.toFixed(1) + '%';
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#chartDonutB"), options);
                chart.render();
            } else {
                document.querySelector("#chartDonutB").innerHTML = '<p class="text-center text-muted">No hay datos de género</p>';
            }
        });
    },

    /**
     * Gráfico 3: Votantes por Rango de Edad (Donut Chart C)
     */
    loadVotantesPorEdad: function() {
        const q = {
            op: 'dashboardedad',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const labels = data.map(item => DASHBOARD.formatLabel(item.rango_edad));
                const series = data.map(item => parseInt(item.total));

                const options = {
                    chart: {
                        type: 'donut',
                        height: 240
                    },
                    series: series,
                    labels: labels,
                    colors: ['#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A'],
                    title: {
                        text: 'Votantes por Rango de Edad',
                        align: 'center',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val.toFixed(1) + '%';
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Votantes',
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#chartDonutC"), options);
                chart.render();
            } else {
                document.querySelector("#chartDonutC").innerHTML = '<p class="text-center text-muted">No hay datos de edad</p>';
            }
        });
    },

    /**
     * Gráfico 4: Análisis de Estudio por Mes (Últimos 6 meses) - Line Chart
     */
    loadAnalisisPorMes: function() {
        const q = {
            op: 'dashboardanalisismes',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const categories = data.map(item => item.mes_nombre);
                const series = data.map(item => parseInt(item.total));

                const options = {
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Análisis Realizados',
                        data: series
                    }],
                    xaxis: {
                        categories: categories,
                        title: {
                            text: 'Meses'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Cantidad de Análisis'
                        }
                    },
                    colors: ['#008FFB'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 5
                    },
                    title: {
                        text: 'Análisis de Estudio - Últimos 6 Meses',
                        align: 'center',
                        style: {
                            fontSize: '15px',
                            fontWeight: 900
                        }
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#graficoSeguridadAntioquia"), options);
                chart.render();
            } else {
                document.querySelector("#graficoSeguridadAntioquia").innerHTML = '<p class="text-center text-muted">No hay datos de análisis</p>';
            }
        });
    },

    /**
     * Gráfico 5: Top 5 Candidatos con más Análisis - Horizontal Bar Chart
     */
    loadTopCandidatos: function() {
        const q = {
            op: 'dashboardtopcandidatos',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const categories = data.map(item => item.nombre_completo);
                const series = data.map(item => parseInt(item.total_analisis));

                const options = {
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '70%',
                            distributed: true
                        }
                    },
                    series: [{
                        name: 'Análisis',
                        data: series
                    }],
                    xaxis: {
                        categories: categories,
                        title: {
                            text: 'Número de Análisis'
                        }
                    },
                    colors: ['#00E396', '#008FFB', '#FEB019', '#FF4560', '#775DD0'],
                    title: {
                        text: 'Top 5 Candidatos con Más Análisis',
                        align: 'center',
                        style: {
                            fontSize: '15px',
                            fontWeight: 900
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            colors: ['#333']
                        },
                        formatter: function (val) {
                            return val + ' análisis';
                        }
                    },
                    legend: {
                        show: false
                    }
                };

                const chart = new ApexCharts(document.querySelector("#graficoFactoresAntioquia"), options);
                chart.render();
            } else {
                document.querySelector("#graficoFactoresAntioquia").innerHTML = '<p class="text-center text-muted">No hay datos de candidatos</p>';
            }
        });
    },

    /**
     * Gráfico 6: Votantes por Nivel de Ingresos - Pie Chart
     */
    loadVotantesPorIngresos: function() {
        const q = {
            op: 'dashboardingresos',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const labels = data.map(item => DASHBOARD.formatLabel(item.nivel_ingresos || 'menos_1_salario'));
                const series = data.map(item => parseInt(item.total));

                const options = {
                    chart: {
                        type: 'pie',
                        height: 400
                    },
                    series: series,
                    labels: labels,
                    colors: ['#00E396', '#008FFB', '#FEB019', '#FF4560', '#775DD0'],
                    title: {
                        text: 'Votantes por Nivel de Ingresos',
                        align: 'center',
                        style: {
                            fontSize: '15px',
                            fontWeight: 900
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val.toFixed(1) + '%';
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 300
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                const chart = new ApexCharts(document.querySelector("#radarInstitucional"), options);
                chart.render();
            } else {
                document.querySelector("#radarInstitucional").innerHTML = '<p class="text-center text-muted">No hay datos de ingresos</p>';
            }
        });
    },

    /**
     * Gráfico 7: Grillas por Estado (Activas/Inactivas) - Donut Chart
     */
    loadGrillasPorEstado: function() {
        const q = {
            op: 'dashboardgrillasestado',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };
        UTIL.callAjaxRqstPOST(q, function(response) {
            if (response.output.valid && response.output.response.length > 0) {
                const data = response.output.response;
                const labels = data.map(item => item.estado);
                const series = data.map(item => parseInt(item.total));

                const options = {
                    chart: {
                        type: 'donut',
                        height: 400
                    },
                    series: series,
                    labels: labels,
                    colors: ['#00E396', '#FF4560'],
                    title: {
                        text: 'Estado de Grillas de Evaluación',
                        align: 'center',
                        style: {
                            fontSize: '15px',
                            fontWeight: 900
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val.toFixed(1) + '%';
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Grillas',
                                        fontSize: '16px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#containerSecretarias"), options);
                chart.render();
            } else {
                document.querySelector("#containerSecretarias").innerHTML = '<p class="text-center text-muted">No hay datos de grillas</p>';
            }
        });
    }
};

// DASHBOARD.init() se llama desde el ready del dashboard.php
