/**
 * Sistema de Gestión de Respuestas de Estudio de Votaciones
 * Gestiona la lógica condicional entre preguntas del estudio
 *
 * DEPENDENCIAS ENTRE PREGUNTAS:
 * 1. CONOCE AL CANDIDATO: Es la pregunta base
 *    - SI = "si" -> Habilita pregunta 2 (IMAGEN)
 *    - SI = "no" -> Deshabilita preguntas 2 y 3 (ambas en NO APLICA)
 *
 * 2. IMAGEN FAVORABLE O DESFAVORABLE: Depende de pregunta 1
 *    - SI pregunta 1 = "no" -> NO APLICA (deshabilitada)
 *    - SI pregunta 1 = "si" -> Habilitada
 *      - SI = "favorable" -> Habilita pregunta 3 (VOTARÍA)
 *      - SI = "desfavorable" -> Deshabilita pregunta 3 (NO APLICA)
 *
 * 3. VOTARÍA POR ÉL/ELLA: Depende de pregunta 1 y 2
 *    - SI pregunta 1 = "no" -> NO APLICA
 *    - SI pregunta 2 = "desfavorable" -> NO APLICA
 *    - SI pregunta 1 = "si" Y pregunta 2 = "favorable" -> Habilitada
 */

const EstudioVotaciones = {
  // Almacén de respuestas del usuario
  respuestas: {},

  // Almacén de respuestas de preguntas adicionales P(A), P(B), P(C)
  preguntasAdicionales: {
    pregunta_pa: null,  // P(A): Si las elecciones fueran hoy
    pregunta_pb: null,  // P(B): Si P(A) se retira
    pregunta_pc: null   // P(C): Si P(B) se retira
  },

  // Datos de la grilla recibidos desde PHP
  grillaData: null,

  /**
   * Inicializa el sistema
   * @param {Object} grillaData - Datos de la grilla desde PHP
   */
  init: function(grillaData) {
    this.grillaData = grillaData;
    console.log('Inicializando Sistema de Estudio de Votaciones');
    console.log('Datos de la grilla:', this.grillaData);

    // Verificar si el usuario ya votó hoy en esta grilla
    this.verificarVotoDuplicado();

    this.initializeRespuestas();
    this.bindEvents();
    this.applyInitialState();
  },

  /**
   * Verifica si el usuario ya votó hoy en esta grilla
   */
  verificarVotoDuplicado: function() {
    const datosEnvio = {
      op: 'grillacandidatoverificarvotoduplicado',
      grilla_id: this.grillaData.id
    };

    UTIL.callAjaxRqstPOST(datosEnvio, (response) => {
      console.log('Verificación de voto duplicado:', response);

      if (response && response.output && response.output.valid) {
        const yaVoto = response.output.response.ya_voto;

        if (yaVoto) {
          // El usuario ya votó, mostrar mensaje y deshabilitar la interfaz
          UTIL.mostrarMensajeValidacion(
            '<strong>Voto ya registrado</strong><br><br>' +
            response.output.response.msg +
            '<br><br>Será redirigido a la página de grillas.'
          );

          // Deshabilitar todos los botones toggle
          const toggleButtons = document.querySelectorAll('.toggle-btn');
          toggleButtons.forEach(btn => {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            btn.style.opacity = '0.5';
          });

          // Deshabilitar botón de guardar
          const btnGuardar = document.getElementById('btnGuardarRespuestas');
          if (btnGuardar) {
            btnGuardar.disabled = true;
            btnGuardar.style.cursor = 'not-allowed';
            btnGuardar.style.opacity = '0.5';
          }

          // Redirigir después de 4 segundos
          setTimeout(function() {
            window.location.href = 'grilla.php';
          }, 4000);
        }
      }
    });
  },

  /**
   * Inicializa la estructura de respuestas para todos los candidatos
   */
  initializeRespuestas: function() {
    const rows = document.querySelectorAll('.tabla_grilla tbody tr[data-candidato-id]');
    rows.forEach(row => {
      const candidatoId = row.getAttribute('data-candidato-id');
      this.respuestas[candidatoId] = {
        conoce: 'no',           // Por defecto "No" está activo
        imagen: 'no_aplica',    // Por defecto "NO APLICA"
        votaria: 'no_aplica'    // Por defecto "NO APLICA"
      };
    });
    console.log('Respuestas inicializadas:', this.respuestas);
  },

  /**
   * Vincula eventos de click a todos los botones toggle
   */
  bindEvents: function() {
    const self = this;

    // Event delegation en la tabla
    const tbody = document.querySelector('.tabla_grilla tbody');
    if (!tbody) {
      console.error('No se encontró el tbody de la tabla');
      return;
    }

    tbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.toggle-btn');
      if (!btn) return;

      const row = btn.closest('tr[data-candidato-id]');
      const td = btn.closest('td[data-pregunta]');

      if (!row || !td) return;

      const candidatoId = row.getAttribute('data-candidato-id');
      const pregunta = td.getAttribute('data-pregunta');
      const valor = btn.getAttribute('data-value');

      self.handleToggleClick(candidatoId, pregunta, valor, row, btn);
    });

    // Botón de guardar
    const btnGuardar = document.getElementById('btnGuardarRespuestas');
    if (btnGuardar) {
      btnGuardar.addEventListener('click', function() {
        self.guardarRespuestas();
      });
    }
  },

  /**
   * Maneja el click en un botón toggle
   * @param {string} candidatoId - ID del candidato
   * @param {string} pregunta - Tipo de pregunta (conoce, imagen, votaria)
   * @param {string} valor - Valor seleccionado
   * @param {HTMLElement} row - Fila de la tabla
   * @param {HTMLElement} btn - Botón clickeado
   */
  handleToggleClick: function(candidatoId, pregunta, valor, row, btn) {
    console.log(`Click en candidato ${candidatoId}, pregunta: ${pregunta}, valor: ${valor}`);

    // Actualizar estado visual del toggle
    const toggleGroup = btn.parentElement;
    const buttons = toggleGroup.querySelectorAll('.toggle-btn');
    buttons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Actualizar respuestas
    this.respuestas[candidatoId][pregunta] = valor;

    // Aplicar lógica condicional según la pregunta
    this.applyConditionalLogic(candidatoId, pregunta, valor, row);

    console.log('Respuestas actualizadas:', this.respuestas);
  },

  /**
   * Aplica la lógica condicional según las reglas del negocio
   *
   * REGLAS:
   * 1. Si "CONOCE" = NO -> Deshabilitar "IMAGEN" y "VOTARÍA"
   * 2. Si "CONOCE" = SÍ -> Habilitar "IMAGEN"
   * 3. Si "IMAGEN" = DESFAVORABLE -> Deshabilitar "VOTARÍA"
   * 4. Si "IMAGEN" = FAVORABLE -> Habilitar "VOTARÍA"
   *
   * @param {string} candidatoId - ID del candidato
   * @param {string} pregunta - Tipo de pregunta que cambió
   * @param {string} valor - Nuevo valor
   * @param {HTMLElement} row - Fila de la tabla
   */
  applyConditionalLogic: function(candidatoId, pregunta, valor, row) {
    const imagenTd = row.querySelector('td[data-pregunta="imagen"]');
    const votariaTd = row.querySelector('td[data-pregunta="votaria"]');

    // REGLA 1: Si cambia "CONOCE AL CANDIDATO"
    if (pregunta === 'conoce') {
      if (valor === 'no') {
        // NO conoce al candidato -> Deshabilitar IMAGEN y VOTARÍA
        this.setNoAplica(imagenTd);
        this.setNoAplica(votariaTd);

        // Actualizar respuestas
        this.respuestas[candidatoId].imagen = 'no_aplica';
        this.respuestas[candidatoId].votaria = 'no_aplica';
      } else {
        // SÍ conoce al candidato -> Habilitar IMAGEN
        this.setAplicable(imagenTd, 'favorable');
        this.respuestas[candidatoId].imagen = 'favorable';

        // Habilitar VOTARÍA solo si IMAGEN es favorable
        this.setAplicable(votariaTd, 'si');
        this.respuestas[candidatoId].votaria = 'si';
      }
    }

    // REGLA 2: Si cambia "IMAGEN FAVORABLE O DESFAVORABLE"
    if (pregunta === 'imagen') {
      const conoce = this.respuestas[candidatoId].conoce;

      // Solo aplica si conoce al candidato
      if (conoce === 'si') {
        if (valor === 'desfavorable') {
          // Imagen DESFAVORABLE -> Deshabilitar VOTARÍA
          this.setNoAplica(votariaTd);
          this.respuestas[candidatoId].votaria = 'no_aplica';
        } else if (valor === 'favorable') {
          // Imagen FAVORABLE -> Habilitar VOTARÍA
          this.setAplicable(votariaTd, 'si');
          this.respuestas[candidatoId].votaria = 'si';
        }
      }
    }

    // La pregunta "VOTARÍA" no tiene dependencias hacia abajo
    // (es la última pregunta de la cadena)

    // Actualizar la lista de candidatos aprobados
    this.actualizarCandidatosAprobados();
  },

  /**
   * Marca una celda como "NO APLICA"
   * @param {HTMLElement} td - Celda a modificar
   */
  setNoAplica: function(td) {
    if (!td) return;

    const toggle = td.querySelector('.toggle');
    if (toggle) {
      toggle.style.display = 'none';
    }

    // Agregar texto "NO APLICA" si no existe
    if (!td.querySelector('.no-aplica-text')) {
      const span = document.createElement('span');
      span.className = 'no-aplica-text';
      span.textContent = 'NO APLICA';
      td.appendChild(span);
    }

    td.classList.add('no-aplica');
  },

  /**
   * Reactiva una celda que estaba marcada como "NO APLICA"
   * @param {HTMLElement} td - Celda a modificar
   * @param {string} valorDefault - Valor por defecto a activar
   */
  setAplicable: function(td, valorDefault) {
    if (!td) return;

    const toggle = td.querySelector('.toggle');
    if (toggle) {
      toggle.style.display = 'flex';

      // Activar el botón por defecto
      const buttons = toggle.querySelectorAll('.toggle-btn');
      buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-value') === valorDefault) {
          btn.classList.add('active');
        }
      });
    }

    // Remover texto "NO APLICA"
    const noAplicaText = td.querySelector('.no-aplica-text');
    if (noAplicaText) {
      noAplicaText.remove();
    }

    td.classList.remove('no-aplica');
  },

  /**
   * Aplica el estado inicial de la interfaz
   */
  applyInitialState: function() {
    // Por defecto todos los candidatos NO se conocen,
    // por lo que las preguntas 2 y 3 están deshabilitadas
    console.log('Estado inicial aplicado - Todos en NO por defecto');

    // Actualizar candidatos aprobados (debería estar vacío al inicio)
    this.actualizarCandidatosAprobados();
  },

  /**
   * Guarda las respuestas del usuario
   */
  guardarRespuestas: function() {
    console.log('Guardando respuestas:', this.respuestas);
    console.log('Preguntas adicionales:', this.preguntasAdicionales);

    // Validar que todas las respuestas estén completas
    const candidatosIncompletos = this.validarRespuestas();

    if (candidatosIncompletos.length > 0) {
      const mensaje = 'Por favor, complete todas las respuestas para los candidatos:<br><br>' +
                     candidatosIncompletos.join('<br>');
      UTIL.mostrarMensajeValidacion(mensaje);
      return;
    }

    // Validar preguntas adicionales (si hay candidatos aprobados)
    const aprobados = this.getCandidatosAprobados();
    if (aprobados.length > 0) {
      const faltantes = [];
      if (this.preguntasAdicionales.pregunta_pa === null) {
        faltantes.push('P(A): Si las elecciones fueran hoy, ¿por quién votaría?');
      }
      if (this.preguntasAdicionales.pregunta_pb === null) {
        faltantes.push('P(B): Si su candidato P(A) se retira, ¿por quién votaría?');
      }
      if (this.preguntasAdicionales.pregunta_pc === null) {
        faltantes.push('P(C): Si su candidato P(B) se retira, ¿por quién votaría?');
      }

      if (faltantes.length > 0) {
        const mensaje = 'Por favor, complete las siguientes preguntas adicionales:<br><br>' +
                       faltantes.join('<br>');
        UTIL.mostrarMensajeValidacion(mensaje);
        return;
      }
    }

    // Preparar datos para enviar - RESPUESTAS PRINCIPALES
    const datosEnvio = {
      op: 'grillacandidatoguardarrespuestas',
      grilla_id: this.grillaData.id,
      respuestas: JSON.stringify(this.respuestas)
    };

    console.log('Datos a enviar (respuestas principales):', datosEnvio);

    UTIL.mostrarMensajeError('Sistema de AJAX no disponible. Por favor, recargue la página.');

    // Mostrar cursor ocupado
    UTIL.cursorBusy();

    // Enviar vía AJAX - primero las respuestas principales
    UTIL.callAjaxRqstPOST(datosEnvio, this.guardarRespuestasHandler.bind(this));
  },

  /**
   * Valida que todas las respuestas estén completas
   * @returns {Array} Array con nombres de candidatos incompletos
   */
  validarRespuestas: function() {
    const incompletos = [];

    for (const candidatoId in this.respuestas) {
      const resp = this.respuestas[candidatoId];

      // Validación según las dependencias:
      // 1. Si NO conoce -> imagen y votaría deben ser "no_aplica"
      // 2. Si SÍ conoce -> imagen debe tener valor
      // 3. Si imagen es favorable -> votaría debe tener valor
      // 4. Si imagen es desfavorable -> votaría debe ser "no_aplica"

      if (resp.conoce === 'si') {
        // Debe tener respuesta de imagen
        if (!resp.imagen || resp.imagen === 'no_aplica') {
          const candidato = this.getCandidatoById(candidatoId);
          if (candidato) {
            incompletos.push(candidato.nombre_completo + ' (falta respuesta de IMAGEN)');
          }
        } else if (resp.imagen === 'favorable') {
          // Si imagen es favorable, debe tener respuesta de votaría
          if (!resp.votaria || resp.votaria === 'no_aplica') {
            const candidato = this.getCandidatoById(candidatoId);
            if (candidato) {
              incompletos.push(candidato.nombre_completo + ' (falta respuesta de VOTARÍA)');
            }
          }
        }
      }
    }

    return incompletos;
  },

  /**
   * Obtiene un candidato por su ID
   * @param {string} candidatoId - ID del candidato
   * @returns {Object|null} Datos del candidato o null
   */
  getCandidatoById: function(candidatoId) {
    if (!this.grillaData || !this.grillaData.candidatos) return null;

    return this.grillaData.candidatos.find(c => c.id == candidatoId) || null;
  },

  /**
   * Handler para la respuesta del guardado
   * @param {Object} response - Respuesta del servidor
   */
  guardarRespuestasHandler: function(response) {
    console.log('Respuesta del servidor (respuestas principales):', response);

    if (response && response.output && response.output.valid) {
      const totalGuardadas = response.output.response.total_guardadas || 0;
      console.log(`✓ Respuestas principales guardadas: ${totalGuardadas}`);

      // Verificar si hay preguntas adicionales para guardar
      const aprobados = this.getCandidatosAprobados();
      if (aprobados.length > 0) {
        // Guardar preguntas adicionales (mantener cursor ocupado)
        this.guardarPreguntasAdicionales(totalGuardadas);
      } else {
        // No hay candidatos aprobados, terminar
        UTIL.cursorNormal();
        UTIL.mostrarMensajeExitoso(
          '¡Respuestas guardadas exitosamente!<br><br>Total guardadas: ' + totalGuardadas
        );

        // Opcional: redirigir a otra página después de 1.5 segundos
        // setTimeout(function() {
        //   window.location.href = 'grilla.php';
        // }, 1500);
      }
    } else {
      UTIL.cursorNormal();
      const mensaje = response?.output?.response?.msg ||
                     response?.output?.response?.content ||
                     'Error al guardar las respuestas. Por favor, intente nuevamente.';
      UTIL.mostrarMensajeError(mensaje);
    }
  },

  /**
   * Guarda las preguntas adicionales P(A), P(B), P(C)
   * @param {number} totalRespuestasPrincipales - Total de respuestas principales guardadas
   */
  guardarPreguntasAdicionales: function(totalRespuestasPrincipales) {
    console.log('Guardando preguntas adicionales:', this.preguntasAdicionales);

    const datosEnvio = {
      op: 'grillacandidatoguardarpreguntasadicionales',
      grilla_id: this.grillaData.id,
      pregunta_pa: this.preguntasAdicionales.pregunta_pa,
      pregunta_pb: this.preguntasAdicionales.pregunta_pb,
      pregunta_pc: this.preguntasAdicionales.pregunta_pc
    };

    console.log('Datos a enviar (preguntas adicionales):', datosEnvio);

    // El cursor ya está ocupado desde guardarRespuestas()
    UTIL.callAjaxRqstPOST(datosEnvio, (response) => {
      UTIL.cursorNormal();
      console.log('Respuesta del servidor (preguntas adicionales):', response);

      if (response && response.output && response.output.valid) {
        console.log('✓ Preguntas adicionales guardadas exitosamente');

        UTIL.mostrarMensajeExitoso("¡Todas las respuestas guardadas exitosamente!");
        setTimeout(function () {
          window.location = "dashboard.php";
        }, 1500);

        // Opcional: redirigir a otra página después de 1.5 segundos
        // setTimeout(function() {
        //   window.location.href = 'grilla.php';
        // }, 1500);
      } else {
        const mensaje = response?.output?.response?.msg ||
                       response?.output?.response?.content ||
                       'Error al guardar las preguntas adicionales.';
        UTIL.mostrarMensajeError(
          'Las respuestas principales se guardaron correctamente, pero hubo un error con las preguntas adicionales:<br><br>' +
          mensaje
        );
      }
    });
  },

  /**
   * Obtiene un resumen de las respuestas actuales
   * @returns {Object} Resumen estadístico
   */
  getResumen: function() {
    const resumen = {
      total_candidatos: Object.keys(this.respuestas).length,
      conocen: 0,
      no_conocen: 0,
      imagen_favorable: 0,
      imagen_desfavorable: 0,
      imagen_no_aplica: 0,
      votarian: 0,
      no_votarian: 0,
      votaria_no_aplica: 0
    };

    for (const candidatoId in this.respuestas) {
      const resp = this.respuestas[candidatoId];

      // Contadores de "CONOCE"
      if (resp.conoce === 'si') {
        resumen.conocen++;
      } else {
        resumen.no_conocen++;
      }

      // Contadores de "IMAGEN"
      if (resp.imagen === 'favorable') {
        resumen.imagen_favorable++;
      } else if (resp.imagen === 'desfavorable') {
        resumen.imagen_desfavorable++;
      } else if (resp.imagen === 'no_aplica') {
        resumen.imagen_no_aplica++;
      }

      // Contadores de "VOTARÍA"
      if (resp.votaria === 'si') {
        resumen.votarian++;
      } else if (resp.votaria === 'no') {
        resumen.no_votarian++;
      } else if (resp.votaria === 'no_aplica') {
        resumen.votaria_no_aplica++;
      }
    }

    return resumen;
  },

  /**
   * Muestra el resumen en consola (útil para debugging)
   */
  mostrarResumen: function() {
    const resumen = this.getResumen();
    console.log('=== RESUMEN DE RESPUESTAS ===');
    console.log('Total candidatos:', resumen.total_candidatos);
    console.log('\nCONOCE AL CANDIDATO:');
    console.log('  - Sí:', resumen.conocen);
    console.log('  - No:', resumen.no_conocen);
    console.log('\nIMAGEN:');
    console.log('  - Favorable:', resumen.imagen_favorable);
    console.log('  - Desfavorable:', resumen.imagen_desfavorable);
    console.log('  - No aplica:', resumen.imagen_no_aplica);
    console.log('\nVOTARÍA:');
    console.log('  - Sí:', resumen.votarian);
    console.log('  - No:', resumen.no_votarian);
    console.log('  - No aplica:', resumen.votaria_no_aplica);
    console.log('=============================');

    return resumen;
  },

  /**
   * Obtiene la lista de candidatos que pasaron todas las preguntas
   * Un candidato "pasa" si: conoce='si' AND imagen='favorable' AND votaria='si'
   * @returns {Array} Array de IDs de candidatos aprobados
   */
  getCandidatosAprobados: function() {
    const aprobados = [];

    for (const candidatoId in this.respuestas) {
      const resp = this.respuestas[candidatoId];

      // Condiciones para aprobar:
      // 1. Conoce al candidato = SÍ
      // 2. Imagen = FAVORABLE
      // 3. Votaría = SÍ
      if (resp.conoce === 'si' && resp.imagen === 'favorable' && resp.votaria === 'si') {
        aprobados.push(candidatoId);
      }
    }

    return aprobados;
  },

  /**
   * Actualiza dinámicamente la lista de candidatos aprobados en la columna lateral
   */
  actualizarCandidatosAprobados: function() {
    const container = document.getElementById('candidatosAprobadosContainer');
    const totalElement = document.getElementById('totalAprobados');
    const mensajeVacio = document.getElementById('mensajeVacio');

    if (!container || !totalElement) {
      console.warn('No se encontró el contenedor de candidatos aprobados');
      return;
    }

    // Obtener candidatos aprobados
    const aprobados = this.getCandidatosAprobados();
    const totalAprobados = aprobados.length;

    // Actualizar contador
    totalElement.textContent = totalAprobados;

    // Limpiar contenedor
    container.innerHTML = '';

    if (totalAprobados === 0) {
      // Mostrar mensaje vacío
      if (mensajeVacio) {
        container.appendChild(mensajeVacio.cloneNode(true));
      } else {
        container.innerHTML = `
          <div class="text-center text-muted py-4">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <p class="mb-0">No hay candidatos aprobados aún</p>
          </div>
        `;
      }
    } else {
      // Renderizar candidatos aprobados con diseño visual mejorado
      // Asignar letras automáticamente: A, B, C, D...
      const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

      aprobados.forEach((candidatoId, index) => {
        const candidato = this.getCandidatoById(candidatoId);
        if (!candidato) return;

        const letra = letras[index] || `#${index + 1}`; // A, B, C... o #1, #2, #3...

        const fotoUrl = candidato.foto
          ? 'assets/img/admin/' + candidato.foto
          : 'assets/img/candidato.png';

        const candidatoCard = document.createElement('div');
        candidatoCard.className = 'candidato-aprobado-item mb-2';
        candidatoCard.setAttribute('data-candidato-letra', letra);
        candidatoCard.innerHTML = `
          <div class="candidato-aprobado-card-compacto">
            <div class="candidato-aprobado-foto-container">
              <img src="${fotoUrl}" alt="${candidato.nombre_completo}" class="candidato-aprobado-foto-compacta">
              <div class="candidato-aprobado-letra-badge">
                ${letra}
              </div>
            </div>
            <div class="candidato-aprobado-info">
              <strong class="candidato-nombre-compacto">${letra}. ${candidato.nombre_completo}</strong>
              ${candidato.nombres_partidos ? `<small class="candidato-partido-compacto">${candidato.nombres_partidos}</small>` : ''}
              <div class="respuestas-compactas">
                <span class="si-badge"><i class="fas fa-check"></i> SÍ</span>
                <span class="si-badge"><i class="fas fa-check"></i> SÍ</span>
                <span class="si-badge"><i class="fas fa-check"></i> SÍ</span>
              </div>
            </div>
          </div>
        `;

        container.appendChild(candidatoCard);
      });

      // Renderizar preguntas adicionales
      this.renderizarPreguntasAdicionales(aprobados, letras);
    }

    console.log(`Candidatos aprobados actualizados: ${totalAprobados}`);
  },

  /**
   * Renderiza las preguntas adicionales para candidatos aprobados
   * @param {Array} aprobados - Array de IDs de candidatos aprobados
   * @param {Array} letras - Array de letras del alfabeto
   */
  renderizarPreguntasAdicionales: function(aprobados, letras) {
    const container = document.getElementById('candidatosAprobadosContainer');
    if (!container || aprobados.length === 0) return;

    // Crear sección de preguntas adicionales
    const preguntasSection = document.createElement('div');
    preguntasSection.className = 'preguntas-adicionales mt-3 pt-3 border-top';
    preguntasSection.innerHTML = `
      <h6 class="text-center mb-3"><i class="fas fa-poll"></i> PREGUNTAS ADICIONALES</h6>
    `;

    // Pregunta 1: Si las elecciones fueran hoy, ¿por quién votaría?
    const pregunta1 = this.crearPreguntaAdicional(
      'pregunta1',
      'Si las elecciones fueran hoy, ¿por quién votaría?',
      aprobados,
      letras,
      'P(A)'
    );
    preguntasSection.appendChild(pregunta1);

    // Pregunta 2: Si su candidato P(A) se retira, ¿por quién votaría?
    const pregunta2 = this.crearPreguntaAdicional(
      'pregunta2',
      'Si su candidato P(A) se retira, ¿por quién votaría?',
      aprobados,
      letras,
      'P(B)'
    );
    preguntasSection.appendChild(pregunta2);

    // Pregunta 3: Si su candidato P(B) se retira, ¿por quién votaría?
    const pregunta3 = this.crearPreguntaAdicional(
      'pregunta3',
      'Si su candidato P(B) se retira, ¿por quién votaría?',
      aprobados,
      letras,
      'P(C)'
    );
    preguntasSection.appendChild(pregunta3);

    container.appendChild(preguntasSection);

    UTIL.cursorNormal();
  },

  /**
   * Crea una pregunta adicional con opciones de candidatos
   * @param {string} id - ID de la pregunta
   * @param {string} texto - Texto de la pregunta
   * @param {Array} aprobados - Array de IDs de candidatos aprobados
   * @param {Array} letras - Array de letras del alfabeto
   * @param {string} identificador - Identificador de la respuesta (P(A), P(B), P(C))
   * @returns {HTMLElement} - Elemento de la pregunta
   */
  crearPreguntaAdicional: function(id, texto, aprobados, letras, identificador) {
    const preguntaDiv = document.createElement('div');
    preguntaDiv.className = 'pregunta-adicional-item mb-3';
    preguntaDiv.innerHTML = `
      <p class="pregunta-texto"><strong>${texto}</strong></p>
      <small class="text-muted">Seleccione: ${identificador}</small>
      <div class="opciones-candidatos mt-2">
        ${aprobados.map((candidatoId, index) => {
          const candidato = this.getCandidatoById(candidatoId);
          const letra = letras[index];
          return `
            <button class="btn btn-outline-primary btn-sm btn-candidato-opcion mb-1"
                    data-pregunta="${id}"
                    data-candidato="${candidatoId}"
                    data-letra="${letra}">
              <i class="fas fa-user"></i> ${letra}. ${candidato.nombre_completo}
            </button>
          `;
        }).join('')}
      </div>
    `;

    // Agregar event listeners a los botones
    const self = this;
    setTimeout(() => {
      const botones = preguntaDiv.querySelectorAll('.btn-candidato-opcion');
      botones.forEach(btn => {
        btn.addEventListener('click', () => {
          // Desactivar otros botones de la misma pregunta
          botones.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');

          const preguntaId = btn.getAttribute('data-pregunta');
          const candidatoId = btn.getAttribute('data-candidato');
          const letra = btn.getAttribute('data-letra');

          console.log(`Pregunta ${preguntaId}: Seleccionado candidato ${letra} (ID: ${candidatoId})`);

          // Guardar la respuesta en el objeto de preguntas adicionales
          self.guardarPreguntaAdicional(preguntaId, candidatoId);
        });
      });
    }, 100);

    return preguntaDiv;
  },

  /**
   * Guarda la respuesta de una pregunta adicional
   * @param {string} preguntaId - ID de la pregunta (pregunta1, pregunta2, pregunta3)
   * @param {string} candidatoId - ID del candidato seleccionado
   */
  guardarPreguntaAdicional: function(preguntaId, candidatoId) {
    // Mapear pregunta1 -> pregunta_pa, pregunta2 -> pregunta_pb, pregunta3 -> pregunta_pc
    const mapeo = {
      'pregunta1': 'pregunta_pa',
      'pregunta2': 'pregunta_pb',
      'pregunta3': 'pregunta_pc'
    };

    const campo = mapeo[preguntaId];
    if (campo) {
      this.preguntasAdicionales[campo] = parseInt(candidatoId);
      console.log('Preguntas adicionales actualizadas:', this.preguntasAdicionales);
    }
  }
};

// Exportar para uso global
window.EstudioVotaciones = EstudioVotaciones;
