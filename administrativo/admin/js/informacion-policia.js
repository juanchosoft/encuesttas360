let tablaDinamica = null;
let categoriaActual = "hurtos";

$(function () {
  cargarCategoria(categoriaActual);

  $("#categoriaSelect").on("change", function () {
    categoriaActual = this.value;
    cargarCategoria(categoriaActual);
  });

  $("#customSearch").on("keyup", function () {
    if (tablaDinamica) {
      tablaDinamica.search(this.value).draw();
    }
  });
});

function cargarCategoria(categoria) {
  if (tablaDinamica) {
    tablaDinamica.destroy();
    $("#dynamictable").empty();
  }

  $("#loader").show();

  $.ajax({
    url: "./admin/controllers/apiCtrl.php",
    type: "POST",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify({
      method: "cargaCategoria",
      categoria: categoria,
      draw: 1,
      start: 0,
      length: 10,
      search: { value: "" },
      order: [{ column: 0, dir: "desc" }],
    }),
    success: function (response) {
      if (response.error) {
        alert("Error: " + response.error);
        $("#loader").hide();
        return;
      }

      const headers = response.headers || [];
      const columns = response.columns || [];

      // Generar encabezado dinámico
      let thead = "<thead><tr>";
      headers.forEach((header) => {
        thead += `<th>${header}</th>`;
      });
      thead += "</tr></thead>";

      $("#dynamictable").html(thead);

      // Inicializar DataTable
      tablaDinamica = $("#dynamictable").DataTable({
        order: [[0, "desc"]],
        dom: "lrtip",
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
          url: "./admin/controllers/apiCtrl.php",
          type: "POST",
          contentType: "application/json",
          data: function (d) {
            return JSON.stringify({
              method: "cargaCategoria",
              categoria: categoria,
              data: d,
              draw: d.draw,
              start: d.start,
              length: d.length,
              search: d.search,
              order: d.order,
            });
          },
          dataSrc: function (json) {
            return json.data;
          },
        },
        columns: columns.map((col) => ({
          data: col,
          name: col,
          defaultContent: "",
        })),
      });

      // Ocultar loader cuando DataTables finaliza la carga
      tablaDinamica.on("xhr", function () {
        $("#loader").hide();
      });
    },
    error: function (xhr) {
      console.error("Error AJAX:", xhr.responseText);
      $("#loader").hide();
    },
  });
}
