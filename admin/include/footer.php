<!-- Copyright Start -->
        <div class="container-fluid copyright text-body py-3 footer">
            <div class="container d-flex justify-content-between align-items-center flex-wrap text-white">
                
                <!-- Fecha -->
                <span id="ultima-actualizacion" class="me-3"></span>
                <script>
                    // Crear fecha actual
                    const fecha = new Date();

                    // Array con los nombres de los meses en español
                    const meses = [
                        "enero", "febrero", "marzo", "abril", "mayo", "junio",
                        "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
                    ];

                    // Formatear fecha: día, mes y año
                    const dia = fecha.getDate();
                    const mes = meses[fecha.getMonth()];
                    const año = fecha.getFullYear();

                    // Insertar texto en el span
                    document.getElementById("ultima-actualizacion").textContent =
                        `Última actualización: ${dia} de ${mes} ${año}`;
                </script>
                
                <!-- Texto del copyright -->
                <span>
                <i class="fas fa-copyright me-1"></i>
                2025 estadisticas360.com. Todos los derechos reservados.
                </span>
                
                <!-- Redes sociales -->
                <div class="d-inline-flex align-items-center ms-3">
                <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="">
                    <i class="fab fw-normal">𝕏</i>
                </a>
                <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="">
                    <i class="fab fa-facebook-f fw-normal"></i>
                </a>
                <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="">
                    <i class="fab fa-instagram fw-normal"></i>
                </a>
                <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="">
                    <i class="fab fa-youtube fw-normal"></i>
                </a>
                </div>
            </div>
        </div>
        <!-- Copyright End -->
