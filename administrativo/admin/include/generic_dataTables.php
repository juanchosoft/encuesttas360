      <!-- Paginaciòn -->
      <script type="text/javascript" src="admin/js/datatables/jquery.dataTables.min.js"></script>
      <link href="admin/js/datatables/jquery.dataTables.min.css" rel="stylesheet" /> 
      <!-- Fin Paginaciòn -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

      <script>
        $(document).ready(function () {
          $('#dynamictable').DataTable({
            autoWidth: true,
            columnDefs: [{
              targets: ['_all'],
              className: 'mdc-data-table__cell'
            }],
            language: {
              paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
              },
              search: "Buscar:",
              zeroRecords: "No se encontraron registros"
            }
          });
        });
      </script>
