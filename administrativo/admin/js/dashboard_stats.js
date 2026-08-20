/**
 * Estadística360 - Dashboard de estadísticas
 * -------------------------------------------
 * Versión visual optimizada con ApexCharts.
 *
 * IMPORTANTE:
 * - Conserva las mismas operaciones AJAX.
 * - Conserva los mismos IDs de contenedores.
 * - Conserva los mismos nombres de métodos públicos.
 * - No cambia la estructura esperada de las respuestas del backend.
 */

const DASHBOARD = {

    /* =========================================================
       ESTADO
    ========================================================= */

    // Grilla seleccionada (null = todas)
    grilla_id_seleccionada: null,

    // Instancias de ApexCharts creadas por este módulo.
    _charts: {},

    // Evita que respuestas AJAX antiguas pisen una recarga más reciente.
    _generation: 0,

    /* =========================================================
       SISTEMA VISUAL
    ========================================================= */

    _theme: {
        primary: '#20427F',
        primary2: '#2E58A8',
        blue: '#2563EB',
        cyan: '#06B6D4',
        green: '#10B981',
        yellow: '#F59E0B',
        orange: '#F97316',
        red: '#EF4444',
        pink: '#EC4899',
        purple: '#7C3AED',
        indigo: '#6366F1',
        slate: '#64748B',
        dark: '#0F172A',
        softText: '#667085',
        grid: '#E8EDF5',
        surface: '#FFFFFF',
        light: '#F4F7FC',

        ideology: [
            '#2563EB',
            '#06B6D4',
            '#10B981',
            '#F59E0B',
            '#F97316',
            '#7C3AED'
        ],

        gender: [
            '#2563EB',
            '#EC4899',
            '#7C3AED',
            '#94A3B8'
        ],

        age: [
            '#38BDF8',
            '#2563EB',
            '#6366F1',
            '#7C3AED',
            '#EC4899',
            '#F59E0B'
        ],

        income: [
            '#06B6D4',
            '#2563EB',
            '#6366F1',
            '#7C3AED',
            '#F59E0B'
        ]
    },


    /* =========================================================
       INICIALIZACIÓN
    ========================================================= */

    /**
     * Inicializar dashboard con datos reales.
     */
    init: function() {

        DASHBOARD.cargarGrillas();

        DASHBOARD.recargarTodasLasGraficas();

        DASHBOARD.recargarEstadisticasPrincipales();

        // Mantiene exactamente el mismo filtro original.
        $('#filtro_grilla')
            .off('change.dashboardStats')
            .on('change.dashboardStats', function() {

                DASHBOARD.grilla_id_seleccionada =
                    $(this).val() || null;

                DASHBOARD.recargarTodasLasGraficas();
                DASHBOARD.recargarEstadisticasPrincipales();
            });
    },


    /* =========================================================
       HELPERS GENERALES
    ========================================================= */

    /**
     * Verifica si existe un contenedor.
     * Evita errores cuando este JS se carga en vistas que no contienen
     * todas las gráficas.
     */
    _hasContainer: function(selector) {
        return !!document.querySelector(selector);
    },


    /**
     * Formatea enteros con separador de miles.
     */
    _formatNumber: function(value) {

        const number = Number(value || 0);

        return number.toLocaleString('es-CO', {
            maximumFractionDigits: 0
        });
    },


    /**
     * Convierte de manera segura un valor del backend a entero.
     */
    _toInt: function(value) {

        const parsed = parseInt(value, 10);

        return Number.isFinite(parsed) ? parsed : 0;
    },


    /**
     * Destruye una gráfica existente.
     */
    _destroyChart: function(key) {

        if (!DASHBOARD._charts[key]) {
            return;
        }

        try {
            DASHBOARD._charts[key].destroy();
        } catch (e) {
            console.warn('No fue posible destruir la gráfica:', key, e);
        }

        delete DASHBOARD._charts[key];
    },


    /**
     * Destruye todas las gráficas creadas por este módulo.
     */
    _destroyAllCharts: function() {

        Object.keys(DASHBOARD._charts).forEach(function(key) {
            DASHBOARD._destroyChart(key);
        });
    },


    /**
     * Render seguro.
     */
    _renderChart: function(key, selector, options) {

        const container = document.querySelector(selector);

        if (!container) {
            return;
        }

        DASHBOARD._destroyChart(key);

        container.innerHTML = '';

        const chart = new ApexCharts(container, options);

        DASHBOARD._charts[key] = chart;

        chart.render().catch(function(error) {
            console.error('Error renderizando gráfica "' + key + '":', error);
        });
    },


    /**
     * Estado vacío visual.
     */
    _showEmpty: function(selector, message, icon) {

        const container = document.querySelector(selector);

        if (!container) {
            return;
        }

        container.innerHTML = `
            <div style="
                min-height:220px;
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                gap:10px;
                padding:24px;
                text-align:center;
                color:#94A3B8;
                font-family:Inter,Manrope,system-ui,sans-serif;
            ">
                <div style="
                    width:48px;
                    height:48px;
                    border-radius:15px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    background:#F1F5F9;
                    color:#64748B;
                    font-size:18px;
                ">
                    <i class="${icon || 'fas fa-chart-bar'}"></i>
                </div>

                <strong style="
                    color:#475569;
                    font-size:13px;
                    font-weight:800;
                ">
                    Sin información disponible
                </strong>

                <span style="
                    font-size:12px;
                    line-height:1.55;
                    max-width:280px;
                ">
                    ${message}
                </span>
            </div>
        `;
    },


    /**
     * Opciones base para todas las gráficas.
     */
    _baseChart: function(type, height) {

        return {
            chart: {
                type: type,
                height: height || 340,
                fontFamily: 'Inter, Manrope, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
                foreColor: DASHBOARD._theme.softText,
                background: 'transparent',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 80
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 450
                    }
                },
                parentHeightOffset: 0
            },

            noData: {
                text: 'Sin datos disponibles',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    color: '#94A3B8',
                    fontSize: '13px',
                    fontFamily: 'Inter, Manrope, sans-serif'
                }
            },

            tooltip: {
                theme: 'dark',
                style: {
                    fontSize: '12px'
                }
            }
        };
    },


    /**
     * Configuración visual común para donuts.
     */
    _donutOptions: function(title, totalLabel, colors, height) {

        const base = DASHBOARD._baseChart('donut', height || 320);

        return Object.assign(base, {

            colors: colors,

            stroke: {
                width: 4,
                colors: ['#FFFFFF']
            },

            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: 700,
                labels: {
                    colors: DASHBOARD._theme.softText
                },
                markers: {
                    width: 8,
                    height: 8,
                    radius: 8,
                    offsetX: -2
                },
                itemMargin: {
                    horizontal: 9,
                    vertical: 5
                }
            },

            dataLabels: {
                enabled: true,
                dropShadow: {
                    enabled: false
                },
                style: {
                    fontSize: '11px',
                    fontWeight: 800
                },
                formatter: function(val) {
                    return val >= 4 ? val.toFixed(1) + '%' : '';
                }
            },

            plotOptions: {
                pie: {
                    expandOnClick: false,
                    offsetY: 0,

                    donut: {
                        size: '73%',

                        labels: {
                            show: true,

                            name: {
                                show: true,
                                offsetY: 20,
                                color: '#64748B',
                                fontSize: '11px',
                                fontWeight: 700
                            },

                            value: {
                                show: true,
                                offsetY: -5,
                                color: '#0F172A',
                                fontSize: '25px',
                                fontWeight: 900,
                                formatter: function(val) {
                                    return DASHBOARD._formatNumber(val);
                                }
                            },

                            total: {
                                show: true,
                                showAlways: true,
                                label: totalLabel,
                                color: '#94A3B8',
                                fontSize: '10px',
                                fontWeight: 800,

                                formatter: function(w) {
                                    const total = w.globals.seriesTotals.reduce(function(a, b) {
                                        return a + b;
                                    }, 0);

                                    return DASHBOARD._formatNumber(total);
                                }
                            }
                        }
                    }
                }
            },

            title: {
                text: title,
                align: 'left',
                margin: 16,
                offsetX: 4,
                style: {
                    fontSize: '14px',
                    fontWeight: 900,
                    color: DASHBOARD._theme.dark
                }
            },

            states: {
                hover: {
                    filter: {
                        type: 'lighten',
                        value: 0.06
                    }
                },

                active: {
                    filter: {
                        type: 'none'
                    }
                }
            },

            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(value) {
                        return DASHBOARD._formatNumber(value) + ' registros';
                    }
                }
            },

            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        height: 290
                    },

                    legend: {
                        fontSize: '10px'
                    },

                    dataLabels: {
                        enabled: false
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '76%'
                            }
                        }
                    }
                }
            }]
        });
    },


    /**
     * Configuración común para barras horizontales.
     */
    _horizontalBarOptions: function(title, color, height) {

        const base = DASHBOARD._baseChart('bar', height || 360);

        return Object.assign(base, {

            colors: Array.isArray(color) ? color : [color],

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 8,
                    borderRadiusApplication: 'end',
                    barHeight: '55%',
                    distributed: false
                }
            },

            grid: {
                borderColor: DASHBOARD._theme.grid,
                strokeDashArray: 4,
                padding: {
                    top: 4,
                    right: 32,
                    bottom: 0,
                    left: 8
                }
            },

            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                offsetX: 8,
                style: {
                    fontSize: '11px',
                    fontWeight: 800,
                    colors: ['#0F172A']
                },
                formatter: function(value) {
                    return DASHBOARD._formatNumber(value);
                }
            },

            xaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#94A3B8',
                        fontSize: '10px',
                        fontWeight: 600
                    },
                    formatter: function(value) {
                        return DASHBOARD._formatNumber(value);
                    }
                }
            },

            yaxis: {
                labels: {
                    maxWidth: 180,
                    style: {
                        colors: '#334155',
                        fontSize: '11px',
                        fontWeight: 700
                    }
                }
            },

            title: {
                text: title,
                align: 'left',
                offsetX: 4,
                margin: 18,
                style: {
                    fontSize: '14px',
                    fontWeight: 900,
                    color: DASHBOARD._theme.dark
                }
            },

            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(value) {
                        return DASHBOARD._formatNumber(value);
                    }
                }
            },

            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        height: 330
                    },

                    dataLabels: {
                        enabled: false
                    },

                    yaxis: {
                        labels: {
                            maxWidth: 115,
                            style: {
                                fontSize: '9px'
                            }
                        }
                    }
                }
            }]
        });
    },


    /* =========================================================
       GRILLAS
    ========================================================= */

    /**
     * Cargar lista de grillas disponibles.
     */
    cargarGrillas: function() {

        // Si el selector no existe en esta vista, evitamos trabajo innecesario.
        if (!document.querySelector('#filtro_grilla')) {
            return;
        }

        const q = {
            op: 'dashboardgrillas'
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            console.log('Respuesta getGrillas:', response);

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const $select = $('#filtro_grilla');

                // Evita duplicar opciones si init se ejecuta más de una vez.
                $select.find('option:not(:first)').remove();

                response.output.response.forEach(function(grilla) {

                    $select.append(
                        $('<option></option>')
                            .attr('value', grilla.id)
                            .text(grilla.nombre)
                    );
                });

                console.log(
                    'Grillas cargadas:',
                    response.output.response.length
                );

            } else {

                console.warn(
                    'No se encontraron grillas o la respuesta fue inválida:',
                    response
                );
            }
        });

        UTIL.cursorNormal();
    },


    /* =========================================================
       RECARGAS
    ========================================================= */

    /**
     * Recargar todas las gráficas con el filtro actual.
     */
    recargarTodasLasGraficas: function() {

        DASHBOARD._generation += 1;

        DASHBOARD._destroyAllCharts();

        [
            '#chartDonutA',
            '#chartDonutB',
            '#chartDonutC',
            '#graficoSeguridadAntioquia',
            '#graficoFactoresAntioquia',
            '#radarInstitucional',
            '#containerSecretarias'
        ].forEach(function(selector) {

            const el = document.querySelector(selector);

            if (el) {
                el.innerHTML = '';
            }
        });

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
     * Recargar estadísticas principales.
     */
    recargarEstadisticasPrincipales: function() {

        const q = {
            op: 'dashboardestadisticas',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (
                response &&
                response.output &&
                response.output.valid
            ) {

                const stats = response.output.response || {};

                DASHBOARD._updateStat(
                    '#stat_total_votantes',
                    stats.total_votantes
                );

                DASHBOARD._updateStat(
                    '#stat_total_grillas',
                    stats.total_grillas
                );

                DASHBOARD._updateStat(
                    '#stat_total_analisis',
                    stats.total_analisis
                );

                DASHBOARD._updateStat(
                    '#stat_total_candidatos',
                    stats.total_candidatos
                );
            }
        });

        UTIL.cursorNormal();
    },


    /**
     * Actualización segura de KPIs.
     */
    _updateStat: function(selector, value) {

        const el = document.querySelector(selector);

        if (!el) {
            return;
        }

        el.textContent = DASHBOARD._formatNumber(
            DASHBOARD._toInt(value)
        );
    },


    /* =========================================================
       ETIQUETAS
    ========================================================= */

    /**
     * Formateador de etiquetas.
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

        return translations[label] || label || 'Sin definir';
    },


    /* =========================================================
       GRÁFICO 1
       VOTANTES POR IDEOLOGÍA
       Barra horizontal para comparar mejor las categorías.
    ========================================================= */

    loadVotantesPorIdeologia: function() {

        const selector = '#chartDonutA';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardideologia',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response
                    .map(function(item) {
                        return {
                            label: DASHBOARD.formatLabel(
                                item.ideologia || 'sin_definir'
                            ),
                            total: DASHBOARD._toInt(item.total)
                        };
                    })
                    .sort(function(a, b) {
                        return b.total - a.total;
                    });

                const categories = data.map(function(item) {
                    return item.label;
                });

                const values = data.map(function(item) {
                    return item.total;
                });

                const options = DASHBOARD._horizontalBarOptions(
                    'Votantes por Ideología Política',
                    DASHBOARD._theme.blue,
                    340
                );

                options.series = [{
                    name: 'Votantes',
                    data: values
                }];

                options.xaxis.categories = categories;

                options.tooltip.y.formatter = function(value) {
                    return DASHBOARD._formatNumber(value) + ' votantes';
                };

                DASHBOARD._renderChart(
                    'ideologia',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay datos de ideología política para el filtro seleccionado.',
                    'fas fa-landmark'
                );
            }
        });
    },


    /* =========================================================
       GRÁFICO 2
       VOTANTES POR GÉNERO
       Donut premium.
    ========================================================= */

    loadVotantesPorGenero: function() {

        const selector = '#chartDonutB';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardgenero',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response;

                const labels = data.map(function(item) {
                    return DASHBOARD.formatLabel(
                        item.genero || 'otro'
                    );
                });

                const series = data.map(function(item) {
                    return DASHBOARD._toInt(item.total);
                });

                const options = DASHBOARD._donutOptions(
                    'Distribución por Género',
                    'TOTAL VOTANTES',
                    DASHBOARD._theme.gender,
                    320
                );

                options.series = series;
                options.labels = labels;

                options.tooltip.y.formatter = function(value) {
                    return DASHBOARD._formatNumber(value) + ' votantes';
                };

                DASHBOARD._renderChart(
                    'genero',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay información de género disponible.',
                    'fas fa-venus-mars'
                );
            }
        });
    },


    /* =========================================================
       GRÁFICO 3
       VOTANTES POR RANGO DE EDAD
       Columnas verticales para comparar cohortes.
    ========================================================= */

    loadVotantesPorEdad: function() {

        const selector = '#chartDonutC';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardedad',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response;

                const categories = data.map(function(item) {
                    return DASHBOARD.formatLabel(item.rango_edad);
                });

                const values = data.map(function(item) {
                    return DASHBOARD._toInt(item.total);
                });

                const options = DASHBOARD._baseChart(
                    'bar',
                    330
                );

                Object.assign(options, {

                    series: [{
                        name: 'Votantes',
                        data: values
                    }],

                    colors: [DASHBOARD._theme.primary2],

                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '52%',
                            borderRadius: 8,
                            borderRadiusApplication: 'end',
                            distributed: true
                        }
                    },

                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.2,
                            gradientToColors: [
                                '#38BDF8',
                                '#2563EB',
                                '#6366F1',
                                '#7C3AED',
                                '#EC4899',
                                '#F59E0B'
                            ],
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 0.82,
                            stops: [0, 100]
                        }
                    },

                    dataLabels: {
                        enabled: true,
                        offsetY: -8,
                        style: {
                            fontSize: '11px',
                            fontWeight: 900,
                            colors: ['#334155']
                        },
                        formatter: function(value) {
                            return DASHBOARD._formatNumber(value);
                        }
                    },

                    xaxis: {
                        categories: categories,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            rotate: -20,
                            trim: false,
                            style: {
                                colors: '#64748B',
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },

                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94A3B8',
                                fontSize: '10px'
                            },
                            formatter: function(value) {
                                return DASHBOARD._formatNumber(value);
                            }
                        }
                    },

                    grid: {
                        borderColor: DASHBOARD._theme.grid,
                        strokeDashArray: 4,
                        padding: {
                            top: 16,
                            right: 8,
                            left: 8
                        }
                    },

                    title: {
                        text: 'Votantes por Rango de Edad',
                        align: 'left',
                        offsetX: 4,
                        margin: 18,
                        style: {
                            fontSize: '14px',
                            fontWeight: 900,
                            color: DASHBOARD._theme.dark
                        }
                    },

                    legend: {
                        show: false
                    },

                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(value) {
                                return DASHBOARD._formatNumber(value) + ' votantes';
                            }
                        }
                    },

                    responsive: [{
                        breakpoint: 600,
                        options: {
                            chart: {
                                height: 310
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45,
                                    style: {
                                        fontSize: '9px'
                                    }
                                }
                            }
                        }
                    }]
                });

                DASHBOARD._renderChart(
                    'edad',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay datos de rango de edad disponibles.',
                    'fas fa-birthday-cake'
                );
            }
        });
    },


    /* =========================================================
       GRÁFICO 4
       ANÁLISIS POR MES
       Area chart con tendencia.
    ========================================================= */

    loadAnalisisPorMes: function() {

        const selector = '#graficoSeguridadAntioquia';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardanalisismes',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response;

                const categories = data.map(function(item) {
                    return item.mes_nombre;
                });

                const values = data.map(function(item) {
                    return DASHBOARD._toInt(item.total);
                });

                const options = DASHBOARD._baseChart(
                    'area',
                    350
                );

                Object.assign(options, {

                    series: [{
                        name: 'Análisis realizados',
                        data: values
                    }],

                    colors: [DASHBOARD._theme.blue],

                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },

                    markers: {
                        size: 4,
                        colors: ['#FFFFFF'],
                        strokeColors: DASHBOARD._theme.blue,
                        strokeWidth: 3,
                        hover: {
                            size: 7
                        }
                    },

                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.38,
                            opacityTo: 0.03,
                            stops: [0, 90, 100]
                        }
                    },

                    dataLabels: {
                        enabled: false
                    },

                    xaxis: {
                        categories: categories,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#64748B',
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },

                    yaxis: {
                        min: 0,
                        forceNiceScale: true,
                        labels: {
                            style: {
                                colors: '#94A3B8',
                                fontSize: '10px'
                            },
                            formatter: function(value) {
                                return DASHBOARD._formatNumber(value);
                            }
                        }
                    },

                    grid: {
                        borderColor: DASHBOARD._theme.grid,
                        strokeDashArray: 4,
                        padding: {
                            top: 8,
                            right: 10,
                            left: 8
                        }
                    },

                    title: {
                        text: 'Evolución de Análisis · Últimos 6 Meses',
                        align: 'left',
                        offsetX: 4,
                        margin: 18,
                        style: {
                            fontSize: '14px',
                            fontWeight: 900,
                            color: DASHBOARD._theme.dark
                        }
                    },

                    tooltip: {
                        theme: 'dark',
                        x: {
                            show: true
                        },
                        y: {
                            formatter: function(value) {
                                return DASHBOARD._formatNumber(value) + ' análisis';
                            }
                        }
                    },

                    responsive: [{
                        breakpoint: 600,
                        options: {
                            chart: {
                                height: 310
                            },
                            stroke: {
                                width: 2.5
                            }
                        }
                    }]
                });

                DASHBOARD._renderChart(
                    'analisis_mes',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay datos de análisis por mes para mostrar.',
                    'fas fa-chart-line'
                );
            }
        });
    },


    /* =========================================================
       GRÁFICO 5
       TOP CANDIDATOS
       Ranking horizontal.
    ========================================================= */

    loadTopCandidatos: function() {

        const selector = '#graficoFactoresAntioquia';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardtopcandidatos',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response
                    .map(function(item) {
                        return {
                            nombre: item.nombre_completo || 'Sin nombre',
                            total: DASHBOARD._toInt(item.total_analisis)
                        };
                    })
                    .sort(function(a, b) {
                        return b.total - a.total;
                    });

                const categories = data.map(function(item, index) {
                    return (index + 1) + '. ' + item.nombre;
                });

                const values = data.map(function(item) {
                    return item.total;
                });

                const options = DASHBOARD._horizontalBarOptions(
                    'Top 5 Candidatos con Más Análisis',
                    DASHBOARD._theme.ideology,
                    360
                );

                options.series = [{
                    name: 'Análisis',
                    data: values
                }];

                options.xaxis.categories = categories;

                options.plotOptions.bar.distributed = true;
                options.plotOptions.bar.barHeight = '58%';

                options.dataLabels.formatter = function(value) {
                    return DASHBOARD._formatNumber(value) + ' análisis';
                };

                options.legend = {
                    show: false
                };

                options.tooltip.y.formatter = function(value) {
                    return DASHBOARD._formatNumber(value) + ' análisis';
                };

                DASHBOARD._renderChart(
                    'top_candidatos',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay datos de candidatos disponibles.',
                    'fas fa-user-tie'
                );
            }
        });
    },


    /* =========================================================
       GRÁFICO 6
       VOTANTES POR NIVEL DE INGRESOS
       Barras horizontales para mejor lectura.
    ========================================================= */

    loadVotantesPorIngresos: function() {

        const selector = '#radarInstitucional';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardingresos',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response;

                const categories = data.map(function(item) {
                    return DASHBOARD.formatLabel(
                        item.nivel_ingresos || 'menos_1_salario'
                    );
                });

                const values = data.map(function(item) {
                    return DASHBOARD._toInt(item.total);
                });

                const options = DASHBOARD._horizontalBarOptions(
                    'Votantes por Nivel de Ingresos',
                    DASHBOARD._theme.cyan,
                    370
                );

                options.series = [{
                    name: 'Votantes',
                    data: values
                }];

                options.xaxis.categories = categories;

                options.fill = {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'horizontal',
                        shadeIntensity: 0.25,
                        gradientToColors: ['#2563EB'],
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 0.86,
                        stops: [0, 100]
                    }
                };

                options.tooltip.y.formatter = function(value) {
                    return DASHBOARD._formatNumber(value) + ' votantes';
                };

                DASHBOARD._renderChart(
                    'ingresos',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay información sobre nivel de ingresos.',
                    'fas fa-wallet'
                );
            }
        });
    },


    /* =========================================================
       GRÁFICO 7
       ESTADO DE GRILLAS
       Donut premium.
    ========================================================= */

    loadGrillasPorEstado: function() {

        const selector = '#containerSecretarias';

        if (!DASHBOARD._hasContainer(selector)) {
            return;
        }

        const generation = DASHBOARD._generation;

        const q = {
            op: 'dashboardgrillasestado',
            grilla_id: DASHBOARD.grilla_id_seleccionada
        };

        UTIL.callAjaxRqstPOST(q, function(response) {

            if (generation !== DASHBOARD._generation) {
                return;
            }

            if (
                response &&
                response.output &&
                response.output.valid &&
                Array.isArray(response.output.response) &&
                response.output.response.length > 0
            ) {

                const data = response.output.response;

                const labels = data.map(function(item) {
                    return item.estado || 'Sin estado';
                });

                const series = data.map(function(item) {
                    return DASHBOARD._toInt(item.total);
                });

                const options = DASHBOARD._donutOptions(
                    'Estado de Grillas de Evaluación',
                    'TOTAL GRILLAS',
                    [
                        DASHBOARD._theme.green,
                        DASHBOARD._theme.red,
                        DASHBOARD._theme.slate
                    ],
                    350
                );

                options.series = series;
                options.labels = labels;

                options.tooltip.y.formatter = function(value) {
                    return DASHBOARD._formatNumber(value) + ' grillas';
                };

                DASHBOARD._renderChart(
                    'grillas_estado',
                    selector,
                    options
                );

            } else {

                DASHBOARD._showEmpty(
                    selector,
                    'No hay datos de estado de grillas disponibles.',
                    'fas fa-layer-group'
                );
            }
        });
    }
};


// DASHBOARD.init() se llama desde el ready del dashboard.php
