<?php
require ('clsdireccion.php');

switch ($_GET['opcion']) {
	case 'get_direccion_por_id_usuario':
		get_direccion_por_id_usuario();
		break;
	case 'registrar_direccion':
	    registrar_direccion();
	case 'registrar_casa':
	    insertar_casa();
	    break;
	case 'registrar_trabajo':
	    insertar_trabajo();
	    break;
	case 'get_direccion_por_id_empresa':
	    get_direccion_por_id_empresa();
	    break;
	case 'registrar_direccion_empresa':
		registrar_direccion_empresa();
		break;    
	case 'modificar_direccion_empresa':
		modificar_direccion_empresa();
		break;    
	default:
		
		break;
}

function get_direccion_por_id_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Direccion::get_direccion_por_id_usuario($dato['id_usuario'],$dato['id_empresa']);

	if($row!="-1")
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["direccion"]=$row;
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}		
}
function registrar_direccion()
{

	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Direccion::insertar_direccion($dato['latitud'],$dato['longitud'],$dato['nombre'],$dato['detalle'],$dato['id_usuario']);

	if($row!="-1")
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["id_direccion"]=$row;
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}	
}
function registrar_direccion_empresa()
{

	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Direccion::insertar_direccion_empresa($dato['latitud'],$dato['longitud'],$dato['nombre'],$dato['detalle'],$dato['id_usuario'],$dato['id_empresa']);

	if($row!="-1")
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["id_direccion"]=$row;
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}	
}
function modificar_direccion_empresa()
{

	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Direccion::modificar_direccion_empresa($dato['latitud'],$dato['longitud'],$dato['nombre'],$dato['detalle'],$dato['id_usuario'],$dato['id_empresa'],$dato['id_direccion']);

	if($row==true)
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Se modifico correctamente.";
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}	
}
function insertar_casa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $retorno=Direccion::insertar_casa($dato['nombre'],$dato['detalle'],$dato['latitud'],$dato['longitud'],$dato['id_usuario']);
    if($retorno!="-1")
    {		  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["id_direccion"]=$retorno;
		print json_encode($dato);
    }
    else
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No se obtuvo el perfil'));
    }
}
function insertar_trabajo()
{
		$dato=json_decode(file_get_contents("php://input"),true);
    $retorno=Direccion::insertar_trabajo($dato['nombre'],$dato['detalle'],$dato['latitud'],$dato['longitud'],$dato['id_usuario']);
    if($retorno!="-1")
    {		  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["id_direccion"]=$retorno;
		print json_encode($dato);
    }
    else
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No se obtuvo el perfil'));
    }
}



function get_direccion_por_id_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Direccion::get_direccion_por_id_empresa($dato['id_empresa']);

	if($row!="-1")
	 {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "Correcto.";
	  	 $dato["direccion"]=$row;
		print json_encode($dato);
	 }
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."));
	}		
}


?>