<?php

/**
 * Mapa central `op` (rqst.php) -> clave(s) de permiso requerida(s).
 * Usado por PermissionGate::authorizeOperation() al inicio de rqst.php.
 *
 * Un valor array significa "requiere CUALQUIERA de estas claves" (ej. los
 * endpoints `*save` que sirven tanto de crear como de editar).
 *
 * Cualquier `op` que NO aparezca aquí se permite sin bloquear (ver
 * PermissionGate) — toda operación nueva debe agregarse explícitamente.
 */

return [
    // --- Usuarios ---
    'pms_usrget' => 'configuracion.usuarios.view',
    'pms_usravailable' => 'configuracion.usuarios.view',
    'pms_usrsave' => ['configuracion.usuarios.create', 'configuracion.usuarios.update'],
    'pms_usrpermission' => 'configuracion.usuarios.manage',
    'pms_usrsavepermission' => 'configuracion.usuarios.manage',

    // --- Configuración general ---
    'pms_getconf' => 'configuracion.general.view',
    'pms_getconfJS' => 'configuracion.general.view',
    'pms_confsave' => ['configuracion.general.create', 'configuracion.general.update', 'configuracion.general.manage'],

    // --- Clientes ---
    'clienteget' => 'configuracion.clientes.view',
    'clientebuscar' => 'configuracion.clientes.view',
    'clientesave' => ['configuracion.clientes.create', 'configuracion.clientes.update'],
    'clientedelete' => 'configuracion.clientes.delete',

    // --- Partidos Políticos ---
    'partidopoliticoget' => 'politica.partidos.view',
    'partidopoliticosave' => ['politica.partidos.create', 'politica.partidos.update'],

    // --- Personal Político (participantes) ---
    'participanteget' => 'politica.personal_politico.view',
    'participantesave' => ['politica.personal_politico.create', 'politica.personal_politico.update'],

    // --- Votantes ---
    'votantesget' => 'politica.votantes.view',
    'votantesavailable' => 'politica.votantes.view',
    'votantessave' => ['politica.votantes.create', 'politica.votantes.update'],
    'votantesdelete' => 'politica.votantes.delete',

    // --- Preguntas (Configuración Cuestionarios) ---
    'preguntaget' => 'encuestas.preguntas.view',
    'bancopreguntaget' => ['encuestas.banco_preguntas.view', 'encuestas.preguntas.view', 'estudios.sondeos.view'],
    'bancopreguntacategorias' => ['encuestas.banco_preguntas.view', 'encuestas.preguntas.view', 'estudios.sondeos.view'],
    'bancopreguntasave' => ['encuestas.banco_preguntas.create', 'encuestas.banco_preguntas.update'],
    'bancopreguntadelete' => 'encuestas.banco_preguntas.delete',
    'bancopreguntadesdepregunta' => ['encuestas.banco_preguntas.create', 'encuestas.preguntas.view'],
    'bancopreguntadesdesondeo' => ['encuestas.banco_preguntas.create', 'estudios.sondeos.view'],
    'preguntasave' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntas_upload' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntasavebatch' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntaenunciadosave' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntaenunciadogroupsave' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntaenunciadoselected' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntahabilitadosave' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntareasignar' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntaupdatenumeraladicional' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntarenamecapitulo' => ['encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'preguntadelete' => 'encuestas.preguntas.delete',
    'preguntareglassave' => ['encuestas.preguntas.reglas', 'encuestas.preguntas.update', 'encuestas.preguntas.create'],
    'preguntavotoescalonadosave' => ['encuestas.preguntas.reglas', 'encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'cuestionariodefinicionget' => ['encuestas.preguntas.view', 'politica.votantes.view', 'politica.votantes.create', 'legacy.contestar_cuestionario.view', 'resultados.cuestionarios.view'],
    'cuestionariomapaget' => ['encuestas.preguntas.view', 'encuestas.preguntas.reglas'],
    'cuestionariovalidarconfig' => ['encuestas.preguntas.view', 'encuestas.preguntas.reglas'],
    'cuestionarioconfigget' => ['encuestas.preguntas.view', 'estudios.ficha_tecnica.view'],
    'cuestionarioconfigsave' => ['encuestas.preguntas.reglas', 'encuestas.preguntas.update', 'estudios.ficha_tecnica.update'],
    'zonasmuestreoget' => ['encuestas.preguntas.view', 'encuestas.cuotas.edit', 'politica.votantes.view', 'politica.votantes.create'],
    'zonasmuestreosave' => ['encuestas.cuotas.edit', 'encuestas.preguntas.update', 'estudios.ficha_tecnica.update'],
    'zonasmuestreodelete' => ['encuestas.cuotas.edit', 'encuestas.preguntas.delete'],
    'zonasmuestreocopiar' => ['encuestas.cuotas.edit', 'encuestas.preguntas.update'],
    'equivalenciasvotanteget' => ['encuestas.preguntas.view', 'encuestas.preguntas.reglas'],
    'equivalenciasvotantesave' => ['encuestas.preguntas.reglas', 'encuestas.preguntas.update'],
    'municipioscatalogoget' => ['encuestas.preguntas.view', 'estudios.grilla.view', 'encuestas.cuotas.edit', 'politica.votantes.view'],

    // --- Espacio Geográfico ---
    'espacioGeograficoget' => 'estudios.espacio_geografico.view',
    'espacioGeograficosave' => ['estudios.espacio_geografico.create', 'estudios.espacio_geografico.update'],
    'espacioGeograficoduplicate' => ['estudios.espacio_geografico.create', 'estudios.espacio_geografico.update'],
    'espacioGeograficodelete' => 'estudios.espacio_geografico.manage',

    // --- Ficha Técnica Encuesta ---
    'fichaTecnicaEncuestaget' => 'estudios.ficha_tecnica.view',
    'fichaTecnicaEncuestasave' => ['estudios.ficha_tecnica.create', 'estudios.ficha_tecnica.update'],
    'fichaTecnicaEncuestaduplicar' => ['estudios.ficha_tecnica.create', 'estudios.ficha_tecnica.update'],
    'fichaTecnicaEncuestaupdatetemas' => ['estudios.ficha_tecnica.create', 'estudios.ficha_tecnica.update'],
    'fichaTecnicaEncuestadelete' => 'estudios.ficha_tecnica.delete',

    // --- Sondeos ---
    'sondeoget' => 'estudios.sondeos.view',
    'sondeosave' => ['estudios.sondeos.create', 'estudios.sondeos.update'],
    'sondeotoggle' => ['estudios.sondeos.create', 'estudios.sondeos.update'],
    'sondeodelete' => 'estudios.sondeos.delete',

    // --- Preguntas Grilla ---
    'preguntasgrillaget' => 'estudios.preguntas_grilla.view',
    'preguntasgrillaobtenerconsubpreguntas' => 'estudios.preguntas_grilla.view',
    'preguntasgrillaporid' => 'estudios.preguntas_grilla.view',
    'preguntasgrillasave' => ['estudios.preguntas_grilla.create', 'estudios.preguntas_grilla.update'],
    'preguntasgrilladelete' => 'estudios.preguntas_grilla.delete',

    // --- Grilla ---
    'grillaget' => 'estudios.grilla.view',
    'grillasave' => ['estudios.grilla.create', 'estudios.grilla.update'],
    'grilladelete' => 'estudios.grilla.delete',
    'grillacandidatoguardarrespuestas' => ['estudios.grilla.create', 'estudios.grilla.update'],
    'grillacandidatoguardarpreguntasadicionales' => ['estudios.grilla.create', 'estudios.grilla.update'],
    'grillacandidatoverificarvotoduplicado' => 'estudios.grilla.view',
    'grillacandidatoresultadosentiemporeal' => 'estudios.grilla.view',
    'grillacandidatodemografia' => 'estudios.grilla.view',
    'grillaplantillaget' => ['estudios.grilla.view', 'encuestas.preguntas.view', 'encuestas.banco_preguntas.view'],
    'grillaplantilladetalle' => ['estudios.grilla.view', 'encuestas.preguntas.view'],
    'grillaplantillasave' => ['estudios.grilla.create', 'estudios.grilla.update'],
    'grillaplantilladelete' => 'estudios.grilla.delete',
    'grillaplantilladuplicar' => ['estudios.grilla.create', 'estudios.grilla.update'],
    'grillaplantillauso' => ['estudios.grilla.view', 'encuestas.preguntas.view'],
    'grillaparticipantesget' => ['estudios.grilla.view', 'politica.personal_politico.view'],

    // --- Fórmulas ---
    'formulasget' => 'estudios.formulas.view',
    'formulasgetbyid' => 'estudios.formulas.view',
    'formulassearch' => 'estudios.formulas.view',
    'formulassave' => ['estudios.formulas.create', 'estudios.formulas.update'],
    'formulasimport' => ['estudios.formulas.create', 'estudios.formulas.update'],
    'formulasdelete' => 'estudios.formulas.delete',

    // --- Análisis de Estudio ---
    'analisisestudioget' => 'analisis.estudio.view',
    'analisisestudiogetbyid' => 'analisis.estudio.view',
    'analisisestudiogetcandidatos' => 'analisis.estudio.view',
    'analisisestudiogetresultados' => 'analisis.estudio.view',
    'analisisestudiogetcalculos' => 'analisis.estudio.view',
    'analisisestudiobuscarexistente' => 'analisis.estudio.view',
    'analisisestudiosave' => ['analisis.estudio.create', 'analisis.estudio.update'],
    'analisisestudiodelete' => 'analisis.estudio.delete',

    // --- Dashboard (solo control de acceso, sin tocar lógica interna).
    //     Corrección: dashboard.php ya usaba en su código real los mismos
    //     IDs legacy 90/74 que resultados.sondeos/resultados.cuestionarios
    //     (no un permiso propio) — se preserva esa misma lógica OR. ---
    'dashboardgrillas' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardestadisticas' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardideologia' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardgenero' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardedad' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardanalisismes' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardtopcandidatos' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardingresos' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
    'dashboardgrillasestado' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],

    // --- Dashboard Resultados / Resultados Sondeos ---
    'respuestasondeogetsondeosdisp' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticascompletas' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticasgenerales' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticasporideologia' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticasporgenero' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticasporedad' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticasporingresos' => 'resultados.sondeos.view',
    'respuestasondeogetestadisticasporeducacion' => 'resultados.sondeos.view',

    'certificaciondetalle' => ['certificaciones.view', 'certificaciones.dashboard.view'],
    'certificacionaudiostream' => ['certificaciones.view', 'certificaciones.dashboard.view'],
    'certificacionget' => ['certificaciones.view', 'certificaciones.dashboard.view'],
    'certificacionbyvotante' => 'certificaciones.view',
    'certificacionsave' => ['politica.votantes.create', 'politica.votantes.update', 'resultados.sondeos.create', 'resultados.sondeos.update'],
    'certificacionaudioparte' => ['politica.votantes.create', 'politica.votantes.update', 'resultados.sondeos.create', 'resultados.sondeos.update'],
    'certificacionrevisarguardar' => 'certificaciones.revisar',
    'certificacionvalidaria' => 'certificaciones.validar_ia',
    'certificacionhistorial' => ['certificaciones.historial.view', 'certificaciones.dashboard.view'],
    'certificacionencuestasvinculadas' => 'certificaciones.view',
    'certificaciondashboardkpis' => 'certificaciones.dashboard.view',
    'certificaciondashboardlist' => 'certificaciones.dashboard.view',
    'certificaciondashboardexport' => 'certificaciones.dashboard.export',

    // --- Roles y Permisos ---
    'roleslist' => 'configuracion.roles.view',
    'roleget' => 'configuracion.roles.view',
    'rolepermissionscatalog' => 'configuracion.roles.view',
    'rolesave' => 'configuracion.roles.manage',
    'roledelete' => 'configuracion.roles.manage',

    // --- Informes IA ---
    'informeiaget' => 'ia.informes.view',
    'informeiaview' => 'ia.informes.view',
    'informeiadelete' => ['ia.informes.delete', 'ia.informes.manage'],

    // --- Encuestas / Cuestionarios / Respuestas ---
    'encuestaget' => ['estudios.ficha_tecnica.view', 'encuestas.preguntas.view'],
    'encuestasave' => ['estudios.ficha_tecnica.create', 'estudios.ficha_tecnica.update', 'encuestas.preguntas.create', 'encuestas.preguntas.update'],
    'encuestadelete' => ['estudios.ficha_tecnica.delete', 'encuestas.preguntas.delete'],
    'respuestasave' => ['legacy.contestar_cuestionario.create', 'legacy.contestar_cuestionario.update', 'politica.votantes.create', 'politica.votantes.update', 'resultados.cuestionarios.create'],
    'respuestaget' => ['resultados.cuestionarios.view', 'legacy.contestar_cuestionario.view'],
    'respuestadetalle' => ['resultados.cuestionarios.view', 'certificaciones.view', 'certificaciones.dashboard.view'],
    'cuestionarioresumenestados' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'cuestionarioresultadospregunta' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'cuestionarioresultadosgrilla' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'cuestionariovotoescalonado' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'cuestionarioresultadoscruce' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'cuestionariofiltroscruce' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'cuestionarioexport' => ['resultados.cuestionarios.view', 'certificaciones.dashboard.view'],
    'respuestadetallelegible' => ['resultados.cuestionarios.view', 'certificaciones.view', 'certificaciones.dashboard.view'],
    'votantesdisponiblesget' => ['legacy.contestar_cuestionario.view', 'politica.votantes.view', 'resultados.cuestionarios.view'],
    'estadisticascuestionario' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'votantesrespondieron' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'votantesnorespondieron' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'cuestionariofiltrosdashboard' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'cuestionariokpislistado' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'cuestionarioultimasrespuestasdt' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'votantesrespondierondt' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
    'votantesnorespondierondt' => ['resultados.cuestionarios.view', 'resultados.sondeos.view'],
];

