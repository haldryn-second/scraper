<?php 


if ((substr($url, 0, 33) == "https://www.diarioinformacion.com") ||
	(substr($url, 0, 26) == "https://www.informacion.es")||
	(substr($url, 0, 29) == "https://www.diaridegirona.cat")||
	(substr($url, 0, 28) == "https://www.diariodeibiza.es")||
	(substr($url, 0, 25) == "https://www.farodevigo.es")||
	(substr($url, 0, 31) == "https://www.diariodemallorca.es")||
	(substr($url, 0, 18) == "https://www.lne.es")||
	(substr($url, 0, 30) == "https://www.laopinioncoruna.es")||
	(substr($url, 0, 32) == "https://www.laopiniondemalaga.es")||
	(substr($url, 0, 32) == "https://www.laopiniondezamora.es")||
	(substr($url, 0, 32) == "https://www.laopiniondemurcia.es")||
	(substr($url, 0, 26) == "https://www.laprovincia.es")||
	(substr($url, 0, 27) == "https://www.levante-emv.com")) {

	//SCRAP INFORMACION
 
	$selector_title = 'title';
	$selector_negrita = 'span[itemprop="articleBody"] strong';
	$selector_negrita_h2 = 'span[itemprop="articleBody"] h2 strong';
	$selector_negrita_h3 = 'span[itemprop="articleBody"] h3 strong';
	$selector_negrita_a = 'span[itemprop="articleBody"] a strong';
	$selector_negrita_a_str = 'span[itemprop="articleBody"] a +strong';
	$selector_negrita_str_a = 'span[itemprop="articleBody"] strong+a';
	$selector_img = 'div.cuerpo_noticia div[itemprop="image"] img';
	$selector_img_autor = 'a.FotoAutorNoticia img';
	$selector_cuerpo = 'span[itemprop="articleBody"]';
	$selector_enlace = 'span[itemprop="articleBody"] a';
	$selector_nofollow = 'span[itemprop="articleBody"] a[rel="nofollow"]';
	$selector_sponsored = 'span[itemprop="articleBody"] a[rel="sponsored"]';
	$selector_frame = 'iframe';
	$selector_ladillos = 'span[itemprop="articleBody"] h2+p';
	$selector_pie_foto = 'div.cuerpo_noticia div[itemprop="image"] span.pie_foto';
	$selector_redactor = 'span.autor_sup a';
	$selector_tags = 'div.etiquetasTag ul li';

	$metas = get_meta_tags($url);

	if(sizeof($metas) == 0) {
		$meta=0;
	} 
	else{
		//print_r($metas);
		$output = $metas['description'];
		$meta = strlen($output);
	}
}


else if ((substr($url, 0, 29) == "https://www.diariocordoba.com")||
	(substr($url, 0, 35) == "https://www.elperiodicodearagon.com")||
	(substr($url, 0, 39) == "https://www.elperiodicomediterraneo.com")||
	(substr($url, 0, 38) == "https://www.elperiodicoextremadura.com")){

	//SCRAP CORDOBA - ARAGON - MEDITERRÁNEO - EXTREMADURA

	$selector_title = 'title';
	$selector_negrita = 'div.Contenidos strong';
	$selector_negrita_h2 = 'div.Contenidos h2 strong';
	$selector_negrita_h3 = 'div.Contenidos h3 strong';
	$selector_negrita_a = 'div.Contenidos a strong';
	$selector_negrita_a_str = 'div.Contenidos a +strong';
	$selector_negrita_str_a = 'div.Contenidos strong+a';
	$selector_img = 'div.Contenidos img';
	$selector_img_autor = 'a.FotoAutorNoticia img';
	$selector_cuerpo = 'div.Contenidos';
	$selector_enlace = 'div.CuerpoDeNoticia a';
	$selector_nofollow = 'div.CuerpoDeNoticia a[rel="nofollow"]';
	$selector_sponsored = 'div.CuerpoDeNoticia a[rel="sponsored"]';
	$selector_frame = 'iframe';
	$selector_ladillos = 'div.Contenidos h2+p';
	$selector_pie_foto = 'div.Contenidos p.PieDeFoto';
	$selector_redactor = 'p.AutorDeNoticia strong';
	if ((substr($url, 0, 29) == "https://www.diariocordoba.com")||(substr($url, 0, 38) == "https://www.elperiodicoextremadura.com")){
		$selector_redactor = 'p.AutorDeNoticia a';
	}
	$selector_tags = 'div.ListadodeBotones ul li';

	
	//SCRAP DE METADATOS
	$options  = array('http' => array('user_agent' => 'facebookexternalhit/1.1'));
	$context  = stream_context_create($options);
	$data = file_get_contents($url,false,$context);
	$dom = new \DomDocument;
	@$dom->loadHTML($data);
	$xpath = new \DOMXPath($dom);
	$metas = $xpath->query('//*/meta[starts-with(@property, \'og:\')]');
	$og = array();
	foreach($metas as $meta){
		$property = str_replace('og:', '', $meta->getAttribute('property'));
		$content = $meta->getAttribute('content');
		$og[$property] = $content;
		
	}
	$metas=$og['description'];
	if(sizeof($metas) == 0) {
		$meta=0;
	} 
	else{
		//print_r($metas);
		$output = $metas['description'];
		$meta = strlen($output);
	}
}

?>