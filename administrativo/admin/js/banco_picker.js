var BANCO_PICKER = {
  TIPOS: {
    Dicotomica: 'Sí / No',
    Seleccion_Multiple_unica_respuesta: 'Escoge una sola opción',
    Seleccion_Multiple_multiple_respuesta: 'Escoge varias opciones',
    Preguntas_Ordinales: 'Escala de calificación',
    Preguntas_Cardinales: 'Nivel de satisfacción / intensidad',
    Grilla: 'Tabla de candidatos',
    Lista_desde_grilla: 'Escoger de una tabla (voto escalonado)',
    'Texto Libre': 'Respuesta abierta',
    'No Aplica': 'No aplica'
  },
  uso: 'encuesta',
  alUsar: null,
  filas: [],

  esc: function (s) {
    return $('<div>').text(s == null ? '' : String(s)).html();
  },

  ajax: function (data, ok) {
    UTIL.cursorBusy();
    $.ajax({
      url: 'admin/ajax/rqst.php',
      method: 'POST',
      dataType: 'json',
      data: data,
      success: function (res) {
        UTIL.cursorNormal();
        ok(res);
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError('Error de comunicación con el servidor.');
      }
    });
  },

  agregarAlBanco: function (uso, origenId, textoPregunta, permitirDuplicado) {
    var q = uso === 'sondeo'
      ? { op: 'bancopreguntadesdesondeo', sondeo_id: origenId }
      : { op: 'bancopreguntadesdepregunta', pregunta_id: origenId };
    if (permitirDuplicado) q.permitir_duplicado = 1;
    BANCO_PICKER.ajax(q, function (res) {
      var o = res.output || {};
      if (o.valid) {
        var omitido = o.omitido || [];
        var html = '<p class="mb-2">La pregunta quedó guardada en el banco de preguntas.</p>';
        if (omitido.length) {
          html += '<p class="mb-1 small text-start"><strong>No se copia al banco (depende de ' + (uso === 'sondeo' ? 'este sondeo' : 'esta encuesta') + '):</strong></p><ul class="small text-start mb-0">';
          omitido.forEach(function (t) { html += '<li>' + BANCO_PICKER.esc(t) + '</li>'; });
          html += '</ul>';
        }
        Swal.fire({ title: 'Agregada al banco', html: html, icon: 'success', confirmButtonColor: '#20427F' });
        return;
      }
      if (o.duplicado) {
        Swal.fire({
          title: 'Ya está en el banco',
          text: 'Existe una pregunta igual (mismo texto, tipo y opciones). ¿Desea agregarla de todas formas?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Agregar igual',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#20427F'
        }).then(function (r) {
          if (r.isConfirmed) BANCO_PICKER.agregarAlBanco(uso, origenId, textoPregunta, true);
        });
        return;
      }
      UTIL.mostrarMensajeError((o.response && o.response.content) || 'No se pudo agregar al banco.');
    });
  },

  asegurarModal: function () {
    if (document.getElementById('bncPickerModal')) return;
    var h = '<div class="modal fade" id="bncPickerModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">' +
      '<div class="modal-header"><h5 class="modal-title"><i class="fas fa-database me-2"></i>Agregar pregunta desde el banco</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>' +
      '<div class="modal-body">' +
        '<div class="row g-2 mb-3"><div class="col-md-7"><input type="search" class="form-control" id="bncPickBuscar" placeholder="Buscar por texto, categoría o numeral"></div>' +
        '<div class="col-md-5"><select class="form-select" id="bncPickCategoria"><option value="">Categoría: todas</option></select></div></div>' +
        '<div class="small text-muted mb-3">Al usar una pregunta se abre su formulario con los datos copiados para que confirme el numeral y el capítulo antes de guardar. Cambiar el banco después no modifica lo que guarde aquí.</div>' +
        '<div id="bncPickLista"></div>' +
      '</div></div></div></div>';
    $('body').append(h);
    var tmr = null;
    $(document).on('input', '#bncPickBuscar', function () {
      clearTimeout(tmr);
      tmr = setTimeout(BANCO_PICKER.cargar, 300);
    });
    $(document).on('change', '#bncPickCategoria', BANCO_PICKER.cargar);
    $(document).on('click', '.bnc-pick-usar', function () {
      var id = parseInt($(this).data('id'), 10);
      var fila = BANCO_PICKER.filas.filter(function (f) { return parseInt(f.id, 10) === id; })[0];
      if (!fila) return;
      bootstrap.Modal.getInstance(document.getElementById('bncPickerModal')).hide();
      if (BANCO_PICKER.alUsar) BANCO_PICKER.alUsar(fila);
    });
  },

  abrirSelector: function (uso, alUsar) {
    BANCO_PICKER.uso = uso;
    BANCO_PICKER.alUsar = alUsar;
    BANCO_PICKER.asegurarModal();
    $('#bncPickBuscar').val('');
    $('#bncPickCategoria').val('');
    BANCO_PICKER.ajax({ op: 'bancopreguntacategorias' }, function (res) {
      var cats = (res.output && res.output.valid) ? res.output.response : [];
      var opts = '<option value="">Categoría: todas</option>';
      cats.forEach(function (c) { opts += '<option value="' + BANCO_PICKER.esc(c) + '">' + BANCO_PICKER.esc(c) + '</option>'; });
      $('#bncPickCategoria').html(opts);
    });
    BANCO_PICKER.cargar();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('bncPickerModal')).show();
  },

  cargar: function () {
    BANCO_PICKER.ajax({
      op: 'bancopreguntaget',
      uso: BANCO_PICKER.uso,
      buscar: $('#bncPickBuscar').val(),
      categoria: $('#bncPickCategoria').val(),
      con_opciones: 1
    }, function (res) {
      if (!res.output || !res.output.valid) {
        $('#bncPickLista').html('<div class="text-danger">No se pudo cargar el banco.</div>');
        return;
      }
      BANCO_PICKER.filas = res.output.response || [];
      if (!BANCO_PICKER.filas.length) {
        $('#bncPickLista').html('<div class="text-center text-muted py-4">No hay preguntas del banco para ' + (BANCO_PICKER.uso === 'sondeo' ? 'sondeos' : 'encuestas') + ' con ese filtro.</div>');
        return;
      }
      var h = '';
      BANCO_PICKER.filas.forEach(function (f) {
        var tipo = BANCO_PICKER.uso === 'sondeo' ? f.tipo_sondeo : f.tipo_pregunta;
        var ops = (f.opciones || []).slice(0, 6).map(function (o) { return BANCO_PICKER.esc(o.texto_opcion); }).join(' · ');
        if ((f.opciones || []).length > 6) ops += ' · …';
        h += '<div class="border rounded-3 p-3 mb-2 d-flex gap-3 align-items-start">' +
          '<div class="flex-grow-1"><div class="fw-bold mb-1">' + BANCO_PICKER.esc(f.texto_pregunta) + '</div>' +
          '<div class="mb-1"><span class="badge bg-light text-dark border">' + BANCO_PICKER.esc(BANCO_PICKER.TIPOS[tipo] || tipo || '') + '</span>' +
          (f.categoria ? ' <span class="badge bg-primary-subtle text-primary border">' + BANCO_PICKER.esc(f.categoria) + '</span>' : '') + '</div>' +
          (ops ? '<div class="small text-muted">' + ops + '</div>' : '') + '</div>' +
          '<button type="button" class="btn btn-primary btn-sm fw-bold bnc-pick-usar" data-id="' + f.id + '">Usar</button></div>';
      });
      $('#bncPickLista').html(h);
    });
  }
};
