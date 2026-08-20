

<?php
include './admin/include/head.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - Estadisticas360</title>

    <style>
        :root{
            --primary: #173d7a;
            --primary-dark: #102c59;
            --secondary: #f5f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --card: #ffffff;
            --shadow: 0 20px 45px rgba(15, 23, 42, 0.10);
            --radius: 24px;
        }

        body{
            background: linear-gradient(180deg, #f8fafc 0%, #eef3f9 100%);
            color: var(--text);
        }

        .privacy-page{
            padding: 60px 0 80px;
        }

        .privacy-container{
            width: 100%;
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .privacy-actions{
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .privacy-btn{
            border: 0;
            outline: 0;
            min-width: 180px;
            padding: 14px 26px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all .25s ease;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.10);
        }

        .privacy-btn-light{
            background: #ffffff;
            color: #374151;
            border: 1px solid var(--border);
        }

        .privacy-btn-light:hover{
            background: #f8fafc;
            color: var(--primary);
            transform: translateY(-2px);
        }

        .privacy-btn-primary{
            background: linear-gradient(135deg, #1f4d99 0%, #173d7a 100%);
            color: #ffffff;
        }

        .privacy-btn-primary:hover{
            background: linear-gradient(135deg, #173d7a 0%, #102c59 100%);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .privacy-card{
            background: var(--card);
            border: 1px solid rgba(255,255,255,.7);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .privacy-hero{
            background: linear-gradient(135deg, rgba(23,61,122,0.06) 0%, rgba(31,77,153,0.12) 100%);
            padding: 50px 50px 36px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .privacy-badge{
            display: inline-block;
            background: rgba(23,61,122,0.10);
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 999px;
            margin-bottom: 18px;
        }

        .privacy-title{
            font-size: 42px;
            line-height: 1.15;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 14px;
        }

        .privacy-subtitle{
            font-size: 30px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 12px;
        }

        .privacy-meta{
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 15px;
            font-weight: 600;
        }

        .privacy-content{
            padding: 42px 50px 50px;
        }

        .privacy-section{
            margin-bottom: 34px;
            padding-bottom: 26px;
            border-bottom: 1px solid #edf2f7;
        }

        .privacy-section:last-child{
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .privacy-section h3{
            color: var(--primary);
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .privacy-section h5{
            color: #111827;
            font-size: 20px;
            font-weight: 700;
            margin-top: 18px;
            margin-bottom: 12px;
        }

        .privacy-section p{
            font-size: 17px;
            line-height: 1.9;
            color: #374151;
            margin-bottom: 14px;
        }

        .privacy-section ul{
            padding-left: 22px;
            margin-bottom: 14px;
        }

        .privacy-section ul li{
            font-size: 17px;
            line-height: 1.85;
            color: #374151;
            margin-bottom: 8px;
        }

        .privacy-highlight{
            background: #f8fbff;
            border: 1px solid #dbe8ff;
            border-left: 5px solid var(--primary);
            border-radius: 16px;
            padding: 20px 22px;
            margin-top: 16px;
        }

        .privacy-highlight p:last-child{
            margin-bottom: 0;
        }

        .privacy-grid{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 18px;
        }

        .privacy-mini-card{
            background: #f9fbfd;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 20px;
        }

        .privacy-mini-card p{
            margin-bottom: 8px;
            font-size: 16px;
            line-height: 1.7;
        }

        .privacy-mini-card p:last-child{
            margin-bottom: 0;
        }

        @media (max-width: 991.98px){
            .privacy-page{
                padding: 40px 0 60px;
            }

            .privacy-hero{
                padding: 38px 28px 28px;
            }

            .privacy-content{
                padding: 30px 28px 36px;
            }

            .privacy-title{
                font-size: 34px;
            }

            .privacy-subtitle{
                font-size: 24px;
            }

            .privacy-section h3{
                font-size: 24px;
            }

            .privacy-grid{
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px){
            .privacy-container{
                padding: 0 14px;
            }

            .privacy-actions{
                flex-direction: column;
                align-items: stretch;
            }

            .privacy-btn{
                width: 100%;
                min-width: 100%;
                padding: 15px 20px;
                font-size: 15px;
            }

            .privacy-hero{
                padding: 28px 18px 22px;
            }

            .privacy-content{
                padding: 24px 18px 28px;
            }

            .privacy-title{
                font-size: 28px;
            }

            .privacy-subtitle{
                font-size: 22px;
            }

            .privacy-meta{
                flex-direction: column;
                gap: 8px;
                font-size: 14px;
            }

            .privacy-section h3{
                font-size: 21px;
            }

            .privacy-section h5{
                font-size: 18px;
            }

            .privacy-section p,
            .privacy-section ul li{
                font-size: 15.5px;
                line-height: 1.8;
            }

            .privacy-card{
                border-radius: 18px;
            }
        }
    </style>
</head>

<body>

<?php include './admin/include/loading.php'; ?>
<?php include './admin/include/menu_registro.php'; ?>

<section class="privacy-page">
    <div class="privacy-container">

        <div class="privacy-actions">
            <a href="index.php" class="privacy-btn privacy-btn-light">Ahora no</a>
            <a href="registro.php" class="privacy-btn privacy-btn-primary">
                <i class="fa fa-user-plus"></i> Registrarme
            </a>
        </div>

        <div class="privacy-card">

            <div class="privacy-hero">
                <span class="privacy-badge">Protección de datos personales</span>
                <h1 class="privacy-title">Política de Privacidad y Tratamiento de Datos Personales</h1>
                <div class="privacy-subtitle">estadisticas360.com</div>
                <div class="privacy-meta">
                    <span><strong>Fecha de entrada en vigencia:</strong> 03/01/27 [●]</span>
                    <span><strong>Última actualización:</strong> 03/01/27[●]</span>
                </div>
            </div>

            <div class="privacy-content">

                <div class="privacy-section">
                    <h3>1. Identificación del Responsable del Tratamiento</h3>

                    <div class="privacy-grid">
                        <div class="privacy-mini-card">
                            <p><strong>Razón social / titular del sitio:</strong>ENCUESTAS 360</p>
                            <p><strong>Domicilio:</strong> BUCARAMANGA SANTANDER</p>
                        </div>
                        <div class="privacy-mini-card">
                            <p><strong>Correo de atención de habeas data:</strong> [correo@estadisticas360.com]</p
                            <p><strong>Canal de contacto:</strong> [correo@estadisticas360.com]</p>
                            <p><strong>Sitio web:</strong> estadisticas360.com</p>
                        </div>
                    </div>

                    <p class="mt-4">
                        La presente Política de Privacidad y Tratamiento de Datos Personales regula la recolección,
                        almacenamiento, uso, circulación, transmisión, transferencia, actualización, supresión y, en general,
                        cualquier tratamiento de datos personales realizado a través del sitio web encuestas360.com,
                        de conformidad con la Constitución Política de Colombia, la Ley 1581 de 2012 y sus decretos
                        reglamentarios compilados, principalmente, en el Decreto 1074 de 2015.
                    </p>
                </div>

                <div class="privacy-section">
                    <h3>2. Objeto</h3>
                    <p>Esta política tiene por objeto informar a los titulares de los datos personales acerca de:</p>
                    <ul>
                        <li>Los datos que recolecta estadisticas360.com.</li>
                        <li>Las finalidades del tratamiento.</li>
                        <li>Los derechos de los titulares.</li>
                        <li>Los canales para ejercer consultas, reclamos y solicitudes.</li>
                        <li>Las medidas generales de seguridad aplicables al tratamiento.</li>
                        <li>Las condiciones de circulación, transmisión y eventual transferencia de la información.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>3. Marco normativo aplicable</h3>
                    <ul>
                        <li>Artículo 15 de la Constitución Política de Colombia.</li>
                        <li>Ley 1581 de 2012.</li>
                        <li>Decreto 1377 de 2013.</li>
                        <li>Decreto 1074 de 2015.</li>
                        <li>Demás normas que adicionen, modifiquen o sustituyan el régimen de protección de datos personales en Colombia.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>4. Definiciones</h3>
                    <ul>
                        <li><strong>Dato personal:</strong> cualquier información vinculada o que pueda asociarse a una o varias personas naturales determinadas o determinables.</li>
                        <li><strong>Dato sensible:</strong> aquel que afecta la intimidad del titular o cuyo uso indebido puede generar discriminación.</li>
                        <li><strong>Titular:</strong> persona natural cuyos datos personales son objeto de tratamiento.</li>
                        <li><strong>Responsable del tratamiento:</strong> persona natural o jurídica que decide sobre la base de datos y/o el tratamiento.</li>
                        <li><strong>Encargado del tratamiento:</strong> persona natural o jurídica que realiza el tratamiento por cuenta del responsable.</li>
                        <li><strong>Tratamiento:</strong> cualquier operación sobre datos personales, como recolección, almacenamiento, uso, circulación o supresión.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>5. Principios aplicables al tratamiento</h3>
                    <p>Encuestas360.com aplicará, en todo momento, los principios de:</p>
                    <ul>
                        <li>Legalidad.</li>
                        <li>Finalidad.</li>
                        <li>Libertad.</li>
                        <li>Veracidad o calidad.</li>
                        <li>Transparencia.</li>
                        <li>Acceso y circulación restringida.</li>
                        <li>Seguridad.</li>
                        <li>Confidencialidad.</li>
                        <li>Necesidad y proporcionalidad.</li>
                    </ul>
                    <div class="privacy-highlight">
                        <p>
                            En consecuencia, solo se recolectarán los datos estrictamente necesarios para las finalidades
                            informadas y autorizadas por el titular, y no se realizará tratamiento incompatible con dichas finalidades.
                        </p>
                    </div>
                </div>

                <div class="privacy-section">
                    <h3>6. Datos personales que se recolectan</h3>

                    <h5>6.1. Datos de identificación y acceso</h5>
                    <ul>
                        <li>Correo electrónico.</li>
                        <li>Nombre de usuario.</li>
                        <li>Contraseña cifrada o hasheada en los sistemas internos.</li>
                    </ul>

                    <h5>6.2. Datos de ubicación</h5>
                    <ul>
                        <li>Departamento.</li>
                        <li>Municipio.</li>
                        <li>Barrio (opcional).</li>
                    </ul>

                    <h5>6.3. Datos de perfil y caracterización</h5>
                    <ul>
                        <li>Rango de edad.</li>
                        <li>Nivel socioeconómico.</li>
                        <li>Género.</li>
                        <li>Ocupación.</li>
                        <li>Nivel educativo (opcional).</li>
                    </ul>

                    <h5>6.4. Dato sensible</h5>
                    <ul>
                        <li>Ideología política.</li>
                    </ul>

                    <h5>6.5. Datos técnicos y de navegación</h5>
                    <ul>
                        <li>Dirección IP.</li>
                        <li>Fecha y hora de registro.</li>
                        <li>Logs de acceso.</li>
                        <li>Identificadores de sesión.</li>
                        <li>Cookies técnicas y de seguridad.</li>
                        <li>Información del dispositivo y navegador, cuando ello resulte necesario para la seguridad de la plataforma, prevención de fraude y trazabilidad operativa.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>7. Finalidades del tratamiento</h3>
                    <ul>
                        <li>Crear y administrar la cuenta del usuario.</li>
                        <li>Permitir el acceso a la plataforma y autenticación segura.</li>
                        <li>Gestionar la participación en encuestas, sondeos, formularios y procesos de caracterización.</li>
                        <li>Validar integridad operativa, prevenir fraude, duplicidad de registros, suplantación, abuso de plataforma y accesos no autorizados.</li>
                        <li>Depurar, segmentar y consolidar bases de datos para fines estadísticos, analíticos y de investigación.</li>
                        <li>Generar reportes agregados, anonimizados o seudonimizados, cuando ello sea posible.</li>
                        <li>Contactar al titular para asuntos relacionados con su cuenta, soporte, cambios en el servicio, seguridad o cumplimiento legal.</li>
                        <li>Atender consultas, peticiones, quejas, reclamos y solicitudes de habeas data.</li>
                        <li>Cumplir obligaciones legales, regulatorias, contractuales y requerimientos de autoridad competente.</li>
                        <li>Realizar auditorías internas, controles de seguridad, monitoreo de cumplimiento y trazabilidad.</li>
                        <li>Efectuar transmisión o procesamiento por proveedores tecnológicos, hosting, analítica, mensajería o ciberseguridad.</li>
                        <li>Usar información estadística, agregada o anonimizada para análisis de tendencias, investigación de mercado, comportamiento social y mejora de la plataforma.</li>
                    </ul>

                    <div class="privacy-highlight">
                        <p><strong>Finalidad específica para dato sensible – ideología política:</strong></p>
                        <p>
                            La ideología política solo será recolectada si resulta estrictamente necesaria para la naturaleza
                            de la encuesta, estudio o sondeo correspondiente, y exclusivamente para fines de análisis estadístico,
                            segmentación investigativa, elaboración de resultados agregados y estudios de opinión.
                        </p>
                    </div>
                </div>

                <div class="privacy-section">
                    <h3>8. Carácter facultativo de los datos sensibles</h3>
                    <ul>
                        <li>El titular no está obligado a autorizar el tratamiento de datos sensibles.</li>
                        <li>Debe ser informado de forma previa, explícita y clara sobre el carácter sensible del dato solicitado.</li>
                        <li>La negativa a suministrar datos sensibles no podrá generar discriminación ilegítima, salvo que el dato sea estrictamente indispensable para la finalidad explícita de una encuesta particular y ello haya sido debidamente informado.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>9. Derechos de los titulares</h3>
                    <ul>
                        <li>Conocer, actualizar y rectificar sus datos personales.</li>
                        <li>Solicitar prueba de la autorización otorgada.</li>
                        <li>Ser informado respecto del uso dado a sus datos.</li>
                        <li>Presentar quejas ante la Superintendencia de Industria y Comercio.</li>
                        <li>Revocar la autorización y/o solicitar la supresión del dato cuando proceda.</li>
                        <li>Acceder gratuitamente a sus datos personales objeto de tratamiento.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>10. Autorización del titular</h3>
                    <p>
                        estadisticas360.com recolectará, almacenará y tratará datos personales únicamente con autorización previa,
                        expresa e informada del titular, salvo las excepciones legales.
                    </p>
                    <ul>
                        <li>Quién autorizó.</li>
                        <li>Cuándo autorizó.</li>
                        <li>Qué texto aceptó.</li>
                        <li>Desde qué canal o formulario se recolectó.</li>
                        <li>Cuál era la finalidad informada al momento de la autorización.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>11. Tratamiento de datos de niños, niñas y adolescentes</h3>
                    <p>
                        estadisticas360.com no dirigirá sus servicios a menores de edad ni recolectará intencionalmente datos
                        personales de niños, niñas o adolescentes sin verificar previamente la procedencia legal del tratamiento.
                    </p>
                </div>

                <div class="privacy-section">
                    <h3>12. Seguridad de la información</h3>
                    <p>
                        estadisticas360.com adoptará medidas razonables, técnicas, humanas, administrativas y organizacionales
                        para proteger los datos personales frente a pérdida, acceso no autorizado, uso fraudulento,
                        consulta no autorizada, alteración, divulgación o destrucción.
                    </p>
                    <ul>
                        <li>Cifrado en tránsito mediante HTTPS/TLS.</li>
                        <li>Almacenamiento seguro de contraseñas mediante hash robusto y salado.</li>
                        <li>Control de accesos por roles.</li>
                        <li>Registro de eventos y auditoría.</li>
                        <li>Copias de seguridad.</li>
                        <li>Segregación de ambientes.</li>
                        <li>Gestión de vulnerabilidades.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>13. Circulación, transmisión y transferencia</h3>
                    <p>
                        Los datos personales podrán ser transmitidos o puestos a disposición de encargados del tratamiento,
                        dentro o fuera de Colombia, para fines de hosting, almacenamiento en la nube, soporte tecnológico,
                        analítica, envío de comunicaciones, seguridad digital u operación de la plataforma.
                    </p>
                </div>

                <div class="privacy-section">
                    <h3>14. Tiempo de conservación</h3>
                    <p>
                        Los datos personales se conservarán por el tiempo que resulte necesario para cumplir las finalidades
                        autorizadas, la prestación del servicio, la atención de requerimientos legales y la prevención del fraude.
                    </p>
                </div>

                <div class="privacy-section">
                    <h3>15. Procedimiento para consultas y reclamos</h3>
                    <p>
                        El titular podrá ejercer sus derechos enviando solicitud al correo:
                        <strong>[correo@estadisticas360.com]</strong>
                    </p>
                    <ul>
                        <li>Nombre completo.</li>
                        <li>Tipo de solicitud.</li>
                        <li>Descripción de los hechos.</li>
                        <li>Datos de contacto.</li>
                        <li>Documentos que soporten la petición, si aplica.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>16. Cookies y tecnologías similares</h3>
                    <ul>
                        <li>Mantener sesiones activas.</li>
                        <li>Recordar preferencias.</li>
                        <li>Detectar actividad irregular.</li>
                        <li>Medir desempeño del sitio.</li>
                        <li>Mejorar la experiencia del usuario.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>17. Limitación de uso y prohibiciones</h3>
                    <ul>
                        <li>Usar datos para discriminación ilegal.</li>
                        <li>Publicar datos personales individualizados sin base legal.</li>
                        <li>Ceder credenciales de acceso.</li>
                        <li>Recolectar información de terceros sin autorización.</li>
                        <li>Tratar datos sensibles sin justificación y consentimiento reforzado.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h3>18. Exoneración y reserva legal razonable</h3>
                    <p>
                        Encuestas360.com hará sus mejores esfuerzos para cumplir la normatividad aplicable y mantener información
                        veraz, actualizada y segura. Sin embargo, no será responsable por información falsa o inexacta suministrada
                        directamente por el titular, ni por accesos indebidos originados en culpa exclusiva del usuario.
                    </p>
                </div>

                <div class="privacy-section">
                    <h3>19. Modificaciones de la política</h3>
                    <p>
                        Encuestas360.com podrá modificar esta política en cualquier momento. Las modificaciones sustanciales serán
                        informadas por medios razonables, incluyendo publicación en el sitio web.
                    </p>
                </div>

                <div class="privacy-section">
                    <h3>20. Vigencia</h3>
                    <p>La presente política rige a partir de su publicación.</p>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include './admin/include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/lightbox/js/lightbox.min.js"></script>
<script src="admin/js/lib/util.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>
<script src="js/login.js"></script>

</body>
</html>