<?php 
include './admin/include/head.php';
?>

<!DOCTYPE html>
<html lang="es">

<body>

<?php include './admin/include/loading.php'; ?>
<?php include './admin/include/menu_registro.php'; ?>

<style>
    .about-section-360 {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        padding: 90px 0;
    }

    .about-card-360 {
        background: #ffffff;
        border-radius: 24px;
        padding: 45px 40px;
        box-shadow: 0 18px 45px rgba(0, 83, 156, 0.10);
        border: 1px solid rgba(0, 123, 255, 0.08);
        position: relative;
        overflow: hidden;
    }

    .about-card-360::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg, #071429, #3ea6ff, #00c6ff);
    }

    .about-badge-360 {
        display: inline-block;
        background: rgba(13, 110, 253, 0.10);
        color: #1e3963;
        font-weight: 700;
        font-size: 14px;
        padding: 8px 18px;
        border-radius: 50px;
        margin-bottom: 18px;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .about-title-360 {
        font-size: 2.4rem;
        font-weight: 800;
        color: #0b1f3a;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .about-title-360 span {
        color: #071429;
    }

    .about-text-360 {
        font-size: 1.06rem;
        line-height: 1.95;
        color: #4d5d72;
        text-align: justify;
        margin-bottom: 22px;
    }

    .about-contact-box {
        margin-top: 30px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #eef6ff 0%, #f8fbff 100%);
        border: 1px solid rgba(13, 110, 253, 0.10);
        border-radius: 18px;
    }

    .about-contact-label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #071429;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: .5px;
    }

    .about-contact-link {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0b1f3a;
        text-decoration: none;
        word-break: break-word;
    }

    .about-contact-link:hover {
        color: #071429;
    }

    .about-side-box {
        height: 100%;
        background: linear-gradient(135deg, #071429 0%, #1d4075 100%);
        border-radius: 24px;
        padding: 35px 30px;
        color: #fff;
        box-shadow: 0 18px 45px rgba(13, 110, 253, 0.20);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .about-side-box h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 18px;
        color: #fff;
    }

    .about-side-box p {
        font-size: 1rem;
        line-height: 1.8;
        margin-bottom: 16px;
        color: rgba(255, 255, 255, 0.95);
    }

    .about-side-item {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
        padding: 14px 16px;
        margin-top: 12px;
        font-weight: 600;
        font-size: 0.98rem;
    }

    @media (max-width: 991.98px) {
        .about-section-360 {
            padding: 70px 0;
        }

        .about-card-360 {
            padding: 35px 25px;
        }

        .about-title-360 {
            font-size: 2rem;
        }

        .about-side-box {
            margin-top: 25px;
        }
    }

    @media (max-width: 575.98px) {
        .about-card-360 {
            padding: 28px 18px;
            border-radius: 18px;
        }

        .about-title-360 {
            font-size: 1.7rem;
        }

        .about-text-360 {
            font-size: 0.98rem;
            line-height: 1.85;
        }

        .about-side-box {
            padding: 28px 20px;
            border-radius: 18px;
        }

        .about-side-box h3 {
            font-size: 1.45rem;
        }

        .about-contact-link {
            font-size: 1rem;
        }
    }
</style>

<!-- About Start -->
<div class="container-fluid about-section-360">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            
            <div class="col-lg-8">
                <div class="about-card-360 h-100">
                    <span class="about-badge-360">Quiénes Somos</span>
                    <h1 class="about-title-360">
                        Somos <span>Encuestas360.com</span>
                    </h1>

                    <p class="about-text-360">
                        En <strong>Encuestas360.com</strong> somos una <strong>empresa encuestadora con sede en Bucaramanga, Santander</strong>, especializada en la recolección, procesamiento, análisis e interpretación de información mediante estudios de opinión, sondeos, encuestas y procesos de investigación estadística y social. Contamos con <strong>más de 10 años de experiencia</strong> en este campo, desarrollando soluciones orientadas a entregar información confiable, precisa y técnicamente estructurada para apoyar la toma de decisiones de entidades, organizaciones, campañas, empresas y diferentes sectores que requieren resultados claros, responsables y oportunos. Nuestra trayectoria se ha consolidado gracias al compromiso con la calidad, la transparencia, la confidencialidad de la información y el rigor metodológico en cada uno de los procesos que ejecutamos.
                    </p>

                    <p class="about-text-360 mb-0">
                        Como organización, desarrollamos nuestras actividades en estricto cumplimiento de la normatividad vigente en Colombia, siendo una empresa <strong>reglamentada por la ley y por las disposiciones aplicables del Consejo Nacional Electoral</strong>, en especial cuando se trate de estudios, sondeos o mediciones de opinión dentro de dicho marco. En <strong>Encuestas360.com</strong> creemos en el valor de la información bien construida como herramienta fundamental para comprender realidades, medir tendencias y fortalecer procesos de decisión con respaldo técnico y legal. Nuestro equipo trabaja con ética, responsabilidad y profesionalismo, brindando un servicio serio y confiable, respaldado por una amplia experiencia en Bucaramanga, Santander y otras regiones del país. Para mayor información, atención institucional o contacto comercial, puede comunicarse con nosotros al correo <strong>info@encuestas360.com</strong>.
                    </p>

                    <div class="about-contact-box">
                        <span class="about-contact-label">Correo de contacto</span>
                        <a href="mailto:info@encuestas360.com" class="about-contact-link">
                            info@encuestas360.com
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="about-side-box">
                    <h3>Experiencia, legalidad y confianza</h3>
                    <p>
                        Más de una década aportando experiencia en estudios de opinión, recolección de datos y análisis de información con enfoque técnico, ético y profesional.
                    </p>

                    <div class="about-side-item">📍 Bucaramanga, Santander</div>
                    <div class="about-side-item">⚖️ Reglamentados por la ley colombiana</div>
                    <div class="about-side-item">🏛️ Bajo lineamientos aplicables del CNE</div>
                    <div class="about-side-item">📊 Más de 10 años de experiencia</div>
                    <div class="about-side-item">✉️ info@encuestas360.com</div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- About End -->

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