/* global Choices, UTIL, DEPARTAMENTO_OPTIONS_HTML */

(function () {
  "use strict";

  let geoBlockIndex = 0;
  let q = {};
  const return_page = "espacio_geografico.php";

  // =============================
  // API PRINCIPAL
  // =============================
  window.ESPACIOGEOGRAFICO = {
    init: function () {
      // ✅ evitar colisiones: NO usamos init() global
      $("#tipo_estudio").off("change").on("change", handleTipoEstudioChange);

      // ✅ pinta 1 bloque base apenas abre (para que se vean departamentos)
      renderBaseBlock();

      // Botón agregar (solo Nacional)
      $("#add-departamento-btn").off("click").on("click", function () {
        addDepartmentBlock(1, true, true);
      });
    },

    editData: function (id) {
      q = { op: "espacioGeograficoget", id: id };
      UTIL.callAjaxRqstPOST(q, this.editdataHandler);
    },

    duplicar: function (id) {
      Swal.fire({
        title: '¿Duplicar registro?',
        text: 'Se creará un nuevo espacio geográfico con los mismos datos.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, duplicar',
        cancelButtonText: 'Cancelar'
      }).then(function (result) {
        if (!result.value) return;
        UTIL.cursorBusy();
        $.ajax({
          url: 'admin/ajax/rqst.php',
          type: 'POST',
          dataType: 'json',
          data: { op: 'espacioGeograficoduplicate', id: id },
          success: function (data) {
            UTIL.cursorNormal();
            if (data.output.valid) {
              UTIL.mostrarMensajeExitoso('Duplicado correctamente. ID nuevo: ' + data.output.response);
              setTimeout(function () { window.location.reload(); }, 1800);
            } else {
              UTIL.mostrarMensajeError(
                (data.output.response && data.output.response.content)
                  ? data.output.response.content : 'Error al duplicar.'
              );
            }
          },
          error: function () {
            UTIL.cursorNormal();
            UTIL.mostrarMensajeError('Error de comunicación con el servidor.');
          }
        });
      });
    },

    editdataHandler: function (data) {
      UTIL.cursorNormal();

      if (data && data.output && data.output.valid) {
        const res = data.output.response[0] || {};

        $("#idEspacioGeografico").val(res.id || "");
        $("#observaciones").val(res.observaciones || "");
        $("#tipo_estudio").val(res.tipo_estudio || "").trigger("change");

        $("#numero_comunas").val(res.numero_comunas || "");
        $("#numero_zonas").val(res.numero_zonas || "");
        $("#numero_veredas").val(res.numero_veredas || "");
        $("#cantidad_poblacion").val(res.cantidad_poblacion || "");
        $("#numero_votantes").val(res.numero_votantes || "");

        if (Array.isArray(res.geografias) && res.geografias.length > 0) {
          restoreDynamicBlocks(res.tipo_estudio, res.geografias);
        }

        $("#spanEncuesta").text(
          " Editar Espacio Geográfico N° " + (res.id || "") + " - " + (res.observaciones || "")
        );
        $("#spanModulo").text("");
      } else {
        UTIL.mostrarMensajeError(data?.output?.response?.content || "No se pudo cargar el registro.");
      }
    },

    validateData: function () {
      const msj = "Falta ingresar información obligatoria, marcada con asterisco.";
      const tipoEstudio = $("#tipo_estudio").val();

      if (!tipoEstudio) {
        UTIL.mostrarMensajeValidacion("Debe seleccionar el tipo de estudio.");
        return;
      }

      if ($("#observaciones").val().trim() === "") {
        UTIL.mostrarMensajeValidacion(msj);
        return;
      }

      if ($("#cantidad_poblacion").val() === "") {
        UTIL.mostrarMensajeValidacion(msj);
        return;
      }

      if ($("#numero_votantes").val() === "") {
        UTIL.mostrarMensajeValidacion(msj);
        return;
      }

      const $blocks = $("#dynamic-geo-container").find(".geo-block");
      if ($blocks.length === 0) {
        UTIL.mostrarMensajeValidacion("Debe seleccionar al menos un Departamento.");
        return;
      }

      // reglas por tipo
      if (tipoEstudio === "Departamental" && $blocks.length !== 1) {
        UTIL.mostrarMensajeValidacion("Para Departamental debe seleccionar exactamente 1 departamento.");
        return;
      }
      if (tipoEstudio === "Municipal" && $blocks.length !== 1) {
        UTIL.mostrarMensajeValidacion("Para Municipal debe seleccionar exactamente 1 departamento y 1 municipio.");
        return;
      }

      // validar cada bloque
      let ok = true;
      $blocks.each(function () {
        const $block = $(this);
        const depVal = $block.find('select[name^="departamento_"]').val();
        const $mun = $block.find('select[name^="municipio_"]');
        const munVal = $mun.val();

        if (!depVal) {
          ok = false;
          UTIL.mostrarMensajeValidacion("Debe seleccionar un Departamento en todos los bloques.");
          return false;
        }

        const isEmptyMun = Array.isArray(munVal) ? munVal.length === 0 : !munVal;
        if (depVal !== "00" && isEmptyMun) {
          ok = false;
          UTIL.mostrarMensajeValidacion("Debe seleccionar Municipio(s) en todos los bloques.");
          return false;
        }
      });

      if (!ok) return;

      this.savedata();
    },

    savedata: function () {
      const tipoEstudio = $("#tipo_estudio").val();

      const geoRelationsData = [];
      $("#dynamic-geo-container").find(".geo-block").each(function () {
        const depVal = $(this).find('select[name^="departamento_"]').val();
        const $mun = $(this).find('select[name^="municipio_"]');
        const munVal = $mun.val();

        if (!depVal) return true;

        // Departamento 00 (nacional): dejamos municipio vacío
        if (depVal === "00") {
          geoRelationsData.push({ departamento: depVal, municipio: "" });
          return true;
        }

        if (Array.isArray(munVal)) {
          munVal.forEach((m) => geoRelationsData.push({ departamento: depVal, municipio: m }));
        } else if (munVal) {
          geoRelationsData.push({ departamento: depVal, municipio: munVal });
        }
      });

      q = {
        op: "espacioGeograficosave",
        id: $("#idEspacioGeografico").val(),
        observaciones: $("#observaciones").val(),
        tipo_estudio: tipoEstudio,
        numero_comunas: $("#numero_comunas").val(),
        numero_zonas: $("#numero_zonas").val(),
        numero_veredas: $("#numero_veredas").val(),
        cantidad_poblacion: $("#cantidad_poblacion").val(),
        numero_votantes: $("#numero_votantes").val(),
        geografias: geoRelationsData,
      };

      UTIL.cursorBusy();
      $.ajax({
        data: q,
        type: "POST",
        dataType: "json",
        url: "admin/ajax/rqst.php",
        success: function (data) {
          UTIL.cursorNormal();
          if (data && data.output && data.output.valid) {
            UTIL.mostrarMensajeExitoso("Información guardada correctamente");
            setTimeout(function () {
              window.location = return_page;
            }, 1200);
          } else {
            UTIL.mostrarMensajeError(data?.output?.response?.content || "Error al guardar.");
          }
        },
        error: function (xhr) {
          UTIL.cursorNormal();
          console.error("AJAX error:", xhr?.responseText);
          UTIL.mostrarMensajeError("Ha ocurrido un error en la operación ejecutada.");
        },
      });
    },

    reload: function () {
      window.location = return_page;
    },
  };

  // =============================
  // UI DINÁMICA
  // =============================
  function renderBaseBlock() {
    // 1 bloque básico para que se vean departamentos desde el arranque
    $("#dynamic-geo-container").empty();
    geoBlockIndex = 0;
    addDepartmentBlock(1, false, false);
    $("#add-geo-button-container").hide();
    $("#municipal-fields").hide();
  }

  function handleTipoEstudioChange() {
    const tipoEstudio = $("#tipo_estudio").val();

    $("#dynamic-geo-container").empty();
    geoBlockIndex = 0;

    let initialBlocks = 1;
    let isMunicipioMultiple = false;
    let showAddButton = false;
    let showMunicipalFields = false;
    let allowRemove = false;

    switch (tipoEstudio) {
      case "Departamental":
        isMunicipioMultiple = true;
        showAddButton = false;
        showMunicipalFields = false;
        allowRemove = false;
        break;

      case "Nacional":
        isMunicipioMultiple = true;
        showAddButton = true;
        showMunicipalFields = false;
        allowRemove = true;
        break;

      case "Municipal":
        isMunicipioMultiple = false;
        showAddButton = false;
        showMunicipalFields = true;
        allowRemove = false;
        break;

      default:
        // si vuelve a vacío, pintamos base
        renderBaseBlock();
        return;
    }

    addDepartmentBlock(initialBlocks, allowRemove, isMunicipioMultiple);

    $("#add-geo-button-container").toggle(showAddButton);
    $("#municipal-fields").toggle(showMunicipalFields);

    if (!showMunicipalFields) {
      $("#numero_comunas,#numero_zonas,#numero_veredas").val("");
    }
  }

  function generateGeoBlock(index, allowRemove, isMunicipioMultiple) {
    const depId = `departamento_${index}`;
    const munId = `municipio_${index}`;

    const depOptions =
      typeof DEPARTAMENTO_OPTIONS_HTML !== "undefined"
        ? DEPARTAMENTO_OPTIONS_HTML
        : '<option value="">Error al cargar Departamentos</option>';

    const multipleAttr = isMunicipioMultiple ? 'multiple="multiple"' : "";
    const munName = isMunicipioMultiple ? `${munId}[]` : munId;

    const munLabel = isMunicipioMultiple ? "Municipios" : "Municipio";
    const munPlaceholder = isMunicipioMultiple
      ? "Seleccione uno o varios municipios"
      : "Seleccione un municipio";

    return `
      <div class="col-12 geo-block mb-2" data-index="${index}">
        <div class="row g-3 align-items-end">
          <div class="col-sm-12 col-md-6 col-lg-5">
            <div class="form-floating">
              <select class="form-select dep-select" id="${depId}" name="${depId}" data-target-municipio="${munId}">
                ${depOptions}
              </select>
              <label for="${depId}">Departamento <span class="text-danger">*</span></label>
            </div>
          </div>

          <div class="col-sm-12 col-md-6 col-lg-5">
            ${
              isMunicipioMultiple
                ? `
                  <label class="form-label fw-bold mb-1" for="${munId}">${munLabel} <span class="text-danger">*</span></label>
                  <select class="form-select mun-select" id="${munId}" name="${munName}" ${multipleAttr} disabled>
                    <option value="" disabled>${munPlaceholder}</option>
                  </select>
                `
                : `
                  <div class="form-floating">
                    <select class="form-select mun-select" id="${munId}" name="${munId}" disabled>
                      <option value="" selected disabled>${munPlaceholder}</option>
                    </select>
                    <label for="${munId}">${munLabel} <span class="text-danger">*</span></label>
                  </div>
                `
            }
          </div>

          <div class="col-sm-12 col-md-12 col-lg-2 d-flex justify-content-start gap-2">
            ${
              allowRemove
                ? `<button type="button" class="btn btn-danger btn-sm remove-block-btn">
                     <i class="fas fa-trash-alt"></i>
                   </button>`
                : ""
            }
          </div>
        </div>
      </div>
    `;
  }

  function addDepartmentBlock(count, allowRemove, isMunicipioMultiple) {
    const $container = $("#dynamic-geo-container");

    for (let i = 0; i < count; i++) {
      const html = generateGeoBlock(geoBlockIndex, allowRemove, isMunicipioMultiple);
      $container.append(html);

      const idx = geoBlockIndex;
      const $dep = $(`#departamento_${idx}`);
      const $mun = $(`#municipio_${idx}`);

      // remove
      $container
        .find(`.geo-block[data-index="${idx}"] .remove-block-btn`)
        .off("click")
        .on("click", function () {
          $(this).closest(".geo-block").remove();
        });

      // change departamento => cargar municipios
      $dep.off("change").on("change", function () {
        const depCode = $(this).val();
        cargarMunicipios(depCode, $mun, isMunicipioMultiple);
      });

      geoBlockIndex++;
    }
  }

  function initializeChoices(selectEl) {
    if (!selectEl) return null;

    // destruir si ya existe
    if (selectEl.choicesInstance) {
      try { selectEl.choicesInstance.destroy(); } catch (e) {}
      selectEl.choicesInstance = null;
    }

    const ch = new Choices(selectEl, {
      removeItemButton: true,
      shouldSort: false,
      placeholder: true,
      placeholderValue: "Seleccione municipios",
      searchPlaceholderValue: "Buscar...",
      noResultsText: "No se encontraron resultados",
      itemSelectText: "Presione para seleccionar",
    });

    selectEl.choicesInstance = ch;
    return ch;
  }

  function cargarMunicipios(depCode, $munSelect, isMultiple, callback) {
    // reset
    const el = $munSelect.get(0);
    if (el && el.choicesInstance) {
      try { el.choicesInstance.destroy(); } catch (e) {}
      el.choicesInstance = null;
    }

    $munSelect.prop("disabled", true).empty();

    // Departamento 00: no requiere municipios
    if (!depCode || depCode === "00") {
      $munSelect.append(`<option value="" selected disabled>Aplica a todos (Nacional)</option>`);
      $munSelect.prop("disabled", true);
      if (typeof callback === "function") callback();
      return;
    }

    // placeholder loading
    $munSelect.append(`<option value="" selected disabled>Cargando municipios...</option>`);

    q = { op: "ciudadget", codigo_departamento: depCode };

    UTIL.callAjaxRqstPOST(q, function (resp) {
      UTIL.cursorNormal();
      $munSelect.empty();

      const ok =
        resp && resp.output && resp.output.valid && Array.isArray(resp.output.response);

      if (!ok) {
        $munSelect.append(`<option value="" selected disabled>No hay municipios</option>`);
        UTIL.mostrarMensajeError(resp?.output?.response?.content || "Error al cargar municipios.");
        if (typeof callback === "function") callback();
        return;
      }

      // placeholder
      if (isMultiple) {
        $munSelect.append(`<option value="" disabled>Seleccione uno o varios municipios</option>`);
      } else {
        $munSelect.append(`<option value="" selected disabled>Seleccione un municipio</option>`);
      }

      resp.output.response.forEach((m) => {
        // ✅ tolerante a nombres reales del backend
        const code =
          (m.codigo_municipio ?? m.codigo_muncipio ?? m.codigo_ciudad ?? "").toString().trim();
        const name =
          (m.municipio ?? m.nombre_municipio ?? m.ciudad ?? "").toString().trim();

        if (!code) return;

        $munSelect.append(
          `<option value="${escapeHtml(code)}">${escapeHtml(code)} - ${escapeHtml(name || "Municipio")}</option>`
        );
      });

      $munSelect.prop("disabled", false);

      if (isMultiple) {
        setTimeout(() => {
          initializeChoices($munSelect.get(0));
          if (typeof callback === "function") callback();
        }, 50);
      } else {
        if (typeof callback === "function") callback();
      }
    });
  }

  // =============================
  // RESTORE EDIT
  // =============================
  function restoreDynamicBlocks(tipoEstudio, relaciones) {
    $("#dynamic-geo-container").empty();
    geoBlockIndex = 0;

    const isMultiple = (tipoEstudio === "Departamental" || tipoEstudio === "Nacional");
    const canRemove = (tipoEstudio === "Nacional");

    // agrupar por dep
    const grouped = {};
    relaciones.forEach((r) => {
      const dep = String(r.codigo_departamento ?? r.departamento ?? "").trim();
      const mun = String(r.codigo_ciudad ?? r.codigo_municipio ?? r.municipio ?? "").trim();
      if (!dep) return;
      if (!grouped[dep]) grouped[dep] = [];
      if (mun) grouped[dep].push(mun);
    });

    const deps = Object.keys(grouped);
    if (deps.length === 0) {
      addDepartmentBlock(1, false, isMultiple);
      return;
    }

    deps.forEach((depCode, idx) => {
      addDepartmentBlock(1, canRemove || idx > 0, isMultiple);
      const currentIndex = geoBlockIndex - 1;

      const $dep = $(`#departamento_${currentIndex}`);
      const $mun = $(`#municipio_${currentIndex}`);

      $dep.val(depCode);

      cargarMunicipios(depCode, $mun, isMultiple, function () {
        const munCodes = grouped[depCode] || [];

        if (depCode === "00") return;

        if (isMultiple) {
          const inst = $mun.get(0)?.choicesInstance;
          if (inst) {
            munCodes.forEach((v) => {
              try { inst.setChoiceByValue(String(v)); } catch (e) {}
            });
          }
        } else {
          if (munCodes[0]) $mun.val(String(munCodes[0])).trigger("change");
        }
      });
    });

    $("#municipal-fields").toggle(tipoEstudio === "Municipal");
    $("#add-geo-button-container").toggle(tipoEstudio === "Nacional");
  }

  // =============================
  // HELPERS
  // =============================
  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // ✅ arranque seguro
  $(function () {
    if (typeof DEPARTAMENTO_OPTIONS_HTML === "undefined") {
      console.error("DEPARTAMENTO_OPTIONS_HTML no está definido en la vista.");
    }
    window.ESPACIOGEOGRAFICO.init();
  });

})();
