var BANCO = {
  filas: [],
  plantillas: null,
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

  esc: function (s) {
    return $('<div>').text(s == null ? '' : String(s)).html();
  },

  tipoDe: function (f) {
    var t = f.uso === 'sondeo' ? f.tipo_sondeo : f.tipo_pregunta;
    return BANCO.TIPOS[t] || t || '';
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

  mensajeError: function (res, defecto) {
    return (res && res.output && res.output.response && res.output.response.content) || defecto;
  },

  cargar: function () {
    var q = { op: 'bancopreguntaget', buscar: $('#fBuscar').val(), uso: $('#fUso').val(), categoria: $('#fCategoria').val() };
    BANCO.ajax(q, function (res) {
      if (!res.output || !res.output.valid) {
        UTIL.mostrarMensajeError(BANCO.mensajeError(res, 'No se pudo cargar el banco.'));
        return;
      }
      BANCO.filas = res.output.response || [];
      BANCO.render();
    });
  },

  cargarCategorias: function (despues) {
    BANCO.ajax({ op: 'bancopreguntacategorias' }, function (res) {
      var cats = (res.output && res.output.valid) ? res.output.response : [];
      var actual = $('#fCategoria').val();
      var opts = '<option value="">Categoría: todas</option>';
      var lista = '';
      cats.forEach(function (c) {
        opts += '<option value="' + BANCO.esc(c) + '">' + BANCO.esc(c) + '</option>';
        lista += '<option value="' + BANCO.esc(c) + '"></option>';
      });
      $('#fCategoria').html(opts).val(actual);
      $('#bCategoriasLista').html(lista);
      if (despues) despues();
    });
  },

  render: function () {
    var p = window.BANCO_PERMISOS || {};
    var html = '';
    var nEnc = 0;
    var nSon = 0;
    BANCO.filas.forEach(function (f) {
      if (f.uso === 'sondeo') nSon++; else nEnc++;
      html += '<tr>' +
        '<td>' + f.id + '</td>' +
        '<td class="bnc-texto">' + BANCO.esc(f.texto_pregunta) + '</td>' +
        '<td><span class="bnc-uso bnc-uso-' + BANCO.esc(f.uso) + '">' + (f.uso === 'sondeo' ? 'Sondeo' : 'Encuesta') + '</span></td>' +
        '<td>' + BANCO.esc(BANCO.tipoDe(f)) + '</td>' +
        '<td>' + (f.categoria ? BANCO.esc(f.categoria) : '<span class="text-muted">—</span>') + '</td>' +
        '<td class="text-center">' + f.total_opciones + '</td>' +
        '<td class="text-end text-nowrap">' +
          '<button type="button" class="btn btn-sm btn-outline-secondary me-1 bnc-ver" data-id="' + f.id + '" title="Ver detalle"><i class="fas fa-eye"></i></button>' +
          (p.editar ? '<button type="button" class="btn btn-sm btn-outline-primary me-1 bnc-editar" data-id="' + f.id + '" title="Editar"><i class="fas fa-pen"></i></button>' : '') +
          (p.eliminar ? '<button type="button" class="btn btn-sm btn-outline-danger bnc-eliminar" data-id="' + f.id + '" title="Eliminar"><i class="fas fa-trash"></i></button>' : '') +
        '</td></tr>';
    });
    $('#bncCuerpo').html(html);
    $('#bncVacio').toggleClass('d-none', BANCO.filas.length > 0);
    $('#tblBanco').toggleClass('d-none', BANCO.filas.length === 0);
    $('#kpiTotal').text(BANCO.filas.length);
    $('#kpiEncuesta').text(nEnc);
    $('#kpiSondeo').text(nSon);
  },

  obtener: function (id, cb) {
    BANCO.ajax({ op: 'bancopreguntaget', id: id }, function (res) {
      var f = res.output && res.output.valid && res.output.response[0];
      if (!f) {
        UTIL.mostrarMensajeError(BANCO.mensajeError(res, 'No se encontró la pregunta.'));
        return;
      }
      cb(f);
    });
  },

  verDetalle: function (id) {
    BANCO.obtener(id, function (f) {
      var fila = function (t, v) {
        return v ? '<dt>' + t + '</dt><dd>' + BANCO.esc(v) + '</dd>' : '';
      };
      var h = '<dl class="bnc-detalle mb-0">' +
        fila('Uso', f.uso === 'sondeo' ? 'Sondeo' : 'Encuesta') +
        fila('Categoría', f.categoria) +
        fila('Pregunta', f.texto_pregunta) +
        fila('Tipo', BANCO.tipoDe(f));
      if (f.uso === 'sondeo') {
        h += fila('Descripción', f.descripcion_sondeo) + fila('Tipo de inferenciales', f.tipo_inferenciales) + fila('Es trivia', f.es_trivia === 'si' ? 'Sí' : 'No');
      } else {
        h += fila('Capítulo sugerido', f.capitulo) + fila('Numeral sugerido', f.numeral) + fila('Enunciado de la sección', f.enunciado_pregunta) +
          fila('Instrucción para el encuestador', f.libreto_encuestador) + fila('Nota de análisis', f.nota_tabulacion) +
          fila('Obligatoria', String(f.obligatoria) === '1' ? 'Sí' : 'No') + fila('Pregunta filtro', String(f.es_filtro) === '1' ? 'Sí' : '');
      }
      h += '<dt>Opciones de respuesta</dt><dd>';
      if (!f.opciones || !f.opciones.length) {
        h += '<span class="text-muted">Sin opciones</span>';
      } else {
        h += '<ol class="mb-0">';
        f.opciones.forEach(function (o) {
          var extra = [];
          if (o.valor_numerico !== null && o.valor_numerico !== '') extra.push('valor ' + parseFloat(o.valor_numerico));
          if (o.cuenta_como && o.cuenta_como !== 'ninguno') extra.push('cuenta como ' + o.cuenta_como);
          if (o.accion === 'terminar') extra.push('termina la encuesta' + (o.mensaje_terminar ? ': «' + o.mensaje_terminar + '»' : ''));
          h += '<li>' + BANCO.esc(o.texto_opcion) + (extra.length ? ' <small class="text-muted">(' + BANCO.esc(extra.join(' · ')) + ')</small>' : '') + '</li>';
        });
        h += '</ol>';
      }
      h += '</dd></dl>';
      $('#bncDetalleCuerpo').html(h);
      new bootstrap.Modal(document.getElementById('bncModalDetalle')).show();
    });
  },

  cuentaComoSelect: function (v) {
    var vals = [['ninguno', 'No cuenta'], ['si', 'Cuenta como Sí'], ['no', 'Cuenta como No'], ['favorable', 'Favorable'], ['desfavorable', 'Desfavorable']];
    return '<select class="form-select form-select-sm bnc-o-cuenta">' + vals.map(function (p) {
      return '<option value="' + p[0] + '"' + (p[0] === (v || 'ninguno') ? ' selected' : '') + '>' + p[1] + '</option>';
    }).join('') + '</select>';
  },

  agregarOpcion: function (o) {
    o = o || {};
    var termina = o.accion === 'terminar';
    var h = '<div class="bnc-opcion">' +
      '<input type="text" class="form-control form-control-sm bnc-o-texto" placeholder="Texto de la opción" value="' + BANCO.esc(o.texto_opcion || '') + '">' +
      '<input type="number" step="any" class="form-control form-control-sm bnc-o-valor" placeholder="Valor" value="' + (o.valor_numerico !== null && o.valor_numerico !== undefined && o.valor_numerico !== '' ? parseFloat(o.valor_numerico) : '') + '">' +
      BANCO.cuentaComoSelect(o.cuenta_como) +
      '<select class="form-select form-select-sm bnc-o-accion"><option value="continuar"' + (termina ? '' : ' selected') + '>Continuar</option><option value="terminar"' + (termina ? ' selected' : '') + '>Terminar encuesta</option></select>' +
      '<button type="button" class="btn btn-sm btn-outline-danger bnc-o-quitar" title="Quitar"><i class="fas fa-times"></i></button>' +
      '<input type="text" class="form-control form-control-sm bnc-msg bnc-o-mensaje' + (termina ? '' : ' d-none') + '" placeholder="Mensaje al terminar" value="' + BANCO.esc(o.mensaje_terminar || '') + '">' +
      '</div>';
    $('#bOpciones').append(h);
  },

  ajustarForm: function () {
    var sondeo = $('#bUso').val() === 'sondeo';
    $('.bnc-solo-sondeo').toggleClass('d-none', !sondeo);
    $('.bnc-solo-encuesta').toggleClass('d-none', sondeo);
    var tipo = $('#bTipo').val();
    $('#bLimiteWrap').toggle(!sondeo && tipo === 'Seleccion_Multiple_multiple_respuesta');
    $('#bPlantillaWrap').toggle(!sondeo && (tipo === 'Grilla'));
    var sinOpciones = sondeo ? ['No Aplica', 'Grilla'].indexOf($('#bTipoSondeo').val()) !== -1 : ['Texto Libre', 'Grilla', 'Lista_desde_grilla'].indexOf(tipo) !== -1;
    $('#bOpcionesWrap').toggle(!sinOpciones);
    $('.bnc-o-cuenta, .bnc-o-accion, .bnc-o-valor').closest('.bnc-opcion').find('.bnc-o-cuenta, .bnc-o-accion, .bnc-o-valor').toggle(!sondeo);
    $('.bnc-opcion .bnc-o-mensaje').each(function () {
      $(this).toggleClass('d-none', sondeo || $(this).closest('.bnc-opcion').find('.bnc-o-accion').val() !== 'terminar');
    });
  },

  cargarPlantillas: function (seleccion) {
    var pintar = function () {
      var opts = '<option value="">Seleccione la tabla de candidatos</option>';
      BANCO.plantillas.forEach(function (p) {
        opts += '<option value="' + p.id + '">' + BANCO.esc(p.nombre) + '</option>';
      });
      $('#bPlantilla').html(opts).val(seleccion || '');
    };
    if (BANCO.plantillas) {
      pintar();
      return;
    }
    $.ajax({
      url: 'admin/ajax/rqst.php', method: 'POST', dataType: 'json', data: { op: 'grillaplantillaget' },
      success: function (res) {
        BANCO.plantillas = (res.output && res.output.valid) ? (res.output.response || []) : [];
        pintar();
      },
      error: function () {
        BANCO.plantillas = [];
        pintar();
      }
    });
  },

  abrirForm: function (f) {
    f = f || {};
    $('#bId').val(f.id || '');
    $('#bncFormTitulo').text(f.id ? 'Editar pregunta del banco' : 'Nueva pregunta del banco');
    $('#bUso').val(f.uso || 'encuesta').prop('disabled', !!f.id);
    $('#bCategoria').val(f.categoria || '');
    $('#bTipo').val(f.tipo_pregunta || '');
    $('#bTipoSondeo').val(f.tipo_sondeo || 'No Aplica');
    $('#bTexto').val(f.texto_pregunta || '');
    $('#bDescripcion').val(f.descripcion_sondeo || '');
    $('#bInferenciales').val(f.tipo_inferenciales || 'Pronostico');
    $('#bTrivia').val(f.es_trivia === 'si' ? 'si' : 'no');
    $('#bCapitulo').val(f.capitulo || '');
    $('#bNumeral').val(f.numeral || '');
    $('#bLimite').val(f.limite_respuesta_multiple || 2);
    $('#bEnunciado').val(f.enunciado_pregunta || '');
    $('#bLibreto').val(f.libreto_encuestador || '');
    $('#bNota').val(f.nota_tabulacion || '');
    $('#bObligatoria').prop('checked', f.id ? String(f.obligatoria) === '1' : true);
    $('#bFiltro').prop('checked', String(f.es_filtro) === '1');
    BANCO.cargarPlantillas(f.tbl_grilla_plantilla_id);
    $('#bOpciones').empty();
    (f.opciones || []).forEach(BANCO.agregarOpcion);
    if (!f.id) {
      BANCO.agregarOpcion();
      BANCO.agregarOpcion();
    }
    BANCO.ajustarForm();
    BANCO._extra = { copiar_a_votante_campo: f.copiar_a_votante_campo, rol_resultado: f.rol_resultado, es_variable_cruce: f.es_variable_cruce, etiqueta_cruce: f.etiqueta_cruce };
    bootstrap.Modal.getOrCreateInstance(document.getElementById('bncModalForm')).show();
  },

  recogerForm: function () {
    var sondeo = $('#bUso').val() === 'sondeo';
    var opciones = [];
    if ($('#bOpcionesWrap').is(':visible')) {
      $('#bOpciones .bnc-opcion').each(function () {
        var $o = $(this);
        var texto = $.trim($o.find('.bnc-o-texto').val());
        if (!texto) return;
        opciones.push({
          texto_opcion: texto,
          valor_numerico: sondeo ? '' : $o.find('.bnc-o-valor').val(),
          cuenta_como: sondeo ? 'ninguno' : $o.find('.bnc-o-cuenta').val(),
          accion: sondeo ? 'continuar' : $o.find('.bnc-o-accion').val(),
          mensaje_terminar: $o.find('.bnc-o-mensaje').val()
        });
      });
    }
    var ex = BANCO._extra || {};
    return {
      op: 'bancopreguntasave',
      id: $('#bId').val(),
      uso: $('#bUso').val(),
      categoria: $('#bCategoria').val(),
      texto_pregunta: $('#bTexto').val(),
      tipo_pregunta: sondeo ? '' : $('#bTipo').val(),
      tipo_sondeo: sondeo ? $('#bTipoSondeo').val() : '',
      tipo_inferenciales: $('#bInferenciales').val(),
      descripcion_sondeo: $('#bDescripcion').val(),
      es_trivia: $('#bTrivia').val(),
      capitulo: $('#bCapitulo').val(),
      numeral: $('#bNumeral').val(),
      limite_respuesta_multiple: $('#bTipo').val() === 'Seleccion_Multiple_multiple_respuesta' ? $('#bLimite').val() : 1,
      enunciado_pregunta: $('#bEnunciado').val(),
      libreto_encuestador: $('#bLibreto').val(),
      nota_tabulacion: $('#bNota').val(),
      obligatoria: $('#bObligatoria').is(':checked') ? 1 : 0,
      es_filtro: $('#bFiltro').is(':checked') ? 1 : 0,
      tbl_grilla_plantilla_id: $('#bTipo').val() === 'Grilla' ? $('#bPlantilla').val() : '',
      copiar_a_votante_campo: ex.copiar_a_votante_campo || '',
      rol_resultado: ex.rol_resultado || 'ninguno',
      es_variable_cruce: ex.es_variable_cruce || 0,
      etiqueta_cruce: ex.etiqueta_cruce || '',
      opciones: JSON.stringify(opciones)
    };
  },

  guardar: function (permitirDuplicado) {
    var q = BANCO.recogerForm();
    if (!$.trim(q.texto_pregunta)) {
      UTIL.mostrarMensajeValidacion('Escriba el texto de la pregunta.');
      return;
    }
    if (q.uso === 'encuesta' && !q.tipo_pregunta) {
      UTIL.mostrarMensajeValidacion('Seleccione el tipo de pregunta.');
      return;
    }
    if (permitirDuplicado) q.permitir_duplicado = 1;
    BANCO.ajax(q, function (res) {
      if (res.output && res.output.valid) {
        bootstrap.Modal.getInstance(document.getElementById('bncModalForm')).hide();
        UTIL.mostrarMensajeExitoso('Pregunta guardada en el banco.');
        BANCO.cargarCategorias(BANCO.cargar);
        return;
      }
      if (res.output && res.output.duplicado) {
        Swal.fire({
          title: 'Esa pregunta ya existe',
          text: 'Hay una pregunta igual en el banco (mismo texto, tipo y opciones). ¿Desea guardarla de todas formas?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Guardar igual',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#20427F'
        }).then(function (r) {
          if (r.isConfirmed) BANCO.guardar(true);
        });
        return;
      }
      UTIL.mostrarMensajeError(BANCO.mensajeError(res, 'No se pudo guardar la pregunta.'));
    });
  },

  eliminar: function (id) {
    Swal.fire({
      title: 'Eliminar del banco',
      text: 'La pregunta dejará de aparecer en el banco. Las encuestas y sondeos que ya la usaron no cambian.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#B42318'
    }).then(function (r) {
      if (!r.isConfirmed) return;
      BANCO.ajax({ op: 'bancopreguntadelete', id: id }, function (res) {
        if (res.output && res.output.valid) {
          UTIL.mostrarMensajeExitoso('Pregunta eliminada del banco.');
          BANCO.cargarCategorias(BANCO.cargar);
        } else {
          UTIL.mostrarMensajeError(BANCO.mensajeError(res, 'No se pudo eliminar.'));
        }
      });
    });
  },

  init: function () {
    var tmr = null;
    $('#fBuscar').on('input', function () {
      clearTimeout(tmr);
      tmr = setTimeout(BANCO.cargar, 300);
    });
    $('#fUso, #fCategoria').on('change', BANCO.cargar);
    $('#btnNuevaBanco').on('click', function () { BANCO.abrirForm(); });
    $('#bUso, #bTipo, #bTipoSondeo').on('change', BANCO.ajustarForm);
    $('#bAgregarOpcion').on('click', function () {
      BANCO.agregarOpcion();
      BANCO.ajustarForm();
    });
    $('#bGuardar').on('click', function () { BANCO.guardar(false); });
    $(document).on('click', '.bnc-o-quitar', function () { $(this).closest('.bnc-opcion').remove(); });
    $(document).on('change', '.bnc-o-accion', BANCO.ajustarForm);
    $(document).on('click', '.bnc-ver', function () { BANCO.verDetalle($(this).data('id')); });
    $(document).on('click', '.bnc-editar', function () { BANCO.obtener($(this).data('id'), BANCO.abrirForm); });
    $(document).on('click', '.bnc-eliminar', function () { BANCO.eliminar($(this).data('id')); });
    BANCO.cargarCategorias(BANCO.cargar);
  }
};

$(function () {
  BANCO.init();
});
