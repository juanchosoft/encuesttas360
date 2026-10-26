(function (root, factory) {
    if (typeof module === "object" && module.exports) {
        module.exports = factory();
    } else {
        root.MotorCuestionario = factory();
    }
})(typeof self !== "undefined" ? self : this, function () {
    "use strict";

    var MOTOR_VERSION = "1.0.0";

    function toInt(val) {
        var n = parseInt(val, 10);
        return isNaN(n) ? 0 : n;
    }

    function normalizarEntradaPregunta(pid, item) {
        var opciones = [];
        if (Array.isArray(item.opciones)) {
            item.opciones.forEach(function (opId) {
                var v = toInt(opId);
                if (v > 0 && opciones.indexOf(v) === -1) {
                    opciones.push(v);
                }
            });
        } else if (toInt(item.opcion_id) > 0) {
            opciones.push(toInt(item.opcion_id));
        }

        var texto = item.texto != null ? String(item.texto).trim() : "";

        var grilla = {};
        if (item.grilla && typeof item.grilla === "object") {
            if (Array.isArray(item.grilla)) {
                item.grilla.forEach(function (cell) {
                    if (!cell || typeof cell !== "object") return;
                    var fid = toInt(cell.fila_id);
                    var cid = toInt(cell.columna_id);
                    var cOpts = [];
                    if (Array.isArray(cell.opciones)) {
                        cell.opciones.forEach(function (coId) {
                            var v = toInt(coId);
                            if (v > 0 && cOpts.indexOf(v) === -1) {
                                cOpts.push(v);
                            }
                        });
                    } else if (toInt(cell.opcion_id) > 0) {
                        cOpts.push(toInt(cell.opcion_id));
                    }
                    if (fid > 0 && cid > 0 && cOpts.length > 0) {
                        if (!grilla[fid]) grilla[fid] = {};
                        grilla[fid][cid] = cOpts;
                    }
                });
            } else {
                Object.keys(item.grilla).forEach(function (kFila) {
                    var fid = toInt(kFila);
                    var rowObj = item.grilla[kFila];
                    if (fid <= 0 || !rowObj || typeof rowObj !== "object") return;
                    Object.keys(rowObj).forEach(function (kCol) {
                        var cid = toInt(kCol);
                        var colVal = rowObj[kCol];
                        var cOpts = [];
                        if (Array.isArray(colVal)) {
                            colVal.forEach(function (coId) {
                                var v = toInt(coId);
                                if (v > 0 && cOpts.indexOf(v) === -1) {
                                    cOpts.push(v);
                                }
                            });
                        } else {
                            var v = toInt(colVal);
                            if (v > 0) cOpts.push(v);
                        }
                        if (fid > 0 && cid > 0 && cOpts.length > 0) {
                            if (!grilla[fid]) grilla[fid] = {};
                            grilla[fid][cid] = cOpts;
                        }
                    });
                });
            }
        }

        var lista = null;
        if (item.lista && typeof item.lista === "object") {
            var lf = toInt(item.lista.fila_id) > 0 ? toInt(item.lista.fila_id) : null;
            var lo = toInt(item.lista.opcion_id) > 0 ? toInt(item.lista.opcion_id) : null;
            if (lf !== null || lo !== null) {
                lista = { fila_id: lf, opcion_id: lo };
            }
        }

        return {
            pregunta_id: pid,
            opciones: opciones,
            texto: texto,
            grilla: grilla,
            lista: lista
        };
    }

    function indexarRespuestas(respuestasRaw) {
        var map = {};
        if (!respuestasRaw) return map;
        if (Array.isArray(respuestasRaw)) {
            respuestasRaw.forEach(function (v) {
                if (v && typeof v === "object" && toInt(v.pregunta_id) > 0) {
                    var pid = toInt(v.pregunta_id);
                    map[pid] = normalizarEntradaPregunta(pid, v);
                }
            });
        } else if (typeof respuestasRaw === "object") {
            Object.keys(respuestasRaw).forEach(function (k) {
                var v = respuestasRaw[k];
                if (!v || typeof v !== "object") return;
                var pid = toInt(v.pregunta_id) > 0 ? toInt(v.pregunta_id) : toInt(k);
                if (pid > 0) {
                    map[pid] = normalizarEntradaPregunta(pid, v);
                }
            });
        }
        return map;
    }

    function limiteColumna(c) {
        return Math.max(1, toInt(c.limite_multiple != null ? c.limite_multiple : 1));
    }

    function valorNumericoTexto(txt) {
        return /^-?\d{1,8}(\.\d+)?$/.test(txt) ? parseFloat(txt) : null;
    }

    function sanearRespuestas(def, respMap) {
        var columnasPorId = {};
        var plantillas = (def && def.plantillas) ? def.plantillas : {};
        Object.keys(plantillas).forEach(function (k) {
            (plantillas[k].columnas || []).forEach(function (c) {
                var validas = [];
                (c.opciones || []).forEach(function (co) {
                    validas.push(toInt(co.id));
                });
                columnasPorId[toInt(c.id)] = {
                    validas: validas,
                    multiple: c.tipo_respuesta === "multiple",
                    limite: limiteColumna(c)
                };
            });
        });

        ((def && def.preguntas) || []).forEach(function (p) {
            var pid = toInt(p.id);
            if (!respMap[pid]) return;
            var validas = [];
            (p.opciones || []).forEach(function (op) {
                validas.push(toInt(op.id));
            });
            var elegidas = respMap[pid].opciones.filter(function (oid) {
                return validas.indexOf(oid) !== -1;
            });
            var lim = Math.max(1, toInt(p.limite_respuesta_multiple != null ? p.limite_respuesta_multiple : 1));
            respMap[pid].opciones = elegidas.slice(0, lim);

            var grilla = {};
            Object.keys(respMap[pid].grilla).forEach(function (kf) {
                var fid = toInt(kf);
                Object.keys(respMap[pid].grilla[kf]).forEach(function (kc) {
                    var cid = toInt(kc);
                    var info = columnasPorId[cid];
                    if (!info) return;
                    var ok = respMap[pid].grilla[kf][kc].filter(function (coId) {
                        return info.validas.indexOf(coId) !== -1;
                    }).slice(0, info.multiple ? info.limite : 1);
                    if (ok.length > 0) {
                        if (!grilla[fid]) grilla[fid] = {};
                        grilla[fid][cid] = ok;
                    }
                });
            });
            respMap[pid].grilla = grilla;
        });
        return respMap;
    }

    function obtenerPlantillaPorId(def, plId) {
        if (!def || !def.plantillas || !plId) return null;
        return def.plantillas[plId] || def.plantillas[String(plId)] || null;
    }

    function resolverPlantillaEfectiva(def, pregunta, ctx) {
        var codMun = ctx && ctx.codigo_municipio ? String(ctx.codigo_municipio).trim() : "";
        var variantes = pregunta && pregunta.variantes_municipio && typeof pregunta.variantes_municipio === "object"
            ? pregunta.variantes_municipio
            : {};
        var varKeys = Object.keys(variantes);

        if (varKeys.length > 0) {
            if (!codMun || variantes[codMun] == null) {
                return null;
            }
            var vPlId = toInt(variantes[codMun]);
            return obtenerPlantillaPorId(def, vPlId);
        }

        var plId = toInt(pregunta.tbl_grilla_plantilla_id);
        if (plId <= 0) return null;
        return obtenerPlantillaPorId(def, plId);
    }

    function preguntaEstaCompleta(def, pDef, rItem, plEfectivaId, colsActivasFila, listaItems) {
        var obligatoria = toInt(pDef.obligatoria != null ? pDef.obligatoria : 1) === 1;
        if (!obligatoria) return true;
        if (!rItem) return false;

        var tipo = String(pDef.tipo_pregunta || "");

        if (tipo === "Grilla") {
            if (!plEfectivaId) return true;
            var pl = obtenerPlantillaPorId(def, plEfectivaId);
            if (!pl) return true;

            var colById = {};
            (pl.columnas || []).forEach(function (c) {
                colById[toInt(c.id)] = c;
            });

            var filas = pl.filas || [];
            for (var i = 0; i < filas.length; i++) {
                var fid = toInt(filas[i].id);
                var activas = (colsActivasFila && colsActivasFila[fid]) ? colsActivasFila[fid] : [];
                for (var j = 0; j < activas.length; j++) {
                    var cid = toInt(activas[j]);
                    var cDef = colById[cid];
                    if (cDef && toInt(cDef.obligatoria != null ? cDef.obligatoria : 1) === 1) {
                        var chosen = (rItem.grilla && rItem.grilla[fid] && rItem.grilla[fid][cid]) ? rItem.grilla[fid][cid] : [];
                        if (!chosen.length) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        if (tipo === "Lista_desde_grilla") {
            if (!listaItems || !listaItems.length) return true;
            var lf = rItem.lista && rItem.lista.fila_id ? toInt(rItem.lista.fila_id) : 0;
            var lo = rItem.lista && rItem.lista.opcion_id ? toInt(rItem.lista.opcion_id) : 0;
            for (var li = 0; li < listaItems.length; li++) {
                if (lf > 0 && toInt(listaItems[li].fila_id) === lf) return true;
                if (lf <= 0 && lo > 0 && toInt(listaItems[li].opcion_id) === lo) return true;
            }
            return false;
        }

        if (pDef.opciones && pDef.opciones.length > 0) {
            return Array.isArray(rItem.opciones) && rItem.opciones.length > 0;
        }

        return String(rItem.texto || "").trim() !== "";
    }

    function evaluar(def, respuestasRaw, ctx) {
        ctx = ctx || {};
        var preguntas = (def && def.preguntas) ? def.preguntas : [];
        var respMap = sanearRespuestas(def, indexarRespuestas(respuestasRaw));

        var pregById = {};
        var posPorId = {};
        preguntas.forEach(function (p, i) {
            pregById[toInt(p.id)] = p;
            posPorId[toInt(p.id)] = i;
        });

        var visibles = [];
        var omitidas = [];
        var plantillaEfectivaPorPregunta = {};
        var filasBloqueadasPorPregunta = {};
        var columnasActivasPorFila = {};
        var listasCalculadas = {};

        var terminado = false;
        var terminadoPorFiltro = false;
        var terminadoEnPreguntaId = null;
        var terminadoOpcionId = null;
        var mensajeCierre = (def && def.ficha && def.ficha.mensaje_cierre_defecto)
            ? def.ficha.mensaje_cierre_defecto
            : "Muchas gracias por su tiempo. Hemos terminado la encuesta.";

        var saltoActivoHastaPreguntaId = null;

        for (var idx = 0; idx < preguntas.length; idx++) {
            var p = preguntas[idx];
            var pid = toInt(p.id);

            if (terminado) {
                omitidas.push(pid);
                continue;
            }

            if (saltoActivoHastaPreguntaId !== null) {
                if (pid !== saltoActivoHastaPreguntaId) {
                    omitidas.push(pid);
                    continue;
                }
                saltoActivoHastaPreguntaId = null;
            }

            if (toInt(p.visible_si_pregunta_id) > 0 && toInt(p.visible_si_opcion_id) > 0) {
                var depPid = toInt(p.visible_si_pregunta_id);
                var depOid = toInt(p.visible_si_opcion_id);
                if (visibles.indexOf(depPid) === -1) {
                    omitidas.push(pid);
                    continue;
                }
                var respDep = (respMap[depPid] && respMap[depPid].opciones) ? respMap[depPid].opciones : [];
                if (respDep.indexOf(depOid) === -1) {
                    omitidas.push(pid);
                    continue;
                }
            }

            var tipo = String(p.tipo_pregunta || "");

            if (tipo === "Grilla") {
                var plantilla = resolverPlantillaEfectiva(def, p, ctx);
                if (!plantilla) {
                    omitidas.push(pid);
                    continue;
                }
                plantillaEfectivaPorPregunta[pid] = toInt(plantilla.id);
                visibles.push(pid);

                if (!respMap[pid]) {
                    respMap[pid] = { pregunta_id: pid, opciones: [], texto: "", grilla: {}, lista: null };
                }
                var respGrilla = respMap[pid].grilla;
                filasBloqueadasPorPregunta[pid] = [];
                columnasActivasPorFila[pid] = {};

                var filas = plantilla.filas || [];
                var columnas = plantilla.columnas || [];

                filas.forEach(function (f) {
                    var fid = toInt(f.id);
                    var activasFila = [];
                    var bloqueadaFila = false;

                    columnas.forEach(function (c) {
                        var cid = toInt(c.id);
                        var condColId = toInt(c.condicion_columna_id);
                        var condOptId = toInt(c.condicion_opcion_id);

                        if (condColId > 0 && condOptId > 0) {
                            var chosenInCond = (respGrilla[fid] && respGrilla[fid][condColId]) ? respGrilla[fid][condColId] : [];
                            if (chosenInCond.indexOf(condOptId) === -1) {
                                if (chosenInCond.length > 0) {
                                    bloqueadaFila = true;
                                }
                                if (respGrilla[fid]) {
                                    delete respGrilla[fid][cid];
                                }
                                return;
                            }
                        }
                        activasFila.push(cid);
                    });

                    columnasActivasPorFila[pid][fid] = activasFila;
                    if (bloqueadaFila) {
                        filasBloqueadasPorPregunta[pid].push(fid);
                    }
                });
                continue;
            }

            if (tipo === "Lista_desde_grilla") {
                var origenPid = toInt(p.lista_origen_pregunta_id);
                var origenColId = toInt(p.lista_origen_columna_id);
                var origenOptId = toInt(p.lista_origen_opcion_id);

                if (origenPid <= 0 || visibles.indexOf(origenPid) === -1 || !plantillaEfectivaPorPregunta[origenPid]) {
                    omitidas.push(pid);
                    continue;
                }

                var plIdOrigen = plantillaEfectivaPorPregunta[origenPid];
                var plOrigen = obtenerPlantillaPorId(def, plIdOrigen);
                if (!plOrigen) {
                    omitidas.push(pid);
                    continue;
                }

                var colEfectivaId = origenColId;
                var optEfectivaId = origenOptId;
                var pregOrigenDef = pregById[origenPid] || null;
                var plBaseOrigenId = pregOrigenDef ? toInt(pregOrigenDef.tbl_grilla_plantilla_id) : 0;
                if (plBaseOrigenId <= 0 && origenColId > 0 && def.plantillas) {
                    Object.keys(def.plantillas).forEach(function (kPl) {
                        if (plBaseOrigenId > 0) return;
                        var plCand = def.plantillas[kPl];
                        (plCand.columnas || []).forEach(function (cCand) {
                            if (plBaseOrigenId <= 0 && toInt(cCand.id) === origenColId) plBaseOrigenId = toInt(plCand.id);
                        });
                    });
                }

                if (plBaseOrigenId > 0 && plBaseOrigenId !== plIdOrigen) {
                    var plBase = obtenerPlantillaPorId(def, plBaseOrigenId);
                    if (plBase) {
                        var ordenColBuscado = null;
                        var ordenOptBuscado = null;
                        (plBase.columnas || []).some(function (cb) {
                            if (toInt(cb.id) !== origenColId) return false;
                            ordenColBuscado = toInt(cb.orden);
                            (cb.opciones || []).some(function (ob) {
                                if (toInt(ob.id) !== origenOptId) return false;
                                ordenOptBuscado = toInt(ob.orden);
                                return true;
                            });
                            return true;
                        });
                        if (ordenColBuscado !== null) {
                            (plOrigen.columnas || []).some(function (cv) {
                                if (toInt(cv.orden) !== ordenColBuscado) return false;
                                colEfectivaId = toInt(cv.id);
                                if (ordenOptBuscado !== null) {
                                    (cv.opciones || []).some(function (ov) {
                                        if (toInt(ov.orden) !== ordenOptBuscado) return false;
                                        optEfectivaId = toInt(ov.id);
                                        return true;
                                    });
                                }
                                return true;
                            });
                        }
                    }
                }

                var excluidosFilaIds = [];
                (p.exclusiones_pregunta_ids || []).forEach(function (exPid) {
                    var ep = toInt(exPid);
                    var elegidoFila = (respMap[ep] && respMap[ep].lista && respMap[ep].lista.fila_id)
                        ? toInt(respMap[ep].lista.fila_id)
                        : 0;
                    if (elegidoFila <= 0) return;
                    var enLista = (listasCalculadas[ep] || []).some(function (itEx) {
                        return itEx.tipo_item === "fila" && toInt(itEx.fila_id) === elegidoFila;
                    });
                    if (enLista) {
                        excluidosFilaIds.push(elegidoFila);
                    }
                });

                var respGrillaOrigen = (respMap[origenPid] && respMap[origenPid].grilla) ? respMap[origenPid].grilla : {};
                var candidatosDisponibles = [];
                var totalBase = 0;
                (plOrigen.filas || []).forEach(function (fl) {
                    var fid = toInt(fl.id);
                    if (colEfectivaId > 0 && optEfectivaId > 0) {
                        var marcadas = (respGrillaOrigen[fid] && respGrillaOrigen[fid][colEfectivaId]) ? respGrillaOrigen[fid][colEfectivaId] : [];
                        if (marcadas.indexOf(optEfectivaId) === -1) return;
                    }
                    totalBase++;
                    if (excluidosFilaIds.indexOf(fid) !== -1) return;
                    candidatosDisponibles.push({
                        tipo_item: "fila",
                        fila_id: fid,
                        opcion_id: null,
                        etiqueta: String(fl.etiqueta || ""),
                        subtitulo: fl.subtitulo != null ? fl.subtitulo : null,
                        foto: fl.foto != null ? fl.foto : null
                    });
                });

                var tieneFijas = (p.opciones || []).some(function (of) { return !!toInt(of.es_opcion_fija); });
                if (candidatosDisponibles.length === 0 && !(totalBase > 0 && tieneFijas)) {
                    var siVacia = p.si_lista_vacia || "omitir";
                    if (siVacia === "terminar") {
                        visibles.push(pid);
                        terminado = true;
                        terminadoEnPreguntaId = pid;
                    } else {
                        omitidas.push(pid);
                    }
                    continue;
                }

                var opcionesFijas = [];
                (p.opciones || []).forEach(function (opFija) {
                    opcionesFijas.push({
                        tipo_item: "opcion_fija",
                        fila_id: null,
                        opcion_id: toInt(opFija.id),
                        etiqueta: String(opFija.texto || ""),
                        accion: opFija.accion || "continuar",
                        salto_destino_pregunta_id: opFija.salto_destino_pregunta_id != null ? toInt(opFija.salto_destino_pregunta_id) : null,
                        mensaje_terminar: opFija.mensaje_terminar != null ? opFija.mensaje_terminar : null
                    });
                });

                listasCalculadas[pid] = candidatosDisponibles.concat(opcionesFijas);
                visibles.push(pid);

                var elegidoFilaLista = (respMap[pid] && respMap[pid].lista) ? toInt(respMap[pid].lista.fila_id) : 0;
                var filaListaValida = candidatosDisponibles.some(function (cd) {
                    return cd.fila_id === elegidoFilaLista;
                });
                var elegidoOptFija = (!filaListaValida && respMap[pid] && respMap[pid].lista && respMap[pid].lista.opcion_id)
                    ? toInt(respMap[pid].lista.opcion_id)
                    : 0;
                if (elegidoOptFija > 0) {
                    (p.opciones || []).forEach(function (opDef) {
                        if (toInt(opDef.id) === elegidoOptFija) {
                            var acc = opDef.accion || "continuar";
                            if (acc === "terminar") {
                                terminado = true;
                                terminadoPorFiltro = toInt(p.es_filtro) === 1;
                                terminadoEnPreguntaId = pid;
                                terminadoOpcionId = toInt(opDef.id);
                                if (opDef.mensaje_terminar) {
                                    mensajeCierre = String(opDef.mensaje_terminar);
                                }
                            } else if (acc === "saltar" && toInt(opDef.salto_destino_pregunta_id) > 0) {
                                var destLista = toInt(opDef.salto_destino_pregunta_id);
                                if (posPorId[destLista] !== undefined && posPorId[destLista] > idx) {
                                    saltoActivoHastaPreguntaId = destLista;
                                }
                            }
                        }
                    });
                }
                continue;
            }

            visibles.push(pid);

            var respOpciones = (respMap[pid] && respMap[pid].opciones) ? respMap[pid].opciones : [];
            if (respOpciones.length > 0 && p.opciones && p.opciones.length > 0) {
                for (var oi = 0; oi < p.opciones.length; oi++) {
                    var opDef = p.opciones[oi];
                    var oid = toInt(opDef.id);
                    if (respOpciones.indexOf(oid) !== -1) {
                        var acc = opDef.accion || "continuar";
                        if (acc === "terminar") {
                            terminado = true;
                            terminadoPorFiltro = toInt(p.es_filtro) === 1;
                            terminadoEnPreguntaId = pid;
                            terminadoOpcionId = oid;
                            if (opDef.mensaje_terminar) {
                                mensajeCierre = String(opDef.mensaje_terminar);
                            }
                            break;
                        } else if (acc === "saltar" && toInt(opDef.salto_destino_pregunta_id) > 0) {
                            var destinoId = toInt(opDef.salto_destino_pregunta_id);
                            if (posPorId[destinoId] !== undefined && posPorId[destinoId] > idx) {
                                saltoActivoHastaPreguntaId = destinoId;
                            }
                        }
                    }
                }
            }
        }

        var siguientePreguntaId = null;
        if (!terminado) {
            for (var vi = 0; vi < visibles.length; vi++) {
                var vPid = visibles[vi];
                var pDef = pregById[vPid];
                if (!preguntaEstaCompleta(def, pDef, respMap[vPid] || null, plantillaEfectivaPorPregunta[vPid] || null, columnasActivasPorFila[vPid] || {}, listasCalculadas[vPid] || [])) {
                    siguientePreguntaId = vPid;
                    break;
                }
            }
        }

        return {
            visibles: visibles,
            omitidas: omitidas,
            plantilla_efectiva: plantillaEfectivaPorPregunta,
            filas_bloqueadas: filasBloqueadasPorPregunta,
            columnas_activas_por_fila: columnasActivasPorFila,
            listas_calculadas: listasCalculadas,
            terminado: terminado,
            terminado_por_filtro: terminadoPorFiltro,
            terminado_en_pregunta_id: terminadoEnPreguntaId,
            terminado_opcion_id: terminadoOpcionId,
            mensaje_cierre: mensajeCierre,
            siguiente_pregunta_id: siguientePreguntaId
        };
    }

    function validar(def, respuestasRaw, ctx) {
        ctx = ctx || {};
        var evalRes = evaluar(def, respuestasRaw, ctx);
        var respMap = sanearRespuestas(def, indexarRespuestas(respuestasRaw));
        var pregById = {};
        ((def && def.preguntas) || []).forEach(function (p) {
            pregById[toInt(p.id)] = p;
        });

        var errores = [];
        var normalizadas = [];
        var filasRespuestasPlanas = [];

        evalRes.visibles.forEach(function (pid) {
            var pDef = pregById[pid];
            var rItem = respMap[pid] || { pregunta_id: pid, opciones: [], texto: "", grilla: {}, lista: null };
            var tipo = String(pDef.tipo_pregunta || "");
            var obligatoria = toInt(pDef.obligatoria != null ? pDef.obligatoria : 1) === 1;
            var etiquetaHumana = String((pDef.numeral ? (pDef.numeral + ". ") : "") + (pDef.texto_pregunta || "")).trim();

            if (tipo === "Grilla") {
                var plId = evalRes.plantilla_efectiva[pid] || 0;
                var pl = obtenerPlantillaPorId(def, plId);
                if (!pl) return;

                var colById = {};
                var validOptsByCol = {};
                (pl.columnas || []).forEach(function (c) {
                    var cid = toInt(c.id);
                    colById[cid] = c;
                    validOptsByCol[cid] = {};
                    (c.opciones || []).forEach(function (co) {
                        validOptsByCol[cid][toInt(co.id)] = co;
                    });
                });

                var grillaNorm = [];
                (pl.filas || []).forEach(function (f) {
                    var fid = toInt(f.id);
                    var activas = (evalRes.columnas_activas_por_fila[pid] && evalRes.columnas_activas_por_fila[pid][fid])
                        ? evalRes.columnas_activas_por_fila[pid][fid]
                        : [];

                    activas.forEach(function (cid) {
                        var cDef = colById[cid];
                        var rawChosen = (rItem.grilla && rItem.grilla[fid] && rItem.grilla[fid][cid]) ? rItem.grilla[fid][cid] : [];
                        var validChosen = [];
                        rawChosen.forEach(function (coId) {
                            var v = toInt(coId);
                            if (validOptsByCol[cid] && validOptsByCol[cid][v] && validChosen.indexOf(v) === -1) {
                                validChosen.push(v);
                            }
                        });

                        if (cDef.tipo_respuesta !== "multiple" && validChosen.length > 1) {
                            validChosen = [validChosen[0]];
                        } else if (cDef.tipo_respuesta === "multiple") {
                            var lim = limiteColumna(cDef);
                            if (validChosen.length > lim) {
                                validChosen = validChosen.slice(0, lim);
                            }
                        }

                        if (validChosen.length === 0 && obligatoria && toInt(cDef.obligatoria != null ? cDef.obligatoria : 1) === 1) {
                            errores.push({
                                pregunta_id: pid,
                                fila_id: fid,
                                columna_id: cid,
                                mensaje: "Por favor responda «" + cDef.titulo + "» para " + f.etiqueta + "."
                            });
                        }

                        if (validChosen.length > 0) {
                            grillaNorm.push({
                                fila_id: fid,
                                columna_id: cid,
                                opciones: validChosen
                            });
                            validChosen.forEach(function (coId) {
                                var coObj = validOptsByCol[cid][coId];
                                filasRespuestasPlanas.push({
                                    tbl_pregunta_id: pid,
                                    tbl_opcion_respuesta_id: null,
                                    tbl_grilla_fila_id: fid,
                                    tbl_grilla_columna_id: cid,
                                    tbl_grilla_columna_opcion_id: coId,
                                    valor_numerico: coObj.valor_numerico != null ? Number(coObj.valor_numerico) : null,
                                    respuesta_texto: coObj.texto,
                                    texto_snapshot: f.etiqueta + " | " + cDef.titulo + ": " + coObj.texto
                                });
                            });
                        }
                    });
                });

                normalizadas.push({
                    pregunta_id: pid,
                    opciones: [],
                    texto: "",
                    grilla: grillaNorm,
                    lista: null
                });
                return;
            }

            if (tipo === "Lista_desde_grilla") {
                var itemsLista = evalRes.listas_calculadas[pid] || [];
                if (itemsLista.length === 0) return;

                var validFilaIds = {};
                var validOptFijaIds = {};
                itemsLista.forEach(function (it) {
                    if (it.tipo_item === "fila" && toInt(it.fila_id) > 0) {
                        validFilaIds[toInt(it.fila_id)] = it;
                    } else if (it.tipo_item === "opcion_fija" && toInt(it.opcion_id) > 0) {
                        validOptFijaIds[toInt(it.opcion_id)] = it;
                    }
                });

                var chosenFila = (rItem.lista && toInt(rItem.lista.fila_id) > 0) ? toInt(rItem.lista.fila_id) : null;
                var chosenOpt = (rItem.lista && toInt(rItem.lista.opcion_id) > 0) ? toInt(rItem.lista.opcion_id) : null;

                if (chosenFila !== null && !validFilaIds[chosenFila]) chosenFila = null;
                if (chosenOpt !== null && !validOptFijaIds[chosenOpt]) chosenOpt = null;
                if (chosenFila !== null && chosenOpt !== null) chosenOpt = null;

                if (chosenFila === null && chosenOpt === null) {
                    if (obligatoria) {
                        errores.push({
                            pregunta_id: pid,
                            mensaje: "Por favor seleccione una opción en «" + etiquetaHumana + "»."
                        });
                    }
                    return;
                }

                normalizadas.push({
                    pregunta_id: pid,
                    opciones: chosenOpt !== null ? [chosenOpt] : [],
                    texto: "",
                    grilla: [],
                    lista: {
                        fila_id: chosenFila,
                        opcion_id: chosenOpt
                    }
                });

                if (chosenFila !== null) {
                    var flObj = validFilaIds[chosenFila];
                    filasRespuestasPlanas.push({
                        tbl_pregunta_id: pid,
                        tbl_opcion_respuesta_id: null,
                        tbl_grilla_fila_id: chosenFila,
                        tbl_grilla_columna_id: null,
                        tbl_grilla_columna_opcion_id: null,
                        valor_numerico: null,
                        respuesta_texto: flObj.etiqueta,
                        texto_snapshot: etiquetaHumana + ": " + flObj.etiqueta
                    });
                } else {
                    var opObj = validOptFijaIds[chosenOpt];
                    filasRespuestasPlanas.push({
                        tbl_pregunta_id: pid,
                        tbl_opcion_respuesta_id: chosenOpt,
                        tbl_grilla_fila_id: null,
                        tbl_grilla_columna_id: null,
                        tbl_grilla_columna_opcion_id: null,
                        valor_numerico: null,
                        respuesta_texto: null,
                        texto_snapshot: etiquetaHumana + ": " + opObj.etiqueta
                    });
                }
                return;
            }

            var validOpts = {};
            var hasValidOpts = false;
            (pDef.opciones || []).forEach(function (opDef) {
                validOpts[toInt(opDef.id)] = opDef;
                hasValidOpts = true;
            });

            if (hasValidOpts) {
                var chosenOpts = [];
                (rItem.opciones || []).forEach(function (oid) {
                    var v = toInt(oid);
                    if (validOpts[v] && chosenOpts.indexOf(v) === -1) {
                        chosenOpts.push(v);
                    }
                });
                var lim = Math.max(1, toInt(pDef.limite_respuesta_multiple || 1));
                if (chosenOpts.length > lim) {
                    chosenOpts = chosenOpts.slice(0, lim);
                }

                if (chosenOpts.length === 0) {
                    if (obligatoria) {
                        errores.push({
                            pregunta_id: pid,
                            mensaje: "Por favor responda la pregunta «" + etiquetaHumana + "»."
                        });
                    }
                    return;
                }

                normalizadas.push({
                    pregunta_id: pid,
                    opciones: chosenOpts,
                    texto: "",
                    grilla: [],
                    lista: null
                });

                chosenOpts.forEach(function (oid) {
                    var opObj = validOpts[oid];
                    filasRespuestasPlanas.push({
                        tbl_pregunta_id: pid,
                        tbl_opcion_respuesta_id: oid,
                        tbl_grilla_fila_id: null,
                        tbl_grilla_columna_id: null,
                        tbl_grilla_columna_opcion_id: null,
                        valor_numerico: opObj.valor_numerico != null ? Number(opObj.valor_numerico) : null,
                        respuesta_texto: null,
                        texto_snapshot: etiquetaHumana + ": " + opObj.texto
                    });
                });
            } else {
                var txt = String(rItem.texto || "").trim();
                if (txt === "") {
                    if (obligatoria) {
                        errores.push({
                            pregunta_id: pid,
                            mensaje: "Por favor escriba su respuesta en «" + etiquetaHumana + "»."
                        });
                    }
                    return;
                }

                normalizadas.push({
                    pregunta_id: pid,
                    opciones: [],
                    texto: txt,
                    grilla: [],
                    lista: null
                });

                var numVal = valorNumericoTexto(txt);
                filasRespuestasPlanas.push({
                    tbl_pregunta_id: pid,
                    tbl_opcion_respuesta_id: null,
                    tbl_grilla_fila_id: null,
                    tbl_grilla_columna_id: null,
                    tbl_grilla_columna_opcion_id: null,
                    valor_numerico: numVal,
                    respuesta_texto: txt,
                    texto_snapshot: etiquetaHumana + ": " + txt
                });
            }
        });

        var estadoIntento = evalRes.terminado_por_filtro ? "terminado_filtro" : "completo";
        var validoParaPago = evalRes.terminado_por_filtro ? 0 : 1;

        return {
            valido: errores.length === 0,
            errores: errores,
            estado_intento: estadoIntento,
            valido_para_pago: validoParaPago,
            terminado_en_pregunta_id: evalRes.terminado_en_pregunta_id,
            terminado_opcion_id: evalRes.terminado_opcion_id,
            mensaje_cierre: evalRes.mensaje_cierre,
            respuestas_normalizadas: normalizadas,
            filas_bd: filasRespuestasPlanas,
            evaluacion: evalRes
        };
    }

    return {
        MOTOR_VERSION: MOTOR_VERSION,
        indexarRespuestas: indexarRespuestas,
        resolverPlantillaEfectiva: resolverPlantillaEfectiva,
        evaluar: evaluar,
        validar: validar
    };
});
