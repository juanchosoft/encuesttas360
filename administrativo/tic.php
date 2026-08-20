<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
// Permisos
$view = SessionData::getPermission(73);
$create = SessionData::getPermission(74);
$edit = SessionData::getPermission(75);
if (!$view) {
    require 'permiso_denegado.php';
}

$modulo = 'Tic';

include './admin/classes/Departamento.php';
include './admin/classes/SedesEducativas.php';
include './admin/classes/PcTic.php';


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = '';
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de sedes educativas
$arr = SedesEducativas::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$optionSed = "";
foreach ($arr as $val) {
    $optionSed .= "<option value='" . $val['id'] . "'>" . $val['nombre'] . " </option>";
}

// Informacion de los tic
$arrtic = PcTic::getAll(null);
$isvalid = $arrtic['output']['valid'];
$arrtic = $arrtic['output']['response'];
?>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ navigation menu ] start -->
    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->
    <div class="content">
        <div>
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                    <div class="card-header p-4 border-bottom bg-body">
                        <div class="row g-3 justify-content-between align-items-center">
                            <div class="col-12 col-md">
                                <h4 class="text-body mb-0 d-flex align-items-center">
                                <i class="uil uil-laptop me-2 fs-6"></i>Entregas Tecnología
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 code-to-copy"> 
                            <form id="formsecretaria" class="row g-3 mb-6 needs-validation" role="form" autocomplete="false" novalidate>

                                <input type="hidden" id="id" name="id" value="">
                                <input type="hidden" name="filtro" id="filtro" value="vereda" />
                                <input type="hidden" name="filtroVeredaById" id="filtroVeredaById" value="si" />

                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="date" id="date" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha<span
                                        class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="provincia" name="provincia" aria-label="Seleccione una subregión">
                                    <option selected value="Alto_Putumayo">Alto Putumayo</option>
                                        <option value="Medio_Putumayo">Medio Putumayo</option>
                                        <option value="Bajo_Putumayo">Bajo Putumayo</option>
                                    </select>
                                    <label for="provincia">Subregión <span class="text-danger mb-1">*</span></label>
                                </div>
                                </div>


                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <select onchange="DEPARTAMENTO.getMunicipios();"
                                                class="form-select"
                                                id="tbl_departamento_id"
                                                name="tbl_departamento_id"
                                                required
                                            value="" >
                                            <?php echo $optionDep; ?>
                                        </select>
                                        <label for="tbl_departamento_id">Departamento <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id" required></select>
                                        <label for="tbl_municipio_id">Municipio <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select"id="tbl_vereda_id"name="tbl_vereda_id" required></select>
                                        <label for="tbl_vereda_id">Vereda <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_sede_educativa_id" name="tbl_sede_educativa_id" required>
                                            <?php echo $optionSed; ?>
                                        </select>
                                        <label for="tbl_sede_educativa_id">Sede Educativa <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="tbl_instituciones_educativas_id"
                                            name="tbl_instituciones_educativas_id" placeholder="Institución Educativa" autocomplete="off" disabled>
                                        <label for="tbl_instituciones_educativas_id">Institución Educativa <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="zona" name="zona" required>
                                            <option value="" disabled selected>Seleccione la zona</option>
                                            <option value="Urbano">Urbano</option>
                                            <option value="Rural">Rural</option>
                                        </select>
                                        <label for="zona">Sector <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="robotica"
                                            name="robotica" placeholder="0" required  onkeydown="return event.key !== 'e' && event.key !== 'E';">
                                        <label for="robotica">Cantidad Kit Robótica Entregado</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control" id="computador_alumno" name="computador_alumno" placeholder="0">
                                        <label for="computador_alumno">Cantidad Dotación Computadores Educar Alumnos</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="computadores_institucion"
                                            name="computadores_institucion" placeholder="0">
                                        <label style="font-size: 10px;" for="computadores_institucion">Cantidad Dotación Computadores Educar Instituciones Educativas</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control" id="laboratorio_innovacion" name="laboratorio_innovacion" placeholder="0">
                                        <label for="laboratorio_innovacion">Cantidad Laboratorios de Innovación 2024</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <textarea class="form-control" 
                                                placeholder="Ingrese observaciones de la obra" id="observaciones" 
                                                name="observaciones"  style="height: 50px" required></textarea>
                                        <label for="observaciones">Observaciones</label>
                                    </div>
                                </div>
                                <?php if ($create && $edit) { ?>
                                    <div class="col-12">
                                        <label for="inputState">Foto</label>
                                        <div class="dropzone dropzone-multiple p-0 mb-5" id="my-awesome-dropzone" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                                            <iframe id='ifm' name='ifm' src="upload.php" width="100%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-12">
                                    <div class="row g-3 justify-content-end">
                                        <div class="col-auto">
                                            <button type="button" onclick="UTIL.clearForm('formsecretaria');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                                        </div>
                                        <?php if ($create && $edit): ?>
                                        <div class="col-auto">
                                            <button class="btn btn-primary px-5" type="button" onclick="TIC.validateData();">Ingresar</button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                            <div class="contenedor">
                                <div class="contenido">
                                    <div class="card">
                                        <h5 class="card-header"style="color: #37474f; font-size: 14px">Listado Tic</h5>
                                        <div class="card-body table-border-style mb-4">
                                            <!--INICIO TABLA DE DATOS-->
                                            <div class="table-responsive">
                                                <table id="dynamictable" class="table table-striped mb-0">
                                                    <thead>
                                                    <tr class="border-1">
                                                            <th>Editar</th>
                                                            <th>Fecha</th>
                                                            <th>Municipio</th>
                                                            <th>Vereda</th>
                                                            <th>Establecimiento Educativo</th>
                                                            <th>Zona</th>
                                                            <th>kits Robotica</th>
                                                            <th>Computadores Instituciones</th>
                                                            <th>Computadores Alumnos</th>
                                                            <th>Laboratorios Innovación</th>
                                                            <th>Observaciones</th>
                                                            <th>Foto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($isvalid && !empty($arrtic)): ?>
                                                            <?php foreach ($arrtic as $item): ?>
                                                                <?php
                                                                    $img = !empty($item["img"]) ? "assets/img/admin/" . htmlspecialchars($item["img"]) : 'assets/img/santander.png';
                                                                ?>
                                                                <tr>
                                                                    <!-- Botón Editar -->
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-primary" title="Editar dato"
                                                                            onclick="TIC.editData(<?= htmlspecialchars($item['id']) ?>)">
                                                                            <i class="uil uil-edit"></i>
                                                                        </button>
                                                                    </td>         
                                                                    <!-- Datos del tic -->
                                                                    <td><?= htmlspecialchars($item['date']) ?></td>
                                                                    <td><?= htmlspecialchars($item['municipio']) ?></td>
                                                                    <td><?= htmlspecialchars($item['nombre_vereda'] ?? 'N/A') ?></td>
                                                                    <td><?= htmlspecialchars($item['sede']) ?></td>
                                                                    <td><?= htmlspecialchars($item['zona']) ?></td>
                                                                    <td><?= htmlspecialchars($item['robotica']) ?></td>
                                                                    <td><?= htmlspecialchars($item['computadores_institucion']) ?></td>
                                                                    <td><?= htmlspecialchars($item['computador_alumno']) ?></td>
                                                                    <td><?= htmlspecialchars($item['laboratorio_innovacion']) ?></td>
                                                                    <td><?= htmlspecialchars($item['observaciones']) ?></td>
                                                                    <!-- Imagen -->
                                                                    <td class="text-primary text-center">
                                                                        <img 
                                                                            width="60" 
                                                                            height="60" 
                                                                            src="<?= $img ?>" 
                                                                            alt="Foto evidencia" 
                                                                            data-toggle="modal" 
                                                                            data-target="#imageModal<?= $item['id']; ?>" 
                                                                            style="cursor: pointer;" 
                                                                            class="rounded border object-fit-cover">
                                                                        
                                                                        <!-- Modal -->
                                                                        <div class="modal fade" id="imageModal<?= $item['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel<?= $item['id']; ?>" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="imageModalLabel<?= $item['id']; ?>">
                                                                                            Foto de la entrega TIC en <?= htmlspecialchars($item['nombre_vereda'] ?? 'vereda desconocida'); ?>
                                                                                        </h5>
                                                                                        <button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
                                                                                    </div>
                                                                                    <div class="modal-body text-center">
                                                                                        <img src="<?= $img ?>" alt="Imagen evidencia" class="img-fluid">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>

                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="9" class="text-center text-muted">No se encontraron registros.</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- FIN TABLA DE DATOS -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>                                    
        </div>
         <?php include './admin/include/footer.php'; ?>  
    </div>
    <!-- Required Js -->
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <?php include 'admin/include/gerenic_script.php'; ?>
            <!-- Required Js -->
            <script src="assets/js/vendor-all.min.js"></script>
            <script src="assets/js/plugins/bootstrap.min.js"></script>
            <script src="assets/js/pcoded.min.js"></script>
            <script type="text/javascript" src="admin/js/tic.js"></script>
            <?php include './admin/include/generic_dataTables.php'; ?>
<script>

//SOBREESCRIBIR EL JS DE DEPARTAMENTOS Y VEREDAS PARA QUE EL EDITAR PUEDA FUNCIONAR
let waitMunInterval = null;
let waitVerInterval = null;

TIC.editHandler = function (data) {
    UTIL.cursorNormal();
    if (!data.output.valid) {
        UTIL.mostrarMensajeError(data.output.response.content);
        return;
    }

    const res = data.output.response[0];
    const safe = val => (val === null || val === undefined || val === "null") ? "" : val;
    const normalize = val => (typeof val === "string") ? val.trim().toLowerCase().replace(/_/g, " ") : safe(val);

    // Asignar datos simples
    $("#id").val(safe(res.id));
    $("#date").val(safe(res.date));
    $("#robotica").val(safe(res.robotica));
    $("#computadores_institucion").val(safe(res.computadores_institucion));
    $("#computador_alumno").val(safe(res.computador_alumno));
    $("#laboratorio_innovacion").val(safe(res.laboratorio_innovacion));
    $("#observaciones").val(safe(res.observaciones));
    $("#cod_dane").val(safe(res.cod_dane));
    $("#tbl_sede_educativa_id").val(safe(res.tbl_sede_educativa_id)).trigger("change");

    // Provincia y zona (sector)
    const provinciaVal = normalize(res.provincia);
    const zonaVal = normalize(res.zona);
    $("#provincia_hidden").val(provinciaVal);
    $("#zona_hidden").val(zonaVal);

    if (!$("#provincia option[value='" + provinciaVal + "']").length && provinciaVal !== "") {
        $("#provincia").append(`<option value="${provinciaVal}" selected hidden>${provinciaVal}</option>`);
    }
    $("#provincia").val(provinciaVal);

    if (!$("#zona option[value='" + zonaVal + "']").length && zonaVal !== "") {
        $("#zona").append(`<option value="${zonaVal}" selected hidden>${zonaVal}</option>`);
    }
    $("#zona").val(zonaVal);

    // Departamento, municipio y vereda
    const depVal = safe(res.tbl_departamento_id);
    const municipioVal = safe(res.tbl_municipio_id);
    const veredaVal = safe(res.tbl_vereda_id);
    const municipioNombre = safe(res.municipio);
    const veredaNombre = safe(res.nombre_vereda);

    window.__edicionForzada = true;

    $("#tbl_municipio_id").html('');
    $("#tbl_vereda_id").html('');
    $("#tbl_departamento_id").val(depVal);

    if (waitMunInterval) clearInterval(waitMunInterval);
    if (waitVerInterval) clearInterval(waitVerInterval);

    DEPARTAMENTO.getMunicipios();

    let tries = 0;
    waitMunInterval = setInterval(() => {
        const $mun = $("#tbl_municipio_id");
        if ($mun.children().length > 0 || tries > 10) {
            clearInterval(waitMunInterval);

            $mun.val(municipioVal);
            if (!$mun.find(`option[value="${municipioVal}"]`).length) {
                $mun.append(`<option value="${municipioVal}" selected hidden>${municipioNombre}</option>`);
            }

            DEPARTAMENTO.getVeredasByMunicipioId();

            let triesVer = 0;
            waitVerInterval = setInterval(() => {
                const $ver = $("#tbl_vereda_id");
                if ($ver.children().length > 0 || triesVer > 10) {
                    clearInterval(waitVerInterval);

                    $ver.val(veredaVal);
                    if (!$ver.find(`option[value="${veredaVal}"]`).length) {
                        $ver.append(`<option value="${veredaVal}" selected hidden>${veredaNombre}</option>`);
                    }

                    window.__edicionForzada = false;
                }
                triesVer++;
            }, 300);
        }
        tries++;
    }, 300);

    // Imagen previa
    if (res.img && res.img !== "") {
        const imgPath = "assets/img/admin/" + res.img;
        if ($("#preview-img").length === 0) {
            $("#formsecretaria").append(`
                <div class="form-group">
                    <label>Vista previa imagen</label><br>
                    <img id="preview-img" src="" width="400" class="border rounded mt-1" />
                </div>
            `);
        }
        $("#preview-img").attr("src", imgPath);
    }
};

</script>
<script>
$(document).ready(function () {
    $("#tbl_municipio_id").on("change", function () {
        if (!window.__edicionForzada) {
            DEPARTAMENTO.getVeredasByMunicipioId();
        }
    });

    const departamentoInicial = $("#tbl_departamento_id").val();
    if (departamentoInicial) {
        DEPARTAMENTO.getMunicipios();
    }
});


</script>

<?php include 'admin/include/scriptsgober360.php'; ?>
<script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>