/* =========================================================
   PARTIDOS POLÍTICOS - Fix definitivo:
   - PARTIDOS_POLITICOS queda GLOBAL (window.PARTIDOS_POLITICOS) para onclick
   - Evita conflictos de "q redeclaration" usando request local
   - init corre con $(document).ready
   ========================================================= */

(function () {
  "use strict";

  let isSavingPartido = false;
  const return_page = "partidos_politicos.php";

  // ✅ INIT
  $(document).ready(function initPartidos() {
    // Si el form no existe, no hace nada (evita errores en otras páginas)
    if (!$("#formpartidospoliticos").length) return;

    // UX: limpiar invalid al escribir/cambiar
    $("#formpartidospoliticos").on("input change", "input, select, textarea", function () {
      $(this).removeClass("is-invalid");
    });

    // Normaliza: nombre y representante sin dobles espacios
    $("#nombre_partido, #representante_legal").on("blur", function () {
      const v = ($(this).val() || "").replace(/\s+/g, " ").trim();
      $(this).val(v);
    });

    // 🔎 Debug rápido: confirma que el objeto está cargado
    console.log("✅ partidos_politicos.js cargado:", typeof window.PARTIDOS_POLITICOS);
  });

  // ✅ Helper: request seguro (no usa variable global q)
  function ajaxRqst(data, cbOk, cbFail) {
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: data,
      success: cbOk,
      error: cbFail || function () {
        try { UTIL.cursorNormal(); } catch (e) {}
        try { UTIL.mostrarMensajeError("Hubo un error de comunicación con el servidor."); } catch (e) { alert("Error de comunicación."); }
      }
    });
  }

  // ✅ Helper UI
  function setBtnLoading(loading) {
    const $btn =
      $("button[onclick*='PARTIDOS_POLITICOS.validateData']").first().length
        ? $("button[onclick*='PARTIDOS_POLITICOS.validateData']").first()
        : $("button:contains('Guardar')").first();

    if (!$btn.length) return;

    if (loading) {
      $btn.data("old-html", $btn.html());
      $btn.prop("disabled", true);
      $btn.html(`<i class="fas fa-circle-notch fa-spin me-2"></i>Guardando...`);
    } else {
      const old = $btn.data("old-html");
      if (old) $btn.html(old);
      $btn.prop("disabled", false);
    }
  }

  // ✅ GLOBAL para onclick
  window.PARTIDOS_POLITICOS = window.PARTIDOS_POLITICOS || {

    editData: function (id) {
      const rq = { op: "partidopoliticoget", id: id };

      try { UTIL.cursorBusy(); } catch (e) {}

      ajaxRqst(
        rq,
        function (data) {
          try { UTIL.cursorNormal(); } catch (e) {}

          if (!data?.output?.valid) {
            UTIL.mostrarMensajeError(data?.output?.response?.content || "No fue posible cargar el partido.");
            return;
          }

          const res = data.output.response?.[0] || {};

          $("#idPartido").val(res.id || "");
          $("#nombre_partido").val(res.nombre_partido || "");
          $("#representante_legal").val(res.representante_legal || "");
          $("#resolucion").val(res.resolucion || "");

          if ($("#email_contacto").length) $("#email_contacto").val(res.email_contacto || "");

          // Set logo en iframe (si existe)
          if (res.logo) {
            const iframe = document.getElementById("ifm");
            if (iframe) iframe.setAttribute("data-url", res.logo);
          }

          if (typeof UTIL.scrollToTop === "function") {
            UTIL.scrollToTop("formpartidospoliticos");
          } else {
            window.scrollTo({ top: 0, behavior: "smooth" });
          }
        }
      );
    },

    validateData: function () {
      if (isSavingPartido) return;

      $("#formpartidospoliticos .is-invalid").removeClass("is-invalid");

      const nombre = ($("#nombre_partido").val() || "").trim();
      const rep = ($("#representante_legal").val() || "").trim();

      if (!nombre) $("#nombre_partido").addClass("is-invalid");
      if (!rep) $("#representante_legal").addClass("is-invalid");

      if (!nombre || !rep) {
        UTIL.mostrarMensajeValidacion("Falta ingresar información obligatoria, marcada con asterisco.");
        return;
      }

      window.PARTIDOS_POLITICOS.savedata();
    },

    savedata: function () {
      if (isSavingPartido) return;
      isSavingPartido = true;

      setBtnLoading(true);
      try { UTIL.cursorBusy(); } catch (e) {}

      const iframeLogo = ($("#ifm").attr("data-url") || "").trim();

      const rq = {
        op: "partidopoliticosave",
        id: ($("#idPartido").val() || "").trim(),
        nombre_partido: ($("#nombre_partido").val() || "").trim(),
        representante_legal: ($("#representante_legal").val() || "").trim(),
        resolucion: ($("#resolucion").val() || "").trim(),
        email_contacto: $("#email_contacto").length ? ($("#email_contacto").val() || "").trim() : "",
        logo: iframeLogo || ""
      };

      ajaxRqst(
        rq,
        function (data) {
          try { UTIL.cursorNormal(); } catch (e) {}
          setBtnLoading(false);
          isSavingPartido = false;

          if (data?.output?.valid) {
            UTIL.mostrarMensajeExitoso("Información guardada correctamente");
            setTimeout(function () { window.location = return_page; }, 900);
          } else {
            UTIL.mostrarMensajeError(data?.output?.response?.content || data?.output?.message || "No fue posible guardar el partido.");
          }
        },
        function () {
          try { UTIL.cursorNormal(); } catch (e) {}
          setBtnLoading(false);
          isSavingPartido = false;
          try { UTIL.mostrarMensajeError("Hubo un error de comunicación con el servidor."); } catch (e) { alert("Error de comunicación."); }
        }
      );
    },

    deleteData: function (id) {
      const ejecutar = () => {
        try { UTIL.cursorBusy(); } catch (e) {}
        const rq = { op: "partidopoliticodelete", id: id };

        ajaxRqst(rq, function (data) {
          try { UTIL.cursorNormal(); } catch (e) {}

          if (data?.output?.valid) {
            UTIL.mostrarMensajeExitoso("Partido eliminado correctamente");
            setTimeout(function () { window.location = return_page; }, 900);
          } else {
            UTIL.mostrarMensajeError(data?.output?.response?.content || "No fue posible eliminar el partido.");
          }
        });
      };

      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "¿Eliminar partido?",
          text: "Esta acción no se puede deshacer.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar",
          reverseButtons: true,
          focusCancel: true
        }).then((r) => { if (r.isConfirmed) ejecutar(); });
      } else {
        if (confirm("¿Está seguro que desea eliminar este partido?")) ejecutar();
      }
    }
  };

})();
