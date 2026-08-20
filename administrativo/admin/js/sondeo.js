$(document).on("ready", init);
var q;
let selectedCandidates = [];
let opcionesSondeo = [];
const modal = document.getElementById("participantsModal");

function init() {
  q = {};
  // Asocia el evento 'change' y 'input' a la función de filtrado
  $("#tbl_cargo_publico_id").on("change", SONDEO.filterAndShowData);
  $("#codigo_departamento").on("input", SONDEO.filterAndShowData);
  $("#codigo_municipio").on("input", SONDEO.filterAndShowData);
  SONDEO.handleSondeParaCargoPublicoChange(); // Llama a esta función en la carga inicial
}

var return_page = "sondeos.php";
var SONDEO = {
  editData: function (id) {
    q = {};
    q.op = "sondeoget";
    q.id = id;
    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  editdataHandler: function (data) {
    UTIL.cursorNormal();
    if (data.output.valid) {
      var res = data.output.response[0];
      $("#idSondeo").val(res.id);
      $("#sondeo").val(res.sondeo);
      $("#descripcion_sondeo").val(res.descripcion_sondeo);
      $("#tipo_sondeo").val(res.tipo_sondeo);
      $("#tipo_inferenciales").val(res.tipo_inferenciales);
      $("#aplica_cargos_publicos").val(res.aplica_cargos_publicos);
      $("#tbl_departamento_id").val(res.codigo_departamento);
      $("#tbl_municipio_id").val(res.codigo_municipio);
      $("#tbl_cargo_publico_id").val(res.tbl_cargo_publico_id);
      $("#dtcreate").val(res.dtcreate);
      $("#habilitado").prop('checked', res.habilitado === 'si');
      $("#fecha_inicio").val(res.fecha_inicio);
      $("#fecha_fin").val(res.fecha_fin);
      $("#es_trivia").prop('checked', res.es_trivia === 'si');

      DEPARTAMENTO.getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio(
        res.codigo_departamento,
        res.codigo_municipio
      );

      $("#spanEncuesta").text(
        " Editar Información de Sondeos N° " + res.id + " - " + res.sondeo
      );
      $("#spanModulo").text("");
      UTIL.scrollToTop("formsondeo");

      // Informacion de los candidatos que tiene el sondeo
      selectedCandidates = res.candidatos;

      // Opciones del sondeo
      opcionesSondeo = res.opciones;

      // Manejar cambio en el select "Sondeo para cargo público"
      SONDEO.handleSondeParaCargoPublicoChange();

      // Manejar cambio en el select de Tipo sondeo
      OPCIONES.handleTipoPreguntaChange("tipo_sondeo", opcionesSondeo);
    } else {
      UTIL.mostrarMensajeError(data.output.response.content);
    }
  },
  emptyCells: function () {
    $("#idSondeo").val("");
    $("#sondeo").val("");
    $("#descripcion_sondeo").val("");
    $("#aplica_cargos_publicos").val("no");
    $("#habilitado").prop('checked', true); // Por defecto habilitado
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#es_trivia").prop('checked', false); // Por defecto no es trivia
    $("#spanEncuesta").text("");
    $("#spanModulo").text("Ingreso de Información de Sondeos");
    selectedCandidates = [];
    $("#departamento-field").addClass("d-none");
    $("#municipio-field").addClass("d-none");
    $(".cargo-publico-fields").addClass("d-none");
    $(".table-candidatos").addClass("d-none");

    // Informacion de opciones
    opcionesSondeo = [];
    $(".opciones-preguntas").addClass("d-none");

    SONDEO.handleSondeParaCargoPublicoChange();
  },
  validateData: function () {
    var bValid = true;
    var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
    if ($("#sondeo").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#tipo_sondeo").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#tipo_inferenciales").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }
    if ($("#aplica_cargos_publicos").val() === "") {
      bValid = false;
      UTIL.mostrarMensajeValidacion(msj);
      return;
    }

    if (bValid) {
      SONDEO.savedata();
    }
  },
  savedata: function () {
    q = {};
    q.op = "sondeosave";
    q.id = $("#idSondeo").val();
    q.sondeo = $("#sondeo").val();
    q.descripcion_sondeo = $("#descripcion_sondeo").val();
    q.tipo_sondeo = $("#tipo_sondeo").val();
    q.tipo_inferenciales = $("#tipo_inferenciales").val();
    q.aplica_cargos_publicos = $("#aplica_cargos_publicos").val();
    q.codigo_departamento = $("#tbl_departamento_id").val();
    q.codigo_municipio = $("#tbl_municipio_id").val();
    q.tbl_cargo_publico_id = $("#tbl_cargo_publico_id").val();
    q.habilitado = $("#habilitado").is(':checked') ? 'si' : 'no';
    q.fecha_inicio = $("#fecha_inicio").val();
    q.fecha_fin = $("#fecha_fin").val();
    q.es_trivia = $("#es_trivia").is(':checked') ? 'si' : 'no';
    q.candidatos = getSelectedCandidatesFromTable();
    q.opciones = OPCIONES.getOpciones();

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
  deleteData: function (id) {
    if (!confirm("¿Está seguro de que desea eliminar este registro?")) {
      return;
    }
    q = {};
    q.op = "sondeodelete";
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
  handleCargoPublicoChange: function (thisElement) {
    const cargoPublicoId = thisElement
      ? $(thisElement).val()
      : $("#tbl_cargo_publico_id").val();

    const departamentoFieldContainer = $(".departamento-municipio-fields").eq(
      0
    );
    const municipioFieldContainer = $(".departamento-municipio-fields").eq(1);

    const presidenteId = "1";
    const senadorId = "2";
    const representanteCamaraId = "3";
    const gobernadorId = "4";
    const diputadoId = "5";
    const alcaldeId = "6";
    const concejalId = "7";

    departamentoFieldContainer.addClass("d-none");
    municipioFieldContainer.addClass("d-none");

    // Eliminar el atributo 'required' si el campo se oculta
    $("#codigo_departamento").prop("required", false);
    $("#codigo_municipio").prop("required", false);

    switch (cargoPublicoId) {
      case presidenteId:
      case senadorId:
        // Ya están ocultos por defecto departamento y municipio
        break;
      case gobernadorId:
      case representanteCamaraId:
      case diputadoId:
        // Mostrar solo departamento
        departamentoFieldContainer.removeClass("d-none");
        $("#codigo_departamento").prop("required", true); // Hacerlo requerido si es visible
        break;
      case alcaldeId:
      case concejalId:
        // Mostrar ambos departamento y municipio
        departamentoFieldContainer.removeClass("d-none");
        municipioFieldContainer.removeClass("d-none");
        $("#codigo_departamento").prop("required", true); // Hacerlo requerido si es visible
        $("#codigo_municipio").prop("required", true); // Hacerlo requerido si es visible
        break;
      default:
        // Por defecto, o si no hay selección válida, ocultar todo
        departamentoFieldContainer.addClass("d-none");
        municipioFieldContainer.addClass("d-none");
        break;
    }
    if ($("#aplica_cargos_publicos").val() == "si") {
      $(".table-candidatos").removeClass("d-none");
      SONDEO.getParticipantesByCargoPublicoId(cargoPublicoId);
    }
  },
  handleSondeParaCargoPublicoChange: function (thisElement) {
    const sondeoParaCargoPublicoId = thisElement
      ? $(thisElement).val()
      : $("#aplica_cargos_publicos").val();
    if (sondeoParaCargoPublicoId === "si") {
      $(".cargo-publico-fields").removeClass("d-none");
      $("#departamento-field").removeClass("d-none");
      $("#municipio-field").removeClass("d-none");
      $(".table-candidatos").removeClass("d-none");
      SONDEO.handleCargoPublicoChange();
    } else {
      $("#departamento-field").addClass("d-none");
      $("#municipio-field").addClass("d-none");
      $(".cargo-publico-fields").addClass("d-none");
      $(".table-candidatos").addClass("d-none");
    }
  },
  getParticipantesByCargoPublicoId: function (cargoPublicoId) {
    q = {};
    q.op = "participanteget";
    q.cargoPublicoId = cargoPublicoId;
    UTIL.cursorBusy();
    $.ajax({
      data: q,
      type: "GET",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        q = {};
        UTIL.cursorNormal();
        if (data.output.valid) {
          showDataParticipantes(data.output.response);
        } else {
        }
      },
    });
  },
  // Nuevo método para manejar el filtrado del lado del cliente
  filterAndShowData: function () {
    const cargoPublicoId = $("#tbl_cargo_publico_id").val();

    // Ocultar/mostrar campos de filtro
    const departamentoFieldContainer = $("#departamento-field");
    const municipioFieldContainer = $("#municipio-field");
    departamentoFieldContainer.addClass("d-none");
    municipioFieldContainer.addClass("d-none");

    switch (cargoPublicoId) {
      case "4": // Gobernador
      case "3": // Representante a la Cámara
      case "5": // Diputado
        departamentoFieldContainer.removeClass("d-none");
        break;
      case "6": // Alcalde
      case "7": // Concejal
        departamentoFieldContainer.removeClass("d-none");
        municipioFieldContainer.removeClass("d-none");
        break;
    }

    // Aquí llamamos a la función que obtendrá los datos y los renderizará
    // Siempre obtener todos los participantes del servidor para mostrar la lista completa
    if ($("#aplica_cargos_publicos").val() == "si") {
      SONDEO.getParticipantesByCargoPublicoId(cargoPublicoId);
    }
  },
};
// Función para actualizar la visualización de los IDs seleccionados
function updateSelectedCandidatesDisplay() {
  const displayElement = document.getElementById("selected-ids");
  if (displayElement) {
    displayElement.textContent = JSON.stringify(selectedCandidates, null, 2);
  }
}

// Manejador del evento onchange para los checkboxes
// 'el' es el elemento checkbox que disparó el evento
function handleCheckboxChange(el) {
  const candidateId = parseInt(el.value);

  // Verificar si el candidato ya está seleccionado
  // selectedCandidates puede contener objetos (al editar) o números (al crear)
  const isAlreadySelected = selectedCandidates.some(
    (candidate) => {
      if (typeof candidate === 'object' && candidate !== null) {
        return parseInt(candidate.id) === candidateId;
      }
      return parseInt(candidate) === candidateId;
    }
  );

  if (el.checked) {
    if (!isAlreadySelected) {
      // Al agregar, guardar solo el ID para consistencia
      selectedCandidates.push(candidateId);
    }
  } else {
    // Filtrar tanto objetos como números
    selectedCandidates = selectedCandidates.filter((candidate) => {
      if (typeof candidate === 'object' && candidate !== null) {
        return parseInt(candidate.id) !== candidateId;
      }
      return parseInt(candidate) !== candidateId;
    });
  }
  updateSelectedCandidatesDisplay();
}

// Función principal para renderizar la tabla con la data de participante
function showDataParticipantes(data) {
  const tbody = document.querySelector("#candidatosTable tbody");
  if (!tbody) {
    console.error("Elemento <tbody> con ID 'candidatosTable' no encontrado.");
    return;
  }

  if (!data || data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" class="py-4 text-center text-gray-500">No se encontraron candidatos para los criterios seleccionados.</td></tr>`;
    return;
  }

  const cargoId = $("#tbl_cargo_publico_id").val();
  const departamentoId = $("#tbl_departamento_id").val();
  const municipioId = $("#tbl_municipio_id").val();
  let cargoPublicoId = $("#tbl_cargo_publico_id").val();

  let filteredData = data;

  let validarDepartamento = false;
  let validarMunicipio = false;

  switch (cargoPublicoId) {
    case "4": // Gobernador
    case "3": // Representante a la Cámara
    case "5": // Diputado
      departamentoFieldContainer.removeClass("d-none");
      break;
    case "6": // Alcalde
    case "7": // Concejal
      validarDepartamento = true;
      validarMunicipio = true;
      break;
  }

  if (cargoId) {
    filteredData = filteredData.filter(
      (item) =>
        item.habilitado &&
        parseInt(item.tbl_cargo_publico_id) === parseInt(cargoId)
    );
  }

  if (departamentoId && validarDepartamento) {
    filteredData = filteredData.filter(
      (item) =>
        item.codigo_departamento &&
        item.habilitado &&
        item.codigo_departamento.toString() === departamentoId.toString()
    );
  }

  if (municipioId && validarMunicipio) {
    filteredData = filteredData.filter(
      (item) =>
        item.codigo_municipio &&
        item.habilitado &&
        item.codigo_municipio.toString() === municipioId.toString()
    );
  }

  let tableRows = "";
  const isEditing = $("#idSondeo").val() > 0;

  if (filteredData.length === 0) {
    tableRows = `<tr><td colspan="9" class="py-4 text-center text-gray-500">No se encontraron candidatos para los criterios seleccionados.</td></tr>`;
  } else {
    filteredData.forEach((item) => {
      // Verificar si el candidato está seleccionado (puede ser objeto o número)
      const shouldBeChecked =
        isEditing &&
        selectedCandidates.some((candidate) => {
          if (typeof candidate === 'object' && candidate !== null) {
            return parseInt(candidate.id) === parseInt(item.id);
          }
          return parseInt(candidate) === parseInt(item.id);
        });

      tableRows += `
        <tr>
          <td class="py-3 px-4">
            <input
              onchange="handleCheckboxChange(this)"
              type="checkbox"
              value="${item.id}"
              ${shouldBeChecked ? "checked" : ""}
              class="form-checkbox h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300"
            >
          </td>
          <td class="py-3 px-4">
            ${
              item.foto
                ? `<img width="60" height="60" src="assets/img/admin/${item.foto}" alt="Foto de ${item.nombre_completo}" class="h-8 w-8 rounded-full object-cover">`
                : "N/A"
            }
          </td>
          <td class="py-3 px-4">${item.nombre_completo}</td>
          <td class="py-3 px-4">${item.cargo_publico}</td>
          <td class="py-3 px-4">${item.nombres_partidos}</td>
          <td class="py-3 px-4">${item.nombre_municipio || "N/A"}</td>
          <td class="py-3 px-4">${item.nombre_departamento || "N/A"}</td>
        </tr>
      `;
    });
  }

  tbody.innerHTML = tableRows;
  updateSelectedCandidatesDisplay();
}

function showParticipantsModal(candidatosDataString, sondeo) {
  try {
    const candidatos = candidatosDataString;
    const tableBody = document.querySelector("#candidatosModalTable .list");
    tableBody.innerHTML = ""; // Limpia las filas anteriores

    candidatos.forEach((item) => {
      const row = document.createElement("tr");
      row.innerHTML = `
              <td class="py-3 px-4 border border-gray-300">
                  ${
                    item.foto
                      ? `<img width="60" height="60" src="assets/img/admin/${item.foto}" alt="Foto de ${item.nombre_completo}" class="h-12 w-12 rounded-full object-cover">`
                      : "N/A"
                  }
              </td>
              <td class="py-3 px-4 border border-gray-300">${
                item.nombre_completo
              }</td>
              <td class="py-3 px-4 border border-gray-300">${
                item.cargo_publico
              }</td>
              <td class="py-3 px-4 border border-gray-300">${
                item.nombres_partidos
              }</td>
              <td class="py-3 px-4 border border-gray-300">${
                item.nombre_municipio || "N/A"
              }</td>
              <td class="py-3 px-4 border border-gray-300">${
                item.nombre_departamento || "N/A"
              }</td>
          `;
      tableBody.appendChild(row);
    });

    $("#participantsModal").modal("show");
    $("#sondeo-title").text(sondeo);
  } catch (error) {
    console.error("Error al analizar los datos de los candidatos:", error);
  }
}

function hideParticipantsModal() {
  $("#participantsModal").modal("hide");
}

window.onclick = function (event) {
  if (event.target === modal) {
    hideParticipantsModal();
  }
};

function getSelectedCandidatesFromTable() {
  const checkboxes = document.querySelectorAll(
    '#table-container input[type="checkbox"]:checked'
  );
  const selectedIds = Array.from(checkboxes).map((checkbox) =>
    parseInt(checkbox.value)
  );
  return selectedIds;
}

/**
 * Función para manejar el toggle de habilitación de sondeos.
 * Solo puede haber UN sondeo habilitado a la vez.
 * Al activar uno, se desactivan todos los demás.
 */
$(document).on('change', '.toggle-sondeo-habilitado', function() {
  const checkbox = $(this);
  const sondeoId = checkbox.data('sondeo-id');
  const habilitado = checkbox.is(':checked') ? 'si' : 'no';

  // Deshabilitar el checkbox mientras se procesa
  checkbox.prop('disabled', true);

  $.ajax({
    url: 'admin/ajax/rqst.php',
    type: 'POST',
    dataType: 'json',
    data: {
      op: 'sondeotoggle',
      id: sondeoId,
      habilitado: habilitado
    },
    success: function(response) {
      if (response.output.valid) {
        // Si se activó, desactivar visualmente todos los demás toggles
        if (habilitado === 'si') {
          // Desmarcar todos los demás toggles
          $('.toggle-sondeo-habilitado').not(checkbox).prop('checked', false);

          // Actualizar badges de todos los demás a "Inactivo"
          $('.toggle-sondeo-habilitado').not(checkbox).each(function() {
            const otherSondeoId = $(this).data('sondeo-id');
            $('#badgeSondeo_' + otherSondeoId)
              .removeClass('bg-success')
              .addClass('bg-danger')
              .text('Inactivo');
          });

          // Actualizar badge del sondeo activado
          $('#badgeSondeo_' + sondeoId)
            .removeClass('bg-danger')
            .addClass('bg-success')
            .text('Activo');

          UTIL.mostrarMensajeExitoso('Sondeo activado. Los demás han sido desactivados.');
        } else {
          // Actualizar badge del sondeo desactivado
          $('#badgeSondeo_' + sondeoId)
            .removeClass('bg-success')
            .addClass('bg-danger')
            .text('Inactivo');

          UTIL.mostrarMensajeExitoso('Sondeo desactivado correctamente.');
        }
      } else {
        // Revertir el cambio visual
        checkbox.prop('checked', !checkbox.is(':checked'));
        UTIL.mostrarMensajeError(response.output.message || 'Error al cambiar estado del sondeo');
      }
    },
    error: function() {
      // Revertir el cambio visual
      checkbox.prop('checked', !checkbox.is(':checked'));
      UTIL.mostrarMensajeError('Error de conexión al servidor');
    },
    complete: function() {
      // Rehabilitar el checkbox
      checkbox.prop('disabled', false);
    }
  });
});
