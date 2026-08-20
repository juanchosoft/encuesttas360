
$(function () { init(); });

var q;
var optionCounter = 0;

function init() {
  q = {};

  // Configura los listeners de los botones de opciones (añadir/eliminar)
  PREGUNTAS.setupOptionListeners();
  // Establece el estado inicial del mensaje "No hay opciones"
  OPCIONES.updateNoOptionsMessage();

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

var return_page = "preguntas.php"; // Cambia esto a tu página de listado de preguntas

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

  /**
   * Carga los datos de una pregunta para edición.
   * @param {number} id El ID de la pregunta a editar.
   */
  editData: function (id) {
    q = {};
    q.op = "preguntaget"; // Operación para obtener una pregunta
    q.id = id;



    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  /**
   * Maneja la respuesta de la carga de datos para edición.
   * Popula el formulario y las opciones.
   */
  editdataHandler: function (data) {
    UTIL.cursorNormal();
    if (data.output.valid && data.output.response.length > 0) {
      var res = data.output.response[0];
      $("#id").val(res.id);
      $("#tbl_ficha_tecnica_encuesta_id").val(res.tbl_ficha_tecnica_encuesta_id);
      $("#texto_pregunta").val(res.texto_pregunta);
      $("#enunciado_pregunta").val(res.enunciado_pregunta || '');
      $("#tipo_pregunta").val(res.tipo_pregunta);
      $("#orden").val(res.orden);

      $("#limite_respuesta_multiple").val(res.limite_respuesta_multiple);
      if(res.tipo_pregunta == "Seleccion_Multiple_multiple_respuesta"){
        $("#divLimiteRespuesta").show();
      } else {
        $("#divLimiteRespuesta").hide();
      }

      // Cargar estado de habilitado
      $("#habilitado").prop('checked', res.habilitado === 'si');

      // Cargar estado de visualización
      $("#visualizacion").prop('checked', res.visualizacion === 'si');

      // Importante: No precargar opciones por tipo de pregunta si estamos editando
      // porque queremos mostrar las opciones reales de la pregunta que se está editando.
      $("#opcionesContainer").empty();
      if (res.opciones && res.opciones.length > 0) {
        $.each(res.opciones, function (index, opcion) {
          OPCIONES.addOptionField(opcion.texto, opcion.id); // Re-añadir con datos
        });
      }
      OPCIONES.updateNoOptionsMessage();

      $('#preguntaFormModal').modal('show');
    } else {
      UTIL.mostrarMensajeError(data.output.response.content);
    }
  },
  validateData: function () {
    var bValid = true;
    if (
      $("#tbl_ficha_tecnica_encuesta_id").val() === "0" ||
      $("#tbl_ficha_tecnica_encuesta_id").val() === ""
    ) {
      UTIL.mostrarMensajeValidacion("Por favor, seleccione una Encuesta.");
      bValid = false;
    } else if ($("#texto_pregunta").val().trim() === "") {
      UTIL.mostrarMensajeValidacion("El texto de la pregunta es requerido.");
      bValid = false;
    } else {
      $('#opcionesContainer input[type="text"]').each(function () {
        if ($(this).val().trim() === "" && !$(this).closest(".d-none").length) {
          UTIL.mostrarMensajeValidacion(
            "Las opciones de respuesta no pueden estar vacías."
          );
          bValid = false;
          return false;
        }
      });
    }

    if (!bValid) {
      return;
    }
    PREGUNTAS.savedata();
  },

  savedata: function () {
    q = {};
    q.op = "preguntasave";
    q.id = $("#id").val();
    q.tbl_ficha_tecnica_encuesta_id = $("#tbl_ficha_tecnica_encuesta_id").val();
    q.texto_pregunta = $("#texto_pregunta").val();
    q.enunciado_pregunta = $("#enunciado_pregunta").val();
    q.tipo_pregunta = $("#tipo_pregunta").val();
    q.orden = $("#orden").val();
    q.limite_respuesta_multiple = $("#limite_respuesta_multiple").val();
    q.habilitado = $("#habilitado").is(':checked') ? 'si' : 'no';
    q.visualizacion = $("#visualizacion").is(':checked') ? 'si' : 'no';

    q.opciones = [];
    $('#opcionesContainer input[type="text"]').each(function () {
      const opcionTexto = $(this).val().trim();
      if (opcionTexto !== "") {
        q.opciones.push(opcionTexto);
      }
    });
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
          }, 1500);
        } else {
          UTIL.mostrarMensajeError(data.output.response.content);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        UTIL.cursorNormal();
        console.error(
          "Error AJAX en save Pregunta:",
          textStatus,
          errorThrown,
          jqXHR.responseText
        );
        UTIL.mostrarMensajeError(
          "Error de comunicación con el servidor al guardar la pregunta."
        );
      },
    });
  },

  clearForm: function (formId) {
    UTIL.clearForm(formId);
    $("#enunciado_pregunta").val('');
    $("#opcionesContainer").empty();
    $("#noOptionsMessage").show();
    $("#tbl_ficha_tecnica_encuesta_id").val("0").trigger("change");
    $("#tipo_pregunta").val("Multiple Choice").trigger("change");
    $("#habilitado").prop('checked', true);
    $("#visualizacion").prop('checked', true);
    $('#preguntaFormModal').modal('hide');
  },

  openCreateModal: function () {
    PREGUNTAS.clearForm('formPreguntas');
    $('#preguntaFormModal').modal('show');
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
    var encuestaIdPrincipal = $('#tbl_ficha_tecnica_encuesta_id').val();
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
    var encuestaId  = $('#batchEncuesta').val();
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
    var encuestaId = $('#batchEncuesta').val();
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
  }

}
