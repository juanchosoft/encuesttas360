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

    // --- Certificaciones (Resultados Encuestas, reutiliza el módulo resultados.sondeos
    //     tal como lo hace hoy el código real: certificaciones.php usa el mismo
    //     legacy_id 90 que resultados_sondeos.php) ---
    'certificacionget' => 'resultados.sondeos.view',
    'certificaciondetalle' => 'resultados.sondeos.view',
    'certificacionbyvotante' => 'resultados.sondeos.view',
    'certificacionsave' => ['resultados.sondeos.create', 'resultados.sondeos.update'],

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
];
