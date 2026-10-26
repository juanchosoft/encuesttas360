$(initOpciones);

var q;
var optionCounter = 0;

function initOpciones() {
  q = {};
  OPCIONES.setupOptionListeners();
  OPCIONES.updateNoOptionsMessage();
}

var OPCIONES = {
  setupOptionListeners: function () {
    $("#addOptionBtn").off("click.opciones").on("click.opciones", OPCIONES.addOptionBtnClick);
    $(document).off("click.opciones", ".removeOptionBtn").on("click.opciones", ".removeOptionBtn", OPCIONES.removeOptionBtnClick);
  },

  addOptionBtnClick: function () {
    OPCIONES.addOptionField();
    OPCIONES.updateNoOptionsMessage();
  },

  removeOptionBtnClick: function (event) {
    var $row = $(event).closest(".qst-opt-row, .input-group");
    if ($row.length > 0) {
      $row.remove();
    }
    OPCIONES.updateNoOptionsMessage();
  },

  updateNoOptionsMessage: function () {
    var numOptions = $("#opcionesContainer .qst-opt-row, #opcionesContainer .input-group").length;
    if (numOptions === 0) {
      $("#noOptionsMessage").show();
    } else {
      $("#noOptionsMessage").hide();
    }
  },

  handleTipoPreguntaChange: function (input, dataBD) {
    if (typeof dataBD === "undefined") dataBD = null;
    var opcion = $("#" + (input || "tipo_pregunta")).val();

    $("#divLimiteRespuesta").toggle(opcion === "Seleccion_Multiple_multiple_respuesta");
    $("#qstPanelGrillaConfig").toggle(opcion === "Grilla");
    $("#qstPanelListaGrillaConfig").toggle(opcion === "Lista_desde_grilla");

    var esSinOpcionesManuales = (opcion === "Grilla" || opcion === "Lista_desde_grilla");
    $("#qstSeccionOpcionesManuales").toggle(!esSinOpcionesManuales);

    if (esSinOpcionesManuales) {
      if (opcion === "Lista_desde_grilla" && typeof PREGUNTAS !== "undefined" && PREGUNTAS.onChangeListaOrigenPregunta) {
        PREGUNTAS.onChangeListaOrigenPregunta();
      }
      return;
    }

    $("#opcionesContainer").empty();

    var defaultOptions = [];
    switch (opcion) {
      case "Seleccion_Multiple_multiple_respuesta":
        defaultOptions = [
          { texto: "Opción 1" },
          { texto: "Opción 2" },
          { texto: "Opción 3" }
        ];
        break;
      case "Preguntas_Ordinales":
        defaultOptions = [
          { texto: "1", valor_numerico: 1 },
          { texto: "2", valor_numerico: 2 },
          { texto: "3", valor_numerico: 3 },
          { texto: "4", valor_numerico: 4 },
          { texto: "5", valor_numerico: 5 }
        ];
        break;
      case "Seleccion_Multiple_unica_respuesta":
        defaultOptions = [
          { texto: "Favorable", cuenta_como: "favorable" },
          { texto: "Desfavorable", cuenta_como: "desfavorable" },
          { texto: "No sabe / No responde", cuenta_como: "ninguno" }
        ];
        break;
      case "Dicotomica":
        defaultOptions = [
          { texto: "Sí", cuenta_como: "si" },
          { texto: "No", cuenta_como: "no" }
        ];
        break;
      case "Preguntas_Cardinales":
        defaultOptions = [
          { texto: "Muy satisfecho", valor_numerico: 5 },
          { texto: "Satisfecho", valor_numerico: 4 },
          { texto: "Neutral", valor_numerico: 3 },
          { texto: "Insatisfecho", valor_numerico: 2 },
          { texto: "Muy insatisfecho", valor_numerico: 1 }
        ];
        break;
      default:
        break;
    }

    if (dataBD != null) {
      $.each(dataBD, function (index, item) {
        OPCIONES.addOptionField(item.texto || item.opcion || "", item.id || null, item);
      });
    } else {
      $.each(defaultOptions, function (index, item) {
        OPCIONES.addOptionField(item.texto, null, item);
      });
    }

    OPCIONES.updateNoOptionsMessage();
  },

  aplicarPlantillaOpciones: function (tipoPlantilla) {
    $("#opcionesContainer").empty();
    var lista = [];
    if (tipoPlantilla === "sino") {
      lista = [
        { texto: "Sí", cuenta_como: "si" },
        { texto: "No", cuenta_como: "no" }
      ];
    } else if (tipoPlantilla === "sino_filtro") {
      $("#es_filtro").prop("checked", true);
      lista = [
        { texto: "Sí", cuenta_como: "si", accion: "continuar" },
        { texto: "No", cuenta_como: "no", accion: "terminar", mensaje_terminar: "Muchas gracias por su tiempo. Hemos terminado la encuesta." }
      ];
    } else if (tipoPlantilla === "imagen") {
      lista = [
        { texto: "Favorable", cuenta_como: "favorable" },
        { texto: "Desfavorable", cuenta_como: "desfavorable" },
        { texto: "No tiene imagen / No sabe", cuenta_como: "ninguno" }
      ];
    } else if (tipoPlantilla === "escala15") {
      lista = [
        { texto: "1 - Poco importante", valor_numerico: 1 },
        { texto: "2", valor_numerico: 2 },
        { texto: "3", valor_numerico: 3 },
        { texto: "4", valor_numerico: 4 },
        { texto: "5 - Muy importante", valor_numerico: 5 }
      ];
    }
    $.each(lista, function (idx, item) {
      OPCIONES.addOptionField(item.texto, null, item);
    });
    OPCIONES.updateNoOptionsMessage();
  },

  pegarOpcionesPorRenglon: function () {
    var raw = ($("#qstPegarOpcionesTextarea").val() || "").trim();
    if (!raw) return;
    var lineas = raw.split(/\r?\n/).map(function (l) { return l.trim(); }).filter(function (l) { return l !== ""; });
    if (!lineas.length) return;
    if ($("#qstReemplazarOpcionesChk").is(":checked")) {
      $("#opcionesContainer").empty();
    }
    $.each(lineas, function (i, txt) {
      OPCIONES.addOptionField(txt, null, { texto: txt });
    });
    $("#qstPegarOpcionesTextarea").val("");
    $("#qstPegarOpcionesBox").slideUp(150);
    OPCIONES.updateNoOptionsMessage();
  },

  buildJumpSelectOptionsHtml: function (selectedId) {
    var html = '<option value="">-- Elija la pregunta a la que pasa --</option>';
    var currentQuestionId = parseInt($("#id").val(), 10) || 0;
    var currentOrden = parseInt($("#orden").val(), 10) || 0;
    var catalogo = (window.CUESTIONARIO_PREGUNTAS_CATALOGO || []);
    for (var i = 0; i < catalogo.length; i++) {
      var p = catalogo[i];
      var pid = parseInt(p.id, 10);
      var pOrden = parseInt(p.orden, 10) || 0;
      if (currentQuestionId > 0 && pid === currentQuestionId) continue;
      if (currentOrden > 0 && pOrden > 0 && pOrden <= currentOrden && pid !== parseInt(selectedId, 10)) continue;
      var sel = (parseInt(selectedId, 10) === pid) ? " selected" : "";
      var label = (p.numeral ? (p.numeral + " · ") : ("Pregunta " + pOrden + " · ")) + (p.texto_pregunta || "").substring(0, 65);
      html += '<option value="' + pid + '"' + sel + '>' + OPCIONES.htmlspecialchars(label) + '</option>';
    }
    return html;
  },

  addOptionField: function (initialValue, optionId, meta) {
    if (typeof initialValue === "undefined") initialValue = "";
    if (typeof optionId === "undefined") optionId = null;
    meta = meta || {};

    optionCounter++;
    var accion = meta.accion || "continuar";
    var saltoDestino = meta.salto_destino_pregunta_id || "";
    var msgTerminar = meta.mensaje_terminar || "";
    var valorNum = (meta.valor_numerico !== null && typeof meta.valor_numerico !== "undefined" && meta.valor_numerico !== "") ? meta.valor_numerico : "";
    var cuentaComo = meta.cuenta_como || "ninguno";
    var esFija = meta.es_opcion_fija ? 1 : 0;

    var jumpSelectHtml = OPCIONES.buildJumpSelectOptionsHtml(saltoDestino);

    var rowHtml = '' +
      '<div class="qst-opt-row input-group-card mb-2 p-2 rounded-3" data-opt-id="' + (optionId ? parseInt(optionId, 10) : '') + '" data-es-fija="' + esFija + '" style="border:1px solid #DCE4F0;background:#fff;">' +
        '<div class="row g-2 align-items-center">' +
          '<div class="col-md-5">' +
            '<label class="form-label small text-muted mb-1 fw-bold">Respuesta que puede elegir</label>' +
            '<input type="text" class="form-control qst-opt-texto" placeholder="Ej: Sí, Favorable, 5..." value="' + OPCIONES.htmlspecialchars(String(initialValue)) + '" required style="min-height:48px;font-size:1rem;">' +
          '</div>' +
          '<div class="col-md-4">' +
            '<label class="form-label small text-muted mb-1 fw-bold">Después de esta respuesta</label>' +
            '<select class="form-select qst-opt-accion" onchange="OPCIONES.onAccionChange(this)" style="min-height:48px;font-size:1rem;">' +
              '<option value="continuar"' + (accion === "continuar" ? " selected" : "") + '>Continuar con la siguiente pregunta</option>' +
              '<option value="saltar"' + (accion === "saltar" ? " selected" : "") + '>Pasar directo a otra pregunta…</option>' +
              '<option value="terminar"' + (accion === "terminar" ? " selected" : "") + '>Terminar la encuesta aquí (Filtro)</option>' +
            '</select>' +
          '</div>' +
          '<div class="col-md-2">' +
            '<label class="form-label small text-muted mb-1 fw-bold" title="Opcional: para calcular promedio o nota de 1 a 5">Valor / Nota</label>' +
            '<input type="number" step="0.01" class="form-control qst-opt-valor" placeholder="Ej: 5" value="' + OPCIONES.htmlspecialchars(String(valorNum)) + '" style="min-height:48px;font-size:1rem;">' +
          '</div>' +
          '<div class="col-md-1 text-end pt-3">' +
            '<button onclick="OPCIONES.removeOptionBtnClick(this)" class="btn btn-outline-danger" type="button" title="Quitar opción" style="min-height:48px;min-width:48px;border-radius:10px;">' +
              '<i class="fas fa-trash-alt"></i>' +
            '</button>' +
          '</div>' +
        '</div>' +
        '<div class="row g-2 mt-1 align-items-center qst-opt-extra-row">' +
          '<div class="col-md-4">' +
            '<div class="d-flex align-items-center gap-2">' +
              '<span class="small text-muted fw-semibold" style="white-space:nowrap;">En resultados cuenta como:</span>' +
              '<select class="form-select qst-opt-cuenta" style="font-size:.95rem;">' +
                '<option value="ninguno"' + (cuentaComo === "ninguno" ? " selected" : "") + '>Normal</option>' +
                '<option value="si"' + (cuentaComo === "si" ? " selected" : "") + '>Sí / Positivo</option>' +
                '<option value="no"' + (cuentaComo === "no" ? " selected" : "") + '>No / Negativo</option>' +
                '<option value="favorable"' + (cuentaComo === "favorable" ? " selected" : "") + '>Imagen favorable</option>' +
                '<option value="desfavorable"' + (cuentaComo === "desfavorable" ? " selected" : "") + '>Imagen desfavorable</option>' +
              '</select>' +
            '</div>' +
          '</div>' +
          '<div class="col-md-8 qst-opt-salto-wrap" style="' + (accion === "saltar" ? "" : "display:none;") + '">' +
            '<div class="d-flex align-items-center gap-2">' +
              '<span class="badge bg-info text-dark"><i class="fas fa-share me-1"></i>Saltar a:</span>' +
              '<select class="form-select qst-opt-salto-destino" style="font-size:.95rem;">' +
                jumpSelectHtml +
              '</select>' +
            '</div>' +
          '</div>' +
          '<div class="col-md-8 qst-opt-terminar-wrap" style="' + (accion === "terminar" ? "" : "display:none;") + '">' +
            '<div class="d-flex align-items-center gap-2">' +
              '<span class="badge bg-danger"><i class="fas fa-hand-paper me-1"></i>FILTRO</span>' +
              '<input type="text" class="form-control qst-opt-msg-terminar" placeholder="Mensaje de despedida al encuestado (ej: Muchas gracias por su tiempo...)" value="' + OPCIONES.htmlspecialchars(String(msgTerminar)) + '" style="font-size:.95rem;">' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';

    $("#opcionesContainer").append(rowHtml);
    OPCIONES.updateNoOptionsMessage();
  },

  onAccionChange: function (selectEl) {
    var $row = $(selectEl).closest(".qst-opt-row");
    var val = $(selectEl).val();
    $row.find(".qst-opt-salto-wrap").toggle(val === "saltar");
    $row.find(".qst-opt-terminar-wrap").toggle(val === "terminar");
    if (val === "terminar") {
      $("#es_filtro").prop("checked", true);
    }
  },

  clearForm: function (formId) {
    UTIL.clearForm(formId);
    $("#opcionesContainer").empty();
    $("#noOptionsMessage").show();
    $("#tipo_pregunta").val("").trigger("change");
  },

  showOptionsModal: function (optionsJson, questionText) {
    try {
      var opciones = typeof optionsJson === "string" ? JSON.parse(optionsJson) : optionsJson;
      var modalBodyList = $("#modalOptionsList");
      var modalQuestionText = $("#modalQuestionText");

      modalBodyList.empty();
      modalQuestionText.empty().text(questionText);

      if (opciones && opciones.length > 0) {
        $.each(opciones, function (index, opcion) {
          var badgeHtml = "";
          if (opcion.accion === "terminar") {
            badgeHtml = '<span class="badge bg-danger ms-auto">Termina la encuesta</span>';
          } else if (opcion.accion === "saltar") {
            badgeHtml = '<span class="badge bg-info text-dark ms-auto">Salta a otra pregunta</span>';
          } else if (opcion.valor_numerico !== null && typeof opcion.valor_numerico !== "undefined") {
            badgeHtml = '<span class="badge bg-secondary ms-auto">Valor: ' + opcion.valor_numerico + '</span>';
          }
          var optionHtml = '' +
            '<div class="col-12">' +
              '<div class="rounded-3 py-2 px-3 bg-body-emphasis d-flex align-items-center mb-2 border">' +
                '<span class="fas fa-check text-primary me-3 fs-9"></span>' +
                '<p class="mb-0 text-body-secondary fw-semibold">' + OPCIONES.htmlspecialchars(opcion.texto || opcion.texto_opcion || "") + '</p>' +
                badgeHtml +
              '</div>' +
            '</div>';
          modalBodyList.append(optionHtml);
        });
      } else {
        modalBodyList.append('<p class="text-muted text-center">Esta pregunta toma sus filas de una tabla de candidatos o es abierta.</p>');
      }

      var optionsModal = new bootstrap.Modal(document.getElementById("optionsModal"));
      optionsModal.show();
    } catch (e) {
      UTIL.mostrarMensajeError("Error al cargar las opciones de la pregunta.");
    }
  },

  htmlspecialchars: function (str) {
    if (typeof str !== "string") {
      return str;
    }
    var map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;"
    };
    return str.replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  },

  getOpciones: function () {
    var opciones = [];
    $("#opcionesContainer .qst-opt-row").each(function () {
      var $r = $(this);
      var texto = ($r.find(".qst-opt-texto").val() || "").trim();
      if (texto === "") return;
      var optId = parseInt($r.attr("data-opt-id"), 10) || 0;
      var accion = $r.find(".qst-opt-accion").val() || "continuar";
      var saltoDest = parseInt($r.find(".qst-opt-salto-destino").val(), 10) || null;
      var msgTerm = ($r.find(".qst-opt-msg-terminar").val() || "").trim();
      var valNumRaw = ($r.find(".qst-opt-valor").val() || "").trim();
      var cuenta = $r.find(".qst-opt-cuenta").val() || "ninguno";
      var esFija = parseInt($r.attr("data-es-fija"), 10) || 0;

      opciones.push({
        id: optId > 0 ? optId : undefined,
        texto: texto,
        accion: accion,
        salto_destino_pregunta_id: accion === "saltar" ? saltoDest : null,
        mensaje_terminar: accion === "terminar" ? msgTerm : null,
        valor_numerico: valNumRaw !== "" ? parseFloat(valNumRaw) : null,
        cuenta_como: cuenta,
        es_opcion_fija: esFija
      });
    });
    return opciones;
  }
};
