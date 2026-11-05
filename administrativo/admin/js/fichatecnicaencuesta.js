$(init);

var q = {};
let espacioGeografico = {};
var return_page = "ficha_tecnica_encuesta.php";

var Z_TO_CONFIANZA = {
  "1.95": 95,
  "1.99": 99,
  "1.90": 90,
  "1.85": 85,
};

function getConfianzaPorcentaje(zVal) {
  const key = String(zVal);
  if (Z_TO_CONFIANZA[key] !== undefined) return Z_TO_CONFIANZA[key];
  const n = parseFloat(key);
  return isNaN(n) ? 0 : n;
}

function validarConfianzaMargen() {
  const conf = safeNum($("#nivel_confiabilidad_porcentaje").val());
  const margen = safeNum($("#margen_error_porcentaje").val());

  if (conf <= 0 || conf >= 100 || margen <= 0 || margen >= 100) {
    UTIL.mostrarMensajeValidacion("El nivel de confiabilidad y el margen de error deben ser mayores que 0 y menores que 100.");
    return false;
  }

  const suma = Math.round((conf + margen) * 100) / 100;
  if (Math.abs(suma - 100) > 0.005) {
    UTIL.mostrarMensajeValidacion(
      "Nivel de confiabilidad (" + conf + "%) y margen de error (" + margen +
      "%) deben sumar exactamente 100%. Hoy suman " + suma + "%."
    );
    return false;
  }
  return true;
}

function init() {
  q = {};

  // ✅ DataTable seguro
  initDataTableSafe();

  // ✅ Flatpickr seguro (si está cargado)
  initFlatpickrSafe();

  // ✅ Si cambia espacio geográfico, carga info
  $("#tbl_espacio_geografico_id").off("change.ft").on("change.ft", function () {
    const id = $(this).val();
    if (id) FICHATECNICAENCUESTA.getInformacionEspacioGeografico(id);
  });

  // ✅ Universo según población objetivo
  $("#poblacion_objetivo").off("change.ft").on("change.ft", function () {
    FICHATECNICAENCUESTA.getValidarUniverso($(this).val());
    FICHATECNICAENCUESTA.margenError();
  });

  $("#nivel_confiabilidad_porcentaje").off("input.ft change.ft").on("input.ft change.ft", function () {
    FICHATECNICAENCUESTA.margenError();
  });

  $("#margen_error_porcentaje").off("input.ft change.ft").on("input.ft change.ft", function () {
    FICHATECNICAENCUESTA.confianzaDesdeMargen();
  });
}

// ======================================================
// Helpers
// ======================================================
function initDataTableSafe() {
  if (!$.fn.DataTable) return;
  if (!$("#dynamictable").length) return;

  if ($.fn.DataTable.isDataTable("#dynamictable")) return;

  $("#dynamictable").DataTable({
    pageLength: 25,
    order: [[1, "desc"]],
    language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
    responsive: false,
    autoWidth: false,
    scrollX: false,
  });
}

function initFlatpickrSafe() {
  try {
    if (typeof flatpickr === "function" && $("#espacio_geografico_fecha").length) {
      flatpickr("#espacio_geografico_fecha", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        allowInput: true,
      });
    }
  } catch (e) {
    // no hacemos nada
  }
}

function safeStr(v) {
  return (v === null || v === undefined) ? "" : String(v);
}

function safeNum(v) {
  const n = parseFloat(String(v).replace(",", "."));
  return isNaN(n) ? 0 : n;
}

function setFormTopTitle(res) {
  $("#spanEncuesta").text(
    " Editar Ficha Técnica Encuesta N° " + res.id + " - " + (res.temas_concretos || "")
  );
  $("#spanModulo").text("");
  if (typeof UTIL !== "undefined" && UTIL.scrollToTop) {
    UTIL.scrollToTop("formfichaTecnicaEncuesta");
  }
}

// ======================================================
// Módulo
// ======================================================
var FICHATECNICAENCUESTA = {
  editData: function (id) {
    q = { op: "fichaTecnicaEncuestaget", id: id };
    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  duplicar: function (id) {
    Swal.fire({
      title: '¿Copiar la encuesta completa?',
      html: '<div class="text-start small">Se creará una encuesta nueva con la ficha técnica, todas las preguntas con sus opciones, visibilidad, saltos, listas derivadas, exclusiones, equivalencias y zonas de muestreo.<br><br>No se copian respuestas ni intentos. La copia queda <strong>deshabilitada</strong> para que la revise antes de activarla.</div>',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, copiar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (!result.value) return;
      UTIL.cursorBusy();
      $.ajax({
        url: 'admin/ajax/rqst.php',
        type: 'POST',
        dataType: 'json',
        data: { op: 'fichaTecnicaEncuestaduplicar', id: id },
        success: function (data) {
          UTIL.cursorNormal();
          if (data.output.valid) {
            var r = data.output.response || {};
            var html = '<div class="text-start">Encuesta copiada (ID nuevo: <strong>' + r.id + '</strong>, deshabilitada).<ul class="mb-0 mt-2">' +
              '<li>' + r.preguntas + ' preguntas</li><li>' + r.opciones + ' opciones</li>' +
              '<li>' + r.exclusiones + ' exclusiones de lista, ' + r.variantes + ' variantes de grilla</li>' +
              '<li>' + r.equivalencias + ' equivalencias, ' + r.zonas + ' zonas de muestreo</li></ul>';
            if (r.advertencias && r.advertencias.length) {
              html += '<div class="alert alert-warning small mt-3 mb-0"><strong>Revise:</strong><ul class="mb-0">';
              r.advertencias.forEach(function (a) { html += '<li>' + $('<div>').text(a).html() + '</li>'; });
              html += '</ul></div>';
            }
            html += '</div>';
            Swal.fire({ title: 'Copia creada', html: html, icon: 'success', confirmButtonColor: '#20427F' })
              .then(function () { window.location.reload(); });
          } else {
            UTIL.mostrarMensajeError(
              (data.output.response && data.output.response.content)
                ? data.output.response.content : 'Error al duplicar.'
            );
          }
        },
        error: function () {
          UTIL.cursorNormal();
          UTIL.mostrarMensajeError('Error de comunicación con el servidor.');
        }
      });
    });
  },

  editdataHandler: function (data) {
    UTIL.cursorNormal();

    if (!data || !data.output || !data.output.valid) {
      UTIL.mostrarMensajeError(data?.output?.response?.content || "No se pudo cargar el registro.");
      return;
    }

    var res = data.output.response[0];

    $("#idFichaTecnicaEncuesta").val(res.id);
    $("#realizada_por_o_encomendada_por").val(res.realizada_por_o_encomendada_por);
    $("#fuente_financiacion").val(res.fuente_financiacion);
    $("#tipo_tamano_muestra_y_procedimiento_utilizado").val(res.tipo_tamano_muestra_y_procedimiento_utilizado);

    $("#temas_concretos").val(res.temas_concretos);
    $("#texto_literal_de_la_encuesta_o_preguntas").val(res.texto_literal_de_la_encuesta_o_preguntas);
    $("#candidatos_personas_instituciones_indagados").val(res.candidatos_personas_instituciones_indagados);

    $("#espacio_geografico_fecha_o_periodo_que_se_realizo").val(res.espacio_geografico_fecha_o_periodo_que_se_realizo);
    $("#tipo_estudio").val(res.tipo_estudio);
    $("#proposito_del_estudio").val(res.proposito_del_estudio);
    $("#universo_representado").val(res.universo_representado);
    $("#metodo_recoleccion").val(res.metodo_recoleccion);

    $("#nivel_confiabilidad_porcentaje").val(getConfianzaPorcentaje(res.nivel_confiabilidad_porcentaje) || 95);
    FICHATECNICAENCUESTA.margenError();
    $("#estadisticos_responsables").val(res.estadisticos_responsables);
    $("#declaracion").val(res.declaracion);
    $("#avisos").val(res.avisos);

    $("#dtcreate").val(res.dtcreate);
    $("#dtupdate").val(res.dtupdate);
    $("#tbl_usuario_id").val(res.tbl_usuario_id);

    $("#habilitado").prop("checked", res.habilitado === "si");

    $("#tipo_estudio_descripcion").val(res.tipo_estudio_descripcion);
    $("#tipo_tamano_muestra_y_procedimiento_utilizado_descripcion").val(res.tipo_tamano_muestra_y_procedimiento_utilizado_descripcion);

    $("#tamano_muestra").val(res.tamano_muestra);
    $("#procedimiento_utilizado").val(res.procedimiento_utilizado);
    $("#espacio_geografico_fecha").val(res.espacio_geografico_fecha);
    $("#espacio_geografico_fecha_estado").val(res.espacio_geografico_fecha_estado);

    $("#tipo_encuesta").val(res.tipo_encuesta);

    // ✅ Importante: setea el espacio geográfico y carga su info (universo/tipo encuesta)
    $("#tbl_espacio_geografico_id").val(res.tbl_espacio_geografico_id);
    $("#poblacion_objetivo").val(res.poblacion_objetivo || "habitantes");

    if (res.tbl_espacio_geografico_id) {
      FICHATECNICAENCUESTA.getInformacionEspacioGeografico(res.tbl_espacio_geografico_id, function () {
        // ✅ Recalcular margen después de cargar universo
        FICHATECNICAENCUESTA.margenError();
      });
    } else {
      FICHATECNICAENCUESTA.margenError();
    }

    setFormTopTitle(res);
  },

  validateData: function () {
    var msj = "Falta ingresar información obligatoria, marcada con asterisco.";

    if (!$("#tbl_espacio_geografico_id").val()) {
      UTIL.mostrarMensajeValidacion("Debe seleccionar el Espacio Geográfico.");
      return;
    }
    if ($("#realizada_por_o_encomendada_por").val().trim() === "") {
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if (!validarConfianzaMargen()) {
      return;
    }

    this.savedata();
  },

  savedata: function () {
    q = {};
    q.op = "fichaTecnicaEncuestasave";

    q.id = $("#idFichaTecnicaEncuesta").val();
    q.realizada_por_o_encomendada_por = $("#realizada_por_o_encomendada_por").val();
    q.fuente_financiacion = $("#fuente_financiacion").val();
    q.tipo_tamano_muestra_y_procedimiento_utilizado = $("#tipo_tamano_muestra_y_procedimiento_utilizado").val();

    q.temas_concretos = $("#temas_concretos").val();
    q.texto_literal_de_la_encuesta_o_preguntas = $("#texto_literal_de_la_encuesta_o_preguntas").val();
    q.candidatos_personas_instituciones_indagados = $("#candidatos_personas_instituciones_indagados").val();

    q.espacio_geografico_fecha_o_periodo_que_se_realizo = $("#espacio_geografico_fecha_o_periodo_que_se_realizo").val();
    q.margen_error_porcentaje = $("#margen_error_porcentaje").val();

    q.tipo_estudio = $("#tipo_estudio").val();
    q.proposito_del_estudio = $("#proposito_del_estudio").val();
    q.universo_representado = $("#universo_representado").val();
    q.metodo_recoleccion = $("#metodo_recoleccion").val();

    q.nivel_confiabilidad_porcentaje = $("#nivel_confiabilidad_porcentaje").val();
    q.estadisticos_responsables = $("#estadisticos_responsables").val();
    q.declaracion = $("#declaracion").val();
    q.avisos = $("#avisos").val();

    q.habilitado = $("#habilitado").is(":checked") ? "si" : "no";

    q.tipo_estudio_descripcion = $("#tipo_estudio_descripcion").val();
    q.tipo_tamano_muestra_y_procedimiento_utilizado_descripcion = $("#tipo_tamano_muestra_y_procedimiento_utilizado_descripcion").val();

    q.tamano_muestra = $("#tamano_muestra").val();
    q.procedimiento_utilizado = $("#procedimiento_utilizado").val();
    q.espacio_geografico_fecha = $("#espacio_geografico_fecha").val();
    q.espacio_geografico_fecha_estado = $("#espacio_geografico_fecha_estado").val();

    q.tipo_encuesta = $("#tipo_encuesta").val();
    q.tbl_espacio_geografico_id = $("#tbl_espacio_geografico_id").val();
    q.poblacion_objetivo = $("#poblacion_objetivo").val();

    UTIL.cursorBusy();
    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data?.output?.valid) {
          UTIL.mostrarMensajeExitoso("Información guardada correctamente");
          setTimeout(function () {
            window.location = return_page;
          }, 1200);
        } else {
          UTIL.mostrarMensajeError(data?.output?.response?.content || "No se pudo guardar.");
        }
      },
      error: function (xhr) {
        UTIL.cursorNormal();
        console.error("AJAX Error:", xhr);
        UTIL.mostrarMensajeError("Ha ocurrido un error en la operación ejecutada");
      },
    });
  },

  deleteData: function (id) {
    Swal.fire({
      title: "¿Eliminar ficha técnica?",
      text: "Se borrará del listado pero los datos se conservarán en base de datos. Es distinto a deshabilitar. Solo es posible si no tiene preguntas, respuestas o grillas asociadas.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#dc3545",
    }).then(function (result) {
      if (!result.value) return;

      q = { op: "fichaTecnicaEncuestadelete", id: id };

      UTIL.cursorBusy();
      $.ajax({
        data: q,
        type: "POST",
        dataType: "json",
        url: "admin/ajax/rqst.php",
        success: function (data) {
          UTIL.cursorNormal();
          if (data?.output?.valid) {
            UTIL.mostrarMensajeExitoso("Ficha técnica eliminada correctamente.");
            setTimeout(function () {
              window.location.reload();
            }, 1200);
          } else {
            UTIL.mostrarMensajeError(data?.output?.response?.content || "No se pudo eliminar el registro.");
          }
        },
        error: function () {
          UTIL.cursorNormal();
          UTIL.mostrarMensajeError("Ha ocurrido un error en la operación ejecutada");
        },
      });
    });
  },

  // ✅ Con callback opcional
  getInformacionEspacioGeografico: function (id, cb) {
    if (!id) {
      espacioGeografico = {};
      $("#tipo_encuesta").val("");
      $("#espacio_geografico_fecha_o_periodo_que_se_realizo").val("");
      $("#universo_representado").val("");
      if (typeof cb === "function") cb();
      return;
    }

    q = { op: "espacioGeograficoget", id: id };

    UTIL.cursorBusy();
    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();

        if (data?.output?.valid) {
          var res = data.output.response[0];
          espacioGeografico = res || {};

          $("#tipo_encuesta").val(res.tipo_estudio || "");
          $("#espacio_geografico_fecha_o_periodo_que_se_realizo").val(res.observaciones || "");

          // ✅ Set universo según población objetivo
          FICHATECNICAENCUESTA.getValidarUniverso($("#poblacion_objetivo").val());

          // ✅ Recalcular margen
          FICHATECNICAENCUESTA.margenError();

          if (typeof cb === "function") cb();
        } else {
          espacioGeografico = {};
          UTIL.mostrarMensajeError(data?.output?.response?.content || "Error al cargar el espacio geográfico.");
          if (typeof cb === "function") cb();
        }
      },
      error: function () {
        espacioGeografico = {};
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Ha ocurrido un error en la operación ejecutada");
        if (typeof cb === "function") cb();
      },
    });
  },

  getValidarUniverso: function (value) {
    if (!espacioGeografico || !espacioGeografico.id) {
      $("#universo_representado").val("");
      return;
    }

    if (value === "habitantes") {
      $("#universo_representado").val(safeStr(espacioGeografico.cantidad_poblacion));
    } else if (value === "votantes") {
      $("#universo_representado").val(safeStr(espacioGeografico.numero_votantes));
    } else {
      $("#universo_representado").val("");
    }
  },

  checkAvailability: function (input) {
    var fieldValue = $(input).val();
    var recordId = $("#idFichaTecnicaEncuesta").val();

    if (String(fieldValue || "").trim() === "") return;

    q = {
      op: "fichaTecnicaEncuestaavailable",
      fieldValue: fieldValue,
      id: recordId,
    };

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (data && data.output && !data.output.valid) {
          UTIL.mostrarMensajeError(data.output.response.content || "El valor ya existe.");
          $(input).focus();
        }
      },
    });
  },

  emptyCells: function () {
    $("#idFichaTecnicaEncuesta").val("");
    $("#spanEncuesta").text("");
    $("#spanModulo").text("Ingreso y listado de Ficha Técnica Encuesta");
    $("#habilitado").prop("checked", true);
    $("#nivel_confiabilidad_porcentaje").val(95);
    $("#margen_error_porcentaje").val(5);

    // ✅ Limpia campos que suelen quedar “pegados”
    $("#tbl_espacio_geografico_id").val("");
    $("#tipo_encuesta").val("");
    $("#espacio_geografico_fecha_o_periodo_que_se_realizo").val("");
    $("#universo_representado").val("");
    espacioGeografico = {};
  },

  margenError: function () {
    const conf = safeNum($("#nivel_confiabilidad_porcentaje").val());
    if (conf <= 0 || conf >= 100) {
      $("#margen_error_porcentaje").val("");
    } else {
      $("#margen_error_porcentaje").val(Math.round((100 - conf) * 100) / 100);
    }
    FICHATECNICAENCUESTA.actualizarAyudaMargen();
  },

  confianzaDesdeMargen: function () {
    const margen = safeNum($("#margen_error_porcentaje").val());
    if (margen <= 0 || margen >= 100) {
      $("#nivel_confiabilidad_porcentaje").val("");
    } else {
      $("#nivel_confiabilidad_porcentaje").val(Math.round((100 - margen) * 100) / 100);
    }
    FICHATECNICAENCUESTA.actualizarAyudaMargen();
  },

  actualizarAyudaMargen: function () {
    const conf = safeNum($("#nivel_confiabilidad_porcentaje").val());
    const margen = safeNum($("#margen_error_porcentaje").val());
    const $hint = $("#margen-confianza-hint");
    if (!$hint.length) return;

    const suma = Math.round((conf + margen) * 100) / 100;
    const ok = conf > 0 && margen > 0 && Math.abs(suma - 100) <= 0.005;
    $hint.toggleClass("text-success", ok).toggleClass("text-danger", !ok);
    $hint.text(ok
      ? "Confianza " + conf + "% + margen " + margen + "% = 100% ✓"
      : "Confianza + margen deben sumar 100%. Al cambiar uno, el otro se ajusta solo.");
  },
};
