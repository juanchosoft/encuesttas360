<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir imagen</title>
  <script src="vendors/dropzone/dropzone-min.js"></script>
  <link href="vendors/dropzone/dropzone.css" rel="stylesheet">
  <script src="admin/include/imagen_uploader/js/jquery-1.3.1.min.js"></script>
  <script src="admin/include/imagen_uploader/js/AjaxUpload.2.0.min.js"></script>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: transparent;
    }

    #upload_button {
      border: 2px dashed #ccc;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      
      transition: border-color 0.3s ease;
      width: 100%;
      box-sizing: border-box;
      cursor: pointer;
    }

    #upload_button:hover {
      border-color: #4e73df;
      background-color: #e8f0fe;
    }

    #upload_button img {
      width: 40px;
      margin-top: 10px;
    }

    #upload_button span {
      color: #007bff;
      text-decoration: underline;
      display: block;
      margin-top: 5px;
    }

    #uploadStatus {
      margin-top: 10px;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <!-- <div id="upload_button"> -->
  <div class="dropzone dropzone-multiple p-0 mb-5" id="upload_button" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">

    <p>Arrastra tu pdf aquí</p>
    <span>o haz clic para seleccionar</span>
    <img src="assets/img/icons/file.png" alt="PDF" />
    <div id="uploadStatus"></div>
  </div>

  <input type="hidden" value="0" name="valor_iframe" id="valor_iframe" />

  <script>
  $(document).ready(function () {
    var button = $('#upload_button');

    // AjaxUpload por clic (como ya estaba)
    new AjaxUpload('#upload_button', {
      action: 'upload_images_ajax.php',
      name: 'userfile',
      onSubmit: function (file, ext) {
        var extensiones_permitidas = [".pdf"];
        if (!(ext && /^(pdf)$/i.test(ext))) {
          alert('Error: Solo se permiten archivos con extensiones: ' + extensiones_permitidas.join(", "));
          return false;
        } else {
          $('#uploadStatus').html("Subiendo PDF...");
          this.disable();
        }
      },
      onComplete: function (file, response) {
        $('#uploadStatus').html("<span style='color: green;'>PDF Cargado correctamente</span>");
        $('#valor_iframe').val('1');
        this.enable();

        var iframe = window.frameElement;
        if (iframe) {
          iframe.setAttribute("data-loaded", "true");
          iframe.setAttribute("data-url", response);
        }
      }
    });

    // Soporte para Drag & Drop
    const dropZone = document.getElementById("upload_button");

    dropZone.addEventListener("dragover", function (e) {
      e.preventDefault();
      dropZone.style.borderColor = "#4e73df";
      dropZone.style.backgroundColor = "#e8f0fe";
    });

    dropZone.addEventListener("dragleave", function () {
      dropZone.style.borderColor = "#ccc";
      dropZone.style.backgroundColor = "#f8f9fa";
    });

    dropZone.addEventListener("drop", function (e) {
      e.preventDefault();
      dropZone.style.borderColor = "#ccc";
      dropZone.style.backgroundColor = "#f8f9fa";

      const files = e.dataTransfer.files;
      if (files.length > 0) {
        const file = files[0];
        const ext = file.name.split('.').pop().toLowerCase();
        const extensiones_permitidas = ["pdf"];

        if (!extensiones_permitidas.includes(ext)) {
          alert('Error: Solo se permiten archivos con extensiones: ' + extensiones_permitidas.join(", "));
          return;
        }

        $('#uploadStatus').html("Subiendo archivo...");

        const formData = new FormData();
        formData.append("userfile", file);

        fetch('upload_pdf_ajax.php', {
          method: "POST",
          body: formData
        })
        .then(res => res.text())
        .then(response => {
          $('#uploadStatus').html("<span style='color: green;'>PDF cargado correctamente</span>");
          $('#valor_iframe').val('1');

          const iframe = window.frameElement;
          if (iframe) {
            iframe.setAttribute("data-loaded", "true");
            iframe.setAttribute("data-url", response);
          }
        })
        .catch(err => {
          console.error(err);
          $('#uploadStatus').html("<span style='color: red;'>Error al subir pdf</span>");
        });
      }
    });
  });
</script>

</body>
</html>
