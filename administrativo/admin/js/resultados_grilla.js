/**
 * Sistema de Visualización de Resultados en Tiempo Real
 * Muestra estadísticas del estudio de votaciones con actualización automática
 */

const ResultadosGrilla = {
  // Datos de la grilla recibidos desde PHP
  grillaData: null,

  // Intervalo de actualización automática (en milisegundos)
  intervaloActualizacion: 10000, // 10 segundos

  // ID del timer para actualización automática
  timerId: null,

  /**
   * Inicializa el sistema
   * @param {Object} grillaData - Datos de la grilla desde PHP
   */
  init: function(grillaData) {
    this.grillaData = grillaData;
    console.log('Inicializando Sistema de Resultados en Tiempo Real');
    console.log('Datos de la grilla:', this.grillaData);

    this.bindEvents();
    this.cargarResultados();
    this.iniciarActualizacionAutomatica();
  },

  /**
   * Vincula eventos de la interfaz
   */
  bindEvents: function() {
    const self = this;

    // Botón de actualizar manual
    const btnActualizar = document.getElementById('btnActualizarResultados');
    if (btnActualizar) {
      btnActualizar.addEventListener('click', function() {
        self.cargarResultados(true); // true = manual
      });
    }
  },

  /**
   * Carga los resultados desde el servidor
   * @param {boolean} esManual - Indica si la actualización es manual
   */
  cargarResultados: function(esManual = false) {
    const self = this;

    // Mostrar indicador de carga en el botón
    const btnActualizar = document.getElementById('btnActualizarResultados');
    if (btnActualizar && esManual) {
      const iconoOriginal = btnActualizar.innerHTML;
      btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
      btnActualizar.disabled = true;
    }

    const datosEnvio = {
      op: 'grillacandidatoresultadosentiemporeal',
      grilla_id: this.grillaData.id
    };

    UTIL.callAjaxRqstPOST(datosEnvio, function(response) {
      
      console.log('Resultados recibidos:', response);
      UTIL.cursorNormal();

      if (response && response.output && response.output.valid) {
        const resultados = response.output.response;

        // Actualizar contador de votantes
        self.actualizarContadorVotantes(resultados.total_votantes);

        // Renderizar resultados
        self.renderizarTablaCandidatos(resultados.candidatos);
        self.renderizarPreguntasAdicionales(resultados.preguntas_adicionales);

        // Actualizar totales por pregunta
        self.actualizarTotalesPorPregunta(resultados.totales_por_pregunta, resultados.candidatos);

        // Actualizar timestamp
        self.actualizarTimestamp();

        // Restaurar botón
        if (btnActualizar && esManual) {
          btnActualizar.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';
          btnActualizar.disabled = false;
        }
      } else {
        console.error('Error al cargar resultados:', response);

        // Restaurar botón en caso de error
        if (btnActualizar && esManual) {
          btnActualizar.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';
          btnActualizar.disabled = false;
        }

        UTIL.mostrarMensajeError('Error al cargar los resultados. Por favor, intente nuevamente.');
      }
    });
  },

  /**
   * Renderiza la tabla de candidatos con estadísticas (100% DINÁMICO desde BD)
   * @param {Array} candidatos - Array de candidatos con estadísticas
   */
  renderizarTablaCandidatos: function(candidatos) {
    const tbody = document.getElementById('tbodyCandidatos');
    const thead = document.querySelector('#tablaCandidatos thead tr');

    if (!tbody) return;

    // Contar columnas desde el thead (para colspan dinámico)
    const totalColumnas = thead ? thead.querySelectorAll('th').length : 7;

    // Limpiar tbody
    tbody.innerHTML = '';

    if (!candidatos || candidatos.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="${totalColumnas}" class="text-center py-4">
            <i class="fas fa-info-circle fa-2x mb-2 text-muted"></i>
            <p class="mb-0 text-muted">No hay resultados disponibles aún</p>
          </td>
        </tr>
      `;
      return;
    }

    // Obtener lista de códigos de preguntas dinámicamente desde el thead
    const codigosPreguntas = [];
    if (thead) {
      const headers = thead.querySelectorAll('th');
      // Saltar #, Candidato, Total Votos (primeras 3) y Aprobaciones (última)
      for (let i = 3; i < headers.length - 1; i++) {
        // Intentar inferir código desde el texto del header
        const textoHeader = headers[i].textContent.trim().toLowerCase();

        if (textoHeader.includes('conoce')) {
          codigosPreguntas.push({codigo: 'conoce', campo_si: 'conoce_si', campo_pct: 'conoce_si_pct'});
        } else if (textoHeader.includes('imagen')) {
          codigosPreguntas.push({codigo: 'imagen', campo_si: 'imagen_favorable', campo_pct: 'imagen_favorable_pct'});
        } else if (textoHeader.includes('votaría') || textoHeader.includes('votaria')) {
          codigosPreguntas.push({codigo: 'votaria', campo_si: 'votaria_si', campo_pct: 'votaria_si_pct'});
        }
      }
    }

    // Función helper para formatear valores numéricos (maneja NULL/undefined)
    const formatNum = (val) => val !== null && val !== undefined ? val : 0;
    const formatPct = (val) => val !== null && val !== undefined ? val : '0.00';

    // Renderizar cada candidato
    candidatos.forEach((candidato, index) => {
      const fotoUrl = candidato.foto
        ? 'assets/img/admin/' + candidato.foto
        : 'assets/img/candidato.png';

      const row = document.createElement('tr');

      // Destacar el candidato con más aprobaciones
      if (index === 0 && candidato.total_aprobaciones > 0) {
        row.classList.add('table-success');
      }

      // Columnas fijas
      let html = `
        <td class="text-center"><strong>${index + 1}</strong></td>
        <td>
          <div class="d-flex align-items-center">
            <img src="${fotoUrl}" alt="${candidato.nombre_completo}"
                 class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
            <div>
              <strong style="font-size: 12px;">${candidato.nombre_completo}</strong>
            </div>
          </div>
        </td>
        <td class="text-center">
          <span class="badge badge-secondary">${formatNum(candidato.total_votos)}</span>
        </td>
      `;

      // Columnas dinámicas de preguntas
      codigosPreguntas.forEach(pregunta => {
        const valorSi = candidato[pregunta.campo_si];
        const valorPct = candidato[pregunta.campo_pct];

        html += `
        <td class="text-center">
          <strong>${formatNum(valorSi)}</strong>
          <small class="text-muted d-block">${formatPct(valorPct)}%</small>
        </td>
        `;
      });

      // Columna de aprobaciones (fija)
      html += `
        <td class="text-center">
          <span class="badge ${candidato.total_aprobaciones > 0 ? 'badge-success' : 'badge-light'}"
                style="font-size: 14px; color: #31374a; padding: 8px 12px;">
            <i class="fas fa-trophy" style="color: #FFD700;"></i> ${formatNum(candidato.total_aprobaciones)}
          </span>
        </td>
      `;

      row.innerHTML = html;
      tbody.appendChild(row);
    });
  },

  /**
   * Renderiza los resultados de preguntas adicionales (100% DINÁMICO)
   * @param {Object} preguntasAdicionales - Objeto con arrays pa, pb, pc (compatibilidad)
   */
  renderizarPreguntasAdicionales: function(preguntasAdicionales) {
    // Obtener todos los contenedores de subpreguntas dinámicamente
    const contenedores = document.querySelectorAll('[id^="resultados"]');

    contenedores.forEach(container => {
      const codigo = container.getAttribute('data-codigo');

      if (!codigo) {
        // Compatibilidad con IDs hardcodeados (resultadosPA, resultadosPB, resultadosPC)
        const containerId = container.id;
        let codigoLegacy = null;
        let etiqueta = '';

        if (containerId === 'resultadosPA') {
          codigoLegacy = 'pa';
          etiqueta = 'P(A)';
        } else if (containerId === 'resultadosPB') {
          codigoLegacy = 'pb';
          etiqueta = 'P(B)';
        } else if (containerId === 'resultadosPC') {
          codigoLegacy = 'pc';
          etiqueta = 'P(C)';
        }

        if (codigoLegacy && preguntasAdicionales[codigoLegacy]) {
          this.renderizarPregunta(containerId, preguntasAdicionales[codigoLegacy], etiqueta);
        }
      } else {
        // Nuevo sistema dinámico usando data-codigo
        const votos = preguntasAdicionales[codigo] || [];
        const etiqueta = codigo.toUpperCase();
        this.renderizarPregunta(container.id, votos, etiqueta);
      }
    });
  },

  /**
   * Renderiza una pregunta adicional individual
   * @param {string} containerId - ID del contenedor
   * @param {Array} votos - Array de votos
   * @param {string} etiqueta - Etiqueta de la pregunta
   */
  renderizarPregunta: function(containerId, votos, etiqueta) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Limpiar contenedor
    container.innerHTML = '';

    if (!votos || votos.length === 0) {
      container.innerHTML = `
        <p class="text-center text-muted py-2 mb-0">
          <small>No hay votos para ${etiqueta}</small>
        </p>
      `;
      return;
    }

    // Calcular total de votos
    const totalVotos = votos.reduce((sum, item) => sum + parseInt(item.votos), 0);

    // Renderizar cada candidato
    votos.forEach((item, index) => {
      const porcentaje = totalVotos > 0
        ? ((item.votos / totalVotos) * 100).toFixed(1)
        : 0;

      const itemDiv = document.createElement('div');
      itemDiv.className = 'resultado-item mb-2 p-2 border-bottom';
      itemDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-1">
          <div class="d-flex align-items-center">
            <span class="badge ${index === 0 ? 'badge-primary' : 'badge-secondary'} me-2"
                  style="font-size: 12px;">
              ${index + 1}°
            </span>
            <strong style="font-size: 12px;">${item.nombre}</strong>
          </div>
          <span class="badge badge-success">${item.votos} votos</span>
        </div>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar ${index === 0 ? 'bg-primary' : 'bg-secondary'}"
               role="progressbar"
               style="width: ${porcentaje}%;"
               aria-valuenow="${porcentaje}"
               aria-valuemin="0"
               aria-valuemax="100">
          </div>
        </div>
        <small class="text-muted">${porcentaje}%</small>
      `;

      container.appendChild(itemDiv);
    });
  },

  /**
   * Actualiza el contador de votantes en el header
   * @param {number} totalVotantes - Total de personas que han votado
   */
  actualizarContadorVotantes: function(totalVotantes) {
    const contadorElement = document.getElementById('totalVotantes');
    if (contadorElement) {
      contadorElement.textContent = totalVotantes || 0;
    }
  },

  /**
   * Actualiza los totales por pregunta en el tfoot de la tabla
   * @param {Object} totalesPorPregunta - Objeto con totales por código de pregunta
   * @param {Array} candidatos - Array de candidatos para calcular total de aprobaciones
   */
  actualizarTotalesPorPregunta: function(totalesPorPregunta, candidatos) {
    if (!totalesPorPregunta) return;

    console.log('Actualizando totales por pregunta:', totalesPorPregunta);

    // Actualizar cada columna de pregunta dinámicamente
    for (const codigo in totalesPorPregunta) {
      const totales = totalesPorPregunta[codigo];
      const elementId = 'total_' + codigo;
      const element = document.getElementById(elementId);

      if (element) {
        // Determinar qué valor mostrar según el tipo de pregunta
        let valorMostrar = '';
        let porcentaje = 0;

        if (totales.si > 0) {
          // Pregunta tipo "si/no"
          valorMostrar = totales.si;
          porcentaje = totales.si_pct;
        } else if (totales.favorable > 0) {
          // Pregunta tipo "favorable/desfavorable"
          valorMostrar = totales.favorable;
          porcentaje = totales.favorable_pct;
        } else {
          valorMostrar = 0;
          porcentaje = 0;
        }

        element.innerHTML = `<strong>${valorMostrar}</strong><br><small class="text-muted">(${porcentaje}%)</small>`;
      }
    }

    // Calcular y mostrar total de votos general (suma de todos los votos únicos)
    const totalVotosElement = document.getElementById('totalVotosGeneral');
    if (totalVotosElement && candidatos) {
      const totalVotos = candidatos.reduce((sum, c) => sum + (c.total_votos || 0), 0);
      totalVotosElement.textContent = totalVotos;
    }

    // Calcular y mostrar total de aprobaciones
    const totalAprobacionesElement = document.getElementById('totalAprobaciones');
    if (totalAprobacionesElement && candidatos) {
      const totalAprobaciones = candidatos.reduce((sum, c) => sum + (c.total_aprobaciones || 0), 0);
      totalAprobacionesElement.textContent = totalAprobaciones;
    }
  },

  /**
   * Actualiza el timestamp de la última actualización
   */
  actualizarTimestamp: function() {
    const timestamp = document.getElementById('ultimaActualizacion');
    if (timestamp) {
      const now = new Date();
      const horas = String(now.getHours()).padStart(2, '0');
      const minutos = String(now.getMinutes()).padStart(2, '0');
      const segundos = String(now.getSeconds()).padStart(2, '0');
      timestamp.textContent = `${horas}:${minutos}:${segundos}`;
    }
  },

  /**
   * Inicia la actualización automática cada X segundos
   */
  iniciarActualizacionAutomatica: function() {
    const self = this;

    // Limpiar timer anterior si existe
    if (this.timerId) {
      clearInterval(this.timerId);
    }

    // Crear nuevo timer
    this.timerId = setInterval(function() {
      console.log('Actualización automática de resultados...');
      self.cargarResultados(false); // false = automático
    }, this.intervaloActualizacion);

    console.log(`Actualización automática iniciada (cada ${this.intervaloActualizacion / 1000} segundos)`);
  },

  /**
   * Detiene la actualización automática
   */
  detenerActualizacionAutomatica: function() {
    if (this.timerId) {
      clearInterval(this.timerId);
      this.timerId = null;
      console.log('Actualización automática detenida');
    }
  }
};

// Exportar para uso global
window.ResultadosGrilla = ResultadosGrilla;

// Detener actualización automática al salir de la página
window.addEventListener('beforeunload', function() {
  ResultadosGrilla.detenerActualizacionAutomatica();
});
