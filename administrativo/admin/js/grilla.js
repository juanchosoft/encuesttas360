$(document).on("ready", init);
var q;
let selectedCandidates = [];
let opcionesGrilla = [];
const modal = document.getElementById("participantsModal");

function init() {
  q = {};
  // Asocia el evento 'change' y 'input' a la función de filtrado
  $("#tbl_cargo_publico_id").on("change", GRILLA.filterAndShowData);
  $("#codigo_departamento").on("input", GRILLA.filterAndShowData);
  $("#codigo_municipio").on("input", GRILLA.filterAndShowData);
  GRILLA.handleSondeParaCargoPublicoChange(); // Llama a esta función en la carga inicial
}

var return_page = "grilla.php";
var GRILLA = {
  showGrilla: function (itemJson) {
    // 1. Crear el formulario dinámicamente
    const form = document.createElement("form");
    form.method = "POST"; // Método POST para enviar la data
    form.action = "candidato.php"; // URL de destino
    form.target = "_blank"; // Abrir en una nueva pestaña

    // 2. Crear el input oculto para enviar la cadena JSON
    const input = document.createElement("input");
    input.type = "hidden";
    // Usamos una clave descriptiva, por ejemplo: 'registro_data'
    input.name = "registro_data";
    input.value = itemJson;

    // 3. Adjuntar el input al formulario
    form.appendChild(input);

    // 4. Adjuntar el formulario al cuerpo del documento y enviarlo
    document.body.appendChild(form);

    console.log("Enviando datos JSON completos a candidato.php por POST.");

    form.submit();

    // 5. Limpiar el DOM: eliminar el formulario después de la sumisión (con un pequeño delay si es necesario)
    setTimeout(() => {
      document.body.removeChild(form);
    }, 50); // Pequeño delay para asegurar el envío
  },

  showResultados: function (itemJson) {
    // Crear el formulario dinámicamente para enviar a la página de resultados
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "resultados_grilla.php";
    form.target = "_blank"; // Abrir en una nueva pestaña

    // Crear el input oculto para enviar la cadena JSON
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "registro_data";
    input.value = itemJson;

    // Adjuntar el input al formulario
    form.appendChild(input);

    // Adjuntar el formulario al cuerpo del documento y enviarlo
    document.body.appendChild(form);

    console.log("Abriendo vista de resultados en tiempo real...");

    form.submit();

    // Limpiar el DOM
    setTimeout(() => {
      document.body.removeChild(form);
    }, 50);
  },

  editData: function (id) {
    q = {};
    q.op = "grillaget";
    q.id = id;
    UTIL.callAjaxRqstPOST(q, this.editdataHandler);
  },

  editdataHandler: function (data) {
    UTIL.cursorNormal();
    if (data.output.valid) {
      var res = data.output.response[0];
      $("#idGrilla").val(res.id);
      $("#grilla").val(res.grilla);
      $("#descripcion_grilla").val(res.descripcion_grilla);
      $("#tbl_ficha_tecnica_encuesta_id").val(res.tbl_ficha_tecnica_encuesta_id);
      $("#tipo_inferenciales").val(res.tipo_inferenciales);
      $("#aplica_cargos_publicos").val(res.aplica_cargos_publicos);
      $("#tbl_departamento_id").val(res.codigo_departamento);
      $("#tbl_municipio_id").val(res.codigo_municipio);
      $("#tbl_cargo_publico_id").val(res.tbl_cargo_publico_id);
      $("#dtcreate").val(res.dtcreate);
      $("#habilitado").prop('checked', res.habilitado === 'si');

      DEPARTAMENTO.getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio(
        res.codigo_departamento,
        res.codigo_municipio
      );

      $("#spanEncuesta").text(
        " Editar Información de Grillas N° " + res.id + " - " + res.grilla
      );
      $("#spanModulo").text("");
      UTIL.scrollToTop("formgrilla");

      // Informacion de los candidatos que tiene el grilla
      selectedCandidates = res.candidatos;

      // Manejar cambio en el select "Grilla para cargo público"
      GRILLA.handleSondeParaCargoPublicoChange();
    } else {
      UTIL.mostrarMensajeError(data.output.response.content);
    }
  },
  emptyCells: function () {
    $("#idGrilla").val("");
    $("#grilla").val("");
    $("#descripcion_grilla").val("");
    $("#tbl_ficha_tecnica_encuesta_id").val("");
    $("#aplica_cargos_publicos").val("no");
    $("#habilitado").prop('checked', true); // Por defecto habilitado
    $("#spanEncuesta").text("");
    $("#spanModulo").text("Ingreso de Información de Grillas");
    selectedCandidates = [];
    $("#departamento-field").addClass("d-none");
    $("#municipio-field").addClass("d-none");
    $(".cargo-publico-fields").addClass("d-none");
    $(".table-candidatos").addClass("d-none");

    // Informacion de opciones
    opcionesGrilla = [];
    $(".opciones-preguntas").addClass("d-none");

    GRILLA.handleSondeParaCargoPublicoChange();
  },
  validateData: function () {
    var bValid = true;
    var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
    if ($("#grilla").val() === "") {
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
      GRILLA.savedata();
    }
  },
  savedata: function () {
    q = {};
    q.op = "grillasave";
    q.id = $("#idGrilla").val();
    q.grilla = $("#grilla").val();
    q.descripcion_grilla = $("#descripcion_grilla").val();
    q.tbl_ficha_tecnica_encuesta_id = $("#tbl_ficha_tecnica_encuesta_id").val();
    q.tipo_inferenciales = $("#tipo_inferenciales").val();
    q.aplica_cargos_publicos = $("#aplica_cargos_publicos").val();
    q.codigo_departamento = $("#tbl_departamento_id").val();
    q.codigo_municipio = $("#tbl_municipio_id").val();
    q.tbl_cargo_publico_id = $("#tbl_cargo_publico_id").val();
    q.habilitado = $("#habilitado").is(':checked') ? 'si' : 'no';
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
    q.op = "grilladelete";
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
      GRILLA.getParticipantesByCargoPublicoId(cargoPublicoId);
    }
  },
  handleSondeParaCargoPublicoChange: function (thisElement) {
    const grillaParaCargoPublicoId = thisElement
      ? $(thisElement).val()
      : $("#aplica_cargos_publicos").val();
    if (grillaParaCargoPublicoId === "si") {
      $(".cargo-publico-fields").removeClass("d-none");
      $("#departamento-field").removeClass("d-none");
      $("#municipio-field").removeClass("d-none");
      $(".table-candidatos").removeClass("d-none");
      GRILLA.handleCargoPublicoChange();
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
    if ($("#aplica_cargos_publicos").val() == "si") {
      GRILLA.getParticipantesByCargoPublicoId(cargoPublicoId);
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
  if (el.checked) {
    if (!selectedCandidates.includes(candidateId)) {
      selectedCandidates.push(candidateId);
    }
  } else {
    selectedCandidates = selectedCandidates.filter((id) => id !== candidateId);
  }
  updateSelectedCandidatesDisplay();
}

// Función principal para renderizar la tabla con la data de participante
function showDataParticipantes(data) {
  if (data.length === 0) {
    data = selectedCandidates;
  }

  const tbody = document.querySelector("#candidatosTable tbody");
  if (!tbody) {
    console.error("Elemento <tbody> con ID 'candidatosTable' no encontrado.");
    return;
  }

  if (data.length === 0) {
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
  const isEditing = $("#idGrilla").val() > 0;

  if (filteredData.length === 0) {
    tableRows = `<tr><td colspan="9" class="py-4 text-center text-gray-500">No se encontraron candidatos para los criterios seleccionados.</td></tr>`;
  } else {
    filteredData.forEach((item) => {
      const shouldBeChecked =
        isEditing &&
        selectedCandidates.some(
          (candidate) => parseInt(candidate.id) === parseInt(item.id)
        );

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

function showParticipantsModal(candidatosDataString, grilla) {
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
    $("#grilla-title").text(grilla);
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
