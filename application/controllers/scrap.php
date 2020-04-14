<?php
defined('BASEPATH') or exit('No direct script access allowed');

require "application/libraries/vendor/autoload.php";

use Goutte\Client;

class scrap extends CI_Controller
{

	public function index()
	{

		$this->load->view('main');
		$url =  $_POST['scrap_url'];
		$client = new Client();
		$crawler = $client->request('GET', $url);
		$metas = get_meta_tags($url);

		$selector_title = 'title';
		$selector_negrita = 'span[itemprop="articleBody"] strong';
		$selector_negrita_h2 = 'span[itemprop="articleBody"] h2 strong';
		$selector_negrita_h3 = 'span[itemprop="articleBody"] h3 strong';
		$selector_negrita_a = 'span[itemprop="articleBody"] a strong';
		$selector_negrita_a_str = 'span[itemprop="articleBody"] a +strong';
		$selector_negrita_str_a = 'span[itemprop="articleBody"] strong+a';
		$selector_img = 'div.cuerpo_noticia div[itemprop="image"] img';
		$selector_cuerpo = 'span[itemprop="articleBody"]';
		$selector_enlace = 'span[itemprop="articleBody"] a';
		$selector_nofollow = 'span[itemprop="articleBody"] a[rel="nofollow"]';
		$selector_sponsored = 'span[itemprop="articleBody"] a[rel="sponsored"]';
		$selector_frame = 'iframe';
		$selector_ladillos = 'span[itemprop="articleBody"] h2+p';
		$selector_pie_foto = 'div.cuerpo_noticia div[itemprop="image"] span.pie_foto';
		$selector_redactor = 'span.autor_sup a';
		$selector_tags = 'div.etiquetasTag ul li';

		//CONTEO DE PALABRAS EN EL TITLE
		$output = $crawler->filter($selector_title)->extract(array('_text'));
		$title = strlen($output[0]);


		//CONTEO DE PALABRAS EN LA META DESCRIPCIÓN
		$output = $metas['description'];
		$meta = strlen($output);

		//FIRMA DEL AUTOR
		$output = $crawler->filter($selector_redactor)->extract(array('_text'));
		if (isset(($output[0]))) $autor = 1;
		else $autor = 0;


		//CONTEO DE PALABRAS EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_cuerpo)->extract(array('_text'));
		$str = mb_strtoupper($output[0], 'UTF-8');
		$conv = array("Á" => "A", "É" => "E", "Í" => "I", "Ó" => "O", "Ú" => "U");
		$text_final = strtr($str, $conv);
		$cuerpo = str_word_count($text_final);


		//LADILLOS
		$output = $crawler->filter($selector_ladillos);
		$ladillos = count($output);


		//CONTEO DE FRASES EN NEGRITA
		$output = $crawler->filter($selector_negrita);
		$output_h2 = $crawler->filter($selector_negrita_h2);
		$output_a = $crawler->filter($selector_negrita_a);
		$output_h3 = $crawler->filter($selector_negrita_h3);
		$output_str_a = $crawler->filter($selector_negrita_str_a);
		$output_a_str = $crawler->filter($selector_negrita_a_str);

		$neg = count($output) - count($output_h2) - count($output_h3) - count($output_a) - count($output_str_a) - count($output_a_str);


		//CONTEO DE ENLACES EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_enlace)->extract(array('href'));
		$enlaces = (count($output));

		$enlaces_internos = 0;
		for ($i = 0; $i < count($output); $i++) {
			if ((substr($output[$i], 0, 33) == "https://www.diarioinformacion.com") || (substr($output[$i], 0, 26) == "https://www.informacion.es") || (substr($output[$i], 0, 1) == "/")) ++$enlaces_internos;
		}


		//CONTEO DE ENLACES CON NOFOLLOW O SPONSORED EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_nofollow);
		$nofollow = (count($output));

		$output = $crawler->filter($selector_sponsored);
		$sponsored = (count($output));

		$enlaces_externos = ($nofollow + $sponsored);

		$enlaces_ext_follow = ($enlaces - ($enlaces_internos + $enlaces_externos));


		//ENLACES A PARTIR DEL 50%
		$output = $crawler->filter($selector_cuerpo)->html();
		$html_text = substr($output, strlen($output) / 2, strlen($output));
		if (strpos($html_text, "<a") > 0) $link_50 = 1;
		else $link_50 = 0;


		//CONTEO DE MAPAS
		$output = $crawler->filter($selector_frame)->extract(array('src'));
		$conteo_mapas = 0;
		for ($i = 0; $i < count($output); $i++) {
			if (substr($output[$i], 0, 33) == "https://www.google.com/maps/embed") ++$conteo_mapas;
		}


		//CONTEO DE IMÁGENES
		$imagenes = $crawler->filter($selector_img);
		$imgs = count($imagenes);


		//ALTS o TITLES
		$output = $crawler->filter($selector_img)->extract(array('alt', 'title'));
		$conteo_alts = 0;
		for ($i = 0; $i < count($output); $i++) {
			if (strlen(str_replace(' ', '', $output[$i][0])) >= 20) ++$conteo_alts;
			else if (strlen(str_replace(' ', '', $output[$i][0])) >= 20) ++$conteo_alts;
		}

		//PIES DE IMÁGENES
		$output = $crawler->filter($selector_pie_foto)->extract(array('_text'));
		$conteo_pie = 0;
		for ($i = 0; $i < count($output); $i++) {
			if ($output[$i] != "") ++$conteo_pie;
		}

		//VIDEOS
		$output = $crawler->filter($selector_frame)->extract(array('src'));
		$conteo_vids = 0;
		for ($i = 0; $i < count($output); $i++) {
			++$conteo_vids;
		}
		$conteo_vids = $conteo_vids - $conteo_mapas;


		//TAGS
		$output = $crawler->filter($selector_tags);
		$tags = count($output);



		//PUNTUACIONES
		if ($title >= 70) {
			$punt_title = 200;
		} elseif ($title >= 60) {
			$punt_title = 80;
		} elseif ($title >= 45) {
			$punt_title = 60;
		} else {
			$punt_title = 0;
		}

		if ($meta >= 100) {
			$punt_meta = 200;
		} elseif ($meta >= 80) {
			$punt_meta = 80;
		} elseif ($meta >= 60) {
			$punt_meta = 60;
		} else {
			$punt_meta = 0;
		}

		if ($autor == 1) {
			$punt_autor = 50;
		} else {
			$punt_autor = 0;
		}

		if ($cuerpo >= 600) {
			$punt_cuerpo = 100;
		} elseif ($cuerpo >= 500) {
			$punt_cuerpo = 80;
		} elseif ($cuerpo >= 350) {
			$punt_cuerpo = 50;
		} else {
			$punt_cuerpo = 0;
		}

		if ($imgs >= 3) {
			$punt_imgs = 100;
		} elseif ($imgs == 2) {
			$punt_imgs = 80;
		} elseif ($imgs == 1) {
			$punt_imgs = 60;
		} else {
			$punt_imgs = 0;
		}

		if ($conteo_alts == $imgs) {
			$punt_alts = 100;
		} else {
			$punt_alts = 0;
		}

		if ($conteo_pie == $imgs) {
			$punt_pies = 50;
		} else {
			$punt_pies = 0;
		}

		if ($conteo_vids >= 2) {
			$punt_vids = 100;
		} elseif ($conteo_vids == 1) {
			$punt_vids = 80;
		} else {
			$punt_vids = 0;
		}

		if ($conteo_mapas >= 1) {
			$punt_maps = 100;
		} else {
			$punt_maps = 0;
		}

		if ($neg >= 9) {
			$punt_neg = 100;
		} elseif ($neg >= 7) {
			$punt_neg = 60;
		} elseif ($neg >= 5) {
			$punt_neg = 40;
		} else {
			$punt_neg = 0;
		}

		if ($ladillos >= 3) {
			$punt_lad = 100;
		} elseif ($ladillos >= 2) {
			$punt_lad = 50;
		} elseif ($ladillos >= 1) {
			$punt_lad = 25;
		} else {
			$punt_lad = 0;
		}

		if ($tags >= 5) {
			$punt_tags = 100;
		} elseif ($tags >= 4) {
			$punt_tags = 50;
		} elseif ($tags >= 3) {
			$punt_tags = 25;
		} else {
			$punt_tags = 0;
		}

		if ($enlaces >= 5) {
			$punt_enlaces = 100;
		} elseif ($enlaces >= 4) {
			$punt_enlaces = 50;
		} elseif ($enlaces >= 3) {
			$punt_enlaces = 25;
		} else {
			$punt_enlaces = 0;
		}

		if ($enlaces_internos >= 4) {
			$punt_enlaces_int = 200;
		} elseif ($enlaces_internos >= 3) {
			$punt_enlaces_int = 150;
		} elseif ($enlaces_internos >= 2) {
			$punt_enlaces_int = 100;
		} else {
			$punt_enlaces_int = 0;
		}

		if ($enlaces_ext_follow >= 1) {
			$punt_enlaces_follow = 0;
		} else {
			$punt_enlaces_follow = 500;
		}

		if ($link_50 == 1) {
			$punt_link50 = 100;
		} else {
			$punt_link50 = 0;
		}

		$data = array(
			'title_long' => $title,
			'meta_long' => $meta,
			'firma' => $autor,
			'cuerpo_long' => $cuerpo,
			'lad_long' => $ladillos,
			'negrita' => $neg,
			'links' => $enlaces,
			'spon_nofol' => $enlaces_externos,
			'link50' => $link_50,
			'mapas' => $conteo_mapas,
			'imagenes' => $imgs,
			'alt_title' => $conteo_alts,
			'pies' => $conteo_pie,
			'videos' => $conteo_vids,
			'tags' => $tags
		);

		$json=array(
			'Title'=>$punt_title,
			'Desc'=>$punt_meta,
			'Autor'=>$punt_autor,
			'Cuerpo'=>$punt_cuerpo,
			'Imágenes'=>$punt_imgs,
			'Alt-Title'=>$punt_alts,
			'Pies de foto'=>$punt_pies,
			'Vídeos'=>$punt_vids,
			'Mapas'=>$punt_maps,
			'Negritas'=>$punt_neg,
			'Ladillos'=>$punt_lad,
			'Tags'=>$punt_tags,
			'Número enlaces'=>$punt_enlaces,
			'Enlaces internos'=>$punt_enlaces_int,
			'Enlaces externos dofollow'=>$punt_enlaces_follow,
			'Enlaces después del 50%'=>$punt_link50
		);

		$res_json = json_encode($json,JSON_UNESCAPED_UNICODE);
		echo $res_json;
		
		$this->load->view('scrap_res', $data);
	}
}
