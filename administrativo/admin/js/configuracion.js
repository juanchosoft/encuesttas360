$(document).on('ready', init);

let qConfig = {};
let informacionMunicipio = {};
let isSaving = false;

function init() {
  qConfig = {};
}

/**
 * Helpers UI (no rompe si no existen ciertas librerías)
 */
function safeCursorNormal() {
  try { UTIL.cursorNormal(); } catch (e) {}
}
function showError(msg) {
  try { UTIL.mostrarMensajeError(msg); }
  catch (e) { alert(msg); }
}
function showSuccess(msg) {
  try { UTIL.mostrarMensajeExitoso(msg); }
  catch (e) { alert(msg); }
}
function setBtnLoading(isLoading) {
  // Intenta detectar el botón "Guardar" por onclick o por texto
  const $btn =
    $("button[onclick*='CONFIGURACION.savedata']").first().length
      ? $("button[onclick*='CONFIGURACION.savedata']").first()
      : $("button:contains('Guardar cambios')").first().length
        ? $("button:contains('Guardar cambios')").first()
        : $("button:contains('Guardar')").first();

  if (!$btn || !$btn.length) return;

  if (isLoading) {
    $btn.data("old-html", $btn.html());
    $btn.prop("disabled", true);
    $btn.html(`<i class="fas fa-circle-notch fa-spin me-2"></i>Guardando...`);
  } else {
    const old = $btn.data("old-html");
    if (old) $btn.html(old);
    $btn.prop("disabled", false);
  }
}

function markInvalid($el, invalid) {
  if (!$el || !$el.length) return;
  $el.toggleClass("is-invalid", !!invalid);
}

/**
 * Validación mínima (frontend) para evitar guardar vacío
 */
function validateForm() {
  const $nombre = $("#nombre_proyecto");
  const $dep = $("#departamentoId");
  const $mun = $("#tbl_municipio_id");
  const $ver = $("#tbl_vereda_id");

  const nombre = ($nombre.val() || "").trim();
  const dep = $dep.val();
  const mun = $mun.val();
  const ver = $ver.val();

  // Reset
  markInvalid($nombre, false);
  markInvalid($dep, false);
  markInvalid($mun, false);
  markInvalid($ver, false);

  let ok = true;

  if (nombre.length < 2) { markInvalid($nombre, true); ok = false; }
  if (!dep) { markInvalid($dep, true); ok = false; }
  if (!mun) { markInvalid($mun, true); ok = false; }
  if (!ver) { markInvalid($ver, true); ok = false; }

  if (!ok) {
    showError("Por favor completa los campos obligatorios antes de guardar.");
  }
  return ok;
}

const CONFIGURACION = {

  getMunicipios: function () {
    return new Promise((resolve) => {
      const departamentoId = $("#departamentoId").val();

      if (departamentoId && departamentoId !== "seleccione") {
        const q = { op: "ciudadget", codigo_departamento: departamentoId };

        UTIL.callAjaxRqstPOST(q, (data) => {
          CONFIGURACION.getMunicipiosHandler(data);
          resolve();
        });
      } else {
        $("#tbl_municipio_id").empty().append('<option value="" disabled selected>Seleccione...</option>');
        $("#tbl_vereda_id").empty().append('<option value="" disabled selected>Seleccione...</option>');
        resolve();
      }
    });
  },

  getMunicipiosHandler: function (data) {
    safeCursorNormal();

    if (data?.output?.valid) {
      const res = data.output.response || [];
      let info = '';
      for (let j in res) {
        info += `<option value="${res[j].codigo_muncipio}">${res[j].municipio}</option>`;
      }

      $("#tbl_municipio_id").empty().append('<option value="" disabled selected>Seleccione...</option>' + info);
    } else {
      showError(data?.output?.response?.content || "No fue posible cargar municipios.");
    }
  },

  getVeredasByMunicipioId: function () {
    return new Promise((resolve) => {
      const municipioId = $("#tbl_municipio_id").val();

      if (municipioId && municipioId !== "seleccione") {
        const q = { op: "veredaget", municipio_id: municipioId };

        UTIL.callAjaxRqstPOST(q, (data) => {
          CONFIGURACION.getVeredasByMunicipioIdHandler(data);
          resolve();
        });
      } else {
        $("#tbl_vereda_id").empty().append('<option value="" disabled selected>Seleccione...</option>');
        resolve();
      }
    });
  },

  getVeredasByMunicipioIdHandler: function (data) {
    informacionMunicipio = {};
    safeCursorNormal();

    if (data?.output?.valid) {
      const res = data.output.response || [];
      informacionMunicipio = (data.output.municipio && data.output.municipio[0]) ? data.output.municipio[0] : {};

      let info = '';
      for (let j in res) {
        info += `<option value="${res[j].id}">${res[j].nombre_vereda}</option>`;
      }

      $("#tbl_vereda_id").empty().append('<option value="" disabled selected>Seleccione...</option>' + info);
    } else {
      showError(data?.output?.response?.content || "No fue posible cargar veredas.");
    }
  },

  editdata: function () {
    qConfig = { op: "pms_getconf" };
    UTIL.callAjaxRqstPOST(qConfig, CONFIGURACION.editdatahandler);
  },

  editdatahandler: async function (data) {
    safeCursorNormal();

    if (!data?.output?.valid) {
      showError(data?.output?.response?.content || "No se pudo cargar la configuración.");
      return;
    }

    const res = (data.output.response && data.output.response[0]) ? data.output.response[0] : {};

    $("#idConfig").val(res.id || "");
    $("#nombre_proyecto").val(res.nombre_proyecto || "").trigger("change");

    // ✅ En tu vista este select está comentado: evitamos error si no existe
    if ($("#tipo_configuracion_colores").length) {
      $("#tipo_configuracion_colores").val(res.tipo_configuracion_colores || "").trigger("change");
    }

    $("#comentarios").val(res.comentarios || "").trigger("change");
    $("#opcion_activa_web").val(res.opcion_activa_web || "sondeo").trigger("change");

    // Logo preview
    if (res.logo) {
      $("#logoPreview").attr("src", `${res.logo}`);
      $("#divLogoActual").show();
    } else {
      $("#logoPreview").attr("src", "");
      $("#divLogoActual").hide();
    }

    await cargarUbicacionEdicion(res);
  },

  successMessage: function () {
    showSuccess('Información guardada correctamente');

    // ✅ Si existe return_page, respétalo; si no, recarga
    setTimeout(function () {
      try {
        if (typeof return_page !== "undefined" && return_page) {
          window.location = return_page;
        } else {
          window.location.reload();
        }
      } catch (e) {
        window.location.reload();
      }
    }, 900);
  },

  savedata: function () {
    if (isSaving) return;
    if (!validateForm()) return;

    isSaving = true;
    setBtnLoading(true);

    // ✅ Si el iframe setea data-url en #ifm1 lo tomamos, si no, mandamos string vacío (no null)
    const iframeLogo = ($("#ifm1").attr("data-url") || "").trim();

    const q = {
      op: "pms_confsave",
      id: $("#idConfig").val(),
      nombre_proyecto: ($("#nombre_proyecto").val() || "").trim(),
      comentarios: ($("#comentarios").val() || "").trim(),

      // ✅ existe o no existe el select, no lo rompas
      tipo_configuracion_colores: $("#tipo_configuracion_colores").length ? $("#tipo_configuracion_colores").val() : "",

      codigo_departamento: $("#departamentoId").val(),
      codigo_municipio: $("#tbl_municipio_id").val(),
      tbl_vereda_id: $("#tbl_vereda_id").val(),
      opcion_activa_web: $("#opcion_activa_web").val(),
      logo: iframeLogo
    };

    UTIL.callAjaxRqstPOST(q, (data) => {
      CONFIGURACION.savedataHandler(data);
      isSaving = false;
      setBtnLoading(false);
    });
  },

  savedataHandler: function (data) {
    safeCursorNormal();

    if (data?.output?.valid) {
      CONFIGURACION.successMessage();
    } else {
      showError(data?.output?.response?.content || "No fue posible guardar la configuración.");
    }
  }
};

async function cargarUbicacionEdicion(res) {
  // Set departamento
  $("#departamentoId").val(res.codigo_departamento || "").trigger("change");

  await CONFIGURACION.getMunicipios();
  await esperarOpcionYSeleccionar("#tbl_municipio_id", res.codigo_municipio);

  await CONFIGURACION.getVeredasByMunicipioId();
  await esperarOpcionYSeleccionar("#tbl_vereda_id", res.tbl_vereda_id);
}

function esperarOpcionYSeleccionar(selector, valorBuscado) {
  return new Promise((resolve) => {
    if (valorBuscado === null || typeof valorBuscado === "undefined" || valorBuscado === "") {
      resolve();
      return;
    }

    const maxRetries = 60; // 6 segundos
    let tries = 0;

    const interval = setInterval(() => {
      const $select = $(selector);
      const optionExiste = $select.find(`option[value='${valorBuscado}']`).length > 0;

      if (optionExiste) {
        $select.val(valorBuscado).trigger("change");
        clearInterval(interval);
        resolve();
      } else if (++tries >= maxRetries) {
        clearInterval(interval);
        resolve();
      }
    }, 100);
  });
}
