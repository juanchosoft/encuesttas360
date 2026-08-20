$(document).on("ready", init);

var q;
var return_page = "votantes.php";

function init() {
  q = {};
}

var VOTANTES = {
  editData: function (id) {
    q = {};
    q.op = "votantesget";
    q.id = id;
    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  editdataHandler: function (data) {
    UTIL.cursorNormal();

    if (!data || !data.output || !data.output.valid) {
      UTIL.mostrarMensajeError((data?.output?.response?.content) || "No se pudo cargar la información.");
      return;
    }

    var res = data.output.response[0] || {};

    $("#idVotantes").val(res.id || "");
    $("#nombre_completo").val(res.nombre_completo || "");
    $("#ideologia").val(res.ideologia || "");
    $("#rango_edad").val(res.rango_edad || "");
    $("#nivel_ingresos").val(res.nivel_ingresos || "");
    $("#email").val(res.email || "");
    $("#username").val(res.username || "");
    $("#password").val("");
    $("#genero").val(res.genero || "");
    $("#tbl_departamento_id").val(res.codigo_departamento || "");
    $("#tbl_departamento_id").trigger("change");

    if (typeof DEPARTAMENTO !== "undefined" && typeof DEPARTAMENTO.getMunicipios === "function") {
      DEPARTAMENTO.getMunicipios();
    }

    setTimeout(() => {
      $("#tbl_municipio_id").val(res.codigo_municipio || "");
    }, 500);

    $("#comuna").val(res.comuna || "");
    $("#barrio").val(res.barrio || "");
    $("#nivel_educacion").val(res.nivel_educacion || "");
    $("#ocupacion").val(res.ocupacion || "");
    $("#estado").val(res.estado || "");

    // Si en algún momento quieres visualizar estos campos al editar
    $("#device_token").val(res.device_token || $("#device_token").val() || "");
    $("#device_fingerprint").val(res.device_fingerprint || $("#device_fingerprint").val() || "");
    $("#device_user_agent").val(res.device_user_agent || $("#device_user_agent").val() || "");
    $("#device_platform").val(res.device_platform || $("#device_platform").val() || "");
    $("#device_language").val(res.device_language || $("#device_language").val() || "");
    $("#device_timezone").val(res.device_timezone || $("#device_timezone").val() || "");

    $("#spanEncuesta").text(" Editar Ingreso de Votante N° " + (res.id || ""));
    $("#spanModulo").text("");
  },

  validateData: function () {
    var bValid = true;
    var msj = "Falta ingresar información obligatoria, marcada con asterisco.";

    var id = ($("#idVotantes").val() || "").trim();
    var isNew = (id === "");
    var hasNombreField = $("#nombre_completo").length > 0;
    var hasUsernameField = $("#username").length > 0;

    var checks = [
      { id: "email",              label: "Correo electrónico" },
      { id: "ideologia",          label: "Ideología" },
      { id: "rango_edad",         label: "Rango de edad" },
      { id: "genero",             label: "Género" },
      { id: "tbl_departamento_id",label: "Departamento" },
      { id: "tbl_municipio_id",   label: "Municipio" },
      { id: "ocupacion",          label: "Ocupación" },
      { id: "estado",             label: "Estado" }
    ];

    if (hasNombreField) {
      checks.unshift({ id: "nombre_completo", label: "Nombre completo" });
    }

    if (hasUsernameField) {
      checks.splice(hasNombreField ? 2 : 1, 0, { id: "username", label: "Usuario" });
    }

    if (isNew) {
      checks.push({ id: "password", label: "Contraseña" });
    }

    for (var i = 0; i < checks.length; i++) {
      var val = ($("#" + checks[i].id).val() || "").trim();
      if (val === "") {
        console.warn("[validateData] Campo vacío:", checks[i].label, "(#" + checks[i].id + ")");
        bValid = false;
      }
    }

    if (!$("#politica").is(":checked")) {
      bValid = false;
      msj = "Debe aceptar la política de privacidad";
    }

    if (isNew) {
      if (($("#device_token").val() || "").trim() === "" || ($("#device_fingerprint").val() || "").trim() === "") {
        bValid = false;
        msj = "No fue posible identificar el dispositivo. Recarga la página e intenta nuevamente.";
      }
    }

    if (!bValid) {
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }

    VOTANTES.savedata();
  },

  savedata: function () {
    q = {};
    q.op = "votantessave";

    function v(id) {
      return (($(id).val() ?? "") + "").trim();
    }

    q.id                    = v("#idVotantes");
    q.dtcreate              = v("#dtcreate");
    q.tbl_usuario_id        = v("#tbl_usuario_id");
    q.email                 = v("#email");
    q.username              = $("#username").length ? v("#username") : q.email;
    q.nombre_completo       = $("#nombre_completo").length ? v("#nombre_completo") : q.username;
    q.ideologia             = v("#ideologia");
    q.rango_edad            = v("#rango_edad");
    q.nivel_ingresos        = v("#nivel_ingresos");

    var pass = v("#password");
    var plainPass = pass;
    if (pass !== "") {
      q.password = hex_md5(pass);
    } else {
      q.password = "";
    }

    q.genero                = v("#genero");
    q.codigo_departamento   = v("#tbl_departamento_id");
    q.codigo_municipio      = v("#tbl_municipio_id");
    q.comuna                = v("#comuna");
    q.barrio                = v("#barrio");
    q.nivel_educacion       = v("#nivel_educacion");
    q.ocupacion             = v("#ocupacion");
    q.estado                = v("#estado");

    q.email_verificado       = v("#email_verificado");
    q.ultimo_acceso          = v("#ultimo_acceso");
    q.intentos_login         = v("#intentos_login");
    q.cuenta_bloqueada_hasta = v("#cuenta_bloqueada_hasta");
    q.dtupdate               = v("#dtupdate");
    q.ip_registro            = v("#ip_registro");
    q.user_agent             = v("#user_agent");

    // NUEVOS CAMPOS DE DISPOSITIVO
    q.device_token          = v("#device_token");
    q.device_fingerprint    = v("#device_fingerprint");
    q.device_user_agent     = v("#device_user_agent");
    q.device_platform       = v("#device_platform");
    q.device_language       = v("#device_language");
    q.device_timezone       = v("#device_timezone");

    var loginUsername = q.username || q.email;

    UTIL.cursorBusy();

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();

        if (data && data.output && data.output.valid) {
          if (loginUsername && plainPass) {
            var loginData = new FormData();
            loginData.append('nickname', loginUsername);
            loginData.append('hashpass', plainPass);

            fetch('login_process.php', { method: 'POST', body: loginData })
              .then(function(r) { return r.json(); })
              .then(function(loginRes) {
                if (loginRes.status === 'success') {
                  window.location = loginRes.redirect;
                } else {
                  window.location = "index.php";
                }
              })
              .catch(function() {
                window.location = "index.php";
              });
          } else {
            UTIL.mostrarMensajeExitoso("Información guardada correctamente");
            setTimeout(function () {
              window.location = "index.php";
            }, 1000);
          }
          return;
        }

        var msg = (data?.output?.response?.content) || "No se pudo guardar la información.";
        var code = (data?.output?.response?.code) || "";

        if (
          code === "DEVICE_EXISTS" ||
          code === "DEVICE_ALREADY_REGISTERED" ||
          code === "DUPLICATE_DEVICE"
        ) {
          UTIL.mostrarMensajeError("Este equipo ya fue utilizado para crear una cuenta. No es posible registrarse nuevamente desde este dispositivo.");
          return;
        }

        if (code === "EMAIL_EXISTS") {
          UTIL.mostrarMensajeError("Este correo ya se encuentra registrado.");
          return;
        }

        if (code === "USERNAME_EXISTS") {
          UTIL.mostrarMensajeError("Este usuario ya se encuentra registrado.");
          return;
        }

        if (code === "DEVICE_REQUIRED") {
          UTIL.mostrarMensajeError("No fue posible validar el dispositivo. Recarga la página e intenta nuevamente.");
          return;
        }

        UTIL.mostrarMensajeError(msg);
      },
      error: function (xhr) {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Ha ocurrido un error en la operación ejecutada");
        console.error(xhr);
      }
    });
  },

  checkAvailability: function (input) {
    if (!input || !input.id) return;

    var value = ($(input).val() || "").trim();
    if (value === "") return;

    var field = input.id;
    if (field !== "email" && field !== "username") return;

    var rq = {};
    rq.op = "votantescheckavailability";
    rq.field = field;
    rq.value = value;
    rq.id = ($("#idVotantes").val() || "").trim();

    $.ajax({
      data: rq,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (!data || !data.output) return;

        if (data.output.valid === false) {
          var msg = (data?.output?.response?.content) || ("El " + field + " ya está registrado.");
          UTIL.mostrarMensajeError(msg);
          $(input).focus();
        }
      },
      error: function () {
        console.warn("No fue posible validar disponibilidad de " + field);
      }
    });
  },

  emptyCells: function () {
    $("#idVotantes").val("");
    $("#spanEncuesta").text("");
    $("#ideologia").val("");
    $("#rango_edad").val("");
    $("#nivel_ingresos").val("");
    $("#email").val("");
    if ($("#username").length) {
      $("#username").val("");
    }
    $("#password").val("");
    $("#genero").val("");
    $("#nivel_educacion").val("");
    $("#ocupacion").val("");
    $("#estado").val("activo");
    if ($("#nombre_completo").length) {
      $("#nombre_completo").val("");
    }
    $("#comuna").val("");
    $("#barrio").val("");
    $("#tbl_departamento_id").val("");
    $("#tbl_municipio_id").html('<option value="">Primero elige un departamento</option>');
    $("#politica").prop("checked", false);

    // No limpiamos device_token ni fingerprint porque deben permanecer en este navegador
    $("#spanModulo").text("Ingreso y listado de Votantes");
  }
};
