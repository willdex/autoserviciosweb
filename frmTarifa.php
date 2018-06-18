<?php
require ('clstarifa.php');

switch ($_GET['opcion']) {
	case 'get_tarifa':
		get_tarifa();
		break;
	case 'get_ultima_tarifa':
	   get_ultima_tarifa();
		break;
	
	default:
		
		break;
}

function get_tarifa()
{
	$row=Tarifa::get_tarifa();
	if($row)
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["tarifa"]=$row;
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}		
}

function get_ultima_tarifa()
{
	$row=Tarifa::get_ultima_tarifa();
	if($row!="-1")
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["tarifa"]= array($row);
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}		
}


?>