$(document).on('ready', init);

function init() {
    // Nada necesario por ahora
}

const VISITASG = {
    editData: function (id) {
        $('#editModal').modal('show');
        $('#edit-loading').show();
        $('#editForm')[0].reset();

        const q = {
            op: "spi_visitasg_get",
            id: id
        };

        UTIL.callAjaxRqstPOST(q, VISITASG.editDataHandler);
    },

    editDataHandler: function (data) {
        $('#edit-loading').hide();

        if (data.output.valid) {
            const res = data.output.response[0];

            $('#id').val(res.id);
            $('#tbl_departamento_id').val(res.tbl_departamento_id);
            $('#tbl_municipio_id').val(res.tbl_municipio_id);
            $('#provincia').val(res.provincia);
            $('#linea').val(res.linea);
            $('#estrategia').val(res.estrategia);
            $('#campana').val(res.campana);
            $('#actividad').val(res.actividad);
            $('#poblacion').val(res.poblacion);
            $('#desc_actividad').val(res.desc_actividad);
            $('#inversion').val(res.inversion);
            $('#link').val(res.link);
            $("#tbl_linea_id").val(res.tbl_linea_id);
            $("#tbl_estrategia_id").val(res.tbl_estrategia_id);
        } else {
            alert("Error al cargar datos.");
        }
    },

    saveData: function () {
        Swal.fire({
          title: "¿Está seguro de que desea guardar estos cambios?",
          text: "Esta acción actualizará las referencias existentes.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            const data = {
              op: "spi_visitasg_save",
              id: $('#id').val(),
              tbl_departamento_id: $('#tbl_departamento_id').val(),
              tbl_municipio_id: $('#tbl_municipio_id').val(),
              provincia: $('#provincia').val(),
              poblacion: $('#poblacion').val(),
              desc_actividad: $('#desc_actividad').val(),
              inversion: $('#inversion').val(),
              linea: $('#linea').val(),
              estrategia: $('#estrategia').val(),
              campana: $('#campana').val(),
              actividad: $('#actividad').val(),
              link: $('#link').val(),
              tbl_linea: $('#tbl_linea_id').val(),
              tbl_estrategia: $('#tbl_estrategia_id').val(),
              foto1: $('#ifm1').attr('data-url') || '',
              foto2: $('#ifm2').attr('data-url') || '',
              foto3: $('#ifm3').attr('data-url') || '',
              foto4: $('#ifm4').attr('data-url') || '',
            };
      
            UTIL.cursorBusy();
      
            $.ajax({
              type: "POST",
              url: "admin/ajax/rqst.php",
              data: data,
              dataType: "json",
              success: function (response) {
                UTIL.cursorNormal();
                if (response.output.valid) {
                  UTIL.mostrarMensajeExitoso("Información guardada correctamente.");
                  $('#editModal').modal('hide');
                  location.reload();
                } else {
                  UTIL.mostrarMensajeError(response.output.response.content);
                }
              },
              error: function (xhr, status, error) {
                UTIL.cursorNormal();
                UTIL.mostrarMensajeError("Error de conexión: " + error);
              }
            });
          }
        });
      }
      
};




  

// const CUADRO_CONTROL_VISITASG = {
//     editData: function (id) {
//         q = {};
//         q.op = "spi_visitasg_get";
//         q.id = id;
//         UTIL.callAjaxRqstPOST(q, this.editDataHandler);
//     },
//     editDataHandler: function (data) {
//         UTIL.cursorNormal();
//         if (data.output.valid) {
//             var res = data.output.response[0];
//             $("#id").val(res.id);
//            console.log(res);

//            // setean la informacion

//            $('#editModal').modal('show'); 
  

//         } else {
//             UTIL.mostrarMensajeError(data.output.response.content);
//         }
//     }
// };
