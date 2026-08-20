let graficoChart = null;
let categoriaActual = "hurtos";

$(function () {
  cargarGrafico(categoriaActual);

  $("#categoriaSelect").on("change", function () {
    categoriaActual = this.value;
    cargarGrafico(categoriaActual);
  });
});

function cargarGrafico(categoria) {
  $("#loader").show();

  $.ajax({
    url: "./admin/controllers/apiCtrl.php",
    type: "POST",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify({
      method: "cargaCategoriaGrafico",
      categoria: categoria,
    }),
    success: function (response) {
      $("#loader").hide();

      if (graficoChart) {
        graficoChart.destroy();
      }

      const datos = response.data || [];
      const meses = Array(12).fill(0);

      datos.forEach((item) => {
        const fecha =
          item.fecha_hecho || item["Fecha Hecho"] || item.fecha || "";
        const cantidad = parseInt(item.cantidad || 1);

        if (fecha.includes("/")) {
          const mes = parseInt(fecha.substring(3, 5)) - 1;
          if (!isNaN(mes)) {
            meses[mes] += cantidad;
          }
        } else if (item.mes) {
          const mesNum = parseInt(item.mes) - 1;
          if (!isNaN(mesNum)) {
            meses[mesNum] += cantidad;
          }
        }
      });

      const etiquetasMeses = [
        "Ene",
        "Feb",
        "Mar",
        "Abr",
        "May",
        "Jun",
        "Jul",
        "Ago",
        "Sep",
        "Oct",
        "Nov",
        "Dic",
      ];

      const canvas = document.getElementById("graficoCategoria");
      if (!canvas) {
        console.warn("No se encontró el canvas con id 'graficoCategoria'");
        return;
      }

      const ctx = canvas.getContext("2d");

      // Gradiente de color profesional
      const gradient = ctx.createLinearGradient(0, 0, 0, 400);
      gradient.addColorStop(0, "rgba(75, 192, 192, 0.9)");
      gradient.addColorStop(1, "rgba(75, 192, 192, 0.3)");

      graficoChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: etiquetasMeses,
          datasets: [
            {
              label: `Casos de ${categoria}`,
              data: meses,
              backgroundColor: gradient,
              borderColor: "rgba(75, 192, 192, 1)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(54, 162, 235, 0.8)",
              hoverBorderColor: "rgba(54, 162, 235, 1)",
            },
          ],
        },
        options: {
          responsive: true,
          //maintainAspectRatio: false,
          maintainAspectRatio: true,
          aspectRatio: 2,
          plugins: {
            title: {
              display: true,
              text: `Estadísticas por mes: ${categoria.toUpperCase()}`,
              font: {
                size: 20,
                weight: "bold",
              },
              padding: {
                top: 10,
                bottom: 20,
              },
              color: "#333",
            },
            legend: {
              display: true,
              position: "bottom",
              labels: {
                color: "#444",
                font: {
                  size: 14,
                },
              },
            },
            tooltip: {
              backgroundColor: "#222",
              titleColor: "#fff",
              bodyColor: "#ddd",
              borderColor: "#999",
              borderWidth: 1,
              callbacks: {
                label: function (context) {
                  return ` ${context.parsed.y} casos`;
                },
              },
            },
          },
          scales: {
            x: {
              ticks: {
                color: "#555",
                font: {
                  weight: "bold",
                },
              },
              grid: {
                display: false,
              },
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: "Cantidad de casos",
                color: "#333",
                font: {
                  size: 14,
                },
              },
              ticks: {
                stepSize: 1,
                color: "#555",
              },
              grid: {
                color: "#eee",
              },
            },
          },
        },
      });
    },
    error: function (xhr) {
      $("#loader").hide();
      console.error("Error al cargar gráfico:", xhr.responseText);
    },
  });
}
