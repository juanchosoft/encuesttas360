<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Subir imagen</title>

  <!-- (Opcional) Dropzone assets si los usas en otras partes: aquí no es necesario -->
  <link href="vendors/dropzone/dropzone.css" rel="stylesheet">
  <script src="vendors/dropzone/dropzone-min.js"></script>

  <!-- AjaxUpload (dependía de jQuery viejo). Intentamos usar jQuery del padre, y si no, cargamos el tuyo. -->
  <script>
    (function(){
      if (!window.jQuery && window.parent && window.parent.jQuery) {
        window.jQuery = window.parent.jQuery; // usa el jQuery del sistema
      }
    })();
  </script>
  <script src="admin/include/imagen_uploader/js/jquery-1.3.1.min.js"></script>
  <script src="admin/include/imagen_uploader/js/AjaxUpload.2.0.min.js"></script>

  <style>
    :root{
      --brand:#20427F;
      --brand2:#132b52;
      --brand3:#2e58a8;
      --ink:#0f172a;
      --muted:#64748b;
      --line: rgba(15, 23, 42, .10);
      --bg: rgba(255,255,255,.92);
      --shadow: 0 18px 55px rgba(2, 6, 23, .10);
      --radius: 16px;
    }

    html, body{
      margin:0;
      padding:0;
      background: transparent;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Apple Color Emoji", "Segoe UI Emoji";
    }

    .uploader{
      border: 1px dashed rgba(32,66,127,.35);
      background: radial-gradient(600px 240px at 30% 20%, rgba(46,88,168,.18), transparent 60%),
                  linear-gradient(135deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      width: 100%;
      box-sizing: border-box;
      padding: 14px;
      cursor: pointer;
      transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
      position: relative;
      overflow: hidden;
    }
    .uploader:hover{
      transform: translateY(-1px);
      border-color: rgba(32,66,127,.55);
      box-shadow: 0 24px 70px rgba(2,6,23,.14);
    }

    .top{
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
    }

    .badge{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.14);
      color: var(--brand2);
      font-weight: 800;
      font-size: 12px;
      user-select:none;
    }

    .hint{
      color: var(--muted);
      font-size: 12px;
      font-weight: 700;
      user-select:none;
    }

    .body{
      display:flex;
      gap: 12px;
      align-items: center;
    }

    .icon{
      width: 46px;
      height: 46px;
      border-radius: 14px;
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.14);
      display:flex;
      align-items:center;
      justify-content:center;
      flex: 0 0 auto;
    }
    .icon img{
      width: 22px;
      height: 22px;
      opacity: .9;
    }

    .text{
      flex: 1 1 auto;
      min-width: 0;
    }
    .title{
      margin:0;
      font-weight: 900;
      color: var(--ink);
      font-size: 13px;
      line-height: 1.2;
    }
    .subtitle{
      margin: 4px 0 0 0;
      color: var(--muted);
      font-size: 12px;
      font-weight: 700;
    }

    .status{
      margin-top: 10px;
      font-size: 12px;
      font-weight: 800;
    }
    .status.ok{ color: #15803d; }
    .status.err{ color: #b91c1c; }
    .status.up{ color: var(--brand); }

    .preview{
      margin-top: 12px;
      border-radius: 14px;
      border: 1px solid var(--line);
      background: rgba(255,255,255,.9);
      padding: 10px;
      display:none;
    }
    .preview img{
      width: 100%;
      max-height: 140px;
      object-fit: contain;
      display:block;
      border-radius: 12px;
    }

    .drag{
      outline: 2px solid rgba(32,66,127,.30);
      border-color: rgba(32,66,127,.65);
      background: rgba(32,66,127,.05);
    }
  </style>
</head>

<body>

  <div class="uploader" id="upload_button" role="button" aria-label="Subir imagen">
    <div class="top">
      <div class="badge">📷 Subir logo</div>
      <div class="hint">PNG/JPG • Máx recomendado 2MB</div>
    </div>

    <div class="body">
      <div class="icon">
        <img src="assets/img/icons/image-icon.png" alt="Icono imagen">
      </div>
      <div class="text">
        <p class="title">Arrastra tu imagen aquí o haz clic para seleccionar</p>
        <p class="subtitle">Se actualizará automáticamente en la vista de configuración</p>
      </div>
    </div>

    <div id="uploadStatus" class="status"></div>

    <div class="preview" id="previewBox">
      <img id="previewImg" alt="Preview">
    </div>
  </div>

  <input type="hidden" value="0" name="valor_iframe" id="valor_iframe" />

  <script>
    (function () {

      function cleanResponse(resp){
        // Quita saltos, html, espacios, comillas
        if (resp === null || typeof resp === "undefined") return "";
        let s = String(resp).trim();
        s = s.replace(/^"+|"+$/g, ""); // quita comillas al inicio/fin
        return s;
      }

      function setStatus(type, html){
        var el = document.getElementById("uploadStatus");
        el.className = "status " + (type || "");
        el.innerHTML = html || "";
      }

      function setPreview(url){
        url = cleanResponse(url);
        if(!url) return;
        var box = document.getElementById("previewBox");
        var img = document.getElementById("previewImg");
        img.src = url + (url.includes("?") ? "&" : "?") + "v=" + Date.now();
        box.style.display = "block";
      }

      function updateParent(url){
        url = cleanResponse(url);
        try{
          var iframe = window.frameElement;
          if (iframe) {
            iframe.setAttribute("data-loaded", "true");
            iframe.setAttribute("data-url", url);
          }
        }catch(e){}

        // ✅ Si existe en la vista padre, actualiza el preview del logo inmediatamente
        try{
          if (window.parent && window.parent.document) {
            var img = window.parent.document.getElementById("logoPreview");
            var div = window.parent.document.getElementById("divLogoActual");
            if (img && url) {
              img.src = url + (url.includes("?") ? "&" : "?") + "v=" + Date.now();
            }
            if (div) {
              div.style.display = url ? "block" : "none";
            }
          }
        }catch(e){}
      }

      function validateExt(fileName){
        var ext = (fileName || "").split(".").pop().toLowerCase();
        return ["jpg","jpeg","png","bmp"].includes(ext);
      }

      // =============================
      // AjaxUpload click-to-upload
      // =============================
      try{
        new AjaxUpload('#upload_button', {
          action: 'upload_images_ajax.php',
          name: 'userfile',
          onSubmit: function (file, ext) {
            if (!(ext && /^(jpg|png|jpeg|bmp)$/i.test(ext))) {
              alert('Error: Solo se permiten archivos JPG, JPEG, PNG, BMP');
              return false;
            }
            setStatus("up", "⏳ Subiendo archivo...");
            this.disable();
          },
          onComplete: function (file, response) {
            this.enable();
            var url = cleanResponse(response);

            if(!url){
              setStatus("err", "❌ Error: respuesta inválida al subir.");
              return;
            }

            setStatus("ok", "✅ Imagen cargada correctamente");
            document.getElementById("valor_iframe").value = "1";

            setPreview(url);
            updateParent(url);
          }
        });
      }catch(e){
        // si AjaxUpload falla, igual mantenemos drag & drop
      }

      // =============================
      // Drag & Drop
      // =============================
      var dropZone = document.getElementById("upload_button");

      dropZone.addEventListener("dragover", function (e) {
        e.preventDefault();
        dropZone.classList.add("drag");
      });

      dropZone.addEventListener("dragleave", function () {
        dropZone.classList.remove("drag");
      });

      dropZone.addEventListener("drop", function (e) {
        e.preventDefault();
        dropZone.classList.remove("drag");

        var files = e.dataTransfer.files;
        if (!files || files.length === 0) return;

        var file = files[0];
        if (!validateExt(file.name)) {
          alert('Error: Solo se permiten archivos JPG, JPEG, PNG, BMP');
          return;
        }

        setStatus("up", "⏳ Subiendo archivo...");
        var formData = new FormData();
        formData.append("userfile", file);

        fetch('upload_images_ajax.php', {
          method: "POST",
          body: formData
        })
        .then(function(res){ return res.text(); })
        .then(function(response){
          var url = cleanResponse(response);

          if(!url){
            setStatus("err", "❌ Error: respuesta inválida al subir.");
            return;
          }

          setStatus("ok", "✅ Imagen cargada correctamente");
          document.getElementById("valor_iframe").value = "1";

          setPreview(url);
          updateParent(url);
        })
        .catch(function(err){
          console.error(err);
          setStatus("err", "❌ Error al subir imagen");
        });
      });

    })();
  </script>

</body>
</html>
