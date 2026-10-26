<div class="r-card mb-3" id="panelResCuest" style="display:none;">
  <div class="r-card-body">
    <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Resultados de la encuesta</h5>

    <div class="row g-2 align-items-end mb-3">
      <div class="col-6 col-md-2">
        <label class="form-label fw-bold small" for="prcEstado">Encuestas</label>
        <select id="prcEstado" class="form-select">
          <option value="completo">Completas</option>
          <option value="terminado_filtro">Terminadas por filtro</option>
          <option value="pendiente_audio">Falta audio</option>
          <option value="todas">Todas</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label fw-bold small" for="prcMunicipio">Municipio</label>
        <select id="prcMunicipio" class="form-select"><option value="">Todos</option></select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label fw-bold small" for="prcOrigen">Origen</label>
        <select id="prcOrigen" class="form-select">
          <option value="">Todos</option>
          <option value="interno">Encuestador</option>
          <option value="publico">Web pública</option>
          <option value="contestar">Contestar</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label fw-bold small" for="prcCruceVar">Solo personas con</label>
        <select id="prcCruceVar" class="form-select"><option value="">Cualquier respuesta</option></select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label fw-bold small" for="prcCruceOp">Respuesta</label>
        <select id="prcCruceOp" class="form-select" disabled><option value="">—</option></select>
      </div>
      <div class="col-6 col-md-2 d-flex gap-2">
        <button type="button" class="btn btn-primary flex-fill" id="prcAplicar"><i class="fas fa-filter me-1"></i>Aplicar</button>
        <button type="button" class="btn btn-outline-success" id="prcExportar" title="Descargar para Excel"><i class="fas fa-file-excel"></i></button>
      </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="prcTabs">
      <li class="nav-item"><button type="button" class="nav-link active" data-prc="resumen">Resumen y avance</button></li>
      <li class="nav-item"><button type="button" class="nav-link" data-prc="preguntas">Preguntas</button></li>
      <li class="nav-item"><button type="button" class="nav-link" data-prc="grillas">Tablas de candidatos</button></li>
      <li class="nav-item"><button type="button" class="nav-link" data-prc="listas">Voto</button></li>
      <li class="nav-item"><button type="button" class="nav-link" data-prc="cruces">Cruces</button></li>
    </ul>
    <div id="prcContenido"><div class="text-muted">Seleccione una encuesta.</div></div>
  </div>
</div>
