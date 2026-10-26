
$(function () { init(); });

var q;
var optionCounter = 0;
var CUESTIONARIO_CTX = window.CUESTIONARIO_CTX || {};
var FICHA_ID_LOCKED = parseInt(CUESTIONARIO_CTX.fichaId, 10) || 0;
var return_page = CUESTIONARIO_CTX.returnPage || "preguntas.php";

function getFichaEncuestaId() {
  if (FICHA_ID_LOCKED > 0) {
    return String(FICHA_ID_LOCKED);
  }
  var v = $("#tbl_ficha_tecnica_encuesta_id").val();
  return v ? String(v) : "";
}

function applyFichaLock() {
  if (FICHA_ID_LOCKED <= 0) return;
  var fid = String(FICHA_ID_LOCKED);
  $("#tbl_ficha_tecnica_encuesta_id, #batchEncuesta").val(fid).prop("disabled", true);
  $("#ficha_id_locked").val(fid);
}

function getSiguienteOrden() {
  var fromCtx = parseInt(CUESTIONARIO_CTX.siguienteOrden, 10);
  if (fromCtx > 0) {
    return fromCtx;
  }
  var maxOrden = 0;
  $(".qst-orden-cell[data-orden]").each(function () {
    var o = parseInt($(this).attr("data-orden"), 10) || 0;
    if (o > maxOrden) maxOrden = o;
  });
  return maxOrden > 0 ? maxOrden + 1 : 1;
}

function init() {
  q = {};

  // Configura los listeners de los botones de opciones (añadir/eliminar)
  PREGUNTAS.setupOptionListeners();
  // Establece el estado inicial del mensaje "No hay opciones"
  OPCIONES.updateNoOptionsMessage();
  applyFichaLock();

  // Sincronización bidireccional: selector principal ↔ selector batch
  $('#tbl_ficha_tecnica_encuesta_id').on('change', function () {
    $('#batchEncuesta').val($(this).val());
  });
  $('#batchEncuesta').on('change', function () {
    $('#tbl_ficha_tecnica_encuesta_id').val($(this).val());
  });

  // Listener: checkbox "Seleccionar todo" por capítulo
  $(document).on('change', '.capitulo-chk-all', function () {
    var capituloIdx = $(this).data('capitulo');
    var checked     = $(this).is(':checked');
    $('input.pregunta-chk[data-capitulo="' + capituloIdx + '"]').prop('checked', checked);
    // Sincronizar headers de grupo dentro del capítulo
    $('input.pregunta-chk[data-capitulo="' + capituloIdx + '"]').each(function () {
      var grupo = $(this).data('grupo');
      syncGrupoHeader(grupo);
    });
  });

  // Listener: checkbox "Seleccionar todo" por grupo enunciado
  $(document).on('change', '.grupo-chk-all', function () {
    var grupo   = $(this).data('grupo');
    var checked = $(this).is(':checked');
    var found = $('input.pregunta-chk[data-grupo="' + grupo + '"]');
    found.prop('checked', checked);
    var capituloIdx = found.first().data('capitulo');
    if (capituloIdx) { syncCapituloHeader(capituloIdx); }
  });

  // Listener: al cambiar fila individual, sincronizar headers grupo y capítulo
  $(document).on('change', '.pregunta-chk', function () {
    var grupo       = $(this).data('grupo');
    var capituloIdx = $(this).data('capitulo');
    syncGrupoHeader(grupo);
    if (capituloIdx) { syncCapituloHeader(capituloIdx); }
  });
}

function syncGrupoHeader(grupo) {
  var total   = $('input.pregunta-chk[data-grupo="' + grupo + '"]').length;
  var checked = $('input.pregunta-chk[data-grupo="' + grupo + '"]:checked').length;
  var header  = $('.grupo-chk-all[data-grupo="' + grupo + '"]');
  if (checked === 0)     { header.prop('checked', false).prop('indeterminate', false); }
  else if (checked === total) { header.prop('checked', true).prop('indeterminate', false); }
  else                   { header.prop('checked', false).prop('indeterminate', true); }
}

function syncCapituloHeader(capituloIdx) {
  var total   = $('input.pregunta-chk[data-capitulo="' + capituloIdx + '"]').length;
  var checked = $('input.pregunta-chk[data-capitulo="' + capituloIdx + '"]:checked').length;
  var header  = $('.capitulo-chk-all[data-capitulo="' + capituloIdx + '"]');
  if (checked === 0)     { header.prop('checked', false).prop('indeterminate', false); }
  else if (checked === total) { header.prop('checked', true).prop('indeterminate', false); }
  else                   { header.prop('checked', false).prop('indeterminate', true); }
}

var PREGUNTAS = {
  /**
   * Configura los eventos para los botones de añadir y eliminar opciones.
   */
  setupOptionListeners: function () {
    // Evento para el botón de añadir opción
    $("#addOptionBtn").on("click", PREGUNTAS.addOptionBtnClick);

    // Evento delegado para los botones de eliminar opción (funciona para botones dinámicos)
    $(document).on("click", ".removeOptionBtn", OPCIONES.removeOptionBtnClick);
  },

  /**
   * Manejador de click para el botón 'Añadir Opción'.
   */
  addOptionBtnClick: function () {
    OPCIONES.addOptionField();
    OPCIONES.updateNoOptionsMessage();
  },

  editData: function (id) {
    q = {};
    q.op = "preguntaget";
    q.id = id;
    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  editdataHandler: function (data) {
    UTIL.cursorNormal();
    if (data.output.valid && data.output.response.length > 0) {
      var res = data.output.response[0];
      $("#id").val(res.id);
      $("#tbl_ficha_tecnica_encuesta_id").val(res.tbl_ficha_tecnica_encuesta_id);
      applyFichaLock();
      $("#texto_pregunta").val(res.texto_pregunta);
      $("#enunciado_pregunta").val(res.enunciado_pregunta || "");
      $("#numeral").val(res.numeral || "");
      $("#capitulo").val(res.capitulo || "");
      $("#libreto_encuestador").val(res.libreto_encuestador || "");
      $("#nota_tabulacion").val(res.nota_tabulacion || "");
      $("#tipo_pregunta").val(res.tipo_pregunta);
      $("#orden").val(res.orden);

      $("#limite_respuesta_multiple").val(res.limite_respuesta_multiple || 2);
      $("#obligatoria").prop("checked", String(res.obligatoria) !== "0");
      $("#es_filtro").prop("checked", String(res.es_filtro) === "1");
      $("#es_variable_cruce").prop("checked", String(res.es_variable_cruce) === "1");
      $("#etiqueta_cruce").val(res.etiqueta_cruce || "");
      $("#copiar_a_votante_campo").val(res.copiar_a_votante_campo || "ninguno");
      $("#rol_resultado").val(res.rol_resultado || "ninguno");
      $("#qstCruceExtraWrap").toggle(String(res.es_variable_cruce) === "1");

      $("#habilitado").prop("checked", res.habilitado === "si");
      $("#visualizacion").prop("checked", res.visualizacion === "si");

      PREGUNTAS.populateVisibilidadQuestions(res.id, res.orden, res.visible_si_pregunta_id, res.visible_si_opcion_id);
      PREGUNTAS.populateGrillaAndListaPanels(res);

      OPCIONES.handleTipoPreguntaChange("tipo_pregunta", res.opciones || []);

      $("#preguntaFormModal").modal("show");
    } else {
      UTIL.mostrarMensajeError(data.output.response.content);
    }
  },

  validateData: function () {
    var bValid = true;
    var tipo = $("#tipo_pregunta").val();
    if (getFichaEncuestaId() === "0" || getFichaEncuestaId() === "") {
      UTIL.mostrarMensajeValidacion("Por favor, seleccione una Encuesta.");
      bValid = false;
    } else if (!tipo) {
      UTIL.mostrarMensajeValidacion("Por favor, seleccione el Tipo de Pregunta.");
      bValid = false;
    } else if ($("#texto_pregunta").val().trim() === "") {
      UTIL.mostrarMensajeValidacion("El texto de la pregunta es requerido.");
      bValid = false;
    } else if (tipo === "Grilla") {
      if ($("#grilla_por_municipio").is(":checked")) {
        if ($("#qstGrillaVariantesRows .qst-var-row").length === 0) {
          UTIL.mostrarMensajeValidacion("Agregue al menos un municipio con su tabla de candidatos.");
          bValid = false;
        }
      } else if (!$("#tbl_grilla_plantilla_id").val()) {
        UTIL.mostrarMensajeValidacion("Seleccione la tabla de candidatos para esta pregunta.");
        bValid = false;
      }
    } else if (tipo === "Lista_desde_grilla") {
      if (!$("#lista_origen_pregunta_id").val()) {
        UTIL.mostrarMensajeValidacion("Seleccione de cuál pregunta de tabla vienen los candidatos.");
        bValid = false;
      }
    } else if (tipo !== "Texto Libre" && tipo !== "Abierta") {
      var opts = OPCIONES.getOpciones();
      if (opts.length === 0) {
        UTIL.mostrarMensajeValidacion("Agregue al menos una opción de respuesta.");
        bValid = false;
      }
    }

    if (!bValid) {
      return;
    }
    PREGUNTAS.savedata();
  },

  savedata: function () {
    var tipo = $("#tipo_pregunta").val();
    q = {};
    q.op = "preguntasave";
    q.id = $("#id").val();
    q.tbl_ficha_tecnica_encuesta_id = getFichaEncuestaId();
    q.texto_pregunta = $("#texto_pregunta").val();
    q.enunciado_pregunta = $("#enunciado_pregunta").val();
    q.numeral = $("#numeral").val();
    q.capitulo = $("#capitulo").val();
    q.libreto_encuestador = $("#libreto_encuestador").val();
    q.nota_tabulacion = $("#nota_tabulacion").val();
    q.tipo_pregunta = tipo;
    q.orden = $("#orden").val();
    q.limite_respuesta_multiple = $("#limite_respuesta_multiple").val();
    q.obligatoria = $("#obligatoria").is(":checked") ? 1 : 0;
    q.es_filtro = $("#es_filtro").is(":checked") ? 1 : 0;
    q.es_variable_cruce = $("#es_variable_cruce").is(":checked") ? 1 : 0;
    q.etiqueta_cruce = $("#etiqueta_cruce").val();
    q.copiar_a_votante_campo = $("#copiar_a_votante_campo").val() || "ninguno";
    q.rol_resultado = $("#rol_resultado").val() || "ninguno";
    q.habilitado = $("#habilitado").is(":checked") ? "si" : "no";
    q.visualizacion = $("#visualizacion").is(":checked") ? "si" : "no";

    if ($("#qstModoVisibilidad").val() === "condicional") {
      q.visible_si_pregunta_id = $("#visible_si_pregunta_id").val() || "";
      q.visible_si_opcion_id = $("#visible_si_opcion_id").val() || "";
    } else {
      q.visible_si_pregunta_id = "";
      q.visible_si_opcion_id = "";
    }

    if (tipo === "Grilla") {
      var porMun = $("#grilla_por_municipio").is(":checked") ? 1 : 0;
      q.grilla_por_municipio = porMun;
      if (porMun) {
        var variantes = [];
        $("#qstGrillaVariantesRows .qst-var-row").each(function () {
          var cod = $(this).find(".qst-var-mun").val();
          var plId = $(this).find(".qst-var-pl").val();
          if (cod && plId) {
            variantes.push({ codigo_municipio: cod, tbl_grilla_plantilla_id: parseInt(plId, 10) });
          }
        });
        q.grilla_variantes = JSON.stringify(variantes);
        q.tbl_grilla_plantilla_id = "";
      } else {
        q.tbl_grilla_plantilla_id = $("#tbl_grilla_plantilla_id").val() || "";
        q.grilla_variantes = "[]";
      }
    } else if (tipo === "Lista_desde_grilla") {
      q.lista_origen_pregunta_id = $("#lista_origen_pregunta_id").val() || "";
      q.lista_origen_columna_id = $("#lista_origen_columna_id").val() || "";
      q.lista_origen_opcion_id = $("#lista_origen_opcion_id").val() || "";
      q.si_lista_vacia = $("#si_lista_vacia").val() || "omitir";
      var excl = [];
      $("#qstListaExclusionesContainer .qst-excl-chk:checked").each(function () {
        excl.push(parseInt($(this).val(), 10));
      });
      q.exclusiones = JSON.stringify(excl);
    }

    q.opciones = JSON.stringify(OPCIONES.getOpciones());
    UTIL.cursorBusy();

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
          UTIL.mostrarMensajeExitoso("Pregunta guardada correctamente");
          setTimeout(function () {
            window.location = return_page;
          }, 1200);
        } else {
          UTIL.mostrarMensajeError(data.output.response.content || "No fue posible guardar la pregunta.");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        UTIL.cursorNormal();
        console.error("Error AJAX en save Pregunta:", textStatus, errorThrown, jqXHR.responseText);
        UTIL.mostrarMensajeError("Error de comunicación con el servidor al guardar la pregunta.");
      },
    });
  },

  clearForm: function (formId) {
    UTIL.clearForm(formId);
    $("#id").val("");
    $("#enunciado_pregunta").val("");
    $("#numeral").val("");
    $("#capitulo").val("");
    $("#libreto_encuestador").val("");
    $("#nota_tabulacion").val("");
    $("#obligatoria").prop("checked", true);
    $("#es_filtro").prop("checked", false);
    $("#es_variable_cruce").prop("checked", false);
    $("#etiqueta_cruce").val("");
    $("#copiar_a_votante_campo").val("ninguno");
    $("#rol_resultado").val("ninguno");
    $("#qstCruceExtraWrap").hide();
    $("#qstModoVisibilidad").val("siempre");
    $("#qstVisibilidadCondicionalWrap").hide();
    $("#grilla_por_municipio").prop("checked", false);
    $("#tbl_grilla_plantilla_id").val("");
    $("#qstGrillaVariantesRows").empty();
    $("#qstGrillaUnicaWrap").show();
    $("#qstGrillaPorMunicipioWrap").hide();
    $("#opcionesContainer").empty();
    $("#noOptionsMessage").show();
    if (FICHA_ID_LOCKED > 0) {
      applyFichaLock();
    } else {
      $("#tbl_ficha_tecnica_encuesta_id").val("0").trigger("change");
    }
    $("#tipo_pregunta").val("").trigger("change");
    $("#habilitado").prop("checked", true);
    $("#visualizacion").prop("checked", true);
    $("#preguntaFormModal").modal("hide");
  },

  openCreateModal: function () {
    PREGUNTAS.clearForm("formPreguntas");
    var nextOrd = getSiguienteOrden();
    $("#orden").val(nextOrd);
    PREGUNTAS.populateVisibilidadQuestions(0, nextOrd, null, null);
    PREGUNTAS.populateGrillaAndListaPanels({});
    $("#preguntaFormModal").modal("show");
  },

  /**
   * Muestra un modal con las opciones de respuesta de una pregunta.
   * @param {string} optionsJson Cadena JSON de las opciones.
   * @param {string} questionText Texto de la pregunta.
   */
  showOptionsModal: function (optionsJson, questionText) {
    try {
      const opciones = JSON.parse(optionsJson);
      const modalBodyList = $("#modalOptionsList");
      const modalQuestionText = $("#modalQuestionText");

      modalBodyList.empty();
      modalQuestionText.empty().text(questionText);

      if (opciones.length > 0) {
        $.each(opciones, function (index, opcion) {
          const optionHtml = `
                      <div class="col-md-6 col-xxl-12">
                          <div class="rounded-3 py-2 px-3 bg-body-emphasis d-flex align-items-center mb-3">
                              <span class="fas fa-check text-primary me-3 fs-9"></span>
                              <p class="mb-0 text-body-secondary">${PREGUNTAS.htmlspecialchars(
            opcion.texto
          )}</p>
                          </div>
                      </div>
                  `;
          modalBodyList.append(optionHtml);
        });
      } else {
        modalBodyList.append(
          '<p class="text-muted text-center">No hay opciones para esta pregunta.</p>'
        );
      }

      // Mostramos el modal
      const optionsModal = new bootstrap.Modal(
        document.getElementById("optionsModal")
      );
      optionsModal.show();
    } catch (e) {
      UTIL.mostrarMensajeError("Error al cargar las opciones de la pregunta.");
    }
  },
  // Habilitar / Deshabilitar pregunta
  toggleHabilitado: function (id, esActivo) {
    var nuevoEstado = esActivo ? 'no' : 'si';
    UTIL.cursorBusy();
    $.ajax({
      url: 'admin/ajax/rqst.php',
      type: 'POST',
      dataType: 'json',
      data: { op: 'preguntahabilitadosave', id: id, habilitado: nuevoEstado },
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
          // Actualizar badge de estado
          var badge = nuevoEstado === 'si'
            ? '<span class="badge bg-success" style="border-radius:8px;">Activo</span>'
            : '<span class="badge bg-danger" style="border-radius:8px;">Inactivo</span>';
          $('#estadoFila' + id).html(badge);
          // Actualizar botón
          var btn = $('#btnHab' + id);
          if (nuevoEstado === 'si') {
            btn.removeClass('btn-outline-success').addClass('btn-outline-warning')
               .attr('title','Deshabilitar')
               .attr('onclick','PREGUNTAS.toggleHabilitado(' + id + ', true)')
               .html('<i class="fas fa-toggle-off"></i>');
          } else {
            btn.removeClass('btn-outline-warning').addClass('btn-outline-success')
               .attr('title','Habilitar')
               .attr('onclick','PREGUNTAS.toggleHabilitado(' + id + ', false)')
               .html('<i class="fas fa-toggle-on"></i>');
          }
        } else {
          UTIL.mostrarMensajeError(data.output.response.content || 'Error.');
        }
      },
      error: function () { UTIL.cursorNormal(); UTIL.mostrarMensajeError('Error de comunicación.'); }
    });
  },

  // Eliminar pregunta
  deletePregunta: function (id, texto) {
    Swal.fire({
      title: '⚠️ Acción irreversible',
      html: `
        <p style="color:#64748b;margin-bottom:8px;">Estás a punto de eliminar la siguiente pregunta:</p>
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-weight:600;color:#856404;font-size:.9rem;">
          "${texto}"
        </div>
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:10px 14px;font-size:.85rem;color:#991b1b;">
          <i class="fas fa-exclamation-triangle me-1"></i>
          <strong>Esta acción no se puede deshacer.</strong><br>
          Se eliminarán la pregunta y todas sus opciones de respuesta de forma permanente.
        </div>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sí, eliminar permanentemente',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      reverseButtons: true,
      focusCancel: true
    }).then(function (result) {
      if (!result.value) return;
      UTIL.cursorBusy();
      $.ajax({
        url: 'admin/ajax/rqst.php',
        type: 'POST',
        dataType: 'json',
        data: { op: 'preguntadelete', id: id },
        success: function (data) {
          UTIL.cursorNormal();
          if (data.output.valid) {
            $('#fila' + id).fadeOut(300, function(){ $(this).remove(); });
            UTIL.mostrarMensajeExitoso('Pregunta eliminada.');
          } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al eliminar.');
          }
        },
        error: function () { UTIL.cursorNormal(); UTIL.mostrarMensajeError('Error de comunicación.'); }
      });
    });
  },

  // Colapsar / expandir grupo de ficha técnica
  toggleGrupo: function (fichaId) {
    var body    = $('#grupoBody' + fichaId);
    var chevron = $('#chevronGrupo' + fichaId);
    if (body.is(':visible')) {
      body.slideUp(200);
      chevron.removeClass('fa-chevron-down').addClass('fa-chevron-right');
    } else {
      body.slideDown(200);
      chevron.removeClass('fa-chevron-right').addClass('fa-chevron-down');
    }
  },

  // Colapsar / expandir capítulo
  toggleCapitulo: function (capituloIdx) {
    var body    = $('#capituloBody' + capituloIdx);
    var chevron = $('#chevronCapitulo' + capituloIdx);
    if (body.is(':visible')) {
      body.slideUp(200);
      chevron.removeClass('fa-chevron-down').addClass('fa-chevron-right');
    } else {
      body.slideDown(200);
      chevron.removeClass('fa-chevron-right').addClass('fa-chevron-down');
    }
  },

  // Abrir modal de edición rápida de enunciado (pregunta individual)
  editEnunciado: function (preguntaId, enunciadoActual) {
    $('#editEnunciadoPreguntaId').val(preguntaId);
    $('#editEnunciadoFichaId').val('');
    $('#editEnunciadoModo').val('one');
    $('#editEnunciadoSubtitle').text('Editando pregunta individual');
    $('#editEnunciadoOpcionGrupo').hide();
    $('#editEnunciadoTexto').val(enunciadoActual);
    var modal = new bootstrap.Modal(document.getElementById('editEnunciadoModal'));
    modal.show();
    setTimeout(function(){ $('#editEnunciadoTexto').focus(); }, 400);
  },

  // Abrir modal de edición de enunciado para un grupo específico
  // Acepta: (idsString, enunciadoActual) — desde botón header enunciado
  // O bien: (btnElement) — desde botón "Enunciado Check" toolbar
  // Si hay checkboxes marcados cuyos IDs estén dentro del grupo → usa solo esos
  editEnunciadoGrupo: function (idsStringOrBtn, enunciadoActual) {
    var idsString, enunciado;

    if (idsStringOrBtn && typeof idsStringOrBtn === 'object' && idsStringOrBtn.tagName) {
      // Botón toolbar DOM — leer checkboxes marcados de esa ficha
      var fichaId = $(idsStringOrBtn).data('ficha-id');
      var checked = $('.pregunta-chk[data-grupo^="' + fichaId + '_"]:checked');
      if (checked.length === 0) {
        UTIL.mostrarMensajeValidacion('Selecciona al menos una pregunta con los checkboxes.');
        return;
      }
      idsString = checked.map(function() { return $(this).val(); }).get().join(',');
      enunciado = $(idsStringOrBtn).data('enunciado') || '';
    } else {
      // Botón header enunciado — idsString = todos del grupo
      // Si el usuario marcó checkboxes dentro de ese grupo, usar solo esos
      var allIds = idsStringOrBtn ? idsStringOrBtn.split(',') : [];
      var allIdsSet = {};
      allIds.forEach(function(id) { allIdsSet[id.trim()] = true; });

      var checkedInGroup = $('.pregunta-chk:checked').filter(function() {
        return allIdsSet[$(this).val()];
      });

      if (checkedInGroup.length > 0) {
        idsString = checkedInGroup.map(function() { return $(this).val(); }).get().join(',');
      } else {
        idsString = idsStringOrBtn;
      }
      enunciado = enunciadoActual || '';
    }

    if (!idsString || idsString === '') {
      UTIL.mostrarMensajeValidacion('No hay preguntas en este grupo.');
      return;
    }

    var ids = idsString.split(',');

    $('#editEnunciadoPreguntaId').val(idsString);
    $('#editEnunciadoFichaId').val('');
    $('#editEnunciadoModo').val('selected');
    $('#editEnunciadoSubtitle').text('Edición en lote (' + ids.length + ' preguntas)');
    $('#editEnunciadoOpcionGrupo').hide();
    $('#editEnunciadoTexto').val(enunciado);
    var modal = new bootstrap.Modal(document.getElementById('editEnunciadoModal'));
    modal.show();
    setTimeout(function(){ $('#editEnunciadoTexto').focus(); }, 400);
  },

  // Guardar enunciado via AJAX — maneja modo individual y seleccionados
  saveEnunciado: function () {
    var modo       = $('#editEnunciadoModo').val();
    var enunciado  = $('#editEnunciadoTexto').val().trim();

    UTIL.cursorBusy();

    if (modo === 'selected') {
      // Modo: enunciado para preguntas seleccionadas (checkboxes)
      var ids = $('#editEnunciadoPreguntaId').val();
      if (!ids) { UTIL.cursorNormal(); return; }

      $.ajax({
        url: 'admin/ajax/rqst.php',
        type: 'POST',
        dataType: 'json',
        data: { op: 'preguntaenunciadoselected', ids: ids, enunciado_pregunta: enunciado },
        success: function (data) {
          UTIL.cursorNormal();
          if (data.output.valid) {
            bootstrap.Modal.getInstance(document.getElementById('editEnunciadoModal')).hide();
            UTIL.mostrarMensajeExitoso(data.output.response);
            setTimeout(function () { window.location.reload(); }, 1500);
          } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al guardar.');
          }
        },
        error: function () {
          UTIL.cursorNormal();
          UTIL.mostrarMensajeError('Error de comunicación.');
        }
      });

    } else {
      // Modo: individual
      var preguntaId = $('#editEnunciadoPreguntaId').val();
      if (!preguntaId) { UTIL.cursorNormal(); return; }

      $.ajax({
        url: 'admin/ajax/rqst.php',
        type: 'POST',
        dataType: 'json',
        data: { op: 'preguntaenunciadosave', id: preguntaId, enunciado_pregunta: enunciado },
        success: function (data) {
          UTIL.cursorNormal();
          if (data.output.valid) {
            bootstrap.Modal.getInstance(document.getElementById('editEnunciadoModal')).hide();
            UTIL.mostrarMensajeExitoso('Enunciado guardado correctamente.');
            setTimeout(function () { window.location.reload(); }, 1500);
          } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al guardar.');
          }
        },
        error: function () {
          UTIL.cursorNormal();
          UTIL.mostrarMensajeError('Error de comunicación.');
        }
      });
    }
  },

  showUploadModal: function () {
    const uploadModal = new bootstrap.Modal(
      document.getElementById("uploadModal")
    );
    uploadModal.show();
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
      "'": "&#039;",
    };
    return str.replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  },
  uploadFile: function () {
    // Verificar si hay un archivo cargado en el iframe
    const iframe = document.getElementById('ifm1');

    // Verificar si el iframe tiene el atributo data-loaded establecido como true
    const isFileLoaded = iframe && iframe.getAttribute('data-loaded') === 'true';

    if (!isFileLoaded) {
      UTIL.mostrarMensajeError("Por favor, carga un archivo Excel antes de continuar.");
      return;
    }

    Swal.fire({
      title:
        "Está seguro que desea subir un archivo de preguntas al sistema?",
      text: "Desea continuar!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "Cancelar!",
      closeOnConfirm: false,
    }).then((result) => {
      if (result.value) {
        q = {};
        q.op = "preguntas_upload";

        // El archivo ya está cargado en el iframe, no necesitamos verificar fileInput
        UTIL.cursorBusy();
        $.ajax({
          data: q,
          type: "POST",
          dataType: "json",
          url: "admin/ajax/rqst.php",
          success: function (data) {
            q = {};
            UTIL.cursorNormal();
            if (data.output.valid) {
              UTIL.mostrarMensajeExitoso(data.output.response);
              setTimeout(() => {
                window.location.reload();
              }, 5000);
            } else {
              UTIL.mostrarMensajeError(data.output.response.content);
            }
          },
          error: function() {
            UTIL.cursorNormal();
            UTIL.mostrarMensajeError("Error al procesar el archivo. Inténtelo de nuevo.");
          }
        });
      }
    });
  },

  /* ================================================================
     INGRESO MASIVO - batch
     Modelo: Capítulo → Enunciado → N sub-preguntas
  ================================================================ */

  _batchEnunciadoIdx: 0,
  _batchCapituloIdx: 0,

  showBatchModal: function () {
    applyFichaLock();
    var encuestaIdPrincipal = getFichaEncuestaId();
    if (encuestaIdPrincipal) $('#batchEncuesta').val(encuestaIdPrincipal);

    PREGUNTAS._batchEnunciadoIdx = 0;
    PREGUNTAS._batchCapituloIdx  = 0;
    $('#batchEnunciadosList').empty();
    $('#batchStep1').show();
    $('#batchStep2').hide();
    $('#batchFooter').hide();
    $('#batchQuestionsContainer').empty();

    // Iniciar con 1 capítulo + 1 enunciado por defecto
    PREGUNTAS.batchAddCapitulo();

    const modal = new bootstrap.Modal(document.getElementById('batchModal'));
    modal.show();
  },

  /* --- Paso 1: capítulos y enunciados --- */

  batchAddCapitulo: function () {
    var capIdx = ++PREGUNTAS._batchCapituloIdx;
    var html = `
      <div class="batch-cap-card mb-3" id="batchCapCard${capIdx}"
           style="border-radius:14px;border:2px solid rgba(32,66,127,.25);background:#fff;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#20427F,#132b52);padding:10px 14px;display:flex;align-items:center;gap:10px;">
          <span style="background:rgba(255,255,255,.2);color:#fff;border-radius:8px;font-weight:900;font-size:.85rem;padding:2px 10px;">Cap ${capIdx}</span>
          <strong style="color:#fff;font-size:.92rem;flex:1;">Capítulo ${capIdx}</strong>
          <button type="button" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;padding:2px 8px;"
                  onclick="$('#batchCapCard${capIdx}').remove();">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div style="padding:10px 14px 6px;">
          <input type="text" class="form-control mb-2" id="batchCapTexto${capIdx}"
                 placeholder="Nombre del capítulo (ej: Capítulo 1 · Cómo Vemos Nuestro Contexto)"
                 style="border-radius:10px;font-weight:600;">
        </div>
        <div id="batchCapEnunciados${capIdx}" style="padding:0 14px 4px;"></div>
        <div style="padding:4px 14px 12px;">
          <button type="button" class="btn btn-sm btn-outline-primary w-100"
                  style="border-radius:10px;border-style:dashed;font-size:.82rem;"
                  onclick="PREGUNTAS.batchAddEnunciado(${capIdx})">
            <i class="fas fa-plus me-1"></i> Añadir enunciado al capítulo ${capIdx}
          </button>
        </div>
      </div>`;
    $('#batchEnunciadosList').append(html);
    // Primer enunciado por defecto
    PREGUNTAS.batchAddEnunciado(capIdx);
  },

  batchAddEnunciado: function (capIdx) {
    var idx = ++PREGUNTAS._batchEnunciadoIdx;

    var html = `
      <div class="card mb-2 batch-enc-card" id="batchEncCard${idx}" data-cap-id="${capIdx}"
           style="border-radius:10px;border:1.5px solid rgba(32,66,127,.15);background:rgba(248,250,252,.8);">
        <div class="card-body py-2 px-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge" style="background:rgba(32,66,127,.12);color:#20427F;border-radius:999px;font-weight:900;font-size:.82rem;min-width:24px;text-align:center;">${idx}</span>
            <strong class="small" style="color:#20427F;">Enunciado ${idx}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" style="border-radius:8px;padding:2px 8px;"
                    onclick="$('#batchEncCard${idx}').remove();">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="mb-2">
            <input type="text" class="form-control batch-enc-texto" id="batchEncTexto${idx}"
                   placeholder="Texto del enunciado (ej: Califique los principales problemas...)"
                   style="border-radius:10px;">
          </div>
          <div class="row g-2 mb-1">
            <div class="col-sm-3">
              <input type="text" class="form-control" id="batchEncNumeral${idx}"
                     placeholder="Numeral (ej: 1.1)"
                     style="border-radius:10px;">
            </div>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="batchEncTextoAdicional${idx}"
                     placeholder="Texto adicional (contexto o instrucción extra)"
                     style="border-radius:10px;">
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 mt-2 mb-1">
            <span class="small text-muted">Sub-preguntas:</span>
            <button type="button" class="btn btn-outline-secondary btn-sm" style="border-radius:50%;width:26px;height:26px;padding:0;font-size:.9rem;line-height:1;"
                    onclick="PREGUNTAS.batchChangeSubCount(${idx},-1)">−</button>
            <input type="number" id="batchSubCount${idx}" value="5" min="1" max="30"
                   style="width:52px;text-align:center;font-weight:900;border:1.5px solid rgba(32,66,127,.2);border-radius:8px;padding:2px 0;color:#20427F;font-size:.95rem;">
            <button type="button" class="btn btn-outline-secondary btn-sm" style="border-radius:50%;width:26px;height:26px;padding:0;font-size:.9rem;line-height:1;"
                    onclick="PREGUNTAS.batchChangeSubCount(${idx},1)">+</button>
            <span class="small text-muted ms-2">Prefijo:</span>
            <input type="text" id="batchPrefix${idx}" value="${idx}" maxlength="4"
                   style="width:48px;text-align:center;font-weight:700;border:1.5px solid rgba(32,66,127,.2);border-radius:8px;padding:2px 4px;font-size:.9rem;color:#20427F;">
          </div>
        </div>
      </div>`;
    $('#batchCapEnunciados' + capIdx).append(html);
  },

  batchChangeSubCount: function (idx, delta) {
    var input = document.getElementById('batchSubCount' + idx);
    var val = Math.max(1, Math.min(30, (parseInt(input.value, 10) || 1) + delta));
    input.value = val;
  },

  /* --- Paso 2: generar formulario --- */

  batchGenerate: function () {
    var encuestaId  = getFichaEncuestaId();
    var tipoDefault = $('#batchTipoDefault').val();
    var opcionesRaw = $('#batchOpcionesDefault').val().trim();

    if (!encuestaId || encuestaId === '0' || encuestaId === '') {
      UTIL.mostrarMensajeValidacion('Por favor seleccione una ficha técnica.');
      return;
    }
    if ($('.batch-enc-card').length === 0) {
      UTIL.mostrarMensajeValidacion('Agrega al menos un enunciado.');
      return;
    }

    $('#tbl_ficha_tecnica_encuesta_id').val(encuestaId);

    var opcionesArr = opcionesRaw !== ''
      ? opcionesRaw.split(/[\n,]+/).map(function(o){ return o.trim(); }).filter(function(o){ return o !== ''; })
      : [];

    var container = $('#batchQuestionsContainer');
    container.empty();

    var tiposOptions = `
      <option value="">Seleccione</option>
      <option value="Dicotomica">Dicotómica</option>
      <option value="Preguntas_Ordinales">Preguntas Ordinales</option>
      <option value="Preguntas_Cardinales">Preguntas Cardinales</option>
      <option value="Seleccion_Multiple_unica_respuesta">Selección múltiple única respuesta</option>
      <option value="Seleccion_Multiple_multiple_respuesta">Selección múltiple múltiple respuesta</option>
    `;

    var ordenGlobal = 0;
    var letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // Iterar por cada enunciado definido en paso 1
    $('.batch-enc-card').each(function () {
      var encCard   = $(this);
      var encId     = encCard.attr('id').replace('batchEncCard', '');
      var encTexto  = $('#batchEncTexto' + encId).val().trim();
      var subCount  = parseInt($('#batchSubCount' + encId).val(), 10) || 1;
      var prefix    = $('#batchPrefix' + encId).val().trim() || encId;
      var capId     = encCard.data('cap-id');
      var capitulo  = capId ? $('#batchCapTexto' + capId).val().trim() : '';

      // ── Separador de enunciado con opciones compartidas ──────────────
      // Construir chips de opciones para previsualización
      var opcionesChips = opcionesArr.map(function(o) {
        return `<span style="display:inline-flex;align-items:center;background:#e8edf7;color:#20427F;border:1px solid rgba(32,66,127,.2);border-radius:6px;padding:2px 10px;font-size:.8rem;font-weight:700;">${PREGUNTAS.htmlspecialchars(o)}</span>`;
      }).join('');

      var capBadge = capitulo
        ? `<div class="mb-1"><span style="background:rgba(32,66,127,.08);color:#20427F;border-radius:6px;font-size:.75rem;font-weight:700;padding:2px 8px;"><i class="fas fa-layer-group me-1"></i>${PREGUNTAS.htmlspecialchars(capitulo)}</span></div>`
        : '';

      var sepHtml = `
        <div class="batch-enc-group mb-2 mt-3" data-enc-id="${encId}">
          <div class="card" style="border-radius:14px;border:1.5px solid rgba(32,66,127,.2);background:linear-gradient(135deg,rgba(32,66,127,.06),rgba(32,66,127,.02));overflow:hidden;">
            <div class="card-body py-2 px-3">
              <div class="d-flex align-items-start gap-2 flex-wrap">
                <span class="badge mt-1" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;border-radius:8px;font-size:.8rem;padding:4px 10px;">${encId}</span>
                <div class="flex-grow-1">
                  ${capBadge}
                  <div class="fw-bold" style="color:#0f172a;font-size:.92rem;">${encTexto ? PREGUNTAS.htmlspecialchars(encTexto) : '<span class="text-muted fst-italic">Sin enunciado</span>'}</div>
                  <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">

                    <!-- Tipo compartido -->
                    <div class="d-flex align-items-center gap-2">
                      <span class="small text-muted">Tipo:</span>
                      <select class="form-select form-select-sm" id="batchEncTipo${encId}"
                              style="border-radius:8px;width:auto;min-width:200px;font-size:.82rem;border:1.5px solid rgba(32,66,127,.2);">
                        <option value="">Seleccione</option>
                        <option value="Dicotomica">Dicotómica</option>
                        <option value="Preguntas_Ordinales" ${tipoDefault === 'Preguntas_Ordinales' ? 'selected' : ''}>Preguntas Ordinales</option>
                        <option value="Preguntas_Cardinales" ${tipoDefault === 'Preguntas_Cardinales' ? 'selected' : ''}>Preguntas Cardinales</option>
                        <option value="Seleccion_Multiple_unica_respuesta" ${tipoDefault === 'Seleccion_Multiple_unica_respuesta' ? 'selected' : ''}>Selección múltiple única</option>
                        <option value="Seleccion_Multiple_multiple_respuesta" ${tipoDefault === 'Seleccion_Multiple_multiple_respuesta' ? 'selected' : ''}>Selección múltiple múltiple</option>
                      </select>
                    </div>

                    <!-- Opciones compartidas -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="small text-muted">Opciones:</span>
                      <div class="d-flex gap-1 flex-wrap" id="batchEncOpciones${encId}">
                        ${opcionesChips}
                      </div>
                      <button type="button" class="btn btn-link btn-sm p-0" style="font-size:.75rem;color:#20427F;"
                              onclick="PREGUNTAS.batchToggleEditOpciones('${encId}')">
                        <i class="fas fa-edit me-1"></i>Editar
                      </button>
                    </div>
                  </div>
                  <div id="batchEncOpcionesEdit${encId}" style="display:none;" class="mt-2">
                    <input type="text" class="form-control form-control-sm batch-enc-opciones-input"
                           id="batchEncOpcionesInput${encId}"
                           value="${PREGUNTAS.htmlspecialchars(opcionesArr.join(', '))}"
                           placeholder="Ej: 1, 2, 3, 4, 5"
                           style="border-radius:8px;max-width:320px;"
                           onchange="PREGUNTAS.batchRefreshOpcionesChips('${encId}')">
                    <div class="small text-muted mt-1">Separadas por coma. Se aplicarán a todas las preguntas de este enunciado al guardar.</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>`;
      container.append(sepHtml);

      for (var s = 0; s < subCount; s++) {
        ordenGlobal++;
        var letra   = letras[s] || (s + 1);
        var blockId = encId + '_' + s;
        var label   = prefix + letra;

        var block = `
          <div class="batch-question-block card mb-1 ms-3" data-blockid="${blockId}" data-enc-id="${encId}"
               style="border-radius:10px;border:1px solid rgba(15,23,42,.08);box-shadow:0 2px 8px rgba(2,6,23,.05);overflow:hidden;">
            <div class="batch-block-header d-flex align-items-center justify-content-between px-3 py-2"
                 style="background:rgba(255,255,255,.95);cursor:pointer;user-select:none;"
                 onclick="PREGUNTAS.batchToggleBlockById('${blockId}')">
              <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:rgba(32,66,127,.10);color:#20427F;border:1px solid rgba(32,66,127,.18);border-radius:6px;font-weight:900;min-width:36px;text-align:center;font-size:.8rem;">${label}</span>
                <span class="fw-semibold text-muted small batch-sub-title" id="batchSubTitle${blockId}">Sub-pregunta ${label}</span>
                <span class="badge bg-light text-muted border" id="batchSubStatus${blockId}" style="font-size:.68rem;border-radius:999px;"></span>
              </div>
              <i class="fas fa-chevron-right batch-chevron" id="batchChevron${blockId}" style="color:#20427F;transition:transform .25s;font-size:.78rem;"></i>
            </div>
            <div class="batch-block-body px-3 pb-3 pt-2" id="batchBody${blockId}" style="display:none;">
              <div class="row g-2">
                <div class="col-12">
                  <div class="form-floating">
                    <input type="text" class="form-control form-control-sm batch-sub-texto"
                           id="batchSubTexto${blockId}"
                           placeholder="Texto de la sub-pregunta"
                           oninput="PREGUNTAS.batchUpdateSubTitle('${blockId}','${label}')">
                    <label>${label} — Texto de la pregunta <span class="text-danger">*</span></label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating">
                    <input type="number" class="form-control form-control-sm" id="batchSubOrden${blockId}" value="${ordenGlobal}" min="1">
                    <label>Orden</label>
                  </div>
                </div>
                <div class="col-sm-9 d-flex align-items-center gap-3">
                  <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="batchSubHab${blockId}" checked>
                    <label class="form-check-label small" for="batchSubHab${blockId}">Habilitada</label>
                  </div>
                  <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="batchSubViz${blockId}" checked>
                    <label class="form-check-label small" for="batchSubViz${blockId}">Visible</label>
                  </div>
                </div>
              </div>
            </div>
          </div>`;
        container.append(block);
      }

      // Botón para añadir sub-pregunta extra al grupo
      var addBtnHtml = `
        <div class="ms-3 mb-3" id="batchAddBtn${encId}">
          <button type="button" class="btn btn-sm btn-outline-primary w-100"
                  style="border-radius:10px;border-style:dashed;font-size:.82rem;"
                  onclick="PREGUNTAS.batchAddSubPregunta('${encId}')">
            <i class="fas fa-plus me-1"></i> Añadir pregunta al enunciado ${encId}
          </button>
        </div>`;
      container.append(addBtnHtml);
    });

    $('#batchStep1').hide();
    $('#batchStep2').show();
    $('#batchFooter').show();
    PREGUNTAS.batchUpdateProgress();
  },

  batchToggleBlockById: function (blockId) {
    var body    = $('#batchBody' + blockId);
    var chevron = $('#batchChevron' + blockId);
    if (body.is(':visible')) {
      body.slideUp(180);
      chevron.removeClass('fa-chevron-down').addClass('fa-chevron-right');
    } else {
      body.slideDown(180);
      chevron.removeClass('fa-chevron-right').addClass('fa-chevron-down');
    }
  },

  batchCollapseAll: function () {
    $('.batch-block-body').slideUp(180);
    $('.batch-chevron').removeClass('fa-chevron-down').addClass('fa-chevron-right');
  },

  batchExpandAll: function () {
    $('.batch-block-body').slideDown(180);
    $('.batch-chevron').removeClass('fa-chevron-right').addClass('fa-chevron-down');
  },

  batchUpdateSubTitle: function (blockId, label) {
    var texto    = $('#batchSubTexto' + blockId).val().trim();
    var titleEl  = $('#batchSubTitle' + blockId);
    var statusEl = $('#batchSubStatus' + blockId);
    if (texto.length > 0) {
      titleEl.text(label + ' — ' + (texto.length > 55 ? texto.substring(0, 55) + '…' : texto));
      titleEl.removeClass('text-muted').addClass('text-dark');
      statusEl.removeClass('bg-light text-muted').addClass('bg-success text-white').text('✓');
    } else {
      titleEl.text('Sub-pregunta ' + label);
      titleEl.addClass('text-muted').removeClass('text-dark');
      statusEl.addClass('bg-light text-muted').removeClass('bg-success text-white').text('');
    }
    PREGUNTAS.batchUpdateProgress();
  },

  batchUpdateProgress: function () {
    var total = $('.batch-question-block').length;
    var completas = 0;
    $('.batch-sub-texto').each(function () {
      if ($(this).val().trim() !== '') completas++;
    });
    $('#batchProgress').text(completas + ' de ' + total + ' completas');
  },

  // Añadir una sub-pregunta extra a un grupo ya generado
  batchAddSubPregunta: function (encId) {
    // Contar cuántos bloques ya tiene este grupo para calcular la próxima letra y orden
    var existentes = $('.batch-question-block[data-enc-id="' + encId + '"]').length;
    var letras  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var letra   = letras[existentes] || (existentes + 1);
    var prefix  = $('#batchPrefix' + encId).val() || encId;
    var label   = prefix + letra;
    var blockId = encId + '_dyn_' + Date.now(); // ID único

    // Calcular orden global máximo actual + 1
    var maxOrden = 0;
    $('.batch-question-block input[type="number"]').each(function () {
      var v = parseInt($(this).val(), 10) || 0;
      if (v > maxOrden) maxOrden = v;
    });
    var ordenNuevo = maxOrden + 1;

    var block = `
      <div class="batch-question-block card mb-1 ms-3" data-blockid="${blockId}" data-enc-id="${encId}"
           style="border-radius:10px;border:1px solid rgba(15,23,42,.08);box-shadow:0 2px 8px rgba(2,6,23,.05);overflow:hidden;">
        <div class="batch-block-header d-flex align-items-center justify-content-between px-3 py-2"
             style="background:rgba(255,255,255,.95);cursor:pointer;user-select:none;"
             onclick="PREGUNTAS.batchToggleBlockById('${blockId}')">
          <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background:rgba(32,66,127,.10);color:#20427F;border:1px solid rgba(32,66,127,.18);border-radius:6px;font-weight:900;min-width:36px;text-align:center;font-size:.8rem;">${label}</span>
            <span class="fw-semibold text-muted small batch-sub-title" id="batchSubTitle${blockId}">Sub-pregunta ${label}</span>
            <span class="badge bg-light text-muted border" id="batchSubStatus${blockId}" style="font-size:.68rem;border-radius:999px;"></span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:6px;padding:1px 7px;font-size:.75rem;"
                    onclick="event.stopPropagation(); $(this).closest('.batch-question-block').remove(); PREGUNTAS.batchUpdateProgress();">
              <i class="fas fa-times"></i>
            </button>
            <i class="fas fa-chevron-right batch-chevron" id="batchChevron${blockId}" style="color:#20427F;transition:transform .25s;font-size:.78rem;"></i>
          </div>
        </div>
        <div class="batch-block-body px-3 pb-3 pt-2" id="batchBody${blockId}" style="display:none;">
          <div class="row g-2">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control form-control-sm batch-sub-texto"
                       id="batchSubTexto${blockId}"
                       placeholder="Texto de la sub-pregunta"
                       oninput="PREGUNTAS.batchUpdateSubTitle('${blockId}','${label}')">
                <label>${label} — Texto de la pregunta <span class="text-danger">*</span></label>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-floating">
                <input type="number" class="form-control form-control-sm" id="batchSubOrden${blockId}" value="${ordenNuevo}" min="1">
                <label>Orden</label>
              </div>
            </div>
            <div class="col-sm-9 d-flex align-items-center gap-3">
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="batchSubHab${blockId}" checked>
                <label class="form-check-label small" for="batchSubHab${blockId}">Habilitada</label>
              </div>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="batchSubViz${blockId}" checked>
                <label class="form-check-label small" for="batchSubViz${blockId}">Visible</label>
              </div>
            </div>
          </div>
        </div>
      </div>`;

    // Insertar antes del botón "Añadir"
    $('#batchAddBtn' + encId).before(block);
    // Abrir automáticamente el nuevo bloque
    PREGUNTAS.batchToggleBlockById(blockId);
    PREGUNTAS.batchUpdateProgress();
  },

  // Mostrar/ocultar editor de opciones del enunciado
  batchToggleEditOpciones: function (encId) {
    var editDiv = $('#batchEncOpcionesEdit' + encId);
    editDiv.is(':visible') ? editDiv.slideUp(150) : editDiv.slideDown(150);
  },

  // Actualizar los chips visuales cuando cambia el input de opciones
  batchRefreshOpcionesChips: function (encId) {
    var raw = $('#batchEncOpcionesInput' + encId).val().trim();
    var arr = raw !== '' ? raw.split(/[\n,]+/).map(function(o){ return o.trim(); }).filter(function(o){ return o !== ''; }) : [];
    var chipsHtml = arr.map(function(o) {
      return `<span style="display:inline-flex;align-items:center;background:#e8edf7;color:#20427F;border:1px solid rgba(32,66,127,.2);border-radius:6px;padding:2px 10px;font-size:.8rem;font-weight:700;">${PREGUNTAS.htmlspecialchars(o)}</span>`;
    }).join('');
    $('#batchEncOpciones' + encId).html(chipsHtml);
  },

  // Obtener opciones de un enunciado dado su encId
  batchGetOpcionesByEncId: function (encId) {
    var raw = $('#batchEncOpcionesInput' + encId).val().trim();
    if (raw === '') return [];
    return raw.split(/[\n,]+/).map(function(o){ return o.trim(); }).filter(function(o){ return o !== ''; });
  },

  batchGoBack: function () {
    $('#batchStep2').hide();
    $('#batchFooter').hide();
    $('#batchStep1').show();
  },

  /* --- Guardado: mismo endpoint preguntasavebatch --- */

  batchSave: function () {
    var encuestaId = getFichaEncuestaId();
    var preguntas  = [];

    $('.batch-question-block').each(function () {
      var blockId = $(this).data('blockid');
      var encId   = $(this).data('enc-id');
      var texto   = $('#batchSubTexto' + blockId).val().trim();
      if (texto === '') return; // saltar vacías

      // Enunciado y tipo compartidos del grupo
      var enunciado       = $('#batchEncTexto' + encId).val().trim();
      var numeral         = $('#batchEncNumeral' + encId).val().trim();
      var texto_adicional = $('#batchEncTextoAdicional' + encId).val().trim();
      var capId           = $('#batchEncCard' + encId).data('cap-id');
      var capitulo        = capId ? $('#batchCapTexto' + capId).val().trim() : '';
      var tipo            = $('#batchEncTipo' + encId).val();

      // Opciones compartidas del enunciado
      var opciones = PREGUNTAS.batchGetOpcionesByEncId(encId);

      preguntas.push({
        tbl_ficha_tecnica_encuesta_id: encuestaId,
        texto_pregunta:     texto,
        enunciado_pregunta: enunciado,
        numeral:            numeral,
        texto_adicional:    texto_adicional,
        capitulo:           capitulo,
        tipo_pregunta:      tipo,
        orden:              $('#batchSubOrden' + blockId).val(),
        habilitado:         $('#batchSubHab' + blockId).is(':checked') ? 'si' : 'no',
        visualizacion:      $('#batchSubViz' + blockId).is(':checked') ? 'si' : 'no',
        opciones:           opciones
      });
    });

    if (preguntas.length === 0) {
      UTIL.mostrarMensajeValidacion('No hay preguntas con texto para guardar.');
      return;
    }

    Swal.fire({
      title: '¿Guardar ' + preguntas.length + ' preguntas?',
      text: 'Se registrarán todas las preguntas diligenciadas.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, guardar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (!result.value) return;

      UTIL.cursorBusy();
      $.ajax({
        url: 'admin/ajax/rqst.php',
        type: 'POST',
        dataType: 'json',
        data: { op: 'preguntasavebatch', preguntas: JSON.stringify(preguntas) },
        success: function (data) {
          UTIL.cursorNormal();
          if (data.output.valid) {
            bootstrap.Modal.getInstance(document.getElementById('batchModal')).hide();
            UTIL.mostrarMensajeExitoso(data.output.response);
            setTimeout(function () { window.location.reload(); }, 2000);
          } else {
            UTIL.mostrarMensajeError(
              (data.output.response && data.output.response.content)
                ? data.output.response.content
                : 'Error al guardar.'
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

  reasignarGrupo: function (ids) {
    if (FICHA_ID_LOCKED > 0) {
      UTIL.mostrarMensajeValidacion('La reasignación no está disponible en el editor de una ficha.');
      return;
    }
    $('#reasignarIds').val(ids);
    $('#reasignarFichaId').val('');
    $('#reasignarSubtitle').text(ids.split(',').length + ' pregunta(s) se moverán a la ficha seleccionada');
    new bootstrap.Modal(document.getElementById('reasignarModal')).show();
  },

  confirmarReasignar: function () {
    var ids     = $('#reasignarIds').val();
    var fichaId = $('#reasignarFichaId').val();

    if (!fichaId) {
      UTIL.mostrarMensajeValidacion('Selecciona una ficha técnica destino.');
      return;
    }

    UTIL.cursorBusy();
    $.ajax({
      url: 'admin/ajax/rqst.php',
      type: 'POST',
      dataType: 'json',
      data: { op: 'preguntareasignar', ids: ids, ficha_id: fichaId },
      success: function (data) {
        UTIL.cursorNormal();
        bootstrap.Modal.getInstance(document.getElementById('reasignarModal')).hide();
        if (data.output.valid) {
          UTIL.mostrarMensajeExitoso(data.output.response);
          setTimeout(function () { window.location.reload(); }, 1800);
        } else {
          UTIL.mostrarMensajeError(
            (data.output.response && data.output.response.content)
              ? data.output.response.content : 'Error al reasignar.'
          );
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError('Error de comunicación con el servidor.');
      }
    });
  },

  editFichaTemas: function (fichaId, temasActuales) {
    $('#editFichaTemasId').val(fichaId);
    $('#editFichaTemasTexto').val(temasActuales);
    new bootstrap.Modal(document.getElementById('editFichaTemasModal')).show();
  },

  saveFichaTemas: function () {
    var fichaId = $('#editFichaTemasId').val();
    var temas   = $('#editFichaTemasTexto').val().trim();

    if (!temas) {
      UTIL.mostrarMensajeValidacion('El campo Temas Concretos no puede estar vacío.');
      return;
    }

    UTIL.cursorBusy();
    $.ajax({
      url: 'admin/ajax/rqst.php',
      type: 'POST',
      dataType: 'json',
      data: { op: 'fichaTecnicaEncuestaupdatetemas', id: fichaId, temas_concretos: temas },
      success: function (data) {
        UTIL.cursorNormal();
        bootstrap.Modal.getInstance(document.getElementById('editFichaTemasModal')).hide();
        if (data.output.valid) {
          UTIL.mostrarMensajeExitoso('Temas concretos actualizados correctamente.');
          setTimeout(function () { window.location.reload(); }, 1500);
        } else {
          UTIL.mostrarMensajeError(
            (data.output.response && data.output.response.content)
              ? data.output.response.content : 'Error al actualizar.'
          );
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError('Error de comunicación con el servidor.');
      }
    });
  },

  /* ── Editar numeral/texto_adicional: un solo registro ── */
  editNumeralAdicionalUno: function (id, numeral, textoAdicional) {
    $('#editNumeralAdicionalIds').val(id);
    $('#editNumeralInput').val(numeral);
    $('#editTextoAdicionalInput').val(textoAdicional);
    $('#editNumeralAdicionalSubtitle').text('Editando 1 pregunta');
    $('#editNumeralAdicionalMasivoInfo').hide();
    new bootstrap.Modal(document.getElementById('editNumeralAdicionalModal')).show();
  },

  /* ── Editar numeral/texto_adicional: solo con checkboxes seleccionados ── */
  editNumeralAdicionalMasivo: function (fichaId) {
    var ids = [];
    // Solo recoger checkboxes marcados dentro del grupo
    $('input.pregunta-chk').filter(function () {
      return $(this).data('grupo').toString().split('_')[0] === fichaId.toString() && $(this).is(':checked');
    }).each(function () {
      ids.push($(this).val());
    });

    if (ids.length === 0) {
      UTIL.mostrarMensajeValidacion('Seleccione al menos una pregunta usando los checkboxes para editar numeral/texto adicional.');
      return;
    }

    $('#editNumeralAdicionalIds').val(ids.join(','));
    $('#editNumeralInput').val('');
    $('#editTextoAdicionalInput').val('');
    $('#editNumeralAdicionalSubtitle').text('Edición en lote (' + ids.length + ' preguntas)');
    $('#editNumeralAdicionalMasivoCount').text('Se aplicará a ' + ids.length + ' pregunta(s) seleccionada(s).');
    $('#editNumeralAdicionalMasivoInfo').show();
    new bootstrap.Modal(document.getElementById('editNumeralAdicionalModal')).show();
  },

  /* ── Guardar numeral / texto_adicional ── */
  saveNumeralAdicional: function () {
    var ids            = $('#editNumeralAdicionalIds').val();
    var numeral        = $('#editNumeralInput').val().trim();
    var textoAdicional = $('#editTextoAdicionalInput').val().trim();

    if (!ids) { UTIL.mostrarMensajeValidacion('No hay preguntas seleccionadas.'); return; }

    UTIL.cursorBusy();
    $.ajax({
      url: 'admin/ajax/rqst.php', type: 'POST', dataType: 'json',
      data: { op: 'preguntaupdatenumeraladicional', ids: ids, numeral: numeral, texto_adicional: textoAdicional },
      success: function (data) {
        UTIL.cursorNormal();
        bootstrap.Modal.getInstance(document.getElementById('editNumeralAdicionalModal')).hide();
        if (data.output.valid) {
          UTIL.mostrarMensajeExitoso(data.output.response);
          setTimeout(function () { window.location.reload(); }, 1500);
        } else {
          UTIL.mostrarMensajeError(
            (data.output.response && data.output.response.content)
              ? data.output.response.content : 'Error al guardar.'
          );
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError('Error de comunicación con el servidor.');
      }
    });
  },

  renameCapitulo: function (capituloViejo, fichaId) {
    Swal.fire({
      title: 'Renombrar capítulo',
      input: 'text',
      inputLabel: 'Nuevo nombre del capítulo',
      inputValue: capituloViejo,
      inputAttributes: { maxlength: 255 },
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#20427F',
      inputValidator: function (value) {
        if (!value || !value.trim()) return 'El nombre no puede estar vacío.';
        if (value.trim() === capituloViejo) return 'El nombre es igual al actual.';
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      UTIL.cursorBusy();
      $.ajax({
        url: 'admin/ajax/rqst.php',
        method: 'POST',
        dataType: 'json',
        data: {
          op: 'preguntarenamecapitulo',
          ficha_id: fichaId,
          capitulo_viejo: capituloViejo,
          capitulo_nuevo: result.value.trim()
        },
        success: function (data) {
          UTIL.cursorNormal();
          if (data.output && data.output.valid) {
            UTIL.mostrarMensajeExitoso('Capítulo renombrado correctamente.');
            setTimeout(function () { window.location.reload(); }, 1500);
          } else {
            UTIL.mostrarMensajeError(
              (data.output && data.output.response && data.output.response.content)
                ? data.output.response.content : 'Error al renombrar.'
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

  _municipiosCache: null,
  _plantillasDetalleCache: {},

  ensureMunicipiosCatalogo: function (cb) {
    if (PREGUNTAS._municipiosCache) {
      if (cb) cb(PREGUNTAS._municipiosCache);
      return;
    }
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "municipioscatalogoget", ficha_id: getFichaEncuestaId() },
      success: function (res) {
        PREGUNTAS._municipiosCache = (res && res.output && res.output.valid) ? (res.output.response || []) : [];
        if (cb) cb(PREGUNTAS._municipiosCache);
      },
      error: function () {
        PREGUNTAS._municipiosCache = [];
        if (cb) cb([]);
      }
    });
  },

  ensurePlantillaDetalle: function (plantillaId, cb) {
    var pid = parseInt(plantillaId, 10) || 0;
    if (pid <= 0) { if (cb) cb(null); return; }
    if (PREGUNTAS._plantillasDetalleCache[pid]) {
      if (cb) cb(PREGUNTAS._plantillasDetalleCache[pid]);
      return;
    }
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "grillaplantilladetalle", id: pid },
      success: function (res) {
        var det = (res && res.output && res.output.valid) ? res.output.response : null;
        if (det) PREGUNTAS._plantillasDetalleCache[pid] = det;
        if (cb) cb(det);
      },
      error: function () { if (cb) cb(null); }
    });
  },

  populateVisibilidadQuestions: function (currentId, currentOrden, selPreguntaId, selOpcionId) {
    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var curId = parseInt(currentId, 10) || 0;
    var curOrd = parseInt(currentOrden, 10) || 999999;
    var selPid = parseInt(selPreguntaId, 10) || 0;

    var $selP = $("#visible_si_pregunta_id");
    $selP.empty().append('<option value="">-- Pregunta anterior --</option>');

    for (var i = 0; i < catalogo.length; i++) {
      var p = catalogo[i];
      var pid = parseInt(p.id, 10);
      var pOrd = parseInt(p.orden, 10) || 0;
      if (curId > 0 && pid === curId) continue;
      if (pOrd >= curOrd && pid !== selPid) continue;
      if (p.tipo_pregunta === "Grilla") continue;
      var lbl = (p.numeral ? (p.numeral + " · ") : ("Pregunta " + pOrd + " · ")) + (p.texto_pregunta || "").substring(0, 60);
      $selP.append('<option value="' + pid + '"' + (pid === selPid ? ' selected' : '') + '>' + PREGUNTAS.htmlspecialchars(lbl) + '</option>');
    }

    if (selPid > 0) {
      $("#qstModoVisibilidad").val("condicional");
      $("#qstVisibilidadCondicionalWrap").show();
      PREGUNTAS.onChangeVisibleSiPregunta(selOpcionId);
    } else {
      $("#qstModoVisibilidad").val("siempre");
      $("#qstVisibilidadCondicionalWrap").hide();
      $("#visible_si_opcion_id").empty().append('<option value="">-- Respuesta elegida --</option>');
    }
  },

  onChangeModoVisibilidad: function () {
    var modo = $("#qstModoVisibilidad").val();
    $("#qstVisibilidadCondicionalWrap").toggle(modo === "condicional");
  },

  onChangeVisibleSiPregunta: function (preselectedOpcionId) {
    var pid = parseInt($("#visible_si_pregunta_id").val(), 10) || 0;
    var $selO = $("#visible_si_opcion_id");
    $selO.empty().append('<option value="">-- Respuesta elegida --</option>');
    if (pid <= 0) return;

    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var preg = null;
    for (var i = 0; i < catalogo.length; i++) {
      if (parseInt(catalogo[i].id, 10) === pid) { preg = catalogo[i]; break; }
    }
    if (!preg || !preg.opciones) return;
    var selOid = parseInt(preselectedOpcionId, 10) || 0;
    for (var j = 0; j < preg.opciones.length; j++) {
      var o = preg.opciones[j];
      var oid = parseInt(o.id, 10);
      $selO.append('<option value="' + oid + '"' + (oid === selOid ? ' selected' : '') + '>' + PREGUNTAS.htmlspecialchars(o.texto || "") + '</option>');
    }
  },

  toggleGrillaPorMunicipio: function () {
    var porMun = $("#grilla_por_municipio").is(":checked");
    $("#qstGrillaUnicaWrap").toggle(!porMun);
    $("#qstGrillaPorMunicipioWrap").toggle(porMun);
    if (porMun && $("#qstGrillaVariantesRows .qst-var-row").length === 0) {
      PREGUNTAS.addVarianteMunicipioRow();
    }
  },

  addVarianteMunicipioRow: function (codMun, plantillaId) {
    PREGUNTAS.ensureMunicipiosCatalogo(function (municipios) {
      var munOpts = '<option value="">-- Seleccione municipio --</option>';
      for (var i = 0; i < municipios.length; i++) {
        var m = municipios[i];
        var sel = (String(codMun || "") === String(m.codigo_municipio)) ? " selected" : "";
        munOpts += '<option value="' + PREGUNTAS.htmlspecialchars(m.codigo_municipio) + '"' + sel + '>' + PREGUNTAS.htmlspecialchars(m.municipio) + ' (' + PREGUNTAS.htmlspecialchars(m.codigo_municipio) + ')</option>';
      }
      var plantillas = window.CUESTIONARIO_PLANTILLAS_GRILLA || [];
      var plOpts = '<option value="">-- Seleccione tabla de candidatos --</option>';
      for (var j = 0; j < plantillas.length; j++) {
        var pl = plantillas[j];
        var selP = (parseInt(plantillaId, 10) === parseInt(pl.id, 10)) ? " selected" : "";
        plOpts += '<option value="' + pl.id + '"' + selP + '>' + PREGUNTAS.htmlspecialchars(pl.nombre) + '</option>';
      }
      var rowHtml = '' +
        '<div class="row g-2 align-items-center mb-2 qst-var-row">' +
          '<div class="col-md-5"><select class="form-select form-select-sm qst-var-mun">' + munOpts + '</select></div>' +
          '<div class="col-md-6"><select class="form-select form-select-sm qst-var-pl">' + plOpts + '</select></div>' +
          '<div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest(\'.qst-var-row\').remove();"><i class="fas fa-trash-alt"></i></button></div>' +
        '</div>';
      $("#qstGrillaVariantesRows").append(rowHtml);
    });
  },

  populateGrillaAndListaPanels: function (res) {
    res = res || {};
    var porMun = parseInt(res.grilla_por_municipio, 10) === 1;
    $("#grilla_por_municipio").prop("checked", porMun);
    $("#tbl_grilla_plantilla_id").val(res.tbl_grilla_plantilla_id || "");
    $("#qstGrillaUnicaWrap").toggle(!porMun);
    $("#qstGrillaPorMunicipioWrap").toggle(porMun);
    $("#qstGrillaVariantesRows").empty();

    if (porMun && res.grilla_variantes && res.grilla_variantes.length) {
      for (var v = 0; v < res.grilla_variantes.length; v++) {
        PREGUNTAS.addVarianteMunicipioRow(res.grilla_variantes[v].codigo_municipio, res.grilla_variantes[v].tbl_grilla_plantilla_id);
      }
    }

    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var curId = parseInt(res.id, 10) || 0;
    var curOrd = parseInt(res.orden, 10) || getSiguienteOrden();
    var selOrigP = parseInt(res.lista_origen_pregunta_id, 10) || 0;

    var $selOrig = $("#lista_origen_pregunta_id");
    $selOrig.empty().append('<option value="">-- Seleccione pregunta de tabla --</option>');

    var $exclBox = $("#qstListaExclusionesContainer");
    $exclBox.empty();
    var exclActuales = (res.exclusiones || []).map(function (x) { return parseInt(x, 10); });
    var countExclCandidates = 0;

    for (var i = 0; i < catalogo.length; i++) {
      var p = catalogo[i];
      var pid = parseInt(p.id, 10);
      var pOrd = parseInt(p.orden, 10) || 0;
      if (curId > 0 && pid === curId) continue;
      var lbl = (p.numeral ? (p.numeral + " · ") : ("Pregunta " + pOrd + " · ")) + (p.texto_pregunta || "").substring(0, 60);

      if (p.tipo_pregunta === "Grilla") {
        $selOrig.append('<option value="' + pid + '"' + (pid === selOrigP ? ' selected' : '') + '>' + PREGUNTAS.htmlspecialchars(lbl) + '</option>');
      }
      if (p.tipo_pregunta === "Lista_desde_grilla" && (pOrd < curOrd || exclActuales.indexOf(pid) !== -1)) {
        countExclCandidates++;
        var chk = exclActuales.indexOf(pid) !== -1 ? " checked" : "";
        $exclBox.append(
          '<div class="form-check">' +
            '<input class="form-check-input qst-excl-chk" type="checkbox" value="' + pid + '" id="exclChk' + pid + '"' + chk + '>' +
            '<label class="form-check-label small" for="exclChk' + pid + '">' + PREGUNTAS.htmlspecialchars(lbl) + '</label>' +
          '</div>'
        );
      }
    }
    if (countExclCandidates === 0) {
      $exclBox.html('<span class="small text-muted">No hay preguntas anteriores de lista desde tabla para excluir.</span>');
    }

    $("#si_lista_vacia").val(res.si_lista_vacia || "omitir");

    if (selOrigP > 0) {
      PREGUNTAS.onChangeListaOrigenPregunta(res.lista_origen_columna_id, res.lista_origen_opcion_id);
    }
  },

  onChangeListaOrigenPregunta: function (preColId, preOptId) {
    var origPid = parseInt($("#lista_origen_pregunta_id").val(), 10) || 0;
    var $selCol = $("#lista_origen_columna_id");
    var $selOpt = $("#lista_origen_opcion_id");
    $selCol.empty().append('<option value="">-- Todas las filas de la tabla --</option>');
    $selOpt.empty().append('<option value="">-- Cualquier respuesta --</option>');
    if (origPid <= 0) return;

    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var pOrig = null;
    for (var i = 0; i < catalogo.length; i++) {
      if (parseInt(catalogo[i].id, 10) === origPid) { pOrig = catalogo[i]; break; }
    }
    if (!pOrig) return;

    var plId = parseInt(pOrig.tbl_grilla_plantilla_id, 10) || 0;
    if (plId <= 0 && pOrig.grilla_variantes && pOrig.grilla_variantes.length > 0) {
      plId = parseInt(pOrig.grilla_variantes[0].tbl_grilla_plantilla_id, 10) || 0;
    }
    if (plId <= 0) return;

    PREGUNTAS.ensurePlantillaDetalle(plId, function (det) {
      if (!det || !det.columnas) return;
      var selCid = parseInt(preColId, 10) || 0;
      for (var c = 0; c < det.columnas.length; c++) {
        var col = det.columnas[c];
        var cid = parseInt(col.id, 10);
        $selCol.append('<option value="' + cid + '"' + (cid === selCid ? ' selected' : '') + '>' + PREGUNTAS.htmlspecialchars(col.titulo || ("Pregunta " + (c + 1))) + '</option>');
      }
      if (selCid > 0) {
        PREGUNTAS.onChangeListaOrigenColumna(preOptId);
      }
    });
  },

  onChangeListaOrigenColumna: function (preOptId) {
    var origPid = parseInt($("#lista_origen_pregunta_id").val(), 10) || 0;
    var colId = parseInt($("#lista_origen_columna_id").val(), 10) || 0;
    var $selOpt = $("#lista_origen_opcion_id");
    $selOpt.empty().append('<option value="">-- Cualquier respuesta --</option>');
    if (origPid <= 0 || colId <= 0) return;

    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var pOrig = null;
    for (var i = 0; i < catalogo.length; i++) {
      if (parseInt(catalogo[i].id, 10) === origPid) { pOrig = catalogo[i]; break; }
    }
    if (!pOrig) return;
    var plId = parseInt(pOrig.tbl_grilla_plantilla_id, 10) || 0;
    if (plId <= 0 && pOrig.grilla_variantes && pOrig.grilla_variantes.length > 0) {
      plId = parseInt(pOrig.grilla_variantes[0].tbl_grilla_plantilla_id, 10) || 0;
    }
    PREGUNTAS.ensurePlantillaDetalle(plId, function (det) {
      if (!det || !det.columnas) return;
      var selOid = parseInt(preOptId, 10) || 0;
      for (var c = 0; c < det.columnas.length; c++) {
        if (parseInt(det.columnas[c].id, 10) === colId) {
          var opts = det.columnas[c].opciones || [];
          for (var k = 0; k < opts.length; k++) {
            var oid = parseInt(opts[k].id, 10);
            $selOpt.append('<option value="' + oid + '"' + (oid === selOid ? ' selected' : '') + '>' + PREGUNTAS.htmlspecialchars(opts[k].texto || "") + '</option>');
          }
          break;
        }
      }
    });
  },

  openVotoEscalonadoModal: function () {
    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var $sel = $("#ve_origen_pregunta_id");
    $sel.empty().append('<option value="">-- Seleccione una pregunta de tabla de candidatos --</option>');
    for (var i = 0; i < catalogo.length; i++) {
      var p = catalogo[i];
      if (p.tipo_pregunta === "Grilla") {
        var lbl = (p.numeral ? (p.numeral + " · ") : ("Pregunta " + (p.orden || p.id) + " · ")) + (p.texto_pregunta || "");
        $sel.append('<option value="' + p.id + '">' + PREGUNTAS.htmlspecialchars(lbl) + '</option>');
      }
    }
    $("#ve_origen_columna_id").empty().append('<option value="">-- Columna --</option>');
    $("#ve_origen_opcion_id").empty().append('<option value="">-- Opción --</option>');
    new bootstrap.Modal(document.getElementById("votoEscalonadoModal")).show();
  },

  onChangeVotoEscalonadoOrigen: function () {
    var origPid = parseInt($("#ve_origen_pregunta_id").val(), 10) || 0;
    var $selCol = $("#ve_origen_columna_id");
    var $selOpt = $("#ve_origen_opcion_id");
    $selCol.empty().append('<option value="">-- Columna --</option>');
    $selOpt.empty().append('<option value="">-- Opción --</option>');
    if (origPid <= 0) return;

    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var pOrig = null;
    for (var i = 0; i < catalogo.length; i++) {
      if (parseInt(catalogo[i].id, 10) === origPid) { pOrig = catalogo[i]; break; }
    }
    if (!pOrig) return;
    if (pOrig.capitulo && !$("#ve_capitulo").val()) {
      $("#ve_capitulo").val(pOrig.capitulo);
    }
    var plId = parseInt(pOrig.tbl_grilla_plantilla_id, 10) || 0;
    if (plId <= 0 && pOrig.grilla_variantes && pOrig.grilla_variantes.length > 0) {
      plId = parseInt(pOrig.grilla_variantes[0].tbl_grilla_plantilla_id, 10) || 0;
    }
    PREGUNTAS.ensurePlantillaDetalle(plId, function (det) {
      if (!det || !det.columnas) return;
      var autoColId = 0;
      for (var c = 0; c < det.columnas.length; c++) {
        var col = det.columnas[c];
        $selCol.append('<option value="' + col.id + '">' + PREGUNTAS.htmlspecialchars(col.titulo || ("Pregunta " + (c + 1))) + '</option>');
        if (!autoColId && col.rol_resultado === "intencion") {
          autoColId = col.id;
        }
      }
      if (autoColId > 0) {
        $selCol.val(autoColId);
        PREGUNTAS.onChangeVotoEscalonadoColumna();
      }
    });
  },

  onChangeVotoEscalonadoColumna: function () {
    var origPid = parseInt($("#ve_origen_pregunta_id").val(), 10) || 0;
    var colId = parseInt($("#ve_origen_columna_id").val(), 10) || 0;
    var $selOpt = $("#ve_origen_opcion_id");
    $selOpt.empty().append('<option value="">-- Opción --</option>');
    if (origPid <= 0 || colId <= 0) return;

    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var pOrig = null;
    for (var i = 0; i < catalogo.length; i++) {
      if (parseInt(catalogo[i].id, 10) === origPid) { pOrig = catalogo[i]; break; }
    }
    if (!pOrig) return;
    var plId = parseInt(pOrig.tbl_grilla_plantilla_id, 10) || 0;
    if (plId <= 0 && pOrig.grilla_variantes && pOrig.grilla_variantes.length > 0) {
      plId = parseInt(pOrig.grilla_variantes[0].tbl_grilla_plantilla_id, 10) || 0;
    }
    PREGUNTAS.ensurePlantillaDetalle(plId, function (det) {
      if (!det || !det.columnas) return;
      for (var c = 0; c < det.columnas.length; c++) {
        if (parseInt(det.columnas[c].id, 10) === colId) {
          var opts = det.columnas[c].opciones || [];
          for (var k = 0; k < opts.length; k++) {
            var sel = opts[k].cuenta_como === "si" ? " selected" : "";
            $selOpt.append('<option value="' + opts[k].id + '"' + sel + '>' + PREGUNTAS.htmlspecialchars(opts[k].texto || "") + '</option>');
          }
          break;
        }
      }
    });
  },

  saveVotoEscalonado: function () {
    var origPid = parseInt($("#ve_origen_pregunta_id").val(), 10) || 0;
    var colId = parseInt($("#ve_origen_columna_id").val(), 10) || 0;
    var optId = parseInt($("#ve_origen_opcion_id").val(), 10) || 0;
    if (origPid <= 0 || colId <= 0 || optId <= 0) {
      UTIL.mostrarMensajeValidacion("Seleccione la pregunta de tabla, la columna filtro y la respuesta que aprueba.");
      return;
    }
    var pasos = [];
    for (var n = 1; n <= 3; n++) {
      var fijas = [];
      var fijasRaw = ($("#ve_fijas_" + n).val() || "").split(",");
      for (var i = 0; i < fijasRaw.length; i++) {
        var t = fijasRaw[i].trim();
        if (t) fijas.push(t);
      }
      pasos.push({
        numeral: $("#ve_numeral_" + n).val(),
        texto_pregunta: $("#ve_texto_" + n).val(),
        opciones_fijas: fijas
      });
    }

    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "preguntavotoescalonadosave",
        tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId(),
        lista_origen_pregunta_id: origPid,
        lista_origen_columna_id: colId,
        lista_origen_opcion_id: optId,
        capitulo: $("#ve_capitulo").val(),
        pasos: JSON.stringify(pasos)
      },
      success: function (res) {
        UTIL.cursorNormal();
        if (res && res.output && res.output.valid) {
          UTIL.mostrarMensajeExitoso("Se crearon las 3 preguntas conectadas de voto escalonado.");
          setTimeout(function () { window.location.reload(); }, 1200);
        } else {
          UTIL.mostrarMensajeError((res && res.output && res.output.response && res.output.response.content) || "Error al crear voto escalonado.");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
      }
    });
  },

  openMapaModal: function () {
    var modalEl = document.getElementById("mapaCuestionarioModal");
    if (!modalEl.classList.contains("show")) {
      new bootstrap.Modal(modalEl).show();
    }
    $("#mapaValidadorAlertas").html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Analizando el flujo y validando el cuestionario...</div>');
    $("#mapaFlujoContainer").empty();

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "cuestionariomapaget", tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId() },
      success: function (res) {
        if (!res || !res.output || !res.output.valid) {
          $("#mapaValidadorAlertas").html('<div class="alert alert-danger">No se pudo cargar el mapa del cuestionario.</div>');
          return;
        }
        var data = res.output.response || {};
        var val = data.validacion || {};
        var alertas = val.alertas || [];
        var mapa = data.pasos || [];

        if (alertas.length === 0) {
          $("#mapaValidadorAlertas").html(
            '<div class="alert alert-success d-flex align-items-center mb-0">' +
              '<i class="fas fa-check-circle fa-lg me-3"></i>' +
              '<div><strong>¡Cuestionario válido y consistente!</strong> No se detectaron saltos rotos, preguntas huérfanas ni textos pendientes.</div>' +
            '</div>'
          );
        } else {
          var aHtml = '<div class="alert alert-warning mb-0"><h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Atención: se encontraron ' + alertas.length + ' observación(es)</h6><ul class="mb-0 small">';
          for (var a = 0; a < alertas.length; a++) {
            aHtml += '<li>' + PREGUNTAS.htmlspecialchars(alertas[a].mensaje) + '</li>';
          }
          aHtml += '</ul></div>';
          $("#mapaValidadorAlertas").html(aHtml);
        }

        var mHtml = '<div class="list-group">';
        for (var i = 0; i < mapa.length; i++) {
          var item = mapa[i];
          var badges = '';
          if (item.es_filtro) badges += '<span class="badge bg-danger ms-2">FILTRO</span>';
          if (item.condicion_visibilidad && item.condicion_visibilidad !== "Siempre se muestra") badges += '<span class="badge bg-info text-dark ms-2">' + PREGUNTAS.htmlspecialchars(item.condicion_visibilidad) + '</span>';
          if (item.resumen_grilla_lista) badges += '<span class="badge bg-primary ms-2">' + PREGUNTAS.htmlspecialchars(item.resumen_grilla_lista) + '</span>';

          var reglasHtml = '';
          if (item.reglas_opciones && item.reglas_opciones.length > 0) {
            reglasHtml = '<div class="mt-1 small text-muted">';
            for (var r = 0; r < item.reglas_opciones.length; r++) {
              reglasHtml += '<span class="d-inline-block me-3"><i class="fas fa-arrow-right text-primary me-1"></i>' + PREGUNTAS.htmlspecialchars(item.reglas_opciones[r].resumen) + '</span>';
            }
            reglasHtml += '</div>';
          }

          mHtml += '' +
            '<div class="list-group-item">' +
              '<div class="d-flex align-items-center justify-content-between flex-wrap">' +
                '<div>' +
                  '<span class="badge rounded-pill bg-light text-dark border me-2">#' + (item.orden || (i + 1)) + '</span>' +
                  (item.numeral ? '<span class="badge bg-dark me-2">' + PREGUNTAS.htmlspecialchars(item.numeral) + '</span>' : '') +
                  '<strong>' + PREGUNTAS.htmlspecialchars(item.texto_pregunta || "") + '</strong>' +
                  badges +
                '</div>' +
                '<button type="button" class="btn btn-xs btn-outline-primary" onclick="bootstrap.Modal.getInstance(document.getElementById(\'mapaCuestionarioModal\')).hide(); PREGUNTAS.editData(' + item.id + ');">' +
                  '<i class="fas fa-pen me-1"></i>Editar' +
                '</button>' +
              '</div>' +
              reglasHtml +
            '</div>';
        }
        mHtml += '</div>';
        $("#mapaFlujoContainer").html(mHtml);
      }
    });
  },

  openConfigEncuestaModal: function () {
    var catalogo = window.CUESTIONARIO_PREGUNTAS_CATALOGO || [];
    var $selDest = $("#cfg_pregunta_destacada_id");
    $selDest.empty().append('<option value="">-- Automática (primera pregunta activa que no sea filtro) --</option>');
    for (var i = 0; i < catalogo.length; i++) {
      var p = catalogo[i];
      if (parseInt(p.es_filtro, 10) === 1) continue;
      var lbl = (p.numeral ? (p.numeral + " · ") : ("Pregunta " + (p.orden || p.id) + " · ")) + (p.texto_pregunta || "").substring(0, 65);
      $selDest.append('<option value="' + p.id + '">' + PREGUNTAS.htmlspecialchars(lbl) + '</option>');
    }

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "cuestionarioconfigget", tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId() },
      success: function (res) {
        if (res && res.output && res.output.valid) {
          var c = res.output.response || {};
          $("#cfg_pedir_municipio_zona_inicio").prop("checked", parseInt(c.pedir_municipio_zona_inicio, 10) !== 0);
          $("#cfg_audio_obligatorio").prop("checked", parseInt(c.audio_obligatorio, 10) !== 0);
          $("#cfg_certificar_terminadas_filtro").prop("checked", parseInt(c.certificar_terminadas_filtro, 10) !== 0);
          $("#cfg_mostrar_resultados_publicos").prop("checked", parseInt(c.mostrar_resultados_publicos, 10) !== 0);
          $("#cfg_bloquear_cuota_completa").prop("checked", parseInt(c.bloquear_cuota_completa, 10) === 1);
          $("#cfg_mensaje_cierre_defecto").val(c.mensaje_cierre_defecto || "");
          $("#cfg_pregunta_destacada_id").val(c.pregunta_destacada_id || "");
        }
        new bootstrap.Modal(document.getElementById("configEncuestaModal")).show();
      }
    });
  },

  saveConfigEncuesta: function () {
    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "cuestionarioconfigsave",
        tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId(),
        pedir_municipio_zona_inicio: $("#cfg_pedir_municipio_zona_inicio").is(":checked") ? 1 : 0,
        audio_obligatorio: $("#cfg_audio_obligatorio").is(":checked") ? 1 : 0,
        certificar_terminadas_filtro: $("#cfg_certificar_terminadas_filtro").is(":checked") ? 1 : 0,
        mostrar_resultados_publicos: $("#cfg_mostrar_resultados_publicos").is(":checked") ? 1 : 0,
        bloquear_cuota_completa: $("#cfg_bloquear_cuota_completa").is(":checked") ? 1 : 0,
        mensaje_cierre_defecto: $("#cfg_mensaje_cierre_defecto").val() || "",
        pregunta_destacada_id: $("#cfg_pregunta_destacada_id").val() || ""
      },
      success: function (res) {
        UTIL.cursorNormal();
        if (res && res.output && res.output.valid) {
          bootstrap.Modal.getInstance(document.getElementById("configEncuestaModal")).hide();
          UTIL.mostrarMensajeExitoso("Opciones de la encuesta guardadas correctamente.");
        } else {
          UTIL.mostrarMensajeError("No fue posible guardar las opciones.");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
      }
    });
  },

  openZonasMuestreoModal: function () {
    PREGUNTAS.ensureMunicipiosCatalogo(function (municipios) {
      var $selM = $("#zonasMasivoMunicipio");
      $selM.empty().append('<option value="">-- Seleccione municipio --</option>');
      for (var i = 0; i < municipios.length; i++) {
        $selM.append('<option value="' + PREGUNTAS.htmlspecialchars(municipios[i].codigo_municipio) + '">' + PREGUNTAS.htmlspecialchars(municipios[i].municipio) + '</option>');
      }

      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: { op: "zonasmuestreoget", tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId() },
        success: function (res) {
          $("#zonasMuestreoTbody").empty();
          var rows = (res && res.output && res.output.valid) ? (res.output.response || []) : [];
          for (var r = 0; r < rows.length; r++) {
            PREGUNTAS.addZonaMuestreoRow(rows[r]);
          }
          if (rows.length === 0) {
            PREGUNTAS.addZonaMuestreoRow();
          }
          PREGUNTAS.recalcZonasTotalMeta();
          new bootstrap.Modal(document.getElementById("zonasMuestreoModal")).show();
        }
      });
    });
  },

  addZonaMuestreoRow: function (row) {
    row = row || {};
    var municipios = PREGUNTAS._municipiosCache || [];
    var munOpts = '<option value="">-- Seleccione municipio --</option>';
    for (var i = 0; i < municipios.length; i++) {
      var m = municipios[i];
      var sel = (String(row.codigo_municipio || "") === String(m.codigo_municipio)) ? " selected" : "";
      munOpts += '<option value="' + PREGUNTAS.htmlspecialchars(m.codigo_municipio) + '"' + sel + '>' + PREGUNTAS.htmlspecialchars(m.municipio) + '</option>';
    }
    var tipos = { comuna: "Comuna", sector: "Sector", corregimiento: "Corregimiento", zona: "Zona" };
    var tipoOpts = "";
    Object.keys(tipos).forEach(function (t) {
      tipoOpts += '<option value="' + t + '"' + ((row.tipo || "comuna") === t ? " selected" : "") + '>' + tipos[t] + '</option>';
    });
    var tr = '' +
      '<tr class="zona-row" data-id="' + (parseInt(row.id, 10) || "") + '">' +
        '<td><select class="form-select zona-mun" onchange="PREGUNTAS.recalcZonasTotalMeta()">' + munOpts + '</select></td>' +
        '<td><select class="form-select zona-tipo">' + tipoOpts + '</select></td>' +
        '<td><input type="text" class="form-control zona-nombre" placeholder="Ej: Comuna 1, Zona Norte..." value="' + PREGUNTAS.htmlspecialchars(row.nombre || "") + '"></td>' +
        '<td><input type="text" class="form-control zona-desc" placeholder="Ej: barrios que la componen" value="' + PREGUNTAS.htmlspecialchars(row.descripcion || "") + '"></td>' +
        '<td><input type="number" class="form-control zona-cuota" min="0" value="' + (parseInt(row.cuota, 10) || 0) + '" oninput="PREGUNTAS.recalcZonasTotalMeta()"></td>' +
        '<td class="text-center"><input type="checkbox" class="form-check-input zona-activo" style="width:1.6em;height:1.6em;" ' + (row.habilitado === "no" ? "" : "checked") + '></td>' +
        '<td class="text-center"><button type="button" class="btn btn-outline-danger" title="Quitar esta zona" onclick="PREGUNTAS.quitarZonaMuestreoRow(this)"><i class="fas fa-trash-alt"></i></button></td>' +
      '</tr>';
    $("#zonasMuestreoTbody").append(tr);
  },

  quitarZonaMuestreoRow: function (btn) {
    var $tr = $(btn).closest("tr");
    var zid = parseInt($tr.attr("data-id"), 10) || 0;
    if (zid <= 0) {
      $tr.remove();
      PREGUNTAS.recalcZonasTotalMeta();
      return;
    }
    var nombre = $tr.find(".zona-nombre").val() || "esta zona";
    Swal.fire({
      title: "¿Quitar esta zona?",
      text: "Se quitará «" + nombre + "». Las encuestas ya hechas en ella no se borran.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, quitar",
      cancelButtonText: "No, dejarla",
      reverseButtons: true
    }).then(function (result) {
      if (!result.value) return;
      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: { op: "zonasmuestreodelete", id: zid, tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId() },
        success: function (res) {
          if (res && res.output && res.output.valid) {
            $tr.remove();
            PREGUNTAS.recalcZonasTotalMeta();
          } else {
            UTIL.mostrarMensajeError("No fue posible quitar la zona.");
          }
        }
      });
    });
  },

  aplicarPegadoZonas: function () {
    var codMun = $("#zonasMasivoMunicipio").val();
    var cuotaDef = parseInt($("#zonasMasivoCuotaDefault").val(), 10) || 0;
    var raw = ($("#zonasMasivoTextarea").val() || "").trim();
    if (!codMun) {
      UTIL.mostrarMensajeValidacion("Seleccione el municipio para las zonas que va a pegar.");
      return;
    }
    if (!raw) return;
    var lines = raw.split(/\r?\n/);
    for (var i = 0; i < lines.length; i++) {
      var ln = lines[i].trim();
      if (!ln) continue;
      var parts = ln.split("|");
      var nombre = parts[0].trim();
      var cuota = parts.length > 1 ? (parseInt(parts[1].trim(), 10) || cuotaDef) : cuotaDef;
      PREGUNTAS.addZonaMuestreoRow({ codigo_municipio: codMun, nombre: nombre, cuota: cuota, habilitado: "si" });
    }
    $("#zonasMasivoTextarea").val("");
    $("#zonasPegadoMasivoBox").slideUp(150);
    PREGUNTAS.recalcZonasTotalMeta();
  },

  agregarMunicipioSinZonas: function () {
    var codMun = $("#zonasMasivoMunicipio").val();
    var cuotaDef = parseInt($("#zonasMasivoCuotaDefault").val(), 10) || 0;
    if (!codMun) {
      UTIL.mostrarMensajeValidacion("Seleccione primero el municipio.");
      return;
    }
    var yaTiene = false;
    $("#zonasMuestreoTbody .zona-mun").each(function () {
      if ($(this).val() === codMun) yaTiene = true;
    });
    if (yaTiene) {
      UTIL.mostrarMensajeValidacion("Ese municipio ya tiene filas en la tabla. Quítelas antes de usar «Todo el municipio».");
      return;
    }
    PREGUNTAS.addZonaMuestreoRow({ codigo_municipio: codMun, tipo: "zona", nombre: "Todo el municipio", cuota: cuotaDef, habilitado: "si" });
    $("#zonasPegadoMasivoBox").slideUp(150);
    PREGUNTAS.recalcZonasTotalMeta();
  },

  recalcZonasTotalMeta: function () {
    var total = 0;
    var porMun = {};
    var ordenMun = [];
    $("#zonasMuestreoTbody .zona-row").each(function () {
      var $tr = $(this);
      var cuota = parseInt($tr.find(".zona-cuota").val(), 10) || 0;
      var cod = $tr.find(".zona-mun").val();
      total += cuota;
      if (!cod) return;
      if (!porMun[cod]) {
        porMun[cod] = { nombre: $tr.find(".zona-mun option:selected").text(), zonas: 0, meta: 0 };
        ordenMun.push(cod);
      }
      porMun[cod].zonas++;
      porMun[cod].meta += cuota;
    });
    $("#zonasTotalMetaLabel").text("Meta total: " + total + " encuestas");

    var html = "";
    for (var i = 0; i < ordenMun.length; i++) {
      var m = porMun[ordenMun[i]];
      html += '<span class="badge bg-light text-dark border fw-normal px-3 py-2" style="font-size:1rem;">' + PREGUNTAS.htmlspecialchars(m.nombre) +
        ': <strong>' + m.meta + '</strong> (' + m.zonas + (m.zonas === 1 ? ' zona' : ' zonas') + ')</span>';
    }
    $("#zonasResumenMunicipios").html(html);

    var esperada = parseInt($("#zonasMetaEsperada").val(), 10) || 0;
    var $aviso = $("#zonasAvisoMeta");
    if (esperada > 0 && esperada !== total) {
      $aviso.text("La suma de las metas (" + total + ") no coincide con la meta que espera (" + esperada + "). Puede guardar igual.").removeClass("d-none");
    } else {
      $aviso.addClass("d-none").text("");
    }
  },

  saveZonasMuestreo: function () {
    var zonas = [];
    $("#zonasMuestreoTbody .zona-row").each(function () {
      var $tr = $(this);
      var cod = $tr.find(".zona-mun").val();
      var nom = ($tr.find(".zona-nombre").val() || "").trim();
      if (!cod || !nom) return;
      zonas.push({
        id: parseInt($tr.attr("data-id"), 10) || 0,
        codigo_municipio: cod,
        tipo: $tr.find(".zona-tipo").val() || "comuna",
        nombre: nom,
        descripcion: ($tr.find(".zona-desc").val() || "").trim(),
        cuota: parseInt($tr.find(".zona-cuota").val(), 10) || 0,
        habilitado: $tr.find(".zona-activo").is(":checked") ? "si" : "no"
      });
    });

    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "zonasmuestreosave",
        tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId(),
        zonas: JSON.stringify(zonas)
      },
      success: function (res) {
        UTIL.cursorNormal();
        if (res && res.output && res.output.valid) {
          bootstrap.Modal.getInstance(document.getElementById("zonasMuestreoModal")).hide();
          UTIL.mostrarMensajeExitoso("Zonas y metas guardadas correctamente.");
        } else {
          UTIL.mostrarMensajeError("Error al guardar las zonas.");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
      }
    });
  },

  copiarZonasDeOtraEncuesta: function () {
    var origFicha = parseInt($("#zonasCopiarFichaOrigen").val(), 10) || 0;
    if (origFicha <= 0) {
      UTIL.mostrarMensajeValidacion("Seleccione la encuesta de origen.");
      return;
    }
    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "zonasmuestreocopiar",
        ficha_origen_id: origFicha,
        ficha_destino_id: getFichaEncuestaId()
      },
      success: function (res) {
        UTIL.cursorNormal();
        if (res && res.output && res.output.valid) {
          UTIL.mostrarMensajeExitoso("Zonas copiadas. Recargando lista...");
          PREGUNTAS.openZonasMuestreoModal();
        } else {
          UTIL.mostrarMensajeError((res && res.output && res.output.response && res.output.response.content) || "No se pudieron copiar las zonas.");
        }
      }
    });
  },

  openCrucesEquivalenciasModal: function () {
    var $c = $("#crucesEquivalenciasContainer");
    $c.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Cargando...</div>');
    new bootstrap.Modal(document.getElementById("crucesEquivalenciasModal")).show();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "equivalenciasvotanteget", tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId() },
      success: function (res) {
        var eqs = (res && res.output && res.output.valid) ? (res.output.response || []) : [];
        var eqPorOpcion = {};
        for (var e = 0; e < eqs.length; e++) {
          eqPorOpcion[eqs[e].tbl_opcion_respuesta_id] = eqs[e].valor_votante;
        }
        var campos = window.CUESTIONARIO_CAMPOS_VOTANTE || {};
        var catalogo = (window.CUESTIONARIO_PREGUNTAS_CATALOGO || []).filter(function (p) {
          return p.tipo_pregunta !== "Grilla" && p.tipo_pregunta !== "Lista_desde_grilla" && (p.opciones || []).length > 0;
        });
        $c.empty();
        if (catalogo.length === 0) {
          $c.html('<div class="alert alert-info mb-0">Esta encuesta todavía no tiene preguntas con opciones de respuesta.</div>');
          return;
        }
        $c.append('<p class="mb-3">Marque las preguntas que quiere usar para <strong>comparar resultados</strong> (por ejemplo sexo, edad o estrato). Si además quiere que la respuesta quede en la ficha de la persona encuestada, elija a qué dato corresponde.</p>');
        for (var i = 0; i < catalogo.length; i++) {
          var p = catalogo[i];
          var esCruce = parseInt(p.es_variable_cruce, 10) === 1;
          var campoSel = p.copiar_a_votante_campo || "";
          var campoOpts = '<option value="">No copiar a la ficha de la persona</option>';
          Object.keys(campos).forEach(function (k) {
            campoOpts += '<option value="' + k + '"' + (campoSel === k ? " selected" : "") + '>' + PREGUNTAS.htmlspecialchars(campos[k].etiqueta) + '</option>';
          });
          var card = '' +
            '<div class="card mb-3 border eq-card" data-pregunta-id="' + p.id + '">' +
              '<div class="card-header bg-light py-3">' +
                '<div class="form-check mb-2">' +
                  '<input class="form-check-input eq-es-cruce" type="checkbox" id="eqCruce' + p.id + '"' + (esCruce ? " checked" : "") + ' style="width:1.4em;height:1.4em;">' +
                  '<label class="form-check-label fw-bold ms-2" for="eqCruce' + p.id + '">' + PREGUNTAS.htmlspecialchars((p.numeral ? p.numeral + ". " : "") + (p.texto_pregunta || "")) + '</label>' +
                '</div>' +
                '<div class="row g-2 eq-detalle"' + (esCruce ? "" : ' style="display:none;"') + '>' +
                  '<div class="col-md-5"><label class="form-label small fw-bold mb-1">Nombre para mostrar en los resultados</label>' +
                    '<input type="text" class="form-control eq-etiqueta-cruce" placeholder="Ej: Sexo" value="' + PREGUNTAS.htmlspecialchars(p.etiqueta_cruce || "") + '"></div>' +
                  '<div class="col-md-7"><label class="form-label small fw-bold mb-1">Guardar también en la ficha de la persona como</label>' +
                    '<select class="form-select eq-campo-votante">' + campoOpts + '</select></div>' +
                '</div>' +
              '</div>' +
              '<div class="card-body py-2 eq-opciones" style="display:none;"></div>' +
            '</div>';
          $c.append(card);
          var $card = $c.find('.eq-card[data-pregunta-id="' + p.id + '"]');
          $card.data("opciones", p.opciones);
          $card.data("equivalencias", eqPorOpcion);
          PREGUNTAS.pintarEquivalenciasOpciones($card);
        }
        $c.off("change.eq").on("change.eq", ".eq-es-cruce", function () {
          $(this).closest(".eq-card").find(".eq-detalle").toggle(this.checked);
        }).on("change.eq", ".eq-campo-votante", function () {
          PREGUNTAS.pintarEquivalenciasOpciones($(this).closest(".eq-card"));
        });
      },
      error: function () {
        $c.html('<div class="alert alert-danger mb-0">No fue posible cargar la información. Cierre esta ventana e intente de nuevo.</div>');
      }
    });
  },

  pintarEquivalenciasOpciones: function ($card) {
    var campos = window.CUESTIONARIO_CAMPOS_VOTANTE || {};
    var campo = $card.find(".eq-campo-votante").val() || "";
    var $box = $card.find(".eq-opciones");
    var info = campos[campo];
    $box.empty();
    if (!campo || !info || !info.valores || Array.isArray(info.valores) || Object.keys(info.valores).length === 0) {
      $box.hide();
      return;
    }
    var opciones = $card.data("opciones") || [];
    var guardadas = $card.data("equivalencias") || {};
    var html = '<p class="small fw-bold mb-2">¿A qué corresponde cada respuesta?</p><div class="row">';
    for (var j = 0; j < opciones.length; j++) {
      var o = opciones[j];
      var sel = '<option value="">-- Elija --</option>';
      Object.keys(info.valores).forEach(function (v) {
        sel += '<option value="' + PREGUNTAS.htmlspecialchars(v) + '"' + (guardadas[o.id] === v ? " selected" : "") + '>' + PREGUNTAS.htmlspecialchars(info.valores[v]) + '</option>';
      });
      html += '<div class="col-md-6 mb-2"><label class="form-label small mb-1">' + PREGUNTAS.htmlspecialchars(o.texto_opcion || o.texto || "") + '</label>' +
        '<select class="form-select eq-opt-input" data-opcion-id="' + o.id + '">' + sel + '</select></div>';
    }
    html += '</div>';
    $box.html(html).show();
  },

  saveCrucesEquivalencias: function () {
    var variables = [];
    var equivalencias = [];
    $("#crucesEquivalenciasContainer .eq-card").each(function () {
      var $card = $(this);
      var pid = parseInt($card.attr("data-pregunta-id"), 10);
      var esCruce = $card.find(".eq-es-cruce").is(":checked");
      var campo = esCruce ? ($card.find(".eq-campo-votante").val() || "") : "";
      variables.push({
        pregunta_id: pid,
        es_variable_cruce: esCruce ? 1 : 0,
        etiqueta_cruce: esCruce ? ($card.find(".eq-etiqueta-cruce").val() || "").trim() : "",
        copiar_a_votante_campo: campo
      });
      if (campo) {
        $card.find(".eq-opt-input").each(function () {
          var valor = $(this).val();
          if (valor) {
            equivalencias.push({
              tbl_pregunta_id: pid,
              tbl_opcion_respuesta_id: parseInt($(this).attr("data-opcion-id"), 10),
              campo_votante: campo,
              valor_votante: valor
            });
          }
        });
      }
    });

    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "equivalenciasvotantesave",
        tbl_ficha_tecnica_encuesta_id: getFichaEncuestaId(),
        variables_cruce: JSON.stringify(variables),
        equivalencias: JSON.stringify(equivalencias)
      },
      success: function (res) {
        UTIL.cursorNormal();
        if (res && res.output && res.output.valid) {
          bootstrap.Modal.getInstance(document.getElementById("crucesEquivalenciasModal")).hide();
          UTIL.mostrarMensajeExitoso("Guardado. Los cambios se ven al recargar la página.");
        } else {
          UTIL.mostrarMensajeError("No fue posible guardar. Revise los datos e intente de nuevo.");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
      }
    });
  },

  openFormulasModal: function () {
    PREGUNTAS.limpiarFormularioFormula();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "formulasget" },
      success: function (res) {
        var rows = (res && res.output && res.output.valid) ? (res.output.response || []) : [];
        PREGUNTAS._formulas = {};
        var html = '<div class="table-responsive"><table class="table align-middle"><thead class="table-light"><tr><th>Indicador</th><th>Cómo se calcula</th><th>Qué significa</th><th style="width:150px;"></th></tr></thead><tbody>';
        var hay = false;
        for (var i = 0; i < rows.length; i++) {
          var f = rows[i];
          if (f.habilitado === "no") continue;
          hay = true;
          PREGUNTAS._formulas[f.id] = f;
          html += '<tr>' +
            '<td class="fw-semibold">' + PREGUNTAS.htmlspecialchars(f.indicador || "") + '</td>' +
            '<td>' + PREGUNTAS.htmlspecialchars(f.formula || "") + '</td>' +
            '<td class="text-muted">' + PREGUNTAS.htmlspecialchars(f.explicacion || "") + '</td>' +
            '<td class="text-end text-nowrap">' +
              '<button type="button" class="btn btn-outline-primary me-1" title="Editar" onclick="PREGUNTAS.editFormulaItem(' + parseInt(f.id, 10) + ')"><i class="fas fa-pen"></i></button>' +
              '<button type="button" class="btn btn-outline-danger" title="Quitar" onclick="PREGUNTAS.deleteFormulaItem(' + parseInt(f.id, 10) + ')"><i class="fas fa-trash-alt"></i></button>' +
            '</td>' +
          '</tr>';
        }
        if (!hay) {
          html += '<tr><td colspan="4" class="text-center text-muted py-4">Todavía no hay indicadores. Escriba el primero arriba.</td></tr>';
        }
        html += '</tbody></table></div>';
        $("#formulasListaContainer").html(html);
        var modalEl = document.getElementById("formulasIndicadoresModal");
        if (!modalEl.classList.contains("show")) {
          new bootstrap.Modal(modalEl).show();
        }
      }
    });
  },

  limpiarFormularioFormula: function () {
    $("#formula_edit_id").val("");
    $("#formula_sigla").val("");
    $("#formula_tipo").val("");
    $("#formula_nombre").val("");
    $("#formula_expresion").val("");
    $("#formula_descripcion").val("");
    $("#formulaTituloForm").text("Nuevo indicador");
  },

  editFormulaItem: function (id) {
    var f = (PREGUNTAS._formulas || {})[id];
    if (!f) return;
    $("#formula_edit_id").val(f.id);
    $("#formula_sigla").val(f.sigla || "");
    $("#formula_tipo").val(f.tipo_indicador || "");
    $("#formula_nombre").val(f.indicador || "");
    $("#formula_expresion").val(f.formula || "");
    $("#formula_descripcion").val(f.explicacion || "");
    $("#formulaTituloForm").text("Editando: " + (f.indicador || ""));
    $("#formula_nombre").trigger("focus");
  },

  insertarParteFormula: function (texto) {
    var el = document.getElementById("formula_expresion");
    var v = el.value;
    var ini = el.selectionStart != null ? el.selectionStart : v.length;
    var fin = el.selectionEnd != null ? el.selectionEnd : v.length;
    el.value = v.substring(0, ini) + texto + v.substring(fin);
    el.focus();
    el.selectionStart = el.selectionEnd = ini + texto.length;
  },

  saveFormulaItem: function () {
    var nombre = ($("#formula_nombre").val() || "").trim();
    var expr = ($("#formula_expresion").val() || "").trim();
    var desc = ($("#formula_descripcion").val() || "").trim();
    if (!nombre || !expr) {
      UTIL.mostrarMensajeValidacion("Escriba el nombre del indicador y cómo se calcula.");
      return;
    }
    var editId = parseInt($("#formula_edit_id").val(), 10) || 0;
    var sigla = $("#formula_sigla").val();
    if (!sigla) {
      sigla = "IND_" + nombre.replace(/[^A-Za-z0-9]/g, "").substring(0, 8).toUpperCase() + "_" + Math.floor(Math.random() * 900 + 100);
    }
    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: {
        op: "formulassave",
        id: editId,
        indicador: nombre,
        sigla: sigla,
        tipo_indicador: $("#formula_tipo").val() || "Cuestionario",
        formula: expr,
        explicacion: desc,
        habilitado: "si"
      },
      success: function (res) {
        UTIL.cursorNormal();
        if (res && res.output && res.output.valid) {
          UTIL.mostrarMensajeExitoso("Indicador guardado.");
          PREGUNTAS.openFormulasModal();
        } else {
          UTIL.mostrarMensajeError((res && res.output && res.output.response && res.output.response.content) || "No se pudo guardar el indicador.");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
      }
    });
  },

  deleteFormulaItem: function (id) {
    var f = (PREGUNTAS._formulas || {})[id] || {};
    Swal.fire({
      title: "¿Quitar este indicador?",
      text: "Se quitará «" + (f.indicador || "el indicador") + "» de los análisis donde se use.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, quitar",
      cancelButtonText: "No, dejarlo",
      reverseButtons: true
    }).then(function (result) {
      if (!result.value) return;
      UTIL.cursorBusy();
      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: { op: "formulasdelete", id: id },
        success: function () {
          UTIL.cursorNormal();
          PREGUNTAS.openFormulasModal();
        },
        error: function () {
          UTIL.cursorNormal();
          UTIL.mostrarMensajeError("No se pudo quitar el indicador.");
        }
      });
    });
  }

};

