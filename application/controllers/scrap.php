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
		$text_final= strtr($str, $conv);
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
		$output = $crawler->filter($selector_enlace);
		$enlaces = (count($output));


		//CONTEO DE ENLACES CON NOFOLLOW O SPONSORED EN EL CUERPO DE LA NOTICIA
		$output = $crawler->filter($selector_nofollow);
		$nofollow = (count($output));

		$output = $crawler->filter($selector_sponsored);
		$sponsored = (count($output));

		$link_nof_spon = ($nofollow + $sponsored);


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
			if (substr($output[$i], 0, 19) == "https://www.youtube") ++$conteo_vids;
		}


		//TAGS
		$output = $crawler->filter($selector_tags);
		$tags = count($output);


		$data = array(
			'title_long' => $title,
			'meta_long' => $meta,
			'firma' => $autor,
			'cuerpo_long' => $cuerpo,
			'lad_long' => $ladillos,
			'negrita' => $neg,
			'links' => $enlaces,
			'spon_nofol' => $link_nof_spon,
			'link50' => $link_50,
			'mapas' => $conteo_mapas,
			'imagenes' => $imgs,
			'alt_title' => $conteo_alts,
			'pies' => $conteo_pie,
			'videos' => $conteo_vids,
			'tags' => $tags
		);

		$this->load->view('scrap_res', $data);
	}
}
