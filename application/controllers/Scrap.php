<?php
defined('BASEPATH') or exit('No direct script access allowed');

require "application/libraries/vendor/autoload.php";

// use chriskacerguis\RestServer\RestController;
use Goutte\Client;

use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

class Scrap extends REST_Controller
{
	public function index_get()
	{

		$url =  $_GET['scrap_url'];
		$client = new Client();
		$crawler = $client->request('GET', $url);

		$final_url=substr($url,-10);
		//print_r($final_url);

		if (preg_match("/^\d{5}\.html$/", $final_url)){

		include 'selects_css.php';
		
		//CONTEO DE PALABRAS EN EL TITLE
		$output = $crawler->filter($selector_title)->extract(array('_text'));
		$title = strlen($output[0]);
		
		// print_r($metas) ;


		// //CONTEO DE PALABRAS EN LA META DESCRIPCIÓN
		// $output = $metas['description'];
		// $meta = strlen($output);

		
		// //FIRMA DEL AUTOR
		$output = $crawler->filter($selector_redactor)->extract(array('_text'));
		if (isset(($output[0]))) $autor = 1;
		else $autor = 0;
		

		// //CONTEO DE PALABRAS EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_cuerpo)->extract(array('_text'));
		$str = mb_strtoupper($output[0], 'UTF-8');
		$conv = array("Á" => "A", "É" => "E", "Í" => "I", "Ó" => "O", "Ú" => "U");
		$text_final = strtr($str, $conv);
		$cuerpo = str_word_count($text_final);


		// //LADILLOS
		 $output = $crawler->filter($selector_ladillos);
		 $ladillos = count($output);
		

		// //CONTEO DE FRASES EN NEGRITA
		$output = $crawler->filter($selector_negrita);
		$output_h2 = $crawler->filter($selector_negrita_h2);
		$output_a = $crawler->filter($selector_negrita_a);
		$output_h3 = $crawler->filter($selector_negrita_h3);
		$output_str_a = $crawler->filter($selector_negrita_str_a);
		$output_a_str = $crawler->filter($selector_negrita_a_str);

		$neg = count($output) - count($output_h2) - count($output_h3) - count($output_a) - count($output_str_a) - count($output_a_str);
		

		// //CONTEO DE ENLACES EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_enlace)->extract(array('href'));
		$links = (count($output));


		$links_internos = 0;
		for ($i = 0; $i < count($output); $i++) {
			if ((substr($output[$i], 0, 32) == "http://www.diarioinformacion.com") ||
				(substr($output[$i], 0, 25) == "http://www.informacion.es")||
				(substr($output[$i], 0, 1) == "/")) ++$links_internos;
		}

		for ($i = 0; $i < count($output); $i++) {
				if (substr($output[$i], 0, 18)==(substr($url, 0, 18))) ++$links_internos;
			}

		// //CONTEO DE ENLACES CON NOFOLLOW O SPONSORED EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_nofollow);
		$nofollow = (count($output));

		$output = $crawler->filter($selector_sponsored);
		$sponsored = (count($output));

		$output = $crawler->filter($selector_mail);
		$mails = (count($output));

		$output = $crawler->filter($selector_tel);
		$tels = (count($output));

		$links_externos = ($nofollow + $sponsored + $mails + $tels);

		$links_externos_follow = ($links - ($links_internos + $links_externos));
		if ($links_externos_follow<0)$links_externos_follow=0;

		// //ENLACES A PARTIR DEL 50%
		$output = $crawler->filter($selector_cuerpo)->html();
		$html_text = substr($output, strlen($output) / 2, strlen($output));
		if (strpos($html_text, "<a") > 0) $links50 = 1;
		else $links50 = -1;


		// //CONTEO DE MAPAS
		$output = $crawler->filter($selector_frame)->extract(array('src'));
		$maps = 0;
		for ($i = 0; $i < count($output); $i++) {
			if (substr($output[$i], 0, 33) == "https://www.google.com/maps/embed") ++$maps;
		}
		

		// //CONTEO DE IMÁGENES
		$imagenes = $crawler->filter($selector_img);
		$img_width = $crawler->filter($selector_img)->extract(array('width'));

		$tam_imgs=1;
		for ($i=0; $i<sizeof($img_width);$i++){
			if (($img_width[$i]>600)&&($img_width[$i]<0)){
				$tam_imgs=-1;
			}
		}
		
		$imagenes_autor = $crawler->filter($selector_img_autor);
		
		$imgs = count($imagenes)-count($imagenes_autor);

		// //ALTS o TITLES
		$output = $crawler->filter($selector_img)->extract(array('alt', 'title'));
		$alts = 0;
		for ($i = 0; $i < count($output); $i++) {
			if (strlen(str_replace(' ', '', $output[$i][0])) >= 20) ++$alts;
			else if (strlen(str_replace(' ', '', $output[$i][0])) >= 20) ++$alts;
		}
		
		// //PIES DE IMÁGENES
		$output = $crawler->filter($selector_pie_foto)->extract(array('_text'));
		$pies = 0;
		for ($i = 0; $i < count($output); $i++) {
			if ($output[$i] != "") ++$pies;
		}
		

		// //VIDEOS
		$output = $crawler->filter($selector_frame)->extract(array('src'));
		$vids = 0;
		for ($i = 0; $i < count($output); $i++) {
			++$vids;
		}
		$vids = $vids - $maps;

		// //TAGS
		$output = $crawler->filter($selector_tags);
		$tags = count($output);

		// // //PUNTUACIONES


		$matriz_valores = array(
			"title" => array("A" => 70, "B" => 60, "C" => 45, "D" => 0),
			"meta" => array("A" => 160, "B" => 80, "C" => 60, "D" => 0),
			"autor" => array("A" => 1, "B" => 0, "C" => 0, "D" => 0),
			"cuerpo" => array("A" => 1000, "B" => 600, "C" => 450, "D" => 0),
			"imgs" => array("A" => 3, "B" => 2, "C" => 1, "D" => 0),
			"tam_imgs" => array("A" => 1, "B" =>-1, "C" => -1, "D" => -1),
			"alts" => array("A" => 1, "B" => -1, "C" => -1, "D" => -1),
			"pies" => array("A" => 1, "B" => -1, "C" => -1, "D" => -1),
			"vids" => array("A" => 1, "B" => 0, "C" => 0, "D" => 0),
			"maps" => array("A" => 1, "B" => 0, "C" => 0, "D" => 0),
			"neg" => array("A" => 9, "B" => 7, "C" => 5, "D" => 0),
			"ladillos" => array("A" => 3, "B" => 2, "C" => 1, "D" => 0),
			"tags" => array("A" => 5, "B" => 4, "C" => 3, "D" => 0),
			"links" => array("A" => 5, "B" => 4, "C" => 3, "D" => 0),
			"links_internos" => array("A" => 4, "B" => 3, "C" => 2, "D" => 0),
			"links_externos_follow" => array( "D" => 1, "A" => 0, "B" => 0, "C" => 0),
			"links50" => array("A" => 1, "B" => -1, "C" => -1, "D" => -1)
		);



		$matriz_puntos =  array(
			"title" => array("A" => 300, "B" => 250, "C" => 200, "D" => 0),
			"meta" => array("A" => 300, "B" => 250, "C" => 200, "D" => 0),
			"autor" => array("A" => 100, "B" => 0, "C" => 0, "D" => 0),
			"cuerpo" => array("A" => 300, "B" => 200, "C" => 100, "D" => 0),
			"imgs" => array("A" => 500, "B" => 350, "C" => 200, "D" => 0),
			"tam_imgs" => array("A" => 300, "B" => 0, "C" => 0, "D" => 0),
			"alts" => array("A" => 300, "B" => 0, "C" => 0, "D" => 0),
			"pies" => array("A" => 100, "B" => 0, "C" => 0, "D" => 0),
			"vids" => array("A" => 200, "B" => 0, "C" => 0, "D" => 0),
			"maps" => array("A" => 200, "B" => 0, "C" => 0, "D" => 0),
			"neg" => array("A" => 150, "B" => 125, "C" => 100, "D" => 0),
			"ladillos" => array("A" => 200, "B" => 150, "C" => 100, "D" => 0),
			"tags" => array("A" => 100, "B" => 50, "C" => 25, "D" => 0),
			"links" => array("A" => 200, "B" => 175, "C" => 150, "D" => 0),
			"links_internos" => array("A" => 200, "B" => 175, "C" => 150, "D" => 0),
			"links_externos_follow" => array("A" => 1000, "B" => 1000, "C" => 1000, "D" => 0),
			"links50" => array("A" => 100, "B" => 0, "C" => 0, "D" => 0)
		);



		foreach ($matriz_valores as $param => $valor) {

			$found = false;

			foreach ($valor as $key => $value) {

				if (${$param} >= $value && !$found) { 

					${"punt_" . $param} = $matriz_puntos[$param][$key];
					${"punt_" . $param. "_max"} = $matriz_puntos[$param]["A"];
					$found = true;
				}
			}
		}

		$json = array(
			'estado'=>"ok",
			'code'=>"200",
			'data'=>array('Title' => array("Puntos" => $punt_title, "Max Puntos" => $punt_title_max),
			'Meta' => array("Puntos" =>$punt_meta, "Max Puntos" => $punt_meta_max),
			'Autor' => array("Puntos" =>$punt_autor, "Max Puntos" => $punt_autor_max),
			'Extensión del texto' => array("Puntos" =>$punt_cuerpo, "Max Puntos" => $punt_cuerpo_max),
			'Imágenes' => array("Puntos" =>$punt_imgs, "Max Puntos" => $punt_imgs_max),
			'Tamaño de Imágenes' => array("Puntos" =>$punt_tam_imgs, "Max Puntos" => $punt_tam_imgs_max),
			'Alt-Title' => array("Puntos" =>$punt_alts, "Max Puntos" => $punt_alts_max),
			'Pies de foto' => array("Puntos" =>$punt_pies, "Max Puntos" => $punt_pies_max),
			'Vídeos' => array("Puntos" =>$punt_vids, "Max Puntos" => $punt_vids_max),
			'Mapas' => array("Puntos" =>$punt_maps, "Max Puntos" => $punt_maps_max),
			'Negritas' => array("Puntos" =>$punt_neg, "Max Puntos" => $punt_neg_max),
			'Ladillos' => array("Puntos" =>$punt_ladillos, "Max Puntos" => $punt_ladillos_max),
			'Tags' => array("Puntos" =>$punt_tags, "Max Puntos" => $punt_tags_max),
			'Número enlaces' => array("Puntos" =>$punt_links, "Max Puntos" => $punt_links_max),
			'Enlaces internos' => array("Puntos" =>$punt_links_internos, "Max Puntos" => $punt_links_internos_max),
			'Enlaces externos dofollow' => array("Puntos" =>$punt_links_externos_follow, "Max Puntos" => $punt_links_externos_follow_max),
			'Enlaces después del 50%' => array("Puntos" =>$punt_links50, "Max Puntos" => $punt_links50_max))
		);
		  $this->response($json);

		}
		else{

		$json = array(
			'estado'=>"ok",
			'code'=>"200",
			'data'=>array('Error' => "Esta url no es compatible con el sistema de análisis SEO")
			);
			$this->response($json);
		}
	}
}
