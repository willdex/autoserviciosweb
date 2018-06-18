<?php
require ('clsempresa.php');

switch ($_GET['opcion']) {
	case 'get_administrador':
		get_administrador();
		break;
	case 'actualizar_datos':
		actualizar_datos();
		break;
	default:
		
		break;
}

function get_administrador()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Empresa::get_administrador($dato['id_usuario']);

	if($row!="-1")
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["empresa"]=$row;
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}		
}

function actualizar_datos()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Empresa::actualizar_datos($dato['id_empresa'],$dato['id_administrador'],$dato['direccion'],$dato['telefono'],$dato['razon_social']);

	if($row==true)
	 {print json_encode(array("suceso"=>"1","mensaje"=>"Correcto"));
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}		
}






?>