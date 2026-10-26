(function (root) {
    "use strict";

    function esc(s) {
        return String(s == null ? "" : s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    var ESTADOS = {
        completo: { texto: "Completa", clase: "bg-success" },
        terminado_filtro: { texto: "Terminada por filtro (no cuenta para pago)", clase: "bg-warning text-dark" },
        pendiente_audio: { texto: "Falta subir el audio", clase: "bg-info text-dark" }
    };

    function badgeEstado(estado) {
        var e = ESTADOS[estado] || { texto: estado || "", clase: "bg-secondary" };
        return '<span class="badge ' + e.clase + '" style="font-size:.85rem;">' + esc(e.texto) + "</span>";
    }

    function tablaGrilla(b) {
        var cols = b.columnas || [];
        var h = '<div class="table-responsive"><table class="table table-sm table-bordered align-middle mb-0" style="font-size:.9rem;">' +
            '<thead class="table-light"><tr><th>Candidato o ítem</th>';
        cols.forEach(function (c) { h += "<th>" + esc(c) + "</th>"; });
        h += "</tr></thead><tbody>";
        (b.filas || []).forEach(function (f) {
            var gris = f.bloqueada_condicion ? ' style="background:#f2f4f7;color:#667085;"' : "";
            h += "<tr" + gris + '><td class="fw-semibold">' + esc(f.candidato) + "</td>";
            (f.valores || []).forEach(function (v, i) {
                var txt = v !== "" ? esc(v) : (f.bloqueada_condicion && i > 0 ? '<span class="text-muted">—</span>' : "");
                h += "<td>" + txt + "</td>";
            });
            h += "</tr>";
        });
        return h + "</tbody></table></div>";
    }

    function render(d) {
        if (!d) return '<div class="alert alert-warning mb-0">No se encontraron respuestas registradas.</div>';
        var h = '<div class="d-flex flex-wrap gap-2 align-items-center mb-3">' + badgeEstado(d.estado);
        if (d.zona) h += '<span class="badge bg-light text-dark border">Zona: ' + esc(d.zona) + "</span>";
        h += "</div>";
        var capActual = null;
        (d.bloques || []).forEach(function (b) {
            if (b.capitulo && b.capitulo !== capActual) {
                capActual = b.capitulo;
                h += '<div class="fw-bold text-primary text-uppercase small mt-3 mb-2">' + esc(capActual) + "</div>";
            }
            h += '<div class="mb-3 p-3 bg-light rounded-3"><div class="fw-bold mb-2">' + esc(b.titulo) + "</div>";
            if (b.tipo === "Grilla") {
                h += tablaGrilla(b);
            } else {
                h += '<span class="badge bg-primary" style="font-size:.9rem;white-space:normal;text-align:left;">' + esc(b.respuesta) + "</span>";
            }
            h += "</div>";
        });
        if (!(d.bloques || []).length) h += '<div class="text-muted">Sin respuestas registradas.</div>';
        return h;
    }

    root.DetalleIntentoUI = { render: render, badgeEstado: badgeEstado, esc: esc };
})(typeof self !== "undefined" ? self : this);
