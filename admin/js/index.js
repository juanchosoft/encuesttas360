/* =========================================================
   Estadísticas 360 Web - Dashboard JS (Charts + Mapa)
   - Chart.js horizontal bar con fotos/iniciales
   - Modo sondeo / cuestionario
   - Mapa ganadores + card detalle
========================================================= */

/* =========================
   Colores base
========================= */
let ColoresCandidatos = {
  1: "#1f77b4",
  2: "#ff7f0e",
  3: "#2ca02c",
  4: "#d62728"
};

// Usar colores dinámicos si están disponibles
if (typeof window.ColoresCandidatosDinamicos !== "undefined" && window.ColoresCandidatosDinamicos) {
  ColoresCandidatos = window.ColoresCandidatosDinamicos;
}

const COLOR_TEMA = "#20427F";

// Paleta fallback
const PALETA_COLORES = [
  "#1f77b4",
  "#ff7f0e",
  "#2ca02c",
  "#d62728",
  "#9467bd",
  "#8c564b",
  "#e377c2",
  "#7f7f7f",
  "#bcbd22",
  "#17becf"
];

$(document).ready(function () {
  let grafico = null;         // graficoVotos
  let graficoGeneral = null;  // graficoGeneral
  let preguntaSeleccionada = 0;

  // Modo activo cuando opcion_activa_web = 'ambos'
  // Valores: 'sondeo' | 'cuestionario'
  let modoActual = (window.OPCION_ACTIVA_WEB === 'ambos') ? 'sondeo' : window.OPCION_ACTIVA_WEB || 'sondeo';

  const MAPA_MUNI_HABILITADOS = (window.MAPA_MUNICIPAL_DEPTOS || []).map(function (c) {
    return String(parseInt(c, 10)).padStart(2, '0');
  });
  let nivelMapa = 'pais'; // 'pais' | 'departamento'
  let mapaColombiaHtmlBackup = null;

  function normalizeDep(codigo) {
    if (codigo == null || codigo === '') return '';
    return String(parseInt(codigo, 10)).padStart(2, '0');
  }

  function isMapaMunicipalHabilitado(codigo) {
    const d = normalizeDep(codigo);
    return d !== '' && MAPA_MUNI_HABILITADOS.indexOf(d) !== -1;
  }

  function appendTerritorioIds(requestData) {
    if (modoActual === 'sondeo' && window.DASH_TERRITORIO_ID > 0) {
      requestData.sondeo_id = window.DASH_TERRITORIO_ID;
    }
    // Filtro geo activo en nivel país (depto seleccionado) o en drill municipal
    if (MapaSondeo.departamentoActual) {
      requestData.departamento_click = normalizeDep(MapaSondeo.departamentoActual);
    }
    return requestData;
  }

  function actualizarUiNivel() {
    const enDepto = nivelMapa === 'departamento';
    const nombre = MapaSondeo.nombreTerritorioActual || 'Departamento';
    if (enDepto) {
      $('#tituloMapaNivel').text('Mapa de municipios — ' + nombre);
      $('#btnVolverColombia').removeClass('d-none');
      $('#bcDepto').text(nombre).removeClass('d-none');
      $('#bcPais').removeClass('active').html('<a href="#" id="linkVolverColombiaBc">Colombia</a>');
      $('#tituloResumenNivel').html('<i class="fas fa-chart-column me-2 text-primary"></i>Resumen — ' + nombre);
      $('#subResumenNivel').text('Distribución de respuestas en ' + nombre + '.');
      $('#subDetalleTerritorio').text('Respuestas por municipio (color = opción líder).');
    } else {
      $('#tituloMapaNivel').text('Mapa territorial de Colombia');
      $('#btnVolverColombia').addClass('d-none');
      $('#bcDepto').addClass('d-none').text('');
      $('#bcPais').addClass('active').text('Colombia');
      $('#tituloResumenNivel').html('<i class="fas fa-chart-column me-2 text-primary"></i>Resumen nacional');
      $('#subResumenNivel').text('Distribución de respuestas a nivel país.');
      $('#subDetalleTerritorio').text('Respuestas por departamento (color = opción líder).');
      $('#mapaMunicipalMsg').addClass('d-none').text('');
    }
  }

  /* =========================
     Helpers UI / Util
  ========================= */
  function isMobile() {
    return window.matchMedia("(max-width: 575px)").matches;
  }

  function montarSpinner() {
    return `
      <div class="text-center p-4">
        <div class="spinner-border" style="color:${COLOR_TEMA};" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2 mb-0 text-muted fw-bold">Cargando sondeo...</p>
      </div>
    `;
  }

  function montarVacio() {
    return `
      <div class="p-4 text-center">
        <div class="fw-bold" style="color:#0f172a;">Sin datos disponibles</div>
        <div class="text-muted fw-bold" style="font-size:.92rem;">Intenta con otro departamento.</div>
      </div>
    `;
  }

  function obtenerColorPorIdOIndice(id, index) {
    return ColoresCandidatos[id] || PALETA_COLORES[index % PALETA_COLORES.length] || COLOR_TEMA;
  }

  function dividirEnTresLineas(nombre) {
    const palabras = (nombre || "").split(" ").filter(Boolean);
    if (palabras.length <= 1) return [palabras[0] || ""];
    if (palabras.length === 2) return [palabras[0], palabras[1]];

    const linea1 = palabras[0];
    const linea2 = palabras[1] + (palabras[2] ? " " + palabras[2] : "");
    const linea3 = palabras.slice(3).join(" ");

    const lineas = [linea1, linea2];
    if (linea3.trim() !== "") lineas.push(linea3);
    return lineas;
  }

  // ✅ Calcula padding izquierdo sin matar el chart (limita por % del canvas)
  function calcularPaddingIzquierdo(ctx, labelsMultiLinea, canvasEl, font = "12px system-ui") {
    ctx.save();
    ctx.font = font;

    let maxW = 0;
    (labelsMultiLinea || []).forEach(lines => {
      (lines || []).forEach(line => {
        const w = ctx.measureText(String(line || "")).width;
        if (w > maxW) maxW = w;
      });
    });

    ctx.restore();

    // foto 30px + gap + texto + margen
    let pad = Math.ceil(maxW + 30 + 14 + 26);

    // ✅ NUNCA permitir que coma el ancho del chart
    const canvasW = (canvasEl?.clientWidth || canvasEl?.width || 700);
    const maxPad = Math.floor(canvasW * 0.45); // 45% desktop
    pad = Math.min(pad, maxPad);

    // mínimos sanos
    pad = Math.max(140, pad);

    // mobile: permite un poco más pero con control
    if (window.matchMedia("(max-width: 575px)").matches) {
      const maxPadMobile = Math.floor(canvasW * 0.52);
      pad = Math.min(pad, maxPadMobile);
      pad = Math.max(125, pad);
    }
    return pad;
  }

  /* =========================
     MODO CUESTIONARIO: Preguntas
  ========================= */
  function cargarPreguntasCuestionario() {
    if (modoActual !== "cuestionario") return;

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "encuesta_preguntas_activas" },
      success: function (res) {
        if (!res || !res.success) {
          $("#selectorPregunta").html('<option value="">Sin preguntas disponibles</option>');
          $("#fichaTecnicaNombre").text("No hay cuestionario activo");
          return;
        }

        if (res.ficha && res.ficha.nombre) {
          $("#fichaTecnicaNombre").text(res.ficha.nombre);
        }

        if (res.preguntas && res.preguntas.length > 0) {
          // Store map id → meta for context panel
          window._preguntasMap = {};
          res.preguntas.forEach(p => { window._preguntasMap[p.id] = p; });

          // Group by capítulo using <optgroup>
          const grupos = {};
          res.preguntas.forEach(p => {
            const cap = p.capitulo || "Sin capítulo";
            if (!grupos[cap]) grupos[cap] = [];
            grupos[cap].push(p);
          });

          let options = "";
          let firstId = null;
          Object.keys(grupos).forEach(cap => {
            options += `<optgroup label="${cap}">`;
            grupos[cap].forEach((p, idx) => {
              const selected = (!firstId) ? "selected" : "";
              if (!firstId) firstId = p.id;
              options += `<option value="${p.id}" ${selected}>${p.texto_pregunta}</option>`;
            });
            options += `</optgroup>`;
          });
          $("#selectorPregunta").html(options);

          preguntaSeleccionada = parseInt(firstId) || 0;
          actualizarInfoPreguntaCtx(preguntaSeleccionada);

          cargarGraficoGeneral(preguntaSeleccionada);
          cargarDetalleTerritorialTodos(preguntaSeleccionada);
          actualizarColoresMapaCuestionario(preguntaSeleccionada);
        } else {
          $("#selectorPregunta").html('<option value="">Sin preguntas disponibles</option>');
          $("#infoPreguntaCtx").hide();
        }
      },
      error: function () {
        $("#selectorPregunta").html('<option value="">Error al cargar</option>');
      }
    });
  }

  function actualizarInfoPreguntaCtx(id) {
    const p = window._preguntasMap && window._preguntasMap[id];
    if (!p) { $("#infoPreguntaCtx").hide(); return; }

    const setField = function(sel, val) {
      if (val) { $(sel).text(val).show(); } else { $(sel).hide(); }
    };

    setField("#infoPreguntaCapitulo", p.capitulo || null);
    setField("#infoPreguntaNumeral", p.numeral ? ("Numeral: " + p.numeral) : null);
    setField("#infoPreguntaEnunciado", p.enunciado_pregunta || null);
    setField("#infoPreguntaTextoAdicional", p.texto_adicional ? ("Texto adicional: " + p.texto_adicional) : null);

    const anyVisible = p.capitulo || p.numeral || p.enunciado_pregunta || p.texto_adicional;
    anyVisible ? $("#infoPreguntaCtx").show() : $("#infoPreguntaCtx").hide();
  }

  $(document).on("change", "#selectorPregunta", function () {
    preguntaSeleccionada = parseInt($(this).val()) || 0;
    actualizarInfoPreguntaCtx(preguntaSeleccionada);
    if (nivelMapa === "departamento" && MapaSondeo.departamentoActual) {
      MapaSondeo.entrarDepartamento(MapaSondeo.departamentoActual, MapaSondeo.nombreTerritorioActual);
    } else {
      cargarGraficoGeneral(preguntaSeleccionada);
      cargarDetalleTerritorialTodos(preguntaSeleccionada);
      actualizarColoresMapaCuestionario(preguntaSeleccionada);
    }
  });

  /* =========================
     MAPA: colores cuestionario
  ========================= */
  function actualizarColoresMapaCuestionario(preguntaId) {
    if (!preguntaId) return;

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "encuesta_colores_mapa",
        pregunta_id: preguntaId
      },
      success: function (res) {
        if (!res || !res.success) return;

        const colores = res.colores || {};
        const ganadores = res.ganadores || {};

        ColoresCandidatos = colores;

        $("#mapaContainer svg path.mapaClick").each(function () {
          const codigoRaw = $(this).data("codigo");
          if (!codigoRaw && codigoRaw !== 0) return;
          const codigo = String(parseInt(codigoRaw, 10)).padStart(2, "0");
          const codigoAlt = String(parseInt(codigoRaw, 10));

          const infoGanador = ganadores[codigo] || ganadores[codigoAlt] || ganadores[codigoRaw];
          if (!infoGanador) {
            $(this).attr("fill", "#d9d9d9");
          } else if (infoGanador.empate === true) {
            $(this).attr("fill", "url(#rayasAzules)");
          } else {
            const color = colores[infoGanador.ganador] || colores[String(infoGanador.ganador)] || "#d9d9d9";
            $(this).attr("fill", color);
          }
        });
      }
    });
  }

  /* =========================
     MAPA: pintar ganadores (sondeo)
  ========================= */
  function pintarMapaSegunGanadores() {
    if (modoActual === "cuestionario") {
      // El mapa de cuestionario lo maneja actualizarColoresMapaCuestionario
      if (preguntaSeleccionada > 0) actualizarColoresMapaCuestionario(preguntaSeleccionada);
      return;
    }

    const departamentos = [];
    $("#mapaContainer svg g").each(function () {
      const codigo = $(this).find("path").data("codigo");
      if (codigo) departamentos.push(codigo);
    });

    if (departamentos.length === 0) return;

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "mapa_colores_departamentos",
        departamentos: departamentos
      },
      success: function (res) {
        if (!res || !res.success) return;

        const info = res.data || {};

        $("#mapaContainer svg g").each(function () {
          const path = $(this).find("path");
          const codigo = path.data("codigo");
          if (!codigo) return;

          const ganador = info[codigo];
          if (!ganador) return;

          if (ganador.empate) {
            path.attr("fill", "url(#rayasAzules)");
          } else {
            const color = ColoresCandidatos[ganador.ganador] || "#d9d9d9";
            path.attr("fill", color);
          }
        });
      }
    });
  }

  /* =========================
     GRAFICO GENERAL (barras verticales con etiquetas)
  ========================= */
  function shortLabel(nombre, maxLen) {
    const s = String(nombre || "").trim();
    if (s.length <= maxLen) return s;
    return s.slice(0, Math.max(0, maxLen - 1)) + "…";
  }

  const barValueLabelsPlugin = {
    id: "barValueLabels",
    afterDatasetsDraw(chart) {
      const { ctx } = chart;
      const meta = chart.getDatasetMeta(0);
      if (!meta || !meta.data) return;
      ctx.save();
      ctx.textAlign = "center";
      ctx.textBaseline = "bottom";
      ctx.fillStyle = "#0f172a";
      ctx.font = "700 11px system-ui, -apple-system, Segoe UI, Roboto, Arial";
      meta.data.forEach((bar, i) => {
        const val = chart.data.datasets[0].data[i];
        if (val == null) return;
        const { x, y } = bar.getProps(["x", "y"], true);
        ctx.fillText(String(val), x, y - 4);
      });
      ctx.restore();
    }
  };

  function cargarGraficoGeneral(preguntaId = 0) {
  const endpoint = (modoActual === "cuestionario") ? "encuesta_general_index" : "sondeo_general_index";

  const requestData = appendTerritorioIds({ op: endpoint });
  if (modoActual === "cuestionario" && preguntaId > 0) {
    requestData.pregunta_id = preguntaId;
  }

  $.ajax({
    url: "admin/ajax/rqst.php",
    type: "POST",
    dataType: "json",
    data: requestData,
    success: function (res) {
      if (!res || !res.success || !res.votos) return;

      const canvas = document.getElementById("graficoGeneral");
      if (!canvas) return;

      if (graficoGeneral) graficoGeneral.destroy();

      const labels = res.votos.map(v => shortLabel(v.nombre_completo || v.nombre || "Opción", 22));
      const fullNames = res.votos.map(v => v.nombre_completo || v.nombre || "Opción");
      const data = res.votos.map(v => Number(v.total || 0));

      const coloresAsignados = res.votos.map((v, i) => {
        const id = v.candidato_id || v.id;
        return ColoresCandidatos[id] || PALETA_COLORES[i % PALETA_COLORES.length];
      });

      const wrap = document.getElementById("chartWrapGeneral");
      if (wrap) wrap.style.height = Math.max(300, 120 + labels.length * 28) + "px";

      graficoGeneral = new Chart(canvas, {
        type: "bar",
        plugins: [barValueLabelsPlugin],
        data: {
          labels: labels,
          datasets: [{
            label: "Respuestas",
            data: data,
            backgroundColor: coloresAsignados,
            borderRadius: 8,
            borderSkipped: false,
            maxBarThickness: 48
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                title: function(items) {
                  const idx = items[0] && items[0].dataIndex;
                  return fullNames[idx] || "";
                },
                label: function(ctx) {
                  return " " + (ctx.parsed.y || 0) + " respuestas";
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0, font: { weight: "600" } },
              grid: { color: "rgba(2,6,23,.08)" },
              title: { display: true, text: "Cantidad", font: { weight: "700", size: 11 } }
            },
            x: {
              ticks: {
                font: { weight: "700", size: 11 },
                maxRotation: 45,
                minRotation: 0,
                autoSkip: false
              },
              grid: { display: false },
              title: { display: true, text: "Opción / candidato", font: { weight: "700", size: 11 } }
            }
          },
          layout: { padding: { top: 18, right: 8, left: 4, bottom: 4 } }
        }
      });
    },
    error: function (xhr, status, error) {
      console.error("Error en AJAX cargarGraficoGeneral:", status, error, xhr.responseText);
    }
  });
}

  /* =========================
     Detalle territorial: TODOS los departamentos (sin clic)
  ========================= */
  function cargarDetalleTerritorialTodos(preguntaId = 0) {
    const enDepto = nivelMapa === 'departamento' && MapaSondeo.departamentoActual;
    const endpoint = enDepto
      ? ((modoActual === "cuestionario") ? "encuesta_totales_municipios" : "sondeo_totales_municipios")
      : ((modoActual === "cuestionario") ? "encuesta_totales_departamentos" : "sondeo_totales_departamentos");

    const requestData = appendTerritorioIds({ op: endpoint });
    if (modoActual === "cuestionario" && preguntaId > 0) {
      requestData.pregunta_id = preguntaId;
    }

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: requestData,
      success: function (res) {
        const ctx = document.getElementById("graficoVotos");
        if (!ctx) return;

        const rows = enDepto
          ? ((res && res.success && Array.isArray(res.municipios)) ? res.municipios : [])
          : ((res && res.success && Array.isArray(res.departamentos)) ? res.departamentos : []);

        const tituloNivel = enDepto
          ? ('Municipios de ' + (MapaSondeo.nombreTerritorioActual || 'departamento'))
          : 'Todos los departamentos';
        $("#tituloDetalleTerritorio").text(tituloNivel);
        $("#badgeElectoral").text(enDepto ? (MapaSondeo.nombreTerritorioActual || 'DEPTO').toUpperCase() : 'NACIONAL');

        if (!rows.length) {
          $("#detalleTerritorioEmpty")
            .html(enDepto
              ? "Sin respuestas municipales registradas en este departamento."
              : "Sin respuestas territoriales registradas todavía.")
            .show();
          $("#chartWrapTerritorio").hide();
          if (grafico) { grafico.destroy(); grafico = null; }
          return;
        }

        $("#detalleTerritorioEmpty").hide();
        $("#chartWrapTerritorio").show();

        if (grafico) grafico.destroy();

        const fullNames = rows.map(d => d.nombre || ("Cód. " + d.codigo));
        const labels = fullNames.map(n => shortLabel(n, 16));
        const data = rows.map(d => Number(d.total || 0));
        const bg = rows.map((d, i) => d.color || PALETA_COLORES[i % PALETA_COLORES.length]);
        const lideres = rows.map(d => d.ganador_nombre || (d.empate ? "Empate" : "—"));
        const chartTitle = enDepto ? "Respuestas por municipio" : "Respuestas por departamento";

        const wrap = document.getElementById("chartWrapTerritorio");
        if (wrap) wrap.style.height = Math.max(320, 80 + labels.length * 22) + "px";

        grafico = new Chart(ctx, {
          type: "bar",
          plugins: [{
            id: "barValueLabelsH",
            afterDatasetsDraw(chart) {
              const { ctx: c } = chart;
              const meta = chart.getDatasetMeta(0);
              if (!meta || !meta.data) return;
              c.save();
              c.textAlign = "left";
              c.textBaseline = "middle";
              c.fillStyle = "#0f172a";
              c.font = "700 11px system-ui, -apple-system, Segoe UI, Roboto, Arial";
              meta.data.forEach((bar, i) => {
                const val = chart.data.datasets[0].data[i];
                if (val == null) return;
                const { x, y } = bar.getProps(["x", "y"], true);
                c.fillText(String(val), x + 6, y);
              });
              c.restore();
            }
          }],
          data: {
            labels: labels,
            datasets: [{
              label: chartTitle,
              data: data,
              backgroundColor: bg,
              borderRadius: 6,
              borderSkipped: false,
              maxBarThickness: 18
            }]
          },
          options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                text: chartTitle,
                font: { weight: "800", size: 13 },
                color: "#0f172a",
                padding: { bottom: 8 }
              },
              tooltip: {
                callbacks: {
                  title: (items) => {
                    const i = items[0]?.dataIndex ?? 0;
                    return fullNames[i] || "";
                  },
                  label: (item) => {
                    const i = item.dataIndex;
                    return ["Respuestas: " + (data[i] || 0), "Líder: " + (lideres[i] || "—")];
                  }
                }
              }
            },
            scales: {
              x: {
                beginAtZero: true,
                ticks: { precision: 0, font: { weight: "600" } },
                grid: { color: "rgba(2,6,23,.08)" },
                title: { display: true, text: "Cantidad", font: { weight: "700", size: 11 } }
              },
              y: {
                ticks: { font: { weight: "700", size: 10 }, autoSkip: false },
                grid: { display: false }
              }
            },
            layout: { padding: { top: 8, right: 36, left: 4, bottom: 4 } }
          }
        });
      },
      error: function () {
        $("#detalleTerritorioEmpty")
          .html("No se pudo cargar el detalle territorial.")
          .show();
        $("#chartWrapTerritorio").hide();
      }
    });
  }

  /* =========================
     Card + Mapa + Grafico depto
  ========================= */
  const MapaSondeo = {
    departamentoActual: "",
    municipioActual: "",
    nombreTerritorioActual: "",

    init() {
      this.eventos();
      if (!mapaColombiaHtmlBackup) {
        mapaColombiaHtmlBackup = $("#mapaContainer").html();
      }
      $(document).on("click", "#btnVolverColombia, #linkVolverColombiaBc", (e) => {
        e.preventDefault();
        this.volverAPais();
      });
    },

    hacerMapaClickeable() {
      $("#mapaContainer svg path").each(function () {
        $(this).addClass("mapaClick").css("cursor", "pointer");
      });
    },

    eventos() {
      $("#closeCard").on("click", function (e) {
        e.stopPropagation();
        $("#resultadosCard").addClass("d-none").removeClass("bottom-sheet").hide();
      });

      $(document).on("click", function (e) {
        if (!$(e.target).closest("#resultadosCard").length && !$(e.target).closest(".mapaClick").length) {
          $("#resultadosCard").addClass("d-none").removeClass("bottom-sheet").hide();
        }
      });

      $("#resultadosCard").on("click", function (e) {
        e.stopPropagation();
      });

      $(document).on("click", ".mapaClick", (e) => {
        this.manejarClickMapa(e);
      });

      $(window).on("resize", () => {
        const card = $("#resultadosCard");
        if (!card.hasClass("d-none") && card.is(":visible")) {
          if (isMobile()) this.posicionarBottomSheet();
        }
      });
    },

    setTituloTerritorio(nombre) {
      const label = (nombre || "").toString().trim() || "Territorio";
      this.nombreTerritorioActual = label;
      $("#tituloDetalleTerritorio").text(label);
      $("#badgeElectoral").text(label.toUpperCase());
    },

    manejarClickMapa(e) {
      e.preventDefault();
      e.stopPropagation();

      const path = $(e.target).closest("path");
      if (!path.length) return;

      const nombreReal = path.data("nombre");
      const codigoDane = path.data("codigo");

      if (nivelMapa === "pais") {
        const dep = normalizeDep(codigoDane);
        if (!dep) return;

        this.departamentoActual = dep;
        this.municipioActual = "";
        this.setTituloTerritorio(nombreReal || ("Código " + dep));
        $("#mapaMunicipalMsg").addClass("d-none").text("");

        // Si hay mapa municipal → drill-down; si no (p.ej. Bogotá) → charts + aviso
        if (isMapaMunicipalHabilitado(dep) || MAPA_MUNI_HABILITADOS.length === 0) {
          // length===0: lista aún no llegó; intentar igual vía backend
          this.entrarDepartamento(dep, nombreReal || ("Depto " + dep));
          return;
        }

        $("#mapaMunicipalMsg")
          .removeClass("d-none")
          .text("Este departamento no tiene mapa municipal disponible por ahora.");

        cargarGraficoGeneral(preguntaSeleccionada);

        if (isMobile()) this.posicionarBottomSheet();
        else this.posicionarCard(e.pageX, e.pageY);

        this.obtenerSondeo(dep, true);
        return;
      }

      // Nivel departamento: clic municipio → card
      this.municipioActual = String(codigoDane || "");
      this.setTituloTerritorio(nombreReal || ("Municipio " + codigoDane));

      if (isMobile()) this.posicionarBottomSheet();
      else this.posicionarCard(e.pageX, e.pageY);

      this.obtenerSondeoMunicipio(this.municipioActual);
    },

    entrarDepartamento(codigo, nombre) {
      const dep = normalizeDep(codigo);
      this.departamentoActual = dep;
      this.municipioActual = "";
      this.nombreTerritorioActual = (nombre || "").toString().trim() || ("Depto " + dep);
      nivelMapa = "departamento";
      actualizarUiNivel();

      const dataRqst = appendTerritorioIds({
        op: "mapa_municipios_svg",
        departamento_click: dep,
        modo: modoActual
      });
      if (modoActual === "cuestionario" && preguntaSeleccionada > 0) {
        dataRqst.pregunta_id = preguntaSeleccionada;
      }

      // No destruir el mapa nacional hasta tener SVG (evita “cargando” eterno)
      const prevHtml = $("#mapaContainer").html();
      $("#mapaContainer").css({ opacity: 0.55, pointerEvents: "none" });

      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: dataRqst,
        success: (res) => {
          $("#mapaContainer").css({ opacity: 1, pointerEvents: "" });
          if (!res || !res.success || !res.svg) {
            $("#mapaContainer").html(prevHtml);
            this.hacerMapaClickeable();
            nivelMapa = "pais";
            actualizarUiNivel();
            $("#mapaMunicipalMsg")
              .removeClass("d-none")
              .text((res && res.message) ? res.message : "No se pudo cargar el mapa municipal.");
            return;
          }
          $("#mapaContainer").html(res.svg);
          this.hacerMapaClickeable();
          cargarGraficoGeneral(preguntaSeleccionada);
          cargarDetalleTerritorialTodos(preguntaSeleccionada);
        },
        error: () => {
          $("#mapaContainer").css({ opacity: 1, pointerEvents: "" });
          $("#mapaContainer").html(prevHtml);
          this.hacerMapaClickeable();
          nivelMapa = "pais";
          actualizarUiNivel();
          $("#mapaMunicipalMsg")
            .removeClass("d-none")
            .text("Error de red al cargar el mapa municipal.");
        }
      });
    },

    volverAPais() {
      nivelMapa = "pais";
      this.departamentoActual = "";
      this.municipioActual = "";
      this.nombreTerritorioActual = "";
      actualizarUiNivel();
      $("#resultadosCard").addClass("d-none").hide();

      if (mapaColombiaHtmlBackup) {
        $("#mapaContainer").html(mapaColombiaHtmlBackup);
      }
      this.hacerMapaClickeable();
      setTimeout(() => {
        if (modoActual === "cuestionario") {
          actualizarColoresMapaCuestionario(preguntaSeleccionada);
        } else {
          pintarMapaSegunGanadores();
        }
      }, 50);
      cargarGraficoGeneral(preguntaSeleccionada);
      cargarDetalleTerritorialTodos(preguntaSeleccionada);
    },

    posicionarBottomSheet() {
      const card = $("#resultadosCard");
      card
        .removeClass("d-none")
        .addClass("bottom-sheet")
        .show()
        .css({ top: "auto", left: "12px", right: "12px", bottom: "12px", display: "block" });

      card[0].style.transform = "translateY(10px)";
      card[0].style.opacity = "0";
      requestAnimationFrame(() => {
        card[0].style.transition = "all .18s ease";
        card[0].style.transform = "translateY(0)";
        card[0].style.opacity = "1";
      });
    },

    posicionarCard(x, y) {
      const cardWidth = 360;
      const cardHeight = 520;
      const ww = $(window).width();
      const wh = $(window).height();

      let finalX = x + 15;
      let finalY = y - 15;

      if (finalX + cardWidth > ww) finalX = x - cardWidth - 15;
      if (finalY + cardHeight > wh) finalY = wh - cardHeight - 15;
      if (finalY < 0) finalY = 15;
      if (finalX < 0) finalX = 15;

      $("#resultadosCard")
        .removeClass("d-none")
        .removeClass("bottom-sheet")
        .show()
        .css({ top: finalY + "px", left: finalX + "px", right: "auto", bottom: "auto", display: "block" });

      const card = $("#resultadosCard")[0];
      card.style.transform = "scale(.98)";
      card.style.opacity = "0";
      requestAnimationFrame(() => {
        card.style.transition = "all .18s ease";
        card.style.transform = "scale(1)";
        card.style.opacity = "1";
      });
    },

    obtenerSondeoMunicipio(municipio) {
      $("#resultadosContent").html(montarSpinner());
      $("#resultadosCard").show().removeClass("d-none");

      const endpoint = (modoActual === "cuestionario") ? "encuesta_mapa_index" : "sondeo_presidencial_mapa";
      const dataRqst = appendTerritorioIds({
        op: endpoint,
        municipio_click: municipio,
        departamento_click: normalizeDep(this.departamentoActual)
      });
      if (modoActual === "cuestionario" && preguntaSeleccionada > 0) {
        dataRqst.pregunta_id = preguntaSeleccionada;
      }

      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: dataRqst,
        success: (res) => {
          if (!res || !res.success || !res.votos || res.votos.length === 0) {
            this.mostrarSondeoVacio();
            return;
          }
          this.mostrarSondeo(res.votos);
        },
        error: () => {
          this.mostrarSondeoVacio();
        }
      });
    },

    obtenerSondeo(departamento, actualizarChartDerecho) {
      $("#resultadosContent").html(montarSpinner());
      $("#resultadosCard").show().removeClass("d-none");

      const endpoint = (modoActual === "cuestionario") ? "encuesta_mapa_index" : "sondeo_presidencial_mapa";

      const dataRqst = appendTerritorioIds({ op: endpoint, departamento_click: departamento });
      if (modoActual === "cuestionario" && preguntaSeleccionada > 0) {
        dataRqst.pregunta_id = preguntaSeleccionada;
      }

      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: dataRqst,
        success: (res) => {
          if (!res || !res.success || !res.votos || res.votos.length === 0) {
            this.mostrarSondeoVacio();
            return;
          }

          this.mostrarSondeo(res.votos);
          // En mapa nacional: el chart derecho muestra opciones del depto clicado
          if (actualizarChartDerecho && nivelMapa === "pais") {
            this.actualizarGrafico(res.votos);
          }
        },
        error: () => {
          this.mostrarSondeoVacio();
        }
      });
    },

    mostrarSondeo(votos) {
      let total = votos.reduce((t, v) => t + Number(v.total || 0), 0);
      votos.sort((a, b) => Number(b.total || 0) - Number(a.total || 0));

      let html = "";
      votos.forEach((v, idx) => {
        const votosNum = Number(v.total || 0);
        const porcentaje = total > 0 ? ((votosNum / total) * 100).toFixed(1) : 0;

        const id = Number(v.id_candidato || v.candidato_id || v.tbl_candidato_id || 0);
        const color = obtenerColorPorIdOIndice(id, idx);

        const tieneImagen =
          v.foto_url && v.foto_url.trim() !== "" &&
          !v.foto_url.includes("option_default") &&
          !v.foto_url.includes("default.png");

        const imagenHtml = tieneImagen
          ? `<img src="${v.foto_url}" style="width:38px;height:38px;object-fit:cover;border-radius:999px;border:2px solid rgba(32,66,127,.18);">`
          : `<div style="width:38px;height:38px;border-radius:999px;background:${color};display:flex;align-items:center;justify-content:center;border:2px solid rgba(32,66,127,.18);">
               <span style="color:#fff;font-weight:700;font-size:0.9rem;">${(v.nombre_completo || "?").charAt(0).toUpperCase()}</span>
             </div>`;

        html += `
          <div class="p-2 border-bottom d-flex gap-2 align-items-center" style="background:${idx === 0 ? "rgba(32,66,127,.04)" : "transparent"};">
            ${imagenHtml}
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <strong style="color:#0f172a; font-size: 0.9rem;">${v.nombre_completo}</strong>
                ${idx === 0 && votosNum > 0 ? `<span class="badge" style="background:${color}; color:#fff; font-weight:900; border-radius:999px; font-size: 0.7rem;">Líder</span>` : ``}
              </div>
              <div class="d-flex justify-content-between text-muted fw-bold" style="font-size:.8rem;">
                <span>${votosNum} votos</span>
                <span>${porcentaje}%</span>
              </div>
              <div class="progress mt-1" style="height:5px;border-radius:999px;background:rgba(2,6,23,.06);">
                <div class="progress-bar" style="width:${porcentaje}%; background:${color}; border-radius:999px;"></div>
              </div>
            </div>
          </div>
        `;
      });

      $("#resultadosContent").html(html);

      $("#badgeElectoral").css({
        background: "rgba(32,66,127,.06)",
        borderColor: "rgba(32,66,127,.18)"
      });
      $("#resultadosCard").show().removeClass("d-none");
    },

    actualizarGrafico(votos) {
      const ctx = document.getElementById("graficoVotos");
      if (!ctx) return;

      if (!votos || !votos.length) {
        this.mostrarSondeoVacio();
        return;
      }

      if (grafico) grafico.destroy();

      const territorio = this.nombreTerritorioActual || "Territorio seleccionado";
      $("#tituloDetalleTerritorio").text(territorio);
      $("#detalleTerritorioEmpty").hide();
      $("#chartWrapTerritorio").show();

      const fullNames = (votos || []).map(v => v.nombre_completo || v.nombre || "Opción");
      const labels = fullNames.map(n => shortLabel(n, 20));
      const data = (votos || []).map(v => Number(v.total || 0));

      const bg = (votos || []).map((v, idx) => {
        const id = Number(v.id_candidato || v.candidato_id || v.tbl_candidato_id || 0);
        return obtenerColorPorIdOIndice(id, idx);
      });

      const wrap = document.getElementById("chartWrapTerritorio");
      if (wrap) wrap.style.height = Math.max(300, 120 + labels.length * 28) + "px";

      grafico = new Chart(ctx, {
        type: "bar",
        plugins: [barValueLabelsPlugin],
        data: {
          labels: labels,
          datasets: [{
            label: "Respuestas en " + territorio,
            data: data,
            backgroundColor: bg,
            borderRadius: 8,
            borderSkipped: false,
            maxBarThickness: 48
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            title: {
              display: true,
              text: "Resultados — " + territorio,
              font: { weight: "800", size: 13 },
              color: "#0f172a",
              padding: { bottom: 10 }
            },
            tooltip: {
              callbacks: {
                title: function(items) {
                  const idx = items[0] && items[0].dataIndex;
                  return fullNames[idx] || "";
                },
                label: function(c) {
                  return " " + (c.parsed.y || 0) + " respuestas (" + territorio + ")";
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0, font: { weight: "600" } },
              grid: { color: "rgba(2,6,23,.08)" },
              title: { display: true, text: "Cantidad", font: { weight: "700", size: 11 } }
            },
            x: {
              ticks: {
                font: { weight: "700", size: 11 },
                maxRotation: 45,
                minRotation: 0,
                autoSkip: false
              },
              grid: { display: false },
              title: { display: true, text: "Opción / candidato", font: { weight: "700", size: 11 } }
            }
          },
          layout: { padding: { top: 18, right: 8, left: 4, bottom: 4 } }
        }
      });
    },

    mostrarSondeoVacio() {
      $("#resultadosContent").html(montarVacio());
      $("#detalleTerritorioEmpty")
        .html("No hay resultados para <strong>" + (this.nombreTerritorioActual || "este territorio") + "</strong>.")
        .show();
      $("#chartWrapTerritorio").hide();
      if (grafico) { grafico.destroy(); grafico = null; }
    }
  };

  /* =========================
     INIT
  ========================= */
  function aplicarModo(modo) {
    modoActual = modo;
    preguntaSeleccionada = 0;

    // Resetear gráficos
    if (graficoGeneral) { graficoGeneral.destroy(); graficoGeneral = null; }
    if (grafico) { grafico.destroy(); grafico = null; }

    // Ocultar card detalle al cambiar
    $("#resultadosCard").addClass("d-none");

    // Actualizar botones estilo
    if (modo === "sondeo") {
      $("#btnModoSondeo")
        .css({ background: "linear-gradient(135deg,#20427F,#0b1a89)", color: "#fff", border: "0" });
      $("#btnModoCuestionario")
        .css({ background: "rgba(32,66,127,.10)", color: "#20427F", border: "1px solid rgba(32,66,127,.20)" });
      $("#panelSelectorPregunta").hide();
    } else {
      $("#btnModoCuestionario")
        .css({ background: "linear-gradient(135deg,#20427F,#0b1a89)", color: "#fff", border: "0" });
      $("#btnModoSondeo")
        .css({ background: "rgba(32,66,127,.10)", color: "#20427F", border: "1px solid rgba(32,66,127,.20)" });
      $("#panelSelectorPregunta").show();
    }

    // Recargar datos
    if (nivelMapa === "departamento") {
      MapaSondeo.volverAPais();
    }
    if (modo === "cuestionario") {
      cargarPreguntasCuestionario();
    } else {
      cargarGraficoGeneral();
      cargarDetalleTerritorialTodos();
      setTimeout(() => pintarMapaSegunGanadores(), 100);
    }
  }

  // Exponer globalmente para que listeners nativos en resultado.php puedan llamarla
  window._aplicarModo = aplicarModo;

  // Listeners botones de modo — delegados en document para garantizar que funcionen
  $(document).on("click", "#btnModoSondeo", function () { aplicarModo("sondeo"); });
  $(document).on("click", "#btnModoCuestionario", function () { aplicarModo("cuestionario"); });

  // Arranque inicial
  const opcionActiva = window.OPCION_ACTIVA_WEB || "sondeo";

  if (opcionActiva === "cuestionario") {
    modoActual = "cuestionario";
    cargarPreguntasCuestionario();
  } else if (opcionActiva === "ambos") {
    // Empieza en modo sondeo por defecto
    modoActual = "sondeo";
    cargarGraficoGeneral();
    cargarDetalleTerritorialTodos();
  } else {
    cargarGraficoGeneral();
    cargarDetalleTerritorialTodos();
  }

  setTimeout(() => {
    pintarMapaSegunGanadores();
    MapaSondeo.hacerMapaClickeable();
  }, 250);

  MapaSondeo.init();
  window.MapaSondeo = MapaSondeo;

  /* =========================
     CSS extra bottom-sheet
  ========================= */
  if (!document.getElementById("bottomSheetStyle")) {
    const style = document.createElement("style");
    style.id = "bottomSheetStyle";
    style.innerHTML = `
      #resultadosCard.bottom-sheet{
        position: fixed !important;
        width: auto !important;
        max-height: 72vh;
        overflow: hidden;
      }
      #resultadosCard.bottom-sheet .card-body{
        max-height: calc(72vh - 130px);
        overflow: auto;
      }
    `;
    document.head.appendChild(style);
  }
});
