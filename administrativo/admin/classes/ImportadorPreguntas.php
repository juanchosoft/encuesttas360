<?php

require_once __DIR__ . '/XlsxEscritor.php';
require_once __DIR__ . '/XlsxLector.php';

class ImportadorPreguntas
{
    public const MAX_BYTES = 5 * 1024 * 1024;
    public const MAX_PREGUNTAS = 300;
    public const MAX_OPCIONES = 100;
    private const FILAS_PLANTILLA = 300;
    private const FILAS_PLANTILLA_OPCIONES = 1500;

    public const TIPOS = [
        'Sí / No' => 'Dicotomica',
        'Escoge una sola opción' => 'Seleccion_Multiple_unica_respuesta',
        'Escoge varias opciones' => 'Seleccion_Multiple_multiple_respuesta',
        'Escala de calificación' => 'Preguntas_Ordinales',
        'Nivel de satisfacción / intensidad' => 'Preguntas_Cardinales',
        'Tabla de candidatos' => 'Grilla',
        'Respuesta abierta' => 'Texto Libre',
    ];
    public const TIPO_NO_IMPORTABLE = ['Escoger de una tabla (voto escalonado)' => 'Lista_desde_grilla'];

    private const ALIAS_TIPOS = [
        'si no' => 'Dicotomica', 'dicotomica' => 'Dicotomica', 'si o no' => 'Dicotomica',
        'una sola opcion' => 'Seleccion_Multiple_unica_respuesta', 'unica' => 'Seleccion_Multiple_unica_respuesta',
        'seleccion multiple unica respuesta' => 'Seleccion_Multiple_unica_respuesta', 'seleccion multiple' => 'Seleccion_Multiple_unica_respuesta',
        'varias opciones' => 'Seleccion_Multiple_multiple_respuesta', 'multiple' => 'Seleccion_Multiple_multiple_respuesta',
        'seleccion multiple multiple respuesta' => 'Seleccion_Multiple_multiple_respuesta',
        'escala' => 'Preguntas_Ordinales', 'ordinal' => 'Preguntas_Ordinales', 'preguntas ordinales' => 'Preguntas_Ordinales',
        'cardinal' => 'Preguntas_Cardinales', 'preguntas cardinales' => 'Preguntas_Cardinales', 'satisfaccion' => 'Preguntas_Cardinales',
        'tabla' => 'Grilla', 'grilla' => 'Grilla', 'candidatos' => 'Grilla',
        'abierta' => 'Texto Libre', 'texto libre' => 'Texto Libre', 'texto' => 'Texto Libre',
        'voto escalonado' => 'Lista_desde_grilla', 'lista desde grilla' => 'Lista_desde_grilla',
    ];

    private const COLS_PREGUNTAS = [
        'codigo' => ['codigo', 'codigo de la pregunta', 'cod'],
        'capitulo' => ['capitulo', 'seccion'],
        'numeral' => ['numeral', 'numero'],
        'enunciado' => ['enunciado de la seccion', 'enunciado', 'instruccion de la seccion'],
        'pregunta' => ['pregunta', 'texto de la pregunta', 'texto pregunta'],
        'tipo' => ['tipo', 'tipo de pregunta'],
        'obligatoria' => ['obligatoria'],
        'filtro' => ['es filtro', 'filtro'],
        'max' => ['max selecciones', 'maximo de selecciones', 'maximo selecciones', 'limite'],
        'tabla' => ['tabla de candidatos', 'tabla', 'plantilla'],
        'libreto' => ['libreto del encuestador', 'libreto'],
        'nota' => ['nota de tabulacion', 'nota tabulacion'],
    ];
    private const COLS_OPCIONES = [
        'codigo' => ['codigo de la pregunta', 'codigo', 'cod'],
        'opcion' => ['opcion', 'texto de la opcion', 'respuesta'],
        'valor' => ['valor numerico', 'valor'],
        'cuenta' => ['cuenta como'],
        'accion' => ['accion'],
        'mensaje' => ['mensaje si termina', 'mensaje al terminar', 'mensaje'],
    ];

    public static function normalizar(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        $s = preg_replace('/[^a-z0-9]+/', ' ', $s);
        return trim($s);
    }

    private static function siNo(string $v, bool $defecto): ?bool
    {
        $n = self::normalizar($v);
        if ($n === '') {
            return $defecto;
        }
        if (in_array($n, ['si', 's', '1', 'true', 'x', 'yes'], true)) {
            return true;
        }
        if (in_array($n, ['no', 'n', '0', 'false'], true)) {
            return false;
        }
        return null;
    }

    public static function tipoDesdeTexto(string $texto): ?string
    {
        $n = self::normalizar($texto);
        if ($n === '') {
            return null;
        }
        foreach (array_merge(self::TIPOS, self::TIPO_NO_IMPORTABLE) as $label => $interno) {
            if ($n === self::normalizar($label) || $n === self::normalizar($interno)) {
                return $interno;
            }
        }
        return self::ALIAS_TIPOS[$n] ?? null;
    }

    public static function etiquetaTipo(string $interno): string
    {
        foreach (array_merge(self::TIPOS, self::TIPO_NO_IMPORTABLE) as $label => $i) {
            if ($i === $interno) {
                return $label;
            }
        }
        return $interno;
    }

    private static function mapearColumnas(array $encabezado, array $campos): array
    {
        $mapa = [];
        foreach ($encabezado as $idx => $txt) {
            $n = self::normalizar((string)$txt);
            if ($n === '') {
                continue;
            }
            foreach ($campos as $campo => $alias) {
                if (isset($mapa[$campo])) {
                    continue;
                }
                foreach ($alias as $a) {
                    if ($n === $a || strpos($n, $a . ' ') === 0) {
                        $mapa[$campo] = $idx;
                        continue 3;
                    }
                }
            }
        }
        return $mapa;
    }

    private static function extraerTabla(array $filas, array $campos, array $requeridos): ?array
    {
        $encabezadoFila = null;
        $mapa = [];
        $intentos = 0;
        foreach ($filas as $nFila => $fila) {
            if (++$intentos > 15) {
                break;
            }
            $m = self::mapearColumnas($fila, $campos);
            $faltan = array_diff($requeridos, array_keys($m));
            if (empty($faltan)) {
                $encabezadoFila = $nFila;
                $mapa = $m;
                break;
            }
        }
        if ($encabezadoFila === null) {
            return null;
        }
        $salida = [];
        foreach ($filas as $nFila => $fila) {
            if ($nFila <= $encabezadoFila) {
                continue;
            }
            $reg = ['fila' => $nFila];
            $vacia = true;
            foreach ($campos as $campo => $_) {
                $v = isset($mapa[$campo]) ? trim((string)($fila[$mapa[$campo]] ?? '')) : '';
                $reg[$campo] = $v;
                if ($v !== '') {
                    $vacia = false;
                }
            }
            if (!$vacia) {
                $salida[] = $reg;
            }
        }
        return ['filas' => $salida, 'mapa' => $mapa];
    }

    private static function leerCsv(string $ruta): array
    {
        $contenido = file_get_contents($ruta);
        if ($contenido === false || $contenido === '') {
            throw new RuntimeException('El archivo CSV está vacío.');
        }
        if (strncmp($contenido, "\xEF\xBB\xBF", 3) === 0) {
            $contenido = substr($contenido, 3);
        }
        if (!mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }
        $primera = strtok($contenido, "\n");
        $delim = substr_count($primera, ';') > substr_count($primera, ',') ? ';' : ',';
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $contenido);
        rewind($fh);
        $filas = [];
        $n = 0;
        while (($r = fgetcsv($fh, 0, $delim)) !== false) {
            $n++;
            if ($n > 5000) {
                break;
            }
            $filas[$n] = array_map(function ($c) {
                return trim((string)$c);
            }, $r);
        }
        fclose($fh);
        return $filas;
    }

    private static function tablasDesdeArchivo(string $ruta, string $nombreOriginal): array
    {
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $errores = [];

        if ($ext === 'csv') {
            $filas = self::leerCsv($ruta);
            $t = self::extraerTabla($filas, array_merge(self::COLS_PREGUNTAS, [
                'opcion' => self::COLS_OPCIONES['opcion'],
                'valor' => self::COLS_OPCIONES['valor'],
                'cuenta' => self::COLS_OPCIONES['cuenta'],
                'accion' => self::COLS_OPCIONES['accion'],
                'mensaje' => self::COLS_OPCIONES['mensaje'],
            ]), ['pregunta', 'tipo']);
            if ($t === null) {
                throw new RuntimeException('No se encontró la fila de encabezados. Debe tener al menos las columnas «Pregunta» y «Tipo».');
            }
            $preguntas = [];
            $opciones = [];
            $actual = null;
            $contador = 0;
            foreach ($t['filas'] as $f) {
                if ($f['pregunta'] !== '') {
                    $contador++;
                    $actual = $f['codigo'] !== '' ? $f['codigo'] : 'CSV' . $contador;
                    $preguntas[] = array_merge($f, ['codigo' => $actual]);
                }
                if ($f['opcion'] !== '' && $actual !== null) {
                    $opciones[] = ['fila' => $f['fila'], 'codigo' => ($f['pregunta'] === '' && $f['codigo'] !== '') ? $f['codigo'] : $actual, 'opcion' => $f['opcion'], 'valor' => $f['valor'], 'cuenta' => $f['cuenta'], 'accion' => $f['accion'], 'mensaje' => $f['mensaje']];
                }
            }
            return ['preguntas' => $preguntas, 'opciones' => $opciones, 'origen' => 'csv'];
        }

        if ($ext !== 'xlsx') {
            throw new RuntimeException('Formato no admitido. Suba un archivo Excel (.xlsx) o CSV (.csv).');
        }

        $hojas = XlsxLector::leer($ruta);
        $hojaP = null;
        $hojaO = null;
        foreach ($hojas as $nombre => $filas) {
            $n = self::normalizar($nombre);
            if ($n === 'preguntas') {
                $hojaP = $filas;
            } elseif ($n === 'opciones') {
                $hojaO = $filas;
            }
        }
        if ($hojaP === null) {
            throw new RuntimeException('No se encontró la hoja «Preguntas». Use la plantilla descargada desde el sistema.');
        }
        $tp = self::extraerTabla($hojaP, self::COLS_PREGUNTAS, ['pregunta', 'tipo']);
        if ($tp === null) {
            throw new RuntimeException('En la hoja «Preguntas» no se encontraron las columnas «Pregunta» y «Tipo». Use la plantilla descargada desde el sistema.');
        }
        $opciones = [];
        if ($hojaO !== null) {
            $to = self::extraerTabla($hojaO, self::COLS_OPCIONES, ['codigo', 'opcion']);
            if ($to === null) {
                throw new RuntimeException('En la hoja «Opciones» no se encontraron las columnas «Código de la pregunta» y «Opción».');
            }
            $opciones = $to['filas'];
        }
        return ['preguntas' => $tp['filas'], 'opciones' => $opciones, 'origen' => 'xlsx'];
    }

    private static function plantillasGrilla(PDO $pdo, DbConection $db): array
    {
        $st = $pdo->query("SELECT id, nombre FROM " . $db->getTable('tbl_grilla_plantillas') . " WHERE eliminado = 0 AND habilitado = 'si' ORDER BY nombre");
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[self::normalizar($r['nombre'])] = ['id' => (int)$r['id'], 'nombre' => $r['nombre']];
        }
        return $out;
    }

    private static function claveDuplicado(?string $numeral, string $texto): string
    {
        return self::normalizar((string)$numeral) . '|' . self::normalizar($texto);
    }

    public static function analizarArchivo(string $ruta, string $nombreOriginal, int $fichaId): array
    {
        $tablas = self::tablasDesdeArchivo($ruta, $nombreOriginal);

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stF = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id = :id AND (eliminado = 'no' OR eliminado IS NULL)");
            $stF->execute([':id' => $fichaId]);
            if (!$stF->fetchColumn()) {
                throw new RuntimeException('La encuesta seleccionada no existe.');
            }
            $plantillas = self::plantillasGrilla($pdo, $db);
            $stE = $pdo->prepare("SELECT numeral, texto_pregunta FROM " . $db->getTable('tbl_preguntas') . " WHERE tbl_ficha_tecnica_encuesta_id = :f AND eliminado = 0");
            $stE->execute([':f' => $fichaId]);
            $existentes = [];
            foreach ($stE->fetchAll(PDO::FETCH_ASSOC) as $e) {
                $existentes[self::claveDuplicado($e['numeral'], $e['texto_pregunta'])] = true;
            }
        } finally {
            $db->closeConect();
        }

        if (count($tablas['preguntas']) > self::MAX_PREGUNTAS) {
            throw new RuntimeException('El archivo trae más de ' . self::MAX_PREGUNTAS . ' preguntas. Divídalo en varios archivos.');
        }
        if (empty($tablas['preguntas'])) {
            throw new RuntimeException('El archivo no trae preguntas. Llene la hoja «Preguntas» a partir de la segunda fila.');
        }

        $opcionesPorCodigo = [];
        $generales = [];
        $codigosVistos = [];
        foreach ($tablas['preguntas'] as $p) {
            if ($p['codigo'] !== '') {
                $codigosVistos[self::normalizar($p['codigo'])] = true;
            }
        }
        foreach ($tablas['opciones'] as $o) {
            $k = self::normalizar($o['codigo']);
            if ($k === '' || !isset($codigosVistos[$k])) {
                $generales[] = ['tipo' => 'error', 'texto' => 'Hoja «Opciones», fila ' . $o['fila'] . ': el código «' . $o['codigo'] . '» no existe en la hoja «Preguntas».'];
                continue;
            }
            $opcionesPorCodigo[$k][] = $o;
        }

        $filas = [];
        $items = [];
        $codigosUsados = [];
        $clavesArchivo = [];
        $capituloPrevio = '';
        $enunciadoPrevio = '';
        $totalOpciones = 0;

        foreach ($tablas['preguntas'] as $p) {
            $msgs = [];
            $error = false;
            $err = function (string $t) use (&$msgs, &$error) {
                $msgs[] = ['tipo' => 'error', 'texto' => $t];
                $error = true;
            };
            $aviso = function (string $t) use (&$msgs) {
                $msgs[] = ['tipo' => 'aviso', 'texto' => $t];
            };

            $capitulo = $p['capitulo'] !== '' ? $p['capitulo'] : $capituloPrevio;
            $enunciado = $p['enunciado'] !== '' ? $p['enunciado'] : ($p['capitulo'] !== '' ? '' : $enunciadoPrevio);
            if ($p['capitulo'] !== '') {
                $capituloPrevio = $p['capitulo'];
                $enunciadoPrevio = $p['enunciado'];
            } elseif ($p['enunciado'] !== '') {
                $enunciadoPrevio = $p['enunciado'];
            }

            $texto = $p['pregunta'];
            $tipo = self::tipoDesdeTexto($p['tipo']);
            $estado = 'nueva';

            if ($texto === '') {
                $err('Falta el texto de la pregunta.');
            }
            if ($tipo === null) {
                $err($p['tipo'] === '' ? 'Falta el tipo de pregunta.' : 'Tipo «' . $p['tipo'] . '» no reconocido. Use uno de la lista desplegable.');
            }
            if (mb_strlen($capitulo) > 255) {
                $err('El capítulo supera los 255 caracteres.');
            }

            $codigoNorm = self::normalizar($p['codigo']);
            if ($codigoNorm !== '') {
                if (isset($codigosUsados[$codigoNorm])) {
                    $err('El código «' . $p['codigo'] . '» está repetido en la hoja «Preguntas».');
                }
                $codigosUsados[$codigoNorm] = true;
            }

            $opcs = $codigoNorm !== '' ? ($opcionesPorCodigo[$codigoNorm] ?? []) : [];
            $oblig = self::siNo($p['obligatoria'], true);
            $filtro = self::siNo($p['filtro'], false);
            if ($oblig === null) {
                $err('«Obligatoria» debe ser Sí o No.');
            }
            if ($filtro === null) {
                $err('«Es filtro» debe ser Sí o No.');
            }

            $plantillaId = null;
            $opcionesFinal = [];
            $limite = 1;

            if ($tipo === 'Lista_desde_grilla') {
                $estado = 'omitida';
                $aviso('Las preguntas de voto escalonado no se importan: créela a mano desde «Nueva pregunta».');
            } elseif ($tipo !== null) {
                if ($tipo === 'Grilla') {
                    $nombreT = self::normalizar($p['tabla']);
                    if ($nombreT === '') {
                        $aviso('No se indicó la tabla de candidatos: la pregunta se crea sin tabla; edítela y elija la tabla (o las tablas por municipio).');
                    } elseif (!isset($plantillas[$nombreT])) {
                        $err('La tabla «' . $p['tabla'] . '» no existe. Disponibles: ' . implode(', ', array_map(function ($x) {
                            return $x['nombre'];
                        }, $plantillas)) . '.');
                    } else {
                        $plantillaId = $plantillas[$nombreT]['id'];
                    }
                    if ($opcs) {
                        $aviso('Las opciones de una tabla de candidatos se ignoran: salen de la tabla.');
                    }
                } elseif ($tipo === 'Texto Libre') {
                    if ($opcs) {
                        $aviso('Una respuesta abierta no usa opciones: se ignoran.');
                    }
                } else {
                    if ($codigoNorm === '' && !in_array($tipo, ['Dicotomica', 'Preguntas_Ordinales'], true)) {
                        $err('Falta el código: lo necesita para unir la pregunta con sus opciones.');
                    }
                    foreach ($opcs as $o) {
                        $r = self::normalizarOpcion($o);
                        foreach ($r['errores'] as $t) {
                            $err('Hoja «Opciones», fila ' . $o['fila'] . ': ' . $t);
                        }
                        foreach ($r['avisos'] as $t) {
                            $aviso('Hoja «Opciones», fila ' . $o['fila'] . ': ' . $t);
                        }
                        if ($r['opcion'] !== null) {
                            $opcionesFinal[] = $r['opcion'];
                        }
                    }
                    if (count($opcionesFinal) > self::MAX_OPCIONES) {
                        $err('Tiene más de ' . self::MAX_OPCIONES . ' opciones.');
                    }
                    if (empty($opcionesFinal)) {
                        if ($tipo === 'Dicotomica') {
                            $opcionesFinal = [
                                ['texto_opcion' => 'Sí', 'valor_numerico' => null, 'cuenta_como' => 'si', 'accion' => 'continuar', 'mensaje_terminar' => null],
                                ['texto_opcion' => 'No', 'valor_numerico' => null, 'cuenta_como' => 'no', 'accion' => 'continuar', 'mensaje_terminar' => null],
                            ];
                            $aviso('Sin opciones: se crearon «Sí» y «No».');
                        } elseif ($tipo === 'Preguntas_Ordinales') {
                            for ($i = 1; $i <= 5; $i++) {
                                $opcionesFinal[] = ['texto_opcion' => (string)$i, 'valor_numerico' => (float)$i, 'cuenta_como' => 'ninguno', 'accion' => 'continuar', 'mensaje_terminar' => null];
                            }
                            $aviso('Sin opciones: se creó la escala de 1 a 5.');
                        } else {
                            $err('Necesita al menos 2 opciones en la hoja «Opciones».');
                        }
                    }
                    if (!empty($opcionesFinal)) {
                        if ($tipo === 'Dicotomica' && count($opcionesFinal) !== 2) {
                            $err('Una pregunta Sí / No debe tener exactamente 2 opciones (tiene ' . count($opcionesFinal) . ').');
                        } elseif ($tipo !== 'Dicotomica' && $tipo !== 'Preguntas_Ordinales' && count($opcionesFinal) < 2) {
                            $err('Necesita al menos 2 opciones.');
                        }
                    }
                    if ($tipo === 'Seleccion_Multiple_multiple_respuesta') {
                        $maxTxt = $p['max'];
                        if ($maxTxt === '') {
                            $limite = max(1, count($opcionesFinal));
                        } elseif (!ctype_digit($maxTxt) || (int)$maxTxt < 1 || (int)$maxTxt > max(1, count($opcionesFinal))) {
                            $err('«Máx. selecciones» debe ser un número entre 1 y el total de opciones (' . count($opcionesFinal) . ').');
                        } else {
                            $limite = (int)$maxTxt;
                        }
                    } elseif ($p['max'] !== '') {
                        $aviso('«Máx. selecciones» solo aplica a «Escoge varias opciones»: se ignora.');
                    }
                    $hayTerminar = false;
                    foreach ($opcionesFinal as $of) {
                        if ($of['accion'] === 'terminar') {
                            $hayTerminar = true;
                        }
                    }
                    if ($filtro && !$hayTerminar) {
                        $aviso('Está marcada como filtro pero ninguna opción termina la encuesta.');
                    }
                    if ($hayTerminar && !$filtro) {
                        $filtro = true;
                    }
                }
            }

            if ($texto !== '' && !$error && $estado === 'nueva') {
                $clave = self::claveDuplicado($p['numeral'], $texto);
                if (isset($existentes[$clave])) {
                    $estado = 'duplicada';
                    $aviso('Ya existe en la encuesta (mismo numeral y texto): no se agrega.');
                } elseif (isset($clavesArchivo[$clave])) {
                    $estado = 'duplicada';
                    $aviso('Está repetida dentro del archivo (mismo numeral y texto): solo se agrega la primera.');
                }
                $clavesArchivo[$clave] = true;
            }

            if ($error) {
                $estado = 'error';
            }

            $filas[] = [
                'fila' => $p['fila'],
                'codigo' => $p['codigo'],
                'numeral' => $p['numeral'],
                'capitulo' => $capitulo,
                'pregunta' => $texto,
                'tipo' => $tipo !== null ? self::etiquetaTipo($tipo) : $p['tipo'],
                'opciones' => count($opcionesFinal),
                'estado' => $estado,
                'mensajes' => $msgs,
            ];

            if ($estado === 'nueva') {
                $totalOpciones += count($opcionesFinal);
                $items[] = [
                    'texto_pregunta' => $texto,
                    'enunciado_pregunta' => $enunciado,
                    'numeral' => $p['numeral'],
                    'capitulo' => $capitulo,
                    'tipo_pregunta' => $tipo,
                    'limite_respuesta_multiple' => $limite,
                    'obligatoria' => $oblig ? 1 : 0,
                    'es_filtro' => $filtro ? 1 : 0,
                    'libreto_encuestador' => $p['libreto'],
                    'nota_tabulacion' => $p['nota'],
                    'tbl_grilla_plantilla_id' => $plantillaId,
                    'opciones' => $opcionesFinal,
                ];
            }
        }

        $cuenta = ['nueva' => 0, 'duplicada' => 0, 'omitida' => 0, 'error' => 0];
        $avisos = 0;
        foreach ($filas as $f) {
            $cuenta[$f['estado']]++;
            foreach ($f['mensajes'] as $m) {
                if ($m['tipo'] === 'aviso') {
                    $avisos++;
                }
            }
        }
        $erroresGenerales = count(array_filter($generales, function ($g) {
            return $g['tipo'] === 'error';
        }));

        return [
            'filas' => $filas,
            'generales' => $generales,
            'items' => $items,
            'resumen' => [
                'total' => count($filas),
                'nuevas' => $cuenta['nueva'],
                'duplicadas' => $cuenta['duplicada'],
                'omitidas' => $cuenta['omitida'],
                'con_error' => $cuenta['error'] + $erroresGenerales,
                'avisos' => $avisos,
                'opciones' => $totalOpciones,
            ],
            'puede_importar' => ($cuenta['error'] + $erroresGenerales) === 0 && $cuenta['nueva'] > 0,
        ];
    }

    private static function normalizarOpcion(array $o): array
    {
        $errores = [];
        $avisos = [];
        $texto = trim($o['opcion']);
        if ($texto === '') {
            return ['opcion' => null, 'errores' => $errores, 'avisos' => $avisos];
        }
        $valor = null;
        if ($o['valor'] !== '') {
            $v = str_replace(',', '.', $o['valor']);
            if (!is_numeric($v)) {
                $errores[] = 'el valor numérico «' . $o['valor'] . '» no es un número.';
            } else {
                $valor = (float)$v;
            }
        }
        $cuenta = 'ninguno';
        if ($o['cuenta'] !== '') {
            $c = self::normalizar($o['cuenta']);
            if (!in_array($c, ['ninguno', 'si', 'no', 'favorable', 'desfavorable'], true)) {
                $errores[] = '«Cuenta como» debe ser ninguno, sí, no, favorable o desfavorable.';
            } else {
                $cuenta = $c;
            }
        }
        $accion = 'continuar';
        if ($o['accion'] !== '') {
            $a = self::normalizar($o['accion']);
            if ($a === 'terminar') {
                $accion = 'terminar';
            } elseif ($a === 'saltar') {
                $avisos[] = 'los saltos entre preguntas se configuran a mano: la opción queda en «continuar».';
            } elseif ($a !== 'continuar') {
                $errores[] = '«Acción» debe ser continuar o terminar.';
            }
        }
        $mensaje = null;
        if ($accion === 'terminar') {
            $mensaje = $o['mensaje'] !== '' ? $o['mensaje'] : 'Gracias por su tiempo.';
            if ($o['mensaje'] === '') {
                $avisos[] = 'la opción termina la encuesta sin mensaje: se usó «Gracias por su tiempo.»';
            }
        } elseif ($o['mensaje'] !== '') {
            $avisos[] = 'el mensaje solo se usa cuando la acción es «terminar»: se ignora.';
        }
        return [
            'opcion' => ['texto_opcion' => $texto, 'valor_numerico' => $valor, 'cuenta_como' => $cuenta, 'accion' => $accion, 'mensaje_terminar' => $mensaje],
            'errores' => $errores,
            'avisos' => $avisos,
        ];
    }

    public static function ejecutar(int $fichaId, array $items, string $archivo, ?int $usuarioId = null): array
    {
        require_once __DIR__ . '/Pregunta.php';
        $creadas = [];
        $opcionesTotal = 0;
        $fallo = null;

        foreach ($items as $idx => $it) {
            $res = Pregunta::save([
                'tbl_ficha_tecnica_encuesta_id' => $fichaId,
                'texto_pregunta' => $it['texto_pregunta'],
                'enunciado_pregunta' => $it['enunciado_pregunta'],
                'numeral' => $it['numeral'],
                'capitulo' => $it['capitulo'],
                'tipo_pregunta' => $it['tipo_pregunta'],
                'limite_respuesta_multiple' => $it['limite_respuesta_multiple'],
                'obligatoria' => $it['obligatoria'],
                'es_filtro' => $it['es_filtro'],
                'libreto_encuestador' => $it['libreto_encuestador'],
                'nota_tabulacion' => $it['nota_tabulacion'],
                'tbl_grilla_plantilla_id' => $it['tbl_grilla_plantilla_id'],
                'habilitado' => 'si',
                'opciones' => $it['opciones'],
            ]);
            if (empty($res['output']['valid'])) {
                $fallo = 'La pregunta «' . mb_substr($it['texto_pregunta'], 0, 60) . '» no se pudo crear: '
                    . ($res['output']['response']['content'] ?? 'error desconocido');
                break;
            }
            $creadas[] = (int)$res['output']['id'];
            $opcionesTotal += count($it['opciones']);
        }

        if ($fallo !== null) {
            self::deshacer($creadas);
            self::auditar($fichaId, $archivo, 0, 0, 'error', $fallo, $usuarioId);
            return ['ok' => false, 'mensaje' => $fallo . ' No se guardó ninguna pregunta de este archivo.'];
        }

        self::auditar($fichaId, $archivo, count($creadas), $opcionesTotal, 'completado', null, $usuarioId);
        return ['ok' => true, 'preguntas' => count($creadas), 'opciones' => $opcionesTotal, 'ids' => $creadas];
    }

    private static function deshacer(array $ids): void
    {
        if (empty($ids)) {
            return;
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $in = implode(',', array_map('intval', $ids));
            $pdo->exec("DELETE FROM " . $db->getTable('tbl_preguntas_lista_exclusiones') . " WHERE tbl_pregunta_id IN ($in)");
            $pdo->exec("DELETE FROM " . $db->getTable('tbl_preguntas_grilla_variantes') . " WHERE tbl_pregunta_id IN ($in)");
            $pdo->exec("DELETE FROM " . $db->getTable('tbl_preguntas_grilla_variantes_region') . " WHERE tbl_pregunta_id IN ($in)");
            $pdo->exec("DELETE FROM " . $db->getTable('tbl_preguntas_ambito') . " WHERE tbl_pregunta_id IN ($in)");
            $pdo->exec("DELETE FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id IN ($in)");
            $pdo->exec("DELETE FROM " . $db->getTable('tbl_preguntas') . " WHERE id IN ($in)");
        } catch (Exception $e) {
            error_log('ImportadorPreguntas::deshacer error: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }
    }

    private static function auditar(int $fichaId, string $archivo, int $preguntas, int $opciones, string $estado, ?string $mensaje, ?int $usuarioId): void
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $usuarioId = $usuarioId ?? (isset($_SESSION['session_user']['id']) ? (int)$_SESSION['session_user']['id'] : 1);
            $pdo->prepare("INSERT INTO " . $db->getTable('tbl_auditoria_preguntas') . "
                (tbl_usuario_id, archivo_nombre, cantidad_preguntas, cantidad_opciones, fecha_subida, ip_address, navegador, estado, mensaje_error, tbl_ficha_tecnica_encuesta_id)
                VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)")
                ->execute([
                    $usuarioId,
                    mb_substr($archivo, 0, 255),
                    $preguntas,
                    $opciones,
                    mb_substr((string)Util::get_real_ipaddress(), 0, 45),
                    mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
                    $estado,
                    $mensaje,
                    $fichaId,
                ]);
        } catch (Exception $e) {
            error_log('ImportadorPreguntas::auditar error: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }
    }

    public static function revisar($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : 0;
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('Seleccione primero la encuesta a la que desea cargar las preguntas.');
        }
        $f = $_FILES['archivo'] ?? null;
        if (!$f || !empty($f['error']) || empty($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) {
            return Util::error_missing_data_description('No se recibió el archivo. Seleccione un Excel (.xlsx) o CSV.');
        }
        if ((int)$f['size'] <= 0 || (int)$f['size'] > self::MAX_BYTES) {
            return Util::error_missing_data_description('El archivo debe pesar menos de 5 MB.');
        }
        $nombre = (string)$f['name'];
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'csv'], true)) {
            return Util::error_missing_data_description('Formato no admitido. Suba un archivo Excel (.xlsx) o CSV (.csv).');
        }
        if ($ext === 'xlsx' && file_get_contents($f['tmp_name'], false, null, 0, 2) !== 'PK') {
            return Util::error_missing_data_description('El archivo no es un Excel (.xlsx) válido.');
        }

        try {
            $a = self::analizarArchivo($f['tmp_name'], $nombre, $fichaId);
        } catch (RuntimeException $e) {
            return Util::error_missing_data_description($e->getMessage());
        } catch (Exception $e) {
            error_log('ImportadorPreguntas::revisar error: ' . $e->getMessage());
            return Util::error_general('No se pudo leer el archivo.');
        }

        $token = bin2hex(random_bytes(16));
        $_SESSION['import_preguntas'] = [
            'token' => $token,
            'ficha_id' => $fichaId,
            'archivo' => $nombre,
            'items' => $a['puede_importar'] ? $a['items'] : [],
            'creado' => time(),
        ];

        return ['output' => ['valid' => true, 'response' => [
            'token' => $token,
            'archivo' => $nombre,
            'puede_importar' => $a['puede_importar'],
            'resumen' => $a['resumen'],
            'filas' => $a['filas'],
            'generales' => $a['generales'],
        ]]];
    }

    public static function confirmar($rqst)
    {
        $token = (string)($rqst['token'] ?? '');
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : 0;
        $s = $_SESSION['import_preguntas'] ?? null;
        if (!$s || !hash_equals((string)$s['token'], $token) || (int)$s['ficha_id'] !== $fichaId) {
            return Util::error_missing_data_description('La revisión del archivo ya no es válida. Suba el archivo de nuevo.');
        }
        if (time() - (int)$s['creado'] > 1800) {
            unset($_SESSION['import_preguntas']);
            return Util::error_missing_data_description('La revisión caducó (más de 30 minutos). Suba el archivo de nuevo.');
        }
        if (empty($s['items'])) {
            return Util::error_missing_data_description('No hay preguntas nuevas para importar.');
        }
        unset($_SESSION['import_preguntas']);

        $r = self::ejecutar($fichaId, $s['items'], $s['archivo']);
        if (!$r['ok']) {
            return Util::error_general($r['mensaje']);
        }
        return ['output' => ['valid' => true, 'response' => ['preguntas' => $r['preguntas'], 'opciones' => $r['opciones']]]];
    }

    private static function encabezadoPreguntas(): array
    {
        $r = XlsxEscritor::ESTILO_ENCABEZADO;
        $o = XlsxEscritor::ESTILO_ENCABEZADO_OPCIONAL;
        return [
            ['v' => 'Código', 's' => $r], ['v' => 'Capítulo', 's' => $o], ['v' => 'Numeral', 's' => $o],
            ['v' => 'Enunciado de la sección', 's' => $o], ['v' => 'Pregunta', 's' => $r], ['v' => 'Tipo', 's' => $r],
            ['v' => 'Obligatoria', 's' => $o], ['v' => 'Es filtro', 's' => $o], ['v' => 'Máx. selecciones', 's' => $o],
            ['v' => 'Tabla de candidatos', 's' => $o], ['v' => 'Libreto del encuestador', 's' => $o], ['v' => 'Nota de tabulación', 's' => $o],
        ];
    }

    private static function encabezadoOpciones(): array
    {
        $r = XlsxEscritor::ESTILO_ENCABEZADO;
        $o = XlsxEscritor::ESTILO_ENCABEZADO_OPCIONAL;
        return [
            ['v' => 'Código de la pregunta', 's' => $r], ['v' => 'Opción', 's' => $r], ['v' => 'Valor numérico', 's' => $o],
            ['v' => 'Cuenta como', 's' => $o], ['v' => 'Acción', 's' => $o], ['v' => 'Mensaje si termina', 's' => $o],
        ];
    }

    private static function nombresPlantillas(): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            return array_column(array_values(self::plantillasGrilla($pdo, $db)), 'nombre');
        } finally {
            $db->closeConect();
        }
    }

    private static function filasVacias(int $desde, int $cantidad, int $columnas): array
    {
        $filas = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $fila = [];
            for ($c = 0; $c < $columnas; $c++) {
                $fila[] = ['v' => '', 's' => XlsxEscritor::ESTILO_TEXTO];
            }
            $filas[] = $fila;
        }
        return $filas;
    }

    private static function hojaInstrucciones(bool $esExportacion): array
    {
        $t = XlsxEscritor::ESTILO_TITULO;
        $s = XlsxEscritor::ESTILO_SECCION;
        $x = XlsxEscritor::ESTILO_TEXTO;
        $tipos = [];
        foreach (self::TIPOS as $label => $_) {
            $tipos[] = $label;
        }
        $filas = [
            [['v' => $esExportacion ? 'Encuesta exportada' : 'Carga masiva de preguntas', 's' => $t]],
            [['v' => $esExportacion ? 'Este archivo tiene el mismo formato de la carga masiva. Puede editarlo y subirlo a otra encuesta; las preguntas que ya existan (mismo numeral y texto) no se vuelven a agregar.' : 'Llene las hojas «Preguntas» y «Opciones» y súbalo desde la encuesta con el botón «Subir Excel». Antes de guardar, el sistema muestra una revisión con los errores y avisos de cada fila.', 's' => $x]],
            [''],
            [['v' => 'Cómo llenarlo', 's' => $s]],
            [['v' => '1. En «Preguntas» escriba una fila por pregunta. Las columnas azules son obligatorias; las grises, opcionales.', 's' => $x]],
            [['v' => '2. En «Opciones» escriba una fila por opción de respuesta, repitiendo el «Código de la pregunta» al que pertenece. No hay límite de opciones.', 's' => $x]],
            [['v' => '3. Si deja «Capítulo» o «Enunciado de la sección» vacío, toma el de la fila anterior (así no tiene que repetirlo en las preguntas de una misma sección).', 's' => $x]],
            [['v' => '4. Las preguntas quedan habilitadas y al final de la encuesta, en el orden del archivo.', 's' => $x]],
            [''],
            [['v' => 'Tipos de pregunta', 's' => $s]],
            [['v' => 'Sí / No: si no pone opciones, se crean «Sí» y «No». Debe tener exactamente 2 opciones.', 's' => $x]],
            [['v' => 'Escoge una sola opción / Escoge varias opciones: requieren al menos 2 opciones. En «varias», «Máx. selecciones» limita cuántas puede marcar (vacío = todas).', 's' => $x]],
            [['v' => 'Escala de calificación: si no pone opciones, se crea la escala de 1 a 5.', 's' => $x]],
            [['v' => 'Nivel de satisfacción / intensidad: requiere sus opciones (por ejemplo, Muy satisfecho … Muy insatisfecho).', 's' => $x]],
            [['v' => 'Tabla de candidatos: escriba en «Tabla de candidatos» el nombre exacto de una tabla ya creada (vea la hoja «Listas»). No lleva opciones.', 's' => $x]],
            [['v' => 'Respuesta abierta: no lleva opciones.', 's' => $x]],
            [''],
            [['v' => 'Columnas de «Opciones»', 's' => $s]],
            [['v' => 'Valor numérico: puntaje de la opción (opcional). Cuenta como: ninguno, sí, no, favorable o desfavorable. Acción: continuar o terminar; si es «terminar», escriba el mensaje que verá el encuestado.', 's' => $x]],
            [''],
            [['v' => 'Se configura después, a mano', 's' => $s]],
            [['v' => 'Visibilidad condicionada, saltos entre preguntas, voto escalonado (listas derivadas de una tabla), exclusiones, variables de cruce y equivalencias del votante.', 's' => $x]],
        ];
        return $filas;
    }

    private static function construirLibro(array $filasPreguntas, array $filasOpciones, bool $conEjemplo, bool $esExportacion): string
    {
        $w = new XlsxEscritor();
        $tablas = self::nombresPlantillas();
        $nP = max(count($filasPreguntas) + 50, self::FILAS_PLANTILLA);
        $nO = max(count($filasOpciones) + 200, self::FILAS_PLANTILLA_OPCIONES);

        $w->agregarHoja('Instrucciones', self::hojaInstrucciones($esExportacion), [
            'anchos' => [110],
            'mostrar_cuadricula' => false,
        ]);

        $pFilas = array_merge([self::encabezadoPreguntas()], $filasPreguntas);
        $vacP = self::filasVacias(0, max(0, $nP - count($filasPreguntas)), 12);
        $w->agregarHoja('Preguntas', array_merge($pFilas, $vacP), [
            'anchos' => [10, 26, 10, 38, 60, 30, 12, 10, 12, 26, 38, 30],
            'altos' => [0 => 34],
            'congelar_fila' => true,
            'validaciones' => [
                ['rango' => 'F2:F' . ($nP + 1), 'lista' => array_keys(self::TIPOS)],
                ['rango' => 'G2:H' . ($nP + 1), 'lista' => ['Sí', 'No']],
                ['rango' => 'J2:J' . ($nP + 1), 'formula' => 'Listas!$E$2:$E$60'],
            ],
        ]);

        $oFilas = array_merge([self::encabezadoOpciones()], $filasOpciones);
        $vacO = self::filasVacias(0, max(0, $nO - count($filasOpciones)), 6);
        $w->agregarHoja('Opciones', array_merge($oFilas, $vacO), [
            'anchos' => [18, 60, 14, 16, 14, 50],
            'altos' => [0 => 34],
            'congelar_fila' => true,
            'validaciones' => [
                ['rango' => 'D2:D' . ($nO + 1), 'lista' => ['ninguno', 'sí', 'no', 'favorable', 'desfavorable']],
                ['rango' => 'E2:E' . ($nO + 1), 'lista' => ['continuar', 'terminar']],
            ],
        ]);

        if ($conEjemplo) {
            $s = XlsxEscritor::ESTILO_SECCION;
            $n = XlsxEscritor::ESTILO_NOTA;
            $ejP = [
                ['P1', 'Filtro', 'F', '', '¿Usted votó en las pasadas elecciones?', 'Sí / No', 'Sí', 'Sí', '', '', 'Lea la pregunta tal cual.', ''],
                ['P2', 'Pregunta 1 · Problemas del país', '1A', 'Califique los principales problemas que afectan hoy al departamento.', 'La seguridad y el orden público', 'Escala de calificación', 'Sí', 'No', '', '', '', 'Escala 1 a 5'],
                ['P3', '', '1B', '', 'El costo de vida', 'Escala de calificación', 'Sí', 'No', '', '', '', ''],
                ['P4', 'Pregunta 2 · Gobierno', '2', 'Responda según su percepción.', '¿Qué tan satisfecho está con la gestión actual?', 'Nivel de satisfacción / intensidad', 'Sí', 'No', '', '', '', ''],
                ['P5', 'Pregunta 3 · Información', '3', '', '¿Por qué medios se informa? (máximo 2)', 'Escoge varias opciones', 'Sí', 'No', '2', '', '', ''],
                ['P6', 'Pregunta 4 · Candidatos', '4', '', 'Le voy a nombrar unos posibles candidatos…', 'Tabla de candidatos', 'Sí', 'No', '', $tablas[0] ?? 'Nombre de la tabla', '', ''],
                ['P7', 'Cierre', '5', '', '¿Algún comentario adicional?', 'Respuesta abierta', 'No', 'No', '', '', '', ''],
            ];
            $ejO = [
                ['P1', 'Sí', '', 'sí', 'continuar', ''],
                ['P1', 'No', '', 'no', 'terminar', 'Gracias por su tiempo. Esta encuesta es solo para quienes votaron.'],
                ['P4', 'Muy satisfecho', '5', 'favorable', 'continuar', ''],
                ['P4', 'Satisfecho', '4', 'favorable', 'continuar', ''],
                ['P4', 'Insatisfecho', '2', 'desfavorable', 'continuar', ''],
                ['P4', 'Muy insatisfecho', '1', 'desfavorable', 'continuar', ''],
                ['P5', 'Televisión', '', '', 'continuar', ''],
                ['P5', 'Radio', '', '', 'continuar', ''],
                ['P5', 'Redes sociales', '', '', 'continuar', ''],
                ['P5', 'Prensa', '', '', 'continuar', ''],
            ];
            $filas = [
                [['v' => 'Ejemplo de la hoja «Preguntas» (esta hoja no se importa)', 's' => $s]],
                [['v' => 'P2, P3: «Escala de calificación» sin opciones crea la escala de 1 a 5. P3 repite el capítulo y el enunciado de P2 al dejarlos vacíos.', 's' => $n]],
                self::encabezadoPreguntas(),
            ];
            foreach ($ejP as $r) {
                $filas[] = $r;
            }
            $filas[] = [''];
            $filas[] = [['v' => 'Ejemplo de la hoja «Opciones»', 's' => $s]];
            $filas[] = self::encabezadoOpciones();
            foreach ($ejO as $r) {
                $filas[] = $r;
            }
            $w->agregarHoja('Ejemplo', $filas, ['anchos' => [18, 40, 10, 38, 52, 30, 12, 10, 12, 26, 30, 24]]);
        }

        $listas = [[
            ['v' => 'Tipos de pregunta', 's' => XlsxEscritor::ESTILO_ENCABEZADO],
            ['v' => 'Sí / No', 's' => XlsxEscritor::ESTILO_ENCABEZADO],
            ['v' => 'Cuenta como', 's' => XlsxEscritor::ESTILO_ENCABEZADO],
            ['v' => 'Acción', 's' => XlsxEscritor::ESTILO_ENCABEZADO],
            ['v' => 'Tablas de candidatos existentes', 's' => XlsxEscritor::ESTILO_ENCABEZADO],
        ]];
        $tiposL = array_keys(self::TIPOS);
        $cuentaL = ['ninguno', 'sí', 'no', 'favorable', 'desfavorable'];
        $max = max(count($tiposL), count($cuentaL), count($tablas), 2);
        for ($i = 0; $i < $max; $i++) {
            $listas[] = [$tiposL[$i] ?? '', ['Sí', 'No'][$i] ?? '', $cuentaL[$i] ?? '', ['continuar', 'terminar'][$i] ?? '', $tablas[$i] ?? ''];
        }
        $w->agregarHoja('Listas', $listas, ['anchos' => [38, 10, 16, 14, 40]]);

        return $w->contenido();
    }

    public static function plantillaXlsx(): string
    {
        return self::construirLibro([], [], true, false);
    }

    public static function plantillaCsv(): string
    {
        $cab = ['Código', 'Capítulo', 'Numeral', 'Enunciado de la sección', 'Pregunta', 'Tipo', 'Obligatoria', 'Es filtro', 'Máx. selecciones', 'Tabla de candidatos', 'Libreto del encuestador', 'Nota de tabulación', 'Opción', 'Valor numérico', 'Cuenta como', 'Acción', 'Mensaje si termina'];
        $filas = [
            $cab,
            ['P1', 'Filtro', 'F', '', '¿Usted votó en las pasadas elecciones?', 'Sí / No', 'Sí', 'Sí', '', '', '', '', 'Sí', '', 'sí', 'continuar', ''],
            ['', '', '', '', '', '', '', '', '', '', '', '', 'No', '', 'no', 'terminar', 'Gracias por su tiempo.'],
            ['P2', 'Pregunta 1', '1', '', 'Medios por los que se informa', 'Escoge varias opciones', 'Sí', 'No', '2', '', '', '', 'Televisión', '', '', 'continuar', ''],
            ['', '', '', '', '', '', '', '', '', '', '', '', 'Radio', '', '', 'continuar', ''],
        ];
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, "\xEF\xBB\xBF");
        foreach ($filas as $f) {
            fputcsv($fh, $f, ';');
        }
        rewind($fh);
        $out = stream_get_contents($fh);
        fclose($fh);
        return $out;
    }

    public static function exportarFicha(int $fichaId): string
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $st = $pdo->prepare("SELECT p.*, pl.nombre AS plantilla_nombre FROM " . $db->getTable('tbl_preguntas') . " p
                LEFT JOIN " . $db->getTable('tbl_grilla_plantillas') . " pl ON pl.id = p.tbl_grilla_plantilla_id
                WHERE p.tbl_ficha_tecnica_encuesta_id = :f AND p.eliminado = 0 ORDER BY p.orden, p.id");
            $st->execute([':f' => $fichaId]);
            $preguntas = $st->fetchAll(PDO::FETCH_ASSOC);
            $stO = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :p AND eliminado = 0 ORDER BY orden, id");
            $fp = [];
            $fo = [];
            $x = XlsxEscritor::ESTILO_TEXTO;
            foreach ($preguntas as $i => $p) {
                $cod = 'P' . ($i + 1);
                $fp[] = [
                    ['v' => $cod, 's' => $x], ['v' => (string)$p['capitulo'], 's' => $x], ['v' => (string)$p['numeral'], 's' => $x],
                    ['v' => (string)$p['enunciado_pregunta'], 's' => $x], ['v' => (string)$p['texto_pregunta'], 's' => $x],
                    ['v' => self::etiquetaTipo((string)$p['tipo_pregunta']), 's' => $x],
                    ['v' => (int)$p['obligatoria'] ? 'Sí' : 'No', 's' => $x], ['v' => (int)$p['es_filtro'] ? 'Sí' : 'No', 's' => $x],
                    ['v' => $p['tipo_pregunta'] === 'Seleccion_Multiple_multiple_respuesta' ? (string)(int)$p['limite_respuesta_multiple'] : '', 's' => $x],
                    ['v' => (string)$p['plantilla_nombre'], 's' => $x], ['v' => (string)$p['libreto_encuestador'], 's' => $x], ['v' => (string)$p['nota_tabulacion'], 's' => $x],
                ];
                if (in_array($p['tipo_pregunta'], ['Grilla', 'Lista_desde_grilla', 'Texto Libre'], true)) {
                    continue;
                }
                $stO->execute([':p' => $p['id']]);
                foreach ($stO->fetchAll(PDO::FETCH_ASSOC) as $o) {
                    $fo[] = [
                        ['v' => $cod, 's' => $x], ['v' => (string)$o['texto_opcion'], 's' => $x],
                        ['v' => $o['valor_numerico'] !== null ? rtrim(rtrim((string)$o['valor_numerico'], '0'), '.') : '', 's' => $x],
                        ['v' => $o['cuenta_como'] === 'si' ? 'sí' : (string)$o['cuenta_como'], 's' => $x],
                        ['v' => $o['accion'] === 'terminar' ? 'terminar' : 'continuar', 's' => $x],
                        ['v' => $o['accion'] === 'terminar' ? (string)$o['mensaje_terminar'] : '', 's' => $x],
                    ];
                }
            }
        } finally {
            $db->closeConect();
        }
        return self::construirLibro($fp, $fo, false, true);
    }
}
