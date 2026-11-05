var ASIGNACIONENCUESTADORES = (function () {
  function esc(t) {
    return String(t == null ? '' : t)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function msgError(texto) {
    if (window.UTIL && UTIL.mostrarMensajeError) { UTIL.mostrarMensajeError(texto); return; }
    Swal.fire({ icon: 'error', title: texto });
  }

  function mensajeDe(data, porDefecto) {
    var o = data && data.output;
    if (o && o.response && o.response.content) return o.response.content;
    if (o && o.message) return o.message;
    return porDefecto;
  }

  function panel(opts) {
    var vacio = opts.items.length === 0;
    var html = '<div class="text-start">' +
      '<p class="small text-muted mb-2">' + opts.ayuda + '</p>' +
      (vacio ? '<div class="alert alert-warning small mb-0">' + opts.vacio + '</div>' :
        '<input type="search" id="asgBuscar" class="form-control form-control-sm mb-2" placeholder="Buscar...">' +
        '<div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="asgTodos">' +
        '<label class="form-check-label small fw-bold" for="asgTodos">Seleccionar todos los visibles</label></div>' +
        '<div id="asgLista" style="max-height:320px;overflow:auto;border:1px solid #dee2e6;border-radius:8px;padding:6px 10px;">' +
        opts.items.map(function (it) {
          return '<div class="form-check py-1 asg-item" data-texto="' + esc((it.etiqueta + ' ' + (it.detalle || '')).toLowerCase()) + '">' +
            '<input class="form-check-input asg-chk" type="checkbox" id="asg_' + it.id + '" value="' + it.id + '"' + (it.asignado ? ' checked' : '') + '>' +
            '<label class="form-check-label" for="asg_' + it.id + '">' + esc(it.etiqueta) +
            (it.detalle ? ' <small class="text-muted">' + esc(it.detalle) + '</small>' : '') +
            (it.aviso ? ' <span class="badge bg-secondary">' + esc(it.aviso) + '</span>' : '') +
            '</label></div>';
        }).join('') +
        '</div><div class="small text-muted mt-2" id="asgContador"></div>') +
      '</div>';
    return html;
  }

  function enlazar() {
    var $lista = $('#asgLista');
    function contar() {
      var n = $lista.find('.asg-chk:checked').length;
      $('#asgContador').text(n + ' seleccionado(s)');
    }
    $('#asgBuscar').on('input', function () {
      var t = $(this).val().toString().toLowerCase().trim();
      $lista.find('.asg-item').each(function () {
        $(this).toggle(t === '' || $(this).attr('data-texto').indexOf(t) !== -1);
      });
    });
    $('#asgTodos').on('change', function () {
      var marcar = this.checked;
      $lista.find('.asg-item:visible .asg-chk').prop('checked', marcar);
      contar();
    });
    $lista.on('change', '.asg-chk', contar);
    contar();
  }

  function seleccion() {
    return $('#asgLista .asg-chk:checked').map(function () { return parseInt(this.value, 10); }).get();
  }

  function abrir(cfg) {
    UTIL.callAjaxRqstPOST(cfg.cargar, function (data) {
      try { UTIL.cursorNormal(); } catch (e) {}
      if (!data || !data.output || !data.output.valid) {
        msgError(mensajeDe(data, 'No fue posible cargar la información.'));
        return;
      }
      var res = data.output.response;
      var items = cfg.mapear(res);
      Swal.fire({
        title: cfg.titulo(res),
        html: panel({ items: items, ayuda: cfg.ayuda, vacio: cfg.vacio }),
        width: 640,
        showCancelButton: true,
        showConfirmButton: items.length > 0,
        confirmButtonText: 'Guardar asignación',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        didOpen: enlazar,
        preConfirm: function () {
          return new Promise(function (resolve) {
            var q = cfg.guardar(seleccion());
            UTIL.callAjaxRqstPOST(q, function (r) {
              try { UTIL.cursorNormal(); } catch (e) {}
              if (r && r.output && r.output.valid) { resolve(true); return; }
              Swal.showValidationMessage(mensajeDe(r, 'No fue posible guardar.'));
              resolve(false);
            });
          });
        }
      }).then(function (r) {
        if (r.isConfirmed) {
          Swal.fire({ icon: 'success', title: 'Asignación guardada', timer: 1400, showConfirmButton: false });
        }
      });
    });
  }

  return {
    porFicha: function (fichaId) {
      abrir({
        cargar: { op: 'asignacionencuestadoresget', ficha_id: fichaId },
        titulo: function (res) { return 'Encuestadores de: ' + res.titulo; },
        ayuda: 'Marca los encuestadores que pueden aplicar esta encuesta. Solo la verán si está activa.',
        vacio: 'No hay usuarios con rol Encuestador. Créalos en Configuración &rarr; Usuarios.',
        mapear: function (res) {
          return res.items.map(function (u) {
            return { id: u.id, etiqueta: u.nombre, detalle: u.correo, asignado: u.asignado, aviso: u.activo ? '' : 'Usuario inactivo' };
          });
        },
        guardar: function (ids) { return { op: 'asignacionencuestadoressave', ficha_id: fichaId, usuarios_ids: JSON.stringify(ids) }; }
      });
    },
    porUsuario: function (usuarioId, nombre) {
      abrir({
        cargar: { op: 'asignacionfichasget', usuario_id: usuarioId },
        titulo: function () { return 'Encuestas de: ' + (nombre || 'encuestador'); },
        ayuda: 'Marca las encuestas que este encuestador puede aplicar. Solo verá las que estén activas.',
        vacio: 'No hay encuestas creadas.',
        mapear: function (res) {
          return res.items.map(function (f) {
            return { id: f.id, etiqueta: f.nombre, detalle: '#' + f.id, asignado: f.asignado, aviso: f.activa ? '' : 'Inactiva' };
          });
        },
        guardar: function (ids) { return { op: 'asignacionfichassave', usuario_id: usuarioId, fichas_ids: JSON.stringify(ids) }; }
      });
    }
  };
})();
