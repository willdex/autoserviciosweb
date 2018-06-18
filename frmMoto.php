<?php 
require('clsmoto.php');

switch ($_GET['opcion']) {
			case 'get_moto':
			   get_moto();
				break;
			case 'set_moto':
			   set_moto();
				break;
			case 'get_motos_en_rango':
			    get_motos_en_rango();
			    break;
			case 'set_ubicacion_punto':
				set_ubicacion_punto();
				break;
			case 'set_estado':
				set_estado();
				break;
			case 'get_login':
				get_login();
			break;
			case 'cerrar_sesion':
				cerrar_sesion();
			break;
			case 'obtener_ubicacion_por_id_pedido':
			    obtener_ubicacion_por_id_pedido();
			break;
			case 'obtener_ubicacion_por_id_pedido_carrera':
				obtener_ubicacion_por_id_pedido_carrera();
			  break;
			case 'set_token':
			     set_token();
			break;
			case 'get_imagen':
			     get_imagen();
			break;
			case 'get_imagen_usuario':
			     get_imagen_usuario();
			break;
		    default:
			
			break;
	}
function set_moto()
{
$dato=json_decode(file_get_contents("php://input"),true);
$dato=Moto::set_moto($dato['nombre'],$dato['apellido'],$dato['ci'],$dato['celular'],$dato['email'],$dato['marca'],$dato['modelo'],$dato['placa'],$dato['direccion'],$dato['telefono'],$dato['referencia'],$dato['codigo'],$dato['credito']);

if($dato )
print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.' ));
else
print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al cargar.' ));
}

function get_moto()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Moto::get_moto($dato['celular'],$dato['codigo']);
$token=Moto::set_token($dato['celular'],$dato['token']);
if($row!="-1" && $token===true)
{
	$dato['suceso']='1';
	$dato['mensaje']='Inicio sesion correctamente.';
	$dato['perfil']=array($row);
	
	print json_encode($dato);
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'No se pudo inciar sesion. porque su cuenta ya esta iniciada en otro dispositivo. Por favor contactese con el administrador.' ));
}
}

function get_motos_en_rango()
{
	 $dato=json_decode(file_get_contents("php://input"),true);
$row=Moto::get_motos_en_rango($dato['latitud'],$dato['longitud'],500000);
if($row!="-1")
{ $mensaje['suceso']='1';
  $mensaje['mensaje']='Correcto';
  $mensaje['moto']=$row;
	print json_encode($mensaje);
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al ingresar los datos.' ));
}
}

function set_ubicacion_punto()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Moto::set_ubicacion_punto($dato['latitud'],$dato['longitud'],$dato['id_moto']);
	if($row===true)
	{// di tiene id_carrera=0 quiere decir q el pedido esta en camino... porque el estado en 0--
		// si el id_carrera=-1 quiere decir que se inicio una carrera y se finalizo.. ey esta en espera a una nueva carrera o finalizar todo el pedido.....
		if($dato['id_carrera']!="-1")
		{
		$cargar=Moto::set_puntos($dato['latitud'],$dato['longitud'],$dato['id_pedido'],$dato['id_carrera'],$dato['numero']);
	}
	print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	print json_encode(array('suceso' => '2' ,'mensaje'=>'Error: Al ingresar mi ubicacion.' ));
			}
			
}

function set_estado()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Moto::set_estado($dato['estado'],$dato['id_moto']);
	if($row===true)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'Error: Al cargar el estado.' ));	
	}
}

function get_login()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Moto::get_login($dato['id_moto']);
	if($row)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'Error: Al cargar el estado.' ));	
	}
}
function cerrar_sesion()
{

	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Moto::set_login($dato['id_moto'],'0');
	if($row)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Sesion cerrado con Exito'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'Error: Al cerrar sesion.' ));	
	}
}
function obtener_ubicacion_por_id_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Moto::obtener_ubicacion_por_id_pedido($dato['id_pedido']);
	if($row!="-1" || $row!=false)
	{
		$dato['suceso']='1';
		$dato['mensaje']='Correcto';
		$dato['punto']=array($row);
		print json_encode($dato);
	}
	else
	{
		 print json_encode(array('suceso' => '2' ,'mensaje'=>'No se pudo obtener su posicion.' ));	
	}
}

function obtener_ubicacion_por_id_pedido_carrera()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Moto::obtener_ubicacion_por_id_pedido_carrera($dato['id_pedido']);
	if($row!="-1" || $row!=false)
	{
		$dato['suceso']='1';
		$dato['mensaje']='Correcto';
		$dato['punto']=array($row);
		print json_encode($dato);
	}
	else
	{
		 print json_encode(array('suceso' => '2' ,'mensaje'=>'No se pudo obtener su posicion.' ));	
	}
}

function get_imagen()
{
	$row=Moto::get_imagen($_GET['id_moto']);
	
	if($row!="-1" )
	{

$row=str_replace("data:image/png;base64,", "", $row);
$row=str_replace("data:image/jpeg;base64,", "", $row);
	 header('content-type: image/jpeg');

	 echo base64_decode($row);
	


	}
	else
		{ echo "null";}
	
}
function get_imagen_usuario()
{
	$row=Moto::get_imagen_usuario($_GET['id_usuario']);
	
	if($row!="-1" )
	{
	 header('content-type: image/jpeg');	 
	 echo base64_decode($row);
	}
	else
		{ echo "null";}
	
}
?>


