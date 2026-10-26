(function ($) {
    "use strict";

    var P = {
        ficha: 0,
        tab: "resumen",
        filtros: null,
        cache: {}
    };

    function esc(s) {
        return String(s == null ? "" : s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function barra(pct, color) {
        var v = Math.max(0, Math.min(100, Number(pct) || 0));
        return '<div class="progress" style="height:12px;min-width:90px;"><div class="progress-bar" style="width:' + v + '%;background:' + (color || "#2d63bd") + ';"></div></div>';
    }

    function consulta(op, extra) {
        var d = $.extend({ op: op, ficha_id: P.ficha }, filtrosActuales(), extra || {});
        return $.ajax({ url: "admin/ajax/rqst.php", type: "POST", dataType: "json", data: d });
    }

    function filtrosActuales() {
        return {
            estado: $("#prcEstado").val() || "completo",
            municipio: $("#prcMunicipio").val() || "",
            origen: $("#prcOrigen").val() || "",
            cruce_pregunta_id: $("#prcCruceVar").val() || 0,
            cruce_opcion_id: $("#prcCruceOp").val() || 0
        };
    }

    function ok(res) {
        return res && res.output && res.output.valid ? res.output.response : null;
    }

    function error(msg) {
        $("#prcContenido").html('<div class="alert alert-danger">' + esc(msg || "No se pudo calcular. Intente de nuevo.") + "</div>");
    }

    function cargando() {
        $("#prcContenido").html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    }

    function renderResumen(r) {
        var e = r.por_estado || {};
        var h = '<div class="row g-3 mb-3">' +
            kpi("Completas", e.completo || 0, "#198754") +
            kpi("Terminadas por filtro", e.terminado_filtro || 0, "#b54708") +
            kpi("Falta subir audio", e.pendiente_audio || 0, "#0b7285") +
            kpi("Meta total", r.meta_total || 0, "#344054") + "</div>";
        (r.filtros || []).forEach(function (f) {
            h += '<div class="mb-3 p-3 bg-light rounded-3"><div class="fw-bold mb-2">' + esc(f.pregunta) + ' <span class="text-muted fw-normal">(' + f.total + ' personas)</span></div>';
            f.opciones.forEach(function (o) {
                h += '<div class="d-flex align-items-center gap-2 mb-1"><div style="width:140px;">' + esc(o.opcion) + '</div><div class="flex-fill">' + barra(o.pct) + '</div><div style="width:110px;" class="text-end"><strong>' + o.n + "</strong> · " + o.pct + "%</div></div>";
            });
            h += "</div>";
        });
        if ((r.avance_municipios || []).length) {
            h += '<h6 class="fw-bold mt-3">Avance frente a la meta</h6><div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Municipio / zona</th><th style="width:90px;">Hechas</th><th style="width:90px;">Meta</th><th style="min-width:160px;">Avance</th></tr></thead><tbody>';
            r.avance_municipios.forEach(function (m) {
                h += '<tr class="table-light fw-bold"><td>' + esc(m.municipio) + "</td><td>" + m.realizadas + "</td><td>" + m.meta + "</td><td>" + barra(m.avance, "#198754") + "</td></tr>";
                if (m.zonas.length > 1) {
                    m.zonas.forEach(function (z) {
                        h += "<tr><td class='ps-4'>" + esc(z.nombre) + "</td><td>" + z.realizadas + "</td><td>" + z.meta + "</td><td>" + barra(z.avance, "#74c69d") + "</td></tr>";
                    });
                }
            });
            h += "</tbody></table></div>";
        }
        $("#prcContenido").html(h);
    }

    function kpi(t, v, c) {
        return '<div class="col-6 col-md-3"><div class="p-3 rounded-3 border" style="border-left:6px solid ' + c + ' !important;"><div class="small text-muted fw-bold">' + esc(t) + '</div><div style="font-size:1.8rem;font-weight:800;">' + v + "</div></div></div>";
    }

    function renderPreguntas(r) {
        var h = '<div class="small text-muted mb-2">' + r.poblacion + " encuestas en este cálculo</div>";
        var cap = null;
        (r.preguntas || []).forEach(function (q) {
            if (q.capitulo && q.capitulo !== cap) {
                cap = q.capitulo;
                h += '<div class="fw-bold text-primary text-uppercase small mt-3 mb-2">' + esc(cap) + "</div>";
            }
            h += '<div class="mb-3 p-3 bg-light rounded-3"><div class="fw-bold mb-2">' + esc(q.pregunta) +
                (q.nota != null ? ' <span class="badge bg-success ms-2">Nota ' + q.nota + " sobre 5</span>" : "") +
                ' <span class="text-muted fw-normal">(' + q.respondieron + " personas)</span></div>";
            q.opciones.forEach(function (o) {
                h += '<div class="d-flex align-items-center gap-2 mb-1"><div style="flex:0 0 38%;">' + esc(o.opcion) + '</div><div class="flex-fill">' + barra(o.pct) + '</div><div style="width:110px;" class="text-end"><strong>' + o.n + "</strong> · " + o.pct + "%</div></div>";
            });
            h += "</div>";
        });
        $("#prcContenido").html(h || '<div class="text-muted">Sin datos.</div>');
    }

    function renderGrillas(r) {
        var h = '<div class="small text-muted mb-2">' + r.poblacion + " encuestas en este cálculo</div>";
        (r.grillas || []).forEach(function (g) {
            h += '<div class="mb-4"><div class="fw-bold mb-2">' + esc(g.pregunta) + "</div>";
            (g.tablas || []).forEach(function (t) {
                h += '<div class="fw-semibold text-primary mb-1">' + esc(t.plantilla) + '</div><div class="table-responsive"><table class="table table-sm table-bordered align-middle"><thead class="table-light"><tr><th>Candidato o ítem</th><th>Personas</th><th>Lo conocen %</th><th>Imagen favorable %</th><th>Intención de voto %</th><th>Nota</th></tr></thead><tbody>';
                t.filas.forEach(function (f, i) {
                    var ind = f.indicadores;
                    var id = "prcdet_" + g.pregunta_id + "_" + i + "_" + Math.random().toString(36).slice(2, 6);
                    h += '<tr><td><button type="button" class="btn btn-link p-0 text-start fw-semibold prc-detalle" data-t="' + id + '">' + esc(f.fila) + "</button></td><td>" + f.respondieron + "</td><td>" + (ind.conocimiento != null ? ind.conocimiento : "—") +
                        "</td><td>" + (ind.favorabilidad != null ? ind.favorabilidad : "—") + "</td><td>" + (ind.intencion != null ? ind.intencion : "—") + "</td><td>" + (ind.nota != null ? ind.nota : "—") + "</td></tr>";
                    h += '<tr id="' + id + '" style="display:none;"><td colspan="6" class="bg-light">';
                    f.columnas.forEach(function (c) {
                        h += '<div class="mb-2"><div class="small fw-bold">' + esc(c.columna) + ' <span class="text-muted fw-normal">(' + c.respondieron + ')</span></div>';
                        c.opciones.forEach(function (o) {
                            h += '<div class="d-flex align-items-center gap-2"><div style="flex:0 0 40%;" class="small">' + esc(o.opcion) + '</div><div class="flex-fill">' + barra(o.pct, "#7c6af1") + '</div><div style="width:90px;" class="small text-end">' + o.n + " · " + o.pct + "%</div></div>";
                        });
                        h += "</div>";
                    });
                    h += "</td></tr>";
                });
                h += "</tbody></table></div>";
            });
            h += "</div>";
        });
        $("#prcContenido").html(h || '<div class="text-muted">Sin tablas.</div>');
    }

    function renderListas(r) {
        var h = '<div class="small text-muted mb-2">' + r.poblacion + " encuestas en este cálculo. El porcentaje es sobre las personas que respondieron cada pregunta.</div>";
        (r.listas || []).forEach(function (l) {
            h += '<div class="mb-3 p-3 bg-light rounded-3"><div class="fw-bold mb-2">' + esc(l.pregunta) + ' <span class="text-muted fw-normal">(' + l.respondieron + " personas)</span></div>";
            l.items.forEach(function (o) {
                h += '<div class="d-flex align-items-center gap-2 mb-1"><div style="flex:0 0 38%;">' + esc(o.opcion) + (o.opcion_fija ? ' <span class="badge bg-secondary">fija</span>' : "") + '</div><div class="flex-fill">' + barra(o.pct, "#b54708") + '</div><div style="width:110px;" class="text-end"><strong>' + o.n + "</strong> · " + o.pct + "%</div></div>";
            });
            h += "</div>";
        });
        $("#prcContenido").html(h || '<div class="text-muted">Esta encuesta no tiene preguntas de voto en cascada.</div>');
    }

    function renderCruces() {
        var preguntas = (P.cache.preguntas && P.cache.preguntas.preguntas) || [];
        var vars = (P.cache.opciones && P.cache.opciones.variables) || [];
        var h = '<div class="row g-2 mb-3"><div class="col-md-5"><label class="form-label fw-bold small" for="prcX">Pregunta</label><select id="prcX" class="form-select">';
        preguntas.forEach(function (q) { h += '<option value="' + q.pregunta_id + '">' + esc(q.pregunta.slice(0, 90)) + "</option>"; });
        h += '</select></div><div class="col-md-4"><label class="form-label fw-bold small" for="prcY">Cruzada con</label><select id="prcY" class="form-select">';
        vars.forEach(function (v) { h += '<option value="' + v.pregunta_id + '">' + esc(v.nombre) + "</option>"; });
        h += '</select></div><div class="col-md-3 d-flex align-items-end"><button type="button" class="btn btn-primary w-100" id="prcCruzar">Cruzar</button></div></div><div id="prcCruceRes"></div>';
        $("#prcContenido").html(h);
    }

    function pintarCruce(r) {
        var h = '<div class="table-responsive"><table class="table table-sm table-bordered align-middle"><thead class="table-light"><tr><th></th>';
        r.columnas.forEach(function (c) { h += "<th>" + esc(c.opcion) + ' <span class="text-muted fw-normal">(' + c.total + ")</span></th>"; });
        h += "</tr></thead><tbody>";
        r.filas.forEach(function (f) {
            h += '<tr><td class="fw-semibold">' + esc(f.opcion) + "</td>";
            f.celdas.forEach(function (c) { h += "<td>" + c.n + ' <span class="text-muted">· ' + c.pct_columna + "%</span></td>"; });
            h += "</tr>";
        });
        $("#prcCruceRes").html(h + "</tbody></table></div><div class='small text-muted'>El porcentaje es sobre cada columna.</div>");
    }

    function mostrar() {
        cargando();
        if (P.tab === "resumen") {
            consulta("cuestionarioresumenestados").done(function (res) { var r = ok(res); r ? renderResumen(r) : error(); }).fail(function () { error(); });
        } else if (P.tab === "preguntas") {
            consulta("cuestionarioresultadospregunta").done(function (res) { var r = ok(res); if (r) { P.cache.preguntas = r; renderPreguntas(r); } else error(); }).fail(function () { error(); });
        } else if (P.tab === "grillas") {
            consulta("cuestionarioresultadosgrilla").done(function (res) { var r = ok(res); r ? renderGrillas(r) : error(); }).fail(function () { error(); });
        } else if (P.tab === "listas") {
            consulta("cuestionariovotoescalonado").done(function (res) { var r = ok(res); r ? renderListas(r) : error(); }).fail(function () { error(); });
        } else if (P.tab === "cruces") {
            consulta("cuestionarioresultadospregunta").done(function (res) {
                var r = ok(res);
                if (!r) return error();
                P.cache.preguntas = r;
                renderCruces();
            }).fail(function () { error(); });
        }
    }

    function exportar() {
        consulta("cuestionarioexport").done(function (res) {
            var r = ok(res);
            if (!r) return error("No se pudo preparar el archivo.");
            var bin = atob(r.contenido_base64);
            var bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
            var url = URL.createObjectURL(new Blob([bytes], { type: "text/csv;charset=utf-8" }));
            var a = document.createElement("a");
            a.href = url;
            a.download = r.archivo;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }).fail(function () { error("No se pudo preparar el archivo."); });
    }

    var PANEL_RES_CUEST = {
        cargar: function (fichaId) {
            P.ficha = parseInt(fichaId, 10) || 0;
            if (P.ficha <= 0) {
                $("#panelResCuest").hide();
                return;
            }
            $("#panelResCuest").show();
            consulta("cuestionariofiltroscruce").done(function (res) {
                var r = ok(res) || { variables: [], municipios: [] };
                P.cache.opciones = r;
                var m = '<option value="">Todos</option>';
                r.municipios.forEach(function (x) { m += '<option value="' + esc(x.codigo_municipio) + '">' + esc(x.municipio || x.codigo_municipio) + "</option>"; });
                $("#prcMunicipio").html(m);
                var v = '<option value="">Cualquier respuesta</option>';
                r.variables.forEach(function (x) { v += '<option value="' + x.pregunta_id + '">' + esc(x.nombre) + "</option>"; });
                $("#prcCruceVar").html(v);
                $("#prcCruceOp").html('<option value="">—</option>').prop("disabled", true);
            });
            mostrar();
        }
    };

    $(function () {
        $(document).on("click", "#prcTabs .nav-link", function () {
            $("#prcTabs .nav-link").removeClass("active");
            $(this).addClass("active");
            P.tab = $(this).data("prc");
            mostrar();
        });
        $(document).on("change", "#prcCruceVar", function () {
            var id = parseInt($(this).val(), 10) || 0;
            var o = '<option value="">—</option>';
            ((P.cache.opciones && P.cache.opciones.variables) || []).forEach(function (v) {
                if (v.pregunta_id === id) v.opciones.forEach(function (x) { o += '<option value="' + x.id + '">' + esc(x.texto_opcion) + "</option>"; });
            });
            $("#prcCruceOp").html(o).prop("disabled", !id);
        });
        $(document).on("click", "#prcAplicar", mostrar);
        $(document).on("click", "#prcExportar", exportar);
        $(document).on("click", ".prc-detalle", function () { $("#" + $(this).data("t")).toggle(); });
        $(document).on("click", "#prcCruzar", function () {
            consulta("cuestionarioresultadoscruce", { pregunta_x_id: $("#prcX").val(), pregunta_y_id: $("#prcY").val() }).done(function (res) {
                var r = ok(res);
                r ? pintarCruce(r) : $("#prcCruceRes").html('<div class="alert alert-warning">No se pudo calcular el cruce.</div>');
            });
        });
    });

    window.PANEL_RES_CUEST = PANEL_RES_CUEST;
})(jQuery);
