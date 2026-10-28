(function (root) {
    "use strict";

    function esc(str) {
        return String(str == null ? "" : str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function reemplazarMunicipio(texto, ctx) {
        var s = String(texto == null ? "" : texto);
        var munNombre = (ctx && ctx.nombre_municipio) ? ctx.nombre_municipio : ((ctx && ctx.codigo_municipio) ? ctx.codigo_municipio : "su municipio");
        return s.replace(/\{municipio\}/gi, munNombre);
    }

    function renderLibreto(libreto, ctx) {
        if (ctx && ctx.publico) return "";
        if (!libreto || !String(libreto).trim()) return "";
        return (
            '<div class="cq-libreto-box p-3 mb-3 rounded" style="background:#FFFBEB;border:2px solid #F59E0B;color:#92400E;font-size:18px;">' +
                '<strong><i class="fas fa-bullhorn me-2"></i>Lea al encuestado:</strong> ' +
                esc(reemplazarMunicipio(libreto, ctx)) +
            '</div>'
        );
    }

    function renderCierreFiltro(mensajeCierre, onConfirmarAttr, publico) {
        return (
            '<div class="cq-cierre-filtro-box p-4 my-3 rounded text-center" style="background:#FEF3F2;border:2px solid #F04438;color:#912018;">' +
                '<div class="mb-2" style="font-size:28px;"><i class="fas fa-hand-paper"></i></div>' +
                '<h4 style="font-size:24px;font-weight:800;margin-bottom:10px;">' + (publico ? 'Gracias por participar' : 'Encuesta finalizada por pregunta filtro') + '</h4>' +
                '<p style="font-size:19px;font-weight:600;margin-bottom:18px;">' + esc(mensajeCierre || "Muchas gracias por su tiempo. Hemos terminado la encuesta.") + '</p>' +
                (onConfirmarAttr
                    ? '<button type="button" class="btn btn-danger btn-lg fw-bold px-4 py-3" style="min-height:56px;font-size:19px;border-radius:12px;" ' + onConfirmarAttr + '>' +
                      '<i class="fas fa-paper-plane me-2"></i>Enviar y terminar' +
                      '</button>'
                    : '') +
            '</div>'
        );
    }

    function limpio(v) {
        return String(v == null ? "" : v).trim();
    }

    function seccionDe(def, pid, visibles) {
        var porId = {};
        (def.preguntas || []).forEach(function (p) { porId[parseInt(p.id, 10)] = p; });
        var idx = visibles.indexOf(pid);
        var actual = porId[pid];
        if (idx < 0 || !actual) return null;
        var cap = limpio(actual.capitulo);
        var ini = idx;
        var fin = idx;
        while (ini > 0 && porId[visibles[ini - 1]] && limpio(porId[visibles[ini - 1]].capitulo) === cap) ini--;
        while (fin < visibles.length - 1 && porId[visibles[fin + 1]] && limpio(porId[visibles[fin + 1]].capitulo) === cap) fin++;
        var ids = visibles.slice(ini, fin + 1);
        function primero(campo) {
            for (var i = 0; i < ids.length; i++) {
                var v = limpio(porId[ids[i]] && porId[ids[i]][campo]);
                if (v) return v;
            }
            return "";
        }
        return {
            capitulo: cap,
            enunciado: primero("enunciado"),
            libreto: primero("libreto_encuestador"),
            nota: primero("nota_tabulacion"),
            ids: ids,
            posicion: idx - ini + 1,
            total: ids.length,
            primera: idx === ini
        };
    }

    function renderCabeceraSeccion(sec, ctx, compacta) {
        if (!sec || (!sec.capitulo && !sec.enunciado && !sec.libreto && !sec.nota)) return "";
        var capHtml = sec.capitulo
            ? '<div class="cq-seccion-capitulo text-primary fw-bold text-uppercase mb-2" style="font-size:19px;letter-spacing:.3px;">' + esc(reemplazarMunicipio(sec.capitulo, ctx)) +
              (sec.total > 1 ? ' <span class="text-muted">· ' + sec.posicion + ' de ' + sec.total + '</span>' : '') + '</div>'
            : '';
        if (compacta) {
            return '<div class="cq-seccion-compacta mb-3">' + capHtml + '</div>';
        }
        return (
            '<div class="cq-seccion-cabecera p-3 mb-3 rounded" style="background:#F8FAFC;border:2px solid #D0D5DD;">' +
                capHtml +
                (sec.enunciado ? '<div class="cq-seccion-enunciado mb-3" style="font-size:28px;line-height:1.3;font-weight:800;color:#0A2248;">' + esc(reemplazarMunicipio(sec.enunciado, ctx)) + '</div>' : '') +
                renderLibreto(sec.libreto, ctx) +
                (sec.nota && !(ctx && ctx.publico)
                    ? '<div class="cq-nota-box p-2 rounded small" style="background:#EFF6FF;border:1px solid #93C5FD;color:#1E3A8A;"><strong><i class="fas fa-chart-bar me-1"></i>Nota de análisis:</strong> ' + esc(reemplazarMunicipio(sec.nota, ctx)) + '</div>'
                    : '') +
            '</div>'
        );
    }

    function renderPreguntaCard(def, pregunta, evalState, respPregunta, ctx, uiState, seccion) {
        ctx = ctx || {};
        uiState = uiState || {};
        var pid = parseInt(pregunta.id, 10);
        var tipo = String(pregunta.tipo_pregunta || "");
        var numeral = pregunta.numeral ? (esc(pregunta.numeral) + ". ") : "";
        var titulo = reemplazarMunicipio(pregunta.texto_pregunta || "", ctx);
        var propio = function (campo) {
            var v = limpio(pregunta[campo]);
            return seccion && v === limpio(seccion[campo === "libreto_encuestador" ? "libreto" : campo === "nota_tabulacion" ? "nota" : campo]) ? "" : v;
        };
        var libretoHtml = renderLibreto(propio("libreto_encuestador"), ctx);
        var enunciadoPropio = propio("enunciado");

        var badgeFiltro = parseInt(pregunta.es_filtro || 0, 10) === 1 && !ctx.publico
            ? '<span class="badge bg-warning text-dark ms-2" style="font-size:14px;">Pregunta filtro</span>'
            : '';

        var headerHtml =
            '<div class="cq-pregunta-header mb-3">' +
                (!seccion && pregunta.capitulo ? '<div class="text-primary fw-bold small text-uppercase mb-1">' + esc(reemplazarMunicipio(pregunta.capitulo, ctx)) + '</div>' : '') +
                (enunciadoPropio ? '<div class="p-2 mb-2 rounded bg-light border text-secondary" style="font-size:16px;">' + esc(reemplazarMunicipio(enunciadoPropio, ctx)) + '</div>' : '') +
                libretoHtml +
                '<h4 class="cq-pregunta-titulo" style="font-size:22px;font-weight:800;color:#101828;">' +
                    numeral + esc(titulo) +
                    (parseInt(pregunta.obligatoria != null ? pregunta.obligatoria : 1, 10) === 1 ? ' <span class="text-danger">*</span>' : '') +
                    badgeFiltro +
                '</h4>' +
            '</div>';

        var bodyHtml = "";

        if (tipo === "Grilla") {
            var plId = evalState.plantilla_efectiva && evalState.plantilla_efectiva[pid];
            var pl = (def.plantillas && plId) ? (def.plantillas[plId] || def.plantillas[String(plId)]) : null;
            if (!pl) {
                return "";
            }
            var libretoPlHtml = pl.libreto_encuestador ? renderLibreto(pl.libreto_encuestador, ctx) : "";
            var filas = pl.filas || [];
            var columnas = pl.columnas || [];
            var respGrilla = (respPregunta && respPregunta.grilla) ? respPregunta.grilla : {};
            var colsActivas = (evalState.columnas_activas_por_fila && evalState.columnas_activas_por_fila[pid])
                ? evalState.columnas_activas_por_fila[pid]
                : {};

            var tieneOpcionesLargas = columnas.some(function (c) { return (c.opciones || []).length >= 6; });
            var btnTarjetaFull = tieneOpcionesLargas
                ? '<div class="mb-3"><button type="button" class="btn btn-outline-primary fw-bold cq-btn-tarjeta-emociones" data-pregunta="' + pid + '" style="min-height:48px;font-size:17px;"><i class="fas fa-expand me-2"></i>Mostrar tarjeta de opciones a pantalla completa</button></div>'
                : '';

            var fIdxActual = uiState.filaActivaPorPregunta && uiState.filaActivaPorPregunta[pid] != null
                ? parseInt(uiState.filaActivaPorPregunta[pid], 10)
                : 0;
            if (fIdxActual < 0 || fIdxActual >= filas.length) fIdxActual = 0;

            var usarTarjetas = pl.presentacion !== "tabla" || (typeof window !== "undefined" && window.innerWidth < 768);

            if (usarTarjetas && filas.length > 0) {
                var filaActual = filas[fIdxActual];
                var fid = parseInt(filaActual.id, 10);
                var activasEnFila = colsActivas[fid] || [];
                var bloqueada = evalState.filas_bloqueadas && evalState.filas_bloqueadas[pid] && evalState.filas_bloqueadas[pid].indexOf(fid) !== -1;

                var colsHtml = "";
                columnas.forEach(function (col) {
                    var cid = parseInt(col.id, 10);
                    if (activasEnFila.indexOf(cid) === -1) return;

                    var elegidas = (respGrilla[fid] && respGrilla[fid][cid]) ? respGrilla[fid][cid] : [];
                    var esMult = col.tipo_respuesta === "multiple";

                    colsHtml += '<div class="cq-grilla-col-block mb-3 pt-3 border-top">';
                    if (col.libreto_encuestador) {
                        colsHtml += renderLibreto(col.libreto_encuestador, ctx);
                    }
                    colsHtml += '<div class="fw-bold mb-2" style="font-size:19px;">' + esc(col.titulo) + (esMult ? ' <small class="text-muted">(Puede escoger varias)</small>' : '') + '</div>';
                    colsHtml += '<div class="d-grid gap-2">';

                    (col.opciones || []).forEach(function (co) {
                        var coId = parseInt(co.id, 10);
                        var marcada = elegidas.indexOf(coId) !== -1;
                        colsHtml +=
                            '<button type="button" class="cq-grilla-opt-btn text-start fw-bold p-3 rounded d-flex justify-content-between align-items-center' + (marcada ? ' active' : '') + '" ' +
                                'data-pregunta="' + pid + '" data-fila="' + fid + '" data-columna="' + cid + '" data-opcion="' + coId + '" data-multiple="' + (esMult ? '1' : '0') + '" data-limite="' + (col.limite_multiple || 1) + '" ' +
                                'style="min-height:56px;font-size:18px;background:' + (marcada ? '#EBF3FF' : '#FFFFFF') + ';border:2px solid ' + (marcada ? '#2D63BD' : '#98A2B3') + ';color:#101828;">' +
                                '<span>' + esc(co.texto) + '</span>' +
                                '<i class="fas ' + (marcada ? (esMult ? 'fa-check-square text-primary' : 'fa-check-circle text-primary') : (esMult ? 'fa-square text-muted' : 'fa-circle text-muted')) + '"></i>' +
                            '</button>';
                    });
                    colsHtml += '</div></div>';
                });

                if (bloqueada) {
                    var colBloqueante = columnas.filter(function (c) {
                        return parseInt(c.condicion_columna_id, 10) > 0 && activasEnFila.indexOf(parseInt(c.id, 10)) === -1;
                    })[0];
                    var msgB = (colBloqueante && colBloqueante.mensaje_bloqueo) ? colBloqueante.mensaje_bloqueo : "No hay más preguntas para esta fila. Pase a la siguiente.";
                    colsHtml +=
                        '<div class="p-3 rounded my-2" style="background:#F2F4F7;border:2px solid #D0D5DD;color:#344054;font-size:18px;font-weight:700;">' +
                            '<i class="fas fa-forward me-2"></i>' + esc(msgB) +
                        '</div>';
                }

                var filaCompleta = bloqueada || activasEnFila.every(function (cid) {
                    var el = respGrilla[fid] && respGrilla[fid][cid];
                    return el && el.length > 0;
                });
                var sentido = uiState.animarFila === "atras" ? "cq-slide-atras" : uiState.animarFila === "adelante" ? "cq-slide-adelante" : "";
                uiState.animarFila = null;

                bodyHtml =
                    libretoPlHtml +
                    btnTarjetaFull +
                    '<div class="cq-candidato-card ' + sentido + ' p-4 rounded" style="background:#F8FAFC;border:2px solid #D0D5DD;">' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="badge bg-primary" style="font-size:15px;">' + (fIdxActual + 1) + ' de ' + filas.length + '</span>' +
                        '</div>' +
                        '<h5 style="font-size:24px;font-weight:800;color:#0A2248;margin-bottom:4px;">' + esc(filaActual.etiqueta) + '</h5>' +
                        (filaActual.subtitulo ? '<div class="text-muted mb-2" style="font-size:16px;">' + esc(filaActual.subtitulo) + '</div>' : '') +
                        colsHtml +
                        '<div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">' +
                            '<button type="button" class="btn btn-outline-secondary fw-bold cq-grilla-prev-fila" data-pregunta="' + pid + '"' + (fIdxActual === 0 ? ' disabled' : '') + ' style="min-height:50px;font-size:17px;">' +
                                '<i class="fas fa-arrow-left me-1"></i> Anterior' +
                            '</button>' +
                            '<button type="button" class="btn btn-primary fw-bold cq-grilla-next-fila" data-pregunta="' + pid + '"' + ((fIdxActual >= filas.length - 1 || !filaCompleta) ? ' disabled' : '') + ' style="min-height:50px;font-size:17px;">' +
                                'Siguiente <i class="fas fa-arrow-right ms-1"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>';
            } else {
                var ths = '<th>Candidato / Ítem</th>';
                columnas.forEach(function (c) {
                    ths += '<th>' + esc(c.titulo) + '</th>';
                });
                var trs = '';
                filas.forEach(function (fl) {
                    var fid = parseInt(fl.id, 10);
                    var activasEnFila = colsActivas[fid] || [];
                    trs += '<tr><td class="fw-bold" style="font-size:17px;">' + esc(fl.etiqueta) + '</td>';
                    columnas.forEach(function (c) {
                        var cid = parseInt(c.id, 10);
                        if (activasEnFila.indexOf(cid) === -1) {
                            trs += '<td class="bg-light text-muted"><em>' + esc(c.mensaje_bloqueo || "No aplica") + '</em></td>';
                            return;
                        }
                        var elegidas = (respGrilla[fid] && respGrilla[fid][cid]) ? respGrilla[fid][cid] : [];
                        var btns = '<div class="d-flex flex-wrap gap-2">';
                        (c.opciones || []).forEach(function (co) {
                            var coId = parseInt(co.id, 10);
                            var marcada = elegidas.indexOf(coId) !== -1;
                            btns +=
                                '<button type="button" class="btn ' + (marcada ? 'btn-primary fw-bold' : 'btn-outline-secondary') + ' cq-grilla-opt-btn" ' +
                                    'data-pregunta="' + pid + '" data-fila="' + fid + '" data-columna="' + cid + '" data-opcion="' + coId + '" data-multiple="' + (c.tipo_respuesta === "multiple" ? '1' : '0') + '" data-limite="' + (c.limite_multiple || 1) + '" ' +
                                    'style="min-height:46px;font-size:16px;">' +
                                    esc(co.texto) +
                                '</button>';
                        });
                        btns += '</div>';
                        trs += '<td>' + btns + '</td>';
                    });
                    trs += '</tr>';
                });

                bodyHtml =
                    libretoPlHtml +
                    btnTarjetaFull +
                    '<div class="table-responsive"><table class="table table-bordered align-middle"><thead><tr>' + ths + '</tr></thead><tbody>' + trs + '</tbody></table></div>';
            }
        } else if (tipo === "Lista_desde_grilla") {
            var items = (evalState.listas_calculadas && evalState.listas_calculadas[pid]) ? evalState.listas_calculadas[pid] : [];
            var selFila = (respPregunta && respPregunta.lista && respPregunta.lista.fila_id) ? parseInt(respPregunta.lista.fila_id, 10) : 0;
            var selOpt = (respPregunta && respPregunta.lista && respPregunta.lista.opcion_id) ? parseInt(respPregunta.lista.opcion_id, 10) : 0;

            bodyHtml = '<div class="d-grid gap-2">';
            items.forEach(function (it) {
                var esFila = it.tipo_item === "fila";
                var idVal = esFila ? parseInt(it.fila_id, 10) : parseInt(it.opcion_id, 10);
                var marcada = esFila ? (selFila === idVal) : (selOpt === idVal);
                bodyHtml +=
                    '<button type="button" class="cq-lista-opt-btn text-start fw-bold p-3 rounded d-flex justify-content-between align-items-center' + (marcada ? ' active' : '') + '" ' +
                        'data-pregunta="' + pid + '" data-tipo-item="' + esc(it.tipo_item) + '" data-id="' + idVal + '" ' +
                        'style="min-height:56px;font-size:18px;background:' + (marcada ? '#EBF3FF' : '#FFFFFF') + ';border:2px solid ' + (marcada ? '#2D63BD' : '#98A2B3') + ';color:#101828;">' +
                        '<span>' + esc(it.etiqueta) + (it.subtitulo ? ' <small class="text-muted">(' + esc(it.subtitulo) + ')</small>' : '') + '</span>' +
                        '<i class="fas ' + (marcada ? 'fa-check-circle text-primary' : 'fa-circle text-muted') + '"></i>' +
                    '</button>';
            });
            bodyHtml += '</div>';
        } else if (pregunta.opciones && pregunta.opciones.length > 0) {
            var elegidasNorm = (respPregunta && respPregunta.opciones) ? respPregunta.opciones : [];
            var limMult = Math.max(1, parseInt(pregunta.limite_respuesta_multiple || 1, 10));
            var esMultPreg = limMult > 1 || tipo === "Preguntas_Cardinales";

            bodyHtml = '<div class="d-grid gap-2">';
            pregunta.opciones.forEach(function (op) {
                var oid = parseInt(op.id, 10);
                var marcada = elegidasNorm.indexOf(oid) !== -1;
                bodyHtml +=
                    '<button type="button" class="cq-normal-opt-btn text-start fw-bold p-3 rounded d-flex justify-content-between align-items-center' + (marcada ? ' active' : '') + '" ' +
                        'data-pregunta="' + pid + '" data-opcion="' + oid + '" data-multiple="' + (esMultPreg ? '1' : '0') + '" data-limite="' + limMult + '" ' +
                        'style="min-height:56px;font-size:18px;background:' + (marcada ? '#EBF3FF' : '#FFFFFF') + ';border:2px solid ' + (marcada ? '#2D63BD' : '#98A2B3') + ';color:#101828;">' +
                        '<span>' + esc(op.texto) + '</span>' +
                        '<i class="fas ' + (marcada ? (esMultPreg ? 'fa-check-square text-primary' : 'fa-check-circle text-primary') : (esMultPreg ? 'fa-square text-muted' : 'fa-circle text-muted')) + '"></i>' +
                    '</button>';
            });
            bodyHtml += '</div>';
        } else {
            var txtVal = (respPregunta && respPregunta.texto) ? respPregunta.texto : "";
            bodyHtml =
                '<textarea class="form-control cq-texto-input" data-pregunta="' + pid + '" rows="3" ' +
                'style="font-size:18px;border:2px solid #98A2B3;border-radius:12px;" placeholder="Escriba la respuesta aquí...">' +
                esc(txtVal) +
                '</textarea>';
        }

        return (
            '<div class="cq-pregunta-card p-4 mb-4 rounded bg-white" data-pregunta-id="' + pid + '" style="border:2px solid #D0D5DD;box-shadow:0 8px 24px rgba(16,24,40,.05);">' +
                headerHtml +
                bodyHtml +
            '</div>'
        );
    }

    function renderLista(def, visibles, evalState, respuestas, ctx, uiState) {
        var porId = {};
        (def.preguntas || []).forEach(function (p) { porId[parseInt(p.id, 10)] = p; });
        var html = "";
        visibles.forEach(function (pid) {
            var pDef = porId[pid];
            if (!pDef) return;
            var sec = seccionDe(def, pid, visibles);
            if (sec && sec.primera && sec.total >= 1) {
                html += renderCabeceraSeccion(sec, ctx, false);
            }
            var rPreg = respuestas[pid] || { pregunta_id: pid, opciones: [], texto: "", grilla: {}, lista: null };
            html += renderPreguntaCard(def, pDef, evalState, rPreg, ctx, uiState, sec);
        });
        return html;
    }

    function instalarTarjetaCompleta() {
        if (typeof document === "undefined" || root.__cqTarjetaCompleta) return;
        root.__cqTarjetaCompleta = true;
        var css = document.createElement("style");
        css.textContent =
            ".cq-pregunta-card.cq-fullscreen{position:fixed;inset:0;z-index:3000;overflow:auto;margin:0!important;border-radius:0!important;padding:20px!important;}" +
            ".cq-pregunta-card.cq-fullscreen .cq-grilla-opt-btn{min-height:72px!important;font-size:22px!important;}" +
            "@keyframes cqEntraDer{from{opacity:0;transform:translateX(60px)}to{opacity:1;transform:none}}" +
            "@keyframes cqEntraIzq{from{opacity:0;transform:translateX(-60px)}to{opacity:1;transform:none}}" +
            ".cq-candidato-card.cq-slide-adelante{animation:cqEntraDer .28s ease-out}" +
            ".cq-candidato-card.cq-slide-atras{animation:cqEntraIzq .28s ease-out}";
        document.head.appendChild(css);
        document.addEventListener("click", function (e) {
            var btn = e.target.closest ? e.target.closest(".cq-btn-tarjeta-emociones") : null;
            if (!btn) return;
            var card = btn.closest(".cq-pregunta-card");
            if (!card) return;
            var activa = card.classList.toggle("cq-fullscreen");
            btn.innerHTML = activa
                ? '<i class="fas fa-compress me-2"></i>Volver a la vista normal'
                : '<i class="fas fa-expand me-2"></i>Mostrar tarjeta de opciones a pantalla completa';
        });
        var temporizadorSiguiente = null;
        document.addEventListener("click", function (e) {
            var opt = e.target.closest ? e.target.closest(".cq-grilla-opt-btn") : null;
            if (!opt || opt.getAttribute("data-multiple") === "1" || !opt.closest(".cq-candidato-card")) return;
            var pid = opt.getAttribute("data-pregunta");
            clearTimeout(temporizadorSiguiente);
            temporizadorSiguiente = setTimeout(function () {
                var sig = document.querySelector('.cq-grilla-next-fila[data-pregunta="' + pid + '"]');
                if (sig && !sig.disabled) sig.click();
            }, 450);
        });
        document.addEventListener("keydown", function (e) {
            if (e.key !== "Escape") return;
            var abierta = document.querySelector(".cq-pregunta-card.cq-fullscreen");
            if (abierta) abierta.classList.remove("cq-fullscreen");
        });
    }

    function vincularEventos(host) {
        var $ = root.jQuery;
        var ns = ".cqMotor";
        $(document).off(ns);
        function entrada(pid) {
            if (!host.respuestas[pid]) {
                host.respuestas[pid] = { pregunta_id: pid, opciones: [], texto: "", grilla: {}, lista: null };
            }
            return host.respuestas[pid];
        }
        function cambio() {
            if (host.guardar) host.guardar();
            host.render();
        }

        $(document).on("click" + ns, ".cq-normal-opt-btn", function () {
            var $b = $(this);
            var pid = parseInt($b.data("pregunta"), 10);
            var oid = parseInt($b.data("opcion"), 10);
            var esMult = String($b.data("multiple")) === "1";
            var limite = parseInt($b.data("limite") || 1, 10);
            var e = entrada(pid);
            if (!esMult) {
                e.opciones = [oid];
            } else {
                var i = e.opciones.indexOf(oid);
                if (i !== -1) {
                    e.opciones.splice(i, 1);
                } else {
                    if (e.opciones.length >= limite) {
                        host.aviso("Puede elegir máximo " + limite + " opciones en esta pregunta.");
                        return;
                    }
                    e.opciones.push(oid);
                }
            }
            cambio();
        });

        $(document).on("click" + ns, ".cq-lista-opt-btn", function () {
            var $b = $(this);
            var pid = parseInt($b.data("pregunta"), 10);
            var idVal = parseInt($b.data("id"), 10);
            var e = entrada(pid);
            if (String($b.data("tipo-item")) === "fila") {
                e.lista = { fila_id: idVal, opcion_id: null };
                e.opciones = [];
            } else {
                e.lista = { fila_id: null, opcion_id: idVal };
                e.opciones = [idVal];
            }
            cambio();
        });

        $(document).on("click" + ns, ".cq-grilla-opt-btn", function () {
            var $b = $(this);
            var pid = parseInt($b.data("pregunta"), 10);
            var fid = parseInt($b.data("fila"), 10);
            var cid = parseInt($b.data("columna"), 10);
            var coId = parseInt($b.data("opcion"), 10);
            var esMult = String($b.data("multiple")) === "1";
            var limite = parseInt($b.data("limite") || 1, 10);
            var e = entrada(pid);
            if (!e.grilla[fid]) e.grilla[fid] = {};
            if (!Array.isArray(e.grilla[fid][cid])) e.grilla[fid][cid] = [];
            if (!esMult) {
                e.grilla[fid][cid] = [coId];
            } else {
                var arr = e.grilla[fid][cid];
                var i = arr.indexOf(coId);
                if (i !== -1) {
                    arr.splice(i, 1);
                } else {
                    if (arr.length >= limite) {
                        host.aviso("Puede escoger máximo " + limite + " opciones en esta columna.");
                        return;
                    }
                    arr.push(coId);
                }
            }
            cambio();
        });

        $(document).on("click" + ns, ".cq-grilla-goto-fila", function () {
            host.uiState.filaActivaPorPregunta[parseInt($(this).data("pregunta"), 10)] = parseInt($(this).data("fila-idx"), 10);
            host.render();
        });
        $(document).on("click" + ns, ".cq-grilla-prev-fila", function () {
            var pid = parseInt($(this).data("pregunta"), 10);
            var cur = host.uiState.filaActivaPorPregunta[pid] || 0;
            if (cur > 0) {
                host.uiState.filaActivaPorPregunta[pid] = cur - 1;
                host.uiState.animarFila = "atras";
                host.render();
            }
        });
        $(document).on("click" + ns, ".cq-grilla-next-fila", function () {
            var pid = parseInt($(this).data("pregunta"), 10);
            host.uiState.filaActivaPorPregunta[pid] = (host.uiState.filaActivaPorPregunta[pid] || 0) + 1;
            host.uiState.animarFila = "adelante";
            host.render();
        });
        $(document).on("input" + ns + " change" + ns, ".cq-texto-input", function () {
            entrada(parseInt($(this).data("pregunta"), 10)).texto = $(this).val() || "";
            if (host.guardar) host.guardar();
            if (host.alEscribir) host.alEscribir();
        });
    }

    instalarTarjetaCompleta();

    root.MotorCuestionarioUI = {
        esc: esc,
        reemplazarMunicipio: reemplazarMunicipio,
        renderLibreto: renderLibreto,
        renderCierreFiltro: renderCierreFiltro,
        renderPreguntaCard: renderPreguntaCard,
        renderLista: renderLista,
        seccionDe: seccionDe,
        renderCabeceraSeccion: renderCabeceraSeccion,
        vincularEventos: vincularEventos
    };
})(typeof self !== "undefined" ? self : this);
