const PREGUNTAS_GRILLA = {
  preguntasPrincipales: [],
  subpreguntas: [],
  preguntasConfig: [],
  editandoId: 0,

  init: function () {
    console.log('Inicializando m�dulo de Preguntas de Grilla');
    this.bindEvents();
    this.cargarPreguntas();
  },

  /**
   * Vincula eventos a los elementos
   */
  bindEvents: function () {
    const self = this;

    // Bot�n Nueva Pregunta
    $('#btnNuevaPregunta').on('click', function () {
      self.mostrarModalNueva();
    });

    // Bot�n Guardar Pregunta
    $('#btnGuardarPregunta').on('click', function () {
      self.guardarPregunta();
    });

    // Cambio de tipo de pregunta
    $('#tipoPregunta').on('change', function () {
      self.toggleCamposPorTipo();
    });

    // Pesta�as
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
      const target = $(e.target).attr('data-bs-target');
      if (target === '#preview') {
        self.generarVistaPrevia();
      }
    });
  },

  /**
   * Carga todas las preguntas desde el servidor
   */
  cargarPreguntas: function () {
    const self = this;
    const q = { op: 'preguntasgrillaobtenerconsubpreguntas' };
    UTIL.cursorNormal();

    UTIL.callAjaxRqstPOST(q, function (response) {
      console.log('Preguntas cargadas:', response);

      if (response && response.output && response.output.valid) {
        self.preguntasPrincipales = response.output.response.preguntas || [];
        self.subpreguntas = response.output.response.subpreguntas || [];
        self.preguntasConfig = [...self.preguntasPrincipales, ...self.subpreguntas];

        self.renderizarPreguntasPrincipales();
        self.renderizarSubpreguntas();
        self.actualizarSelectorPreguntasPadre();
      } else {
        UTIL.mostrarMensajeError('Error al cargar preguntas');
      }
      UTIL.cursorNormal();
    });
  },

  /**
   * Renderiza la tabla de preguntas principales
   */
  renderizarPreguntasPrincipales: function () {
    const tbody = $('#tbodyPreguntasPrincipales');
    tbody.empty();

    if (this.preguntasPrincipales.length === 0) {
      tbody.html(`
        <tr>
          <td colspan="7" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
            No hay preguntas principales configuradas
          </td>
        </tr>
      `);
      return;
    }

    this.preguntasPrincipales.forEach(pregunta => {
      const opciones = pregunta.opciones_respuesta
        ? JSON.parse(pregunta.opciones_respuesta).join(', ')
        : '-';

      const condicion = this.obtenerTextoCondicion(pregunta.condicion_habilitacion);
      const estadoBadge = parseInt(pregunta.habilitado) === 1
        ? '<span class="badge bg-success">Activa</span>'
        : '<span class="badge bg-secondary">Inactiva</span>';

      const row = `
        <tr data-id="${pregunta.id}">
          <td class="text-center">
            <span class="badge bg-primary">${pregunta.orden}</span>
          </td>
          <td><code>${pregunta.codigo_pregunta}</code></td>
          <td>${pregunta.texto_pregunta}</td>
          <td><small>${opciones}</small></td>
          <td><small>${condicion}</small></td>
          <td>${estadoBadge}</td>
          <td>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-primary btn-editar" data-id="${pregunta.id}" title="Editar">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-eliminar" data-id="${pregunta.id}" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
      tbody.append(row);
    });

    // Vincular eventos de editar y eliminar
    this.vincularEventosCRUD();
  },

  /**
   * Renderiza la tabla de subpreguntas
   */
  renderizarSubpreguntas: function () {
    const tbody = $('#tbodySubpreguntas');
    tbody.empty();

    if (this.subpreguntas.length === 0) {
      tbody.html(`
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
            No hay subpreguntas configuradas
          </td>
        </tr>
      `);
      return;
    }

    this.subpreguntas.forEach(subpregunta => {
      const preguntaPadre = this.preguntasPrincipales.find(p => p.id === subpregunta.pregunta_padre_id);
      const nombrePadre = preguntaPadre ? preguntaPadre.texto_pregunta : '-';

      const estadoBadge = parseInt(subpregunta.habilitado) === 1
        ? '<span class="badge bg-success">Activa</span>'
        : '<span class="badge bg-secondary">Inactiva</span>';

      const row = `
        <tr data-id="${subpregunta.id}">
          <td class="text-center">
            <span class="badge bg-info">${subpregunta.orden}</span>
          </td>
          <td><code>${subpregunta.codigo_pregunta}</code></td>
          <td>${subpregunta.texto_pregunta}</td>
          <td><small class="text-muted">${nombrePadre}</small></td>
          <td>${estadoBadge}</td>
          <td>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-primary btn-editar" data-id="${subpregunta.id}" title="Editar">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-eliminar" data-id="${subpregunta.id}" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
      tbody.append(row);
    });

    // Vincular eventos de editar y eliminar
    this.vincularEventosCRUD();
  },

  /**
   * Vincula eventos de editar y eliminar en las tablas
   */
  vincularEventosCRUD: function () {
    const self = this;

    // Editar
    $('.btn-editar').off('click').on('click', function () {
      const id = $(this).data('id');
      self.editarPregunta(id);
    });

    // Eliminar
    $('.btn-eliminar').off('click').on('click', function () {
      const id = $(this).data('id');
      self.eliminarPregunta(id);
    });
  },

  /**
   * Muestra el modal para crear una nueva pregunta
   */
  mostrarModalNueva: function () {
    this.editandoId = 0;
    $('#tituloModalPregunta').html('<i class="fas fa-plus-circle me-2"></i>Nueva Pregunta');
    $('#formPregunta')[0].reset();
    $('#preguntaId').val('0');
    $('#habilitado').prop('checked', true);

    // Limpiar Choices de grillas
    const selectElement = document.getElementById('grillasAsociadas');
    if (selectElement && selectElement.choicesInstance) {
      selectElement.choicesInstance.removeActiveItems();
    }

    this.toggleCamposPorTipo();
    this.initializeChoices();
    $('#modalPregunta').modal('show');
  },

  /**
   * Inicializa Choices.js para grillas asociadas
   */
  initializeChoices: function () {
    const selectElement = document.getElementById('grillasAsociadas');
    if (!selectElement) return;

    // Destruir instancia previa si existe
    if (selectElement.choicesInstance) {
      try {
        selectElement.choicesInstance.destroy();
      } catch (e) {
        console.log('Error al destruir Choices:', e);
      }
      selectElement.choicesInstance = null;
    }

    // Crear nueva instancia de Choices.js
    const choices = new Choices(selectElement, {
      removeItemButton: true,
      placeholder: true,
      placeholderValue: 'Seleccione las grillas (vacío = todas las grillas)',
      searchPlaceholderValue: 'Buscar grilla...',
      noResultsText: 'No se encontraron grillas',
      itemSelectText: 'Presione para seleccionar',
      noChoicesText: 'No hay grillas disponibles'
    });

    // Guardar referencia en el elemento
    selectElement.choicesInstance = choices;

    return choices;
  },

  /**
   * Edita una pregunta existente
   */
  editarPregunta: function (id) {
    const self = this;
    const q = { op: 'preguntasgrillaporid', id: id };

    UTIL.callAjaxRqstPOST(q, function (response) {
      if (response && response.output && response.output.valid) {
        const pregunta = response.output.response;
        self.editandoId = id;

        $('#tituloModalPregunta').html('<i class="fas fa-edit me-2"></i>Editar Pregunta');
        $('#preguntaId').val(pregunta.id);
        $('#tipoPregunta').val(pregunta.tipo_pregunta);
        $('#codigoPregunta').val(pregunta.codigo_pregunta);
        $('#textoPregunta').val(pregunta.texto_pregunta);
        $('#ordenPregunta').val(pregunta.orden);
        $('#preguntaPadreId').val(pregunta.pregunta_padre_id || '');
        $('#opcionesRespuesta').val(pregunta.opciones_respuesta || '');
        $('#habilitaSubpreguntas').prop('checked', parseInt(pregunta.habilita_subpreguntas) === 1);
        $('#condicionHabilitacion').val(pregunta.condicion_habilitacion || '');
        $('#requiereTodasSi').prop('checked', parseInt(pregunta.requiere_todas_si) === 1);
        $('#activaSeccionSubpreguntas').prop('checked', parseInt(pregunta.activa_seccion_subpreguntas) === 1);
        $('#habilitado').prop('checked', parseInt(pregunta.habilitado) === 1);

        self.toggleCamposPorTipo();
        self.initializeChoices();

        // Cargar grillas asociadas (debe hacerse DESPUÉS de inicializar Choices)
        setTimeout(() => {
          const selectElement = document.getElementById('grillasAsociadas');
          if (selectElement && selectElement.choicesInstance && pregunta.grillas_ids) {
            const grillasArray = pregunta.grillas_ids.split(',');
            grillasArray.forEach(grilla_id => {
              try {
                selectElement.choicesInstance.setChoiceByValue(grilla_id.trim());
              } catch (e) {
                console.log('Error al seleccionar grilla:', e);
              }
            });
          }
        }, 100);

        $('#modalPregunta').modal('show');
      }
    });

    UTIL.cursorNormal();
  },

  /**
   * Guarda una pregunta (crear o actualizar)
   */
  guardarPregunta: function () {
    const self = this;
    const form = $('#formPregunta')[0];

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    // Validar JSON de opciones
    const opcionesRespuesta = $('#opcionesRespuesta').val().trim();
    if (opcionesRespuesta && $('#tipoPregunta').val() === 'pregunta') {
      try {
        JSON.parse(opcionesRespuesta);
      } catch (e) {
        UTIL.mostrarMensajeError('El formato de opciones de respuesta no es un JSON correcto.');
        return;
      }
    }

    // Obtener grillas asociadas desde Choices.js
    const selectElement = document.getElementById('grillasAsociadas');
    let grillasAsociadas = [];

    if (selectElement && selectElement.choicesInstance) {
      // Obtener valores seleccionados desde Choices.js
      const selectedValues = selectElement.choicesInstance.getValue(true);
      grillasAsociadas = Array.isArray(selectedValues) ? selectedValues : [selectedValues].filter(v => v);
    } else {
      // Fallback: obtener directamente del select
      grillasAsociadas = $('#grillasAsociadas').val() || [];
    }

    console.log('Grillas asociadas a guardar:', grillasAsociadas);

    const datos = {
      op: 'preguntasgrillasave',
      id: $('#preguntaId').val(),
      tipo_pregunta: $('#tipoPregunta').val(),
      codigo_pregunta: $('#codigoPregunta').val(),
      texto_pregunta: $('#textoPregunta').val(),
      orden: $('#ordenPregunta').val(),
      pregunta_padre_id: $('#preguntaPadreId').val() || null,
      opciones_respuesta: opcionesRespuesta || null,
      habilita_subpreguntas: $('#habilitaSubpreguntas').is(':checked') ? 1 : 0,
      condicion_habilitacion: $('#condicionHabilitacion').val() || null,
      requiere_todas_si: $('#requiereTodasSi').is(':checked') ? 1 : 0,
      habilitado: $('#habilitado').is(':checked') ? 1 : 0,
      grillas_asociadas: JSON.stringify(grillasAsociadas || [])
    };

    UTIL.callAjaxRqstPOST(datos, function (response) {
      console.log('Respuesta guardar:', response);

      if (response && response.output && response.output.valid) {
        UTIL.mostrarMensajeExitoso(response.output.response.msg || 'Pregunta guardada correctamente');
        $('#modalPregunta').modal('hide');
        self.cargarPreguntas();
      } else {
        UTIL.mostrarMensajeError(response.output.response.content);
      }
    });
  },

  /**
   * Elimina una pregunta
   */
  eliminarPregunta: function (id) {
    const self = this;

    Swal.fire({
      title:
        "Está seguro de eliminar esta pregunta? Esta acción no se puede deshacer.",
      text: "¿Desea continuar?",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "Cancelar!",
      closeOnConfirm: false,
    }).then((result) => {
      if (result.value) {
        const q = { op: "preguntasgrilladelete", id: id };

        UTIL.callAjaxRqstPOST(q, function (response) {
          if (
            response &&
            response.output &&
            response.output.valid
          ) {
            UTIL.mostrarMensajeExitoso(
              "Pregunta eliminada correctamente"
            );
            self.cargarPreguntas();
          } else {
            UTIL.mostrarMensajeError(
              "Error al eliminar la pregunta"
            );
          }
        });
      }
    });
  },

  /**
   * Muestra/oculta campos seg�n el tipo de pregunta
   */
  toggleCamposPorTipo: function () {
    const tipo = $('#tipoPregunta').val();

    if (tipo === 'pregunta') {
      // Pregunta principal
      $('#contenedorPreguntaPadre').hide();
      $('#contenedorOpciones').show();
      $('#contenedorHabilitaSubpreguntas').show();
      $('#contenedorCondicion').show();
      $('#contenedorRequiereTodasSi').show();
      $('#contenedorGrillasAsociadas').show(); // Mostrar campo de grillas para preguntas principales
    } else {
      // Subpregunta - Las subpreguntas heredan las grillas de su pregunta padre
      $('#contenedorPreguntaPadre').show();
      $('#contenedorOpciones').hide();
      $('#contenedorHabilitaSubpreguntas').hide();
      $('#contenedorCondicion').hide();
      $('#contenedorRequiereTodasSi').hide();
      $('#contenedorGrillasAsociadas').hide(); // Ocultar campo de grillas para subpreguntas
    }
  },

  /**
   * Actualiza el selector de preguntas padre
   */
  actualizarSelectorPreguntasPadre: function () {
    const select = $('#preguntaPadreId');
    select.empty();
    select.append('<option value="">Ninguna</option>');

    this.preguntasPrincipales.forEach(pregunta => {
      select.append(`<option value="${pregunta.id}">${pregunta.texto_pregunta}</option>`);
    });
  },

  /**
   * Obtiene el texto descriptivo de una condici�n
   */
  obtenerTextoCondicion: function (condicion) {
    const condiciones = {
      'si': 'Si responde SÍ',
      'favorable': 'Si responde FAVORABLE',
      'todas_si': 'Si todas son SÍ',
      '': '-',
      null: '-'
    };
    return condiciones[condicion] || '-';
  },

  /**
   * Genera la vista previa de c�mo se ver�n las preguntas
   */
  generarVistaPrevia: function () {
    const container = $('#previewContenido');
    container.empty();

    if (this.preguntasPrincipales.length === 0) {
      container.html(`
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          No hay preguntas configuradas para mostrar vista previa
        </div>
      `);
      return;
    }

    // Tabla de vista previa similar a la interfaz real
    let html = `
      <div class="table-responsive">
        <table class="table tabla_grilla">
          <thead class="table-header">
            <tr>
              <th>CANDIDATOS</th>
    `;

    // Headers de preguntas principales
    this.preguntasPrincipales.forEach(pregunta => {
      html += `<th class="text-center">${pregunta.texto_pregunta}</th>`;
    });

    html += `
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-xl me-3">
                    <div class="avatar-name rounded-circle bg-primary text-white">
                      <span>CP</span>
                    </div>
                  </div>
                  <div>
                    <strong>Candidato de Prueba</strong>
                    <br><small class="text-muted">Ejemplo de visualización</small>
                  </div>
                </div>
              </td>
    `;

    // Columnas de toggle para cada pregunta
    this.preguntasPrincipales.forEach(pregunta => {
      const opciones = pregunta.opciones_respuesta ? JSON.parse(pregunta.opciones_respuesta) : [];
      html += `<td class="text-center"><div class="toggle">`;

      opciones.forEach(opcion => {
        let texto = opcion.toUpperCase();
        let icono = '';
        let clase = '';

        if (opcion === 'si') {
          icono = '<i class="fas fa-check"></i>';
          clase = 'si';
        } else if (opcion === 'no') {
          icono = '<i class="fas fa-times"></i>';
          clase = 'no';
        } else if (opcion === 'favorable') {
          texto = 'SÍ';
          clase = 'si';
        } else if (opcion === 'desfavorable') {
          texto = 'NO';
          clase = 'no';
        }

        html += `<button class="toggle-btn ${clase}" disabled>${icono || texto}</button>`;
      });

      html += `</div></td>`;
    });

    html += `
            </tr>
          </tbody>
        </table>
      </div>
    `;

    // Mostrar subpreguntas si existen
    if (this.subpreguntas.length > 0) {
      html += `
        <div class="mt-4">
          <h5 class="mb-3"><i class="fas fa-indent me-2"></i>Subpreguntas (Solo para candidatos aprobados)</h5>
          <div class="row">
      `;

      this.subpreguntas.forEach(subpregunta => {
        html += `
          <div class="col-md-4 mb-3">
            <div class="card">
              <div class="card-body">
                <h6 class="card-title text-primary">
                  <code>${subpregunta.codigo_pregunta.toUpperCase()}</code>
                </h6>
                <p class="card-text small">${subpregunta.texto_pregunta}</p>
              </div>
            </div>
          </div>
        `;
      });

      html += `
          </div>
        </div>
      `;
    }

    container.html(html);

    UTIL.cursorNormal();
  }
};

// Inicializar cuando el documento est� listo
$(document).on('ready', function () {
  PREGUNTAS_GRILLA.init();
});
