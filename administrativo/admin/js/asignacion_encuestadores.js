var ASIGNACIONENCUESTADORES = (function () {
  var munSel = {};
  var catalogoFijo = [];
  var cats = {};
  var cfgActual = null;

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

  function resumenMunicipios(uid) {
    var n = (munSel[uid] || []).length;
    return n === 0 ? 'Municipios: libre dentro de la encuesta' : 'Municipios: ' + n + ' asignado(s)';
  }

  function panel(opts) {
    var vacio = opts.items.length === 0;
    var conMun = !!opts.conMunicipios;
    var html = '<div class="text-start">' +
      '<p class="small text-muted mb-2">' + opts.ayuda + '</p>' +
      (vacio ? '<div class="alert alert-warning small mb-0">' + opts.vacio + '</div>' :
        '<input type="search" id="asgBuscar" class="form-control form-control-sm mb-2" placeholder="Buscar...">' +
        '<div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="asgTodos">' +
        '<label class="form-check-label small fw-bold" for="asgTodos">Seleccionar todos los visibles</label></div>' +
        '<div id="asgLista" style="max-height:58vh;overflow:auto;border:1px solid #dee2e6;border-radius:8px;padding:6px 10px;">' +
        opts.items.map(function (it) {
          return '<div class="py-1 asg-item" data-texto="' + esc((it.etiqueta + ' ' + (it.detalle || '')).toLowerCase()) + '">' +
            '<div class="form-check">' +
            '<input class="form-check-input asg-chk" type="checkbox" id="asg_' + it.id + '" value="' + it.id + '"' + (it.asignado ? ' checked' : '') + '>' +
            '<label class="form-check-label" for="asg_' + it.id + '">' + esc(it.etiqueta) +
            (it.detalle ? ' <small class="text-muted">' + esc(it.detalle) + '</small>' : '') +
            (it.aviso ? ' <span class="badge bg-secondary">' + esc(it.aviso) + '</span>' : '') +
            '</label></div>' +
            (conMun ? '<div class="asg-mun-wrap ms-4 mt-1" data-uid="' + it.id + '"' + (it.asignado ? '' : ' style="display:none"') + '>' +
              '<button type="button" class="btn btn-sm btn-outline-secondary asg-mun-btn" data-uid="' + it.id + '"><i class="bi bi-geo-alt"></i> <span class="asg-mun-resumen">' + esc(resumenMunicipios(it.id)) + '</span></button>' +
              '<div class="asg-mun-box" data-uid="' + it.id + '" style="display:none;border:1px solid #dee2e6;border-radius:8px;padding:6px 10px;margin-top:6px;"></div>' +
              '</div>' : '') +
            '</div>';
        }).join('') +
        '</div><div class="small text-muted mt-2" id="asgContador"></div>') +
      '</div>';
    return html;
  }

  function obtenerCatalogo(id, cb) {
    if (cfgActual.catalogo) { cb(catalogoFijo); return; }
    if (cats[id]) { cb(cats[id]); return; }
    UTIL.callAjaxRqstPOST({ op: 'asignacionmunicipioscatalogo', ficha_id: id }, function (r) {
      try { UTIL.cursorNormal(); } catch (e) {}
      if (!r || !r.output || !r.output.valid) { msgError(mensajeDe(r, 'No fue posible cargar los municipios.')); return; }
      cats[id] = r.output.response.catalogo || [];
      cb(cats[id]);
    });
  }

  function construirCajaMunicipios($box, catalogo) {
    var uid = parseInt($box.attr('data-uid'), 10);
    var marcados = munSel[uid] || [];
    var grupos = {};
    var orden = [];
    catalogo.forEach(function (m) {
      if (!grupos[m.codigo_departamento]) { grupos[m.codigo_departamento] = { nombre: m.departamento, items: [] }; orden.push(m.codigo_departamento); }
      grupos[m.codigo_departamento].items.push(m);
    });
    var html = '<input type="search" class="form-control form-control-sm mb-2 asg-mun-buscar" placeholder="Buscar municipio...">' +
      '<div class="small mb-1"><a href="#" class="asg-mun-limpiar">Quitar todos (libre)</a></div>' +
      '<div class="asg-mun-lista" style="max-height:280px;overflow:auto;">';
    orden.forEach(function (dep) {
      var g = grupos[dep];
      html += '<div class="asg-mun-dep" data-dep="' + esc(dep) + '"><div class="small fw-bold mt-1">' + esc(g.nombre) +
        ' <a href="#" class="asg-mun-dep-todos fw-normal ms-1" data-dep="' + esc(dep) + '">marcar departamento</a></div>';
      g.items.forEach(function (m) {
        html += '<div class="form-check asg-mun-item" data-texto="' + esc((m.municipio + ' ' + m.departamento).toLowerCase()) + '">' +
          '<input class="form-check-input asg-mun-chk" type="checkbox" id="asgm_' + uid + '_' + m.codigo + '" data-uid="' + uid + '" data-dep="' + esc(dep) + '" value="' + esc(m.codigo) + '"' + (marcados.indexOf(m.codigo) !== -1 ? ' checked' : '') + '>' +
          '<label class="form-check-label small" for="asgm_' + uid + '_' + m.codigo + '">' + esc(m.municipio) + '</label></div>';
      });
      html += '</div>';
    });
    html += '</div>';
    $box.html(html).attr('data-listo', '1');
  }

  function actualizarResumen(uid) {
    $('.asg-mun-wrap[data-uid="' + uid + '"] .asg-mun-resumen').text(resumenMunicipios(uid));
  }

  function leerCaja(uid) {
    munSel[uid] = $('.asg-mun-box[data-uid="' + uid + '"] .asg-mun-chk:checked').map(function () { return this.value; }).get();
    actualizarResumen(uid);
  }

  function enlazar() {
    var $lista = $('#asgLista');
    function contar() {
      var n = $lista.find('.asg-chk:checked').length;
      $('#asgContador').text(n + ' seleccionado(s)');
      $lista.find('.asg-mun-wrap').each(function () {
        var uid = $(this).attr('data-uid');
        $(this).toggle($('#asg_' + uid).is(':checked'));
      });
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

    $lista.on('click', '.asg-mun-btn', function () {
      var uid = $(this).attr('data-uid');
      var $box = $lista.find('.asg-mun-box[data-uid="' + uid + '"]');
      if (!$box.attr('data-listo')) {
        obtenerCatalogo(uid, function (cat) { construirCajaMunicipios($box, cat); $box.show(); });
        return;
      }
      $box.toggle();
    });
    $lista.on('change', '.asg-mun-chk', function () {
      leerCaja($(this).attr('data-uid'));
    });
    $lista.on('input', '.asg-mun-buscar', function () {
      var t = $(this).val().toString().toLowerCase().trim();
      var $box = $(this).closest('.asg-mun-box');
      $box.find('.asg-mun-item').each(function () {
        $(this).toggle(t === '' || $(this).attr('data-texto').indexOf(t) !== -1);
      });
      $box.find('.asg-mun-dep').each(function () {
        $(this).toggle($(this).find('.asg-mun-item:visible').length > 0);
      });
    });
    $lista.on('click', '.asg-mun-limpiar', function (e) {
      e.preventDefault();
      var $box = $(this).closest('.asg-mun-box');
      $box.find('.asg-mun-chk').prop('checked', false);
      leerCaja($box.attr('data-uid'));
    });
    $lista.on('click', '.asg-mun-dep-todos', function (e) {
      e.preventDefault();
      var $box = $(this).closest('.asg-mun-box');
      $box.find('.asg-mun-chk[data-dep="' + $(this).attr('data-dep') + '"]:visible').prop('checked', true);
      leerCaja($box.attr('data-uid'));
    });
    contar();
  }

  function seleccion() {
    return $('#asgLista .asg-chk:checked').map(function () { return parseInt(this.value, 10); }).get();
  }

  function municipiosSeleccionados(ids) {
    var r = {};
    ids.forEach(function (uid) {
      if ((munSel[uid] || []).length) r[uid] = munSel[uid];
    });
    return r;
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
      munSel = {};
      cfgActual = cfg;
      cats = {};
      catalogoFijo = cfg.catalogo ? cfg.catalogo(res) : [];
      items.forEach(function (it) { if (it.municipios && it.municipios.length) munSel[it.id] = it.municipios.slice(); });
      Swal.fire({
        title: cfg.titulo(res),
        html: panel({ items: items, ayuda: cfg.ayuda(res), vacio: cfg.vacio, conMunicipios: !!(cfg.catalogo || cfg.municipiosPorItem) }),
        width: 680,
        showCancelButton: true,
        showConfirmButton: items.length > 0,
        confirmButtonText: 'Guardar asignación',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        didOpen: enlazar,
        preConfirm: function () {
          return new Promise(function (resolve) {
            var ids = seleccion();
            var q = cfg.guardar(ids, municipiosSeleccionados(ids));
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
        ayuda: function (res) {
          var alcance = { nacional: 'nacional (cualquier municipio)', departamental: 'departamental', municipal: 'municipal' }[String(res.tipo_estudio).toLowerCase()] || res.tipo_estudio;
          return 'Marca los encuestadores que pueden aplicar esta encuesta. Solo la verán si está activa.<br>' +
            'Espacio geográfico: <b>' + esc(alcance) + '</b>. Opcional: con el botón de municipios puedes limitar a cada encuestador a ciertos municipios; ' +
            'si no le asignas ninguno, elegirá libremente dentro del espacio geográfico de la encuesta.';
        },
        vacio: 'No hay usuarios con rol Encuestador. Créalos en Configuración &rarr; Usuarios.',
        catalogo: function (res) { return res.catalogo || []; },
        mapear: function (res) {
          return res.items.map(function (u) {
            return { id: u.id, etiqueta: u.nombre, detalle: u.correo, asignado: u.asignado, municipios: u.municipios, aviso: u.activo ? '' : 'Usuario inactivo' };
          });
        },
        guardar: function (ids, munis) { return { op: 'asignacionencuestadoressave', ficha_id: fichaId, usuarios_ids: JSON.stringify(ids), municipios: JSON.stringify(munis) }; }
      });
    },
    porUsuario: function (usuarioId, nombre) {
      abrir({
        cargar: { op: 'asignacionfichasget', usuario_id: usuarioId },
        titulo: function () { return 'Encuestas de: ' + (nombre || 'encuestador'); },
        municipiosPorItem: true,
        ayuda: function () { return 'Marca las encuestas que este encuestador puede aplicar. Solo verá las que estén activas. Opcional: con el botón de municipios limitas al encuestador a ciertos municipios de esa encuesta; si no le asignas ninguno, elegirá libremente dentro del espacio geográfico de la encuesta.'; },
        vacio: 'No hay encuestas creadas.',
        mapear: function (res) {
          return res.items.map(function (f) {
            return { id: f.id, etiqueta: f.nombre, detalle: '#' + f.id, asignado: f.asignado, municipios: f.municipios, aviso: f.activa ? '' : 'Inactiva' };
          });
        },
        guardar: function (ids, munis) { return { op: 'asignacionfichassave', usuario_id: usuarioId, fichas_ids: JSON.stringify(ids), municipios: JSON.stringify(munis) }; }
      });
    }
  };
})();
