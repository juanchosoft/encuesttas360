<?php
session_start();
require_once __DIR__ . '/admin/classes/DbConection.php';
require_once __DIR__ . '/admin/classes/Util.php';
require_once __DIR__ . '/admin/classes/SessionData.php';
require_once __DIR__ . '/admin/classes/Pregunta.php';
require_once __DIR__ . '/admin/classes/GrillaPlantilla.php';
require_once __DIR__ . '/admin/classes/cuestionario/DefinicionCuestionario.php';
require_once __DIR__ . '/admin/classes/cuestionario/MotorReglasCuestionario.php';
require_once __DIR__ . '/admin/db/lib_cargar_encuesta.php';

header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function pagina($titulo, $cuerpo)
{
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="robots" content="noindex"><title>' . h($titulo) . '</title><style>'
        . 'body{font-family:system-ui,Segoe UI,Arial,sans-serif;background:#f4f6f9;color:#1f2937;margin:0;padding:24px 16px}'
        . '.c{max-width:920px;margin:0 auto;background:#fff;border:1px solid #d9dee5;border-radius:10px;padding:24px}'
        . 'h1{font-size:22px;margin:0 0 6px}p.s{color:#6b7280;margin:0 0 18px}'
        . 'table{width:100%;border-collapse:collapse;margin:14px 0}th,td{text-align:left;padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:14px}'
        . '.box{border-radius:8px;padding:12px 14px;margin:14px 0;font-size:14px}.warn{background:#fffbeb;border:1px solid #fcd34d}.bad{background:#fef2f2;border:1px solid #fca5a5}.good{background:#ecfdf5;border:1px solid #6ee7b7}'
        . 'button,a.btn{display:inline-block;background:#1d4ed8;color:#fff;border:0;border-radius:8px;padding:9px 16px;font-size:14px;cursor:pointer;text-decoration:none}button:disabled{background:#9ca3af;cursor:not-allowed}'
        . 'label{display:block;margin:10px 0 4px;font-weight:600;font-size:14px}select,input[type=text]{width:100%;padding:8px;border:1px solid #9ca3af;border-radius:6px;font-size:14px}.chk{font-weight:400}.chk input{margin-right:6px}'
        . '</style></head><body><div class="c">' . $cuerpo . '</div></body></html>';
    exit;
}

if (!SessionData::superAdministrador()) {
    http_response_code(403);
    pagina('Acceso restringido', '<h1>Acceso restringido</h1><p class="s">Este asistente solo lo puede usar un SuperAdministrador con la sesión iniciada.</p><p><a href="login.php">Iniciar sesión</a></p>');
}

if (empty($_SESSION['stdr_token'])) {
    $_SESSION['stdr_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['stdr_token'];

$db = new DbConection();
$pdo = $db->openConect();
$pdo->exec('USE `' . $db->getDbName() . '`');
$pdo->exec('SET NAMES utf8mb4');

$fixtureJson = <<<'FIXTURE_JSON'
{
 "ficha": {
  "titulo": "Estudio econométrico Santander (STDR)",
  "tema": "Gobernación y alcaldías de Santander",
  "config": {
   "pedir_municipio_zona_inicio": 1,
   "audio_obligatorio": 1,
   "certificar_terminadas_filtro": 1,
   "mostrar_resultados_publicos": 0,
   "bloquear_cuota_completa": 0,
   "mensaje_cierre_defecto": "Muchas gracias por su tiempo. Hemos terminado la encuesta."
  }
 },
 "plantillas": {
  "gobernacion": {
   "nombre": "Gobernación (estudio econométrico)",
   "tipo": "candidatos",
   "presentacion": "tarjetas",
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato: si el encuestado responde que SI lo conoce haga las siguientes preguntas de dos posibles respuestas. Si dice que no conoce al candidato continúe con el siguiente candidato hasta terminar bajo esta condición de conocimiento. Para Eduard Sánchez y Omar Lengerke deben activarse la asociación positiva y negativa sin importar si votaría por ellos.",
   "filas": [
    {
     "etiqueta": "MIGUEL ANGEL PINTO"
    },
    {
     "etiqueta": "JULIAN NIÑO"
    },
    {
     "etiqueta": "LAURENO TIRADO"
    },
    {
     "etiqueta": "EDUARD SANCHEZ"
    },
    {
     "etiqueta": "OMAR LENGERKE"
    },
    {
     "etiqueta": "FABIAN DIAZ"
    },
    {
     "etiqueta": "SERGIO ISNARDO MUÑOZ"
    },
    {
     "etiqueta": "LINA BARRERA"
    },
    {
     "etiqueta": "EMIRO ARIAS"
    }
   ],
   "columnas": [
    {
     "titulo": "LO CONOCE",
     "tipo_respuesta": "unica",
     "rol_resultado": "conocimiento",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ]
    },
    {
     "titulo": "IMAGEN",
     "tipo_respuesta": "unica",
     "rol_resultado": "imagen",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "FAV",
       "cuenta_como": "favorable"
      },
      {
       "texto": "DESFAV",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "PARA UD EL CANDIDATO ES",
     "tipo_respuesta": "unica",
     "rol_resultado": "ideologia",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "IZQUIERDA"
      },
      {
       "texto": "DERECHA"
      },
      {
       "texto": "CANDIDATO INDEPENDIENTE O CENTRO"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "VOTARIA POR EL",
     "tipo_respuesta": "unica",
     "rol_resultado": "intencion",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "Asociación, en una palabra positiva",
     "tipo_respuesta": "unica",
     "rol_resultado": "asociacion_positiva",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "1. Honesto",
       "cuenta_como": "favorable"
      },
      {
       "texto": "2. Carismático",
       "cuenta_como": "favorable"
      },
      {
       "texto": "3. Comprometido",
       "cuenta_como": "favorable"
      },
      {
       "texto": "4. Tiene experiencia",
       "cuenta_como": "favorable"
      },
      {
       "texto": "5. Es D-C-I",
       "cuenta_como": "favorable"
      },
      {
       "texto": "6. Buena gente",
       "cuenta_como": "favorable"
      },
      {
       "texto": "7. Trabajador",
       "cuenta_como": "favorable"
      },
      {
       "texto": "8. Escucha al pueblo",
       "cuenta_como": "favorable"
      }
     ],
     "condicion": {
      "columna": "VOTARIA POR EL",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato.",
     "excepciones": [
      {
       "fila": "EDUARD SANCHEZ",
       "columna": "LO CONOCE",
       "opcion": "SI"
      },
      {
       "fila": "OMAR LENGERKE",
       "columna": "LO CONOCE",
       "opcion": "SI"
      }
     ]
    },
    {
     "titulo": "Asociación, en una palabra negativa",
     "tipo_respuesta": "unica",
     "rol_resultado": "asociacion_negativa",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "1. Corrupto",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "2. Soberbio",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "3. Escándalos",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "4. Sin experiencia",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "5. Es D-C-I (tibio)",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "6. No cumple",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "7. Títere",
       "cuenta_como": "desfavorable"
      },
      {
       "texto": "8. Género",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "VOTARIA POR EL",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato.",
     "excepciones": [
      {
       "fila": "EDUARD SANCHEZ",
       "columna": "LO CONOCE",
       "opcion": "SI"
      },
      {
       "fila": "OMAR LENGERKE",
       "columna": "LO CONOCE",
       "opcion": "SI"
      }
     ]
    }
   ]
  },
  "alcaldia_bucaramanga": {
   "nombre": "Alcaldía Bucaramanga (estudio econométrico)",
   "tipo": "candidatos",
   "presentacion": "tarjetas",
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato; si dice que no lo conoce continúe con el siguiente candidato.",
   "filas": [
    {
     "etiqueta": "JUAN DAVID CAVANZO"
    },
    {
     "etiqueta": "ALFONSO \"POCHO\" PINTO"
    },
    {
     "etiqueta": "DEIMER MOSQUERA"
    },
    {
     "etiqueta": "MARISOL PINZON"
    },
    {
     "etiqueta": "CRISTIAN REYES"
    },
    {
     "etiqueta": "TITO RANGEL"
    },
    {
     "etiqueta": "FABIAN OVIEDO"
    },
    {
     "etiqueta": "HUMBERTO SALAZAR"
    },
    {
     "etiqueta": "SERGIO ARENAS"
    }
   ],
   "columnas": [
    {
     "titulo": "LO CONOCE",
     "tipo_respuesta": "unica",
     "rol_resultado": "conocimiento",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ]
    },
    {
     "titulo": "IMAGEN",
     "tipo_respuesta": "unica",
     "rol_resultado": "imagen",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "FAV",
       "cuenta_como": "favorable"
      },
      {
       "texto": "DESFAV",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "PARA UD EL CANDIDATO ES",
     "tipo_respuesta": "unica",
     "rol_resultado": "ideologia",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "IZQUIERDA"
      },
      {
       "texto": "DERECHA"
      },
      {
       "texto": "CANDIDATO INDEPENDIENTE O CENTRO"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "VOTARIA POR EL",
     "tipo_respuesta": "unica",
     "rol_resultado": "intencion",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    }
   ]
  },
  "alcaldia_floridablanca": {
   "nombre": "Alcaldía Floridablanca (estudio econométrico)",
   "tipo": "candidatos",
   "presentacion": "tarjetas",
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato; si dice que no lo conoce continúe con el siguiente candidato.",
   "filas": [
    {
     "etiqueta": "JAHIR MANRIQUE"
    },
    {
     "etiqueta": "NORBERTO ARDILA"
    },
    {
     "etiqueta": "SERGIO FLECHAS"
    },
    {
     "etiqueta": "ALVARO RUEDA"
    },
    {
     "etiqueta": "JOSE ALFREDO MARIN"
    },
    {
     "etiqueta": "JONATHAN PINZON"
    },
    {
     "etiqueta": "WILSON ARDILA"
    },
    {
     "etiqueta": "JERSON GONZALEZ"
    }
   ],
   "columnas": [
    {
     "titulo": "LO CONOCE",
     "tipo_respuesta": "unica",
     "rol_resultado": "conocimiento",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ]
    },
    {
     "titulo": "IMAGEN",
     "tipo_respuesta": "unica",
     "rol_resultado": "imagen",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "FAV",
       "cuenta_como": "favorable"
      },
      {
       "texto": "DESFAV",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "PARA UD EL CANDIDATO ES",
     "tipo_respuesta": "unica",
     "rol_resultado": "ideologia",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "IZQUIERDA"
      },
      {
       "texto": "DERECHA"
      },
      {
       "texto": "CANDIDATO INDEPENDIENTE O CENTRO"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "VOTARIA POR EL",
     "tipo_respuesta": "unica",
     "rol_resultado": "intencion",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    }
   ]
  },
  "alcaldia_piedecuesta": {
   "nombre": "Alcaldía Piedecuesta (estudio econométrico)",
   "tipo": "candidatos",
   "presentacion": "tarjetas",
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato; si dice que no lo conoce continúe con el siguiente candidato.",
   "filas": [
    {
     "etiqueta": "JORGE NAVAS"
    },
    {
     "etiqueta": "WILSON GONZALEZ"
    },
    {
     "etiqueta": "SILVIA CARVAJAL"
    },
    {
     "etiqueta": "ALDEMAR SALAZAR"
    },
    {
     "etiqueta": "RAIMUNDO DUARTE"
    }
   ],
   "columnas": [
    {
     "titulo": "LO CONOCE",
     "tipo_respuesta": "unica",
     "rol_resultado": "conocimiento",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ]
    },
    {
     "titulo": "IMAGEN",
     "tipo_respuesta": "unica",
     "rol_resultado": "imagen",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "FAV",
       "cuenta_como": "favorable"
      },
      {
       "texto": "DESFAV",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "PARA UD EL CANDIDATO ES",
     "tipo_respuesta": "unica",
     "rol_resultado": "ideologia",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "IZQUIERDA"
      },
      {
       "texto": "DERECHA"
      },
      {
       "texto": "CANDIDATO INDEPENDIENTE O CENTRO"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "VOTARIA POR EL",
     "tipo_respuesta": "unica",
     "rol_resultado": "intencion",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    }
   ]
  },
  "alcaldia_giron": {
   "nombre": "Alcaldía Girón (estudio econométrico)",
   "tipo": "candidatos",
   "presentacion": "tarjetas",
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato; si dice que no lo conoce continúe con el siguiente candidato.",
   "filas": [
    {
     "etiqueta": "CARLOS ROMAN"
    },
    {
     "etiqueta": "YULIA RODRIGUEZ"
    },
    {
     "etiqueta": "WILLIAM MANTILLA"
    },
    {
     "etiqueta": "SARITA REYES"
    }
   ],
   "columnas": [
    {
     "titulo": "LO CONOCE",
     "tipo_respuesta": "unica",
     "rol_resultado": "conocimiento",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ]
    },
    {
     "titulo": "IMAGEN",
     "tipo_respuesta": "unica",
     "rol_resultado": "imagen",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "FAV",
       "cuenta_como": "favorable"
      },
      {
       "texto": "DESFAV",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "PARA UD EL CANDIDATO ES",
     "tipo_respuesta": "unica",
     "rol_resultado": "ideologia",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "IZQUIERDA"
      },
      {
       "texto": "DERECHA"
      },
      {
       "texto": "CANDIDATO INDEPENDIENTE O CENTRO"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "VOTARIA POR EL",
     "tipo_respuesta": "unica",
     "rol_resultado": "intencion",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    }
   ]
  },
  "alcaldia_barrancabermeja": {
   "nombre": "Alcaldía Barrancabermeja (estudio econométrico)",
   "tipo": "candidatos",
   "presentacion": "tarjetas",
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato; si dice que no lo conoce continúe con el siguiente candidato.",
   "filas": [
    {
     "etiqueta": "CAMILO PARADA"
    },
    {
     "etiqueta": "ALEIDA NOGUERA"
    },
    {
     "etiqueta": "CRISTIAN ARGUELLO"
    },
    {
     "etiqueta": "LEONARDO GRANADOS"
    },
    {
     "etiqueta": "ALFONSO EL JACH"
    },
    {
     "etiqueta": "CIRO FERNANDEZ"
    },
    {
     "etiqueta": "CLAUDIA ANDRADE"
    },
    {
     "etiqueta": "ADITH ROMERO"
    },
    {
     "etiqueta": "EDWING GARCIA"
    }
   ],
   "columnas": [
    {
     "titulo": "LO CONOCE",
     "tipo_respuesta": "unica",
     "rol_resultado": "conocimiento",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ]
    },
    {
     "titulo": "IMAGEN",
     "tipo_respuesta": "unica",
     "rol_resultado": "imagen",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "FAV",
       "cuenta_como": "favorable"
      },
      {
       "texto": "DESFAV",
       "cuenta_como": "desfavorable"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "PARA UD EL CANDIDATO ES",
     "tipo_respuesta": "unica",
     "rol_resultado": "ideologia",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "IZQUIERDA"
      },
      {
       "texto": "DERECHA"
      },
      {
       "texto": "CANDIDATO INDEPENDIENTE O CENTRO"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    },
    {
     "titulo": "VOTARIA POR EL",
     "tipo_respuesta": "unica",
     "rol_resultado": "intencion",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "SI",
       "cuenta_como": "si"
      },
      {
       "texto": "NO",
       "cuenta_como": "no"
      }
     ],
     "condicion": {
      "columna": "LO CONOCE",
      "opcion": "SI"
     },
     "mensaje_bloqueo": "Si no lo conoce, continúe con el siguiente candidato."
    }
   ]
  },
  "atributos": {
   "nombre": "Atributos del próximo gobernador (estudio econométrico)",
   "tipo": "escala",
   "presentacion": "tarjetas",
   "filas": [
    {
     "etiqueta": "2A Que sea joven"
    },
    {
     "etiqueta": "2B Que sea santandereano"
    },
    {
     "etiqueta": "2C Que sea mujer"
    },
    {
     "etiqueta": "2D Que tenga carácter y liderazgo para guiar la Ciudad"
    },
    {
     "etiqueta": "2E Que no esté en la polarización (izquierda – Derecha)"
    },
    {
     "etiqueta": "2F Que sea una persona destacada en mundo académico, trabajo con la juventud y apoyo a la educación y desarrollo"
    },
    {
     "etiqueta": "2G Que se haya destacado en administración pública y/o haya sido candidatos anteriormente al congreso o la misma Gobernación o Alcaldías del Departamento"
    },
    {
     "etiqueta": "2H Que por el contrario no tenga ninguna trayectoria política ni experiencia en lo público, pero si sea un personaje reconocido en la vida local"
    },
    {
     "etiqueta": "2I Que sea creyente y persona de fe"
    },
    {
     "etiqueta": "2J Que se un hombre o mujer de familia y de hogar"
    }
   ],
   "columnas": [
    {
     "titulo": "Importancia del atributo",
     "tipo_respuesta": "unica",
     "obligatoria": 1,
     "opciones": [
      {
       "texto": "1",
       "valor_numerico": 1
      },
      {
       "texto": "2",
       "valor_numerico": 2
      },
      {
       "texto": "3",
       "valor_numerico": 3
      },
      {
       "texto": "4",
       "valor_numerico": 4
      },
      {
       "texto": "5",
       "valor_numerico": 5
      }
     ]
    }
   ]
  }
 },
 "preguntas": [
  {
   "key": "filtro",
   "numeral": "Filtro",
   "capitulo": "Filtro",
   "texto": "¿Ud. votó en las pasadas elecciones presidenciales?",
   "tipo": "Dicotomica",
   "es_filtro": 1,
   "opciones": [
    {
     "texto": "SI"
    },
    {
     "texto": "NO",
     "accion": "terminar",
     "mensaje_terminar": "Gracias por su tiempo. Esta encuesta es solo para quienes votaron en las pasadas elecciones presidenciales."
    }
   ],
   "libreto_encuestador": "Si marca SI: continúe. Si marca NO: Agradezca, Termine y Remplace.",
   "nota_tabulacion": "Esta encuesta no es válida para el pago, pero se debe contabilizar cuántas personas dijeron sí y cuántas no.",
   "obligatoria": 1
  },
  {
   "key": "cal1A",
   "numeral": "1A",
   "capitulo": "Pregunta 1 – Problemas de Santander",
   "texto": "La seguridad y el orden público",
   "tipo": "Preguntas_Ordinales",
   "enunciado": "Califique los principales problemas que afectan hoy a Santander.",
   "libreto_encuestador": "(siendo 1 'Sin importancia', 2 'poco importante', 3 'algo importante', 4 'importante' y 5 'muy importante')",
   "opciones": [
    {
     "texto": "1",
     "valor_numerico": 1
    },
    {
     "texto": "2",
     "valor_numerico": 2
    },
    {
     "texto": "3",
     "valor_numerico": 3
    },
    {
     "texto": "4",
     "valor_numerico": 4
    },
    {
     "texto": "5",
     "valor_numerico": 5
    }
   ],
   "nota_tabulacion": "Contar respuestas por número, sacar el porcentaje de cada número, multiplicar por su número y sumar: nota sobre cinco.",
   "obligatoria": 1
  },
  {
   "key": "sol1A",
   "numeral": "1A-sol",
   "capitulo": "Pregunta 1 – Soluciones",
   "texto": "¿Cuál cree que es la solución a este problema? (La seguridad y el orden público)",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "A) Mano dura contra los criminales por parte de fuerza pública."
    },
    {
     "texto": "B) Diálogo con las organizaciones delincuenciales del departamento."
    },
    {
     "texto": "C) Instalar cámaras y alarmas comunitarias coordinando con alcaldes."
    },
    {
     "texto": "D) Crear bloques de búsqueda para perseguir a los delincuentes."
    },
    {
     "texto": "E) Sistema de colaboración ciudadana basada en recompensas y patrullas cívicas municipios."
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "cal1B",
   "numeral": "1B",
   "capitulo": "Pregunta 1 – Problemas de Santander",
   "texto": "La corrupción en la gobernación y alcaldías del departamento",
   "tipo": "Preguntas_Ordinales",
   "enunciado": "Califique los principales problemas que afectan hoy a Santander.",
   "libreto_encuestador": "(siendo 1 'Sin importancia', 2 'poco importante', 3 'algo importante', 4 'importante' y 5 'muy importante')",
   "opciones": [
    {
     "texto": "1",
     "valor_numerico": 1
    },
    {
     "texto": "2",
     "valor_numerico": 2
    },
    {
     "texto": "3",
     "valor_numerico": 3
    },
    {
     "texto": "4",
     "valor_numerico": 4
    },
    {
     "texto": "5",
     "valor_numerico": 5
    }
   ],
   "nota_tabulacion": "Contar respuestas por número, sacar el porcentaje de cada número, multiplicar por su número y sumar: nota sobre cinco.",
   "obligatoria": 1
  },
  {
   "key": "sol1B",
   "numeral": "1B-sol",
   "capitulo": "Pregunta 1 – Soluciones",
   "texto": "¿Cuál cree que es la solución a este problema? (La corrupción en la gobernación y alcaldías del departamento)",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "A) Control ciudadano a través de veedurías con \"abogado a domicilio\" pagado por la gobernación."
    },
    {
     "texto": "B) Control político por medio de la Asamblea Departamental que permita la moción de censura de procuraduría y de la gobernación a funcionarios, contratistas y alcaldes."
    },
    {
     "texto": "C) Moción de censura pública a los corruptos por parte de ciudadanía para que sean judicializados y retirados."
    },
    {
     "texto": "D) Paredón de corruptos expuesto en redes sociales."
    },
    {
     "texto": "E) Programa \"abogado en mi comunidad\" pagado por la gobernación, para que reciba denuncias contra los corruptos."
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "cal1C",
   "numeral": "1C",
   "capitulo": "Pregunta 1 – Problemas de Santander",
   "texto": "Costo de vida en Santander",
   "tipo": "Preguntas_Ordinales",
   "enunciado": "Califique los principales problemas que afectan hoy a Santander.",
   "libreto_encuestador": "(siendo 1 'Sin importancia', 2 'poco importante', 3 'algo importante', 4 'importante' y 5 'muy importante')",
   "opciones": [
    {
     "texto": "1",
     "valor_numerico": 1
    },
    {
     "texto": "2",
     "valor_numerico": 2
    },
    {
     "texto": "3",
     "valor_numerico": 3
    },
    {
     "texto": "4",
     "valor_numerico": 4
    },
    {
     "texto": "5",
     "valor_numerico": 5
    }
   ],
   "nota_tabulacion": "Contar respuestas por número, sacar el porcentaje de cada número, multiplicar por su número y sumar: nota sobre cinco.",
   "obligatoria": 1
  },
  {
   "key": "sol1C",
   "numeral": "1C-sol",
   "capitulo": "Pregunta 1 – Soluciones",
   "texto": "¿Cuál cree que es la solución a este problema? (Costo de vida en Santander)",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "A) Crear un DANE Santandereano para el control de precios e inflación en el departamento."
    },
    {
     "texto": "B) Sistema de información en medios departamentales de productos de la canasta familiar."
    },
    {
     "texto": "C) Capacitaciones ciudadanas en finanzas personales y familiares para el manejo del dinero."
    },
    {
     "texto": "D) Alternativas para bajar el costo de los servicios públicos Ej: energías alternativas y mayor competencia."
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "cal1D",
   "numeral": "1D",
   "capitulo": "Pregunta 1 – Problemas de Santander",
   "texto": "Red hospitalaria y clínicas del departamento",
   "tipo": "Preguntas_Ordinales",
   "enunciado": "Califique los principales problemas que afectan hoy a Santander.",
   "libreto_encuestador": "(siendo 1 'Sin importancia', 2 'poco importante', 3 'algo importante', 4 'importante' y 5 'muy importante')",
   "opciones": [
    {
     "texto": "1",
     "valor_numerico": 1
    },
    {
     "texto": "2",
     "valor_numerico": 2
    },
    {
     "texto": "3",
     "valor_numerico": 3
    },
    {
     "texto": "4",
     "valor_numerico": 4
    },
    {
     "texto": "5",
     "valor_numerico": 5
    }
   ],
   "nota_tabulacion": "Contar respuestas por número, sacar el porcentaje de cada número, multiplicar por su número y sumar: nota sobre cinco.",
   "obligatoria": 1
  },
  {
   "key": "sol1D",
   "numeral": "1D-sol",
   "capitulo": "Pregunta 1 – Soluciones",
   "texto": "¿Cuál cree que es la solución a este problema? (Red hospitalaria y clínicas del departamento)",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "A) Programa \"salud en la casa\" con atención médica a domicilio y servicio de ambulancia."
    },
    {
     "texto": "B) Sistema de Video-Citas con especialistas mediante copago."
    },
    {
     "texto": "C) Programa de atención psicología y de terapias. Ej: pareja, adicciones."
    },
    {
     "texto": "D) Programas de promoción y prevención con paramédicos."
    },
    {
     "texto": "E) paramédicos en salud promoción y prevención de la gobernación de Santander"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "cal1E",
   "numeral": "1E",
   "capitulo": "Pregunta 1 – Problemas de Santander",
   "texto": "Problemas viales y de movilidad en la infraestructura del departamento",
   "tipo": "Preguntas_Ordinales",
   "enunciado": "Califique los principales problemas que afectan hoy a Santander.",
   "libreto_encuestador": "(siendo 1 'Sin importancia', 2 'poco importante', 3 'algo importante', 4 'importante' y 5 'muy importante')",
   "opciones": [
    {
     "texto": "1",
     "valor_numerico": 1
    },
    {
     "texto": "2",
     "valor_numerico": 2
    },
    {
     "texto": "3",
     "valor_numerico": 3
    },
    {
     "texto": "4",
     "valor_numerico": 4
    },
    {
     "texto": "5",
     "valor_numerico": 5
    }
   ],
   "nota_tabulacion": "Contar respuestas por número, sacar el porcentaje de cada número, multiplicar por su número y sumar: nota sobre cinco.",
   "obligatoria": 1
  },
  {
   "key": "sol1E",
   "numeral": "1E-sol",
   "capitulo": "Pregunta 1 – Soluciones",
   "texto": "¿Cuál cree que es la solución a este problema? (Problemas viales y de movilidad en la infraestructura del departamento)",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "A) Sistema de descuentos a usuarios frecuentes a través de peajes inteligentes."
    },
    {
     "texto": "B) Sistema inteligente de mantenimiento de vías con interventoría de recursos de las concesiones."
    },
    {
     "texto": "C) Programas de caravanas turísticas para viajeros."
    },
    {
     "texto": "D) Intermediación de las carreteras nacionales."
    },
    {
     "texto": "E) Cronograma público de infraestructura vial"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_1",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en Área Metropolitana, ¿Cuál es el principal problema que debe resolver el gobernador de Santander para el Área Metropolitana?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "municipio",
     "valor": "68001"
    },
    {
     "tipo": "municipio",
     "valor": "68276"
    },
    {
     "tipo": "municipio",
     "valor": "68307"
    },
    {
     "tipo": "municipio",
     "valor": "68547"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Área Metropolitana)",
   "opciones": [
    {
     "texto": "a) Seguridad y microtráfico"
    },
    {
     "texto": "b) Sistema de transporte masivo"
    },
    {
     "texto": "c) Recolección de basuras"
    },
    {
     "texto": "d) Empleo y costo de vida"
    },
    {
     "texto": "e) Vivienda y educación"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_2",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en la Provincia de Yariguíes (antigua Mares): Barrancabermeja, Puerto Wilches, Sabana de Torres, San Vicente de Chucurí. ¿Cuál es el principal problema que debe resolver el gobernador de Santander para la Provincia de Yariguíes?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "region",
     "valor": "Yariguíes"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Provincia de Yariguíes)",
   "opciones": [
    {
     "texto": "a) Costo de vida y de servicios públicos"
    },
    {
     "texto": "b) Alcantarillado, acueducto y saneamiento básico"
    },
    {
     "texto": "c) Vías intermunicipales"
    },
    {
     "texto": "d) Relaciones con el gobierno central (Ecopetrol)"
    },
    {
     "texto": "e) Salud, vivienda y educación"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_3",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en la Provincia Antigua Soto: Lebrija, Rionegro, El Playón. ¿Cuál es el principal problema que debe resolver el gobernador de Santander para la Provincia Antigua Soto?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "municipio",
     "valor": "68406"
    },
    {
     "tipo": "municipio",
     "valor": "68615"
    },
    {
     "tipo": "municipio",
     "valor": "68255"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Provincia Antigua Soto)",
   "opciones": [
    {
     "texto": "a) Integrar el municipio al Área Metropolitana de Bucaramanga"
    },
    {
     "texto": "b) Transporte intermunicipal al Área Metropolitana de Bucaramanga"
    },
    {
     "texto": "c) Eliminar los peajes de acceso al Área Metropolitana de Bucaramanga"
    },
    {
     "texto": "d) Presencia policial fines de semana cuando viajan los residentes del Área Metropolitana"
    },
    {
     "texto": "e) Acueducto, alcantarillado y servicios públicos"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_4",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en la Provincia García Rovira: Málaga. ¿Cuál es el principal problema que debe resolver el gobernador de Santander para la Provincia García Rovira?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "region",
     "valor": "García Rovira"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Provincia García Rovira)",
   "opciones": [
    {
     "texto": "a) Crisis y escasez de agua potable"
    },
    {
     "texto": "b) Tratamiento de aguas residuales"
    },
    {
     "texto": "c) Conectividad vial (vía Curos - Málaga)"
    },
    {
     "texto": "d) Afectación por patrimonio urbano"
    },
    {
     "texto": "e) Arreglo de viviendas de \"tapia pisada\""
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_5",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en la Provincia Comunera: El Socorro. ¿Cuál es el principal problema que debe resolver el gobernador de Santander para la Provincia Comunera?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "region",
     "valor": "Comunera"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Provincia Comunera)",
   "opciones": [
    {
     "texto": "a) Estado de las vías e infraestructura"
    },
    {
     "texto": "b) Crisis del sector panelero"
    },
    {
     "texto": "c) Servicios públicos y costo de vida"
    },
    {
     "texto": "d) Problemas de infraestructura turística como consecuencia de la ruta del sol"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_6",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en la Provincia Guanentá: San Gil. ¿Cuál es el principal problema que debe resolver el gobernador de Santander para la Provincia de Guanentá?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "region",
     "valor": "Guanentá"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Provincia Guanentá)",
   "opciones": [
    {
     "texto": "a) Crisis en la infraestructura vial"
    },
    {
     "texto": "b) Problemas de infraestructura turística como consecuencia de la ruta del sol"
    },
    {
     "texto": "c) Inseguridad en zonas rurales"
    },
    {
     "texto": "d) Problemática urbana y ambiental"
    },
    {
     "texto": "e) Conflictos por recursos hídricos e infraestructura eléctrica"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "reg1F_7",
   "numeral": "1F",
   "capitulo": "Pregunta 1F – Problema principal de su región",
   "texto": "Si usted está en la Provincia Vélez: Vélez, Barbosa, Cimitarra. ¿Cuál es el principal problema que debe resolver el gobernador de Santander para la Provincia de Vélez?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "ambito": [
    {
     "tipo": "region",
     "valor": "Vélez"
    }
   ],
   "libreto_encuestador": "Dependiendo del lugar donde esté encuestando, el programa genera una pregunta exclusiva para el área donde usted está preguntando. (Provincia de Vélez)",
   "opciones": [
    {
     "texto": "a) Desabastecimiento de agua potable"
    },
    {
     "texto": "b) Impacto en la industria del bocadillo"
    },
    {
     "texto": "c) Problemas en la infraestructura de salud"
    },
    {
     "texto": "d) Vivienda y educación"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "atributos",
   "numeral": "2",
   "capitulo": "Pregunta 2 – Atributos del próximo Gobernador",
   "texto": "¿Qué tan importante es que el próximo gobernador de Santander tenga estas características? Díganos si para Ud. este atributo que le leo es:",
   "tipo": "Grilla",
   "grilla": {
    "plantilla": "atributos"
   },
   "libreto_encuestador": "1. Nada importante que el líder sea o tenga ese atributo. 2. Poco importante que el líder sea así o tenga ese atributo. 3. Le es indiferente que el líder tenga esos atributos. 4. Es muy importante que el líder sea y tenga esos atributos. 5. Es absolutamente necesario, importante y básico que el líder que sea nuestro próximo Gobernador de Santander tenga ese atributo mencionado.",
   "obligatoria": 1
  },
  {
   "key": "v3a",
   "numeral": "3A",
   "capitulo": "Pregunta 3 – Voto anterior",
   "texto": "¿Por quién votó en las pasadas elecciones a la gobernación de Santander?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "a) Juvenal Díaz"
    },
    {
     "texto": "b) Héctor Mantilla"
    },
    {
     "texto": "c) Luis Ferley Sierra"
    },
    {
     "texto": "d) Rodolfo Hernández"
    },
    {
     "texto": "e) Otro candidato"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "v3b",
   "numeral": "3B",
   "capitulo": "Pregunta 3 – Voto anterior",
   "texto": "¿Está arrepentido de su voto?",
   "tipo": "Dicotomica",
   "opciones": [
    {
     "texto": "a) Sí"
    },
    {
     "texto": "b) No"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "p4",
   "numeral": "4",
   "capitulo": "Pregunta 4 – Posición política",
   "texto": "Independientemente de su voto en las pasadas elecciones, Ud. se considera…",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "PETRISTA"
    },
    {
     "texto": "URIBISTA"
    },
    {
     "texto": "NI PETRISTA NI URIBISTA"
    },
    {
     "texto": "ABELARDISTA"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "p4sub",
   "numeral": "4-sub",
   "capitulo": "Pregunta 4 – Posición política",
   "texto": "Ud. se considera…",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "visible_si": {
    "pregunta": "p4",
    "opcion": "NI PETRISTA NI URIBISTA"
   },
   "libreto_encuestador": "Si el encuestado dice que identifica que no es ni Petrista ni Uribista hacer la subpregunta.",
   "opciones": [
    {
     "texto": "DE IZQUIERDA"
    },
    {
     "texto": "DE DERECHA"
    },
    {
     "texto": "DE CENTRO IZQUIERDA"
    },
    {
     "texto": "DE CENTRO DERECHA"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "g5a",
   "numeral": "5A",
   "capitulo": "Pregunta 5 – Candidatos a la Gobernación",
   "texto": "Le voy a nombrar unos posibles candidatos a la Gobernación de Santander. Ud. me dirá si lo conoce, ha leído o ha escuchado de él.",
   "tipo": "Grilla",
   "grilla": {
    "plantilla": "gobernacion"
   },
   "libreto_encuestador": "El sistema de grilla está condicionado por el conocimiento del candidato: si el encuestado responde que SI lo conoce haga las siguientes preguntas de dos posibles respuestas. Si el encuestado dice que no conoce al candidato continúe con el siguiente candidato hasta terminar bajo esta condición de conocimiento.",
   "obligatoria": 1
  },
  {
   "voto_escalonado": {
    "origen": "g5a",
    "columna": "VOTARIA POR EL",
    "opcion": "SI",
    "capitulo": "Pregunta 5 – Candidatos a la Gobernación",
    "si_lista_vacia": "omitir",
    "pasos": [
     {
      "key": "e5b",
      "numeral": "5B",
      "texto_pregunta": "Ud. anteriormente mencionó algunos personajes que reconoce. Si las elecciones fueran hoy, ¿por cuál de los candidatos que seleccionó estaría dispuesto a votar a la gobernación de Santander?",
      "libreto_encuestador": "Subgrilla con los SI VOTARIAN.",
      "opciones_fijas": []
     },
     {
      "key": "e5c",
      "numeral": "5C",
      "texto_pregunta": "Como falta bastante tiempo, puede que el candidato que seleccionó en la pregunta no se presente. En ese caso, ¿cuál sería su plan B para votar si las elecciones para gobernador fueran hoy?",
      "opciones_fijas": []
     },
     {
      "key": "e5d",
      "numeral": "5D",
      "texto_pregunta": "¿Cuál sería su plan C para votar si las elecciones para gobernador fueran hoy y sus dos primeras opciones no estuvieran en el tarjetón?",
      "libreto_encuestador": "Aquí sí está él No sabe / No responde y el Ninguno.",
      "opciones_fijas": [
       "No sabe / No responde",
       "Ninguno"
      ]
     }
    ]
   }
  },
  {
   "key": "alc",
   "numeral": "5AB",
   "capitulo": "Pregunta 5 – Candidatos a la Alcaldía",
   "texto": "Le voy a nombrar unos posibles candidatos a la Alcaldía de {municipio}. Ud. me dirá si lo conoce, ha leído o ha escuchado de él.",
   "tipo": "Grilla",
   "grilla": {
    "por_municipio": {
     "68001": "alcaldia_bucaramanga",
     "68276": "alcaldia_floridablanca",
     "68547": "alcaldia_piedecuesta",
     "68307": "alcaldia_giron",
     "68081": "alcaldia_barrancabermeja"
    }
   },
   "libreto_encuestador": "Según el lugar donde esté el encuestado (Bucaramanga, Floridablanca, Piedecuesta, Girón o Barrancabermeja) el programa muestra la grilla de ese municipio. En los demás municipios no se genera esta grilla.",
   "obligatoria": 1
  },
  {
   "key": "l5ba",
   "numeral": "5BA",
   "capitulo": "Pregunta 5 – Candidatos a la Alcaldía",
   "tipo": "Lista_desde_grilla",
   "texto": "Ud. anteriormente mencionó algunos personajes que reconoce. Si las elecciones fueran hoy, ¿por cuál de los candidatos que seleccionó estaría dispuesto a votar a la ALCALDIA DE {municipio}?",
   "libreto_encuestador": "Subgrilla con los SI VOTARIAN según el municipio.",
   "lista": {
    "origen": "alc",
    "columna": "VOTARIA POR EL",
    "opcion": "SI",
    "si_lista_vacia": "omitir"
   },
   "obligatoria": 1
  },
  {
   "key": "p8a",
   "numeral": "8A",
   "capitulo": "Pregunta 8 – Preferencias",
   "texto": "¿Por cuál de estos candidatos preferiría votar?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "Un candidato que mejore la economía."
    },
    {
     "texto": "Un candidato que acabe con la delincuencia."
    },
    {
     "texto": "Un candidato que termine con la corrupción."
    },
    {
     "texto": "Un Candidato que acabe con la polarización e induzca la unidad de los Santandereanos."
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "p8b",
   "numeral": "8B",
   "capitulo": "Pregunta 8 – Preferencias",
   "texto": "¿Por quién votaría a la gobernación de Santander en 2027?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "a) El que diga Abelardo De La Espriella"
    },
    {
     "texto": "b) El que diga Gustavo Petro e Iván Cepeda y/o Pacto Histórico"
    },
    {
     "texto": "c) El que diga Jaime Andrés Beltrán"
    },
    {
     "texto": "d) El que diga Jhon Aviut Ramirez"
    },
    {
     "texto": "e) El que diga el Gobernador actual Juvenal Díaz"
    },
    {
     "texto": "f) El que diga Álvaro Uribe y Centro Democrático"
    },
    {
     "texto": "g) El que diga el partido Republicano (partido de Rodolfo y Senadora Cabal)"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "p8c",
   "numeral": "8C",
   "capitulo": "Pregunta 8 – Preferencias",
   "texto": "¿Por cuál candidato preferiría votar a la gobernación de Santander en 2027?",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "opciones": [
    {
     "texto": "a) Un candidato que ya se haya presentado a las elecciones pasadas"
    },
    {
     "texto": "b) Un exalcalde"
    },
    {
     "texto": "c) Una persona que se presente por firmas (movimiento independiente)"
    },
    {
     "texto": "d) Una persona que se presente por una coalición de partidos (con avales)"
    },
    {
     "texto": "e) Una persona que se haya presentado a la Cámara de Representantes o Senado"
    },
    {
     "texto": "f) Una persona apoyada por las casas políticas Aguilar, Galán, Díaz Mateus, Tavera, Serpa o Liberales"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "genero",
   "numeral": "9",
   "capitulo": "Variables para el cruce de resultados",
   "texto": "Género",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "es_variable_cruce": 1,
   "etiqueta_cruce": "Género",
   "copiar_a_votante_campo": "genero",
   "libreto_encuestador": "Generar estos ítems para cruce de variables antes del envío por parte del encuestador.",
   "opciones": [
    {
     "texto": "a) Masculino"
    },
    {
     "texto": "b) Femenino"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "edad",
   "numeral": "10",
   "capitulo": "Variables para el cruce de resultados",
   "texto": "Rango de edad",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "es_variable_cruce": 1,
   "etiqueta_cruce": "Rango de edad",
   "copiar_a_votante_campo": "rango_edad",
   "opciones": [
    {
     "texto": "a) 18 a 25 años de edad (jóvenes)"
    },
    {
     "texto": "b) 26 a 35 años de edad (jóvenes mayores)"
    },
    {
     "texto": "c) 36 a 55 años de edad (adultos)"
    },
    {
     "texto": "d) Mayores de 55 años de edad (adultos mayores)"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "estrato",
   "numeral": "11",
   "capitulo": "Variables para el cruce de resultados",
   "texto": "Estrato socioeconómico",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "es_variable_cruce": 1,
   "etiqueta_cruce": "Estrato",
   "opciones": [
    {
     "texto": "a) 1"
    },
    {
     "texto": "b) 2"
    },
    {
     "texto": "c) 3"
    },
    {
     "texto": "d) 4"
    },
    {
     "texto": "e) 5"
    },
    {
     "texto": "f) 6"
    }
   ],
   "obligatoria": 1
  },
  {
   "key": "provincia",
   "numeral": "12",
   "capitulo": "Variables para el cruce de resultados",
   "texto": "Provincias",
   "tipo": "Seleccion_Multiple_unica_respuesta",
   "es_variable_cruce": 1,
   "etiqueta_cruce": "Provincia",
   "opciones": [
    {
     "texto": "1. Área Metropolitana: Bucaramanga, Floridablanca, Girón, Piedecuesta"
    },
    {
     "texto": "2. Provincia Antigua Soto: Lebrija, Rionegro, El Playón"
    },
    {
     "texto": "3. Provincia Comunera: El Socorro"
    },
    {
     "texto": "4. Provincia Guanentá: San Gil"
    },
    {
     "texto": "5. Provincia de Yariguíes (antigua Mares): Barrancabermeja, Puerto Wilches, Sabana de Torres, San Vicente de Chucurí"
    },
    {
     "texto": "6. Provincia García Rovira: Málaga"
    },
    {
     "texto": "7. Provincia de Vélez: Vélez, Barbosa, Cimitarra"
    }
   ],
   "obligatoria": 1
  }
 ],
 "zonas": [
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "1 Norte",
   "cuota": 39
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "2 Nor Oriental",
   "cuota": 25
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "3 San Francisco",
   "cuota": 32
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "13 Oriental",
   "cuota": 38
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "14 Morrorico",
   "cuota": 16
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "4 Occidental",
   "cuota": 27
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "15 Centro",
   "cuota": 6
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "12 Cabecera",
   "cuota": 24
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "5 García Rovira",
   "cuota": 30
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "6 Concordia",
   "cuota": 21
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "7 Ciudadela",
   "cuota": 21
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "17 Mutis",
   "cuota": 19
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "8 Sur Occidente",
   "cuota": 13
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "11 Sur",
   "cuota": 21
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "10 Provenza",
   "cuota": 23
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "9 Pedregoza",
   "cuota": 13
  },
  {
   "municipio": "68001",
   "tipo": "comuna",
   "nombre": "16 Lagos del Casique",
   "cuota": 11
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "1 Altamira",
   "cuota": 9
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "2 Cañaveral",
   "cuota": 18
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "3 Bucarica",
   "cuota": 30
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "4 Caldas",
   "cuota": 24
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "5 El Bosque",
   "cuota": 18
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "6 Lagos",
   "cuota": 9
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "7 Valencia",
   "cuota": 12
  },
  {
   "municipio": "68276",
   "tipo": "comuna",
   "nombre": "8 La Cumbre",
   "cuota": 30
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 1",
   "cuota": 14
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 2",
   "cuota": 20
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 3",
   "cuota": 21
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 4",
   "cuota": 15
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 5",
   "cuota": 34
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 6",
   "cuota": 16
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Comuna 7",
   "cuota": 19
  },
  {
   "municipio": "68081",
   "tipo": "comuna",
   "nombre": "Corregimiento Centro",
   "cuota": 12
  },
  {
   "municipio": "68307",
   "tipo": "sector",
   "nombre": "Sector 1",
   "cuota": 28,
   "descripcion": "Río Frío, Acapulco, Barbosa, Llanadas"
  },
  {
   "municipio": "68307",
   "tipo": "sector",
   "nombre": "Sector 2",
   "cuota": 14,
   "descripcion": "Convivir, Cogreso, Palenque, Bahondo, Villa San Juan"
  },
  {
   "municipio": "68307",
   "tipo": "sector",
   "nombre": "Sector 3",
   "cuota": 56,
   "descripcion": "Rincón de Girón, Poblado, Parque Central, San Jorge, Portal, Malecón"
  },
  {
   "municipio": "68307",
   "tipo": "sector",
   "nombre": "Sector 4",
   "cuota": 35,
   "descripcion": "Carrizal, Vía Café Madrid, Bocas, Pablón"
  },
  {
   "municipio": "68547",
   "tipo": "sector",
   "nombre": "Sector 1",
   "cuota": 30,
   "descripcion": "San Francisco, San Carlos, Quinta Granada, La Rioja, Villa Nueva"
  },
  {
   "municipio": "68547",
   "tipo": "sector",
   "nombre": "Sector 2",
   "cuota": 23,
   "descripcion": "San Marcos, Cabecera, Plazuela San Antonio, Urb. del Sur"
  },
  {
   "municipio": "68547",
   "tipo": "sector",
   "nombre": "Sector 3",
   "cuota": 37,
   "descripcion": "Centro, La Cantera, Urb. Villa del Rocío, Barrio Piedecuesta, La Castellana"
  },
  {
   "municipio": "68547",
   "tipo": "sector",
   "nombre": "Sector 4",
   "cuota": 30,
   "descripcion": "La Argentina, El Refugio, El Paraíso, Urb. San Telmo, Urb. Callejuela"
  },
  {
   "municipio": "68679",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 100
  },
  {
   "municipio": "68755",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 80
  },
  {
   "municipio": "68077",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 80
  },
  {
   "municipio": "68861",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 80
  },
  {
   "municipio": "68190",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 80
  },
  {
   "municipio": "68575",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 50
  },
  {
   "municipio": "68406",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 50
  },
  {
   "municipio": "68689",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 50
  },
  {
   "municipio": "68615",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 40
  },
  {
   "municipio": "68255",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 30
  },
  {
   "municipio": "68655",
   "tipo": "zona",
   "nombre": "Todo el municipio",
   "cuota": 30
  }
 ],
 "equivalencias": [
  {
   "pregunta": "genero",
   "opcion": "a) Masculino",
   "campo": "genero",
   "valor": "masculino"
  },
  {
   "pregunta": "genero",
   "opcion": "b) Femenino",
   "campo": "genero",
   "valor": "femenino"
  },
  {
   "pregunta": "edad",
   "opcion": "a) 18 a 25 años de edad (jóvenes)",
   "campo": "rango_edad",
   "valor": "18-25"
  },
  {
   "pregunta": "edad",
   "opcion": "b) 26 a 35 años de edad (jóvenes mayores)",
   "campo": "rango_edad",
   "valor": "26-35"
  }
 ]
}
FIXTURE_JSON;
$fixture = json_decode($fixtureJson, true);

$tablasRequeridas = ['tbl_preguntas_grilla_variantes_region', 'tbl_preguntas_ambito', 'tbl_grilla_columna_excepciones'];
$faltantes = [];
foreach ($tablasRequeridas as $t) {
    $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t');
    $st->execute([':t' => $t]);
    if (!(int)$st->fetchColumn()) {
        $faltantes[] = $t;
    }
}
if ($faltantes) {
    pagina('Falta migrar', '<h1>Falta ejecutar las migraciones</h1><div class="box bad">No existen estas tablas: <b>' . h(implode(', ', $faltantes)) . '</b>.<br>Ejecute primero <code>migrar_produccion.php</code> (migraciones fase-m-002, fase-m-003 y fase-m-004) y vuelva a abrir esta página.</div>');
}

function resumenFichas(PDO $pdo): array
{
    $sql = "SELECT f.id, f.realizada_por_o_encomendada_por AS titulo, f.habilitado,
                   (SELECT COUNT(*) FROM tbl_preguntas p WHERE p.tbl_ficha_tecnica_encuesta_id = f.id AND p.eliminado = 0) AS preguntas,
                   (SELECT COUNT(*) FROM tbl_cuestionario_intentos i WHERE i.tbl_ficha_tecnica_encuesta_id = f.id) AS intentos
            FROM tbl_ficha_tecnica_encuestas f WHERE f.eliminado = 'no' ORDER BY f.id DESC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!hash_equals($token, (string)($_POST['token'] ?? ''))) {
        pagina('Solicitud inválida', '<div class="box bad">Token inválido. Recargue la página.</div>');
    }
    $destino = (int)($_POST['ficha_id'] ?? 0);
    $espacioId = (int)($_POST['espacio_id'] ?? 0);
    $titulo = trim((string)($_POST['titulo'] ?? ''));
    $esNueva = $destino === 0;
    $_SESSION['session_user']['id'] = $_SESSION['session_user']['id'] ?? 1;

    if ($esNueva) {
        if ($titulo === '' || $espacioId <= 0) {
            pagina('Datos incompletos', '<div class="box bad">Para crear una ficha nueva indique el título y el espacio geográfico.</div><p><a href="migrar_encuesta_stdr.php">Volver</a></p>');
        }
        $fixture['ficha']['titulo'] = $titulo;
    } else {
        $st = $pdo->prepare("SELECT (SELECT COUNT(*) FROM tbl_cuestionario_intentos WHERE tbl_ficha_tecnica_encuesta_id = f.id) AS intentos FROM tbl_ficha_tecnica_encuestas f WHERE f.id = :f AND f.eliminado = 'no'");
        $st->execute([':f' => $destino]);
        $fila = $st->fetch(PDO::FETCH_ASSOC);
        if (!$fila) {
            pagina('Ficha inexistente', '<div class="box bad">La ficha elegida no existe.</div><p><a href="migrar_encuesta_stdr.php">Volver</a></p>');
        }
        if ((int)$fila['intentos'] > 0) {
            pagina('Ficha con respuestas', '<div class="box bad">La ficha ya tiene respuestas registradas; no se reemplaza su cuestionario.</div><p><a href="migrar_encuesta_stdr.php">Volver</a></p>');
        }
        $previas = $pdo->prepare('SELECT id FROM tbl_preguntas WHERE tbl_ficha_tecnica_encuesta_id = :f AND eliminado = 0');
        $previas->execute([':f' => $destino]);
        $idsPrevios = $previas->fetchAll(PDO::FETCH_COLUMN);
        if ($idsPrevios && empty($_POST['reemplazar'])) {
            pagina('Confirme el reemplazo', '<div class="box bad">La ficha tiene ' . count($idsPrevios) . ' pregunta(s). Marque la casilla de reemplazo para continuar.</div><p><a href="migrar_encuesta_stdr.php">Volver</a></p>');
        }
        foreach ($idsPrevios as $idViejo) {
            Pregunta::delete(['id' => (int)$idViejo]);
        }
    }

    $res = cargarEncuestaDesdeFixture($fixture, $pdo, $db, '', $esNueva ? 0 : $destino);
    $fichaId = (int)$res['ficha_id'];

    if ($res['errores']) {
        if ($esNueva) {
            borrarEncuestaCargada($pdo, $db, $res);
        }
        $lista = '';
        foreach ($res['errores'] as $e) {
            $lista .= '<li>' . h(mb_substr($e, 0, 400)) . '</li>';
        }
        pagina('Error al montar', '<h1>No se pudo montar la encuesta</h1><div class="box bad"><ul>' . $lista . '</ul>' . ($esNueva ? 'La ficha nueva se retiró; no quedó nada creado.' : 'Revise la ficha elegida antes de repetir.') . '</div>');
    }

    if ($esNueva) {
        $pdo->prepare("UPDATE tbl_ficha_tecnica_encuestas SET tbl_espacio_geografico_id = :e, habilitado = 'no' WHERE id = :f")
            ->execute([':e' => $espacioId, ':f' => $fichaId]);
    }
    $destacada = (int)($res['preguntas']['e5b'] ?? 0);
    if ($destacada > 0) {
        Pregunta::saveConfigEncuesta(['ficha_id' => $fichaId, 'pregunta_destacada_id' => $destacada] + $fixture['ficha']['config']);
    }

    $def = DefinicionCuestionario::cargar($pdo, $fichaId, $db->getDbName());
    $val = MotorReglasCuestionario::validarConfiguracion($def);
    $avisos = '';
    foreach ($val['alertas'] as $a) {
        $avisos .= '<li>' . h($a['mensaje']) . '</li>';
    }
    $totalCuota = 0;
    foreach ($fixture['zonas'] as $z) {
        $totalCuota += (int)$z['cuota'];
    }
    pagina('Encuesta montada', '<h1>Encuesta montada</h1><div class="box good">Ficha <b>#' . $fichaId . '</b>: <b>' . count($res['preguntas']) . '</b> preguntas, <b>' . count($res['plantillas']) . '</b> tablas de candidatos y <b>' . (int)($res['zonas'] ?? 0) . '</b> zonas de muestreo (cuota total ' . $totalCuota . ').'
        . ($esNueva ? '<br>La ficha nueva quedó <b>deshabilitada</b>: revísela y habilítela desde la ficha técnica.' : '') . '</div>'
        . ($avisos ? '<div class="box warn"><b>Avisos del validador:</b><ul>' . $avisos . '</ul></div>' : '<div class="box good">El validador de configuración no reportó avisos.</div>')
        . '<p>Cuando termine, elimine este archivo del servidor.</p><p><a class="btn" href="cuestionario_preguntas.php">Ir a las preguntas</a></p>');
}

$fichas = resumenFichas($pdo);
$opcFichas = '<option value="0">— Crear una ficha nueva —</option>';
foreach ($fichas as $f) {
    $bloq = (int)$f['intentos'] > 0;
    $opcFichas .= '<option value="' . (int)$f['id'] . '"' . ($bloq ? ' disabled' : '') . '>#' . (int)$f['id'] . ' · ' . h(mb_substr((string)$f['titulo'], 0, 70)) . ' · ' . (int)$f['preguntas'] . ' preguntas' . ($bloq ? ' · tiene respuestas' : '') . '</option>';
}
$espacios = $pdo->query("SELECT id, tipo_estudio, observaciones FROM tbl_espacio_geografico WHERE habilitado = 'si' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$opcEsp = '';
foreach ($espacios as $e) {
    $opcEsp .= '<option value="' . (int)$e['id'] . '">#' . (int)$e['id'] . ' · ' . h($e['tipo_estudio']) . ' · ' . h(mb_substr((string)$e['observaciones'], 0, 60)) . '</option>';
}
$nPreg = 0;
foreach ($fixture['preguntas'] as $p) {
    $nPreg += isset($p['voto_escalonado']) ? count($p['voto_escalonado']['pasos']) : 1;
}

pagina('Montar encuesta STDR', '<h1>Montar el estudio econométrico de Santander</h1>'
    . '<p class="s">Crea las preguntas, tablas de candidatos, preguntas regionales y zonas de muestreo del cuestionario STDR.</p>'
    . '<div class="box warn">Contenido a montar: <b>' . $nPreg . '</b> preguntas, <b>' . count($fixture['plantillas']) . '</b> tablas de candidatos y <b>' . count($fixture['zonas']) . '</b> zonas de muestreo.</div>'
    . '<form method="post"><input type="hidden" name="token" value="' . h($token) . '">'
    . '<label for="ficha_id">Destino</label><select name="ficha_id" id="ficha_id">' . $opcFichas . '</select>'
    . '<label class="chk"><input type="checkbox" name="reemplazar" value="1"> Si la ficha elegida ya tiene preguntas, reemplazarlas (solo se permite sin respuestas registradas)</label>'
    . '<label for="titulo">Título (solo para ficha nueva)</label><input type="text" name="titulo" id="titulo" value="' . h($fixture['ficha']['titulo']) . '">'
    . '<label for="espacio_id">Espacio geográfico (solo para ficha nueva)</label><select name="espacio_id" id="espacio_id">' . $opcEsp . '</select>'
    . '<p><button type="submit">Montar encuesta</button></p></form>');
