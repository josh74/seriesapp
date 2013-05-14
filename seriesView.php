<?php
	/****************
	Vista(s).
	Se implementan métodos relativos a la visualización (pantallas o como se
	quiera llamar). Además también se realiza el paso de parámetros entre
	las diferentes vistas.
	
	Contiene una única propiedad, que es $html, el código html que será visualizado
	mediante la función display.

	La mecánica de esta clase es definir la información html en cada función.
	El método display se encarga de asociar el parámetro de la vista a invocar
	pasada por el controlador, con el método que corresponda.
	Además se genera un formulario para pasar por POST los parámetros
	(antes se hacía por url).
	El parámetro obligatorio a pasar es task. De otra forma, se volvería a la vista
	de inicio por defecto.
	Un ejemplo de paso de este parámetro sería
		<input type="text" name="task" value="serverList"/>
	Lo que se hace es decir al controlador que se ejecute la task 'serverList'
	Por otro lado, el action del formulario es 'index.php', de no ser así no funcionaría
	ya que index.php es el que actúa como punto de entrada al controlador, que a su vez
	se encarga de controlar la app.
	***********************/


class seriesView {

	var $html;

	function display($view,$param=null){
		switch($view){
			case 'start': 			$this->start();break;
			case 'serverList':		$this->serverList($param);break;
			case 'seriesList':		$this->seriesList($param);break;
			case 'chapterList':		$this->chapterList($param);break;
			default  	:			$this->error();
		}
		echo $this->html;		
	}



	function DEBUG($desc,$var) {
		echo '<pre>['.$desc.']<br/>';
		print_r($var);echo '</pre>';
	}



	function error() {
		$html='No se encontró nada de nada...<br/>
		<a href="index.php"> Volver </a>';
	}



	function start(){
		$html='
		<!DOCTYPE html>
		<html>
		<head>
		<title>SeriesApp2</title>
		</head>
		<body>	
			<h3>Series App2</h3>
			<p>Pulse "Empezar" para ver los proveedores de vídeo disponibles</p>
			<form id="formView" action="index.php" method="post">
			<input type="hidden" name="task" value="serverList"/>
			<input type="submit" value="Empezar"/> 
			</form>
		</body>
		</html>';
		$this->html=$html;
	}



	function serverList($aServers){
		if(empty($aServers)) {
			$this->error();
		}
		else{
			$html = '<form id="formView" action="index.php" method="post">';
			$html.= '<select name="server">';
			foreach ($aServers as $s){
				$html.='<option value="'.$s['url'].'">'.$s['name'].'</option>';
				$html.=$s['name'].'</a></li>';
			}
			$html.='<input type="submit" value="Ver series"/>'; 
			$html.='<input type="hidden" name="task" value="seriesList"/>';
			$html.='</select></form>';
		}
		$this->html=$html;
	}



	function seriesList($aSeries){
		if(empty($aSeries)) {
			$this->error();
		}
		else{
			//$this->DEBUG('aSeries',$aSeries);die();
			$html = '';
			foreach ($aSeries as $indexGroup){
				foreach ($indexGroup as $k=>$serie) {
					if ($k==0)	$html.='<h3>'.$serie.'</h3><ul>';
					else $html.='<li>'.trim($serie).'</li>';					
				}
				$html.='</ul>';
			}
			$html.='<input type="hidden" name="task" value="chapterList"/>';
			$html.='</select></form>';
		}
		$this->html=$html;
	}

	
	function chapterList($aChapters){
	//ahora mismo se hace un print_r sin mas
	echo '<h3>Vista "chapterList" pendiente de implementar</h3>';
	echo'<pre>';print_r($aChapters);echo'</pre>';
	}	

	
}
?>
