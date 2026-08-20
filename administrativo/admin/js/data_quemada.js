// admin/js/data_quemada.js

document.addEventListener("DOMContentLoaded", function () {
  // ===================== GRÁFICOS TIPO DONUT =====================
const opcionesDonut = (id, title, labels, data, colors) => {
  const options = {
    series: data,
    chart: { type: 'donut', height: 270},
    labels: labels,
    colors: colors,
    legend: { position: 'bottom' },
    title: {
      text: title,
      align: 'center',
      style: { fontSize: '15px' }
    }
  };
  const chart = new ApexCharts(document.querySelector("#" + id), options);
  chart.render();
};

opcionesDonut("chartDonutA", "Porcentaje de Municipios según el color",
  ["Verde", "Azul", "Amarillo", "Naranja", "Rojo"], [10, 15, 40, 20, 15],
  ["#28a745", "#007bff", "#ffc107", "#fd7e14", "#dc3545"]
);

opcionesDonut("chartDonutB", "Porcentaje de Veredas según el color",
  ["Verde", "Azul", "Amarillo", "Naranja", "Rojo"], [8, 12, 15, 8, 57],
  ["#28a745", "#007bff", "#ffc107", "#fd7e14", "#dc3545"]
);

opcionesDonut("chartDonutC", "Distribución de Inversión por Secretaría",
  ["Infraestructura", "Educación", "Salud", "Ambiente", "Seguridad"],
  [35, 25, 20, 10, 10],
  ["#007bff", "#28a745", "#ffc107", "#17a2b8", "#dc3545"]
);


  // ===================== GRÁFICOS DE SEGURIDAD =====================
  new ApexCharts(document.querySelector("#graficoSeguridadAntioquia"), {
    chart: { type: 'bar', height: 350 },
    series: [{
      name: "Casos",
      data: [
        { x: "Cultivos ilícitos", y: 35 },
        { x: "Minería ilegal", y: 28 },
        { x: "Delitos", y: 42 },
        { x: "Capturas", y: 19 }
      ]
    }],
    colors: ['#C0392B', '#E67E22', '#2980B9', '#8E44AD'],
    plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 6 } },
    dataLabels: { enabled: true },
    title: { text: 'Indicadores de Seguridad', align: 'center', style: { fontSize: '16px' } },
    xaxis: { labels: { style: { fontWeight: 600 } } },
    yaxis: { title: { text: 'Cantidad' } }
  }).render();

  new ApexCharts(document.querySelector("#graficoFactoresAntioquia"), {
    chart: { type: 'bar', height: 350 },
    series: [{
      name: "Eventos",
      data: [
        { x: "Derrumbes", y: 33 },
        { x: "Vías en mal estado", y: 48 },
        { x: "Fallas eléctricas", y: 14 },
        { x: "Aislamiento vial", y: 21 }
      ]
    }],
    colors: ['#27AE60', '#F1C40F', '#E74C3C', '#7F8C8D'],
    plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 6 } },
    dataLabels: { enabled: true },
    title: { text: 'Infraestructura y Riesgos', align: 'center', style: { fontSize: '16px' } },
    xaxis: { labels: { style: { fontWeight: 600 } } },
    yaxis: { title: { text: 'Incidentes' } }
  }).render();

  // ===================== MUNICIPIOS SEGUROS =====================
  new ApexCharts(document.querySelector("#graficoMunicipiosSeguros"), {
    chart: { type: 'bar', height: 450 },
    series: [{
      name: "Tasa de homicidios por 100 000",
      data: [
        { x: "Envigado (Valle de Aburrá)", y: 19 },
        { x: "Rionegro (Oriente)", y: 22 },
        { x: "La Estrella (Valle de Aburrá)", y: 25 },
        { x: "Itagüí (Valle de Aburrá)", y: 30 }
      ]
    }],
    colors: ['#2E86C1', '#28B463', '#F4D03F', '#AF7AC5'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 6 } },
    dataLabels: { enabled: true, formatter: val => val + " /100 000" },
    title: { text: 'Municipios más seguros por Subregión', align: 'center' },
    xaxis: { title: { text: 'Tasa homicidios' } }
  }).render();

  // ===================== MUNICIPIOS INSEGUROS =====================
  new ApexCharts(document.querySelector("#graficoMunicipiosInseguros"), {
    chart: { type: 'bar', height: 450 },
    series: [{
      name: "Tasa de homicidios por 100 000",
      data: [
        { x: "Tarazá (Bajo Cauca)", y: 225 },
        { x: "Caucasia (Bajo Cauca)", y: 170 },
        { x: "Ituango (Norte)", y: 155 },
        { x: "Valdivia (Norte)", y: 140 },
        { x: "Segovia (Nordeste)", y: 120 }
      ]
    }],
    colors: ['#E74C3C', '#D35400', '#C0392B', '#A93226', '#922B21'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 6 } },
    dataLabels: { enabled: true, formatter: val => val + " /100 000" },
    title: { text: 'Municipios con mayores problemáticas de seguridad', align: 'center' },
    xaxis: { title: { text: 'Tasa de homicidios / conflictividad' } }
  }).render();
  new ApexCharts(document.querySelector("#radarInstitucional"), {
  chart: {
    height: 400,
    type: 'radar',
    toolbar: { show: false }
  },
  series: [
    {
      name: "Valle de Aburrá",
      data: [95, 90, 85, 80, 75]
    },
    {
      name: "Bajo Cauca",
      data: [40, 35, 30, 25, 20]
    },
    {
      name: "Nordeste",
      data: [50, 45, 40, 30, 35]
    },
    {
      name: "Urabá",
      data: [60, 55, 50, 45, 50]
    }
  ],
  labels: [
    "Cobertura policial",
    "Tiempo de respuesta",
    "Presencia judicial",
    "Infraestructura judicial",
    "Inversión en seguridad"
  ],
  stroke: {
    width: 2
  },
  fill: {
    opacity: 0.25
  },
  markers: {
    size: 4
  },
  yaxis: {
    show: true,
    min: 0,
    max: 100,
    tickAmount: 5,
    labels: { formatter: val => val + "%" }
  },
  legend: {
    position: "bottom"
  }
}).render();

});
// 🔁 Reemplazo de getPromedioPs2025PorSecretaria con data quemada
ESTADO_GENERAL.getPromedioPs2025PorSecretaria = function () {
    const seriesData = [
        { name: "Educación", y: 78.5 },
        { name: "Salud", y: 64.2 },
        { name: "Infraestructura", y: 55.1 },
        { name: "Vivienda", y: 82.3 },
        { name: "Cultura", y: 69.0 },
        { name: "Ambiente", y: 73.4 },
        { name: "Trabajo", y: 60.2 },
        { name: "Seguridad", y: 50.9 },
        { name: "TIC", y: 90.0 }
    ];

    const options = {
        chart: {
            height: 400,
            type: 'bar',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                columnWidth: '60%',
                borderRadius: 8,
            },
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '11px',
                fontWeight: 'bold',
                colors: ['#fff']
            },
            background: {
                enabled: true,
                foreColor: '#333',
                borderRadius: 3
            }
        },
        colors: ['#27AE60', '#F1C40F', '#E74C3C'],
        stroke: {
            show: true,
            width: 1,
            colors: ['#fff'],
        },
        series: [{
            name: "Promedio PS2025",
            data: seriesData.map(item => item.y),
        }],
        xaxis: {
            categories: seriesData.map(item => item.name),
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold',
                }
            }
        },
        yaxis: {
            title: {
                text: "Secretaría",
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "horizontal",
                shadeIntensity: 0.5,
                gradientToColors: ['#F39C12', '#E67E22', '#C0392B'],
                inverseColors: true,
                opacityFrom: 0.8,
                opacityTo: 1,
                stops: [0, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(1) + " %";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#containerSecretarias"), options);
    chart.render();
};
// Datos quemados simulados por color
const datosQuemados = {
    2: { rojo: 5, naranja: 10, amarillo: 15, azul: 20, verde: 25 },
    3: { rojo: 10, naranja: 20, amarillo: 10, azul: 30, verde: 10 },
    4: { rojo: 15, naranja: 10, amarillo: 25, azul: 10, verde: 40 },
    5: { rojo: 20, naranja: 5, amarillo: 15, azul: 25, verde: 35 },
    7: { rojo: 5, naranja: 10, amarillo: 20, azul: 30, verde: 35 },
    8: { rojo: 15, naranja: 15, amarillo: 20, azul: 20, verde: 30 },
    10: { rojo: 10, naranja: 20, amarillo: 30, azul: 20, verde: 20 },
    11: { rojo: 25, naranja: 25, amarillo: 20, azul: 15, verde: 15 },
    13: { rojo: 30, naranja: 20, amarillo: 10, azul: 20, verde: 20 },
};

const coloresHex = {
    rojo: "#cd162c",
    naranja: "#cd7d16",
    amarillo: "#dbd509",
    azul: "#2774f1",
    verde: "#62af0a"
};

ESTADO_GENERAL.mostarFactoresDeEstabilidadMunicipiosByPilarIdPorColores = function () {
    const pilarId = $("#pilarId").val();
    const datos = datosQuemados[pilarId] || { rojo: 0, naranja: 0, amarillo: 0, azul: 0, verde: 0 };

    const labels = Object.keys(datos).map(color => color.charAt(0).toUpperCase() + color.slice(1));
    const values = Object.values(datos);
    const backgroundColors = Object.keys(datos).map(color => coloresHex[color]);

    const container = document.querySelector("#graficasPorFatoresMunicipiosPorColor");
    container.innerHTML = "";

    const wrapper = document.createElement("div");
    wrapper.style.display = "flex";
    wrapper.style.flexDirection = "column";
    wrapper.style.alignItems = "center";
    wrapper.style.width = "100%";

    const canvasContainer = document.createElement("div");
    canvasContainer.style.width = "100%";
    canvasContainer.style.height = "250px";
    canvasContainer.style.display = "flex";
    canvasContainer.style.justifyContent = "center";
    canvasContainer.style.alignItems = "center";

    const canvas = document.createElement("canvas");
    canvas.style.maxWidth = "100%";
    canvas.style.height = "100%";

    canvasContainer.appendChild(canvas);
    wrapper.appendChild(canvasContainer);
    container.appendChild(wrapper);

    const ctx = canvas.getContext("2d");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: $("#pilarId").find(":selected").text(),
                data: values,
                backgroundColor: backgroundColors,
                borderRadius: 10,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });
};

ESTADO_GENERAL.mostarFactoresDeEstabilidadVeredasByPilarIdPorColores = function () {
    const pilarId = $("#pilarId").val();
    const datos = datosQuemados[pilarId] || { rojo: 0, naranja: 0, amarillo: 0, azul: 0, verde: 0 };

    const labels = Object.keys(datos).map(color => color.charAt(0).toUpperCase() + color.slice(1));
    const values = Object.values(datos);
    const backgroundColors = Object.keys(datos).map(color => coloresHex[color]);

    const container = document.querySelector("#graficasPorFatoresVeredasPorColor");
    container.innerHTML = "";

    const wrapper = document.createElement("div");
    wrapper.style.display = "flex";
    wrapper.style.flexDirection = "column";
    wrapper.style.alignItems = "center";
    wrapper.style.width = "100%";

    const canvasContainer = document.createElement("div");
    canvasContainer.style.width = "100%";
    canvasContainer.style.height = "250px";
    canvasContainer.style.display = "flex";
    canvasContainer.style.justifyContent = "center";
    canvasContainer.style.alignItems = "center";

    const canvas = document.createElement("canvas");
    canvas.style.maxWidth = "100%";
    canvas.style.height = "100%";

    canvasContainer.appendChild(canvas);
    wrapper.appendChild(canvasContainer);
    container.appendChild(wrapper);

    const ctx = canvas.getContext("2d");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: $("#pilarId").find(":selected").text(),
                data: values,
                backgroundColor: backgroundColors,
                borderRadius: 10,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });
};
      
      