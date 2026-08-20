<?php 

include '../classes/Estado.php';
require '../../mapas_utils.php';

$webroot = base_url();

$veredas  = Estado::getVeredasByColor($rqst["nivel"]);


 ?>


<div class="table-responsive">
	<h2 class="text-info text-center">
		Información de las veredas con el puntaje acorde al nivel: <?php echo $rqst["nivel"] ?>, total <?php echo count($veredas) ?> veredas
	</h2>
	<table class="table table-hovered" id="dataVeredas">
		<thead>
			<tr>
				<th>Vereda</th>
				<th>Puntaje actual</th>
				<th>Municipio</th>
				<th>Departamento</th>
				<th>Batallón</th>
				<th>Brigada</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($veredas)): ?>
				<tr>
					<td class="text-center" colspan="6">
						No hay información
					</td>
				</tr>
			<?php else: ?>
				<?php foreach ($veredas as $key => $value): ?>
					<tr class="infoVeredas">
						<td>
							<a href="<?php echo $webroot ?>../../estado_vereda.php?mun=<?php echo $value["codigo_muncipio"] ?>&dep=<?php echo $value["codigo_departamento"] ?>&vereda=<?php echo $value["nombre_vereda"] ?>" class="btn btn-info btn-xs ml-2">
								<i class="fa fa-eye"></i>
							</a>
							<?php echo str_replace("-", " ", utf8_encode($value["nombre_vereda"])) ?> 
						</td>
						<td><?php echo $value["puntaje"] ?></td>
						<td><?php echo $value["municipio"] ?></td>
						<td><?php echo $value["departamento"] ?></td>
						<td><?php echo $value["batallon"] ?></td>
						<td><?php echo $value["brigada"] ?></td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>
	</table>
</div>