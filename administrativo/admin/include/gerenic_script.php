
  <!-- jQuery -->
  <script src="./plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="./plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="./plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="./plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <!-- <script src="./plugins/sparklines/sparkline.js"></script> -->
  <!-- JQVMap -->
  <script src="./plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="./plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="./plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="./plugins/moment/moment.min.js"></script>
  <script src="./plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="./plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="./plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="./plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>


  <!--  Plugin for Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!--Js de nosotros que hemos agregado -->
  <script type="text/javascript" src="admin/js/lib/util.js"></script>
  <script type="text/javascript" src="admin/js/jquery/solonumeros.js"></script>
  <script type="text/javascript" src="admin/js/jquery/numeral.min.2.0.6.js"></script>


  <!-- Select2 -->
  <script src="./plugins/select2/js/select2.full.min.js"></script>


    <!-- Incluye jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Incluye el script de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (class_exists('SessionData') && SessionData::hasPermission('ia.asistente.use') && !defined('YAMIL_WIDGET_RENDERED')): ?>
<?php define('YAMIL_WIDGET_RENDERED', true); ?>
<style>
#yamilBoton{position:fixed;right:22px;bottom:22px;z-index:99990;width:58px;height:58px;border-radius:50%;border:0;display:flex;align-items:center;justify-content:center;color:#fff;background:linear-gradient(135deg,#4F8CFF,#20427F);box-shadow:0 14px 32px rgba(32,66,127,.35);font-size:1.35rem;cursor:pointer;}
#yamilBoton:hover{transform:translateY(-2px);}
#yamilPanel{position:fixed;right:22px;bottom:92px;z-index:99991;width:360px;max-width:calc(100vw - 32px);height:520px;max-height:calc(100vh - 140px);border-radius:20px;background:#fff;box-shadow:0 28px 70px rgba(15,23,42,.28);display:flex;flex-direction:column;overflow:hidden;opacity:0;pointer-events:none;transform:translateY(12px);transition:opacity .18s ease,transform .18s ease;font-family:"Inter",system-ui,sans-serif;}
#yamilPanel.yamil-open{opacity:1;pointer-events:auto;transform:translateY(0);}
.yamil-head{padding:14px 16px;background:linear-gradient(135deg,#173D79,#0B1F43);color:#fff;display:flex;align-items:center;justify-content:space-between;}
.yamil-head strong{font-size:.86rem;font-weight:800;}
.yamil-head span{display:block;font-size:.65rem;color:rgba(255,255,255,.65);margin-top:1px;}
.yamil-head-actions{display:flex;gap:6px;}
.yamil-head-actions button{width:28px;height:28px;border:0;border-radius:8px;background:rgba(255,255,255,.14);color:#fff;font-size:.75rem;cursor:pointer;}
#yamilMensajes{flex:1;overflow-y:auto;padding:14px;background:#F6F8FC;display:flex;flex-direction:column;gap:8px;}
.yamil-msg{max-width:85%;padding:9px 12px;border-radius:14px;font-size:.78rem;line-height:1.45;}
.yamil-msg-user{align-self:flex-end;background:#20427F;color:#fff;border-bottom-right-radius:4px;}
.yamil-msg-bot{align-self:flex-start;background:#fff;color:#1D2939;border:1px solid #E6EBF2;border-bottom-left-radius:4px;max-width:92%;}
.yamil-msg-bot p{margin:0 0 8px;}
.yamil-msg-bot p:last-child{margin-bottom:0;}
.yamil-msg-bot h4{margin:6px 0 4px;font-size:.82rem;font-weight:800;color:#20427F;}
.yamil-msg-bot ul,.yamil-msg-bot ol{margin:0 0 8px;padding-left:18px;}
.yamil-msg-bot li{margin-bottom:2px;}
.yamil-msg-bot code{background:#F1F4F9;color:#20427F;padding:1px 4px;border-radius:4px;font-size:.72rem;}
.yamil-msg-bot table.yamil-md-table{border-collapse:collapse;width:100%;margin:4px 0 8px;font-size:.72rem;}
.yamil-msg-bot table.yamil-md-table th{background:#20427F;color:#fff;padding:5px 7px;text-align:left;}
.yamil-msg-bot table.yamil-md-table td{padding:5px 7px;border-bottom:1px solid #E6EBF2;}
.yamil-typing{display:flex;gap:4px;padding:12px 14px;}
.yamil-typing span{width:6px;height:6px;border-radius:50%;background:#98A2B3;animation:yamilBlink 1.1s infinite ease-in-out;}
.yamil-typing span:nth-child(2){animation-delay:.15s;}
.yamil-typing span:nth-child(3){animation-delay:.3s;}
@keyframes yamilBlink{0%,80%,100%{opacity:.3;}40%{opacity:1;}}
.yamil-inputbar{display:flex;gap:8px;padding:12px;border-top:1px solid #E6EBF2;background:#fff;}
.yamil-inputbar textarea{flex:1;resize:none;height:40px;border:1px solid #D9E0EA;border-radius:12px;padding:9px 11px;font-size:.78rem;font-family:inherit;}
.yamil-inputbar button{width:40px;height:40px;flex:0 0 40px;border:0;border-radius:12px;background:linear-gradient(135deg,#4F8CFF,#20427F);color:#fff;font-size:.9rem;cursor:pointer;}
.yamil-inputbar button:disabled{opacity:.6;cursor:default;}
</style>

<button type="button" id="yamilBoton" title="Abrir a Yamil, el asistente IA">
    <i class="fas fa-robot"></i>
</button>

<div id="yamilPanel">
    <div class="yamil-head">
        <div>
            <strong>Yamil</strong>
            <span>Asistente IA de Estadísticas 360</span>
        </div>
        <div class="yamil-head-actions">
            <button type="button" id="yamilNuevo" title="Nueva conversación"><i class="fas fa-plus"></i></button>
            <button type="button" id="yamilCerrar" title="Cerrar"><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div id="yamilMensajes"></div>
    <div class="yamil-inputbar">
        <textarea id="yamilInput" placeholder="Pregúntale algo a Yamil..."></textarea>
        <button type="button" id="yamilEnviar" title="Enviar"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script src="assets/js/ia-widget.js"></script>
<?php endif; ?>
