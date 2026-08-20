<?php
include_once './admin/classes/Configuracion_Puntaje.php';


// Obtener datos de Configuracion_Puntaje
$arr = Configuracion_Puntaje::getAll(null);
$isvalid = $arr['output']['valid'];
$data = $arr['output']['response'];

// Ordenar los datos por "Desde" (rango_desde) de menor a mayor
usort($data, function ($a, $b) {
    return $a['rango_desde'] - $b['rango_desde'];
});
?>

<div style=" margin-left: auto;">
  <table class="table table-sm table-bordered text-center table-colores-mapa">
    <thead>
      <tr>
        <th colspan="2" class="titulo-tabla">Rango de Colores</th>
      </tr>
      <tr>
        <th scope="col">Desde</th>
        <th scope="col">Hasta</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($isvalid && count($data) > 0): ?>
        <?php foreach ($data as $index => $item): ?>
          <tr style="background: <?php echo htmlspecialchars($item['color'], ENT_QUOTES, 'UTF-8'); ?>; color: <?php echo ($index == 2) ? 'black' : 'white'; ?>">
            <td><?php echo htmlspecialchars($item['rango_desde'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($item['rango_hasta'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="2">No hay datos disponibles</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<style>
.table-colores-mapa {
  width: 100%;
  max-width: 100%;
  table-layout: fixed;
  border: 1px solid #ccc;
  font-size: 11px;
}
.table-colores-mapa th,
.table-colores-mapa td {
  padding: 1px 2px;
  border: 1px solid #ccc;
  word-wrap: break-word;
}
.titulo-tabla {
  background-color: #f8f9fa;
  font-weight: bold;
  font-size: 12px;
}
</style>
