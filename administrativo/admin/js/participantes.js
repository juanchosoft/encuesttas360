/* =========================================================
   PARTICIPANTES - Versión organizada y lista para producción
   - window.PARTICIPANTES global para onclick/onchange inline
   - init seguro con $(document).ready
   - helpers robustos + defensivos
   - evita fallos si Select2 / DEPARTAMENTO / UTIL no existen
   ========================================================= */

(function () {
  "use strict";

  // -----------------------------------------
  // Config
  // -----------------------------------------
  const RETURN_PAGE = "participantes.php";

  // ⚠️ OJO: no uses "let q = {}" si en el sistema ya existe "q" global (te dio error en usuarios.js).
  // Usamos variable interna segura.
  let req = {};

  // -----------------------------------------
  // Helpers (robustos)
  // -----------------------------------------
  function isEmpty(val) {
    return val === null || val === undefined || String(val).trim() === "" || val === "0";
  }

  function esperarOpcionYSeleccionar(selector, valorBuscado, triggerChange = true) {
    return new Promise((resolve) => {
      const maxRetries = 60; // ~6s
      let tries = 0;

      const interval = setInterval(() => {
        const $select = $(selector);
        const exists = $select.find(`option[value='${valorBuscado}']`).length > 0;

        if (exists) {
          $select.val(valorBuscado);
          if (triggerChange) $select.trigger("change");
          clearInterval(interval);
          resolve(true);
        } else if (++tries >= maxRetries) {
          clearInterval(interval);
          resolve(false);
        }
      }, 100);
    });
  }

  function getIframeUrl(idIframe) {
    const $ifm = $(idIframe);
    const url = $ifm.attr("data-url") || $ifm.data("url") || null;
    return url && String(url).trim() !== "" ? String(url).trim() : "";
  }

  function clearIframeDataUrl(idIframe) {
    const $ifm = $(idIframe);
    $ifm.removeAttr("data-url").removeAttr("data-loaded");
    $ifm.data("url", "");
  }

  function safeVal(sel) {
    const $el = $(sel);
    if (!$el.length) return "";
    const v = $el.val();
    if (Array.isArray(v)) return (v[0] ?? "").toString().trim();
    return (v ?? "").toString().trim();
  }

  function safeMsg(type, msg) {
    // type: ok | warn | err
    try {
      if (window.UTIL) {
        if (type === "ok" && typeof UTIL.mostrarMensajeExitoso === "function") return UTIL.mostrarMensajeExitoso(msg);
        if (type === "warn" && typeof UTIL.mostrarMensajeValidacion === "function") return UTIL.mostrarMensajeValidacion(msg);
        if (typeof UTIL.mostrarMensajeError === "function") return UTIL.mostrarMensajeError(msg);
      }
      alert(msg);
    } catch (e) {
      alert(msg);
    }
  }

  // -----------------------------------------
  // Init
  // -----------------------------------------
  $(document).ready(function init() {
    req = {};

    // Select2 (si existe)
    const $sel = $("#partidoPoliticoId");
    if ($sel.length && typeof $sel.select2 === "function") {
      $sel.select2({
        theme: "bootstrap4",
        placeholder: "Seleccione uno o más partidos",
        allowClear: true,
        width: "100%",
      });
    }

    // Limpia invalid al escribir/cambiar (UX)
    $("#formparticipantes").on("input change", "input, select, textarea", function () {
      $(this).removeClass("is-invalid");
    });

    // Debug suave
    console.log("✅ participantes.js cargado. window.PARTICIPANTES =", window.PARTICIPANTES);
  });

  // -----------------------------------------
  // ✅ OBJETO GLOBAL (para onclick/onchange inline)
  // -----------------------------------------
  window.PARTICIPANTES = window.PARTICIPANTES || {

    editData: function (id) {
      req = { op: "participanteget", id: id };

      if ($("#formparticipantes").length) {
        $("html, body").animate({ scrollTop: $("#formparticipantes").offset().top - 90 }, 600);
      }

      if (!window.UTIL || typeof UTIL.callAjaxRqstPOST !== "function") {
        safeMsg("err", "No se encontró UTIL.callAjaxRqstPOST(). Verifica que cargue generic_script.");
        return;
      }

      UTIL.callAjaxRqstPOST(req, window.PARTICIPANTES.editdataHandler);
    },

    editdataHandler: async function (data) {
      try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}

      if (!data?.output?.valid) {
        safeMsg("err", data?.output?.response?.content || "No fue posible cargar el participante.");
        return;
      }

      const res = data.output.response?.[0] || {};

      $("#idParticipante").val(res.id || "");
      $("#cargoPublicoId").val(res.tbl_cargo_publico_id || "").trigger("change");
      $("#nombre_completo").val(res.nombre_completo || "");
      $("#habilitado").prop("checked", (res.habilitado || "si") === "si");

      $("#numero_candidato").val(res.numero_candidato || "seleccione");
      $("#favorito").val(res.favorito || "no");
      $("#cliente").val(res.cliente || "no");
      $("#puntos").val(res.puntos || "");
      $("#modalidad").val(res.modalidad || "Lista Cerrada");

      // Visibilidad por cargo
      const cargoPublicoId = String(res.tbl_cargo_publico_id || "");
      const showDepartamento = !["1", "2"].includes(cargoPublicoId);
      const showMunicipio = !["1", "2", "3", "4", "5"].includes(cargoPublicoId);

      if (showDepartamento) {
        $("#tbl_departamento_id").val(res.codigo_departamento || "").trigger("change");

        // Cargar municipios
        if (window.DEPARTAMENTO && typeof DEPARTAMENTO.getMunicipios === "function") {
          DEPARTAMENTO.getMunicipios();
        }

        if (showMunicipio) {
          await esperarOpcionYSeleccionar("#tbl_municipio_id", res.codigo_municipio || "", false);
          $("#tbl_municipio_id").val(res.codigo_municipio || "").trigger("change");
        } else {
          $("#tbl_municipio_id").val("").trigger("change");
        }
      } else {
        $("#tbl_departamento_id").val("").trigger("change");
        $("#tbl_municipio_id").val("").trigger("change");
      }

      // Partidos (Select2 multiple)
      if (res.partidoPoliticoIds) {
        const selected = Array.isArray(res.partidoPoliticoIds)
          ? res.partidoPoliticoIds
          : String(res.partidoPoliticoIds)
              .split(",")
              .map((x) => String(x).trim())
              .filter((x) => x !== "");

        $("#partidoPoliticoId").val(selected).trigger("change");
      } else {
        $("#partidoPoliticoId").val(null).trigger("change");
      }

      // Ajustes UI (mostrar/ocultar campos)
      window.PARTICIPANTES.handleCargoPublicoChange();
    },

    validateData: function () {
      // reset invalid
      $("#formparticipantes .is-invalid").removeClass("is-invalid");

      const markInvalid = (sel) => $(sel).addClass("is-invalid");

      const cargoPublicoId = String(safeVal("#cargoPublicoId"));
      const showDepartamento = !["1", "2"].includes(cargoPublicoId);
      const showMunicipio = !["1", "2", "3", "4", "5"].includes(cargoPublicoId);

      const nombre = safeVal("#nombre_completo");
      const dep = safeVal("#tbl_departamento_id");
      const mun = safeVal("#tbl_municipio_id");
      const partidos = $("#partidoPoliticoId").val();

      if (!nombre) {
        markInvalid("#nombre_completo");
        safeMsg("warn", "El nombre completo del participante es requerido.");
        return;
      }

      if (showDepartamento && isEmpty(dep)) {
        markInvalid("#tbl_departamento_id");
        safeMsg("warn", "Por favor, seleccione un Departamento.");
        return;
      }

      if (showMunicipio && isEmpty(mun)) {
        markInvalid("#tbl_municipio_id");
        safeMsg("warn", "Por favor, seleccione un Municipio.");
        return;
      }

      if (!partidos || partidos.length === 0) {
        markInvalid("#partidoPoliticoId");
        safeMsg("warn", "Por favor, seleccione al menos un Partido Político.");
        return;
      }

      window.PARTICIPANTES.savedata();
    },

    savedata: function () {
      req = {
        op: "participantesave",
        id: safeVal("#idParticipante"),
        cargo_publico_id: safeVal("#cargoPublicoId"),
        nombre_completo: safeVal("#nombre_completo"),
        codigo_departamento: safeVal("#tbl_departamento_id"),
        codigo_municipio: safeVal("#tbl_municipio_id"),
        habilitado: $("#habilitado").is(":checked") ? "si" : "no",
        numero_candidato: safeVal("#numero_candidato"),
        favorito: safeVal("#favorito"),
        cliente: safeVal("#cliente"),
        puntos: safeVal("#puntos"),
        modalidad: safeVal("#modalidad"),
      };

      // Partidos multiple -> string CSV
      const selectedPartidos = $("#partidoPoliticoId").val();
      req.partidoPoliticoIds = selectedPartidos ? selectedPartidos.join(",") : "";

      // Foto desde iframe (compat)
      const fotoUrl = getIframeUrl("#ifm1");
      req.foto = fotoUrl;
      req.foto1 = fotoUrl;

      try { if (window.UTIL && typeof UTIL.cursorBusy === "function") UTIL.cursorBusy(); } catch (e) {}

      $.ajax({
        data: req,
        type: "POST",
        dataType: "json",
        url: "admin/ajax/rqst.php",
        success: function (data) {
          req = {};
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}

          if (data?.output?.valid) {
            safeMsg("ok", "Información guardada correctamente");
            setTimeout(() => (window.location = RETURN_PAGE), 900);
          } else {
            safeMsg("err", data?.output?.response?.content || "No fue posible guardar.");
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}
          console.error("AJAX error:", textStatus, errorThrown, jqXHR.responseText);
          safeMsg("err", "Hubo un error de comunicación con el servidor. Intenta de nuevo.");
        },
      });
    },

    deleteData: function (id) {
      if (!id) return;

      if (!confirm("¿Seguro que deseas eliminar este participante? Esta acción no se puede deshacer.")) return;

      try { if (window.UTIL && typeof UTIL.cursorBusy === "function") UTIL.cursorBusy(); } catch (e) {}

      $.ajax({
        data: { op: "participantedelete", id: id },
        type: "POST",
        dataType: "json",
        url: "admin/ajax/rqst.php",
        success: function (data) {
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}

          if (data?.output?.valid) {
            safeMsg("ok", "Participante eliminado correctamente");
            setTimeout(() => (window.location = RETURN_PAGE), 900);
          } else {
            safeMsg("err", data?.output?.response?.content || "No fue posible eliminar.");
          }
        },
        error: function () {
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}
          safeMsg("err", "Error de comunicación con el servidor.");
        },
      });
    },

    handleCargoPublicoChange: function () {
      const cargoPublicoId = String(safeVal("#cargoPublicoId"));

      const $dep = $(".departamento-municipio-fields").eq(0);
      const $mun = $(".departamento-municipio-fields").eq(1);

      // reset
      $dep.addClass("d-none");
      $mun.addClass("d-none");
      $("#tbl_departamento_id").prop("required", false);
      $("#tbl_municipio_id").prop("required", false);

      // ids
      const presidenteId = "1";
      const senadorId = "2";
      const representanteCamaraId = "3";
      const gobernadorId = "4";
      const diputadoId = "5";
      const alcaldeId = "6";
      const concejalId = "7";

      if (cargoPublicoId === presidenteId || cargoPublicoId === senadorId) {
        // nada
      } else if (
        cargoPublicoId === gobernadorId ||
        cargoPublicoId === representanteCamaraId ||
        cargoPublicoId === diputadoId
      ) {
        $dep.removeClass("d-none");
        $("#tbl_departamento_id").prop("required", true);
      } else if (cargoPublicoId === alcaldeId || cargoPublicoId === concejalId) {
        $dep.removeClass("d-none");
        $mun.removeClass("d-none");
        $("#tbl_departamento_id").prop("required", true);
        $("#tbl_municipio_id").prop("required", true);
      }

      window.PARTICIPANTES.handleCargoPublicoChangeRamaEjecutivaLegislativa();
    },

    handleCargoPublicoChangeRamaEjecutivaLegislativa: function () {
      const selectedText = $("#cargoPublicoId option:selected").text() || "";
      const $divNumero = $("#div-numero-candidato");
      const $divModalidad = $("#div-modalidad");

      if (selectedText.includes("Rama Ejecutiva")) {
        $divNumero.hide();
        $divModalidad.hide();
      } else if (selectedText.includes("Rama Legislativa")) {
        $divNumero.show();
        $divModalidad.show();
      } else {
        $divNumero.hide();
        $divModalidad.hide();
      }
    },

    emptyCells: function () {
      $("#idParticipante").val("");
      $("#puntos").val("");
      $("#nombre_completo").val("");
      $("#habilitado").prop("checked", true);
      $("#favorito").val("no");
      $("#cliente").val("no");
      $("#numero_candidato").val("seleccione");
      $("#modalidad").val("Lista Cerrada");

      $("#partidoPoliticoId").val(null).trigger("change");
      $("#cargoPublicoId").prop("selectedIndex", 0).trigger("change");

      $("#tbl_departamento_id").val("").trigger("change");
      $("#tbl_municipio_id").empty().append('<option value="">Seleccione...</option>').trigger("change");

      clearIframeDataUrl("#ifm1");
    },
  };

})();
