/* =========================================================
   USUARIOS - Fix definitivo SIN choque con variable global "q"
   - NO usa "q" (porque ya existe en otros scripts)
   - USUARIO queda GLOBAL (window.USUARIO) para onclick
   - Validación robusta con municipio dinámico
   ========================================================= */

(function () {
  // 🔥 NO usar q (ya existe global en el sistema)
  var qUsr = {};
  var isSavingUser = false;
  var return_page = "usuarios.php";

  $(document).ready(initusuario);

  function initusuario() {
    qUsr = {};

    // nickname2 para compatibilidad (si no existe, lo crea)
    if (!$("#nickname2").length && $("#formusuarios").length) {
      $("<input/>", { type: "hidden", id: "nickname2", name: "nickname2", value: "" })
        .appendTo("#formusuarios");
    }

    // Si municipios NO están cargados (solo "Seleccione" cuenta como vacío)
    setTimeout(function () {
      var $mun = $("#tbl_municipio_id");
      var munVal = ($mun.val() || "").toString().trim();
      var munOpts = $mun.find("option").length;

      if (
        typeof window.DEPARTAMENTO !== "undefined" &&
        typeof window.DEPARTAMENTO.getMunicipios === "function" &&
        $mun.length &&
        (munOpts <= 1 || munVal === "")
      ) {
        window.DEPARTAMENTO.getMunicipios();
      }
    }, 600);
  }

  function valOf(sel) {
    var $el = $(sel);
    if (!$el.length) return "";
    var v = $el.val();
    if (Array.isArray(v)) v = v[0] || "";
    return (v ?? "").toString().trim();
  }

  function safeMsg(type, msg) {
    try {
      if (type === "ok") return UTIL.mostrarMensajeExitoso(msg);
      if (type === "warn") return UTIL.mostrarMensajeValidacion(msg);
      return UTIL.mostrarMensajeError(msg);
    } catch (e) {
      alert(msg);
    }
  }

  // ✅ USUARIO GLOBAL para onclick
  window.USUARIO = window.USUARIO || {};

  window.USUARIO.editData = function (id) {
    qUsr = { op: "pms_usrget", id: id };
    UTIL.callAjaxRqstPOST(qUsr, window.USUARIO.editDataHandler);
  };

  window.USUARIO.editDataHandler = function (data) {
    UTIL.cursorNormal();

    if (!data?.output?.valid) {
      safeMsg("err", data?.output?.response?.content || "No fue posible cargar el usuario.");
      return;
    }

    var res = data.output.response[0] || {};

    $("#id").val(res.id || "");
    $("#tbl_departamento_id").val(res.tbl_departamento_id || "").trigger("change");

    $("#nombre").val(res.nombre || "");
    $("#apellido").val(res.apellido || "");

    $("#nickname").val(res.nickname || "");
    $("#nickname2").val(res.nickname || "");

    $("#hashpass").val("");
    $("#hashpass1").val("");

    $("#tipo").val(res.tipo || "").trigger("change");
    $("#habilitado").val(res.habilitado || "si").trigger("change");

    // set municipio cuando ya exista el listado
    setTimeout(function () {
      $("#tbl_municipio_id").val(res.tbl_municipio_id || "").trigger("change");

      // si quedó vacío, selecciona el primero real
      if (!$("#tbl_municipio_id").val()) {
        var firstReal = $("#tbl_municipio_id option[value!='']").first().val() || "";
        if (firstReal) $("#tbl_municipio_id").val(firstReal).trigger("change");
      }
    }, 900);
  };

  window.USUARIO.validateData = function () {
    console.log("ENTRÓ A validateData()");

    if (isSavingUser) return;

    $("#formusuarios .is-invalid").removeClass("is-invalid");

    var getVal = function (sel) {
      var $el = $(sel);
      if (!$el.length) return "";

      if ($el.is("select")) {
        var v = $el.find("option:selected").val();
        if (v === undefined || v === null) v = $el.val();
        if (Array.isArray(v)) v = v[0] || "";
        return (v ?? "").toString().trim();
      }
      return ($el.val() ?? "").toString().trim();
    };

    var markInvalid = function (sel) { $(sel).addClass("is-invalid"); };

    var nombre = getVal("#nombre");
    var apellido = getVal("#apellido");
    var nickname = getVal("#nickname");
    var tipo = getVal("#tipo");
    var dep = getVal("#tbl_departamento_id");

    // MUNICIPIO: si está vacío pero hay opciones reales, auto-selecciona el primero
    var mun = getVal("#tbl_municipio_id");
    var munOptions = $("#tbl_municipio_id option").length || 0;

    if (!mun && munOptions > 1) {
      var firstReal = $("#tbl_municipio_id option[value!='']").first().val() || "";
      if (firstReal) {
        $("#tbl_municipio_id").val(firstReal).trigger("change");
        mun = getVal("#tbl_municipio_id");
      }
    }

    console.log("DEBUG VALIDACIÓN USUARIO", {
      nombre: nombre,
      apellido: apellido,
      nickname: nickname,
      tipo: tipo,
      dep: dep,
      mun: mun,
      munOptions: $("#tbl_municipio_id option").length
    });

    var missing = [];
    if (!nombre)   { missing.push("Nombres"); markInvalid("#nombre"); }
    if (!apellido) { missing.push("Apellidos"); markInvalid("#apellido"); }
    if (!nickname) { missing.push("Correo"); markInvalid("#nickname"); }
    if (!tipo)     { missing.push("Tipo"); markInvalid("#tipo"); }
    if (!dep)      { missing.push("Departamento"); markInvalid("#tbl_departamento_id"); }
    if (!mun)      { missing.push("Alcaldía / Municipio"); markInvalid("#tbl_municipio_id"); }

    var munOptsNow = $("#tbl_municipio_id option").length || 0;

    if (missing.length) {
      if (!mun && munOptsNow <= 1) {
        safeMsg("warn", "No se han cargado municipios. Cambia el departamento o espera 1 segundo y vuelve a intentar.");
        return;
      }
      safeMsg("warn", "Faltan obligatorios: " + missing.join(", ") + ".");
      return;
    }

    // Email válido
    if (nickname) {
      var okMail = UTIL.isEmail(nickname);
      if (!okMail) {
        markInvalid("#nickname");
        safeMsg("warn", "El correo debe ser un email válido.");
        return;
      }
    }

    var id = getVal("#id");
    var pass = ($("#hashpass").val() || "").toString();
    var pass2 = ($("#hashpass1").val() || "").toString();

    if (!id) {
      if (!pass)  { markInvalid("#hashpass");  safeMsg("warn", "Ingrese su contraseña."); return; }
      if (!pass2) { markInvalid("#hashpass1"); safeMsg("warn", "Debe confirmar su contraseña."); return; }
    }

    if (pass.length > 0 || pass2.length > 0) {
      if (pass !== pass2) {
        markInvalid("#hashpass"); markInvalid("#hashpass1");
        safeMsg("err", "Las contraseñas no coinciden. Inténtalo de nuevo.");
        return;
      }
      if (pass.length > 0 && pass.length < 6) {
        markInvalid("#hashpass"); markInvalid("#hashpass1");
        safeMsg("warn", "La contraseña debe tener mínimo 6 caracteres.");
        return;
      }
    }

    window.USUARIO.savedata();
  };

  window.USUARIO.savedata = function () {
    if (isSavingUser) return;
    isSavingUser = true;

    try { UTIL.cursorBusy(); } catch (e) {}
    window.USUARIO.setBtnLoading(true);

    var id = valOf("#id");
    var nickname = valOf("#nickname");
    var nickname2 = valOf("#nickname2");

    var rawPass = ($("#hashpass").val() || "").toString();
    var rawPass2 = ($("#hashpass1").val() || "").toString();

    var hashpass = "";
    var hashpass1 = "";

    if (rawPass.length > 0) {
      if (typeof hex_md5 !== "function") {
        UTIL.cursorNormal();
        window.USUARIO.setBtnLoading(false);
        isSavingUser = false;
        safeMsg("err", "No se encontró la función MD5 (data-md5.js). Verifica que esté cargando.");
        return;
      }
      hashpass = hex_md5(rawPass);
      hashpass1 = hex_md5(rawPass2);
    }

    if (nickname.length > 0 && nickname !== nickname2) {
      qUsr = { op: "pms_usravailable", nickname: nickname, id: id };

      $.ajax({
        data: qUsr,
        type: "GET",
        dataType: "json",
        url: "admin/ajax/rqst.php",
        success: function (data) {
          if (data?.output?.valid) {
            window.USUARIO.sendDataSave(hashpass, hashpass1);
          } else {
            UTIL.cursorNormal();
            window.USUARIO.setBtnLoading(false);
            isSavingUser = false;
            safeMsg("err", "El usuario *" + nickname + "* ya existe, utiliza uno nuevo.");
            $("#hashpass").val("");
            $("#hashpass1").val("");
          }
        },
        error: function () {
          UTIL.cursorNormal();
          window.USUARIO.setBtnLoading(false);
          isSavingUser = false;
          safeMsg("err", "No fue posible validar el usuario. Intenta nuevamente.");
        }
      });
    } else {
      window.USUARIO.sendDataSave(hashpass, hashpass1);
    }
  };

  window.USUARIO.sendDataSave = function (hashpass, hashpass1) {
    var iframeFoto = ($("#ifm").attr("data-url") || "").toString().trim();

    qUsr = {
      op: "pms_usrsave",
      id: valOf("#id"),
      nombre: valOf("#nombre"),
      apellido: valOf("#apellido"),
      nickname: valOf("#nickname"),
      nickname2: valOf("#nickname2"),

      hashpass: hashpass || "",
      hashpass1: hashpass1 || "",

      habilitado: valOf("#habilitado"),
      tipo: valOf("#tipo"),

      departamentoId: valOf("#tbl_departamento_id"),
      tbl_departamento_id: valOf("#tbl_departamento_id"),
      tbl_municipio_id: valOf("#tbl_municipio_id"),

      img: iframeFoto
    };

    UTIL.callAjaxRqstPOST(qUsr, window.USUARIO.savedatahandler);
  };

  window.USUARIO.savedatahandler = function (data) {
    UTIL.cursorNormal();
    window.USUARIO.setBtnLoading(false);
    isSavingUser = false;

    if (data?.output?.valid) {
      safeMsg("ok", "Información guardada correctamente");
      setTimeout(function () { window.location = return_page; }, 900);
    } else {
      safeMsg("err", data?.output?.response?.content || "No fue posible guardar el usuario.");
    }
  };

  window.USUARIO.setBtnLoading = function (loading) {
    var $btn =
      $("button[onclick*='USUARIO.validateData']").first().length
        ? $("button[onclick*='USUARIO.validateData']").first()
        : $("button:contains('Guardar')").first();

    if (!$btn.length) return;

    if (loading) {
      $btn.data("old-html", $btn.html());
      $btn.prop("disabled", true);
      $btn.html(`<i class="fas fa-circle-notch fa-spin me-2"></i>Guardando...`);
    } else {
      var old = $btn.data("old-html");
      if (old) $btn.html(old);
      $btn.prop("disabled", false);
    }
  };

  // Toggle password global
  window.togglePassword = window.togglePassword || function (fieldId, btn) {
    var input = document.getElementById(fieldId);
    if (!input) return;

    var icon = btn ? btn.querySelector("i") : null;

    if (input.type === "password") {
      input.type = "text";
      if (icon) { icon.classList.remove("fa-eye"); icon.classList.add("fa-eye-slash"); }
    } else {
      input.type = "password";
      if (icon) { icon.classList.remove("fa-eye-slash"); icon.classList.add("fa-eye"); }
    }
  };

})();
