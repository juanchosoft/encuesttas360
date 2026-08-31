/* global Choices, UTIL, DEPARTAMENTO_OPTIONS_HTML, Swal */

(function () {
  "use strict";

  let geoBlockIndex = 0;
  let q = {};
  const return_page = "espacio_geografico.php";

  window.ESPACIOGEOGRAFICO = {
    init: function () {
      $("#tipo_estudio").off("change.eg").on("change.eg", handleTipoEstudioChange);
      $("#add-departamento-btn").off("click.eg").on("click.eg", function () {
        addDepartmentBlock("municipal", true);
      });
      resetTerritorioUi();
    },

    editData: function (id) {
      q = { op: "espacioGeograficoget", id: id };
      UTIL.callAjaxRqstPOST(q, this.editdataHandler);
    },

    duplicar: function (id) {
      Swal.fire({
        title: "¿Duplicar registro?",
        text: "Se creará un nuevo espacio geográfico con los mismos datos.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, duplicar",
        cancelButtonText: "Cancelar",
      }).then(function (result) {
        if (!result.value) return;
        UTIL.cursorBusy();
        $.ajax({
          url: "admin/ajax/rqst.php",
          type: "POST",
          dataType: "json",
          data: { op: "espacioGeograficoduplicate", id: id },
          success: function (data) {
            UTIL.cursorNormal();
            if (data.output.valid) {
              UTIL.mostrarMensajeExitoso("Duplicado correctamente. ID nuevo: " + data.output.response);
              setTimeout(function () { window.location.reload(); }, 1800);
            } else {
              UTIL.mostrarMensajeError(
                (data.output.response && data.output.response.content)
                  ? data.output.response.content
                  : "Error al duplicar."
              );
            }
          },
          error: function () {
            UTIL.cursorNormal();
            UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
          },
        });
      });
    },

    eliminar: function (id) {
      Swal.fire({
        title: "¿Eliminar espacio geográfico?",
        text: "Se conservará en base de datos pero dejará de estar disponible. Solo es posible si no está en uso por una encuesta activa.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#dc3545",
      }).then(function (result) {
        if (!result.value) return;
        UTIL.cursorBusy();
        $.ajax({
          url: "admin/ajax/rqst.php",
          type: "POST",
          dataType: "json",
          data: { op: "espacioGeograficodelete", id: id },
          success: function (data) {
            UTIL.cursorNormal();
            if (data.output && data.output.valid) {
              UTIL.mostrarMensajeExitoso("Espacio geográfico eliminado correctamente.");
              setTimeout(function () { window.location.reload(); }, 1200);
            } else {
              UTIL.mostrarMensajeError(
                (data.output && data.output.response && data.output.response.content)
                  ? data.output.response.content
                  : "No se pudo eliminar el registro."
              );
            }
          },
          error: function () {
            UTIL.cursorNormal();
            UTIL.mostrarMensajeError("Error de comunicación con el servidor.");
          },
        });
      });
    },

    verGeografias: function (id) {
      const modalEl = document.getElementById("modalGeografias");
      if (!modalEl) return;

      $("#modalGeografiasBody").html(
        '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x" style="color:var(--geo-brand);"></i>' +
        '<p class="mt-3 mb-0" style="color:#667085;font-weight:700;">Cargando información...</p></div>'
      );

      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();

      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: { op: "espacioGeograficoget", id: id },
        success: function (data) {
          if (!data || !data.output || !data.output.valid || !data.output.response.length) {
            $("#modalGeografiasBody").html('<div class="alert alert-warning mb-0">No se encontró información.</div>');
            return;
          }

          const res = data.output.response[0];
          const tipo = res.tipo_estudio || "N/A";
          const geografias = Array.isArray(res.geografias) ? res.geografias : [];

          let html = '<div class="row g-3 mb-3">';
          html += '<div class="col-md-6"><strong>Tipo:</strong> ' + escapeHtml(tipo) + "</div>";
          html += '<div class="col-md-6"><strong>Observaciones:</strong> ' + escapeHtml(res.observaciones || "") + "</div>";
          html += '<div class="col-md-6"><strong>Población:</strong> ' + escapeHtml(String(res.cantidad_poblacion || "—")) + "</div>";
          html += '<div class="col-md-6"><strong>Encuestados:</strong> ' + escapeHtml(String(res.numero_votantes || "—")) + "</div>";
          html += "</div>";

          if (String(tipo).toLowerCase() === "nacional") {
            html += '<div class="alert alert-info mb-0"><i class="fas fa-globe-americas me-2"></i>Aplica a todo el país. Sin restricción departamental ni municipal.</div>';
          } else if (geografias.length === 0) {
            html += '<div class="alert alert-secondary mb-0">Sin cobertura territorial registrada.</div>';
          } else {
            html += '<div class="table-responsive"><table class="table table-sm table-striped mb-0">';
            html += "<thead><tr><th>Departamento</th><th>Municipio</th></tr></thead><tbody>";
            geografias.forEach(function (g) {
              const dep = g.codigo_departamento || g.departamento || "—";
              const mun = g.codigo_ciudad || g.municipio || "";
              const munTxt = mun === "" ? "<em class=\"text-muted\">Todo el departamento</em>" : escapeHtml(mun);
              html += "<tr><td>" + escapeHtml(dep) + "</td><td>" + munTxt + "</td></tr>";
            });
            html += "</tbody></table></div>";
          }

          $("#modalGeografiasBody").html(html);
        },
        error: function () {
          $("#modalGeografiasBody").html('<div class="alert alert-danger mb-0">Error al cargar las geografías.</div>');
        },
      });
    },

    editdataHandler: function (data) {
      UTIL.cursorNormal();

      if (data && data.output && data.output.valid) {
        const res = data.output.response[0] || {};

        $("#idEspacioGeografico").val(res.id || "");
        $("#observaciones").val(res.observaciones || "");
        $("#tipo_estudio").val(res.tipo_estudio || "").trigger("change");

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

      const tipoLower = String(tipoEstudio).toLowerCase();

      if (tipoLower === "nacional") {
        this.savedata();
        return;
      }

      const $blocks = $("#dynamic-geo-container").find(".geo-block");
      if ($blocks.length === 0) {
        UTIL.mostrarMensajeValidacion("Debe configurar la cobertura territorial.");
        return;
      }

      if (tipoLower === "departamental" && $blocks.length !== 1) {
        UTIL.mostrarMensajeValidacion("Para Departamental debe seleccionar exactamente un departamento.");
        return;
      }

      if (tipoLower === "municipal" && $blocks.length !== 1) {
        UTIL.mostrarMensajeValidacion("Para Municipal debe seleccionar un departamento y sus municipios.");
        return;
      }

      let ok = true;
      $blocks.each(function () {
        const $block = $(this);
        const depVal = $block.find('select[name^="departamento_"]').val();

        if (!depVal) {
          ok = false;
          UTIL.mostrarMensajeValidacion("Debe seleccionar un departamento.");
          return false;
        }

        if (tipoLower === "municipal") {
          const $mun = $block.find('select[name^="municipio_"]');
          const munVal = $mun.val();
          const isEmptyMun = Array.isArray(munVal) ? munVal.length === 0 : !munVal;
          if (isEmptyMun) {
            ok = false;
            UTIL.mostrarMensajeValidacion("Debe seleccionar al menos un municipio.");
            return false;
          }
        }
      });

      if (!ok) return;
      this.savedata();
    },

    savedata: function () {
      const tipoEstudio = $("#tipo_estudio").val();
      const tipoLower = String(tipoEstudio).toLowerCase();
      const geoRelationsData = [];

      if (tipoLower !== "nacional") {
        $("#dynamic-geo-container").find(".geo-block").each(function () {
          const depVal = $(this).find('select[name^="departamento_"]').val();
          if (!depVal) return true;

          if (tipoLower === "departamental") {
            geoRelationsData.push({ departamento: depVal, municipio: "" });
            return true;
          }

          const $mun = $(this).find('select[name^="municipio_"]');
          const munVal = $mun.val();
          if (Array.isArray(munVal)) {
            munVal.forEach(function (m) {
              if (m) geoRelationsData.push({ departamento: depVal, municipio: m });
            });
          } else if (munVal) {
            geoRelationsData.push({ departamento: depVal, municipio: munVal });
          }
        });
      }

      q = {
        op: "espacioGeograficosave",
        id: $("#idEspacioGeografico").val(),
        observaciones: $("#observaciones").val(),
        tipo_estudio: tipoEstudio,
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

  function resetTerritorioUi() {
    $("#dynamic-geo-container").empty();
    geoBlockIndex = 0;
    $("#add-geo-button-container").hide();
    $("#geo-section-territorio").hide();
    $("#geoDynamicPlaceholder").show();
  }

  function handleTipoEstudioChange() {
    const tipoEstudio = $("#tipo_estudio").val();
    const tipoLower = String(tipoEstudio || "").toLowerCase();

    $("#dynamic-geo-container").empty();
    geoBlockIndex = 0;
    $("#add-geo-button-container").hide();

    if (!tipoEstudio) {
      resetTerritorioUi();
      return;
    }

    if (tipoLower === "nacional") {
      $("#geo-section-territorio").hide();
      $("#geoDynamicPlaceholder").hide();
      return;
    }

    $("#geo-section-territorio").show();
    $("#geoDynamicPlaceholder").hide();

    if (tipoLower === "departamental") {
      addDepartmentBlock("departamental", false);
      return;
    }

    if (tipoLower === "municipal") {
      addDepartmentBlock("municipal", false);
    }
  }

  function generateGeoBlock(index, allowRemove, mode) {
    const depId = "departamento_" + index;
    const munId = "municipio_" + index;
    const depOptions =
      typeof DEPARTAMENTO_OPTIONS_HTML !== "undefined"
        ? DEPARTAMENTO_OPTIONS_HTML
        : '<option value="">Error al cargar Departamentos</option>';

    const isMunicipal = mode === "municipal";
    const munName = isMunicipal ? munId + "[]" : munId;

    let munCol = "";
    if (mode === "departamental") {
      munCol = "";
    } else if (isMunicipal) {
      munCol =
        '<div class="col-sm-12 col-md-6 col-lg-5">' +
        '<label class="form-label fw-bold mb-1" for="' + munId + '">Municipios <span class="text-danger">*</span></label>' +
        '<select class="form-select mun-select" id="' + munId + '" name="' + munName + '" multiple="multiple" disabled>' +
        '<option value="" disabled>Seleccione uno o varios municipios</option>' +
        "</select>" +
        '<div class="d-flex flex-wrap gap-2 mt-2 geo-muni-actions" data-target="' + munId + '" style="display:none;">' +
        '<button type="button" class="btn btn-sm btn-outline-primary btn-select-all-muni" data-target="' + munId + '">' +
        '<i class="fas fa-check-double me-1"></i>Seleccionar todos' +
        "</button>" +
        '<button type="button" class="btn btn-sm btn-outline-secondary btn-deselect-all-muni" data-target="' + munId + '">' +
        '<i class="fas fa-times me-1"></i>Quitar todos' +
        "</button>" +
        "</div>" +
        "</div>";
    }

    const depColClass = mode === "departamental" ? "col-sm-12 col-md-8 col-lg-8" : "col-sm-12 col-md-6 col-lg-5";

    return (
      '<div class="col-12 geo-block mb-2" data-index="' + index + '" data-mode="' + mode + '">' +
      '<div class="row g-3 align-items-end">' +
      '<div class="' + depColClass + '">' +
      '<div class="form-floating">' +
      '<select class="form-select dep-select" id="' + depId + '" name="' + depId + '" data-target-municipio="' + munId + '">' +
      depOptions +
      "</select>" +
      '<label for="' + depId + '">Departamento <span class="text-danger">*</span></label>' +
      "</div></div>" +
      munCol +
      '<div class="col-sm-12 col-md-12 col-lg-2 d-flex justify-content-start gap-2">' +
      (allowRemove
        ? '<button type="button" class="btn btn-danger btn-sm remove-block-btn"><i class="fas fa-trash-alt"></i></button>'
        : "") +
      "</div></div></div>"
    );
  }

  function addDepartmentBlock(mode, allowRemove) {
    const $container = $("#dynamic-geo-container");
    const html = generateGeoBlock(geoBlockIndex, allowRemove, mode);
    $container.append(html);

    const idx = geoBlockIndex;
    const $dep = $("#departamento_" + idx);
    const $mun = $("#municipio_" + idx);

    $container.find('.geo-block[data-index="' + idx + '"] .remove-block-btn').off("click").on("click", function () {
      $(this).closest(".geo-block").remove();
    });

    $dep.off("change.eg").on("change.eg", function () {
      if (mode === "municipal") {
        cargarMunicipios($(this).val(), $mun, true);
      }
    });

    const munTarget = $mun.attr("id");
    $container.off("click.selectAll", '.btn-select-all-muni[data-target="' + munTarget + '"]');
    $container.on("click.selectAll", '.btn-select-all-muni[data-target="' + munTarget + '"]', function () {
      selectAllMunicipios($mun);
    });
    $container.off("click.deselectAll", '.btn-deselect-all-muni[data-target="' + munTarget + '"]');
    $container.on("click.deselectAll", '.btn-deselect-all-muni[data-target="' + munTarget + '"]', function () {
      deselectAllMunicipios($mun);
    });

    geoBlockIndex++;
  }

  function selectAllMunicipios($munSelect) {
    const el = $munSelect.get(0);
    if (!el) return;
    const inst = el.choicesInstance;
    if (inst) {
      const values = Array.from(el.options).map(function (o) { return o.value; }).filter(function (v) { return v; });
      inst.removeActiveItems();
      values.forEach(function (v) {
        try { inst.setChoiceByValue(String(v)); } catch (e) { /* ignore */ }
      });
    } else {
      Array.from(el.options).forEach(function (o) {
        if (o.value) o.selected = true;
      });
      $munSelect.trigger("change");
    }
  }

  function deselectAllMunicipios($munSelect) {
    const el = $munSelect.get(0);
    if (!el) return;
    const inst = el.choicesInstance;
    if (inst) {
      inst.removeActiveItems();
    } else {
      Array.from(el.options).forEach(function (o) {
        o.selected = false;
      });
      $munSelect.trigger("change");
    }
  }

  function initializeChoices(selectEl) {
    if (!selectEl) return null;

    if (selectEl.choicesInstance) {
      try { selectEl.choicesInstance.destroy(); } catch (e) { /* ignore */ }
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
    const el = $munSelect.get(0);
    const $muniActions = $('.geo-muni-actions[data-target="' + $munSelect.attr("id") + '"]');

    if (el && el.choicesInstance) {
      try { el.choicesInstance.destroy(); } catch (e) { /* ignore */ }
      el.choicesInstance = null;
    }

    $munSelect.prop("disabled", true).empty();
    $muniActions.hide();

    if (!depCode) {
      $munSelect.append('<option value="" selected disabled>Seleccione un departamento primero</option>');
      if (typeof callback === "function") callback();
      return;
    }

    $munSelect.append('<option value="" selected disabled>Cargando municipios...</option>');

    q = { op: "ciudadget", codigo_departamento: depCode };

    UTIL.callAjaxRqstPOST(q, function (resp) {
      UTIL.cursorNormal();
      $munSelect.empty();

      const ok = resp && resp.output && resp.output.valid && Array.isArray(resp.output.response);

      if (!ok) {
        $munSelect.append('<option value="" selected disabled>No hay municipios</option>');
        UTIL.mostrarMensajeError(resp?.output?.response?.content || "Error al cargar municipios.");
        if (typeof callback === "function") callback();
        return;
      }

      $munSelect.append('<option value="" disabled>Seleccione uno o varios municipios</option>');

      resp.output.response.forEach(function (m) {
        const code = (m.codigo_municipio ?? m.codigo_muncipio ?? m.codigo_ciudad ?? "").toString().trim();
        const name = (m.municipio ?? m.nombre_municipio ?? m.ciudad ?? "").toString().trim();
        if (!code) return;
        $munSelect.append(
          '<option value="' + escapeHtml(code) + '">' + escapeHtml(code) + " - " + escapeHtml(name || "Municipio") + "</option>"
        );
      });

      $munSelect.prop("disabled", false);

      if (isMultiple) {
        setTimeout(function () {
          initializeChoices($munSelect.get(0));
          $muniActions.css("display", "flex");
          if (typeof callback === "function") callback();
        }, 50);
      } else if (typeof callback === "function") {
        callback();
      }
    });
  }

  function restoreDynamicBlocks(tipoEstudio, relaciones) {
    $("#dynamic-geo-container").empty();
    geoBlockIndex = 0;

    const tipoLower = String(tipoEstudio || "").toLowerCase();

    if (tipoLower === "nacional") {
      $("#geo-section-territorio").hide();
      return;
    }

    $("#geo-section-territorio").show();
    $("#geoDynamicPlaceholder").hide();

    if (tipoLower === "departamental") {
      addDepartmentBlock("departamental", false);
      const depCode = String(relaciones[0]?.codigo_departamento ?? relaciones[0]?.departamento ?? "").trim();
      if (depCode) {
        $("#departamento_0").val(depCode);
      }
      return;
    }

    const grouped = {};
    relaciones.forEach(function (r) {
      const dep = String(r.codigo_departamento ?? r.departamento ?? "").trim();
      const mun = String(r.codigo_ciudad ?? r.codigo_municipio ?? r.municipio ?? "").trim();
      if (!dep) return;
      if (!grouped[dep]) grouped[dep] = [];
      if (mun) grouped[dep].push(mun);
    });

    const deps = Object.keys(grouped);
    if (deps.length === 0) {
      addDepartmentBlock("municipal", false);
      return;
    }

    deps.forEach(function (depCode, blockIdx) {
      addDepartmentBlock("municipal", blockIdx > 0);
      const currentIndex = geoBlockIndex - 1;
      const $dep = $("#departamento_" + currentIndex);
      const $mun = $("#municipio_" + currentIndex);

      $dep.val(depCode);

      cargarMunicipios(depCode, $mun, true, function () {
        const munCodes = grouped[depCode] || [];
        const inst = $mun.get(0)?.choicesInstance;
        if (inst) {
          munCodes.forEach(function (v) {
            try { inst.setChoiceByValue(String(v)); } catch (e) { /* ignore */ }
          });
        } else if (munCodes[0]) {
          $mun.val(munCodes).trigger("change");
        }
      });
    });
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  $(function () {
    if (typeof DEPARTAMENTO_OPTIONS_HTML === "undefined") {
      console.error("DEPARTAMENTO_OPTIONS_HTML no está definido en la vista.");
    }
    window.ESPACIOGEOGRAFICO.init();
  });
})();
