$(document).on("ready", init);
var q;

function init() {
  q = {};
}

var return_page = "encuestas.php";
var ENCUESTA = {
  editData: function (id) {
    q = {};
    q.op = "encuestaget";
    q.id = id;
    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  editdataHandler: function (data) {
    UTIL.cursorNormal();
    if (data.output.valid) {
      var res = data.output.response[0];
      $("#idEncuesta").val(res.id);
      $("#fecha_realizacion").val(res.fecha_realizacion);
      $("#fecha_publicacion").val(res.fecha_publicacion);
      $("#fecha_de_recibo").val(res.fecha_de_recibo);
      $("#fuente_financiamiento").val(res.fuente_financiamiento);
      $("#tema").val(res.tema);
      $("#tamano_de_la_muestra").val(res.tamano_de_la_muestra);
      $("#observaciones").val(res.observaciones);
      $("#cumple_con_reglamentacion").val(res.cumple_con_reglamentacion);
      $("#tipo_muestra").val(res.tipo_muestra);
      $("#tecnica_de_recoleccion").val(res.tecnica_de_recoleccion);
      $("#enlace_documento").val(res.enlace_documento);
      $("#habilitado").val(res.habilitado);
      $("#spanEncuesta").text(" Editar Encuesta N° " + res.id + " - " + res.tema);
      $("#spanModulo").text("");
      UTIL.scrollToTop("formencuesta");
    } else {
      UTIL.mostrarMensajeError(data.output.response.content);
    }
  },

  validateData: function () {
    var bValid = true;
    var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
    if ($("#fecha_realizacion").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#fecha_publicacion").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#fecha_de_recibo").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#fuente_financiamiento").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#tema").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#tamano_de_la_muestra").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#observaciones").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#cumple_con_reglamentacion").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#tipo_muestra").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#tecnica_de_recoleccion").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#enlace_documento").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#habilitado").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }

    if (bValid) {
      ENCUESTA.savedata();
    }
  },

  savedata: function () {
    q = {};
    q.op = "encuestasave";
    q.id = $("#idEncuesta").val();
    q.fecha_realizacion = $("#fecha_realizacion").val();
    q.fecha_publicacion = $("#fecha_publicacion").val();
    q.fecha_de_recibo = $("#fecha_de_recibo").val();
    q.fuente_financiamiento = $("#fuente_financiamiento").val();
    q.tema = $("#tema").val();
    q.tamano_de_la_muestra = $("#tamano_de_la_muestra").val();
    q.observaciones = $("#observaciones").val();
    q.cumple_con_reglamentacion = $("#cumple_con_reglamentacion").val();
    q.tipo_muestra = $("#tipo_muestra").val();
    q.tecnica_de_recoleccion = $("#tecnica_de_recoleccion").val();
    q.enlace_documento = $("#enlace_documento").val();
    q.habilitado = $("#habilitado").val();

    UTIL.cursorBusy();
    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
          UTIL.mostrarMensajeExitoso("Información guardada correctamente");
          setTimeout(function () {
            window.location = return_page;
          }, 1500);
        } else {
          UTIL.mostrarMensajeError(data.output.response.content);
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError(
          "Ha ocurrido un error en la operación ejecutada"
        );
      },
    });
  },

  // --- NUEVAS FUNCIONES ---

  deleteData: function (id) {
    if (!confirm("¿Está seguro de que desea eliminar este registro?")) {
      return;
    }
    q = {};
    q.op = "encuestadelete";
    q.id = id;

    UTIL.cursorBusy();
    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
          UTIL.mostrarMensajeExitoso("Registro eliminado correctamente.");
          setTimeout(function () {
            window.location.reload();
          }, 1500);
        } else {
          UTIL.mostrarMensajeError(
            data.output.response.content || "No se pudo eliminar el registro."
          );
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError(
          "Ha ocurrido un error en la operación ejecutada"
        );
      },
    });
  },

  checkAvailability: function (input) {
    var fieldValue = $(input).val();
    var recordId = $("#idEncuesta").val();

    if (fieldValue.trim() === "") {
      return;
    }

    q = {};
    q.op = "encuestaavailable";
    q.fieldValue = fieldValue;
    q.id = recordId;

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (!data.output.valid) {
          // El valor ya existe, mostrar un error
          UTIL.mostrarMensajeError(data.output.response.content);
          $(input).focus(); // Opcional: enfocar el campo problemático
        }
      },
    });
  },
};
