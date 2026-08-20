<!-- CSS Bootstrap + DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- JS DataTables + Bootstrap 5 -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>
$('#dynamictable').DataTable({
  searching: true,
  paging: true,
  pageLength: 10,
  lengthChange: false,
  info: false,
  ordering: true,
  language: {
    search: "Buscar:",
    zeroRecords: "No se encontraron registros",
    paginate: {
      previous: '<button class="btn btn-sm btn-phoenix-secondary me-2"><i class="fas fa-chevron-left"></i></button>',
      next: '<button class="btn btn-sm btn-phoenix-secondary"><i class="fas fa-chevron-right"></i></button>'
    }
  },
  dom: '<"top"f>rt<"bottom"p><"clear">'
});


</script>
<style>
  .dataTables_paginate .pagination {
    justify-content: center;
    gap: 0.5rem;
  }
  .dataTables_paginate .page-item .page-link {
    border: none;
    background-color: #f5f5f5;
    padding: 6px 12px;
    border-radius: 6px;
    color: #333;
  }
  .dataTables_paginate .page-item.active .page-link {
    background-color: #0d6efd;
    color: #fff;
  }
    /* Aplica a todas las filas impares y pares */
    #dynamictable tbody tr:nth-child(odd) {
    background-color: #ffffff !important;
  }

  #dynamictable tbody tr:nth-child(even) {
    background-color: rgb(228 231 238);
  }



  /* Ajuste para celdas oscuras de DataTables que heredan fondo */
  #dynamictable td {
    background-color: transparent !important;
  }
</style>
