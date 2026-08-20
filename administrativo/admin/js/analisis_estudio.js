/**
 * Módulo de Análisis de Estudio
 * Permite a investigadores calcular y registrar indicadores para candidatos en grillas
 */

const ANALISIS_ESTUDIO = {
  candidatoSeleccionado: null,
  formulaSeleccionada: null,
  grillaSeleccionada: null,
  todasLasFormulas: [],
  operaciones: [], // Array para almacenar las operaciones de la calculadora
  contadorOperaciones: 0, // Contador para IDs únicos de operaciones

  init: function () {
    ANALISIS_ESTUDIO.cargarTodasFormulas();
    ANALISIS_ESTUDIO.cargarHistorial();
    ANALISIS_ESTUDIO.configurarContadorCaracteres();
  },

  /**
   * Configura el contador de caracteres para texto_resultado_calculado
   */
  configurarContadorCaracteres: function () {
    const input = $("#texto_resultado_calculado");
    const contador = $("#contador-caracteres");

    input.on("input", function () {
      const length = $(this).val().length;
      contador.text(length + "/20");

      // Cambiar color si se acerca al límite
      if (length >= 18) {
        contador.addClass("text-danger").removeClass("text-warning");
      } else if (length >= 15) {
        contador.addClass("text-warning").removeClass("text-danger");
      } else {
        contador.removeClass("text-danger text-warning");
      }
    });
  },

  /**
   * Carga todas las fórmulas disponibles
   */
  cargarTodasFormulas: function () {
    const q = { op: "formulasget" };
    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (data.output.valid) {
          ANALISIS_ESTUDIO.todasLasFormulas = data.output.response;
          console.log("Fórmulas cargadas:", ANALISIS_ESTUDIO.todasLasFormulas.length);
        } else {
          console.error("Error al cargar fórmulas:", data);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error AJAX al cargar fórmulas:", error);
      },
    });
  },

  /**
   * Evento cuando se selecciona una grilla
   */
  onGrillaChange: function () {
    const grillaId = $("#tbl_grilla_id").val();

    if (!grillaId) {
      $("#seccion-candidatos").hide();
      $("#seccion-tipo-indicador").hide();
      $("#seccion-formulas").hide();
      $("#seccion-analisis").hide();
      $("#hr-indicadores, #hr-formulas, #hr-analisis").hide();
      // Cargar historial sin filtro de grilla
      ANALISIS_ESTUDIO.cargarHistorial();
      return;
    }

    ANALISIS_ESTUDIO.grillaSeleccionada = grillaId;
    ANALISIS_ESTUDIO.cargarCandidatos(grillaId);
    // Cargar historial filtrado por la grilla seleccionada
    ANALISIS_ESTUDIO.cargarHistorial(grillaId);
  },

  /**
   * Carga los candidatos de una grilla
   */
  cargarCandidatos: function (grillaId) {
    UTIL.cursorBusy();
    const q = { op: "analisisestudiogetcandidatos", tbl_grilla_id: grillaId };

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid && data.output.response.length > 0) {
          ANALISIS_ESTUDIO.mostrarCandidatos(data.output.response);
          $("#seccion-candidatos").show();
        } else {
          $("#candidatos-container").html(
            '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>No hay candidatos registrados en esta grilla</div>'
          );
          $("#seccion-candidatos").show();
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error al cargar candidatos");
      },
    });
  },

  /**
   * Muestra los candidatos en tarjetas
   */
  mostrarCandidatos: function (candidatos) {
    let html = '<div class="row g-3">';

    candidatos.forEach(function (candidato) {
      const fotoUrl = candidato.foto ? "assets/img/admin/" + candidato.foto : "assets/img/default-avatar.png";
      const fotoParam = candidato.foto ? `'${candidato.foto}'` : "null";

      html += `
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 candidato-card" style="cursor: pointer;"
               onclick="ANALISIS_ESTUDIO.seleccionarCandidato(${candidato.id}, '${candidato.nombre_completo}', ${fotoParam})">
            <div class="card-body text-center">
              <img src="${fotoUrl}" alt="${candidato.nombre_completo}"
                   class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
              <h6 class="card-title mb-1">${candidato.nombre_completo}</h6>
              ${candidato.cargo_nombre ? `<small class="text-muted">${candidato.cargo_nombre}</small>` : ""}
            </div>
          </div>
        </div>
      `;
    });

    html += "</div>";
    $("#candidatos-container").html(html);
  },

  /**
   * Selecciona un candidato
   */
  seleccionarCandidato: function (candidatoId, nombreCandidato, foto) {
    ANALISIS_ESTUDIO.candidatoSeleccionado = {
      id: candidatoId,
      nombre: nombreCandidato,
      foto: foto || null
    };

    // Resetear fórmula seleccionada al cambiar de candidato
    ANALISIS_ESTUDIO.formulaSeleccionada = null;

    // Resaltar candidato seleccionado
    $(".candidato-card").removeClass("border-primary bg-light");
    $(event.currentTarget).addClass("border-primary bg-light");

    // Resetear tipo de indicador y secciones posteriores
    $("#tipo_indicador").val("");
    $("#formulas-container").html(
      '<tr><td colspan="4" class="text-center text-muted">Seleccione un tipo de indicador para ver las fórmulas disponibles</td></tr>'
    );
    $(".formula-row").removeClass("table-primary");

    // Ocultar secciones de fórmulas y análisis
    $("#seccion-formulas").hide();
    $("#seccion-analisis").hide();
    $("#hr-formulas, #hr-analisis").hide();

    // Mostrar siguiente sección
    $("#seccion-tipo-indicador").show();
    $("#hr-indicadores").show();
    $("#info-candidato").text(nombreCandidato);

    // Actualizar foto del candidato en el banner
    if (foto && foto !== 'null' && foto !== 'undefined') {
      const fotoUrl = "assets/img/admin/" + foto;
      $("#foto-candidato-banner").attr("src", fotoUrl);
      $("#foto-candidato-banner").show();
      $("#icono-candidato-default").hide();
    } else {
      // No hay foto, mostrar ícono
      $("#foto-candidato-banner").hide();
      $("#icono-candidato-default").show();
    }

    $("#tbl_participante_id").val(candidatoId);
  },

  /**
   * Evento cuando se selecciona tipo de indicador
   */
  onTipoIndicadorChange: function () {
    const tipoIndicador = $("#tipo_indicador").val();
    console.log("Tipo de indicador seleccionado:", tipoIndicador);

    // Resetear fórmula seleccionada al cambiar tipo de indicador
    ANALISIS_ESTUDIO.formulaSeleccionada = null;
    $(".formula-row").removeClass("table-primary");

    // Ocultar sección de análisis al cambiar tipo
    $("#seccion-analisis").hide();
    $("#hr-analisis").hide();

    if (!tipoIndicador) {
      $("#seccion-formulas").hide();
      $("#hr-formulas").hide();
      return;
    }

    ANALISIS_ESTUDIO.filtrarFormulas(tipoIndicador);
    $("#seccion-formulas").show();
    $("#hr-formulas").show();
  },

  /**
   * Filtra las fórmulas por tipo de indicador
   */
  filtrarFormulas: function (tipoIndicador) {
    console.log("Filtrando por tipo:", tipoIndicador);
    console.log("Total fórmulas disponibles:", ANALISIS_ESTUDIO.todasLasFormulas.length);

    if (ANALISIS_ESTUDIO.todasLasFormulas.length === 0) {
      $("#formulas-container").html(
        '<tr><td colspan="4" class="text-center text-info"><i class="fas fa-spinner fa-spin me-2"></i>Cargando fórmulas...</td></tr>'
      );
      // Reintentar después de 1 segundo
      setTimeout(function () {
        if (ANALISIS_ESTUDIO.todasLasFormulas.length > 0) {
          ANALISIS_ESTUDIO.filtrarFormulas(tipoIndicador);
        } else {
          $("#formulas-container").html(
            '<tr><td colspan="4" class="text-center text-danger">Error al cargar las fórmulas. Recargue la página.</td></tr>'
          );
        }
      }, 1000);
      return;
    }

    const formulasFiltradas = ANALISIS_ESTUDIO.todasLasFormulas.filter(
      (f) => f.tipo_indicador === tipoIndicador
    );

    console.log("Fórmulas filtradas:", formulasFiltradas.length);

    if (formulasFiltradas.length === 0) {
      $("#formulas-container").html(
        '<tr><td colspan="4" class="text-center text-warning">No hay fórmulas disponibles para este tipo de indicador</td></tr>'
      );
      return;
    }

    let html = "";
    formulasFiltradas.forEach(function (formula) {
      html += `
        <tr class="formula-row" style="cursor: pointer;"
            data-formula-id="${formula.id}"
            data-indicador="${formula.indicador.replace(/"/g, '&quot;')}"
            data-sigla="${formula.sigla.replace(/"/g, '&quot;')}"
            data-formula="${formula.formula.replace(/"/g, '&quot;')}"
            onclick="ANALISIS_ESTUDIO.seleccionarFormulaByElement(this)">
          <td>
            <button type="button" class="btn btn-sm btn-info" title="Seleccionar">
              <i class="fas fa-check-circle"></i>
            </button>
          </td>
          <td><span class="badge bg-primary">${formula.sigla}</span></td>
          <td>${formula.indicador}</td>
          <td><code class="small">${formula.formula.substring(0, 60)}${formula.formula.length > 60 ? "..." : ""}</code></td>
        </tr>
      `;
    });

    $("#formulas-container").html(html);
  },

  /**
   * Selecciona una fórmula desde el elemento TR
   */
  seleccionarFormulaByElement: function (element) {
    const $row = $(element);
    const formulaId = $row.attr("data-formula-id");
    const indicador = $row.attr("data-indicador");
    const sigla = $row.attr("data-sigla");
    const formula = $row.attr("data-formula");

    console.log("Fórmula seleccionada:", formulaId, sigla);

    ANALISIS_ESTUDIO.formulaSeleccionada = {
      id: formulaId,
      nombre: indicador,
      sigla: sigla,
      formula: formula,
    };

    // Resaltar fórmula seleccionada
    $(".formula-row").removeClass("table-primary");
    $row.addClass("table-primary");

    // Actualizar información
    $("#info-indicador").html(`<strong>${sigla}</strong> - ${indicador}`);
    $("#info-formula").html(`<code>${formula}</code>`);
    $("#tbl_formula_id").val(formulaId);

    // Cargar resultados de votación
    ANALISIS_ESTUDIO.cargarResultadosVotacion();

    // Mostrar formulario de análisis
    $("#seccion-analisis").show();
    $("#hr-analisis").show();
  },

  /**
   * Selecciona una fórmula (método legacy)
   */
  seleccionarFormula: function (formulaId, nombreIndicador, sigla, formula) {
    console.log("Fórmula seleccionada (legacy):", formulaId, sigla);

    ANALISIS_ESTUDIO.formulaSeleccionada = {
      id: formulaId,
      nombre: nombreIndicador,
      sigla: sigla,
      formula: formula,
    };

    // Resaltar fórmula seleccionada
    $(".formula-row").removeClass("table-primary");

    // Actualizar información
    $("#info-indicador").html(`<strong>${sigla}</strong> - ${nombreIndicador}`);
    $("#info-formula").html(`<code>${formula}</code>`);
    $("#tbl_formula_id").val(formulaId);

    // Cargar resultados de votación
    ANALISIS_ESTUDIO.cargarResultadosVotacion();

    // Mostrar formulario de análisis
    $("#seccion-analisis").show();
    $("#hr-analisis").show();
  },

  /**
   * Carga los resultados de votación del candidato en la grilla
   */
  cargarResultadosVotacion: function () {
    if (!ANALISIS_ESTUDIO.grillaSeleccionada || !ANALISIS_ESTUDIO.candidatoSeleccionado) {
      return;
    }

    const q = {
      op: "analisisestudiogetresultados",
      tbl_grilla_id: ANALISIS_ESTUDIO.grillaSeleccionada,
      tbl_participante_id: ANALISIS_ESTUDIO.candidatoSeleccionado.id,
    };

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (data.output.valid && data.output.response) {
          const r = data.output.response;
          $("#resultado-conoce-si").text(r.conoce_si || 0);
          $("#resultado-conoce-no").text(r.conoce_no || 0);
          $("#resultado-imagen-favorable").text(r.imagen_favorable || 0);
          $("#resultado-imagen-desfavorable").text(r.imagen_desfavorable || 0);
          $("#resultado-votaria-si").text(r.votaria_si || 0);
          $("#resultado-votaria-no").text(r.votaria_no || 0);

          // Cargar demografía automáticamente
          ANALISIS_ESTUDIO.cargarDemografia(
            ANALISIS_ESTUDIO.grillaSeleccionada,
            ANALISIS_ESTUDIO.candidatoSeleccionado.id,
            'positivas'
          );

          // Buscar si ya existe un análisis guardado para esta combinación
          ANALISIS_ESTUDIO.buscarYCargarAnalisisExistente();
        }
      },
    });
  },

  /**
   * Buscar análisis existente y cargar sus datos (resultado, observaciones, operaciones)
   */
  buscarYCargarAnalisisExistente: function() {
    if (!ANALISIS_ESTUDIO.grillaSeleccionada ||
        !ANALISIS_ESTUDIO.candidatoSeleccionado ||
        !ANALISIS_ESTUDIO.formulaSeleccionada) {
      return;
    }

    const q = {
      op: "analisisestudiobuscarexistente",
      tbl_grilla_id: ANALISIS_ESTUDIO.grillaSeleccionada,
      tbl_participante_id: ANALISIS_ESTUDIO.candidatoSeleccionado.id,
      tbl_formula_id: ANALISIS_ESTUDIO.formulaSeleccionada.id
    };

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (data.output.valid && data.output.response) {
          const analisis = data.output.response;

          // Cargar ID del análisis (para actualizar en lugar de crear nuevo)
          $("#id").val(analisis.id);

          // Cargar resultado calculado
          if (analisis.resultado_calculado) {
            $("#resultado_calculado").val(analisis.resultado_calculado);
          }

          // Cargar texto resultado calculado
          if (analisis.texto_resultado_calculado) {
            $("#texto_resultado_calculado").val(analisis.texto_resultado_calculado);
            // Actualizar contador
            $("#contador-caracteres").text(analisis.texto_resultado_calculado.length + "/20");
          }

          // Cargar observaciones
          if (analisis.observaciones) {
            $("#observaciones").val(analisis.observaciones);
          }

          // Cargar operaciones de la calculadora
          if (analisis.calculos && analisis.calculos.length > 0) {
            ANALISIS_ESTUDIO.cargarOperaciones(analisis.calculos);
          }
        } else {
          // No existe análisis previo, limpiar campos
          $("#id").val("0");
          $("#resultado_calculado").val("");
          $("#observaciones").val("");
          ANALISIS_ESTUDIO.limpiarCalculadora();
        }
      },
      error: function() {
        // En caso de error, dejar campos limpios
        $("#id").val("0");
      }
    });
  },

  /**
   * Ver detalles de una fórmula
   */
  verDetalleFormula: function (formulaId) {
    const formula = ANALISIS_ESTUDIO.todasLasFormulas.find((f) => f.id == formulaId);
    if (!formula) return;

    let detalleHtml = `
      <h6><strong>Sigla:</strong> ${formula.sigla}</h6>
      <h6><strong>Indicador:</strong> ${formula.indicador}</h6>
      <h6><strong>Tipo:</strong> ${formula.tipo_indicador}</h6>
      <hr>
      <h6>Fórmula:</h6>
      <code class="d-block p-2 bg-light">${formula.formula}</code>
      <hr>
      <h6>Explicación:</h6>
      <p>${formula.explicacion || "No disponible"}</p>
      ${formula.observaciones ? `<h6>Observaciones:</h6><p>${formula.observaciones}</p>` : ""}
    `;

    Swal.fire({
      title: "Detalle de la Fórmula",
      html: detalleHtml,
      icon: "info",
      width: "600px",
      confirmButtonText: "Cerrar",
    });
  },

  /**
   * Guarda el análisis
   */
  guardarAnalisis: function () {
    const resultado = $("#resultado_calculado").val().trim();
    const textoResultado = $("#texto_resultado_calculado").val().trim();

    if (!resultado) {
      UTIL.mostrarMensajeError("Debe ingresar el resultado calculado");
      return;
    }

    // Validar longitud del texto descriptivo
    if (textoResultado.length > 20) {
      UTIL.mostrarMensajeError("El texto descriptivo no puede exceder 20 caracteres");
      return;
    }

    if (!ANALISIS_ESTUDIO.grillaSeleccionada) {
      UTIL.mostrarMensajeError("Debe seleccionar una grilla");
      return;
    }

    if (!ANALISIS_ESTUDIO.candidatoSeleccionado) {
      UTIL.mostrarMensajeError("Debe seleccionar un candidato");
      return;
    }

    if (!ANALISIS_ESTUDIO.formulaSeleccionada) {
      UTIL.mostrarMensajeError("Debe seleccionar una fórmula");
      return;
    }

    const q = {
      op: "analisisestudiosave",
      id: $("#id").val(),
      tbl_grilla_id: ANALISIS_ESTUDIO.grillaSeleccionada,
      tbl_participante_id: ANALISIS_ESTUDIO.candidatoSeleccionado.id,
      tbl_formula_id: ANALISIS_ESTUDIO.formulaSeleccionada.id,
      resultado_calculado: resultado,
      texto_resultado_calculado: textoResultado,
      observaciones: $("#observaciones").val().trim(),
      calculos: JSON.stringify(ANALISIS_ESTUDIO.obtenerDatosCalculadora()),
    };

    UTIL.cursorBusy();
    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
          const action = data.output.action || 'saved';
          const mensaje = action === 'updated'
            ? "Análisis actualizado correctamente"
            : "Análisis guardado correctamente";
          UTIL.mostrarMensajeExitoso(mensaje);

          // Recargar historial con el filtro de grilla actual
          ANALISIS_ESTUDIO.cargarHistorial(ANALISIS_ESTUDIO.grillaSeleccionada);

          // Limpiar formulario (la demografía ya está visible desde la selección de fórmula)
          ANALISIS_ESTUDIO.limpiarFormulario();
        } else {
          UTIL.mostrarMensajeError(data.output.response || "Error al guardar");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error al guardar el análisis");
      },
    });
  },

  /**
   * Limpia el formulario
   */
  limpiarFormulario: function () {
    $("#formAnalisis")[0].reset();
    $("#id").val("0");
    ANALISIS_ESTUDIO.candidatoSeleccionado = null;
    ANALISIS_ESTUDIO.formulaSeleccionada = null;

    // Resetear explícitamente el select de tipo de indicador
    $("#tipo_indicador").val("").trigger("change");

    // Limpiar contenedor de fórmulas
    $("#formulas-container").html(
      '<tr><td colspan="4" class="text-center text-muted">Seleccione un tipo de indicador para ver las fórmulas disponibles</td></tr>'
    );

    $("#seccion-tipo-indicador").hide();
    $("#seccion-formulas").hide();
    $("#seccion-analisis").hide();
    $("#seccion-demografia").hide();
    $("#hr-indicadores, #hr-formulas, #hr-analisis").hide();

    $(".candidato-card").removeClass("border-primary bg-light");
    $(".formula-row").removeClass("table-primary");

    $("#resultado-conoce-si, #resultado-conoce-no").text("0");
    $("#resultado-imagen-favorable, #resultado-imagen-desfavorable").text("0");
    $("#resultado-votaria-si, #resultado-votaria-no").text("0");

    // Limpiar contador de caracteres
    $("#contador-caracteres").text("0/20").removeClass("text-danger text-warning");

    // Resetear foto del candidato
    $("#foto-candidato-banner").hide();
    $("#icono-candidato-default").show();

    // Limpiar calculadora de operaciones
    ANALISIS_ESTUDIO.limpiarCalculadora();
  },

  /**
   * Carga el historial de análisis
   * @param {number} grillaId - ID de la grilla para filtrar (opcional)
   */
  cargarHistorial: function (grillaId) {
    const q = { op: "analisisestudioget" };

    // Si se proporciona ID de grilla, filtrar por ella
    if (grillaId) {
      q.tbl_grilla_id = grillaId;
    }

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        if (data.output.valid && data.output.response.length > 0) {
          ANALISIS_ESTUDIO.mostrarHistorial(data.output.response);
        } else {
          const mensaje = grillaId
            ? "No hay análisis registrados para esta grilla"
            : "No hay análisis registrados";
          $("#historial-container").html(
            `<tr><td colspan="6" class="text-center text-muted">${mensaje}</td></tr>`
          );
        }
      },
      error: function () {
        $("#historial-container").html(
          '<tr><td colspan="6" class="text-center text-danger">Error al cargar el historial</td></tr>'
        );
      },
    });
  },

  /**
   * Muestra el historial de análisis en la tabla
   */
  mostrarHistorial: function (analisis) {
    let html = "";

    analisis.forEach(function (item) {
      const fecha = new Date(item.dtcreate).toLocaleString("es-CO");

      html += `
        <tr>
          <td>
            <button type="button" class="btn btn-sm btn-primary" title="Editar" onclick="ANALISIS_ESTUDIO.editarAnalisis(${item.id})">
              <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger" title="Eliminar" onclick="ANALISIS_ESTUDIO.eliminarAnalisis(${item.id})">
              <i class="fas fa-trash"></i>
            </button>
          </td>
          <td>${item.nombre_grilla}</td>
          <td>${item.nombre_candidato}</td>
          <td><span class="badge bg-info">${item.sigla_indicador}</span> ${item.nombre_indicador}</td>
          <td><strong>${item.resultado_calculado}</strong></td>
          <td><small>${fecha}</small></td>
        </tr>
      `;
    });

    $("#historial-container").html(html);
  },

  /**
   * Editar un análisis existente
   */
  editarAnalisis: function (id) {
    UTIL.cursorBusy();

    const q = { op: "analisisestudiogetbyid", id: id };

    $.ajax({
      data: q,
      type: "POST",
      dataType: "json",
      url: "admin/ajax/rqst.php",
      success: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid && data.output.response) {
          const analisis = data.output.response;
          ANALISIS_ESTUDIO.cargarAnalisisParaEdicion(analisis);
        } else {
          UTIL.mostrarMensajeError("Error al cargar el análisis");
        }
      },
      error: function () {
        UTIL.cursorNormal();
        UTIL.mostrarMensajeError("Error al cargar el análisis");
      }
    });
  },

  /**
   * Cargar todos los datos del análisis en el formulario para edición
   */
  cargarAnalisisParaEdicion: function(analisis) {
    // 1. Seleccionar grilla
    $("#tbl_grilla_id").val(analisis.tbl_grilla_id).trigger('change');
    ANALISIS_ESTUDIO.grillaSeleccionada = analisis.tbl_grilla_id;

    // 2. Esperar a que carguen los candidatos y luego seleccionar
    setTimeout(() => {
      // Cargar candidatos
      ANALISIS_ESTUDIO.cargarCandidatos(analisis.tbl_grilla_id);

      // 3. Esperar y seleccionar candidato
      setTimeout(() => {
        ANALISIS_ESTUDIO.candidatoSeleccionado = {
          id: analisis.tbl_participante_id,
          nombre: analisis.nombre_candidato,
          foto: analisis.foto_candidato || null
        };

        // Resaltar card del candidato
        $(`.candidato-card`).removeClass("border-primary bg-light");
        $(`.candidato-card`).each(function() {
          const cardHtml = $(this).html();
          if (cardHtml.includes(analisis.nombre_candidato)) {
            $(this).addClass("border-primary bg-light");
          }
        });

        // 4. Mostrar sección de tipo de indicador
        $("#seccion-tipo-indicador").show();
        $("#hr-indicadores").show();
        $("#info-candidato").text(analisis.nombre_candidato);
        $("#tbl_participante_id").val(analisis.tbl_participante_id);

        // 4.1. Actualizar foto del candidato en el banner
        if (analisis.foto_candidato && analisis.foto_candidato !== 'null' && analisis.foto_candidato !== 'undefined') {
          const fotoUrl = "assets/img/admin/" + analisis.foto_candidato;
          $("#foto-candidato-banner").attr("src", fotoUrl);
          $("#foto-candidato-banner").show();
          $("#icono-candidato-default").hide();
        } else {
          // No hay foto, mostrar ícono
          $("#foto-candidato-banner").hide();
          $("#icono-candidato-default").show();
        }

        // 5. Seleccionar tipo de indicador
        $("#tipo_indicador").val(analisis.tipo_indicador).trigger('change');

        // 6. Esperar y seleccionar fórmula
        setTimeout(() => {
          ANALISIS_ESTUDIO.formulaSeleccionada = {
            id: analisis.tbl_formula_id,
            nombre: analisis.nombre_indicador,
            sigla: analisis.sigla_indicador,
            formula: analisis.formula
          };

          // Mostrar sección de fórmulas
          $("#seccion-formulas").show();
          $("#hr-formulas").show();

          // Resaltar fórmula seleccionada
          $(`.formula-row`).removeClass("table-primary");
          $(`.formula-row[data-formula-id="${analisis.tbl_formula_id}"]`).addClass("table-primary");

          // 7. Actualizar información de indicador
          $("#info-indicador").html(`<strong>${analisis.sigla_indicador}</strong> - ${analisis.nombre_indicador}`);
          $("#info-formula").html(`<code>${analisis.formula}</code>`);
          $("#tbl_formula_id").val(analisis.tbl_formula_id);

          // 8. Cargar resultados de votación
          ANALISIS_ESTUDIO.cargarResultadosVotacion();

          // 9. Mostrar sección de análisis
          $("#seccion-analisis").show();
          $("#hr-analisis").show();

          // 10. Cargar ID del análisis
          $("#id").val(analisis.id);

          // 11. Cargar resultado calculado
          $("#resultado_calculado").val(analisis.resultado_calculado);

          // 12. Cargar observaciones
          if (analisis.observaciones) {
            $("#observaciones").val(analisis.observaciones);
          }

          // 13. Cargar operaciones de la calculadora
          if (analisis.calculos && analisis.calculos.length > 0) {
            ANALISIS_ESTUDIO.cargarOperaciones(analisis.calculos);
          }

          // 14. Scroll suave hacia la sección de análisis
          setTimeout(() => {
            $('html, body').animate({
              scrollTop: $("#seccion-analisis").offset().top - 100
            }, 800);
          }, 500);

        }, 800);
      }, 600);
    }, 400);
  },

  /**
   * Eliminar un análisis
   */
  eliminarAnalisis: function (id) {
    Swal.fire({
      title: "¿Está seguro?",
      text: "Esta acción eliminará el análisis permanentemente",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        const q = { op: "analisisestudiodelete", id: id };

        $.ajax({
          data: q,
          type: "POST",
          dataType: "json",
          url: "admin/ajax/rqst.php",
          success: function (data) {
            if (data.output.valid) {
              UTIL.mostrarMensajeExitoso("Análisis eliminado correctamente");
              // Recargar historial con el filtro de grilla actual
              ANALISIS_ESTUDIO.cargarHistorial(ANALISIS_ESTUDIO.grillaSeleccionada);
            } else {
              UTIL.mostrarMensajeError("Error al eliminar");
            }
          },
        });
      }
    });
  },

  /**
   * Ver resultados completos de la grilla
   */
  verResultadosGrilla: function () {
    const grillaId = $("#tbl_grilla_id").val();
    if (!grillaId) {
      UTIL.mostrarMensajeError("Debe seleccionar una grilla primero");
      return;
    }

    // Redirigir a la página de grilla
    window.open(`grilla.php`, "_blank");
  },

  /**
   * Cargar demografía de votantes para un candidato
   */
  cargarDemografia: function(grillaId, candidatoId, tipoRespuesta = 'positivas') {
    const q = {
      op: 'grillacandidatodemografia',
      grilla_id: grillaId,
      candidato_id: candidatoId,
      tipo_respuesta: tipoRespuesta
    };

    UTIL.callAjaxRqstPOST(q, (response) => {
      if (response && response.output && response.output.valid) {
        const demografia = response.output.response;
        ANALISIS_ESTUDIO.renderizarDemografia(demografia);

        // Mostrar la sección de demografía con efecto suave
        $("#seccion-demografia").slideDown(300);
      } else {
        console.error('Error al cargar demografía:', response);
        // Ocultar sección si hay error
        $("#seccion-demografia").slideUp(200);
      }
    });
  },

  /**
   * Renderizar visualización de demografía
   */
  renderizarDemografia: function(demografia) {
    UTIL.cursorNormal();
    const container = $("#demografia-container");
    container.empty();

    if (demografia.total_votantes === 0) {
      container.html(`
        <div class="col-12 text-center text-muted py-2">
          <small><i class="fas fa-info-circle me-1"></i>No hay datos demográficos disponibles</small>
        </div>
      `);
      return;
    }

    // Mostrar total de votantes (compacto)
    container.append(`
      <div class="col-12 mb-2">
        <div class="alert alert-info py-1 px-2 mb-0 d-flex align-items-center small">
          <i class="fas fa-users me-2"></i>
          <strong>Total de Votantes en la Grilla: ${demografia.total_votantes}</strong>
          <small class="text-muted ms-2">(perfil demográfico de todos los participantes)</small>
        </div>
      </div>
    `);

    // Mapeo de etiquetas amigables
    const labelsMap = {
      'ideologia': {
        title: 'Ideología Política',
        icon: 'fa-balance-scale',
        values: {
          'izquierda': 'Izquierda',
          'centro_izquierda': 'Centro-Izquierda',
          'centro': 'Centro',
          'centro_derecha': 'Centro-Derecha',
          'derecha': 'Derecha',
          'independiente': 'Independiente',
          'sin_definir': 'Sin Definir'
        }
      },
      'genero': {
        title: 'Género',
        icon: 'fa-venus-mars',
        values: {
          'masculino': 'Masculino',
          'femenino': 'Femenino',
          'otro': 'Otro'
        }
      },
      'rango_edad': {
        title: 'Rango de Edad',
        icon: 'fa-birthday-cake',
        values: {
          '18-25': '18-25 años',
          '26-35': '26-35 años',
          '36-45': '36-45 años',
          '46-55': '46-55 años',
          '56-65': '56-65 años',
          '66+': '66+ años'
        }
      },
      'nivel_ingresos': {
        title: 'Nivel de Ingresos',
        icon: 'fa-dollar-sign',
        values: {
          'menos_1_salario': 'Menos de 1 salario',
          '1-2_salarios': '1-2 salarios',
          '3-5_salarios': '3-5 salarios',
          '6-10_salarios': '6-10 salarios',
          'mas_10_salarios': 'Más de 10 salarios'
        }
      },
      'nivel_educacion': {
        title: 'Nivel de Educación',
        icon: 'fa-graduation-cap',
        values: {
          'primaria_incompleta': 'Primaria Incompleta',
          'primaria_completa': 'Primaria Completa',
          'secundaria_incompleta': 'Secundaria Incompleta',
          'secundaria_completa': 'Secundaria Completa',
          'tecnico': 'Técnico',
          'tecnologo': 'Tecnólogo',
          'universitario_incompleto': 'Universitario Incompleto',
          'universitario_completo': 'Universitario Completo',
          'posgrado': 'Posgrado'
        }
      }
    };

    // Renderizar cada categoría demográfica (mostrando TODOS los datos)
    ['ideologia', 'genero', 'rango_edad', 'nivel_ingresos', 'nivel_educacion'].forEach(categoria => {
      const datos = demografia[categoria];
      if (!datos || Object.keys(datos).length === 0) return;

      const config = labelsMap[categoria];
      const colSize =  'col-md-3';

      let html = `
        <div class="${colSize}">
          <div class="border rounded p-2 bg-white">
            <div class="mb-1">
              <small class="text-primary fw-bold">
                <i class="fas ${config.icon} me-1"></i>${config.title}
              </small>
            </div>
      `;

      // Convertir a array y ordenar por porcentaje descendente
      const datosArray = Object.entries(datos).map(([valor, data]) => ({
        valor,
        ...data
      })).sort((a, b) => b.porcentaje - a.porcentaje);

      // Mostrar TODOS los datos (no solo top 3)
      datosArray.forEach(item => {
        const label = config.values[item.valor] || item.valor;
        const porcentaje = item.porcentaje || 0;
        const cantidad = item.cantidad || 0;

        // Color de la barra según el porcentaje
        let colorClass = 'bg-secondary';
        if (porcentaje >= 40) colorClass = 'bg-success';
        else if (porcentaje >= 25) colorClass = 'bg-primary';
        else if (porcentaje >= 15) colorClass = 'bg-info';

        html += `
          <div class="mb-1">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-dark" style="font-size: 0.75rem;">${label}</small>
              <small class="text-muted" style="font-size: 0.7rem;">${cantidad} (${porcentaje.toFixed(0)}%)</small>
            </div>
            <div class="progress" style="height: 5px;">
              <div class="progress-bar ${colorClass}" role="progressbar"
                   style="width: ${porcentaje}%"
                   aria-valuenow="${porcentaje}"
                   aria-valuemin="0"
                   aria-valuemax="100">
              </div>
            </div>
          </div>
        `;
      });

      html += `
          </div>
        </div>
      `;

      container.append(html);
    });
  },

  /**
   * CALCULADORA DE OPERACIONES
   */

  /**
   * Agregar una nueva operación a la calculadora
   */
  agregarOperacion: function() {
    const operacionId = ++ANALISIS_ESTUDIO.contadorOperaciones;
    const operacion = {
      id: operacionId,
      descripcion: '',
      valor1: 0,
      operador: '+',
      valor2: 0,
      resultado: 0
    };

    ANALISIS_ESTUDIO.operaciones.push(operacion);
    ANALISIS_ESTUDIO.renderizarOperacion(operacion);
    ANALISIS_ESTUDIO.actualizarResumen();
  },

  /**
   * Renderizar una operación en el DOM
   */
  renderizarOperacion: function(operacion) {
    const html = `
      <div class="operacion-item border rounded p-2 mb-2 bg-white" data-operacion-id="${operacion.id}">
        <div class="row g-2 align-items-center">
          <!-- Descripción -->
          <div class="col-12 col-md-3">
            <input type="text" class="form-control form-control-sm"
                   placeholder="Descripción (ej: Total votos)"
                   value="${operacion.descripcion}"
                   onchange="ANALISIS_ESTUDIO.actualizarOperacion(${operacion.id}, 'descripcion', this.value)">
          </div>

          <!-- Valor 1 -->
          <div class="col-4 col-md-2">
            <input type="number" step="0.0001" class="form-control form-control-sm"
                   placeholder="Valor 1"
                   value="${operacion.valor1}"
                   oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
                   onchange="ANALISIS_ESTUDIO.actualizarOperacion(${operacion.id}, 'valor1', parseFloat(this.value) || 0)">
          </div>

          <!-- Operador -->
          <div class="col-2 col-md-1">
            <select class="form-select form-select-sm"
                    onchange="ANALISIS_ESTUDIO.actualizarOperacion(${operacion.id}, 'operador', this.value)">
              <option value="+" ${operacion.operador === '+' ? 'selected' : ''}>+</option>
              <option value="-" ${operacion.operador === '-' ? 'selected' : ''}>-</option>
              <option value="*" ${operacion.operador === '*' ? 'selected' : ''}>×</option>
              <option value="/" ${operacion.operador === '/' ? 'selected' : ''}>÷</option>
              <option value="%" ${operacion.operador === '%' ? 'selected' : ''}>%</option>
            </select>
          </div>

          <!-- Valor 2 -->
          <div class="col-4 col-md-2">
            <input type="number" step="0.0001" class="form-control form-control-sm"
                   placeholder="Valor 2"
                   value="${operacion.valor2}"
                   oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
                   onchange="ANALISIS_ESTUDIO.actualizarOperacion(${operacion.id}, 'valor2', parseFloat(this.value) || 0)">
          </div>

          <!-- Resultado -->
          <div class="col-10 col-md-2">
            <div class="input-group input-group-sm">
              <span class="input-group-text">=</span>
              <input type="text" class="form-control form-control-sm bg-light"
                     id="resultado-${operacion.id}"
                     value="${operacion.resultado.toFixed(4)}"
                     readonly>
            </div>
          </div>

          <!-- Botón eliminar -->
          <div class="col-2 col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-danger"
                    onclick="ANALISIS_ESTUDIO.eliminarOperacion(${operacion.id})"
                    title="Eliminar">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    `;

    $('#operaciones-container').append(html);
  },

  /**
   * Actualizar una operación y recalcular resultado
   */
  actualizarOperacion: function(operacionId, campo, valor) {
    const operacion = ANALISIS_ESTUDIO.operaciones.find(op => op.id === operacionId);
    if (!operacion) return;

    operacion[campo] = valor;

    // Recalcular resultado si es un campo numérico u operador
    if (['valor1', 'valor2', 'operador'].includes(campo)) {
      operacion.resultado = ANALISIS_ESTUDIO.calcularResultado(
        operacion.valor1,
        operacion.operador,
        operacion.valor2
      );

      // Actualizar el campo de resultado en el DOM
      $(`#resultado-${operacionId}`).val(operacion.resultado.toFixed(4));
    }

    ANALISIS_ESTUDIO.actualizarResumen();
  },

  /**
   * Calcular el resultado de una operación
   */
  calcularResultado: function(valor1, operador, valor2) {
    const v1 = parseFloat(valor1) || 0;
    const v2 = parseFloat(valor2) || 0;

    switch(operador) {
      case '+': return v1 + v2;
      case '-': return v1 - v2;
      case '*': return v1 * v2;
      case '/': return v2 !== 0 ? v1 / v2 : 0;
      case '%': return v2 !== 0 ? v1 % v2 : 0;
      default: return 0;
    }
  },

  /**
   * Eliminar una operación
   */
  eliminarOperacion: function(operacionId) {
    // Eliminar del array
    ANALISIS_ESTUDIO.operaciones = ANALISIS_ESTUDIO.operaciones.filter(op => op.id !== operacionId);

    // Eliminar del DOM
    $(`.operacion-item[data-operacion-id="${operacionId}"]`).remove();

    ANALISIS_ESTUDIO.actualizarResumen();
  },

  /**
   * Actualizar el resumen de cálculos
   */
  actualizarResumen: function() {
    if (ANALISIS_ESTUDIO.operaciones.length === 0) {
      $('#resumen-calculos').hide();
      return;
    }

    let resumenHtml = '';
    ANALISIS_ESTUDIO.operaciones.forEach((op, index) => {
      const simboloOperador = op.operador === '*' ? '×' : op.operador === '/' ? '÷' : op.operador;
      const descripcion = op.descripcion ? `<strong>${op.descripcion}:</strong> ` : '';
      resumenHtml += `
        <div class="mb-1">
          ${index + 1}. ${descripcion}${op.valor1} ${simboloOperador} ${op.valor2} = <strong>${op.resultado.toFixed(4)}</strong>
        </div>
      `;
    });

    $('#resumen-operaciones').html(resumenHtml);
    $('#resumen-calculos').show();
  },

  /**
   * Limpiar todas las operaciones de la calculadora
   */
  limpiarCalculadora: function() {
    ANALISIS_ESTUDIO.operaciones = [];
    ANALISIS_ESTUDIO.contadorOperaciones = 0;
    $('#operaciones-container').empty();
    $('#resumen-calculos').hide();
  },

  /**
   * Obtener datos de operaciones para guardar
   */
  obtenerDatosCalculadora: function() {
    return ANALISIS_ESTUDIO.operaciones.map((op, index) => ({
      orden: index + 1,
      descripcion: op.descripcion || null,
      valor1: op.valor1,
      operador: op.operador,
      valor2: op.valor2,
      resultado: op.resultado
    }));
  },

  /**
   * Cargar operaciones existentes
   */
  cargarOperaciones: function(operaciones) {
    ANALISIS_ESTUDIO.limpiarCalculadora();

    if (!operaciones || operaciones.length === 0) return;

    operaciones.forEach(op => {
      const operacionId = ++ANALISIS_ESTUDIO.contadorOperaciones;
      const operacion = {
        id: operacionId,
        descripcion: op.descripcion || '',
        valor1: parseFloat(op.valor1) || 0,
        operador: op.operador,
        valor2: parseFloat(op.valor2) || 0,
        resultado: parseFloat(op.resultado) || 0
      };

      ANALISIS_ESTUDIO.operaciones.push(operacion);
      ANALISIS_ESTUDIO.renderizarOperacion(operacion);
    });

    ANALISIS_ESTUDIO.actualizarResumen();
  },
};

// Inicializar al cargar la página
$(document).on("ready", function () {
  ANALISIS_ESTUDIO.init();
});
