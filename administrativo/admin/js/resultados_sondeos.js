$(document).on("ready", init);

// Instancias ApexCharts activas
var apexCharts = {};

function init() {
  console.log("Módulo de Resultados de Sondeos cargado");
  $("#estadisticas-container").hide();
  $("#empty-state").show();
}

// Paletas de colores cohesivas
var PALETAS = {
  ideologia: ['#2E93fA','#66DA26','#546E7A','#E91E63','#FF9800','#00BCD4','#9C27B0'],
  genero:    ['#008FFB','#FF4560','#775DD0','#00E396'],
  edad:      ['#00E396','#FEB019','#FF4560','#775DD0','#546E7A','#2E93fA'],
  ingresos:  ['#20427F','#2e58a8','#4facfe','#00f2fe','#a8edea'],
  educacion: ['#11998e','#38ef7d','#43b89c','#1a7a1a','#2ca02c'],
  geo:       ['#20427F','#2e58a8','#4facfe','#FEB019','#FF4560','#00E396','#775DD0','#546E7A','#FF9800','#00BCD4']
};

function destroyChart(id) {
  if (apexCharts[id]) {
    apexCharts[id].destroy();
    delete apexCharts[id];
  }
}

function makeDonut(elId, series, labels, palette, totalLabel) {
  destroyChart(elId);
  var el = document.getElementById(elId);
  if (!el) return;
  el.innerHTML = '';

  var chart = new ApexCharts(el, {
    chart: { type: 'donut', height: 230, toolbar: { show: false } },
    series: series,
    labels: labels,
    colors: palette,
    dataLabels: {
      enabled: true,
      formatter: function(val) { return val.toFixed(1) + '%'; },
      style: { fontSize: '11px', fontWeight: 700 },
      dropShadow: { enabled: false }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '62%',
          labels: {
            show: true,
            total: {
              show: true,
              label: totalLabel || 'Total',
              fontSize: '13px',
              fontWeight: 700,
              color: '#0f172a'
            }
          }
        }
      }
    },
    legend: { position: 'bottom', fontSize: '12px', fontWeight: 700 },
    stroke: { width: 2 },
    tooltip: {
      y: {
        formatter: function(val, opts) {
          var total = opts.series.reduce(function(a,b){ return a+b; }, 0);
          return val + ' (' + ((val/total)*100).toFixed(1) + '%)';
        }
      }
    },
    responsive: [{ breakpoint: 480, options: { chart: { height: 190 }, legend: { position: 'bottom' } } }]
  });
  chart.render();
  apexCharts[elId] = chart;
}

function makeBar(elId, labels, valores, palette, horizontal) {
  destroyChart(elId);
  var el = document.getElementById(elId);
  if (!el) return;
  el.innerHTML = '';

  var colores = labels.map(function(_, i){ return palette[i % palette.length]; });

  var chart = new ApexCharts(el, {
    chart: {
      type: 'bar',
      height: horizontal ? Math.max(180, labels.length * 30) : 200,
      toolbar: { show: false },
      animations: { enabled: true, speed: 400 }
    },
    series: [{ name: 'Respuestas', data: valores }],
    xaxis: {
      categories: labels,
      labels: { style: { fontSize: '11px', fontWeight: 600 } }
    },
    yaxis: { labels: { style: { fontSize: '11px' } } },
    colors: colores,
    plotOptions: {
      bar: {
        distributed: true,
        horizontal: !!horizontal,
        borderRadius: 5,
        dataLabels: { position: horizontal ? 'top' : 'top' }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function(val, opts) {
        try {
          var s = opts && opts.series && opts.series[0];
          if (!s) return '';
          var total = s.reduce(function(a,b){ return a+b; }, 0);
          return total > 0 ? ((val/total)*100).toFixed(1)+'%' : '';
        } catch(e) { return ''; }
      },
      style: { fontSize: '10px', fontWeight: 700, colors: ['#0f172a'] },
      offsetY: horizontal ? 0 : -18
    },
    legend: { show: false },
    grid: { borderColor: 'rgba(15,23,42,.07)' },
    tooltip: {
      y: {
        formatter: function(val, opts) {
          try {
            var s = opts && opts.series && opts.series[0];
            if (!s) return String(val);
            var total = s.reduce(function(a,b){ return a+b; }, 0);
            return val + ' (' + (total > 0 ? ((val/total)*100).toFixed(1) : 0) + '%)';
          } catch(e) { return String(val); }
        }
      }
    }
  });
  chart.render();
  apexCharts[elId] = chart;
}

const RESULTADOS_SONDEO = {
  territorioFiltro: { departamento: '', municipio: '', nombre: 'Colombia (nacional)', nivel: 'pais' },

  init: function () {
    console.log("RESULTADOS_SONDEO.init() ejecutado");
    $("#estadisticas-container").hide();
    $("#empty-state").show();
  },

  setTerritorio: function (payload) {
    RESULTADOS_SONDEO.territorioFiltro = {
      departamento: (payload && payload.departamento) || '',
      municipio: (payload && payload.municipio) || '',
      nombre: (payload && payload.nombre) || 'Colombia (nacional)',
      nivel: (payload && payload.nivel) || 'pais'
    };
  },

  cargarEstadisticas: function (opts) {
    const sondeoId = $("#sondeo_selector").val();
    const silent = !!(opts && opts.silent);

    if (!sondeoId) {
      if (!silent) UTIL.mostrarMensajeValidacion("Por favor selecciona un sondeo");
      return;
    }

    if (!silent) UTIL.cursorBusy();

    const q = {
      op: "respuestasondeogetestadisticascompletas",
      tbl_sondeo_id: sondeoId,
    };
    if (RESULTADOS_SONDEO.territorioFiltro.departamento) {
      q.departamento_click = RESULTADOS_SONDEO.territorioFiltro.departamento;
    }
    if (RESULTADOS_SONDEO.territorioFiltro.municipio) {
      q.municipio_click = RESULTADOS_SONDEO.territorioFiltro.municipio;
    }

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (!silent) UTIL.cursorNormal();
        if (data.output.valid) {
          RESULTADOS_SONDEO.mostrarEstadisticas(data.output.response, { silent: silent });
        } else if (!silent) {
          UTIL.mostrarMensajeError(
            data.output.response.content || "Error al cargar las estadísticas"
          );
        }
      },
      error: function () {
        if (!silent) UTIL.cursorNormal();
        if (!silent) UTIL.mostrarMensajeError("Ha ocurrido un error al cargar las estadísticas");
      },
    });
  },

  mostrarEstadisticas: function (data, opts) {
    const silent = !!(opts && opts.silent);
    console.log("Datos recibidos:", data);

    $("#empty-state").hide();
    $("#estadisticas-container").show();

    const general = data.generales.general;
    $("#stat-total-respuestas").text(general.total_respuestas || 0);
    $("#stat-votantes-unicos").text(general.votantes_unicos || 0);
    $("#stat-dias-activo").text(general.dias_activo || 0);
    const promedioDiario = general.dias_activo > 0
      ? Math.round(general.total_respuestas / general.dias_activo) : 0;
    $("#stat-promedio-diario").text(promedioDiario);

    setTimeout(function() {
      RESULTADOS_SONDEO.renderChartGeneral(data.generales);
      RESULTADOS_SONDEO.renderChartIdeologia(data.ideologia);
      RESULTADOS_SONDEO.renderChartGenero(data.genero);
      RESULTADOS_SONDEO.renderChartEdad(data.edad);
      RESULTADOS_SONDEO.renderChartIngresos(data.ingresos);
      RESULTADOS_SONDEO.renderChartEducacion(data.educacion);
      RESULTADOS_SONDEO.renderChartDepartamento(data.departamento);
      RESULTADOS_SONDEO.renderChartMunicipio(data.municipio);
    }, 150);

    if (!silent && $("#estadisticas-container").offset()) {
      $('html, body').animate({ scrollTop: $("#estadisticas-container").offset().top - 100 }, 500);
    }
  },

  renderChartGeneral: function (data) {
    var el = document.getElementById("chart-general");
    if (!el) return;

    var labels = [], valores = [];

    if (data.candidatos && data.candidatos.length > 0) {
      data.candidatos.forEach(function(c) {
        labels.push(c.nombre_completo);
        valores.push(parseInt(c.votos) || 0);
      });
    } else if (data.opciones && data.opciones.length > 0) {
      data.opciones.forEach(function(o) {
        labels.push(o.respuesta_opcion);
        valores.push(parseInt(o.cantidad) || 0);
      });
    } else {
      el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos generales</p>';
      return;
    }

    // Si son pocas opciones (≤6) usar donut, si son más usar barra horizontal
    if (labels.length <= 6) {
      makeDonut('chart-general', valores, labels, PALETAS.geo, 'Total');
    } else {
      makeBar('chart-general', labels, valores, PALETAS.geo, true);
    }
  },

  renderChartIdeologia: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-ideologia");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var labels  = data.map(function(i){ return i.ideologia; });
    var valores = data.map(function(i){ return parseInt(i.cantidad); });
    makeDonut('chart-ideologia', valores, labels, PALETAS.ideologia, 'Total');
  },

  renderChartGenero: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-genero");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var labels  = data.map(function(i){ return i.genero; });
    var valores = data.map(function(i){ return parseInt(i.cantidad); });
    makeDonut('chart-genero', valores, labels, PALETAS.genero, 'Total');
  },

  renderChartEdad: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-edad");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var labels  = data.map(function(i){ return i.rango_edad; });
    var valores = data.map(function(i){ return parseInt(i.cantidad); });
    makeDonut('chart-edad', valores, labels, PALETAS.edad, 'Total');
  },

  renderChartIngresos: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-ingresos");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var labels  = data.map(function(i){ return i.nivel_ingresos; });
    var valores = data.map(function(i){ return parseInt(i.cantidad); });
    makeDonut('chart-ingresos', valores, labels, PALETAS.ingresos, 'Total');
  },

  renderChartEducacion: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-educacion");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var labels  = data.map(function(i){ return i.nivel_educacion; });
    var valores = data.map(function(i){ return parseInt(i.cantidad); });
    makeBar('chart-educacion', labels, valores, PALETAS.educacion, true);
  },

  renderChartDepartamento: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-departamento");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var labels  = data.map(function(i){ return i.departamento || 'Sin definir'; });
    var valores = data.map(function(i){ return parseInt(i.cantidad); });
    makeBar('chart-departamento', labels, valores, PALETAS.geo, true);
  },

  renderChartMunicipio: function (data) {
    if (!data || data.length === 0) {
      var el = document.getElementById("chart-municipio");
      if (el) el.innerHTML = '<p class="text-center text-muted py-4 fw-bold">Sin datos</p>';
      return;
    }
    var top10   = data.slice(0, 10);
    var labels  = top10.map(function(i){
      var dep = i.departamento || '';
      return dep ? (i.municipio||'?') + ', ' + dep : (i.municipio||'Sin definir');
    });
    var valores = top10.map(function(i){ return parseInt(i.cantidad); });
    makeBar('chart-municipio', labels, valores, PALETAS.geo, true);
  },
};
