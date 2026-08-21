/**
 * Fase B — Geolocalización → DANE (solo afecta listado de encuestas).
 */
(function (global) {
  'use strict';

  var RQST = 'admin/ajax/rqst.php';

  function resolveFromCoords(lat, lng) {
    var body = new URLSearchParams();
    body.set('op', 'georesolverdane');
    body.set('lat', String(lat));
    body.set('lng', String(lng));
    return fetch(RQST, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString(),
      credentials: 'same-origin'
    }).then(function (r) { return r.json(); });
  }

  function obtenerUbicacion(options) {
    options = options || {};
    return new Promise(function (resolve, reject) {
      if (!navigator.geolocation) {
        reject({ code: 'UNSUPPORTED', message: 'La geolocalización no es soportada por este navegador.' });
        return;
      }
      navigator.geolocation.getCurrentPosition(
        function (pos) {
          resolveFromCoords(pos.coords.latitude, pos.coords.longitude)
            .then(function (data) {
              if (data && data.output && data.output.valid) {
                resolve(data.output.response || {});
              } else {
                reject({
                  code: 'RESOLVE_FAIL',
                  message: (data && data.output && data.output.response && data.output.response.content)
                    || 'No se pudo resolver tu ubicación.'
                });
              }
            })
            .catch(function () {
              reject({ code: 'NETWORK', message: 'Error al consultar el servicio de ubicación.' });
            });
        },
        function (err) {
          var msg = 'No se pudo obtener tu ubicación.';
          if (err && err.code === 1) msg = 'Permiso de ubicación denegado.';
          if (err && err.code === 2) msg = 'Ubicación no disponible.';
          if (err && err.code === 3) msg = 'Tiempo de espera agotado al obtener ubicación.';
          reject({ code: 'GEO_ERROR', message: msg, raw: err });
        },
        {
          enableHighAccuracy: !!(options.enableHighAccuracy),
          timeout: options.timeout || 15000,
          maximumAge: options.maximumAge || 60000
        }
      );
    });
  }

  global.GeoParticipacion = {
    obtenerUbicacion: obtenerUbicacion,
    resolveFromCoords: resolveFromCoords
  };
})(window);
