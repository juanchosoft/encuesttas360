/* =========================================================
   CLIENTES - Fix definitivo (GLOBAL + sin choque de "q")
   - CLIENTES queda GLOBAL: window.CLIENTES (para onclick)
   - NO redeclara "q" (evita: redeclaration / non-configurable)
   - init seguro con $(document).ready
   - validación + DV DIAN + UX
   ========================================================= */

(function () {
  "use strict";

  const RETURN_PAGE = "clientes.php";

  // ⚠️ NO uses "let q = {}" aquí: ya te dio conflicto en otras vistas.
  // Usamos una variable interna segura.
  let req = {};
  let isSavingCliente = false;

  // -----------------------------
  // Helpers
  // -----------------------------
  function safeMsg(type, msg) {
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

  function val(sel) {
    const $el = $(sel);
    if (!$el.length) return "";
    const v = $el.val();
    if (Array.isArray(v)) return (v[0] ?? "").toString().trim();
    return (v ?? "").toString().trim();
  }

  function markInvalid(sel) {
    $(sel).addClass("is-invalid");
  }

  function resetInvalid() {
    $("#formclientes .is-invalid").removeClass("is-invalid");
  }

  // -----------------------------
  // Init
  // -----------------------------
  $(document).ready(function initClientes() {
    req = {};

    // UX: limpiar estilos al escribir/cambiar
    $("#formclientes").on("input change", "input, select, textarea", function () {
      $(this).removeClass("is-invalid");
    });

    // Normalización: identificación (números excepto pasaporte)
    $("#identificacion_num").on("input", function () {
      const tipo = (val("#identificacion_tipo") || "").toUpperCase();
      let v = $(this).val() || "";

      if (tipo === "PASAPORTE") v = v.replace(/[^a-zA-Z0-9]/g, "");
      else v = v.replace(/\D/g, "");

      $(this).val(v);
    });

    // Teléfonos: números y +
    $("#celular, #telefono, #tel_contacto").on("input", function () {
      let v = $(this).val() || "";
      v = v.replace(/[^\d+]/g, "");
      $(this).val(v);
    });

    // Cambia tipo identificación
    $("#identificacion_tipo").on("change", function () {
      $("#identificacion_num").trigger("input");
      const tipo = (val("#identificacion_tipo") || "").toUpperCase();
      if (tipo !== "NIT") $("#dv").val("");
    });

    // Auto DV para NIT (blur)
    $("#identificacion_num").on("blur", function () {
      const tipo = (val("#identificacion_tipo") || "").toUpperCase();
      if (tipo !== "NIT") return;

      const nit = (val("#identificacion_num") || "").replace(/\D/g, "");
      if (nit.length < 6) return;

      const dv = window.CLIENTES.calcularDV(nit);
      if (dv !== null) $("#dv").val(String(dv));
    });

    console.log("✅ clientes.js cargado. window.CLIENTES =", window.CLIENTES);
  });

  // -----------------------------
  // ✅ OBJETO GLOBAL (para onclick)
  // -----------------------------
  window.CLIENTES = window.CLIENTES || {

    editData: function (id) {
      req = { op: "clienteget", id: id };

      if (!window.UTIL || typeof UTIL.callAjaxRqstPOST !== "function") {
        safeMsg("err", "No se encontró UTIL.callAjaxRqstPOST(). Verifica que cargue gerenic_script.php.");
        return;
      }

      UTIL.callAjaxRqstPOST(req, window.CLIENTES.editdataHandler);
    },

    editdataHandler: function (data) {
      try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}

      if (!data?.output?.valid) {
        safeMsg("err", data?.output?.response?.content || "No fue posible cargar el cliente.");
        return;
      }

      const res = data.output.response?.[0] || {};

      $("#idCliente").val(res.id || "");
      $("#identificacion_tipo").val(res.identificacion_tipo || "").trigger("change");
      $("#identificacion_num").val(res.identificacion_num || "");
      $("#dv").val(res.dv || "");

      $("#nombre_completo").val(res.nombre_completo || "");
      $("#tipo_cliente").val(res.tipo_cliente || "");

      $("#cumpleanos").val(res.cumpleanos || "");
      $("#habilitado").val(res.habilitado || "SI");

      $("#direccion").val(res.direccion || "");
      $("#ubicacion").val(res.ubicacion || "");
      $("#barrio").val(res.barrio || "");

      $("#telefono").val(res.telefono || "");
      $("#celular").val(res.celular || "");
      $("#email").val(res.email || "");

      $("#contacto").val(res.contacto || "");
      $("#tel_contacto").val(res.tel_contacto || "");

      // subir al form
      if (window.UTIL && typeof UTIL.scrollToTop === "function") {
        UTIL.scrollToTop("formclientes");
      } else {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    },

    validateEmail: function (email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || "").trim());
    },

    // ✅ DV DIAN para NIT (retorna 0-9 o null)
    calcularDV: function (nit) {
      try {
        const v = String(nit).replace(/\D/g, "");
        const pesos = [71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3];
        let suma = 0;

        for (let i = 0; i < v.length; i++) {
          const dig = parseInt(v.charAt(i), 10);
          const peso = pesos[i + (pesos.length - v.length)];
          suma += dig * peso;
        }

        const residuo = suma % 11;
        return residuo > 1 ? 11 - residuo : residuo;
      } catch (e) {
        return null;
      }
    },

    validateData: function () {
      if (isSavingCliente) return;

      resetInvalid();

      const tipoId = val("#identificacion_tipo");
      const numId = val("#identificacion_num");
      const nombre = val("#nombre_completo");
      const direccion = val("#direccion");
      const estado = val("#habilitado");

      const msj = "Falta ingresar información obligatoria, marcada con asterisco.";

      if (!tipoId) markInvalid("#identificacion_tipo");
      if (!numId) markInvalid("#identificacion_num");
      if (!nombre) markInvalid("#nombre_completo");
      if (!direccion) markInvalid("#direccion");
      if (!estado) markInvalid("#habilitado");

      if (!tipoId || !numId || !nombre || !direccion || !estado) {
        safeMsg("warn", msj);
        return;
      }

      // NIT: DV si lo ingresan => 0-9
      const tipoUpper = tipoId.toUpperCase();
      if (tipoUpper === "NIT") {
        const dv = val("#dv");
        if (dv !== "" && !/^\d$/.test(dv)) {
          markInvalid("#dv");
          safeMsg("warn", "El dígito de verificación (DV) debe ser un número de 0 a 9.");
          return;
        }
      }

      // Email si lo ingresan
      const email = val("#email");
      if (email && !window.CLIENTES.validateEmail(email)) {
        markInvalid("#email");
        safeMsg("warn", "El formato del correo electrónico no es válido.");
        return;
      }

      // Celular mínimo 7 (si existe)
      const celular = val("#celular").replace(/[^\d]/g, "");
      if (celular && celular.length < 7) {
        markInvalid("#celular");
        safeMsg("warn", "El celular parece incompleto. Verifica el número.");
        return;
      }

      window.CLIENTES.savedata();
    },

    savedata: function () {
      if (isSavingCliente) return;
      isSavingCliente = true;

      window.CLIENTES.setBtnLoading(true);
      try { if (window.UTIL && typeof UTIL.cursorBusy === "function") UTIL.cursorBusy(); } catch (e) {}

      req = {
        op: "clientesave",
        id: val("#idCliente"),

        identificacion_tipo: val("#identificacion_tipo"),
        identificacion_num: val("#identificacion_num"),
        dv: val("#dv"),

        nombre_completo: val("#nombre_completo"),
        tipo_cliente: val("#tipo_cliente"),
        cumpleanos: val("#cumpleanos"),
        habilitado: val("#habilitado"),

        direccion: val("#direccion"),
        ubicacion: val("#ubicacion"),
        barrio: val("#barrio"),

        telefono: val("#telefono"),
        celular: val("#celular"),
        email: val("#email"),

        contacto: val("#contacto"),
        tel_contacto: val("#tel_contacto"),
      };

      $.ajax({
        data: req,
        type: "POST",
        dataType: "json",
        url: "admin/ajax/rqst.php",
        success: function (data) {
          req = {};
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}
          window.CLIENTES.setBtnLoading(false);
          isSavingCliente = false;

          if (data?.output?.valid) {
            safeMsg("ok", "Cliente guardado correctamente");
            setTimeout(function () {
              window.location = RETURN_PAGE;
            }, 900);
          } else {
            safeMsg("err", data?.output?.response?.content || "No fue posible guardar el cliente.");
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}
          window.CLIENTES.setBtnLoading(false);
          isSavingCliente = false;
          console.error("AJAX error:", textStatus, errorThrown, jqXHR.responseText);
          safeMsg("err", "Hubo un error de comunicación con el servidor.");
        },
      });
    },

    deleteData: function (id) {
      if (!id) return;

      req = { op: "clientedelete", id: id };
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
            safeMsg("ok", "Cliente eliminado correctamente");
            setTimeout(function () {
              window.location = RETURN_PAGE;
            }, 900);
          } else {
            safeMsg("err", data?.output?.response?.content || "No fue posible eliminar el cliente.");
          }
        },
        error: function () {
          try { if (window.UTIL && typeof UTIL.cursorNormal === "function") UTIL.cursorNormal(); } catch (e) {}
          safeMsg("err", "Hubo un error de comunicación con el servidor.");
        },
      });
    },

    setBtnLoading: function (loading) {
      const $btn =
        $("button[onclick*='CLIENTES.validateData']").first().length
          ? $("button[onclick*='CLIENTES.validateData']").first()
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
    },
  };

})();
