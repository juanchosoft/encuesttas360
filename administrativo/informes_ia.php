<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/InformeIA.php';

include './admin/include/generic_info_configuracion.php';

$view = SessionData::hasPermission('ia.informes.view');
$manage = SessionData::hasPermission('ia.informes.manage');
$puedeEliminar = $manage || SessionData::hasPermission('ia.informes.delete');

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

$arrResponse = InformeIA::getAll(null);
$isvalid = $arrResponse['output']['valid'] ?? false;
$arr = $arrResponse['output']['response'] ?? [];
$modulo = 'Informes IA';

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

$totalInformes = is_array($arr) ? count($arr) : 0;
?>

<body class="s360-partidos-page">

<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

<?php include './admin/include/navbar.php'; ?>
<?php include './admin/include/header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
    --s360-primary:#20427F;
    --s360-primary-2:#2F67C4;
    --s360-primary-3:#4F8CFF;
    --s360-deep:#0B1F43;
    --s360-page:#F3F6FB;
    --s360-text:#101828;
    --s360-text-2:#344054;
    --s360-muted:#667085;
    --s360-soft:#98A2B3;
    --s360-line:#E6EBF2;
    --s360-r-xxl:30px;
    --s360-r-xl:24px;
    --s360-shadow:0 22px 60px rgba(15,23,42,.09);
    --s360-shadow-soft:0 12px 32px rgba(15,23,42,.065);
}
*{box-sizing:border-box;}
body.s360-partidos-page{
    margin:0;
    background:radial-gradient(820px 440px at 4% -4%, rgba(47,103,196,.11), transparent 65%),linear-gradient(180deg,#F7F9FC 0%,#F2F5FA 100%);
    color:var(--s360-text);
    font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}
.content{padding-top:18px !important;padding-bottom:38px !important;}
.s360-shell{width:100%;max-width:1400px;margin:0 auto;padding:0 18px;}
.s360-hero{
    position:relative;overflow:hidden;min-height:150px;padding:26px 30px;margin-bottom:16px;
    border:1px solid rgba(255,255,255,.12);border-radius:var(--s360-r-xxl);color:#fff;
    background:radial-gradient(520px 260px at 11% 3%, rgba(79,140,255,.34), transparent 65%),linear-gradient(135deg,#173D79 0%,#102A56 44%,#09172F 100%);
    box-shadow:0 28px 75px rgba(12,31,66,.24);
}
.s360-eyebrow{display:inline-flex;align-items:center;gap:8px;min-height:32px;padding:7px 11px;margin-bottom:13px;border:1px solid rgba(255,255,255,.14);border-radius:999px;color:rgba(255,255,255,.88);background:rgba(255,255,255,.075);font-size:.68rem;font-weight:800;letter-spacing:.62px;text-transform:uppercase;}
.s360-live-dot{width:7px;height:7px;border-radius:50%;background:#5DE4A0;box-shadow:0 0 0 5px rgba(93,228,160,.11);}
.s360-hero h1{margin:0;color:#fff;font-family:"Manrope","Inter",sans-serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;letter-spacing:-1.2px;}
.s360-hero h1 span{color:#A9C7FF;}
.s360-hero p{max-width:760px;margin:10px 0 0;color:rgba(255,255,255,.70);font-size:.88rem;line-height:1.6;font-weight:500;}
.s360-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 15px;margin-bottom:16px;border:1px solid var(--s360-line);border-radius:18px;background:rgba(255,255,255,.92);box-shadow:var(--s360-shadow-soft);}
.s360-toolbar-copy{display:flex;align-items:center;gap:10px;}
.s360-toolbar-icon{width:38px;height:38px;flex:0 0 38px;display:flex;align-items:center;justify-content:center;border-radius:12px;color:var(--s360-primary);background:#EDF4FF;font-size:.9rem;}
.s360-toolbar-copy strong{display:block;color:var(--s360-text);font-size:.79rem;font-weight:800;}
.s360-toolbar-copy span{display:block;margin-top:2px;color:var(--s360-soft);font-size:.66rem;font-weight:600;}
.s360-directory{overflow:hidden;border:1px solid var(--s360-line);border-radius:var(--s360-r-xl);background:#fff;box-shadow:var(--s360-shadow);}
.s360-directory-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px;border-bottom:1px solid #EDF0F5;background:linear-gradient(180deg,#FFFFFF,#FBFCFF);}
.s360-directory-head-left{display:flex;align-items:center;gap:11px;}
.s360-directory-icon{width:43px;height:43px;flex:0 0 43px;display:flex;align-items:center;justify-content:center;border-radius:13px;color:#fff;background:linear-gradient(135deg,var(--s360-primary-3),var(--s360-primary));font-size:.92rem;}
.s360-directory h2{margin:0;color:var(--s360-text);font-family:"Manrope","Inter",sans-serif;font-size:1rem;font-weight:800;}
.s360-directory p{margin:3px 0 0;color:var(--s360-soft);font-size:.66rem;font-weight:600;}
.s360-badge{display:inline-flex;align-items:center;gap:6px;min-height:31px;padding:6px 10px;border:1px solid #DCE8FA;border-radius:999px;color:#265EA9;background:#EEF5FF;font-size:.65rem;font-weight:800;}
.s360-table-body{padding:15px;}
.s360-directory table{width:100% !important;margin:0 !important;border-collapse:separate !important;border-spacing:0 7px !important;}
.s360-directory table thead th{padding:10px 12px !important;border:0 !important;color:#667085 !important;background:transparent !important;font-size:.63rem !important;font-weight:800 !important;letter-spacing:.45px;text-transform:uppercase;white-space:nowrap !important;}
.s360-directory table tbody td{padding:11px 12px !important;border-top:1px solid #E9EDF4 !important;border-bottom:1px solid #E9EDF4 !important;color:#344054 !important;background:#fff !important;font-size:.71rem !important;font-weight:600;vertical-align:middle !important;}
.s360-directory table tbody td:first-child{border-left:1px solid #E9EDF4 !important;border-radius:13px 0 0 13px;}
.s360-directory table tbody td:last-child{border-right:1px solid #E9EDF4 !important;border-radius:0 13px 13px 0;}
.s360-actions-cell{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.s360-icon-btn{width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center;padding:0;border-radius:10px !important;}
.s360-view-btn{border:0 !important;color:#fff !important;background:linear-gradient(135deg,#4F8CFF,#2563B9) !important;box-shadow:0 8px 16px rgba(37,99,185,.17);}
.s360-pdf-btn{border:0 !important;color:#fff !important;background:linear-gradient(135deg,#2FBF71,#1C8F52) !important;box-shadow:0 8px 16px rgba(28,143,82,.17);}
.s360-delete-btn{border:0 !important;color:#fff !important;background:linear-gradient(135deg,#F36A6A,#D83B47) !important;box-shadow:0 8px 16px rgba(216,59,71,.16);}
.s360-empty-row{padding:45px 20px !important;text-align:center;}
.s360-empty-inline{display:flex;flex-direction:column;align-items:center;justify-content:center;}
.s360-empty-inline i{width:52px;height:52px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;border-radius:16px;color:var(--s360-primary);background:#EEF5FF;font-size:1.1rem;}
.s360-empty-inline strong{color:#344054;font-size:.76rem;}
.s360-empty-inline span{margin-top:3px;color:#98A2B3;font-size:.64rem;}
#s360InformeModal .modal-dialog{max-width:min(1180px,94vw);height:92vh;margin:4vh auto;}
#s360InformeModal .modal-content{height:100%;}
#s360InformeModal .modal-body{flex:1 1 auto;padding:14px;overflow:hidden;}
#s360InformeFrame{width:100%;height:100%;border:1px solid var(--s360-line);border-radius:12px;background:#fff;}
</style>

<div class="content">
    <div class="s360-shell">

        <section class="s360-hero" aria-label="Informes generados por Yamil">
            <div class="s360-eyebrow">
                <span class="s360-live-dot"></span>
                Estadística360 · Asistente IA
            </div>
            <h1>Informes <span>IA</span></h1>
            <p>Informes generados por Yamil a partir de tus conversaciones: interpretación de sondeos, cuestionarios y análisis, listos para consultar en pantalla o descargar en PDF.</p>
        </section>

        <section class="s360-toolbar">
            <div class="s360-toolbar-copy">
                <div class="s360-toolbar-icon"><i class="fas fa-file-lines"></i></div>
                <div>
                    <strong>Historial de informes</strong>
                    <span>Generados desde el chat de Yamil, nunca creados manualmente aquí.</span>
                </div>
            </div>
        </section>

        <section class="s360-directory">
            <div class="s360-directory-head">
                <div class="s360-directory-head-left">
                    <div class="s360-directory-icon"><i class="fas fa-robot"></i></div>
                    <div>
                        <h2>Informes generados</h2>
                        <p>Consulta, descarga en PDF o elimina los informes de tu asistente.</p>
                    </div>
                </div>
                <span class="s360-badge">
                    <i class="fas fa-database"></i>
                    <?= (int) $totalInformes ?> <?= $totalInformes === 1 ? 'registro' : 'registros' ?>
                </span>
            </div>

            <div class="s360-table-body">
                <div class="table-responsive">
                    <table id="dynamictable" class="table table-sm fs-9 mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Acciones</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="dt-list">
                        <?php if ($isvalid && count($arr) > 0): ?>
                            <?php foreach ($arr as $item): ?>
                                <tr>
                                    <td>
                                        <div class="s360-actions-cell">
                                            <button type="button" class="btn s360-icon-btn s360-view-btn" title="Ver informe" onclick="INFORMES_IA.verInforme(<?= (int) $item['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a class="btn s360-icon-btn s360-pdf-btn" title="Descargar PDF" href="admin/ajax/ia_informe_pdf.php?id=<?= (int) $item['id'] ?>" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <?php if ($puedeEliminar || $manage): ?>
                                                <button type="button" class="btn s360-icon-btn s360-delete-btn btn-delete-informe" title="Eliminar informe" data-id="<?= (int) $item['id'] ?>" data-titulo="<?= h($item['titulo']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><strong><?= h($item['titulo']) ?></strong></td>
                                    <td><?= h(trim($item['autor'] ?? '') !== '' ? $item['autor'] : '—') ?></td>
                                    <td><?= h(date('d/m/Y H:i', strtotime($item['dt_create']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="s360-empty-row">
                                    <div class="s360-empty-inline">
                                        <i class="fas fa-file-lines"></i>
                                        <strong>Todavía no hay informes</strong>
                                        <span>Pídele a Yamil, tu asistente IA, que genere un informe desde el chat.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <?php include './admin/include/footer.php'; ?>
    </div>
</div>

<div class="modal fade" id="s360InformeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="s360InformeModalTitulo">Informe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <iframe id="s360InformeFrame" sandbox=""></iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'admin/include/gerenic_script.php'; ?>
<?php include './admin/include/generic_dataTables.php'; ?>
<script src="admin/js/informes_ia.js"></script>
<?php include 'admin/include/scriptsgober360.php'; ?>

<script>
(function(){
    "use strict";
    $(document).on("click", ".btn-delete-informe", function(){
        const id = $(this).data("id");
        const titulo = $(this).data("titulo") || "este informe";
        const ejecutar = function(){ window.INFORMES_IA.deleteData(id); };
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "¿Eliminar informe?",
                html: "Estás a punto de eliminar: <strong>" + titulo + "</strong><br><span style='color:#b42318;'>Esta acción no se puede deshacer.</span>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                reverseButtons: true,
                focusCancel: true,
                confirmButtonColor: "#D83B47"
            }).then(function(r){ if (r.isConfirmed) ejecutar(); });
        } else if (confirm("¿Eliminar " + titulo + "?")) {
            ejecutar();
        }
    });
})();
</script>

</body>
</html>
