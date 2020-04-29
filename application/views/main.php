<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<title>Scraper para artículos del Diario Información</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<style type="text/css">

	</style>
</head>

<body>

	<div id="container" class="container-lg">
		<h1 class="text-center mb-5 mt-3">SCRAP PARA ARTÍCULOS</h1>
<!-- 
		<div id="body">
			<form>
				Inserta aquí la web para hacer scrap: <input type="text" name="scrap_url" id="scrap_url"><input type="submit">
			</form>
		</div> -->

		<form class="form" method="GET" action="<?= base_url() ?>scrap">
			<div class="form-group row">
				<label for="inputPassword" class="col-sm-1 col-form-label text-center">URL</label>
				<div class="col-sm-8 mb-4">
					<input type="text" class="form-control" name="scrap_url" id="scrap_url" placeholder="Inserta aquí la dirección a analizar">
				</div>
				<div class="text-center col-sm-3">
				<button type="submit" class="btn btn-primary mb-2 ">COMPROBAR</button>
				</div>
			</div>
	</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>