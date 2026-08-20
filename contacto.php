<?php
include './admin/include/head.php';
?>

<!DOCTYPE html>
<html lang="es">

<body>

<?php include './admin/include/loading.php'; ?>
<?php include './admin/include/menu_registro.php'; ?>

<style>
    .contact-360-section {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        padding: 90px 0;
    }

    .contact-360-header {
        max-width: 900px;
        margin: 0 auto 50px auto;
        text-align: center;
    }

    .contact-360-badge {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 50px;
        background: rgba(13, 110, 253, 0.10);
        color: #08172c;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 15px;
    }

    .contact-360-title {
        font-size: 2.4rem;
        font-weight: 800;
        color: #0b1f3a;
        margin-bottom: 15px;
    }

    .contact-360-subtitle {
        font-size: 1.05rem;
        color: #5b6b7f;
        line-height: 1.9;
        margin: 0 auto;
        max-width: 850px;
    }

    .contact-360-info {
        background: #ffffff;
        border-radius: 24px;
        padding: 30px 25px;
        box-shadow: 0 18px 45px rgba(0, 83, 156, 0.10);
        border: 1px solid rgba(13, 110, 253, 0.08);
        height: 100%;
    }

    .contact-360-info-item {
        text-align: center;
        padding: 22px 15px;
        border-bottom: 1px solid #eef3f9;
    }

    .contact-360-info-item:last-child {
        border-bottom: none;
    }

    .contact-360-info-item i {
        font-size: 2.4rem;
        color: #08172c;
        margin-bottom: 14px;
    }

    .contact-360-info-item h4 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0b1f3a;
        margin-bottom: 10px;
    }

    .contact-360-info-item p,
    .contact-360-info-item a {
        font-size: 1rem;
        color: #5b6b7f;
        margin-bottom: 0;
        text-decoration: none;
        word-break: break-word;
    }

    .contact-360-info-item a:hover {
        color: #08172c;
    }

    .contact-360-form-box {
        background: #ffffff;
        border-radius: 24px;
        padding: 35px 30px;
        box-shadow: 0 18px 45px rgba(0, 83, 156, 0.10);
        border: 1px solid rgba(13, 110, 253, 0.08);
        height: 100%;
    }

    .contact-360-form-box h3 {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0b1f3a;
        margin-bottom: 10px;
    }

    .contact-360-form-box p {
        color: #5b6b7f;
        font-size: 1rem;
        line-height: 1.8;
        margin-bottom: 28px;
    }

    .contact-360-form-box .form-floating > .form-control,
    .contact-360-form-box textarea.form-control {
        border: 1px solid #dbe7f5 !important;
        border-radius: 16px !important;
        background: #f9fcff;
        padding-left: 16px;
        color: #0b1f3a;
        box-shadow: none !important;
    }

    .contact-360-form-box .form-floating > label {
        color: #6b7c93;
        padding-left: 16px;
    }

    .contact-360-form-box .form-control:focus {
        border-color: #08172c !important;
        background: #ffffff;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.10) !important;
    }

    .btn-contact-360 {
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #08172c 0%, #0a58ca 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        padding: 14px 24px;
        transition: all 0.3s ease;
        box-shadow: 0 12px 24px rgba(13, 110, 253, 0.22);
    }

    .btn-contact-360:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(13, 110, 253, 0.28);
        color: #fff;
    }

    .btn-contact-360:disabled {
        opacity: .75;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 991.98px) {
        .contact-360-section {
            padding: 70px 0;
        }

        .contact-360-title {
            font-size: 2rem;
        }

        .contact-360-info {
            margin-bottom: 10px;
        }
    }

    @media (max-width: 767.98px) {
        .contact-360-form-box,
        .contact-360-info {
            border-radius: 20px;
            padding: 25px 18px;
        }

        .contact-360-title {
            font-size: 1.7rem;
        }

        .contact-360-subtitle {
            font-size: .98rem;
            line-height: 1.8;
        }
    }
</style>

<div class="container-fluid contact-360-section">
    <div class="container">
        <div class="contact-360-header">
            <span class="contact-360-badge">Contáctanos</span>
            <h1 class="contact-360-title">Estamos aquí para ayudarte</h1>
            <p class="contact-360-subtitle">
                En <strong>Encuestas360.com</strong> estamos disponibles para atender solicitudes de información, consultas comerciales,
                requerimientos institucionales y dudas relacionadas con nuestros servicios de encuestas, sondeos, estudios de opinión
                e investigación. Nuestro equipo está comprometido con brindar una atención seria, oportuna y profesional.
            </p>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-4">
                <div class="contact-360-info">
                    <div class="contact-360-info-item">
                        <i class="fa fa-map-marker-alt"></i>
                        <h4>Ubicación</h4>
                        <p>Bucaramanga, Santander<br>Colombia</p>
                    </div>

                    <div class="contact-360-info-item">
                        <i class="fa fa-building"></i>
                        <h4>Quiénes Somos</h4>
                        <p>Empresa encuestadora con más de 10 años de experiencia, comprometida con la legalidad, la ética y el rigor metodológico.</p>
                    </div>

                    <div class="contact-360-info-item">
                        <i class="fa fa-envelope-open"></i>
                        <h4>Correo de contacto</h4>
                        <p><a href="mailto:info@encuestas360.com">info@encuestas360.com</a></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="contact-360-form-box">
                    <h3>Envíanos un mensaje</h3>
                    <p>
                        Si deseas comunicarte con nosotros para obtener información sobre nuestros servicios,
                        resolver inquietudes o solicitar acompañamiento profesional, completa el siguiente formulario
                        y te responderemos a la mayor brevedad posible.
                    </p>

                    <form id="contactForm360" novalidate>
                        <input type="hidden" name="_subject" value="Nuevo mensaje de contacto - Encuestas360">
                        <input type="hidden" name="_language" value="es">
                        <input type="text" name="_gotcha" style="display:none">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo" required>
                                    <label for="nombre">Nombre completo</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Correo electrónico" required>
                                    <label for="email">Correo electrónico</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Asunto" required>
                                    <label for="asunto">Asunto</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="mensaje" name="mensaje" placeholder="Escribe aquí tu mensaje" style="height: 180px" required></textarea>
                                    <label for="mensaje">Mensaje</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button id="btnEnviarMensaje" class="btn-contact-360 w-100" type="submit">
                                    Enviar mensaje
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm360');
    const btn = document.getElementById('btnEnviarMensaje');

    if (!form || !btn) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const endpoint = 'https://formspree.io/f/mykbjrjb';
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = 'Enviando mensaje...';

        const formData = new FormData(form);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => null);
            console.log('Respuesta Formspree:', data);

            if (response.ok) {
                form.reset();

                await Swal.fire({
                    icon: 'success',
                    title: '¡Mensaje enviado!',
                    text: 'Su mensaje se envió con éxito.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#08172c'
                });
            } else {
                let errorText = 'No fue posible enviar el mensaje.';

                if (data && data.errors && Array.isArray(data.errors)) {
                    errorText = data.errors.map(item => item.message).join(' ');
                }

                await Swal.fire({
                    icon: 'error',
                    title: 'Error al enviar',
                    text: errorText,
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (error) {
            console.error('Error en fetch:', error);

            await Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'Ocurrió un problema al intentar enviar el formulario.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
});
</script>

</body>
</html>