(function () {
  "use strict";

  const return_page = "informes_ia.php";

  function ajaxRqst(data, cbOk, cbFail) {
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: data,
      success: cbOk,
      error: cbFail || function () {
        try { UTIL.cursorNormal(); } catch (e) {}
        try { UTIL.mostrarMensajeError("Hubo un error de comunicación con el servidor."); } catch (e) { alert("Error de comunicación."); }
      }
    });
  }

  function construirDocumentoInforme(titulo, contenidoHtml) {
    const estilo = [
      "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap');",
      "*{box-sizing:border-box;}",
      "body{margin:0;padding:28px 32px 40px;color:#101828;background:#fff;font-family:'Inter',system-ui,sans-serif;font-size:14px;line-height:1.65;}",
      "h1,h2,h3,h4{font-family:'Manrope','Inter',sans-serif;color:#152A52;font-weight:800;letter-spacing:-.2px;}",
      "h1{font-size:1.5rem;margin:0 0 6px;}",
      "h2{font-size:1.18rem;margin:26px 0 10px;padding-bottom:6px;border-bottom:2px solid #E6EBF2;}",
      "h3{font-size:1.02rem;margin:18px 0 8px;color:#2F67C4;}",
      "h4{font-size:.92rem;margin:14px 0 6px;color:#375C99;}",
      "p{margin:0 0 12px;color:#344054;}",
      "ul,ol{margin:0 0 14px;padding-left:22px;color:#344054;}",
      "li{margin-bottom:4px;}",
      "small{color:#8A97AB;font-size:.76rem;}",
      "table{border-collapse:collapse;width:100%;margin:4px 0 18px;font-size:.86rem;}",
      "th{background:#1D3A6E;color:#fff;padding:9px 11px;text-align:left;font-weight:700;}",
      "td{padding:8px 11px;border-bottom:1px solid #E6EBF2;}",
      "tbody tr:nth-child(even) td{background:#F7F9FC;}",
      "table.s360-table-highlight th{background:linear-gradient(135deg,#4F8CFF,#20427F);}",
      "table.s360-table-highlight{box-shadow:0 10px 26px rgba(32,66,127,.14);border-radius:10px;overflow:hidden;}",
      "table.s360-kpis{border:0;width:100%;margin:16px 0 22px;}",
      "table.s360-kpis td{border:0;padding:6px;}",
      "td.s360-kpi{background:linear-gradient(150deg,#F3F7FF,#EAF1FF);border:1px solid #DCE7FB;border-radius:14px;padding:16px 14px !important;text-align:center;width:1%;}",
      "div.s360-kpi-value{font-family:'Manrope',sans-serif;font-size:1.7rem;font-weight:800;color:#20427F;line-height:1.1;}",
      "div.s360-kpi-label{margin-top:4px;font-size:.68rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;color:#5C7195;}",
      "div.s360-callout,blockquote.s360-callout{margin:14px 0;padding:12px 16px;border-radius:10px;border-left:4px solid;font-size:.88rem;}",
      "div.s360-callout-info{background:#EFF6FF;border-color:#3B82F6;color:#1E3A5F;}",
      "div.s360-callout-warning{background:#FFF8E6;border-color:#F5A623;color:#6B4A05;}",
      "div.s360-callout-danger{background:#FDEEEE;border-color:#E5484D;color:#7A1B1E;}",
      "div.s360-callout-success{background:#EAFBF3;border-color:#12B981;color:#0B5B3E;}",
      "span.s360-badge-dato,span.s360-badge-interpretacion{display:inline-block;font-size:.62rem;font-weight:800;letter-spacing:.4px;text-transform:uppercase;padding:2px 8px;border-radius:999px;margin-right:6px;vertical-align:middle;}",
      "span.s360-badge-dato{background:#E7F7EF;color:#0B7A45;}",
      "span.s360-badge-interpretacion{background:#EEF1FF;color:#3B49C7;}",
      "div.s360-quote,blockquote.s360-quote{margin:22px 0 6px;padding:16px 20px;background:#0B1F43;color:#EAF1FF;border-radius:14px;font-family:'Manrope',sans-serif;font-weight:600;font-size:.95rem;line-height:1.55;}",
    ].join("");

    return "<html><head><meta charset='utf-8'><title>" + titulo.replace(/</g, "&lt;") + "</title><style>" + estilo + "</style></head><body>"
      + "<h1>" + titulo.replace(/</g, "&lt;") + "</h1>"
      + (contenidoHtml || "")
      + "</body></html>";
  }

  window.INFORMES_IA = window.INFORMES_IA || {

    verInforme: function (id) {
      const rq = { op: "informeiaview", id: id };

      try { UTIL.cursorBusy(); } catch (e) {}

      ajaxRqst(rq, function (data) {
        try { UTIL.cursorNormal(); } catch (e) {}

        if (!data?.output?.valid) {
          UTIL.mostrarMensajeError(data?.output?.response?.content || "No fue posible cargar el informe.");
          return;
        }

        const res = data.output.response || {};
        document.getElementById("s360InformeModalTitulo").textContent = res.titulo || "Informe";

        const frame = document.getElementById("s360InformeFrame");
        frame.srcdoc = construirDocumentoInforme(res.titulo || "Informe", res.contenido_html || "");

        const modalEl = document.getElementById("s360InformeModal");
        if (window.bootstrap && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
          $(modalEl).modal("show");
        }
      });
    },

    deleteData: function (id) {
      try { UTIL.cursorBusy(); } catch (e) {}
      const rq = { op: "informeiadelete", id: id };

      ajaxRqst(rq, function (data) {
        try { UTIL.cursorNormal(); } catch (e) {}

        if (data?.output?.valid) {
          UTIL.mostrarMensajeExitoso("Informe eliminado correctamente");
          setTimeout(function () { window.location = return_page; }, 900);
        } else {
          UTIL.mostrarMensajeError(data?.output?.response?.content || "No fue posible eliminar el informe.");
        }
      });
    }
  };

})();
