<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">
<body>

<div class="container-lg">

	<div class=" row border rounded p-5 shadow">
		<div class="col-12 p-3 my-2 border rounded"><?php if ($title_long < 70) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>La etiqueta TITLE tiene <?= $title_long ?> caracteres de los 70 requeridos</span></div>
		<div class="col-12 p-3 my-2 border rounded"><?php if ($meta_long < 100) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>La meta descrición tiene <?= $meta_long ?> caracteres de los 100 requeridos</span></div>
		<div class="col-12 p-3 my-2 border rounded">
			<?php if ($firma == 1) { ?>
				<span class="text-success">El autor ha impreso su firma correctamente</span>
			<?php } else { ?>
				<span class="text-danger">El autor no ha impreso su firma correctamente (no existe o no tiene enlace)</span>
			<?php } ?>
		</div>
		<div class="col-12 p-3 my-2 border rounded"><?php if ($cuerpo_long < 500) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>El cuerpo del artículo tiene <?= $cuerpo_long ?> caracteres de los 500 requeridos</span></div>
		<div class="col-6 border rounded">
			<div class="col-12 p-3 my-2 "><?php if ($imagenes < 2) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>Se han encontrado <?= $imagenes ?> imágenes (Mínimo 2)</span></div>
			<div class="col-12 p-3 my-2 "><?php if ($alt_title < $imagenes) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>Del total de imágenes, <?= $alt_title ?> tienen un alt o title correcto (>20 palabras)</span></div>
			<div class="col-12 p-3 my-2 "><?php if ($pies < $imagenes) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>Del total de imágenes, <?= $pies ?> tienen pie de foto</span></div>
		</div>
		<div class="col-6 border rounded">
			<div class="col-12 p-3 my-2 ">Se han encontrado un total de <?= $links ?> enlaces</span></div>
			<div class="col-12 p-3 my-2 "><?php if ($spon_nofol < $links) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>De todos los enlaces, <?= $spon_nofol ?> tienen el atributo <i>nofollowed</i> o <i>sponsored</i></span></div>
			<div class="col-12 p-3 my-2 ">
				<?php if ($link50 == 1) { ?>
					<span class="text-success">Existen enlaces tras el 50% de la noticia</span>
				<?php } else { ?>
					<span class="text-danger">No existen enlaces tras el 50% de la noticia</span>
				<?php } ?>
			</div>
		</div>

		<div class="col-12 p-3 my-2 border rounded">Se han encontrado un total de <?= $videos ?> video/s</div>
		<div class="col-12 p-3 my-2 border rounded">Se han encontrado un total de <?= $mapas ?> mapa/s</div>
		<div class="col-12 p-3 my-2 border rounded"><?php if ($negrita < 5) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>El texto contiene un total de <?= $negrita ?> bloque/s en negrita de los 5 requeridos</div>
		<div class="col-12 p-3 my-2 border rounded">El texto contiene un total de <?= $lad_long ?> ladillo/s</div>
		<div class="col-12 p-3 my-2 border rounded"><?php if ($tags < 5) { ?><span class="text-danger"><?php } else { ?><span class="text-success"><?php } ?>El texto contiene un total de <?= $tags ?> Tag/s del mínimo de 5</span></div>


	</div>
</div>
</body>

</html>