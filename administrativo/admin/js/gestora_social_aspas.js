$(document).on("ready", init);

function init() {
    q = {};
}

var GESTORA_SOCIAL = {
    charts: {}, // Almacena instancias de gráficos

    init: function () {
        // Carga los datos iniciales
        GESTORA_SOCIAL.fetchData("getPoblacionImpactadaPorMunicipioAspas")
            .then((dataMostrar) => {
                // Click para cambiar graficas
                $('a[data-toggle="tab"]').on('click', function (e) {
                    e.preventDefault();

                    const provincia = $(this).attr("href").replace("#", ""); // ID de la provincia
                    GESTORA_SOCIAL.cambiarGrafica(provincia, dataMostrar); // Cambia la gráfica
                });

                // Renderiza la gráfica inicial
                GESTORA_SOCIAL.cambiarGrafica("Soto_Norte", dataMostrar);
            })
            .catch((error) => {
                console.error("Error al cargar los datos de población:", error);
            });
    },

    fetchData: function (operation) {
        const q = { op: operation };

        return $.ajax({
            data: q,
            type: "GET",
            dataType: "json",
            url: "admin/ajax/rqst.php",
        }).then((data) => {
            if (data.output.valid) {
                return data.output.response.map(item => ({
                    name: item.municipio,
                    provincia: item.provincia || null,
                    y: parseFloat(item.total),
                }));
            } else {
                return [];
            }
        });
    },

    cambiarGrafica: function (provincia, dataMostrar) {
        console.log(`Cambiando gráfica para la provincia: ${provincia}`);
        const containerId = `#bar-chart-Soto_Norte`; // ID genérico

        // Filtra los datos para la provincia seleccionada
        const dataFiltrada = dataMostrar.filter(item => item.provincia === provincia);

        console.log(`Datos filtrados para ${provincia}:`, dataFiltrada);

        // Actualiza el subtítulo de la gráfica
        const subtitulo = `Total Población Impactada ${provincia}`;
        $(`${containerId}`).closest(".card").find(".card-header h5").text(subtitulo);

        // Destruye cualquier gráfica previa
        if (GESTORA_SOCIAL.charts[provincia]) {
            GESTORA_SOCIAL.charts[provincia].destroy();
        }

        // Limpia el contenedor y renderiza una nueva gráfica si hay datos
        $(containerId).html("");
        if (dataFiltrada.length > 0) {
            GESTORA_SOCIAL.renderChart(containerId, subtitulo, dataFiltrada, provincia);
        } else {
            $(containerId).html(`<p>No hay datos disponibles para ${provincia}.</p>`);
        }
    },

    renderChart: function (container, title, seriesData, chartKey) {
        const options = {
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded',
                },
            },
            dataLabels: {
                enabled: false,
            },
            colors: ["#0e9e4a", "#1abc9c", "#e74c3c"],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent'],
            },
            series: [{
                name: title,
                data: seriesData.map(item => item.y),
            }],
            xaxis: {
                categories: seriesData.map(item => item.name),
            },
            yaxis: {
                title: {
                    text: 'Total',
                },
            },
            fill: {
                opacity: 1,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString();
                    },
                },
            },
        };

        const chart = new ApexCharts(
            document.querySelector(container),
            options
        );
        chart.render();

        GESTORA_SOCIAL.charts[chartKey] = chart;
    },
};

$(document).ready(function () {
    GESTORA_SOCIAL.init();
});
document.addEventListener("DOMContentLoaded", function () {
    const datosDemo = [
        { name: "Poblacion 1", y: 12500 },
        { name: "Poblacion 2", y: 18400 },
        { name: "Poblacion 3", y: 9400 },
        { name: "Poblacion 4", y: 15300 },
        { name: "Poblacion 5", y: 21100 },
    ];

    const options = {
        chart: {
            height: 350,
            type: 'bar',
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
            },
        },
        dataLabels: {
            enabled: false,
        },
        colors: ["#0e9e4a", "#1abc9c", "#e74c3c"],
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent'],
        },
        series: [{
            name: "Población",
            data: datosDemo.map(item => item.y),
        }],
        xaxis: {
            categories: datosDemo.map(item => item.name),
        },
        yaxis: {
            title: {
                text: 'Total',
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString();
                },
            },
        },
    };

    const chart = new ApexCharts(document.querySelector("#bar-chart-demo"), options);
    chart.render();
});
