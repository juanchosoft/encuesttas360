$(document).ready(function () {
  q = {};
});

var q;
var informacionMunicipio = {};

var DEPARTAMENTO = {
  getMunicipiosConParametros: function () {
    if (typeof depSelect !== "undefined" && depSelect !== "") {
      $("#tbl_departamento_id").val(depSelect).trigger("change");
    }

    const dep = ($("#tbl_departamento_id").val() || "").toString().trim();
    if (dep && dep !== "seleccione") {
      q = { op: "ciudadget", codigo_departamento: dep };
      UTIL.callAjaxRqstPOST(q, this.getMunicipiosHandler);
    } else {
      $("#tbl_municipio_id").empty().append(`<option value="">Seleccione</option>`);
    }
  },

  getMunicipiosConDepartamentoPrincipal: function () {
    const dep = (UTIL.getDepartamentoPrincipal() || "").toString().trim();
    q = { op: "ciudadget", codigo_departamento: dep };
    UTIL.callAjaxRqstPOST(q, this.getMunicipiosHandler);
  },

  getMunicipios: function () {
    const dep = ($("#tbl_departamento_id").val() || "").toString().trim();

    if (dep && dep !== "seleccione") {
      q = { op: "ciudadget", codigo_departamento: dep };
      UTIL.callAjaxRqstPOST(q, this.getMunicipiosHandler);
    } else {
      $("#tbl_municipio_id").empty().append(`<option value="">Seleccione</option>`);
    }
  },

  getMunicipiosHandler: function (data) {
    UTIL.cursorNormal();

    if (!data?.output?.valid) {
      UTIL.mostrarMensajeError(data?.output?.response?.content || "Error al obtener municipios.");
      $("#tbl_municipio_id").empty().append(`<option value="">Seleccione</option>`);
      return;
    }

    const depto = ($("#tbl_departamento_id").val() || "").toString().trim();
    const res = data.output.response || [];

    // ✅ Si no existen estos inputs en la vista, NO filtrar
    const $munUser = $("#municipioUsuario");
    const $tipoUser = $("#tipoUsuario");

    const municipioDelUsuario = $munUser.length ? ($munUser.val() || "").toString().trim() : "";
    const tipoUsuario = $tipoUser.length ? ($tipoUser.val() || "").toString().trim() : "";

    const esAlcalde = (tipoUsuario === "Alcalde" || tipoUsuario === "Auxiliar_Alcalde");

    let optionsHtml = `<option value="">Seleccione</option>`;

    const generarOpcion = (m, rutaMapa) => {
      const codigo = (m.codigo_muncipio || "").toString().trim();
      const nombre = (m.municipio || "").toString().trim();
      const mapa = (rutaMapa || "").toString().replace(/'/g, "");
      if (!codigo) return "";
      return `<option value="${codigo}" data-mapa="${mapa}">${nombre}</option>`;
    };

    res.forEach((m) => {
      // ✅ Filtro solo si existen tipoUsuario/municipioUsuario y aplica
      if (esAlcalde && municipioDelUsuario && municipioDelUsuario !== (m.codigo_muncipio + "")) return;

      const rutaMapa = m.carpeta_mapa
        ? m.carpeta_mapa.replace("mapa-veredas/", `mapa-veredas/${depto}/`)
        : "";

      optionsHtml += generarOpcion(m, rutaMapa);
    });

    // ✅ Si quedó sin municipios, mostrar mensaje
    if (optionsHtml === `<option value="">Seleccione</option>`) {
      optionsHtml += `<option value="" disabled>Sin municipios disponibles</option>`;
    }

    const $sel = $("#tbl_municipio_id");
    $sel.empty().append(optionsHtml);

    // ✅ Selección automática para evitar value vacío
    // 1) si existe munSelect lo aplica
    if (typeof munSelect !== "undefined" && munSelect) {
      setTimeout(function () {
        $sel.val(munSelect).trigger("change");
        // si no existe esa opción, selecciona la primera real
        if (!$sel.val()) {
          const firstReal = $sel.find("option[value!='']").first().val() || "";
          if (firstReal) $sel.val(firstReal).trigger("change");
        }
      }, 10);
      return;
    }

    // 2) si no hay munSelect, selecciona el primer municipio real
    setTimeout(function () {
      if (!$sel.val()) {
        const firstReal = $sel.find("option[value!='']").first().val() || "";
        if (firstReal) $sel.val(firstReal).trigger("change");
      }
    }, 10);

    // ✅ Si está el filtro vereda
    if ($("#filtro").length && $("#filtro").val() === "vereda") {
      DEPARTAMENTO.getVeredasByMunicipioId();
    }
  },

  // ==========================
  // Veredas (sin cambios fuertes)
  // ==========================
  getVeredasByMunicipioIdInformacionMapa: function (municipioId, veredaIdASetar = 0) {
    q = {};
    q.op = "veredaget";
    q.municipio_id = municipioId;

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
          var info = "";
          var res = data.output.response;
          for (var j in res) {
            info += "<option value='" + res[j].id + "' selected>" + res[j].nombre_vereda + "</option>";
          }
          $("#tbl_vereda_id").empty().append(info);

          if (veredaIdASetar && veredaIdASetar > 0) {
            $("#tbl_vereda_id").val(veredaIdASetar).trigger("change");
          }
        } else {
          UTIL.mostrarMensajeError(data.output.response.content);
        }
      },
    });
  },

  getVeredasByMunicipioId: function (changeValue = false) {
    if (changeValue) {
      let urlParts = URLToArray(ACTUAL_URL);
      urlParts.mun = $("#tbl_municipio_id").val();
      var newURL = window.location.href.split("?")[0] + "?" + $.param(urlParts);
      location.href = newURL;
    }

    if ($("#tbl_departamento_id").val() != "seleccione") {
      q = {};
      q.op = "veredaget";
      q.municipio_id = $("#tbl_municipio_id").val();
      UTIL.callAjaxRqstPOST(q, this.getVeredasByMunicipioIdHandler);
    } else {
      $("#tbl_vereda_id").empty().append("");
    }
  },

  getVeredasByMunicipioIdHandler: function (data) {
    informacionMunicipio = {};
    UTIL.cursorNormal();

    if (data.output.valid) {
      var res = data.output.response;
      informacionMunicipio = data.output.municipio[0];
      var info = "";

      for (var j in res) {
        if ($("#filtroVeredaById").val() === "si") {
          info += "<option value='" + res[j].id + "'>" + res[j].nombre_vereda + "</option>";
        } else {
          if (typeof veredaSelect != "undefined" && veredaSelect == res[j].nombre_vereda) {
            info += "<option value='" + res[j].nombre_vereda + "' selected>" + res[j].nombre_vereda + "</option>";
          } else {
            info += "<option value='" + res[j].nombre_vereda + "'>" + res[j].nombre_vereda + "</option>";
          }
        }
      }

      $("#tbl_vereda_id").empty().append(info);
    } else {
      UTIL.mostrarMensajeError(data.output.response.content);
    }
  },
  getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio: function (
    departamentoId,
    municipioSetear = 0
  ) {
    UTIL.cursorBusy();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "GET",
      dataType: "json",
      data: {
        op: "ciudadget",
        codigo_departamento: departamentoId,
      },
      success: function (data) {
        UTIL.cursorNormal();

        if (!data.output.valid) {
          UTIL.mostrarMensajeError(data.output.response.content);
          return;
        }

        const res = data.output.response;
        const $municipioSelect = $("#tbl_municipio_id");

        // Generar opciones con map y join para mejor rendimiento
        const opciones = res
          .map(
            (m) =>
              `<option value="${m.codigo_muncipio}">${m.municipio}</option>`
          )
          .join("");

        $municipioSelect.empty().append(opciones);

        // Asegurar que el valor se setea correctamente después de actualizar las opciones
        if (municipioSetear > 0) {
          setTimeout(
            () => $municipioSelect.val(municipioSetear).trigger("change"),
            10
          );
        }
      },
    });
  },
};
