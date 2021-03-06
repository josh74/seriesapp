<?php

	class seriesMaster {
			
			var $servers=array(
							array(
								'name'=>'Series Yonkis',
								'url'=>'www.seriesyonkis.com',
								'indice'=>'filters'
							),
							array(
								'name'=>'Series Pepito',
								'url'=>'www.seriespepito.com',
								'indice'=>'lista_letras'
							)
						);
							
		/**
		*	Devuelve lista de servidores
		*/
		function serverList(){		
			return $this->servers;			
		}
		
		
		function getInnerHTML($Node)
		{
			$Body = $Node->ownerDocument->documentElement->firstChild->firstChild;
			$Document = new DOMDocument();    
			$Document->appendChild($Document->importNode($Body,true));
			return $Document->saveHTML();
		}
		function printDomElement($element)
		{
			$newdoc = new DOMDocument();
			$cloned = $element->cloneNode(TRUE);
			$newdoc->appendChild($newdoc->importNode($cloned,TRUE));
			return $newdoc->saveHTML();
		}

		
		//-- Devuelve las series que se encuentran en una url concreta, no todas las series del servidor --//
		//-- Devuelve las series ubicadas en una pagina con objeto con id "list-container" --//
		function urlSeriesList($url_orig,$url,&$series_link_list)
		{
			$html_text=$this->getPage($url);
			$dom_doc = new DOMDocument();
			$dom_doc->loadHTML($html_text);	
			
			/*El elemento que contiene la lista de series 
				para la letra que se está procesando es list-container*/
			$list_series=$dom_doc->getElementById("list-container");
				
			if (is_object($list_series))
			$childNodes = $list_series->getElementsByTagName('a');
		
			for ($k = 0; $k < $childNodes->length; $k++)
			{
				$node_dict = $childNodes->item($k);
				if ($node_dict->nodeType <> XML_TEXT_NODE)
				{
					//IMPORTANTE: aquí manipulamos la url del href	
					$href=$node_dict->getAttribute('href');
					$href='index.php?param='.$url_orig.$href;
					$href.='&task=chapterList';
					$node_dict->setAttribute('href',$href);
					$s=trim($this->printDomElement($node_dict));
					if (!empty($s))	$series_link_list[]=$s;		
					
				}
			}
		
		}
		
		function getElementsByClassName(DOMDocument $DOMDocument, $ClassName)
		{
			$Elements = $DOMDocument -> getElementsByTagName("*");
			$Matched = array();
 
			foreach($Elements as $node)
			{
				if( ! $node -> hasAttributes())
					continue;
		 
				$classAttribute = $node -> attributes -> getNamedItem('class');
		 
				if( ! $classAttribute)
					continue;
		 
				$classes = explode(' ', $classAttribute -> nodeValue);
		 
				if(in_array($ClassName, $classes))
					$Matched[] = $node;
			}
		 
			return $Matched;
		}
				
		

		/**
		*	Devuelve lista de series
		*/
		function seriesList($url){
		
			libxml_use_internal_errors(true); //para que no salgan warnings creo 

			$links_array = array();
			
			/* Rellenamos links_array con los links asociados 
			 a cada entrada de la clasificación alfabetica de series. 
			 Elemplos:
			 	series que empiezan por A sería /lista_series/A 
			 	las que empiezan con B /lista_series/B etc... */
			
			$classname="dictionary";
			$classnamelist="dictionary-list";
			$html=$this->getPage('http://'.$url);
			$doc = new DOMDocument();
			@$doc->loadHTML($html);

			/* Los diferentes links para obtener las listas 
			de series que empiezan por cada una de las letras se 
			encuentran en el elemento filters de la pagina principal*/

			$nodes_dictionary=$doc->getElementById( "filters" );
			
			if ($nodes_dictionary == NULL)	{
				echo "no hay indice de series";
				return null;
			}

			/* Recorremos dicho elemento (filters)
			 y rellenamos  el array con los links */
			
			$childNodes = $nodes_dictionary->childNodes;
			
			$nueva_pagina = "";


			//-- Por cada letra del alfabeto coger los links para esa letra--//

			for ($i = 1; $i < $childNodes->length; $i++) {
				$node_dict = $childNodes->item($i);
				if ($node_dict->nodeType == XML_TEXT_NODE) {
					/* los nodos tipo texto los obviamos, 
					ahora se muestran pero solo por saber que tienen*/
					echo $this->printDomElement($node_dict);
				}
				else {
					$link = $node_dict->childNodes->item(0);
					/* En el atributo href del item 0 de los nodos
					 hijos del filters tenemos el link */
					$dir=$link->getAttribute('href');
					array_push($links_array, $dir);					
				}	
			} 
						
			/* Recorremos los diferentes links y vamos obteniendo 
			la lista de series para cada letra del alfabeto */
			$aIndexSeries = array();
			for ($j=0;$j<count($links_array);$j++){
						
				$aSerie = array();
				$aSerie[0] = 'http://'.$url.$links_array[$j];

				$this->urlSeriesList($url,'http://'.$url.$links_array[$j],$aSerie);
				
				$html_new=$this->getPage('http://'.$url.$links_array[$j]);
				$doc_new = new DOMDocument();
				$doc_new->loadHTML($html_new);							
								
				//-- coger los paginadores del 0 al penultimo --//
				//-- hay que coger este elemento por la clase  <div class="paginator" > --//
				$finder = new DomXPath($doc_new);
				$classname="paginator";
				$nodes_paginator = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
				
				for ($k = 0; $k < $nodes_paginator->length; $k++){
					if ($nodes_paginator->item[k]->nodeType<> XML_TEXT_NODE)
					{
						//echo $this->printDomElement($nodes_paginator->item($k));
						$childNodes_links_paginator = $nodes_paginator->item($k)->getElementsByTagName('a');
						for ($p = 0; $p < $childNodes_links_paginator->length-1; $p++)
						{
						//	echo $this->printDomElement($childNodes_links_paginator->item($p));
							$href=$childNodes_links_paginator->item($p)->getAttribute('href');
							$this->urlSeriesList($url,'http://'.$url.$href,$aSerie);
						}
					}
				}			
				$aIndexSeries[]=$aSerie;
				$aSerie = array();
				$aSerie[0] = 'http://'.$url.$links_array[$j];

				$html_new=$this->getPage('http://'.$url.$links_array[$j]);
				$doc_new = new DOMDocument();
				$doc_new->loadHTML($html_new);	
				
				/*El elemento que contiene la lista de series 
				para la letra que se está procesando es list-container*/
				$list_series=$doc_new->getElementById("list-container");
				
				if (is_object($list_series))
				$childNodes = $list_series->getElementsByTagName('a');
			
				for ($k = 1; $k < $childNodes->length; $k++){
					$node_dict = $childNodes->item($k);
					if ($node_dict->nodeType <> XML_TEXT_NODE){
						//IMPORTANTE: aquí manipulamos la url del href	
						$href=$node_dict->getAttribute('href');
						$href='index.php?param='.$url.$href;
						$href.='&task=chapterList';
						$node_dict->setAttribute('href',$href);
						$s=trim($this->printDomElement($node_dict));
						if (!empty($s))	$aSerie[]=$s;		
						
					}
			    }
				
				$aIndexSeries[]=$aSerie;
			}

			return $aIndexSeries;
				
		}
		
		
		/**
		*	Devuelve lista de servidores para un capítulo
		*/
		function chapterServerList($url){
			$html=$this->getPage($url);
			$dom=new DOMDocument();
			@$dom->loadHTML($html);
			//echo '<pre>';print_r($dom);die;
			//$dom=$dom->getElementByID('section-content');
			$finder = new DomXPath($dom);
			//array de nombres de servidores
			$aSrvNames = $finder->query("//td[@class='episode-server-img']/a/span/@class"); 		
			//array de links a los servers
			$aSrvLinks = $finder->query("//td[@class='episode-server-img']/a/@href"); 
			//Los combinamos en uno montando este tinglao...
			if (count($aSrvNames)==count($aSrvLinks)) {
				$max=$aSrvNames->length;
				for ($i=0;$i<$max;$i++){
					$aServers[]=array('srv'=>$aSrvNames->item($i)->value,'url'=>$aSrvLinks->item($i)->value);
				}
									//echo '<pre>'.$max;print_r($aServers);print_r($aSrvNames);
			}
		
			/*
			 $aServers es un array que contiene tuplas, cada una de las cuales tiene
			'srv'=> el nombre del servidor (se obtiene de la clase de un span, ej 'server streamcloud')
			'url'=> es una cadena de url que llevará al server
			*/
			return $aServers;
		}
		
		
		
		
		
		
		/**
		*	Devuelve lista de capítulos
		*/
		function chapterList($url){
			$html=$this->getPage('http://'.$url);
			$dom=new DOMDocument();
			@$dom->loadHTML($html);
			$dom=$dom->getElementByID('seasons-list');
			
			$childNodes = $dom->getElementsByTagName('li');
			for ($k = 0; $k < $childNodes->length; $k++)
			{
				$node_season = $childNodes->item($k);			
				$chapter_list = $node_season->getElementsByTagName('a');

				for ($p = 0; $p < $chapter_list->length; $p++){
					
					$chapter_node = $chapter_list->item($p);
					
					if ($chapter_node->nodeType != XML_TEXT_NODE) {
						$href=$chapter_node->getAttribute('href');
						$href='index.php?param='.$href;
						$href.='&task=chapterServerList';
						$chapter_node->setAttribute('href',$href);
						
						if ($chapter_node->nodeType == XML_ELEMENT_NODE){
							$s = trim($this->printDomElement($chapter_node));
							if (!empty($s))	{
								$aSeason[]=$s;
								}
							}
						}
						
				}
			
				$aChapter[]=$aSeason;
			}
			return $aChapter;
		}
		
		/**
		*	Devuelve lista de servidores
		*/
		function play(){
		}
		
		/**
		 * Pasando de reinventar ruedas por el momento...
		 * http://www.webmaster-talk.com/php-forum/120410-using-php-to-get-external-file.html
		 * Fetch the specified page source, even if fopen_url_wrapper is disabled
		 * For that purpose, we use the curl wrapper. It should work everywhere
		 * 
		 * @param String $url The url to fetch
		 * @return String The page source code
		 */
		function getPage($url="http://www.example.com/"){
		 $ch = curl_init();
		 curl_setopt($ch,CURLOPT_URL, $url);
		 curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
		 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		 curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		 curl_setopt($ch,CURLOPT_REFERER,'http://www.google.com/');
		 curl_setopt($ch,CURLOPT_TIMEOUT,10);
		 $html=curl_exec($ch);
		 if($html==false){
		  $m=curl_error(($ch));
		  error_log($m);
		 }
		 curl_close($ch);
		 return $html;
		}
		
		/**
		*	Devuelve el ID del indice de series, según el proveedor
		*/
		function getIndiceID($url){
		echo $url;
			foreach ($this->servers as $s) {
				if ($s['url']==$url) return $s['indice'];
			}
			die('No se pudo encontrar el ID del índice de series');
		}
		
	
	}	

?>
